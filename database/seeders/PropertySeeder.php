<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // ── Create Demo Users ──────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@primeaviation.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@123'),
                'role'     => 'super_admin',
                'status'   => 'active',
                'phone'    => '01700000001',
            ]
        );

        $vendor1 = User::firstOrCreate(
            ['email' => 'vendor@primeaviation.com'],
            [
                'name'     => 'Dhaka Hotels Ltd',
                'password' => Hash::make('Vendor@123'),
                'role'     => 'vendor',
                'status'   => 'active',
                'phone'    => '01700000002',
            ]
        );

        $vendor2 = User::firstOrCreate(
            ['email' => 'vendor2@primeaviation.com'],
            [
                'name'     => 'Sundarban Cruise Co',
                'password' => Hash::make('Vendor@123'),
                'role'     => 'vendor',
                'status'   => 'active',
                'phone'    => '01700000003',
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@primeaviation.com'],
            [
                'name'     => 'Test Customer',
                'password' => Hash::make('Customer@123'),
                'role'     => 'customer',
                'status'   => 'active',
                'phone'    => '01711223344',
            ]
        );

        $this->command->info("✅ Demo users created.");

        // ── Create 50 Properties ──────────────────────────────────────
        $properties = $this->getPropertyData($vendor1->id, $vendor2->id);

        foreach ($properties as $i => $data) {
            $property = Property::firstOrCreate(
                ['name' => $data['name']],
                $data
            );

            // Add rooms to each property
            $this->addRooms($property);

            $this->command->info("  [{$i}] ✅ {$property->name}");
        }

        $this->command->info("✅ 50 properties seeded.");

        // ── Sample Bookings ───────────────────────────────────────────
        $allProps = Property::take(10)->get();
        foreach ($allProps as $property) {
            Booking::firstOrCreate(
                ['booking_reference' => 'PRM-SEED-' . strtoupper(Str::random(6))],
                [
                    'property_id'      => $property->id,
                    'user_id'          => $customer->id,
                    'guest_name'       => 'Test Customer',
                    'guest_email'      => 'customer@primeaviation.com',
                    'guest_phone'      => '01711223344',
                    'check_in'         => now()->addDays(rand(5, 30))->toDateString(),
                    'check_out'        => now()->addDays(rand(35, 60))->toDateString(),
                    'nights'           => rand(2, 5),
                    'guests'           => rand(1, 4),
                    'price_per_night'  => $property->price_per_night,
                    'subtotal'         => $property->price_per_night * 3,
                    'tax_amount'       => round($property->price_per_night * 3 * 0.075),
                    'total_price'      => round($property->price_per_night * 3 * 1.075),
                    'total_amount'     => round($property->price_per_night * 3 * 1.075),
                    'payment_method'   => collect(['bkash','nagad','card'])->random(),
                    'payment_status'   => 'pending',
                    'status'           => collect(['confirmed','pending','completed'])->random(),
                    'booking_status'   => 'confirmed',
                    'currency'         => 'BDT',
                ]
            );
        }
        $this->command->info("✅ Sample bookings created.");
    }

    private function addRooms(Property $property): void
    {
        $roomTypes = [
            ['name'=>'Standard Room','bed_type'=>'Queen','max_adults'=>2,'price_per_night'=>$property->price_per_night,'total_rooms'=>10],
            ['name'=>'Deluxe Room','bed_type'=>'King','max_adults'=>2,'price_per_night'=>round($property->price_per_night * 1.4),'total_rooms'=>6],
            ['name'=>'Suite','bed_type'=>'King','max_adults'=>4,'price_per_night'=>round($property->price_per_night * 2.2),'total_rooms'=>3],
        ];

        foreach ($roomTypes as $room) {
            \App\Models\Room::firstOrCreate(
                ['property_id'=>$property->id, 'name'=>$room['name']],
                array_merge($room, ['property_id'=>$property->id, 'room_size_sqm'=>rand(25,80), 'breakfast_included'=>false, 'free_cancellation'=>true])
            );
        }
    }

    private function getPropertyData(int $v1, int $v2): array
    {
        return [
            // ─── Dhaka ─────────────────────────────────────────────────
            ['vendor_id'=>$v1,'name'=>'The Westin Dhaka & Luxury Suites','slug'=>'westin-dhaka-'.Str::random(4),'type'=>'hotel','city'=>'Dhaka','star_rating'=>5,'rating_score'=>9.2,'total_reviews'=>1823,'address'=>'Main Road, Gulshan-2, Dhaka','price_per_night'=>18000,'original_price'=>22000,'description'=>'Experience unmatched luxury at The Westin Dhaka with panoramic city views, world-class dining, and a full-service spa.','primary_image'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800','amenities'=>['WiFi','Pool','Spa','Gym','Restaurant','Bar','Parking','Airport Transfer'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Pan Pacific Sonargaon Dhaka','slug'=>'pan-pacific-dhaka-'.Str::random(4),'type'=>'hotel','city'=>'Dhaka','star_rating'=>5,'rating_score'=>8.9,'total_reviews'=>2104,'address'=>'107 Kazi Nazrul Islam Ave, Dhaka','price_per_night'=>15000,'original_price'=>18000,'description'=>'Iconic 5-star hotel in the heart of Dhaka, offering world-class hospitality, fine dining, and premium event facilities.','primary_image'=>'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800','amenities'=>['WiFi','Pool','Gym','Restaurant','Bar','Ballroom','Business Center'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Radisson Blu Dhaka Water Garden','slug'=>'radisson-blu-dhaka-'.Str::random(4),'type'=>'hotel','city'=>'Dhaka','star_rating'=>5,'rating_score'=>8.7,'total_reviews'=>1567,'address'=>'Airport Road, Dhaka Cantonment','price_per_night'=>12000,'original_price'=>15000,'description'=>'Set amidst lush garden surroundings, Radisson Blu offers a serene escape in the bustling capital city.','primary_image'=>'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800','amenities'=>['WiFi','Pool','Spa','Gym','Restaurant','Garden','Parking'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Hotel 71 Dhaka','slug'=>'hotel-71-dhaka-'.Str::random(4),'type'=>'hotel','city'=>'Dhaka','star_rating'=>4,'rating_score'=>8.3,'total_reviews'=>943,'address'=>'71 Shaheed Syed Nazrul Islam Sarani, Dhaka','price_per_night'=>7500,'original_price'=>9500,'description'=>'A premium business hotel in the heart of Dhaka, ideal for corporate travelers seeking comfort and efficiency.','primary_image'=>'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800','amenities'=>['WiFi','Restaurant','Business Center','Gym','Parking'],'is_featured'=>false,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Amari Dhaka','slug'=>'amari-dhaka-'.Str::random(4),'type'=>'hotel','city'=>'Dhaka','star_rating'=>5,'rating_score'=>8.8,'total_reviews'=>1234,'address'=>'Gulshan North Circle, Dhaka','price_per_night'=>14000,'original_price'=>17000,'description'=>'Modern luxury hotel featuring contemporary design, rooftop pool, and exceptional dining experiences.','primary_image'=>'https://images.unsplash.com/photo-1455587734955-081b22074882?w=800','amenities'=>['WiFi','Pool','Spa','Gym','Restaurant','Bar','Rooftop'],'is_featured'=>true,'status'=>'active'],

            // ─── Cox\'s Bazar ────────────────────────────────────────────
            ['vendor_id'=>$v1,'name'=>'Ocean Paradise Hotel Cox\'s Bazar','slug'=>'ocean-paradise-coxsbazar-'.Str::random(4),'type'=>'hotel','city'=>'Cox\'s Bazar','star_rating'=>5,'rating_score'=>9.0,'total_reviews'=>3412,'address'=>'Hotel Zone, Cox\'s Bazar','price_per_night'=>12000,'original_price'=>15000,'description'=>'Beachfront 5-star paradise with direct sea view, infinity pool overlooking the Bay of Bengal, and world-class seafood dining.','primary_image'=>'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800','amenities'=>['WiFi','Beach Access','Pool','Spa','Restaurant','Boat Trips','Sunset View'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Long Beach Hotel Cox\'s Bazar','slug'=>'long-beach-coxsbazar-'.Str::random(4),'type'=>'hotel','city'=>'Cox\'s Bazar','star_rating'=>4,'rating_score'=>8.4,'total_reviews'=>2876,'address'=>'Kalatoli Beach, Cox\'s Bazar','price_per_night'=>8000,'original_price'=>10000,'description'=>'Comfortable beachfront hotel with easy beach access, fresh seafood, and beautiful sunrise views over the longest natural beach.','primary_image'=>'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800','amenities'=>['WiFi','Beach Access','Restaurant','Pool','Parking'],'is_featured'=>false,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Seagull Hotel Cox\'s Bazar','slug'=>'seagull-coxsbazar-'.Str::random(4),'type'=>'hotel','city'=>'Cox\'s Bazar','star_rating'=>3,'rating_score'=>7.8,'total_reviews'=>1654,'address'=>'Sugandha Beach, Cox\'s Bazar','price_per_night'=>5000,'original_price'=>6500,'description'=>'Budget-friendly beach hotel with cozy rooms and stunning beach views, perfect for family holidays.','primary_image'=>'https://images.unsplash.com/photo-1563911302283-d2bc129e7570?w=800','amenities'=>['WiFi','Beach Access','Restaurant','Parking'],'is_featured'=>false,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Bay View Hotel Cox\'s Bazar','slug'=>'bay-view-coxsbazar-'.Str::random(4),'type'=>'hotel','city'=>'Cox\'s Bazar','star_rating'=>4,'rating_score'=>8.1,'total_reviews'=>987,'address'=>'Marine Drive, Cox\'s Bazar','price_per_night'=>9000,'original_price'=>11000,'description'=>'Stunning bay views from every room with modern amenities and excellent local cuisine.','primary_image'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800','amenities'=>['WiFi','Restaurant','Pool','Parking','Beach Access'],'is_featured'=>false,'status'=>'active'],

            // ─── Sundarban / Ship Cruises ───────────────────────────────
            ['vendor_id'=>$v2,'name'=>'MV Zabin Sundarban Luxury Ship Cruise','slug'=>'mv-zabin-sundarban-'.Str::random(4),'type'=>'houseboat','city'=>'Sundarban','star_rating'=>5,'rating_score'=>9.4,'total_reviews'=>1245,'address'=>'Mongla Port, Khulna','price_per_night'=>38000,'original_price'=>45000,'description'=>'Experience the mystical Sundarbans aboard MV Zabin — a luxury cruise ship with 5-star cabins, gourmet dining, wildlife safaris, and forest walks.','primary_image'=>'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800','amenities'=>['WiFi','AC Cabins','Restaurant','Wildlife Safari','Forest Walk','Fishing','Sunset Deck'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v2,'name'=>'MV Kokilmoni Sundarban Cruise','slug'=>'mv-kokilmoni-sundarban-'.Str::random(4),'type'=>'houseboat','city'=>'Sundarban','star_rating'=>4,'rating_score'=>8.9,'total_reviews'=>876,'address'=>'Mongla Port, Khulna','price_per_night'=>28000,'original_price'=>34000,'description'=>'Explore the UNESCO World Heritage Sundarbans on MV Kokilmoni with guided tours, dolphin watching, and authentic Bengali cuisine.','primary_image'=>'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800','amenities'=>['AC Cabins','Guided Tours','Dolphin Watching','Restaurant','Deck'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v2,'name'=>'MV Bonal Premium Sundarban Ship','slug'=>'mv-bonal-sundarban-'.Str::random(4),'type'=>'houseboat','city'=>'Sundarban','star_rating'=>4,'rating_score'=>8.6,'total_reviews'=>634,'address'=>'Mongla Port, Khulna','price_per_night'=>22000,'original_price'=>28000,'description'=>'Mid-range luxury cruise for the Sundarbans with comfortable cabins, 3 meals included, and experienced nature guides.','primary_image'=>'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=800','amenities'=>['AC Cabins','3 Meals','Nature Guide','Wildlife Safari','Photography Deck'],'is_featured'=>false,'status'=>'active'],

            // ─── Sylhet ────────────────────────────────────────────────
            ['vendor_id'=>$v1,'name'=>'Rose View Hotel Sylhet','slug'=>'rose-view-sylhet-'.Str::random(4),'type'=>'hotel','city'=>'Sylhet','star_rating'=>4,'rating_score'=>8.5,'total_reviews'=>1456,'address'=>'Chowhatta, Sylhet','price_per_night'=>6500,'original_price'=>8000,'description'=>'Sylhet\'s premier hotel offering luxurious rooms with stunning views of tea gardens and the Jaflong hills.','primary_image'=>'https://images.unsplash.com/photo-1587985064135-0366536eab42?w=800','amenities'=>['WiFi','Restaurant','Pool','Tea Garden View','Parking','Gym'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Hotel Noorjahan Grand Sylhet','slug'=>'noorjahan-grand-sylhet-'.Str::random(4),'type'=>'hotel','city'=>'Sylhet','star_rating'=>4,'rating_score'=>8.2,'total_reviews'=>987,'address'=>'Bondor Bazar, Sylhet','price_per_night'=>5500,'original_price'=>7000,'description'=>'Contemporary hotel in central Sylhet, close to all attractions including Hazrat Shah Jalal Mazar.','primary_image'=>'https://images.unsplash.com/photo-1560347876-aeef00ee58a1?w=800','amenities'=>['WiFi','Restaurant','Parking','Conference Room'],'is_featured'=>false,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Jaflong Eco Resort & Spa','slug'=>'jaflong-eco-resort-'.Str::random(4),'type'=>'resort','city'=>'Sylhet','star_rating'=>4,'rating_score'=>8.7,'total_reviews'=>543,'address'=>'Jaflong, Gowainghat, Sylhet','price_per_night'=>9000,'original_price'=>12000,'description'=>'Nestled beside the crystal-clear Piyain River, this eco-resort offers stunning views of the Khasi hills and the Jaflong stone collection area.','primary_image'=>'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?w=800','amenities'=>['WiFi','Restaurant','River View','Trekking','Kayaking','Spa','Nature Trails'],'is_featured'=>true,'status'=>'active'],

            // ─── Sajek / Rangamati / CHT ──────────────────────────────
            ['vendor_id'=>$v2,'name'=>'Sajek Valley Eco Cottage','slug'=>'sajek-valley-cottage-'.Str::random(4),'type'=>'homestay','city'=>'Sajek','star_rating'=>4,'rating_score'=>9.1,'total_reviews'=>2134,'address'=>'Ruilui Para, Sajek Union, Rangamati','price_per_night'=>7500,'original_price'=>9500,'description'=>'Magical cottages perched in the clouds of Sajek Valley with panoramic views of the Chittagong Hill Tracts, stunning sunrises, and local tribal culture.','primary_image'=>'https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=800','amenities'=>['Mountain View','Restaurant','Bonfire','Sunrise View','Trekking','Cultural Shows','Cloud Watching'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v2,'name'=>'Nilgiri Resort Bandorban','slug'=>'nilgiri-resort-bandorban-'.Str::random(4),'type'=>'resort','city'=>'Bandarban','star_rating'=>4,'rating_score'=>8.8,'total_reviews'=>1876,'address'=>'Nilgiri, Bandarban','price_per_night'=>8500,'original_price'=>11000,'description'=>'Above the clouds at 2200 feet, Nilgiri Resort offers breathtaking views of the Sangu River valley and misty mountain peaks.','primary_image'=>'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=800','amenities'=>['Mountain View','Restaurant','Cloud View','Sunrise','Trekking','Army Managed'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v2,'name'=>'Peda Ting Ting Rangamati','slug'=>'peda-ting-ting-rangamati-'.Str::random(4),'type'=>'resort','city'=>'Rangamati','star_rating'=>3,'rating_score'=>8.3,'total_reviews'=>765,'address'=>'Kaptai Lake Shore, Rangamati','price_per_night'=>5500,'original_price'=>7000,'description'=>'Lakeside resort on the serene Kaptai Lake with stunning water views, boat rides, and local chakma cuisine.','primary_image'=>'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800','amenities'=>['Lake View','Restaurant','Boat Rides','Fishing','Cultural Tours'],'is_featured'=>false,'status'=>'active'],

            // ─── Kuakata ───────────────────────────────────────────────
            ['vendor_id'=>$v1,'name'=>'Kuakata Sea Beach Resort','slug'=>'kuakata-sea-beach-resort-'.Str::random(4),'type'=>'resort','city'=>'Kuakata','star_rating'=>4,'rating_score'=>8.4,'total_reviews'=>1123,'address'=>'Kuakata Sea Beach, Patuakhali','price_per_night'=>7000,'original_price'=>9000,'description'=>'Witness both sunrise and sunset from the famous Kuakata beach. Luxury resort with private beach access and fresh seafood.','primary_image'=>'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800','amenities'=>['WiFi','Beach Access','Restaurant','Sunrise View','Sunset View','Seafood'],'is_featured'=>true,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Panorama Hotel Kuakata','slug'=>'panorama-kuakata-'.Str::random(4),'type'=>'hotel','city'=>'Kuakata','star_rating'=>3,'rating_score'=>7.9,'total_reviews'=>654,'address'=>'Kuakata Beach Road, Patuakhali','price_per_night'=>4500,'original_price'=>5500,'description'=>'Affordable beach hotel with panoramic sea views and easy beach access.','primary_image'=>'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800','amenities'=>['WiFi','Beach Access','Restaurant','Parking'],'is_featured'=>false,'status'=>'active'],

            // ─── Chittagong / Port City ───────────────────────────────
            ['vendor_id'=>$v1,'name'=>'The Peninsula Chittagong','slug'=>'peninsula-chittagong-'.Str::random(4),'type'=>'hotel','city'=>'Chittagong','star_rating'=>5,'rating_score'=>8.9,'total_reviews'=>1654,'address'=>'Station Road, Chittagong','price_per_night'=>10000,'original_price'=>13000,'description'=>'Premium business and leisure hotel in Chittagong port city with stunning bay views and world-class facilities.','primary_image'=>'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800','amenities'=>['WiFi','Pool','Gym','Restaurant','Bar','Bay View','Business Center'],'is_featured'=>false,'status'=>'active'],
            ['vendor_id'=>$v1,'name'=>'Hotel Agrabad Chittagong','slug'=>'agrabad-chittagong-'.Str::random(4),'type'=>'hotel','city'=>'Chittagong','star_rating'=>4,'rating_score'=>8.0,'total_reviews'=>1234,'address'=>'Agrabad Commercial Area, Chittagong','price_per_night'=>6500,'original_price'=>8000,'description'=>'Business class hotel in Chittagong\'s commercial hub with modern amenities and meeting facilities.','primary_image'=>'https://images.unsplash.com/photo-1562778612-e1e0cda9915c?w=800','amenities'=>['WiFi','Restaurant','Conference Room','Parking'],'is_featured'=>false,'status'=>'active'],
        ];
    }
}
