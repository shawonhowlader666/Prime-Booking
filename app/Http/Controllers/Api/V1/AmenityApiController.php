<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AmenityOrder;
use App\Models\AmenityService as AmenityServiceModel;
use App\Services\AmenityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmenityApiController extends Controller
{
    public function __construct(
        protected AmenityService $amenityService
    ) {}

    private function getHotelId(Request $request): int
    {
        return $request->user()?->hotel_id ?? 1;
    }

    public function services(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $services = AmenityServiceModel::where('hotel_id', $hotelId)
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Amenity services fetched',
            'data' => $services,
        ]);
    }

    public function book(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $validated = $request->validate([
            'service_id' => 'required|exists:amenity_services,id',
            'booked_for_date' => 'required|date',
            'time_slot' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:1',
            'reservation_id' => 'nullable|exists:reservations,id',
            'room_id' => 'nullable|exists:rooms,id',
            'guest_id' => 'nullable|exists:guests,id',
            'post_to_room' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $order = $this->amenityService->bookAmenity(
            $hotelId,
            (int)$validated['service_id'],
            $validated['reservation_id'] ?? null,
            $validated['guest_id'] ?? null,
            $validated['room_id'] ?? null,
            $validated['booked_for_date'],
            $validated['time_slot'] ?? null,
            (int)($validated['quantity'] ?? 1),
            ($request->boolean('post_to_room') && !empty($validated['reservation_id'])) ? 'ROOM_CHARGE' : 'CASH',
            $validated['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Amenity booked successfully',
            'data' => $order,
        ], 201);
    }
}
