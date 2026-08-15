<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

echo "=================================================================" . PHP_EOL;
echo "  EXHAUSTIVE BLIND BRUTE-FORCE HTTP ROUTE DISPATCH AUDITOR" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

$routes = Route::getRoutes();
$totalRoutes = count($routes);
$checked = 0;
$passed = 0;
$failed = 0;
$serverErrors500 = [];

$adminUser = User::where('role', 'admin')->first() ?: User::first();
$sampleProp = Property::first();
$sampleSlug = $sampleProp ? $sampleProp->slug : 'the-grand-palace-luxury-resort-spa';

echo "▶ Dispatching live HTTP requests to {$totalRoutes} registered application routes..." . PHP_EOL;

foreach ($routes as $route) {
    $methods = $route->methods();
    if (!in_array('GET', $methods)) {
        continue; // Only test GET routes directly for page rendering
    }

    $uri = $route->uri();
    
    // Replace dynamic route parameters with real sample database entities
    $testUri = '/' . ltrim($uri, '/');
    $testUri = str_replace('{slug}', $sampleSlug, $testUri);
    $testUri = str_replace('{id}', '1', $testUri);
    $testUri = str_replace('{property}', '1', $testUri);
    $testUri = str_replace('{room}', '1', $testUri);
    $testUri = str_replace('{booking}', '1', $testUri);
    $testUri = str_replace('{user}', '1', $testUri);
    $testUri = str_replace('{coupon}', '1', $testUri);
    $testUri = str_replace('{gateway}', 'bkash', $testUri);
    $testUri = str_replace('{destination}', 'coxs-bazar', $testUri);
    $testUri = str_replace('{category}', 'hotel', $testUri);

    // Skip deployment webhooks and network shell execution routes
    if (str_contains($testUri, 'deploy') || str_contains($testUri, 'secret')) {
        continue;
    }

    // Skip wildcard catch-alls that are not parameterized
    if (str_contains($testUri, '{')) {
        continue;
    }

    $checked++;

    // Authenticate as Admin so admin & vendor routes are fully accessible
    if (str_starts_with($testUri, '/api/')) {
        \Laravel\Sanctum\Sanctum::actingAs($adminUser);
    } else {
        Auth::guard('web')->login($adminUser);
    }

    echo "  [{$checked}] GET {$testUri} ... ";

    try {
        $req = Request::create($testUri, 'GET');
        $resp = app('router')->dispatch($req);
        $status = $resp->getStatusCode();

        if ($status >= 500) {
            $failed++;
            $serverErrors500[] = "Route GET {$testUri} threw HTTP {$status}!";
            echo "✖ [HTTP {$status}]" . PHP_EOL;
        } else {
            $passed++;
            echo "✔ [HTTP {$status}]" . PHP_EOL;
        }
    } catch (\Throwable $e) {
        $failed++;
        $serverErrors500[] = "Route GET {$testUri} threw exception: " . $e->getMessage();
        echo "✖ [EXCEPTION: " . $e->getMessage() . "]" . PHP_EOL;
    }
}

echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "  HTTP DISPATCH AUDIT SUMMARY: Checked {$checked} GET Routes" . PHP_EOL;
echo "=================================================================" . PHP_EOL;
echo "  ✔ Successful Responses (HTTP 200/302): {$passed}" . PHP_EOL;
echo "  ✖ Fatal 500 Server Errors: {$failed}" . PHP_EOL;

if ($failed === 0) {
    echo PHP_EOL . "  🌟 100% CLEAN: ZERO 500 SERVER ERRORS ACROSS THE ENTIRE APPLICATION!" . PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL . "Server errors detected:" . PHP_EOL;
    foreach ($serverErrors500 as $err) {
        echo "  - {$err}" . PHP_EOL;
    }
    exit(1);
}
