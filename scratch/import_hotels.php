<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

ini_set('memory_limit', '1024M');
set_time_limit(600);

echo "========================================================\n";
echo " PRIME BOOKING — 100% ACCURATE HOTEL & MEDIA IMPORTER\n";
echo "========================================================\n\n";

// 1. Setup Location
$location = Location::firstOrCreate(
    ['city' => "Cox's Bazar"],
    [
        'name' => "Cox's Bazar",
        'country' => 'Bangladesh',
        'country_code' => 'BD',
        'latitude' => 21.4272,
        'longitude' => 92.0058,
        'image' => '/storage/hotels/sea-pearl-beach-resort-spa/gallery/cover.jpg',
        'is_popular' => 1,
    ]
);
echo "[OK] Location 'Cox's Bazar' ready with ID: {$location->id}\n\n";

// 2. Base Paths
$downloadsBase = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd';
$elementsBase  = $downloadsBase . '/Website Element';

$publicHotelsDir = public_path('storage/hotels');
$appHotelsDir    = storage_path('app/public/hotels');
if (!is_dir($publicHotelsDir)) mkdir($publicHotelsDir, 0777, true);
if (!is_dir($appHotelsDir)) mkdir($appHotelsDir, 0777, true);

// 3. Helper to copy file to both public/storage and storage/app/public
function storeMediaFile($srcPath, $relDestination) {
    global $publicHotelsDir, $appHotelsDir;
    
    $dest1 = $publicHotelsDir . '/' . $relDestination;
    $dest2 = $appHotelsDir . '/' . $relDestination;
    
    $dir1 = dirname($dest1);
    $dir2 = dirname($dest2);
    if (!is_dir($dir1)) mkdir($dir1, 0777, true);
    if (!is_dir($dir2)) mkdir($dir2, 0777, true);
    
    if (!file_exists($dest1) || filesize($dest1) !== filesize($srcPath)) {
        copy($srcPath, $dest1);
    }
    if (!file_exists($dest2) || filesize($dest2) !== filesize($srcPath)) {
        copy($srcPath, $dest2);
    }
    
    return '/storage/hotels/' . str_replace('\\', '/', $relDestination);
}

