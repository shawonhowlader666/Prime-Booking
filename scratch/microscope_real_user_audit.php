<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\UserReward;
use App\Models\RewardTransaction;
use App\Models\RewardPayoutRequest;
use App\Services\VIPLoyaltyService;
use App\Services\RewardPointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "====================================================================================\n";
echo "🔬 END-TO-END MICROSCOPIC REAL-USER LIVE JOURNEY AUDIT (NO SEEDERS / RAW DATABASE)\n";
echo "====================================================================================\n\n";

$startTime = microtime(true);
$totalAssertions = 0;
$passedAssertions = 0;

function assertCheck($label, $condition, &$total, &$passed, $extra = '') {
    $total++;
    if ($condition) {
        $passed++;
        echo "   ✅ [PASS] " . str_pad($label, 50, '.') . " {$extra}\n";
    } else {
        echo "   ❌ [FAIL] " . str_pad($label, 50, '.') . " {$extra}\n";
        throw new \Exception("Assertion Failed: {$label}");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 1: REAL USER REGISTRATION & REWARDS INITIALIZATION
// ─────────────────────────────────────────────────────────────────────────────
echo "📌 STAGE 1: USER REGISTRATION & WALLET INITIALIZATION\n";

$uniqueSuffix = time() . '_' . rand(100, 999);
$realUser = User::create([
    'name'              => "Microscope Real User {$uniqueSuffix}",
    'email'             => "real_user_{$uniqueSuffix}@primebooking.test",
    'password'          => Hash::make('SecretPass123!'),
    'email_verified_at' => now(),
]);

assertCheck("User created in database", $realUser && $realUser->id > 0, $totalAssertions, $passedAssertions, "User ID: #{$realUser->id}");

$rewardService = app(RewardPointService::class);
$initialSummary = $rewardService->getUserRewardSummary($realUser);

assertCheck("Initial points balance is 0", $initialSummary['points_balance'] === 0, $totalAssertions, $passedAssertions, "0 Pts (= ৳0.00)");
assertCheck("Withdrawal locked (< 100 pts)", $initialSummary['can_withdraw'] === false, $totalAssertions, $passedAssertions, "Locked (0/100 Pts)");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 2: POINTSMAX AIRLINE PROGRAM LINKING
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 2: POINTSMAX AIRLINE LOYALTY PROGRAM LINKING\n";

$pointsmaxProgram = [
    'id'            => uniqid(),
    'program'       => 'Singapore Airlines KrisFlyer',
    'membership_id' => 'SQ987654321',
    'is_primary'    => true,
    'linked_at'     => now()->toFormattedDateString(),
];

$realUser->pointsmax_programs = json_encode([$pointsmaxProgram]);
$realUser->save();
$realUser->refresh();

$savedPrograms = json_decode($realUser->pointsmax_programs, true);
assertCheck("PointsMAX Singapore Airlines Linked", count($savedPrograms) === 1 && $savedPrograms[0]['membership_id'] === 'SQ987654321', $totalAssertions, $passedAssertions, "KrisFlyer #SQ987654321");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 3: PROPERTY DISCOVERY & REAL LIVE BOOKING #1 (৳25,000 SPEND)
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 3: REAL HOTEL BOOKING #1 (৳25,000 SPEND)\n";

$property = Property::first();
if (!$property) {
    $property = Property::create([
        'name'            => 'The Grand Palace Resort',
        'city'            => 'Cox\'s Bazar',
        'country'         => 'Bangladesh',
        'price_per_night' => 12500,
        'status'          => 'active',
        'address'         => 'Marine Drive',
    ]);
}

$booking1Ref = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));
$booking1 = Booking::create([
    'booking_reference' => $booking1Ref,
    'property_id'       => $property->id,
    'user_id'           => $realUser->id,
    'guest_name'        => $realUser->name,
    'guest_email'       => $realUser->email,
    'guest_phone'       => '01712345678',
    'check_in'          => now()->addDays(3)->toDateString(),
    'check_out'         => now()->addDays(5)->toDateString(),
    'guests'            => 2,
    'nights'            => 2,
    'price_per_night'   => 12500,
    'subtotal'          => 25000,
    'tax_amount'        => 1875,
    'total_price'       => 26875,
    'total_amount'      => 26875,
    'currency'          => 'BDT',
    'payment_method'    => 'bkash',
    'payment_status'    => 'paid',
    'status'            => 'confirmed',
    'booking_status'    => 'confirmed',
]);

assertCheck("Booking #1 stored in DB", $booking1 && $booking1->id > 0, $totalAssertions, $passedAssertions, "Ref: {$booking1->booking_reference}");

// Auto-Credit Points: ৳26,875 paid -> floor(26875 / 1000) = 26 Points
$earned1 = $rewardService->creditBookingPoints($booking1);
assertCheck("Reward points auto-credited (1k=1pt)", $earned1 === 26, $totalAssertions, $passedAssertions, "+26 Points (floor(26875/1000))");

