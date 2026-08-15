<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;
use App\Services\Search\LocationNormalizerService;
use App\Services\Search\CityInsightService;

echo "==================================================" . PHP_EOL;
echo "  SMART ALGORITHMS & DATA STRUCTURES TEST SUITE" . PHP_EOL;
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

// 1. Levenshtein Fuzzy Typo Correction Algorithm
$normalizer = new LocationNormalizerService();

$t1 = $normalizer->resolve('coxsbazr'); // typo
assertCondition("Levenshtein: 'coxsbazr' corrected to 'Cox\'s Bazar'", $t1['canonical'] === "Cox's Bazar" && $t1['similarity'] >= 0.8);

$t2 = $normalizer->resolve('sylet'); // typo
assertCondition("Levenshtein: 'sylet' corrected to 'Sylhet'", $t2['canonical'] === "Sylhet" && $t2['similarity'] >= 0.8);

$t3 = $normalizer->resolve('sjake'); // typo
assertCondition("Levenshtein: 'sjake' corrected to 'Sajek'", $t3['canonical'] === "Sajek" && $t3['similarity'] >= 0.7);

$t4 = $normalizer->resolve('sundrbon'); // typo
assertCondition("Levenshtein: 'sundrbon' corrected to 'Sundarbans'", $t4['canonical'] === "Sundarbans");

// 2. City Insight & Seasonal Intelligence Engine
$insightCox = CityInsightService::getInsights("Cox's Bazar");
assertCondition("CityInsight: Cox's Bazar returns Beach Season with ~29°C", str_contains($insightCox['season_badge'], 'Beach') && !empty($insightCox['temp']));

$insightSajek = CityInsightService::getInsights("Sajek Valley");
assertCondition("CityInsight: Sajek returns Cloud Valley with ~24°C", str_contains($insightSajek['condition'], 'Cloud'));

// 3. Proximity & Travel Time Breakdown (Walking vs Driving Speed Algorithm)
$prop = new Property([
    'name' => 'Royal Tulip Cox',
    'city' => 'Cox\'s Bazar',
    'nearest_landmark' => 'Inani Coral Beach (150m)',
    'latitude' => 21.4272,
    'longitude' => 91.9702,
]);
$breakdown = $prop->proximity_breakdown;
assertCondition("Proximity breakdown returns 4 structured categories", count($breakdown) === 4);
assertCondition("Proximity walking category has walking time estimation", $breakdown[0]['mode'] === 'walking' && str_contains($breakdown[0]['time_est'], 'walk'));
assertCondition("Proximity transit category has driving time estimation", $breakdown[2]['mode'] === 'driving' && str_contains($breakdown[2]['time_est'], 'drive'));

// 4. Spatial Bounding Box Query
$boxSql = Property::active()->inBoundingBox(24.0, 92.0, 21.0, 90.0)->toSql();
assertCondition("Spatial Bounding Box builds composite between latitude and longitude SQL", str_contains($boxSql, 'latitude') && str_contains($boxSql, 'longitude') && str_contains($boxSql, 'between'));

echo PHP_EOL . "==================================================" . PHP_EOL;
echo "  RESULTS: {$passed} / {$total} TESTS PASSED" . PHP_EOL;
echo "==================================================" . PHP_EOL;

if ($passed === $total) {
    exit(0);
} else {
    exit(1);
}
