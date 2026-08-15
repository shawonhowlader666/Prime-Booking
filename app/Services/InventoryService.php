<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Check if a room has sufficient availability for all requested nights.
     * Uses cache for high-throughput reads.
     */
    public function checkAvailability(int $roomId, string $checkIn, string $checkOut, int $requiredRooms = 1): array
    {
        $startDate = Carbon::parse($checkIn)->startOfDay();
        $endDate   = Carbon::parse($checkOut)->startOfDay();

        if ($endDate->lte($startDate)) {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'Invalid date range.'];
        }

        $room = Room::find($roomId);
        if (!$room) {
            return ['is_available' => false, 'min_available' => 0, 'reason' => 'Room not found.'];
        }

        $totalCapacity = (int) ($room->total_rooms ?? 10);
        $period = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

        $minAvailable = $totalCapacity;
        $dateBreakdown = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $cacheKey = "room_avail_units_{$roomId}_{$dateStr}";

            $availableUnits = Cache::remember($cacheKey, 60, function () use ($roomId, $dateStr, $totalCapacity) {
                // Check custom vendor availability record
                $override = RoomAvailability::where('room_id', $roomId)
                    ->whereDate('date', $dateStr)
                    ->first();

                if ($override && ($override->is_blocked || $override->is_closed || $override->status === 'blocked')) {
                    return 0;
                }

                $baseCapacity = $override && isset($override->available_qty) ? (int)$override->available_qty : $totalCapacity;

                // Count active overlapping bookings
                $bookedUnits = Booking::where('room_id', $roomId)
                    ->where(function ($q) {
                        $q->where('status', 'confirmed')
                          ->orWhere('booking_status', 'confirmed')
                          ->orWhere('status', 'pending');
                    })
                    ->whereDate('check_in', '<=', $dateStr)
                    ->whereDate('check_out', '>', $dateStr)
                    ->count();

                return max(0, $baseCapacity - $bookedUnits);
            });

            $dateBreakdown[$dateStr] = $availableUnits;
            if ($availableUnits < $minAvailable) {
                $minAvailable = $availableUnits;
            }

            if ($minAvailable < $requiredRooms) {
                return [
                    'is_available'   => false,
                    'min_available'  => $minAvailable,
                    'sold_out_date'  => $dateStr,
                    'date_breakdown' => $dateBreakdown,
                    'reason'         => "Sold out on {$date->format('M d, Y')}.",
                ];
            }
        }

        return [
            'is_available'   => true,
            'min_available'  => $minAvailable,
            'date_breakdown' => $dateBreakdown,
        ];
    }

    /**
     * Atomically lock and deduct inventory during booking creation.
     */
    public function lockAndDeduct(Booking $booking): void
    {
        if (!$booking->room_id) {
            return;
        }

        $startDate = Carbon::parse($booking->check_in)->startOfDay();
        $endDate   = Carbon::parse($booking->check_out)->startOfDay();
        $period    = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            // Invalidate cache key
            Cache::forget("room_avail_units_{$booking->room_id}_{$dateStr}");
            Cache::forget("property_availability_{$booking->property_id}_{$dateStr}");
        }

        Log::info("Inventory locked for booking #{$booking->booking_reference} (Room #{$booking->room_id}) from {$booking->check_in} to {$booking->check_out}.");
    }

    /**
     * Atomically release inventory on booking cancellation.
     */
    public function releaseInventory(Booking $booking): void
    {
        if (!$booking->room_id) {
            return;
        }

        $startDate = Carbon::parse($booking->check_in)->startOfDay();
        $endDate   = Carbon::parse($booking->check_out)->startOfDay();
        $period    = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            Cache::forget("room_avail_units_{$booking->room_id}_{$dateStr}");
            Cache::forget("property_availability_{$booking->property_id}_{$dateStr}");
        }

        Log::info("Inventory released for cancelled booking #{$booking->booking_reference} (Room #{$booking->room_id}).");
    }
}
