<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;

class RoomAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth()->id();
        $properties = Property::where('vendor_id', $vendorId)->with('rooms')->get();

        $selectedRoomId = $request->query('room_id');
        $selectedRoom   = null;
        $availabilities = collect();
        $startDate      = Carbon::now()->startOfDay();
        $endDate        = Carbon::now()->addDays(30)->endOfDay();

        if ($selectedRoomId) {
            $selectedRoom = Room::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
                ->where('id', $selectedRoomId)
                ->first();

            if ($selectedRoom) {
                $availabilities = RoomAvailability::where('room_id', $selectedRoom->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get()
                    ->keyBy(fn($item) => $item->date->format('Y-m-d'));
            }
        } elseif ($properties->isNotEmpty() && $properties->first()->rooms->isNotEmpty()) {
            $selectedRoom = $properties->first()->rooms->first();
            $availabilities = RoomAvailability::where('room_id', $selectedRoom->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(fn($item) => $item->date->format('Y-m-d'));
        }

        return view('vendor.rooms.availability', compact('properties', 'selectedRoom', 'availabilities', 'startDate', 'endDate'));
    }

    public function updateRange(Request $request)
    {
        $vendorId = auth()->id();
        $validated = $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'nullable|numeric|min:0',
            'is_blocked' => 'nullable|boolean',
        ]);

        $room = Room::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('id', $validated['room_id'])
            ->firstOrFail();

        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
        $isBlocked = $request->has('is_blocked');

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            RoomAvailability::updateOrCreate(
                [
                    'room_id' => $room->id,
                    'date'    => $date->format('Y-m-d'),
                ],
                [
                    'price'      => $validated['price'] ?: null,
                    'is_blocked' => $isBlocked,
                ]
            );
        }

        return back()->with('success', 'Room availability and pricing updated successfully for selected date range!');
    }
}
