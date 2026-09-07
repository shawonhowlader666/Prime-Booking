<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\InventoryService;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $query = Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->where('hotel_id', $hotelId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_code', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($g) use ($search) {
                      $g->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $reservations = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guest_first_name' => 'required|string|max:100',
            'guest_last_name' => 'nullable|string|max:100',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string|max:30',
            'guest_id_number' => 'nullable|string|max:50',
            'guest_id_type' => 'nullable|string|in:NID,PASSPORT,DRIVING_LICENSE',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string',
            'source' => 'nullable|string|in:DIRECT_WALKIN,PRIME_BOOKING_OTA,PHONE,CORPORATE',
        ]);

        $data['hotel_id'] = $request->user()->hotel_id ?? 1;

        try {
            $reservation = $this->reservationService->createReservation($data, $request->user());

            return response()->json([
                'success' => true,
                'status_code' => 201,
                'message' => 'Reservation created successfully',
                'data' => $reservation,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $reservation = Reservation::with(['guest', 'room', 'roomType', 'folio.items', 'folio.payments'])
            ->where('hotel_id', $hotelId)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    public function checkIn(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $reservation = Reservation::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();
        $reservation = $this->reservationService->checkIn($reservation, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Guest checked in successfully',
            'data' => $reservation,
        ]);
    }

    public function checkOut(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $reservation = Reservation::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();
        $reservation = $this->reservationService->checkOut($reservation, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Guest checked out successfully',
            'data' => $reservation,
        ]);
    }

    public function matrix(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;
        $startDate = $request->get('start_date', now()->toDateString());
        $days = $request->integer('days', 14);

        $data = $this->inventoryService->getVisualMatrix($hotelId, $startDate, $days);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
