<?php

require __DIR__ . (file_exists(__DIR__ . '/vendor/autoload.php') ? '/vendor/autoload.php' : '/../vendor/autoload.php');
$app = require_once __DIR__ . (file_exists(__DIR__ . '/bootstrap/app.php') ? '/bootstrap/app.php' : '/../bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/search?destination=Sundarbans&q=Gulshan',
    '/search?destination=Sundarbans',
    '/search?q=Gulshan',
    '/search?destination=Dhaka',
    '/search?destination=Cox%27s+Bazar',
];

foreach ($urls as $url) {
    echo "Testing: $url\n";
    $req = Illuminate\Http\Request::create($url, 'GET');
    try {
        $resp = $kernel->handle($req);
        echo "Status: " . $resp->getStatusCode() . "\n";
        if ($resp->getStatusCode() == 500) {
            if (isset($resp->exception)) {
                echo "EXCEPTION MESSAGE: " . $resp->exception->getMessage() . "\n";
                echo "EXCEPTION FILE: " . $resp->exception->getFile() . ":" . $resp->exception->getLine() . "\n";
                echo "TRACE:\n" . $resp->exception->getTraceAsString() . "\n";
            }
        }
    } catch (\Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
    echo "----------------------------------------\n";
}
