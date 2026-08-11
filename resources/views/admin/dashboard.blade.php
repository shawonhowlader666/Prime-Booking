@extends('layouts.admin')

@section('title', 'Sales Summary Reports | Prime Aviation Admin')

@section('content')

{{-- =============================================
     PAGE HEADER
     ============================================= --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Reports</span>
        <span class="sep">-</span><strong style="color:#333;">Sales Summary</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Sales Summary Reports</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <button class="btn-export-csv" onclick="exportTable('csv')">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </button>
            <button class="btn-export-pdf" onclick="exportTable('pdf')">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>
</div>

{{-- =============================================
     FILTER BAR
     ============================================= --}}
<div class="page-filters-bar">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Tourist Region / City</label>
            <select id="filterRegion" class="form-select" onchange="filterTable()">
                <option value="">All Regions &amp; Destinations</option>
                <option>Cox's Bazar</option>
                <option>Dhaka</option>
                <option>Sylhet</option>
                <option>Sajek</option>
                <option>Sundarban</option>
                <option>Kuakata</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Customer Category</label>
            <select id="filterCategory" class="form-select" onchange="filterTable()">
                <option value="">All Customers &amp; Bookers</option>
                <option value="bKash">bKash MFS Customers</option>
                <option value="Nagad">Nagad MFS Customers</option>
                <option value="Card">Visa / Mastercard Users</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Date Range</label>
            <select class="form-select">
                <option selected>Last 30 Days (Aug 2026)</option>
                <option>Last 7 Days</option>
                <option>This Quarter</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Search Orders</label>
            <div style="display:flex;">
                <input type="text" id="searchInput" class="form-control" placeholder="Search ref or guest..." oninput="filterTable()" style="border-radius:6px 0 0 6px; border-right:none;">
                <button class="btn-search" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- =============================================
     PAGE CONTENT
     ============================================= --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-chart-line"></i></div>
                    <div>
                        <p class="kpi-value">BDT {{ number_format($stats['total_revenue'] ?? $stats['monthly_revenue'] ?? 4850000) }}</p>
                        <p class="kpi-label">Total Sales Volume (GBV)</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-up"></i> +18.4% vs last month</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-percent"></i></div>
                    <div>
                        <p class="kpi-value">BDT {{ number_format(($stats['monthly_revenue'] ?? 4850000) * 0.12) }}</p>
                        <p class="kpi-label">Platform Commission (12%)</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check-circle"></i> Net Platform Income</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#00cfe8;"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['total_bookings'] ?? 142 }} Stays</p>
                        <p class="kpi-label">Total Sales Orders</p>
                        <p class="kpi-growth-down"><i class="fa-solid fa-clock"></i> {{ $stats['pending_bookings'] ?? 12 }} Pending Action</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-hotel"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['total_properties'] ?? 9 }} Listings</p>
                        <p class="kpi-label">Active Properties</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-location-dot"></i> 100% Bangladesh Hubs</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- =============================================
         ANALYTICS CHARTS ROW
         ============================================= --}}
    <div class="row g-3 mb-4">
        {{-- Revenue Line Chart --}}
        <div class="col-12 col-lg-8">
            <div class="data-table-card" style="padding:0;">
                <div class="data-table-card-header">
                    <h6><i class="fa-solid fa-chart-line me-1" style="color:var(--primary);"></i> Revenue Trend — Last 7 Months</h6>
                    <span style="font-size:12px; color:#8c8c8c;">Monthly booking revenue (BDT)</span>
                </div>
                <div style="padding:8px 16px 16px;">
                    <div id="revenueChart" style="min-height:260px;"></div>
                </div>
            </div>
        </div>

        {{-- Booking Status Donut --}}
        <div class="col-12 col-lg-4">
            <div class="data-table-card" style="padding:0;">
                <div class="data-table-card-header">
                    <h6><i class="fa-solid fa-chart-pie me-1" style="color:#28c76f;"></i> Booking Status</h6>
                    <span style="font-size:12px; color:#8c8c8c;">All time</span>
                </div>
                <div style="padding:8px 16px 16px;">
                    <div id="statusChart" style="min-height:220px;"></div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; justify-content:center; margin-top:4px;">
                        @php $statusColors = ['confirmed'=>'#28c76f','pending'=>'#ff9f43','cancelled'=>'#ea5455','completed'=>'#1890ff']; @endphp
                        @foreach($bookingStatusChart as $status => $count)
                        <div style="display:flex; align-items:center; gap:5px; font-size:11px; color:#595959;">
                            <span style="width:10px; height:10px; border-radius:50%; background:{{ $statusColors[$status] ?? '#8c8c8c' }}; display:inline-block;"></span>
                            {{ ucfirst($status) }}: <strong>{{ $count }}</strong>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Properties + Quick Links Row --}}
    <div class="row g-3 mb-4">
        {{-- Top Properties --}}
        <div class="col-12 col-lg-7">
            <div class="data-table-card" style="padding:0;">
                <div class="data-table-card-header">
                    <h6><i class="fa-solid fa-trophy me-1" style="color:#ff9f43;"></i> Top Properties by Bookings</h6>
                    <a href="{{ route('admin.properties.index') }}" style="font-size:12px; color:var(--primary); text-decoration:none; font-weight:600;">View All →</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table-stockifly" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Property Name</th>
                                <th>City</th>
                                <th>Bookings</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($topProperties ?? [] as $i => $prop)
                            <tr>
                                <td><strong style="color:#8c8c8c; font-size:13px;">{{ $i + 1 }}</strong></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <img src="{{ $prop->primary_image ?? 'https://placehold.co/36x28/1890ff/white?text=H' }}"
                                             style="width:36px; height:28px; border-radius:4px; object-fit:cover;">
                                        <strong style="font-size:12.5px; color:#1e293b;">{{ Str::limit($prop->name, 28) }}</strong>
                                    </div>
                                </td>
                                <td style="font-size:12px; color:#595959;">{{ $prop->city }}</td>
                                <td><strong style="color:var(--primary);">{{ $prop->bookings_count ?? 0 }}</strong></td>
                                <td><span class="badge-status {{ $prop->status == 'active' ? 'active' : 'pending' }}">{{ ucfirst($prop->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:20px; color:#8c8c8c; font-size:12.5px;">No property data yet</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Admin Actions --}}
        <div class="col-12 col-lg-5">
            <div class="data-table-card">
                <div class="data-table-card-header">
                    <h6><i class="fa-solid fa-bolt me-1" style="color:#7367f0;"></i> Quick Admin Actions</h6>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:4px 0;">
                    @foreach([
                        [route('admin.properties.create'), 'fa-plus', '#1890ff', 'Add Property', 'List new hotel or ship'],
                        [route('admin.bookings.index'),   'fa-calendar-check', '#28c76f', 'Bookings', 'Manage reservations'],
                        [route('admin.users.index'),      'fa-users', '#ff9f43', 'Users', 'Manage all accounts'],
                        [route('admin.coupons.index'),    'fa-ticket', '#ea5455', 'Coupons', 'Promo codes'],
                        [route('admin.reviews.index'),    'fa-star', '#7367f0', 'Reviews', 'Moderate reviews'],
                        [route('admin.content.hero'),     'fa-image', '#00cfe8', 'CMS Hero', 'Edit homepage banner'],
                    ] as $action)
                    <a href="{{ $action[0] }}" style="display:flex; align-items:center; gap:10px; padding:12px; border:1px solid #f0f0f0; border-radius:8px; text-decoration:none; background:#fafafa; transition:all .15s;" onmouseover="this.style.borderColor='{{ $action[2] }}'; this.style.background='{{ $action[2] }}11';" onmouseout="this.style.borderColor='#f0f0f0'; this.style.background='#fafafa';">
                        <div style="width:34px; height:34px; border-radius:8px; background:{{ $action[2] }}22; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid {{ $action[1] }}" style="color:{{ $action[2] }}; font-size:14px;"></i>
                        </div>
                        <div>
                            <strong style="font-size:12.5px; color:#1e293b; display:block;">{{ $action[3] }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $action[4] }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Main Data Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Sales Orders &amp; Reservation Records</h6>
            <span class="live-feed-badge">Real-Time MySQL Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="salesTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Order Ref</th>
                        <th>Customer / Guest</th>
                        <th>Property &amp; Dates</th>
                        <th>Sales Amount</th>
                        <th>Payment Gateway</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">

                @forelse($recentBookings as $booking)
                    @php
                        // Support both real Eloquent models AND fallback (object)[] plain objects
                        $isEloquent   = $booking instanceof \App\Models\Booking;
                        $ref          = $isEloquent
                            ? 'PRM-' . date('Y') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)
                            : ($booking->booking_reference ?? 'PRM-' . date('Y') . '-0000');
                        $guestName    = $isEloquent ? ($booking->guest_name ?? optional($booking->user)->name ?? 'N/A') : ($booking->guest_name ?? 'N/A');
                        $guestPhone   = $isEloquent ? ($booking->guest_phone ?? optional($booking->user)->phone ?? '') : ($booking->guest_phone ?? '');
                        $propName     = $isEloquent ? optional($booking->property)->name : ($booking->property_name ?? 'N/A');
                        $city         = $isEloquent ? optional($booking->property)->city : '';
                        $checkIn      = $isEloquent ? ($booking->check_in ?? '') : ($booking->check_in ?? '');
                        $checkOut     = $isEloquent ? ($booking->check_out ?? '') : ($booking->check_out ?? '');
                        $amount       = $isEloquent ? ($booking->total_price ?? 0) : ($booking->total_price ?? 0);
                        $gateway      = $isEloquent ? ($booking->payment_method ?? 'Online') : ($booking->payment_method ?? 'Online');
                        $status       = $isEloquent ? ($booking->status ?? 'confirmed') : ($booking->status ?? 'confirmed');
                        $createdAt    = $isEloquent ? $booking->created_at : ($booking->created_at ?? now());
                    @endphp
                    <tr class="sales-row"
                        data-region="{{ strtolower($city) }}"
                        data-gateway="{{ strtolower($gateway) }}">
                        <td>
                            <a href="{{ $isEloquent ? route('admin.bookings.show', $booking->id) : route('admin.bookings.index') }}" class="order-ref-link">{{ $ref }}</a>
                            <span class="order-date">{{ \Carbon\Carbon::parse($createdAt)->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $guestName }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $guestPhone }}</span>
                        </td>
                        <td>
                            <span style="font-size:13px; color:#334155;">{{ \Illuminate\Support\Str::limit($propName ?? 'N/A', 30) }}</span>
                            <span class="order-date">{{ $checkIn }} to {{ $checkOut }}</span>
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:13px;">BDT {{ number_format($amount) }}</strong>
                        </td>
                        <td>
                            <span class="badge-gateway">{{ $gateway }}</span>
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($status) }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('booking.voucher.download', $ref) }}" target="_blank" class="btn-table-action primary">
                                PDF Voucher <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#8c8c8c; font-size:13px;">
                            <i class="fa-solid fa-inbox" style="font-size:28px; color:#d9d9d9; display:block; margin-bottom:8px;"></i>
                            No bookings found yet.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

        <div style="padding:10px 16px; border-top:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; font-size:12px; color:#8c8c8c;">
            <span>Showing {{ $recentBookings->count() }} latest reservation records</span>
            <a href="{{ route('admin.properties.index') }}" style="color:var(--primary); font-weight:600; font-size:12px; text-decoration:none;">
                View All Inventory <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

    </div>

