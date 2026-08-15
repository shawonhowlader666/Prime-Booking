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

            $properties  = Property::where('vendor_id', $vendorId)->get();
            $propertyIds = $properties->pluck('id');

            $bookings = Booking::whereIn('property_id', $propertyIds)
                ->whereNotIn('status', ['cancelled'])
                ->whereNotIn('booking_status', ['cancelled'])
                ->with('property:id,commission_rate')
                ->get();

            $totalRevenue    = 0;
            $totalCommission = 0;
            foreach ($bookings as $b) {
                $gross = (float)($b->total_price ?? $b->total_amount ?? 0);
                $rate  = (float)($b->property->commission_rate ?? 15.00);
                $totalRevenue    += $gross;
                $totalCommission += ($gross * ($rate / 100));
            }

            $thisMonth = Booking::whereIn('property_id', $propertyIds)
                ->whereNotIn('status', ['cancelled'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)'));

            $netEarnings       = $totalRevenue - $totalCommission;
            $avgCommissionRate = $totalRevenue > 0 ? round(($totalCommission / $totalRevenue) * 100, 1) : 15.0;

            return [
                'total_properties' => $properties->count(),
                'active_listings'  => $properties->where('status', 'active')->count(),
                'pending_listings' => $properties->where('status', 'pending')->count(),
                'total_bookings'   => $bookings->count(),
                'pending_bookings' => $bookings->filter(fn($b) => ($b->status ?? $b->booking_status ?? '') === 'pending')->count(),
                'total_revenue'    => $totalRevenue,
                'monthly_revenue'  => $thisMonth,
                'commission_rate'  => $avgCommissionRate,
                'admin_commission' => round($totalCommission),
                'net_earnings'     => round($netEarnings),
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

    public function updateBookingStatus(Request $request, $reference)
    {
        $vendorId = auth()->id() ?? 1;
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');
        $booking = Booking::where('booking_reference', $reference)
            ->whereIn('property_id', $propertyIds)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update([
            'status' => $validated['status'],
            'booking_status' => $validated['status'],
        ]);

        return back()->with('success', "Booking #{$reference} status updated to {$validated['status']}.");
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
