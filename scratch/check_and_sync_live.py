import paramiko
import os
import sys
import time

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)
    print("[+] SSH Connected successfully!")
    
    commands = [
        "cd applications/yayxamnaue/public_html/laravel && git pull origin master",
        "cd applications/yayxamnaue/public_html/laravel && php artisan optimize:clear",
    ]
    for cmd in commands:
        print(f"\n--- {cmd} ---")
        stdin, stdout, stderr = ssh.exec_command(cmd)
        print(stdout.read().decode())
        err = stderr.read().decode()
        if err:
            print("[ERR]: " + err)
            
    ssh.close()
    print("[SUCCESS] Live server updated to latest commit!")
except Exception as e:
    print(f"[-] SSH not ready yet: {e}")
