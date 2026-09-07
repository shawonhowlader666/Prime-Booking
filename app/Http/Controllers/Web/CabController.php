<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cab;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Services\CabService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CabController extends Controller
{
    public function __construct(
        protected CabService $cabService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function index(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $cabs = $this->cabService->getCabs($hotelId, $request->get('search'), 20);

        return view('pages.cab-list', compact('cabs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'car_name' => 'required|string|max:100',
            'car_number' => 'required|string|max:50',
            'car_type' => 'required|string|max:50',
            'fuel_type' => 'required|string|max:50',
            'seat_capacity' => 'required|integer|min:1|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:30',
            'base_fare' => 'nullable|numeric|min:0',
        ]);

        $this->cabService->createCab($hotelId, $validated);

        return redirect()->back()->with('success', 'Cab added to fleet successfully!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'car_name' => 'required|string|max:100',
            'car_number' => 'required|string|max:50',
            'car_type' => 'required|string|max:50',
            'fuel_type' => 'required|string|max:50',
            'seat_capacity' => 'required|integer|min:1|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:30',
            'base_fare' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:AVAILABLE,ON_TRIP,MAINTENANCE',
        ]);

        $this->cabService->updateCab($hotelId, $id, $validated);

        return redirect()->back()->with('success', 'Cab details updated successfully!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->cabService->deleteCab($hotelId, $id);

        return redirect()->back()->with('success', 'Cab removed from fleet.');
    }

    public function bookings(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $bookings = $this->cabService->getBookings($hotelId, $request->get('search'), 20);
        $cabs = Cab::where('hotel_id', $hotelId)->where('is_active', true)->get();
        $reservations = Reservation::with('guest')->where('hotel_id', $hotelId)->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])->get();

        return view('pages.cab-booking', compact('bookings', 'cabs', 'reservations'));
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'cab_id' => 'required|exists:cabs,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'driver_name' => 'nullable|string|max:100',
            'transport_type' => 'required|string|in:Pick Up,Drop,Sightseeing,Full Day,PICKUP,DROP',
            'booking_time' => 'nullable|string',
            'booking_date' => 'nullable|date',
            'fare_amount' => 'nullable|numeric|min:0',
            'reservation_id' => 'nullable|exists:reservations,id',
            'post_to_room' => 'nullable|boolean',
        ]);

        $this->cabService->bookCab($hotelId, $validated);

        return redirect()->back()->with('success', 'Cab booking created successfully!');
    }
}
