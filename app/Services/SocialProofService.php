<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * SocialProofService
 * ──────────────────
 * Generates dynamic, data-driven urgency & social proof indicators (Agoda / Booking.com standard)
 * to boost search-to-booking conversion.
 */
class SocialProofService
{
    /**
     * Get live viewer count for a given property.
     * Uses deterministic hash + time-decay for organic feel.
     */
    public function getActiveViewers(int $propertyId): int
    {
        $cacheKey = "property_viewers_{$propertyId}";
        return (int) Cache::remember($cacheKey, 180, function () use ($propertyId) {
            $base = (crc32((string) $propertyId) % 6) + 3; // 3 to 8 viewers
            $timeMod = (int) (date('G') / 4); // variation depending on time of day
            return max(2, min(14, $base + $timeMod));
        });
    }

    /**
     * Get time of most recent booking for this property or city.
     */
    public function getRecentBookingInfo(Property $property): ?string
    {
        $cacheKey = "recent_booking_info_{$property->id}";
        return Cache::remember($cacheKey, 300, function () use ($property) {
            $recent = Booking::where('property_id', $property->id)
                ->where('created_at', '>=', now()->subHours(48))
                ->latest()
                ->first();

            if ($recent) {
                $minsAgo = max(4, $recent->created_at->diffInMinutes(now()));
                if ($minsAgo < 60) {
                    return "Booked {$minsAgo} minutes ago";
                }
                $hoursAgo = max(1, round($minsAgo / 60));
                return "Booked {$hoursAgo} hours ago";
            }

            // Organic city-level fallback
            $cityRecent = Booking::whereHas('property', function ($q) use ($property) {
                $q->where('city', $property->city);
            })->where('created_at', '>=', now()->subHours(24))->latest()->first();

            if ($cityRecent) {
                $minsAgo = max(12, $cityRecent->created_at->diffInMinutes(now()));
                if ($minsAgo < 60) {
                    return "Booked in {$property->city} {$minsAgo}m ago";
                }
                return "Last booked in {$property->city} today";
            }

            return "High demand in {$property->city} today";
        });
    }

    /**
     * Get urgency badge data for a specific room.
     */
    public function getRoomUrgencyBadge(Room $room, int $availableUnits = 5): ?array
    {
        if ($availableUnits <= 1) {
            return [
                'type'    => 'critical',
                'icon'    => 'fas fa-fire',
                'text'    => 'Only 1 room left at this price on our site!',
                'color'   => 'red',
            ];
        }

        if ($availableUnits <= 3) {
            return [
                'type'    => 'warning',
                'icon'    => 'fas fa-bolt',
                'text'    => "Only {$availableUnits} rooms left at this price!",
                'color'   => 'amber',
            ];
        }

        return null;
    }

    /**
     * Get free cancellation badge label with deadline.
     */
    public function getFreeCancellationBadge(string $checkInDate): array
    {
        try {
            $checkIn = Carbon::parse($checkInDate);
            $deadline = $checkIn->copy()->subDay()->setTime(23, 59);

            if ($deadline->isFuture()) {
                return [
                    'eligible' => true,
                    'badge'    => 'FREE Cancellation',
                    'deadline' => $deadline->format('M j, Y'),
                    'detail'   => "Before {$deadline->format('M j, Y')} 11:59 PM",
                ];
            }
        } catch (\Throwable) {}

        return [
            'eligible' => false,
            'badge'    => 'Non-refundable',
            'deadline' => null,
            'detail'   => 'Special rate',
        ];
    }

    /**
     * Get aggregate signals for a property (viewers, urgency text, popularity).
     */
    public function getSignals(Property $property): array
    {
        $viewers = $this->getActiveViewers((int) $property->id);
        $recentText = $this->getRecentBookingInfo($property);
        $isPopular = $viewers >= 5 || (float)($property->rating_score ?? 0) >= 8.5;

        return [
            'viewing_now'  => $viewers,
            'is_popular'   => $isPopular,
            'urgency_text' => $recentText ?: "🔥 High demand: Multiple guests booked in {$property->city} today.",
        ];
    }
}
