<?php
/**
 * Seed the 3 InfoCheck orders into tblsearches as New Requests.
 * Usage: php scripts/seed-new-requests.php
 */

$mysqli = new mysqli('localhost', 'root', 'root', 'crm_az');
if ($mysqli->connect_error) {
    fwrite(STDERR, $mysqli->connect_error . PHP_EOL);
    exit(1);
}
$mysqli->set_charset('utf8');

$orders = [
    [
        'search_id' => 8490171,
        'first_name' => 'CHRISTYNA',
        'middle_name' => '',
        'last_name' => 'ESTIVENE',
        'dob' => '1989-08-21',
        'ssn' => '144066807',
        'county_id' => 2028, // UNION NJ
        'county_name' => 'UNION',
        'state' => 'NJ',
        'client_notes' => "156: 10 YEAR STANDARD SCOPE; REPORT TRAFFIC MISDEMEANORS",
        'years_searched' => 7,
    ],
    [
        'search_id' => 8490172,
        'first_name' => 'CHRISTYNA',
        'middle_name' => '',
        'last_name' => 'ESTIVENE',
        'dob' => '1989-08-21',
        'ssn' => '144066807',
        'county_id' => 2014, // ESSEX NJ
        'county_name' => 'ESSEX',
        'state' => 'NJ',
        'client_notes' => "156: 10 YEAR STANDARD SCOPE; REPORT TRAFFIC MISDEMEANORS",
        'years_searched' => 7,
    ],
    [
        'search_id' => 8493268,
        'first_name' => 'JUSTIN',
        'middle_name' => 'JOHN',
        'last_name' => 'MCCULLOUGH',
        'dob' => '1973-08-19',
        'ssn' => '055743140',
        'county_id' => 2023, // OCEAN NJ
        'county_name' => 'OCEAN',
        'state' => 'NJ',
        'client_notes' => "156: 10 YEAR STANDARD SCOPE; REPORT TRAFFIC MISDEMEANORS",
        'years_searched' => 7,
    ],
];

$now = time();
$meta = serialize(['new' => 1]);

foreach ($orders as $o) {
    $subject = (object) [
        'first_name' => $o['first_name'],
        'middle_name' => $o['middle_name'],
        'last_name' => $o['last_name'],
        'name_suffix' => '',
        'date_of_birth' => $o['dob'],
        'ssn' => $o['ssn'],
        'address1' => '',
        'city' => $o['county_name'],
        'state' => $o['state'],
        'zip_code' => '',
        'country' => 'US',
        'position_state' => $o['state'],
        'years_searched' => $o['years_searched'],
        'common_name' => '',
    ];

    $search = (object) [
        'search_id' => $o['search_id'],
        'acknowledged' => '',
        'search_type' => 'F/M',
        'search_status' => 'P',
        'search_county_id' => $o['county_id'],
        'client_notes' => $o['client_notes'],
        'known_hit' => '',
        'subject' => $subject,
    ];

    $orig = serialize($search);
    $sid = (int) $o['search_id'];
    $fn = $mysqli->real_escape_string($o['first_name']);
    $mn = $mysqli->real_escape_string($o['middle_name']);
    $ln = $mysqli->real_escape_string($o['last_name']);
    $st = $mysqli->real_escape_string($o['state']);
    $origEsc = $mysqli->real_escape_string($orig);
    $metaEsc = $mysqli->real_escape_string($meta);

    $exists = $mysqli->query("SELECT list_id FROM tblsearches WHERE search_ID={$sid}")->num_rows > 0;
    if ($exists) {
        $sql = "UPDATE tblsearches SET
            search_status='P',
            first_name='{$fn}',
            middle_name='{$mn}',
            last_name='{$ln}',
            state='{$st}',
            orig_data='{$origEsc}',
            meta='{$metaEsc}',
            added_date='{$now}',
            sent_date=NULL
            WHERE search_ID={$sid}";
        $action = 'updated';
    } else {
        $sql = "INSERT INTO tblsearches
            (search_ID, search_status, first_name, middle_name, last_name, state, orig_data, meta, cases, added_date, sent_date)
            VALUES
            ({$sid}, 'P', '{$fn}', '{$mn}', '{$ln}', '{$st}', '{$origEsc}', '{$metaEsc}', NULL, '{$now}', NULL)";
        $action = 'inserted';
    }

    if (!$mysqli->query($sql)) {
        fwrite(STDERR, "Failed {$sid}: {$mysqli->error}\n");
        exit(1);
    }
    echo "{$action} search_ID={$sid} {$ln}, {$fn} {$o['county_name']} {$st}\n";
}

$q = $mysqli->query("SELECT COUNT(*) c FROM tblsearches WHERE search_status='P' AND meta!=''");
echo 'New Requests count: ' . $q->fetch_assoc()['c'] . PHP_EOL;
$mysqli->close();
