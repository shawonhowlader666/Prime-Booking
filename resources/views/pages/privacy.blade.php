@extends('layouts.main', ['activePage' => 'home'])

@section('title', 'Privacy Policy | Prime Booking Official')

@section('content')
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 22px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-shield-halved text-warning me-2" style="font-size: 20px;"></i> Privacy Policy &amp; Data Security
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500;">
                256-Bit SSL Encryption for Hotel Bookings, Payments &amp; Guest Data Privacy
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">🔒</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">SSL Secured</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">100% Protected</div>
                </div>
            </div>
            <span style="font-size: 38px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" class="d-none d-lg-inline-block">🛡️</span>
        </div>
    </div>
</div>
    <div class="card border border-gray-200 rounded-3 p-5 bg-white shadow-xs" style="border-radius: 12px !important;">
        <h3 class="fw-bold text-dark mb-4">Privacy Policy</h3>
        <p class="text-secondary small">Effective Date: July 2026</p>

        <h5 class="fw-bold text-dark mt-4">1. Information We Collect</h5>
        <p class="text-secondary small">We collect personal details such as your name, mobile number, email address, travel dates, and payment information to fulfill hotel, flight, and tour bookings.</p>

        <h5 class="fw-bold text-dark mt-4">2. How We Protect Your Data</h5>
        <p class="text-secondary small">All transaction data is processed over 256-bit SSL encrypted channels. Payment credentials for bKash, Nagad, and credit cards are processed through PCI-DSS compliant gateways.</p>

        <h5 class="fw-bold text-dark mt-4">3. Contacting Us</h5>
        <p class="text-secondary small">If you have any questions regarding privacy, contact us at <strong>primeaviation.ltd@gmail.com</strong> or call <strong>01770887733</strong>.</p>
    </div>
</div>
@endsection
