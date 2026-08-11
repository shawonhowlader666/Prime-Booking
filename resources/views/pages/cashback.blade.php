@extends('layouts.main', ['activePage' => 'cashback'])

@section('title', 'PrimeCash & Cashback Rewards | Prime Booking')

@section('content')
<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <div class="bg-white border shadow-sm" style="border-color: #cbd5e1 !important; border-radius: 20px !important; padding: 20px 14px 28px 14px;">
                    <div class="d-flex flex-column" style="gap: 4px;">
                        
                        <a href="{{ route('trips') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-calendar-check text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('My Trips') }}</span>
                        </a>

                        <a href="{{ route('bookings') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-suitcase text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('All bookings') }}</span>
                        </a>

                        <a href="{{ route('search.index') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-hotel text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('Hotels') }}</span>
                        </a>

                        <a href="{{ route('services') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-plane text-dark" style="font-size: 17px; transform: rotate(-45deg);"></i></div>
                            <span>{{ __('Flights') }}</span>
                        </a>

                        <a href="{{ route('search.index') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-icons text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('Activities') }}</span>
                        </a>

                        <a href="{{ route('messages') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-comment-dots text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('Property messages') }}</span>
                        </a>

                        <a href="{{ route('reviews') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-star text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('Reviews') }}</span>
                        </a>

                        <a href="{{ route('vip') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;">
                                <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;"><i class="fa-solid fa-star"></i></span>
                            </div>
                            <span>PrimeVIP</span>
                        </a>

                        <!-- PrimeCash (Active Blue Pill) -->
                        <a href="{{ route('cashback') }}" class="text-decoration-none d-flex align-items-center text-white fw-bold active-booking-tab" style="background-color: #2067e1; padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;">
                                <span class="bg-white text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">a</span>
                            </div>
                            <span>PrimeCash</span>
                        </a>

                        <!-- Cashback Rewards -->
                        <a href="{{ route('cashback') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-hand-holding-dollar text-dark" style="font-size: 17px;"></i></div>
                            <span>Cashback Rewards</span>
                        </a>

                        <a href="{{ route('pointsmax') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;">
                                <span class="bg-dark text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">P</span>
                            </div>
                            <span>PointsMAX</span>
                        </a>

                        <a href="{{ route('profile') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center;"><i class="fa-solid fa-user text-dark" style="font-size: 17px;"></i></div>
                            <span>{{ __('Profile') }}</span>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Right Column: PrimeCash & Cashback Wallet -->
            <div class="col-lg-9 col-md-8">
                
                <!-- Wallet Overview Banner -->
                <div class="card border-0 rounded-4 p-4 p-md-5 mb-4 shadow-sm" style="background: linear-gradient(135deg, #2067e1 0%, #1d4ed8 100%); color: #ffffff; border-radius: 20px !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                        <div>
                            <span class="badge bg-white text-primary fw-bold px-3 py-1.5 mb-3" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-wallet me-1"></i> PrimeCash Wallet Balance
                            </span>
                            <h2 class="fw-bold display-5 mb-2" style="letter-spacing: -0.5px;">
                                BDT 0.00
                            </h2>
                            <p class="mb-0 text-white-50" style="font-size: 14px;">
                                Use PrimeCash to instantly pay for hotel stays and flight tickets during checkout.
                            </p>
                        </div>

                        <div>
                            <button type="button" class="btn bg-white text-primary fw-bold px-4 py-2.5 rounded-pill shadow-sm" style="font-size: 14px;">
                                {{ __('Request Cashback Payout') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- How PrimeCash Works Card -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 rounded-4 p-3.5 bg-white shadow-xs h-100" style="border-radius: 16px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">
                                    1
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Book Eligible Stay</h6>
                                    <p class="text-secondary mb-0" style="font-size: 12.5px;">Choose hotels or flights tagged with PrimeCash rewards.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 rounded-4 p-3.5 bg-white shadow-xs h-100" style="border-radius: 16px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">
                                    2
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Earn Instant Cashback</h6>
                                    <p class="text-secondary mb-0" style="font-size: 12.5px;">Credits are automatically deposited into your wallet after trip.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 rounded-4 p-3.5 bg-white shadow-xs h-100" style="border-radius: 16px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning-subtle text-warning fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">
                                    3
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Pay or Withdraw (bKash)</h6>
                                    <p class="text-secondary mb-0" style="font-size: 12.5px;">Use for future bookings or cash out to bKash / Nagad.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cashback History Table Card -->
                <div class="card border shadow-xs p-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 17px;">{{ __('Cashback & Credit Activity') }}</h5>
                        <a href="{{ route('search.index') }}" class="fw-bold text-decoration-none" style="color: #2067e1; font-size: 13.5px;">{{ __('Earn Cashback on Hotels ➔') }}</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle text-start mb-0" style="font-size: 14px;">
                            <thead class="table-light text-secondary fw-semibold">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Booking / Description</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-secondary" style="font-size: 13px;">2026-07-28</td>
                                    <td>
                                        <span class="fw-bold text-dark">Welcome Bonus Cashback</span>
                                        <div class="text-muted" style="font-size: 12px;">Prime Booking Account Registration</div>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success fw-bold">Credited</span></td>
                                    <td class="text-end fw-bold text-success">+ BDT 500.00</td>
                                </tr>
                                <tr>
                                    <td class="text-secondary" style="font-size: 13px;">2026-07-15</td>
                                    <td>
                                        <span class="fw-bold text-dark">Cox's Bazar Sea Princess Hotel</span>
                                        <div class="text-muted" style="font-size: 12px;">Booking ID: AGD-998241</div>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary fw-bold">Processing</span></td>
                                    <td class="text-end fw-bold text-dark">BDT 250.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
