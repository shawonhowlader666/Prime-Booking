import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

HOST = "168.144.27.74"
USER = "master_gxpvmujegu"
PASSWORDS = ["76uG9CVVcKa22", "76uG9CVVcKa2"]
PORT = 22

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

for p in PASSWORDS:
    try:
        ssh.connect(HOST, port=PORT, username=USER, password=p, timeout=20, look_for_keys=False, allow_agent=False)
        break
    except Exception:
        pass

# 1. Rename WordPress index.php to index.wp_backup.php and replace with Laravel bootstrap
cmd = """
cd /home/master/applications/yayxamnaue/public_html
mv index.php index.wp_old.php 2>/dev/null || true

cat << 'EOF' > index.php
<?php

use Illuminate\\Contracts\\Http\\Kernel;
use Illuminate\\Http\\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

cat << 'EOF' > .htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/laravel/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ laravel/public/$1 [L,QSA]
</IfModule>
EOF

chmod 644 index.php .htaccess
"""

stdin, stdout, stderr = ssh.exec_command(cmd)
print("CMD output:\n", stdout.read().decode('utf-8'))
err = stderr.read().decode('utf-8')
if err: print("Error:\n", err)

# Check index.php content now
stdin, stdout, stderr = ssh.exec_command("head -n 20 /home/master/applications/yayxamnaue/public_html/index.php")
print("New index.php content:\n", stdout.read().decode('utf-8'))

ssh.close()
