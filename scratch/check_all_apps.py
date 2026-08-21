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

apps = ["cdkwbagedg", "guufnbempw", "qzuhvevmvm", "yayxamnaue"]
for app in apps:
    stdin, stdout, stderr = ssh.exec_command(f"head -n 20 /home/master/applications/{app}/public_html/wp-config.php 2>/dev/null")
    content = stdout.read().decode('utf-8')
    print(f"=== App: {app} ===")
    if content:
        print("Has wp-config.php")
    else:
        print("No wp-config.php")
    
    stdin, stdout, stderr = ssh.exec_command(f"ls -la /home/master/applications/{app}/public_html/ 2>/dev/null")
    print(stdout.read().decode('utf-8')[:300])

ssh.close()
