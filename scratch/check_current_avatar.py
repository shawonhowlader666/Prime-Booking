import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

php_code = r"""<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'vendor@primebooking.com.bd')->first();
echo "User: " . $user->name . "\n";
echo "Avatar in DB: " . $user->avatar . "\n";

if ($user->avatar) {
    $parsed = parse_url($user->avatar, PHP_URL_PATH);
    $rel = preg_replace('/^\/storage\//', '', $parsed);
    $diskPath = storage_path('app/public/' . $rel);
    echo "Disk path: " . $diskPath . " exists: " . (file_exists($diskPath) ? 'YES' : 'NO') . " size: " . (file_exists($diskPath) ? filesize($diskPath) : 0) . "\n";
}
"""

sftp = ssh.open_sftp()
with sftp.file("applications/yayxamnaue/public_html/laravel/check_avatar_db.php", "w") as f:
    f.write(php_code)
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php check_avatar_db.php && rm check_avatar_db.php")
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
