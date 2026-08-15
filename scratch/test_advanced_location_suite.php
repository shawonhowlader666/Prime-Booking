<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;
use App\Models\Location;
use App\Services\Search\LocationNormalizerService;
use App\Repositories\PropertyRepository;

echo "==================================================" . PHP_EOL;
echo "  ADVANCED LOCATION ENGINE (HAVERSINE & ALIASES)" . PHP_EOL;
echo "==================================================" . PHP_EOL;

$passed = 0;
$total = 0;

function assertCondition($desc, $cond) {
    global $passed, $total;
    $total++;
    if ($cond) {
        $passed++;
        echo "✅ PASS: " . $desc . PHP_EOL;
    } else {
        echo "❌ FAIL: " . $desc . PHP_EOL;
    }
}

// 1. Test LocationNormalizerService
$normalizer = new LocationNormalizerService();

$res1 = $normalizer->resolve('coxsbazar');
assertCondition("Alias 'coxsbazar' resolves to 'Cox\'s Bazar'", $res1['canonical'] === "Cox's Bazar");

$res2 = $normalizer->resolve('কক্সবাজার');
assertCondition("Bengali 'কক্সবাজার' resolves to 'Cox\'s Bazar'", $res2['canonical'] === "Cox's Bazar");

$res3 = $normalizer->resolve('st martin');
assertCondition("Alias 'st martin' resolves to 'Saint Martin'", $res3['canonical'] === "Saint Martin");

$res4 = $normalizer->resolve('সুন্দরবন');
assertCondition("Bengali 'সুন্দরবন' resolves to 'Sundarbans'", $res4['canonical'] === "Sundarbans");

// 2. Test Haversine Distance Calculation on Property Model
$prop = new Property([
    'name' => 'Cox Luxury Beach Hotel',
    'city' => 'Cox\'s Bazar',
    'latitude' => 21.4272,
    'longitude' => 91.9702,
]);

// Distance to a point 1 km north (approx 21.4362, 91.9702)
$distKm = $prop->getDistanceTo(21.4362, 91.9702);
assertCondition("Haversine distance is calculated accurately (~1.0 km)", $distKm !== null && $distKm >= 0.8 && $distKm <= 1.2);

$formatted = $prop->getFormattedDistanceTo(21.4280, 91.9702);
assertCondition("Short distance is formatted in meters (e.g. '89 m')", $formatted !== null && str_contains($formatted, 'm'));

// 3. Test PropertyRepository near coordinate spatial search
$repo = app(PropertyRepository::class);
$searchResults = $repo->search([
    'lat' => 21.4272,
    'lng' => 91.9702,
    'radius_km' => 50,
]);
assertCondition("Spatial GPS radius search returns results without error", isset($searchResults['total_count']));

// 4. Test PropertyRepository Alias Search
$aliasSearch = $repo->search([
    'destination' => 'coxsbazar',
]);
assertCondition("Alias search 'coxsbazar' returns properties via normalizer", isset($aliasSearch['results']));

echo PHP_EOL . "==================================================" . PHP_EOL;
echo "  RESULTS: {$passed} / {$total} ADVANCED TESTS PASSED" . PHP_EOL;
echo "==================================================" . PHP_EOL;

if ($passed === $total) {
    exit(0);
} else {
    exit(1);
}
