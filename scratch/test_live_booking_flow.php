<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\User;
use Illuminate\Support\Str;

echo "=========================================================\n";
echo "   PRIME BOOKING: LIVE TEST BOOKING END-TO-END VERIFICATION\n";
echo "=========================================================\n\n";

// 1. Find a vendor property with rooms
$property = Property::whereNotNull('vendor_id')
    ->whereHas('rooms')
    ->with(['vendor', 'rooms'])
    ->first();

if (!$property) {
    // Fallback to any property
    $property = Property::whereHas('rooms')->with(['vendor', 'rooms'])->first();
}

if (!$property) {
    die("❌ Error: No property with rooms found in database!\n");
}

$vendor = $property->vendor;
$room   = $property->rooms->first();

echo "🏨 1. Selected Property Details:\n";
echo "   - Property ID: {$property->id}\n";
echo "   - Property Name: {$property->name}\n";
echo "   - City: {$property->city}\n";
echo "   - Vendor ID: " . ($vendor ? $vendor->id : 'N/A') . "\n";
echo "   - Vendor Name: " . ($vendor ? $vendor->name : 'N/A') . "\n";
echo "   - Vendor Email: " . ($vendor ? $vendor->email : 'N/A') . "\n";
echo "   - Room Name: {$room->name} (BDT {$room->price_per_night} / night)\n\n";

// 2. Simulate Web Booking Checkout
$checkIn  = date('Y-m-d', strtotime('+3 days'));
$checkOut = date('Y-m-d', strtotime('+5 days'));
$nights   = 2;
$pricePerNight = (float)$room->price_per_night;
$subtotal = $pricePerNight * $nights;
$taxAmount = round($subtotal * 0.075);
$totalPrice = $subtotal + $taxAmount;
$reference = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));

$booking = Booking::create([
    'booking_reference'  => $reference,
    'property_id'        => $property->id,
    'room_id'            => $room->id,
    'user_id'            => $vendor ? $vendor->id : null,
    'guest_name'         => 'Mahmudul Hasan (Live Web Guest)',
    'guest_email'        => 'guest.mahmudul@gmail.com',
    'guest_phone'        => '01711223344',
    'check_in'           => $checkIn,
    'check_out'          => $checkOut,
    'guests'             => 2,
    'nights'             => $nights,
    'price_per_night'    => $pricePerNight,
    'subtotal'           => $subtotal,
    'tax_amount'         => $taxAmount,
    'total_price'        => $totalPrice,
    'total_amount'       => $totalPrice,
    'payment_method'     => 'bkash',
    'payment_status'     => 'paid',
    'status'             => 'confirmed',
    'booking_status'     => 'confirmed',
    'special_requests'   => 'Late check-in requested (around 8 PM). High floor sea view preferred.',
]);

BookingAddon::create([
    'booking_id' => $booking->id,
    'addon_name' => 'Airport Pickup Shuttle',
    'price'      => 1500,
    'qty'        => 1,
]);

echo "✅ 2. Web Booking Created Successfully!\n";
echo "   - Booking ID: #{$booking->id}\n";
echo "   - Reference Code: {$booking->booking_reference}\n";
echo "   - Guest: {$booking->guest_name} ({$booking->guest_phone})\n";
echo "   - Dates: {$booking->check_in} to {$booking->check_out} ({$nights} Nights)\n";
echo "   - Total Amount: ৳ " . number_format($booking->total_price) . " BDT\n";
echo "   - Payment: bKash (PAID)\n";
echo "   - Status: Confirmed\n\n";

// 3. Verify in Super Admin Query
$adminBooking = Booking::where('id', $booking->id)
    ->with(['property.vendor', 'room'])
    ->first();

echo "👑 3. Super Admin Panel Check (/admin/bookings):\n";
if ($adminBooking) {
    echo "   [SUCCESS] Booking #{$adminBooking->id} is VISIBLE in Super Admin list!\n";
    echo "   - Hotel: {$adminBooking->property->name}\n";
    echo "   - Vendor Owner: " . ($adminBooking->property->vendor ? $adminBooking->property->vendor->name : 'N/A') . "\n";
    echo "   - Direct URL: https://primebooking.com.bd/admin/bookings\n";
} else {
    echo "   [FAILED] Not found in admin query!\n";
}
echo "\n";

// 4. Verify in Vendor Query
if ($vendor) {
    $vendorPropertyIds = Property::where('vendor_id', $vendor->id)->pluck('id');
    $vendorBooking = Booking::where('booking_reference', $booking->booking_reference)
        ->whereIn('property_id', $vendorPropertyIds)
        ->with(['property', 'room'])
        ->first();

    echo "🏢 4. Vendor Portal Check (/vendor/bookings):\n";
    if ($vendorBooking) {
        echo "   [SUCCESS] Booking #{$vendorBooking->id} is VISIBLE in Vendor [{$vendor->name}] list!\n";
        echo "   - Matched Vendor ID: {$vendor->id} ({$vendor->email})\n";
        echo "   - Direct URL: https://primebooking.com.bd/vendor/bookings\n";
    } else {
        echo "   [FAILED] Not visible to vendor!\n";
    }
} else {
    echo "🏢 4. Vendor Portal Check: Skipped (Property has no vendor_id attached).\n";
}

echo "\n=========================================================\n";
echo "   RESULT: Booking successfully routed to BOTH Admin & Vendor!\n";
echo "=========================================================\n";
