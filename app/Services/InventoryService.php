<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Clear all cached metrics and matrices for a specific hotel.
     */
    public function clearHotelCache(int $hotelId): void
    {
        Cache::forget("hotel_{$hotelId}_kpis");
        Cache::forget("hotel_{$hotelId}_matrix_14");
    }

    /**
     * Compute real-time dashboard KPIs for hotel property (Redis/Cache powered).
     */
    public function getDashboardKPIs(int $hotelId): array
    {
        return Cache::remember("hotel_{$hotelId}_kpis", 30, function () use ($hotelId) {
            $totalRooms = Room::where('hotel_id', $hotelId)->where('is_active', true)->count();
            $occupiedRooms = Room::where('hotel_id', $hotelId)->where('status', 'OCCUPIED')->count();
            $availableRooms = Room::where('hotel_id', $hotelId)->where('status', 'AVAILABLE')->count();
            $dirtyRooms = Room::where('hotel_id', $hotelId)->where('status', 'DIRTY')->count();
            $maintenanceRooms = Room::where('hotel_id', $hotelId)->where('status', 'MAINTENANCE')->count();

            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0.0;

            $todayCheckIns = Reservation::where('hotel_id', $hotelId)
                ->whereDate('check_in_date', now()->toDateString())
                ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
                ->count();

            $todayCheckOuts = Reservation::where('hotel_id', $hotelId)
                ->whereDate('check_out_date', now()->toDateString())
                ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
                ->count();

            $todayRevenue = Payment::where('hotel_id', $hotelId)
                ->whereDate('created_at', now()->toDateString())
                ->where('status', 'SUCCESS')
                ->sum('amount');

            $monthRevenue = Payment::where('hotel_id', $hotelId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'SUCCESS')
                ->sum('amount');

            return [
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'available_rooms' => $availableRooms,
                'dirty_rooms' => $dirtyRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'occupancy_rate' => $occupancyRate,
                'today_check_ins' => $todayCheckIns,
                'today_check_outs' => $todayCheckOuts,
                'today_revenue' => (float) $todayRevenue,
                'month_revenue' => (float) $monthRevenue,
            ];
        });
    }

    /**
     * Generate 14-day live room availability & occupancy matrix.
     */
    public function getLiveMatrix(int $hotelId, ?string $startDate = null, int $days = 14): array
    {
        $start = $startDate ? Carbon::parse($startDate) : now()->startOfDay();
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $dates[] = $start->copy()->addDays($i)->toDateString();
        }

        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get();

        $reservations = Reservation::with('guest')
            ->where('hotel_id', $hotelId)
            ->where('check_out_date', '>=', $dates[0])
            ->where('check_in_date', '<=', end($dates))
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->get();

        $matrix = [];
        foreach ($rooms as $room) {
            $row = [
                'room' => $room,
                'days' => []
            ];
            foreach ($dates as $date) {
                $booking = $reservations->first(function ($r) use ($room, $date) {
                    return $r->room_id === $room->id && $r->check_in_date <= $date && $r->check_out_date > $date;
                });

                $row['days'][$date] = $booking ? [
                    'status' => $booking->status,
                    'reservation_code' => $booking->reservation_code,
                    'guest_name' => $booking->guest ? ($booking->guest->first_name . ' ' . $booking->guest->last_name) : 'Guest',
                    'source' => $booking->source,
                ] : [
                    'status' => $room->status === 'MAINTENANCE' ? 'MAINTENANCE' : 'AVAILABLE',
                    'reservation_code' => null,
                    'guest_name' => null,
                    'source' => null,
                ];
            }
            $matrix[] = $row;
        }

        return [
            'dates' => $dates,
            'matrix' => $matrix,
        ];
    }
    /**
     * Record incoming purchased stock with atomic increment.
     */
    public function recordPurchaseIn(
        int $hotelId,
        int $itemId,
        float $quantity,
        float $unitCost,
        ?string $referenceNo = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($hotelId, $itemId, $quantity, $unitCost, $referenceNo, $userId, $notes) {
            $item = InventoryItem::where('hotel_id', $hotelId)->lockForUpdate()->findOrFail($itemId);

            $totalCost = $quantity * $unitCost;

            // 1. Immutable ledger movement
            $movement = StockMovement::create([
                'hotel_id' => $hotelId,
                'item_id' => $itemId,
                'user_id' => $userId,
                'type' => 'PURCHASE_IN',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_no' => $referenceNo ?? 'PO-' . date('Ymd') . '-' . rand(100, 999),
                'notes' => $notes ?? 'Stock In / Purchase Received',
            ]);

            // 2. Atomic stock balance update
            $item->increment('current_stock', $quantity);
            if ($unitCost > 0) {
                $item->update(['purchase_price' => $unitCost]);
            }

            return $movement;
        });
    }

    /**
     * Record stock issued to room or housekeeping with atomic decrement.
     */
    public function recordRoomIssue(
        int $hotelId,
        int $itemId,
        float $quantity,
        ?string $roomNo = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($hotelId, $itemId, $quantity, $roomNo, $userId, $notes) {
            $item = InventoryItem::where('hotel_id', $hotelId)->lockForUpdate()->findOrFail($itemId);

            $unitCost = (float) $item->purchase_price;
            $totalCost = $quantity * $unitCost;

            $movement = StockMovement::create([
                'hotel_id' => $hotelId,
                'item_id' => $itemId,
                'user_id' => $userId,
                'type' => 'ROOM_ISSUE',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_no' => $roomNo ? "ROOM-{$roomNo}" : 'HK-ISSUE',
                'notes' => $notes ?? "Issued to Room {$roomNo}",
            ]);

            $item->decrement('current_stock', $quantity);

            return $movement;
        });
    }

    /**
     * Record damaged, expired, or broken goods write-off.
     */
    public function recordDamageWriteOff(
        int $hotelId,
        int $itemId,
        float $quantity,
        ?int $userId = null,
        ?string $reason = null
    ): StockMovement {
        return DB::transaction(function () use ($hotelId, $itemId, $quantity, $userId, $reason) {
            $item = InventoryItem::where('hotel_id', $hotelId)->lockForUpdate()->findOrFail($itemId);

            $unitCost = (float) $item->purchase_price;
            $totalCost = $quantity * $unitCost;

            $movement = StockMovement::create([
                'hotel_id' => $hotelId,
                'item_id' => $itemId,
                'user_id' => $userId,
                'type' => 'DAMAGED',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_no' => 'DMG-' . date('Ymd') . '-' . rand(100, 999),
                'notes' => $reason ?? 'Damaged / Expired stock write-off',
            ]);

            $item->decrement('current_stock', $quantity);

            return $movement;
        });
    }

    /**
     * Compute aggregate inventory metrics and valuations.
     */
    public function getValuationMetrics(int $hotelId): array
    {
        $items = InventoryItem::where('hotel_id', $hotelId)->where('is_active', true)->get();

        $totalItems = $items->count();
        $totalValuation = $items->sum(fn($i) => (float) $i->current_stock * (float) $i->purchase_price);
        $lowStockCount = $items->filter(fn($i) => (float) $i->current_stock <= (float) $i->min_alert_stock && (float) $i->current_stock > 0)->count();
        $outOfStockCount = $items->filter(fn($i) => (float) $i->current_stock <= 0)->count();

        return [
            'total_items' => $totalItems,
            'total_valuation' => $totalValuation,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ];
    }
}
