import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

fix_commands = """
cd /home/1614833.cloudwaysapps.com/yayxamnaue/public_html

# 1. Remove the symlink
if [ -L storage ]; then
    rm -f storage
fi

# 2. Create real storage directory structure in public_html
mkdir -p storage/uploads/avatars
mkdir -p storage/uploads/properties/gallery
mkdir -p storage/uploads/properties/videos
mkdir -p storage/uploads/rooms
mkdir -p storage/uploads/branding

# 3. Copy all existing uploads from laravel/storage/app/public to real storage directory
if [ -d laravel/storage/app/public/uploads ]; then
    cp -r laravel/storage/app/public/uploads/* storage/uploads/ 2>/dev/null || true
fi

# 4. Set full 777 permissions on public_html/storage so www-data / php-fpm can write directly
chmod -R 777 storage

# 5. Also create storage directory in laravel/public for any direct public/ access
mkdir -p laravel/public/storage
if [ -L laravel/public/storage ]; then
    rm -f laravel/public/storage
fi
mkdir -p laravel/public/storage/uploads
cp -r storage/uploads/* laravel/public/storage/uploads/ 2>/dev/null || true
chmod -R 777 laravel/public/storage

echo "=== VERIFY FILES IN REAL STORAGE ==="
ls -la storage/uploads/avatars/
"""

stdin, stdout, stderr = ssh.exec_command(fix_commands)
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
