@extends('layouts.admin')
@section('title', 'Vendor Payouts & Settlement | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Users &amp; Vendors</span>
        <span class="sep">-</span><strong style="color:#333;">Vendor Payouts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Vendor Settlement &amp; Withdrawal Payouts</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage vendor earnings, withdrawal requests, bKash/bank settlements, and payout receipts</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('payoutsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('payoutsTable', 'Vendor_Payouts')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('payoutsTable', 'Vendor_Payouts')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('payoutsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('payoutsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addPayoutModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Process Direct Payout</span>
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stockifly KPI Summary Cards Row --}}
    @php
        $payoutColl = method_exists($payouts, 'getCollection') ? $payouts->getCollection() : collect($payouts);
        $totalVolume = $payoutColl->sum('amount');
        $disbursedPaid = $payoutColl->where('status', 'paid')->sum('amount');
        $pendingVolume = $payoutColl->where('status', 'pending')->sum('amount');
        $pendingCount = $payoutColl->where('status', 'pending')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">TOTAL DISBURSED</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($disbursedPaid) }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING APPROVALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($pendingVolume) }} ({{ $pendingCount }})</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">PAYOUT VOLUME</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">৳ {{ number_format($totalVolume) }}</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">RECORDED PAYOUTS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ count($payouts) }} Requests</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR CARD --}}
    <div class="card border-0 mb-4 shadow-sm" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.payouts.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#475569;">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:36px; font-size:12.5px; border-radius:4px;">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#475569;">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:36px; font-size:12.5px; border-radius:4px;">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#475569;">Payout Status</label>
                        <select name="status" class="form-select form-select-sm" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Completed / Paid</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex gap-2 align-self-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="height:36px; font-size:12.5px; border-radius:4px; background-color:var(--primary); border:none;">
                            <i class="fa-solid fa-filter me-1"></i> Filter Date
                        </button>
                        <a href="{{ route('admin.payouts.index') }}" class="btn btn-light border btn-sm fw-bold text-secondary" style="height:36px; font-size:12.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i> Vendor Payout Request Ledger ({{ count($payouts) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search payouts..." onkeyup="filterTableSearch('payoutsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="payoutsTable">
                <thead>
                    <tr>
                        <th>Vendor Partner</th>
                        <th>Payout Amount</th>
                        <th>Payment Method &amp; Account</th>
                        <th>Transaction Ref</th>
                        <th>Requested Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payouts as $p)
                    <tr>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $p->vendor_name ?? ($p->vendor?->name ?? 'Vendor Partner') }}</strong>
                            <span style="font-size:11px; color:#64748b;">Payout ID #{{ $p->id }}</span>
                        </td>
                        <td>
                            <strong style="color:#28c76f; font-size:14px; font-weight:800;">৳ {{ number_format($p->amount) }} BDT</strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary border-opacity-25" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px; display:inline-block; margin-bottom:2px;">
                                {{ $p->payment_method }}
                            </span>
                            <span style="font-size:11.5px; color:#64748b; display:block;">{{ $p->account_details ?: 'N/A' }}</span>
                        </td>
                        <td style="font-size:12.5px; font-family:monospace; color:#334155; font-weight:600;">
                            {{ $p->reference_number ?: 'N/A' }}
                        </td>
                        <td style="font-size:12.5px; color:#64748b;">
                            {{ $p->created_at ? (is_string($p->created_at) ? $p->created_at : $p->created_at->format('M d, Y')) : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($p->status) == 'paid' ? 'confirmed' : (strtolower($p->status) == 'pending' ? 'pending' : 'cancelled') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#viewPayoutModal{{ $p->id }}">
                                            <i class="fa-solid fa-file-invoice text-primary me-2"></i> View Receipt &amp; Voucher
                                        </button>
                                    </li>
                                    @if(strtolower($p->status) == 'pending')
                                        <li>
                                            <button class="dropdown-item py-1.5 px-3 text-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $p->id }}">
                                                <i class="fa-solid fa-check-circle me-2"></i> Approve &amp; Disburse Payout
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.payouts.update-status', $p->id) }}" method="POST" class="m-0" onsubmit="return confirm('Reject this payout request?');">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-xmark-circle me-2"></i> Reject Request
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <span class="dropdown-item-text py-1.5 px-3 text-muted">
                                                <i class="fa-solid fa-check-double text-success me-2"></i> Settlement Completed
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- VIEW PAYOUT RECEIPT MODAL --}}
                    <div class="modal fade" id="viewPayoutModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                        <i class="fa-solid fa-file-invoice text-primary me-2"></i> Official Payout Voucher #{{ $p->id }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding:20px;">
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Vendor Partner Name:</span>
                                            <strong class="text-dark" style="font-size:13.5px;">{{ $p->vendor_name ?: 'Vendor Partner' }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Disbursed Amount:</span>
                                            <strong class="text-success" style="font-size:16px; font-weight:800;">৳ {{ number_format($p->amount) }} BDT</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Payment Method / Gateway:</span>
                                            <span class="badge bg-primary text-white" style="font-size:11px;">{{ $p->payment_method }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Account / Phone Number:</span>
                                            <span class="text-dark fw-bold" style="font-size:12.5px;">{{ $p->account_details ?: 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Bank/bKash Transaction TrxID:</span>
                                            <span class="font-monospace text-dark fw-bold" style="font-size:13px; background:#e2e8f0; padding:2px 8px; border-radius:4px;">{{ $p->reference_number ?: 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-secondary" style="font-size:12px;">Settlement Status:</span>
                                            <span class="badge-status {{ strtolower($p->status) == 'paid' ? 'confirmed' : (strtolower($p->status) == 'pending' ? 'pending' : 'cancelled') }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($p->notes)
                                    <div class="p-3 bg-white rounded border">
                                        <span class="text-secondary d-block mb-1" style="font-size:11.5px; font-weight:700;">REMARKS &amp; AUDIT NOTES:</span>
                                        <p class="mb-0 text-dark" style="font-size:12.5px;">{{ $p->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer d-flex justify-content-between" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="window.print()" style="border-radius:4px;">
                                        <i class="fa-solid fa-print me-1"></i> Print Receipt Voucher
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal" style="border-radius:4px;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- APPROVE & DISBURSE MODAL --}}
                    @if(strtolower($p->status) == 'pending')
                    <div class="modal fade" id="approveModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <form action="{{ route('admin.payouts.update-status', $p->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="paid">
                                    <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                        <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i> Approve Payout #{{ $p->id }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="padding:20px;">
                                        <div class="p-3 bg-light rounded border mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-secondary" style="font-size:12px;">Vendor Partner:</span>
                                                <strong class="text-dark" style="font-size:13px;">{{ $p->vendor_name ?: 'Vendor Partner' }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-secondary" style="font-size:12px;">Payout Amount:</span>
                                                <strong class="text-success" style="font-size:14px;">৳ {{ number_format($p->amount) }} BDT</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-secondary" style="font-size:12px;">Payment Details:</span>
                                                <span class="text-dark fw-bold" style="font-size:12px;">{{ $p->payment_method }} ({{ $p->account_details }})</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Bank / bKash Transaction Reference No. <span style="color:#ff4d4f;">*</span></label>
                                            <input type="text" name="reference_number" class="form-control" placeholder="e.g. TRX-BK-998822 or DBBL-443311" required style="font-size:13px; height:38px; border-radius:4px;">
                                            <small class="text-muted" style="font-size:11px;">Enter bank transfer reference number or bKash TrxID for audit trail.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                        <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                                        <button type="submit" class="btn btn-success fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; border:none;">Confirm &amp; Disburse Payout <i class="fa-solid fa-check ms-1"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Payout Requests Recorded</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no vendor settlement payout requests found matching your filter criteria.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addPayoutModal">
                                    <i class="fa-solid fa-plus"></i> Process First Payout
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$payouts" :perPage="15" />
    </div>

</div>

{{-- PROCESS DIRECT PAYOUT MODAL --}}
<div class="modal fade" id="addPayoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('admin.payouts.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-hand-holding-dollar text-primary me-2"></i> Process Direct Vendor Payout
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Vendor Partner Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="vendor_name" class="form-control" placeholder="e.g. Ocean Paradise Resort & Spa" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Payout Amount (BDT) <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 50000" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Payment Method <span style="color:#ff4d4f;">*</span></label>
                            <select name="payment_method" class="form-select" style="font-size:13px; height:38px; border-radius:4px;" required>
                                <option value="bKash Merchant">bKash Merchant</option>
                                <option value="Nagad Personal">Nagad Personal</option>
                                <option value="Rocket">Rocket</option>
                                <option value="Dutch Bangla Bank (DBBL)">Dutch Bangla Bank (DBBL)</option>
                                <option value="City Bank Electronic Transfer">City Bank Electronic Transfer</option>
                                <option value="Brac Bank Transfer">Brac Bank Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Account Details / Phone / A/C No. <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="account_details" class="form-control" placeholder="e.g. A/C: 124.110.45892 (Gulshan Branch) or 01711..." required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Transaction Reference No. (Optional)</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="e.g. TRX-BK-998822" style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Notes / Remarks</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional settlement notes..." style="font-size:13px; border-radius:4px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Record Payout Request <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
