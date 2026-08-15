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

        // Parse facilities from checkboxes and textarea
        $facilities = (array) ($request->amenities ?? []);
        if ($request->facilities_text) {
            $customFac = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
            $facilities = array_unique(array_merge($facilities, $customFac));
        }
        $bathroomFeatures = (array) ($request->bathroom_features ?? []);

        $roomImages = [];
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/rooms'), $filename);
            $roomImages = ['/uploads/rooms/' . $filename];
        } elseif ($request->filled('image_url')) {
            $roomImages = array_values(array_filter(array_map('trim', explode("\n", $request->image_url))));
        }

        Room::create([
            'property_id'        => $property->id,
            'name'               => $request->name,
            'room_type'          => $request->room_type ?? 'deluxe',
            'bed_type'           => $request->bed_type,
            'view_type'          => $request->view_type ?? 'City Skyline View',
            'bathroom_count'     => (int) ($request->bathroom_count ?? 1),
            'bathroom_features'  => array_values($bathroomFeatures),
            'smoking_policy'     => $request->smoking_policy ?? 'Non-Smoking',
            'balcony_type'       => $request->balcony_type ?? 'Private Balcony',
            'extra_bed_allowed'  => $request->boolean('extra_bed_allowed'),
            'price_per_night'    => (float) $request->price_per_night,
            'max_adults'         => (int) $request->max_adults,
            'max_children'       => (int) ($request->max_children ?? 1),
            'max_guests'         => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'room_size_sqm'      => $request->room_size_sqm ? (int) $request->room_size_sqm : null,
            'total_rooms'        => (int) ($request->total_rooms ?? 10),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'         => array_values($facilities),
            'images'             => $roomImages,
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

        $facilities = (array) ($request->amenities ?? []);
        if ($request->facilities_text) {
            $customFac = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
            $facilities = array_unique(array_merge($facilities, $customFac));
        }

        $bathroomFeatures = (array) ($request->bathroom_features ?? ($room->bathroom_features ?? []));

        $roomImages = is_array($room->images) ? $room->images : [];
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/rooms'), $filename);
            $roomImages = ['/uploads/rooms/' . $filename];
        } elseif ($request->filled('image_url')) {
            $roomImages = array_values(array_filter(array_map('trim', explode("\n", $request->image_url))));
        }

        $room->update([
            'name'               => $request->name,
            'room_type'          => $request->room_type ?? ($room->room_type ?? 'deluxe'),
            'bed_type'           => $request->bed_type ?? $room->bed_type,
            'view_type'          => $request->view_type ?? $room->view_type,
            'bathroom_count'     => (int) ($request->bathroom_count ?? $room->bathroom_count),
            'bathroom_features'  => array_values($bathroomFeatures),
            'smoking_policy'     => $request->smoking_policy ?? $room->smoking_policy,
            'balcony_type'       => $request->balcony_type ?? $room->balcony_type,
            'extra_bed_allowed'  => $request->has('extra_bed_allowed') ? $request->boolean('extra_bed_allowed') : $room->extra_bed_allowed,
            'price_per_night'    => (float) $request->price_per_night,
            'room_size_sqm'      => $request->room_size_sqm ? (int) $request->room_size_sqm : $room->room_size_sqm,
            'max_adults'         => (int) $request->max_adults,
            'max_children'       => (int) ($request->max_children ?? $room->max_children),
            'max_guests'         => (int) $request->max_adults + (int) ($request->max_children ?? $room->max_children),
            'total_rooms'        => (int) ($request->total_rooms ?? $room->total_rooms),
            'breakfast_included' => $request->has('breakfast_included') ? $request->boolean('breakfast_included') : $room->breakfast_included,
            'free_cancellation'  => $request->has('free_cancellation') ? $request->boolean('free_cancellation') : $room->free_cancellation,
            'facilities'         => array_values($facilities) ?: ($room->facilities ?? []),
            'images'             => $roomImages,
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
