<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\RoomOperationsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomOperationsController extends Controller
{
    public function __construct(
        protected RoomOperationsService $roomService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function roomList(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $rooms = $this->roomService->getRoomList($hotelId, $request->get('search'), 20);
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('pages.room-list', compact('rooms', 'roomTypes'));
    }

    public function storeRoom(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'room_type_name' => 'nullable|string|max:100',
            'room_type_id' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'room_price' => 'nullable|numeric',
            'floor' => 'nullable|integer',
        ]);

        $this->roomService->createRoom($hotelId, $validated);

        return redirect()->back()->with('success', 'Room added successfully.');
    }

    public function roomBookingList(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $bookings = $this->roomService->getRoomBookingList($hotelId, $request->get('search'), 20);

        return view('pages.room-booking-list', compact('bookings'));
    }

    public function roomStatus(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $roomStatuses = $this->roomService->getRoomStatuses($hotelId);

        return view('pages.room-status', compact('roomStatuses'));
    }

    public function floorPlans(): View
    {
        $hotelId = $this->getHotelId();
        $floorPlans = $this->roomService->getFloorPlans($hotelId);

        return view('pages.floor-plan-list', compact('floorPlans'));
    }

    public function storeFloorPlan(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'floor_name' => 'required|string|max:100',
            'floor_number' => 'nullable|integer',
            'number_of_rooms' => 'nullable|integer',
            'start_room_number' => 'nullable|string|max:50',
        ]);

        $this->roomService->createFloorPlan($hotelId, $validated);

        return redirect()->back()->with('success', 'Floor created successfully.');
    }

    public function assignFloor(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'floor_name' => 'required|string|max:100',
            'number_of_rooms' => 'required|integer',
            'start_room_number' => 'required|string|max:50',
        ]);

        $this->roomService->assignFloor($hotelId, $validated);

        return redirect()->back()->with('success', 'Floor assigned successfully.');
    }

    public function roomImages(): View
    {
        $hotelId = $this->getHotelId();
        $images = $this->roomService->getRoomImages($hotelId);
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('pages.room-image', compact('images', 'roomTypes'));
    }

    public function storeRoomImage(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'room_type_id' => 'nullable|integer',
            'room_id' => 'nullable|integer',
            'image_url' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:255',
        ]);

        $this->roomService->createRoomImage($hotelId, $validated);

        return redirect()->back()->with('success', 'Room image uploaded successfully.');
    }

    public function checkinOut(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $roomId = $request->get('room_id') ? (int) $request->get('room_id') : null;
        $data = $this->roomService->getCheckinOutData($hotelId, $roomId);

        return view('pages.checkin-out', $data);
    }
}
