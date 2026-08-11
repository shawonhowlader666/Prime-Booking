@extends('layouts.main', ['activePage' => 'home'])

@section('title', 'Terms & Conditions | Prime Booking Official')

@section('content')
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 22px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-file-contract text-warning me-2" style="font-size: 20px;"></i> Terms &amp; Conditions
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500;">
                Official Terms of Service, Cancellation Policies &amp; User Agreements
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">📜</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Legal Terms</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Prime Booking</div>
                </div>
            </div>
            <span style="font-size: 38px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" class="d-none d-lg-inline-block">⚖️</span>
        </div>
    </div>
</div>
    <div class="card border border-gray-200 rounded-3 p-5 bg-white shadow-xs" style="border-radius: 12px !important;">
        <h3 class="fw-bold text-dark mb-4">Terms &amp; Conditions</h3>
        <p class="text-secondary small">Last Updated: July 2026</p>

        <h5 class="fw-bold text-dark mt-4">1. Booking Agreement</h5>
        <p class="text-secondary small">By placing a booking through Prime Booking, you agree to abide by the hotel policies, cancellation terms, and airline fare conditions associated with your selected itinerary.</p>

        <h5 class="fw-bold text-dark mt-4">2. Cancellation &amp; Refunds</h5>
        <p class="text-secondary small">Bookings marked with "Free Cancellation" can be canceled without penalty before the deadline specified on your booking voucher. Non-refundable bookings are subject to property rules.</p>

        <h5 class="fw-bold text-dark mt-4">3. Governing Law</h5>
        <p class="text-secondary small">These terms are governed by the laws of the People's Republic of Bangladesh. Trade License No: 19/515, Khulna, Bangladesh.</p>
    </div>
</div>
@endsection
