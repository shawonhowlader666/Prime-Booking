<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cab;
use App\Services\CabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabApiController extends Controller
{
    public function __construct(
        protected CabService $cabService
    ) {}

    private function getHotelId(Request $request): int
    {
        return $request->user()?->hotel_id ?? 1;
    }

    public function cabs(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $cabs = $this->cabService->getCabs($hotelId, $request->get('search'), $request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Cab fleet fetched successfully',
            'data' => $cabs->items(),
            'pagination' => [
                'current_page' => $cabs->currentPage(),
                'total' => $cabs->total(),
            ],
        ]);
    }

    public function bookings(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $bookings = $this->cabService->getBookings($hotelId, $request->get('search'), $request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Cab bookings fetched successfully',
            'data' => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    public function book(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $validated = $request->validate([
            'cab_id' => 'required|exists:cabs,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'driver_name' => 'nullable|string|max:100',
            'transport_type' => 'required|string',
            'booking_date' => 'nullable|date',
            'booking_time' => 'nullable|string',
            'fare_amount' => 'nullable|numeric|min:0',
            'reservation_id' => 'nullable|exists:reservations,id',
            'post_to_room' => 'nullable|boolean',
        ]);

        $booking = $this->cabService->bookCab($hotelId, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Cab booking confirmed successfully',
            'data' => $booking,
        ], 201);
    }
}
