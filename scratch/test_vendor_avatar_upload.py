import paramiko
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
php_code = r"""<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'vendor@primebooking.com.bd')->first();
echo "User ID: " . ($user ? $user->id : 'none') . "\n";
echo "Avatar: " . ($user ? $user->avatar : 'none') . "\n";
$columns = Schema::getColumnListing('users');
echo "Columns in users: " . implode(', ', $columns) . "\n";

$storagePath = storage_path('app/public/uploads/avatars');
echo "Storage Path: " . $storagePath . " exists=" . (file_exists($storagePath) ? 'yes' : 'no') . " writable=" . (is_writable($storagePath) ? 'yes' : 'no') . "\n";

$publicLink = public_path('storage');
echo "Public Link: " . $publicLink . " exists=" . (file_exists($publicLink) ? 'yes' : 'no') . " is_link=" . (is_link($publicLink) ? 'yes' : 'no') . " link_target=" . (is_link($publicLink) ? readlink($publicLink) : 'none') . "\n";

$cloudwaysPublic = dirname(base_path()) . '/storage';
echo "Cloudways Webroot storage link: " . $cloudwaysPublic . " exists=" . (file_exists($cloudwaysPublic) ? 'yes' : 'no') . " is_link=" . (is_link($cloudwaysPublic) ? 'yes' : 'no') . " link_target=" . (is_link($cloudwaysPublic) ? readlink($cloudwaysPublic) : 'none') . "\n";
"""

with sftp.file("applications/yayxamnaue/public_html/laravel/test_check.php", "w") as f:
    f.write(php_code)
sftp.close()

stdin, stdout, stderr = ssh.exec_command('cd applications/yayxamnaue/public_html/laravel && php test_check.php && rm test_check.php')
print("[TEST OUTPUT]:\n" + stdout.read().decode())
err = stderr.read().decode()
if err:
    print("[ERROR]:\n" + err)

ssh.close()
