import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\resources\views\pages\hotel-detail.blade.php", "applications/yayxamnaue/public_html/laravel/resources/views/pages/hotel-detail.blade.php")
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan view:clear && php artisan optimize:clear")
print("[OPTIMIZE LIVE]:\n" + stdout.read().decode())

stdin, stdout, stderr = ssh.exec_command("curl -s -o /dev/null -w '%{http_code}' 'https://primebooking.com.bd/property/the-grand-horizon-luxury-palace-water-villas'")
status = stdout.read().decode().strip()
print(f"HTTP Status for https://primebooking.com.bd/property/the-grand-horizon-luxury-palace-water-villas -> {status}")

ssh.close()
