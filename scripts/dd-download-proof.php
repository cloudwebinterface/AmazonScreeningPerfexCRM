<?php
/**
 * One-shot DigitalDelve GET ORDERS proof (no PUSH / SEND RECEIPT).
 * Usage: php scripts/dd-download-proof.php
 */

define('BASEPATH', true);

$configFile = dirname(__DIR__) . '/application/config/app-config.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "Missing app-config.php\n");
    exit(1);
}
require $configFile;

require dirname(__DIR__) . '/modules/digital_delve/libraries/Digital_delve_client.php';

if (!function_exists('log_message')) {
    function log_message($level, $message)
    {
        echo '[' . strtoupper($level) . '] ' . $message . PHP_EOL;
    }
}

$mysqli = new mysqli(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);
if ($mysqli->connect_error) {
    fwrite(STDERR, 'DB connect failed: ' . $mysqli->connect_error . PHP_EOL);
    exit(1);
}
$mysqli->set_charset(APP_DB_CHARSET);

$table = 'tbl_digital_delve_orders';
$exists = $mysqli->query("SHOW TABLES LIKE '{$table}'");
if (!$exists || $exists->num_rows === 0) {
    $sql = "CREATE TABLE `{$table}` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    if (!$mysqli->query($sql)) {
        fwrite(STDERR, 'Create table failed: ' . $mysqli->error . PHP_EOL);
        exit(1);
    }
    echo "Created {$table}\n";
}

$existing = [];
$res = $mysqli->query("SELECT order_detail_order_id FROM {$table}");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $existing[] = $row['order_detail_order_id'];
    }
}

$client = new Digital_delve_client();
if (!$client->is_configured()) {
    fwrite(STDERR, "DD API not configured\n");
    exit(1);
}

echo "Calling GET ORDERS (limit " . DD_IMPORT_LIMIT . ")...\n";
$orders = $client->download_new_orders($existing);
if ($orders === false) {
    fwrite(STDERR, 'Download failed: ' . $client->get_last_error() . PHP_EOL);
    exit(1);
}

$saved = 0;
$now = date('Y-m-d H:i:s');
foreach ($orders as $o) {
    $cols = [
        'order_detail_order_id' => $o['order_detail_order_id'],
        'account_code'          => $o['account_code'],
        'service_code'          => $o['service_code'],
        'first_name'            => $o['first_name'],
        'middle_name'           => $o['middle_name'],
        'last_name'             => $o['last_name'],
        'dob'                   => $o['dob'],
        'ssn'                   => $o['ssn'],
        'address_street'        => $o['address_street'],
        'address_city'          => $o['address_city'],
        'address_state'         => $o['address_state'],
        'address_zip'           => $o['address_zip'],
        'county'                => $o['county'],
        'state'                 => $o['state'],
        'records_requested'     => $o['records_requested'],
        'years_to_search'       => $o['years_to_search'],
        'court_docs_requested'  => $o['court_docs_requested'],
        'rush_requested'        => $o['rush_requested'],
        'special_instructions'  => $o['special_instructions'],
        'reference_number'      => $o['reference_number'],
        'aliases'               => $o['aliases'],
        'status'                => 'new',
        'response_sent'         => 'No',
        'raw_xml'               => $o['raw_xml'],
        'imported_at'           => $now,
        'created_at'            => $now,
        'updated_at'            => $now,
    ];
    $names = array_keys($cols);
    $placeholders = implode(',', array_fill(0, count($names), '?'));
    $types = str_repeat('s', count($names));
    $sql = 'INSERT IGNORE INTO `' . $table . '` (`' . implode('`,`', $names) . '`) VALUES (' . $placeholders . ')';
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        fwrite(STDERR, 'Prepare failed: ' . $mysqli->error . PHP_EOL);
        exit(1);
    }
    $values = array_values($cols);
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $saved++;
        echo sprintf(
            "Saved: %s | %s, %s | %s, %s | %s\n",
            $o['order_detail_order_id'],
            $o['last_name'],
            $o['first_name'],
            $o['county'],
            $o['state'],
            $o['service_code']
        );
    }
    $stmt->close();
}

$countRes = $mysqli->query("SELECT COUNT(*) AS c FROM {$table}");
$total = $countRes ? (int) $countRes->fetch_assoc()['c'] : 0;

echo "Imported this run: {$saved}\n";
echo "Total rows in {$table}: {$total}\n";
echo "DONE (no receipt/push sent)\n";

$mysqli->close();
