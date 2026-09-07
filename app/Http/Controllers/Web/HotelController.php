<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function dashboard(Request $request): View
    {
        $hotel = Hotel::first();
        $hotelId = $hotel->id ?? 1;
        $kpis = $this->inventoryService->getDashboardKPIs($hotelId);
        $rooms = Room::with('roomType')->where('hotel_id', $hotelId)->orderBy('room_number')->get();
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();
        $todayBookings = \App\Models\Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->where('hotel_id', $hotelId)
            ->latest()
            ->take(10)
            ->get();

        return view('hotels.dashboard', compact('hotel', 'kpis', 'rooms', 'roomTypes', 'todayBookings'));
    }

    public function overview(): View
    {
        return view('hotels.overview');
    }

    public function activity(): View
    {
        return view('hotels.activity');
    }

    public function matrix(Request $request): View
    {
        $hotel = Hotel::first();
        $hotelId = $hotel->id ?? 1;
        $startDate = $request->get('start_date', now()->toDateString());
        $days = $request->integer('days', 14);

        $matrixData = $this->inventoryService->getVisualMatrix($hotelId, $startDate, $days);
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('hotels.matrix', compact('hotel', 'matrixData', 'startDate', 'days', 'roomTypes'));
    }
}
