<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher — {{ $booking->booking_reference }} | Prime Booking</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #f1f5f9;
            padding: 24px;
        }
        .voucher-wrap { max-width: 800px; margin: 0 auto; }

        /* ── Main Card ── */
        .voucher-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* ── Header ── */
        .voucher-header {
            background: linear-gradient(135deg, #2067e1 0%, #1a52c0 100%);
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-name { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -0.5px; }
        .brand-sub  { font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 2px; text-transform: uppercase; letter-spacing: 1px; }
        .voucher-type { text-align: right; color: rgba(255,255,255,0.9); }
        .voucher-type-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .voucher-type-name  { font-size: 18px; font-weight: 800; color: #fff; }

        /* ── Reference strip ── */
        .ref-strip {
            background: #0f172a;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ref-label { color: rgba(255,255,255,0.6); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .ref-code  { color: #60a5fa; font-size: 22px; font-weight: 900; font-family: 'Courier New', monospace; letter-spacing: 3px; }
        .ref-status { background: #16a34a; color: #fff; padding: 4px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; }

        /* ── Property section ── */
        .property-section {
            padding: 20px 32px;
            background: #f8fafc;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .property-img { width: 100px; height: 72px; object-fit: cover; border-radius: 10px; flex-shrink: 0; border: 2px solid #e2e8f0; }
        .property-name { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .property-location { font-size: 13px; color: #64748b; }
        .property-stars { color: #f59e0b; font-size: 15px; letter-spacing: 1px; }
        .room-chip { display: inline-block; background: #dbeafe; color: #1d4ed8; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-top: 6px; }

        /* ── Details grid ── */
        .details-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; border-bottom: 1px solid #e2e8f0; }
        .detail-box { padding: 18px 24px; border-right: 1px solid #e2e8f0; }
        .detail-box:last-child { border-right: none; }
        .detail-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
        .detail-value { font-size: 16px; font-weight: 700; color: #0f172a; }
        .detail-sub   { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* ── Guest info ── */
        .guest-section { padding: 20px 32px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; border-bottom: 1px solid #e2e8f0; }
        .info-item .info-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 3px; }
        .info-item .info-val   { font-size: 13px; font-weight: 600; color: #0f172a; }

        /* ── Price summary ── */
        .price-section { padding: 20px 32px; border-bottom: 1px solid #e2e8f0; }
        .price-table { width: 100%; }
        .price-table tr td { padding: 6px 0; font-size: 13px; color: #475569; }
        .price-table tr td:last-child { text-align: right; font-weight: 600; }
        .price-total td { font-size: 16px; font-weight: 800; color: #0f172a; border-top: 2px solid #e2e8f0; padding-top: 10px; }

        /* ── Policies ── */
        .policies-section { padding: 20px 32px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .policy-item { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: #475569; }

        /* ── Footer ── */
        .voucher-footer { padding: 16px 32px; background: #1e293b; text-align: center; }
        .footer-text { color: rgba(255,255,255,0.6); font-size: 11px; }
        .footer-contact { color: rgba(255,255,255,0.85); font-size: 12px; font-weight: 600; margin-top: 4px; }

        /* ── Print Actions (no-print) ── */
        .print-actions { display: flex; gap: 12px; justify-content: center; margin-bottom: 20px; }
        .print-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px;
            border-radius: 10px; font-weight: 700; font-size: 14px;
            text-decoration: none; cursor: pointer; border: none; transition: all 0.2s;
        }
        .btn-blue  { background: #2067e1; color: #fff; }
        .btn-green { background: #16a34a; color: #fff; }
        .btn-gray  { background: #fff; color: #374151; border: 2px solid #e2e8f0; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-actions { display: none; }
            .voucher-card { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
    {{-- ── Print / Download Actions ── --}}
    <div class="print-actions no-print">
        <button onclick="window.print()" class="print-btn btn-blue">
            🖨️ Print Voucher
        </button>
        <a href="{{ route('booking.confirmation', $booking->booking_reference) }}" class="print-btn btn-gray">
            ← Back to Booking
        </a>
        <a href="{{ route('home') }}" class="print-btn btn-gray">
            🏠 Home
        </a>
    </div>

    <div class="voucher-wrap">
    <div class="voucher-card">

        {{-- ── Header ── --}}
        <div class="voucher-header">
            <div>
                <div class="brand-name">PRIME BOOKING</div>
                <div class="brand-sub">Official Hotel Accommodation Voucher</div>
            </div>
            <div class="voucher-type">
                <div class="voucher-type-label">Document Type</div>
                <div class="voucher-type-name">E-TICKET</div>
                <div style="font-size:11px; color:rgba(255,255,255,0.7); margin-top:2px;">Present this at hotel check-in</div>
            </div>
        </div>

        {{-- ── Booking Reference Strip ── --}}
        <div class="ref-strip">
            <div>
                <div class="ref-label">Booking Reference</div>
                <div class="ref-code">{{ $booking->booking_reference }}</div>
            </div>
            <div style="text-align:right;">
                <div class="ref-label" style="margin-bottom:4px;">Booking Status</div>
                <span class="ref-status">✓ {{ strtoupper($booking->effective_status) }}</span>
            </div>
        </div>

        {{-- ── Property Information ── --}}
        <div class="property-section">
            <img src="{{ $booking->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80' }}"
                 alt="{{ $booking->property?->name }}"
                 class="property-img">
            <div>
                <div class="property-stars">
                    @for($i = 0; $i < intval($booking->property?->star_rating ?? 5); $i++)★@endfor
                </div>
                <div class="property-name">{{ $booking->property?->name ?? 'Property' }}</div>
                <div class="property-location">📍 {{ $booking->property?->address ?? $booking->property?->city }}, Bangladesh</div>
                @if($booking->room)
                <div class="room-chip">🛏 {{ $booking->room->name }}</div>
                @endif
            </div>
        </div>

        {{-- ── Stay Details Grid ── --}}
        <div class="details-grid">
            <div class="detail-box">
                <div class="detail-label">Check-in Date</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</div>
                <div class="detail-sub">{{ \Carbon\Carbon::parse($booking->check_in)->format('l') }} · After 2:00 PM</div>
            </div>
            <div class="detail-box">
                <div class="detail-label">Check-out Date</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</div>
                <div class="detail-sub">{{ \Carbon\Carbon::parse($booking->check_out)->format('l') }} · Before 12:00 noon</div>
            </div>
            <div class="detail-box">
                <div class="detail-label">Duration · Guests</div>
                <div class="detail-value">{{ $booking->nights_count }} Night{{ $booking->nights_count > 1 ? 's' : '' }}</div>
                <div class="detail-sub">{{ $booking->guests ?? 2 }} Guest{{ ($booking->guests ?? 2) > 1 ? 's' : '' }}</div>
            </div>
        </div>

        {{-- ── Guest Information ── --}}
        <div class="guest-section">
            <div class="info-item">
                <div class="info-label">Guest Name</div>
                <div class="info-val">{{ $booking->guest_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email Address</div>
                <div class="info-val">{{ $booking->guest_email }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone Number</div>
                <div class="info-val">{{ $booking->guest_phone ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Booking Date</div>
                <div class="info-val">{{ $booking->created_at->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Payment Method</div>
                <div class="info-val">{{ ucfirst(str_replace('_', ' ', $booking->payment_method ?? 'Pending')) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Payment Status</div>
                <div class="info-val" style="color:{{ $booking->payment_status === 'paid' ? '#16a34a' : '#ca8a04' }};">
                    {{ ucfirst($booking->payment_status ?? 'pending') }}
                </div>
            </div>
        </div>

        {{-- ── Price Summary ── --}}
        @php
            $vNights   = $booking->nights_count;
            $vRate     = $booking->price_per_night > 0 ? $booking->price_per_night : ($booking->property?->price_per_night ?? 12500);
            $vSubtotal = $booking->subtotal > 0 ? $booking->subtotal : ($vRate * $vNights);
            $vAddons   = $addons->sum('price');
            $vTax      = $booking->tax_amount > 0 ? $booking->tax_amount : round(($vSubtotal + $vAddons) * 0.075);
            $vTotal    = $vSubtotal + $vAddons + $vTax;
        @endphp
        <div class="price-section">
            <div style="font-size:13px; font-weight:700; color:#475569; text-transform:uppercase; margin-bottom:12px; letter-spacing:0.5px;">Price Breakdown</div>
            <table class="price-table">
                <tr>
                    <td>Room Rate ({{ $vNights }} night{{ $vNights > 1 ? 's' : '' }} × {{ \App\Services\CurrencyService::format($vRate) }})</td>
                    <td>{{ \App\Services\CurrencyService::format($vSubtotal) }}</td>
                </tr>
                @if($addons->isNotEmpty())
                @foreach($addons as $addon)
                <tr>
                    <td>{{ $addon->addon_name }}</td>
                    <td>{{ \App\Services\CurrencyService::format($addon->price) }}</td>
                </tr>
                @endforeach
                @endif
                <tr>
                    <td>Taxes & Fees (7.5%)</td>
                    <td>{{ \App\Services\CurrencyService::format($vTax) }}</td>
                </tr>
                <tr class="price-total">
                    <td><strong>Total Amount Charged</strong></td>
                    <td><strong style="color:#2067e1;">{{ \App\Services\CurrencyService::format($vTotal) }}</strong></td>
                </tr>
            </table>
        </div>

        @if($booking->special_requests)
        <div style="padding:14px 32px; background:#fffbeb; border-bottom:1px solid #fef3c7; font-size:12px;">
            <strong style="color:#92400e;">📝 Special Request:</strong>
            <span style="color:#78350f;"> {{ $booking->special_requests }}</span>
        </div>
        @endif

        {{-- ── Policies ── --}}
        <div class="policies-section">
            <div class="policy-item">
                <span style="color:#2067e1;">ℹ️</span>
                <span>Please present this voucher (printed or digital) at the hotel front desk upon check-in.</span>
            </div>
            <div class="policy-item">
                <span style="color:#16a34a;">✅</span>
                <span>Free cancellation up to 24 hours before check-in date. Contact us for amendments.</span>
            </div>
            <div class="policy-item">
                <span style="color:#f59e0b;">⚠️</span>
                <span>Room type and availability are subject to confirmation by the property.</span>
            </div>
            <div class="policy-item">
                <span style="color:#dc2626;">📞</span>
                <span>For urgent assistance: <strong>+880 1800-PRIME</strong> or support@primebooking.com</span>
            </div>
        </div>

        {{-- ── Footer ── --}}
        <div class="voucher-footer">
            <div class="footer-text">This is an official booking voucher issued by Prime Booking · primebooking.com</div>
            <div class="footer-contact">📞 +880 1800-PRIME · ✉ support@primebooking.com · 🌐 www.primebooking.com</div>
            <div style="color:rgba(255,255,255,0.4); font-size:10px; margin-top:6px;">
                Generated: {{ now()->format('d M Y, h:i A') }} · Ref: {{ $booking->booking_reference }}
            </div>
        </div>

    </div>
    </div>
</body>
</html>
