@extends('layouts.admin')
@section('title', 'Booking Management | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Reservations</span>
        <span class="sep">-</span><strong style="color:#333;">All Bookings</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Booking &amp; Reservation Management</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <a href="{{ route('admin.bookings.export') }}" class="btn-export-csv" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.bookings.export-pdf') }}" target="_blank" class="btn-export-pdf" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>
</div>

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
                <select name="payment" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('payment') == 'all' ? 'selected' : '' }}>All Payments</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="refunded" {{ request('payment') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Search Guest / Ref / Phone</label>
                <div style="display:flex;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, ref, email, phone..." style="border-radius:6px 0 0 6px; border-right:none;">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2 text-end">
                <a href="{{ route('admin.bookings.index') }}" class="btn-table-action" style="padding: 6px 12px; height: 32px; display: inline-flex; align-items: center;">Reset Filters</a>
            </div>
        </div>
    </form>
</div>

{{-- PAGE CONTENT --}}
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
        <div class="data-table-card-header">
            <h6>Master Reservations &amp; Guest Orders</h6>
            <span class="live-feed-badge">Live System Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest Details</th>
                        <th>Property Name</th>
                        <th>Check-In / Out</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
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
                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn-table-action primary">View &amp; Edit <i class="fa-solid fa-eye ms-1"></i></a>
                            <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this booking record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-table-action danger" style="margin-left:4px;">Delete <i class="fa-solid fa-trash"></i></button>
                            </form>
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

        <div style="padding:12px 16px; border-top:1px solid #f0f0f0; font-size:12px; color:#8c8c8c;">
            @if(method_exists($bookings, 'links'))
                {{ $bookings->links() }}
            @else
                Showing all current records
            @endif
        </div>
    </div>

</div>
@endsection
