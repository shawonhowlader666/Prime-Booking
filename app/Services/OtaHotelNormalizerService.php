<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Str;

class OtaHotelNormalizerService
{
    /**
     * Ingest and normalize any raw external API hotel payload into a standard Property & Room structure.
     *
     * @param array<string, mixed> $payload
     * @param int|null $vendorId
     * @return Property
     */
    public static function normalizeAndSave(array $payload, ?int $vendorId = null): Property
    {
        $name = $payload['name'] ?? $payload['hotel_name'] ?? 'Luxury Stay';
        $city = $payload['city'] ?? $payload['destination'] ?? 'Dhaka';
        $price = (float)($payload['price'] ?? $payload['price_per_night'] ?? $payload['base_rate'] ?? 8500);
        $star = (int)($payload['star_rating'] ?? $payload['stars'] ?? 4);

        $propertyData = [
            'vendor_id'               => $vendorId,
            'name'                    => $name,
            'slug'                    => Str::slug($name) . '-' . Str::random(4),
            'type'                    => $payload['type'] ?? 'hotel',
            'city'                    => $city,
            'star_rating'             => $star,
            'rating_score'            => (float)($payload['rating_score'] ?? $payload['score'] ?? 8.7),
            'total_reviews'           => (int)($payload['total_reviews'] ?? $payload['reviews_count'] ?? 142),
            'address'                 => $payload['address'] ?? $payload['street'] ?? "{$city}, Bangladesh",
            'description'             => $payload['description'] ?? "Experience superior hospitality and world-class amenities at {$name} in {$city}.",
            'price_per_night'         => $price,
            'original_price'          => (float)($payload['original_price'] ?? round($price * 1.25)),
            'primary_image'           => $payload['primary_image'] ?? $payload['image'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            'images'                  => (array)($payload['images'] ?? $payload['gallery'] ?? []),
            'amenities'               => (array)($payload['amenities'] ?? ['WiFi', 'Air Conditioning', 'Free Breakfast', 'Parking', '24/7 Front Desk']),
            'is_featured'             => (bool)($payload['is_featured'] ?? false),
            'status'                  => 'active',
            'free_cancellation'       => (bool)($payload['free_cancellation'] ?? true),
            'no_credit_card_required' => (bool)($payload['no_credit_card_required'] ?? false),
            'latitude'                => (string)($payload['latitude'] ?? $payload['lat'] ?? '23.8103'),
            'longitude'               => (string)($payload['longitude'] ?? $payload['lng'] ?? '90.4125'),
            'map_embed_url'           => $payload['map_embed_url'] ?? null,
            'nearest_landmark'        => $payload['nearest_landmark'] ?? "Near {$city} Central Transit Point",
            'checkin_time'            => $payload['checkin_time'] ?? '14:00',
            'checkout_time'           => $payload['checkout_time'] ?? '12:00',
            'contact_phone'           => $payload['contact_phone'] ?? '+8801700000000',
            'contact_email'           => $payload['contact_email'] ?? 'info@primeaviation.com',
            'total_floors'            => (int)($payload['total_floors'] ?? 10),
            'total_rooms_count'       => (int)($payload['total_rooms_count'] ?? 120),
            'year_built'              => (int)($payload['year_built'] ?? 2021),
            'languages_spoken'        => (array)($payload['languages_spoken'] ?? ['English', 'Bengali']),
            'pets_policy'             => $payload['pets_policy'] ?? 'Pets Not Allowed',
        ];

        $property = Property::create($propertyData);

        // Normalize Rooms
        $rawRooms = (array)($payload['rooms'] ?? []);
        if (empty($rawRooms)) {
            $rawRooms = [
                [
                    'name'              => 'Superior Deluxe Room',
                    'bed_type'          => '1 King Bed or 2 Twin Beds',
                    'max_adults'        => 2,
                    'max_children'      => 1,
                    'price_per_night'   => $price,
                    'room_size_sqm'     => 46,
                    'view_type'         => 'City Skyline View',
                    'bathroom_count'    => 1,
                    'bathroom_features' => ['Private Bathroom', 'Hot Water Geyser', 'Free Toiletries'],
                    'smoking_policy'    => 'Non-Smoking',
                    'balcony_type'      => 'Private Balcony',
                    'extra_bed_allowed' => true,
                ],
                [
                    'name'              => 'Executive Suite',
                    'bed_type'          => '1 King Bed + Living Area',
                    'max_adults'        => 3,
                    'max_children'      => 2,
                    'price_per_night'   => round($price * 1.4),
                    'room_size_sqm'     => 68,
                    'view_type'         => 'Panoramic View',
                    'bathroom_count'    => 2,
                    'bathroom_features' => ['Private Bathroom', 'Bathtub / Jacuzzi', 'Hot Water Geyser', 'Bathrobe & Slippers'],
                    'smoking_policy'    => 'Non-Smoking',
                    'balcony_type'      => 'Large Terrace',
                    'extra_bed_allowed' => true,
                ],
            ];
        }

        foreach ($rawRooms as $r) {
            Room::create([
                'property_id'        => $property->id,
                'name'               => $r['name'] ?? 'Deluxe Room',
                'bed_type'           => $r['bed_type'] ?? '1 King Bed',
                'max_adults'         => (int)($r['max_adults'] ?? 2),
                'max_children'       => (int)($r['max_children'] ?? 1),
                'price_per_night'    => (float)($r['price_per_night'] ?? $price),
                'total_rooms'        => (int)($r['total_rooms'] ?? 10),
                'room_size_sqm'      => (int)($r['room_size_sqm'] ?? 46),
                'view_type'          => $r['view_type'] ?? 'City View',
                'bathroom_count'     => (int)($r['bathroom_count'] ?? 1),
                'bathroom_features'  => (array)($r['bathroom_features'] ?? ['Private Bathroom', 'Hot Water Geyser']),
                'smoking_policy'     => $r['smoking_policy'] ?? 'Non-Smoking',
                'balcony_type'       => $r['balcony_type'] ?? 'Private Balcony',
                'extra_bed_allowed'  => (bool)($r['extra_bed_allowed'] ?? true),
                'breakfast_included' => (bool)($r['breakfast_included'] ?? true),
                'free_cancellation'  => (bool)($r['free_cancellation'] ?? true),
                'status'             => 'active',
            ]);
        }

        return $property;
    }
}
