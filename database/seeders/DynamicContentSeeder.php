<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourPackage;
use App\Models\Deal;
use App\Models\CmsContent;
use App\Models\Amenity;

class DynamicContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tour Packages
        if (TourPackage::count() === 0) {
            $packages = [
                [
                    'title' => 'Bangkok & Phuket Fantasy',
                    'days' => '5D/4N',
                    'price' => 45000,
                    'badge' => 'Popular',
                    'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Flight', '4 Star Hotel', 'Breakfast', 'City Tour'],
                    'description' => 'Experience the thrilling nightlife of Bangkok and pristine beaches of Phuket with flight and 4-star hotel stay included.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Dubai Luxury Holiday',
                    'days' => '6D/5N',
                    'price' => 65000,
                    'badge' => 'Best Seller',
                    'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Visa', 'Flight', 'Desert Safari', 'Dhow Cruise'],
                    'description' => 'Explore Burj Khalifa, Desert Safari with BBQ dinner, and luxury shopping in Dubai.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Premium Umrah Package',
                    'days' => '14 Days',
                    'price' => 155000,
                    'badge' => 'Spiritual',
                    'image_url' => 'https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Visa', 'Direct Flight', 'Near Haram Hotel', 'Ziyarah'],
                    'description' => 'Fulfill your spiritual journey with 5-star hotels steps away from Masjid al-Haram and Al-Masjid an-Nabawi.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Sundarbans Eco Wildlife Adventure',
                    'days' => '3D/2N',
                    'price' => 12500,
                    'badge' => 'Domestic Special',
                    'image_url' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Cruiser Vessel', 'All Meals', 'Forest Guard', 'Guide'],
                    'description' => 'Cruise deep into the mangrove forests, spot Royal Bengal Tigers and exotic birds with expert guides.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Malaysia & Singapore Combo',
                    'days' => '7D/6N',
                    'price' => 78000,
                    'badge' => 'Family Package',
                    'image_url' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Visa Assistance', 'Flight', 'Hotels', 'Cable Car'],
                    'description' => 'Visit Petronas Twin Towers, Genting Highlands, Universal Studios Singapore and Sentosa Island.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 5,
                ],
                [
                    'title' => 'Maldives Resort Escape',
                    'days' => '4D/3N',
                    'price' => 85000,
                    'badge' => 'Honeymoon',
                    'image_url' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=800&q=80',
                    'includes' => ['Speedboat Transfer', 'Water Villa', 'All Meals', 'Snorkeling'],
                    'description' => 'Stay in an overwater villa in the crystal turquoise lagoons of the Maldives.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 6,
                ],
            ];

            foreach ($packages as $pkg) {
                TourPackage::create($pkg);
            }
        }

        // 2. Deals
        if (Deal::count() === 0) {
            $deals = [
                [
                    'title' => 'Flash Sale: Cox\'s Bazar Luxury Resorts',
                    'subtitle' => 'Exclusive beach resort discount for Prime members',
                    'discount_pct' => 35,
                    'original_price' => 12000,
                    'sale_price' => 7800,
                    'valid_until' => now()->addDays(7),
                    'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                    'badge_text' => '35% OFF',
                    'link_url' => '/search?destination=Cox%27s+Bazar',
                    'type' => 'hotel',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Sylhet Tea Garden Eco Stay Special',
                    'subtitle' => 'Weekend getaway discount in green hills',
                    'discount_pct' => 25,
                    'original_price' => 8500,
                    'sale_price' => 6375,
                    'valid_until' => now()->addDays(12),
                    'image_url' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=800&q=80',
                    'badge_text' => 'SAVE 25%',
                    'link_url' => '/search?destination=Sylhet',
                    'type' => 'hotel',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Dhaka 5-Star City Break',
                    'subtitle' => 'Includes luxury breakfast & late checkout',
                    'discount_pct' => 20,
                    'original_price' => 18000,
                    'sale_price' => 14400,
                    'valid_until' => now()->addDays(5),
                    'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'badge_text' => '20% OFF',
                    'link_url' => '/search?destination=Dhaka',
                    'type' => 'hotel',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
            ];

            foreach ($deals as $dl) {
                Deal::create($dl);
            }
        }

        // 3. CMS Contents
        $cmsPages = [
            [
                'key' => 'about',
                'title' => 'About Prime Booking',
                'group' => 'pages',
                'content' => 'Prime Booking is Bangladesh\'s premier hotel, flight, and tour booking platform. We connect millions of travelers with thousands of verified hotels, luxury resorts, eco-lodges, and airlines worldwide.',
                'meta_data' => [
                    'mission' => 'To make travel accessible, seamless, and memorable for everyone.',
                    'vision' => 'To become South Asia\'s leading digital travel ecosystem.',
                    'founded' => '2024',
                    'happy_customers' => '500,000+',
                ],
            ],
            [
                'key' => 'services',
                'title' => 'Our Services',
                'group' => 'pages',
                'content' => 'We offer end-to-end travel solutions including hotel reservations, flight tickets, airport transfers, tour packages, and car rentals.',
                'meta_data' => [
                    'services_list' => [
                        ['icon' => 'fa-hotel', 'name' => 'Hotel & Resort Booking', 'desc' => 'Book from over 2,000,000+ verified properties with free cancellation.'],
                        ['icon' => 'fa-plane', 'name' => 'Flight Reservations', 'desc' => 'Compare and book domestic and international flights at guaranteed best fares.'],
                        ['icon' => 'fa-suitcase-rolling', 'name' => 'Custom Tour Packages', 'desc' => 'Handpicked holiday packages with guide, transfers, and activities.'],
                        ['icon' => 'fa-car', 'name' => 'Airport Transfers & Car Rental', 'desc' => 'Chauffeur driven cars and airport pickup services 24/7.'],
                    ],
                ],
            ],
            [
                'key' => 'contact_info',
                'title' => 'Contact Details',
                'group' => 'general',
                'content' => 'Reach out to our 24/7 customer support team anytime.',
                'meta_data' => [
                    'address' => 'Prime Tower, Level 12, Gulshan Avenue, Dhaka-1212, Bangladesh',
                    'email' => 'support@primebooking.com',
                    'phone' => '+880 9612-345678',
                    'hotline' => '16234',
                ],
            ],
        ];

        foreach ($cmsPages as $cms) {
            CmsContent::updateOrCreate(['key' => $cms['key']], $cms);
        }

        // 4. Amenities
        if (Amenity::count() === 0) {
            $amenities = [
                ['name' => 'Free High-Speed Wi-Fi', 'icon' => 'fa-wifi', 'category' => 'general'],
                ['name' => 'Swimming Pool', 'icon' => 'fa-water-ladder', 'category' => 'recreation'],
                ['name' => 'Free Breakfast', 'icon' => 'fa-utensils', 'category' => 'dining'],
                ['name' => 'Airport Shuttle', 'icon' => 'fa-van-shuttle', 'category' => 'services'],
                ['name' => 'Fitness Center / Gym', 'icon' => 'fa-dumbbell', 'category' => 'recreation'],
                ['name' => 'Air Conditioning', 'icon' => 'fa-snowflake', 'category' => 'general'],
                ['name' => 'Spa & Wellness Center', 'icon' => 'fa-spa', 'category' => 'recreation'],
                ['name' => '24-Hour Front Desk', 'icon' => 'fa-clock', 'category' => 'services'],
                ['name' => 'Free Parking', 'icon' => 'fa-square-parking', 'category' => 'general'],
                ['name' => 'Ocean / Sea View', 'icon' => 'fa-water', 'category' => 'general'],
            ];

            foreach ($amenities as $am) {
                Amenity::create($am);
            }
        }
    }
}
