@extends('layouts.admin')
@section('title', 'Master Accounts & Financial Ledger Hub | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Master Accounts &amp; Financial Ledger Hub</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('admin.accounts.ledger') }}" class="btn-tbl-copy" style="text-decoration:none;">
                <i class="fa-solid fa-book-journal-whills me-1"></i> General Ledger
            </a>
            <a href="{{ route('admin.accounts.vendor-statements') }}" class="btn-tbl-excel" style="text-decoration:none;">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Vendor Settlements
            </a>
            <a href="{{ route('admin.accounts.ledger.export') }}" class="btn-add-primary ms-1" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 16px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <i class="fa-solid fa-file-excel"></i> <span>Export Ledger CSV</span>
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Accounts &amp; Finance Hub</strong>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="page-filters-bar mb-4" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff; padding:16px;">
        <form method="GET" action="{{ route('admin.accounts.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Year (Chart Curve)</label>
                    <select name="year" class="form-select" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-4 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold px-3 flex-grow-1" style="height:36px; font-size:12.5px; border-radius:4px; background-color:var(--primary); border:none;">
                        <i class="fa-solid fa-filter me-1"></i> Calculate Accounts
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border fw-bold text-secondary px-3" style="height:36px; font-size:12.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stockifly KPI Summary Cards Row 1 --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">GROSS TURNOVER (GBV)</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">৳ {{ number_format($kpis['gross_booking_value'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-receipt me-1"></i> {{ number_format($kpis['total_orders']) }} Completed Orders</small>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">PLATFORM COMMISSION (12%)</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($kpis['platform_commission'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-percentage me-1"></i> 12.00% Standard Service Fee</small>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">NET COMPANY PROFIT</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#7367f0; margin:0;">৳ {{ number_format($kpis['net_profit'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-credit-card me-1"></i> Gateway Fees: ৳ {{ number_format($kpis['gateway_fees'], 2) }}</small>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- Stockifly KPI Summary Cards Row 2 --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">VENDOR NET SHARE (88%)</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($kpis['vendor_payable'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-money-bill-transfer me-1"></i> Settled: ৳ {{ number_format($kpis['total_settled_payouts'], 2) }}</small>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">PENDING PAYOUT QUEUE</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ea5455; margin:0;">৳ {{ number_format($kpis['pending_payouts'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-clock-rotate-left me-1"></i> Awaiting Disbursement</small>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">LIQUID ESCROW POOL</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#00cfe8; margin:0;">৳ {{ number_format($kpis['escrow_holding_pool'], 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-shield-halved me-1"></i> Liquid Escrow Balance</small>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- CHART & RECENT TRANSACTIONS --}}
    <div class="row g-3 mb-4">
        {{-- Annual P&L Bar Chart --}}
        <div class="col-lg-7">
            <div class="data-table-card p-0 h-100" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
                <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-chart-simple text-primary me-2"></i> Annual Financial Revenue Curve ({{ $year }})</h6>
                    <span style="font-size:11.5px; color:#64748b;"><span class="live-feed-badge me-1"></span> Live Data</span>
                </div>
                <div class="p-3" style="height:320px;">
                    <canvas id="pnlChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Gateway Share & Recent Transactions --}}
        <div class="col-lg-5">
            <div class="data-table-card p-0 h-100" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
                <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-list-check text-primary me-2"></i> Recent Audit Entries</h6>
                    <a href="{{ route('admin.accounts.ledger') }}" class="btn btn-sm btn-outline-primary fw-bold" style="font-size:11.5px; height:28px; padding:0 10px; border-radius:4px; text-decoration:none;">
                        View All <i class="fa-solid fa-chevron-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-stockifly mb-0">
                        <thead>
                            <tr>
                                <th>REFERENCE</th>
                                <th>TYPE</th>
                                <th style="text-align:right;">GROSS</th>
                                <th style="text-align:right;">COMMISSION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLedgers as $r)
                            <tr>
                                <td>
                                    <strong style="font-size:12.5px; color:#1e293b; display:block;">{{ $r->txn_reference }}</strong>
                                    <span style="font-size:10.5px; color:#8c8c8c;">{{ $r->created_at ? $r->created_at->format('d M, h:i A') : '' }}</span>
                                </td>
                                <td>
                                    <span class="badge-status {{ $r->type === 'credit' ? 'confirmed' : 'pending' }}" style="font-size:10px; font-weight:700;">
                                        {{ strtoupper($r->type) }}
                                    </span>
                                </td>
                                <td style="text-align:right; font-weight:700; color:#0f172a; font-size:12.5px;">
                                    ৳ {{ number_format($r->gross_amount) }}
                                </td>
                                <td style="text-align:right; font-weight:700; color:#28c76f; font-size:12.5px;">
                                    +৳ {{ number_format($r->commission_amount) }}
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
                    label: 'Platform Commission (BDT)',
                    data: commission,
                    backgroundColor: 'rgba(40, 199, 111, 0.85)',
                    borderRadius: 4,
                },
                {
                    label: 'Vendor Payouts (BDT)',
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
