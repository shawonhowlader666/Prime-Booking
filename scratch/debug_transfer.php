<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = app(\App\Http\Controllers\Web\TransferBookingController::class);
    $req = \Illuminate\Http\Request::create('/transfers', 'GET');
    $res = $controller->index($req);
    echo "Transfer response rendered OK!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
