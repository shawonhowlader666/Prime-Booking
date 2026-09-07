<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function stats(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $kpis = $this->inventoryService->getDashboardKPIs($hotelId);

        $recentBookings = Reservation::with(['guest', 'room', 'roomType'])
            ->where('hotel_id', $hotelId)
            ->latest()
            ->limit(8)
            ->get();

        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'data' => [
                'kpis' => $kpis,
                'recent_bookings' => $recentBookings,
                'rooms' => $rooms,
            ],
        ]);
    }
}
