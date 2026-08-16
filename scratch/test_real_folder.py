import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

test_commands = """
cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html

# 1. Create a test real folder test_media
mkdir -p test_media
cp laravel/storage/app/public/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg test_media/test.jpg
chmod -R 777 test_media

echo "=== TEST REAL FOLDER CURL ==="
curl -I -s http://127.0.0.1:80/test_media/test.jpg
"""

stdin, stdout, stderr = ssh.exec_command(test_commands)
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
