<?php

namespace App\Services;

use App\Models\AmenityOrder;
use App\Models\AmenityService as AmenityServiceModel;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class AmenityService
{
    /**
     * Book an amenity service for a guest or room, optionally posting charges to the Room Folio.
     */
    public function bookAmenity(
        int $hotelId,
        int $serviceId,
        ?int $reservationId,
        ?int $guestId,
        ?int $roomId,
        string $bookedDate,
        ?string $timeSlot,
        int $quantity = 1,
        string $paymentMethod = 'ROOM_CHARGE', // ROOM_CHARGE, CASH, CARD
        ?string $notes = null
    ): AmenityOrder {
        return DB::transaction(function () use (
            $hotelId, $serviceId, $reservationId, $guestId, $roomId,
            $bookedDate, $timeSlot, $quantity, $paymentMethod, $notes
        ) {
            $service = AmenityServiceModel::where('hotel_id', $hotelId)->findOrFail($serviceId);

            $subtotal = $service->price * $quantity;
            $taxAmount = ($subtotal * $service->tax_percentage) / 100;
            $totalAmount = $subtotal + $taxAmount;

            $paymentStatus = ($paymentMethod === 'ROOM_CHARGE') ? 'CHARGED_TO_ROOM' : 'PAID';

            // 1. Create the Amenity Booking Order
            $order = AmenityOrder::create([
                'hotel_id' => $hotelId,
                'service_id' => $serviceId,
                'guest_id' => $guestId,
                'reservation_id' => $reservationId,
                'room_id' => $roomId,
                'booked_for_date' => $bookedDate,
                'time_slot' => $timeSlot ?? 'Standard Slot',
                'quantity' => $quantity,
                'unit_price' => $service->price,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'status' => 'CONFIRMED',
                'notes' => $notes,
            ]);

            // 2. If charged to room, post directly into the Guest's open Folio
            if ($paymentMethod === 'ROOM_CHARGE' && $reservationId) {
                $folio = Folio::where('hotel_id', $hotelId)
                    ->where('reservation_id', $reservationId)
                    ->where('status', 'OPEN')
                    ->first();

                if ($folio) {
                    // Create Folio Line Item for the Amenity Service
                    FolioItem::create([
                        'hotel_id' => $hotelId,
                        'folio_id' => $folio->id,
                        'item_type' => 'AMENITY_SERVICE',
                        'description' => "Amenity Service: {$service->name} ({$quantity}x)",
                        'unit_price' => $service->price,
                        'quantity' => $quantity,
                        'total_amount' => $subtotal,
                        'posted_at' => now(),
                    ]);

                    // Create Folio Line Item for Amenity VAT
                    if ($taxAmount > 0) {
                        FolioItem::create([
                            'hotel_id' => $hotelId,
                            'folio_id' => $folio->id,
                            'item_type' => 'TAX',
                            'description' => "VAT on {$service->name} ({$service->tax_percentage}%)",
                            'unit_price' => $taxAmount,
                            'quantity' => 1,
                            'total_amount' => $taxAmount,
                            'posted_at' => now(),
                        ]);
                    }

                    // Update Folio Totals
                    $folio->increment('total_charges', $totalAmount);
                    $folio->increment('balance', $totalAmount);
                }
            }

            return $order;
        });
    }

    /**
     * Get aggregate booking stats for a specific category.
     */
    public function getCategoryStats(int $hotelId, string $category): array
    {
        $services = AmenityServiceModel::where('hotel_id', $hotelId)
            ->where('category', $category)
            ->where('is_active', true)
            ->get();

        $serviceIds = $services->pluck('id');

        $totalBookings = AmenityOrder::where('hotel_id', $hotelId)
            ->whereIn('service_id', $serviceIds)
            ->count();

        $todayBookings = AmenityOrder::where('hotel_id', $hotelId)
            ->whereIn('service_id', $serviceIds)
            ->whereDate('booked_for_date', now()->toDateString())
            ->count();

        $totalRevenue = AmenityOrder::where('hotel_id', $hotelId)
            ->whereIn('service_id', $serviceIds)
            ->where('status', '!=', 'CANCELLED')
            ->sum('total_amount');

        return [
            'services' => $services,
            'total_bookings' => $totalBookings,
            'today_bookings' => $todayBookings,
            'total_revenue' => (float) $totalRevenue,
        ];
    }
}
