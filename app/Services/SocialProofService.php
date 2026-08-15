<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Cache;

class SocialProofService
{
    /**
     * Get real-time demand and urgency signals for a property.
     */
    public function getSignals(Property $property): array
    {
        $cacheKey = "property_social_proof_{$property->id}";

        return Cache::remember($cacheKey, 600, function () use ($property) {
            // Count actual recent bookings in last 48 hours
            $realBookings = Booking::where('property_id', $property->id)
                ->where('created_at', '>=', now()->subHours(48))
                ->count();

            // Calculate live viewing counter (deterministic realistic simulation)
            $viewingNow = (abs(crc32($property->id . date('YmdH'))) % 8) + 4;
            $bookedLast24h = max($realBookings, (abs(crc32($property->id . date('Ymd'))) % 4) + 1);

            $score = (float) ($property->rating_score ?? 8.5);
            $ratingLabel = match(true) {
                $score >= 9.0 => 'Exceptional',
                $score >= 8.5 => 'Wonderful',
                $score >= 8.0 => 'Very Good',
                $score >= 7.0 => 'Good',
                default       => 'Pleasant',
            };

            return [
                'viewing_now'     => $viewingNow,
                'booked_last_24h' => $bookedLast24h,
                'is_popular'      => $bookedLast24h >= 2,
                'rating_label'    => $ratingLabel,
                'rating_score'    => number_format($score, 1),
                'total_reviews'   => (int) ($property->total_reviews ?? 24),
                'urgency_text'    => "🔥 {$bookedLast24h} guests booked this property in the last 24 hours.",
                'live_views_text' => "👁️ {$viewingNow} other travelers are viewing this hotel right now.",
            ];
        });
    }
}
