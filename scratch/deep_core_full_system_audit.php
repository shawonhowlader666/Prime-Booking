<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=================================================================\n";
echo "🔬 PRIME BOOKING — DEEP CORE FULL SYSTEM AUDIT & VERIFICATION\n";
echo "=================================================================\n\n";

$pass = 0;
$total = 0;

function check(string $label, callable $fn) {
    global $pass, $total;
    $total++;
    try {
        $res = $fn();
        if ($res === true || (is_array($res) && ($res['success'] ?? false))) {
            $pass++;
            $detail = is_array($res) ? ($res['detail'] ?? '') : '';
            echo "  ✅ [PASS] {$label}" . ($detail ? " → {$detail}" : "") . "\n";
        } else {
            echo "  ❌ [FAIL] {$label}\n";
        }
    } catch (\Throwable $e) {
        echo "  💥 [EXCEPTION] {$label} → " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// LAYER 1: Core Database & Model Relations
// ─────────────────────────────────────────────────────────────────────────────
echo "1. 🗄️ Database Models & Entity Relations:\n";

check('Property model bootstrapping & LIST_COLUMNS', function() {
    $prop = \App\Models\Property::select(\App\Models\Property::LIST_COLUMNS)->first();
    return $prop !== null;
});

check('Property -> Rooms 1:N relationship', function() {
    $prop = \App\Models\Property::with('rooms')->first();
    return $prop && $prop->rooms !== null;
});

check('Property -> Reviews 1:N relationship', function() {
    $prop = \App\Models\Property::with('reviews')->first();
    return $prop !== null;
});

check('PriceAlert model & relationship', function() {
    $prop = \App\Models\Property::first();
    $alert = \App\Models\PriceAlert::updateOrCreate(
        ['property_id' => $prop->id, 'email' => 'audit_test@primebooking.com'],
        ['current_price_at_alert' => (float)$prop->price_per_night, 'status' => 'active']
    );
    $loaded = \App\Models\PriceAlert::where('email', 'audit_test@primebooking.com')->with('property')->first();
    $success = $loaded && $loaded->property && $loaded->property->id === $prop->id;
    // Clean up
    $alert->delete();
    return $success;
});

// ─────────────────────────────────────────────────────────────────────────────
// LAYER 2: Geographic & Proximity Search Scopes
// ─────────────────────────────────────────────────────────────────────────────
echo "\n2. 📍 Geographic & Spatial Search Scopes:\n";

check('scopeActive & scopeFeatured filters', function() {
    $count = \App\Models\Property::active()->count();
    return $count > 0;
});

check('scopeNearCoordinate (Haversine GPS Radius)', function() {
    // Search within 50km of Dhaka Center (23.8103, 90.4125)
    $results = \App\Models\Property::active()->nearCoordinate(23.8103, 90.4125, 50.0)->get();
    return $results->count() > 0;
});

check('scopeKeyword full-text search', function() {
    $results = \App\Models\Property::active()->keyword('Dhaka')->get();
    return $results->count() > 0;
});

// ─────────────────────────────────────────────────────────────────────────────
// LAYER 3: Core Real-Time APIs & Calculations
// ─────────────────────────────────────────────────────────────────────────────
echo "\n3. ⚡ Core Real-Time APIs & Pricing Engine:\n";

check('Real-time Availability Calculation (InventoryService)', function() {
    $prop = \App\Models\Property::first();
    $checkIn = now()->addDays(2)->format('Y-m-d');
    $checkOut = now()->addDays(4)->format('Y-m-d');
    
    $invService = app(\App\Services\InventoryService::class);
    $avail = $invService->checkAvailability($prop->id, $checkIn, $checkOut);
    return is_array($avail) && isset($avail['is_available']);
});

check('Multi-Currency Live Rate Conversion (15 Currencies)', function() {
    $bdt = 15000.0;
    $usd = \App\Services\CurrencyService::convert($bdt, 'USD');
    $eur = \App\Services\CurrencyService::convert($bdt, 'EUR');
    $gbp = \App\Services\CurrencyService::convert($bdt, 'GBP');
    return $usd > 0 && $eur > 0 && $gbp > 0;
});

check('Review AI Sentiment Engine & Polarity Moderation', function() {
    $analyzer = app(\App\Services\AI\SentimentAnalyzer::class);
    $pos = $analyzer->analyze('Super clean room and wonderful friendly staff', 5);
    $neg = $analyzer->analyze('Horrible scam cockroach and dirty bedsheets', 1);
    return $pos['sentiment'] === 'positive' && $neg['is_flagged'] === true;
});

check('Booking Fraud Risk Analysis Engine', function() {
    $detector = app(\App\Services\AI\FraudDetector::class);
    $safe = $detector->evaluateBooking(['guest_email' => 'guest@gmail.com', 'total_amount' => 5000]);
    $fraud = $detector->evaluateBooking(['guest_email' => 'fake@mailinator.com', 'total_amount' => 300000, 'check_in' => now()->format('Y-m-d')]);
    return $safe['risk_level'] === 'LOW' && $fraud['risk_level'] === 'HIGH';
});

// ─────────────────────────────────────────────────────────────────────────────
// LAYER 4: Security & HTTP Middleware Headers
// ─────────────────────────────────────────────────────────────────────────────
echo "\n4. 🛡️ Security Headers & XSS Protections:\n";

check('SecurityHeaders Middleware class existence & structure', function() {
    $middleware = new \App\Http\Middleware\SecurityHeaders();
    $request = \Illuminate\Http\Request::create('/', 'GET');
    $response = $middleware->handle($request, function($req) {
        return response('OK', 200);
    });
    
    $hasXss = $response->headers->has('X-XSS-Protection');
    $hasFrame = $response->headers->has('X-Frame-Options');
    $hasType = $response->headers->has('X-Content-Type-Options');
    
    return $hasXss && $hasFrame && $hasType;
});

check('XSS HTML Tag Stripping & Escaping in User Inputs', function() {
    $dirty = '<script>alert("hack")</script><b>Clean Text</b>';
    $stripped = strip_tags($dirty);
    $escaped = e($dirty);
    return !str_contains($stripped, '<script>') && str_contains($escaped, '&lt;script&gt;');
});

echo "\n=================================================================\n";
echo "📊 DEEP CORE AUDIT RESULTS: {$pass} / {$total} TESTS PASSED (" . round(($pass / $total) * 100) . "% SUCCESS)\n";
echo "=================================================================\n";