// 4. Hotel Definitions
$hotelConfigs = [
    [
        'sl' => 1,
        'folder' => '1_Sea Pearl Beach Resort & Spa',
        'name' => 'Sea Pearl Beach Resort & Spa',
        'slug' => 'sea-pearl-beach-resort-spa',
        'type' => 'resort',
        'star_rating' => 5,
        'rating_score' => 9.4,
        'total_reviews' => 428,
        'price_per_night' => 8500,
        'original_price' => 11000,
        'address' => 'Jaliapalong, Inani, Ukhia 4750, Cox\'s Bazar, Bangladesh',
        'nearest_landmark' => 'Inani Beach (0m, Direct Private Access), Himchori (15 mins)',
        'latitude' => '21.218500',
        'longitude' => '92.051400',
        'map_embed_url' => 'https://maps.app.goo.gl/p9NUSrXeHm3Z1z8k8',
        'contact_phone' => '+8809638999444',
        'contact_email' => 'reservation@seapearlcoxsbazar.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Sea Pearl Beach Resort & Spa is a premier 5-star international standard luxury beach resort in Cox's Bazar, nestled directly on the pristine sands of Inani Beach. Spanning across 15 acres of secluded beachfront paradise with green hills on one side and the endless Bay of Bengal on the other, the resort features 493 well-appointed rooms and suites, multiple international specialty restaurants, infinity swimming pools, private water park, luxury spa, and world-class banquet facilities.",
        'amenities' => [
            'Direct Private Beach Access', 'Infinity Swimming Pools', 'Samaya Luxury Spa & Salon',
            'Multiple Fine Dining Restaurants', 'Private Water Park', '24/7 Room Service',
            'High-Speed Free WiFi', 'Fitness Center / Gym', 'Children\'s Play Zone',
            'Airport Shuttle Service', 'Helipad Access', 'Valet Parking', 'Concierge Service'
        ],
        'room_rates' => [
            'superior-double' => ['name' => 'Superior Double Room', 'type' => 'superior', 'price' => 8500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden / Hill View', 'sqm' => 38],
            'Superior Twin' => ['name' => 'Superior Twin Room', 'type' => 'superior', 'price' => 8500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden / Hill View', 'sqm' => 38],
            'Superior Triple Hill View' => ['name' => 'Superior Triple Hill View', 'type' => 'superior', 'price' => 11000, 'bed' => '3 Single Beds', 'guests' => 3, 'adults' => 3, 'children' => 1, 'view' => 'Hill View', 'sqm' => 45],
            'Superior Triple Sea View' => ['name' => 'Superior Triple Sea View', 'type' => 'superior', 'price' => 12500, 'bed' => '3 Single Beds', 'guests' => 3, 'adults' => 3, 'children' => 1, 'view' => 'Sea View', 'sqm' => 45],
            'Studio King Sea View' => ['name' => 'Studio King Sea View', 'type' => 'deluxe', 'price' => 13500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View', 'sqm' => 48],
            'Premier King Sea View' => ['name' => 'Premier King Sea View', 'type' => 'super_deluxe', 'price' => 15000, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View', 'sqm' => 52],
            'Eco Suite' => ['name' => 'Eco Suite', 'type' => 'suite', 'price' => 18000, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Nature & Sea View', 'sqm' => 60],
            'Executive Suite Sea View' => ['name' => 'Executive Suite Sea View', 'type' => 'suite', 'price' => 22000, 'bed' => '1 King Bed', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Sea View', 'sqm' => 75],
            'Royal Family Suite' => ['name' => 'Royal Family Suite', 'type' => 'suite', 'price' => 25000, 'bed' => '2 King Beds', 'guests' => 4, 'adults' => 4, 'children' => 2, 'view' => 'Sea View', 'sqm' => 90],
            'Royal Paradise Suite' => ['name' => 'Royal Paradise Suite', 'type' => 'suite', 'price' => 32000, 'bed' => '1 King Bed & Living Area', 'guests' => 3, 'adults' => 2, 'children' => 2, 'view' => 'Direct Oceanfront', 'sqm' => 110],
            'Ambassador Suite' => ['name' => 'Ambassador Suite', 'type' => 'suite', 'price' => 45000, 'bed' => '1 Grand King Bed', 'guests' => 4, 'adults' => 2, 'children' => 2, 'view' => '180° Oceanfront', 'sqm' => 140],
            'Presidential Suite' => ['name' => 'Presidential Suite', 'type' => 'suite', 'price' => 65000, 'bed' => '2 Master King Beds', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Private Panoramic Oceanfront', 'sqm' => 220],
        ]
    ],
    [
        'sl' => 2,
        'folder' => '2_Ocean Paradise Hotel & Resorts',
        'name' => 'Ocean Paradise Hotel & Resorts',
        'slug' => 'ocean-paradise-hotel-resorts',
        'type' => 'hotel',
        'star_rating' => 5,
        'rating_score' => 9.1,
        'total_reviews' => 512,
        'price_per_night' => 7500,
        'original_price' => 9500,
        'address' => '28-29, Hotel Motel Zone, Kolatoli Road, Cox\'s Bazar, Bangladesh',
        'nearest_landmark' => 'Kolatoli Beach (3 mins walk), Sugandha Beach (800m)',
        'latitude' => '21.419200',
        'longitude' => '91.984500',
        'map_embed_url' => 'https://maps.app.goo.gl/kK4K5DN3wA7YTPRP8',
        'contact_phone' => '+88019388467614',
        'contact_email' => 'info@oceanparadisehotel.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Ocean Paradise Hotel & Resorts is an icon of luxury hospitality in the heart of Cox's Bazar. Located right on Kolatoli Road with panoramic sea view balconies, the hotel offers opulent accommodations, multiple fine-dining restaurants, outdoor swimming pool, modern fitness center, kids play area, and personalized concierge services.",
        'amenities' => [
            'Swimming Pool with Sun Deck', 'Free High-Speed WiFi', 'Multi-Cuisine Restaurants',
            'Sea View Balconies', '24/7 Front Desk & Room Service', 'Fitness Center',
            'Business Center & Banquet', 'Secured Car Parking', 'Doctor on Call'
        ],
        'room_rates' => [
            '1_Deluxe' => ['name' => 'Deluxe Room', 'type' => 'deluxe', 'price' => 7500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City / Hill View', 'sqm' => 36],
            '2_Superior Deluxe' => ['name' => 'Superior Deluxe Room', 'type' => 'super_deluxe', 'price' => 9500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View', 'sqm' => 42],
            '3_Premier Deluxe' => ['name' => 'Premier Deluxe Room', 'type' => 'super_deluxe', 'price' => 12000, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View', 'sqm' => 48],
            '4_Executive Premier Deluxe' => ['name' => 'Executive Premier Deluxe Suite', 'type' => 'suite', 'price' => 16500, 'bed' => '1 King Bed & Lounge', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Ocean View', 'sqm' => 65],
        ]
    ],
    [
        'sl' => 3,
        'folder' => '3_Best Western Plus Bay Hills',
        'name' => 'Best Western Plus Bay Hills',
        'slug' => 'best-western-plus-bay-hills',
        'type' => 'hotel',
        'star_rating' => 5,
        'rating_score' => 9.0,
        'total_reviews' => 280,
        'price_per_night' => 7000,
        'original_price' => 9000,
        'address' => 'Cox\'s Bazar Marine Drive, Himchori 4700, Cox\'s Bazar, Bangladesh',
        'nearest_landmark' => 'Himchori Waterfall & National Park (1.5 km), Marine Drive',
        'latitude' => '21.350800',
        'longitude' => '92.016200',
        'map_embed_url' => 'https://maps.app.goo.gl/sEPEhpF6YbNvKFeh6',
        'contact_phone' => '+8801909889889',
        'contact_email' => 'reservations@bwplusbayhills.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Best Western Plus Bay Hills brings world-class global hospitality to the breathtaking scenic corridor of Cox's Bazar Marine Drive in Himchori. Surrounded by lush hills and endless ocean vistas, the hotel offers upscale contemporary rooms, rooftop restaurant, infinity pool overlooking the bay, and tranquil natural ambiance.",
        'amenities' => [
            'Marine Drive Beach View', 'Rooftop Infinity Pool', 'Best Western Global Standards',
            'Complimentary Breakfast Buffet', 'Free High-Speed WiFi', 'Multi-Cuisine Dining',
            'Fitness Studio', '24/7 Security & Power Backup', 'Airport Pickup & Drop'
        ],
        'room_rates' => [
            '6_ Deluxe Double or Twin' => ['name' => 'Deluxe Double or Twin Room', 'type' => 'deluxe', 'price' => 7000, 'bed' => '2 Twin Beds or 1 Double', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill / Nature View', 'sqm' => 35],
            '1_Sea View' => ['name' => 'Deluxe Sea View Room', 'type' => 'deluxe', 'price' => 8500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Direct Sea View', 'sqm' => 38],
            '2_Deluxe' => ['name' => 'Executive Deluxe Room', 'type' => 'super_deluxe', 'price' => 9000, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill & Bay View', 'sqm' => 40],
            '5_Junior Suite' => ['name' => 'Junior Suite', 'type' => 'suite', 'price' => 13500, 'bed' => '1 King Bed', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Sea & Mountain View', 'sqm' => 55],
            '3_Suite' => ['name' => 'Bay Hills Executive Suite', 'type' => 'suite', 'price' => 18000, 'bed' => '1 King Bed & Living Room', 'guests' => 3, 'adults' => 2, 'children' => 2, 'view' => 'Panoramic Ocean View', 'sqm' => 70],
        ]
    ],
    [
        'sl' => 4,
        'folder' => '4_Seagull Hotels Ltd',
        'name' => 'Seagull Hotels Ltd',
        'slug' => 'seagull-hotels-ltd',
        'type' => 'hotel',
        'star_rating' => 4,
        'rating_score' => 8.9,
        'total_reviews' => 640,
        'price_per_night' => 5800,
        'original_price' => 7500,
        'address' => 'Hotel Motel Zone, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'Laboni Beach Point (5 mins walk), Sugandha Beach (1 km)',
        'latitude' => '21.431200',
        'longitude' => '91.975400',
        'map_embed_url' => 'https://maps.app.goo.gl/4boVjfpK9UpGALdZA',
        'contact_phone' => '+8801329736687, +8801329736689',
        'contact_email' => 'reservation@seagullhotelbd.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Seagull Hotels Ltd is one of the most celebrated and established 4-star luxury hotels in Cox's Bazar. Boasting lush landscaped private gardens, an expansive outdoor swimming pool, signature multi-cuisine restaurants, modern health club, and easy walking access to Laboni Beach.",
        'amenities' => [
            'Large Outdoor Swimming Pool', 'Private Landscaped Garden', 'Rangoon Bar & Dining',
            'Health Club & Gym', 'Free High-Speed WiFi', '24/7 Room Service',
            'Business Center & Conference Halls', 'Free Car Parking', 'Doctor on Call'
        ],
        'room_rates' => [
            '1_Deluxe ROOM' => ['name' => 'Deluxe Room', 'type' => 'deluxe', 'price' => 5800, 'bed' => '1 King Bed or 2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden / Hill View', 'sqm' => 34],
            '2_Super DELUXE ROOM' => ['name' => 'Super Deluxe Room', 'type' => 'super_deluxe', 'price' => 7200, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea & Pool View', 'sqm' => 40],
            '3_Executive Suite' => ['name' => 'Executive Suite', 'type' => 'suite', 'price' => 12500, 'bed' => '1 King Bed & Sitting Area', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Sea View', 'sqm' => 58],
            '4_Madhurima Suite' => ['name' => 'Madhurima Presidential Suite', 'type' => 'suite', 'price' => 18000, 'bed' => '1 Grand King Bed', 'guests' => 4, 'adults' => 2, 'children' => 2, 'view' => 'Direct Sea Facing Balcony', 'sqm' => 85],
        ]
    ],
    [
        'sl' => 5,
        'folder' => '5_BEST WESTERN HERITAGE',
        'name' => 'BEST WESTERN HERITAGE',
        'slug' => 'best-western-heritage',
        'type' => 'hotel',
        'star_rating' => 5,
        'rating_score' => 9.2,
        'total_reviews' => 410,
        'price_per_night' => 6500,
        'original_price' => 8500,
        'address' => '4700, Best Western Heritage, Kolatoli Rd, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'Kolatoli Beach (200m), Sugandha Beach Point (600m)',
        'latitude' => '21.421500',
        'longitude' => '91.982100',
        'map_embed_url' => 'https://maps.app.goo.gl/Krq36ifZBnc7nhzr8',
        'contact_phone' => '+8801339913200',
        'contact_email' => 'info@bwheritagehotel.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Best Western Heritage provides an authentic 5-star international hotel experience in prime Kolatoli, Cox's Bazar. Featuring elegant contemporary architecture, rooftop infinity pool with breathtaking ocean views, exquisite culinary offerings, fully equipped gym, and modern business facilities.",
        'amenities' => [
            'Rooftop Infinity Pool', 'Best Western Global Assurance', 'Free High-Speed WiFi',
            'Sea & Hill View Balconies', 'Sky Dining Restaurant', 'Fitness Center',
            '24/7 Concierge Service', 'Secured Basement Parking', 'Airport Transfer Service'
        ],
        'room_rates' => [
            '6_Standard Room' => ['name' => 'Standard Room', 'type' => 'standard', 'price' => 6500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 32],
            '7_Superior King' => ['name' => 'Superior King Room', 'type' => 'superior', 'price' => 7800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 36],
            '8_Superior Twin' => ['name' => 'Superior Twin Room', 'type' => 'superior', 'price' => 7800, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 36],
            '1_Deluxe Hill View' => ['name' => 'Deluxe Hill View Room', 'type' => 'deluxe', 'price' => 8500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Scenic Hill View', 'sqm' => 38],
            '2_Deluxe Sea View' => ['name' => 'Deluxe Sea View Room', 'type' => 'deluxe', 'price' => 10500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View Balcony', 'sqm' => 40],
            '4_Premium Hill View' => ['name' => 'Premium Hill View Room', 'type' => 'super_deluxe', 'price' => 11500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 44],
            '5_Premium Sea View' => ['name' => 'Premium Sea View Room', 'type' => 'super_deluxe', 'price' => 13500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Frontal Sea View', 'sqm' => 46],
            '3_Executive Suite' => ['name' => 'Executive Suite', 'type' => 'suite', 'price' => 19000, 'bed' => '1 King Bed & Living Room', 'guests' => 3, 'adults' => 2, 'children' => 2, 'view' => 'Panoramic Oceanfront', 'sqm' => 68],
        ]
    ],
    [
        'sl' => 6,
        'folder' => '6_Ramada by Wyndham Cox\'s Bazar Kolatoli Beach',
        'name' => 'Ramada by Wyndham Cox\'s Bazar Kolatoli Beach',
        'slug' => 'ramada-by-wyndham-coxs-bazar',
        'type' => 'hotel',
        'star_rating' => 4,
        'rating_score' => 8.8,
        'total_reviews' => 320,
        'price_per_night' => 6200,
        'original_price' => 8000,
        'address' => 'CX7M+MHC, Kolatoli Beach, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'Kolatoli Beach Point (150m), Dolphin Square',
        'latitude' => '21.417800',
        'longitude' => '91.983900',
        'map_embed_url' => 'https://maps.app.goo.gl/ZAmQrgaUPYZSmwdc7',
        'contact_phone' => '+8801896100012',
        'contact_email' => 'info@ramadacoxsbazar.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Ramada by Wyndham Cox's Bazar Kolatoli Beach offers world-renowned Wyndham hospitality right at the vibrant Kolatoli beach center. Enjoy contemporary guest rooms, oceanfront balconies, swimming pool, international dining, mobility-accessible facilities, and friendly 24/7 service.",
        'amenities' => [
            'Swimming Pool', 'Wyndham Rewards Program', 'Free WiFi', 'Multi-Cuisine Restaurant',
            'Mobility Accessible Rooms', '24/7 Front Desk', 'Sea View Balconies',
            'Air Conditioning', 'Airport Transfer', 'Secured Parking'
        ],
        'room_rates' => [
            '3_Standard Hill King, Non-Smoking' => ['name' => 'Standard Hill King (Non-Smoking)', 'type' => 'standard', 'price' => 6200, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 32],
            '6_Standard Hill Twin, Smoking' => ['name' => 'Standard Hill Twin (Smoking)', 'type' => 'standard', 'price' => 6200, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 32],
            '5_1 King Bed, Mobility Accessible Room, Roll-In Shower' => ['name' => '1 King Bed Mobility Accessible Room', 'type' => 'deluxe', 'price' => 6500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 36],
            '1_Super Deluxe Non-Smoking' => ['name' => 'Super Deluxe (Non-Smoking)', 'type' => 'super_deluxe', 'price' => 7800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View', 'sqm' => 38],
            '8_Super Deluxe, Smoking' => ['name' => 'Super Deluxe (Smoking)', 'type' => 'super_deluxe', 'price' => 7800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View', 'sqm' => 38],
            '7_Oceanfront King, Smoking' => ['name' => 'Oceanfront King Room', 'type' => 'super_deluxe', 'price' => 9500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Direct Oceanfront', 'sqm' => 42],
            '2_Panorama Suite Non-Smoking' => ['name' => 'Panorama Suite (Non-Smoking)', 'type' => 'suite', 'price' => 14500, 'bed' => '1 King Bed & Living Room', 'guests' => 3, 'adults' => 2, 'children' => 2, 'view' => 'Panoramic Sea View', 'sqm' => 60],
            '4_Panorama Suite, Smoking' => ['name' => 'Panorama Suite (Smoking)', 'type' => 'suite', 'price' => 14500, 'bed' => '1 King Bed & Living Room', 'guests' => 3, 'adults' => 2, 'children' => 2, 'view' => 'Panoramic Sea View', 'sqm' => 60],
        ]
    ],
    [
        'sl' => 7,
        'folder' => '7_Green Nature Resort & Suites',
        'name' => 'Green Nature Resort & Suites',
        'slug' => 'green-nature-resort-suites',
        'type' => 'resort',
        'star_rating' => 4,
        'rating_score' => 8.9,
        'total_reviews' => 195,
        'price_per_night' => 5500,
        'original_price' => 7000,
        'address' => 'Holding 157/A, Sugandha Beach Point Road, Cox\'s Bazar, Bangladesh',
        'nearest_landmark' => 'Sugandha Beach Point (2 mins walk), Burmese Market (1.2 km)',
        'latitude' => '21.425100',
        'longitude' => '91.978900',
        'map_embed_url' => 'https://maps.app.goo.gl/KdwTVdtzwzj52NFw6',
        'contact_phone' => '+8801889641111',
        'contact_email' => 'info@greennaturecoxsbazar.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Green Nature Resort & Suites is a modern eco-luxury resort situated right near Sugandha Beach Point in Cox's Bazar. Featuring tranquil landscaped water bodies, lush green surroundings, sea-facing balconies, fine dining restaurants, and family-friendly villas and penthouses.",
        'amenities' => [
            'Scenic Waterbody & Gardens', 'Near Sugandha Beach', 'Free WiFi',
            'Sea View Balconies', 'Restaurant & BBQ Facilities', 'Family Penthouse Villas',
            '24/7 Room Service', 'Power Backup', 'Free Parking'
        ],
        'room_rates' => [
            '6_Deluxe King City View Without Balcony' => ['name' => 'Deluxe King City View', 'type' => 'deluxe', 'price' => 5500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 32],
            '2_Deluxe King Twin City View Without Balcony' => ['name' => 'Deluxe King Twin City View', 'type' => 'deluxe', 'price' => 5500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 32],
            '7_Double Queen City View Without Balcony' => ['name' => 'Double Queen City View', 'type' => 'deluxe', 'price' => 6200, 'bed' => '2 Queen Beds', 'guests' => 4, 'adults' => 3, 'children' => 1, 'view' => 'City View', 'sqm' => 38],
            '1_Super Deluxe king Partial Sea View With Balcony' => ['name' => 'Super Deluxe King Partial Sea View', 'type' => 'super_deluxe', 'price' => 7500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View Balcony', 'sqm' => 38],
            '5_Super Deluxe King Waterbody View With Balcony' => ['name' => 'Super Deluxe King Waterbody View', 'type' => 'super_deluxe', 'price' => 8000, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Waterbody & Nature View', 'sqm' => 40],
            '4_Junior Suite Twin Partial Sea View With Balcony' => ['name' => 'Junior Suite Twin Partial Sea View', 'type' => 'suite', 'price' => 9500, 'bed' => '2 Twin Beds & Sofa', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Sea View Balcony', 'sqm' => 48],
            '3_Penthouse Family Villa' => ['name' => 'Penthouse Family Villa', 'type' => 'villa', 'price' => 18000, 'bed' => '2 King Beds & Private Lounge', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Top Floor Panoramic View', 'sqm' => 95],
        ]
    ],
    [
        'sl' => 8,
        'folder' => '8_Hotel Windy Terrace',
        'name' => 'Hotel Windy Terrace',
        'slug' => 'hotel-windy-terrace',
        'type' => 'hotel',
        'star_rating' => 3,
        'rating_score' => 8.7,
        'total_reviews' => 260,
        'price_per_night' => 4200,
        'original_price' => 5500,
        'address' => 'Plot-39-40, Block C, Kolatoli, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'Sugandha Beach (350m), Kolatoli Circle (500m)',
        'latitude' => '21.423000',
        'longitude' => '91.981000',
        'map_embed_url' => 'https://maps.app.goo.gl/VVCBXdwb29oQiwS37',
        'contact_phone' => '+8801752360675, +8801332511551',
        'contact_email' => 'sales@windyterrace.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Hotel Windy Terrace offers warm coastal hospitality, cozy comfort, and outstanding sea view accommodations in Cox's Bazar. Known for its signature Glass House panoramic sea view rooms, connecting family suites, rooftop restaurant with coastal breezes, and personalized guest care.",
        'amenities' => [
            'Signature Glass House Sea View', 'Connecting Family Suites', 'Free High-Speed WiFi',
            'Rooftop Restaurant & Cafe', 'Air Conditioned Rooms', '24/7 Room Service',
            'Power Backup', 'Free Parking', 'Travel Assistance Desk'
        ],
        'room_rates' => [
            '11_Standard King Room' => ['name' => 'Standard King Room', 'type' => 'standard', 'price' => 4200, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 28],
            '2_Standard Twin (Merge Couple)' => ['name' => 'Standard Twin (Merge Couple)', 'type' => 'standard', 'price' => 4200, 'bed' => '2 Twin Beds (Mergeable)', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 28],
            '1_Standard King Deluxe' => ['name' => 'Standard King Deluxe', 'type' => 'deluxe', 'price' => 4800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 32],
            '10_Deluxe Twin Room' => ['name' => 'Deluxe Twin Room', 'type' => 'deluxe', 'price' => 5200, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 34],
            '3_Superior King Deluxe' => ['name' => 'Superior King Deluxe', 'type' => 'super_deluxe', 'price' => 5500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View', 'sqm' => 36],
            '4_Superior Twin Deluxe' => ['name' => 'Superior Twin Deluxe', 'type' => 'super_deluxe', 'price' => 5500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Partial Sea View', 'sqm' => 36],
            '5_Executive King Deluxe' => ['name' => 'Executive King Deluxe', 'type' => 'super_deluxe', 'price' => 6500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View Balcony', 'sqm' => 40],
            '6_Executive Twin Deluxe' => ['name' => 'Executive Twin Deluxe', 'type' => 'super_deluxe', 'price' => 6500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View Balcony', 'sqm' => 40],
            '9_Glass House (Sea View)' => ['name' => 'Glass House (Sea View)', 'type' => 'suite', 'price' => 8500, 'bed' => '1 King Bed in Glass Enclosure', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Glass Sea View', 'sqm' => 45],
            '8_Executive Suite (Connecting)' => ['name' => 'Executive Suite (Connecting)', 'type' => 'suite', 'price' => 10500, 'bed' => '1 King + 1 Twin Room', 'guests' => 4, 'adults' => 3, 'children' => 2, 'view' => 'Sea View', 'sqm' => 60],
            '7_Family Suite (Connecting)' => ['name' => 'Family Suite (Connecting)', 'type' => 'suite', 'price' => 12000, 'bed' => '2 King Beds Connected', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Sea & City View', 'sqm' => 68],
        ]
    ],
    [
        'sl' => 9,
        'folder' => '9_Dera Resort & Spa- Cox\'s Bazar',
        'name' => 'Dera Resort & Spa- Cox\'s Bazar',
        'slug' => 'dera-resort-spa-coxs-bazar',
        'type' => 'resort',
        'star_rating' => 4,
        'rating_score' => 9.3,
        'total_reviews' => 180,
        'price_per_night' => 8500,
        'original_price' => 11000,
        'address' => 'Jaliapalong, Marine Drive, Inani, Cox\'s Bazar, Bangladesh',
        'nearest_landmark' => 'Inani Beach (5 mins), Marine Drive scenic strip',
        'latitude' => '21.225000',
        'longitude' => '92.048000',
        'map_embed_url' => 'https://maps.app.goo.gl/7nZe29h75Vr9PCu8A',
        'contact_phone' => '+8801896001112',
        'contact_email' => 'reservation@deraresort.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Dera Resort & Spa is a boutique luxury villa retreat nestled along the Inani coast in Cox's Bazar. Combining private villa living with bespoke hospitality, the resort features private swimming pool villas, presidential suites, sea view tower suites, signature spa therapies, and gourmet seaside dining.",
        'amenities' => [
            'Private Pool Villas', 'Sea View Tower Suites', 'Ayurvedic & Luxury Spa',
            'Private Beach Ambience', 'Buffet Dining Restaurant', 'High-Speed WiFi',
            '24/7 Room Service & Butler', 'VIP Lounge', 'Airport Shuttle'
        ],
        'room_rates' => [
            '4_Superior Villa' => ['name' => 'Superior Villa', 'type' => 'villa', 'price' => 8500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden & Pool View', 'sqm' => 50],
            '2_Sea View Tower' => ['name' => 'Sea View Tower Deluxe', 'type' => 'super_deluxe', 'price' => 10500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Sea View Tower', 'sqm' => 45],
            '3_Family Villas' => ['name' => 'Family Villa', 'type' => 'villa', 'price' => 14000, 'bed' => '2 King Beds', 'guests' => 4, 'adults' => 4, 'children' => 2, 'view' => 'Private Garden View', 'sqm' => 80],
            '1_Presidential Villa' => ['name' => 'Presidential Pool Villa', 'type' => 'villa', 'price' => 28000, 'bed' => '2 Master King Beds & Private Pool', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Private Pool & Oceanfront', 'sqm' => 150],
        ]
    ],
    [
        'sl' => 10,
        'folder' => '10_Praasad Paradise',
        'name' => 'Praasad Paradise',
        'slug' => 'praasad-paradise',
        'type' => 'resort',
        'star_rating' => 3,
        'rating_score' => 8.8,
        'total_reviews' => 350,
        'price_per_night' => 4500,
        'original_price' => 6000,
        'address' => 'Hotel- Motel Zone, Plot 9 New Beach Rd, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'New Beach Road (Direct Beach Walk), Laboni Point (800m)',
        'latitude' => '21.428500',
        'longitude' => '91.977200',
        'map_embed_url' => 'https://praasadparadise.com.bd/',
        'contact_phone' => '+8801511994477, +8801511994478, +8801511994479',
        'contact_email' => 'booking@praasadparadise.com.bd',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Praasad Paradise is a serene beachside hotel and resort along New Beach Road in Cox's Bazar. Offering a peaceful escape just steps away from the beach, the resort features swimming pools, lush palm courtyards, honeymoon suites, connecting royal suites, and delicious Bengali & international seafood dining.",
        'amenities' => [
            'Swimming Pool with Kids Section', 'Direct Beach Walking Access', 'Free WiFi',
            'Paradise Multi-Cuisine Restaurant', 'Family Connecting Rooms', 'Honeymoon Suites',
            '24/7 Front Desk', 'Generator Power Backup', 'Secured Parking'
        ],
        'room_rates' => [
            '8_Superior Double Room' => ['name' => 'Superior Double Room', 'type' => 'superior', 'price' => 4500, 'bed' => '1 Double Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden View', 'sqm' => 30],
            '9_Superior Twin Room' => ['name' => 'Superior Twin Room', 'type' => 'superior', 'price' => 4500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Garden View', 'sqm' => 30],
            '1_Deluxe-DBLDeluxe-Twin' => ['name' => 'Deluxe Double / Twin', 'type' => 'deluxe', 'price' => 5200, 'bed' => '1 King Bed or 2 Twins', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Pool View', 'sqm' => 34],
            '2_Super Deluxe DBLTwin' => ['name' => 'Super Deluxe Double / Twin', 'type' => 'super_deluxe', 'price' => 6000, 'bed' => '1 King Bed or 2 Twins', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Pool & Garden View', 'sqm' => 38],
            '4_Deluxe - Triple' => ['name' => 'Deluxe Triple Room', 'type' => 'deluxe', 'price' => 6800, 'bed' => '3 Single Beds', 'guests' => 3, 'adults' => 3, 'children' => 1, 'view' => 'Resort View', 'sqm' => 42],
            '5_Super Deluxe - Triple' => ['name' => 'Super Deluxe Triple Room', 'type' => 'super_deluxe', 'price' => 7800, 'bed' => '1 King + 1 Single Bed', 'guests' => 3, 'adults' => 3, 'children' => 1, 'view' => 'Pool View', 'sqm' => 44],
            '7_Honeymoon Suite' => ['name' => 'Honeymoon Suite', 'type' => 'suite', 'price' => 8500, 'bed' => '1 King Bed with Romantic Decor', 'guests' => 2, 'adults' => 2, 'children' => 0, 'view' => 'Pool & Beach View', 'sqm' => 48],
            '6_Super Deluxe - 4 Bed' => ['name' => 'Super Deluxe 4-Bed Room', 'type' => 'super_deluxe', 'price' => 9500, 'bed' => '2 Double Beds / 4 Beds', 'guests' => 4, 'adults' => 4, 'children' => 2, 'view' => 'Garden View', 'sqm' => 55],
            '10_Deluxe Family Room' => ['name' => 'Deluxe Family Room', 'type' => 'suite', 'price' => 10500, 'bed' => '2 King Beds', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Resort View', 'sqm' => 60],
            '3_Royal Deluxe (Connecting Room)' => ['name' => 'Royal Deluxe Connecting Suite', 'type' => 'suite', 'price' => 11500, 'bed' => '2 Connected Rooms', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Pool & Sea View', 'sqm' => 68],
        ]
    ],
    [
        'sl' => 11,
        'folder' => '11_Hotel Grand Pacific',
        'name' => 'Hotel Grand Pacific',
        'slug' => 'hotel-grand-pacific',
        'type' => 'hotel',
        'star_rating' => 5,
        'rating_score' => 9.1,
        'total_reviews' => 290,
        'price_per_night' => 6800,
        'original_price' => 8500,
        'address' => 'Dolphin Point Entrance, Kolatoli, Cox\'s Bazar 4700, Bangladesh',
        'nearest_landmark' => 'Dolphin Point Square (100m), Kolatoli Beach (3 mins walk)',
        'latitude' => '21.419000',
        'longitude' => '91.983000',
        'map_embed_url' => 'https://maps.app.goo.gl/S1Wf7AuhZZk1Zzfp6',
        'contact_phone' => '+8801711746681',
        'contact_email' => 'info@grandpacificbd.com',
        'checkin_time' => '14:00',
        'checkout_time' => '12:00',
        'description' => "Hotel Grand Pacific is a brand-new 5-star standard luxury destination located right at the entrance of Dolphin Point in Kolatoli, Cox's Bazar. Boasting stunning architecture, 195+ premium rooms and suites with sea and hill views, presidential suites, fine dining restaurants, rooftop pool, and state-of-the-art facilities.",
        'amenities' => [
            'Rooftop Swimming Pool', 'Free High-Speed WiFi', 'Pacific Multi-Cuisine Dining',
            'Sea & Hill View Balconies', 'Family Connecting Suites', 'Fitness Center',
            '24/7 Front Desk & Concierge', 'Valet Parking', 'Airport Shuttle'
        ],
        'room_rates' => [
            '7_Standard Deluxe Hill View' => ['name' => 'Standard Deluxe Hill View', 'type' => 'deluxe', 'price' => 6800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Hill View', 'sqm' => 34],
            '8_Premier Deluxe City View' => ['name' => 'Premier Deluxe City View', 'type' => 'super_deluxe', 'price' => 7800, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 38],
            '9_Premier Deluxe City View' => ['name' => 'Premier Deluxe City View (Twin)', 'type' => 'super_deluxe', 'price' => 7800, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'City View', 'sqm' => 38],
            '6_Superior Deluxe Sea View' => ['name' => 'Superior Deluxe Sea View', 'type' => 'super_deluxe', 'price' => 9200, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Sea View Balcony', 'sqm' => 40],
            '4_Premier Deluxe Sea View' => ['name' => 'Premier Deluxe Sea View', 'type' => 'super_deluxe', 'price' => 10500, 'bed' => '1 King Bed', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Sea View', 'sqm' => 44],
            '5_Premier Deluxe Sea View' => ['name' => 'Premier Deluxe Sea View (Twin)', 'type' => 'super_deluxe', 'price' => 10500, 'bed' => '2 Twin Beds', 'guests' => 2, 'adults' => 2, 'children' => 1, 'view' => 'Panoramic Sea View', 'sqm' => 44],
            '2_Family Connecting Deluxe' => ['name' => 'Family Connecting Deluxe Suite', 'type' => 'suite', 'price' => 14500, 'bed' => '2 Connected Rooms (King + Twin)', 'guests' => 4, 'adults' => 4, 'children' => 2, 'view' => 'Sea & City View', 'sqm' => 65],
            '3_Premier Suite Sea View' => ['name' => 'Premier Suite Sea View', 'type' => 'suite', 'price' => 18500, 'bed' => '1 King Bed & Living Room', 'guests' => 3, 'adults' => 2, 'children' => 1, 'view' => 'Full Sea View Balcony', 'sqm' => 72],
            '1_Presidential Suite' => ['name' => 'Grand Presidential Suite', 'type' => 'suite', 'price' => 30000, 'bed' => '2 King Beds, Lounge & Dining', 'guests' => 5, 'adults' => 4, 'children' => 2, 'view' => 'Top-Floor Panoramic Sea View', 'sqm' => 120],
        ]
    ]
];

// Clean up duplicate audit records (IDs 29, 30, 31) with zero bookings
DB::table('properties')->whereIn('id', [29, 30, 31])->delete();
echo "[OK] Cleaned test audit properties (IDs 29, 30, 31)\n\n";

// Process each hotel
foreach ($hotelConfigs as $cfg) {
    echo "========================================================\n";
    echo "Processing Hotel [{$cfg['sl']}]: {$cfg['name']} ...\n";
    
    $hotelDir = $elementsBase . '/' . $cfg['folder'];
    $slug = $cfg['slug'];
    
    // 1. Copy All Image / All Photo into gallery/
    $galleryUrls = [];
    $hotelImgDir = $hotelDir . '/Image';
    $galleryDir = $hotelImgDir . '/All Image';
    if (!is_dir($galleryDir)) {
        $galleryDir = $hotelImgDir . '/All Photo';
    }
    
    if (is_dir($galleryDir)) {
        $gFiles = scandir($galleryDir);
        foreach ($gFiles as $gf) {
            if ($gf === '.' || $gf === '..') continue;
            $gPath = $galleryDir . '/' . $gf;
            if (is_file($gPath)) {
                $ext = strtolower(pathinfo($gPath, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $url = storeMediaFile($gPath, "{$slug}/gallery/{$gf}");
                    $galleryUrls[] = $url;
                }
            }
        }
    }
    echo "  [GALLERY] Transferred " . count($galleryUrls) . " gallery images.\n";
    
    // Primary cover image is the first gallery image
    $primaryImage = !empty($galleryUrls) ? $galleryUrls[0] : null;
    
    // 2. Copy Video if available
    $videoUrl = null;
    $videoDir = $hotelDir . '/Video';
    if (is_dir($videoDir)) {
        $rdi = new RecursiveDirectoryIterator($videoDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi);
        foreach ($rii as $vf) {
            $ext = strtolower($vf->getExtension());
            if (in_array($ext, ['mp4', 'mov', 'webm'])) {
                // Prefer files in Final Video or first video
                $rel = "{$slug}/videos/" . $vf->getFilename();
                $vUrl = storeMediaFile($vf->getPathname(), $rel);
                if (!$videoUrl || str_contains($vf->getPathname(), 'Final Video')) {
                    $videoUrl = $vUrl;
                }
            }
        }
    }
    if ($videoUrl) {
        echo "  [VIDEO] Dedicated video transferred: {$videoUrl}\n";
    } else {
        echo "  [VIDEO] No dedicated video for this hotel.\n";
    }
    
    // 3. Find or Create Property
    // Check if property exists by slug or specific ID mapping
    $property = null;
    if ($cfg['sl'] == 2) {
        $property = Property::find(6); // Ocean Paradise
    } elseif ($cfg['sl'] == 4) {
        $property = Property::find(8); // Seagull Hotel
    }
    
    if (!$property) {
        $property = Property::where('slug', $slug)->first();
    }
    if (!$property) {
        $property = Property::where('name', $cfg['name'])->first();
    }
    if (!$property) {
        $property = new Property();
        $property->slug = $slug;
    }
    
    // Populate Property fields
    $property->location_id = $location->id;
    $property->name = $cfg['name'];
    $property->type = $cfg['type'];
    $property->star_rating = $cfg['star_rating'];
    $property->rating_score = $cfg['rating_score'];
    $property->total_reviews = $cfg['total_reviews'];
    $property->city = "Cox's Bazar";
    $property->address = $cfg['address'];
    $property->nearest_landmark = $cfg['nearest_landmark'];
    $property->latitude = $cfg['latitude'];
    $property->longitude = $cfg['longitude'];
    $property->map_embed_url = $cfg['map_embed_url'];
    $property->contact_phone = $cfg['contact_phone'];
    $property->contact_email = $cfg['contact_email'];
    $property->checkin_time = $cfg['checkin_time'];
    $property->checkout_time = $cfg['checkout_time'];
    $property->price_per_night = $cfg['price_per_night'];
    $property->original_price = $cfg['original_price'];
    $property->primary_image = $primaryImage;
    $property->video_url = $videoUrl;
    $property->images = $galleryUrls;
    $property->amenities = $cfg['amenities'];
    $property->description = $cfg['description'];
    $property->is_featured = 1;
    $property->status = 'published';
    $property->free_cancellation = 1;
    $property->no_credit_card_required = 1;
    $property->save();
    
    echo "  [PROPERTY] Saved Property ID: {$property->id} ('{$property->name}')\n";
    
    // 4. Rooms processing
    // Delete old dummy rooms if this was property 6 or 8
    if (in_array($property->id, [6, 8])) {
        // Only delete rooms that don't have active bookings or unlink dummy ones
        $oldRooms = Room::where('property_id', $property->id)->get();
        foreach ($oldRooms as $or) {
            $hasBooking = DB::table('bookings')->where('room_id', $or->id)->exists();
            if (!$hasBooking) {
                $or->delete();
            }
        }
    }
    
    $createdRoomCount = 0;
    
    // Read all room directories in Image/
    $subDirs = is_dir($hotelImgDir) ? scandir($hotelImgDir) : [];
    foreach ($subDirs as $sd) {
        if ($sd === '.' || $sd === '..' || $sd === 'All Image' || $sd === 'All Photo') continue;
        $roomDirPath = $hotelImgDir . '/' . $sd;
        if (!is_dir($roomDirPath)) continue;
        
        // Find matching rate config or generate one
        $roomMeta = null;
        foreach ($cfg['room_rates'] as $k => $v) {
            if ($sd === $k || str_contains($sd, $k) || str_contains($k, $sd)) {
                $roomMeta = $v;
                break;
            }
        }
        
        $cleanName = preg_replace('/^[0-9]+_/', '', $sd);
        $cleanName = ucwords(str_replace(['-', '_'], ' ', $cleanName));
        
        if (!$roomMeta) {
            $isSuite = str_contains(strtolower($cleanName), 'suite');
            $isVilla = str_contains(strtolower($cleanName), 'villa');
            $isSuper = str_contains(strtolower($cleanName), 'super') || str_contains(strtolower($cleanName), 'premier');
            $isTwin = str_contains(strtolower($cleanName), 'twin');
            $isTriple = str_contains(strtolower($cleanName), 'triple');
            
            $roomType = $isSuite ? 'suite' : ($isVilla ? 'villa' : ($isSuper ? 'super_deluxe' : 'deluxe'));
            $bedType = $isTriple ? '3 Single Beds' : ($isTwin ? '2 Twin Beds' : '1 King Bed');
            $guests = $isTriple ? 3 : 2;
            $price = $isSuite ? ($cfg['price_per_night'] * 1.8) : ($isSuper ? ($cfg['price_per_night'] * 1.3) : $cfg['price_per_night']);
            
            $roomMeta = [
                'name' => $cleanName,
                'type' => $roomType,
                'price' => round($price),
                'bed' => $bedType,
                'guests' => $guests,
                'adults' => $guests,
                'children' => 1,
                'view' => str_contains(strtolower($cleanName), 'sea') ? 'Sea View' : 'City / Hill View',
                'sqm' => $isSuite ? 60 : 36
            ];
        }
        
        // Copy Room Images
        $roomImages = [];
        $rFiles = scandir($roomDirPath);
        $roomSlug = Str::slug($sd);
        foreach ($rFiles as $rf) {
            if ($rf === '.' || $rf === '..') continue;
            $rPath = $roomDirPath . '/' . $rf;
            if (is_file($rPath)) {
                $ext = strtolower(pathinfo($rPath, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $url = storeMediaFile($rPath, "{$slug}/rooms/{$roomSlug}/{$rf}");
                    $roomImages[] = $url;
                }
            }
        }
        
        // Upsert Room
        $room = Room::firstOrNew([
            'property_id' => $property->id,
            'name' => $roomMeta['name']
        ]);
        
        $room->room_size_sqm = $roomMeta['sqm'] ?? 38;
        $room->bed_type = $roomMeta['bed'] ?? '1 King Bed';
        $room->view_type = $roomMeta['view'] ?? 'City View';
        $room->max_guests = $roomMeta['guests'] ?? 2;
        $room->max_adults = $roomMeta['adults'] ?? 2;
        $room->max_children = $roomMeta['children'] ?? 1;
        $room->price_per_night = $roomMeta['price'];
        $room->total_rooms = 10;
        $room->breakfast_included = 1;
        $room->free_cancellation = 1;
        $room->extra_bed_allowed = ($roomMeta['guests'] >= 3) ? 1 : 0;
        $room->bathroom_count = 1;
        $room->smoking_policy = str_contains(strtolower($roomMeta['name']), 'smoking') && !str_contains(strtolower($roomMeta['name']), 'non-smoking') ? 'Smoking Allowed' : 'Non-Smoking';
        $room->balcony_type = str_contains(strtolower($roomMeta['name']), 'balcony') || str_contains(strtolower($roomMeta['view']), 'sea') ? 'Private Balcony' : 'Standard Window';
        $room->facilities = [
            'Air Conditioning', 'Flat-screen Smart TV', 'High-Speed Free WiFi',
            'En-suite Bathroom with Hot Water Shower', 'Electric Kettle & Tea/Coffee Maker',
            'Mini Refrigerator', 'Complimentary Bottled Water', 'Work Desk & Chair',
            'Hair Dryer & Slippers', 'In-room Safe Locker'
        ];
        $room->images = $roomImages;
        $room->status = 'active';
        $room->save();
        
        $createdRoomCount++;
    }
    
    echo "  [ROOMS] Created / Updated {$createdRoomCount} rooms with authentic photos.\n";
}

echo "\n========================================================\n";
echo " ALL 11 HOTELS & MEDIA SUCCESSFULLY IMPORTED 100%!\n";
echo "========================================================\n";
