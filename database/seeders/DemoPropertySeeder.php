<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Room;
use App\Models\Location;

class DemoPropertySeeder extends Seeder
{
    public function run(): void
    {
        $coxsBazar = Location::firstOrCreate(
            ['city' => 'Cox\'s Bazar'],
            ['name' => 'Cox\'s Bazar Beach', 'country' => 'Bangladesh', 'country_code' => 'BD', 'is_popular' => true]
        );

        $sreemangal = Location::firstOrCreate(
            ['city' => 'Sreemangal'],
            ['name' => 'Sreemangal Tea Gardens', 'country' => 'Bangladesh', 'country_code' => 'BD', 'is_popular' => true]
        );

        $dhaka = Location::firstOrCreate(
            ['city' => 'Dhaka'],
            ['name' => 'Dhaka Airport Road', 'country' => 'Bangladesh', 'country_code' => 'BD', 'is_popular' => true]
        );

        $sylhet = Location::firstOrCreate(
            ['city' => 'Sylhet'],
            ['name' => 'Sylhet Jaflong & Tea Estate', 'country' => 'Bangladesh', 'country_code' => 'BD', 'is_popular' => true]
        );

        $properties = [
            [
                'name' => 'Sea Pearl Beach Resort & Spa',
                'slug' => 'sea-pearl-beach-resort-spa',
                'location_id' => $coxsBazar->id,
                'address' => 'Inani Beach, Cox\'s Bazar',
                'star_rating' => 5,
                'rating_score' => 9.2,
                'total_reviews' => 1420,
                'price_per_night' => 8500,
                'description' => '5-Star Luxury Oceanfront Resort located on Inani Beach with private balcony ocean views, 3 swimming pools, waterpark, and spa.',
                'rooms' => [
                    ['name' => 'Deluxe Ocean View Room', 'price_per_night' => 8500, 'max_guests' => 2, 'bed_type' => '1 King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                    ['name' => 'Executive Suite (Balcony View)', 'price_per_night' => 14500, 'max_guests' => 4, 'bed_type' => '2 Super King Beds', 'breakfast_included' => true, 'free_cancellation' => true],
                ]
            ],
            [
                'name' => 'Grand Sultan Tea Resort & Golf',
                'slug' => 'grand-sultan-tea-resort',
                'location_id' => $sreemangal->id,
                'address' => 'Radhanagar, Sreemangal, Moulvibazar',
                'star_rating' => 5,
                'rating_score' => 9.4,
                'total_reviews' => 980,
                'price_per_night' => 11000,
                'description' => 'Premier 5-star tea garden resort in Sreemangal featuring 9-hole golf course, 3 temperature-controlled swimming pools, and spa.',
                'rooms' => [
                    ['name' => 'King Deluxe Room', 'price_per_night' => 11000, 'max_guests' => 2, 'bed_type' => '1 King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                    ['name' => 'Presidential Villa', 'price_per_night' => 24000, 'max_guests' => 4, 'bed_type' => '2 King Beds', 'breakfast_included' => true, 'free_cancellation' => true],
                ]
            ],
            [
                'name' => 'Radisson Blu Dhaka Water Garden',
                'slug' => 'radisson-blu-dhaka',
                'location_id' => $dhaka->id,
                'address' => 'Airport Road, Cantonment, Dhaka',
                'star_rating' => 5,
                'rating_score' => 9.0,
                'total_reviews' => 2100,
                'price_per_night' => 12500,
                'description' => 'Luxury 5-star airport hotel set amidst 7 acres of manicured gardens, outdoor pool, golf course, and fine dining restaurants.',
                'rooms' => [
                    ['name' => 'Superior Garden View', 'price_per_night' => 12500, 'max_guests' => 2, 'bed_type' => '1 King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                    ['name' => 'Executive Club Room', 'price_per_night' => 18000, 'max_guests' => 2, 'bed_type' => '1 Super King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                ]
            ],
            [
                'name' => 'Sayeman Beach Resort',
                'slug' => 'sayeman-beach-resort',
                'location_id' => $coxsBazar->id,
                'address' => 'Marine Drive, Kolatoli, Cox\'s Bazar',
                'star_rating' => 4,
                'rating_score' => 8.8,
                'total_reviews' => 1650,
                'price_per_night' => 6500,
                'description' => 'Iconic beachfront hotel on Marine Drive with infinity pool facing the Bay of Bengal, rooftop dining, and direct beach access.',
                'rooms' => [
                    ['name' => 'Super Deluxe Ocean View', 'price_per_night' => 6500, 'max_guests' => 2, 'bed_type' => '1 Queen Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                    ['name' => 'Panorama Suite', 'price_per_night' => 11500, 'max_guests' => 3, 'bed_type' => '1 King + 1 Single Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                ]
            ],
            [
                'name' => 'Dusai Resort & Spa',
                'slug' => 'dusai-resort-spa',
                'location_id' => $sylhet->id,
                'address' => 'Niteshwar, Sreemangal Road, Moulvibazar',
                'star_rating' => 5,
                'rating_score' => 9.1,
                'total_reviews' => 740,
                'price_per_night' => 9500,
                'description' => 'Exclusive boutique hill resort surrounded by tea gardens and natural lake, private infinity pools, and 3D cinema hall.',
                'rooms' => [
                    ['name' => 'Villa King Room', 'price_per_night' => 9500, 'max_guests' => 2, 'bed_type' => '1 King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                    ['name' => 'Honeymoon Pool Villa', 'price_per_night' => 19000, 'max_guests' => 2, 'bed_type' => '1 Super King Bed', 'breakfast_included' => true, 'free_cancellation' => true],
                ]
            ]
        ];

        foreach ($properties as $data) {
            $rooms = $data['rooms'];
            unset($data['rooms']);

            $property = Property::updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($rooms as $r) {
                Room::updateOrCreate([
                    'property_id' => $property->id,
                    'name' => $r['name']
                ], $r);
            }
        }
    }
}
