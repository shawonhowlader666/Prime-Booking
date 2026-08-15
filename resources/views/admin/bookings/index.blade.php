@extends('layouts.admin')
@section('title', 'Booking Management — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Booking &amp; Reservation Management</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('bookingsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('bookingsTable', 'bookings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <a href="{{ route('admin.bookings.export') }}" class="btn-export-csv" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</a>
            <a href="{{ route('admin.bookings.export-pdf') }}" target="_blank" class="btn-export-pdf" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
            <button type="button" class="btn-tbl-print" onclick="printTable('bookingsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button></div>
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
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL BOOKINGS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? 0 }} Orders</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">CONFIRMED STAYS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['confirmed'] ?? 0 }} Stays</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING ACTION</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['pending'] ?? 0 }} Pending</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">PAID BOOKING REVENUE</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1890ff; margin:0;">BDT {{ number_format($stats['revenue'] ?? 0) }}</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-receipt me-1 text-primary"></i> Master Reservations &amp; Guest Orders ({{ count($bookings) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search table..." onkeyup="filterTableSearch('bookingsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="bookingsTable">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('bookingsTable', this)" title="Select All Rows"></th>
                        <th>Booking Ref</th>
                        <th>Guest Details</th>
                        <th>Property &amp; Room</th>
                        <th>Check-In / Out</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <strong style="color:var(--primary); font-size:13px; font-family:monospace;">{{ $b->booking_reference ?? 'PRM-'.str_pad($b->id,4,'0',STR_PAD_LEFT) }}</strong>
                            <span style="font-size:11px; color:#8c8c8c; display:block;">Booked: {{ $b->created_at ? $b->created_at->format('M d, Y') : 'N/A' }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name ?? optional($b->user)->name ?? 'Guest User' }}</strong>
                            @php
                                $gPhone   = $b->guest_phone ?? optional($b->user)->phone ?? null;
                                $gEmail   = $b->guest_email ?? optional($b->user)->email ?? null;
                                $gPhNum   = $gPhone ? preg_replace('/[^0-9]/', '', $gPhone) : null;
                                $gWaNum   = $gPhNum ? (str_starts_with($gPhNum, '880') ? $gPhNum : '880' . ltrim($gPhNum, '0')) : null;
                            @endphp
                            <div style="font-size:11.5px; color:#475569; display:flex; align-items:center; gap:5px; margin-top:3px; flex-wrap:wrap;">
                                @if($gPhone)
                                    <a href="tel:{{ $gPhone }}" class="text-dark fw-semibold" style="text-decoration:none; font-size:12px;" title="Call Guest">
                                        <i class="fa-solid fa-phone text-secondary" style="font-size:10px;"></i> {{ $gPhone }}
                                    </a>
                                    {{-- WhatsApp --}}
                                    <a href="https://wa.me/{{ $gWaNum }}" target="_blank"
                                       class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"
                                       style="font-size:10px; padding:2px 6px; text-decoration:none;" title="WhatsApp Chat">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    {{-- IP/VoIP Call placeholder --}}
                                    <a href="tel:{{ $gPhone }}" data-ipcall="{{ $gPhone }}"
                                       class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"
                                       style="font-size:10px; padding:2px 6px; text-decoration:none;" title="IP/VoIP Call">
                                        <i class="fa-solid fa-headset"></i>
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size:11px;">No Phone</span>
                                @endif
                            </div>
                            @if($gEmail)
                                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">{{ $gEmail }}</div>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ Str::limit(optional($b->property)->name ?? $b->property_name ?? 'Property N/A', 28) }}</strong>
                            <span style="font-size:11px; color:#64748b;">
                                <i class="fa-solid fa-bed me-1 text-secondary"></i>{{ optional($b->room)->name ?? 'Standard Room' }} • {{ $b->guests ?? 1 }} Guest(s)
                            </span>
                        </td>
                        <td style="font-size:12px; color:#475569;">
                            <div><i class="fa-solid fa-calendar-days text-primary me-1"></i>{{ $b->check_in }} → {{ $b->check_out }}</div>
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:10px; font-weight:600;">
                                <i class="fa-solid fa-moon me-1 text-warning"></i>{{ $b->nights_count }} Night(s)
                            </span>
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:13.5px;">BDT {{ number_format($b->amount ?? 0) }}</strong>
                        </td>
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
                                    <i class="fa-solid fa-wallet text-primary me-1"></i>{{ str_replace('_', ' ', $b->payment_method) }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($b->effective_status) == 'confirmed' ? 'confirmed' : (strtolower($b->effective_status) == 'cancelled' ? 'cancelled' : 'pending') }}">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.bookings.show', $b->id) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View Order Details
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.bookings.update-status', $b->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-success">
                                                <i class="fa-solid fa-circle-check me-2"></i> Mark Confirmed
                                            </button>
                                        </form>
                                    </li>
                                    @if(strtolower($b->payment_status ?? '') !== 'paid')
                                    <li>
                                        <form action="{{ route('admin.bookings.update-payment', $b->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="paid">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-primary">
                                                <i class="fa-solid fa-credit-card me-2"></i> Mark Payment Paid
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(isset($gPhone) && $gPhone)
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        {{-- 📞 IP/VoIP Call — connect Twilio / Vonage / PortSIP here --}}
                                        <a class="dropdown-item py-1.5 px-3 text-primary" href="tel:{{ $gPhone }}" data-ipcall="{{ $gPhone }}">
                                            <i class="fa-solid fa-headset me-2"></i> IP/VoIP Call Guest
                                        </a>
                                    </li>
                                    <li>
                                        {{-- 💬 WhatsApp Chat --}}
                                        <a class="dropdown-item py-1.5 px-3" style="color:#25d366;"
                                           href="https://wa.me/{{ $gWaNum }}?text={{ urlencode('Hello ' . ($b->guest_name ?? 'Guest') . ', your booking ' . $b->booking_reference . ' is confirmed.') }}"
                                           target="_blank">
                                            <i class="fa-brands fa-whatsapp me-2"></i> WhatsApp Guest
                                        </a>
                                    </li>
                                    <li>
                                        {{-- 📲 SMS — connect BulkSMSBD / Twilio SMS API here --}}
                                        <a class="dropdown-item py-1.5 px-3 text-secondary" href="sms:{{ $gPhone }}" data-sms="{{ $gPhone }}">
                                            <i class="fa-solid fa-comment-sms me-2"></i> Send SMS
                                        </a>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider my-1"></li>
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
                        <td colspan="9" class="text-center py-5" style="background:#ffffff;">
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
@endsection

