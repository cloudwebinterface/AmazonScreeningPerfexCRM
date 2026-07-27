#!/bin/bash
SITE=/www/wwwroot/portal.amazonscreening.com
echo "=== company uploads ==="
ls -la "$SITE/uploads/company/" 2>&1 | head -40
echo "=== app-config company hints ==="
grep -i logo "$SITE/application/config/app-config.php" 2>/dev/null || true
echo "=== DB options ==="
# try reading db creds from app-config
php -r '
include "/www/wwwroot/portal.amazonscreening.com/application/config/app-config.php";
$m = new mysqli(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);
if ($m->connect_error) { fwrite(STDERR, $m->connect_error."\n"); exit(1); }
$r = $m->query("SELECT name,value FROM tbloptions WHERE name LIKE \"%logo%\" OR name IN (\"companyname\",\"company_logo\",\"company_logo_dark\")");
while ($row = $r->fetch_assoc()) { echo $row["name"]."=".$row["value"]."\n"; }
'
echo "=== logo HTML sample ==="
curl -sL "https://portal.amazonscreening.com/admin/authentication" | grep -oE 'uploads/company/[^"'\'' ]+|company-logo|id="logo"|<img[^>]+>' | head -30
