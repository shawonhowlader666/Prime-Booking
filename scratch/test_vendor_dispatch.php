<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $vendorUser = \App\Models\User::where('role', 'vendor')->first() ?? \App\Models\User::first();
    \Illuminate\Support\Facades\Auth::setUser($vendorUser);
    
    $req = \Illuminate\Http\Request::create('/vendor/bookings', 'GET');
    $req->headers->set('Accept', 'text/html');
    $req->setUserResolver(fn() => $vendorUser);
    
    $response = $app->handle($req);
    echo "Status Code: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 400) {
        echo "Response Content: " . substr(strip_tags($response->getContent()), 0, 500) . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
