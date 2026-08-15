import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

test_code = """<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);
$req = Illuminate\\Http\\Request::create('/search?destination=Sundarbans&q=Gulshan', 'GET');
$resp = $kernel->handle($req);
if (isset($resp->exception)) {
    echo 'EXCEPTION: ' . $resp->exception->getMessage() . PHP_EOL;
    echo 'FILE: ' . $resp->exception->getFile() . ':' . $resp->exception->getLine() . PHP_EOL;
    $lines = explode(PHP_EOL, $resp->exception->getTraceAsString());
    echo implode(PHP_EOL, array_slice($lines, 0, 15)) . PHP_EOL;
}
"""

sftp = ssh.open_sftp()
with sftp.file("applications/yayxamnaue/public_html/laravel/test_err.php", "w") as f:
    f.write(test_code)
sftp.close()

cmd = "cd applications/yayxamnaue/public_html/laravel && php test_err.php"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[TRACE FIRST 15 LINES]:\n" + stdout.read().decode())
print("[STDERR]:\n" + stderr.read().decode())

ssh.exec_command("rm -f applications/yayxamnaue/public_html/laravel/test_err.php")
ssh.close()
