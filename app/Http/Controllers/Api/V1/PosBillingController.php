<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosBillingController extends Controller
{
    /**
     * Post a direct Point-of-Sale (POS) outlet charge to an in-house guest room folio.
     */
    public function chargeRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_number' => 'nullable|string',
            'reservation_code' => 'nullable|string',
            'outlet' => 'required|string|max:100', // e.g. "Restaurant", "Spa", "Lounge", "Laundry"
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'pos_reference' => 'nullable|string|max:100',
        ]);

        $hotelId = $request->user()?->hotel_id ?? $request->header('X-Hotel-ID');

        // Locate active reservation
        $reservationQuery = Reservation::with(['guest', 'room', 'folio'])
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['CHECKED_IN', 'CONFIRMED', 'checked_in', 'confirmed']);

        if (!empty($validated['reservation_code'])) {
            $reservationQuery->where('reservation_code', $validated['reservation_code']);
        } elseif (!empty($validated['room_number'])) {
            $room = Room::where('hotel_id', $hotelId)
                ->where('room_number', $validated['room_number'])
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => "Room {$validated['room_number']} does not exist in this property.",
                ], 404);
            }

            $reservationQuery->where('room_id', $room->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Either room_number or reservation_code must be provided.',
            ], 422);
        }

        $reservation = $reservationQuery->latest('id')->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'No active in-house guest found for the specified room or reservation code.',
            ], 404);
        }

        // Execute atomic double-entry folio update
        $result = DB::transaction(function () use ($reservation, $validated, $hotelId, $request) {
            $folio = $reservation->folio;
            if (!$folio) {
                $folio = Folio::create([
                    'hotel_id' => $hotelId,
                    'reservation_id' => $reservation->id,
                    'folio_number' => 'FOL-' . ($reservation->reservation_code ?? $reservation->id),
                    'total_charges' => 0.00,
                    'total_payments' => 0.00,
                    'balance' => 0.00,
                    'status' => 'OPEN',
                ]);
            }

            $baseAmount = (float) $validated['amount'];
            $taxAmount = (float) ($validated['tax_amount'] ?? 0.0);
            $serviceCharge = (float) ($validated['service_charge'] ?? 0.0);
            $totalCharge = round($baseAmount + $taxAmount + $serviceCharge, 2);

            // Create Folio Item
            $folioItem = FolioItem::create([
                'hotel_id' => $hotelId,
                'folio_id' => $folio->id,
                'item_type' => 'POS_OUTLET_CHARGE',
                'description' => "[{$validated['outlet']}] {$validated['description']}",
                'unit_price' => $totalCharge,
                'quantity' => 1,
                'total_amount' => $totalCharge,
                'posted_by_user_id' => $request->user()?->id,
                'posted_at' => now(),
            ]);

            // Increment Folio balance atomically
            $folio->total_charges = round($folio->total_charges + $totalCharge, 2);
            $folio->balance = round($folio->balance + $totalCharge, 2);
            $folio->save();

            // Record Audit Log
            AuditLog::create([
                'hotel_id' => $hotelId,
                'user_id' => $request->user()?->id,
                'action' => 'POS_ROOM_CHARGE_POSTED',
                'entity_type' => FolioItem::class,
                'entity_id' => $folioItem->id,
                'old_values' => null,
                'new_values' => [
                    'outlet' => $validated['outlet'],
                    'pos_reference' => $validated['pos_reference'] ?? null,
                    'total_charged' => $totalCharge,
                    'new_folio_balance' => $folio->balance,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return [
                'folio_item_id' => $folioItem->id,
                'total_charged' => $totalCharge,
                'new_folio_balance' => $folio->balance,
                'folio_number' => $folio->folio_number,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully charged ৳" . number_format($result['total_charged'], 2) . " to Room {$reservation->room?->room_number}",
            'data' => [
                'receipt_id' => 'POS-' . strtoupper(bin2hex(random_bytes(4))),
                'guest_name' => $reservation->guest ? ($reservation->guest->first_name . ' ' . $reservation->guest->last_name) : 'Valued Guest',
                'room_number' => $reservation->room?->room_number,
                'reservation_code' => $reservation->reservation_code,
                'outlet' => $validated['outlet'],
                'description' => $validated['description'],
                'amount_charged' => $result['total_charged'],
                'current_folio_balance' => $result['new_folio_balance'],
                'timestamp' => now()->toIso8601String(),
            ],
        ], 200);
    }
}
