@extends('layouts.vendor')
@section('title', 'Earnings & Payout Statement | Vendor Portal')

@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Earnings &amp; Payouts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <h1 class="page-title">Vendor Financial Statements</h1>
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('vendorEarningsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('vendorEarningsTable', 'vendor_earnings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('vendorEarningsTable', 'vendor_earnings')" title="Export to CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('vendorEarningsTable', 'vendor_earnings')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('vendorEarningsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
        </div>
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
    {{-- FILTER BAR --}}
    <div class="page-filters-bar mb-3">
        <form method="GET" action="{{ route('vendor.earnings.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-4">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-12 col-md-4 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="height:32px; font-size:12px; font-weight:600;" title="Apply Filter"><i class="fa-solid fa-filter me-1"></i> Filter Date Range</button>
                    <a href="{{ route('vendor.earnings.index') }}" class="btn btn-light border btn-sm" style="height:32px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- Monthly Breakdown Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h6 style="margin:0;">Monthly Revenue Statement</h6>
                <span class="live-feed-badge">Financial Audit</span>
            </div>
            <div class="tbl-search-wrap">
                <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
                <input type="text" class="tbl-search-input" placeholder="Quick search statement..." onkeyup="filterTableSearch('vendorEarningsTable', this.value)">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="vendorEarningsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('vendorEarningsTable', this)" title="Select All Rows"></th>
                        <th>Month</th>
                        <th>Gross Booking Volume</th>
                        <th>Platform Fee (12%)</th>
                        <th style="text-align:right;">Net Payable <div style="position:relative; display:inline-block; margin-left:4px;"><button type="button" class="btn-tbl-gear" onclick="toggleColVis('vendorEarningsTable', this)" title="Column Settings"><i class="fa-solid fa-gear"></i></button><div class="col-vis-dropdown" id="colVisDropdown_vendorEarningsTable" style="display:none;"></div></div></th>
                    </tr>
                </thead>
                <tbody>
                @foreach(($monthlyData['labels'] ?? []) as $idx => $label)
                    @php $rev = $monthlyData['revenue'][$idx] ?? 0; @endphp
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
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
