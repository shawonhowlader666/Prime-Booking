#!/usr/bin/env bash
# Prime Booking 1-Click Live VPS Deployment Script (Bash)

KEY_PATH="${HOME}/.ssh/id_vps_deploy"
REMOTE_HOST="31.97.238.194"
PORT=65002
USER="u345402313"
REMOTE_PATH="/home/u345402313/domains/primebooking.com.bd/prime-booking/"

echo "=========================================================="
echo "  PRIME BOOKING - LIVE VPS 1-CLICK DEPLOYER"
echo "=========================================================="

if [ ! -f "$KEY_PATH" ]; then
    echo "[ERROR] SSH key not found at $KEY_PATH"
    echo "Please place your 'id_vps_deploy' file in '${HOME}/.ssh/id_vps_deploy'"
    exit 1
fi

echo "[1/3] Syncing latest Git changes on VPS..."
ssh -p "$PORT" -i "$KEY_PATH" -o StrictHostKeyChecking=no "$USER@$REMOTE_HOST" "cd $REMOTE_PATH && git pull origin master"

echo "[2/3] Clearing Laravel View and Application Cache..."
ssh -p "$PORT" -i "$KEY_PATH" -o StrictHostKeyChecking=no "$USER@$REMOTE_HOST" "cd $REMOTE_PATH && php artisan view:clear && php artisan cache:clear && php artisan route:clear"

echo "=========================================================="
echo "  SUCCESS! Live server updated and caches cleared!"
echo "  Website: https://primebooking.com.bd"
echo "=========================================================="

