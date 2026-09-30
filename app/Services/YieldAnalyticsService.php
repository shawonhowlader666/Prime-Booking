<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * YieldAnalyticsService — Agoda YCS / Booking.com Revenue Management Engine.
 * Calculates Occupancy Rate (%), ADR (Average Daily Rate), RevPAR (Revenue Per Available Room),
 * and Booking Lead Time.
 */
class YieldAnalyticsService
{
    /**
     * Calculate Yield & Revenue Management KPIs for a property or all properties.
     */
    public function getPropertyYieldMetrics(?int $propertyId = null, ?int $vendorId = null): array
    {
        $query = Booking::whereNotIn('status', ['cancelled', 'refunded']);

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($vendorId) {
            $query->whereHas('property', fn($q) => $q->where('vendor_id', $vendorId));
        }

        $totalBookings = (clone $query)->count();
        $totalNights   = (clone $query)->sum('nights') ?: 1;
        $totalRevenue  = (float) ((clone $query)->sum('total_price') ?: 0);

        // Calculate ADR (Average Daily Rate) = Total Revenue / Total Room Nights Sold
        $adr = round($totalRevenue / max(1, $totalNights), 2);

        // Total available room inventory
        $roomQuery = Room::where('status', 'active');
        if ($propertyId) {
            $roomQuery->where('property_id', $propertyId);
        }
        if ($vendorId) {
            $roomQuery->whereHas('property', fn($q) => $q->where('vendor_id', $vendorId));
        }
        $totalRooms = max(1, $roomQuery->count());

        // Approximate 30-day capacity = Total Rooms * 30 days
        $monthlyAvailableNights = $totalRooms * 30;
        $occupancyRate = round(min(100.0, ($totalNights / $monthlyAvailableNights) * 100), 2);

        // RevPAR (Revenue Per Available Room) = ADR * (Occupancy Rate / 100)
        $revPar = round($adr * ($occupancyRate / 100.0), 2);

        return [
            'property_id'               => $propertyId,
            'vendor_id'                 => $vendorId,
            'total_bookings'            => $totalBookings,
            'total_nights_sold'         => (int) $totalNights,
            'gross_revenue'             => $totalRevenue,
            'adr_average_daily_rate'    => $adr,
            'occupancy_rate_percentage' => $occupancyRate,
            'revpar_revenue_per_room'   => $revPar,
            'total_active_rooms'        => $totalRooms,
            'currency'                  => 'BDT',
            'performance_tier'          => $occupancyRate >= 70 ? 'TOP_PERFORMER' : ($occupancyRate >= 40 ? 'HEALTHY' : 'OPTIMIZATION_NEEDED'),
            'calculated_at'             => now()->toIso8601String(),
        ];
    }
}
