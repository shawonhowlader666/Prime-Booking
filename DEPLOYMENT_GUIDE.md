# Prime Booking - Deployment & Setup Guide

## 1. Quick Setup on a New Machine

```bash
# Clone the repository
git clone https://github.com/shawonhowlader666/Prime-Booking.git
cd Prime-Booking

# Install dependencies
composer install
npm install
npm run build

# Environment Setup
cp .env.example .env
php artisan key:generate
php artisan storage:link
```
*(Note: Replace `.env` with your secure production/staging environment configuration).*

---

## 2. Live VPS Deployment

The live website is hosted on Hostinger VPS:
- **Domain:** `https://primebooking.com.bd`
- **Host:** `31.97.238.194`
- **Port:** `65002`
- **User:** `u345402313`
- **Remote Path:** `/home/u345402313/domains/primebooking.com.bd/prime-booking/`
- **Authentication:** SSH Private Key (`id_vps_deploy`) placed in `~/.ssh/id_vps_deploy`

### 1-Click Deployment:
* On Windows (PowerShell):
  ```powershell
  .\deploy_live.ps1
  ```
* On Linux / macOS / Git Bash:
  ```bash
  bash deploy_live.sh
  ```

### Manual Deployment via SCP / SSH:
```bash
# Upload a changed file:
scp -P 65002 -i ~/.ssh/id_vps_deploy resources/views/pages/hotel-detail.blade.php u345402313@31.97.238.194:/home/u345402313/domains/primebooking.com.bd/prime-booking/resources/views/pages/hotel-detail.blade.php

# Clear cache:
ssh -p 65002 -i ~/.ssh/id_vps_deploy u345402313@31.97.238.194 "cd /home/u345402313/domains/primebooking.com.bd/prime-booking && php artisan view:clear && php artisan cache:clear"
```
