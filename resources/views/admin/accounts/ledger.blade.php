@extends('layouts.admin')

@section('title', 'Double-Entry General Ledger — Prime Booking')

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
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">General Ledger</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold" style="color:#0f172a; font-size:20px; letter-spacing:-0.3px;">
                    <i class="fa-solid fa-book-journal-whills text-primary me-2"></i> Double-Entry General Ledger
                </h4>
                <p class="text-muted mb-0" style="font-size:12.5px;">Complete transaction audit trail of credits, debits, OTA commissions, gateway charges &amp; disbursements.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary fw-bold" style="font-size:12.5px; height:36px; border-radius:4px;">
                    <i class="fa-solid fa-chart-pie me-1"></i> Accounts Hub
                </a>
                <a href="{{ route('admin.accounts.ledger.export', request()->query()) }}" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                    <i class="fa-solid fa-file-excel"></i> Export Ledger CSV
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="card border-0 mb-4" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.accounts.ledger') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-12">
                    <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Search Reference / Desc</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="TXN reference, details..." value="{{ $filters['search'] ?? '' }}" style="font-size:12.5px; height:34px; border-radius:4px;">
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
                    <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm" style="font-size:12.5px; height:34px; border-radius:4px;">
                        <option value="all" {{ ($filters['payment_method'] ?? 'all') === 'all' ? 'selected' : '' }}>All Gateways</option>
                        <option value="bkash" {{ ($filters['payment_method'] ?? '') === 'bkash' ? 'selected' : '' }}>bKash</option>
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
                    <button type="submit" class="btn btn-primary btn-sm fw-bold w-100" style="font-size:12.5px; height:34px; border-radius:4px; background:var(--primary); border:none;" title="Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.accounts.ledger') }}" class="btn btn-light border btn-sm text-secondary fw-bold" style="font-size:12.5px; height:34px; border-radius:4px;" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- LEDGER TABLE --}}
    <div class="card border-0 p-0" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color:#e2e8f0 !important;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Audit Ledger Records ({{ $ledgers->total() }} Transactions)
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">TXN REFERENCE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">TYPE &amp; CAT</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569;">SOURCE / VENDOR</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">GROSS (BDT)</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">COMMISSION</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">GATEWAY FEE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:right;">NET PAYABLE</th>
                        <th style="padding:12px 16px; font-weight:700; color:#475569; text-align:center;">METHOD</th>
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
                            @if($l->type === 'credit')
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">CREDIT (IN)</span>
                            @elseif($l->type === 'payout')
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">PAYOUT (OUT)</span>
                            @elseif($l->type === 'refund')
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">REFUND</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">{{ strtoupper($l->type) }}</span>
                            @endif
                            <small class="text-muted d-block mt-0.5" style="font-size:11px;">{{ ucwords(str_replace('_', ' ', $l->category)) }}</small>
                        </td>
                        <td style="padding:12px 16px;">
                            @if($l->property)
                                <strong class="text-dark d-block">{{ $l->property->name }}</strong>
                            @endif
                            <small class="text-muted">{{ $l->vendor?->name ?? 'Direct Prime Booking' }}</small>
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:800; color:#0f172a;">
                            ৳{{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:700; color:#28c76f;">
                            +৳{{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:600; color:#ea5455;">
                            -৳{{ number_format($l->gateway_fee, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:right; font-weight:800; color:#ff9f43;">
                            ৳{{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="padding:12px 16px; text-align:center;">
                            <span class="badge bg-light text-dark border fw-semibold px-2 py-1" style="font-size:10.5px; border-radius:3px; text-transform:uppercase;">
                                {{ $l->payment_method ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding:12px 16px; text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;"><i class="fa-solid fa-check me-1"></i> Completed</span>
                            @elseif($l->status === 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1" style="font-size:10.5px; border-radius:3px;">{{ ucfirst($l->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No ledger transaction records found for the selected filter.</p>
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
