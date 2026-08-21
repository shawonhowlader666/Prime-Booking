@extends('layouts.admin')

@section('title', 'Vendor Financial Statements & Settlements — Prime Booking')

@section('content')
<div class="container-fluid px-4 py-3" style="max-width: 1600px;">

    {{-- HEADER CARD --}}
    <div class="page-header-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size:12px;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}" class="text-decoration-none text-muted">Accounts</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Vendor Statements</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold" style="color:#0f172a; font-size:20px; letter-spacing:-0.3px;">
                    <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Vendor Financial Statements &amp; Settlements
                </h4>
                <p class="text-muted mb-0" style="font-size:12.5px;">Accurate breakdown of vendor hotel sales, OTA platform commissions, disbursed payouts &amp; pending balances.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary fw-bold" style="font-size:12.5px; height:36px; border-radius:4px;">
                    <i class="fa-solid fa-chart-pie me-1"></i> Accounts Hub
                </a>
                <a href="{{ route('admin.payouts.index') }}" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                    <i class="fa-solid fa-money-bill-transfer"></i> Manage Payout Requests
                </a>
            </div>
        </div>
    </div>

    {{-- VENDOR SETTLEMENTS TABLE --}}
    <div class="card border-0 p-0" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color:#e2e8f0 !important;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-hotel text-primary me-2"></i> Partner Hotel Settlements Directory ({{ $vendors->total() }} Vendors)
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">VENDOR PARTNER</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:center;">PROPERTIES</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:center;">BOOKINGS</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">GROSS SALES</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">COMMISSION (12%)</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">NET EARNINGS</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">SETTLED PAID</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">AVAILABLE BALANCE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                    <tr>
                        <td style="padding:12px 16px;">
                            <strong class="text-dark d-block" style="font-size:13px;">{{ $v->name }}</strong>
                            <small class="text-muted">{{ $v->email }} &bull; {{ $v->phone ?? 'No phone' }}</small>
                        </td>
                        <td style="padding:12px 16px; text-align:center;">
                            <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size:11px; border-radius:3px;">
                                {{ $v->properties_count }} Hotels
                            </span>
                        </td>
                        <td style="padding:12px 16px; text-align:center; font-weight:700; color:#0f172a;">
                            {{ $v->finance_stats->total_bookings }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#0f172a;">
                            ৳{{ number_format($v->finance_stats->gross_sales, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#28c76f;">
                            -৳{{ number_format($v->finance_stats->commission_deducted, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:800; color:#0f172a;">
                            ৳{{ number_format($v->finance_stats->net_payable, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#7367f0;">
                            ৳{{ number_format($v->finance_stats->payouts_paid, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            @if($v->finance_stats->available_balance > 0)
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1.5" style="font-size:12px; border-radius:4px;">
                                    ৳{{ number_format($v->finance_stats->available_balance, 2) }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:12px;">৳0.00 (Cleared)</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; text-align:center;">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <a href="{{ route('admin.accounts.ledger', ['vendor_id' => $v->id]) }}" class="btn btn-sm btn-outline-primary fw-bold" style="font-size:11.5px; border-radius:3px; height:28px; padding:2px 8px;" title="View Vendor Ledger">
                                    <i class="fa-solid fa-list me-1"></i> Ledger
                                </a>
                                <a href="{{ route('admin.accounts.vendor-statements.print', $v->id) }}" target="_blank" class="btn btn-sm btn-outline-dark fw-bold" style="font-size:11.5px; border-radius:3px; height:28px; padding:2px 8px;" title="Print Official Statement">
                                    <i class="fa-solid fa-print me-1"></i> Statement
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-hotel fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No vendor partners found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="p-3 border-top d-flex justify-content-end" style="border-color:#e2e8f0 !important;">
            {{ $vendors->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
