import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

urls = [
    "https://primebooking.com.bd/search?destination=Sundarbans&q=Gulshan",
    "https://primebooking.com.bd/search?destination=Sundarbans",
    "https://primebooking.com.bd/search?destination=Dhaka",
    "https://primebooking.com.bd/search?destination=Cox%27s+Bazar",
    "https://primebooking.com.bd/search?destination=Sylhet",
    "https://primebooking.com.bd/search?q=Gulshan",
    "https://primebooking.com.bd/admin/dashboard",
    "https://primebooking.com.bd/vendor/dashboard",
    "https://primebooking.com.bd/"
]

print("[*] Testing live endpoints via cURL on server:")
for url in urls:
    cmd = f"curl -s -o /dev/null -w '%{{http_code}}' '{url}'"
    stdin, stdout, stderr = ssh.exec_command(cmd)
    code = stdout.read().decode().strip()
    status_icon = "PASS" if code in ["200", "302"] else "FAIL"
    print(f"  [{status_icon}] [{code}] {url}")

ssh.close()
