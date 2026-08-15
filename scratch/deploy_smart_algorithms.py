import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()

files_to_sync = [
    "app/Services/RecommendationService.php",
    "app/Services/SocialProofService.php",
    "app/Services/SeoSchemaService.php",
    "app/Http/Controllers/Web/PropertyDetailController.php",
    "resources/views/pages/hotel-detail.blade.php",
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
print("[OPTIMIZE]:\n" + stdout.read().decode())

ssh.close()
print("Smart algorithms, recommendations, social proof, and SEO schema deployed successfully!")
