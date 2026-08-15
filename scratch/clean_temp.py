import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

stdin, stdout, stderr = ssh.exec_command("rm -f applications/yayxamnaue/public_html/laravel/test_hotel.php")
stdout.read()
ssh.close()
