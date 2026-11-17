<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Room Types Management — Admin can add/edit/delete room types for any property
 * Accessible at /admin/properties/{id}/rooms
 */
class RoomController extends Controller
{
    /** GET /admin/properties/{propertyId}/rooms — List rooms for a property */
    public function index($propertyId)
    {
        $property = Property::with(['rooms' => function($q) {
            $q->orderBy('price_per_night', 'asc');
        }])->findOrFail($propertyId);

        $rooms = $property->rooms;
        $totalCategories = $rooms->count();
        $totalUnits      = (int) $rooms->sum('total_rooms');
        $avgPrice        = $totalCategories > 0 ? (float) $rooms->avg('price_per_night') : 0;
        $minPrice        = $totalCategories > 0 ? (float) $rooms->min('price_per_night') : 0;
        $maxPrice        = $totalCategories > 0 ? (float) $rooms->max('price_per_night') : 0;

        $stats = [
            'total_categories' => $totalCategories,
            'total_units'      => $totalUnits,
            'avg_price'        => $avgPrice,
            'min_price'        => $minPrice,
            'max_price'        => $maxPrice,
        ];

        return view('admin.rooms.index', compact('property', 'rooms', 'stats'));
    }

    /** GET /admin/properties/{propertyId}/rooms/create — Show create form */
    public function create($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        return view('admin.rooms.create', compact('property'));
    }

    /** POST /admin/properties/{propertyId}/rooms — Store new room */
    public function store(Request $request, $propertyId, \App\Services\Property\RoomFormService $form)
    {
        $property = Property::findOrFail($propertyId);

        $request->validate($form->rules());

        Room::create($form->attributes($request, null) + ['property_id' => $property->id]);

        return redirect()
            ->route('admin.rooms.index', $property->id)
            ->with('success', 'Room type added successfully!');
    }

    /** GET /admin/properties/{propertyId}/rooms/{roomId}/edit */
    public function edit($propertyId, $roomId)
    {
        $property = Property::findOrFail($propertyId);
        $room     = Room::where('property_id', $propertyId)->findOrFail($roomId);
        $facilitiesText = implode("\n", $room->facilities ?? []);
        return view('admin.rooms.edit', compact('property', 'room', 'facilitiesText'));
    }

    /** PUT /admin/properties/{propertyId}/rooms/{roomId} */
    public function update(Request $request, $propertyId, $roomId, \App\Services\Property\RoomFormService $form)
    {
        $room = Room::where('property_id', $propertyId)->findOrFail($roomId);

        $request->validate($form->rules($room));

        $room->update($form->attributes($request, $room));

        return redirect()
            ->route('admin.rooms.index', $propertyId)
            ->with('success', '"' . $room->name . '" updated successfully!');
    }

    /** DELETE /admin/properties/{propertyId}/rooms/{roomId} */
    public function destroy($propertyId, $roomId)
    {
        $room = Room::where('property_id', $propertyId)->findOrFail($roomId);

        // Security Guard: Prevent deleting room types with active or upcoming reservations
        $activeBookingsCount = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_out', '>=', now()->toDateString())
            ->count();

        if ($activeBookingsCount > 0) {
            return back()->with('error', "Cannot delete room category \"{$room->name}\". There are {$activeBookingsCount} active or upcoming reservation(s) booked for this room. Please reassign or fulfill the reservations first.");
        }

        $name = $room->name;
        $room->delete();
        return redirect()
            ->route('admin.rooms.index', $propertyId)
            ->with('success', '"' . $name . '" room type deleted.');
    }
}
