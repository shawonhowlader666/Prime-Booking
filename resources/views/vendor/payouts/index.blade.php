@extends('layouts.main', ['activePage' => 'vendor'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Vendor Earnings & Payout Requests | Prime Booking Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-wallet text-success me-2"></i> {{ __('Vendor Earnings & Payout Requests') }}
                </h3>
                <p class="text-secondary small mb-0">Track total revenue from bookings and withdraw available balance to bKash or Bank.</p>
            </div>
            
            <button class="btn text-white fw-bold rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#requestPayoutModal" style="background-color: #2067e1;">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ __('Request Withdrawal') }}
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Financial Overview Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Total Bookings Revenue</small>
                    <h4 class="fw-bold text-dark mb-0 mt-1">{{ CurrencyService::format($totalRevenue) }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Gross revenue</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Platform Commission (10%)</small>
                    <h4 class="fw-bold text-danger mb-0 mt-1">-{{ CurrencyService::format($platformCommission) }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Platform fee</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-secondary fw-bold text-uppercase" style="font-size: 11px;">Total Paid Out</small>
                    <h4 class="fw-bold text-info mb-0 mt-1">{{ CurrencyService::format($totalPaidOut) }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Transferred to you</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
                    <small class="text-white-50 fw-bold text-uppercase" style="font-size: 11px;">Available for Payout</small>
                    <h4 class="fw-bold text-white mb-0 mt-1">{{ CurrencyService::format($availableBalance) }}</h4>
                    <small class="text-white-50" style="font-size: 11px;">Ready to withdraw</small>
                </div>
            </div>
        </div>

        {{-- Payout History Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i> Payout Request History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px;">
                        <tr>
                            <th class="ps-4">Request Date</th>
                            <th>Amount</th>
                            <th>Payout Method</th>
                            <th>Account Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $p)
                        <tr>
                            <td class="ps-4 fw-medium text-dark">
                                {{ \Carbon\Carbon::parse($p->created_at ?? $p->requested_at)->format('d M Y, h:i A') }}
                            </td>
                            <td class="fw-bold text-dark">{{ CurrencyService::format($p->amount) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold text-uppercase">
                                    {{ $p->payment_method }}
                                </span>
                            </td>
                            <td class="font-mono text-secondary small">{{ $p->account_number }}</td>
                            <td>
                                <span class="badge bg-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning text-dark' : 'danger') }} fw-bold px-3 py-1">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-wallet display-4 opacity-25 d-block mb-3"></i>
                                <h6 class="fw-bold text-dark">No payout requests submitted yet</h6>
                                <p class="small mb-0">Click "Request Withdrawal" above to transfer your available balance to your bKash or Bank account.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Request Payout Modal --}}
<div class="modal fade" id="requestPayoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom p-4 bg-light">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Request Payout Withdrawal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vendor.payouts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-3 small">
                        <strong>Available Balance:</strong> {{ CurrencyService::format($availableBalance) }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Withdrawal Amount (BDT ৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control rounded-3" placeholder="Min BDT 500" min="500" max="{{ (int)$availableBalance }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Payout Method <span class="text-danger">*</span></label>
                        <select name="payout_method" class="form-select rounded-3" required>
                            <option value="bkash">bKash (Merchant / Personal)</option>
                            <option value="nagad">Nagad Wallet</option>
                            <option value="rocket">Rocket Wallet</option>
                            <option value="bank_transfer">Electronic Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Account Number / Bank Details <span class="text-danger">*</span></label>
                        <textarea name="account_details" class="form-control rounded-3" rows="3" placeholder="e.g. bKash Personal Number: 01711223344 or Bank Name, Branch & A/C No" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-bold rounded-pill px-4" style="background-color: #2067e1;">SUBMIT PAYOUT REQUEST</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
