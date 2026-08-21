import paramiko
import sys

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

stdin, stdout, stderr = ssh.exec_command("ls -la /home/master/applications/yayxamnaue/public_html")
print("Files in public_html:\n", stdout.read().decode('utf-8'))

stdin, stdout, stderr = ssh.exec_command("cat /home/master/applications/yayxamnaue/public_html/.htaccess")
print("Root .htaccess:\n", stdout.read().decode('utf-8'))

ssh.close()
