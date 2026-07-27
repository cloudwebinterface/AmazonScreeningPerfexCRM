<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Permission wrapper matching Perfex feature/capability model.
 * Conceptual names: nj_court_search_view → staff_can('view', 'nj_court_search')
 */
function nj_court_search_staff_can($capability, $staff_id = '')
{
    return staff_can($capability, NJ_COURT_SEARCH_MODULE_NAME, $staff_id);
}

/**
 * UUID v4 for idempotency keys.
 */
function nj_court_search_generate_uuid()
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * All known local statuses.
 */
function nj_court_search_statuses()
{
    return [
        'draft',
        'submission_failed',
        'queued',
        'processing',
        'completed',
        'no_results',
        'failed',
        'cancelled',
    ];
}

/**
 * Statuses that may be polled / refreshed.
 */
function nj_court_search_pollable_statuses()
{
    return ['queued', 'processing', 'submission_failed'];
}

/**
 * Terminal statuses (no further polling).
 */
function nj_court_search_terminal_statuses()
{
    return ['completed', 'no_results', 'failed', 'cancelled'];
}

/**
 * Statuses eligible for retry.
 */
function nj_court_search_retryable_statuses()
{
    return ['failed', 'submission_failed'];
}

/**
 * Statuses eligible for cancel.
 */
function nj_court_search_cancellable_statuses()
{
    return ['queued', 'draft', 'submission_failed'];
}

/**
 * Allowed local status transitions.
 * Same-status updates are always allowed (idempotent refresh).
 *
 * @return array<string, string[]>
 */
function nj_court_search_allowed_transitions()
{
    return [
        'draft'             => ['queued', 'submission_failed', 'cancelled'],
        'submission_failed' => ['draft', 'queued', 'cancelled'],
        'queued'            => ['processing', 'completed', 'no_results', 'failed', 'cancelled'],
        'processing'        => ['queued', 'completed', 'no_results', 'failed', 'cancelled'],
        'completed'         => [],
        'no_results'        => [],
        'failed'            => ['draft', 'queued'], // retry workflow only
        'cancelled'         => [],
    ];
}

/**
 * Whether moving from $from to $to is allowed.
 */
function nj_court_search_can_transition($from, $to)
{
    $from = (string) $from;
    $to   = (string) $to;

    if ($from === $to) {
        return true;
    }

    $map = nj_court_search_allowed_transitions();
    if (!isset($map[$from])) {
        return false;
    }

    return in_array($to, $map[$from], true);
}

/**
 * Development mock mode may only run outside production.
 */
function nj_court_search_mock_mode_allowed()
{
    return defined('ENVIRONMENT') && ENVIRONMENT !== 'production';
}

/**
 * Whether mock API mode is currently active.
 */
function nj_court_search_mock_mode_enabled()
{
    return nj_court_search_mock_mode_allowed()
        && get_option('nj_court_search_mock_mode') === '1';
}

/**
 * Whether result retention purge is enabled.
 */
function nj_court_search_retention_is_enabled($days = null)
{
    if ($days === null) {
        $days = get_option('nj_court_search_result_retention_days');
    }
    if ($days === '' || $days === null || $days === false) {
        return false;
    }

    return (int) $days > 0;
}

/**
 * Cutoff datetime for retention (UTC-style app timezone via date()).
 *
 * @return string|null Y-m-d H:i:s or null when disabled
 */
function nj_court_search_retention_cutoff($days = null, $now = null)
{
    if (!nj_court_search_retention_is_enabled($days)) {
        return null;
    }
    $days = (int) ($days === null ? get_option('nj_court_search_result_retention_days') : $days);
    $ts = $now !== null ? (int) $now : time();

    return date('Y-m-d H:i:s', $ts - ($days * 86400));
}

/**
 * Statuses allowed for retention purge (terminal only).
 * Failed is included only when a sensitive result_json payload exists.
 */
function nj_court_search_retention_purge_statuses()
{
    return ['completed', 'no_results', 'failed', 'cancelled'];
}

/**
 * Whether a search row is eligible for result payload purge.
 *
 * @param array $row Must include status, result_json, result_purged_at, completed_at
 * @param string $cutoff Y-m-d H:i:s
 */
