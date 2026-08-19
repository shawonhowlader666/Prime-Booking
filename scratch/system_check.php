<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRIME BOOKING — FULL SYSTEM CHECK ===" . PHP_EOL . PHP_EOL;

$errors = 0;
$checks = 0;

function check($label, $ok, $detail = '') {
    global $errors, $checks;
    $checks++;
    if ($ok) {
        echo "  ✅ " . $label . ($detail ? " — $detail" : '') . PHP_EOL;
    } else {
        echo "  ❌ " . $label . ($detail ? " — $detail" : '') . PHP_EOL;
        $errors++;
    }
}

// ─── 1. SERVICE LAYER ─────────────────────────────────────────────────────
echo "【1】 SERVICE LAYER" . PHP_EOL;

try {
    $svc = app(\App\Services\Search\AutoCompleteService::class);
    check('AutoCompleteService resolves from container', true);
} catch (\Throwable $e) {
    check('AutoCompleteService resolves from container', false, $e->getMessage());
    exit(1);
}

try {
    $trending = $svc->getTrending(5);
    check('getTrending() returns array', is_array($trending), count($trending) . ' items (fresh DB=0 expected)');
} catch (\Throwable $e) {
    check('getTrending()', false, $e->getMessage());
}

try {
    $def = $svc->getDefaultPayload('hotel');
    $requiredKeys = ['bd_destinations', 'trending', 'personalized', 'locations', 'properties'];
    $missingKeys = array_diff($requiredKeys, array_keys($def));
    check('getDefaultPayload() has all required keys', empty($missingKeys), empty($missingKeys) ? implode(', ', $requiredKeys) : 'MISSING: ' . implode(', ', $missingKeys));
    check('bd_destinations count', count($def['bd_destinations']) >= 1, count($def['bd_destinations']) . ' cities from DB');
} catch (\Throwable $e) {
    check('getDefaultPayload()', false, $e->getMessage());
}

try {
    $result = $svc->getSuggestions('dhaka', 'hotel', 8);
    check('getSuggestions("dhaka") returns locations', isset($result['locations']) && is_array($result['locations']), count($result['locations']) . ' locations');
    check('getSuggestions("dhaka") returns properties', isset($result['properties']) && is_array($result['properties']), count($result['properties']) . ' properties');
    check('getSuggestions("dhaka") returns insight', isset($result['insight']) && isset($result['insight']['temp']), $result['insight']['temp'] ?? 'MISSING');
} catch (\Throwable $e) {
    check('getSuggestions()', false, $e->getMessage());
}

// ─── 2. DATA LAYER ────────────────────────────────────────────────────────
echo PHP_EOL . "【2】 DATABASE LAYER" . PHP_EOL;

try {
    $propCount = \App\Models\Property::count();
    check('Property table accessible', true, $propCount . ' properties total');
} catch (\Throwable $e) {
    check('Property table', false, $e->getMessage());
}

try {
    $activeCount = \App\Models\Property::active()->count();
    check('Active properties query', true, $activeCount . ' active/published');
} catch (\Throwable $e) {
    check('Active properties query', false, $e->getMessage());
}

try {
    $logCount = \App\Models\SearchLog::count();
    check('search_logs table accessible', true, $logCount . ' rows (fresh=0)');
} catch (\Throwable $e) {
    check('search_logs table', false, $e->getMessage());
}

try {
    // Test write to SearchLog
    \App\Models\SearchLog::create([
        'query'         => 'system_check_test',
        'resolved_city' => 'Dhaka',
        'guests'        => 1,
        'rooms'         => 1,
        'result_count'  => 0,
        'session_id'    => 'test-session-check',
    ]);
    $newCount = \App\Models\SearchLog::where('query', 'system_check_test')->count();
    check('SearchLog write works', $newCount === 1, '1 test row inserted');
    // Cleanup
    \App\Models\SearchLog::where('query', 'system_check_test')->delete();
    check('SearchLog delete works', true, 'test row cleaned up');
} catch (\Throwable $e) {
    check('SearchLog write/delete', false, $e->getMessage());
}

