<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Location;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Review;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    private function vendorId(): int
    {
        $id = auth()->id();
        if (! $id) {
            abort(401, 'Unauthorized vendor session.');
        }
        return (int) $id;
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
        $locations = Location::orderBy('is_popular', 'desc')->orderBy('name')->get();
        return view('vendor.create-property', compact('locations'));
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
            'primary_image'   => 'nullable|string',
            'video_url'       => 'nullable|string',
            'primary_image_file'    => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'gallery_image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'video_file'            => 'nullable|file|mimes:mp4,webm,mov,ogg|max:102400',
        ]);

        $galleryImages = [];
        $uploadCategory = $request->input('new_gallery_category', 'property_views');
        if (!in_array($uploadCategory, ['property_views', 'facilities', 'dining', 'rooms', 'other'])) {
            $uploadCategory = 'property_views';
        }

        // Support multiple gallery file uploads with categorization
        if ($request->hasFile('gallery_image_files')) {
            foreach ($request->file('gallery_image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('uploads/properties/gallery', 'public');
                    $galleryImages[] = [
                        'url'      => asset('storage/' . $path),
                        'category' => $uploadCategory,
                        'title'    => $request->name . ' - ' . ucfirst(str_replace('_', ' ', $uploadCategory)),
                    ];
                }
            }
        }

        // Support gallery URL text lines
        if ($request->gallery_images) {
            $urls = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
            foreach ($urls as $u) {
                $galleryImages[] = [
                    'url'      => $u,
                    'category' => $uploadCategory,
                    'title'    => $request->name . ' - ' . ucfirst(str_replace('_', ' ', $uploadCategory)),
                ];
            }
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

        $property = DB::transaction(function () use ($request, $primaryImage, $videoUrl, $galleryImages, $vendorId) {
            $prop = Property::create([
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
            'total_floors'             => $request->total_floors ? (int)$request->total_floors : null,
            'total_rooms_count'        => $request->total_rooms_count ? (int)$request->total_rooms_count : null,
            'year_built'               => $request->year_built ? (int)$request->year_built : null,
            'languages_spoken'         => (array)($request->languages_spoken ?? ['English', 'Bengali']),
            'pets_policy'              => $request->pets_policy ?? 'Pets Not Allowed',
            'primary_image'            => $primaryImage,
            'video_url'                => $videoUrl,
            'images'                   => array_values($galleryImages),
            'amenities'                => $request->amenities ?? [],
            'free_cancellation'        => $request->boolean('free_cancellation', true),
            'no_credit_card_required'  => $request->boolean('no_credit_card_required', false),
            'feature_snippet'          => $request->feature_snippet ?: null,
            'ribbon_badge'             => $request->ribbon_badge ?: null,
            'is_sold_out'              => $request->boolean('is_sold_out'),
            'rooms_left'               => $request->has('rooms_left') ? (int)$request->rooms_left : 5,
            'location_score'           => $request->location_score ? (float)$request->location_score : 8.8,
            'rating_score'             => $request->rating_score ? (float)$request->rating_score : 8.8,
            'total_reviews'            => $request->has('total_reviews') ? (int)$request->total_reviews : 128,
            'is_featured'              => false,
            'status'                   => 'pending',
            'cancellation_policy'      => $request->cancellation_policy ?: null,
            'vendor_id'                => $vendorId,
        ]);

        // Auto-create default room type for newly created property
        try {
            \App\Models\Room::create([
                'property_id'     => $prop->id,
                'name'            => 'Standard Deluxe Room',
                'bed_type'        => '1 King Bed or 2 Twin Beds',
                'view_type'       => 'City View',
                'bathroom_count'  => 1,
                'bathroom_features' => ['Private Attached Bathroom', 'Hot Water Geyser'],
                'smoking_policy'  => 'Non-Smoking',
                'balcony_type'    => 'Private Balcony',
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
                    'property_id' => $prop->id,
                    'subject'     => '🏢 New Property Submitted for Review',
                    'message'     => "Vendor partner submitted listing '{$prop->name}' ({$prop->city}) for admin review & approval.",
                ]);
            }
        } catch (\Exception $e) {}

            return $prop;
        });

        return redirect()->route('vendor.properties.index')
            ->with('success', 'Property submitted for admin review! It will go live once approved.');
    }

    // ── Edit Property ──────────────────────────────────────────
    public function editProperty($id)
    {
        $property    = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();
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
        return view('vendor.edit-property', compact('property', 'galleryText', 'structuredGallery', 'locations'));
    }

    // ── Update Property ────────────────────────────────────────
    public function updateProperty(Request $request, $id)
    {
        $property = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();

        $request->validate([
            'name'            => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'description'     => 'required|string',
            'primary_image'   => 'nullable|string',
            'video_url'       => 'nullable|string',
            'set_primary_image_url' => 'nullable|string',
            'primary_image_file'    => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'gallery_image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'existing_gallery_replace.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'video_file'            => 'nullable|file|mimes:mp4,webm,mov,ogg|max:102400',
        ]);

        $finalGallery = [];

        // 1. Existing gallery items management (re-categorize, re-title, in-place replace, delete)
        if ($request->has('existing_gallery') && is_array($request->existing_gallery)) {
            foreach ($request->existing_gallery as $gIdx => $item) {
                if (!empty($item['delete'])) {
                    continue; // Vendor chose to delete this photo
                }
                $url = trim($item['url'] ?? '');

                // Check if vendor uploaded a replacement file for this specific photo
                if ($request->hasFile("existing_gallery_replace.{$gIdx}") && $request->file("existing_gallery_replace.{$gIdx}")->isValid()) {
                    $repPath = $request->file("existing_gallery_replace.{$gIdx}")->store('uploads/properties/gallery', 'public');
                    $url = asset('storage/' . $repPath);
                }

                if (empty($url)) continue;

                $cat = trim($item['category'] ?? 'property_views');
                if (!in_array($cat, ['property_views', 'facilities', 'dining', 'rooms', 'other'])) {
                    $cat = 'property_views';
                }

                $title = trim($item['title'] ?? '') ?: ($property->name . ' - ' . ucfirst(str_replace('_', ' ', $cat)));

                $finalGallery[] = [
                    'url' => $url,
                    'category' => $cat,
                    'title' => $title
                ];
            }
        } else {
            $rawExisting = is_array($property->images) ? $property->images : (json_decode($property->images ?? '[]', true) ?: []);
            foreach ($rawExisting as $item) {
                if (is_array($item)) {
                    $finalGallery[] = $item;
                } elseif (is_string($item) && !empty($item)) {
                    $finalGallery[] = [
                        'url' => $item,
                        'category' => 'property_views',
                        'title' => $property->name
                    ];
                }
            }
        }

        // 2. New uploads with vendor-selected category
        $uploadCategory = $request->input('new_gallery_category', 'property_views');
        if (!in_array($uploadCategory, ['property_views', 'facilities', 'dining', 'rooms', 'other'])) {
            $uploadCategory = 'property_views';
        }

        // Support multiple gallery file uploads
        if ($request->hasFile('gallery_image_files')) {
            foreach ($request->file('gallery_image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('uploads/properties/gallery', 'public');
                    $finalGallery[] = [
                        'url' => asset('storage/' . $path),
                        'category' => $uploadCategory,
                        'title' => $request->name . ' - ' . ucfirst(str_replace('_', ' ', $uploadCategory))
                    ];
                }
            }
        }

        if ($request->gallery_images) {
            $urls = array_filter(
                array_map('trim', explode("\n", $request->gallery_images)),
                fn($line) => !empty($line) && filter_var($line, FILTER_VALIDATE_URL)
            );
            foreach ($urls as $u) {
                $finalGallery[] = [
                    'url' => $u,
                    'category' => $uploadCategory,
                    'title' => $request->name . ' - ' . ucfirst(str_replace('_', ' ', $uploadCategory))
                ];
            }
        }

        // Determine Primary / Cover Image
        if ($request->hasFile('primary_image_file') && $request->file('primary_image_file')->isValid()) {
            $path = $request->file('primary_image_file')->store('uploads/properties', 'public');
            $primaryImage = asset('storage/' . $path);
        } elseif ($request->filled('set_primary_image_url')) {
            $primaryImage = $request->input('set_primary_image_url');
        } elseif ($request->filled('primary_image')) {
            $primaryImage = $request->input('primary_image');
        } else {
            $primaryImage = $property->primary_image;
        }

        // Handle video file upload, remove, or URL
        if ($request->boolean('remove_video')) {
            $videoUrl = null;
        } elseif ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $videoPath = $request->file('video_file')->store('uploads/properties/videos', 'public');
            $videoUrl = asset('storage/' . $videoPath);
        } elseif ($request->filled('video_url')) {
            $rawVideo = trim($request->input('video_url'));
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $rawVideo, $m)) {
                $videoUrl = "https://www.youtube.com/embed/" . $m[1];
            } else {
                $videoUrl = $rawVideo;
            }
        } else {
            $videoUrl = $property->video_url;
        }

        $wasRejected = $property->status === 'rejected';

        DB::transaction(function () use ($property, $request, $primaryImage, $videoUrl, $finalGallery, $wasRejected) {
            $property->update([
            'name'                     => $request->name,
            'type'                     => $request->type ?? $property->type,
            'city'                     => $request->city ?? $property->city,
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
            'total_floors'             => $request->total_floors ? (int)$request->total_floors : $property->total_floors,
            'total_rooms_count'        => $request->total_rooms_count ? (int)$request->total_rooms_count : $property->total_rooms_count,
            'year_built'               => $request->year_built ? (int)$request->year_built : $property->year_built,
            'languages_spoken'         => $request->has('languages_spoken') ? (array)$request->languages_spoken : $property->languages_spoken,
            'pets_policy'              => $request->pets_policy ?? $property->pets_policy,
            'primary_image'            => $primaryImage,
            'video_url'                => $videoUrl,
            'images'                   => $finalGallery,
            'amenities'                => $request->amenities ?? [],
            'free_cancellation'        => $request->has('free_cancellation') ? $request->boolean('free_cancellation') : $property->free_cancellation,
            'no_credit_card_required'  => $request->has('no_credit_card_required') ? $request->boolean('no_credit_card_required') : $property->no_credit_card_required,
            'rooms_left'               => $request->has('rooms_left') ? (int)$request->rooms_left : $property->rooms_left,
            'location_score'           => $request->location_score ? (float)$request->location_score : $property->location_score,
            'rating_score'             => $request->rating_score ? (float)$request->rating_score : $property->rating_score,
            'total_reviews'            => $request->has('total_reviews') ? (int)$request->total_reviews : $property->total_reviews,
            'feature_snippet'          => $request->has('feature_snippet') ? $request->feature_snippet : $property->feature_snippet,
            'ribbon_badge'             => $request->has('ribbon_badge') ? $request->ribbon_badge : $property->ribbon_badge,
            'is_sold_out'              => $request->boolean('is_sold_out'),
            'cancellation_policy'      => $request->cancellation_policy ?: $property->cancellation_policy,
            'status'                   => $wasRejected ? 'pending' : $property->status,
            'rejection_reason'         => $wasRejected ? null : $property->rejection_reason,
            ]);
        });

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

    // ── Guest Inquiries ────────────────────────────────────────
    public function inquiries(Request $request)
    {
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $inquiries = \App\Models\Inquiry::where(function($q) use ($vendorId, $propertyIds) {
                $q->where('vendor_id', $vendorId)
                  ->orWhereIn('property_id', $propertyIds);
            })
            ->with('property:id,name,city')
            ->latest('id')
            ->paginate(20);

        return view('vendor.inquiries', compact('inquiries'));
    }

    public function replyInquiry(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $inquiry = \App\Models\Inquiry::findOrFail($id);
        $inquiry->update([
            'reply'      => $request->reply,
            'status'     => 'answered',
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Reply transmitted to guest (' . $inquiry->name . ' - ' . $inquiry->phone . ') successfully.');
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
            'avatar'                => 'nullable|image|max:10240',
            'new_password'          => 'nullable|string|min:8|confirmed',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->has('phone')) {
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
        return back()->with('success', 'Profile & Photo updated successfully!');
    }

    // ── Support & Help ─────────────────────────────────────────
    public function support(Request $request)
    {
        $userId  = $this->vendorId();
        $tickets = SupportTicket::where('user_id', $userId)
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('vendor.support', compact('tickets'));
    }

    public function submitSupport(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'message'  => 'required|string|max:5000',
        ]);

        SupportTicket::create([
            'user_id'       => $this->vendorId(),
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject'       => $request->subject,
            'category'      => $request->category,
            'message'       => $request->message,
            'status'        => 'open',
            'priority'      => 'medium',
            'source'        => 'vendor',
        ]);

        return back()->with('success', 'Your support ticket has been submitted! Ticket number sent to your email. Our team will respond within 24 hours.');
    }

    public function viewTicket(int $id)
    {
        $ticket = SupportTicket::where('user_id', $this->vendorId())
            ->with(['replies.user'])
            ->findOrFail($id);

        return view('vendor.support-ticket', compact('ticket'));
    }

    public function replyTicket(Request $request, int $id)
    {
        $ticket = SupportTicket::where('user_id', $this->vendorId())->findOrFail($id);
        $request->validate(['message' => 'required|string|max:5000']);

        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => $this->vendorId(),
            'message'        => $request->message,
            'is_admin_reply' => false,
        ]);

        if ($ticket->status === 'closed' || $ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    // ── Notifications Center ───────────────────────────────────
    public function notifications(Request $request)
    {
        $user = auth()->user();
        $vendorId = $this->vendorId();

        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        // Recent bookings (confirmed or new)
        $recentBookings = Booking::whereIn('property_id', $propertyIds)
            ->with(['property', 'room'])
            ->latest('id')
            ->take(15)
            ->get();

        // Recent reviews
        $recentReviews = Review::whereIn('property_id', $propertyIds)
            ->with('property')
            ->latest('id')
            ->take(10)
            ->get();

        // Recent payouts
        $recentPayouts = Payout::where('vendor_id', $vendorId)
            ->latest('id')
            ->take(5)
            ->get();

        // Merge into structured unified activity feed
        $items = collect();

        foreach ($recentBookings as $b) {
            $isCancel = ($b->status === 'cancelled');
            $items->push([
                'id'         => 'booking_' . $b->id,
                'type'       => $isCancel ? 'booking_cancelled' : 'booking_new',
                'title'      => $isCancel ? 'Booking Cancelled' : 'New Reservation Confirmed',
                'message'    => "Ref #{$b->booking_reference} for " . ($b->property->name ?? 'Property') . " by {$b->guest_name} (৳" . number_format($b->total_price ?? 0) . ")",
                'time'       => $b->created_at,
                'link'       => route('vendor.bookings.show', $b->id),
                'icon'       => $isCancel ? 'fa-solid fa-ban text-danger' : 'fa-solid fa-calendar-check text-success',
                'bg'         => $isCancel ? 'rgba(239, 68, 68, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                'is_read'    => false,
            ]);
        }

        foreach ($recentReviews as $r) {
            $items->push([
                'id'         => 'review_' . $r->id,
                'type'       => 'review_received',
                'title'      => "New {$r->rating}-Star Guest Review",
                'message'    => "Guest review received on " . ($r->property->name ?? 'Property') . ": \"" . \Illuminate\Support\Str::limit($r->comment, 60) . "\"",
                'time'       => $r->created_at,
                'link'       => route('vendor.reviews.index'),
                'icon'       => 'fa-solid fa-star text-warning',
                'bg'         => 'rgba(245, 158, 11, 0.1)',
                'is_read'    => false,
            ]);
        }

        foreach ($recentPayouts as $p) {
            $items->push([
                'id'         => 'payout_' . $p->id,
                'type'       => 'payout_status',
                'title'      => 'Payout ' . ucfirst($p->status),
                'message'    => "Payout request of ৳" . number_format($p->amount) . " is " . ucfirst($p->status) . " (Ref: {$p->reference_number})",
                'time'       => $p->created_at,
                'link'       => route('vendor.payouts.index'),
                'icon'       => 'fa-solid fa-money-bill-transfer text-primary',
                'bg'         => 'rgba(24, 144, 255, 0.1)',
                'is_read'    => true,
            ]);
        }

        // Sort unified stream newest first
        $notifications = $items->sortByDesc('time')->values();

        return view('vendor.notifications', compact('notifications'));
    }

    public function markNotificationRead(Request $request, $id)
    {
        if ($user = auth()->user()) {
            $user->unreadNotifications()->where('id', $id)->update(['read_at' => now()]);
        }
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead(Request $request)
    {
        if ($user = auth()->user()) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    // ── Financial & Analytics Reports ──────────────────────────
    public function reports(Request $request)
    {
        $vendorId = $this->vendorId();
        $selectedYear = (int) ($request->get('year', now()->year));
        $selectedPropertyId = $request->get('property_id');

        $propertiesQuery = Property::where('vendor_id', $vendorId);
        $vendorProperties = $propertiesQuery->select(['id', 'name'])->get();
        $propertyIds = $vendorProperties->pluck('id')->toArray();

        // Base bookings query for this vendor
        $bookingsQuery = Booking::whereIn('property_id', $propertyIds)
            ->whereYear('created_at', $selectedYear);

        if ($selectedPropertyId && in_array($selectedPropertyId, $propertyIds)) {
            $bookingsQuery->where('property_id', $selectedPropertyId);
        }

        // Export CSV if requested
        if ($request->get('export') === 'csv') {
            $filename = "vendor_report_{$selectedYear}_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $exportBookings = (clone $bookingsQuery)->with(['property', 'room'])->latest('id')->get();

            $callback = function () use ($exportBookings) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Ref #', 'Guest Name', 'Property', 'Room', 'Check-in', 'Check-out', 'Amount (BDT)', 'Status', 'Payment Status', 'Created At']);

                foreach ($exportBookings as $b) {
                    fputcsv($file, [
                        $b->booking_reference ?? $b->booking_code ?? $b->id,
                        $b->guest_name,
                        $b->property->name ?? 'N/A',
                        $b->room->name ?? 'Standard Room',
                        $b->check_in ? $b->check_in->format('Y-m-d') : 'N/A',
                        $b->check_out ? $b->check_out->format('Y-m-d') : 'N/A',
                        $b->total_price ?? $b->total_amount ?? 0,
                        ucfirst($b->status ?? 'pending'),
                        ucfirst($b->payment_status ?? 'unpaid'),
                        $b->created_at->format('Y-m-d H:i'),
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Metrics
        $totalBookings = (clone $bookingsQuery)->count();
        $grossRevenue = (clone $bookingsQuery)->whereNotIn('status', ['cancelled'])->sum('total_price');
        $cancellations = (clone $bookingsQuery)->where('status', 'cancelled')->count();
        $validBookings = max(1, $totalBookings - $cancellations);
        $avgValue = $totalBookings > 0 ? round($grossRevenue / $validBookings, 2) : 0;

        // 12-Month aggregated data for Chart.js
        $monthlyRevenueRaw = (clone $bookingsQuery)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('MONTH(created_at) as month_num, SUM(total_price) as rev, COUNT(*) as cnt')
            ->groupBy('month_num')
            ->pluck('rev', 'month_num')
            ->toArray();

        $monthlyBookingsRaw = (clone $bookingsQuery)
            ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as cnt')
            ->groupBy('month_num')
            ->pluck('cnt', 'month_num')
            ->toArray();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyData = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month'    => $monthNames[$m - 1],
                'revenue'  => (float) ($monthlyRevenueRaw[$m] ?? 0),
                'bookings' => (int) ($monthlyBookingsRaw[$m] ?? 0),
            ];
        }

        // Top properties ranked
        $topProperties = Property::where('vendor_id', $vendorId)
            ->withCount(['bookings' => function ($q) use ($selectedYear) {
                $q->whereYear('created_at', $selectedYear);
            }])
            ->orderByDesc('bookings_count')
            ->take(6)
            ->get();

        // Recent completed bookings for the report table
        $recentBookings = (clone $bookingsQuery)
            ->with(['property', 'room'])
            ->latest('id')
            ->take(10)
            ->get();

        return view('vendor.reports', compact(
            'selectedYear',
            'selectedPropertyId',
            'vendorProperties',
            'grossRevenue',
            'totalBookings',
            'avgValue',
            'cancellations',
            'monthlyData',
            'topProperties',
            'recentBookings'
        ));
    }
}
