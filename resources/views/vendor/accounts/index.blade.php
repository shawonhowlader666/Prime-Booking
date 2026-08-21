@extends('layouts.vendor')

@section('title', 'Financial Accounts & Statements — Vendor Portal')

@section('content')
<div class="container-fluid px-4 py-3" style="max-width: 1400px;">

    {{-- HEADER CARD --}}
    <div class="page-header-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size:12px;">
                        <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Accounts &amp; Statements</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold" style="color:#0f172a; font-size:20px; letter-spacing:-0.3px;">
                    <i class="fa-solid fa-wallet text-primary me-2"></i> Financial Accounts &amp; Payout Statements
                </h4>
                <p class="text-muted mb-0" style="font-size:12.5px;">Real-time booking revenue, service commission deductions, settled payouts &amp; withdrawable balance.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('vendor.accounts.statement.print') }}" target="_blank" class="btn btn-outline-dark fw-bold d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px;">
                    <i class="fa-solid fa-print"></i> Print Official Statement
                </a>
                <a href="{{ route('vendor.payouts.index') }}" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                    <i class="fa-solid fa-money-bill-transfer"></i> Request Withdrawal Payout
                </a>
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Available Withdrawable Balance --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #28c76f !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#28c76f; text-transform:uppercase;">Withdrawable Balance</span>
                    <i class="fa-solid fa-vault" style="color:#28c76f; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#28c76f;">৳{{ number_format($finance['withdrawable_balance'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-circle-check me-1 text-success"></i> Ready for Bank / bKash Payout</span>
            </div>
        </div>

        {{-- Gross Revenue --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #1890ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#1890ff; text-transform:uppercase;">Gross Booking Turnover</span>
                    <i class="fa-solid fa-chart-line" style="color:#1890ff; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#0f172a;">৳{{ number_format($finance['gross_revenue'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-receipt me-1"></i> {{ $finance['total_bookings'] }} Completed Reservations</span>
            </div>
        </div>

        {{-- Commission Paid --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #ff9f43 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#ff9f43; text-transform:uppercase;">OTA Commission (12%)</span>
                    <i class="fa-solid fa-percentage" style="color:#ff9f43; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#ff9f43;">-৳{{ number_format($finance['commission_paid'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-shield-halved me-1"></i> Platform Marketing &amp; Engine Fee</span>
            </div>
        </div>

        {{-- Total Paid Out --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #7367f0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#7367f0; text-transform:uppercase;">Settled Payouts</span>
                    <i class="fa-solid fa-money-bill-transfer" style="color:#7367f0; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#7367f0;">৳{{ number_format($finance['payouts_paid'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-clock me-1"></i> Pending Payouts: ৳{{ number_format($finance['payouts_pending'], 2) }}</span>
            </div>
        </div>
    </div>

    {{-- LEDGER & PAYOUTS TABS --}}
    <div class="card border-0 p-0" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color:#e2e8f0 !important;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Financial Audit Ledger
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">TXN REF</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">PROPERTY / SOURCE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">GROSS SALE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">COMMISSION (12%)</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">NET TO VENDOR</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:center;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td style="padding:12px 16px;">
                            <strong class="text-dark d-block">{{ $l->txn_reference }}</strong>
                            <small class="text-muted">{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : 'N/A' }}</small>
                        </td>
                        <td style="padding:12px 16px;">
                            <strong class="text-dark d-block">{{ $l->property?->name ?? 'Hotel Booking' }}</strong>
                            <small class="text-muted">{{ $l->description }}</small>
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#0f172a;">
                            ৳{{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#ff9f43;">
                            -৳{{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:800; color:#28c76f;">
                            +৳{{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;"><i class="fa-solid fa-check me-1"></i> Credited</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">{{ ucfirst($l->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No financial transactions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
        <div class="p-3 border-top d-flex justify-content-end" style="border-color:#e2e8f0 !important;">
            {{ $ledgers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
