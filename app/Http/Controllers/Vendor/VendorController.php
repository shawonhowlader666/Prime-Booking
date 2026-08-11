<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /** Vendor Dashboard — shows only their own properties & bookings */
    public function dashboard()
    {
        // In real app: $vendorId = auth()->id(); for now use 1 or null
        $vendorId = 1;

        $vendorStats = [
            'total_properties' => Property::where('vendor_id', $vendorId)->count(),
            'active_listings'  => Property::where('vendor_id', $vendorId)->where('status', 'active')->count(),
            'total_bookings'   => Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))->count(),
            'total_earnings'   => 420000,
            'pending_payout'   => 85000,
        ];

        $properties    = Property::where('vendor_id', $vendorId)->latest()->take(6)->get();
        $recentBookings = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
                                  ->with('property:id,name,city')
                                  ->latest()->take(5)->get();

        return view('vendor.dashboard', compact('vendorStats', 'properties', 'recentBookings'));
    }

    /** Show add new property form */
    public function createProperty()
    {
        return view('vendor.create-property');
    }

    /** Store a new vendor property (submitted as pending/inactive for admin review) */
    public function storeProperty(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:hotel,houseboat,homestay,apartment,resort',
            'city'            => 'required|string',
            'star_rating'     => 'required|integer|min:1|max:5',
            'address'         => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'description'     => 'required|string',
            'primary_image'   => 'required|url',
            'video_url'       => 'nullable|url',
        ]);

        // Parse gallery images from textarea
        $galleryImages = [];
        if ($request->gallery_images) {
            $galleryImages = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
        }

        // Draft vs Submit for Review
        $status = $request->action === 'draft' ? 'inactive' : 'inactive'; // always pending admin approval

        Property::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . time(),
            'type'            => $request->type,
            'city'            => $request->city,
            'star_rating'     => (int) $request->star_rating,
            'address'         => $request->address,
            'price_per_night' => (float) $request->price_per_night,
            'original_price'  => $request->original_price ? (float) $request->original_price : null,
            'description'     => $request->description,
            'primary_image'   => $request->primary_image,
            'video_url'       => $request->video_url,
            'images'          => array_values($galleryImages),
            'amenities'       => $request->amenities ?? [],
            'is_featured'     => false,
            'status'          => $status,
            'vendor_id'       => 1, // auth()->id() in real app
        ]);

        $msg = $request->action === 'publish'
            ? 'Property submitted for admin review! It will go live once approved.'
            : 'Property saved as draft.';

        return redirect()->route('vendor.dashboard')->with('success', $msg);
    }

    /** Show edit form for vendor's own property */
    public function editProperty($id)
    {
        $property    = Property::where('id', $id)->firstOrFail();
        $galleryText = implode("\n", $property->images ?? []);
        return view('vendor.edit-property', compact('property', 'galleryText'));
    }

    /** Update vendor's own property */
    public function updateProperty(Request $request, $id)
    {
        $property = Property::where('id', $id)->firstOrFail();

        $request->validate([
            'name'            => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'description'     => 'required|string',
            'primary_image'   => 'nullable|url',
            'video_url'       => 'nullable|url',
        ]);

        $galleryImages = [];
        if ($request->gallery_images) {
            $galleryImages = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
        }

        $property->update([
            'name'            => $request->name,
            'type'            => $request->type ?? $property->type,
            'city'            => $request->city ?? $property->city,
            'star_rating'     => $request->star_rating ?? $property->star_rating,
            'address'         => $request->address ?? $property->address,
            'price_per_night' => (float) $request->price_per_night,
            'original_price'  => $request->original_price ? (float) $request->original_price : $property->original_price,
            'description'     => $request->description,
            'primary_image'   => $request->primary_image ?: $property->primary_image,
            'video_url'       => $request->video_url ?? $property->video_url,
            'images'          => array_values($galleryImages) ?: ($property->images ?? []),
            'amenities'       => $request->amenities ?? [],
        ]);

        return redirect()->route('vendor.dashboard')->with('success', '"' . $property->name . '" updated successfully!');
    }

    /** Toggle property active/inactive */
    public function togglePropertyStatus($id)
    {
        $property  = Property::where('id', $id)->firstOrFail();
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        return back()->with('success', 'Listing status changed to ' . ucfirst($newStatus) . '.');
    }

    /** Delete vendor's own property */
    public function destroyProperty($id)
    {
        $property = Property::where('id', $id)->firstOrFail();
        $name     = $property->name;
        $property->delete();
        return redirect()->route('vendor.dashboard')->with('success', '"' . $name . '" deleted successfully.');
    }
}
