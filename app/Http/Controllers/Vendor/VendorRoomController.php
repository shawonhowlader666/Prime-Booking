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
        return auth()->id() ?? 1;
    }

    /** List rooms for vendor property */
    public function index($propertyId)
    {
        $property = Property::where('id', $propertyId)
            ->where('vendor_id', $this->vendorId())
            ->with('rooms')
            ->firstOrFail();

        return view('vendor.rooms.index', compact('property'));
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

        $facilities = [];
        if ($request->facilities_text) {
            $facilities = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
        }

        Room::create([
            'property_id'        => $property->id,
            'name'               => $request->name,
            'bed_type'           => $request->bed_type,
            'price_per_night'    => (float) $request->price_per_night,
            'max_adults'         => (int) $request->max_adults,
            'max_children'       => (int) ($request->max_children ?? 1),
            'max_guests'         => (int) $request->max_adults + (int) ($request->max_children ?? 1),
            'total_rooms'        => (int) ($request->total_rooms ?? 10),
            'breakfast_included' => $request->boolean('breakfast_included'),
            'free_cancellation'  => $request->boolean('free_cancellation'),
            'facilities'          => array_values($facilities),
        ]);

        return redirect()
            ->route('vendor.rooms.index', $property->id)
            ->with('success', '✅ New room type added successfully!');
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

        $facilities = [];
        if ($request->facilities_text) {
            $facilities = array_filter(
                array_map('trim', explode("\n", $request->facilities_text)),
                fn($line) => !empty($line)
            );
        }

        $room->update([
            'name'               => $request->name,
            'bed_type'           => $request->bed_type ?? $room->bed_type,
            'price_per_night'    => (float) $request->price_per_night,
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
