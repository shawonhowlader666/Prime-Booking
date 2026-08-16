import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

commands = [
    "cd applications/yayxamnaue/public_html/laravel && git pull origin master",
    "cd applications/yayxamnaue/public_html/laravel && php artisan optimize:clear",
    "cd applications/yayxamnaue/public_html/laravel && php artisan view:clear",
    "cd applications/yayxamnaue/public_html/laravel && php artisan route:clear",
]

for cmd in commands:
    print(f"--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[ERR]: " + err)

ssh.close()
print("Live git pull and optimize complete!")
