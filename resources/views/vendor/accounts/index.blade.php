@extends('layouts.vendor')

@section('title', 'Financial Accounts & Statements — PRIME BOOKING Partner')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Financial Accounts &amp; Payout Statements</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('vendor.accounts.statement.print') }}" target="_blank" class="btn-tbl-col" style="text-decoration:none;">
                <i class="fa-solid fa-print me-1"></i> Print Official Statement
            </a>
            <a href="{{ route('vendor.payouts.index') }}" class="btn-add-primary ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-money-bill-transfer me-1"></i> Request Withdrawal Payout
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Accounts &amp; Statements</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Available Withdrawable Balance --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">WITHDRAWABLE BALANCE</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($finance['withdrawable_balance'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-circle-check me-1 text-success"></i> Ready for Bank / bKash</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        {{-- Gross Revenue --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">GROSS BOOKING TURNOVER</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">৳ {{ number_format($finance['gross_revenue'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-receipt me-1"></i> {{ $finance['total_bookings'] }} Completed Orders</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>

        {{-- Commission Paid --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">OTA COMMISSION (12%)</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ff9f43; margin:0;">-৳ {{ number_format($finance['commission_paid'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-shield-halved me-1"></i> Platform Marketing Fee</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-percentage"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>

        {{-- Total Paid Out --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">SETTLED PAYOUTS</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#7367f0; margin:0;">৳ {{ number_format($finance['payouts_paid'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-clock me-1"></i> Pending: ৳{{ number_format($finance['payouts_pending'], 2) }}</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#f0eefc; color:#7367f0; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- LEDGER & TRANSACTIONS DATA TABLE --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Financial Audit Ledger &amp; Earnings Records
            </h6>
            <div style="font-size:12px; color:#64748b;">
                <span class="live-feed-badge me-2"></span> Instant 88% Net Disbursement Calculation
            </div>
        </div>

        <div class="table-responsive">
            <table class="table stockifly-data-table align-middle mb-0" id="vendorLedgerTable">
                <thead>
                    <tr>
                        <th>TXN Ref</th>
                        <th>Hotel / Booking</th>
                        <th style="text-align:right;">Gross (BDT)</th>
                        <th style="text-align:right;">Commission (12%)</th>
                        <th style="text-align:right;">Net Earnings</th>
                        <th style="text-align:center;">Method</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->txn_reference }}</strong>
                            <span class="order-date">{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : '' }}</span>
                        </td>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->property?->name ?? 'Hotel Reservation' }}</strong>
                            <small class="text-muted" style="font-size:11px;">#{{ $l->booking?->booking_code ?? 'DIRECT' }}</small>
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#ff9f43; font-size:13px;">
                            -৳ {{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:800; color:#28c76f; font-size:13.5px;">
                            ৳ {{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <span class="badge-gateway" style="font-size:11px;">
                                {{ strtoupper($l->payment_method ?? 'PAY_AT_HOTEL') }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge-status confirmed" style="font-size:11px; font-weight:700;">
                                    <i class="fa-solid fa-check me-1"></i> Settled
                                </span>
                            @else
                                <span class="badge-status pending" style="font-size:11px; font-weight:700;">
                                    {{ ucfirst($l->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No booking ledger records yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
        <div class="stockifly-table-footer">
            <div class="footer-left">
                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} records
            </div>
            <div class="footer-right">
                {{ $ledgers->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
