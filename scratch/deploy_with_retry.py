import paramiko
import os
import sys
import time

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

connected = False
for attempt in range(5):
    try:
        print(f"Connecting attempt {attempt+1}...")
        ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)
        connected = True
        print("[+] Connected successfully!")
        break
    except Exception as e:
        print(f"[-] Connection attempt {attempt+1} failed: {e}")
        time.sleep(10)

if not connected:
    print("Could not connect.")
    sys.exit(1)

sftp = ssh.open_sftp()

files_to_sync = [
    "routes/web.php",
    "app/Http/Controllers/Vendor/VendorController.php",
    "resources/views/vendor/profile.blade.php",
]

base_local = r"c:\Users\Shawon\Desktop\Prime Booking"
base_remote = "applications/yayxamnaue/public_html/laravel"

for rel in files_to_sync:
    local_path = os.path.join(base_local, rel.replace('/', '\\'))
    remote_path = f"{base_remote}/{rel}"
    print(f"Uploading {rel}...")
    sftp.put(local_path, remote_path)

sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan route:clear && php artisan view:clear && php artisan config:clear && php artisan cache:clear")
print("[OPTIMIZE]:\n" + stdout.read().decode())

ssh.close()
print("[SUCCESS] All files deployed and caches cleared successfully!")
