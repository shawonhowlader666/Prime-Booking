<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;

/**
 * VendorRoomController — Manage room types for vendor's owned properties
 */
class VendorRoomController extends Controller
{
    private function vendorId(): int
    {
        $id = auth()->id();
        abort_unless($id, 403, 'Unauthorized vendor access.');
        return $id;
    }

    /** List rooms for vendor property with real-time KPI metrics */
    public function index($propertyId)
    {
        $vendorId = $this->vendorId();

        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $vendorId)
            ->with(['rooms' => function($q) {
                $q->orderBy('price_per_night', 'asc');
            }])
            ->firstOrFail();

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

        return view('vendor.rooms.index', compact('property', 'rooms', 'stats'));
    }

    /** Store new room for vendor property */
    public function store(Request $request, $propertyId)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $request->validate([
            'name'            => 'required|string|max:255',
            'bed_type'        => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_adults'      => 'required|integer|min:1',
            'max_children'    => 'nullable|integer|min:0',
            'total_rooms'     => 'nullable|integer|min:1',
        ]);

        $facilities = (array) ($request->amenities ?? []);
        if ($request->facilities_text) {
            $customFac = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
            $facilities = array_unique(array_merge($facilities, $customFac));
        }

        $bathroomFeatures = (array) ($request->bathroom_features ?? []);

        Room::create([
            'property_id'        => $property->id,
            'name'               => $request->name,
            'bed_type'           => $request->bed_type,
            'view_type'          => $request->view_type ?? 'City View',
            'bathroom_count'     => (int) ($request->bathroom_count ?? 1),
            'bathroom_features'  => array_values($bathroomFeatures),
            'smoking_policy'     => $request->smoking_policy ?? 'Non-Smoking',
            'balcony_type'       => $request->balcony_type ?? 'Private Balcony',
            'extra_bed_allowed'  => $request->boolean('extra_bed_allowed'),
            'price_per_night'    => (float) $request->price_per_night,
            'room_size_sqm'      => $request->room_size_sqm ? (int) $request->room_size_sqm : null,
            'max_adults'         => (int) $request->max_adults,
            'max_children'       => (int) ($request->max_children ?? 1),
            'max_guests'         => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'total_rooms'        => (int) ($request->total_rooms ?? 10),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'         => array_values($facilities),
        ]);

        return redirect()
            ->route('vendor.rooms.index', $property->id)
            ->with('success', '✅ New room category added successfully!');
    }

    /** Update room type */
    public function update(Request $request, $propertyId, $roomId)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $room = Room::where('property_id', $property->id)->findOrFail($roomId);

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

        $room->update([
            'name'               => $request->name,
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
            'facilities'         => array_values($facilities),
        ]);

        $room->update([
            'name'               => $request->name,
            'bed_type'           => $request->bed_type ?? $room->bed_type,
            'price_per_night'    => (float) $request->price_per_night,
            'room_size_sqm'      => $request->room_size_sqm ? (int) $request->room_size_sqm : $room->room_size_sqm,
            'max_adults'         => (int) $request->max_adults,
            'max_children'       => (int) ($request->max_children ?? $room->max_children),
            'max_guests'         => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'total_rooms'        => (int) ($request->total_rooms ?? $room->total_rooms),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'          => array_values($facilities) ?: ($room->facilities ?? []),
        ]);

        return redirect()
            ->route('vendor.rooms.index', $property->id)
            ->with('success', '"' . $room->name . '" updated successfully!');
    }

    /** Delete room type */
    public function destroy($propertyId, $roomId)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $room = Room::where('property_id', $property->id)->findOrFail($roomId);
        $name = $room->name;
        $room->delete();

        return redirect()
            ->route('vendor.rooms.index', $property->id)
            ->with('success', '"' . $name . '" room type deleted.');
    }
}
