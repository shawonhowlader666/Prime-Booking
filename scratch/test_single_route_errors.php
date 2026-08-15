<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('role', 'admin')->first() ?: User::first();
Auth::guard('web')->login($admin);
\Laravel\Sanctum\Sanctum::actingAs($admin);

$routes = [
    '/admin/featured-destinations/create',
    '/vendor/availability',
    '/api/v1/auth/me',
    '/api/v1/search',
    '/api/v1/suggestions',
];

foreach ($routes as $uri) {
    echo "Testing {$uri} ... ";
    try {
        $req = Request::create($uri, 'GET');
        $resp = app('router')->dispatch($req);
        $status = $resp->getStatusCode();
        echo "Status {$status}" . PHP_EOL;
        if (isset($resp->exception)) {
            echo "  Error: " . $resp->exception->getMessage() . " in " . basename($resp->exception->getFile()) . ":" . $resp->exception->getLine() . PHP_EOL;
        }
    } catch (\Throwable $e) {
        echo "Exception: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine() . PHP_EOL;
    }
}
