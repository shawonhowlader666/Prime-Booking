import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

commands = [
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/storage",
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg",
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/public/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg",
    "curl -I -s http://127.0.0.1:80/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg",
]

for cmd in commands:
    print(f"\n--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[ERR]: " + err)

ssh.close()
