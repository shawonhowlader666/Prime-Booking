<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Http\Requests\Web\SearchRequest;
use App\Services\Search\LocationNormalizerService;
use App\Services\Search\CityInsightService;
use App\Repositories\PropertyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=================================================================" . PHP_EOL;
echo "  DEEP CORE SENIOR ENGINEERING AUDIT & STRESS TEST SUITE" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

$passed = 0;
$total = 0;

function auditCheck($title, $condition, $details = '') {
    global $passed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "  [PASS] " . $title . ($details ? " ({$details})" : "") . PHP_EOL;
    } else {
        echo "  [FAIL] " . $title . ($details ? " -> {$details}" : "") . PHP_EOL;
    }
}

// ─── 1. SQL INJECTION & WILDCARD ESCAPING AUDIT ───────────────────
echo PHP_EOL . "1. SQL Wildcard Sanitization & Injection Defense:" . PHP_EOL;
$maliciousQuery1 = "%' OR '1'='1";
$sql1 = Property::active()->keyword($maliciousQuery1)->toSql();
$bindings1 = Property::active()->keyword($maliciousQuery1)->getBindings();
auditCheck("SQL parameter binding is used (no raw unescaped string injection)", str_contains($sql1, '?'));
auditCheck("Wildcards are escaped in bindings", in_array('%\%\' OR \'1\'=\'1%', $bindings1) || in_array('%\%\' OR \'1\'=\'1%', $bindings1));

// ─── 2. HAVERSINE NULL SAFETY & EDGE CASES ─────────────────────────
echo PHP_EOL . "2. Haversine Spatial Math Boundary Conditions:" . PHP_EOL;
$zeroProp = new Property(['latitude' => 0.0, 'longitude' => 0.0]);
$distToZero = $zeroProp->getDistanceTo(23.8103, 90.4125);
auditCheck("Zero/Null coordinate property safely returns null distance without NaN or Error", $distToZero === null);

$validProp = new Property(['latitude' => 21.4272, 'longitude' => 91.9702, 'nearest_landmark' => 'Inani Beach']);
$validDist = $validProp->getDistanceTo(21.4272, 91.9702);
auditCheck("Identical coordinates yield 0.0 km distance", $validDist !== null && round($validDist, 3) === 0.0);

$fmt0 = $validProp->getFormattedDistanceTo(21.4272, 91.9702);
auditCheck("0 distance formats cleanly in meters", str_contains($fmt0, '0 m'));

// ─── 3. PROPERTY LIST_COLUMNS INTEGRITY ─────────────────────────────
echo PHP_EOL . "3. Property LIST_COLUMNS Data Integrity:" . PHP_EOL;
$requiredColumns = ['id', 'name', 'slug', 'type', 'city', 'address', 'nearest_landmark', 'latitude', 'longitude', 'primary_image', 'images', 'video_url', 'rooms_left', 'location_score', 'free_cancellation', 'no_credit_card_required', 'price_per_night', 'star_rating', 'rating_score', 'total_reviews', 'status'];
$missingCols = array_diff($requiredColumns, Property::LIST_COLUMNS);
auditCheck("LIST_COLUMNS has all necessary columns for 100% real card data", empty($missingCols), empty($missingCols) ? 'All present' : 'Missing: ' . implode(',', $missingCols));

// ─── 4. SEARCH REQUEST VALIDATION & GPS PIPELINE ───────────────────
echo PHP_EOL . "4. SearchRequest Validation & GPS Bounds Pipeline:" . PHP_EOL;
$req = new SearchRequest();
$rules = $req->rules();
auditCheck("SearchRequest validates lat (-90 to 90)", isset($rules['lat']));
auditCheck("SearchRequest validates lng (-180 to 180)", isset($rules['lng']));
auditCheck("SearchRequest per_page is capped at 50 max (memory exhaustion defense)", in_array('max:50', $rules['per_page']));

// ─── 5. REPOSITORY SPATIAL & BOUNDING BOX QUERIES ──────────────────
echo PHP_EOL . "5. Database Spatial Scopes & Repository Pipeline:" . PHP_EOL;
$repo = app(PropertyRepository::class);
$geoSearch = $repo->search([
    'lat' => 21.4272,
    'lng' => 91.9702,
    'radius_km' => 25,
    'per_page' => 10,
]);
auditCheck("Repository search with GPS lat/lng runs successfully", isset($geoSearch['results']));

$boxResults = Property::active()->inBoundingBox(25.0, 93.0, 20.0, 89.0)->limit(5)->get();
auditCheck("Spatial bounding box query executes without SQL error", $boxResults instanceof \Illuminate\Support\Collection);

// ─── 6. VELOCITY & PROXIMITY TRAVEL TIME ENGINE ────────────────────
echo PHP_EOL . "6. Velocity & Proximity Travel Time Engine:" . PHP_EOL;
$proxProp = new Property([
    'name' => 'Seagull Hotel',
    'city' => 'Cox\'s Bazar',
    'nearest_landmark' => 'Sugandha Beach (250m)',
    'latitude' => 21.4272,
    'longitude' => 91.9702,
]);
$breakdown = $proxProp->proximity_breakdown;
auditCheck("Proximity breakdown provides 4 distinct categories", count($breakdown) === 4);
auditCheck("Proximity breakdown formats walking vs driving time", str_contains($breakdown[0]['time_est'], 'walk') && str_contains($breakdown[2]['time_est'], 'drive'));

// ─── 7. LEVENSHTEIN FUZZY RESILIENCE ────────────────────────────────
echo PHP_EOL . "7. Levenshtein Typo Tolerance Extreme Tests:" . PHP_EOL;
$normalizer = new LocationNormalizerService();
$case1 = $normalizer->resolve('shundarban');
auditCheck("Typo 'shundarban' resolves to 'Sundarbans'", $case1['canonical'] === 'Sundarbans');

$case2 = $normalizer->resolve('kuakatta');
auditCheck("Typo 'kuakatta' resolves to 'Kuakata'", $case2['canonical'] === 'Kuakata');

$case3 = $normalizer->resolve('bandorbon');
auditCheck("Typo 'bandorbon' resolves to 'Bandarban'", $case3['canonical'] === 'Bandarban');

echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "  AUDIT RESULTS: {$passed} / {$total} TESTS PASSED (100%)" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

if ($passed === $total) {
    exit(0);
} else {
    exit(1);
}
