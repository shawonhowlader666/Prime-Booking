@extends('layouts.admin')

@section('title', 'Vendor Financial Statements & Settlements — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Vendor Financial Statements &amp; Settlements</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('statementsTable')" title="Copy Table to Clipboard">
                <i class="fa-regular fa-copy me-1"></i> Copy
            </button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('statementsTable', 'vendor_statements')" title="Export to Excel">
                <i class="fa-solid fa-file-excel me-1"></i> XL
            </button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('statementsTable', 'vendor_statements')" title="Export CSV">
                <i class="fa-solid fa-file-csv me-1"></i> CSV
            </button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('statementsTable', 'vendor_statements')" title="Export PDF">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn-tbl-print" onclick="printTable('statementsTable')" title="Print Table">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.accounts.index') }}" class="btn-tbl-col ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-chart-pie me-1"></i> Accounts Hub
            </a>
            <a href="{{ route('admin.payouts.index') }}" class="btn-add-primary ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-money-bill-transfer me-1"></i> Manage Payouts
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.accounts.index') }}">Accounts</a>
        <span class="sep">-</span><strong style="color:#333;">Vendor Statements</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI SUMMARY ROW --}}
    @php
        $totalGrossSales = $vendors->sum(fn($v) => $v->finance_stats->gross_sales ?? 0);
        $totalCommission = $vendors->sum(fn($v) => $v->finance_stats->commission_deducted ?? 0);
        $totalAvailable  = $vendors->sum(fn($v) => $v->finance_stats->available_balance ?? 0);
        $totalPaidOut    = $vendors->sum(fn($v) => $v->finance_stats->payouts_paid ?? 0);
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">TOTAL PARTNER VENDORS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $vendors->total() }} Active Partners</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#0f172a; font-size:10.5px; font-weight:700;">GROSS VENDOR TURNOVER</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">৳ {{ number_format($totalGrossSales, 2) }}</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f1f5f9; color:#0f172a; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#0f172a;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACCRUED COMMISSION (12%)</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($totalCommission, 2) }}</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">TOTAL PAYABLE BALANCE</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($totalAvailable, 2) }}</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- STOCKIFLY FILTER BAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <div class="row g-2 align-items-center">
            <div class="col-md-9 col-12">
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Quick search partner name, email, phone, or hotel listing..." onkeyup="filterTableSearch('statementsTable', this.value)" style="padding-left:34px; height:36px; font-size:13px;">
                </div>
            </div>
            <div class="col-md-3 col-12 text-end">
                <a href="{{ route('admin.accounts.vendor-statements') }}" class="btn btn-light border btn-sm text-secondary fw-bold" style="height:36px; font-size:12.5px; padding:0 16px; display:inline-flex; align-items:center; gap:6px;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i> <span>Reset List</span>
                </a>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-hotel text-primary me-2"></i> Partner Hotel Settlements Directory ({{ $vendors->total() }} Vendors Listed)
            </h6>
            <div style="font-size:12px; color:#64748b;">
                <span class="live-feed-badge me-2">Real-Time Ledger</span> Standard 12.00% OTA Commission Rule
            </div>
        </div>

        <div class="table-responsive">
            <table class="table stockifly-data-table align-middle mb-0" id="statementsTable">
                <thead>
                    <tr>
                        <th style="padding:12px 16px; font-weight:700;">VENDOR PARTNER</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:center;">PROPERTIES</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:center;">BOOKINGS</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">GROSS SALES</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">COMMISSION (12%)</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">NET EARNINGS</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">SETTLED PAID</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">AVAILABLE BALANCE</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                    <tr>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13.5px; color:#1e293b; display:block;">{{ $v->name }}</strong>
                            <span style="font-size:11.5px; color:#64748b;">
                                <i class="fa-solid fa-envelope me-1 text-muted"></i>{{ $v->email }} 
                                @if($v->phone) &bull; <i class="fa-solid fa-phone me-1 text-muted"></i>{{ $v->phone }} @endif
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <span class="badge-gateway" style="font-size:11px;">
                                <i class="fa-solid fa-hotel me-1 text-primary"></i>{{ $v->properties_count }} Hotels
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center; font-weight:700; color:#0f172a; font-size:13px;">
                            {{ $v->finance_stats->total_bookings }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($v->finance_stats->gross_sales, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#ff9f43; font-size:13px;">
                            -৳ {{ number_format($v->finance_stats->commission_deducted, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:800; color:#1890ff; font-size:13.5px;">
                            ৳ {{ number_format($v->finance_stats->net_payable, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#7367f0; font-size:13px;">
                            ৳ {{ number_format($v->finance_stats->payouts_paid, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            @if($v->finance_stats->available_balance > 0)
                                <span class="badge-status active" style="font-size:12px; font-weight:800; padding:4px 10px; background:#f6ffed; color:#28c76f; border:1px solid #b7eb8f; border-radius:4px;">
                                    ৳ {{ number_format($v->finance_stats->available_balance, 2) }}
                                </span>
                            @else
                                <span style="font-size:11.5px; color:#8c8c8c;">৳ 0.00 (Cleared)</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="{{ route('admin.accounts.ledger', ['vendor_id' => $v->id]) }}" class="btn btn-sm btn-outline-primary fw-bold d-inline-flex align-items-center gap-1.5" style="height:32px; font-size:12px; border-radius:4px; padding:0 12px;" title="View Vendor Audit Ledger">
                                    <i class="fa-solid fa-list"></i> <span>Ledger</span>
                                </a>
                                <a href="{{ route('admin.accounts.vendor-statements.print', $v->id) }}" target="_blank" class="btn btn-sm btn-outline-dark fw-bold d-inline-flex align-items-center gap-1.5" style="height:32px; font-size:12px; border-radius:4px; padding:0 12px;" title="Print Official Tax Statement">
                                    <i class="fa-solid fa-print"></i> <span>Statement</span>
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
