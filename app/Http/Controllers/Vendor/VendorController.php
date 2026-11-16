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
    public function storeProperty(Request $request, \App\Services\Property\PropertyFormService $form)
    {
        $vendorId = $this->vendorId();

        $request->validate($form->rules(isAdmin: false));

        $attributes = $form->attributes($request, null, isAdmin: false);

        $property = DB::transaction(function () use ($form, $attributes, $vendorId) {
            // Ratings, badges, commission and publication are never taken from a vendor's form.
            $prop = Property::create($attributes + [
                'slug'           => $form->uniqueSlug($attributes['name']),
                'vendor_id'      => $vendorId,
                'status'         => 'pending',
                'is_featured'    => false,
                'rating_score'   => 0,
                'location_score' => 0,
                'total_reviews'  => 0,
                'rooms_left'     => 0,
            ]);

            // Tell the platform admins there is a listing to review.
            try {
                foreach (\App\Models\User::whereIn('role', ['admin', 'super_admin'])->pluck('id') as $adminId) {
                    \App\Models\Message::create([
                        'sender_id'   => $vendorId,
                        'receiver_id' => $adminId,
                        'property_id' => $prop->id,
                        'subject'     => 'New Property Submitted for Review',
                        'message'     => "Vendor partner submitted listing '{$prop->name}' ({$prop->city}) for admin review & approval.",
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $prop;
        });

        return redirect()->route('vendor.rooms.index', $property->id)
            ->with('success', 'Property submitted for admin review. Now add your room types, prices and photos so it can go live.');
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
    public function updateProperty(Request $request, $id, \App\Services\Property\PropertyFormService $form)
    {
        $property = Property::where('id', $id)->where('vendor_id', $this->vendorId())->firstOrFail();

        $request->validate($form->rules(isAdmin: false, property: $property));

        $attributes  = $form->attributes($request, $property, isAdmin: false);
        $wasRejected = $property->status === 'rejected';

        if ($wasRejected) {
            $attributes['status'] = 'pending';
            $attributes['rejection_reason'] = null;
        }

        DB::transaction(fn () => $property->update($attributes));

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

        // Security Guard: Prevent deleting properties with active or upcoming reservations
        $activeBookingsCount = Booking::where('property_id', $property->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_out', '>=', now()->toDateString())
            ->count();

        if ($activeBookingsCount > 0) {
            return back()->with('error', "Cannot delete \"{$property->name}\". There are {$activeBookingsCount} active or upcoming reservation(s) associated with this hotel. Please fulfill or resolve the reservations first.");
        }

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

        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $inquiry = \App\Models\Inquiry::where(function ($q) use ($vendorId, $propertyIds) {
                $q->whereIn('property_id', $propertyIds);
                if (\Illuminate\Support\Facades\Schema::hasColumn('inquiries', 'vendor_id')) {
                    $q->orWhere('vendor_id', $vendorId);
                }
            })
            ->findOrFail($id);

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
            'trade_license'         => 'nullable|string|max:100',
            'payout_method'         => 'nullable|string|max:50',
            'account_no'            => 'nullable|string|max:100',
            'account_holder'        => 'nullable|string|max:150',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('trade_license')) {
            $user->trade_license = $request->trade_license;
        }
        if ($request->has('payout_method')) {
            $user->payout_method = $request->payout_method;
        }
        if ($request->has('account_no')) {
            $user->account_no = $request->account_no;
        }
        if ($request->has('account_holder')) {
            $user->account_holder = $request->account_holder;
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
