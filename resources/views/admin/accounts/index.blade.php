@extends('layouts.admin')

@section('title', 'Master Accounts & Financial Ledger Hub — Prime Booking')

@section('content')
<div class="container-fluid px-4 py-3" style="max-width: 1600px;">

    {{-- HEADER CARD --}}
    <div class="page-header-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size:12px;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Accounts &amp; Finance</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold" style="color:#0f172a; font-size:20px; letter-spacing:-0.3px;">
                    <i class="fa-solid fa-scale-balanced text-primary me-2"></i> Master Accounts &amp; Financial Ledger Hub
                </h4>
                <p class="text-muted mb-0" style="font-size:12.5px;">Real-time OTA gross booking turnover, commission revenue, vendor payables &amp; liquid escrow vault.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.accounts.ledger') }}" class="btn btn-outline-primary fw-bold d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px;">
                    <i class="fa-solid fa-book"></i> General Ledger
                </a>
                <a href="{{ route('admin.accounts.vendor-statements') }}" class="btn btn-outline-dark fw-bold d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Vendor Settlements
                </a>
                <a href="{{ route('admin.accounts.ledger.export') }}" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                    <i class="fa-solid fa-file-excel"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    {{-- DATE RANGE FILTER --}}
    <div class="card border-0 mb-4" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.accounts.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="font-size:12.5px; height:34px; border-radius:4px;">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="font-size:12.5px; height:34px; border-radius:4px;">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Year (Chart)</label>
                    <select name="year" class="form-select form-select-sm" style="font-size:12.5px; height:34px; border-radius:4px;">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 col-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold w-100" style="font-size:12.5px; height:34px; border-radius:4px; background:var(--primary); border:none;">
                        <i class="fa-solid fa-filter me-1"></i> Calculate Accounts
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border btn-sm text-secondary fw-bold" style="font-size:12.5px; height:34px; border-radius:4px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- EXECUTIVE FINANCIAL KPIS ROW 1 --}}
    <div class="row g-3 mb-4">
        {{-- Gross Booking Value --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #1890ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Gross Turnover (GBV)</span>
                    <i class="fa-solid fa-chart-line" style="color:#1890ff; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#0f172a;">৳{{ number_format($kpis['gross_booking_value'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-receipt me-1"></i> {{ number_format($kpis['total_orders']) }} Completed Orders</span>
            </div>
        </div>

        {{-- Platform Commission / Net Revenue --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #28c76f !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#28c76f; text-transform:uppercase;">Platform Commission</span>
                    <i class="fa-solid fa-hand-holding-dollar" style="color:#28c76f; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#28c76f;">৳{{ number_format($kpis['platform_commission'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-percentage me-1"></i> ~12% Standard Platform Service Fee</span>
            </div>
        </div>

        {{-- Net Company Profit --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #7367f0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#7367f0; text-transform:uppercase;">Net Profit (After Gateway Fees)</span>
                    <i class="fa-solid fa-wallet" style="color:#7367f0; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#7367f0;">৳{{ number_format($kpis['net_profit'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-credit-card me-1"></i> Deducted Gateway Fees: ৳{{ number_format($kpis['gateway_fees'], 2) }}</span>
            </div>
        </div>
    </div>

    {{-- EXECUTIVE FINANCIAL KPIS ROW 2 --}}
    <div class="row g-3 mb-4">
        {{-- Total Vendor Payable --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #ff9f43 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#ff9f43; text-transform:uppercase;">Vendor Net Share (88%)</span>
                    <i class="fa-solid fa-hotel" style="color:#ff9f43; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#ff9f43;">৳{{ number_format($kpis['vendor_payable'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-money-bill-transfer me-1"></i> Settled: ৳{{ number_format($kpis['total_settled_payouts'], 2) }} | Pending: ৳{{ number_format($kpis['pending_payouts'], 2) }}</span>
            </div>
        </div>

        {{-- Escrow Liquid Vault --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #00cfe8 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#00cfe8; text-transform:uppercase;">Escrow Cash Flow in Hand</span>
                    <i class="fa-solid fa-vault" style="color:#00cfe8; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#00cfe8;">৳{{ number_format($kpis['escrow_vault_balance'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-shield-halved me-1"></i> Undisbursed Liquid Holding Balance</span>
            </div>
        </div>

        {{-- Total Refunded --}}
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 p-3 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; border-left:4px solid #ea5455 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-size:11px; font-weight:700; color:#ea5455; text-transform:uppercase;">Refunds &amp; Cancellations</span>
                    <i class="fa-solid fa-rotate-left" style="color:#ea5455; font-size:16px;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:22px; color:#ea5455;">৳{{ number_format($kpis['total_refunded'], 2) }}</h3>
                <span class="text-muted" style="font-size:11.5px;"><i class="fa-solid fa-circle-xmark me-1"></i> Customer Returned Funds</span>
            </div>
        </div>
    </div>

    {{-- CHART & LEDGER PREVIEW ROW --}}
    <div class="row g-3 mb-4">
        {{-- Interactive 12-Month P&L Curve --}}
        <div class="col-lg-7">
            <div class="card border-0 p-4 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0" style="font-size:14.5px; color:#0f172a;">
                        <i class="fa-solid fa-chart-area text-primary me-2"></i> {{ $year }} Monthly P&amp;L Financial Curve
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:11px; border-radius:4px;">Real-Time Financial Engine</span>
                </div>
                <div style="height: 300px; position:relative;">
                    <canvas id="pnlChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Quick Transaction Ledger --}}
        <div class="col-lg-5">
            <div class="card border-0 p-0 h-100" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold mb-0" style="font-size:14px; color:#0f172a;">
                        <i class="fa-solid fa-clock-rotate-left text-success me-2"></i> Recent Double-Entry Txns
                    </h6>
                    <a href="{{ route('admin.accounts.ledger') }}" class="text-primary text-decoration-none fw-bold" style="font-size:12px;">View All &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="padding:10px 14px; font-weight:700; color:#475569;">TXN REF</th>
                                <th style="padding:10px 14px; font-weight:700; color:#475569;">METHOD</th>
                                <th style="padding:10px 14px; font-weight:700; color:#475569; text-align:right;">GROSS</th>
                                <th style="padding:10px 14px; font-weight:700; color:#475569; text-align:right;">COMM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTxns as $t)
                            <tr>
                                <td style="padding:10px 14px;">
                                    <span class="fw-bold text-dark d-block">{{ $t->txn_reference }}</span>
                                    <small class="text-muted">{{ $t->created_at ? $t->created_at->format('d M, H:i') : 'N/A' }}</small>
                                </td>
                                <td style="padding:10px 14px;">
                                    <span class="badge bg-light text-dark border fw-semibold" style="font-size:10px; border-radius:3px; text-transform:uppercase;">{{ $t->payment_method ?? 'N/A' }}</span>
                                </td>
                                <td style="padding:10px 14px; text-align:right; font-weight:700; color:#0f172a;">
                                    ৳{{ number_format($t->gross_amount, 2) }}
                                </td>
                                <td style="padding:10px 14px; text-align:right; font-weight:700; color:#28c76f;">
                                    ৳{{ number_format($t->commission_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No transactions recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('pnlChart').getContext('2d');
    const months = @json($chartData['months']);
    const revenue = @json($chartData['revenue']);
    const commission = @json($chartData['commission']);
    const payouts = @json($chartData['payouts']);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Gross Turnover (BDT)',
                    data: revenue,
                    backgroundColor: 'rgba(24, 144, 255, 0.75)',
                    borderRadius: 4,
                },
                {
                    label: 'Platform Net Profit / Comm (BDT)',
                    data: commission,
                    backgroundColor: 'rgba(40, 199, 111, 0.85)',
                    borderRadius: 4,
                },
                {
                    label: 'Vendor Disbursed Payouts (BDT)',
                    data: payouts,
                    backgroundColor: 'rgba(255, 159, 67, 0.75)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11, family: 'Inter, sans-serif' } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) { return '৳' + value.toLocaleString(); },
                        font: { size: 10.5 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endsection
