<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $vendor = \App\Models\User::where('role', 'vendor')->first() ?? \App\Models\User::first();
    auth()->setUser($vendor);
    $controller = app(\App\Http\Controllers\Vendor\VendorDashboardController::class);
    $req = \Illuminate\Http\Request::create('/vendor/bookings', 'GET');
    $res = $controller->bookings($req);
    echo "Vendor bookings rendered OK!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
