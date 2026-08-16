import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=15)

php_code = r"""<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$route = Route::getRoutes()->getByName('login');
echo "Login Route action: " . json_encode($route ? $route->getActionName() : 'none') . "\n";

$viewFinder = view()->getFinder();
echo "View auth.login path: " . $viewFinder->find('auth.login') . "\n";

$content = view('auth.login')->render();
echo "Rendered length: " . strlen($content) . "\n";
echo "Has pb-shimmer-card: " . (strpos($content, 'pb-shimmer-card') !== false ? 'YES' : 'NO') . "\n";
echo "First 500 chars:\n" . substr($content, 0, 500) . "\n";
"""

sftp = ssh.open_sftp()
with sftp.file("applications/yayxamnaue/public_html/laravel/test_login_render.php", "w") as f:
    f.write(php_code)
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php test_login_render.php && rm test_login_render.php")
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
