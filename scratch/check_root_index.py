import paramiko

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

stdin, stdout, stderr = ssh.exec_command("cat /home/master/applications/yayxamnaue/public_html/index.php")
print("public_html/index.php content:\n", stdout.read().decode('utf-8'))

ssh.close()
