import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

cmd = "cd /home/master/applications/yayxamnaue/public_html/laravel && grep -n -C 5 'local.ERROR' storage/logs/laravel.log | tail -n 35"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[LAST ERRORS]:\n" + stdout.read().decode())

ssh.close()
