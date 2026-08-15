import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

cmd = "cd /home/master/applications/yayxamnaue/public_html/laravel && tail -n 120 storage/logs/laravel.log"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[LOGS]:\n" + stdout.read().decode())

ssh.close()
