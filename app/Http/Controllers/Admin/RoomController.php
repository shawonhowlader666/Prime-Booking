<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;

/**
 * Room Types Management — Admin can add/edit/delete room types for any property
 * Accessible at /admin/properties/{id}/rooms
 */
class RoomController extends Controller
{
    /** GET /admin/properties/{propertyId}/rooms — List rooms for a property */
    public function index($propertyId)
    {
        $property = Property::with('rooms')->findOrFail($propertyId);
        return view('admin.rooms.index', compact('property'));
    }

    /** GET /admin/properties/{propertyId}/rooms/create — Show create form */
    public function create($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        return view('admin.rooms.create', compact('property'));
    }

    /** POST /admin/properties/{propertyId}/rooms — Store new room */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);

        $request->validate([
            'name'            => 'required|string|max:255',
            'bed_type'        => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_adults'      => 'required|integer|min:1',
            'max_children'    => 'nullable|integer|min:0',
            'room_size_sqm'   => 'nullable|integer|min:1',
            'total_rooms'     => 'nullable|integer|min:1',
        ]);

        // Parse facilities from textarea
        $facilities = [];
        if ($request->facilities_text) {
            $facilities = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
        }

        Room::create([
            'property_id'       => $property->id,
            'name'              => $request->name,
            'bed_type'          => $request->bed_type,
            'price_per_night'   => (float) $request->price_per_night,
            'max_adults'        => (int) $request->max_adults,
            'max_children'      => (int) ($request->max_children ?? 1),
            'max_guests'        => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'room_size_sqm'     => $request->room_size_sqm ? (int) $request->room_size_sqm : null,
            'total_rooms'       => (int) ($request->total_rooms ?? 10),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'         => array_values($facilities),
        ]);

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
    public function update(Request $request, $propertyId, $roomId)
    {
        $room = Room::where('property_id', $propertyId)->findOrFail($roomId);

        $request->validate([
            'name'            => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'max_adults'      => 'required|integer|min:1',
        ]);

        $facilities = [];
        if ($request->facilities_text) {
            $facilities = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
        }

        $room->update([
            'name'              => $request->name,
            'bed_type'          => $request->bed_type ?? $room->bed_type,
            'price_per_night'   => (float) $request->price_per_night,
            'max_adults'        => (int) $request->max_adults,
            'max_children'      => (int) ($request->max_children ?? $room->max_children),
            'max_guests'        => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'room_size_sqm'     => $request->room_size_sqm ? (int) $request->room_size_sqm : $room->room_size_sqm,
            'total_rooms'       => (int) ($request->total_rooms ?? $room->total_rooms),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'         => array_values($facilities) ?: ($room->facilities ?? []),
        ]);

        return redirect()
            ->route('admin.rooms.index', $propertyId)
            ->with('success', '"' . $room->name . '" updated successfully!');
    }

    /** DELETE /admin/properties/{propertyId}/rooms/{roomId} */
    public function destroy($propertyId, $roomId)
    {
        $room = Room::where('property_id', $propertyId)->findOrFail($roomId);
        $name = $room->name;
        $room->delete();
        return redirect()
            ->route('admin.rooms.index', $propertyId)
            ->with('success', '"' . $name . '" room type deleted.');
    }
}
