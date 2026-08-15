import paramiko
import sys

sys.stdout.reconfigure(encoding='utf-8')

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

test_script = """<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);

try {
    $request = Illuminate\\Http\\Request::create('/hotels/217', 'GET');
    $response = $kernel->handle($request);
    echo 'STATUS: ' . $response->getStatusCode() . PHP_EOL;
    if (isset($response->exception) && $response->exception) {
        echo 'EXCEPTION: ' . $response->exception->getMessage() . PHP_EOL;
        echo 'FILE: ' . $response->exception->getFile() . ':' . $response->exception->getLine() . PHP_EOL;
        echo 'TRACE: ' . $response->exception->getTraceAsString() . PHP_EOL;
    }
} catch (\\Throwable $e) {
    echo 'CAUGHT: ' . $e->getMessage() . PHP_EOL;
    echo 'FILE: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    echo 'TRACE: ' . $e->getTraceAsString() . PHP_EOL;
}
"""

sftp = ssh.open_sftp()
with sftp.file("applications/yayxamnaue/public_html/laravel/test_hotel.php", "w") as f:
    f.write(test_script)
sftp.close()

stdin, stdout, stderr = ssh.exec_command("cd applications/yayxamnaue/public_html/laravel && php test_hotel.php")
print(stdout.read().decode('utf-8', errors='ignore'))

ssh.close()
