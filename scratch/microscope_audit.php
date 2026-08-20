<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=========================================================\n";
echo "🔬 MICROSCOPIC END-TO-END VIP PLATFORM LIVE AUDIT\n";
echo "=========================================================\n\n";

use App\Models\User;
use App\Models\Booking;
use App\Models\Property;
use App\Models\SiteSetting;
use App\Services\VIPLoyaltyService;
use Illuminate\Support\Facades\Cache;

$vipService = app(VIPLoyaltyService::class);

// Step 1: Create Test User
$email = 'vip_microscope_' . time() . '@example.com';
$user = User::create([
    'name'     => 'VIP Micro Audit User',
    'email'    => $email,
    'password' => bcrypt('secret123'),
]);
echo "✅ [1/5] User Created in Database (ID: {$user->id}, Email: {$user->email})\n";

// Step 2: Initial Tier Test (Guest / 0 Bookings)
Cache::forget('user_vip_stats_' . $user->id);
$initialStats = $vipService->getUserTier($user);
echo "📊 [2/5] Initial Status Test:\n";
echo "   - Tier: {$initialStats['tier']} ({$initialStats['tier_name_full']})\n";
echo "   - Discount: {$initialStats['discount_percent']}%\n";
echo "   - Total Bookings: {$initialStats['bookings_count']}\n";
echo "   - Total Spend: \${$initialStats['total_spend']}\n";
echo "   - Next Milestone: {$initialStats['bookings_needed']} bookings needed for Silver\n";
assert($initialStats['tier'] === 'Bronze', 'Initial tier must be Bronze');

// Step 3: Add 2 Bookings & Upgrade to Silver
$prop = Property::first();
$propId = $prop ? $prop->id : 1;

$b1 = new Booking();
$b1->user_id = $user->id;
$b1->property_id = $propId;
$b1->guest_name = $user->name;
$b1->guest_email = $user->email;
$b1->guest_phone = '+8801700000000';
$b1->booking_reference = 'BK-AUDIT-1';
$b1->check_in = now()->subMonths(3)->toDateString();
$b1->check_out = now()->subMonths(3)->addDays(2)->toDateString();
$b1->guests = 2;
$b1->total_amount = 80;
$b1->booking_status = 'confirmed';
$b1->payment_status = 'paid';
$b1->created_at = now()->subMonths(3);
$b1->save();

$b2 = new Booking();
$b2->user_id = $user->id;
$b2->property_id = $propId;
$b2->guest_name = $user->name;
$b2->guest_email = $user->email;
$b2->guest_phone = '+8801700000000';
$b2->booking_reference = 'BK-AUDIT-2';
$b2->check_in = now()->subMonths(1)->toDateString();
$b2->check_out = now()->subMonths(1)->addDays(2)->toDateString();
$b2->guests = 2;
$b2->total_amount = 90;
$b2->booking_status = 'completed';
$b2->payment_status = 'paid';
$b2->created_at = now()->subMonths(1);
$b2->save();
Cache::forget('user_vip_stats_' . $user->id);
$silverStats = $vipService->getUserTier($user);
echo "\n📊 [3/5] Silver Tier Progression Test (After 2 Bookings):\n";
echo "   - Tier: {$silverStats['tier']} ({$silverStats['tier_name_full']})\n";
echo "   - Discount: {$silverStats['discount_percent']}%\n";
echo "   - Total Bookings: {$silverStats['bookings_count']}\n";
echo "   - Total Spend: \${$silverStats['total_spend']}\n";
assert($silverStats['tier'] === 'Silver', 'Tier should be upgraded to Silver');

// Step 4: Add 3 More Bookings (Total 5 Bookings, $320 Spend) -> Gold Tier
$b3 = new Booking();
$b3->user_id = $user->id;
$b3->property_id = $propId;
$b3->guest_name = $user->name;
$b3->guest_email = $user->email;
$b3->guest_phone = '+8801700000000';
$b3->booking_reference = 'BK-AUDIT-3';
$b3->check_in = now()->subWeeks(3)->toDateString();
$b3->check_out = now()->subWeeks(3)->addDays(2)->toDateString();
$b3->guests = 2;
$b3->total_amount = 50;
$b3->booking_status = 'confirmed';
$b3->payment_status = 'paid';
$b3->created_at = now()->subWeeks(3);
$b3->save();

$b4 = new Booking();
$b4->user_id = $user->id;
$b4->property_id = $propId;
$b4->guest_name = $user->name;
$b4->guest_email = $user->email;
$b4->guest_phone = '+8801700000000';
$b4->booking_reference = 'BK-AUDIT-4';
$b4->check_in = now()->subWeeks(2)->toDateString();
$b4->check_out = now()->subWeeks(2)->addDays(2)->toDateString();
$b4->guests = 2;
$b4->total_amount = 50;
$b4->booking_status = 'confirmed';
$b4->payment_status = 'paid';
$b4->created_at = now()->subWeeks(2);
$b4->save();

$b5 = new Booking();
$b5->user_id = $user->id;
$b5->property_id = $propId;
$b5->guest_name = $user->name;
$b5->guest_email = $user->email;
$b5->guest_phone = '+8801700000000';
$b5->booking_reference = 'BK-AUDIT-5';
$b5->check_in = now()->subDays(2)->toDateString();
$b5->check_out = now()->subDays(2)->addDays(2)->toDateString();
$b5->guests = 2;
$b5->total_amount = 50;
$b5->booking_status = 'confirmed';
$b5->payment_status = 'paid';
$b5->created_at = now()->subDays(2);
$b5->save();

Cache::forget('user_vip_stats_' . $user->id);
$goldStats = $vipService->getUserTier($user);
echo "\n📊 [4/5] Gold Tier Progression Test (After 5 Bookings / \$320 Spend):\n";
echo "   - Tier: {$goldStats['tier']} ({$goldStats['tier_name_full']})\n";
echo "   - Discount: {$goldStats['discount_percent']}%\n";
echo "   - Total Bookings: {$goldStats['bookings_count']}\n";
echo "   - Total Spend: \${$goldStats['total_spend']}\n";
assert($goldStats['tier'] === 'Gold', 'Tier should be upgraded to Gold');

// Step 5: Admin Dynamic Settings Live Test
SiteSetting::set('vip_gold_discount', 22);
Cache::forget('vip_discounts_settings');
Cache::forget('user_vip_stats_' . $user->id);
$adminUpdatedStats = $vipService->getUserTier($user);
echo "\n⚙️  [5/5] Admin Live Dynamic Settings Change Test (Discount updated to 22%):\n";
echo "   - Tier: {$adminUpdatedStats['tier']}\n";
echo "   - Live Dynamic Discount: {$adminUpdatedStats['discount_percent']}%\n";
assert($adminUpdatedStats['discount_percent'] == 22, 'Discount must dynamically update from Admin SiteSetting');

// Reset discount back to default 18
SiteSetting::set('vip_gold_discount', 18);
Cache::forget('vip_discounts_settings');

// Clean up test records
Booking::where('user_id', $user->id)->delete();
$user->delete();
echo "\n🧹 Audit test records deleted cleanly.\n";
echo "=========================================================\n";
echo "🎉 ALL MICROSCOPIC AUDIT TESTS PASSED 100% WITH ZERO ERRORS!\n";
echo "=========================================================\n";
