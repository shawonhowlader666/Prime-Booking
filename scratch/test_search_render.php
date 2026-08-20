<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/search?search_type=hotel&check_in=2026-08-20&check_out=2026-08-27', 'GET');
$response = $kernel->handle($request);
if ($response->getStatusCode() >= 400 && isset($response->exception)) {
    echo "EX_MSG: " . $response->exception->getMessage() . "\n";
    echo "EX_FILE: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
} else {
    echo "STATUS: " . $response->getStatusCode() . "\n";
}
