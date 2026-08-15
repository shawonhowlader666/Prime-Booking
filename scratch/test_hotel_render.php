<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$property = App\Models\Property::where('slug', 'the-grand-horizon-luxury-palace-water-villas')->first();
if (!$property) {
    $property = App\Models\Property::first();
}

$req = Illuminate\Http\Request::create("/property/{$property->slug}", 'GET');
$resp = $kernel->handle($req);

echo "HTTP STATUS: " . $resp->getStatusCode() . "\n";
echo "CONTAINS SEARCH: " . (strpos($resp->getContent(), 'SEARCH') !== false ? 'YES' : 'NO') . "\n";
echo "CONTAINS STICKY NAV: " . (strpos($resp->getContent(), 'agoda-sticky-nav-bar') !== false ? 'YES' : 'NO') . "\n";
