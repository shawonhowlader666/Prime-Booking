@extends('layouts.vendor')
@section('title', 'Financial Reports | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="page-title m-0"><i class="fa-solid fa-chart-bar me-2" style="color:#7367f0;"></i>Financial Reports</h1>
        <form action="{{ route('vendor.reports') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="year" class="form-select form-select-sm" style="height:32px;font-size:12.5px;width:110px;" onchange="this.form.submit()">
                @for( = now()->year;  >= now()->year - 4; --)
                    <option value="{{  }}" {{  ==  ? 'selected' : '' }}>{{  }}</option>
                @endfor
            </select>
        </form>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong>Financial Reports</strong>
    </div>
</div>
<div class="page-content-area">
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(40,199,111,0.1);color:#28c76f;"><i class="fa-solid fa-coins"></i></div><div class="kpi-content"><div class="kpi-value">৳{{ number_format() }}</div><div class="kpi-label">{{  }} Gross Revenue</div></div><div class="kpi-accent-bar" style="background:#28c76f;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(24,144,255,0.1);color:#1890ff;"><i class="fa-solid fa-calendar-check"></i></div><div class="kpi-content"><div class="kpi-value">{{  }}</div><div class="kpi-label">Total Bookings</div></div><div class="kpi-accent-bar" style="background:#1890ff;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(115,103,240,0.1);color:#7367f0;"><i class="fa-solid fa-calculator"></i></div><div class="kpi-content"><div class="kpi-value">৳{{ number_format() }}</div><div class="kpi-label">Avg Booking Value</div></div><div class="kpi-accent-bar" style="background:#7367f0;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(234,84,85,0.1);color:#ea5455;"><i class="fa-solid fa-ban"></i></div><div class="kpi-content"><div class="kpi-value">{{  }}</div><div class="kpi-label">Cancellations</div></div><div class="kpi-accent-bar" style="background:#ea5455;"></div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="stockifly-card p-3">
                <div class="fw-bold mb-3" style="font-size:13px;">Monthly Revenue — {{  }}</div>
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="stockifly-card">
                <div class="p-3 border-bottom fw-bold" style="font-size:13px;">Top Properties by Bookings</div>
                @forelse( as )
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div style="font-size:12.5px;font-weight:600;">{{ ->name }}</div>
                    <span class="badge-gateway">{{ ->bookings_count }} bookings</span>
                </div>
                @empty
                <div class="p-3 text-center" style="color:#94a3b8;font-size:12.5px;">No data available</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column(, 'month')) !!},
        datasets: [{
            label: 'Revenue (BDT)',
            data: {!! json_encode(array_column(, 'revenue')) !!},
            backgroundColor: 'rgba(24,144,255,0.15)',
            borderColor: '#1890ff',
            borderWidth: 2,
            borderRadius: 4,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '৳' + (v>=1000 ? (v/1000).toFixed(0)+'K' : v) } } } }
});
</script>
@endsection
