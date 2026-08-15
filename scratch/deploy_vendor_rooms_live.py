import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\resources\views\vendor\rooms\index.blade.php", "applications/yayxamnaue/public_html/laravel/resources/views/vendor/rooms/index.blade.php")
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php artisan view:clear && php artisan optimize:clear")
print("[OPTIMIZE LIVE]:\n" + stdout.read().decode())

ssh.close()
print("Deployed vendor rooms view to live!")
