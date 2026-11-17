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
 * Urgency and social-proof signals built only from real activity on the site.
 * When there is nothing real to say, the methods return null / zero and the
 * page shows nothing. Invented demand signals mislead guests and breach
 * consumer-protection rules, so there are no fallbacks here.
 */
class SocialProofService
{
    /** Viewers are counted in 5-minute buckets; the last three buckets form the "now" window. */
    private const VIEW_BUCKET_SECONDS = 300;
    private const VIEW_WINDOW_BUCKETS = 3;

    /**
     * Record that a browser session is looking at a property. Each session is counted once per bucket.
     */
    public function recordView(int $propertyId, string $sessionId): void
    {
        if ($propertyId <= 0 || $sessionId === '') {
            return;
        }

        $bucket = intdiv(time(), self::VIEW_BUCKET_SECONDS);
        $ttl    = self::VIEW_BUCKET_SECONDS * (self::VIEW_WINDOW_BUCKETS + 1);

        if (Cache::add("pv:{$propertyId}:{$bucket}:" . sha1($sessionId), 1, $ttl)) {
            Cache::add("pv:{$propertyId}:{$bucket}", 0, $ttl);
            Cache::increment("pv:{$propertyId}:{$bucket}");
        }
    }

    /**
     * Distinct sessions that viewed this property in the last ~15 minutes, excluding the caller.
     */
    public function getActiveViewers(int $propertyId, int $excludeSelf = 1): int
    {
        $bucket = intdiv(time(), self::VIEW_BUCKET_SECONDS);
        $total  = 0;
        for ($i = 0; $i < self::VIEW_WINDOW_BUCKETS; $i++) {
            $total += (int) Cache::get("pv:{$propertyId}:" . ($bucket - $i), 0);
        }

        return max(0, $total - $excludeSelf);
    }

    /**
     * Get time of most recent booking for this property or city.
     */
    public function getRecentBookingInfo(Property $property): ?string
    {
        $cacheKey = "recent_booking_info_{$property->id}";
        return Cache::remember($cacheKey, 300, function () use ($property) {
            $recent = Booking::where('property_id', $property->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', '>=', now()->subHours(48))
                ->latest()
                ->first();

            if ($recent) {
                $minsAgo = max(1, (int) $recent->created_at->diffInMinutes(now()));
                if ($minsAgo < 60) {
                    return "Booked {$minsAgo} minutes ago";
                }
                $hoursAgo = max(1, round($minsAgo / 60));
                return "Booked {$hoursAgo} hours ago";
            }

            // City-level signal from real bookings only.
            $cityCount = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', '>=', now()->subHours(24))
                ->whereIn('property_id', Property::active()->where('city', $property->city)->select('id'))
                ->count();

            return $cityCount > 0
                ? "{$cityCount} " . ($cityCount === 1 ? 'booking' : 'bookings') . " in {$property->city} in the last 24 hours"
                : null;
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
     * Recent confirmed bookings for the site-wide notification. Guests are never named.
     *
     * @return list<array{property:string, city:string, minutes_ago:int, url:string}>
     */
    public function recentBookingsFeed(int $limit = 5): array
    {
        return Cache::remember('social_proof_feed', 120, function () use ($limit) {
            return Booking::query()
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', '>=', now()->subHours(24))
                ->whereHas('property', fn ($q) => $q->active())
                ->with('property:id,name,city,slug')
                ->latest('id')
                ->limit($limit)
                ->get(['id', 'property_id', 'created_at'])
                ->map(fn ($b) => [
                    'property'    => (string) $b->property->name,
                    'city'        => (string) $b->property->city,
                    'minutes_ago' => max(1, (int) $b->created_at->diffInMinutes(now())),
                    'url'         => $b->property->slug ? route('hotel.show', $b->property->slug) : route('hotels.show', $b->property_id),
                ])
                ->values()
                ->all();
        });
    }

    /**
     * Get aggregate signals for a property (viewers, urgency text, popularity).
     */
    public function getSignals(Property $property): array
    {
        $viewers = $this->getActiveViewers((int) $property->id);
        $recentText = $this->getRecentBookingInfo($property);
        $isPopular = $viewers >= 5 || ((int) ($property->total_reviews ?? 0) >= 10 && (float) ($property->rating_score ?? 0) >= 8.5);

        return [
            'viewing_now'  => $viewers >= 2 ? $viewers : 0,
            'is_popular'   => $isPopular,
            'urgency_text' => $recentText,
        ];
    }
}
