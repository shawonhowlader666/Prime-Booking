<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Http\Controllers\Web\AutocompleteController;
use App\Http\Controllers\Web\SearchController;
use App\Services\Search\SearchService;
use App\Repositories\PropertyRepository;
use Illuminate\Http\Request;

echo "==================================================" . PHP_EOL;
echo "  LOCATION SYSTEM COMPREHENSIVE TEST SUITE" . PHP_EOL;
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

// 1. Verify Database Locations
$locCount = Location::count();
assertCondition("Database has rich locations table (count >= 20)", $locCount >= 20);

$sundarbanLoc = Location::where('name', 'like', '%Sundarban%')->first();
assertCondition("Sundarbans location exists in DB with real coordinates", $sundarbanLoc !== null && $sundarbanLoc->latitude > 0);

// 2. Test AutocompleteController API (100% Dynamic DB)
$autocompleteCtrl = new AutocompleteController();
$request = Request::create('/autocomplete', 'GET', ['q' => 'Sundar']);
$response = $autocompleteCtrl->search($request);
$data = json_decode($response->getContent(), true);

assertCondition("Autocomplete returns success response", $data['success'] === true);
assertCondition("Autocomplete finds Sundarbans from DB", !empty($data['destinations']) && str_contains(strtolower($data['destinations'][0]['title']), 'sundarban'));

$requestCox = Request::create('/autocomplete', 'GET', ['q' => 'Cox']);
$responseCox = $autocompleteCtrl->search($requestCox);
$dataCox = json_decode($responseCox->getContent(), true);
assertCondition("Autocomplete finds Cox's Bazar from DB", !empty($dataCox['destinations']) && str_contains(strtolower($dataCox['destinations'][0]['title']), 'cox'));

// 3. Test Dynamic Popular Areas in SearchController (Zero Hardcoded Gulshan for Sundarbans)
$repo = app(PropertyRepository::class);
$service = new SearchService($repo);
$searchCtrl = new SearchController($service);

// Use reflection to test private getPopularAreasForDestination
$refMethod = new ReflectionMethod(SearchController::class, 'getPopularAreasForDestination');
$refMethod->setAccessible(true);

$areasSundarbans = $refMethod->invoke($searchCtrl, 'Sundarbans');
assertCondition("Sundarbans popular areas do NOT contain Dhaka areas like Gulshan/Banani", !in_array('Gulshan', $areasSundarbans) && !in_array('Banani', $areasSundarbans));

// 4. Test Property Model Keyword Search
$propQuery = Property::active()->keyword('Sundarban')->toSql();
assertCondition("Property scopeKeyword searches nearest_landmark and location relations", str_contains($propQuery, 'nearest_landmark') && str_contains($propQuery, 'locations'));

// 5. Test Property Model Dynamic AI Highlights and Landmarks
$testProp = new Property([
    'name' => 'Sundarbans Eco River Lodge',
    'city' => 'Sundarbans & Mongla',
    'price_per_night' => 8500,
]);
$highlights = $testProp->ai_highlights;
assertCondition("AI Highlights dynamically reflects property city (Sundarbans)", str_contains($highlights['location']['desc'], 'Sundarbans'));

echo PHP_EOL . "==================================================" . PHP_EOL;
echo "  RESULTS: {$passed} / {$total} TESTS PASSED" . PHP_EOL;
echo "==================================================" . PHP_EOL;

if ($passed === $total) {
    exit(0);
} else {
    exit(1);
}
