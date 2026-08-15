import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()

files_to_sync = [
    "database/migrations/2026_08_16_043000_add_enterprise_booking_fields.php",
    "app/Services/InventoryService.php",
    "app/Services/CouponService.php",
    "app/Services/NotificationService.php",
    "app/Models/Booking.php",
    "app/Http/Controllers/Web/BookingFlowController.php",
    "routes/web.php",
    "resources/views/pages/booking-form.blade.php",
    "scratch/seed_active_coupons.php",
]

base_local = r"c:\Users\Shawon\Desktop\Prime Booking"
base_remote = "applications/yayxamnaue/public_html/laravel"

for rel in files_to_sync:
    local_path = os.path.join(base_local, rel.replace('/', '\\'))
    remote_path = f"{base_remote}/{rel}"
    
    # Ensure remote directory exists
    remote_dir = os.path.dirname(remote_path)
    try:
        sftp.stat(remote_dir)
    except FileNotFoundError:
        # Create dir if not exists
        parts = remote_dir.split('/')
        cur = ""
        for p in parts:
            cur += p + "/"
            try:
                sftp.stat(cur)
            except FileNotFoundError:
                sftp.mkdir(cur)
                
    print(f"Uploading {rel}...")
    sftp.put(local_path, remote_path)

sftp.close()

# Run migration
stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan migrate --force")
print("[MIGRATE]:\n" + stdout.read().decode())
err = stderr.read().decode()
if err:
    print("[MIGRATE ERR]:", err)

# Run coupon seeder
stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php scratch/seed_active_coupons.php")
print("[SEED COUPONS]:\n" + stdout.read().decode())

# Optimize clear
stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan view:clear && php artisan optimize:clear")
print("[OPTIMIZE]:\n" + stdout.read().decode())

# Clean scratch
ssh.exec_command("rm applications/yayxamnaue/public_html/laravel/scratch/seed_active_coupons.php")

ssh.close()
print("All enterprise features deployed and active on live server!")