function nj_court_search_record_is_purge_eligible(array $row, $cutoff)
{
    $status = isset($row['status']) ? $row['status'] : '';
    if (!in_array($status, nj_court_search_retention_purge_statuses(), true)) {
        return false;
    }

    // Never purge active / non-terminal workflow states (belt and suspenders)
    if (in_array($status, ['draft', 'queued', 'processing', 'submission_failed'], true)) {
        return false;
    }

    if (!empty($row['result_purged_at'])) {
        return false;
    }

    $json = isset($row['result_json']) ? $row['result_json'] : null;
    if ($json === null || $json === '') {
        return false;
    }

    // Already a purged placeholder
    if (is_string($json)) {
        $decoded = json_decode($json, true);
        if (is_array($decoded) && !empty($decoded['_purged'])) {
            return false;
        }
    }

    $completedAt = !empty($row['completed_at']) ? $row['completed_at'] : null;
    if (!$completedAt) {
        return false;
    }

    return $completedAt <= $cutoff;
}

/**
 * Placeholder JSON stored after retention purge (non-sensitive).
 */
function nj_court_search_purged_result_placeholder()
{
    return json_encode([
        '_purged' => true,
        'message' => 'Sensitive result payload removed by retention policy',
    ]);
}

/**
 * Constant-time webhook signature compare (hex, case-insensitive).
 */
function nj_court_search_signatures_match($expectedHex, $provided)
{
    $expected = strtolower(trim((string) $expectedHex));
    $provided = strtolower(trim((string) $provided));

    if ($expected === '' || $provided === '' || strlen($expected) !== strlen($provided)) {
        return false;
    }

    return hash_equals($expected, $provided);
}

/**
 * Bootstrap badge HTML for a status.
 */
function nj_court_search_status_badge($status)
{
    $map = [
        'draft'              => 'default',
        'submission_failed'  => 'warning',
        'queued'             => 'info',
        'processing'         => 'primary',
        'completed'          => 'success',
        'no_results'         => 'success',
        'failed'             => 'danger',
        'cancelled'          => 'default',
    ];

    $class = isset($map[$status]) ? $map[$status] : 'default';
    $label = _l('nj_court_search_status_' . $status);
    if ($label === 'nj_court_search_status_' . $status) {
        $label = ucfirst(str_replace('_', ' ', $status));
    }

    return '<span class="label label-' . $class . '">' . htmlspecialchars($label) . '</span>';
}

/**
 * Format DOB for display; mask unless view_sensitive.
 */
function nj_court_search_format_dob($dob, $allow_sensitive = null)
{
    if ($allow_sensitive === null) {
        $allow_sensitive = nj_court_search_staff_can('view_sensitive');
    }

    if (empty($dob) || $dob === '0000-00-00') {
        return '—';
    }

    if ($allow_sensitive) {
        return _d($dob);
    }

    // Mask as **/**/YYYY
    $ts = strtotime($dob);
    if (!$ts) {
        return '**/****/****';
    }

    return '**/**/' . date('Y', $ts);
}

/**
 * Decrypt a module secret option. Returns empty string on failure.
 */
function nj_court_search_get_secret($option_name)
{
    $stored = get_option($option_name);
    if ($stored === '' || $stored === null || $stored === false) {
        return '';
    }

    $CI = &get_instance();
    try {
        $plain = $CI->encryption->decrypt($stored);
        if ($plain === false || $plain === null) {
            // Treat undecryptable values as unset — do not return possible plaintext leftovers.
            return '';
        }

        return (string) $plain;
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Encrypt and store a secret option. Never logs the value.
 */
function nj_court_search_set_secret($option_name, $plaintext)
{
    $CI = &get_instance();
    $encrypted = $CI->encryption->encrypt($plaintext);
    update_option($option_name, $encrypted);
}

/**
 * Masked indicator that a secret is configured.
 */
function nj_court_search_secret_mask($configured_option)
{
    if (get_option($configured_option) == '1') {
        return '••••••••••••••••';
    }

    return '';
}

/**
 * Redact sensitive tokens from arbitrary text before logging.
 */
function nj_court_search_redact($text)
{
    if (!is_string($text)) {
        $text = json_encode($text);
    }

    $patterns = [
        '/(api[_-]?key["\']?\s*[:=]\s*["\']?)([^"\'\s,&]+)/i',
        '/(authorization["\']?\s*[:=]\s*["\']?bearer\s+)([^\s"\']+)/i',
        '/(x-api-key["\']?\s*[:=]\s*["\']?)([^"\'\s,&]+)/i',
        '/(webhook[_-]?secret["\']?\s*[:=]\s*["\']?)([^"\'\s,&]+)/i',
        '/\b\d{4}-\d{2}-\d{2}\b/', // DOB-like dates
    ];

    $replacements = [
        '$1[REDACTED]',
        '$1[REDACTED]',
        '$1[REDACTED]',
        '$1[REDACTED]',
        '[DATE_REDACTED]',
    ];

    return preg_replace($patterns, $replacements, $text);
}

/**
 * Safe module log — always redacted. Never write secrets.
 */
function nj_court_search_log($message, $level = 'error')
{
    $message = nj_court_search_redact($message);
    log_message($level, '[nj_court_search] ' . $message);
}

/**
 * Validate DOB: YYYY-MM-DD or Perfex date format, not future, plausible age.
 *
 * @return string|false Normalized Y-m-d or false
 */
function nj_court_search_normalize_dob($input)
{
    if ($input === null || $input === '') {
        return false;
    }

    $input = trim($input);

    // Already Y-m-d
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
        $normalized = $input;
    } else {
        // Perfex datepicker format via to_sql_date when available
        if (function_exists('to_sql_date')) {
            $converted = to_sql_date($input);
            if (!$converted || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $converted)) {
                return false;
            }
            $normalized = $converted;
        } else {
            $ts = strtotime($input);
            if (!$ts) {
                return false;
            }
            $normalized = date('Y-m-d', $ts);
        }
    }

    $parts = explode('-', $normalized);
    if (count($parts) !== 3 || !checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
        return false;
    }

    $today = date('Y-m-d');
    if ($normalized > $today) {
        return false;
    }

    // Reject absurd historical dates (before 1900)
    if ((int) $parts[0] < 1900) {
        return false;
    }

    return $normalized;
}

