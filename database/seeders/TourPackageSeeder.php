<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourPackage;
use Illuminate\Support\Str;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title' => "Cox's Bazar Beach & Marine Drive Luxury Holiday",
                'slug' => 'coxs-bazar-beach-marine-drive-luxury-holiday',
                'destination' => "Cox's Bazar",
                'duration_days' => 3,
                'duration_nights' => 2,
                'price_per_person' => 8500,
                'discount_price' => 10500,
                'featured_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'inclusions' => ['5-Star Resort Stay', 'AC Bus Transport', 'Buffet Breakfast', 'Sunset Marine Drive Tour', 'Tour Guide'],
                'highlights' => ['Explore 120km longest sea beach', 'Sunset view at Himchari Waterfall', 'Inani Beach Coral Tour', 'Sea Pearl Water Park'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival & Sunset Beach Walk', 'description' => 'Check in at 5-Star Beach Resort. Afternoon leisure walk at Kolatoli Beach. Watch golden sunset.'],
                    ['day' => 2, 'title' => 'Marine Drive & Inani Coral Excursion', 'description' => 'Morning drive along scenic Marine Drive. Visit Himchari national park and Inani coral beach.'],
                    ['day' => 3, 'title' => 'Shopping & Departure', 'description' => 'Morning Burmese Market shopping. Check out and return journey to Dhaka.'],
                ],
                'status' => 'active',
                'max_seats' => 25,
                'available_seats' => 18,
            ],
            [
                'title' => "Sylhet Tea Garden & Ratargul Swamp Forest Tour",
                'slug' => 'sylhet-tea-garden-ratargul-swamp-forest-tour',
                'destination' => "Sylhet",
                'duration_days' => 3,
                'duration_nights' => 2,
                'price_per_person' => 7800,
                'discount_price' => 9500,
                'featured_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
                'inclusions' => ['Eco Resort Stay', 'Boat Safari', 'Daily Meals', 'AC Microbus Transport', 'Local Guide'],
                'highlights' => ['Ratargul Freshwater Swamp Forest Boat Ride', 'Jaflong Crystal River & Stone Collection', 'Malnicherra Tea Estate Walk', 'Shah Jalal Shrine Visit'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Sylhet Arrival & Shrine Visit', 'description' => 'Arrive in Sylhet. Hotel check in. Visit Hazrat Shah Jalal & Shah Paran Mazar.'],
                    ['day' => 2, 'title' => 'Jaflong & Tamabil Border Tour', 'description' => 'Full day sightseeing to Jaflong stone collection river, Khasi village and waterfalls.'],
                    ['day' => 3, 'title' => 'Ratargul Forest & Tea Estate', 'description' => 'Morning wooden boat ride through Ratargul swamp forest canopy. Evening return.'],
                ],
                'status' => 'active',
                'max_seats' => 20,
                'available_seats' => 14,
            ],
            [
                'title' => "Sundarbans Mangrove Forest Wildlife Cruise",
                'slug' => 'sundarbans-mangrove-forest-wildlife-cruise',
                'destination' => "Sundarbans",
                'duration_days' => 4,
                'duration_nights' => 3,
                'price_per_person' => 14500,
                'discount_price' => 17500,
                'featured_image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
                'inclusions' => ['3-Star Cruise Cabin Stay', 'All 4 Days Meals & Snacks', 'Forest Permit Fees', 'Armed Forest Guard Protection', 'Small Boat Canal Safaris'],
                'highlights' => ['Spot Royal Bengal Tigers & Spotted Deer', 'Kotka Watchtower & Jamtola Beach Walk', 'Harbaria Eco Park Canopy Walk', 'Canal Cruising'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Khulna Boarding & Harbaria Entry', 'description' => 'Board luxury cruise ship at Khulna ghat. Cruise into UNESCO Sundarbans. Evening Harbaria trail.'],
                    ['day' => 2, 'title' => 'Kotka Wildlife Sanctuary & Jamtola Beach', 'description' => 'Early morning canal boat safari. Walk through Kotka jungle trail to Jamtola secluded beach.'],
                    ['day' => 3, 'title' => 'Kochikhali Jungle Trail & Dublar Char', 'description' => 'Explore Kochikhali river channels. Watch deer herds and saltwater crocodiles.'],
                    ['day' => 4, 'title' => 'Karamjal Crocodile Center & Return', 'description' => 'Visit Karamjal breeding center. Disembark at Khulna with unforgettable memories.'],
                ],
                'status' => 'active',
                'max_seats' => 30,
                'available_seats' => 22,
            ],
            [
                'title' => "Sajek Valley Cloud Kingdom & Alutila Cave Tour",
                'slug' => 'sajek-valley-cloud-kingdom-alutila-cave-tour',
                'destination' => "Sajek Valley",
                'duration_days' => 3,
                'duration_nights' => 2,
                'price_per_person' => 6900,
                'discount_price' => 8500,
                'featured_image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'inclusions' => ['Hilltop Resort Stay', 'Chander Gari (4x4 Jeep) Ride', 'All Meals', 'Army Escort Permission', 'Local Guide'],
                'highlights' => ['Floating clouds view from Konglak Hill', 'Sunrise & Sunset at Ruiluipara', 'Alutila Mysterious Cave Expedition', 'Risang Waterfall'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Khagrachari to Sajek Army Escort', 'description' => 'Arrive in Khagrachari. Take 4x4 Chander Gari with Army Escort up Sajek winding mountain road.'],
                    ['day' => 2, 'title' => 'Konglak Peak Summit & Tribal Culture', 'description' => 'Early morning cloud watching. Hike to Konglak Peak (highest point in Sajek). Tribal dinner.'],
                    ['day' => 3, 'title' => 'Alutila Cave & Return Journey', 'description' => 'Descend to Khagrachari. Torchlight walk through Alutila natural rock cave.'],
                ],
                'status' => 'active',
                'max_seats' => 16,
                'available_seats' => 10,
            ]
        ];

        foreach ($packages as $pkg) {
            TourPackage::updateOrCreate(
                ['slug' => $pkg['slug']],
                $pkg
            );
        }
    }
}
