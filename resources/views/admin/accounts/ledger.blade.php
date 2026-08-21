@extends('layouts.admin')

@section('title', 'Double-Entry General Ledger — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Double-Entry General Ledger</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('ledgerTable')" title="Copy Table to Clipboard">
                <i class="fa-regular fa-copy me-1"></i> Copy
            </button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('ledgerTable', 'general_ledger')" title="Export to Excel">
                <i class="fa-solid fa-file-excel me-1"></i> XL
            </button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('ledgerTable', 'general_ledger')" title="Export CSV">
                <i class="fa-solid fa-file-csv me-1"></i> CSV
            </button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('ledgerTable', 'general_ledger')" title="Export PDF">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn-tbl-print" onclick="printTable('ledgerTable')" title="Print Table">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.accounts.index') }}" class="btn-tbl-col ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-chart-pie me-1"></i> Accounts Hub
            </a>
            <a href="{{ route('admin.accounts.ledger.export', request()->query()) }}" class="btn-add-primary ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-file-excel me-1"></i> Export Ledger CSV
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.accounts.index') }}">Accounts</a>
        <span class="sep">-</span><strong style="color:#333;">General Ledger</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- STOCKIFLY FILTER BAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('admin.accounts.ledger') }}" class="row g-2 align-items-end">
            <div class="col-md-3 col-12">
                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Search Reference / Details</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="TXN reference, guest, property..." value="{{ $filters['search'] ?? '' }}" style="font-size:12.5px; height:34px; border-radius:4px;">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Type</label>
                <select name="type" class="form-select form-select-sm" style="font-size:12.5px; height:34px; border-radius:4px;">
                    <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="credit" {{ ($filters['type'] ?? '') === 'credit' ? 'selected' : '' }}>Credit (Inflow)</option>
                    <option value="debit" {{ ($filters['type'] ?? '') === 'debit' ? 'selected' : '' }}>Debit (Outflow)</option>
                    <option value="payout" {{ ($filters['type'] ?? '') === 'payout' ? 'selected' : '' }}>Vendor Payout</option>
                    <option value="refund" {{ ($filters['type'] ?? '') === 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Payment Gateway</label>
                <select name="payment_method" class="form-select form-select-sm" style="font-size:12.5px; height:34px; border-radius:4px;">
                    <option value="all" {{ ($filters['payment_method'] ?? 'all') === 'all' ? 'selected' : '' }}>All Gateways</option>
                    <option value="bkash" {{ ($filters['payment_method'] ?? '') === 'bkash' ? 'selected' : '' }}>bKash Instant</option>
                    <option value="nagad" {{ ($filters['payment_method'] ?? '') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="card" {{ ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' }}>Card / Visa / MC</option>
                    <option value="sslcommerz" {{ ($filters['payment_method'] ?? '') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                    <option value="cash" {{ ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' }}>Pay at Hotel</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">From Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] ?? '' }}" style="font-size:12.5px; height:34px; border-radius:4px;">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">To Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] ?? '' }}" style="font-size:12.5px; height:34px; border-radius:4px;">
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm fw-bold w-100 d-inline-flex align-items-center justify-content-center" style="font-size:12.5px; height:34px; border-radius:4px; background:var(--primary); border:none;" title="Apply Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.accounts.ledger') }}" class="btn btn-light border btn-sm text-secondary fw-bold d-inline-flex align-items-center justify-content-center" style="font-size:12.5px; height:34px; border-radius:4px;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-book-journal-whills text-primary me-2"></i> Audit Ledger Records ({{ $ledgers->total() }} Transactions)
            </h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search in table..." onkeyup="filterTableSearch('ledgerTable', this.value)" style="height:32px; font-size:12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table stockifly-data-table align-middle mb-0" id="ledgerTable">
                <thead>
                    <tr>
                        <th style="padding:12px 16px; font-weight:700;">TXN REFERENCE</th>
                        <th style="padding:12px 16px; font-weight:700;">TYPE &amp; CAT</th>
                        <th style="padding:12px 16px; font-weight:700;">SOURCE / VENDOR</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">GROSS (BDT)</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">COMMISSION</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">GATEWAY FEE</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:right;">NET PAYABLE</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:center;">METHOD</th>
                        <th style="padding:12px 16px; font-weight:700; text-align:center;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->txn_reference }}</strong>
                            <span class="order-date">{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </td>
                        <td style="padding:14px 16px;">
                            @if($l->type === 'credit')
                                <span class="badge-status active" style="background:#e6f7ff; color:#1890ff; font-size:11px; font-weight:700;">CREDIT (IN)</span>
                            @elseif($l->type === 'debit')
                                <span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; font-size:11px; font-weight:700;">DEBIT (OUT)</span>
                            @elseif($l->type === 'payout')
                                <span class="badge-status pending" style="background:#fff7e6; color:#fa8c16; font-size:11px; font-weight:700;">PAYOUT</span>
                            @else
                                <span class="badge-status pending" style="font-size:11px; font-weight:700;">{{ strtoupper($l->type) }}</span>
                            @endif
                            <small class="text-muted d-block mt-0.5" style="font-size:10.5px;">{{ ucfirst(str_replace('_', ' ', $l->category)) }}</small>
                        </td>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">
                                {{ $l->property?->name ?? ($l->booking?->property?->name ?? 'Prime Booking Platform') }}
                            </strong>
                            <span style="font-size:11px; color:#64748b;">
                                {{ $l->vendor?->name ?? 'Admin Direct Account' }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#28c76f; font-size:13px;">
                            +৳ {{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#ff4d4f; font-size:13px;">
                            -৳ {{ number_format($l->gateway_fee, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:800; color:#1890ff; font-size:13.5px;">
                            ৳ {{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <span class="badge-gateway" style="font-size:11px;">
                                {{ strtoupper($l->payment_method ?? 'ONLINE') }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge-status confirmed" style="font-size:11px; font-weight:700;">
                                    <i class="fa-solid fa-check me-1"></i> Completed
                                </span>
                            @elseif($l->status === 'pending')
                                <span class="badge-status pending" style="font-size:11px; font-weight:700;">
                                    <i class="fa-solid fa-clock me-1"></i> Pending
                                </span>
                            @else
                                <span class="badge-status cancelled" style="font-size:11px; font-weight:700;">
                                    {{ ucfirst($l->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No ledger transactions found matching the selected criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
        <div class="stockifly-table-footer">
            <div class="footer-left">
                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} entries
            </div>
            <div class="footer-right">
                {{ $ledgers->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