</div>

@endsection

@section('scripts')
{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
<script>
// ── Revenue Area Chart ──────────────────────────────────────────────────
const revenueMonths  = @json($revenueChart['months']);
const revenueValues  = @json($revenueChart['revenue']);

new ApexCharts(document.getElementById('revenueChart'), {
    series: [{ name: 'Revenue (BDT)', data: revenueValues }],
    chart: { type: 'area', height: 260, toolbar: { show: false }, sparkline: { enabled: false } },
    colors: ['#1890ff'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
    stroke: { curve: 'smooth', width: 2.5 },
    xaxis: { categories: revenueMonths, labels: { style: { fontSize: '11px', colors: '#8c8c8c' } } },
    yaxis: { labels: { formatter: v => 'BDT ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : (v/1000).toFixed(0)+'K'), style: { fontSize: '11px', colors: '#8c8c8c' } } },
    grid: { borderColor: '#f0f0f0', strokeDashArray: 3 },
    tooltip: { y: { formatter: v => 'BDT ' + new Intl.NumberFormat('en-BD').format(v) } },
    markers: { size: 4, colors: ['#1890ff'], strokeColors: '#fff', strokeWidth: 2 },
    dataLabels: { enabled: false },
}).render();

// ── Booking Status Donut Chart ───────────────────────────────────────────
const statusData   = @json(array_values($bookingStatusChart));
const statusLabels = @json(array_map('ucfirst', array_keys($bookingStatusChart)));
const statusColors = { 'Confirmed':'#28c76f', 'Pending':'#ff9f43', 'Cancelled':'#ea5455', 'Completed':'#1890ff' };
const chartColors  = statusLabels.map(l => statusColors[l] || '#8c8c8c');

new ApexCharts(document.getElementById('statusChart'), {
    series: statusData,
    labels: statusLabels,
    chart: { type: 'donut', height: 220, toolbar: { show: false } },
    colors: chartColors,
    plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '12px', color: '#8c8c8c', formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0) } } } } },
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: { width: 2 },
    tooltip: { y: { formatter: v => v + ' bookings' } },
}).render();

// ── Table Filter ────────────────────────────────────────────────────────
function filterTable() {
    const region   = document.getElementById('filterRegion').value.toLowerCase();
    const category = document.getElementById('filterCategory').value.toLowerCase();
    const search   = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#salesTable tbody .sales-row').forEach(function(row) {
        const ok = (!region   || (row.dataset.region  || '').includes(region))
                && (!category || (row.dataset.gateway || '').includes(category))
                && (!search   || row.textContent.toLowerCase().includes(search));
        row.style.display = ok ? '' : 'none';
    });
}

function exportTable(type) {
    alert('Exporting ' + type.toUpperCase() + ' — Sales Summary Report...');
}
</script>
@endsection

