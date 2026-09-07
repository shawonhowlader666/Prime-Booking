<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AmenityOrder;
use App\Models\AmenityService as AmenityServiceModel;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Services\AmenityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function __construct(
        protected AmenityService $amenityService
    ) {}

    private function getActiveHotel(Request $request): Hotel
    {
        $user = $request->user();
        if ($user && $user->hotel_id) {
            return Hotel::find($user->hotel_id) ?? Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
        }
        return Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
    }

    private function getCommonData(Request $request, string $category): array
    {
        $hotel = $this->getActiveHotel($request);
        $stats = $this->amenityService->getCategoryStats($hotel->id, $category);

        $activeReservations = Reservation::with(['guest', 'room'])
            ->where('hotel_id', $hotel->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->latest()
            ->get();

        $serviceIds = $stats['services']->pluck('id');

        $orders = AmenityOrder::with(['service', 'guest', 'room', 'reservation'])
            ->where('hotel_id', $hotel->id)
            ->whereIn('service_id', $serviceIds)
            ->latest()
            ->paginate(15);

        return [
            'hotel' => $hotel,
            'category' => $category,
            'services' => $stats['services'],
            'stats' => $stats,
            'activeReservations' => $activeReservations,
            'orders' => $orders,
        ];
    }

    public function jacuzzi(Request $request): View
    {
        $data = $this->getCommonData($request, 'JACUZZI');
        return view('pages.personalised-jacuzzi', $data);
    }

    public function swimmingPool(Request $request): View
    {
        $data = $this->getCommonData($request, 'SWIMMING_POOL');
        return view('pages.personalised-swimmingpool', $data);
    }

    public function spa(Request $request): View
    {
        $data = $this->getCommonData($request, 'SPA');
        return view('pages.personalised-spa', $data);
    }

    public function pedicure(Request $request): View
    {
        $data = $this->getCommonData($request, 'PEDICURE');
        return view('pages.personalised-pedicure', $data);
    }

    public function theater(Request $request): View
    {
        $data = $this->getCommonData($request, 'THEATER');
        return view('pages.personalised-theater', $data);
    }

    public function banquet(Request $request): View
    {
        $data = $this->getCommonData($request, 'BANQUET');
        return view('pages.personalised-banquet', $data);
    }

    public function settings(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $services = AmenityServiceModel::where('hotel_id', $hotel->id)->latest()->get();

        return view('pages.personalised-settings', compact('hotel', 'services'));
    }

    public function bookService(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'service_id' => 'required|exists:amenity_services,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'booked_for_date' => 'required|date',
            'time_slot' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:ROOM_CHARGE,CASH,CARD',
            'notes' => 'nullable|string',
        ]);

        $reservation = null;
        $guestId = null;
        $roomId = null;

        if (!empty($validated['reservation_id'])) {
            $reservation = Reservation::find($validated['reservation_id']);
            $guestId = $reservation?->guest_id;
            $roomId = $reservation?->room_id;
        }

        $order = $this->amenityService->bookAmenity(
            $hotel->id,
            $validated['service_id'],
            $reservation?->id,
            $guestId,
            $roomId,
            $validated['booked_for_date'],
            $validated['time_slot'] ?? null,
            (int) $validated['quantity'],
            $validated['payment_method'],
            $validated['notes'] ?? null
        );

        $msg = "Service '{$order->service->name}' booked successfully!";
        if ($validated['payment_method'] === 'ROOM_CHARGE' && $reservation) {
            $msg .= " Total ৳" . number_format($order->total_amount, 2) . " posted directly to Room {$reservation->room->room_number} folio.";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'category' => 'required|in:JACUZZI,SPA,SWIMMING_POOL,PEDICURE,THEATER,BANQUET',
            'name' => 'required|string|max:150',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $service = AmenityServiceModel::create([
            'hotel_id' => $hotel->id,
            'category' => $validated['category'],
            'name' => $validated['name'],
            'duration_minutes' => $validated['duration_minutes'],
            'price' => $validated['price'],
            'tax_percentage' => $validated['tax_percentage'] ?? 15.00,
            'capacity' => $validated['capacity'] ?? 1,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Service '{$service->name}' configured successfully!");
    }
}