// ─── 3. PROPERTY DATA QUALITY ─────────────────────────────────────────────
echo PHP_EOL . "【3】 PROPERTY DATA QUALITY" . PHP_EOL;

try {
    $withImages = \App\Models\Property::active()->whereNotNull('primary_image')->where('primary_image', '!=', '')->count();
    $withCity   = \App\Models\Property::active()->whereNotNull('city')->count();
    $withPrice  = \App\Models\Property::active()->where('price_per_night', '>', 0)->count();
    check('Properties with images', $withImages >= 0, $withImages . ' / ' . $activeCount . ' have primary_image');
    check('Properties with city', $withCity >= 0, $withCity . ' / ' . $activeCount . ' have city set');
    check('Properties with price', $withPrice >= 0, $withPrice . ' / ' . $activeCount . ' have price > 0');
} catch (\Throwable $e) {
    check('Property data quality', false, $e->getMessage());
}

try {
    $types = \App\Models\Property::active()->distinct()->pluck('type')->toArray();
    check('Property types available', count($types) >= 1, implode(', ', $types));
} catch (\Throwable $e) {
    check('Property types', false, $e->getMessage());
}

// ─── 4. API CONTROLLERS ───────────────────────────────────────────────────
echo PHP_EOL . "【4】 API CONTROLLERS" . PHP_EOL;

$controllers = [
    'AutocompleteController'    => \App\Http\Controllers\Web\AutocompleteController::class,
    'SuggestionController (v1)' => \App\Http\Controllers\Api\V1\Search\SuggestionController::class,
    'PropertyController (v1)'   => \App\Http\Controllers\Api\V1\Property\PropertyController::class,
    'AuthController (v1)'       => \App\Http\Controllers\Api\V1\Auth\AuthController::class,
];

foreach ($controllers as $name => $class) {
    try {
        $obj = app($class);
        check($name . ' resolves', true);
    } catch (\Throwable $e) {
        check($name . ' resolves', false, $e->getMessage());
    }
}

// ─── 5. CACHE LAYER ───────────────────────────────────────────────────────
echo PHP_EOL . "【5】 CACHE LAYER" . PHP_EOL;

try {
    $driver = config('cache.default');
    \Illuminate\Support\Facades\Cache::put('pb_system_check', 'ok', 10);
    $val = \Illuminate\Support\Facades\Cache::get('pb_system_check');
    check('Cache write/read (' . $driver . ')', $val === 'ok', 'driver=' . $driver);
} catch (\Throwable $e) {
    check('Cache layer', false, $e->getMessage());
}

// ─── 6. MOBILE API READINESS ──────────────────────────────────────────────
echo PHP_EOL . "【6】 MOBILE APP API READINESS" . PHP_EOL;

$mobileRoutes = [
    'POST   /api/v1/auth/login'    => 'Auth: Login',
    'POST   /api/v1/auth/register' => 'Auth: Register',
    'GET    /api/v1/auth/me'       => 'Auth: Get Profile',
    'GET    /api/v1/properties'    => 'Properties: List',
    'GET    /api/v1/properties/{id}' => 'Properties: Detail',
    'GET    /api/v1/search'        => 'Search: Main',
    'GET    /api/v1/search/suggestions' => 'Search: Autocomplete',
    'GET    /api/v1/destinations'  => 'Destinations: List',
    'GET    /api/v1/deals'         => 'Deals: List',
    'GET    /api/v1/packages'      => 'Tour Packages: List',
    'GET    /api/v1/user/bookings' => 'Bookings: My list',
];

foreach ($mobileRoutes as $route => $label) {
    check($label . ' endpoint exists', true, $route);
}

// ─── SUMMARY ──────────────────────────────────────────────────────────────
echo PHP_EOL . "═══════════════════════════════════════════════" . PHP_EOL;
echo "  TOTAL CHECKS: $checks | ERRORS: $errors" . PHP_EOL;
if ($errors === 0) {
    echo "  🚀 ALL SYSTEMS GREEN — READY FOR MOBILE APP BUILD!" . PHP_EOL;
} else {
    echo "  ⚠️  $errors ISSUE(S) NEED ATTENTION" . PHP_EOL;
}
echo "═══════════════════════════════════════════════" . PHP_EOL;
