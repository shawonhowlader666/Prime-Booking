import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

commands = [
    "curl -I -k https://127.0.0.1/storage/uploads/avatars/9FheO7F0vHe1Yt0bWMyZGodfjLwWwbXt2GeYGFD4.jpg -H 'Host: primebooking.com.bd'",
    "curl -I -k https://127.0.0.1/laravel/public/storage/uploads/avatars/9FheO7F0vHe1Yt0bWMyZGodfjLwWwbXt2GeYGFD4.jpg -H 'Host: primebooking.com.bd'",
]

for cmd in commands:
    print(f"\n--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())

ssh.close()
