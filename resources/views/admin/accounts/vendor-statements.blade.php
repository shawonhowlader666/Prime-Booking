@extends('layouts.admin')
@section('title', 'Vendor Financial Statements & Settlements | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Vendor Financial Statements &amp; Settlements</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('statementsTable')"><i class="fa-solid fa-copy me-1"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('statementsTable', 'vendor_statements')"><i class="fa-solid fa-file-excel me-1"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('statementsTable', 'vendor_statements')"><i class="fa-solid fa-file-csv me-1"></i> CSV</button>
            <button class="btn-export-pdf" onclick="exportTablePDF('statementsTable', 'vendor_statements')"><i class="fa-solid fa-file-pdf me-1"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('statementsTable')"><i class="fa-solid fa-print me-1"></i> Print</button>
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border fw-bold text-secondary" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 14px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <i class="fa-solid fa-chart-pie"></i> <span>Accounts Hub</span>
            </a>
            <a href="{{ route('admin.payouts.index') }}" class="btn-add-primary" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 16px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <i class="fa-solid fa-money-bill-transfer"></i> <span>Manage Payouts</span>
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.accounts.index') }}" style="text-decoration:none; color:inherit;">Accounts &amp; Finance</a>
        <span class="sep">-</span><strong style="color:#333;">Vendor Statements</strong>
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
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-9">
                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Search Vendor Directory</label>
                <input type="text" class="form-control" placeholder="Search by partner name, email address, phone, or hotel listing..." onkeyup="filterTableSearch('statementsTable', this.value)" style="height:36px; font-size:12.5px; border-radius:4px;">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.accounts.vendor-statements') }}" class="btn btn-light border fw-bold text-secondary px-3 w-100" style="height:36px; font-size:12.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i> <span>Reset List</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stockifly KPI Summary Cards Row --}}
    @php
        $totalGrossSales = $vendors->sum(fn($v) => $v->finance_stats->gross_sales ?? 0);
        $totalCommission = $vendors->sum(fn($v) => $v->finance_stats->commission_deducted ?? 0);
        $totalAvailable  = $vendors->sum(fn($v) => $v->finance_stats->available_balance ?? 0);
        $totalPaidOut    = $vendors->sum(fn($v) => $v->finance_stats->payouts_paid ?? 0);
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL PARTNER VENDORS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $vendors->total() }} Partners</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">GROSS VENDOR TURNOVER</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1890ff; margin:0;">৳ {{ number_format($totalGrossSales, 2) }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACCRUED COMMISSION (12%)</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($totalCommission, 2) }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">AVAILABLE WITHDRAWABLE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($totalAvailable, 2) }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-hotel me-2 text-primary"></i> Partner Hotel Settlements Directory ({{ $vendors->total() }} Vendors Listed)</h6>
            <div style="font-size:12px; color:#64748b;">
                <span class="live-feed-badge me-2">Real-Time Ledger</span> 12.00% Platform Commission Rule
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="statementsTable">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th style="text-align:center;">Hotels</th>
                        <th style="text-align:center;">Bookings</th>
                        <th style="text-align:right;">Gross Sales</th>
                        <th style="text-align:right;">Commission (12%)</th>
                        <th style="text-align:right;">Net Pay</th>
                        <th style="text-align:right;">Paid Out</th>
                        <th style="text-align:right;">Balance</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background-color:#1890ff; font-size:14px;">
                                    {{ strtoupper(substr($v->name ?? 'V', 0, 1)) }}
                                </div>
                                <div>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $v->name }}</strong>
                                    <span style="font-size:11px; color:#64748b;">{{ $v->email }} @if($v->phone) &bull; {{ $v->phone }} @endif</span>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">
                                <i class="fa-solid fa-hotel me-1 text-primary"></i> {{ $v->properties_count }} Hotels
                            </span>
                        </td>
                        <td style="text-align:center; font-weight:700; color:#0f172a; font-size:13px;">
                            {{ $v->finance_stats->total_bookings }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($v->finance_stats->gross_sales, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#ff9f43; font-size:13px;">
                            -৳ {{ number_format($v->finance_stats->commission_deducted, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:800; color:#1890ff; font-size:13px;">
                            ৳ {{ number_format($v->finance_stats->net_payable, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#7367f0; font-size:13px;">
                            ৳ {{ number_format($v->finance_stats->payouts_paid, 2) }}
                        </td>
                        <td style="text-align:right;">
                            @if($v->finance_stats->available_balance > 0)
                                <span class="badge-status confirmed" style="font-size:11.5px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                    ৳ {{ number_format($v->finance_stats->available_balance, 2) }}
                                </span>
                            @else
                                <span style="font-size:11.5px; color:#8c8c8c;">৳ 0.00 (Cleared)</span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="table-action-group justify-content-end">
                                <a href="{{ route('admin.accounts.ledger', ['vendor_id' => $v->id]) }}" class="table-action-btn primary" title="View Audit Ledger">
                                    <i class="fa-solid fa-list-check"></i>
                                </a>
                                <a href="{{ route('admin.accounts.vendor-statements.print', $v->id) }}" target="_blank" class="table-action-btn dark" title="Print Official Statement">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-hotel fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No vendor partner financial records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="stockifly-table-footer">
            <div class="footer-left">
                Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} records
            </div>
            <div class="footer-right">
                {{ $vendors->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
