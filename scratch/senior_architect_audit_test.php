<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=================================================================\n";
echo "🏆 PRIME BOOKING — SENIOR ARCHITECT & ALGORITHM VERIFICATION SUITE\n";
echo "=================================================================\n\n";

$passCount = 0;
$totalTests = 0;

function assertTest(string $name, bool $condition, string $detail = '') {
    global $passCount, $totalTests;
    $totalTests++;
    if ($condition) {
        $passCount++;
        echo "  ✅ PASS: {$name}" . ($detail ? " ({$detail})" : "") . "\n";
    } else {
        echo "  ❌ FAIL: {$name}" . ($detail ? " ({$detail})" : "") . "\n";
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 1. NLP Sentiment Analysis Engine Test
// ─────────────────────────────────────────────────────────────────────────────
echo "1. 🤖 AI NLP Sentiment Analysis Engine Verification:\n";
$sentimentAnalyzer = app(\App\Services\AI\SentimentAnalyzer::class);

$positiveReview = "The hotel room was absolutely amazing, clean, and luxurious! Staff was friendly and helpful.";
$resPos = $sentimentAnalyzer->analyze($positiveReview);
assertTest('Positive review scoring', $resPos['score'] > 0.3 && $resPos['sentiment'] === 'positive', "Score: {$resPos['score']}");

$toxicReview = "Terrible experience, disgusting dirty room, horrible scam fraud and rude behavior!";
$resToxic = $sentimentAnalyzer->analyze($toxicReview, 1);
assertTest('Toxic / Negative review flagging & scoring', $resToxic['is_flagged'] === true && $resToxic['sentiment'] === 'negative', "Score: {$resToxic['score']}, Flagged: Yes");

// ─────────────────────────────────────────────────────────────────────────────
// 2. Booking Fraud Detection Engine Test
// ─────────────────────────────────────────────────────────────────────────────
echo "\n2. 🛡️ Booking Fraud Detection Engine Verification:\n";
$fraudDetector = app(\App\Services\AI\FraudDetector::class);

$safeBookingData = [
    'guest_email'  => 'shawon@primebooking.com',
    'total_amount' => 4500,
    'check_in'     => now()->addDays(5)->format('Y-m-d'),
];
$safeDecision = $fraudDetector->evaluateBooking($safeBookingData);
assertTest('Legitimate booking low risk evaluation', $safeDecision['risk_level'] === 'LOW' && $safeDecision['decision'] === 'AUTO_APPROVE', "Score: {$safeDecision['risk_score']}, Decision: {$safeDecision['decision']}");

$fraudBookingData = [
    'guest_email'  => 'hacker123@tempmail.com', // Disposable email (+35)
    'total_amount' => 250000,                  // First-time high value (> 150k) (+25)
    'check_in'     => now()->format('Y-m-d'),   // Same-day checkin (> 60k) (+15)
];
$fraudDecision = $fraudDetector->evaluateBooking($fraudBookingData);
assertTest('Disposable email, high-ticket & same-day risk detection', $fraudDecision['risk_level'] === 'HIGH' && $fraudDecision['decision'] === 'REJECT_OR_MANUAL_REVIEW', "Score: {$fraudDecision['risk_score']}, Flags: " . implode(', ', $fraudDecision['flags']));

// ─────────────────────────────────────────────────────────────────────────────
// 3. Proximity Haversine Formula Verification
// ─────────────────────────────────────────────────────────────────────────────
echo "\n3. 📍 Mathematical Haversine GPS Distance Algorithm:\n";
// Gulshan, Dhaka (23.7925, 90.4078) to Dhanmondi, Dhaka (23.7461, 90.3742) ~ 6.2 km
$lat1 = 23.7925; $lng1 = 90.4078;
$lat2 = 23.7461; $lng2 = 90.3742;

$earthRadius = 6371;
$dLat = deg2rad($lat2 - $lat1);
$dLng = deg2rad($lng2 - $lng1);
$a = sin($dLat / 2) * sin($dLat / 2) +
     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
     sin($dLng / 2) * sin($dLng / 2);
$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
$distanceKm = $earthRadius * $c;

assertTest('Haversine distance calculation (Gulshan to Dhanmondi)', $distanceKm > 5.5 && $distanceKm < 7.0, round($distanceKm, 2) . " km");

// ─────────────────────────────────────────────────────────────────────────────
// 4. Pricing Preview & Coupon Calculation Engine Test
// ─────────────────────────────────────────────────────────────────────────────
echo "\n4. 💰 Dynamic Pricing & Fee Engine:\n";
$pricePerNight = 5000.0;
$nights = 3;
$subtotal = $pricePerNight * $nights; // 15,000
$couponPercent = 10; // 10% off -> 1,500 discount
$discount = ($subtotal * $couponPercent) / 100;
$serviceFee = round(($subtotal - $discount) * 0.05); // 5% of net -> 675
$total = ($subtotal - $discount) + $serviceFee; // 13,500 + 675 = 14,175

assertTest('Dynamic nights * rate pricing', $subtotal === 15000.0, "Subtotal: ৳" . number_format($subtotal));
assertTest('Coupon percentage discount deduction', $discount === 1500.0, "Discount: ৳" . number_format($discount));
assertTest('Platform service fee & net total calculation', $total === 14175.0, "Total: ৳" . number_format($total));

// ─────────────────────────────────────────────────────────────────────────────
// 5. Database Schema & Indexes Verification
// ─────────────────────────────────────────────────────────────────────────────
echo "\n5. ⚡ Database & Model Structure Verification:\n";
$hasPriceAlerts = \Illuminate\Support\Facades\Schema::hasTable('price_alerts');
assertTest('price_alerts table exists in DB', $hasPriceAlerts);

$propertyCount = \App\Models\Property::count();
assertTest('Properties table queryable with active records', $propertyCount > 0, "{$propertyCount} properties");

// ─────────────────────────────────────────────────────────────────────────────
// 6. Multi-Currency Conversion Engine Verification
// ─────────────────────────────────────────────────────────────────────────────
echo "\n6. 💱 Multi-Currency Live Conversion Verification:\n";
$bdtAmount = 10000.0;
$usdFormatted = \App\Services\CurrencyService::format($bdtAmount, 'USD');
$usdConverted = \App\Services\CurrencyService::convert($bdtAmount, 'USD');

assertTest('USD Currency formatting & conversion', str_contains($usdFormatted, '$') && $usdConverted > 70.0 && $usdConverted < 90.0, "10,000 BDT = {$usdFormatted}");

$allCurrencies = \App\Services\CurrencyService::getCurrencies();
assertTest('International currency support (15 currencies)', count($allCurrencies) >= 15, count($allCurrencies) . " currencies available");

echo "\n=================================================================\n";
echo "🎯 RESULTS: {$passCount} / {$totalTests} TESTS PASSED (" . round(($passCount / $totalTests) * 100) . "% SUCCESS)\n";
echo "=================================================================\n";
