<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\RoomConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomConfigController extends Controller
{
    public function __construct(
        protected RoomConfigService $configService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    // 1. Promocodes
    public function promocodes(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $promocodes = $this->configService->getPromocodes($hotelId, $request->get('search'), 20);
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('pages.promocode-list', compact('promocodes', 'roomTypes'));
    }

    public function storePromocode(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'room_type_id' => 'nullable|exists:room_types,id',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
        ]);

        $this->configService->createPromocode($hotelId, $validated);

        return redirect()->back()->with('success', 'Promocode created successfully!');
    }

    public function destroyPromocode(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deletePromocode($hotelId, $id);

        return redirect()->back()->with('success', 'Promocode removed.');
    }

    // 2. Facilities
    public function facilities(): View
    {
        $hotelId = $this->getHotelId();
        $facilities = $this->configService->getFacilities($hotelId);

        return view('pages.room-facilites-list', compact('facilities'));
    }

    public function storeFacility(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $this->configService->createFacility($hotelId, $validated);

        return redirect()->back()->with('success', 'Facility added successfully!');
    }

    public function destroyFacility(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteFacility($hotelId, $id);

        return redirect()->back()->with('success', 'Facility removed.');
    }

    public function facilityDetails(): View
    {
        $hotelId = $this->getHotelId();
        $facilities = $this->configService->getFacilities($hotelId);

        return view('pages.facilites-details', compact('facilities'));
    }

    // 3. Bed Types
    public function beds(): View
    {
        $hotelId = $this->getHotelId();
        $beds = $this->configService->getBeds($hotelId);

        return view('pages.bed-list', compact('beds'));
    }

    public function storeBed(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $this->configService->createBed($hotelId, $validated);

        return redirect()->back()->with('success', 'Bed Type added successfully!');
    }

    public function destroyBed(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteBed($hotelId, $id);

        return redirect()->back()->with('success', 'Bed Type removed.');
    }

    // 4. Room Sizes
    public function sizes(): View
    {
        $hotelId = $this->getHotelId();
        $sizes = $this->configService->getSizes($hotelId);

        return view('pages.room-size', compact('sizes'));
    }

    public function storeSize(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'size_sqft' => 'nullable|numeric|min:0',
        ]);

        $this->configService->createSize($hotelId, $validated);

        return redirect()->back()->with('success', 'Room Size added successfully!');
    }

    public function destroySize(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteSize($hotelId, $id);

        return redirect()->back()->with('success', 'Room Size removed.');
    }

    // 5. Booking Types
    public function bookingTypes(): View
    {
        $hotelId = $this->getHotelId();
        $types = $this->configService->getBookingTypes($hotelId);

        return view('pages.booking-type-list', compact('types'));
    }

    public function storeBookingType(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'channel' => 'nullable|string|max:50',
        ]);

        $this->configService->createBookingType($hotelId, $validated);

        return redirect()->back()->with('success', 'Booking Type added successfully!');
    }

    public function destroyBookingType(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteBookingType($hotelId, $id);

        return redirect()->back()->with('success', 'Booking Type removed.');
    }

    // 6. Commissions
    public function commissions(): View
    {
        $hotelId = $this->getHotelId();
        $commissions = $this->configService->getCommissions($hotelId);

        return view('pages.commission-list', compact('commissions'));
    }

    public function storeCommission(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'agent_name' => 'required|string|max:100',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'channel' => 'nullable|string|max:50',
        ]);

        $this->configService->createCommission($hotelId, $validated);

        return redirect()->back()->with('success', 'Commission rate added successfully!');
    }

    public function destroyCommission(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteCommission($hotelId, $id);

        return redirect()->back()->with('success', 'Commission rate removed.');
    }

    // 7. Complementary
    public function complementary(): View
    {
        $hotelId = $this->getHotelId();
        $services = $this->configService->getComplementary($hotelId);

        return view('pages.complementary-list', compact('services'));
    }

    public function storeComplementary(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $this->configService->createComplementary($hotelId, $validated);

        return redirect()->back()->with('success', 'Complementary service added successfully!');
    }

    public function destroyComplementary(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->configService->deleteComplementary($hotelId, $id);

        return redirect()->back()->with('success', 'Complementary service removed.');
    }

    // 8. Floor Plans & Images
    public function floorPlans(): View
    {
        return view('pages.floor-plan-list');
    }

    public function roomImages(): View
    {
        return view('pages.room-image');
    }
}
