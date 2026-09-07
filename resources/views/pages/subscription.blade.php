@extends('layouts.app')

@section('title', 'SaaS Subscription & License Hub | Prime PMS')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
    <ol class="breadcrumb mb-0 bg-transparent">
        <li class="breadcrumb-item"><a href="{{ url('/dashboard.html') }}" title="home">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/tuning.html') }}">Tuning</a></li>
        <li class="breadcrumb-item active" aria-current="page">Subscription Hub</li>
    </ol>
</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-2">Prime PMS Cloud License</span>
                        <h2 class="fw-bold mb-1">{{ $hotel->name }}</h2>
                        <p class="mb-0 opacity-75">Hotel Code: <strong>{{ $hotel->code }}</strong> | Plan: <strong>{{ $details['plan_name'] }}</strong></p>
                    </div>
                    <div class="text-md-end">
                        <div class="fs-4 fw-bold mb-1">
                            @if($details['is_active'])
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> ACTIVE LICENSE</span>
                            @else
                                <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-lock me-1"></i> EXPIRED / SUSPENDED</span>
                            @endif
                        </div>
                        <span class="text-white-50">Expires on: {{ $details['expires_at'] ? $details['expires_at']->format('d M, Y (h:i A)') : 'Lifetime' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="avatar lg rounded-circle bg-primary-subtle text-primary mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-calendar-days fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Remaining Validity</h6>
                    <h2 class="fw-bold mb-0 text-primary">{{ $details['days_remaining'] }} Days</h2>
                    <p class="text-muted small mt-2 mb-0">Automatic grace period protection active</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="avatar lg rounded-circle bg-success-subtle text-success mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-money-bill-wave fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Monthly License Fee</h6>
                    <h2 class="fw-bold mb-0 text-success">৳{{ number_format($details['monthly_fee'], 2) }}</h2>
                    <p class="text-muted small mt-2 mb-0">Billed monthly per property</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="avatar lg rounded-circle bg-info-subtle text-info mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plane-departure fs-4"></i>
                    </div>
                    <h6 class="text-muted mb-1">Prime Booking Master Bridge</h6>
                    <h4 class="fw-bold mb-0 text-info">HMAC SHA-256</h4>
                    <p class="text-muted small mt-2 mb-0">Remote control and telemetry verified</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Renew Subscription Form Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="card-title fw-bold mb-0">Renew / Extend Subscription</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('subscription.renew') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Select Duration</label>
                            <select name="months" class="form-select form-select-lg">
                                <option value="1">1 Month (৳{{ number_format($details['monthly_fee'] * 1, 2) }})</option>
                                <option value="3">3 Months (৳{{ number_format($details['monthly_fee'] * 3, 2) }})</option>
                                <option value="6">6 Months (৳{{ number_format($details['monthly_fee'] * 6, 2) }})</option>
                                <option value="12" selected>1 Year (৳{{ number_format($details['monthly_fee'] * 12, 2) }})</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select form-select-lg">
                                <option value="BKASH">bKash Merchant</option>
                                <option value="NAGAD">Nagad Direct</option>
                                <option value="SSLCOMMERZ">SSLCommerz (Visa / Mastercard)</option>
                                <option value="PRIME_PAY">Prime Booking Central Balance</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 fw-bold">
                            <i class="fa-solid fa-bolt me-2"></i> Instant License Renewal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subscription Billing Ledger Table -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="card-title fw-bold mb-0">Subscription Billing History</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref / Invoice</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Validity Range</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($details['history'] as $sub)
                                    <tr>
                                        <td><strong>{{ $sub->transaction_reference }}</strong></td>
                                        <td><span class="badge bg-primary-subtle text-primary">{{ $sub->plan_name }}</span></td>
                                        <td><strong>৳{{ number_format($sub->amount, 2) }}</strong></td>
                                        <td><span class="text-muted small">{{ $sub->payment_method }}</span></td>
                                        <td class="small">{{ $sub->starts_at?->format('d M y') }} - {{ $sub->expires_at?->format('d M y') }}</td>
                                        <td><span class="badge bg-success">PAID</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No past invoices recorded. Current trial or master license is active.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
