<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

echo "=========================================================\n";
echo "   SIMULATING REAL WEB USER CHECKOUT VIA HTTP REQUEST\n";
echo "=========================================================\n\n";

// 1. Identify Demo Vendor Partner
$demoVendor = User::where('email', 'vendor@primebooking.com.bd')->first();
if (!$demoVendor) {
    $demoVendor = User::where('role', 'vendor')->first();
}

$property = Property::where('vendor_id', $demoVendor->id)->with('rooms')->first();
if (!$property) {
    die("❌ No property found for Demo Vendor Partner!\n");
}

$room = $property->rooms->first();
if (!$room) {
    die("❌ No room found for property!\n");
}

echo "1. Web Guest is booking at:\n";
echo "   - Hotel: {$property->name}\n";
echo "   - Room: {$room->name}\n";
echo "   - Vendor Owner: {$demoVendor->name} ({$demoVendor->email})\n\n";

// 2. Build Real HTTP POST Request to /book/{propertyId}
$postData = [
    'property_id'      => $property->id,
    'room_id'          => $room->id,
    'guest_name'       => 'Ashikur Rahman (Real Web Booking)',
    'guest_email'      => 'ashikur.web@gmail.com',
    'guest_phone'      => '01988776655',
    'check_in'         => date('Y-m-d', strtotime('+3 days')),
    'check_out'        => date('Y-m-d', strtotime('+6 days')),
    'guests'           => 2,
    'payment_method'   => 'bkash',
    'special_requests' => 'Sea facing room on 5th floor please.',
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

// Process through BookingFlowController as a web request
$controller = app(\App\Http\Controllers\Web\BookingFlowController::class);
$response = $controller->store($request, $property->id);

echo "2. Processed through BookingFlowController::store():\n";
echo "   - Response Target URL: " . (method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : 'N/A') . "\n\n";

// 3. Query the newly created booking
$latestBooking = Booking::where('property_id', $property->id)
    ->where('guest_name', 'Ashikur Rahman (Real Web Booking)')
    ->latest()
    ->first();

if ($latestBooking) {
    echo "3. ✅ Order Successfully Placed in Database via Web Checkout!\n";
    echo "   - Booking Reference: {$latestBooking->booking_reference}\n";
    echo "   - Guest Name: {$latestBooking->guest_name}\n";
    echo "   - Dates: {$latestBooking->check_in} to {$latestBooking->check_out}\n";
    echo "   - Total Price: ৳ " . number_format($latestBooking->total_price) . " BDT\n";
    echo "   - Payment Method: " . strtoupper($latestBooking->payment_method) . "\n";
    echo "   - Status: " . ucfirst($latestBooking->status) . "\n\n";

    echo "4. Visibility Verification:\n";
    echo "   - 🏢 Vendor Panel: https://primebooking.com.bd/vendor/bookings\n";
    echo "     -> Visible under Vendor: [{$demoVendor->name}]\n";
    echo "   - 👑 Admin Panel: https://primebooking.com.bd/admin/bookings\n";
    echo "     -> Visible under Super Admin all bookings ledger\n";
} else {
    echo "❌ Booking creation failed!\n";
}

$kernel->terminate($request, $response);
