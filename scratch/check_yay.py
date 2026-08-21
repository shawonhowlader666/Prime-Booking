import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

HOST = "168.144.27.74"
USER = "master_gxpvmujegu"
PASSWORDS = ["76uG9CVVcKa22", "76uG9CVVcKa2"]
PORT = 22

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

for p in PASSWORDS:
    try:
        ssh.connect(HOST, port=PORT, username=USER, password=p, timeout=20, look_for_keys=False, allow_agent=False)
        break
    except Exception:
        pass

stdin, stdout, stderr = ssh.exec_command("cat /home/master/applications/yayxamnaue/public_html/.htaccess")
print("yayxamnaue .htaccess:\n", stdout.read().decode('utf-8'))

stdin, stdout, stderr = ssh.exec_command("head -n 25 /home/master/applications/yayxamnaue/public_html/index.php")
print("yayxamnaue index.php:\n", stdout.read().decode('utf-8'))

ssh.close()
