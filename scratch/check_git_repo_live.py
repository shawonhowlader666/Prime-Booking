import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

commands = [
    "cd /home/1614833.cloudwaysapps.com/yayxamnaue/git_repo && git status",
    "cd /home/1614833.cloudwaysapps.com/yayxamnaue/git_repo && git remote -v",
    "ls -la /home/1614833.cloudwaysapps.com/yayxamnaue/public_html"
]

for cmd in commands:
    print(f"\n[EXEC] {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    print(stderr.read().decode())

ssh.close()
