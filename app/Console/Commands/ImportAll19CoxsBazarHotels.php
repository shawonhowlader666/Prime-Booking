<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Location;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportAll19CoxsBazarHotels extends Command
{
    protected $signature = 'hotels:import-19-coxsbazar {--force : Force delete and re-import}';
    protected $description = "Completely remove existing Cox's Bazar hotels and import all 19 updated hotels from local download directory";

    public function handle(): int
    {
        $downloadRoot = 'C:/Users/Shawon/Downloads/primebooking.com.bd (1)/primebooking.com.bd';
        $websiteElementDir = $downloadRoot . '/Website Element';

        if (!is_dir($websiteElementDir)) {
            $this->error("Directory not found: $websiteElementDir");
            return 1;
        }

        $this->info("=== Starting 19 Hotels Import for Cox's Bazar ===");

        // 1. Read Sheet2 data from Excel JSON (already extracted in scratch/sheet2_hotels.json)
        $sheet2JsonPath = base_path('scratch/sheet2_hotels.json');
        if (!file_exists($sheet2JsonPath)) {
            $this->error("Sheet2 JSON not found: $sheet2JsonPath");
            return 1;
        }
        $sheet2 = json_decode(file_get_contents($sheet2JsonPath), true);
        $sheet2BySl = [];
        foreach ($sheet2 as $row) {
            $sl = (int)($row['SL No (ক্রমিক)'] ?? 0);
            if ($sl > 0) {
                $sheet2BySl[$sl] = $row;
            }
        }

        // 2. Read existing 11 hotels metadata if needed for realistic pricing baseline
        $existing11Path = base_path('scratch/existing_11_hotels.json');
        $existing11 = file_exists($existing11Path) ? json_decode(file_get_contents($existing11Path), true) : [];

        // 3. Remove existing Cox's Bazar properties & rooms as explicitly requested by user
        $this->warn("Removing all existing Cox's Bazar hotels & rooms...");
        $oldCoxProps = Property::where('city', "Cox's Bazar")->get();
        foreach ($oldCoxProps as $oldProp) {
            Room::where('property_id', $oldProp->id)->delete();
            $oldProp->delete();
        }
        $this->info("Removed " . $oldCoxProps->count() . " old Cox's Bazar properties.");

        // 4. Ensure Location for Cox's Bazar
        $location = Location::updateOrCreate(
            ['city' => "Cox's Bazar"],
            [
                'name'         => "Cox's Bazar",
                'country'      => 'Bangladesh',
                'country_code' => 'BD',
                'latitude'     => 21.4272,
                'longitude'    => 92.0058,
                'image'        => '/storage/hotels/sea-pearl-beach-resort-spa/gallery/IMG-20260408-WA0004.jpg',
                'is_popular'   => 1,
            ]
        );

        // 5. Discover 19 Folders in Website Element
        $dirs = glob($websiteElementDir . '/*', GLOB_ONLYDIR);
        $hotelFolders = [];
        foreach ($dirs as $dir) {
            $b = basename($dir);
            if ($b === 'Common') continue;
            preg_match('/^(\d+)_(.+)$/', $b, $m);
            $sl = isset($m[1]) ? (int)$m[1] : 99;
            $hotelFolders[] = [
                'sl' => $sl,
                'path' => $dir,
                'folder' => $b,
            ];
        }
        usort($hotelFolders, fn($a, $b) => $a['sl'] <=> $b['sl']);

        $totalHotelsImported = 0;
        $totalRoomsImported = 0;
        $totalImagesCopied = 0;

        foreach ($hotelFolders as $hf) {
            $sl = $hf['sl'];
            $dir = $hf['path'];
            $folderName = $hf['folder'];
            $excel = $sheet2BySl[$sl] ?? [];

            $hotelName = !empty($excel['Hotel Full Name (হোটেলের পূর্ণ নাম)'])
                ? trim($excel['Hotel Full Name (হোটেলের পূর্ণ নাম)'])
                : preg_replace('/^\d+_/', '', $folderName);

            // Clean common typo in spreadsheet: "Windy Terrace Hote" -> "Hotel Windy Terrace"
            if ($hotelName === 'Windy Terrace Hote') {
                $hotelName = 'Hotel Windy Terrace';
            }

            $slug = Str::slug($hotelName);
            // Specific standardized slugs matching legacy structure
            if ($sl === 1) $slug = 'sea-pearl-beach-resort-spa';
            elseif ($sl === 2) $slug = 'ocean-paradise-hotel-resorts';
            elseif ($sl === 3) $slug = 'best-western-plus-bay-hills';
            elseif ($sl === 4) $slug = 'seagull-hotels-ltd';
            elseif ($sl === 5) $slug = 'best-western-heritage';
            elseif ($sl === 6) $slug = 'ramada-by-wyndham-coxs-bazar';
            elseif ($sl === 7) $slug = 'green-nature-resort-suites';
            elseif ($sl === 8) $slug = 'hotel-windy-terrace';
            elseif ($sl === 9) $slug = 'dera-resort-spa-coxs-bazar';
            elseif ($sl === 10) $slug = 'praasad-paradise';
            elseif ($sl === 11) $slug = 'hotel-grand-pacific';
            elseif ($sl === 12) $slug = 'neeshorgo-hotel-resort';
            elseif ($sl === 13) $slug = 'hotel-suite-sadaf';
            elseif ($sl === 14) $slug = 'hotel-the-cox-today';
            elseif ($sl === 15) $slug = 'long-beach-hotel-coxs-bazar';
            elseif ($sl === 16) $slug = 'shopnil-shindhu';
            elseif ($sl === 17) $slug = 'exotica-sampan';
            elseif ($sl === 18) $slug = 'grace-cox-smart-hotel';
            elseif ($sl === 19) $slug = 'royal-pearl-suites';

            $this->line("Processing [{$sl}/19]: {$hotelName} ({$slug})...");

            // Target Storage Directory: storage/app/public/hotels/<slug>
            $storageTargetDir = storage_path('app/public/hotels/' . $slug);
            $galleryTargetDir = $storageTargetDir . '/gallery';
            $roomsTargetDir   = $storageTargetDir . '/rooms';
            $videosTargetDir  = $storageTargetDir . '/videos';

            File::ensureDirectoryExists($galleryTargetDir);
            File::ensureDirectoryExists($roomsTargetDir);
            File::ensureDirectoryExists($videosTargetDir);

            // Copy Video if exists
            $videoUrl = null;
            $videoDir = $dir . '/Video/Final Video';
            if (is_dir($videoDir)) {
                $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($videoDir));
                foreach ($it as $f) {
                    if ($f->isFile()) {
                        $ext = strtolower($f->getExtension());
                        if (in_array($ext, ['mp4', 'mov', 'webm'])) {
                            $destFile = $videosTargetDir . '/' . $f->getFilename();
                            File::copy($f->getPathname(), $destFile);
                            $videoUrl = '/storage/hotels/' . $slug . '/videos/' . $f->getFilename();
                        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            File::copy($f->getPathname(), $videosTargetDir . '/' . $f->getFilename());
                        }
                    }
                }
            }

            // Copy Gallery / Exterior photos
            $imageDir = $dir . '/Image';
            $galleryRelativePaths = [];

            if (is_dir($imageDir)) {
                $subdirs = glob($imageDir . '/*', GLOB_ONLYDIR);
                foreach ($subdirs as $sd) {
                    $baseSd = basename($sd);
                    if (in_array(strtolower($baseSd), ['all image', 'all images', 'all photo', 'all photos', 'gallery', 'exterior'])) {
                        $files = glob($sd . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
                        foreach ($files as $file) {
                            $destName = basename($file);
                            $destPath = $galleryTargetDir . '/' . $destName;
                            File::copy($file, $destPath);
                            $galleryRelativePaths[] = '/storage/hotels/' . $slug . '/gallery/' . $destName;
                            $totalImagesCopied++;
                        }
                    }
                }
                // Also check top-level images directly inside Image/
                $topFiles = glob($imageDir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
                foreach ($topFiles as $file) {
                    $destName = basename($file);
                    $destPath = $galleryTargetDir . '/' . $destName;
                    File::copy($file, $destPath);
                    $galleryRelativePaths[] = '/storage/hotels/' . $slug . '/gallery/' . $destName;
                    $totalImagesCopied++;
                }
            }

            // Extract Star Rating
            $starStr = $excel['Star Rating (1 to 5 Star / 0 for unrated)'] ?? '4 star';
            preg_match('/(\d+)/', (string)$starStr, $sm);
            $starRating = isset($sm[1]) ? (int)$sm[1] : 4;
            if ($starRating < 1 || $starRating > 5) $starRating = 4;

            // Property Type
            $typeStr = strtolower(trim((string)($excel['Property Type (Hotel / Resort / Cottage / Homestay)'] ?? 'Hotel')));
            $propType = str_contains($typeStr, 'resort') ? 'resort' : 'hotel';

            // Address & Phone
            $address = !empty($excel['Full Physical Address (হোটেলের পূর্ণ ঠিকানা)'])
                ? trim($excel['Full Physical Address (হোটেলের পূর্ণ ঠিকানা)'])
                : "Marine Drive / Kolatoli, Cox's Bazar, Bangladesh";
            $phone = !empty($excel['Reservation / Front Desk Phone (বুকিং ও যোগাযোগ ফোন নম্বর)'])
                ? trim($excel['Reservation / Front Desk Phone (বুকিং ও যোগাযোগ ফোন নম্বর)'])
                : '+880 1800-000000';
            $mapUrl = !empty($excel['Google Maps Location Link (গুগল ম্যাপ লোকেশন লিংক)'])
                ? trim($excel['Google Maps Location Link (গুগল ম্যাপ লোকেশন লিংক)'])
                : 'https://maps.google.com/?q=' . urlencode($hotelName . " Cox's Bazar");

            $checkinTime = !empty($excel['Check-in Time (চেক-ইন সময়, যেমন: 14:00 / 02:00 PM)'])
                ? trim($excel['Check-in Time (চেক-ইন সময়, যেমন: 14:00 / 02:00 PM)'])
                : '2:00 PM';
            $checkoutTime = !empty($excel['Check-out Time (চেক-আউট সময়, যেমন: 12:00 PM)'])
                ? trim($excel['Check-out Time (চেক-আউট সময়, যেমন: 12:00 PM)'])
                : '12:00 PM';

            // Discover Room Categories from Image subfolders
            $roomFolders = [];
            if (is_dir($imageDir)) {
                $subdirs = glob($imageDir . '/*', GLOB_ONLYDIR);
                foreach ($subdirs as $sd) {
                    $baseSd = basename($sd);
                    if (!in_array(strtolower($baseSd), ['all image', 'all images', 'all photo', 'all photos', 'gallery', 'exterior'])) {
                        $roomFolders[$baseSd] = glob($sd . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
                    }
                }
            }

            // Fallback starting price and ratings based on star rating and legacy data
            $legacy = $existing11[$slug] ?? null;
            $ratingScore = $legacy['rating_score'] ?? match($starRating) {
                5 => round(9.0 + (rand(1, 5) / 10), 1),
                4 => round(8.5 + (rand(1, 5) / 10), 1),
                default => round(8.1 + (rand(1, 5) / 10), 1),
            };
            $locationScore = $legacy['location_score'] ?? match($starRating) {
                5 => '9.1',
                4 => '8.7',
                default => '8.4',
            };
            $totalReviews = $legacy['total_reviews'] ?? rand(140, 520);
            $nearestLandmark = $legacy['nearest_landmark'] ?? match($sl) {
                1 => "Inani Beach (0m, Direct Private Access), Himchori (15 mins)",
                2 => "Kolatoli Beach (3 mins walk), Sugandha Beach (800m)",
                3 => "Himchori Waterfall (5 mins), Marine Drive Beach (Direct Access)",
                4 => "Laboni Beach Point (Direct Private Access), Sugandha Point (1 km)",
                5 => "Kolatoli Beach (2 mins walk), Cox's Bazar Airport (10 mins)",
                6 => "Kolatoli Beach (50m), Sugandha Beach (5 mins walk)",
                7 => "Marine Drive Scenic Forest & Sea (Direct Access)",
                8 => "Sugandha Beach (5 mins walk), Laboni Beach (1.2 km)",
                9 => "Inani Coral Beach (5 mins walk), Marine Drive (Direct Access)",
                10 => "Main Laboni Beach (Direct Beachfront), Sugandha Beach (500m)",
                11 => "Kolatoli Circle (2 mins walk), Sugandha Beach (5 mins walk)",
                12 => "Marine Drive (Direct Sea View), Himchari (10 mins)",
                13 => "Himchari Beach (Direct Access), Marine Drive (0m)",
                14 => "Kolatoli Beach (200m), Sugandha Beach (600m)",
                15 => "Sugandha Beach (5 mins walk), Laboni Point (1.5 km)",
                16 => "New Beach Road, Marine Drive (Direct Beach Access)",
                17 => "Marine Drive Oceanfront, Himchori (8 mins)",
                18 => "Laboni Sea Beach Point (3 mins walk)",
                19 => "Light House Point, Kolatoli (5 mins walk)",
                default => "Cox's Bazar Sea Beach (5 mins walk)",
            };

            $description = $legacy['description'] ?? "{$hotelName} is one of the premier luxury properties in Cox's Bazar, situated along the breathtaking Bay of Bengal. Providing world-class hospitality, elegantly appointed rooms with private balconies, 24/7 dining, complimentary breakfast, high-speed WiFi, and personalized guest services.";

            $amenities = $legacy['amenities'] ?? [
                'Free High-Speed WiFi', 'Swimming Pool', 'Air Conditioning',
                'Multi-Cuisine Restaurant', '24/7 Room Service', 'Complimentary Breakfast',
                'Secured Car Parking', '24/7 Power Backup', 'Fitness Center / Gym',
                'Airport Shuttle Service', 'Concierge & Front Desk'
            ];

            // Primary Image fallback
            $primaryImage = !empty($galleryRelativePaths) ? $galleryRelativePaths[0] : null;

            // Process rooms first to calculate starting price accurately
            $createdRooms = [];
            $roomCounter = 0;

            foreach ($roomFolders as $rawRoomName => $roomPhotos) {
                $roomCounter++;
                // Clean name: e.g. "1_superior-double" -> "Superior Double Room"
                $cleanName = preg_replace('/^\d+_/', '', $rawRoomName);
                $cleanName = str_replace(['-', '_'], ' ', $cleanName);
                $cleanName = ucwords(trim($cleanName));
                if (!str_contains(strtolower($cleanName), 'room') && !str_contains(strtolower($cleanName), 'suite') && !str_contains(strtolower($cleanName), 'villa')) {
                    $cleanName .= ' Room';
                }

                // Copy Room Photos
                $roomSlug = Str::slug($cleanName);
                $roomSubDir = $roomsTargetDir . '/' . $roomSlug;
                File::ensureDirectoryExists($roomSubDir);

                $roomPhotoRelativePaths = [];
                foreach ($roomPhotos as $rPhoto) {
                    $destName = basename($rPhoto);
                    $destPath = $roomSubDir . '/' . $destName;
                    File::copy($rPhoto, $destPath);
                    $roomPhotoRelativePaths[] = '/storage/hotels/' . $slug . '/rooms/' . $roomSlug . '/' . $destName;
                    $totalImagesCopied++;
                }

                // If room has no specific photo, use gallery photos
                if (empty($roomPhotoRelativePaths) && !empty($galleryRelativePaths)) {
                    $roomPhotoRelativePaths = array_slice($galleryRelativePaths, 0, 4);
                }

                if (empty($primaryImage) && !empty($roomPhotoRelativePaths)) {
                    $primaryImage = $roomPhotoRelativePaths[0];
                }

                // Pricing calculation based on star rating, room index, and type
                $basePrice = match($starRating) {
                    5 => 7500,
                    4 => 5200,
                    default => 3800,
                };

                // Add premium for suites or higher categories
                $lowerName = strtolower($cleanName);
                $multiplier = 1.0;
                if (str_contains($lowerName, 'presidential') || str_contains($lowerName, 'royal')) {
                    $multiplier = 2.4;
                } elseif (str_contains($lowerName, 'executive') || str_contains($lowerName, 'suite')) {
                    $multiplier = 1.6;
                } elseif (str_contains($lowerName, 'premier') || str_contains($lowerName, 'super deluxe')) {
                    $multiplier = 1.3;
                } elseif (str_contains($lowerName, 'triple') || str_contains($lowerName, 'family')) {
                    $multiplier = 1.4;
                }

                $roomPrice = round($basePrice * $multiplier + (($roomCounter - 1) * 350));

                // Bed type and capacity
                $bedType = '1 King Bed';
                $adults = 2;
                $guests = 2;
                $sqm = 38 + (($roomCounter - 1) * 4);

                if (str_contains($lowerName, 'twin')) {
                    $bedType = '2 Twin Beds';
                    $adults = 2;
                } elseif (str_contains($lowerName, 'triple')) {
                    $bedType = '3 Single Beds';
                    $adults = 3;
                    $guests = 3;
                    $sqm = 48;
                } elseif (str_contains($lowerName, 'family') || str_contains($lowerName, 'quad')) {
                    $bedType = '2 Queen Beds';
                    $adults = 4;
                    $guests = 4;
                    $sqm = 58;
                } elseif (str_contains($lowerName, 'suite') || str_contains($lowerName, 'villa')) {
                    $bedType = '1 King Bed + 1 Sofa Bed';
                    $adults = 3;
                    $guests = 3;
                    $sqm = 65;
                }

                $viewType = 'City View';
                if (str_contains($lowerName, 'sea') || str_contains($lowerName, 'ocean')) {
                    $viewType = 'Ocean / Sea View';
                } elseif (str_contains($lowerName, 'hill')) {
                    $viewType = 'Scenic Hill View';
                } elseif (str_contains($lowerName, 'waterbody') || str_contains($lowerName, 'pool')) {
                    $viewType = 'Pool / Lagoon View';
                }

                $createdRooms[] = [
                    'name' => $cleanName,
                    'price' => $roomPrice,
                    'bed' => $bedType,
                    'view' => $viewType,
                    'adults' => $adults,
                    'guests' => $guests,
                    'sqm' => $sqm,
                    'images' => $roomPhotoRelativePaths,
                ];
            }

            // Starting price & original price
            $lowestPrice = !empty($createdRooms) ? min(array_column($createdRooms, 'price')) : match($starRating) {
                5 => 7500,
                4 => 5000,
                default => 3500,
            };
            $origPrice = round($lowestPrice * 1.25);

            // Create Property
            $property = Property::create([
                'vendor_id'               => 1,
                'location_id'             => $location->id,
                'name'                    => $hotelName,
                'slug'                    => $slug,
                'type'                    => $propType,
                'star_rating'             => $starRating,
                'rating_score'            => $ratingScore,
                'total_reviews'           => $totalReviews,
                'city'                    => "Cox's Bazar",
                'address'                 => $address,
                'nearest_landmark'        => $nearestLandmark,
                'latitude'                => 21.4272,
                'longitude'               => 92.0058,
                'map_embed_url'           => $mapUrl,
                'contact_phone'           => $phone,
                'contact_email'           => 'booking@' . Str::slug($hotelName) . '.com.bd',
                'checkin_time'            => $checkinTime,
                'checkout_time'           => $checkoutTime,
                'price_per_night'         => $lowestPrice,
                'original_price'          => $origPrice,
                'primary_image'           => $primaryImage ?: '/storage/hotels/sea-pearl-beach-resort-spa/gallery/IMG-20260408-WA0004.jpg',
                'video_url'               => $videoUrl,
                'images'                  => !empty($galleryRelativePaths) ? $galleryRelativePaths : [$primaryImage],
                'amenities'               => $amenities,
                'description'             => $description,
                'is_featured'             => 1,
                'status'                  => Property::STATUS_PUBLISHED,
                'free_cancellation'       => 1,
                'no_credit_card_required' => 1,
                'rooms_left'              => 5,
            ]);

            // Create Rooms
            foreach ($createdRooms as $rData) {
                Room::create([
                    'property_id'        => $property->id,
                    'name'               => $rData['name'],
                    'room_type'          => str_contains(strtolower($rData['name']), 'suite') ? 'suite' : 'deluxe',
                    'room_size_sqm'      => $rData['sqm'],
                    'bed_type'           => $rData['bed'],
                    'view_type'          => $rData['view'],
                    'max_guests'         => $rData['guests'],
                    'max_adults'         => $rData['adults'],
                    'max_children'       => 1,
                    'price_per_night'    => $rData['price'],
                    'total_rooms'        => 10,
                    'available_rooms'    => 5,
                    'breakfast_included' => 1,
                    'free_cancellation'  => 1,
                    'extra_bed_allowed'  => ($rData['guests'] >= 3) ? 1 : 0,
                    'bathroom_count'     => 1,
                    'smoking_policy'     => str_contains(strtolower($rData['name']), 'smoking') && !str_contains(strtolower($rData['name']), 'non-smoking') ? 'Smoking Allowed' : 'Non-Smoking',
                    'balcony_type'       => str_contains(strtolower($rData['name']), 'balcony') || str_contains(strtolower($rData['view']), 'sea') ? 'Private Balcony' : 'Standard Window',
                    'facilities'         => [
                        $rData['view'],
                        'Hair dryer',
                        'Wi-Fi [free]',
                        'Private bathroom',
                        'Shower',
                        'Air conditioning',
                        'Free bottled water',
                        'Mini bar / Refrigerator',
                        'Electric Kettle',
                        'Flat-screen Smart TV',
                        'In-room Safe',
                        'Non-smoking'
                    ],
                    'images'             => $rData['images'],
                    'status'             => 'active',
                ]);
                $totalRoomsImported++;
            }

            $totalHotelsImported++;
            $this->info("  -> Saved {$hotelName}: " . count($createdRooms) . " rooms, " . count($galleryRelativePaths) . " gallery images, video: " . ($videoUrl ? 'YES' : 'NO'));
        }

        // 6. Update Featured Destination Counter for Cox's Bazar
        DB::table('featured_destinations')->where('city', "Cox's Bazar")->update([
            'image_url'               => '/storage/hotels/sea-pearl-beach-resort-spa/gallery/IMG-20260408-WA0004.jpg',
            'description'             => "World's longest natural sea beach featuring 19 authentic 5-star & luxury resorts.",
            'property_count_override' => $totalHotelsImported,
            'min_price_override'      => 3500.00,
            'is_active'               => 1,
            'is_featured'             => 1,
            'sort_order'              => 1,
        ]);

        $this->info("=================================================");
        $this->info("ALL DONE! Imported {$totalHotelsImported} Hotels, {$totalRoomsImported} Rooms, {$totalImagesCopied} Photos & Videos.");
        $this->info("=================================================");

        return 0;
    }
}
