<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Public signed webhook receiver.
 *
 * URL: POST /nj_court_search/webhook
 *
 * Authentication is HMAC only — no staff session.
 *
 * Expected headers:
 * - X-NJ-Court-Timestamp: unix seconds
 * - X-NJ-Court-Signature: hex(HMAC-SHA256(timestamp + "." + rawBody, webhook_secret))
 * - X-NJ-Court-Event-Id: unique event id (required for replay protection)
 *
 * CSRF: this route must be listed in $app_csrf_exclude_uris (see module README).
 * Do not disable CSRF globally.
 */
class Webhook extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('nj_court_search/nj_court_search_model');
        $this->load->helper('nj_court_search/nj_court_search');
    }

    public function index()
    {
        if ($this->input->method(true) !== 'POST') {
            $this->respond(405, ['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (get_option('nj_court_search_enabled') != '1'
            || get_option('nj_court_search_webhook_enabled') != '1') {
            $this->respond(503, ['success' => false, 'error' => 'Webhook processing disabled']);
            return;
        }

        $rawBody = file_get_contents('php://input');
        if ($rawBody === false || $rawBody === '') {
            $this->log_rejected(null, 'empty_body');
            $this->respond(400, ['success' => false, 'error' => 'Empty body']);
            return;
        }

        $timestamp = $this->header_value('X-NJ-Court-Timestamp');
        $signature = $this->header_value('X-NJ-Court-Signature');
        $eventId   = $this->header_value('X-NJ-Court-Event-Id');

        if ($timestamp === '' || $signature === '' || $eventId === '') {
            $this->log_rejected($eventId ?: null, 'missing_headers');
            $this->respond(401, ['success' => false, 'error' => 'Missing signature headers']);
            return;
        }

        $tolerance = (int) get_option('nj_court_search_webhook_tolerance');
        if ($tolerance < 60) {
            $tolerance = 300;
        }

        if (!ctype_digit((string) $timestamp)
            || abs(time() - (int) $timestamp) > $tolerance) {
            $this->log_rejected($eventId, 'stale_timestamp');
            $this->respond(401, ['success' => false, 'error' => 'Stale or invalid timestamp']);
            return;
        }

        $secret = nj_court_search_get_secret('nj_court_search_webhook_secret');
        if ($secret === '') {
            $this->log_rejected($eventId, 'secret_not_configured');
            $this->respond(503, ['success' => false, 'error' => 'Webhook secret not configured']);
            return;
        }

        $expected = nj_court_search_compute_webhook_signature($timestamp, $rawBody, $secret);
        if (!nj_court_search_signatures_match($expected, $signature)) {
            $this->log_rejected($eventId, 'invalid_signature');
            $this->respond(401, ['success' => false, 'error' => 'Invalid signature']);
            return;
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            $this->log_rejected($eventId, 'invalid_json');
            $this->respond(400, ['success' => false, 'error' => 'Invalid JSON']);
            return;
        }

        $jobId = isset($payload['jobId']) ? (string) $payload['jobId']
            : (isset($payload['external_job_id']) ? (string) $payload['external_job_id'] : '');

        $payloadHash = hash('sha256', $rawBody);
        $recorded = $this->nj_court_search_model->record_webhook_event(
            $eventId,
            $jobId !== '' ? $jobId : null,
            $payloadHash,
            $timestamp
        );

        if (empty($recorded['accepted'])) {
            $this->respond(500, ['success' => false, 'error' => 'Unable to record webhook']);
            return;
        }

        if (!empty($recorded['duplicate'])) {
            // Idempotent success
            $this->respond(200, ['success' => true, 'duplicate' => true]);
            return;
        }

        $webhookRowId = isset($recorded['id']) ? (int) $recorded['id'] : 0;

        if ($jobId === '') {
            $this->nj_court_search_model->mark_webhook_processed($webhookRowId, 'missing_job_id');
            $this->log_rejected($eventId, 'missing_job_id');
            $this->respond(422, ['success' => false, 'error' => 'Missing jobId']);
            return;
        }

        $search = $this->nj_court_search_model->get_by_external_job_id($jobId);
        if (!$search) {
            $this->nj_court_search_model->mark_webhook_processed($webhookRowId, 'unknown_job');
            // Acknowledge to avoid endless retries for unknown jobs
            $this->respond(202, ['success' => true, 'warning' => 'Unknown job']);
            return;
        }

        $this->nj_court_search_model->add_event($search['id'], 'webhook_received', [
            'staff_id'          => null,
            'external_event_id' => $eventId,
            'event_data'        => [
                'event_type' => isset($payload['event']) ? $payload['event'] : null,
                'status'     => isset($payload['status']) ? $payload['status'] : null,
            ],
        ]);

        try {
            $applied = $this->nj_court_search_model->apply_remote_status_payload(
                $search['id'],
                $payload,
                null
            );

            // If payload lacked sufficient status detail, force a status pull
            if (!$applied['success']) {
                $this->nj_court_search_model->refresh_status($search['id'], null, 'webhook');
            }

            $this->nj_court_search_model->mark_webhook_processed($webhookRowId, null);
            $this->respond(200, ['success' => true]);
        } catch (Exception $e) {
            nj_court_search_log('Webhook processing error: ' . $e->getMessage());
            $this->nj_court_search_model->mark_webhook_processed($webhookRowId, 'processing_exception');
            $this->respond(500, ['success' => false, 'error' => 'Processing failed']);
        }
    }

    protected function header_value($name)
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$serverKey])) {
            return trim((string) $_SERVER[$serverKey]);
        }

        // Fallback via CI input
        $alt = $this->input->get_request_header($name, true);
        return $alt ? trim((string) $alt) : '';
    }

    protected function log_rejected($eventId, $reason)
    {
        // Minimal audit — no secrets, no raw body
        if ($eventId) {
            $this->nj_court_search_model->add_event(null, 'webhook_rejected', [
                'staff_id'          => null,
                'external_event_id' => $eventId,
                'event_data'        => ['reason' => $reason],
            ]);
        }
        nj_court_search_log('Webhook rejected: ' . $reason);
    }

    protected function respond($code, array $body)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($body));
    }
}
