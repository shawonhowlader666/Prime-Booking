@extends('layouts.main', ['activePage' => 'rewards'])

@section('title', 'Prime Rewards Loyalty & Points | Prime Booking')
@section('meta_description', 'Earn 1 Reward Point for every ৳1,000 spent. Redeem points directly at checkout or cashout to bKash / Nagad / Bank once you reach 100 points.')

@section('content')
<style>
/* 1:1 Agoda / Prime Rewards Styling */
.rewards-page-wrapper {
    background-color: #f7f9fa;
    min-height: 88vh;
    padding: 36px 0 70px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.rewards-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 16px;
}
.reward-wallet-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #2067e1 60%, #3b82f6 100%);
    border-radius: 20px;
    color: #ffffff;
    box-shadow: 0 12px 32px -4px rgba(32, 103, 225, 0.35);
    position: relative;
    overflow: hidden;
}
.reward-step-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
    height: 100%;
}
.reward-step-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.1);
    border-color: #cbd5e1;
}
.progress-bar-custom {
    height: 10px;
    border-radius: 5px;
    background-color: rgba(255, 255, 255, 0.2);
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #facc15 0%, #fbbf24 100%);
    border-radius: 5px;
    transition: width 0.5s ease-in-out;
}
</style>

<div class="rewards-page-wrapper">
    <div class="rewards-container">
        <div class="row g-4">
            
            {{-- Left User Account Sidebar Navigation --}}
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="rewards" />
            </div>

            {{-- Right Column: Rewards Dashboard --}}
            <div class="col-lg-9 col-md-8">
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- ── 1. Rewards Hero Wallet Card ── --}}
                <div class="reward-wallet-card p-4 p-md-5 mb-4">
                    <div class="row align-items-center g-4">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                                    <i class="fa-solid fa-crown me-1"></i> Prime Rewards Loyalty
                                </span>
                                <span class="badge bg-white bg-opacity-25 text-white fw-bold px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                                    1 Pt = ৳{{ number_format($rewardSummary['point_value']) }} BDT
                                </span>
                            </div>

                            <h1 class="display-6 fw-bold mb-1" style="letter-spacing: -0.5px;">
                                {{ number_format($rewardSummary['points_balance']) }} <span style="font-size: 24px; font-weight: 500;">Points</span>
                            </h1>
                            <div class="h5 fw-bold text-warning mb-3">
                                ≈ ৳{{ number_format($rewardSummary['bdt_value'], 2) }} Cash Value
                            </div>

                            {{-- Progress towards 100 Points Milestone --}}
                            <div class="mb-3" style="max-width: 420px;">
                                <div class="d-flex justify-content-between text-white small fw-bold mb-1">
                                    <span>Milestone: {{ $rewardSummary['min_threshold'] }} Points (৳{{ number_format($rewardSummary['min_threshold_bdt']) }})</span>
                                    <span>{{ $rewardSummary['progress_percent'] }}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-bar-fill" style="width: {{ $rewardSummary['progress_percent'] }}%;"></div>
                                </div>
                                <small class="text-white-50 mt-1 d-block" style="font-size: 11.5px;">
                                    @if($rewardSummary['can_withdraw'])
                                        🎉 Congratulations! You have unlocked cash withdrawal &amp; checkout redemption!
                                    @else
                                        Earn {{ max(0, $rewardSummary['min_threshold'] - $rewardSummary['points_balance']) }} more points to unlock bKash / Nagad withdrawal.
                                    @endif
                                </small>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-5 text-md-end">
                            <div class="d-flex flex-column flex-sm-row flex-md-column gap-2 justify-content-md-end">
                                @if($rewardSummary['can_withdraw'])
                                    <button type="button" class="btn btn-warning text-dark fw-bold px-4 py-2.5 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#payoutModal">
                                        <i class="fa-solid fa-money-bill-transfer me-1"></i> Withdraw to bKash / Nagad
                                    </button>
                                @else
                                    <button type="button" class="btn btn-light bg-opacity-75 text-secondary fw-semibold px-4 py-2.5 rounded-pill" disabled title="Need minimum 100 points">
                                        <i class="fa-solid fa-lock me-1"></i> Withdraw (Req: 100 Pts)
                                    </button>
                                @endif
                                <a href="{{ route('search.index') }}" class="btn btn-outline-light fw-bold px-4 py-2.5 rounded-pill">
                                    <i class="fa-solid fa-hotel me-1"></i> Book Hotel &amp; Earn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── 2. How Prime Rewards Works (3-Step Matrix) ── --}}
                <h4 class="fw-bold text-dark mb-3" style="font-size: 19px;">
                    How Prime Rewards Works
                </h4>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="reward-step-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                                    1
                                </div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Book &amp; Spend</h6>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">
                                For every <strong>৳1,000</strong> you spend on eligible hotels or flights, you automatically earn <strong>1 Reward Point</strong>.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="reward-step-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-warning-subtle text-warning fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                                    2
                                </div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">1 Pt = ৳10 Value</h6>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">
                                Every point in your wallet carries real currency power (<strong>1 Point = ৳10 BDT</strong> cash redemption value).
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="reward-step-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                                    3
                                </div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Redeem or Withdraw</h6>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">
                                Reach <strong>100 Points (৳1,000)</strong> to withdraw cash via <strong>bKash / Nagad / Bank</strong> or use instantly during hotel checkout!
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ── 3. Immutable Transaction Ledger ── --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark" style="font-size: 16px;">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ __('Rewards History & Ledger') }}
                        </h5>
                        <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 12px;">
                            Lifetime Earned: {{ number_format($rewardSummary['total_earned_points']) }} Pts
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Description</th>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th>Value (BDT)</th>
                                    <th>Date</th>
                                    <th class="pe-4 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $tx->description }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary text-uppercase" style="font-size: 11px;">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $tx->points > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }} Pts
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">৳{{ number_format($tx->amount_value, 2) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $tx->created_at->format('d M Y, h:i A') }}</small>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($tx->status === 'completed')
                                            <span class="badge bg-success fw-bold">Completed</span>
                                        @elseif($tx->status === 'pending')
                                            <span class="badge bg-warning text-dark fw-bold">Pending Review</span>
                                        @else
                                            <span class="badge bg-danger fw-bold">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-coins fs-2 mb-2 d-block text-secondary"></i>
                                        No reward transactions recorded yet. Book a stay to earn your first points!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($transactions->hasPages())
                    <div class="p-3 border-top">
                        {{ $transactions->links() }}
                    </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>

