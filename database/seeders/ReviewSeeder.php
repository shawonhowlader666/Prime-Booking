<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Property;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'customer')->first() ?? User::first();
        $userId = $user?->id ?? 1;

        $reviewsData = [
            1 => [
                [
                    'guest_name'    => 'Tanvir Ahmed',
                    'rating'        => 5.0,
                    'comment'       => 'Breathtaking beachfront view! The infinity pool is sensational and the breakfast spread at Kasbah was extraordinary. Highly recommended for couples.',
                    'status'        => 'approved',
                    'helpful_count' => 18,
                    'vendor_reply'  => 'Thank you Tanvir for staying with us! We look forward to welcoming you back to Inani Beach soon.',
                    'replied_at'    => now()->subDays(2),
                    'created_at'    => now()->subDays(5),
                ],
                [
                    'guest_name'    => 'Farhana Yasmin',
                    'rating'        => 5.0,
                    'comment'       => 'One of the best luxury resorts in Bangladesh. Private beach access is serene and peaceful, far away from the crowded Kolatoli beach.',
                    'status'        => 'approved',
                    'helpful_count' => 14,
                    'vendor_reply'  => 'Dear Farhana, our team is thrilled you enjoyed the serene private beach experience.',
                    'replied_at'    => now()->subDays(6),
                    'created_at'    => now()->subDays(10),
                ],
                [
                    'guest_name'    => 'Mahmudul Hasan',
                    'rating'        => 4.0,
                    'comment'       => 'Excellent hospitality and spotless rooms. Check-in was swift. The dinner buffet was tasty though could have slightly more local seafood options.',
                    'status'        => 'approved',
                    'helpful_count' => 7,
                    'vendor_reply'  => null,
                    'replied_at'    => null,
                    'created_at'    => now()->subDays(15),
                ],
                [
                    'guest_name'    => 'Sadia Afrin',
                    'rating'        => 5.0,
                    'comment'       => 'Superb family vacation! Kids loved the water park and the room was spacious with premium amenities and ocean-view balcony.',
                    'status'        => 'approved',
                    'helpful_count' => 22,
                    'vendor_reply'  => 'Thank you Sadia! Delighted that your children had such a great time at our water park.',
                    'replied_at'    => now()->subDays(18),
                    'created_at'    => now()->subDays(20),
                ],
            ],
            2 => [
                [
                    'guest_name'    => 'Zubair Rahman',
                    'rating'        => 5.0,
                    'comment'       => 'Classic heritage 5-star in central Dhaka. Impeccable service, Oasis pool is lovely, and Cafe Bazar buffet is always top notch.',
                    'status'        => 'approved',
                    'helpful_count' => 25,
                    'vendor_reply'  => 'Thank you Zubair for your longstanding loyalty to Pan Pacific Sonargaon Dhaka.',
                    'replied_at'    => now()->subDays(3),
                    'created_at'    => now()->subDays(7),
                ],
                [
                    'guest_name'    => 'Nusrat Jahan',
                    'rating'        => 4.0,
                    'comment'       => 'Great business stay in Karwan Bazar. High-speed Wi-Fi, comfortable orthopedic beds, and very professional concierge team.',
                    'status'        => 'approved',
                    'helpful_count' => 11,
                    'vendor_reply'  => null,
                    'replied_at'    => null,
                    'created_at'    => now()->subDays(12),
                ],
                [
                    'guest_name'    => 'Rafiqul Islam',
                    'rating'        => 5.0,
                    'comment'       => 'Heritage elegance with modern comfort. The Pacific Club Lounge facilities and meeting spaces are by far the best in Dhaka.',
                    'status'        => 'approved',
                    'helpful_count' => 16,
                    'vendor_reply'  => 'Dear Rafiqul, we take pride in offering the finest executive hospitality in the capital.',
                    'replied_at'    => now()->subDays(14),
                    'created_at'    => now()->subDays(18),
                ],
            ],
            3 => [
                [
                    'guest_name'    => 'Anisur Rahman',
                    'rating'        => 5.0,
                    'comment'       => 'Unforgettable expedition through the mangrove forests of Sundarbans! Saw spotted deer, wild boars and dolphin pods. Cabin was air-conditioned and very clean.',
                    'status'        => 'approved',
                    'helpful_count' => 31,
                    'vendor_reply'  => 'Thank you Anisur! Preserving eco-friendly hospitality in the Sundarbans is our prime mission.',
                    'replied_at'    => now()->subDays(1),
                    'created_at'    => now()->subDays(4),
                ],
                [
                    'guest_name'    => 'Dr. Shireen Akter',
                    'rating'        => 5.0,
                    'comment'       => 'Expert tour guides with armed forest guards made us feel 100% safe. Delicious fresh hilsa and local crab dishes served every day on deck.',
                    'status'        => 'approved',
                    'helpful_count' => 19,
                    'vendor_reply'  => 'Dr. Shireen, thank you for cruising with MV Zabin. Happy that you relished the fresh seafood cuisine!',
                    'replied_at'    => now()->subDays(8),
                    'created_at'    => now()->subDays(11),
                ],
                [
                    'guest_name'    => 'Imtiaz Chowdhury',
                    'rating'        => 5.0,
                    'comment'       => 'World-class cruise ship experience in Bangladesh. Waking up to the morning mist over the Sundarban river is pure magic.',
                    'status'        => 'approved',
                    'helpful_count' => 27,
                    'vendor_reply'  => null,
                    'replied_at'    => null,
                    'created_at'    => now()->subDays(22),
                ],
            ],
            4 => [
                [
                    'guest_name'    => 'Kamrul Hasan',
                    'rating'        => 5.0,
                    'comment'       => 'Direct cloud-kissed balcony view from Helipad road! Watching the morning sea of clouds from the bed was surreal. Cozy wooden interior.',
                    'status'        => 'approved',
                    'helpful_count' => 36,
                    'vendor_reply'  => 'Thank you Kamrul! We love sharing the Sajek cloud view with our valued guests.',
                    'replied_at'    => now()->subDays(2),
                    'created_at'    => now()->subDays(5),
                ],
                [
                    'guest_name'    => 'Tanjila Haque',
                    'rating'        => 4.0,
                    'comment'       => 'Warm indigenous hospitality. Traditional bamboo chicken dinner was delicious and solar electricity was reliable throughout the night.',
                    'status'        => 'approved',
                    'helpful_count' => 15,
                    'vendor_reply'  => null,
                    'replied_at'    => null,
                    'created_at'    => now()->subDays(14),
                ],
                [
                    'guest_name'    => 'Mehedi Hasan',
                    'rating'        => 5.0,
                    'comment'       => 'Best eco-cottage experience in Sajek. Peaceful atmosphere, clean attached western washroom, and breathtaking sunrise views over the lush hills.',
                    'status'        => 'approved',
                    'helpful_count' => 24,
                    'vendor_reply'  => 'Mehedi bhai, thank you so much! Looking forward to having you back during winter.',
                    'replied_at'    => now()->subDays(19),
                    'created_at'    => now()->subDays(21),
                ],
            ],
        ];

        foreach ($reviewsData as $propId => $reviews) {
            $prop = Property::find($propId);
            if (!$prop) continue;

            foreach ($reviews as $r) {
                $createdAt = $r['created_at'];
                unset($r['created_at']);
                $rev = Review::firstOrNew([
                    'property_id' => $propId,
                    'guest_name'  => $r['guest_name'],
                ]);
                $rev->fill(array_merge($r, [
                    'property_id' => $propId,
                    'user_id'     => $userId,
                ]));
                $rev->created_at = $createdAt;
                $rev->save();
            }

            Review::syncPropertyRating($propId);
        }
    }
}

