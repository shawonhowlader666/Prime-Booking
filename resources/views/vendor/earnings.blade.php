@extends('layouts.vendor')
@section('title', 'Earnings & Payout Statement | Vendor Portal')

@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Earnings &amp; Payouts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Vendor Financial Statements</h1>
    </div>
</div>

<div class="page-content-area">

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <p class="kpi-value">{{ CurrencyService::format($totalRevenue) }}</p>
                        <p class="kpi-label">Gross Revenue</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-up"></i> Total Bookings Value</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div>
                        <p class="kpi-value">{{ CurrencyService::format($totalRevenue * 0.88) }}</p>
                        <p class="kpi-label">Net Earnings (88%)</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check"></i> Platform Commission 12%</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-building-columns"></i></div>
                    <div>
                        <p class="kpi-value">{{ $bestProperty?->name ?? 'All Properties' }}</p>
                        <p class="kpi-label">Top Grossing Listing</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-star"></i> Highest Occupancy</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- Monthly Breakdown Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Monthly Revenue Statement</h6>
        </div>

        <x-table-toolbar tableId="vendorEarningsTable" exportName="vendor_earnings" searchPlaceholder="Search month..." />

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="vendorEarningsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Gross Booking Volume</th>
                        <th>Platform Fee (12%)</th>
                        <th>Net Payable</th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($monthlyData['labels'] ?? []) as $idx => $label)
                    @php $rev = $monthlyData['revenue'][$idx] ?? 0; @endphp
                    <tr>
                        <td><strong>{{ $label }}</strong></td>
                        <td>{{ CurrencyService::format($rev) }}</td>
                        <td style="color:#dc2626;">-{{ CurrencyService::format($rev * 0.12) }}</td>
                        <td style="color:#16a34a; font-weight:700;">{{ CurrencyService::format($rev * 0.88) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
