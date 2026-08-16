import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

test_commands = """
echo "=== 1. TEST WITH HOST HEADER ==="
curl -I -s -H 'Host: primebooking.com.bd' http://127.0.0.1/test_media/test.jpg

echo "=== 2. TEST STORAGE SYMLINK WITH HOST HEADER ==="
curl -I -s -H 'Host: primebooking.com.bd' http://127.0.0.1/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg

rm -rf /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/test_media
"""

stdin, stdout, stderr = ssh.exec_command(test_commands)
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
