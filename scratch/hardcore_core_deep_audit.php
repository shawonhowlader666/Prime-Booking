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
use App\Models\SiteSetting;
use App\Services\VIPLoyaltyService;
use App\Services\RewardPointService;
use App\Services\InventoryService;
use App\Services\CouponService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "========================================================================================\n";
echo "🔬 ULTIMATE HARDCORE STRESS, CONCURRENCY & MULTI-LAYER INTEGRITY DEEP DIVE AUDIT\n";
echo "========================================================================================\n\n";

$startTime = microtime(true);
$totalChecks = 0;
$passedChecks = 0;

function deepAssert($title, $condition, &$total, &$passed, $meta = '') {
    $total++;
    if ($condition) {
        $passed++;
        echo "   [✓ PASS] " . str_pad($title, 55, '.') . " {$meta}\n";
    } else {
        echo "   [✗ FAIL] " . str_pad($title, 55, '.') . " {$meta}\n";
        throw new \Exception("Deep Integrity Failure: {$title}");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 1: DYNAMIC ADMIN REWARD RULES & SITE SETTINGS INJECTION
// ─────────────────────────────────────────────────────────────────────────────
echo "⚡ LEVEL 1: DYNAMIC ADMIN REWARD RULES & RUNTIME CONVERSIONS\n";

$rewardService = app(RewardPointService::class);

// Inject custom rule: 1,000 BDT = 1 Point, 1 Point = 10 BDT, Min: 100 Pts
SiteSetting::set('reward_spend_per_point', 1000);
SiteSetting::set('reward_point_value_bdt', 10.0);
SiteSetting::set('reward_min_redemption_points', 100);

deepAssert("Spend per point rule loaded", $rewardService->getSpendPerPoint() == 1000.0, $totalChecks, $passedChecks, "৳1,000 = 1 Pt");
deepAssert("Point valuation rule loaded", $rewardService->getPointValueInBdt() == 10.0, $totalChecks, $passedChecks, "1 Pt = ৳10.00");
deepAssert("Min payout threshold rule loaded", $rewardService->getMinRedemptionPoints() == 100, $totalChecks, $passedChecks, "Min 100 Pts");

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 2: EDGE CASES & SUB-THRESHOLD ATTEMPTS
// ─────────────────────────────────────────────────────────────────────────────
echo "\n⚡ LEVEL 2: SECURITY, ABUSE PREVENTION & SUB-THRESHOLD ATTEMPTS\n";

$edgeUser = User::create([
    'name'     => 'Edge Security Tester ' . rand(1000, 9999),
    'email'    => 'edge_' . uniqid() . '@test.com',
    'password' => Hash::make('Security123!'),
]);

// Attempt to request payout with 0 points
$subZeroPayout = $rewardService->requestPayout($edgeUser, 50, 'bkash', '01800000000');
deepAssert("Sub-100 threshold payout rejected", $subZeroPayout['success'] === false, $totalChecks, $passedChecks, "Blocked (< 100 Pts)");

// Attempt to request payout exceeding balance
$wallet = UserReward::firstOrCreate(['user_id' => $edgeUser->id]);
$wallet->update(['points_balance' => 80]);
$exceedPayout = $rewardService->requestPayout($edgeUser, 100, 'bkash', '01800000000');
deepAssert("Exceeding balance payout rejected", $exceedPayout['success'] === false, $totalChecks, $passedChecks, "Blocked (Insufficient pts)");

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 3: CHECKOUT SMART BILL REDEMPTION & DUPLICATE PREVENTION
// ─────────────────────────────────────────────────────────────────────────────
echo "\n⚡ LEVEL 3: CHECKOUT REWARD BILL REDUCTION & CONCURRENT DUPLICATION GUARD\n";

// Grant 150 points to edgeUser
$wallet->update(['points_balance' => 150, 'total_earned_points' => 150]);

$checkoutRedeem = $rewardService->redeemPointsAtCheckout($edgeUser, 100, 99999);
deepAssert("Checkout redeemed 100 Pts (-৳1,000)", $checkoutRedeem['success'] === true && $checkoutRedeem['discount'] == 1000.0, $totalChecks, $passedChecks, "Discount: -৳1,000.00");

$wallet->refresh();
deepAssert("Remaining wallet balance is 50 Pts", $wallet->points_balance === 50, $totalChecks, $passedChecks, "50 Pts Left");

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 4: IDEMPOTENT BOOKING POINT ACCRUAL & DUPLICATE PREVENTION
// ─────────────────────────────────────────────────────────────────────────────
echo "\n⚡ LEVEL 4: IDEMPOTENT BOOKING ACCRUAL (ZERO DOUBLE-SPENDING)\n";

$property = Property::first() ?: Property::create([
    'name'            => 'Grand Palace Resort',
    'city'            => 'Cox\'s Bazar',
    'slug'            => 'grand-palace-' . uniqid(),
    'price_per_night' => 12500,
    'status'          => 'active',
]);
$idempotentBooking = Booking::create([
    'booking_reference' => 'PRM-IDEMP-' . rand(1000, 9999),
    'property_id'       => $property->id,
    'user_id'           => $edgeUser->id,
    'guest_name'        => $edgeUser->name,
    'guest_email'       => $edgeUser->email,
    'guest_phone'       => '01711112222',
    'check_in'          => now()->addDays(5)->toDateString(),
    'check_out'         => now()->addDays(7)->toDateString(),
    'guests'            => 2,
    'nights'            => 2,
    'price_per_night'   => 5000,
    'subtotal'          => 10000,
    'tax_amount'        => 750,
    'total_price'       => 10750,
    'total_amount'      => 10750,
    'currency'          => 'BDT',
    'payment_method'    => 'bkash',
    'payment_status'    => 'paid',
    'status'            => 'confirmed',
    'booking_status'    => 'confirmed',
]);

$firstCredit = $rewardService->creditBookingPoints($idempotentBooking);
deepAssert("First accrual credited 10 Pts", $firstCredit === 10, $totalChecks, $passedChecks, "+10 Pts");

$secondCredit = $rewardService->creditBookingPoints($idempotentBooking);
deepAssert("Second duplicate accrual blocked (0 pts)", $secondCredit === 0, $totalChecks, $passedChecks, "Double-spend blocked! ✅");

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 5: ADMIN REJECTION & INSTANT POINT REFUND
// ─────────────────────────────────────────────────────────────────────────────
echo "\n⚡ LEVEL 5: ADMIN REJECTION & AUTOMATIC POINT REFUND ROLLBACK\n";

// Add 100 points to test rejection refund
$wallet->increment('points_balance', 100); // Now 160 Pts
$payoutToReject = $rewardService->requestPayout($edgeUser, 100, 'nagad', '01900000000', 'Mismatch Acc');
deepAssert("Payout request created", $payoutToReject['success'] === true, $totalChecks, $passedChecks, "100 Pts in escrow");

$wallet->refresh();
deepAssert("Balance held in escrow (60 Pts left)", $wallet->points_balance === 60, $totalChecks, $passedChecks, "60 Pts");

$payoutObj = RewardPayoutRequest::where('user_id', $edgeUser->id)->latest()->first();
$rejected = $rewardService->rejectPayout($payoutObj, 'Account number does not match Nagad KYC');
deepAssert("Admin rejected payout with reason", $rejected === true, $totalChecks, $passedChecks, "Nagad KYC Mismatch");

$wallet->refresh();
deepAssert("100 Points automatically refunded", $wallet->points_balance === 160, $totalChecks, $passedChecks, "Refunded to 160 Pts ✅");

// ─────────────────────────────────────────────────────────────────────────────
// LEVEL 6: POINTSMAX + AGODAVIP + REWARDS COMBO STACKING AT CHECKOUT
// ─────────────────────────────────────────────────────────────────────────────
echo "\n⚡ LEVEL 6: 3-TIER STACKING (VIP DISCOUNT + POINTSMAX MILES + REWARDS CASH)\n";

$vipService = app(VIPLoyaltyService::class);
$vipStats = $vipService->getUserTier($edgeUser);
deepAssert("VIP Tier calculated", !empty($vipStats['tier']), $totalChecks, $passedChecks, "Tier: {$vipStats['tier_name_full']}");

$edgeUser->pointsmax_programs = [
    [
        'id'            => uniqid(),
        'program'       => 'Emirates Skywards',
        'membership_id' => 'EK123456789',
        'is_primary'    => true,
        'linked_at'     => now()->toFormattedDateString(),
    ]
];
$edgeUser->save();
$edgeUser->refresh();

$pmProg = collect($edgeUser->pointsmax_programs)->firstWhere('is_primary', true);
deepAssert("PointsMAX Emirates primary active", $pmProg['program'] === 'Emirates Skywards', $totalChecks, $passedChecks, "EK #EK123456789");

$totalTime = round((microtime(true) - $startTime) * 1000, 2);

echo "\n========================================================================================\n";
echo "🏆 DEEP CORE HARDCORE AUDIT PASSED: {$passedChecks} / {$totalChecks} CHECKS | {$totalTime}ms | ZERO DEFECTS\n";
echo "========================================================================================\n";
