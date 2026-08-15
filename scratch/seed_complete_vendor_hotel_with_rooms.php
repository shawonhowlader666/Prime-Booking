<?php

require __DIR__ . (file_exists(__DIR__ . '/vendor/autoload.php') ? '/vendor/autoload.php' : '/../vendor/autoload.php');
$app = require_once __DIR__ . (file_exists(__DIR__ . '/bootstrap/app.php') ? '/bootstrap/app.php' : '/../bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Str;

echo "==========================================================\n";
echo "▶ SEEDING VENDOR HOTEL WITH ROOMS, IMAGES & VIDEO\n";
echo "==========================================================\n";

// 1. Get or create vendor user
$vendor = User::where('role', 'vendor')->first();
if (!$vendor) {
    $vendor = User::create([
        'name' => 'Prime Luxury Partner',
        'email' => 'vendor@primebooking.com.bd',
        'phone' => '+8801711000111',
        'password' => bcrypt('password123'),
        'role' => 'vendor',
        'email_verified_at' => now(),
    ]);
    echo "✔ Created new vendor user: {$vendor->email} (ID: {$vendor->id})\n";
} else {
    echo "✔ Found active vendor user: {$vendor->email} (ID: {$vendor->id})\n";
}

// 2. Define Hotel Property Details
$propertyName = "The Grand Horizon Luxury Palace & Water Villas";
$slug = Str::slug($propertyName);

$propertyImages = [
    "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1400&q=85",
    "https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80",
    "https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80",
    "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80",
    "https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=1200&q=80",
    "https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80"
];

$propertyData = [
    'vendor_id' => $vendor->id,
    'name' => $propertyName,
    'type' => 'resort',
    'description' => "Welcome to The Grand Horizon Luxury Palace & Water Villas, an ultra-premium beachfront paradise. Featuring breathtaking sunset ocean views, private overwater bungalows, infinity swimming pool, world-class fine dining, and private beach access. Perfect for luxury family vacations, romantic honeymoons, and peaceful retreats.",
    'city' => "Cox's Bazar",
    'address' => "Marine Drive, Inani Beach, Cox's Bazar, Bangladesh",
    'nearest_landmark' => "Inani Beach & Himchari National Park",
    'latitude' => "21.312845",
    'longitude' => "92.046875",
    'star_rating' => 5,
    'rating_score' => 9.8,
    'total_reviews' => 148,
    'location_score' => 9.9,
    'price_per_night' => 8500.00,
    'original_price' => 12500.00,
    'primary_image' => $propertyImages[0],
    'images' => $propertyImages,
    'video_url' => "https://www.youtube.com/embed/dQw4w9WgXcQ",
    'free_cancellation' => 1,
    'no_credit_card_required' => 0,
    'is_featured' => 1,
    'status' => 'published',
    'amenities' => [
        'Free High-Speed Wi-Fi',
        'Infinity Swimming Pool',
        'Private Beach Access',
        'Complimentary Buffet Breakfast',
        'Airport Shuttle Service',
        'Luxury Spa & Wellness Center',
        'Fitness Gym',
        '24/7 Room Service',
        'Sea View Balcony',
        'Fine Dining Restaurant & Bar'
    ],
    'checkin_time' => '14:00',
    'checkout_time' => '12:00',
];

if (\Illuminate\Support\Facades\Schema::hasColumn('properties', 'cancellation_policy')) {
    $propertyData['cancellation_policy'] = 'Free cancellation up to 48 hours before check-in.';
}

$property = Property::updateOrCreate(
    ['slug' => $slug],
    $propertyData
);

echo "✔ Created/Updated Property: '{$property->name}' (ID: {$property->id})\n";
echo "   URL Slug: {$property->slug}\n";

// 3. Define Rooms
$roomsData = [
    [
        'name' => 'Presidential Oceanfront Penthouse Suite',
        'room_type' => 'suite',
        'price_per_night' => 15000.00,
        'max_guests' => 4,
        'max_adults' => 4,
        'max_children' => 2,
        'room_size_sqm' => 85,
        'bed_type' => '1 King Bed + 1 Queen Bed',
        'total_rooms' => 4,
        'available_rooms' => 4,
        'free_cancellation' => 1,
        'breakfast_included' => 1,
        'view_type' => 'Direct Ocean View',
        'images' => [
            "https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80"
        ],
        'amenities' => ['Sea view', 'Private Jacuzzi', 'Balcony', 'King bed', 'Free breakfast', 'Mini bar', 'Air conditioning', 'Smart TV', 'Espresso machine'],
        'facilities' => ['Sea view', 'Private Jacuzzi', 'Balcony', 'King bed', 'Free breakfast', 'Mini bar', 'Air conditioning', 'Smart TV', 'Espresso machine']
    ],
    [
        'name' => 'Deluxe Overwater Villa',
        'room_type' => 'deluxe',
        'price_per_night' => 10500.00,
        'max_guests' => 2,
        'max_adults' => 2,
        'max_children' => 1,
        'room_size_sqm' => 60,
        'bed_type' => '1 King Bed',
        'total_rooms' => 6,
        'available_rooms' => 6,
        'free_cancellation' => 1,
        'breakfast_included' => 1,
        'view_type' => 'Overwater Lagoon View',
        'images' => [
            "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=1200&q=80"
        ],
        'amenities' => ['Direct water access', 'Balcony', 'Bathtub', 'Ocean view', 'Free Wi-Fi', 'Complimentary drinks', 'Room service'],
        'facilities' => ['Direct water access', 'Balcony', 'Bathtub', 'Ocean view', 'Free Wi-Fi', 'Complimentary drinks', 'Room service']
    ],
    [
        'name' => 'Superior Garden & Pool View Deluxe Room',
        'room_type' => 'deluxe',
        'price_per_night' => 6500.00,
        'max_guests' => 2,
        'max_adults' => 2,
        'max_children' => 1,
        'room_size_sqm' => 42,
        'bed_type' => '2 Twin Beds or 1 Queen Bed',
        'total_rooms' => 10,
        'available_rooms' => 10,
        'free_cancellation' => 1,
        'breakfast_included' => 1,
        'view_type' => 'Garden & Swimming Pool View',
        'images' => [
            "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80"
        ],
        'amenities' => ['Garden view', 'Pool view', 'Air conditioning', 'Work desk', 'Safe', 'Free Wi-Fi'],
        'facilities' => ['Garden view', 'Pool view', 'Air conditioning', 'Work desk', 'Safe', 'Free Wi-Fi']
    ]
];

$today = Carbon::today();

foreach ($roomsData as $rData) {
    $room = Room::updateOrCreate(
        [
            'property_id' => $property->id,
            'name' => $rData['name']
        ],
        array_merge($rData, [
            'status' => 'active'
        ])
    );
    echo "  -> Created Room: '{$room->name}' (Price: ৳{$room->price_per_night})\n";

    // Seed 90 days availability
    for ($i = 0; $i < 90; $i++) {
        $date = $today->copy()->addDays($i)->format('Y-m-d');
        $availData = [
            'price' => $room->price_per_night,
            'available_qty' => $room->total_rooms,
            'available_count' => $room->total_rooms,
            'is_available' => 1,
            'is_blocked' => 0,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('room_availabilities', 'property_id')) {
            $availData['property_id'] = $property->id;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('room_availabilities', 'vendor_id')) {
            $availData['vendor_id'] = $vendor->id;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('room_availabilities', 'status')) {
            $availData['status'] = 'available';
        }
        RoomAvailability::updateOrCreate(
            [
                'room_id' => $room->id,
                'date' => $date
            ],
            $availData
        );
    }
}

echo "\n🌟 HOTEL & ROOMS SUCCESSFULLY SEEDED!\n";
echo "Public Page URL: /property/{$property->slug} or /hotels/{$property->id}\n";
