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

stdin, stdout, stderr = ssh.exec_command("grep -E 'APP_URL|DB_DATABASE' /home/master/applications/yayxamnaue/public_html/laravel/.env")
print("yayxamnaue env:\n", stdout.read().decode('utf-8'))

for app in ["cdkwbagedg", "guufnbempw", "qzuhvevmvm"]:
    stdin, stdout, stderr = ssh.exec_command(f"ls -d /home/master/applications/{app}/public_html/laravel 2>/dev/null")
    res = stdout.read().decode('utf-8').strip()
    if res:
        print(f"Laravel found in {app}!")

ssh.close()
