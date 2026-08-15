import paramiko
import os

host = "168.144.27.74"
port = 22
user = "master_gxpvmujegu"
passw = "76uG9CVVcKa22"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=port, username=user, password=passw, timeout=15)

sftp = ssh.open_sftp()
local_root = r"c:\Users\Shawon\Desktop\Prime Booking"
remote_root = "applications/yayxamnaue/public_html/laravel"

# Directories to sync
dirs_to_sync = [
    "app",
    "config",
    "database/migrations",
    "resources/views",
    "routes",
    "public/css",
    "public/js",
    "bootstrap"
]

def upload_dir(local_dir, remote_dir):
    try:
        sftp.mkdir(remote_dir)
    except IOError:
        pass
    for item in os.listdir(local_dir):
        lpath = os.path.join(local_dir, item)
        rpath = f"{remote_dir}/{item}".replace("\\", "/")
        if os.path.isdir(lpath):
            upload_dir(lpath, rpath)
        else:
            print(f"Uploading: {lpath} -> {rpath}")
            sftp.put(lpath, rpath)

for d in dirs_to_sync:
    local_path = os.path.join(local_root, d.replace("/", os.sep))
    remote_path = f"{remote_root}/{d}".replace("\\", "/")
    if os.path.exists(local_path):
        print(f"\n[*] Syncing directory: {d}...")
        upload_dir(local_path, remote_path)

sftp.close()

# Run artisan migrate and clear cache
commands = [
    "cd applications/yayxamnaue/public_html/laravel && php artisan migrate --force",
    "cd applications/yayxamnaue/public_html/laravel && php artisan optimize:clear",
]

for cmd in commands:
    print(f"\n[RUN]: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode()
    err = stderr.read().decode()
    if out: print("[OUT]:\n" + out)
    if err: print("[ERR]:\n" + err)

# Test live search with curl
print("\n[VERIFYING LIVE SEARCH ENDPOINT]:")
stdin, stdout, stderr = ssh.exec_command("curl -s -o /dev/null -w '%{http_code}' 'https://primebooking.com.bd/search?destination=Sundarbans&q=Gulshan'")
status = stdout.read().decode().strip()
print(f"HTTP Status for https://primebooking.com.bd/search?destination=Sundarbans&q=Gulshan -> {status}")

ssh.close()
