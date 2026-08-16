import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="yayxamnaue", password="QBg9eAMFCW", timeout=10)

commands = [
    "cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html && rm -f storage && ln -s laravel/storage/app/public storage",
    "cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/public && rm -f storage && ln -s ../storage/app/public storage",
    "chmod -R 775 /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/storage",
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/storage",
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/public/storage"
]

for cmd in commands:
    print(f"\n--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[ERR]: " + err)

ssh.close()
