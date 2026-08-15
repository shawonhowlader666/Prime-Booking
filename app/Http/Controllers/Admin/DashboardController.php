<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $company = config('company');

        // ── Real KPI Stats (Calculated per Property Contract Rate) ──
        $activeBookings = Booking::with('property:id,commission_rate')
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalRevenue     = (float) $activeBookings->sum('total_price');
        $customCommission = (float) $activeBookings->sum(function($b) {
            $rate = $b->property?->commission_rate ?? 12.00;
            return $b->total_price * ($rate / 100);
        });

        $vendorPayout    = $totalRevenue - $customCommission;
        $totalBookings       = Booking::count();
        $pendingBookings     = Booking::where('status', 'pending')->count();
        
        // ── Real DB Inventory Metrics (OTA Importer + Vendor Hotels) ──
        $totalDbProperties   = Property::count();
        $activeProperties    = Property::whereIn('status', ['active', 'published'])->count();
        $coveredCitiesCount  = Property::whereNotNull('city')->where('city', '!=', '')->distinct('city')->count('city');
        $totalRoomsCount     = \App\Models\Room::count();
        $totalVendorsCount   = User::where('role', 'vendor')->count();
        $totalUsers          = User::count();
        $pendingProperties   = Property::where('status', 'pending')->count();

        $stats = [
            'total_revenue'       => $totalRevenue,
            'monthly_revenue'     => $totalRevenue,
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

    /** Generate last 7 months revenue data for ApexCharts */
    private function getRevenueChartData(): array
    {
        $months  = [];
        $revenue = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $rev = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');

            $revenue[] = (float) $rev;
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
