import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()

files = [
    "resources/views/vendor/bookings.blade.php",
    "resources/views/admin/bookings/index.blade.php",
]

base_local  = r"c:\Users\Shawon\Desktop\Prime Booking"
base_remote = "applications/yayxamnaue/public_html/laravel"

for rel in files:
    lp = os.path.join(base_local, rel.replace('/', '\\'))
    rp = f"{base_remote}/{rel}"
    print(f"Uploading {rel}...")
    sftp.put(lp, rp)

sftp.close()

stdin, stdout, stderr = ssh.exec_command(
    "cd applications/yayxamnaue/public_html/laravel && php artisan view:clear && php artisan optimize:clear"
)
print("[OPTIMIZE]:\n" + stdout.read().decode())
err = stderr.read().decode()
if err.strip():
    print("[ERR]:", err)

ssh.close()
print("All A-Z fixes deployed successfully!")
