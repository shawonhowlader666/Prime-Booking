<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\HousekeepingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    public function __construct(
        protected HousekeepingService $housekeepingService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function assignRoom(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $rooms = Room::with('roomType')->where('hotel_id', $hotelId)->get();
        $housekeepers = User::where('hotel_id', $hotelId)->get();
        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('pages.housekeeping-assign-room', compact('rooms', 'housekeepers', 'roomTypes'));
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'priority' => 'nullable|string|in:LOW,MEDIUM,HIGH,URGENT',
        ]);

        foreach ($validated['room_ids'] as $roomId) {
            $this->housekeepingService->assignTask($hotelId, [
                'room_id' => $roomId,
                'assigned_to_user_id' => $validated['assigned_to_user_id'],
                'priority' => $validated['priority'] ?? 'MEDIUM',
            ]);
        }

        return redirect()->back()->with('success', 'Rooms successfully assigned for housekeeping!');
    }

    public function roomCleaning(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $tasks = $this->housekeepingService->getTasks($hotelId, $request->get('search'), $request->get('status'), 20);

        return view('pages.housekeeping-roomcleaning', compact('tasks'));
    }

    public function completeCleaning(int $taskId): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->housekeepingService->completeTask($hotelId, $taskId);

        return redirect()->back()->with('success', 'Room marked Clean and task completed!');
    }

    public function checklist(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $checklists = $this->housekeepingService->getChecklists($hotelId, $request->get('type'));

        return view('pages.housekeeping-Checklist', compact('checklists'));
    }

    public function storeChecklist(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'checkpoint' => 'required|string|max:150',
            'type' => 'required|string|max:50',
        ]);

        $this->housekeepingService->createChecklist($hotelId, $validated);

        return redirect()->back()->with('success', 'Checklist item added successfully!');
    }

    public function updateChecklist(Request $request, int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'checkpoint' => 'required|string|max:150',
            'type' => 'required|string|max:50',
        ]);

        $this->housekeepingService->updateChecklist($hotelId, $id, $validated);

        return redirect()->back()->with('success', 'Checklist item updated successfully!');
    }

    public function destroyChecklist(int $id): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $this->housekeepingService->deleteChecklist($hotelId, $id);

        return redirect()->back()->with('success', 'Checklist item deleted.');
    }

    public function qrList(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $rooms = Room::where('hotel_id', $hotelId)->get();

        return view('pages.housekeeping-qrlist', compact('rooms'));
    }

    public function report(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $reports = $this->housekeepingService->getStaffReport($hotelId);

        return view('pages.housekeeping-report', compact('reports'));
    }
}
