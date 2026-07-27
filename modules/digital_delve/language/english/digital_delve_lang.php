<?php

defined('BASEPATH') or exit('No direct script access allowed');

$lang['digital_delve'] = 'DigitalDelve';
$lang['digital_delve_orders'] = 'DigitalDelve Orders';
$lang['digital_delve_download'] = 'Download orders';
$lang['digital_delve_downloading'] = 'Downloading…';
$lang['digital_delve_download_confirm'] = 'Live FFO portal — download only, max 3, no receipt/push. Continue?';
$lang['digital_delve_safety_notice'] = 'Download-only proof: this module calls GET ORDERS only. It never sends PUSH RESULTS or SEND RECEIPT, so the live FileFinders download pool is not acknowledged.';
$lang['digital_delve_not_configured'] = 'DigitalDelve API credentials are not configured. Set DD_API_URL, DD_API_USERNAME, and DD_API_PASSWORD in application/config/app-config.php.';
$lang['digital_delve_download_post_required'] = 'Download must be submitted via the Download button (POST).';
$lang['digital_delve_download_failed'] = 'DigitalDelve download failed';
$lang['digital_delve_download_none'] = 'No new FFO orders to import (pool empty or all already saved).';
$lang['digital_delve_download_success'] = 'Imported %s new DigitalDelve order(s).';
$lang['digital_delve_total'] = 'Total stored';
$lang['digital_delve_last_download'] = 'Last download';
$lang['digital_delve_new_rows'] = 'new';
$lang['digital_delve_no_orders'] = 'No orders downloaded yet.';
$lang['digital_delve_order_id'] = 'Order ID';
$lang['digital_delve_subject'] = 'Subject';
$lang['digital_delve_dob'] = 'DOB';
$lang['digital_delve_county_state'] = 'County / State';
$lang['digital_delve_service'] = 'Service';
$lang['digital_delve_account'] = 'Account';
$lang['digital_delve_status'] = 'Status';
$lang['digital_delve_imported_at'] = 'Imported';
$lang['digital_delve_permission_view'] = 'View DigitalDelve orders';
$lang['digital_delve_permission_download'] = 'Download DigitalDelve orders (GET ORDERS)';
