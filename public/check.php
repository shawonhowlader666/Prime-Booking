<?php
header('Content-Type: text/plain');

$base = dirname(__DIR__);
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Base dir: " . $base . "\n";
echo ".env exists: " . (file_exists($base . '/.env') ? 'YES' : 'NO') . "\n";
if (file_exists($base . '/.env')) {
    $envContent = file_get_contents($base . '/.env');
    echo "Has APP_KEY: " . (strpos($envContent, 'APP_KEY=base64') !== false ? 'YES' : 'NO') . "\n";
    echo "Has DB_DATABASE: " . (strpos($envContent, 'DB_DATABASE=') !== false ? 'YES' : 'NO') . "\n";
} else {
    $parentEnv = dirname($base) . '/prime-booking/.env';
    echo "prime-booking/.env exists: " . (file_exists($parentEnv) ? 'YES' : 'NO') . "\n";
}

echo "storage exists: " . (is_dir($base . '/storage') ? 'YES' : 'NO') . "\n";
echo "storage writable: " . (is_writable($base . '/storage') ? 'YES' : 'NO') . "\n";
echo "storage/framework/views writable: " . (is_writable($base . '/storage/framework/views') ? 'YES' : 'NO') . "\n";
echo "bootstrap/cache writable: " . (is_writable($base . '/bootstrap/cache') ? 'YES' : 'NO') . "\n";

$logFile = $base . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "\n--- LAST 25 LINES OF LARAVEL.LOG ---\n";
    $lines = array_slice(file($logFile), -25);
    echo implode("", $lines);
} else {
    echo "laravel.log does not exist\n";
}
