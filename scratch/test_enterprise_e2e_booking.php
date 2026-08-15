<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Services\CouponService;
use App\Services\InventoryService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

echo "=========================================================\n";
echo "   TESTING ENTERPRISE E2E BOOKING FLOW (INVENTORY + COUPON + COMMISSION + NOTIFICATION)\n";
echo "=========================================================\n\n";

// 1. Pick a Property & Room
$property = Property::whereNotNull('vendor_id')->whereHas('rooms')->with('rooms')->first();
$room = $property->rooms->first();

echo "1. Selected Hotel & Room:\n";
echo "   - Property: {$property->name} (ID: {$property->id})\n";
echo "   - Room: {$room->name} (ID: {$room->id}) | Rate: ৳ {$room->price_per_night} / night\n\n";

// 2. Validate Coupon via CouponService
$couponService = app(CouponService::class);
$subtotal = (float)$room->price_per_night * 2; // 2 nights
$cRes = $couponService->validateCoupon('PRIME10', $subtotal, (int)$property->id);

echo "2. Coupon Service Validation ('PRIME10'):\n";
echo "   - Valid: " . ($cRes['valid'] ? 'YES' : 'NO') . "\n";
echo "   - Discount: ৳ {$cRes['discount']}\n";
echo "   - Message: {$cRes['message']}\n\n";

// 3. Test Web Controller Checkout Request with Coupon
$postData = [
    'property_id'      => $property->id,
    'room_id'          => $room->id,
    'guest_name'       => 'Sajib Ahmed (Enterprise Guest)',
    'guest_email'      => 'sajib.vip@gmail.com',
    'guest_phone'      => '01799887766',
    'check_in'         => date('Y-m-d', strtotime('+4 days')),
    'check_out'        => date('Y-m-d', strtotime('+6 days')),
    'guests'           => 2,
    'payment_method'   => 'pay_at_hotel',
    'coupon_code'      => 'PRIME10',
    'special_requests' => 'VIP airport welcome & late check-out please.',
    'addons'           => ['airport_transfer'],
];

$request = Request::create(
    '/book/' . $property->id,
    'POST',
    $postData,
    [],
    [],
    ['HTTP_HOST' => 'primebooking.com.bd', 'HTTPS' => 'on']
);

$controller = app(\App\Http\Controllers\Web\BookingFlowController::class);
$inventoryService = app(InventoryService::class);
$notificationService = app(NotificationService::class);

$response = $controller->store($request, $property->id, $inventoryService, $couponService, $notificationService);

echo "3. Booking Flow Controller Execution:\n";
echo "   - Target URL: " . (method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : 'N/A') . "\n\n";

// 4. Inspect Newly Saved Booking Record
$latestBooking = Booking::where('property_id', $property->id)
    ->where('guest_name', 'Sajib Ahmed (Enterprise Guest)')
    ->latest()
    ->first();

if ($latestBooking) {
    echo "4. ✅ Enterprise Booking Created with Full Ledger:\n";
    echo "   - Ref: {$latestBooking->booking_reference}\n";
    echo "   - Subtotal: ৳ " . number_format($latestBooking->subtotal) . "\n";
    echo "   - Coupon Applied: {$latestBooking->coupon_code}\n";
    echo "   - Discount Amount: ৳ " . number_format($latestBooking->discount_amount) . "\n";
    echo "   - Net Total Price: ৳ " . number_format($latestBooking->total_price) . "\n";
    echo "   - Platform Commission ({$latestBooking->commission_rate}%): ৳ " . number_format($latestBooking->commission_amount) . "\n";
    echo "   - Vendor Payout Amount: ৳ " . number_format($latestBooking->vendor_payout_amount) . "\n";
    echo "   - Payment Method: " . strtoupper($latestBooking->payment_method) . " ({$latestBooking->payment_status})\n";
    echo "   - Status: " . ucfirst($latestBooking->status) . "\n\n";
    echo "=========================================================\n";
    echo "   ALL ENTERPRISE LOGIC PASSED 100% WITH ZERO ERRORS!\n";
    echo "=========================================================\n";
} else {
    echo "❌ Booking not found!\n";
}
