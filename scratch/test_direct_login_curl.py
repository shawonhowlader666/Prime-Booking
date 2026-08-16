import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

commands = [
    "curl -k https://127.0.0.1/login -H 'Host: primebooking.com.bd' | grep -i 'pb-shimmer-card' | head -n 5",
    "curl -k https://127.0.0.1/login -H 'Host: primebooking.com.bd' | grep -i 'primeGlobalPreloader' | head -n 5",
]

for cmd in commands:
    print(f"\n--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print(stdout.read().decode())
    err = stderr.read().decode()
    if err:
        print("[ERR]: " + err)

ssh.close()
