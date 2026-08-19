@extends('layouts.vendor')
@section('title', 'Vendor Earnings & Payout Requests | Vendor Partner Portal')

@php use App\Services\CurrencyService; @endphp

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-wallet text-success me-2"></i> Earnings &amp; Payout Requests
        </h1>
        <button type="button" class="btn btn-primary text-white fw-bold d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#requestPayoutModal" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px; border: none;">
            <i class="fa-solid fa-hand-holding-dollar"></i> Request Withdrawal
        </button>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Payout Requests</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Financial Overview Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; border-left: 4px solid #1890ff !important;">
                <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Total Bookings Revenue</small>
                <h4 class="fw-bold text-dark mb-0 mt-1" style="font-size: 20px;">{{ CurrencyService::format($totalRevenue) }}</h4>
                <small class="text-muted" style="font-size: 11px;">Gross revenue</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; border-left: 4px solid #ff4d4f !important;">
                <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Platform Commission (10%)</small>
                <h4 class="fw-bold text-danger mb-0 mt-1" style="font-size: 20px;">-{{ CurrencyService::format($platformCommission) }}</h4>
                <small class="text-muted" style="font-size: 11px;">Platform fee</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; border-left: 4px solid #13c2c2 !important;">
                <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Total Paid Out</small>
                <h4 class="fw-bold text-dark mb-0 mt-1" style="font-size: 20px;">{{ CurrencyService::format($totalPaidOut) }}</h4>
                <small class="text-muted" style="font-size: 11px;">Transferred to you</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 text-white" style="background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%); border-radius: 4px; border: none;">
                <small class="text-white-50 fw-bold text-uppercase" style="font-size: 11px;">Available for Payout</small>
                <h4 class="fw-bold text-white mb-0 mt-1" style="font-size: 20px;">{{ CurrencyService::format($availableBalance) }}</h4>
                <small class="text-white-50" style="font-size: 11px;">Ready to withdraw</small>
            </div>
        </div>
    </div>

    {{-- Payout History Table --}}
    <div class="card p-0 bg-white" style="border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0" style="font-size: 14px;"><i class="fa-solid fa-list-check me-1 text-primary"></i> Payout Request History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-2.5">Request Date</th>
                        <th class="py-2.5">Amount</th>
                        <th class="py-2.5">Payout Method</th>
                        <th class="py-2.5">Account Details</th>
                        <th class="pe-3 text-end py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $p)
                    <tr>
                        <td class="ps-3 fw-medium text-dark">
                            {{ \Carbon\Carbon::parse($p->created_at ?? $p->requested_at)->format('d M Y, h:i A') }}
                        </td>
                        <td class="fw-bold text-dark">{{ CurrencyService::format($p->amount) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold text-uppercase" style="font-size: 11px; border-radius: 3px;">
                                {{ $p->payment_method }}
                            </span>
                        </td>
                        <td class="text-secondary small">{{ $p->account_number }}</td>
                        <td class="pe-3 text-end">
                            <span class="badge bg-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning text-dark' : 'danger') }} fw-bold px-2.5 py-1" style="font-size: 11px; border-radius: 3px;">
                                {{ strtoupper($p->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-secondary">
                            <i class="fa-solid fa-wallet fs-1 opacity-25 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">No payout requests submitted yet</h6>
                            <p class="small text-muted mb-0" style="font-size: 12px;">Click "Request Withdrawal" above to transfer your available balance to your bKash, Nagad or Bank account.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Request Payout Modal --}}
<div class="modal fade" id="requestPayoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 4px;">
            <div class="modal-header border-bottom p-3 bg-light">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 15px;">
                    <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Request Payout Withdrawal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vendor.payouts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3.5">
                    <div class="alert alert-info border-0 mb-3 small p-2.5" style="border-radius: 4px;">
                        <strong>Available Balance:</strong> {{ CurrencyService::format($availableBalance) }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Withdrawal Amount (BDT ৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" placeholder="Min BDT 500" min="500" max="{{ (int)$availableBalance }}" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Payout Method <span class="text-danger">*</span></label>
                        <select name="payout_method" class="form-select" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                            <option value="bkash">bKash (Personal / Merchant)</option>
                            <option value="nagad">Nagad Wallet</option>
                            <option value="rocket">Rocket Wallet</option>
                            <option value="bank_transfer">Electronic Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Account Number / Bank Details <span class="text-danger">*</span></label>
                        <textarea name="account_details" class="form-control" rows="3" placeholder="e.g. bKash Personal Number: 01711223344 or Bank Name, Branch & A/C No" required style="border-radius: 4px; font-size: 13px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-2.5 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border text-secondary px-3" data-bs-dismiss="modal" style="border-radius: 4px; font-size: 13px;">Cancel</button>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; border: none;">SUBMIT REQUEST</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
