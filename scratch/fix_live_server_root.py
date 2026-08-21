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

sftp = ssh.open_sftp()

# 1. Update public_html/index.php to boot Laravel
laravel_bootstrap_index = """<?php

use Illuminate\\Contracts\\Http\\Kernel;
use Illuminate\\Http\\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/laravel/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
"""

with sftp.file('/home/master/applications/yayxamnaue/public_html/index.php', 'w') as f:
    f.write(laravel_bootstrap_index)

print("✅ Updated /home/master/applications/yayxamnaue/public_html/index.php to Laravel!")

# 2. Update public_html/.htaccess to route everything properly
htaccess_content = """<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
"""

with sftp.file('/home/master/applications/yayxamnaue/public_html/.htaccess', 'w') as f:
    f.write(htaccess_content)

print("✅ Updated /home/master/applications/yayxamnaue/public_html/.htaccess!")

sftp.close()

# 3. Clear Laravel caches
stdin, stdout, stderr = ssh.exec_command("cd /home/master/applications/yayxamnaue/public_html/laravel && php artisan optimize:clear")
print("Cache output:\n", stdout.read().decode('utf-8'))

ssh.close()
