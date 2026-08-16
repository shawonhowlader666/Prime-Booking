import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

test_commands = """
cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html
mkdir -p test_media
cp laravel/storage/app/public/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg test_media/test.jpg

echo "=== 1. HTTPS DIRECT FILE IN REAL FOLDER ==="
curl -I -k https://127.0.0.1/test_media/test.jpg -H 'Host: primebooking.com.bd'

echo "=== 2. HTTPS STORAGE SYMLINK ==="
curl -I -k https://127.0.0.1/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg -H 'Host: primebooking.com.bd'

rm -rf test_media
"""

stdin, stdout, stderr = ssh.exec_command(test_commands)
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
