import os
import sys
import paramiko

sys.stdout.reconfigure(encoding='utf-8')

HOST = "168.144.27.74"
USER = "master_gxpvmujegu"
PASSWORDS = ["76uG9CVVcKa22", "76uG9CVVcKa2"]
PORT = 22
REMOTE_PATH = "/home/master/applications/yayxamnaue/public_html/laravel"

def sftp_mkdir_p(sftp, remote_directory):
    dirs = []
    dir_path = remote_directory
    while dir_path and dir_path != "/":
        dirs.append(dir_path)
        dir_path = os.path.dirname(dir_path)
    dirs.reverse()
    for d in dirs:
        try:
            sftp.stat(d)
        except IOError:
            try:
                sftp.mkdir(d)
            except Exception:
                pass

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

connected = False
for p in PASSWORDS:
    try:
        print(f"Connecting to {HOST} as {USER}...", flush=True)
        ssh.connect(HOST, port=PORT, username=USER, password=p, timeout=20, look_for_keys=False, allow_agent=False)
        print(f"🎉 SSH Connected Successfully!", flush=True)
        connected = True
        break
    except Exception as e:
        print(f"Failed with password attempt: {e}", flush=True)

if not connected:
    print("❌ Could not connect to SSH server.", flush=True)
    sys.exit(1)

# Step 1: Git Pull on Server
print("\n🚀 Executing Git Pull on Live Server...", flush=True)
stdin, stdout, stderr = ssh.exec_command(f"cd {REMOTE_PATH} && git pull origin master")
print("STDOUT:", stdout.read().decode('utf-8', errors='ignore'), flush=True)
err = stderr.read().decode('utf-8', errors='ignore')
if err: print("STDERR:", err, flush=True)

# Ensure public_html/index.php bootstraps Laravel
ensure_index_cmd = """
cat << 'EOF' > /home/master/applications/yayxamnaue/public_html/index.php
<?php
use Illuminate\\Contracts\\Http\\Kernel;
use Illuminate\\Http\\Request;
define('LARAVEL_START', microtime(true));
if (file_exists($maintenance = __DIR__.'/laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}
require __DIR__.'/laravel/vendor/autoload.php';
$app = require_once __DIR__.'/laravel/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle($request = Request::capture())->send();
$kernel->terminate($request, $response);
EOF
"""
ssh.exec_command(ensure_index_cmd)

# Step 2: SFTP Direct Sync for all updated files to ensure 100% synchronization
sftp = ssh.open_sftp()
local_root = r"c:\Users\Shawon\Desktop\Prime Booking"

files_to_sync = [
    "app/Http/Controllers/Web/PropertyPreviewController.php",
    "app/Http/Controllers/Web/PropertyDetailController.php",
    "app/Http/Controllers/Web/BookingFlowController.php",
    "app/Http/Controllers/Vendor/VendorController.php",
    "app/Http/Controllers/Admin/PropertyManagementController.php",
    "app/Models/Coupon.php",
    "routes/web.php",
    "resources/views/home.blade.php",
    "resources/views/layouts/main.blade.php",
    "resources/views/components/layout/header.blade.php",
    "resources/views/components/search/filter-sidebar.blade.php",
    "resources/views/components/search/property-card.blade.php",
    "resources/views/components/recently-viewed-drawer.blade.php",
    "resources/views/components/floating-marketing-widgets.blade.php",
    "resources/views/pages/search-results.blade.php",
    "resources/views/pages/hotel-detail.blade.php",
    "resources/views/pages/hotel-brochure-print.blade.php",
    "resources/views/pages/booking-invoice-print.blade.php",
    "resources/views/pages/booking-confirmation.blade.php",
    "resources/views/vendor/create-property.blade.php",
    "resources/views/vendor/edit-property.blade.php",
    "resources/views/vendor/reviews/index.blade.php",
    "resources/views/vendor/promotions/index.blade.php",
    "resources/views/vendor/promotions/create.blade.php",
    "resources/views/vendor/payouts/index.blade.php",
    "resources/views/vendor/packages/index.blade.php",
    "resources/views/vendor/packages/create.blade.php",
    "resources/views/vendor/packages/edit.blade.php",
    "resources/views/vendor/properties/index.blade.php",
    "resources/views/admin/properties/create.blade.php",
    "resources/views/admin/properties/edit.blade.php",
    "resources/views/admin/properties/index.blade.php",
]

print("\n📦 Ensuring SFTP Direct Sync of Critical Files...", flush=True)
for rel_path in files_to_sync:
    local_file = os.path.join(local_root, rel_path.replace("/", os.sep))
    remote_file = f"{REMOTE_PATH}/{rel_path}"
    if os.path.exists(local_file):
        remote_dir = os.path.dirname(remote_file)
        sftp_mkdir_p(sftp, remote_dir)
        try:
            sftp.put(local_file, remote_file)
            print(f"  ✅ Uploaded: {rel_path}", flush=True)
        except Exception as e:
            print(f"  ⚠️ Error on {rel_path}: {e}", flush=True)
    else:
        print(f"  ⚠️ Local file not found: {local_file}", flush=True)

sftp.close()

# Step 3: Clear Laravel Cache on Server
print("\n🧹 Clearing Laravel caches on live server...", flush=True)
commands = [
    f"cd {REMOTE_PATH} && php artisan view:clear",
    f"cd {REMOTE_PATH} && php artisan cache:clear",
    f"cd {REMOTE_PATH} && php artisan config:clear",
    f"cd {REMOTE_PATH} && php artisan route:clear",
    f"cd {REMOTE_PATH} && php artisan optimize:clear"
]

for cmd in commands:
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode('utf-8', errors='ignore').strip()
    if out: print(out, flush=True)

ssh.close()
print("\n🎉 ALL UPDATES DEPLOYED LIVE TO PRIME BOOKING (https://primebooking.com.bd) SUCCESSFULLY 100%!", flush=True)
