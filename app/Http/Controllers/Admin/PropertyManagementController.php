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

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'featured') {
                $query->where('is_featured', true);
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('city', 'like', $s)
                  ->orWhere('address', 'like', $s);
            });
        }

        $properties = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Property::count(),
            'active'   => Property::where('status', 'active')->count(),
            'pending'  => Property::where('status', 'pending')->count(),
            'featured' => Property::where('is_featured', true)->count(),
            'inactive' => Property::where('status', 'inactive')->count(),
        ];

        return view('admin.properties.index', compact('properties', 'stats'));
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

        $property = Property::create([
            'name'             => $request->name,
            'slug'             => Str::slug($request->name) . '-' . time(),
            'type'             => $request->type,
            'city'             => $request->city,
            'star_rating'      => (int) $request->star_rating,
            'address'          => $request->address,
            'price_per_night'  => (float) $request->price_per_night,
            'original_price'   => $request->original_price ? (float) $request->original_price : null,
            'description'      => $request->description,
            'primary_image'    => $request->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80',
            'video_url'              => $request->video_url ?: null,
            'latitude'               => $request->latitude ? (float)$request->latitude : null,
            'longitude'              => $request->longitude ? (float)$request->longitude : null,
            'nearest_landmark'       => $request->nearest_landmark ?: null,
            'free_cancellation'      => $request->has('free_cancellation'),
            'no_credit_card_required'=> $request->has('no_credit_card_required'),
            'images'                 => array_values($galleryImages),
            'amenities'        => $request->amenities ?? [],
            'is_featured'      => $request->boolean('is_featured'),
            'status'           => $status,
            'vendor_id'        => $request->vendor_id ?: null,
        ]);

        if ($request->has('room_type') && is_array($request->room_type)) {
            foreach ($request->room_type as $i => $rtName) {
                $price = !empty($request->room_price[$i]) ? (float)$request->room_price[$i] : null;
                $qty   = !empty($request->room_qty[$i]) ? (int)$request->room_qty[$i] : 5;
                if ($price && $price > 0) {
                    try {
                        \App\Models\Room::create([
                            'property_id'    => $property->id,
                            'name'           => $rtName,
                            'price_per_night'=> $price,
                            'total_rooms'    => $qty,
                            'available_rooms'=> $qty,
                            'max_occupancy'  => $request->room_beds[$i] ?? 2,
                        ]);
                    } catch (\Exception $e) {}
                }
            }
        }

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
        $oldStatus = $property->status;
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        $msg = $oldStatus === 'pending'
            ? 'Property "' . $property->name . '" approved and published live!'
            : 'Property status changed to ' . ucfirst($newStatus) . '.';
        return back()->with('success', $msg);
    }

    /** Explicitly approve a vendor property and publish it live */
    public function approve($id)
    {
        $property = Property::findOrFail($id);
        $property->update([
            'status'           => 'active',
            'approved_at'      => now(),
            'rejection_reason' => null,
        ]);

        // Dispatch System Notification to Vendor Partner
        if ($property->vendor_id) {
            try {
                \App\Models\Message::create([
                    'sender_id'   => auth()->id() ?? 1,
                    'receiver_id' => $property->vendor_id,
                    'property_id' => $property->id,
                    'subject'     => '🎉 Property Approved & Published Live',
                    'message'     => "Congratulations! Your property '{$property->name}' has been reviewed and approved by admin. It is now live on the platform for guest bookings!",
                ]);
            } catch (\Exception $e) {}
        }

        return back()->with('success', '✅ Property "' . $property->name . '" has been approved and is now live on the website!');
    }

    /** Explicitly reject/unpublish a vendor property with feedback reason */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($id);
        $reason   = $request->input('rejection_reason') ?: 'Listing details did not meet platform quality guidelines.';

        $property->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejection_reason' => $reason,
        ]);

        // Dispatch System Notification to Vendor Partner
        if ($property->vendor_id) {
            try {
                \App\Models\Message::create([
                    'sender_id'   => auth()->id() ?? 1,
                    'receiver_id' => $property->vendor_id,
                    'property_id' => $property->id,
                    'subject'     => '⚠️ Property Listing Review Update',
                    'message'     => "Your property listing '{$property->name}' requires changes before publishing. Admin Feedback: {$reason}",
                ]);
            } catch (\Exception $e) {}
        }

        return back()->with('success', '⚠️ Property "' . $property->name . '" has been rejected with feedback provided to vendor.');
    }

    /** Handle Bulk Actions for selected properties (Approve, Deactivate, Delete) */
    public function bulkAction(Request $request)
    {
        $ids    = $request->input('ids', []);
        $action = $request->input('bulk_action');

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Please select at least one property to apply bulk action.');
        }

        if ($action === 'approve') {
            Property::whereIn('id', $ids)->update(['status' => 'active']);
            return back()->with('success', '✅ Selected ' . count($ids) . ' properties approved and published live!');
        }

        if ($action === 'deactivate') {
            Property::whereIn('id', $ids)->update(['status' => 'inactive']);
            return back()->with('success', 'Selected ' . count($ids) . ' properties deactivated.');
        }

        if ($action === 'delete') {
            Property::whereIn('id', $ids)->delete();
            return back()->with('success', 'Selected ' . count($ids) . ' properties deleted permanently.');
        }

        return back()->with('error', 'Invalid bulk action selected.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $name     = $property->name;
        $property->delete();
        return redirect()->route('admin.properties.index')->with('success', '"' . $name . '" deleted successfully.');
    }
}
