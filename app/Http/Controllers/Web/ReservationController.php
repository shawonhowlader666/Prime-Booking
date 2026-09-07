<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $hotel = Hotel::first();
        $hotelId = $hotel->id ?? 1;

        $reservations = Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->where('hotel_id', $hotelId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where('reservation_code', 'like', "%{$s}%")
                  ->orWhereHas('guest', fn($g) => $g->where('first_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15);

        $rooms = Room::where('hotel_id', $hotelId)->where('is_active', true)->get();
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('rooms.reservations', compact('hotel', 'reservations', 'rooms', 'roomTypes'));
    }

    public function checkinOut(): View
    {
        return view('rooms.checkin-out');
    }
}
