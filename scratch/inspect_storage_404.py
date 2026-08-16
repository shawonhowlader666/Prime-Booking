import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

commands = [
    "ls -la applications/yayxamnaue/public_html/",
    "ls -la applications/yayxamnaue/public_html/storage/",
    "ls -la applications/yayxamnaue/public_html/storage/uploads/",
    "ls -la applications/yayxamnaue/public_html/storage/uploads/avatars/",
    "ls -la applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/avatars/",
    "cat applications/yayxamnaue/public_html/.htaccess | head -n 30"
]

for cmd in commands:
    print(f"\n--- RUNNING: {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[STDERR]: " + err)

ssh.close()
