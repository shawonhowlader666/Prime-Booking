<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorDashboardController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────

    public function index()
    {
        $vendorId = auth()->id() ?? 1; // fallback for dev

        // Cache vendor stats for 5 min (keyed by vendor id)
        $stats = Cache::remember("vendor:stats:{$vendorId}", 300, function () use ($vendorId) {

            $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

            $totalRevenue = Booking::whereIn('property_id', $propertyIds)
                ->whereNotIn('status', ['cancelled'])
                ->whereNotIn('booking_status', ['cancelled'])
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));

            $thisMonth = Booking::whereIn('property_id', $propertyIds)
                ->whereNotIn('status', ['cancelled'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));

            return [
                'total_properties' => Property::where('vendor_id', $vendorId)->count(),
                'active_listings'  => Property::where('vendor_id', $vendorId)->where('status', 'active')->count(),
                'pending_listings' => Property::where('vendor_id', $vendorId)->where('status', 'inactive')->count(),
                'total_bookings'   => Booking::whereIn('property_id', $propertyIds)->count(),
                'pending_bookings' => Booking::whereIn('property_id', $propertyIds)
                    ->where(fn($q) => $q->where('status','pending')->orWhere('booking_status','pending'))->count(),
                'total_revenue'    => $totalRevenue,
                'monthly_revenue'  => $thisMonth,
                'commission_rate'  => 12, // platform takes 12%
                'net_earnings'     => round($totalRevenue * 0.88),
            ];
        });

        // Revenue chart — last 6 months
        $propertyIds   = Property::where('vendor_id', $vendorId)->pluck('id');
        $revenueChart  = $this->getMonthlyRevenue($propertyIds);

        // Recent bookings for this vendor
        $recentBookings = Booking::whereIn('property_id', $propertyIds)
            ->with('property:id,name,city,primary_image')
            ->latest()
            ->take(8)
            ->get();

        // Properties list
        $properties = Property::where('vendor_id', $vendorId)
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->get();

        return view('vendor.dashboard', compact(
            'stats', 'recentBookings', 'properties', 'revenueChart'
        ));
    }

    // ─── Vendor Bookings ──────────────────────────────────────────────────

    public function bookings(Request $request)
    {
        $vendorId    = auth()->id() ?? 1;
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $query = Booking::whereIn('property_id', $propertyIds)
            ->with('property:id,name,city,primary_image');

        if ($status = $request->status) {
            $query->where(fn($q) => $q->where('status', $status)->orWhere('booking_status', $status));
        }
        if ($search = $request->search) {
            $query->where(fn($q) => $q
                ->where('guest_name',  'like', "%{$search}%")
                ->orWhere('guest_email', 'like', "%{$search}%")
                ->orWhere('booking_reference', 'like', "%{$search}%")
            );
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();

        return view('vendor.bookings', compact('bookings'));
    }

    // ─── Single Booking Detail ─────────────────────────────────────────────

    public function bookingDetail($reference)
    {
        $vendorId    = auth()->id() ?? 1;
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $booking = Booking::where('booking_reference', $reference)
            ->whereIn('property_id', $propertyIds)
            ->with(['property', 'room'])
            ->firstOrFail();

        return view('vendor.booking-detail', compact('booking'));
    }

    // ─── Earnings Report ──────────────────────────────────────────────────

    public function earnings()
    {
        $vendorId    = auth()->id() ?? 1;
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $monthlyData = $this->getMonthlyRevenue($propertyIds, 12);

        $totalRevenue = Booking::whereIn('property_id', $propertyIds)
            ->where(fn($q) => $q->where('status','confirmed')->orWhere('booking_status','confirmed')
                ->orWhere('status','completed')->orWhere('booking_status','completed'))
            ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));

        $bestProperty = Property::where('vendor_id', $vendorId)
            ->withCount(['bookings' => fn($q) => $q->whereNotIn('status', ['cancelled'])])
            ->orderByDesc('bookings_count')
            ->first();

        return view('vendor.earnings', compact('monthlyData', 'totalRevenue', 'bestProperty'));
    }

    // ─── Helper: Monthly Revenue Chart ───────────────────────────────────

    private function getMonthlyRevenue($propertyIds, int $months = 6): array
    {
        $labels  = [];
        $revenue = [];

        for ($i = ($months - 1); $i >= 0; $i--) {
            $date      = now()->subMonths($i);
            $labels[]  = $date->format('M Y');
            $revenue[] = (float) Booking::whereIn('property_id', $propertyIds)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereNotIn('status', ['cancelled'])
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));
        }

        return ['labels' => $labels, 'revenue' => $revenue];
    }
}
