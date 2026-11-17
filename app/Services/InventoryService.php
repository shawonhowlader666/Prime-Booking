<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\RoomHold;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Room inventory: availability, checkout holds and cache invalidation.
 *
 * Availability for a night = capacity for that night
 *                            - rooms in active bookings covering the night
 *                            - rooms in unexpired checkout holds (other sessions).
 *
 * Everything is read straight from the database. Results are never cached,
 * because a stale "available" answer is how double bookings happen.
 */
class InventoryService
{
    /** Minutes an online-payment booking may stay unpaid before its rooms are released. */
    public const PAYMENT_WINDOW_MINUTES = 30;

    /**
     * Check whether $requiredRooms units are free on every night of the stay.
     *
     * @param string|null $ignoreHoldToken The caller's own hold, which must not count against them.
     */
    public function checkAvailability(int $roomId, string $checkIn, string $checkOut, int $requiredRooms = 1, ?string $ignoreHoldToken = null): array
    {
        try {
            $startDate = Carbon::parse($checkIn)->startOfDay();
            $endDate   = Carbon::parse($checkOut)->startOfDay();
        } catch (\Throwable $e) {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'Invalid date range.'];
        }

        if ($endDate->lte($startDate)) {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'Invalid date range.'];
        }

        $room = Room::find($roomId);
        if (!$room) {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'Room not found.'];
        }

        if (($room->status ?? 'active') !== 'active') {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'This room is not currently on sale.'];
        }

        $requiredRooms = max(1, $requiredRooms);
        $breakdown     = $this->nightlyAvailability($room, $startDate, $endDate, $ignoreHoldToken);
        $minAvailable  = $breakdown === [] ? 0 : min($breakdown);

        foreach ($breakdown as $dateStr => $units) {
            if ($units < $requiredRooms) {
                return [
                    'is_available'   => false,
                    'min_available'  => $minAvailable,
                    'sold_out_date'  => $dateStr,
                    'date_breakdown' => $breakdown,
                    'reason'         => $units <= 0
                        ? 'Sold out on ' . Carbon::parse($dateStr)->format('M d, Y') . '.'
                        : "Only {$units} room(s) left on " . Carbon::parse($dateStr)->format('M d, Y') . '.',
                ];
            }
        }

        return [
            'is_available'   => true,
            'min_available'  => $minAvailable,
            'date_breakdown' => $breakdown,
        ];
    }

    /**
     * Free units per night for [start, end). Three queries total, regardless of stay length.
     *
     * @return array<string,int> date (Y-m-d) => free units
     */
    public function nightlyAvailability(Room $room, Carbon $startDate, Carbon $endDate, ?string $ignoreHoldToken = null): array
    {
        $from = $startDate->format('Y-m-d');
        $to   = $endDate->format('Y-m-d');

        $overrides = RoomAvailability::where('room_id', $room->id)
            ->where('date', '>=', $from)
            ->where('date', '<', $to)
            ->get(['date', 'available_qty', 'is_blocked']);

        $bookings = $this->activeBookingsQuery($room->id, $from, $to)
            ->get(['check_in', 'check_out', 'rooms_count']);

        $holds = RoomHold::where('room_id', $room->id)
            ->where('expires_at', '>', now())
            ->where('check_in', '<', $to)
            ->where('check_out', '>', $from)
            ->when($ignoreHoldToken, fn ($q) => $q->where('token', '!=', $ignoreHoldToken))
            ->get(['check_in', 'check_out', 'rooms_count']);

        return $this->freeUnitsByNight((int) ($room->total_rooms ?? 1), $overrides, $bookings, $holds, $startDate, $endDate);
    }

    /**
     * Fewest free units on any night of [start, end) for many rooms at once — three queries
     * in total however many rooms are passed. Used by search to flag sold-out hotels.
     *
     * @param iterable<Room> $rooms
     * @return array<int,int> room id => fewest free units on any night (0 = sold out / not on sale)
     */
    public function minFreeUnitsForRooms(iterable $rooms, Carbon $startDate, Carbon $endDate): array
    {
        $rooms = collect($rooms)->keyBy('id');
        if ($rooms->isEmpty() || $endDate->lte($startDate)) {
            return [];
        }

        $ids  = $rooms->keys()->map(fn ($id) => (int) $id)->all();
        $from = $startDate->format('Y-m-d');
        $to   = $endDate->format('Y-m-d');

        $overrides = RoomAvailability::whereIn('room_id', $ids)
            ->where('date', '>=', $from)
            ->where('date', '<', $to)
            ->get(['room_id', 'date', 'available_qty', 'is_blocked'])
            ->groupBy('room_id');

        $bookings = $this->overlappingBookings(Booking::whereIn('room_id', $ids), $from, $to)
            ->get(['room_id', 'check_in', 'check_out', 'rooms_count'])
            ->groupBy('room_id');

        $holds = RoomHold::whereIn('room_id', $ids)
            ->where('expires_at', '>', now())
            ->where('check_in', '<', $to)
            ->where('check_out', '>', $from)
            ->get(['room_id', 'check_in', 'check_out', 'rooms_count'])
            ->groupBy('room_id');

        $result = [];
        foreach ($rooms as $id => $room) {
            if (($room->status ?? 'active') !== 'active') {
                $result[(int) $id] = 0;
                continue;
            }
            $nights = $this->freeUnitsByNight(
                (int) ($room->total_rooms ?? 1),
                $overrides->get($id, collect()),
                $bookings->get($id, collect()),
                $holds->get($id, collect()),
                $startDate,
                $endDate
            );
            $result[(int) $id] = $nights === [] ? 0 : min($nights);
        }

        return $result;
    }

    /**
     * @param iterable<object> $overrides rows with date, available_qty, is_blocked
     * @param iterable<object> $bookings  rows with check_in, check_out, rooms_count
     * @param iterable<object> $holds     rows with check_in, check_out, rooms_count
     * @return array<string,int>
     */
    private function freeUnitsByNight(int $capacity, iterable $overrides, iterable $bookings, iterable $holds, Carbon $startDate, Carbon $endDate): array
    {
        $capacity = max(0, $capacity);
        $byDate = collect($overrides)->keyBy(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'));

        $occupying = [];
        foreach ([$bookings, $holds] as $set) {
            foreach ($set as $row) {
                $occupying[] = [
                    Carbon::parse($row->check_in)->format('Y-m-d'),
                    Carbon::parse($row->check_out)->format('Y-m-d'),
                    max(1, (int) $row->rooms_count),
                ];
            }
        }

        $result = [];
        foreach (CarbonPeriod::create($startDate, $endDate->copy()->subDay()) as $date) {
            $dateStr  = $date->format('Y-m-d');
            $override = $byDate->get($dateStr);

            if ($override && $override->is_blocked) {
                $result[$dateStr] = 0;
                continue;
            }

            $base = $override ? min($capacity, max(0, (int) $override->available_qty)) : $capacity;

            $taken = 0;
            foreach ($occupying as [$in, $out, $count]) {
                if ($in <= $dateStr && $out > $dateStr) {
                    $taken += $count;
                }
            }

            $result[$dateStr] = max(0, $base - $taken);
        }

        return $result;
    }

    /**
     * Bookings that occupy inventory for the window [from, to).
     * Unpaid online bookings stop counting once their payment window has passed.
     */
    public function activeBookingsQuery(int $roomId, string $from, string $to)
    {
        return $this->overlappingBookings(Booking::where('room_id', $roomId), $from, $to);
    }

    private function overlappingBookings($query, string $from, string $to)
    {
        return $query
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $to)
            ->where('check_out', '>', $from)
            ->where(function ($q) {
                $q->where('status', 'confirmed')
                  ->orWhere('payment_status', '!=', 'pending')
                  ->orWhereNull('payment_expires_at')
                  ->orWhere('payment_expires_at', '>', now());
            });
    }

    /**
     * Place (or refresh) a checkout hold. Returns false when the rooms are not free.
     */
    public function holdRoom(int $roomId, string $checkIn, string $checkOut, string $holdToken, int $minutes = 15, int $roomsCount = 1): bool
    {
        try {
            $start = Carbon::parse($checkIn)->startOfDay();
            $end   = Carbon::parse($checkOut)->startOfDay();
        } catch (\Throwable $e) {
            return false;
        }

        if ($end->lte($start) || $holdToken === '') {
            return false;
        }

        $roomsCount = max(1, $roomsCount);
        $existing   = RoomHold::where('token', $holdToken)->first();

        // Same session, same selection: keep the original expiry so the timer cannot be extended by refreshing.
        if ($existing
            && (int) $existing->room_id === $roomId
            && $existing->check_in->equalTo($start)
            && $existing->check_out->equalTo($end)
            && (int) $existing->rooms_count === $roomsCount
            && $existing->expires_at->isFuture()) {
            return true;
        }

        if (Room::whereKey($roomId)->exists()) {
            $check = $this->checkAvailability($roomId, $start->toDateString(), $end->toDateString(), $roomsCount, $holdToken);
            if (!$check['is_available']) {
                $existing?->delete();
                return false;
            }
        }

        RoomHold::updateOrCreate(
            ['token' => $holdToken],
            [
                'room_id'     => $roomId,
                'check_in'    => $start->toDateString(),
                'check_out'   => $end->toDateString(),
                'rooms_count' => $roomsCount,
                'expires_at'  => now()->addMinutes(max(1, $minutes)),
            ]
        );

        return true;
    }

    /** Seconds left on a hold, 0 when it does not exist or has expired. */
    public function holdSecondsRemaining(string $holdToken): int
    {
        $hold = RoomHold::where('token', $holdToken)->first();
        if (!$hold || $hold->expires_at->isPast()) {
            return 0;
        }

        return (int) max(0, now()->diffInSeconds($hold->expires_at, false));
    }

    public function releaseHold(string $holdToken): void
    {
        if ($holdToken !== '') {
            RoomHold::where('token', $holdToken)->delete();
        }
    }

    /** Remove expired holds. Returns the number of rows deleted. */
    public function pruneExpiredHolds(): int
    {
        return RoomHold::where('expires_at', '<=', now())->delete();
    }

    /**
     * Called after a booking row is written. Inventory is derived from booking rows,
     * so this only has to drop caches that depend on availability.
     */
    public function lockAndDeduct(Booking $booking): void
    {
        $this->flushAvailabilityCaches($booking);
        Log::info("Inventory locked for booking #{$booking->booking_reference} (Room #{$booking->room_id}).");
    }

    /** Called after a booking is cancelled or expires. */
    public function releaseInventory(Booking $booking): void
    {
        $this->flushAvailabilityCaches($booking);
        Log::info("Inventory released for booking #{$booking->booking_reference} (Room #{$booking->room_id}).");
    }

    private function flushAvailabilityCaches(Booking $booking): void
    {
        if (!$booking->room_id || !$booking->check_in || !$booking->check_out) {
            return;
        }

        $start = Carbon::parse($booking->check_in)->startOfDay();
        $end   = Carbon::parse($booking->check_out)->startOfDay();
        if ($end->lte($start)) {
            return;
        }

        foreach (CarbonPeriod::create($start, $end->copy()->subDay()) as $date) {
            Cache::forget("property_availability_{$booking->property_id}_{$date->format('Y-m-d')}");
        }
    }
}
