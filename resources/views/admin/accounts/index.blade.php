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
            <button type="button" class="btn btn-primary fw-bold" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 16px; display:inline-flex; align-items:center; gap:6px;" data-bs-toggle="modal" data-bs-target="#recordTxnModal">
                <i class="fa-solid fa-plus-circle"></i> <span>Record Transaction</span>
            </button>
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
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">৳ {{ number_format($kpis['gross_booking_value'] ?? 0, 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-receipt me-1"></i> {{ number_format($kpis['total_orders'] ?? 0) }} Completed Orders</small>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">PLATFORM COMMISSION (12%)</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($kpis['platform_commission'] ?? 0, 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-percentage me-1"></i> 12.00% Standard Service Fee</small>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">NET COMPANY PROFIT</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#7367f0; margin:0;">৳ {{ number_format($kpis['net_profit'] ?? 0, 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-credit-card me-1"></i> Gateway Fees: ৳ {{ number_format($kpis['gateway_fees'] ?? 0, 2) }}</small>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- Stockifly KPI Summary Cards Row 2 --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">VENDOR NET SHARE (88%)</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($kpis['vendor_payable'] ?? 0, 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-money-bill-transfer me-1"></i> Settled: ৳ {{ number_format($kpis['total_settled_payouts'] ?? 0, 2) }}</small>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">PENDING PAYOUT QUEUE</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ea5455; margin:0;">৳ {{ number_format($kpis['pending_payouts'] ?? 0, 2) }}</p>
                <small class="text-muted d-block mt-1" style="font-size:11px;"><i class="fa-solid fa-clock-rotate-left me-1"></i> Awaiting Disbursement</small>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">LIQUID ESCROW POOL</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#00cfe8; margin:0;">৳ {{ number_format($kpis['escrow_holding_pool'] ?? $kpis['escrow_vault_balance'] ?? 0, 2) }}</p>
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
                                <th>TXN Ref</th>
                                <th>Type</th>
                                <th style="text-align:right;">Gross</th>
                                <th style="text-align:right;">Commission</th>
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

function calcCommission() {
    let gross = parseFloat(document.getElementById('modalGrossAmount').value) || 0;
    let commField = document.getElementById('modalCommAmount');
    let type = document.getElementById('txnTypeSelect').value;
    if (type === 'commission') {
        commField.value = gross.toFixed(2);
    } else {
        commField.value = (gross * 0.12).toFixed(2);
    }
}
</script>

{{-- RECORD TRANSACTION MODAL --}}
<div class="modal fade" id="recordTxnModal" tabindex="-1" aria-labelledby="recordTxnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
            <div class="modal-header bg-dark text-white px-4 py-3" style="background:#0f172a !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-warning fs-5"></i>
                    <h5 class="modal-title fw-bold m-0 text-white" id="recordTxnModalLabel" style="font-size:16px;">Record Manual Ledger Entry / Commission Adjustment</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.accounts.manual-entry') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background:#f8fafc;">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Transaction Flow / Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required style="font-size:13px; height:38px;" id="txnTypeSelect">
                                <option value="credit">Credit (Inflow / Money Received)</option>
                                <option value="debit">Debit (Outflow / Money Disbursed)</option>
                                <option value="commission">Commission Settlement (Platform Fee)</option>
                                <option value="payout">Vendor Payout Disbursement</option>
                                <option value="refund">Refund to Guest</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Accounting Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required style="font-size:13px; height:38px;">
                                <option value="manual_adjustment">Manual Adjustment</option>
                                <option value="vendor_commission_collected">Vendor Direct Commission Paid</option>
                                <option value="direct_bank_deposit">Direct Bank Deposit / Offline Booking</option>
                                <option value="cash_counter_payment">Cash Counter Payment</option>
                                <option value="vendor_settlement">Vendor Payout Settlement</option>
                                <option value="chargeback_fee">Fee / Penalty / Chargeback</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Vendor / Hotel Partner (Optional)</label>
                            <select name="vendor_id" class="form-select" style="font-size:13px; height:38px;">
                                <option value="">-- Platform Master / Direct Guest --</option>
                                @foreach($vendorsList as $vnd)
                                <option value="{{ $vnd->id }}">{{ $vnd->name }} ({{ $vnd->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size:11px;">Select vendor if this transaction credits/debits a specific hotel partner.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Payment Method / Channel <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required style="font-size:13px; height:38px;">
                                <option value="bkash">bKash Manual / Merchant</option>
                                <option value="nagad">Nagad Manual</option>
                                <option value="bank_transfer">Direct Bank Transfer / BEFTN / RTGS</option>
                                <option value="cash">Cash in Hand / Counter</option>
                                <option value="pos_card">POS Terminal / Card Swiped</option>
                                <option value="cheque">Bank Cheque / Pay Order</option>
                                <option value="adjustment">System Credit Adjustment</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Gross Amount (BDT) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold">৳</span>
                                <input type="number" step="0.01" name="gross_amount" id="modalGrossAmount" class="form-control fw-bold" required placeholder="0.00" oninput="calcCommission()" style="font-size:13px; height:38px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">OTA Commission (12%)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">৳</span>
                                <input type="number" step="0.01" name="commission_amount" id="modalCommAmount" class="form-control" placeholder="0.00" style="font-size:13px; height:38px;">
                            </div>
                            <small class="text-muted" style="font-size:11px;">Auto-calculated or custom</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Transaction Date</label>
                            <input type="datetime-local" name="created_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" style="font-size:13px; height:38px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Transaction Reference / TrxID / Cheque No</label>
                        <input type="text" name="txn_reference" class="form-control mono" placeholder="e.g. TXN-BK-984218 / BEFTN-00129 / CHQ-8821" style="font-size:13px; height:38px;">
                        <small class="text-muted" style="font-size:11px;">Leave blank to auto-generate a unique system reference.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Description / Purpose <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" required placeholder="e.g. Direct cash payment for Cox's Bazar booking / 12% Monthly commission received" style="font-size:13px; height:38px;">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Internal Admin Audit Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Audit remarks, approved by accounts team, deposit slip reference..." style="font-size:12.5px;"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-white px-4 py-3 d-flex justify-content-between border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Save to General Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
