<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\RatePlan;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PricingService
{
    /**
     * Calculate stay cost breakdown including seasonal rate plans, taxes, and service charges.
     */
    public function calculateStayPrice(
        Hotel $hotel,
        RoomType $roomType,
        string $checkInDate,
        string $checkOutDate,
        int $roomsCount = 1
    ): array {
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        $dailyRates = [];
        $subtotal = 0.00;

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            // Find matching seasonal Rate Plan if active
            $ratePlan = RatePlan::where('hotel_id', $hotel->id)
                ->where('room_type_id', $roomType->id)
                ->where('is_active', true)
                ->where('start_date', '<=', $dateString)
                ->where('end_date', '>=', $dateString)
                ->first();

            $nightlyRate = $ratePlan ? (float) $ratePlan->price_per_night : (float) $roomType->base_price;

            // Weekend surge multiplier (e.g. Friday & Saturday +10%)
            if ($date->isFriday() || $date->isSaturday()) {
                $nightlyRate *= 1.10;
            }

            $nightlyRate = round($nightlyRate, 2);
            $totalForNight = $nightlyRate * $roomsCount;

            $dailyRates[] = [
                'date' => $dateString,
                'rate_plan' => $ratePlan?->title ?? 'Standard Base Rate',
                'price_per_room' => $nightlyRate,
                'rooms_count' => $roomsCount,
                'total' => $totalForNight,
            ];

            $subtotal += $totalForNight;
        }

        $serviceCharge = round(($subtotal * $hotel->service_charge_percentage) / 100, 2);
        $tax = round(($subtotal * $hotel->tax_percentage) / 100, 2);
        $grandTotal = $subtotal + $serviceCharge + $tax;

        return [
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType->id,
            'room_type_name' => $roomType->name,
            'check_in' => $checkInDate,
            'check_out' => $checkOutDate,
            'nights' => count($period),
            'rooms_count' => $roomsCount,
            'daily_breakdown' => $dailyRates,
            'subtotal' => $subtotal,
            'service_charge_percentage' => $hotel->service_charge_percentage,
            'service_charge_amount' => $serviceCharge,
            'tax_percentage' => $hotel->tax_percentage,
            'tax_amount' => $tax,
            'grand_total' => $grandTotal,
        ];
    }
}
