<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Cab;
use App\Models\CabBooking;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CabService
{
    /**
     * Fetch paginated cabs for a hotel with search.
     */
    public function getCabs(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Cab::where('hotel_id', $hotelId)
            ->when($search, function ($q) use ($search) {
                $q->where(fn($sub) => $sub->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%")
                    ->orWhere('car_type', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new cab unit in hotel fleet.
     */
    public function createCab(int $hotelId, array $data): Cab
    {
        $data['hotel_id'] = $hotelId;
        $cab = Cab::create($data);

        AuditService::log($hotelId, 'CAB_CREATED', $cab);

        return $cab;
    }

    /**
     * Update an existing cab unit.
     */
    public function updateCab(int $hotelId, int $cabId, array $data): Cab
    {
        $cab = Cab::where('hotel_id', $hotelId)->findOrFail($cabId);
        $old = $cab->toArray();
        $cab->update($data);

        AuditService::log($hotelId, 'CAB_UPDATED', $cab, $old, $cab->toArray());

        return $cab;
    }

    /**
     * Delete a cab unit.
     */
    public function deleteCab(int $hotelId, int $cabId): bool
    {
        $cab = Cab::where('hotel_id', $hotelId)->findOrFail($cabId);
        $cab->delete();

        return true;
    }

    /**
     * Fetch paginated cab bookings for a hotel with search.
     */
    public function getBookings(int $hotelId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return CabBooking::with(['cab', 'reservation.guest'])
            ->where('hotel_id', $hotelId)
            ->when($search, function ($q) use ($search) {
                $q->where(fn($sub) => $sub->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhere('transport_type', 'like', "%{$search}%")
                    ->orWhereHas('cab', fn($c) => $c->where('car_name', 'like', "%{$search}%")->orWhere('car_number', 'like', "%{$search}%")));
            })
            ->latest('booking_date')
            ->paginate($perPage);
    }

    /**
     * Book a cab trip with optional room folio billing.
     */
    public function bookCab(int $hotelId, array $data): CabBooking
    {
        return DB::transaction(function () use ($hotelId, $data) {
            $cab = Cab::where('hotel_id', $hotelId)->findOrFail($data['cab_id']);

            $reservationId = $data['reservation_id'] ?? null;
            $guestId = $data['guest_id'] ?? null;

            if ($reservationId && !$guestId) {
                $reservation = Reservation::where('hotel_id', $hotelId)->find($reservationId);
                $guestId = $reservation?->guest_id;
            }

            $driverName = $data['driver_name'] ?? $cab->driver_name ?? 'Hotel Driver';
            $fareAmount = (float)($data['fare_amount'] ?? $cab->base_fare ?? 0.00);

            $paymentMethod = $data['payment_status'] ?? 'PENDING';
            $isChargedToRoom = ($paymentMethod === 'CHARGED_TO_ROOM' || !empty($data['post_to_room']));

            $booking = CabBooking::create([
                'hotel_id' => $hotelId,
                'cab_id' => $cab->id,
                'reservation_id' => $reservationId,
                'guest_id' => $guestId,
                'customer_name' => $data['customer_name'] ?? 'Hotel Guest',
                'customer_phone' => $data['customer_phone'] ?? null,
                'booking_date' => $data['booking_date'] ?? now()->toDateString(),
                'booking_time' => $data['booking_time'] ?? now()->format('H:i A'),
                'transport_type' => $data['transport_type'] ?? 'PICKUP',
                'pickup_location' => $data['pickup_location'] ?? null,
                'drop_location' => $data['drop_location'] ?? null,
                'fare_amount' => $fareAmount,
                'payment_status' => $isChargedToRoom ? 'CHARGED_TO_ROOM' : $paymentMethod,
                'status' => 'CONFIRMED',
                'driver_name' => $driverName,
                'notes' => $data['notes'] ?? null,
            ]);

            // Post to Stay Folio if charged to room
            if ($isChargedToRoom && $reservationId && $fareAmount > 0) {
                $folio = Folio::where('hotel_id', $hotelId)
                    ->where('reservation_id', $reservationId)
                    ->first();

                if ($folio) {
                    FolioItem::create([
                        'hotel_id' => $hotelId,
                        'folio_id' => $folio->id,
                        'item_type' => 'CAB_SERVICE',
                        'description' => "Cab Transport ({$cab->car_name} - {$booking->transport_type})",
                        'unit_price' => $fareAmount,
                        'quantity' => 1,
                        'total_amount' => $fareAmount,
                        'posted_at' => now(),
                    ]);

                    $folio->total_charges = (float)$folio->total_charges + $fareAmount;
                    $folio->balance = $folio->total_charges - (float)$folio->total_payments;
                    $folio->save();
                }
            }

            AuditService::log($hotelId, 'CAB_BOOKING_CREATED', $booking);

            return $booking;
        });
    }
}
