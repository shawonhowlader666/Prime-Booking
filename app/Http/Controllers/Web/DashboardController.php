<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    protected function getActiveHotel(Request $request): Hotel
    {
        return $request->user()?->hotel ?? Hotel::first() ?? new Hotel();
    }

    public function index(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $hotelId = $hotel->id ?? 1;

        $kpis = $this->inventoryService->getDashboardKPIs($hotelId);

        $recentBookings = Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->latest()
            ->limit(8)
            ->get();

        $rooms = Room::with('roomType')
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get();

        $roomTypes = RoomType::all();

        return view('pages.index', compact('hotel', 'kpis', 'recentBookings', 'rooms', 'roomTypes'));
    }

    public function matrix(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $hotelId = $hotel->id ?? 1;
        $startDate = $request->get('start_date', now()->toDateString());
        $days = $request->integer('days', 14);

        $matrixData = $this->inventoryService->getVisualMatrix($hotelId, $startDate, $days);
        $roomTypes = RoomType::all();

        return view('pages.room-booking-list', compact('hotel', 'matrixData', 'startDate', 'days', 'roomTypes'));
    }

    public function rooms(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);

        $rooms = Room::with('roomType')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('floor'), fn($q) => $q->where('floor', $request->floor))
            ->orderBy('room_number')
            ->get();

        $roomTypes = RoomType::all();

        return view('pages.room-list', compact('hotel', 'rooms', 'roomTypes'));
    }

    public function reservations(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);

        $reservations = Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where('reservation_code', 'like', "%{$s}%")
                  ->orWhereHas('guest', fn($g) => $g->where('first_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15);

        $rooms = Room::where('is_active', true)->get();
        $roomTypes = RoomType::all();

        return view('pages.room-booking-list', compact('hotel', 'reservations', 'rooms', 'roomTypes'));
    }

    public function folioInvoice(int $id, Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $folio = Folio::with(['reservation.guest', 'reservation.room', 'reservation.roomType', 'items.postedBy', 'payments.processedBy'])
            ->where('id', $id)
            ->firstOrFail();

        return view('pages.transaction', compact('hotel', 'folio'));
    }

    public function guests(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);

        $guests = Guest::withCount('reservations')
            ->with(['reservations' => fn($q) => $q->latest()->limit(3)])
            ->latest()
            ->paginate(15);

        return view('pages.index-two', compact('hotel', 'guests'));
    }

    public function housekeeping(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);

        $tasks = HousekeepingTask::with(['room.roomType', 'assignedTo'])
            ->latest()
            ->get();

        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        return view('pages.housekeeping-roomcleaning', compact('hotel', 'tasks', 'rooms'));
    }

    public function cabBooking(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.cab-booking', compact('hotel'));
    }

    public function employees(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $employees = User::where('hotel_id', $hotel->id)->get();
        return view('pages.employees-all', compact('hotel', 'employees'));
    }

    public function leaveTracking(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.employees-leave', compact('hotel'));
    }

    public function attendance(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $employees = User::where('hotel_id', $hotel->id)->get();
        return view('pages.employees-attendance', compact('hotel', 'employees'));
    }

    public function departments(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.employees-departments', compact('hotel'));
    }

    public function loans(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.employees-loan', compact('hotel'));
    }

    public function appraisal(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.employees-appraisal', compact('hotel'));
    }

    public function dutyChanges(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $employees = User::where('hotel_id', $hotel->id)->get();
        return view('pages.duty-assigned-list', compact('hotel', 'employees'));
    }

    public function syncHub(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $roomTypes = RoomType::withCount('rooms')->get();

        return view('pages.tuning', compact('hotel', 'roomTypes'));
    }

    public function tuning(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.tuning', compact('hotel'));
    }

    public function commonSettings(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        return view('pages.common-settings', compact('hotel'));
    }

    public function saveCommonSettings(Request $request)
    {
        $hotel = $this->getActiveHotel($request);
        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        if ($hotel && $hotel->exists) {
            $hotel->update(array_filter($validated));
        }

        return redirect()->back()->with('success', 'Property common settings updated successfully.');
    }

    public function hotels(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $hotels = Hotel::all();
        return view('pages.hotels', compact('hotel', 'hotels'));
    }

    public function search(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $query = $request->get('q', '');

        $results = [];
        if (!empty($query)) {
            $results['guests'] = Guest::where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)->get();
            $results['reservations'] = Reservation::where('reservation_code', 'like', "%{$query}%")
                ->limit(5)->get();
            $results['rooms'] = Room::where('room_number', 'like', "%{$query}%")
                ->limit(5)->get();
        }

        return view('pages.search', compact('hotel', 'query', 'results'));
    }

    public function indexTwo(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $hotelId = $hotel->id ?? 1;

        $kpis = $this->inventoryService->getDashboardKPIs($hotelId);
        $recentBookings = Reservation::with(['guest', 'room', 'roomType', 'folio'])
            ->latest()
            ->limit(8)
            ->get();

        $rooms = Room::with('roomType')
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get();

        return view('pages.index-two', compact('hotel', 'kpis', 'recentBookings', 'rooms'));
    }

    public function hotelDashboard(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $hotelId = $hotel->id ?? 1;
        $kpis = $this->inventoryService->getDashboardKPIs($hotelId);
        $recentBookings = Reservation::with(['guest', 'room', 'roomType', 'folio'])->latest()->limit(8)->get();
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('room_number')->get();

        return view('pages.hotel-dashboard', compact('hotel', 'kpis', 'recentBookings', 'rooms'));
    }

    public function indexHr(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $employees = User::where('hotel_id', $hotel->id)->latest()->get();
        $departments = \App\Models\Department::all();

        return view('pages.index-hr', compact('hotel', 'employees', 'departments'));
    }
}
