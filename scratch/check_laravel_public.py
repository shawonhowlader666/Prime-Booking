import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

stdin, stdout, stderr = ssh.exec_command("ls -la applications/yayxamnaue/public_html/laravel/public/")
print("[LARAVEL PUBLIC]:\n" + stdout.read().decode())

stdin, stdout, stderr = ssh.exec_command("ls -la applications/yayxamnaue/public_html/laravel/public/storage/")
print("[LARAVEL PUBLIC STORAGE]:\n" + stdout.read().decode())
print("[STDERR]:\n" + stderr.read().decode())

ssh.close()
