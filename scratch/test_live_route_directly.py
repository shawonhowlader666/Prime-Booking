import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

test_code = """
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);
$request = Illuminate\\Http\\Request::create('/search?destination=Sundarbans&q=Gulshan', 'GET');
try {
    $response = $kernel->handle($request);
    echo 'STATUS: ' . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() >= 400) {
        if (isset($response->exception)) {
            echo 'EXCEPTION: ' . $response->exception->getMessage() . PHP_EOL . $response->exception->getFile() . ':' . $response->exception->getLine() . PHP_EOL;
            echo $response->exception->getTraceAsString();
        }
    }
} catch (\\Throwable $e) {
    echo 'CAUGHT: ' . $e->getMessage() . PHP_EOL . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    echo $e->getTraceAsString();
}
"""

cmd = f'cd /home/master/applications/yayxamnaue/public_html/laravel && php -r "{test_code.replace(chr(10), " ")}"'
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[LIVE EXECUTION RESULT]:\n" + stdout.read().decode())
print("[LIVE STDERR]:\n" + stderr.read().decode())

ssh.close()
