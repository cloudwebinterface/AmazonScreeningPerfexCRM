<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$prefix = db_prefix();

if ($CI->db->table_exists($prefix . 'digital_delve_orders')) {
    $CI->db->query('DROP TABLE `' . $prefix . 'digital_delve_orders`');
}

delete_option('digital_delve_last_download_at');
delete_option('digital_delve_last_download_count');
