import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

commands = [
    "cd applications/yayxamnaue/public_html && rm -f storage && ln -s laravel/storage/app/public storage",
    "cd applications/yayxamnaue/public_html/laravel/public && rm -f storage && ln -s ../storage/app/public storage",
    "chmod -R 777 applications/yayxamnaue/public_html/laravel/storage/app/public",
    "ls -la applications/yayxamnaue/public_html/storage",
    "ls -la applications/yayxamnaue/public_html/storage/uploads/avatars/",
    "cat applications/yayxamnaue/public_html/.htaccess"
]

for cmd in commands:
    print(f"\n--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[ERR]: " + err)

ssh.close()
