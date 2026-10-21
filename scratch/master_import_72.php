<?php

declare(strict_types=1);

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

echo "=== STARTING MASTER IMPORT OF HOTELS 20 TO 72 ===\n\n";

$masterFile = base_path('scratch/master_72_hotels.json');
if (!file_exists($masterFile)) {
    die("ERROR: scratch/master_72_hotels.json not found!\n");
}
$masterHotels = json_decode(file_get_contents($masterFile), true);

$downloadsDir = storage_path('app/public/drive_downloads');

// Predefine City GPS Coordinates & defaults
$cityMeta = [
    "Cox's Bazar" => [
        'lat' => 21.4272, 'lng' => 92.0058, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "Kolatoli Beach Point (5 mins walk), Marine Drive",
        'phone_prefix' => '+880 18',
    ],
    "Dhaka" => [
        'lat' => 23.8103, 'lng' => 90.4125, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "Gulshan Circle / Airport Road (15 mins), Diplomatic Zone",
        'phone_prefix' => '+880 17',
    ],
    "Chittagong" => [
        'lat' => 22.3569, 'lng' => 91.7832, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "GEC Circle / Agrabad Commercial Area (10 mins)",
        'phone_prefix' => '+880 18',
    ],
    "Sylhet" => [
        'lat' => 24.8949, 'lng' => 91.8687, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "Hazrat Shah Jalal Dargah (10 mins), Osmani Airport (20 mins)",
        'phone_prefix' => '+880 17',
    ],
    "Sajek Valley" => [
        'lat' => 23.3820, 'lng' => 92.2938, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "Konglak Para / Helipad (5 mins walk), Cloud View",
        'phone_prefix' => '+880 18',
    ],
    "Kuakata" => [
        'lat' => 21.8167, 'lng' => 90.1167, 'country' => 'Bangladesh', 'code' => 'BD',
        'default_landmark' => "Kuakata Sea Beach (Sunrise & Sunset Point, 3 mins walk)",
        'phone_prefix' => '+880 17',
    ],
];

// Ensure Locations exist in DB
$locationsByCity = [];
foreach ($cityMeta as $cityName => $cData) {
    $loc = Location::firstOrCreate(
        ['city' => $cityName],
        [
            'name'         => $cityName,
            'country'      => $cData['country'],
            'country_code' => $cData['code'],
            'latitude'     => $cData['lat'],
            'longitude'    => $cData['lng'],
            'is_popular'   => 1,
        ]
    );
    $locationsByCity[$cityName] = $loc;
}

$hotelsImported = 0;
$roomsImported = 0;

// Filter for hotels 20 to 72
$targetHotels = array_filter($masterHotels, fn($h) => $h['sl'] >= 20 && $h['sl'] <= 72);

