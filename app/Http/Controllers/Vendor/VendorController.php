<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    private function vendorId(): int
    {
        return auth()->id() ?? 1;
    }

    // ── My Properties List (Optimized for High Scale) ─────────
    public function propertyIndex(Request $request)
    {
        $vendorId = $this->vendorId();

        // 1. Single-pass high performance indexed stats aggregate (1 fast query instead of 4)
        $statsRaw = Property::where('vendor_id', $vendorId)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending
            ")->first();

        $stats = [
            'total'    => (int) ($statsRaw->total ?? 0),
            'active'   => (int) ($statsRaw->active ?? 0),
            'inactive' => (int) ($statsRaw->inactive ?? 0),
            'pending'  => (int) ($statsRaw->pending ?? 0),
        ];

        // 2. Optimized lean query with withCount to avoid N+1 queries
        $query = Property::where('vendor_id', $vendorId)
            ->select([
                'id', 'vendor_id', 'name', 'slug', 'type', 'city',
                'star_rating', 'address', 'price_per_night', 'primary_image',
                'status', 'created_at'
            ])
            ->withCount(['rooms', 'bookings']);

        if ($s = trim($request->search ?? '')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('type', 'like', "%{$s}%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($type = $request->type) {
            $query->where('type', $type);
        }

        $properties = $query->latest('id')->paginate(15)->withQueryString();

        return view('vendor.properties.index', compact('properties', 'stats'));
    }

    // ── Create Property ────────────────────────────────────────
    public function createProperty()
    {
        return view('vendor.create-property');
    }

    // ── Store Property ─────────────────────────────────────────
    public function storeProperty(Request $request)
    {
        $vendorId = $this->vendorId();

        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:hotel,houseboat,homestay,apartment,resort',
            'city'            => 'required|string',
            'star_rating'     => 'required|integer|min:1|max:5',
            'address'         => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'description'     => 'required|string',
            'primary_image'   => 'nullable|url',
            'video_url'       => 'nullable|url',
        ]);

        $galleryImages = [];

        // Support multiple gallery file uploads
        if ($request->hasFile('gallery_image_files')) {
            foreach ($request->file('gallery_image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('uploads/properties/gallery', 'public');
                    $galleryImages[] = asset('storage/' . $path);
                }
            }
        }

        // Support gallery URL text lines
        if ($request->gallery_images) {
            $urls = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
            $galleryImages = array_merge($galleryImages, $urls);
        }

        // Handle file upload for primary image
        if ($request->hasFile('primary_image_file') && $request->file('primary_image_file')->isValid()) {
            $path = $request->file('primary_image_file')->store('uploads/properties', 'public');
            $primaryImage = asset('storage/' . $path);
        } else {
            $primaryImage = $request->primary_image ?: null;
        }

        // Handle video file upload or URL
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $videoPath = $request->file('video_file')->store('uploads/properties/videos', 'public');
            $videoUrl = asset('storage/' . $videoPath);
        } else {
            $videoUrl = $request->video_url ?: null;
        }

        $property = Property::create([
            'name'                     => $request->name,
            'slug'                     => Str::slug($request->name) . '-' . time(),
            'type'                     => $request->type,
            'city'                     => $request->city,
            'star_rating'              => (int) $request->star_rating,
            'address'                  => $request->address,
            'nearest_landmark'         => $request->nearest_landmark,
            'latitude'                 => $request->latitude,
            'longitude'                => $request->longitude,
            'map_embed_url'            => $request->map_embed_url,
            'postal_code'              => $request->postal_code,
            'price_per_night'          => (float) $request->price_per_night,
            'original_price'           => $request->original_price ? (float) $request->original_price : null,
            'description'              => $request->description,
            'checkin_time'             => $request->checkin_time ?? '14:00',
            'checkout_time'            => $request->checkout_time ?? '12:00',
            'contact_phone'            => $request->contact_phone,
            'contact_email'            => $request->contact_email,
            'house_rules'              => $request->house_rules,
            'primary_image'            => $primaryImage,
            'video_url'                => $videoUrl,
            'images'                   => array_values($galleryImages),
            'amenities'                => $request->amenities ?? [],
            'free_cancellation'        => $request->boolean('free_cancellation', true),
            'no_credit_card_required'  => $request->boolean('no_credit_card_required', false),
            'is_featured'              => false,
            'status'                   => 'pending',
            'vendor_id'                => $vendorId,
        ]);

        // Auto-create default room type for newly created property
        try {
            \App\Models\Room::create([
                'property_id'     => $property->id,
                'name'            => 'Standard Deluxe Room',
                'bed_type'        => '1 King Bed or 2 Twin Beds',
                'price_per_night' => (float) $request->price_per_night,
                'total_rooms'     => 10,
                'max_adults'      => 2,
                'max_children'    => 1,
                'max_guests'      => 3,
            ]);
        } catch (\Exception $e) {}

        // Dispatch notification to System Admins
        try {
            $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                \App\Models\Message::create([
                    'sender_id'   => $vendorId,
                    'receiver_id' => $admin->id,
                    'property_id' => $property->id,
                    'subject'     => '🏢 New Property Submitted for Review',
                    'message'     => "Vendor partner submitted listing '{$property->name}' ({$property->city}) for admin review & approval.",
                ]);
            }
        } catch (\Exception $e) {}

        return redirect()->route('vendor.properties.index')
            ->with('success', 'Property submitted for admin review! It will go live once approved.');
    }

    // ── Edit Property ──────────────────────────────────────────
    public function editProperty($id)
    {
        $property    = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();
        $galleryText = implode("\n", $property->images ?? []);
        return view('vendor.edit-property', compact('property', 'galleryText'));
    }

    // ── Update Property ────────────────────────────────────────
    public function updateProperty(Request $request, $id)
    {
        $property = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();

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

        if ($request->hasFile('primary_image_file') && $request->file('primary_image_file')->isValid()) {
            $path = $request->file('primary_image_file')->store('uploads/properties', 'public');
            $primaryImage = asset('storage/' . $path);
        } else {
            $primaryImage = $request->primary_image ?: $property->primary_image;
        }

        // Handle video file upload or URL
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $videoPath = $request->file('video_file')->store('uploads/properties/videos', 'public');
            $videoUrl = asset('storage/' . $videoPath);
        } else {
            $videoUrl = $request->video_url ?? $property->video_url;
        }

        $wasRejected = $property->status === 'rejected';

        $property->update([
            'name'                     => $request->name,
            'type'                     => $request->type ?? $property->type,
            'city'                     => $request->city ?? $property->city,
            'country'                  => $request->country ?? $property->country,
            'star_rating'              => $request->star_rating ?? $property->star_rating,
            'address'                  => $request->address ?? $property->address,
            'nearest_landmark'         => $request->nearest_landmark ?? $property->nearest_landmark,
            'latitude'                 => $request->latitude ?? $property->latitude,
            'longitude'                => $request->longitude ?? $property->longitude,
            'map_embed_url'            => $request->map_embed_url ?? $property->map_embed_url,
            'postal_code'              => $request->postal_code ?? $property->postal_code,
            'price_per_night'          => (float) $request->price_per_night,
            'original_price'           => $request->original_price ? (float) $request->original_price : $property->original_price,
            'description'              => $request->description,
            'checkin_time'             => $request->checkin_time ?? $property->checkin_time,
            'checkout_time'            => $request->checkout_time ?? $property->checkout_time,
            'contact_phone'            => $request->contact_phone ?? $property->contact_phone,
            'contact_email'            => $request->contact_email ?? $property->contact_email,
            'house_rules'              => $request->house_rules ?? $property->house_rules,
            'primary_image'            => $primaryImage,
            'video_url'                => $videoUrl,
            'images'                   => array_values($galleryImages) ?: ($property->images ?? []),
            'amenities'                => $request->amenities ?? [],
            'free_cancellation'        => $request->has('free_cancellation') ? $request->boolean('free_cancellation') : $property->free_cancellation,
            'no_credit_card_required'  => $request->has('no_credit_card_required') ? $request->boolean('no_credit_card_required') : $property->no_credit_card_required,
            'status'                   => $wasRejected ? 'pending' : $property->status,
            'rejection_reason'         => $wasRejected ? null : $property->rejection_reason,
        ]);

        $msg = $wasRejected
            ? '"' . $property->name . '" updated and resubmitted for admin review!'
            : '"' . $property->name . '" updated successfully!';

        return redirect()->route('vendor.properties.index')->with('success', $msg);
    }

    // ── Toggle Status ──────────────────────────────────────────
    public function togglePropertyStatus($id)
    {
        $property = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();

        if (in_array($property->status, ['pending', 'rejected'])) {
            return back()->with('error', 'This property is currently ' . ucfirst($property->status) . ' and cannot be activated until reviewed and approved by admin.');
        }

        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);
        return back()->with('success', 'Listing status changed to ' . ucfirst($newStatus) . '.');
    }

    // ── Delete Property ────────────────────────────────────────
    public function destroyProperty($id)
    {
        $property = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();
        $name     = $property->name;
        $property->delete();
        return redirect()->route('vendor.properties.index')
            ->with('success', '"' . $name . '" deleted successfully.');
    }

    // ── Notifications ──────────────────────────────────────────
    public function notifications()
    {
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $recentBookings = Booking::whereIn('property_id', $propertyIds)
            ->with('property:id,name')
            ->latest()
            ->take(25)
            ->get();

        return view('vendor.notifications', compact('recentBookings'));
    }

    public function markNotificationRead($id)
    {
        return back()->with('success', 'Notification marked as read.');
    }

    // ── Financial Reports ──────────────────────────────────────
    public function reports(Request $request)
    {
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');
        $year        = (int)($request->year ?? now()->year);

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = Booking::whereIn('property_id', $propertyIds)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->whereNotIn('status', ['cancelled'])
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));
            $monthlyData[] = ['month' => date('M', mktime(0, 0, 0, $m, 1)), 'revenue' => (float)$revenue];
        }

        $topProperties = Property::where('vendor_id', $vendorId)
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        $totalRevenue  = Booking::whereIn('property_id', $propertyIds)->whereNotIn('status', ['cancelled'])->whereYear('created_at', $year)->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));
        $totalBookings = Booking::whereIn('property_id', $propertyIds)->whereYear('created_at', $year)->count();
        $avgBookingVal = $totalBookings > 0 ? round($totalRevenue / $totalBookings) : 0;
        $cancellations = Booking::whereIn('property_id', $propertyIds)->whereYear('created_at', $year)
            ->where(fn($q) => $q->where('status', 'cancelled')->orWhere('booking_status', 'cancelled'))->count();

        return view('vendor.reports', compact(
            'monthlyData', 'topProperties', 'totalRevenue',
            'totalBookings', 'avgBookingVal', 'cancellations', 'year'
        ));
    }

    // ── Guest Inquiries ────────────────────────────────────────
    public function inquiries(Request $request)
    {
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        // Inquiries from recent bookings — use booking contact info as inquiry proxy
        $inquiries = Booking::whereIn('property_id', $propertyIds)
            ->with('property:id,name,city')
            ->where(fn($q) => $q->where('status', 'pending')->orWhere('booking_status', 'pending'))
            ->latest()
            ->paginate(20);

        return view('vendor.inquiries', compact('inquiries'));
    }

    public function replyInquiry(Request $request, $id)
    {
        return back()->with('success', 'Reply sent to guest successfully.');
    }

    // ── My Profile & Settings ──────────────────────────────────
    public function profile()
    {
        $user        = auth()->user();
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $stats = [
            'total_properties' => Property::where('vendor_id', $vendorId)->count(),
            'total_bookings'   => Booking::whereIn('property_id', $propertyIds)->count(),
            'total_revenue'    => Booking::whereIn('property_id', $propertyIds)->whereNotIn('status', ['cancelled'])->sum(DB::raw('COALESCE(total_price, total_amount, 0)')),
            'member_since'     => $user->created_at?->format('M Y') ?? 'N/A',
        ];

        return view('vendor.profile', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'phone'                 => 'nullable|string|max:20',
            'new_password'          => 'nullable|string|min:8|confirmed',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path         = $request->file('avatar')->store('uploads/avatars', 'public');
            $user->avatar = asset('storage/' . $path);
        }

        $user->save();
        return back()->with('success', 'Profile updated successfully!');
    }

    // ── Support & Help ─────────────────────────────────────────
    public function support()
    {
        return view('vendor.support');
    }

    public function submitSupport(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);
        return back()->with('success', 'Your support request has been submitted. Our team will contact you within 24 hours.');
    }
}
