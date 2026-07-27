<?php

defined('BASEPATH') or exit('No direct script access allowed');

$prefix  = db_prefix();
$charset = $CI->db->char_set;

if (!$CI->db->table_exists($prefix . 'digital_delve_orders')) {
    $CI->db->query('CREATE TABLE `' . $prefix . "digital_delve_orders` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `order_detail_order_id` VARCHAR(64) NOT NULL,
        `account_code` VARCHAR(32) NULL DEFAULT NULL,
        `service_code` VARCHAR(64) NULL DEFAULT NULL,
        `first_name` VARCHAR(100) NULL DEFAULT NULL,
        `middle_name` VARCHAR(100) NULL DEFAULT NULL,
        `last_name` VARCHAR(100) NULL DEFAULT NULL,
        `dob` VARCHAR(32) NULL DEFAULT NULL,
        `ssn` VARCHAR(32) NULL DEFAULT NULL,
        `address_street` VARCHAR(255) NULL DEFAULT NULL,
        `address_city` VARCHAR(100) NULL DEFAULT NULL,
        `address_state` VARCHAR(32) NULL DEFAULT NULL,
        `address_zip` VARCHAR(20) NULL DEFAULT NULL,
        `county` VARCHAR(100) NULL DEFAULT NULL,
        `state` VARCHAR(32) NULL DEFAULT NULL,
        `records_requested` VARCHAR(64) NULL DEFAULT NULL,
        `years_to_search` VARCHAR(64) NULL DEFAULT NULL,
        `court_docs_requested` VARCHAR(32) NULL DEFAULT NULL,
        `rush_requested` VARCHAR(32) NULL DEFAULT NULL,
        `special_instructions` TEXT NULL,
        `reference_number` VARCHAR(100) NULL DEFAULT NULL,
        `aliases` TEXT NULL,
        `status` VARCHAR(32) NOT NULL DEFAULT 'new',
        `response_sent` VARCHAR(8) NOT NULL DEFAULT 'No',
        `raw_xml` LONGTEXT NULL,
        `imported_at` DATETIME NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_dd_order_detail_order_id` (`order_detail_order_id`),
        KEY `idx_dd_status` (`status`),
        KEY `idx_dd_account_code` (`account_code`),
        KEY `idx_dd_imported_at` (`imported_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ';');
}

add_option('digital_delve_last_download_at', '');
add_option('digital_delve_last_download_count', '0');
