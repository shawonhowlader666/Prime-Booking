@extends('layouts.admin')
@section('title', 'Vendor Payouts & Settlement | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Finance</span>
        <span class="sep">-</span><strong style="color:#333;">Vendor Payouts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Vendor Settlement &amp; Withdrawal Payouts</h1>
        <button class="btn-export-csv" onclick="alert('Exporting Payouts CSV...')">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div>
                        <p class="kpi-value">BDT 1,65,000</p>
                        <p class="kpi-label">Total Payout Volume</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-up"></i> Disbursed to Vendors</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ff9f43;"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <p class="kpi-value">BDT 1,20,000</p>
                        <p class="kpi-label">Pending Approval</p>
                        <p class="kpi-growth-down"><i class="fa-solid fa-hourglass-half"></i> 1 Request Pending</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-building-columns"></i></div>
                    <div>
                        <p class="kpi-value">bKash &amp; DBBL</p>
                        <p class="kpi-label">Settlement Gateways</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-check"></i> Direct Transfer Active</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
    </div>

    {{-- Payouts Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Vendor Payout Request Ledger</h6>
            <span class="live-feed-badge">Finance Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Vendor Partner</th>
                        <th>Payout Amount</th>
                        <th>Payment Method &amp; Account</th>
                        <th>Transaction Ref</th>
                        <th>Requested Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payouts as $p)
                    <tr>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $p->vendor_name ?? (isset($p->vendor) ? $p->vendor->name : 'Vendor Partner') }}</strong>
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:14px;">BDT {{ number_format($p->amount) }}</strong>
                        </td>
                        <td>
                            <span class="badge-gateway">{{ $p->payment_method }}</span>
                            <span style="font-size:11px; color:#8c8c8c; display:block; margin-top:2px;">{{ $p->account_details }}</span>
                        </td>
                        <td style="font-size:12px; font-family:monospace;">
                            {{ $p->reference_number ?: 'N/A' }}
                        </td>
                        <td style="font-size:12px; color:#8c8c8c;">
                            {{ $p->created_at ? (is_string($p->created_at) ? $p->created_at : $p->created_at->format('M d, Y')) : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($p->status) == 'paid' ? 'confirmed' : (strtolower($p->status) == 'pending' ? 'pending' : 'cancelled') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            @if(strtolower($p->status) == 'pending')
                                <form action="{{ route('admin.payouts.update-status', $p->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn-table-action success">Approve &amp; Pay <i class="fa-solid fa-check"></i></button>
                                </form>
                            @else
                                <span style="font-size:11px; color:#8c8c8c;">Completed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#8c8c8c;">
                            No payout requests recorded.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

