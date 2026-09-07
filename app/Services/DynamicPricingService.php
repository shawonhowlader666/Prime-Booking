<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DynamicPricingService
{
    /**
     * Calculate intelligent dynamic pricing for a room type based on real-time demand,
     * occupancy rate, day of week, and booking lead time.
     */
    public function calculateDynamicRate(RoomType $roomType, string $checkInDate, int $nights = 1): array
    {
        $hotelId = $roomType->hotel_id;
        $basePrice = (float) $roomType->base_price;

        $startDate = Carbon::parse($checkInDate)->startOfDay();
        $endDate = (clone $startDate)->addDays($nights)->endOfDay();

        // 1. Calculate Hotel & RoomType Occupancy for that window
        $totalRooms = Room::where('hotel_id', $hotelId)
            ->where('room_type_id', $roomType->id)
            ->where('is_active', true)
            ->count();

        $totalRooms = max($totalRooms, 1);

        $occupiedRooms = Reservation::where('hotel_id', $hotelId)
            ->where('room_type_id', $roomType->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('check_in_date', '<', $endDate->format('Y-m-d'))
                  ->where('check_out_date', '>', $startDate->format('Y-m-d'));
            })
            ->count();

        $occupancyRate = min(($occupiedRooms / $totalRooms) * 100, 100.0);

        // 2. Determine Occupancy Multiplier
        $occupancyMultiplier = 1.0;
        if ($occupancyRate >= 85) {
            $occupancyMultiplier = 1.30; // +30% surge (High Demand)
        } elseif ($occupancyRate >= 70) {
            $occupancyMultiplier = 1.15; // +15% surge (Moderate Demand)
        } elseif ($occupancyRate <= 30) {
            $occupancyMultiplier = 0.90; // -10% discount (Low Demand Stimulation)
        }

        // 3. Lead Time Multiplier (Last-minute vs Early Bird)
        $daysUntilCheckIn = max((int) now()->diffInDays($startDate, false), 0);
        $leadTimeMultiplier = 1.0;
        if ($daysUntilCheckIn <= 1 && $occupancyRate > 50) {
            $leadTimeMultiplier = 1.10; // +10% last-minute surge
        } elseif ($daysUntilCheckIn >= 30) {
            $leadTimeMultiplier = 0.95; // -5% early-bird incentive
        }

        // 4. Calculate day-by-day rates factoring weekend premiums
        $period = CarbonPeriod::create($startDate, '1 day', (clone $startDate)->addDays($nights - 1));
        $dailyBreakdown = [];
        $totalRate = 0.0;

        foreach ($period as $day) {
            $dayMultiplier = $occupancyMultiplier * $leadTimeMultiplier;

            // Weekend surge (Friday & Saturday)
            if ($day->isFriday() || $day->isSaturday()) {
                $dayMultiplier *= 1.15; // +15% weekend premium
            }

            $effectiveDailyPrice = round($basePrice * $dayMultiplier, 2);
            $totalRate += $effectiveDailyPrice;

            $dailyBreakdown[] = [
                'date' => $day->format('Y-m-d'),
                'day_name' => $day->format('l'),
                'is_weekend' => $day->isFriday() || $day->isSaturday(),
                'base_price' => $basePrice,
                'multiplier' => round($dayMultiplier, 2),
                'price' => $effectiveDailyPrice,
            ];
        }

        return [
            'room_type_id' => $roomType->id,
            'room_type_name' => $roomType->name,
            'base_price' => $basePrice,
            'nights' => $nights,
            'occupancy_rate_percentage' => round($occupancyRate, 1),
            'recommended_total_price' => round($totalRate, 2),
            'average_price_per_night' => round($totalRate / max($nights, 1), 2),
            'surge_applied' => $occupancyMultiplier > 1.0 || $leadTimeMultiplier > 1.0,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }

    /**
     * Generate Yield & Dynamic Pricing Insights across all room types for a hotel.
     */
    public function getPricingRecommendations(Hotel $hotel): array
    {
        $roomTypes = RoomType::where('hotel_id', $hotel->id)->get();
        $recommendations = [];

        foreach ($roomTypes as $roomType) {
            $recommendations[] = $this->calculateDynamicRate($roomType, now()->format('Y-m-d'), 1);
        }

        return [
            'hotel_id' => $hotel->id,
            'hotel_code' => $hotel->code,
            'generated_at' => now()->toIso8601String(),
            'recommendations' => $recommendations,
        ];
    }
}
