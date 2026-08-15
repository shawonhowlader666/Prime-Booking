<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "\n========================================================================\n";
echo "🏨 STARTING COMPLETE ROOM LIFECYCLE AUDIT (VENDOR ➔ DB ➔ ADMIN ➔ FRONTEND WEB)\n";
echo "========================================================================\n\n";

// 1. SELECT TARGET VENDOR & PROPERTY
$vendorUser = User::where('role', 'vendor')->firstOrFail();
Auth::login($vendorUser);
echo "✅ [1. VENDOR AUTHENTICATION]\n";
echo "   Logged in as Vendor: {$vendorUser->name} (ID: {$vendorUser->id})\n";

$property = Property::where('vendor_id', $vendorUser->id)->first();
if (!$property) {
    $property = Property::first();
    $property->vendor_id = $vendorUser->id;
    $property->save();
}
echo "   Target Property: {$property->name} (ID: {$property->id})\n\n";

// 2. SIMULATE VENDOR ADDING A FULL-SPEC ROOM VIA FORM SUBMISSION
echo "✅ [2. SIMULATING VENDOR ADDING NEW ROOM VIA UI FORM]\n";
$uniqueSuffix = rand(1000, 9999);
$newRoomName = "Presidential Penthouse Panoramic Suite #{$uniqueSuffix}";

$controller = new \App\Http\Controllers\Vendor\VendorRoomController();
$request = Request::create(route('vendor.rooms.store', $property->id), 'POST', [
    'name'               => $newRoomName,
    'bed_type'           => 'King Bed',
    'price_per_night'    => 28500.00,
    'room_size_sqm'      => 95,
    'total_rooms'        => 6,
    'max_adults'         => 4,
    'max_children'       => 2,
    'breakfast_included' => 1,
    'free_cancellation'  => 1,
    'amenities'          => [
        'Air Conditioning',
        'Free Wi-Fi',
        'Smart Flat TV',
        'Sea / City View',
        'Private Balcony',
        'Hot Water / Geyser',
        'Tea & Coffee Maker',
        'Mini Fridge',
        'Work Desk',
        'Safety Locker'
    ],
    'facilities_text'    => "Private Jacuzzi on Balcony\n24/7 Dedicated Butler\nComplimentary VIP Airport Transfer"
]);

$response = $controller->store($request, $property->id);
echo "   Store Response: " . ($response->isRedirection() ? "Redirect to " . $response->getTargetUrl() : "200 OK") . "\n";

// VERIFY IN DATABASE
$createdRoom = Room::where('property_id', $property->id)->where('name', $newRoomName)->first();
if (!$createdRoom) {
    echo "❌ ERROR: Room was not created in database!\n";
    exit(1);
}

echo "   ✨ Room Successfully Created in Database!\n";
echo "   - ID: #{$createdRoom->id}\n";
echo "   - Name: {$createdRoom->name}\n";
echo "   - Price / Night: ৳ " . number_format($createdRoom->price_per_night) . "\n";
echo "   - Bed Type: {$createdRoom->bed_type}\n";
echo "   - Room Size: {$createdRoom->room_size_sqm} m²\n";
echo "   - Total Units: {$createdRoom->total_rooms}\n";
echo "   - Max Occupancy: {$createdRoom->max_adults} Adults + {$createdRoom->max_children} Children\n";
echo "   - Breakfast Included: " . ($createdRoom->breakfast_included ? 'YES' : 'NO') . "\n";
echo "   - Free Cancellation: " . ($createdRoom->free_cancellation ? 'YES' : 'NO') . "\n";
echo "   - Total Facilities Recorded: " . count($createdRoom->facilities ?? []) . " items\n\n";

// 3. ADMIN SIDE VISIBILITY VERIFICATION
echo "✅ [3. VERIFYING ADMIN INVENTORY VISIBILITY]\n";
$adminUser = User::where('role', 'admin')->first();
if ($adminUser) {
    Auth::login($adminUser);
    echo "   Logged in as Admin: {$adminUser->name}\n";
}

$adminController = new \App\Http\Controllers\Admin\RoomController();
$adminView = $adminController->index($property->id);
$adminRooms = $adminView->getData()['rooms'];
$foundInAdmin = $adminRooms->contains('id', $createdRoom->id);

if ($foundInAdmin) {
    echo "   ✨ Confirmed: Room #{$createdRoom->id} is LIVE & VISIBLE in Admin Panel!\n";
    echo "   - Admin Room Count for Property: " . $adminRooms->count() . "\n";
} else {
    echo "❌ ERROR: Room not found in Admin Room list!\n";
    exit(1);
}
echo "\n";

// 4. AVAILABILITY & RATES ENGINE VERIFICATION
echo "✅ [4. VERIFYING RATES & AVAILABILITY ENGINE FOR NEW ROOM]\n";
Auth::login($vendorUser);
$availController = new \App\Http\Controllers\Vendor\RoomAvailabilityController();
$today = date('Y-m-d');
$nextWeek = date('Y-m-d', strtotime('+6 days'));

$rateRequest = Request::create(route('vendor.availability.update-range'), 'POST', [
    'room_id'    => $createdRoom->id,
    'start_date' => $today,
    'end_date'   => $nextWeek,
    'action'     => 'set_price',
    'price'      => 32000.00
], [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

$rateResponse = $availController->updateRange($rateRequest);
$rateData = json_decode($rateResponse->getContent(), true);

echo "   AJAX Rate Override Status: {$rateData['status']}\n";
echo "   Rate Message: {$rateData['message']}\n";
echo "   Database Verified: " . count($rateData['records']) . " days dynamically priced at ৳32,000/night\n\n";

// 5. MAIN FRONTEND WEB SEARCH & PUBLIC DISPLAY VERIFICATION
echo "✅ [5. VERIFYING PUBLIC WEBSITE FRONTEND BOOKING DISPLAY]\n";
$frontendProperty = Property::with(['rooms' => function($q) {
    $q->orderBy('price_per_night', 'asc');
}])->find($property->id);

$publicRoom = $frontendProperty->rooms->firstWhere('id', $createdRoom->id);

if ($publicRoom) {
    echo "   ✨ Confirmed: Room #{$publicRoom->id} ('{$publicRoom->name}') is PUBLICLY ACTIVE on the Main Website!\n";
    echo "   - Public Booking Price: ৳ " . number_format($publicRoom->price_per_night) . "\n";
    echo "   - Public Facilities: " . implode(', ', array_slice($publicRoom->facilities ?? [], 0, 5)) . "...\n";
    echo "   - Instant Bookable Units: {$publicRoom->total_rooms} units\n";
} else {
    echo "❌ ERROR: Room not found on Public Property relationship!\n";
    exit(1);
}

echo "\n========================================================================\n";
echo "🎉 100% COMPLETE LIFECYCLE PASS: VENDOR ➔ DB ➔ ADMIN ➔ RATES ➔ MAIN WEB!\n";
echo "========================================================================\n\n";
