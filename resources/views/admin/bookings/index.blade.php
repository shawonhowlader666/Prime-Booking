@extends('layouts.admin')
@section('title', 'Booking Management | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Reservations</span>
        <span class="sep">-</span><strong style="color:#333;">All Bookings</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <h1 class="page-title">Booking &amp; Reservation Management</h1>
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('bookingsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('bookingsTable', 'bookings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <a href="{{ route('admin.bookings.export') }}" class="btn-export-csv" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            <a href="{{ route('admin.bookings.export-pdf') }}" target="_blank" class="btn-export-pdf" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
            <button type="button" class="btn-tbl-print" onclick="printTable('bookingsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="page-filters-bar">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">Booking Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All Payments</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid / Verified</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid / Pending</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Search Guest / Reference</label>
                    <div style="display:flex;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by ref, guest name, or phone…" style="border-radius:4px 0 0 4px !important; border-right:none;">
                        <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-2 text-end">
                    <a href="{{ route('admin.bookings.index') }}" class="btn-table-action" style="padding: 6px 12px; height: 32px; display: inline-flex; align-items: center;">Reset Filters</a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-receipt"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['total'] ?? 0 }}</p>
                        <p class="kpi-label">Total Bookings</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-globe"></i> Lifetime Reservations</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['confirmed'] ?? 0 }}</p>
                        <p class="kpi-label">Confirmed Stays</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check-double"></i> Ready for Check-in</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['pending'] ?? 0 }}</p>
                        <p class="kpi-label">Pending Action</p>
                        <p class="kpi-growth-down"><i class="fa-solid fa-hourglass-half"></i> Requires Approval</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#00cfe8;"><i class="fa-solid fa-bangladeshi-taka-sign"></i></div>
                    <div>
                        <p class="kpi-value">BDT {{ number_format($stats['revenue'] ?? 0) }}</p>
                        <p class="kpi-label">Paid Booking Revenue</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-shield-halved"></i> Verified Transactions</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h6 style="margin:0;">Master Reservations &amp; Guest Orders</h6>
                <span class="live-feed-badge">Live System Feed</span>
            </div>
            <div class="tbl-search-wrap">
                <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
                <input type="text" class="tbl-search-input" placeholder="Quick search table..." onkeyup="filterTableSearch('bookingsTable', this.value)">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="bookingsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('bookingsTable', this)" title="Select All Rows"></th>
                        <th>Booking Ref</th>
                        <th>Guest Details</th>
                        <th>Property Name</th>
                        <th>Check-In / Out</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions <div style="position:relative; display:inline-block; margin-left:4px;"><button type="button" class="btn-tbl-gear" onclick="toggleColVis('bookingsTable', this)" title="Column Settings"><i class="fa-solid fa-gear"></i></button><div class="col-vis-dropdown" id="colVisDropdown_bookingsTable" style="display:none;"></div></div></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <strong style="color:var(--primary); font-size:13px;">{{ $b->booking_reference ?? 'PRM-'.str_pad($b->id,4,'0',STR_PAD_LEFT) }}</strong>
                            <span style="font-size:11px; color:#8c8c8c; display:block;">{{ $b->created_at ? $b->created_at->format('M d, Y') : 'N/A' }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name ?? optional($b->user)->name ?? 'Guest User' }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $b->guest_phone ?? optional($b->user)->phone ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <strong style="font-size:12.5px; color:#334155;">{{ Str::limit(optional($b->property)->name ?? $b->property_name ?? 'Property N/A', 28) }}</strong>
                        </td>
                        <td style="font-size:12px; color:#595959;">
                            {{ $b->check_in }} → {{ $b->check_out }}
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:13px;">BDT {{ number_format($b->total_amount ?? $b->total_price ?? 0) }}</strong>
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($b->payment_status ?? 'pending') == 'paid' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($b->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($b->booking_status ?? $b->status ?? 'confirmed') }}">
                                {{ ucfirst($b->booking_status ?? $b->status ?? 'Confirmed') }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.bookings.show', $b->id) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View &amp; Edit Order
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this booking record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Order
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:#8c8c8c;">
                            <i class="fa-solid fa-inbox" style="font-size:28px; color:#d9d9d9; display:block; margin-bottom:8px;"></i>
                            No booking records found in database.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$bookings" :perPage="20" />
    </div>

</div>
@endsection

