@extends('layouts.admin')
@section('title', 'Booking Management — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Booking &amp; Reservation Management</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('bookingsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('bookingsTable', 'bookings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <a href="{{ route('admin.bookings.export') }}" class="btn-export-csv" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            <a href="{{ route('admin.bookings.export-pdf') }}" target="_blank" class="btn-export-pdf" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
            <button type="button" class="btn-tbl-print" onclick="printTable('bookingsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Reservations</span>
        <span class="sep">-</span><strong style="color:#333;">All Bookings</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="page-filters-bar mb-3">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Booking Status</label>
                    <select name="status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Payment Status</label>
                    <select name="payment_status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All Payments</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid / Verified</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Unpaid / Pending</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Search Ref / Guest</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search ref, guest name, phone..." style="height:32px; font-size:12px;">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn-add-primary flex-grow-1" style="height:32px; font-size:12px; justify-content:center;" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn-tbl-copy" style="height:32px; font-size:12px; display:inline-flex; align-items:center; justify-content:center; padding:0 10px;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI SUMMARY ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div class="kpi-title">Total Bookings</div>
                        <div class="kpi-value">{{ number_format($stats['total'] ?? 0) }}</div>
                        <div class="kpi-subtitle">All-time reservations</div>
                    </div>
                    <div class="kpi-icon-wrap" style="background:#eff6ff; color:#2563eb;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div class="kpi-title">Confirmed Stays</div>
                        <div class="kpi-value text-success">{{ number_format($stats['confirmed'] ?? 0) }}</div>
                        <div class="kpi-subtitle">Verified &amp; active bookings</div>
                    </div>
                    <div class="kpi-icon-wrap" style="background:#f0fdf4; color:#16a34a;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div class="kpi-title">Pending Confirmation</div>
                        <div class="kpi-value text-warning">{{ number_format($stats['pending'] ?? 0) }}</div>
                        <div class="kpi-subtitle">Awaiting confirmation/payment</div>
                    </div>
                    <div class="kpi-icon-wrap" style="background:#fffbeb; color:#d97706;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div class="kpi-title">Paid Revenue</div>
                        <div class="kpi-value text-primary">BDT {{ number_format($stats['revenue'] ?? 0) }}</div>
                        <div class="kpi-subtitle">Completed &amp; verified revenue</div>
                    </div>
                    <div class="kpi-icon-wrap" style="background:#f5f3ff; color:#7c3aed;">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE CARD --}}
    <div class="data-table-card">
        <div class="data-table-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h6 style="margin:0; font-weight:700; color:#1e293b;">Reservation Ledger</h6>
                <span class="live-feed-badge">Live System Feed</span>
            </div>
            <div class="tbl-search-wrap">
                <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
                <input type="text" class="tbl-search-input" placeholder="Quick search in table..." onkeyup="filterTableSearch('bookingsTable', this.value)">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="bookingsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('bookingsTable', this)" title="Select All Rows"></th>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Guest Phone</th>
                        <th>Property &amp; Room</th>
                        <th>Check-In / Out</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th style="text-align:right; width:80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    @php
                        $gPhone   = $b->guest_phone ?? optional($b->user)->phone ?? null;
                        $gEmail   = $b->guest_email ?? optional($b->user)->email ?? null;
                        $gPhNum   = $gPhone ? preg_replace('/[^0-9]/', '', $gPhone) : null;
                        $gWaNum   = $gPhNum ? (str_starts_with($gPhNum, '880') ? $gPhNum : '880' . ltrim($gPhNum, '0')) : null;
                        $adminPreviewJson = json_encode([
                            'id'                => $b->id,
                            'reference'         => $b->booking_reference ?? 'PRM-'.str_pad($b->id,4,'0',STR_PAD_LEFT),
                            'guest_name'        => $b->guest_name ?? optional($b->user)->name ?? 'Guest User',
                            'guest_phone'       => $gPhone ?? 'N/A',
                            'guest_email'       => $gEmail ?? 'N/A',
                            'property_name'     => optional($b->property)->name ?? $b->property_name ?? 'Property Stay',
                            'room_name'         => optional($b->room)->name ?? 'Standard Room',
                            'check_in'          => $b->check_in ? \Carbon\Carbon::parse($b->check_in)->format('M d, Y') : 'N/A',
                            'check_out'         => $b->check_out ? \Carbon\Carbon::parse($b->check_out)->format('M d, Y') : 'N/A',
                            'nights'            => $b->nights_count ?? $b->nights ?? 1,
                            'guests'            => $b->guests ?? 1,
                            'subtotal'          => number_format((float)($b->subtotal ?? $b->amount ?? 0)),
                            'discount'          => number_format((float)($b->discount_amount ?? 0)),
                            'coupon'            => $b->coupon_code,
                            'commission'        => number_format((float)($b->commission_amount ?? 0)),
                            'vendor_payout'     => number_format((float)($b->vendor_payout_amount ?? 0)),
                            'tax'               => number_format((float)($b->tax_amount ?? 0)),
                            'total'             => number_format((float)($b->amount ?? $b->total_price ?? 0)),
                            'payment_method'    => strtoupper(str_replace('_', ' ', $b->payment_method ?? 'CASH')),
                            'payment_status'    => strtoupper($b->payment_status ?? 'UNPAID'),
                            'status'            => ucfirst($b->effective_status),
                            'special_requests'  => $b->special_requests ?? 'None',
                            'show_url'          => route('admin.bookings.show', $b->id),
                            'wa_url'            => $gWaNum ? "https://wa.me/{$gWaNum}?text=" . urlencode("Hello " . ($b->guest_name ?? 'Guest') . ", regarding your booking " . ($b->booking_reference ?? 'PRM-'.$b->id) . " at " . (optional($b->property)->name ?? 'our hotel') . ".") : null,
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        
                        {{-- Booking Ref --}}
                        <td>
                            <strong style="color:var(--primary); font-size:13px; font-family:monospace;">{{ $b->booking_reference ?? 'PRM-'.str_pad($b->id,4,'0',STR_PAD_LEFT) }}</strong>
                            <span style="font-size:11px; color:#8c8c8c; display:block;">Booked: {{ $b->created_at ? $b->created_at->format('M d, Y') : 'N/A' }}</span>
                        </td>

                        {{-- Guest Name & Email --}}
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name ?? optional($b->user)->name ?? 'Guest User' }}</strong>
                            <div style="font-size:11px; color:#94a3b8; margin-top:1px;">{{ $gEmail ?? 'No email' }}</div>
                        </td>

                        {{-- Dedicated Phone Column --}}
                        <td>
                            @if($gPhone)
                                <a href="tel:{{ $gPhone }}" class="text-dark fw-bold d-inline-flex align-items-center gap-1" style="text-decoration:none; font-size:12px;" title="Call Guest">
                                    <i class="fa-solid fa-phone text-primary" style="font-size:10.5px;"></i> {{ $gPhone }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size:11px;">N/A</span>
                            @endif
                        </td>

                        {{-- Property & Room --}}
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ Str::limit(optional($b->property)->name ?? $b->property_name ?? 'Property N/A', 26) }}</strong>
                            <span style="font-size:11px; color:#64748b;">
                                <i class="fa-solid fa-bed me-1 text-secondary"></i>{{ optional($b->room)->name ?? 'Standard Room' }} • {{ $b->guests ?? 1 }} Guest(s)
                            </span>
                        </td>

                        {{-- Check-In / Out --}}
                        <td style="font-size:12px; color:#475569; white-space:nowrap;">
                            <div><i class="fa-solid fa-calendar-days text-primary me-1"></i>{{ $b->check_in }} → {{ $b->check_out }}</div>
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:10px; font-weight:600;">
                                <i class="fa-solid fa-moon me-1 text-warning"></i>{{ $b->nights_count }} Night(s)
                            </span>
                        </td>

                        {{-- Total Amount --}}
                        <td>
                            <strong style="color:var(--primary); font-size:13.5px;">BDT {{ number_format($b->amount ?? 0) }}</strong>
                            @if($b->discount_amount > 0)
                                <div style="font-size:10px; color:#16a34a;">-৳ {{ number_format($b->discount_amount) }} (Coupon)</div>
                            @endif
                        </td>

                        {{-- Payment Method & Status --}}
                        <td>
                            @if(strtolower($b->payment_status ?? '') === 'paid')
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold border border-success border-opacity-25" style="font-size:10px; padding:2px 8px; border-radius:4px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> PAID
                                </span>
                            @elseif(strtolower($b->payment_status ?? '') === 'refunded')
                                <span class="badge bg-info bg-opacity-10 text-info fw-bold border border-info border-opacity-25" style="font-size:10px; padding:2px 8px; border-radius:4px;">
                                    <i class="fa-solid fa-rotate-left me-1"></i> REFUNDED
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-15 text-dark fw-bold border border-warning border-opacity-25" style="font-size:10px; padding:2px 8px; border-radius:4px;">
                                    <i class="fa-solid fa-clock me-1 text-warning"></i> UNPAID
                                </span>
                            @endif
                            @if($b->payment_method)
                            <div style="margin-top:3px;">
                                <span class="badge bg-light text-dark border" style="font-size:10px; font-weight:600; text-transform:uppercase;">
                                    <i class="fa-solid fa-wallet text-secondary me-1"></i>{{ str_replace('_', ' ', $b->payment_method) }}
                                </span>
                            </div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge-status {{ strtolower($b->effective_status) == 'confirmed' ? 'confirmed' : (strtolower($b->effective_status) == 'cancelled' ? 'cancelled' : 'pending') }}">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </td>

                        {{-- 3-Dot Actions Dropdown Menu --}}
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown d-inline-block position-relative">
                                <button class="btn btn-light btn-sm shadow-none border-0 action-3dot-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:6px; background:#f1f5f9; color:#334155;" title="Manage Booking">
                                    <i class="fa-solid fa-ellipsis-vertical fs-6"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:6px; font-size:12.5px; border:1px solid #e2e8f0; padding:5px 0; min-width:190px; z-index:1060; margin-top:2px;">
                                    
                                    {{-- 1. Quick Preview Modal Trigger --}}
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 text-primary fw-semibold" onclick="openAdminBookingPreviewModal({{ $adminPreviewJson }})">
                                            <i class="fa-solid fa-eye me-2 text-primary"></i> Quick Preview
                                        </button>
                                    </li>

                                    {{-- 2. Full Order Details Page --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-dark" href="{{ route('admin.bookings.show', $b->id) }}">
                                            <i class="fa-solid fa-file-lines text-secondary me-2"></i> Full Order Details
                                        </a>
                                    </li>

                                    {{-- 3. Mark Confirmed --}}
                                    <li>
                                        <form action="{{ route('admin.bookings.update-status', $b->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-success fw-bold">
                                                <i class="fa-solid fa-circle-check me-2"></i> Mark Confirmed
                                            </button>
                                        </form>
                                    </li>

                                    {{-- 4. Mark Paid (if unpaid) --}}
                                    @if(strtolower($b->payment_status ?? '') !== 'paid')
                                    <li>
                                        <form action="{{ route('admin.bookings.update-payment', $b->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="paid">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-primary fw-bold">
                                                <i class="fa-solid fa-credit-card me-2"></i> Mark Payment Paid
                                            </button>
                                        </form>
                                    </li>
                                    @endif

                                    @if(isset($gPhone) && $gPhone)
                                    <li><hr class="dropdown-divider my-1"></li>
                                    {{-- 5. Call Guest --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-dark" href="tel:{{ $gPhone }}">
                                            <i class="fa-solid fa-phone text-primary me-2"></i> Call Guest
                                        </a>
                                    </li>
                                    {{-- 6. WhatsApp Guest --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" style="color:#16a34a;" href="https://wa.me/{{ $gWaNum }}?text={{ urlencode('Hello ' . ($b->guest_name ?? 'Guest') . ', regarding your booking ' . ($b->booking_reference ?? 'PRM-'.$b->id) . ' at ' . (optional($b->property)->name ?? 'our hotel') . '.') }}" target="_blank">
                                            <i class="fa-brands fa-whatsapp me-2"></i> WhatsApp Guest
                                        </a>
                                    </li>
                                    {{-- 7. Send SMS --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-secondary" href="sms:{{ $gPhone }}">
                                            <i class="fa-solid fa-comment-sms me-2"></i> Send SMS
                                        </a>
                                    </li>
                                    @endif

                                    <li><hr class="dropdown-divider my-1"></li>
                                    {{-- 8. Delete Booking --}}
                                    <li>
                                        <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this booking record permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Booking
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Booking Records Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no reservations matching your current filter criteria in the database.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$bookings" :perPage="20" />
    </div>

</div>

{{-- ── Admin Interactive Quick View / Preview Modal ────────────────────────────── --}}
<div class="modal fade" id="adminBookingPreviewModal" tabindex="-1" aria-labelledby="adminBookingPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header text-white" style="background:linear-gradient(135deg, #1e293b, #0f172a); padding:16px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-shield-halved fs-5 text-warning"></i>
                    <div>
                        <h5 class="modal-title m-0 fw-bold" id="adminBookingPreviewModalLabel" style="font-size:16px;">Admin Reservation Overview</h5>
                        <small class="text-secondary" style="font-size:11.5px;">Reference: <strong id="apv_ref" class="text-white font-monospace"></strong></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background:#f8fafc;">
                <div class="row g-3">
                    
                    {{-- 1. Guest Information Card --}}
                    <div class="col-md-6">
                        <div class="card border p-3 h-100 bg-white rounded-3 shadow-xs">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom text-dark" style="font-size:12.5px; text-transform:uppercase;">
                                <i class="fa-solid fa-user me-1 text-primary"></i> Guest Information
                            </h6>
                            <div class="d-flex flex-column gap-2" style="font-size:12.5px;">
                                <div><span class="text-muted">Full Name:</span> <strong id="apv_name" class="text-dark"></strong></div>
                                <div><span class="text-muted">Phone:</span> <a href="" id="apv_phone_link" class="fw-bold text-primary text-decoration-none"></a></div>
                                <div><span class="text-muted">Email:</span> <span id="apv_email" class="text-dark"></span></div>
                                <div><span class="text-muted">Special Requests:</span> <span id="apv_requests" class="text-secondary fst-italic"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Stay & Room Details Card --}}
                    <div class="col-md-6">
                        <div class="card border p-3 h-100 bg-white rounded-3 shadow-xs">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom text-dark" style="font-size:12.5px; text-transform:uppercase;">
                                <i class="fa-solid fa-hotel me-1 text-primary"></i> Property &amp; Room
                            </h6>
                            <div class="d-flex flex-column gap-2" style="font-size:12.5px;">
                                <div><span class="text-muted">Property:</span> <strong id="apv_property" class="text-dark"></strong></div>
                                <div><span class="text-muted">Room Type:</span> <span id="apv_room" class="fw-semibold text-secondary"></span></div>
                                <div><span class="text-muted">Check-In:</span> <strong id="apv_checkin" class="text-dark"></strong></div>
                                <div><span class="text-muted">Check-Out:</span> <strong id="apv_checkout" class="text-dark"></strong></div>
                                <div><span class="text-muted">Duration:</span> <span id="apv_duration" class="badge bg-light text-dark border"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Financial, Commission & Payout Summary --}}
                    <div class="col-12">
                        <div class="card border p-3 bg-white rounded-3 shadow-xs">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom text-dark d-flex align-items-center justify-content-between" style="font-size:12.5px; text-transform:uppercase;">
                                <span><i class="fa-solid fa-receipt me-1 text-success"></i> Full Financial &amp; Commission Ledger</span>
                                <span id="apv_status_badge"></span>
                            </h6>
                            <div class="row g-2 align-items-center" style="font-size:12.5px;">
                                <div class="col-sm-2">
                                    <div class="text-muted" style="font-size:11px;">Subtotal</div>
                                    <strong class="text-dark">৳ <span id="apv_subtotal"></span></strong>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-muted" style="font-size:11px;">Discount</div>
                                    <span class="text-success fw-bold">- ৳ <span id="apv_discount"></span></span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">Admin Commission</div>
                                    <span class="text-primary fw-bold">৳ <span id="apv_commission"></span></span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">Vendor Net Payout</div>
                                    <span class="text-secondary fw-bold">৳ <span id="apv_vendor_payout"></span></span>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-muted" style="font-size:11px;">Grand Total</div>
                                    <strong class="text-primary fs-6">৳ <span id="apv_total"></span></strong>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div style="font-size:12px;">
                                    <span class="text-muted">Payment Gateway:</span> <strong id="apv_gateway"></strong>
                                </div>
                                <div>
                                    <span class="text-muted me-1">Payment Status:</span>
                                    <span id="apv_payment_badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <a href="" id="apv_wa_btn" target="_blank" class="btn btn-success btn-sm fw-bold px-3">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Guest
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="" id="apv_full_btn" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fa-solid fa-file-lines me-1"></i> Full Order Details
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openAdminBookingPreviewModal(data) {
    document.getElementById('apv_ref').innerText = data.reference;
    document.getElementById('apv_name').innerText = data.guest_name;
    document.getElementById('apv_phone_link').innerText = data.guest_phone;
    document.getElementById('apv_phone_link').href = 'tel:' + data.guest_phone;
    document.getElementById('apv_email').innerText = data.guest_email;
    document.getElementById('apv_requests').innerText = data.special_requests;
    
    document.getElementById('apv_property').innerText = data.property_name;
    document.getElementById('apv_room').innerText = data.room_name;
    document.getElementById('apv_checkin').innerText = data.check_in;
    document.getElementById('apv_checkout').innerText = data.check_out;
    document.getElementById('apv_duration').innerText = data.nights + ' night(s) · ' + data.guests + ' guest(s)';
    
    document.getElementById('apv_subtotal').innerText = data.subtotal;
    document.getElementById('apv_discount').innerText = data.discount + (data.coupon ? ' (' + data.coupon + ')' : '');
    document.getElementById('apv_commission').innerText = data.commission;
    document.getElementById('apv_vendor_payout').innerText = data.vendor_payout;
    document.getElementById('apv_total').innerText = data.total;
    document.getElementById('apv_gateway').innerText = data.payment_method;
    
    const isPaid = data.payment_status.toLowerCase() === 'paid';
    document.getElementById('apv_payment_badge').innerHTML = isPaid 
        ? '<span class="badge bg-success text-white fw-bold px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> PAID</span>'
        : '<span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-clock me-1"></i> UNPAID</span>';
        
    document.getElementById('apv_status_badge').innerHTML = '<span class="badge bg-primary text-white px-2 py-1">' + data.status + '</span>';
    
    document.getElementById('apv_full_btn').href = data.show_url;
    
    const waBtn = document.getElementById('apv_wa_btn');
    if (data.wa_url) {
        waBtn.style.display = 'inline-flex';
        waBtn.href = data.wa_url;
    } else {
        waBtn.style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('adminBookingPreviewModal'));
    modal.show();
}
</script>
@endsection
