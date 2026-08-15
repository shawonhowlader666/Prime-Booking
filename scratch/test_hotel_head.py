import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php test_hotel.php | head -n 20")
print(stdout.read().decode('utf-8', errors='ignore'))

ssh.close()
