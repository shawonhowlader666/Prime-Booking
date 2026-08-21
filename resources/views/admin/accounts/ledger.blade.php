@extends('layouts.admin')
@section('title', 'Double-Entry General Ledger | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Double-Entry General Ledger</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('ledgerTable')"><i class="fa-solid fa-copy me-1"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('ledgerTable', 'general_ledger')"><i class="fa-solid fa-file-excel me-1"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('ledgerTable', 'general_ledger')"><i class="fa-solid fa-file-csv me-1"></i> CSV</button>
            <button class="btn-export-pdf" onclick="exportTablePDF('ledgerTable', 'general_ledger')"><i class="fa-solid fa-file-pdf me-1"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('ledgerTable')"><i class="fa-solid fa-print me-1"></i> Print</button>
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-light border fw-bold text-secondary" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 14px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <i class="fa-solid fa-chart-pie"></i> <span>Accounts Hub</span>
            </a>
            <a href="{{ route('admin.accounts.ledger.export', request()->query()) }}" class="btn-add-primary" style="height:36px; font-size:12.5px; border-radius:4px; padding:0 16px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <i class="fa-solid fa-file-excel"></i> <span>Export Ledger CSV</span>
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.accounts.index') }}" style="text-decoration:none; color:inherit;">Accounts &amp; Finance</a>
        <span class="sep">-</span><strong style="color:#333;">General Ledger</strong>
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
        <form method="GET" action="{{ route('admin.accounts.ledger') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Search Reference / Details</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="TXN reference, details, guest..." style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Transaction Type</label>
                    <select name="type" class="form-select" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                        <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="credit" {{ ($filters['type'] ?? '') === 'credit' ? 'selected' : '' }}>Credit (Inflow)</option>
                        <option value="debit" {{ ($filters['type'] ?? '') === 'debit' ? 'selected' : '' }}>Debit (Outflow)</option>
                        <option value="payout" {{ ($filters['type'] ?? '') === 'payout' ? 'selected' : '' }}>Vendor Payout</option>
                        <option value="refund" {{ ($filters['type'] ?? '') === 'refund' ? 'selected' : '' }}>Refund</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Payment Gateway</label>
                    <select name="payment_method" class="form-select" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                        <option value="all" {{ ($filters['payment_method'] ?? 'all') === 'all' ? 'selected' : '' }}>All Gateways</option>
                        <option value="bkash" {{ ($filters['payment_method'] ?? '') === 'bkash' ? 'selected' : '' }}>bKash Instant</option>
                        <option value="nagad" {{ ($filters['payment_method'] ?? '') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="card" {{ ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' }}>Card / Visa / MC</option>
                        <option value="sslcommerz" {{ ($filters['payment_method'] ?? '') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                        <option value="cash" {{ ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' }}>Pay at Hotel</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">From Date</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control" style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">To Date</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control" style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold flex-grow-1" style="height:36px; font-size:12.5px; border-radius:4px; background-color:var(--primary); border:none;" title="Apply Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.accounts.ledger') }}" class="btn btn-light border fw-bold text-secondary" style="height:36px; font-size:12.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; padding:0 12px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-book-journal-whills me-2 text-primary"></i> Audit Ledger Records ({{ $ledgers->total() }} Transactions)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search in table..." onkeyup="filterTableSearch('ledgerTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="ledgerTable">
                <thead>
                    <tr>
                        <th>TXN Ref</th>
                        <th>Type</th>
                        <th>Hotel / Source</th>
                        <th style="text-align:right;">Gross (BDT)</th>
                        <th style="text-align:right;">Commission</th>
                        <th style="text-align:right;">Fee</th>
                        <th style="text-align:right;">Net Pay</th>
                        <th style="text-align:center;">Method</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $l)
                    <tr>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->txn_reference }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </td>
                        <td>
                            @if($l->type === 'credit')
                                <span class="badge bg-primary text-white" style="font-size:10.5px; font-weight:700; padding:3px 7px; border-radius:4px;">CREDIT (IN)</span>
                            @elseif($l->type === 'debit')
                                <span class="badge bg-danger text-white" style="font-size:10.5px; font-weight:700; padding:3px 7px; border-radius:4px;">DEBIT (OUT)</span>
                            @elseif($l->type === 'payout')
                                <span class="badge bg-warning text-dark" style="font-size:10.5px; font-weight:700; padding:3px 7px; border-radius:4px;">PAYOUT</span>
                            @else
                                <span class="badge bg-secondary text-white" style="font-size:10.5px; font-weight:700; padding:3px 7px; border-radius:4px;">{{ strtoupper($l->type) }}</span>
                            @endif
                            <small class="text-muted d-block mt-0.5" style="font-size:11px;">{{ ucfirst(str_replace('_', ' ', $l->category)) }}</small>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">
                                {{ $l->property?->name ?? ($l->booking?->property?->name ?? 'Prime Booking Platform') }}
                            </strong>
                            <span style="font-size:11px; color:#64748b;">
                                {{ $l->vendor?->name ?? 'Admin Direct Account' }}
                            </span>
                        </td>
                        <td style="text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#28c76f; font-size:13px;">
                            +৳ {{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#ea5455; font-size:13px;">
                            -৳ {{ number_format($l->gateway_fee, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:800; color:#1890ff; font-size:13px;">
                            ৳ {{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="text-align:center;">
                            <span class="badge bg-light text-secondary border" style="font-size:11px; font-weight:600; padding:3px 7px; border-radius:4px;">
                                {{ strtoupper($l->payment_method ?? 'ONLINE') }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge-status confirmed">
                                    <i class="fa-solid fa-check me-1"></i> Completed
                                </span>
                            @elseif($l->status === 'pending')
                                <span class="badge-status pending">
                                    <i class="fa-solid fa-clock me-1"></i> Pending
                                </span>
                            @else
                                <span class="badge-status cancelled">
                                    {{ ucfirst($l->status) }}
                                </span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="table-action-group justify-content-end">
                                @if($l->booking_id)
                                    <a href="{{ route('admin.bookings.show', $l->booking_id) }}" class="table-action-btn primary" title="View Booking Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endif
                                @if($l->vendor_id)
                                    <a href="{{ route('admin.accounts.vendor-statements.print', $l->vendor_id) }}" target="_blank" class="table-action-btn dark" title="Print Partner Tax Statement">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
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
