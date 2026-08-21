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

for app in ["cdkwbagedg", "guufnbempw", "qzuhvevmvm", "yayxamnaue"]:
    stdin, stdout, stderr = ssh.exec_command(f"grep -i 'primebooking' /home/master/applications/{app}/public_html/wp-config.php 2>/dev/null")
    res = stdout.read().decode('utf-8').strip()
    print(f"App {app} wp-config primebooking search: {res}")

    stdin, stdout, stderr = ssh.exec_command(f"head -n 5 /home/master/applications/{app}/conf/server.nginx 2>/dev/null")
    print(f"App {app} nginx conf:\n", stdout.read().decode('utf-8'))

ssh.close()
