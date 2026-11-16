<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Location;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyManagementController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dynamic scope-aware stats aggregate (reflects selected source, city, and type in real-time)
        $statsQuery = Property::query();
        if ($request->filled('source') && $request->source !== 'all') {
            if ($request->source === 'vendor') {
                $statsQuery->whereNotNull('vendor_id');
            } elseif (in_array($request->source, ['admin', 'api_system', 'system'], true)) {
                $statsQuery->whereNull('vendor_id');
            } elseif (is_numeric($request->source)) {
                $statsQuery->where('vendor_id', (int) $request->source);
            }
        }
        if ($request->filled('city')) {
            $statsQuery->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $statsQuery->where('type', $request->type);
        }

        $statsRaw = $statsQuery->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured,
            COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive
        ")->first();

        $stats = [
            'total'    => (int) ($statsRaw->total ?? 0),
            'active'   => (int) ($statsRaw->active ?? 0),
            'pending'  => (int) ($statsRaw->pending ?? 0),
            'featured' => (int) ($statsRaw->featured ?? 0),
            'inactive' => (int) ($statsRaw->inactive ?? 0),
        ];

        // 2. Lean, optimized query with eager loading to prevent N+1 queries
        $query = Property::with(['vendor:id,name,email'])
            ->select([
                'id', 'vendor_id', 'name', 'slug', 'type', 'city',
                'star_rating', 'address', 'price_per_night', 'primary_image',
                'status', 'is_featured', 'created_at'
            ])
            ->withCount(['rooms', 'bookings']);

        if ($request->filled('source') && $request->source !== 'all') {
            if ($request->source === 'vendor') {
                $query->whereNotNull('vendor_id');
            } elseif (in_array($request->source, ['admin', 'api_system', 'system'], true)) {
                $query->whereNull('vendor_id');
            } elseif (is_numeric($request->source)) {
                $query->where('vendor_id', (int) $request->source);
            }
        }
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

        $properties = $query->latest('id')->paginate(15)->withQueryString();
        $vendors = User::where('role', 'vendor')->orderBy('name')->get(['id', 'name']);

        return view('admin.properties.index', compact('properties', 'stats', 'vendors'));
    }

    public function create()
    {
        $vendors   = User::where('role', 'vendor')->orderBy('name')->get();
        $locations = Location::orderBy('is_popular', 'desc')->orderBy('name')->get();
        return view('admin.properties.create', compact('vendors', 'locations'));
    }

    public function store(Request $request, \App\Services\Property\PropertyFormService $form)
    {
        $request->validate($form->rules(isAdmin: true) + [
            'room_type'    => 'nullable|array|max:30',
            'room_type.*'  => 'nullable|string|max:150',
            'room_price.*' => 'nullable|numeric|min:0|max:99999999',
            'room_qty.*'   => 'nullable|integer|min:1|max:10000',
            'room_beds.*'  => 'nullable|integer|min:1|max:30',
        ]);

        // "Save as Draft" button vs "Publish Live"
        $status = $request->input('action') === 'draft' ? 'inactive' : ($request->input('status') ?: 'active');

        $attributes = $form->attributes($request, null, isAdmin: true);

        $property = DB::transaction(function () use ($request, $form, $attributes, $status) {
            $prop = Property::create($attributes + [
                'slug'        => $form->uniqueSlug($attributes['name']),
                'status'      => $status,
                'approved_at' => in_array($status, ['active', 'published'], true) ? now() : null,
            ]);

            foreach ((array) $request->input('room_type', []) as $i => $roomName) {
                $roomName = trim((string) $roomName);
                $price    = (float) ($request->input("room_price.{$i}") ?? 0);
                if ($roomName === '' || $price <= 0) {
                    continue;
                }

                $guests = max(1, (int) ($request->input("room_beds.{$i}") ?? 2));

                \App\Models\Room::create([
                    'property_id'     => $prop->id,
                    'name'            => $roomName,
                    'price_per_night' => $price,
                    'total_rooms'     => max(1, (int) ($request->input("room_qty.{$i}") ?? 1)),
                    'max_guests'      => $guests,
                    'max_adults'      => $guests,
                    'max_children'    => 0,
                    'bathroom_count'  => 1,
                    'smoking_policy'  => 'Non-Smoking',
                    'free_cancellation' => (bool) ($attributes['free_cancellation'] ?? true),
                    'status'          => 'active',
                ]);
            }

            return $prop;
        });

        if ($property->rooms()->count() === 0) {
            return redirect()->route('admin.rooms.index', $property->id)
                ->with('success', 'Property saved. Add at least one room type so guests can book it.');
        }

        $msg = in_array($status, ['active', 'published'], true) ? 'Property published live successfully!' : 'Property saved as draft.';
        return redirect()->route('admin.properties.index')->with('success', $msg);
    }

    public function edit($id)
    {
        $property    = Property::findOrFail($id);
        $vendors     = User::where('role', 'vendor')->orderBy('name')->get();
        $locations   = Location::orderBy('is_popular', 'desc')->orderBy('name')->get();

        $rawGallery = is_array($property->images) ? $property->images : (json_decode($property->images ?? '[]', true) ?: []);
        
        // Ensure primary / cover image is included in editable gallery
        if (!empty($property->primary_image)) {
            $hasPrimary = false;
            foreach ($rawGallery as $imgItem) {
                $u = is_array($imgItem) ? ($imgItem['url'] ?? '') : (string)$imgItem;
                if ($u === $property->primary_image) {
                    $hasPrimary = true;
                    break;
                }
            }
            if (!$hasPrimary) {
                array_unshift($rawGallery, [
                    'url' => $property->primary_image,
                    'category' => 'property_views',
                    'title' => $property->name . ' - Cover Photo'
                ]);
            }
        }
        $structuredGallery = [];
        $urlStrings = [];

        foreach ($rawGallery as $idx => $item) {
            if (is_array($item)) {
                $url = $item['url'] ?? '';
                if (!empty($url)) {
                    $structuredGallery[] = [
                        'url' => $url,
                        'category' => $item['category'] ?? 'property_views',
                        'title' => $item['title'] ?? ''
                    ];
                    $urlStrings[] = $url;
                }
            } elseif (is_string($item) && !empty($item)) {
                $lower = strtolower($item);
                $cat = 'property_views';
                if (preg_match('/(restaurant|dining|cafe|buffet|breakfast|food|bar|coffee|kitchen)/i', $lower)) {
                    $cat = 'dining';
                } elseif (preg_match('/(pool|swimming|gym|fitness|spa|lobby|reception|lounge|hall|conference|ballroom)/i', $lower)) {
                    $cat = 'facilities';
                } elseif (preg_match('/(room|bed|suite|deluxe|superior|standard|twin|king)/i', $lower)) {
                    $cat = 'rooms';
                }
                $structuredGallery[] = [
                    'url' => $item,
                    'category' => $cat,
                    'title' => ''
                ];
                $urlStrings[] = $item;
            }
        }

        $galleryText = implode("\n", $urlStrings);
        return view('admin.properties.edit', compact('property', 'vendors', 'galleryText', 'structuredGallery', 'locations'));
    }

    public function update(Request $request, $id, \App\Services\Property\PropertyFormService $form)
    {
        $property = Property::findOrFail($id);

        $request->validate($form->rules(isAdmin: true, property: $property));

        $attributes = $form->attributes($request, $property, isAdmin: true);

        if ($request->filled('status')) {
            $attributes['status'] = $request->input('status');
            if (in_array($attributes['status'], ['active', 'published'], true) && !$property->approved_at) {
                $attributes['approved_at'] = now();
            }
        }

        DB::transaction(fn () => $property->update($attributes));

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
    public function approve(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        DB::transaction(function () use ($property, $request) {
            $data = [
                'status'           => 'active',
                'approved_at'      => now(),
                'rejection_reason' => null,
            ];

            if ($request->filled('commission_rate')) {
                $data['commission_rate'] = (float) $request->input('commission_rate');
            }

            $property->update($data);

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
        });

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

        DB::transaction(function () use ($property, $reason) {
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
        });

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
            $today = now()->toDateString();
            $blocked = [];
            $canDelete = [];

            foreach ($ids as $id) {
                $hasActive = Booking::where('property_id', $id)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->where('check_out', '>=', $today)
                    ->exists();

                if ($hasActive) {
                    $prop = Property::find($id);
                    $blocked[] = $prop ? $prop->name : "#{$id}";
                } else {
                    $canDelete[] = $id;
                }
            }

            if (!empty($canDelete)) {
                Property::whereIn('id', $canDelete)->delete();
            }

            if (!empty($blocked)) {
                $msg = 'The following properties cannot be deleted because they have active or upcoming reservations: ' . implode(', ', $blocked);
                if (!empty($canDelete)) {
                    $msg .= '. (Other ' . count($canDelete) . ' properties deleted successfully).';
                }
                return back()->with('error', $msg);
            }

            return back()->with('success', 'Selected ' . count($ids) . ' properties deleted permanently.');
        }

        return back()->with('error', 'Invalid bulk action selected.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        // Security Guard: Prevent deleting properties with active or upcoming reservations
        $activeBookingsCount = Booking::where('property_id', $property->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_out', '>=', now()->toDateString())
            ->count();

        if ($activeBookingsCount > 0) {
            return back()->with('error', "Cannot delete \"{$property->name}\". There are {$activeBookingsCount} active or upcoming reservation(s) associated with this hotel. Please fulfill or reassign the reservations first.");
        }

        $name = $property->name;
        $property->delete();
        return redirect()->route('admin.properties.index')->with('success', '"' . $name . '" deleted successfully.');
    }
}
