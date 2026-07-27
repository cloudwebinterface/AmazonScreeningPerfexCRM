<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Uninstall behavior.
 *
 * Search history is NEVER deleted unless the administrator explicitly set
 * option nj_court_search_purge_on_uninstall = 1 before uninstalling.
 *
 * Deactivation alone never deletes data.
 */

$CI = &get_instance();
$prefix = db_prefix();
$purge = get_option('nj_court_search_purge_on_uninstall') === '1';

if ($purge) {
    if ($CI->db->table_exists($prefix . 'nj_court_search_events')) {
        $CI->db->query('DROP TABLE IF EXISTS `' . $prefix . 'nj_court_search_events`');
    }
    if ($CI->db->table_exists($prefix . 'nj_court_webhook_events')) {
        $CI->db->query('DROP TABLE IF EXISTS `' . $prefix . 'nj_court_webhook_events`');
    }
    if ($CI->db->table_exists($prefix . 'nj_court_searches')) {
        $CI->db->query('DROP TABLE IF EXISTS `' . $prefix . 'nj_court_searches`');
    }
}

$options = [
    'nj_court_search_enabled',
    'nj_court_search_api_base_url',
    'nj_court_search_api_key',
    'nj_court_search_api_timeout',
    'nj_court_search_poll_interval',
    'nj_court_search_poll_batch_size',
    'nj_court_search_webhook_secret',
    'nj_court_search_webhook_tolerance',
    'nj_court_search_webhook_enabled',
    'nj_court_search_cron_polling_enabled',
    'nj_court_search_result_retention_days',
    'nj_court_search_retention_batch_size',
    'nj_court_search_role_restrictions',
    'nj_court_search_purge_on_uninstall',
    'nj_court_search_api_key_configured',
    'nj_court_search_webhook_secret_configured',
    'nj_court_search_mock_mode',
    'nj_court_search_mock_scenario',
];

foreach ($options as $option) {
    delete_option($option);
}
