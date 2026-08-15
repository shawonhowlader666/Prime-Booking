import paramiko
import os

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()

files_to_sync = [
    "resources/views/vendor/rooms/index.blade.php",
    "app/Http/Controllers/Vendor/VendorRoomController.php",
    "app/Http/Controllers/Admin/RoomController.php",
    "resources/views/admin/rooms/create.blade.php",
    "resources/views/admin/rooms/edit.blade.php",
]

base_local = r"c:\Users\Shawon\Desktop\Prime Booking"
base_remote = "applications/yayxamnaue/public_html/laravel"

for rel in files_to_sync:
    local_path = os.path.join(base_local, rel.replace('/', '\\'))
    remote_path = f"{base_remote}/{rel}"
    print(f"Uploading {rel}...")
    sftp.put(local_path, remote_path)

sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan view:clear && php artisan optimize:clear")
print("[OPTIMIZE LIVE]:\n" + stdout.read().decode())

ssh.close()
print("All refined field labels, full 3-column rows, and room categories successfully deployed to live server!")
