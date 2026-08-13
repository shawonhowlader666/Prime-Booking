<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::withCount('bookings')->latest();

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $properties = $query->paginate(15)->withQueryString();
        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        $vendors = User::where('role', 'vendor')->orderBy('name')->get();
        return view('admin.properties.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|string',
            'city'            => 'required|string',
            'star_rating'     => 'required|integer|min:1|max:5',
            'address'         => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'description'     => 'required|string',
            'primary_image'   => 'nullable|url',
        ]);

        // Parse gallery images from textarea (one per line)
        $galleryImages = [];
        if ($request->gallery_images) {
            $galleryImages = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
            $galleryImages = array_values($galleryImages);
        }

        // Determine publish status — "Save as Draft" button vs "Publish Live"
        $status = $request->action === 'draft' ? 'inactive' : 'active';

        Property::create([
            'name'             => $request->name,
            'slug'             => Str::slug($request->name) . '-' . time(),
            'type'             => $request->type,
            'city'             => $request->city,
            'star_rating'      => (int) $request->star_rating,
            'address'          => $request->address,
            'price_per_night'  => (float) $request->price_per_night,
            'original_price'   => $request->original_price ? (float) $request->original_price : null,
            'description'      => $request->description,
            'primary_image'    => $request->primary_image,
            'video_url'        => $request->video_url ?: null,
            'latitude'         => $request->latitude ? (float)$request->latitude : null,
            'longitude'        => $request->longitude ? (float)$request->longitude : null,
            'nearest_landmark' => $request->nearest_landmark ?: null,
            'images'           => array_values($galleryImages),
            'amenities'        => $request->amenities ?? [],
            'is_featured'      => $request->boolean('is_featured'),
            'status'           => $status,
            'vendor_id'        => $request->vendor_id ?: null,
        ]);

        $msg = $status === 'active' ? 'Property published live successfully!' : 'Property saved as draft.';
        return redirect()->route('admin.properties.index')->with('success', $msg);
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $vendors  = User::where('role', 'vendor')->orderBy('name')->get();
        // Convert gallery images array back to one-per-line for textarea
        $galleryText = implode("\n", $property->images ?? []);
        return view('admin.properties.edit', compact('property', 'vendors', 'galleryText'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|string',
            'city'            => 'required|string',
            'star_rating'     => 'required|integer|min:1|max:5',
            'address'         => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
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
            $galleryImages = array_values($galleryImages);
        }

        $property = Property::findOrFail($id);
        $property->update([
            'name'            => $request->name,
            'type'            => $request->type,
            'city'            => $request->city,
            'star_rating'     => (int) $request->star_rating,
            'address'         => $request->address,
            'price_per_night' => (float) $request->price_per_night,
            'original_price'  => $request->original_price ? (float) $request->original_price : null,
            'commission_rate' => $request->commission_rate ? (float) $request->commission_rate : ($property->commission_rate ?? 15.00),
            'description'     => $request->description,
            'primary_image'   => $request->primary_image ?: $property->primary_image,
            'video_url'       => $request->video_url ?: $property->video_url,
            'latitude'        => $request->latitude ? (float)$request->latitude : $property->latitude,
            'longitude'       => $request->longitude ? (float)$request->longitude : $property->longitude,
            'nearest_landmark'=> $request->nearest_landmark ?: $property->nearest_landmark,
            'images'          => array_values($galleryImages) ?: ($property->images ?? []),
            'amenities'       => $request->amenities ?? [],
            'is_featured'     => $request->boolean('is_featured'),
            'status'          => $request->status ?? $property->status,
            'vendor_id'       => $request->vendor_id ?: $property->vendor_id,
        ]);

        return redirect()->route('admin.properties.index')->with('success', 'Property "' . $property->name . '" updated successfully!');
    }

    public function toggleStatus(Request $request, $id)
    {
        $property  = Property::findOrFail($id);
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        return back()->with('success', 'Property status changed to ' . ucfirst($newStatus) . '.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $name     = $property->name;
        $property->delete();
        return redirect()->route('admin.properties.index')->with('success', '"' . $name . '" deleted successfully.');
    }
}
