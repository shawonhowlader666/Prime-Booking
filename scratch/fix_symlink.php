<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$link = __DIR__ . '/../public/storage';
$target = __DIR__ . '/../storage/app/public';

if (is_link($link) || file_exists($link)) {
    if (PHP_OS_FAMILY === 'Windows') {
        exec("rmdir " . escapeshellarg(str_replace('/', '\\', realpath($link) ?: $link)));
    } else {
        unlink($link);
    }
}

$success = symlink(realpath($target), $link);
echo "Symlink created: " . ($success ? "YES" : "NO") . "\n";
echo "public/storage exists: " . (file_exists($link) ? "YES" : "NO") . "\n";
