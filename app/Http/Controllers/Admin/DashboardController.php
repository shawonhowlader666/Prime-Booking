<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $company = config('company');

        // ── Revenue KPIs: money actually received, aggregated in SQL (never loads bookings into memory) ──
        // Optional window from the dashboard's date-range selector: 7 / 30 / 90 days, or all time.
        $days  = in_array((int) request('days'), [7, 30, 90], true) ? (int) request('days') : null;
        $since = $days ? now()->subDays($days)->startOfDay() : null;

        $paid = fn () => Booking::query()
            ->where('bookings.payment_status', 'paid')
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->when($since, fn ($q) => $q->where('bookings.created_at', '>=', $since));

        $money = $paid()
            ->leftJoin('properties', 'properties.id', '=', 'bookings.property_id')
            ->selectRaw('COALESCE(SUM(bookings.total_price), 0) AS revenue')
            ->selectRaw('COALESCE(SUM(bookings.total_price * COALESCE(properties.commission_rate, 12) / 100), 0) AS commission')
            ->first();

        $totalRevenue     = (float) $money->revenue;
        $customCommission = (float) $money->commission;
        $vendorPayout     = $totalRevenue - $customCommission;
        $totalBookings    = Booking::when($since, fn ($q) => $q->where('created_at', '>=', $since))->count();
        $pendingBookings  = Booking::where('status', 'pending')->count();

        // ── Real DB Inventory Metrics (OTA Importer + Vendor Hotels) ──
        $totalDbProperties   = Property::count();
        $activeProperties    = Property::whereIn('status', ['active', 'published'])->count();
        $coveredCitiesCount  = Property::whereNotNull('city')->where('city', '!=', '')->distinct('city')->count('city');
        $totalRoomsCount     = \App\Models\Room::count();
        $totalVendorsCount   = User::where('role', 'vendor')->count();
        $totalUsers          = User::count();
        $pendingProperties   = Property::where('status', 'pending')->count();

        // ── Month-over-Month Revenue Trend ──
        // Date ranges (not MONTH()/YEAR() functions) so the created_at index is used.
        $collected = fn () => Booking::where('payment_status', 'paid')->whereIn('status', ['confirmed', 'completed']);
        $thisMonthRevenue = (float) $collected()->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('total_price');
        $lastMonthRevenue = (float) $collected()->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->sum('total_price');
        $momGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100.0 : 0.0);

        // ── Today's Live Metrics ──
        $todayRevenue   = (float) $collected()->where('created_at', '>=', today())->sum('total_price');
        $todayBookings  = Booking::where('created_at', '>=', today())->count();

        // ── Pending Alerts for Admin Action Bar ──
        $pendingPayouts     = Payout::where('status', 'pending')->count();
        $pendingProperties  = Property::where('status', 'pending')->count();
        $openInquiries      = Inquiry::whereIn('status', ['open', 'pending'])->count();
        $pendingAlerts = $pendingPayouts + $pendingProperties + $openInquiries;

        $stats = [
            'total_revenue'       => $totalRevenue,
            'monthly_revenue'     => $thisMonthRevenue,
            'last_month_revenue'  => $lastMonthRevenue,
            'mom_growth'          => $momGrowth,
            'today_revenue'       => $todayRevenue,
            'today_bookings'      => $todayBookings,
            'total_bookings'      => $totalBookings,
            'pending_bookings'    => $pendingBookings,
            'total_db_inventory'  => $totalDbProperties,
            'active_properties'   => $activeProperties,
            'pending_properties'  => $pendingProperties,
            'covered_cities'      => $coveredCitiesCount,
            'total_rooms'         => $totalRoomsCount,
            'total_vendors'       => $totalVendorsCount,
            'active_users'        => $totalUsers,
            'commission'          => round($customCommission),
            'vendor_payout'       => round($vendorPayout),
            'pending_payouts'     => $pendingPayouts,
            'open_inquiries'      => $openInquiries,
            'pending_alerts'      => $pendingAlerts,
            'days'                => $days,
        ];

        // ── Revenue Chart — last 7 months from DB ──
        $revenueChart = $this->getRevenueChartData();

        // ── Booking Status Breakdown — for pie chart ──
        $bookingStatusChart = $this->getBookingStatusData();

        // ── Top Properties by bookings ──
        $topProperties = Property::withCount(['bookings' => fn($q) => $q->where('status', '!=', 'cancelled')])
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        // ── Recent Bookings ──
        $recentBookings = Booking::with(['property:id,name,city', 'user:id,name,email'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'company', 'stats', 'recentBookings',
            'revenueChart', 'bookingStatusChart', 'topProperties'
        ));
    }

    /** Received revenue for the last 7 months, one grouped query. */
    private function getRevenueChartData(): array
    {
        $start = now()->subMonthsNoOverflow(6)->startOfMonth();
        $driver = DB::connection()->getDriverName();
        $bucket = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = Booking::where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('created_at', '>=', $start)
            ->selectRaw("{$bucket} AS ym, SUM(total_price) AS rev")
            ->groupBy('ym')
            ->pluck('rev', 'ym');

        $months = [];
        $revenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonthsNoOverflow($i);
            $months[] = $date->format('M Y');
            $revenue[] = (float) ($rows[$date->format('Y-m')] ?? 0);
        }

        return ['months' => $months, 'revenue' => $revenue];
    }

    /** Booking status breakdown for donut chart */
    private function getBookingStatusData(): array
    {
        $statuses = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return $statuses ?: ['confirmed' => 0, 'pending' => 0, 'cancelled' => 0, 'completed' => 0];
    }
}
