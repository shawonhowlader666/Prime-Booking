<?php

require __DIR__ . (file_exists(__DIR__ . '/vendor/autoload.php') ? '/vendor/autoload.php' : '/../vendor/autoload.php');
$app = require_once __DIR__ . (file_exists(__DIR__ . '/bootstrap/app.php') ? '/bootstrap/app.php' : '/../bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;

echo "==========================================================\n";
echo "▶ SIMULATING MANUAL VENDOR WEB FORM SUBMISSIONS (HTTP POST)\n";
echo "==========================================================\n";

// 1. Authenticate as Vendor User
$vendor = User::where('role', 'vendor')->first();
auth()->login($vendor);
echo "✔ Authenticated as Vendor: {$vendor->email} (ID: {$vendor->id})\n";

// 2. Simulate Vendor Property Creation Form Submission (POST /vendor/properties)
$propertyPayload = [
    'name' => 'Royal Paradise Beach Resort & Eco Spa',
    'type' => 'resort',
    'city' => "Cox's Bazar",
    'address' => 'Sea Beach Road, Kolatoli, Cox\'s Bazar, Bangladesh',
    'nearest_landmark' => 'Sugandha Beach & Kolatoli Point',
    'latitude' => '21.427218',
    'longitude' => '91.970215',
    'star_rating' => 5,
    'price_per_night' => 7500,
    'original_price' => 11000,
    'description' => 'Experience the pinnacle of luxury at Royal Paradise Beach Resort & Eco Spa. Located right in front of the world longest natural sea beach in Cox\'s Bazar. Features an infinity swimming pool, sea-view balconies, multi-cuisine restaurant, and complimentary airport shuttle.',
    'amenities' => ['Free Wi-Fi', 'Swimming Pool', 'Private Beach Area', 'Free Breakfast', 'Airport Transfer', 'Spa & Massage', 'Fitness Center', 'Room Service'],
    'primary_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
    'images' => [
        'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80'
    ],
    'video_url' => 'https://www.youtube.com/embed/ScMzIvxBSi4',
    'checkin_time' => '14:00',
    'checkout_time' => '12:00',
    'free_cancellation' => 1,
    'no_credit_card_required' => 1,
];

echo "\n[*] Executing App\Http\Controllers\Vendor\VendorController::storeProperty()...\n";

$request = Illuminate\Http\Request::create('/vendor/properties', 'POST', $propertyPayload);
$request->setUserResolver(fn() => $vendor);
auth()->setUser($vendor);

$vendorController = app(\App\Http\Controllers\Vendor\VendorController::class);
try {
    $response = $vendorController->storeProperty($request);
    echo "  -> Property Creation Result Type: " . get_class($response) . "\n";
} catch (\Exception $e) {
    echo "  ✖ Exception: " . $e->getMessage() . "\n";
}

$createdProperty = Property::where('name', 'Royal Paradise Beach Resort & Eco Spa')->first();
if ($createdProperty) {
    echo "  ✔ Successfully created Property ID #{$createdProperty->id} (Slug: {$createdProperty->slug})\n";
    echo "  ✔ Video URL: {$createdProperty->video_url}\n";
    echo "  ✔ Images count: " . count($createdProperty->images ?? []) . "\n";

    // 3. Simulate Vendor Room Creation Form Submission
    $roomPayloads = [
        [
            'name' => 'Royal Sea View King Suite',
            'room_type' => 'suite',
            'price_per_night' => 12000,
            'max_adults' => 3,
            'max_children' => 2,
            'room_size_sqm' => 70,
            'bed_type' => '1 Super King Bed',
            'total_rooms' => 5,
            'available_rooms' => 5,
            'breakfast_included' => 1,
            'free_cancellation' => 1,
            'view_type' => 'Full Ocean View',
            'amenities' => ['Ocean view balcony', 'Bathtub', 'Air conditioning', 'Smart TV', 'Free Wi-Fi', 'Complimentary Breakfast'],
            'facilities' => ['Ocean view balcony', 'Bathtub', 'Air conditioning', 'Smart TV', 'Free Wi-Fi', 'Complimentary Breakfast'],
            'images' => [
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80'
            ]
        ],
        [
            'name' => 'Deluxe Pool Access Premier Room',
            'room_type' => 'deluxe',
            'price_per_night' => 7500,
            'max_adults' => 2,
            'max_children' => 1,
            'room_size_sqm' => 45,
            'bed_type' => '1 King Bed or 2 Twin Beds',
            'total_rooms' => 8,
            'available_rooms' => 8,
            'breakfast_included' => 1,
            'free_cancellation' => 1,
            'view_type' => 'Pool View',
            'amenities' => ['Direct Pool Access', 'Balcony', 'Mini Bar', 'Air conditioning', 'Free Wi-Fi'],
            'facilities' => ['Direct Pool Access', 'Balcony', 'Mini Bar', 'Air conditioning', 'Free Wi-Fi'],
            'images' => [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80'
            ]
        ]
    ];

    $roomController = app(\App\Http\Controllers\Vendor\VendorRoomController::class);

    foreach ($roomPayloads as $rPayload) {
        echo "\n[*] Executing VendorRoomController::store() for '{$rPayload['name']}'...\n";
        $roomRequest = Illuminate\Http\Request::create("/vendor/properties/{$createdProperty->id}/rooms", 'POST', $rPayload);
        $roomRequest->setUserResolver(fn() => $vendor);
        try {
            $rResponse = $roomController->store($roomRequest, $createdProperty->id);
            echo "  -> Room Creation Result Type: " . get_class($rResponse) . "\n";
        } catch (\Exception $e) {
            echo "  ✖ Room Exception: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🌟 HOTEL & ROOMS SUCCESSFULLY ADDED VIA VENDOR CONTROLLER ACTION!\n";
    echo "Public URL: https://primebooking.com.bd/property/{$createdProperty->slug}\n";
} else {
    echo "✖ Property was not created.\n";
}
