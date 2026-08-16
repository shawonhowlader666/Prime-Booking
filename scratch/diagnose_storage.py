import paramiko

host = "168.144.27.74"
port = 22
passwords_to_try = [
    ("master_gxpvmujegu", "76uG9CVVcKa2"),
    ("master_gxpvmujegu", "76uG9CVVcKa22"),
    ("yayxamnaue", "QBg9eAMFCW"),
]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

for u, p in passwords_to_try:
    try:
        ssh.connect(host, port=port, username=u, password=p, timeout=10)
        print(f"[+] Authenticated as {u}")
        break
    except Exception as e:
        continue

test_script = """
echo "=== TESTING DIRECT ACCESS ==="
cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html
ls -la storage/uploads/avatars/

echo "=== APACHE CONFIG / HTACCESS TEST ==="
# Test accessing avatar through laravel route or direct static
curl -I -s https://primebooking.com.bd/storage/uploads/avatars/9FheO7F0vHe1Yt0bWMyZGodfjLwWwbXt2GeYGFD4.jpg

# Test accessing via relative symlink instead of absolute symlink
# Sometimes Nginx or Apache forbids following symlinks with different owner or outside root!
"""

stdin, stdout, stderr = ssh.exec_command(test_script)
print(stdout.read().decode())
print("[STDERR]:\n" + stderr.read().decode())

ssh.close()
