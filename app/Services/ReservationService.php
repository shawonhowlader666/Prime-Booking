<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Guest;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReservationService
{
    /**
     * Create a reservation with concurrency-safe pessimistic locking.
     */
    public function createReservation(array $data, ?User $actor = null): Reservation
    {
        return DB::transaction(function () use ($data, $actor) {
            $hotelId = $data['hotel_id'];
            $checkIn = Carbon::parse($data['check_in_date'])->toDateString();
            $checkOut = Carbon::parse($data['check_out_date'])->toDateString();
            $roomId = $data['room_id'] ?? null;
            $roomTypeId = $data['room_type_id'];

            // 1. Verify and lock room if specified
            if ($roomId) {
                $room = Room::where('hotel_id', $hotelId)
                    ->where('id', $roomId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Check for overlapping confirmed or checked-in reservations
                $hasOverlap = Reservation::where('hotel_id', $hotelId)
                    ->where('room_id', $roomId)
                    ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in_date', '<', $checkOut)
                              ->where('check_out_date', '>', $checkIn);
                    })
                    ->exists();

                if ($hasOverlap) {
                    throw new InvalidArgumentException("Room {$room->room_number} is already booked for the selected dates.");
                }
            }

            // 2. Resolve or create Guest
            $guestId = $data['guest_id'] ?? null;
            if (!$guestId) {
                $guest = Guest::create([
                    'hotel_id' => $hotelId,
                    'first_name' => $data['guest_first_name'] ?? 'Walk-in',
                    'last_name' => $data['guest_last_name'] ?? 'Guest',
                    'email' => $data['guest_email'] ?? null,
                    'phone' => $data['guest_phone'] ?? null,
                    'id_type' => $data['guest_id_type'] ?? 'NID',
                    'id_number_encrypted' => $data['guest_id_number'] ?? null,
                    'address' => $data['guest_address'] ?? null,
                ]);
                $guestId = $guest->id;
            }

            // 3. Generate Unique Code
            $reservationCode = 'RES-' . strtoupper(Str::random(6)) . '-' . rand(100, 999);

            // 4. Create Reservation Record
            $reservation = Reservation::create([
                'hotel_id' => $hotelId,
                'reservation_code' => $reservationCode,
                'guest_id' => $guestId,
                'room_id' => $roomId,
                'room_type_id' => $roomTypeId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'status' => $data['status'] ?? 'CONFIRMED',
                'source' => $data['source'] ?? 'DIRECT_WALKIN',
                'total_amount' => $data['total_amount'] ?? 0.00,
                'paid_amount' => $data['paid_amount'] ?? 0.00,
                'balance_amount' => ($data['total_amount'] ?? 0.00) - ($data['paid_amount'] ?? 0.00),
                'special_requests' => $data['special_requests'] ?? null,
                'idempotency_key' => $data['idempotency_key'] ?? Str::uuid()->toString(),
            ]);

            // 5. Create Billing Folio
            $folio = Folio::create([
                'hotel_id' => $hotelId,
                'reservation_id' => $reservation->id,
                'folio_number' => 'FOL-' . $reservationCode,
                'total_charges' => $data['total_amount'] ?? 0.00,
                'total_payments' => $data['paid_amount'] ?? 0.00,
                'balance' => ($data['total_amount'] ?? 0.00) - ($data['paid_amount'] ?? 0.00),
                'status' => 'OPEN',
            ]);

            // Post initial Room Tariff charge
            if (($data['total_amount'] ?? 0) > 0) {
                FolioItem::create([
                    'hotel_id' => $hotelId,
                    'folio_id' => $folio->id,
                    'item_type' => 'ROOM_CHARGE',
                    'description' => 'Room Tariff for ' . $checkIn . ' to ' . $checkOut,
                    'unit_price' => $data['total_amount'],
                    'quantity' => 1,
                    'total_amount' => $data['total_amount'],
                    'posted_by_user_id' => $actor?->id,
                    'posted_at' => now(),
                ]);
            }

            // 6. Audit Logging
            AuditLog::create([
                'hotel_id' => $hotelId,
                'user_id' => $actor?->id,
                'action' => 'RESERVATION_CREATED',
                'entity_type' => Reservation::class,
                'entity_id' => $reservation->id,
                'new_values' => $reservation->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Invalidate cached KPIs
            Cache::forget("hotel_{$hotelId}_kpis");

            return $reservation->load(['guest', 'room', 'roomType', 'folio']);
        });
    }

    /**
     * Check in guest.
     */
    public function checkIn(Reservation $reservation, ?User $actor = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $actor) {
            if ($reservation->status === 'CHECKED_IN') {
                return $reservation;
            }

            $reservation->status = 'CHECKED_IN';
            $reservation->checked_in_at = now();
            $reservation->save();

            if ($reservation->room_id) {
                $room = Room::where('id', $reservation->room_id)->lockForUpdate()->first();
                if ($room) {
                    $room->status = 'OCCUPIED';
                    $room->save();
                }
            }

            AuditLog::create([
                'hotel_id' => $reservation->hotel_id,
                'user_id' => $actor?->id,
                'action' => 'GUEST_CHECKED_IN',
                'entity_type' => Reservation::class,
                'entity_id' => $reservation->id,
                'new_values' => ['status' => 'CHECKED_IN', 'checked_in_at' => now()],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Cache::forget("hotel_{$reservation->hotel_id}_kpis");

            return $reservation->fresh(['room', 'guest', 'folio']);
        });
    }

    /**
     * Check out guest.
     */
    public function checkOut(Reservation $reservation, ?User $actor = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $actor) {
            $reservation->status = 'CHECKED_OUT';
            $reservation->checked_out_at = now();
            $reservation->save();

            if ($reservation->room_id) {
                $room = Room::where('id', $reservation->room_id)->lockForUpdate()->first();
                if ($room) {
                    $room->status = 'DIRTY';
                    $room->save();

                    // Create Housekeeping Task automatically
                    HousekeepingTask::create([
                        'hotel_id' => $reservation->hotel_id,
                        'room_id' => $room->id,
                        'priority' => 'HIGH',
                        'status' => 'PENDING',
                        'notes' => 'Checkout cleaning required for Room ' . $room->room_number,
                    ]);
                }
            }

            // Close folio if settled
            if ($reservation->folio && $reservation->folio->balance <= 0) {
                $reservation->folio->status = 'SETTLED';
                $reservation->folio->closed_at = now();
                $reservation->folio->save();
            }

            AuditLog::create([
                'hotel_id' => $reservation->hotel_id,
                'user_id' => $actor?->id,
                'action' => 'GUEST_CHECKED_OUT',
                'entity_type' => Reservation::class,
                'entity_id' => $reservation->id,
                'new_values' => ['status' => 'CHECKED_OUT', 'checked_out_at' => now()],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Cache::forget("hotel_{$reservation->hotel_id}_kpis");

            return $reservation->fresh(['room', 'guest', 'folio']);
        });
    }
}
