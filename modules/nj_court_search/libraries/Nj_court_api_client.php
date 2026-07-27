<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reusable HTTP client for the external NJ Court REST API.
 *
 * Uses cURL (Perfex/core convention). Does not log API keys, DOBs, or secrets.
 *
 * Normalized response:
 * [
 *   'success'     => bool,
 *   'status_code' => int|null,
 *   'data'        => array|null,
 *   'error_code'  => string|null,
 *   'message'     => string,
 * ]
 *
 * Mock mode (development only via nj_court_search_mock_mode) never contacts
 * VMware, Redis, BullMQ, or the external API.
 */
class Nj_court_api_client
{
    /** @var object */
    protected $ci;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $apiKey;

    /** @var int */
    protected $timeout;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->reload_config();
    }

    public function reload_config()
    {
        $this->baseUrl = rtrim((string) get_option('nj_court_search_api_base_url'), '/');
        $this->apiKey  = nj_court_search_get_secret('nj_court_search_api_key');
        $timeout       = (int) get_option('nj_court_search_api_timeout');
        $this->timeout = $timeout > 0 ? $timeout : 30;
    }

    /**
     * GET /health readiness check (does not require API key).
     */
    public function test_connection()
    {
        if (nj_court_search_mock_mode_enabled()) {
            return $this->success('Mock connection successful.', ['status' => 'ok', 'mock' => true], 200);
        }

        if ($this->baseUrl === '') {
            return $this->failure('API base URL is not configured.', null, null, 'config_missing');
        }

        $paths = ['/health', '/api/health', '/api/nj/health'];
        $lastError = null;

        foreach ($paths as $path) {
            $result = $this->request('GET', $path, null, [], false);
            if ($result['success']) {
                return $this->success('Connection successful.', $result['data'], $result['status_code']);
            }
            $lastError = $result;
            if ($result['status_code'] === null || (int) $result['status_code'] !== 404) {
                break;
            }
        }

        return $lastError ?: $this->failure('Unable to reach the NJ Court API.', null, null, 'unreachable');
    }

    public function submit_search(array $payload)
    {
        $body = [
            'firstName' => isset($payload['firstName']) ? $payload['firstName'] : '',
            'lastName'  => isset($payload['lastName']) ? $payload['lastName'] : '',
            'dob'       => isset($payload['dob']) ? $payload['dob'] : '',
        ];

        if (!empty($payload['referenceId'])) {
            $body['referenceId'] = $payload['referenceId'];
        }
        if (!empty($payload['middleName'])) {
            $body['middleName'] = $payload['middleName'];
        }
        if (!empty($payload['suffix'])) {
            $body['suffix'] = $payload['suffix'];
        }

        $headers = [];
        if (!empty($payload['idempotencyKey'])) {
            $headers[] = 'Idempotency-Key: ' . $payload['idempotencyKey'];
        }

        return $this->request('POST', '/api/nj/search', $body, $headers, true);
    }

    public function get_search_status($jobId)
    {
        $jobId = rawurlencode((string) $jobId);

        return $this->request('GET', '/api/nj/search/' . $jobId, null, [], true);
    }

    public function get_search_result($jobId)
    {
        $jobId = rawurlencode((string) $jobId);

        return $this->request('GET', '/api/nj/result/' . $jobId, null, [], true);
    }

    public function retry_search($jobId)
    {
        $jobId = rawurlencode((string) $jobId);

        return $this->request('POST', '/api/nj/search/' . $jobId . '/retry', null, [], true);
    }

    public function cancel_search($jobId)
    {
        $jobId = rawurlencode((string) $jobId);

        return $this->request('POST', '/api/nj/search/' . $jobId . '/cancel', null, [], true);
    }

    /**
     * @param string     $method
     * @param string     $path
     * @param array|null $body
     * @param array      $extraHeaders
     * @param bool       $requireApiKey
     * @return array
     */
    protected function request($method, $path, $body = null, array $extraHeaders = [], $requireApiKey = true)
    {
        if (nj_court_search_mock_mode_enabled()) {
            return $this->mock_response($method, $path, $body);
        }

        if ($this->baseUrl === '') {
            return $this->failure('API base URL is not configured.', null, null, 'config_missing');
        }

        if ($requireApiKey && $this->apiKey === '') {
            return $this->failure('API key is not configured.', null, null, 'config_missing');
        }

        $path = '/' . ltrim($path, '/');
        $url  = $this->baseUrl . $path;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];
        if ($this->apiKey !== '') {
            $headers[] = 'x-api-key: ' . $this->apiKey;
        }
        $headers = array_merge($headers, $extraHeaders);

        $encodedBody = null;
        if ($body !== null) {
            $encodedBody = json_encode($body);
            if ($encodedBody === false) {
                return $this->failure('Unable to encode request payload.', null, null, 'json_encode_error');
            }
        }

        $ch = curl_init();
        if ($ch === false) {
            return $this->failure('Unable to initialize HTTP client.', null, null, 'curl_init');
        }

        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if ($encodedBody !== null) {
            $opts[CURLOPT_POSTFIELDS] = $encodedBody;
        }

        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            nj_court_search_log('HTTP connection error [' . $errno . ']: ' . $error);
            return $this->failure('Unable to connect to the NJ Court API.', null, null, 'connection_error');
        }

        $decoded = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                nj_court_search_log('Invalid JSON response (HTTP ' . $httpCode . ') for ' . $path);
                return $this->failure(
                    'The NJ Court API returned an invalid response.',
                    null,
                    $httpCode,
                    'invalid_json'
                );
            }
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return $this->success('OK', is_array($decoded) ? $decoded : null, $httpCode);
        }

        $message = 'NJ Court API request failed.';
        if (is_array($decoded)) {
            if (!empty($decoded['error']) && is_string($decoded['error'])) {
                $message = $decoded['error'];
            } elseif (!empty($decoded['message']) && is_string($decoded['message'])) {
                $message = $decoded['message'];
            }
        }

        nj_court_search_log('API HTTP ' . $httpCode . ' on ' . $method . ' ' . $path . ': ' . $message);

        return $this->failure($message, is_array($decoded) ? $decoded : null, $httpCode, 'http_' . $httpCode);
    }

    /**
     * Development-only mock responses. Never calls external systems.
     */
    protected function mock_response($method, $path, $body)
    {
        $scenario = get_option('nj_court_search_mock_scenario');
        if ($scenario === '' || $scenario === false || $scenario === null) {
            $scenario = 'success_flow';
        }

        if ($scenario === 'timeout') {
            return $this->failure('Unable to connect to the NJ Court API.', null, null, 'connection_error');
        }
        if ($scenario === 'malformed') {
            return $this->failure('The NJ Court API returned an invalid response.', null, 200, 'invalid_json');
        }
        if ($scenario === 'submission_failure') {
            if (stripos($path, '/api/nj/search') !== false && strtoupper($method) === 'POST'
                && stripos($path, '/retry') === false && stripos($path, '/cancel') === false) {
                return $this->failure('Mock submission rejected.', null, 400, 'http_400');
            }
        }

        if (preg_match('#/health$#', $path)) {
            return $this->success('OK', ['status' => 'ok', 'mock' => true], 200);
        }

        if (stripos($path, '/api/nj/search') !== false && strtoupper($method) === 'POST'
            && stripos($path, '/retry') === false && stripos($path, '/cancel') === false) {
            $jobId = 'mock-' . substr(hash('sha256', json_encode($body) . microtime(true)), 0, 12);

            return $this->success('OK', [
                'success' => true,
                'jobId'   => $jobId,
                'status'  => 'queued',
            ], 202);
        }

        if (preg_match('#/api/nj/search/([^/]+)$#', $path, $m) && strtoupper($method) === 'GET') {
            $status = 'queued';
            if ($scenario === 'processing') {
                $status = 'processing';
            } elseif ($scenario === 'completed') {
                $status = 'completed';
            } elseif ($scenario === 'no_results') {
                $status = 'completed';
            } elseif ($scenario === 'failed') {
                $status = 'failed';
            } elseif ($scenario === 'success_flow') {
                $status = 'completed';
            }

            return $this->success('OK', [
                'success' => true,
                'jobId'   => rawurldecode($m[1]),
                'status'  => $status,
                'data'    => ['status' => $status],
            ], 200);
        }

        if (preg_match('#/api/nj/result/([^/]+)$#', $path, $m) && strtoupper($method) === 'GET') {
            if ($scenario === 'no_results') {
                return $this->success('OK', [
                    'success' => true,
                    'jobId'   => rawurldecode($m[1]),
                    'status'  => 'completed',
                    'data'    => [
                        'caseCount' => 0,
                        'results'   => ['cases' => []],
                    ],
                ], 200);
            }

            return $this->success('OK', [
                'success' => true,
                'jobId'   => rawurldecode($m[1]),
                'status'  => 'completed',
                'data'    => [
                    'caseCount' => 1,
                    'results'   => [
                        'cases' => [[
                            'caseNumber' => 'MOCK-2026-0001',
                            'court'      => 'Mock Municipal Court',
                            'status'     => 'Disposed',
                            'note'       => 'Development mock result — not a real court record',
                        ]],
                    ],
                ],
            ], 200);
        }

        if (stripos($path, '/retry') !== false) {
            return $this->success('OK', ['success' => true, 'status' => 'queued'], 200);
        }
        if (stripos($path, '/cancel') !== false) {
            return $this->success('OK', ['success' => true, 'status' => 'cancelled'], 200);
        }

        return $this->failure('Mock route not handled.', null, 404, 'http_404');
    }

    protected function success($message, $data = null, $statusCode = 200)
    {
        return [
            'success'     => true,
            'status_code' => $statusCode,
            'data'        => $data,
            'error_code'  => null,
            'message'     => $message,
            // Backward-compatible alias used by older call sites
            'http_code'   => $statusCode,
        ];
    }

    protected function failure($message, $data = null, $statusCode = null, $errorCode = 'error')
    {
        return [
            'success'     => false,
            'status_code' => $statusCode,
            'data'        => $data,
            'error_code'  => $errorCode,
            'message'     => $message,
            'http_code'   => $statusCode === null ? 0 : $statusCode,
        ];
    }
}