{{-- ── 4. Withdraw to bKash / Nagad Modal ── --}}
<div class="modal fade" id="payoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('rewards.payout.submit') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> Request Cashout / Payout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Available Balance:</span>
                            <strong class="text-primary">{{ number_format($rewardSummary['points_balance']) }} Points</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Equivalent Cash Value:</span>
                            <strong class="text-success">৳{{ number_format($rewardSummary['bdt_value'], 2) }} BDT</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Points to Withdraw (Min: 100)</label>
                        <input type="number" name="points" id="withdraw_points_input" class="form-control fw-bold" min="{{ $rewardSummary['min_threshold'] }}" max="{{ $rewardSummary['points_balance'] }}" value="{{ $rewardSummary['points_balance'] }}" required oninput="calculateWithdrawBdt(this.value)">
                        <small class="text-muted" id="payout_bdt_calc">You will receive: <strong>৳{{ number_format($rewardSummary['bdt_value'], 2) }} BDT</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Payment Method</label>
                        <select name="payment_gateway" class="form-select" required>
                            <option value="bkash" selected>bKash (Personal / Agent)</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Account Number (e.g. 017xxxxxxxx)</label>
                        <input type="text" name="account_number" class="form-control font-monospace fw-bold" placeholder="01XXXXXXXXX" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Account Name (Optional)</label>
                        <input type="text" name="account_name" class="form-control" placeholder="e.g. Personal Account">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">Submit Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateWithdrawBdt(pts) {
    const pointValue = {{ $rewardSummary['point_value'] }};
    const bdt = (pts * pointValue).toFixed(2);
    document.getElementById('payout_bdt_calc').innerHTML = `You will receive: <strong>৳${Number(bdt).toLocaleString()} BDT</strong>`;
}
</script>
@endsection
