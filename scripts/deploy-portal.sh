#!/bin/bash
set -euo pipefail

SITE=/www/wwwroot/portal.amazonscreening.com
SRC=/home/almalinux/amazonscreening-deploy
TS=$(date +%Y%m%d_%H%M%S)
BACKUP=/home/almalinux/deploy_backups/portal.amazonscreening.com_${TS}

echo "Backup to ${BACKUP}"
sudo mkdir -p /home/almalinux/deploy_backups
sudo rsync -a \
  --exclude uploads \
  --exclude application/logs \
  --exclude application/cache \
  "${SITE}/" "${BACKUP}/"

echo "Syncing code from ${SRC}..."
sudo rsync -a --delete \
  --exclude .git \
  --exclude CRM.code-workspace \
  --exclude node_modules \
  --exclude application/config/app-config.php \
  --exclude uploads \
  --exclude application/logs \
  --exclude application/cache \
  --exclude .user.ini \
  --exclude .well-known \
  "${SRC}/" "${SITE}/"

sudo chown -R www:www "${SITE}"

echo "Done"
ls -la "${SITE}/assets/css/custom.css"
ls -la "${SITE}/application/config/app-config.php"
test -f "${SITE}/application/config/app-config.php" && echo APP_CONFIG_OK
test -f "${SITE}/assets/css/custom.css" && echo CUSTOM_CSS_OK
