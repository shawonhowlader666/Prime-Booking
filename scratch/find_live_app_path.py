import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

cmd = "pwd && ls -la && find /home -maxdepth 4 -name 'artisan' 2>/dev/null"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[LIVE PATHS]:\n" + stdout.read().decode())

ssh.close()
