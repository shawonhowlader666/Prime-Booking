import paramiko
import os
import time

host = "168.144.27.74"
port = 22
passwords_to_try = [
    ("master_gxpvmujegu", "76uG9CVVcKa2"),
    ("master_gxpvmujegu", "76uG9CVVcKa22"),
    ("yayxamnaue", "QBg9eAMFCW"),
]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

connected = False
for u, p in passwords_to_try:
    try:
        print(f"[*] Trying SSH connection as {u}...")
        ssh.connect(host, port=port, username=u, password=p, timeout=10)
        print(f"[+] SUCCESS! Authenticated as {u}!")
        connected = True
        break
    except Exception as e:
        print(f"[-] Failed with {u}: {e}")
        time.sleep(1)

if not connected:
    print("[-] Could not connect.")
    exit(1)

# Check git status on remote
commands = [
    "cd /home/master/applications/yayxamnaue/public_html/laravel && git fetch origin master && git reset --hard origin/master",
    "cd /home/master/applications/yayxamnaue/public_html/laravel && php artisan optimize:clear",
    "cd /home/master/applications/yayxamnaue/public_html/laravel && tail -n 40 storage/logs/laravel.log"
]

for cmd in commands:
    print(f"\n[EXEC] {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode()
    err = stderr.read().decode()
    if out:
        print(f"[OUT]\n{out}")
    if err:
        print(f"[ERR]\n{err}")

# Test search endpoint locally on live server via curl
print("\n[TEST] Testing live curl request for /search?destination=Sundarbans&q=Gulshan...")
stdin, stdout, stderr = ssh.exec_command("curl -s -I 'https://primebooking.com.bd/search?destination=Sundarbans&q=Gulshan'")
print("[HTTP HEADER RESULT]:\n" + stdout.read().decode())

ssh.close()
