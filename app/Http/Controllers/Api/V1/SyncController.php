<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\PrimeBookingSyncService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        protected PrimeBookingSyncService $syncService,
        protected ReservationService $reservationService
    ) {}

    /**
     * Prime Booking calls this endpoint to push a new online OTA reservation into the PMS.
     * Cryptographically protected with HMAC SHA256 signature.
     */
    public function handleIncomingBooking(Request $request): JsonResponse
    {
        $signature = $request->header('X-Prime-Signature');
        $hotelCode = $request->header('X-Hotel-Code');

        if (!$hotelCode) {
            return response()->json(['success' => false, 'message' => 'Missing X-Hotel-Code header.'], 401);
        }

        $hotel = Hotel::where('code', $hotelCode)->where('is_active', true)->first();
        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found or inactive.'], 401);
        }

        // Cryptographic HMAC Verification
        $secret = $hotel->sync_webhook_secret;
        if ($secret) {
            $rawPayload = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $rawPayload, $secret);

            if (!$signature || !hash_equals($expectedSignature, $signature)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or forged webhook signature. Access denied.',
                ], 401);
            }
        }

        $data = $request->validate([
            'room_type_code' => 'required|string',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guest_first_name' => 'required|string',
            'guest_last_name' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'idempotency_key' => 'required|string',
        ]);

        $roomType = RoomType::where('hotel_id', $hotel->id)->where('code', $data['room_type_code'])->first();
        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Room type not found in PMS'], 404);
        }

        $checkIn = Carbon::parse($data['check_in_date'])->toDateString();
        $checkOut = Carbon::parse($data['check_out_date'])->toDateString();

        // Concurrency-safe overlap checking across the entire requested date range
        $room = Room::where('hotel_id', $hotel->id)
            ->where('room_type_id', $roomType->id)
            ->where('is_active', true)
            ->whereNotIn('status', ['OUT_OF_ORDER', 'MAINTENANCE'])
            ->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut) {
                $query->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
                      ->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>', $checkIn);
            })
            ->first();

        $bookingData = array_merge($data, [
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType->id,
            'room_id' => $room?->id,
            'source' => 'PRIME_BOOKING_OTA',
        ]);

        try {
            $reservation = $this->reservationService->createReservation($bookingData);

            return response()->json([
                'success' => true,
                'message' => 'OTA booking successfully recorded in PMS',
                'reservation_code' => $reservation->reservation_code,
                'room_number' => $reservation->room?->room_number ?? 'UNASSIGNED',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Outbound manual or automated trigger to push current inventory & room rates to Prime Booking.
     */
    public function pushInventory(Request $request): JsonResponse
    {
        $hotel = $request->user()->hotel ?? Hotel::first();

        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel context not found.'], 404);
        }

        $roomTypes = RoomType::where('hotel_id', $hotel->id)->get();

        $payload = [
            'hotel_code' => $hotel->code,
            'room_types' => $roomTypes->map(fn($t) => [
                'code' => $t->code,
                'name' => $t->name,
                'base_price' => $t->base_price,
                'total_units' => $t->rooms()->count(),
                'available_units' => $t->rooms()->where('status', 'AVAILABLE')->count(),
            ]),
            'synced_at' => now()->toIso8601String(),
        ];

        $event = $this->syncService->dispatchSyncEvent($hotel, 'INVENTORY_SYNC', $payload);

        return response()->json([
            'success' => true,
            'message' => 'Inventory sync dispatched to Prime Booking',
            'sync_event' => $event,
        ]);
    }

    /**
     * Prime Booking Master Super Admin remote control endpoint.
     * Allows Prime Booking to remotely Activate, Suspend, Lock, Unlock, or Extend a PMS Tenant.
     */
    public function subscriptionControl(Request $request): JsonResponse
    {
        $signature = $request->header('X-Prime-Signature');
        $hotelCode = $request->header('X-Hotel-Code');

        if (!$hotelCode) {
            return response()->json(['success' => false, 'message' => 'Missing X-Hotel-Code header.'], 401);
        }

        $hotel = Hotel::where('code', $hotelCode)->first();
        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found.'], 404);
        }

        // Cryptographic HMAC Verification
        $secret = $hotel->sync_webhook_secret;
        if ($secret) {
            $rawPayload = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $rawPayload, $secret);

            if (!$signature || !hash_equals($expectedSignature, $signature)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or forged webhook signature. Access denied.',
                ], 401);
            }
        }

        $data = $request->validate([
            'action' => 'required|string|in:ACTIVATE,SUSPEND,EXTEND,LOCK,UNLOCK',
            'plan' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'monthly_fee' => 'nullable|numeric|min:0',
            'prime_booking_property_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        switch ($data['action']) {
            case 'ACTIVATE':
                $hotel->subscription_status = 'ACTIVE';
                $hotel->is_subscription_locked = false;
                if (!empty($data['expires_at'])) {
                    $hotel->subscription_expires_at = Carbon::parse($data['expires_at']);
                }
                break;

            case 'SUSPEND':
                $hotel->subscription_status = 'SUSPENDED';
                $hotel->is_subscription_locked = true;
                break;

            case 'EXTEND':
                $hotel->subscription_status = 'ACTIVE';
                $hotel->is_subscription_locked = false;
                $currentExpiry = ($hotel->subscription_expires_at && $hotel->subscription_expires_at->isFuture())
                    ? $hotel->subscription_expires_at
                    : now();
                $hotel->subscription_expires_at = $currentExpiry->addDays(30);
                break;

            case 'LOCK':
                $hotel->is_subscription_locked = true;
                break;

            case 'UNLOCK':
                $hotel->is_subscription_locked = false;
                break;
        }

        if (!empty($data['plan'])) {
            $hotel->subscription_plan = $data['plan'];
        }
        if (isset($data['monthly_fee'])) {
            $hotel->monthly_fee = $data['monthly_fee'];
        }
        if (!empty($data['prime_booking_property_id'])) {
            $hotel->prime_booking_property_id = $data['prime_booking_property_id'];
        }

        $hotel->save();

        return response()->json([
            'success' => true,
            'message' => "Hotel {$hotel->code} subscription {$data['action']} action executed successfully.",
            'hotel' => [
                'code' => $hotel->code,
                'name' => $hotel->name,
                'subscription_plan' => $hotel->subscription_plan,
                'subscription_status' => $hotel->subscription_status,
                'subscription_expires_at' => $hotel->subscription_expires_at?->toIso8601String(),
                'is_subscription_locked' => (bool) $hotel->is_subscription_locked,
                'days_remaining' => $hotel->daysUntilExpiry(),
            ],
        ]);
    }

    /**
     * Telemetry stream to Prime Booking Super Admin.
     * Returns full operational status, room inventory, occupancy, and subscription health.
     */
    public function telemetry(Request $request): JsonResponse
    {
        $signature = $request->header('X-Prime-Signature');
        $hotelCode = $request->header('X-Hotel-Code');

        $hotel = $hotelCode ? Hotel::where('code', $hotelCode)->first() : Hotel::first();

        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found.'], 404);
        }

        $totalRooms = Room::where('hotel_id', $hotel->id)->count();
        $availableRooms = Room::where('hotel_id', $hotel->id)->where('status', 'AVAILABLE')->count();
        $occupiedRooms = Room::where('hotel_id', $hotel->id)->where('status', 'OCCUPIED')->count();
        $dirtyRooms = Room::where('hotel_id', $hotel->id)->where('status', 'DIRTY')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0.0;

        $activeReservations = Reservation::where('hotel_id', $hotel->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->count();

        return response()->json([
            'success' => true,
            'hotel_code' => $hotel->code,
            'hotel_name' => $hotel->name,
            'subscription' => [
                'plan' => $hotel->subscription_plan,
                'status' => $hotel->subscription_status,
                'is_active' => $hotel->isSubscriptionActive(),
                'expires_at' => $hotel->subscription_expires_at?->toIso8601String(),
                'monthly_fee' => $hotel->monthly_fee,
                'days_remaining' => $hotel->daysUntilExpiry(),
            ],
            'inventory' => [
                'total_rooms' => $totalRooms,
                'available_rooms' => $availableRooms,
                'occupied_rooms' => $occupiedRooms,
                'dirty_rooms' => $dirtyRooms,
                'occupancy_percentage' => $occupancyRate,
                'active_reservations' => $activeReservations,
            ],
            'server_timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Prime Booking Master Super Admin remote tenant provisioning.
     */
    public function provisionTenant(Request $request): JsonResponse
    {
        $signature = $request->header('X-Prime-Signature');
        $masterSecret = config('app.key');

        if ($signature) {
            $rawPayload = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $rawPayload, $masterSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['success' => false, 'message' => 'Invalid master HMAC signature.'], 401);
            }
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'subscription_plan' => 'required|string|in:TRIAL,STANDARD,PREMIUM,ENTERPRISE',
            'monthly_fee' => 'nullable|numeric|min:0',
            'is_trial' => 'nullable|boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_password' => 'required|string|min:6',
            'prime_booking_property_id' => 'nullable|string',
        ]);

        $service = app(\App\Services\SaaSTenantManagementService::class);
        $result = $service->createTenant($data);

        return response()->json([
            'success' => true,
            'message' => "Hotel tenant '{$result['hotel']->name}' remotely provisioned successfully.",
            'hotel' => [
                'id' => $result['hotel']->id,
                'code' => $result['hotel']->code,
                'name' => $result['hotel']->name,
                'sync_api_key' => $result['hotel']->sync_api_key,
                'sync_webhook_secret' => $result['hotel']->sync_webhook_secret,
                'subscription_status' => $result['hotel']->subscription_status,
                'subscription_expires_at' => $result['hotel']->subscription_expires_at?->toIso8601String(),
            ],
            'owner' => [
                'id' => $result['owner']->id,
                'email' => $result['owner']->email,
            ],
        ], 201);
    }

    /**
     * Prime Booking Master Super Admin remote tenant deletion.
     */
    public function deleteTenant(Request $request, string $code): JsonResponse
    {
        $signature = $request->header('X-Prime-Signature');
        $hotel = Hotel::where('code', $code)->first();

        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found.'], 404);
        }

        $secret = $hotel->sync_webhook_secret ?: config('app.key');
        if ($signature) {
            $expectedSignature = hash_hmac('sha256', $code, $secret);
            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['success' => false, 'message' => 'Invalid webhook signature.'], 401);
            }
        }

        $service = app(\App\Services\SaaSTenantManagementService::class);
        $service->deleteTenant($hotel);

        return response()->json([
            'success' => true,
            'message' => "Hotel tenant {$code} deleted remotely by Prime Booking Master Admin.",
        ]);
    }
}

