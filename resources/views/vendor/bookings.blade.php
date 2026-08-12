@extends('layouts.vendor')
@section('title', 'Manage Guest Bookings | Vendor Portal')

@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Bookings Ledger</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <h1 class="page-title">Guest Reservations &amp; Bookings</h1>
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('vendorBookingsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('vendorBookingsTable', 'vendor_bookings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('vendorBookingsTable', 'vendor_bookings')" title="Export to CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('vendorBookingsTable', 'vendor_bookings')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('vendorBookingsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>
</div>

<div class="page-content-area">

    {{-- Filter Bar --}}
    <div class="page-filters-bar">
        <form action="{{ route('vendor.bookings.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Booking Status</label>
                    <select name="status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Search Guest / Reference</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search ref, guest name, phone..." style="height:32px; font-size:12px;">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="height:32px; font-size:12px; font-weight:600;" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    <a href="{{ route('vendor.bookings.index') }}" class="btn btn-light border btn-sm" style="height:32px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h6 style="margin:0;">Guest Reservations</h6>
                <span class="live-feed-badge">Live Ledger</span>
            </div>
            <div class="tbl-search-wrap">
                <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
                <input type="text" class="tbl-search-input" placeholder="Quick search bookings..." onkeyup="filterTableSearch('vendorBookingsTable', this.value)">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="vendorBookingsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('vendorBookingsTable', this)" title="Select All Rows"></th>
                        <th>Booking Ref</th>
                        <th>Guest Info</th>
                        <th>Property</th>
                        <th>Check-in → Check-out</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions <div style="position:relative; display:inline-block; margin-left:4px;"><button type="button" class="btn-tbl-gear" onclick="toggleColVis('vendorBookingsTable', this)" title="Column Settings"><i class="fa-solid fa-gear"></i></button><div class="col-vis-dropdown" id="colVisDropdown_vendorBookingsTable" style="display:none;"></div></div></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td style="font-family:monospace; font-weight:700; font-size:13px;">
                            {{ $b->booking_reference }}
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $b->guest_email }}</span>
                        </td>
                        <td>
                            <span style="font-size:12.5px; font-weight:600;">{{ $b->property?->name ?? 'Property' }}</span>
                        </td>
                        <td style="font-size:12px;">
                            {{ \Carbon\Carbon::parse($b->check_in)->format('M d') }} → {{ \Carbon\Carbon::parse($b->check_out)->format('M d, Y') }}
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:14px;">{{ CurrencyService::format($b->amount) }}</strong>
                        </td>
                        <td>
                            <span class="badge-gateway">{{ ucfirst($b->payment_method ?? 'bKash') }}</span>
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($b->effective_status) == 'confirmed' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('vendor.bookings.show', $b->booking_reference) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View Reservation Details
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:#8c8c8c;">
                            No reservations found for your properties.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($bookings, 'links'))
        <div style="padding:12px 16px; border-top:1px solid #f0f0f0;">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
