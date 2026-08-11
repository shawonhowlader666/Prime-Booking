<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation — {{ $booking->booking_reference }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f5f7fa; color:#1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1890ff 0%, #0050a0 100%); padding:36px 32px; text-align:center; }
        .header img { height:36px; margin-bottom:16px; }
        .header h1 { color:#fff; font-size:24px; font-weight:700; margin-bottom:6px; }
        .header p { color:rgba(255,255,255,0.85); font-size:14px; }
        .badge { background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.4); border-radius:24px; padding:6px 18px; font-size:14px; font-weight:700; display:inline-block; margin-top:14px; letter-spacing:1px; font-family:monospace; }
        .body { padding:32px; }
        .greeting { font-size:16px; color:#1e293b; margin-bottom:20px; line-height:1.6; }
        .property-card { background: linear-gradient(135deg, #e6f7ff, #f0f9ff); border:1px solid #91d5ff; border-radius:10px; padding:20px; margin-bottom:24px; display:flex; gap:16px; align-items:flex-start; }
        .property-img { width:80px; height:60px; border-radius:8px; object-fit:cover; flex-shrink:0; }
        .property-info h3 { font-size:15px; font-weight:700; color:#1890ff; margin-bottom:4px; }
        .property-info p { font-size:12px; color:#595959; }
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
        .detail-item { background:#f8fafc; border:1px solid #e8e8e8; border-radius:8px; padding:14px 16px; }
        .detail-item .label { font-size:10px; font-weight:700; color:#8c8c8c; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .detail-item .value { font-size:14px; font-weight:700; color:#1e293b; }
        .price-box { background: linear-gradient(135deg, #f6ffed, #fff); border:2px solid #b7eb8f; border-radius:10px; padding:20px; margin-bottom:24px; }
        .price-row { display:flex; justify-content:space-between; font-size:13px; color:#595959; margin-bottom:8px; }
        .price-row.total { font-size:16px; font-weight:700; color:#1e293b; border-top:1px solid #e8e8e8; padding-top:10px; margin-top:4px; }
        .price-row.total .amount { color:#28c76f; font-size:18px; }
        .cta { text-align:center; margin:24px 0; }
        .cta a { background: linear-gradient(135deg, #1890ff, #0050a0); color:#fff; text-decoration:none; padding:14px 36px; border-radius:8px; font-weight:700; font-size:15px; display:inline-block; border-radius:50px; }
        .info-box { background:#fffbe6; border:1px solid #ffe58f; border-radius:8px; padding:16px; margin-bottom:20px; font-size:13px; color:#7d6608; }
        .info-box strong { display:block; margin-bottom:4px; color:#614700; }
        .footer { background:#f8fafc; border-top:1px solid #f0f0f0; padding:24px 32px; text-align:center; }
        .footer p { font-size:12px; color:#8c8c8c; line-height:1.6; }
        .footer a { color:#1890ff; text-decoration:none; }
        .social { display:flex; justify-content:center; gap:12px; margin:12px 0; }
        .social a { width:32px; height:32px; border-radius:50%; background:#1890ff; color:#fff; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:13px; }
        @media(max-width:480px) {
            .detail-grid { grid-template-columns:1fr; }
            .property-card { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">

        {{-- HEADER --}}
        <div class="header">
            <div style="font-size:28px; margin-bottom:10px;">✈️</div>
            <h1>Booking Confirmed!</h1>
            <p>Your reservation has been successfully placed.</p>
            <div class="badge">{{ $booking->booking_reference }}</div>
        </div>

        {{-- BODY --}}
        <div class="body">

            <p class="greeting">
                Dear <strong>{{ $booking->guest_name }}</strong>, 👋<br><br>
                Thank you for choosing <strong>PRIME BOOKING</strong>! Your booking has been confirmed.
                We look forward to making your stay unforgettable.
            </p>

            {{-- Property Info --}}
            <div class="property-card">
                <img src="{{ $property->primary_image ?? 'https://placehold.co/80x60/1890ff/white?text=Hotel' }}"
                     alt="{{ $property->name }}" class="property-img">
                <div class="property-info">
                    <h3>{{ $property->name }}</h3>
                    <p>📍 {{ $property->city ?? $property->address }}</p>
                    @if($room)
                        <p style="margin-top:4px;">🛏️ {{ $room->name }} — {{ $room->bed_type }}</p>
                    @endif
                    <p style="margin-top:4px;">
                        @for($i = 0; $i < ($property->star_rating ?? 4); $i++) ⭐ @endfor
                    </p>
                </div>
            </div>

            {{-- Booking Details Grid --}}
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="label">📅 Check-in</div>
                    <div class="value">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M d, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">📅 Check-out</div>
                    <div class="value">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M d, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">🌙 Duration</div>
                    <div class="value">{{ $booking->nights_count }} Night{{ $booking->nights_count != 1 ? 's' : '' }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">👥 Guests</div>
                    <div class="value">{{ $booking->guests ?? $booking->adults ?? 2 }} Guests</div>
                </div>
                <div class="detail-item">
                    <div class="label">📱 Payment</div>
                    <div class="value">{{ ucfirst($booking->payment_method ?? 'Online') }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">🟢 Status</div>
                    <div class="value" style="color:#28c76f;">✓ {{ ucfirst($booking->effective_status) }}</div>
                </div>
            </div>

            {{-- Price Breakdown --}}
            <div class="price-box">
                <div class="price-row">
                    <span>BDT {{ number_format($booking->price_per_night ?? ($booking->amount / max(1,$booking->nights_count))) }}/night × {{ $booking->nights_count }} nights</span>
                    <span>BDT {{ number_format($booking->subtotal ?? $booking->amount) }}</span>
                </div>
                @if($booking->tax_amount)
                <div class="price-row">
                    <span>VAT & Taxes (7.5%)</span>
                    <span>BDT {{ number_format($booking->tax_amount) }}</span>
                </div>
                @endif
                <div class="price-row total">
                    <span>Total Charged</span>
                    <span class="amount">BDT {{ number_format($booking->amount) }}</span>
                </div>
            </div>

            {{-- Special Requests --}}
            @if($booking->special_requests)
            <div class="info-box">
                <strong>📝 Your Special Requests:</strong>
                {{ $booking->special_requests }}
            </div>
            @endif

            {{-- Important Info --}}
            <div class="info-box" style="background:#fff2e8; border-color:#ffbb96;">
                <strong>⚠️ Important Information:</strong>
                • Check-in time: 2:00 PM &nbsp;|&nbsp; Check-out time: 12:00 PM<br>
                • Please carry a valid photo ID at check-in<br>
                • Contact property directly for early check-in requests
            </div>

            {{-- CTA --}}
            <div class="cta">
                <a href="{{ config('app.url') }}/booking/confirmation/{{ $booking->booking_reference }}">
                    View My Booking Voucher →
                </a>
            </div>

            <p style="font-size:13px; color:#8c8c8c; text-align:center; line-height:1.6;">
                Need help? Contact us at
                <a href="mailto:support@primeaviation.com" style="color:#1890ff;">support@primeaviation.com</a><br>
                or WhatsApp: <a href="https://wa.me/8801XXXXXXXXX" style="color:#1890ff;">+880 1XXX-XXXXXX</a>
            </p>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="social">
                <a href="#" title="Facebook">f</a>
                <a href="#" title="Instagram">in</a>
                <a href="#" title="WhatsApp" style="background:#25d366;">w</a>
            </div>
            <p>
                <strong>PRIME BOOKING</strong> — Bangladesh's Premium Travel & Hotel Booking Platform<br>
                <a href="{{ config('app.url') }}">www.primeaviation.com</a> &bull;
                <a href="{{ config('app.url') }}/privacy">Privacy Policy</a> &bull;
                <a href="{{ config('app.url') }}/terms">Terms</a><br><br>
                You received this email because you made a booking on PRIME BOOKING.<br>
                &copy; {{ date('Y') }} PRIME BOOKING. All rights reserved.
            </p>
        </div>

    </div>
</div>
</body>
</html>

