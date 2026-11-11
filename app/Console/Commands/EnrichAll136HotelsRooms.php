<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class EnrichAll136HotelsRooms extends Command
{
    protected $signature = 'hotels:enrich-rooms {--hotel= : Specific hotel ID or slug}';
    protected $description = 'Enrich, deduplicate and ensure authentic Agoda-style differentiation for all 136 hotels rooms';

    public function handle(): int
    {
        $this->info("Starting Room Enrichment & Deduplication across all hotels...");

        $hotelOpt = $this->option('hotel');
        $query = Property::with('rooms');
        if ($hotelOpt) {
            $query->where('id', $hotelOpt)->orWhere('slug', $hotelOpt);
        }
        $properties = $query->get();

        $this->info("Found " . $properties->count() . " properties to process.");

        $totalDeletedDuplicates = 0;
        $totalUpdatedRooms = 0;

        foreach ($properties as $property) {
            $propRooms = $property->rooms;
            if ($propRooms->isEmpty()) {
                continue;
            }

            // 1. Deduplication within Property
            $seenNames = [];
            foreach ($propRooms as $room) {
                // Normalize name: strip trailing "Room", "Rooms", punctuation, trim, lowercase
                $normName = strtolower(trim(preg_replace('/\brooms?\b/i', '', $room->name)));
                $normName = preg_replace('/[^a-z0-9]/', '', $normName);

                if (isset($seenNames[$normName])) {
                    $existingRoom = $seenNames[$normName];
                    
                    // Decide which one to keep: prefer room with richer photos or non-dummy data
                    $existingImages = is_array($existingRoom->images) ? $existingRoom->images : [];
                    $roomImages = is_array($room->images) ? $room->images : [];
                    $existingHasDummy = empty($existingImages) || str_contains(json_encode($existingImages), 'update_room');
                    $roomHasDummy = empty($roomImages) || str_contains(json_encode($roomImages), 'update_room');

                    if ($existingHasDummy && !$roomHasDummy) {
                        // Delete existing, keep current
                        $existingRoom->delete();
                        $seenNames[$normName] = $room;
                        $totalDeletedDuplicates++;
                    } else {
                        // Delete current duplicate
                        $room->delete();
                        $totalDeletedDuplicates++;
                    }
                } else {
                    $seenNames[$normName] = $room;
                }
            }

            // Reload remaining unique rooms
            $property->load('rooms');
            $rooms = $property->rooms->values();
            $basePrice = (float)($property->price_per_night ?: 4500);

            // 2. Enrich each room with distinct, authentic Agoda attributes
            foreach ($rooms as $idx => $room) {
                $name = $room->name;
                $nameLower = strtolower($name);

                // --- BED TYPE INFERENCE ---
                if (str_contains($nameLower, 'twin') || str_contains($nameLower, '2 single') || str_contains($nameLower, 'single beds')) {
                    $bedType = '2 Single Beds';
                    $adults = 2;
                    $kids = 1;
                } elseif (str_contains($nameLower, 'triple') || str_contains($nameLower, '3 bed') || str_contains($nameLower, '3 single')) {
                    $bedType = '3 Single Beds';
                    $adults = 3;
                    $kids = 1;
                } elseif (str_contains($nameLower, 'family') || str_contains($nameLower, 'quad') || str_contains($nameLower, '4 bed')) {
                    $bedType = '2 Queen Beds';
                    $adults = 4;
                    $kids = 2;
                } elseif (str_contains($nameLower, 'queen')) {
                    $bedType = '1 Queen Bed';
                    $adults = 2;
                    $kids = 1;
                } elseif (str_contains($nameLower, 'super king')) {
                    $bedType = '1 Super King Bed';
                    $adults = 2;
                    $kids = 1;
                } elseif (str_contains($nameLower, 'suite') || str_contains($nameLower, 'presidential') || str_contains($nameLower, 'executive')) {
                    $bedType = '1 King Bed + 1 Sofa Bed';
                    $adults = 3;
                    $kids = 2;
                } elseif (str_contains($nameLower, 'single') && !str_contains($nameLower, 'double')) {
                    $bedType = '1 Single Bed';
                    $adults = 1;
                    $kids = 0;
                } elseif (str_contains($nameLower, 'double')) {
                    $bedType = '1 Double Bed';
                    $adults = 2;
                    $kids = 1;
                } elseif (str_contains($nameLower, 'king')) {
                    $bedType = '1 King Bed';
                    $adults = 2;
                    $kids = 1;
                } else {
                    // Stagger realistic bed types if generic
                    $genericBeds = ['1 King Bed', '2 Single Beds', '1 Queen Bed', '1 King Bed'];
                    $bedType = $genericBeds[$idx % count($genericBeds)];
                    $adults = 2;
                    $kids = 1;
                }

                // --- VIEW TYPE INFERENCE ---
                $city = strtolower($property->city ?? '');
                if (str_contains($nameLower, 'sea') || str_contains($nameLower, 'ocean') || str_contains($nameLower, 'beach')) {
                    $viewType = 'Ocean / Sea View';
                } elseif (str_contains($nameLower, 'hill') || str_contains($nameLower, 'mountain')) {
                    $viewType = 'Scenic Mountain View';
                } elseif (str_contains($nameLower, 'garden') || str_contains($nameLower, 'park') || str_contains($nameLower, 'lawn')) {
                    $viewType = 'Garden view';
                } elseif (str_contains($nameLower, 'pool') || str_contains($nameLower, 'lagoon')) {
                    $viewType = 'Pool / Lagoon View';
                } elseif (str_contains($nameLower, 'lake') || str_contains($nameLower, 'river')) {
                    $viewType = 'Lake View';
                } elseif (str_contains($nameLower, 'city')) {
                    $viewType = 'City View';
                } else {
                    // Geographic sensible defaults
                    if (str_contains($city, 'cox') || str_contains($city, 'kuakata') || str_contains($city, 'martin')) {
                        $viewType = ($idx % 2 === 0) ? 'Ocean / Sea View' : 'City View';
                    } elseif (str_contains($city, 'sajek') || str_contains($city, 'bandarban') || str_contains($city, 'rangamati') || str_contains($city, 'sylhet') || str_contains($city, 'sreemangal')) {
                        $viewType = ($idx % 2 === 0) ? 'Scenic Mountain View' : 'Valley & Cloud View';
                    } else {
                        $viewType = ($idx % 2 === 0) ? 'City View' : 'Garden view';
                    }
                }

                // --- BALCONY TYPE INFERENCE ---
                if (str_contains($nameLower, 'balcony') || str_contains($nameLower, 'terrace') || str_contains($viewType, 'Sea') || str_contains($viewType, 'Mountain')) {
                    $balcony = 'Private Balcony';
                } else {
                    $balcony = ($idx % 3 === 0) ? 'Private Balcony' : 'Balcony / Window';
                }

                // --- ROOM SIZE INFERENCE ---
                if (str_contains($nameLower, 'presidential') || str_contains($nameLower, 'royal') || str_contains($nameLower, 'villa')) {
                    $sizeSqm = 95 + ($idx * 5);
                } elseif (str_contains($nameLower, 'suite') || str_contains($nameLower, 'executive')) {
                    $sizeSqm = 65 + ($idx * 3);
                } elseif (str_contains($nameLower, 'family') || str_contains($nameLower, 'quad') || str_contains($nameLower, 'connecting')) {
                    $sizeSqm = 56 + ($idx * 2);
                } elseif (str_contains($nameLower, 'premium') || str_contains($nameLower, 'superior')) {
                    $sizeSqm = 48 + ($idx * 2);
                } elseif (str_contains($nameLower, 'deluxe')) {
                    $sizeSqm = 38 + ($idx * 2);
                } elseif (str_contains($nameLower, 'standard') || str_contains($nameLower, 'economy') || str_contains($nameLower, 'single')) {
                    $sizeSqm = 30 + $idx;
                } else {
                    $sizeSqm = 35 + ($idx * 3);
                }

                // --- HIERARCHICAL TIERED PRICING ---
                // Calculate tiered price based on room tier
                $tierMultiplier = 1.0;
                if (str_contains($nameLower, 'presidential') || str_contains($nameLower, 'royal')) {
                    $tierMultiplier = 2.2;
                } elseif (str_contains($nameLower, 'executive suite') || str_contains($nameLower, 'villa')) {
                    $tierMultiplier = 1.85;
                } elseif (str_contains($nameLower, 'suite')) {
                    $tierMultiplier = 1.65;
                } elseif (str_contains($nameLower, 'family') || str_contains($nameLower, 'quad')) {
                    $tierMultiplier = 1.45;
                } elseif (str_contains($nameLower, 'superior') || str_contains($nameLower, 'premium')) {
                    $tierMultiplier = 1.30;
                } elseif (str_contains($nameLower, 'deluxe') && (str_contains($viewType, 'Sea') || str_contains($nameLower, 'bathtub'))) {
                    $tierMultiplier = 1.20;
                } elseif (str_contains($nameLower, 'deluxe')) {
                    $tierMultiplier = 1.10;
                } elseif (str_contains($nameLower, 'standard') || str_contains($nameLower, 'economy')) {
                    $tierMultiplier = 0.95;
                } else {
                    $tierMultiplier = 1.0 + ($idx * 0.08);
                }

                // If room price was flat or zero, compute realistic tiered price
                $calculatedPrice = round(($basePrice * $tierMultiplier) / 50) * 50;
                
                // Keep original room price if it's already distinct and higher than base, otherwise apply tiered price
                $finalPrice = $calculatedPrice;
                if ($room->price_per_night > 0 && $idx > 0 && abs($room->price_per_night - $basePrice) > 200) {
                    $finalPrice = (float)$room->price_per_night;
                }

                // Extra bed allowed
                $extraBed = ($adults >= 3 || str_contains($nameLower, 'suite') || str_contains($nameLower, 'deluxe')) ? 1 : 0;

                // Bathroom features
                $bathFeatures = str_contains($nameLower, 'bathtub') 
                    ? 'Deep soaking bathtub, rain shower, luxury amenities, hair dryer'
                    : 'Private bathroom, rain shower, free toiletries, hot water';

                // Save updates
                $room->bed_type = $bedType;
                $room->view_type = $viewType;
                $room->balcony_type = $balcony;
                $room->room_size_sqm = $sizeSqm;
                $room->max_adults = $adults;
                $room->max_guests = $adults;
                $room->max_children = $kids;
                $room->price_per_night = $finalPrice;
                $room->bathroom_features = $bathFeatures;
                $room->extra_bed_allowed = $extraBed;
                $room->smoking_policy = str_contains($nameLower, 'smoking allowed') ? 'Smoking Allowed' : 'Non-Smoking';
                $room->breakfast_included = 1;
                $room->free_cancellation = 1;
                $room->save();

                $totalUpdatedRooms++;
            }
        }

        $this->info("Completed!");
        $this->info("Deleted Duplicate Rooms: {$totalDeletedDuplicates}");
        $this->info("Enriched & Differentiated Rooms: {$totalUpdatedRooms}");

        return 0;
    }
}

