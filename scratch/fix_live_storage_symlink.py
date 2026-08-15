import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

cmds = [
    # Create subdirectories in storage/app/public
    "mkdir -p applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/avatars",
    "mkdir -p applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/properties",
    "mkdir -p applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/rooms",
    "mkdir -p applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/branding",
    "mkdir -p applications/yayxamnaue/public_html/laravel/storage/app/public/uploads/videos",
    
    # Permissions
    "chmod -R 777 applications/yayxamnaue/public_html/laravel/storage",
    "chmod -R 777 applications/yayxamnaue/public_html/laravel/bootstrap/cache",
    
    # Create symlink at root public_html/storage -> laravel/storage/app/public
    "rm -rf applications/yayxamnaue/public_html/storage",
    "ln -s /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/storage/app/public /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/storage",
    
    # Also ensure laravel/public/storage is symlinked
    "rm -rf applications/yayxamnaue/public_html/laravel/public/storage",
    "ln -s /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/storage/app/public /home/1614833.cloudwaysapps.com/yayxamnaue/public_html/laravel/public/storage",
    
    # Verify symlinks
    "ls -ld applications/yayxamnaue/public_html/storage",
    "ls -ld applications/yayxamnaue/public_html/laravel/public/storage",
]

for cmd in cmds:
    print(f"--- {cmd} ---")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode('utf-8', errors='ignore')
    err = stderr.read().decode('utf-8', errors='ignore')
    if out: print(out)
    if err: print("[ERR]: " + err)

ssh.close()
print("Storage symlinks & permissions fixed!")
