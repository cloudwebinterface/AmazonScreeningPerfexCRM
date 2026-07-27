<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module activation schema and default options.
 * Uses db_prefix() dynamically. Does not drop existing data on re-activation.
 */

$prefix = db_prefix();
$charset = $CI->db->char_set;

/* --------------------------------------------------------------------------
 * Table: nj_court_searches
 * ------------------------------------------------------------------------ */
if (!$CI->db->table_exists($prefix . 'nj_court_searches')) {
    $CI->db->query('CREATE TABLE `' . $prefix . "nj_court_searches` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `external_job_id` VARCHAR(64) NULL DEFAULT NULL,
        `idempotency_key` CHAR(36) NOT NULL,
        `first_name` VARCHAR(100) NOT NULL,
        `middle_name` VARCHAR(100) NULL DEFAULT NULL,
        `last_name` VARCHAR(100) NOT NULL,
        `suffix` VARCHAR(50) NULL DEFAULT NULL,
        `dob` DATE NOT NULL,
        `reference_id` VARCHAR(100) NULL DEFAULT NULL,
        `lead_id` INT(11) NULL DEFAULT NULL,
        `client_id` INT(11) NULL DEFAULT NULL,
        `contact_id` INT(11) NULL DEFAULT NULL,
        `notes` TEXT NULL,
        `status` VARCHAR(32) NOT NULL DEFAULT 'draft',
        `result_count` INT(11) NOT NULL DEFAULT 0,
        `external_result_version` VARCHAR(64) NULL DEFAULT NULL,
        `result_checksum` VARCHAR(64) NULL DEFAULT NULL,
        `result_json` LONGTEXT NULL,
        `result_purged_at` DATETIME NULL DEFAULT NULL,
        `error_code` VARCHAR(64) NULL DEFAULT NULL,
        `error_message` TEXT NULL,
        `submitted_by` INT(11) NULL DEFAULT NULL,
        `submitted_at` DATETIME NULL DEFAULT NULL,
        `processing_started_at` DATETIME NULL DEFAULT NULL,
        `completed_at` DATETIME NULL DEFAULT NULL,
        `last_checked_at` DATETIME NULL DEFAULT NULL,
        `next_poll_at` DATETIME NULL DEFAULT NULL,
        `retry_count` INT(11) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_nj_court_idempotency` (`idempotency_key`),
        KEY `idx_nj_court_external_job_id` (`external_job_id`),
        KEY `idx_nj_court_status` (`status`),
        KEY `idx_nj_court_reference_id` (`reference_id`),
        KEY `idx_nj_court_lead_id` (`lead_id`),
        KEY `idx_nj_court_client_id` (`client_id`),
        KEY `idx_nj_court_contact_id` (`contact_id`),
        KEY `idx_nj_court_next_poll_at` (`next_poll_at`),
        KEY `idx_nj_court_created_at` (`created_at`),
        KEY `idx_nj_court_result_purged_at` (`result_purged_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ';');
}

/* Idempotent schema upgrades for existing installs */
if ($CI->db->table_exists($prefix . 'nj_court_searches')) {
    if (!$CI->db->field_exists('result_purged_at', $prefix . 'nj_court_searches')) {
        $CI->db->query('ALTER TABLE `' . $prefix . 'nj_court_searches`
            ADD COLUMN `result_purged_at` DATETIME NULL DEFAULT NULL AFTER `result_json`,
            ADD KEY `idx_nj_court_result_purged_at` (`result_purged_at`)');
    }
}

/* --------------------------------------------------------------------------
 * Table: nj_court_search_events
 * ------------------------------------------------------------------------ */
if (!$CI->db->table_exists($prefix . 'nj_court_search_events')) {
    $CI->db->query('CREATE TABLE `' . $prefix . "nj_court_search_events` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `search_id` INT(11) NULL DEFAULT NULL,
        `event_type` VARCHAR(64) NOT NULL,
        `old_status` VARCHAR(32) NULL DEFAULT NULL,
        `new_status` VARCHAR(32) NULL DEFAULT NULL,
        `event_data` TEXT NULL,
        `staff_id` INT(11) NULL DEFAULT NULL,
        `external_event_id` VARCHAR(128) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_nj_court_events_search_id` (`search_id`),
        KEY `idx_nj_court_events_type` (`event_type`),
        KEY `idx_nj_court_events_external` (`external_event_id`),
        KEY `idx_nj_court_events_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ';');
}

/* --------------------------------------------------------------------------
 * Table: nj_court_webhook_events
 * ------------------------------------------------------------------------ */
if (!$CI->db->table_exists($prefix . 'nj_court_webhook_events')) {
    $CI->db->query('CREATE TABLE `' . $prefix . "nj_court_webhook_events` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `external_event_id` VARCHAR(128) NOT NULL,
        `external_job_id` VARCHAR(64) NULL DEFAULT NULL,
        `payload_hash` VARCHAR(64) NOT NULL,
        `signature_timestamp` VARCHAR(32) NULL DEFAULT NULL,
        `processed` TINYINT(1) NOT NULL DEFAULT 0,
        `processing_error` TEXT NULL,
        `received_at` DATETIME NOT NULL,
        `processed_at` DATETIME NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_nj_court_webhook_event` (`external_event_id`),
        KEY `idx_nj_court_webhook_job` (`external_job_id`),
        KEY `idx_nj_court_webhook_processed` (`processed`),
        KEY `idx_nj_court_webhook_received` (`received_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ';');
}

/* --------------------------------------------------------------------------
 * Settings via Perfex options (no dedicated settings table)
 * Secrets are stored encrypted at write-time by the settings controller.
 * ------------------------------------------------------------------------ */
$defaults = [
    'nj_court_search_enabled'                 => '0',
    'nj_court_search_api_base_url'            => '',
    'nj_court_search_api_key'                 => '',
    'nj_court_search_api_timeout'             => '30',
    'nj_court_search_poll_interval'           => '60',
    'nj_court_search_poll_batch_size'         => '20',
    'nj_court_search_webhook_secret'          => '',
    'nj_court_search_webhook_tolerance'       => '300',
    'nj_court_search_webhook_enabled'         => '0',
    'nj_court_search_cron_polling_enabled'    => '1',
    'nj_court_search_result_retention_days'   => '0',
    'nj_court_search_retention_batch_size'    => '50',
    'nj_court_search_role_restrictions'       => '',
    'nj_court_search_purge_on_uninstall'      => '0',
    'nj_court_search_api_key_configured'      => '0',
    'nj_court_search_webhook_secret_configured' => '0',
    'nj_court_search_mock_mode'               => '0',
    'nj_court_search_mock_scenario'           => 'success_flow',
];

foreach ($defaults as $name => $value) {
    // add_option is a no-op when the option already exists — preserves secrets & settings
    add_option($name, $value);
}
