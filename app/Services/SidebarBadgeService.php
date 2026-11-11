<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\Review;
use App\Models\Property;
use App\Models\Payout;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Enterprise Sidebar Notification Badge Service
 *
 * Implements High-Water Mark (Timestamp Cursor) tracking per user.
 * Badges only appear for new items, never show '0', and instantly
 * disappear when clicked or visited (optimistic UI + server-side persistence).
 */
class SidebarBadgeService
{
    /**
     * Retrieve high-water mark timestamp for a given user & menu key.
     */
    public static function getLastSeen(int $userId, string $key): Carbon
    {
        // 1. Session check for current request lifecycle
        $sessionTs = session("user_menu_seen_{$userId}_{$key}");
        if ($sessionTs) {
            return Carbon::parse($sessionTs);
        }

        // 2. Persistent Cache check (valid for 30 days)
        $cacheKey = "user_menu_seen_ts_{$userId}_{$key}";
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return Carbon::parse($cached);
        }

        // 3. Initial Baseline: 24 hours ago
        $baseline = Carbon::now()->subHours(24);
        Cache::put($cacheKey, $baseline->toDateTimeString(), 86400 * 30);
        session(["user_menu_seen_{$userId}_{$key}" => $baseline->toDateTimeString()]);
        return $baseline;
    }

    /**
     * Mark a specific menu as seen (high-water mark cursor = now).
     */
    public static function markAsSeen(int $userId, string $key): void
    {
        $now = Carbon::now();
        session(["user_menu_seen_{$userId}_{$key}" => $now->toDateTimeString()]);
        Cache::put("user_menu_seen_ts_{$userId}_{$key}", $now->toDateTimeString(), 86400 * 30);

        // Invalidate cached count arrays so next read calculates freshly
        Cache::forget("prime_admin_sidebar_counts_{$userId}");
        Cache::forget("prime_vendor_sidebar_counts_{$userId}");
    }

    /**
     * Automatically detect the current route/URL and mark that menu as seen.
     */
    public static function autoMarkCurrentRoute(?int $userId = null): ?string
    {
        $userId = $userId ?: (auth()->id() ? (int)auth()->id() : 1);
        $route = request()->route() ? (string)request()->route()->getName() : '';
        $path = trim(request()->path(), '/');

        $matchedKey = null;

        if (str_contains($route, 'bookings') || str_contains($path, 'bookings')) {
            $matchedKey = 'bookings';
        } elseif (str_contains($route, 'inquiries') || str_contains($path, 'inquiries')) {
            $matchedKey = 'inquiries';
        } elseif (str_contains($route, 'reviews') || str_contains($path, 'reviews')) {
            $matchedKey = 'reviews';
        } elseif (str_contains($route, 'properties') || str_contains($path, 'properties')) {
            $matchedKey = 'properties';
        } elseif (str_contains($route, 'payouts') || str_contains($path, 'payouts')) {
            $matchedKey = 'payouts';
        } elseif (str_contains($route, 'support') || str_contains($path, 'support')) {
            $matchedKey = 'tickets';
        } elseif (str_contains($route, 'notifications') || str_contains($path, 'notifications')) {
            $matchedKey = 'notifications';
        }

        if ($matchedKey) {
            self::markAsSeen($userId, $matchedKey);
        }

        return $matchedKey;
    }

    /**
     * Retrieve aggregated unread counts for admin sidebar.
     *
     * @param int|null $userId
     * @param bool $fresh
     * @return array<string, int>
     */
    public static function getAdminCounts(?int $userId = null, bool $fresh = false): array
    {
        $userId = $userId ?: (auth()->id() ? (int)auth()->id() : 1);
        $cacheKey = "prime_admin_sidebar_counts_{$userId}";

        if (!$fresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 1. Bookings (pending & created after last seen)
        try {
            $sinceBookings = self::getLastSeen($userId, 'bookings');
            $bookings = Booking::where(function($q) {
                $q->whereIn('status', ['pending', 'new', 'unconfirmed'])
                  ->orWhereIn('booking_status', ['pending', 'new', 'unconfirmed']);
            })->where('created_at', '>', $sinceBookings)->count();
        } catch (\Throwable $e) {
            $bookings = 0;
        }

        // 2. Inquiries (unreplied/pending & created after last seen)
        try {
            $sinceInquiries = self::getLastSeen($userId, 'inquiries');
            $inquiries = Inquiry::where(function($q) {
                $q->whereNull('replied_at')
                  ->orWhereIn('status', ['pending', 'unread', 'new']);
            })->where('created_at', '>', $sinceInquiries)->count();
        } catch (\Throwable $e) {
            $inquiries = 0;
        }

        // 3. Reviews (pending moderation & created after last seen)
        try {
            $sinceReviews = self::getLastSeen($userId, 'reviews');
            $reviews = Review::where('status', 'pending')
                ->where('created_at', '>', $sinceReviews)
                ->count();
        } catch (\Throwable $e) {
            $reviews = 0;
        }

        // 4. Pending Properties (awaiting admin approval)
        try {
            $sinceProps = self::getLastSeen($userId, 'properties');
            $pendingProperties = Property::where('status', 'pending')
                ->where('created_at', '>', $sinceProps)
                ->count();
        } catch (\Throwable $e) {
            $pendingProperties = 0;
        }

        // 5. Pending Payouts (awaiting finance approval)
        try {
            $sincePayouts = self::getLastSeen($userId, 'payouts');
            $pendingPayouts = Payout::where('status', 'pending')
                ->where('created_at', '>', $sincePayouts)
                ->count();
        } catch (\Throwable $e) {
            $pendingPayouts = 0;
        }

        // 6. Support Tickets (open/pending & created after last seen)
        try {
            $sinceTickets = self::getLastSeen($userId, 'tickets');
            $openTickets = SupportTicket::whereIn('status', ['open', 'pending'])
                ->where('created_at', '>', $sinceTickets)
                ->count();
        } catch (\Throwable $e) {
            $openTickets = 0;
        }

        $counts = [
            'bookings'           => $bookings,
            'inquiries'          => $inquiries,
            'reviews'            => $reviews,
            'reservations_total' => $bookings + $inquiries + $reviews,
            'pending_properties' => $pendingProperties,
            'hotels_total'       => $pendingProperties,
            'pending_payouts'    => $pendingPayouts,
            'finance_total'      => $pendingPayouts,
            'open_tickets'       => $openTickets,
            'support_total'      => $openTickets + $inquiries,
            'total_alerts'       => $bookings + $inquiries + $reviews + $pendingProperties + $pendingPayouts + $openTickets,
        ];

        Cache::put($cacheKey, $counts, 15);

        return $counts;
    }

    /**
     * Retrieve aggregated unread counts for vendor sidebar.
     *
     * @param int|null $vendorId
     * @param bool $fresh
     * @return array<string, int>
     */
    public static function getVendorCounts(?int $vendorId = null, bool $fresh = false): array
    {
        $vendorId = $vendorId ?: (auth()->id() ? (int)auth()->id() : 1);
        $cacheKey = "prime_vendor_sidebar_counts_{$vendorId}";

        if (!$fresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $vendorPropIds = Property::where('vendor_id', $vendorId)->pluck('id')->toArray();
        } catch (\Throwable $e) {
            $vendorPropIds = [];
        }

        // 1. Bookings for vendor properties
        try {
            $sinceBookings = self::getLastSeen($vendorId, 'bookings');
            $bookings = Booking::whereIn('property_id', $vendorPropIds)
                ->where(function($q) {
                    $q->whereIn('status', ['pending', 'new', 'unconfirmed'])
                      ->orWhereIn('booking_status', ['pending', 'new', 'unconfirmed']);
                })
                ->where('created_at', '>', $sinceBookings)
                ->count();
        } catch (\Throwable $e) {
            $bookings = 0;
        }

        // 2. Inquiries for vendor properties
        try {
            $sinceInquiries = self::getLastSeen($vendorId, 'inquiries');
            $inquiries = Inquiry::where(function($q) use ($vendorId, $vendorPropIds) {
                $q->where('vendor_id', $vendorId)
                  ->orWhereIn('property_id', $vendorPropIds);
            })->where(function($q) {
                $q->whereNull('replied_at')
                  ->orWhereIn('status', ['pending', 'unread', 'new']);
            })
            ->where('created_at', '>', $sinceInquiries)
            ->count();
        } catch (\Throwable $e) {
            $inquiries = 0;
        }

        // 3. Reviews for vendor properties
        try {
            $sinceReviews = self::getLastSeen($vendorId, 'reviews');
            $reviews = Review::whereIn('property_id', $vendorPropIds)
                ->where('status', 'pending')
                ->where('created_at', '>', $sinceReviews)
                ->count();
        } catch (\Throwable $e) {
            $reviews = 0;
        }

        // 4. Pending Payouts for this vendor
        try {
            $sincePayouts = self::getLastSeen($vendorId, 'payouts');
            $pendingPayouts = Payout::where('vendor_id', $vendorId)
                ->where('status', 'pending')
                ->where('created_at', '>', $sincePayouts)
                ->count();
        } catch (\Throwable $e) {
            $pendingPayouts = 0;
        }

        // 5. Support Tickets for this vendor
        try {
            $sinceTickets = self::getLastSeen($vendorId, 'tickets');
            $openTickets = SupportTicket::where('user_id', $vendorId)
                ->whereIn('status', ['open', 'pending', 'in_progress'])
                ->where('created_at', '>', $sinceTickets)
                ->count();
        } catch (\Throwable $e) {
            $openTickets = 0;
        }

        $propsCount = count($vendorPropIds);

        $counts = [
            'properties'         => $propsCount,
            'bookings'           => $bookings,
            'inquiries'          => $inquiries,
            'reviews'            => $reviews,
            'reservations_total' => $bookings + $inquiries,
            'marketing_total'    => $reviews,
            'pending_payouts'    => $pendingPayouts,
            'finance_total'      => $pendingPayouts,
            'open_tickets'       => $openTickets,
            'support_total'      => $openTickets,
            'total_alerts'       => $bookings + $inquiries + $reviews + $pendingPayouts,
        ];

        Cache::put($cacheKey, $counts, 15);

        return $counts;
    }
}
