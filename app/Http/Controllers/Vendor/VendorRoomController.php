<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function store(Request $request, $propertyId, \App\Services\Property\RoomFormService $form)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $request->validate($form->rules());

        Room::create($form->attributes($request, null) + ['property_id' => $property->id]);

        return redirect()
            ->route('vendor.rooms.index', $property->id)
            ->with('success', 'Room category added successfully!');
    }

    public function edit($propertyId, $roomId)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $room = Room::where('property_id', $property->id)->findOrFail($roomId);

        return view('vendor.rooms.edit', compact('property', 'room'));
    }

    /** Update room type */
    public function update(Request $request, $propertyId, $roomId, \App\Services\Property\RoomFormService $form)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();

        $room = Room::where('property_id', $property->id)->findOrFail($roomId);

        $request->validate($form->rules($room));

        $room->update($form->attributes($request, $room));

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
            ->route('vendor.rooms.index', $property->id)
            ->with('success', '"' . $name . '" room type deleted.');
    }
}
