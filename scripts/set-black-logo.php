<?php
define('BASEPATH', true);
include '/www/wwwroot/portal.amazonscreening.com/application/config/app-config.php';

$m = new mysqli(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);
if ($m->connect_error) {
    fwrite(STDERR, $m->connect_error . PHP_EOL);
    exit(1);
}

$logo = 'logo-black.png';
$m->query("UPDATE tbloptions SET value='" . $m->real_escape_string($logo) . "' WHERE name='company_logo'");
echo "updated rows: " . $m->affected_rows . PHP_EOL;

$r = $m->query("SELECT name, value FROM tbloptions WHERE name LIKE '%logo%' OR name='companyname'");
while ($row = $r->fetch_assoc()) {
    echo $row['name'] . '=' . $row['value'] . PHP_EOL;
}