/**
 * Map remote API status + result count to local status.
 */
function nj_court_search_map_remote_status($remote_status, $result_count = null)
{
    $remote_status = strtolower(trim((string) $remote_status));

    switch ($remote_status) {
        case 'queued':
            return 'queued';
        case 'processing':
            return 'processing';
        case 'completed':
            if ($result_count !== null && (int) $result_count === 0) {
                return 'no_results';
            }

            return 'completed';
        case 'no_results':
            return 'no_results';
        case 'failed':
            return 'failed';
        case 'cancelled':
        case 'canceled':
            return 'cancelled';
        default:
            return null;
    }
}

/**
 * Build HMAC signature payload string: `{timestamp}.{rawBody}`
 * Signature algorithm: HMAC-SHA256 hex digest.
 *
 * Expected headers from external API:
 * - X-NJ-Court-Timestamp: unix seconds
 * - X-NJ-Court-Signature: hex HMAC-SHA256
 * - X-NJ-Court-Event-Id: unique event id (replay protection)
 */
function nj_court_search_compute_webhook_signature($timestamp, $rawBody, $secret)
{
    return hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
}

/**
 * Next poll time with increasing backoff based on age / retry_count.
 */
function nj_court_search_compute_next_poll_at($search)
{
    $base = (int) get_option('nj_court_search_poll_interval');
    if ($base < 15) {
        $base = 15;
    }

    $retry = isset($search['retry_count']) ? (int) $search['retry_count'] : 0;
    $created = isset($search['created_at']) ? strtotime($search['created_at']) : time();
    $ageMinutes = max(0, (int) floor((time() - $created) / 60));

    // Backoff tiers
    $multiplier = 1;
    if ($ageMinutes > 60) {
        $multiplier = 4;
    } elseif ($ageMinutes > 15) {
        $multiplier = 2;
    }

    if ($retry > 0) {
        $multiplier += min(4, $retry);
    }

    $seconds = min(3600, $base * $multiplier);

    return date('Y-m-d H:i:s', time() + $seconds);
}

/**
 * Linked CRM record label for list/detail.
 */
function nj_court_search_linked_record_html($search)
{
    $parts = [];

    if (!empty($search['lead_id'])) {
        $parts[] = '<a href="' . admin_url('leads/index/' . (int) $search['lead_id']) . '">'
            . _l('lead') . ' #' . (int) $search['lead_id'] . '</a>';
    }
    if (!empty($search['client_id'])) {
        $parts[] = '<a href="' . admin_url('clients/client/' . (int) $search['client_id']) . '">'
            . _l('client') . ' #' . (int) $search['client_id'] . '</a>';
    }
    if (!empty($search['contact_id'])) {
        $parts[] = '<a href="' . admin_url('clients/client/' . (int) (!empty($search['client_id']) ? $search['client_id'] : 0)) . '?group=contacts">'
            . _l('contact') . ' #' . (int) $search['contact_id'] . '</a>';
    }

    return $parts ? implode(' | ', $parts) : '—';
}

/**
 * Subject display name.
 */
function nj_court_search_subject_name($search)
{
    $bits = [
        isset($search['first_name']) ? $search['first_name'] : '',
        isset($search['middle_name']) ? $search['middle_name'] : '',
        isset($search['last_name']) ? $search['last_name'] : '',
        isset($search['suffix']) ? $search['suffix'] : '',
    ];

    return trim(preg_replace('/\s+/', ' ', implode(' ', $bits)));
}