$summaryAfterB1 = $rewardService->getUserRewardSummary($realUser);
assertCheck("Wallet balance updated to 26 Pts", $summaryAfterB1['points_balance'] === 26, $totalAssertions, $passedAssertions, "26 Pts (= ৳260.00)");
assertCheck("Milestone Progress is 26%", $summaryAfterB1['progress_percent'] == 26, $totalAssertions, $passedAssertions, "26/100 Points");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 4: REAL HOTEL BOOKING #2 (৳80,000 SPEND -> CROSS 100 POINTS THRESHOLD)
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 4: REAL HOTEL BOOKING #2 (৳80,000 SPEND -> 100+ PTS UNLOCK)\n";

$booking2Ref = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));
$booking2 = Booking::create([
    'booking_reference' => $booking2Ref,
    'property_id'       => $property->id,
    'user_id'           => $realUser->id,
    'guest_name'        => $realUser->name,
    'guest_email'       => $realUser->email,
    'guest_phone'       => '01712345678',
    'check_in'          => now()->addDays(10)->toDateString(),
    'check_out'         => now()->addDays(14)->toDateString(),
    'guests'            => 2,
    'nights'            => 4,
    'price_per_night'   => 20000,
    'subtotal'          => 80000,
    'tax_amount'        => 6000,
    'total_price'       => 86000,
    'total_amount'      => 86000,
    'currency'          => 'BDT',
    'payment_method'    => 'sslcommerz',
    'payment_status'    => 'paid',
    'status'            => 'confirmed',
    'booking_status'    => 'confirmed',
]);

$earned2 = $rewardService->creditBookingPoints($booking2);
assertCheck("Booking #2 points auto-credited", $earned2 === 86, $totalAssertions, $passedAssertions, "+86 Points (floor(86000/1000))");

$summaryAfterB2 = $rewardService->getUserRewardSummary($realUser);
$expectedTotalPts = 26 + 86; // 112 Pts
assertCheck("Total points accumulated: 112 Pts", $summaryAfterB2['points_balance'] === $expectedTotalPts, $totalAssertions, $passedAssertions, "112 Pts (= ৳1,120.00)");
assertCheck("100 Points Milestone UNLOCKED", $summaryAfterB2['can_withdraw'] === true, $totalAssertions, $passedAssertions, "🎉 WITHDRAWAL UNLOCKED (100%)");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 5: WITHDRAWAL REQUEST (BKASH 100 POINTS = ৳1,000)
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 5: WITHDRAWAL & PAYOUT REQUEST TO BKASH\n";

$payoutResult = $rewardService->requestPayout($realUser, 100, 'bkash', '01799998888', 'Personal bKash');
assertCheck("Payout request processed", $payoutResult['success'] === true, $totalAssertions, $passedAssertions, "৳1,000 to bKash (01799998888)");

$summaryAfterPayoutReq = $rewardService->getUserRewardSummary($realUser);
assertCheck("100 points deducted from active wallet", $summaryAfterPayoutReq['points_balance'] === 12, $totalAssertions, $passedAssertions, "Remaining: 12 Pts (= ৳120.00)");

$payoutRecord = RewardPayoutRequest::where('user_id', $realUser->id)->latest()->first();
assertCheck("Payout record pending in DB", $payoutRecord && $payoutRecord->status === 'pending', $totalAssertions, $passedAssertions, "Request #REQ-{$payoutRecord->id}");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 6: ADMIN OBSERVATION & APPROVAL DISPATCH
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 6: ADMIN CENTRAL OBSERVATION & PAYOUT APPROVAL\n";

$adminApproval = $rewardService->approvePayout($payoutRecord, 'Approved & Paid via bKash Merchant API (TRX-998877)');
assertCheck("Admin approved payout", $adminApproval === true, $totalAssertions, $passedAssertions, "Dispatched via TRX-998877");

$payoutRecord->refresh();
assertCheck("Payout status is 'approved'", $payoutRecord->status === 'approved', $totalAssertions, $passedAssertions, "Status: Approved & Processed");

// ─────────────────────────────────────────────────────────────────────────────
// STAGE 7: AUDIT TRAIL IMMUTABLE TRANSACTION LOG
// ─────────────────────────────────────────────────────────────────────────────
echo "\n📌 STAGE 7: IMMUTABLE AUDIT TRAIL VERIFICATION\n";

$transactions = RewardTransaction::where('user_id', $realUser->id)->get();
assertCheck("Audit ledger has 3 events", $transactions->count() === 3, $totalAssertions, $passedAssertions, "Earned #1, Earned #2, Payout");

$earnedCount = $transactions->where('type', 'earned')->count();
$payoutCount = $transactions->where('type', 'payout')->count();
assertCheck("Ledger event types verified", $earnedCount === 2 && $payoutCount === 1, $totalAssertions, $passedAssertions, "2 Earned Logs, 1 Payout Log");

$elapsed = round((microtime(true) - $startTime) * 1000, 2);

echo "\n====================================================================================\n";
echo "🏆 MICROSCOPIC CORE AUDIT PASSED: {$passedAssertions} / {$totalAssertions} ASSERTIONS | {$elapsed}ms | ZERO DEFECTS\n";
echo "====================================================================================\n";
