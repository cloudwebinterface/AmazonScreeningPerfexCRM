<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Nj_court_search_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function table()
    {
        return db_prefix() . 'nj_court_searches';
    }

    public function events_table()
    {
        return db_prefix() . 'nj_court_search_events';
    }

    public function webhooks_table()
    {
        return db_prefix() . 'nj_court_webhook_events';
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function get($id)
    {
        $this->db->where('id', (int) $id);
        $row = $this->db->get($this->table())->row_array();

        return $row ?: null;
    }

    /**
     * @param string $jobId
     * @return array|null
     */
    public function get_by_external_job_id($jobId)
    {
        $this->db->where('external_job_id', $jobId);
        $row = $this->db->get($this->table())->row_array();

        return $row ?: null;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function get_by_idempotency_key($key)
    {
        $this->db->where('idempotency_key', $key);
        $row = $this->db->get($this->table())->row_array();

        return $row ?: null;
    }

    /**
     * Create a local draft/search record prior to (or after) API submit.
     *
     * @param array $data
     * @return int insert id
     */
    public function create_search(array $data)
    {
        $now = date('Y-m-d H:i:s');
        $insert = [
            'external_job_id'       => isset($data['external_job_id']) ? $data['external_job_id'] : null,
            'idempotency_key'       => $data['idempotency_key'],
            'first_name'            => $data['first_name'],
            'middle_name'           => isset($data['middle_name']) ? $data['middle_name'] : null,
            'last_name'             => $data['last_name'],
            'suffix'                => isset($data['suffix']) ? $data['suffix'] : null,
            'dob'                   => $data['dob'],
            'reference_id'          => isset($data['reference_id']) ? $data['reference_id'] : null,
            'lead_id'               => !empty($data['lead_id']) ? (int) $data['lead_id'] : null,
            'client_id'             => !empty($data['client_id']) ? (int) $data['client_id'] : null,
            'contact_id'            => !empty($data['contact_id']) ? (int) $data['contact_id'] : null,
            'notes'                 => isset($data['notes']) ? $data['notes'] : null,
            'status'                => isset($data['status']) ? $data['status'] : 'draft',
            'result_count'          => 0,
            'error_code'            => isset($data['error_code']) ? $data['error_code'] : null,
            'error_message'         => isset($data['error_message']) ? $data['error_message'] : null,
            'submitted_by'          => isset($data['submitted_by']) ? (int) $data['submitted_by'] : get_staff_user_id(),
            'submitted_at'          => isset($data['submitted_at']) ? $data['submitted_at'] : null,
            'next_poll_at'          => isset($data['next_poll_at']) ? $data['next_poll_at'] : null,
            'retry_count'           => 0,
            'created_at'            => $now,
            'updated_at'            => $now,
        ];

        $this->db->insert($this->table(), $insert);

        return (int) $this->db->insert_id();
    }

    /**
     * Update fields and bump updated_at.
     */
    public function update_search($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int) $id);
        $this->db->update($this->table(), $data);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Record an audit event. event_data must not contain secrets or full DOB.
     */
    public function add_event($searchId, $eventType, array $opts = [])
    {
        $payload = isset($opts['event_data']) ? $opts['event_data'] : null;
        if (is_array($payload)) {
            $payload = json_encode($payload);
        }

        $this->db->insert($this->events_table(), [
            'search_id'         => $searchId === null || $searchId === '' ? null : (int) $searchId,
            'event_type'        => $eventType,
            'old_status'        => isset($opts['old_status']) ? $opts['old_status'] : null,
            'new_status'        => isset($opts['new_status']) ? $opts['new_status'] : null,
            'event_data'        => $payload,
            'staff_id'          => array_key_exists('staff_id', $opts) ? $opts['staff_id'] : get_staff_user_id(),
            'external_event_id' => isset($opts['external_event_id']) ? $opts['external_event_id'] : null,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    /**
     * @param int $searchId
     * @return array
     */
    public function get_events($searchId)
    {
        $this->db->where('search_id', (int) $searchId);
        $this->db->order_by('id', 'ASC');

        return $this->db->get($this->events_table())->result_array();
    }

    /**
     * Transition status safely and write an event when it changes.
     * Enforces the allowed-transition map. Strips internal keys like _event_data
     * before writing columns.
     *
     * @return bool|array false on hard failure, or ['success'=>bool,'message'=>...]
     */
    public function transition_status($searchId, $newStatus, array $extra = [], $eventType = 'status_changed', $staffId = null)
    {
        $search = $this->get($searchId);
        if (!$search) {
            return false;
        }

        $old = $search['status'];

        if (!nj_court_search_can_transition($old, $newStatus)) {
            nj_court_search_log('Rejected invalid status transition ' . $old . ' → ' . $newStatus . ' for search #' . (int) $searchId);

            return [
                'success' => false,
                'message' => 'Invalid status transition: ' . $old . ' → ' . $newStatus,
            ];
        }

        $eventData = isset($extra['_event_data']) ? $extra['_event_data'] : ['reason' => 'status_transition'];
        unset($extra['_event_data']);

        $update = array_merge($extra, ['status' => $newStatus]);

        if ($newStatus === 'processing' && empty($search['processing_started_at'])) {
            $update['processing_started_at'] = date('Y-m-d H:i:s');
        }

        if (in_array($newStatus, nj_court_search_terminal_statuses(), true)) {
            if (empty($search['completed_at']) || $old !== $newStatus) {
                $update['completed_at'] = date('Y-m-d H:i:s');
            }
            $update['next_poll_at'] = null;
        }

        $this->update_search($searchId, $update);

        if ($old !== $newStatus) {
            $this->add_event($searchId, $eventType, [
                'old_status' => $old,
                'new_status' => $newStatus,
                'staff_id'   => $staffId,
                'event_data' => $eventData,
            ]);
            $this->notify_status_change($searchId, $search, $old, $newStatus);
        }

        return true;
    }

    /**
     * Notify the submitting staff member of meaningful status changes.
     * Does not include DOB or secrets.
     */
    protected function notify_status_change($searchId, array $search, $oldStatus, $newStatus)
    {
        $touser = !empty($search['submitted_by']) ? (int) $search['submitted_by'] : 0;
        if ($touser < 1) {
            return;
        }

        // Skip noisy intermediate same-workflow chatter except terminal / failure / cancel
        $notable = array_merge(
            nj_court_search_terminal_statuses(),
            ['queued', 'processing', 'submission_failed']
        );
        if (!in_array($newStatus, $notable, true)) {
            return;
        }

        $subject = trim(($search['first_name'] ?? '') . ' ' . ($search['last_name'] ?? ''));
        if ($subject === '') {
            $subject = '#' . (int) $searchId;
        }

        if (!function_exists('add_notification')) {
            return;
        }

        add_notification([
            'fromcompany'     => true,
            'touserid'        => $touser,
            'description'     => 'nj_court_search_notification_status',
            'link'            => 'nj_court_search/view/' . (int) $searchId,
            'additional_data' => serialize([
                $subject,
                $oldStatus,
                $newStatus,
            ]),
        ]);
    }

    /**
     * Submit (or re-submit) a local search to the external API.
     *
     * @return array{success:bool,message:string,search_id?:int}
     */
    public function submit_to_api($searchId)
    {
        $search = $this->get($searchId);
        if (!$search) {
            return ['success' => false, 'message' => 'Search not found.'];
        }

        $this->load->library('nj_court_search/nj_court_api_client');
        /** @var Nj_court_api_client $client */
        $client = $this->nj_court_api_client;

        $this->add_event($searchId, 'api_submission_attempted', [
            'old_status' => $search['status'],
            'new_status' => $search['status'],
            'event_data' => ['reference_id' => $search['reference_id']],
        ]);

        $payload = [
            'firstName'       => $search['first_name'],
            'lastName'        => $search['last_name'],
            'dob'             => $search['dob'],
            'referenceId'     => $search['reference_id'],
            'idempotencyKey'  => $search['idempotency_key'],
        ];
        if (!empty($search['middle_name'])) {
            $payload['middleName'] = $search['middle_name'];
        }
        if (!empty($search['suffix'])) {
            $payload['suffix'] = $search['suffix'];
        }

        $result = $client->submit_search($payload);

        if (!$result['success']) {
            $transition = $this->transition_status($searchId, 'submission_failed', [
                'error_code'    => isset($result['error_code']) ? $result['error_code'] : 'submit_failed',
                'error_message' => isset($result['message']) ? $result['message'] : 'Submission failed',
                '_event_data'   => ['http_code' => isset($result['status_code']) ? $result['status_code'] : null],
            ], 'api_submission_failed');

            if (is_array($transition) && empty($transition['success'])) {
                // draft → submission_failed should always be valid; log if map rejects
                nj_court_search_log('Unable to mark submission_failed for search #' . (int) $searchId);
            }

            return [
                'success'   => false,
                'message'   => isset($result['message']) ? $result['message'] : 'API submission failed.',
                'search_id' => $searchId,
            ];
        }

        $data = is_array($result['data']) ? $result['data'] : [];
        $jobId = isset($data['jobId']) ? $data['jobId'] : (isset($data['job_id']) ? $data['job_id'] : null);
        $remoteStatus = isset($data['status']) ? $data['status'] : 'queued';
        $localStatus = nj_court_search_map_remote_status($remoteStatus) ?: 'queued';

        if (!$jobId) {
            $this->transition_status($searchId, 'submission_failed', [
                'error_code'    => 'missing_job_id',
                'error_message' => 'API did not return a job ID.',
            ], 'api_submission_failed');

            return [
                'success'   => false,
                'message'   => 'API did not return a job ID.',
                'search_id' => $searchId,
            ];
        }

        // Never mark queued without a verified job ID (checked above).
        if (!in_array($localStatus, ['queued', 'processing', 'completed', 'no_results', 'failed'], true)) {
            $localStatus = 'queued';
        }

        $now = date('Y-m-d H:i:s');
        $transition = $this->transition_status($searchId, $localStatus, [
            'external_job_id' => $jobId,
            'submitted_at'    => $now,
            'error_code'      => null,
            'error_message'   => null,
            'last_checked_at' => $now,
            'next_poll_at'    => nj_court_search_compute_next_poll_at($search),
            '_event_data'     => ['external_job_id' => $jobId],
        ], 'api_submission_succeeded');

        if (is_array($transition) && empty($transition['success'])) {
            return [
                'success'   => false,
                'message'   => $transition['message'],
                'search_id' => $searchId,
            ];
        }

        return [
            'success'   => true,
            'message'   => 'Search queued successfully.',
            'search_id' => $searchId,
        ];
    }

    /**
     * Refresh status from API for one search.
     */
    public function refresh_status($searchId, $staffId = null, $source = 'manual_refresh')
    {
        $search = $this->get($searchId);
        if (!$search) {
            return ['success' => false, 'message' => 'Search not found.'];
        }

        if (empty($search['external_job_id'])) {
            return ['success' => false, 'message' => 'No external job ID to refresh.'];
        }

        if (in_array($search['status'], nj_court_search_terminal_statuses(), true) && $source === 'cron') {
            return ['success' => true, 'message' => 'Already terminal.'];
        }

        $this->load->library('nj_court_search/nj_court_api_client');
        $result = $this->nj_court_api_client->get_search_status($search['external_job_id']);

        $this->update_search($searchId, [
            'last_checked_at' => date('Y-m-d H:i:s'),
        ]);

        if ($source === 'manual_refresh') {
            $this->add_event($searchId, 'manual_refresh', [
                'old_status' => $search['status'],
                'new_status' => $search['status'],
                'staff_id'   => $staffId,
            ]);
        }

        if (!$result['success']) {
            // Do not corrupt local status on API outage
            $this->update_search($searchId, [
                'next_poll_at' => nj_court_search_compute_next_poll_at($search),
            ]);

            return [
                'success' => false,
                'message' => isset($result['message']) ? $result['message'] : 'Status refresh failed.',
            ];
        }

        return $this->apply_remote_status_payload($searchId, $result['data'], $staffId);
    }

    /**
     * Apply remote status/result payload to local row.
     */
    public function apply_remote_status_payload($searchId, $data, $staffId = null)
    {
        $search = $this->get($searchId);
        if (!$search) {
            return ['success' => false, 'message' => 'Search not found.'];
        }

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid remote payload.'];
        }

        $remoteStatus = null;
        if (isset($data['status'])) {
            $remoteStatus = $data['status'];
        } elseif (isset($data['data']['status'])) {
            $remoteStatus = $data['data']['status'];
        }

        $mapped = nj_court_search_map_remote_status($remoteStatus);
        if (!$mapped) {
            $this->update_search($searchId, [
                'next_poll_at' => nj_court_search_compute_next_poll_at($search),
            ]);

            return ['success' => false, 'message' => 'Unrecognized remote status.'];
        }

        if (!nj_court_search_can_transition($search['status'], $mapped)) {
            // Idempotent same-status OK handled in can_transition; reject illegal moves.
            nj_court_search_log(
                'Remote status ignored for search #' . (int) $searchId
                . ' (local=' . $search['status'] . ', remote=' . $mapped . ')'
            );

            return [
                'success' => false,
                'message' => 'Remote status is not a valid transition from the current state.',
            ];
        }

        // Fetch results when becoming completed / no_results
        if (in_array($mapped, ['completed', 'no_results'], true)) {
            $fetch = $this->fetch_and_store_results($searchId, $search['external_job_id']);
            if ($fetch['success']) {
                $mapped = ((int) $fetch['result_count'] === 0) ? 'no_results' : 'completed';
                if (!nj_court_search_can_transition($search['status'], $mapped)
                    && $search['status'] !== $mapped) {
                    // Prefer completed/no_results that matches local eligibility
                    if (nj_court_search_can_transition($search['status'], 'completed')) {
                        $mapped = ((int) $fetch['result_count'] === 0) ? 'no_results' : 'completed';
                    }
                }
            } elseif ($mapped === 'completed') {
                // Keep requesting results; do not flip to terminal without payload when fetch fails
                $this->update_search($searchId, [
                    'next_poll_at' => nj_court_search_compute_next_poll_at($search),
                ]);

                // Still allow processing → completed without results stored once? Prefer wait.
                return [
                    'success' => false,
                    'message' => 'Status completed but results could not be retrieved yet.',
                ];
            }
        }

        $extra = [
            'next_poll_at' => in_array($mapped, nj_court_search_terminal_statuses(), true)
                ? null
                : nj_court_search_compute_next_poll_at($search),
        ];

        if ($mapped === 'failed') {
            $err = null;
            if (isset($data['data']['errorMessage']) && is_string($data['data']['errorMessage'])) {
                $err = $data['data']['errorMessage'];
            } elseif (isset($data['error']) && is_string($data['error'])) {
                $err = $data['error'];
            }
            $extra['error_message'] = $err !== null ? $err : $search['error_message'];
        }

        $transition = $this->transition_status($searchId, $mapped, $extra, 'status_changed', $staffId);
        if (is_array($transition) && empty($transition['success'])) {
            return $transition;
        }

        return ['success' => true, 'message' => 'Status updated.', 'status' => $mapped];
    }

    /**
     * Pull result payload and persist.
     */
    public function fetch_and_store_results($searchId, $jobId)
    {
        $this->load->library('nj_court_search/nj_court_api_client');
        $result = $this->nj_court_api_client->get_search_result($jobId);

        if (!$result['success']) {
            return [
                'success'      => false,
                'message'      => isset($result['message']) ? $result['message'] : 'Result fetch failed.',
                'result_count' => 0,
            ];
        }

        $data = is_array($result['data']) ? $result['data'] : [];
        $inner = isset($data['data']) && is_array($data['data']) ? $data['data'] : $data;

        $caseCount = 0;
        if (isset($inner['caseCount'])) {
            $caseCount = (int) $inner['caseCount'];
        } elseif (isset($inner['results']['cases']) && is_array($inner['results']['cases'])) {
            $caseCount = count($inner['results']['cases']);
        } elseif (isset($inner['results']) && is_array($inner['results'])) {
            $caseCount = count($inner['results']);
        }

        $json = json_encode($inner);
        if ($json === false) {
            return [
                'success'      => false,
                'message'      => 'Unable to encode result payload.',
                'result_count' => 0,
            ];
        }
        $checksum = hash('sha256', $json);

        $existing = $this->get($searchId);
        if ($existing && !empty($existing['result_checksum']) && hash_equals((string) $existing['result_checksum'], $checksum)) {
            // Idempotent — no duplicate result_received event
            return [
                'success'      => true,
                'message'      => 'Results unchanged.',
                'result_count' => (int) $existing['result_count'],
            ];
        }

        $this->update_search($searchId, [
            'result_count'            => $caseCount,
            'result_json'             => $json,
            'result_checksum'         => $checksum,
            'external_result_version' => isset($inner['version']) ? (string) $inner['version'] : null,
        ]);

        $this->add_event($searchId, 'result_received', [
            'event_data' => [
                'result_count' => $caseCount,
                'checksum'     => $checksum,
            ],
            'staff_id' => null,
        ]);

        return [
            'success'      => true,
            'message'      => 'Results stored.',
            'result_count' => $caseCount,
        ];
    }

    /**
     * Cron batch poller with claim/lock via conditional next_poll_at update.
     */
    public function poll_pending_searches()
    {
        $batch = (int) get_option('nj_court_search_poll_batch_size');
        if ($batch < 1) {
            $batch = 20;
        }
        if ($batch > 100) {
            $batch = 100;
        }

        // Do not poll submission_failed forever without a job id; only queued/processing.
        $statuses = ['queued', 'processing'];
        $now = date('Y-m-d H:i:s');
        $claimUntil = date('Y-m-d H:i:s', time() + 120);

        $this->db->select('id');
        $this->db->from($this->table());
        $this->db->where_in('status', $statuses);
        $this->db->where('external_job_id IS NOT NULL', null, false);
        $this->db->group_start();
        $this->db->where('next_poll_at <=', $now);
        $this->db->or_where('next_poll_at IS NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('next_poll_at', 'ASC');
        $this->db->limit($batch);

        $candidates = $this->db->get()->result_array();
        $processed = 0;

        foreach ($candidates as $candidate) {
            $id = (int) $candidate['id'];

            // Lightweight claim: only one cron worker wins if next_poll_at still due.
            $this->db->where('id', $id);
            $this->db->group_start();
            $this->db->where('next_poll_at <=', $now);
            $this->db->or_where('next_poll_at IS NULL', null, false);
            $this->db->group_end();
            $this->db->where_in('status', $statuses);
            $this->db->update($this->table(), [
                'next_poll_at' => $claimUntil,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            if ($this->db->affected_rows() < 1) {
                continue;
            }

            try {
                $this->refresh_status($id, null, 'cron');
                $processed++;
            } catch (Exception $e) {
                nj_court_search_log('Cron poll error for search #' . $id . ': ' . $e->getMessage());
                $row = $this->get($id);
                if ($row) {
                    $this->update_search($id, [
                        'next_poll_at' => nj_court_search_compute_next_poll_at($row),
                    ]);
                }
            }
        }

        return $processed;
    }

    /**
     * Retry failed search via API when available; otherwise resubmit.
     */
    public function retry_search($searchId, $staffId)
    {
        $search = $this->get($searchId);
        if (!$search) {
            return ['success' => false, 'message' => 'Search not found.'];
        }

        if (!in_array($search['status'], nj_court_search_retryable_statuses(), true)) {
            return ['success' => false, 'message' => 'Search is not eligible for retry.'];
        }

        $this->add_event($searchId, 'retry_requested', [
            'old_status' => $search['status'],
            'new_status' => $search['status'],
            'staff_id'   => $staffId,
        ]);

        $this->update_search($searchId, [
            'retry_count' => ((int) $search['retry_count']) + 1,
        ]);

        if (!empty($search['external_job_id'])) {
            $this->load->library('nj_court_search/nj_court_api_client');
            $api = $this->nj_court_api_client->retry_search($search['external_job_id']);

            if ($api['success']) {
                $applied = $this->apply_remote_status_payload($searchId, $api['data'], $staffId);
                if ($applied['success']) {
                    return ['success' => true, 'message' => 'Retry accepted by API.'];
                }
            }

            // If API has no retry endpoint (404), fall through to resubmit
            $code = isset($api['status_code']) ? (int) $api['status_code']
                : (isset($api['http_code']) ? (int) $api['http_code'] : 0);
            if ($code !== 404 && !empty($api) && empty($api['success'])) {
                return [
                    'success' => false,
                    'message' => isset($api['message']) ? $api['message'] : 'Retry failed.',
                ];
            }
        }

        // Prepare local retry via allowed transition failed|submission_failed → draft
        $prep = $this->transition_status($searchId, 'draft', [
            'idempotency_key' => nj_court_search_generate_uuid(),
            'error_code'      => null,
            'error_message'   => null,
            'external_job_id' => null,
            '_event_data'     => ['reason' => 'retry_reset'],
        ], 'status_changed', $staffId);

        if (is_array($prep) && empty($prep['success'])) {
            return $prep;
        }

        return $this->submit_to_api($searchId);
    }

    /**
     * Cancel via API when possible; otherwise mark cancelled locally for draft/submission_failed.
     */
    public function cancel_search($searchId, $staffId)
    {
        $search = $this->get($searchId);
        if (!$search) {
            return ['success' => false, 'message' => 'Search not found.'];
        }

        if (!in_array($search['status'], nj_court_search_cancellable_statuses(), true)) {
            return ['success' => false, 'message' => 'Search is not eligible for cancel.'];
        }

        if (!nj_court_search_can_transition($search['status'], 'cancelled')) {
            return ['success' => false, 'message' => 'Cancel is not allowed from the current status.'];
        }

        $this->add_event($searchId, 'cancel_requested', [
            'old_status' => $search['status'],
            'new_status' => $search['status'],
            'staff_id'   => $staffId,
        ]);

        if (!empty($search['external_job_id']) && $search['status'] === 'queued') {
            $this->load->library('nj_court_search/nj_court_api_client');
            $api = $this->nj_court_api_client->cancel_search($search['external_job_id']);

            if ($api['success']) {
                $transition = $this->transition_status($searchId, 'cancelled', [], 'status_changed', $staffId);
                if (is_array($transition) && empty($transition['success'])) {
                    return $transition;
                }

                return ['success' => true, 'message' => 'Search cancelled.'];
            }

            if (isset($api['status_code']) && (int) $api['status_code'] !== 404) {
                return [
                    'success' => false,
                    'message' => isset($api['message']) ? $api['message'] : 'Cancel failed.',
                ];
            }
            // Also support legacy http_code key
            if (isset($api['http_code']) && (int) $api['http_code'] !== 404) {
                return [
                    'success' => false,
                    'message' => isset($api['message']) ? $api['message'] : 'Cancel failed.',
                ];
            }
        }

        $transition = $this->transition_status($searchId, 'cancelled', [], 'status_changed', $staffId);
        if (is_array($transition) && empty($transition['success'])) {
            return $transition;
        }

        return ['success' => true, 'message' => 'Search cancelled.'];
    }

    /**
     * Store inbound webhook for idempotent processing.
     *
     * @return array{accepted:bool,duplicate?:bool,id?:int,error?:string}
     */
    public function record_webhook_event($externalEventId, $externalJobId, $payloadHash, $signatureTimestamp)
    {
        $existing = $this->db->where('external_event_id', $externalEventId)
            ->get($this->webhooks_table())
            ->row_array();

        if ($existing) {
            return ['accepted' => true, 'duplicate' => true, 'id' => (int) $existing['id']];
        }

        $ok = $this->db->insert($this->webhooks_table(), [
            'external_event_id'   => $externalEventId,
            'external_job_id'     => $externalJobId,
            'payload_hash'        => $payloadHash,
            'signature_timestamp' => $signatureTimestamp,
            'processed'           => 0,
            'received_at'         => date('Y-m-d H:i:s'),
        ]);

        if (!$ok) {
            // Likely unique-key race — treat as duplicate
            $existing = $this->db->where('external_event_id', $externalEventId)
                ->get($this->webhooks_table())
                ->row_array();
            if ($existing) {
                return ['accepted' => true, 'duplicate' => true, 'id' => (int) $existing['id']];
            }

            return ['accepted' => false, 'error' => 'insert_failed'];
        }

        return ['accepted' => true, 'duplicate' => false, 'id' => (int) $this->db->insert_id()];
    }

    public function mark_webhook_processed($id, $error = null)
    {
        $this->db->where('id', (int) $id);
        $this->db->update($this->webhooks_table(), [
            'processed'         => $error ? 0 : 1,
            'processing_error'  => $error,
            'processed_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Validate linked Perfex records exist.
     * When both client and contact are supplied, contact must belong to that client.
     */
    public function validate_links($leadId, $clientId, $contactId)
    {
        $errors = [];

        if ($leadId) {
            $this->db->where('id', (int) $leadId);
            if ($this->db->count_all_results(db_prefix() . 'leads') === 0) {
                $errors[] = 'Invalid lead.';
            }
        }

        if ($clientId) {
            $this->db->where('userid', (int) $clientId);
            if ($this->db->count_all_results(db_prefix() . 'clients') === 0) {
                $errors[] = 'Invalid customer.';
            }
        }

        if ($contactId) {
            $this->db->where('id', (int) $contactId);
            $contact = $this->db->get(db_prefix() . 'contacts')->row_array();
            if (!$contact) {
                $errors[] = 'Invalid contact.';
            } elseif ($clientId && (int) $contact['userid'] !== (int) $clientId) {
                $errors[] = 'Contact does not belong to the selected customer.';
            }
        }

        return $errors;
    }

    /**
     * Purge sensitive result_json for expired terminal searches.
     * Does not delete search rows or audit events. Idempotent.
     *
     * @return int Number of records purged this run
     */
    public function purge_expired_results()
    {
        if (!nj_court_search_retention_is_enabled()) {
            return 0;
        }

        $cutoff = nj_court_search_retention_cutoff();
        if (!$cutoff) {
            return 0;
        }

        $batch = (int) get_option('nj_court_search_retention_batch_size');
        if ($batch < 1) {
            $batch = 50;
        }
        if ($batch > 200) {
            $batch = 200;
        }

        $statuses = nj_court_search_retention_purge_statuses();

        $this->db->from($this->table());
        $this->db->where_in('status', $statuses);
        $this->db->where('result_purged_at IS NULL', null, false);
        $this->db->where('result_json IS NOT NULL', null, false);
        $this->db->where('result_json !=', '');
        $this->db->where('completed_at IS NOT NULL', null, false);
        $this->db->where('completed_at <=', $cutoff);
        $this->db->order_by('completed_at', 'ASC');
        $this->db->limit($batch);

        $rows = $this->db->get()->result_array();
        $purged = 0;
        $now = date('Y-m-d H:i:s');
        $placeholder = nj_court_search_purged_result_placeholder();

        foreach ($rows as $row) {
            if (!nj_court_search_record_is_purge_eligible($row, $cutoff)) {
                continue;
            }

            // Atomic claim: only purge if still unpurged
            $this->db->where('id', (int) $row['id']);
            $this->db->where('result_purged_at IS NULL', null, false);
            $this->db->where('result_json IS NOT NULL', null, false);
            $this->db->update($this->table(), [
                'result_json'      => $placeholder,
                'result_purged_at' => $now,
                'updated_at'       => $now,
            ]);

            if ($this->db->affected_rows() < 1) {
                continue;
            }

            // Single purge event — skip if already recorded (belt and suspenders)
            $this->db->where('search_id', (int) $row['id']);
            $this->db->where('event_type', 'result_retention_purged');
            $exists = $this->db->count_all_results($this->events_table());
            if ($exists === 0) {
                $this->add_event((int) $row['id'], 'result_retention_purged', [
                    'staff_id'   => null,
                    'old_status' => $row['status'],
                    'new_status' => $row['status'],
                    'event_data' => [
                        'result_count' => (int) $row['result_count'],
                        'checksum'     => $row['result_checksum'],
                        'purged_at'    => $now,
                    ],
                ]);
            }

            $purged++;
        }

        return $purged;
    }

    /**
     * Resolve display labels for linked CRM records (form redisplay).
     *
     * @return array{lead_label?:string,client_label?:string,contact_label?:string}
     */
    public function resolve_link_labels($leadId, $clientId, $contactId)
    {
        $out = [];

        if ($leadId) {
            $this->db->select('id, name, company, email');
            $this->db->where('id', (int) $leadId);
            $lead = $this->db->get(db_prefix() . 'leads')->row_array();
            if ($lead) {
                $name = trim(($lead['name'] ?? '') . (empty($lead['company']) ? '' : ' (' . $lead['company'] . ')'));
                $out['lead_label'] = $name !== '' ? $name : ('Lead #' . (int) $leadId);
            }
        }

        if ($clientId) {
            $this->db->select('userid, company');
            $this->db->where('userid', (int) $clientId);
            $client = $this->db->get(db_prefix() . 'clients')->row_array();
            if ($client) {
                $out['client_label'] = !empty($client['company'])
                    ? $client['company']
                    : ('Customer #' . (int) $clientId);
            }
        }

        if ($contactId) {
            $this->db->select('id, firstname, lastname, email, userid');
            $this->db->where('id', (int) $contactId);
            $contact = $this->db->get(db_prefix() . 'contacts')->row_array();
            if ($contact) {
                $out['contact_label'] = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
                if ($out['contact_label'] === '') {
                    $out['contact_label'] = 'Contact #' . (int) $contactId;
                }
                if (!empty($contact['email'])) {
                    $out['contact_label'] .= ' - ' . $contact['email'];
                }
            }
        }

        return $out;
    }

    /**
     * AJAX search leads (authorized by nj_court_search create/view).
     */
    public function search_leads($q, $limit = 20)
    {
        $q = trim((string) $q);
        $limit = max(1, min(50, (int) $limit));
        $this->db->select('id, name, company, email, phonenumber');
        $this->db->from(db_prefix() . 'leads');
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('name', $q);
            $this->db->or_like('company', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('phonenumber', $q);
            if (ctype_digit($q)) {
                $this->db->or_where('id', (int) $q);
            }
            $this->db->group_end();
        }
        $this->db->order_by('name', 'ASC');
        $this->db->limit($limit);
        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $row) {
            $name = trim(($row['name'] ?? '') . (empty($row['company']) ? '' : ' (' . $row['company'] . ')'));
            $out[] = [
                'id'      => (int) $row['id'],
                'name'    => $name !== '' ? $name : ('Lead #' . (int) $row['id']),
                'subtext' => $row['email'] ?? '',
            ];
        }

        return $out;
    }

    /**
     * AJAX search customers.
     */
    public function search_customers($q, $limit = 20)
    {
        $q = trim((string) $q);
        $limit = max(1, min(50, (int) $limit));
        $this->db->select('userid, company, vat');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where('active', 1);
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('company', $q);
            $this->db->or_like('vat', $q);
            if (ctype_digit($q)) {
                $this->db->or_where('userid', (int) $q);
            }
            $this->db->group_end();
        }
        $this->db->order_by('company', 'ASC');
        $this->db->limit($limit);
        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id'      => (int) $row['userid'],
                'name'    => !empty($row['company']) ? $row['company'] : ('Customer #' . (int) $row['userid']),
                'subtext' => $row['vat'] ?? '',
            ];
        }

        return $out;
    }

    /**
     * AJAX search contacts, optionally limited to a customer.
     */
    public function search_contacts($q, $clientId = null, $limit = 20)
    {
        $q = trim((string) $q);
        $limit = max(1, min(50, (int) $limit));
        $this->db->select(db_prefix() . 'contacts.id, firstname, lastname, email, userid');
        $this->db->from(db_prefix() . 'contacts');
        $this->db->where(db_prefix() . 'contacts.active', 1);
        if ($clientId) {
            $this->db->where('userid', (int) $clientId);
        }
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('firstname', $q);
            $this->db->or_like('lastname', $q);
            $this->db->or_like('email', $q);
            if (ctype_digit($q)) {
                $this->db->or_where(db_prefix() . 'contacts.id', (int) $q);
            }
            $this->db->group_end();
        }
        $this->db->order_by('firstname', 'ASC');
        $this->db->limit($limit);
        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $row) {
            $name = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
            $out[] = [
                'id'      => (int) $row['id'],
                'name'    => $name !== '' ? $name : ('Contact #' . (int) $row['id']),
                'subtext' => $row['email'] ?? '',
            ];
        }

        return $out;
    }
}
