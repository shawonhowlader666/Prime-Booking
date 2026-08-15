import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
local_path = r"c:\Users\Shawon\Desktop\Prime Booking\scratch\seed_demo_vendor_bookings.php"
remote_path = "applications/yayxamnaue/public_html/laravel/seed_demo_vendor_bookings.php"
sftp.put(local_path, remote_path)
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php seed_demo_vendor_bookings.php")
output = stdout.read().decode()
error = stderr.read().decode()

print(output)
if error:
    print("STDERR:", error)

ssh.exec_command("rm applications/yayxamnaue/public_html/laravel/seed_demo_vendor_bookings.php")
ssh.close()
