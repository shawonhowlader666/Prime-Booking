@extends('layouts.admin')

@section('title', 'Rewards & Loyalty Points Management | Admin Panel')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                <i class="fa-solid fa-coins text-warning me-2"></i> {{ __('Rewards & Loyalty Engine') }}
            </h1>
            <p class="text-muted mb-0" style="font-size: 14px;">
                {{ __('Manage point conversion rates, monitor wallet circulation, and approve user cash payout requests.') }}
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#rewardRulesModal">
                <i class="fa-solid fa-sliders me-1"></i> {{ __('Configure Reward Rules') }}
            </button>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                <div class="text-muted small fw-bold text-uppercase mb-1">Points in Circulation</div>
                <div class="h4 mb-0 fw-bold text-dark">{{ number_format($stats['total_points_in_circulation']) }} Pts</div>
                <small class="text-primary fw-semibold">Worth ৳{{ number_format($stats['total_points_in_circulation'] * $stats['point_value_bdt']) }}</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                <div class="text-muted small fw-bold text-uppercase mb-1">Total Redeemed Points</div>
                <div class="h4 mb-0 fw-bold text-dark">{{ number_format($stats['total_points_redeemed']) }} Pts</div>
                <small class="text-success fw-semibold">৳{{ number_format($stats['total_points_redeemed'] * $stats['point_value_bdt']) }} Disbursed</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                <div class="text-muted small fw-bold text-uppercase mb-1">Pending Payout Requests</div>
                <div class="h4 mb-0 fw-bold text-warning">{{ $stats['pending_payouts_count'] }} Requests</div>
                <small class="text-muted">Total: ৳{{ number_format($stats['pending_payouts_amount'], 2) }}</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
                <div class="text-muted small fw-bold text-uppercase mb-1">Earning & Value Rate</div>
                <div class="h5 mb-0 fw-bold text-dark">৳{{ number_format($stats['spend_per_point']) }} = 1 Pt</div>
                <small class="text-info fw-semibold">1 Pt = ৳{{ number_format($stats['point_value_bdt']) }} | Min: {{ $stats['min_redemption_points'] }} Pts</small>
            </div>
        </div>
    </div>

    {{-- Main Payout Requests Table --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark" style="font-size: 16px;">
                <i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> {{ __('Cash Payout Requests') }}
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.rewards.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-light' }} fw-bold">All</a>
                <a href="{{ route('admin.rewards.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-light' }} fw-bold">Pending</a>
                <a href="{{ route('admin.rewards.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-light' }} fw-bold">Approved</a>
                <a href="{{ route('admin.rewards.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-light' }} fw-bold">Rejected</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Request ID &amp; User</th>
                        <th>Points / Amount</th>
                        <th>Payment Method</th>
                        <th>Account Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">#REQ-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <small class="text-muted">{{ $payout->user?->name }} ({{ $payout->user?->email }})</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($payout->points) }} Pts</div>
                            <span class="badge bg-success-subtle text-success fw-bold font-monospace">৳{{ number_format($payout->amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary text-uppercase">{{ $payout->payment_gateway }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark font-monospace">{{ $payout->account_number }}</div>
                            <small class="text-muted">{{ $payout->account_name ?: 'Personal' }}</small>
                        </td>
                        <td>
                            @if($payout->status === 'pending')
                                <span class="badge bg-warning text-dark fw-bold">Pending Review</span>
                            @elseif($payout->status === 'approved')
                                <span class="badge bg-success fw-bold">Paid &amp; Approved</span>
                            @else
                                <span class="badge bg-danger fw-bold">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $payout->created_at->format('d M Y, h:i A') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            @if($payout->status === 'pending')
                            <div class="d-inline-flex gap-1">
                                <form action="{{ route('admin.rewards.approve', $payout->id) }}" method="POST" onsubmit="return confirm('Approve and mark payout of ৳{{ $payout->amount }} as paid?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm fw-bold" title="Approve & Pay">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $payout->id }}">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade text-start" id="rejectModal{{ $payout->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.rewards.reject', $payout->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Reject Payout #{{ $payout->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">Rejecting will automatically refund {{ $payout->points }} points back to user's wallet balance.</p>
                                                <label class="form-label fw-bold">Reason for Rejection</label>
                                                <input type="text" name="admin_note" class="form-control" placeholder="e.g. Account number does not match bKash account" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger fw-bold">Confirm Reject &amp; Refund</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                            <small class="text-muted font-monospace">{{ $payout->admin_note ?: 'Completed' }}</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block text-secondary"></i>
                            No payout requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $payouts->links() }}
        </div>
    </div>
</div>

{{-- Configure Reward Rules Modal --}}
<div class="modal fade" id="rewardRulesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('admin.rewards.settings.update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-sliders text-primary me-2"></i> Configure Reward Rules</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Spend Amount per 1 Point (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="reward_spend_per_point" class="form-control fw-bold" value="{{ $stats['spend_per_point'] }}" min="1" required>
                        </div>
                        <small class="text-muted">Currently: ৳1,000 spend = 1 Point</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Point Monetary Value (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.1" name="reward_point_value_bdt" class="form-control fw-bold" value="{{ $stats['point_value_bdt'] }}" min="0.1" required>
                        </div>
                        <small class="text-muted">Currently: 1 Point = ৳10 Value</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Minimum Points for Payout / Checkout</label>
                        <input type="number" name="reward_min_redemption_points" class="form-control fw-bold" value="{{ $stats['min_redemption_points'] }}" min="1" required>
                        <small class="text-muted">Currently: Minimum 100 Points required to redeem or withdraw</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Rules</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