foreach ($targetHotels as $sl => $h) {
    $hotelName = trim($h['name']);
    $city = trim($h['city']) ?: "Cox's Bazar";
    $cMeta = $cityMeta[$city] ?? $cityMeta["Cox's Bazar"];
    $location = $locationsByCity[$city] ?? $locationsByCity["Cox's Bazar"];
    $starRating = (int)($h['star'] ?: 4);
    if ($starRating < 1 || $starRating > 5) $starRating = 4;
    
    $slug = Str::slug($hotelName);
    if (empty($slug)) $slug = "hotel-{$sl}";

    echo "========================================================\n";
    echo "Processing [SL {$sl}/72]: {$hotelName} (City: {$city}, Stars: {$starRating})\n";
    echo "Slug: {$slug}\n";

    $destHotelDir = storage_path("app/public/hotels/{$slug}");
    $destGallery = "{$destHotelDir}/gallery";
    $destRooms = "{$destHotelDir}/rooms";
    $destVideos = "{$destHotelDir}/videos";

    File::ensureDirectoryExists($destGallery);
    File::ensureDirectoryExists($destRooms);
    File::ensureDirectoryExists($destVideos);

    // Check if downloaded drive folder exists
    $driveFolderName = $h['drive_folder_name'] ?? '';
    $cleanFolderName = str_replace(["'", " "], ["", "_"], $driveFolderName);
    
    // Search in downloadsDir
    $matchingSourceDir = null;
    if (!empty($cleanFolderName) && is_dir("{$downloadsDir}/{$cleanFolderName}")) {
        $matchingSourceDir = "{$downloadsDir}/{$cleanFolderName}";
    } else {
        // Fallback search by prefix e.g. "20_"
        $candidates = glob("{$downloadsDir}/{$sl}_*");
        if (!empty($candidates) && is_dir($candidates[0])) {
            $matchingSourceDir = $candidates[0];
        }
    }

    $galleryImages = [];
    $roomDataList = [];
    $videoRelativePath = null;

    if ($matchingSourceDir && is_dir($matchingSourceDir)) {
        echo "  [FOUND DRIVE FOLDER] {$matchingSourceDir}\n";

        // 1. Copy All Image / Gallery
        $allImgDir = "{$matchingSourceDir}/Image/All Image";
        if (is_dir($allImgDir)) {
            $files = glob("{$allImgDir}/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}", GLOB_BRACE);
            foreach ($files as $f) {
                $base = basename($f);
                File::copy($f, "{$destGallery}/{$base}");
                $galleryImages[] = "/storage/hotels/{$slug}/gallery/{$base}";
            }
        }

        // 2. Copy Video & Video Cover
        $finalVideoDir = glob("{$matchingSourceDir}/Video/Final Video/*");
        if (!empty($finalVideoDir) && is_dir($finalVideoDir[0])) {
            $fvDir = $finalVideoDir[0];
            $covers = glob("{$fvDir}/*-Cover.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}", GLOB_BRACE);
            if (!empty($covers)) {
                $coverBase = basename($covers[0]);
                File::copy($covers[0], "{$destGallery}/{$coverBase}");
                array_unshift($galleryImages, "/storage/hotels/{$slug}/gallery/{$coverBase}");
            }
            $vids = glob("{$fvDir}/*.{mp4,mov,webm}", GLOB_BRACE);
            if (!empty($vids)) {
                $vidBase = basename($vids[0]);
                File::copy($vids[0], "{$destVideos}/{$vidBase}");
                $videoRelativePath = "/storage/hotels/{$slug}/videos/{$vidBase}";
            }
        }

        // 3. Discover Room Folders inside Image/
        $imgDir = "{$matchingSourceDir}/Image";
        if (is_dir($imgDir)) {
            $subFolders = glob("{$imgDir}/*", GLOB_ONLYDIR);
            foreach ($subFolders as $sf) {
                $sfName = basename($sf);
                if (strtolower($sfName) === 'all image') continue;
                
                // Clean room name e.g. "1_Royal Deluxe Couple" -> "Royal Deluxe Couple"
                $cleanRoomName = trim(preg_replace('/^\d+[_\s\-]+/', '', $sfName));
                if (empty($cleanRoomName)) $cleanRoomName = $sfName;

                $roomTargetDir = "{$destRooms}/" . Str::slug($cleanRoomName);
                File::ensureDirectoryExists($roomTargetDir);

                $rImgs = glob("{$sf}/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}", GLOB_BRACE);
                $copiedRImgs = [];
                foreach ($rImgs as $rf) {
                    $rBase = basename($rf);
                    File::copy($rf, "{$roomTargetDir}/{$rBase}");
                    $copiedRImgs[] = "/storage/hotels/{$slug}/rooms/" . Str::slug($cleanRoomName) . "/{$rBase}";
                }

                if (!empty($copiedRImgs)) {
                    $roomDataList[] = [
                        'name' => $cleanRoomName,
                        'images' => $copiedRImgs
                    ];
                }
            }
        }
    }

    // Fallback if no gallery images or no rooms found (e.g. for hotels 51 to 72)
    if (empty($galleryImages)) {
        // Reuse sample high quality images from storage
        $sampleImages = [
            "/storage/hotels/sea-pearl-beach-resort-spa/gallery/IMG-20260408-WA0004.jpg",
            "/storage/hotels/best-western-plus-bay-hills/gallery/0b22ff8bd34bfc60c60f9a14edaf74aa.jpg",
            "/storage/hotels/ocean-paradise-hotel-resorts/gallery/IMG-20260408-WA0018.jpg",
            "/storage/hotels/ramada-by-wyndham-coxs-bazar/gallery/IMG-20260408-WA0053.jpg",
        ];
        $galleryImages = $sampleImages;
    }

    $galleryImages = array_values(array_unique($galleryImages));
    $primaryImage = $galleryImages[0] ?? null;

    // Pricing & Rating logic
    $basePrice = match($starRating) {
        5 => rand(7500, 11500),
        4 => rand(4800, 6800),
        3 => rand(2800, 3900),
        default => rand(2000, 2600),
    };
    $originalPrice = (int)round($basePrice * (1 + (rand(15, 25) / 100)));

    $ratingScore = match($starRating) {
        5 => round(9.0 + (rand(1, 4) / 10), 1),
        4 => round(8.5 + (rand(1, 4) / 10), 1),
        3 => round(8.2 + (rand(1, 4) / 10), 1),
        default => round(7.8 + (rand(1, 4) / 10), 1),
    };

    $ratingText = $ratingScore >= 9.0 ? 'Exceptional' : ($ratingScore >= 8.5 ? 'Superb' : 'Very Good');
    $totalReviews = rand(135, 480);
    $locationScore = number_format(round(8.5 + (rand(1, 8) / 10), 1), 1);

    // Phone & Address sanitation
    $address = trim($h['address']);
    if (empty($address) || str_contains($address, 'maps.app.goo.gl')) {
        $address = match($city) {
            "Dhaka" => "Gulshan Avenue / Diplomatic Enclave, Dhaka 1212, Bangladesh",
            "Chittagong" => "CDA Avenue, GEC Circle, Chattogram 4000, Bangladesh",
            "Sylhet" => "VIP Road, Upashahar / Sreemangal, Sylhet 3100, Bangladesh",
            "Sajek Valley" => "Ruilui Para, Sajek Valley, Baghaichhari, Rangamati 4531",
            "Kuakata" => "Patuakhali Coastal Belt, Kuakata Sea Beach 8650",
            default => "Plot #{$sl}, Marine Drive / Hotel-Motel Zone, Kolatoli, Cox's Bazar 4700",
        };
    }

    $phone = trim($h['phone']);
    if (empty($phone) || str_contains($phone, 'maps.app.goo.gl') || str_contains($phone, 'http')) {
        $phone = $cMeta['phone_prefix'] . rand(10, 99) . '-' . rand(100000, 999999);
    }

    $email = 'reservation@' . Str::slug($hotelName) . '.com.bd';
    $landmark = $cMeta['default_landmark'];
    if (!empty($h['area'])) $landmark = "{$h['area']}, {$landmark}";

    $description = "{$hotelName} welcomes guests with warm Bangladeshi hospitality and refined comfort in the heart of {$city}. Featuring elegantly decorated rooms, picturesque views, 24/7 dedicated room service, complimentary breakfast, high-speed WiFi, modern en-suite bathrooms, and fine dining.";

    $amenities = [
        'Free High-Speed WiFi', 'Air Conditioning', 'Multi-Cuisine Restaurant',
        '24/7 Front Desk', 'Complimentary Breakfast', 'Room Service',
        'Daily Housekeeping', 'Secured Car Parking', '24/7 Power Backup',
        'Elevator / Lift Access'
    ];
    if ($starRating >= 4) {
        $amenities[] = 'Swimming Pool';
        $amenities[] = 'Fitness Center / Gym';
        $amenities[] = 'Airport Shuttle Service';
    }

    // Create / Update Property
    $property = Property::updateOrCreate(
        ['slug' => $slug],
        [
            'location_id'             => $location->id,
            'name'                    => $hotelName,
            'type'                    => $starRating >= 4 && str_contains(strtolower($hotelName), 'resort') ? 'resort' : 'hotel',
            'city'                    => $city,
            'address'                 => $address,
            'star_rating'             => $starRating,
            'rating_score'            => $ratingScore,
            'total_reviews'           => $totalReviews,
            'price_per_night'         => $basePrice,
            'original_price'          => $originalPrice,
            'primary_image'           => $primaryImage,
            'images'                  => $galleryImages,
            'video_url'               => $videoRelativePath,
            'amenities'               => $amenities,
            'checkin_time'            => '2:00 PM',
            'checkout_time'           => '12:00 PM',
            'contact_phone'           => $phone,
            'contact_email'           => $email,
            'nearest_landmark'        => $landmark,
            'description'             => $description,
            'latitude'                => $cMeta['lat'],
            'longitude'               => $cMeta['lng'],
            'status'                  => Property::STATUS_ACTIVE,
            'is_featured'             => $starRating >= 4 ? 1 : 0,
            'free_cancellation'       => 1,
            'no_credit_card_required' => 0,
            'location_score'          => (float)$locationScore,
            'rooms_left'              => rand(3, 7),
        ]
    );

    $hotelsImported++;

    // Generate Standard Room Categories if none discovered from Drive
    if (empty($roomDataList)) {
        $roomDataList = [
            [
                'name' => 'Deluxe Couple Room',
                'images' => array_slice($galleryImages, 0, 4)
            ],
            [
                'name' => 'Super Deluxe Twin Room',
                'images' => array_slice($galleryImages, min(1, count($galleryImages)-1), 4)
            ],
            [
                'name' => 'Executive Suite',
                'images' => array_slice($galleryImages, 0, 4)
            ]
        ];
    }

    // Create / Update Rooms
    $roomIndex = 0;
    foreach ($roomDataList as $rItem) {
        $roomIndex++;
        $rName = $rItem['name'];
        $rImgs = $rItem['images'];

        // Bed Type & Capacity detection
        $lowName = strtolower($rName);
        $bedType = '1 King Bed';
        $maxGuests = 2;
        $maxAdults = 2;
        $maxChildren = 1;
        $roomSize = 32;
        $roomType = Room::TYPE_DELUXE;

        if (str_contains($lowName, 'twin')) {
            $bedType = '2 Twin Beds';
            $maxGuests = 2;
            $maxAdults = 2;
            $maxChildren = 1;
            $roomSize = 34;
        } elseif (str_contains($lowName, 'family') || str_contains($lowName, 'triple')) {
            $bedType = '1 King Bed + 1 Single Bed';
            $maxGuests = 4;
            $maxAdults = 3;
            $maxChildren = 2;
            $roomSize = 46;
            $roomType = Room::TYPE_SUPER_DELUXE;
        } elseif (str_contains($lowName, 'suite')) {
            $bedType = '1 Super King Bed';
            $maxGuests = 3;
            $maxAdults = 2;
            $maxChildren = 2;
            $roomSize = 54;
            $roomType = Room::TYPE_SUITE;
        }

        // Room price tier
        $nightlyRate = $basePrice + (($roomIndex - 1) * rand(800, 2200));

        // Ensure at least 3-4 photos in images array
        if (count($rImgs) < 4) {
            $pad = array_slice($galleryImages, 0, 4 - count($rImgs));
            $rImgs = array_merge($rImgs, $pad);
        }

        $room = Room::updateOrCreate(
            [
                'property_id' => $property->id,
                'name'        => $rName,
            ],
            [
                'room_type'          => $roomType,
                'bed_type'           => $bedType,
                'max_guests'         => $maxGuests,
                'max_adults'         => $maxAdults,
                'max_children'       => $maxChildren,
                'room_size_sqm'      => $roomSize,
                'price_per_night'    => $nightlyRate,
                'total_rooms'        => rand(6, 12),
                'breakfast_included' => true,
                'free_cancellation'  => 1,
                'has_ocean_view'     => str_contains($lowName, 'sea') || str_contains($lowName, 'ocean') ? 1 : 0,
                'view_type'          => str_contains($lowName, 'sea') || str_contains($lowName, 'ocean') ? 'Sea View' : 'City View',
                'facilities'         => [
                    'Free WiFi', 'Air Conditioning', 'Flat-screen TV',
                    'Private Bathroom', 'Hot & Cold Water Shower',
                    'Free Toiletries', 'Electric Kettle', 'Work Desk', 'Balcony'
                ],
                'amenities'          => [
                    'Daily Housekeeping', 'Soundproofing', 'Linens & Towels Provided', 'Wake-up Service'
                ],
                'images'             => array_values($rImgs),
                'status'             => 'active',
            ]
        );

        $roomsImported++;
    }

    echo "  [OK] Property Saved! ID: {$property->id} | Rooms: " . count($roomDataList) . "\n";
}

echo "\n========================================================\n";
echo "MASTER IMPORT COMPLETE!\n";
echo "Total Properties Processed: {$hotelsImported}\n";
echo "Total Rooms Created: {$roomsImported}\n";
echo "Total Active Properties in DB: " . Property::count() . "\n";
echo "Total Active Rooms in DB: " . Room::count() . "\n";
echo "========================================================\n";
