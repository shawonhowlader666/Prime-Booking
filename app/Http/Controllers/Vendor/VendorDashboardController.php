<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\ActivityLog;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorDashboardController extends Controller
{
    private function vendorId(): int
    {
        $id = auth()->id();
        if (! $id) {
            abort(401, 'Unauthorized vendor session.');
        }
        return (int) $id;
    }

    // ─── Dashboard ────────────────────────────────────────────────────────

    public function index()
    {
        $vendorId = $this->vendorId();

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
                $rate  = (float)($b->property?->commission_rate ?? 15.00);
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
                'active_listings'  => $properties->whereIn('status', \App\Models\Property::LIVE_STATUSES)->count(),
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
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $query = Booking::whereIn('property_id', $propertyIds)
            ->with(['property', 'room']);

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
        $vendorId    = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $booking = Booking::where(function($q) use ($reference) {
                $q->where('booking_reference', $reference)
                  ->orWhere('id', $reference);
            })
            ->whereIn('property_id', $propertyIds)
            ->with(['property', 'room'])
            ->firstOrFail();

        return view('vendor.booking-detail', compact('booking'));
    }

    public function updateBookingStatus(Request $request, $reference)
    {
        $vendorId = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');
        $booking = Booking::where(function($q) use ($reference) {
                $q->where('booking_reference', $reference)
                  ->orWhere('id', $reference);
            })
            ->whereIn('property_id', $propertyIds)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|string|in:confirmed,completed,cancelled',
        ]);

        $new = $validated['status'];
        $old = $booking->status;

        if (in_array($old, ['cancelled', 'refunded', 'failed'], true)) {
            return back()->with('error', 'A cancelled reservation cannot be changed. Ask the guest to make a new booking.');
        }

        if ($new === 'cancelled') {
            // A cancellation by the property always refunds whatever the guest paid.
            app(\App\Services\Booking\BookingLifecycleService::class)->cancel($booking, 'Cancelled by property', true);
            return back()->with('success', "Booking #{$booking->booking_reference} cancelled. The guest has been notified.");
        }

        if ($new === 'confirmed' && $booking->isAwaitingPayment()) {
            return back()->with('error', 'This booking is waiting for the guest\'s online payment and confirms automatically once it is paid.');
        }

        if ($new === 'completed' && $booking->check_in && $booking->check_in->isFuture()) {
            return back()->with('error', 'A stay can only be marked completed on or after its check-in date.');
        }

        $booking->update(['status' => $new]);

        if (in_array($booking->payment_status, ['paid', 'completed'], true)) {
            AccountingService::recordBookingLedger($booking, $booking->payment_method ?? 'cash');
        }

        return back()->with('success', "Booking #{$booking->booking_reference} status updated to {$new}.");
    }
    public function updatePaymentStatus(Request $request, $id)
    {
        $vendorId = $this->vendorId();
        $propertyIds = Property::where('vendor_id', $vendorId)->pluck('id');
        $booking = Booking::where('id', $id)
            ->whereIn('property_id', $propertyIds)
            ->firstOrFail();

        $validated = $request->validate([
            'payment_status' => 'required|string|in:paid,unpaid',
        ]);

        // Online payments are settled only by the payment gateway. A property can record
        // only the money it collects itself at the front desk.
        if (!$booking->isOfflinePayment()) {
            return back()->with('error', 'Online payments are confirmed automatically by the payment gateway and cannot be changed here.');
        }

        if (!in_array($booking->status, ['confirmed', 'completed'], true)) {
            return back()->with('error', 'Payment can only be recorded for an active reservation.');
        }

        $oldPayment = $booking->payment_status;
        $booking->update(['payment_status' => $validated['payment_status']]);

        if ($validated['payment_status'] === 'paid' && $oldPayment !== 'paid') {
            AccountingService::recordBookingLedger($booking, $booking->payment_method ?? 'cash');
        }

        return back()->with('success', "Booking #{$booking->booking_reference} payment marked as " . strtoupper($validated['payment_status']) . ".");
    }
    // ─── Earnings Report ──────────────────────────────────────────────────

    public function earnings()
    {
        $vendorId    = $this->vendorId();
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
