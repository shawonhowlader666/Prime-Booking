<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $hotel = Hotel::first();
        $hotelId = $hotel->id ?? 1;

        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('floor'), fn($q) => $q->where('floor', $request->floor))
            ->orderBy('room_number')
            ->get();

        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('rooms.index', compact('hotel', 'rooms', 'roomTypes'));
    }

    public function status(): View
    {
        return view('rooms.status');
    }

    public function facilities(): View
    {
        return view('rooms.facilities');
    }

    public function facilityDetails(): View
    {
        return view('rooms.facility-details');
    }

    public function sizes(): View
    {
        return view('rooms.sizes');
    }

    public function beds(): View
    {
        return view('rooms.beds');
    }

    public function bookingTypes(): View
    {
        return view('rooms.booking-types');
    }

    public function commissions(): View
    {
        return view('rooms.commissions');
    }

    public function complementary(): View
    {
        return view('rooms.complementary');
    }

    public function floorPlans(): View
    {
        return view('rooms.floor-plans');
    }

    public function images(): View
    {
        return view('rooms.images');
    }

    public function promocodes(): View
    {
        return view('rooms.promocodes');
    }
}
