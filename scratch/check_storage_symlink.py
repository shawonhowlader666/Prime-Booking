import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

cmds = [
    "ls -ld applications/yayxamnaue/public_html/storage",
    "ls -ld applications/yayxamnaue/public_html/laravel/public/storage",
    "ls -la applications/yayxamnaue/public_html/laravel/storage/app/public",
]

for cmd in cmds:
    print(f"--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode('utf-8', errors='ignore'))
    print(stderr.read().decode('utf-8', errors='ignore'))

ssh.close()
