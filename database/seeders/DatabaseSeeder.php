<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@primeavn.com'],
            [
                'name' => 'PRIME BOOKING Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '01770887733',
                'status' => 'active',
            ]
        );

        // Vendor User
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@primeavn.com'],
            [
                'name' => 'Royal Tulip Hotel Manager',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => '01785880033',
                'status' => 'active',
            ]
        );

        // Demo Locations
        $dhaka = Location::firstOrCreate(
            ['name' => 'Dhaka'],
            [
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'is_popular' => true,
            ]
        );

        $coxsbazar = Location::firstOrCreate(
            ['name' => 'Cox\'s Bazar'],
            [
                'city' => 'Cox\'s Bazar',
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'latitude' => 21.4272,
                'longitude' => 91.9702,
                'is_popular' => true,
            ]
        );

        $bangkok = Location::firstOrCreate(
            ['name' => 'Bangkok'],
            [
                'city' => 'Bangkok',
                'country' => 'Thailand',
                'country_code' => 'TH',
                'latitude' => 13.7563,
                'longitude' => 100.5018,
                'is_popular' => true,
            ]
        );

        $dubai = Location::firstOrCreate(
            ['name' => 'Dubai'],
            [
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
                'is_popular' => true,
            ]
        );

        $mecca = Location::firstOrCreate(
            ['name' => 'Makkah'],
            [
                'city' => 'Makkah',
                'country' => 'Saudi Arabia',
                'country_code' => 'SA',
                'latitude' => 21.3891,
                'longitude' => 39.8579,
                'is_popular' => true,
            ]
        );

        // Demo Properties
        $p1 = Property::firstOrCreate(
            ['slug' => 'royal-tulip-sea-pearl-beach-resort'],
            [
                'vendor_id' => $vendor->id,
                'location_id' => $coxsbazar->id,
                'name' => 'Royal Tulip Sea Pearl Beach Resort & Spa',
                'type' => 'Resort',
                'star_rating' => 5,
                'rating_score' => 9.2,
                'total_reviews' => 450,
                'address' => 'Jalia Palong, Inani, Ukhia, Cox\'s Bazar',
                'description' => 'Experience luxury on the world\'s longest natural sea beach. Sea Pearl Beach Resort offers 5-star amenities, beachfront access, multiple swimming pools, and world-class spa facilities.',
                'price_per_night' => 12500.00,
                'original_price' => 16000.00,
                'primary_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                ],
                'amenities' => ['WiFi', 'Pool', 'Parking', 'Spa', 'Gym', 'Restaurant', 'Beachfront', 'AC'],
                'is_featured' => true,
                'status' => 'published',
            ]
        );

        Room::firstOrCreate(
            ['property_id' => $p1->id, 'name' => 'Superior Sea View Room'],
            [
                'max_guests' => 2,
                'max_adults' => 2,
                'max_children' => 1,
                'room_size_sqm' => 42,
                'bed_type' => '1 King Bed or 2 Twin Beds',
                'price_per_night' => 12500.00,
                'total_rooms' => 15,
                'breakfast_included' => true,
                'free_cancellation' => true,
                'facilities' => ['Balcony', 'Ocean View', 'Flat TV', 'Mini Bar', 'Safe'],
            ]
        );

        Room::firstOrCreate(
            ['property_id' => $p1->id, 'name' => 'Executive Ocean Suite'],
            [
                'max_guests' => 4,
                'max_adults' => 3,
                'max_children' => 2,
                'room_size_sqm' => 68,
                'bed_type' => '1 King Bed + Living Lounge',
                'price_per_night' => 22000.00,
                'total_rooms' => 8,
                'breakfast_included' => true,
                'free_cancellation' => true,
                'facilities' => ['Jacuzzi', 'Private Balcony', 'Express Check-in', 'Free Spa Pass'],
            ]
        );

        $p2 = Property::firstOrCreate(
            ['slug' => 'pan-pacific-sonargaon-dhaka'],
            [
                'vendor_id' => $vendor->id,
                'location_id' => $dhaka->id,
                'name' => 'Pan Pacific Sonargaon Dhaka',
                'type' => 'Hotel',
                'star_rating' => 5,
                'rating_score' => 8.8,
                'total_reviews' => 620,
                'address' => '107 Kazi Nazrul Islam Avenue, Dhaka 1215',
                'description' => 'Iconic 5-star hotel in the heart of Dhaka city. Featuring landscaped gardens, outdoor swimming pool, health club, and international dining options.',
                'price_per_night' => 14000.00,
                'original_price' => 18500.00,
                'primary_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                ],
                'amenities' => ['WiFi', 'Pool', 'Parking', 'Gym', 'Restaurant', 'Bar', 'AC', 'Airport Shuttle'],
                'is_featured' => true,
                'status' => 'published',
            ]
        );

        Room::firstOrCreate(
            ['property_id' => $p2->id, 'name' => 'Deluxe King Room'],
            [
                'max_guests' => 2,
                'max_adults' => 2,
                'max_children' => 1,
                'room_size_sqm' => 38,
                'bed_type' => '1 King Bed',
                'price_per_night' => 14000.00,
                'total_rooms' => 20,
                'breakfast_included' => true,
                'free_cancellation' => true,
                'facilities' => ['City View', 'Work Desk', 'Marble Bathroom', 'Coffee Maker'],
            ]
        );

        // Demo Sundarban Ships & Tanguar Haor Houseboats
        $p3 = Property::firstOrCreate(
            ['slug' => 'sundarban-zabin-luxury-ship-cruise'],
            [
                'vendor_id' => $vendor->id,
                'location_id' => $dhaka->id,
                'name' => 'MV Zabin Sundarban Luxury Ship Cruise',
                'type' => 'Ship / Houseboat',
                'star_rating' => 5,
                'rating_score' => 9.5,
                'total_reviews' => 280,
                'address' => 'Mongla Port / Sundarbans Mangrove Forest, Khulna',
                'description' => '3 Days 2 Nights Premium Forest Cruise inside Sundarbans with AC Cabins, Sundarban guides, boat safari, and buffet dining.',
                'price_per_night' => 18500.00,
                'original_price' => 24000.00,
                'primary_image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                ],
                'amenities' => ['WiFi', 'AC', 'Buffet Meals', 'Forest Tour Guide', 'Small Boat Safari'],
                'is_featured' => true,
                'status' => 'published',
            ]
        );

        Room::firstOrCreate(
            ['property_id' => $p3->id, 'name' => 'AC Deluxe Ship Cabin'],
            [
                'max_guests' => 2,
                'max_adults' => 2,
                'max_children' => 1,
                'room_size_sqm' => 24,
                'bed_type' => '1 Double Cabin Bed',
                'price_per_night' => 18500.00,
                'total_rooms' => 10,
                'breakfast_included' => true,
                'free_cancellation' => true,
                'facilities' => ['River View', 'Attached Bath', 'AC', 'All Meals Included'],
            ]
        );

        // Demo Home Stay / Long Stays
        $p4 = Property::firstOrCreate(
            ['slug' => 'sajek-valley-eco-homestay-cottage'],
            [
                'vendor_id' => $vendor->id,
                'location_id' => $dhaka->id,
                'name' => 'Sajek Valley Eco Home Stay & Cottage',
                'type' => 'Homestay / Villa',
                'star_rating' => 4,
                'rating_score' => 9.1,
                'total_reviews' => 310,
                'address' => 'Konglak Para, Sajek Valley, Rangamati',
                'description' => 'Authentic wooden cottage home stay sitting right on top of Sajek Valley clouds with private balcony and local organic food.',
                'price_per_night' => 6500.00,
                'original_price' => 8500.00,
                'primary_image' => 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=800&q=80',
                ],
                'amenities' => ['Cloud View Balcony', 'Local BBQ', 'WiFi', 'Breakfast Included'],
                'is_featured' => true,
                'status' => 'published',
            ]
        );

        Room::firstOrCreate(
            ['property_id' => $p4->id, 'name' => 'Cloud View Wooden Cottage Room'],
            [
                'max_guests' => 3,
                'max_adults' => 2,
                'max_children' => 1,
                'room_size_sqm' => 30,
                'bed_type' => '1 Queen Bed + Sofa Bed',
                'price_per_night' => 6500.00,
                'total_rooms' => 5,
                'breakfast_included' => true,
                'free_cancellation' => true,
                'facilities' => ['Mountain View', 'Private Balcony', 'BBQ Spot'],
            ]
        );
    }
}

