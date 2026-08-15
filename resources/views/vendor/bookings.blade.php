@extends('layouts.vendor')
@section('title', 'Manage Guest Bookings | Vendor Portal')

@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Guest Reservations &amp; Bookings</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('vendorBookingsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('vendorBookingsTable', 'vendor_bookings')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('vendorBookingsTable', 'vendor_bookings')" title="Export to CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('vendorBookingsTable', 'vendor_bookings')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('vendorBookingsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Bookings Ledger</strong>
    </div>
</div>

<div class="page-content-area">

    {{-- Filter Bar --}}
    <div class="page-filters-bar mb-3">
        <form action="{{ route('vendor.bookings.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Booking Status</label>
                    <select name="status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Search Guest / Reference</label>
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
                <h6 style="margin:0; font-weight:700; color:#1e293b;">Guest Reservations</h6>
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
                        <th>Guest Name</th>
                        <th>Guest Phone</th>
                        <th>Property &amp; Room</th>
                        <th>Check-in → Check-out</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right; width:80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    @php
                        $phone    = $b->guest_phone ?? null;
                        $phoneNum = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
                        $waNum    = $phoneNum ? (str_starts_with($phoneNum, '880') ? $phoneNum : '880' . ltrim($phoneNum, '0')) : null;
                        $previewJson = json_encode([
                            'id'                => $b->id,
                            'reference'         => $b->booking_reference,
                            'guest_name'        => $b->guest_name,
                            'guest_phone'       => $b->guest_phone ?? 'N/A',
                            'guest_email'       => $b->guest_email ?? 'N/A',
                            'property_name'     => $b->property?->name ?? 'Hotel Stay',
                            'room_name'         => $b->room?->name ?? 'Standard Room',
                            'check_in'          => $b->check_in ? \Carbon\Carbon::parse($b->check_in)->format('M d, Y') : 'N/A',
                            'check_out'         => $b->check_out ? \Carbon\Carbon::parse($b->check_out)->format('M d, Y') : 'N/A',
                            'nights'            => $b->nights ?? $b->nights_count ?? 1,
                            'guests'            => $b->guests ?? 1,
                            'subtotal'          => number_format((float)($b->subtotal ?? $b->amount ?? 0)),
                            'discount'          => number_format((float)($b->discount_amount ?? 0)),
                            'coupon'            => $b->coupon_code,
                            'tax'               => number_format((float)($b->tax_amount ?? 0)),
                            'total'             => number_format((float)($b->amount ?? $b->total_price ?? 0)),
                            'payment_method'    => strtoupper(str_replace('_', ' ', $b->payment_method ?? 'CASH')),
                            'payment_status'    => strtoupper($b->payment_status ?? 'UNPAID'),
                            'status'            => ucfirst($b->effective_status),
                            'special_requests'  => $b->special_requests ?? 'None',
                            'voucher_url'       => route('checkout.confirmation', $b->booking_reference),
                            'full_details_url'  => route('vendor.bookings.show', $b->booking_reference),
                            'wa_url'            => $waNum ? "https://wa.me/{$waNum}?text=" . urlencode("Hello {$b->guest_name}, regarding your booking {$b->booking_reference} at " . ($b->property?->name ?? 'our hotel') . ".") : null,
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        
                        {{-- Booking Ref --}}
                        <td style="font-family:monospace; font-weight:700; font-size:13px; color:#0f172a;">
                            {{ $b->booking_reference }}
                        </td>

                        {{-- Guest Name & Email --}}
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name }}</strong>
                            <div style="font-size:11px; color:#94a3b8; margin-top:1px;">{{ $b->guest_email ?? 'No email' }}</div>
                        </td>

                        {{-- Dedicated Phone Column --}}
                        <td>
                            @if($phone)
                                <a href="tel:{{ $phone }}" class="text-dark fw-bold d-inline-flex align-items-center gap-1" style="text-decoration:none; font-size:12px;" title="Call Guest">
                                    <i class="fa-solid fa-phone text-primary" style="font-size:10.5px;"></i> {{ $phone }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size:11px;">N/A</span>
                            @endif
                        </td>

                        {{-- Property & Room --}}
                        <td>
                            <span style="font-size:12.5px; font-weight:600; color:#1e293b; display:block;">{{ $b->property?->name ?? 'Property' }}</span>
                            @if($b->room)
                                <div style="font-size:11px; color:#64748b;">{{ $b->room->name }}</div>
                            @endif
                        </td>

                        {{-- Dates --}}
                        <td style="font-size:12px; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($b->check_in)->format('M d') }} → {{ \Carbon\Carbon::parse($b->check_out)->format('M d, Y') }}
                        </td>

                        {{-- Amount --}}
                        <td>
                            <strong style="color:var(--primary); font-size:13.5px;">{{ CurrencyService::format($b->amount) }}</strong>
                            @if(($b->nights ?? 0) > 0)
                                <div style="font-size:10px; color:#94a3b8;">{{ $b->nights ?? $b->nights_count }} night(s)</div>
                            @endif
                        </td>

                        {{-- Payment Status --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-light text-dark border" style="font-size:10px; font-weight:600; text-transform:uppercase; width:fit-content;">
                                    <i class="fa-solid fa-wallet text-secondary me-1"></i>{{ str_replace('_', ' ', $b->payment_method ?? 'Cash') }}
                                </span>
                                @if(strtolower($b->payment_status ?? '') === 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold border border-success border-opacity-25" style="font-size:10px; padding:2px 6px; border-radius:4px; width:fit-content;">
                                        <i class="fa-solid fa-circle-check me-1"></i> PAID
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-15 text-dark fw-bold border border-warning border-opacity-25" style="font-size:10px; padding:2px 6px; border-radius:4px; width:fit-content;">
                                        <i class="fa-solid fa-clock me-1 text-warning"></i> UNPAID
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Booking Status --}}
                        <td>
                            <span class="badge-status {{ strtolower($b->effective_status) == 'confirmed' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </td>

                        {{-- Modern 3-Dot Actions Dropdown Menu --}}
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown d-inline-block position-relative">
                                <button class="btn btn-light btn-sm shadow-none border-0 action-3dot-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:6px; background:#f1f5f9; color:#334155;" title="Manage Booking">
                                    <i class="fa-solid fa-ellipsis-vertical fs-6"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:6px; font-size:12.5px; border:1px solid #e2e8f0; padding:5px 0; min-width:190px; z-index:1060; margin-top:2px;">
                                    
                                    {{-- 1. Quick Preview Modal Trigger --}}
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 text-primary fw-semibold" onclick="openBookingPreviewModal({{ $previewJson }})">
                                            <i class="fa-solid fa-eye me-2 text-primary"></i> Quick Preview
                                        </button>
                                    </li>

                                    {{-- 2. Full Details Page --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-dark" href="{{ route('vendor.bookings.show', $b->booking_reference) }}">
                                            <i class="fa-solid fa-file-lines text-secondary me-2"></i> Full Details Page
                                        </a>
                                    </li>

                                    {{-- 3. Print Voucher / Invoice --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-dark" href="{{ route('checkout.confirmation', $b->booking_reference) }}" target="_blank">
                                            <i class="fa-solid fa-print text-secondary me-2"></i> Print Voucher
                                        </a>
                                    </li>

                                    @if($phone)
                                    <li><hr class="dropdown-divider my-1"></li>
                                    {{-- 4. Call Guest --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-dark" href="tel:{{ $phone }}">
                                            <i class="fa-solid fa-phone text-primary me-2"></i> Call Guest
                                        </a>
                                    </li>
                                    {{-- 5. WhatsApp Guest --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" style="color:#16a34a;" href="https://wa.me/{{ $waNum }}?text={{ urlencode('Hello ' . $b->guest_name . ', regarding your booking ' . $b->booking_reference . ' at ' . ($b->property?->name ?? 'our hotel') . '.') }}" target="_blank">
                                            <i class="fa-brands fa-whatsapp me-2"></i> WhatsApp Guest
                                        </a>
                                    </li>
                                    {{-- 6. Send SMS --}}
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-secondary" href="sms:{{ $phone }}">
                                            <i class="fa-solid fa-comment-sms me-2"></i> Send SMS
                                        </a>
                                    </li>
                                    @endif

                                    @if(strtolower($b->payment_status ?? '') !== 'paid')
                                    <li><hr class="dropdown-divider my-1"></li>
                                    {{-- 7. Mark Paid --}}
                                    <li>
                                        <form action="{{ route('vendor.bookings.update-payment', $b->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="paid">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-success fw-bold">
                                                <i class="fa-solid fa-credit-card me-2"></i> Mark Payment Paid
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:32px; color:#8c8c8c;">
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

{{-- ── Interactive Quick View / Preview Modal ────────────────────────────── --}}
<div class="modal fade" id="bookingPreviewModal" tabindex="-1" aria-labelledby="bookingPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header text-white" style="background:linear-gradient(135deg, #1e293b, #0f172a); padding:16px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-receipt fs-5 text-warning"></i>
                    <div>
                        <h5 class="modal-title m-0 fw-bold" id="bookingPreviewModalLabel" style="font-size:16px;">Reservation Preview</h5>
                        <small class="text-secondary" style="font-size:11.5px;">Reference: <strong id="pv_ref" class="text-white font-monospace"></strong></small>
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
                                <div><span class="text-muted">Full Name:</span> <strong id="pv_name" class="text-dark"></strong></div>
                                <div><span class="text-muted">Phone:</span> <a href="" id="pv_phone_link" class="fw-bold text-primary text-decoration-none"></a></div>
                                <div><span class="text-muted">Email:</span> <span id="pv_email" class="text-dark"></span></div>
                                <div><span class="text-muted">Special Requests:</span> <span id="pv_requests" class="text-secondary fst-italic"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Stay & Room Details Card --}}
                    <div class="col-md-6">
                        <div class="card border p-3 h-100 bg-white rounded-3 shadow-xs">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom text-dark" style="font-size:12.5px; text-transform:uppercase;">
                                <i class="fa-solid fa-hotel me-1 text-primary"></i> Property &amp; Stay
                            </h6>
                            <div class="d-flex flex-column gap-2" style="font-size:12.5px;">
                                <div><span class="text-muted">Property:</span> <strong id="pv_property" class="text-dark"></strong></div>
                                <div><span class="text-muted">Room Type:</span> <span id="pv_room" class="fw-semibold text-secondary"></span></div>
                                <div><span class="text-muted">Check-In:</span> <strong id="pv_checkin" class="text-dark"></strong></div>
                                <div><span class="text-muted">Check-Out:</span> <strong id="pv_checkout" class="text-dark"></strong></div>
                                <div><span class="text-muted">Duration:</span> <span id="pv_duration" class="badge bg-light text-dark border"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Financial & Payment Summary --}}
                    <div class="col-12">
                        <div class="card border p-3 bg-white rounded-3 shadow-xs">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom text-dark d-flex align-items-center justify-content-between" style="font-size:12.5px; text-transform:uppercase;">
                                <span><i class="fa-solid fa-receipt me-1 text-success"></i> Financial &amp; Payment Ledger</span>
                                <span id="pv_status_badge"></span>
                            </h6>
                            <div class="row g-2 align-items-center" style="font-size:12.5px;">
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">Subtotal</div>
                                    <strong class="text-dark">৳ <span id="pv_subtotal"></span></strong>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">Discount (Coupon)</div>
                                    <span class="text-success fw-bold">- ৳ <span id="pv_discount"></span></span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">VAT / Tax</div>
                                    <span class="text-dark">৳ <span id="pv_tax"></span></span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-muted" style="font-size:11px;">Net Total</div>
                                    <strong class="text-primary fs-6">৳ <span id="pv_total"></span></strong>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div style="font-size:12px;">
                                    <span class="text-muted">Payment Gateway:</span> <strong id="pv_gateway"></strong>
                                </div>
                                <div>
                                    <span class="text-muted me-1">Payment Status:</span>
                                    <span id="pv_payment_badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <a href="" id="pv_wa_btn" target="_blank" class="btn btn-success btn-sm fw-bold px-3">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Guest
                    </a>
                    <a href="" id="pv_print_btn" target="_blank" class="btn btn-dark btn-sm fw-bold px-3">
                        <i class="fa-solid fa-print me-1"></i> Print Voucher
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="" id="pv_full_btn" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fa-solid fa-file-lines me-1"></i> Full Order Page
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openBookingPreviewModal(data) {
    document.getElementById('pv_ref').innerText = data.reference;
    document.getElementById('pv_name').innerText = data.guest_name;
    document.getElementById('pv_phone_link').innerText = data.guest_phone;
    document.getElementById('pv_phone_link').href = 'tel:' + data.guest_phone;
    document.getElementById('pv_email').innerText = data.guest_email;
    document.getElementById('pv_requests').innerText = data.special_requests;
    
    document.getElementById('pv_property').innerText = data.property_name;
    document.getElementById('pv_room').innerText = data.room_name;
    document.getElementById('pv_checkin').innerText = data.check_in;
    document.getElementById('pv_checkout').innerText = data.check_out;
    document.getElementById('pv_duration').innerText = data.nights + ' night(s) · ' + data.guests + ' guest(s)';
    
    document.getElementById('pv_subtotal').innerText = data.subtotal;
    document.getElementById('pv_discount').innerText = data.discount + (data.coupon ? ' (' + data.coupon + ')' : '');
    document.getElementById('pv_tax').innerText = data.tax;
    document.getElementById('pv_total').innerText = data.total;
    document.getElementById('pv_gateway').innerText = data.payment_method;
    
    const isPaid = data.payment_status.toLowerCase() === 'paid';
    document.getElementById('pv_payment_badge').innerHTML = isPaid 
        ? '<span class="badge bg-success text-white fw-bold px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> PAID</span>'
        : '<span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-clock me-1"></i> UNPAID</span>';
        
    document.getElementById('pv_status_badge').innerHTML = '<span class="badge bg-primary text-white px-2 py-1">' + data.status + '</span>';
    
    document.getElementById('pv_print_btn').href = data.voucher_url;
    document.getElementById('pv_full_btn').href = data.full_details_url;
    
    const waBtn = document.getElementById('pv_wa_btn');
    if (data.wa_url) {
        waBtn.style.display = 'inline-flex';
        waBtn.href = data.wa_url;
    } else {
        waBtn.style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('bookingPreviewModal'));
    modal.show();
}
</script>
@endsection
