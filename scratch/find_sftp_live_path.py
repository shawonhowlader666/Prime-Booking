import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
print("[SFTP CWD]:", sftp.getcwd())
print("[SFTP ROOT LIST]:", sftp.listdir("."))
print("[APPLICATIONS LIST]:", sftp.listdir("applications"))
print("[YAYXAMNAUE LIST]:", sftp.listdir("applications/yayxamnaue"))
print("[PUBLIC_HTML LIST]:", sftp.listdir("applications/yayxamnaue/public_html"))
sftp.close()
ssh.close()
