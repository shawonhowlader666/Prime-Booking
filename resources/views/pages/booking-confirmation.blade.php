@extends('layouts.main', ['activePage' => 'bookings'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Booking Confirmed — ' . $booking->booking_reference . ' | Prime Booking')

@section('content')
<style>
.confirm-page { background: linear-gradient(160deg, #f0f9ff 0%, #e8f4fd 50%, #f0fdf4 100%); min-height: 100vh; padding: 40px 16px 80px; }
.confirm-card { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); overflow: hidden; }

/* Success header */
.confirm-header { background: linear-gradient(135deg, #2067e1 0%, #1a52c0 100%); padding: 32px; text-align: center; }
.success-icon { width: 72px; height: 72px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; animation: pulse-green 2s infinite; }
@keyframes pulse-green { 0%,100%{ box-shadow: 0 0 0 0 rgba(255,255,255,0.4); } 50%{ box-shadow: 0 0 0 12px rgba(255,255,255,0); } }
.ref-badge { display: inline-block; background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.4); color: #fff; border-radius: 50px; padding: 8px 24px; font-size: 18px; font-weight: 800; font-family: monospace; letter-spacing: 2px; margin-top: 12px; }

/* Detail rows */
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.detail-cell { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
.detail-cell:nth-child(odd) { border-right: 1px solid #f1f5f9; }
.detail-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
.detail-val { font-size: 15px; font-weight: 700; color: #0f172a; }

/* Price summary */
.price-row { display: flex; justify-content: space-between; padding: 8px 20px; font-size: 13px; color: #475569; }
.price-row.total { font-size: 16px; font-weight: 700; color: #0f172a; background: #f8fafc; border-top: 2px solid #e2e8f0; padding-top: 14px; padding-bottom: 14px; }

/* Action buttons */
.action-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
.btn-primary-action { background: #2067e1; color: #fff; }
.btn-primary-action:hover { background: #1a52c0; color: #fff; transform: translateY(-1px); }
.btn-outline-action { background: #fff; color: #2067e1; border: 2px solid #2067e1; }
.btn-outline-action:hover { background: #f0f6ff; color: #2067e1; }

/* Status badge */
.status-confirmed { background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
.status-pending { background: #fef9c3; color: #ca8a04; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }

@media (max-width: 576px) {
    .detail-grid { grid-template-columns: 1fr; }
    .detail-cell:nth-child(odd) { border-right: none; }
}
</style>

<div class="confirm-page">

    {{-- ── Confirmation Header ── --}}
    <div class="confirm-card">

        <div class="confirm-header">
            <div class="success-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h1 style="color:#fff; font-size:26px; font-weight:800; margin-bottom:6px;">Booking Confirmed! 🎉</h1>
            <p style="color:rgba(255,255,255,0.85); font-size:14px; margin:0;">
                Your reservation is confirmed. A voucher has been sent to <strong>{{ $booking->guest_email }}</strong>
            </p>
            <div class="ref-badge">{{ $booking->booking_reference }}</div>
        </div>

        {{-- Property Banner --}}
        <div style="display:flex; align-items:center; gap:16px; padding:20px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <img src="{{ $booking->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80' }}"
                 alt="{{ $booking->property?->name }}"
                 style="width:80px; height:60px; object-fit:cover; border-radius:10px; flex-shrink:0;">
            <div class="flex-grow-1">
                <div style="font-size:16px; font-weight:700; color:#0f172a;">{{ $booking->property?->name ?? 'Property' }}</div>
                <div style="font-size:12px; color:#64748b;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $booking->property?->city }}, Bangladesh</div>
                @if($booking->room)
                <div style="margin-top:4px;"><span style="font-size:11px; background:#f0f6ff; color:#2067e1; padding:2px 8px; border-radius:4px; font-weight:600;"><i class="fa-solid fa-bed me-1"></i>{{ $booking->room->name }}</span></div>
                @endif
            </div>
            <span class="{{ $booking->effective_status === 'confirmed' ? 'status-confirmed' : 'status-pending' }}">
                {{ ucfirst($booking->effective_status) }}
            </span>
        </div>

        {{-- Stay Details Grid --}}
        <div class="detail-grid">
            <div class="detail-cell">
                <div class="detail-label">Check-in</div>
                <div class="detail-val">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') }}</div>
                <div style="font-size:11px; color:#64748b;">From 2:00 PM</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Check-out</div>
                <div class="detail-val">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') }}</div>
                <div style="font-size:11px; color:#64748b;">Until 12:00 noon</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Duration</div>
                <div class="detail-val">{{ $booking->nights_count }} Night{{ $booking->nights_count > 1 ? 's' : '' }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Guests</div>
                <div class="detail-val">{{ $booking->guests ?? 2 }} Guest{{ ($booking->guests ?? 2) > 1 ? 's' : '' }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Guest Name</div>
                <div class="detail-val">{{ $booking->guest_name }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Payment Method</div>
                <div class="detail-val">{{ ucfirst(str_replace('_', ' ', $booking->payment_method ?? 'pending')) }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Booking Date</div>
                <div class="detail-val">{{ $booking->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Payment Status</div>
                <div class="detail-val">
                    @if($booking->payment_status === 'paid')
                        <span class="status-confirmed">Paid</span>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Price Breakdown --}}
        @php
            $cNights   = $booking->nights_count;
            $cRate     = $booking->price_per_night > 0 ? $booking->price_per_night : ($booking->property?->price_per_night ?? 12500);
            $cSubtotal = $booking->subtotal > 0 ? $booking->subtotal : ($cRate * $cNights);
            $cAddons   = $addons->sum('price');
            $cTax      = $booking->tax_amount > 0 ? $booking->tax_amount : round(($cSubtotal + $cAddons) * 0.075);
            $cTotal    = $cSubtotal + $cAddons + $cTax;
        @endphp
        <div class="py-2" style="border-bottom: 1px solid #f1f5f9;">
            <div class="price-row">
                <span>Room rate × {{ $cNights }} night{{ $cNights > 1 ? 's' : '' }}</span>
                <span>{{ CurrencyService::format($cSubtotal) }}</span>
            </div>
            @if($addons->isNotEmpty())
            @foreach($addons as $addon)
            <div class="price-row">
                <span>{{ $addon->addon_name }}</span>
                <span>{{ CurrencyService::format($addon->price) }}</span>
            </div>
            @endforeach
            @endif
            <div class="price-row">
                <span>Taxes & Fees (7.5%)</span>
                <span>{{ CurrencyService::format($cTax) }}</span>
            </div>
            <div class="price-row total">
                <span>Total Charged</span>
                <span style="color:#2067e1;">{{ CurrencyService::format($cTotal) }}</span>
            </div>
        </div>

        @if($booking->special_requests)
        <div style="padding:16px 20px; background:#fffbeb; border-bottom:1px solid #fef3c7; font-size:13px;">
            <strong style="color:#92400e;"><i class="fa-solid fa-note-sticky me-1"></i>Special Request:</strong>
            <span class="text-secondary ms-1">{{ $booking->special_requests }}</span>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div style="padding:24px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:center;">
            <a href="{{ route('booking.voucher', $booking->booking_reference) }}" target="_blank" class="action-btn btn-primary-action shadow-sm">
                <i class="fa-solid fa-ticket"></i> View &amp; Print Voucher
            </a>

            <a href="{{ route('booking.invoice', $booking->booking_reference) }}" target="_blank" class="action-btn" style="background:#0f172a; color:#fff; border:none;">
                <i class="fa-solid fa-file-invoice"></i> Tax Invoice (PDF)
            </a>

            @php
                $voucherUrl = route('booking.voucher', $booking->booking_reference);
                $waShareText = urlencode("🎉 *Prime Booking Reservation Confirmed!*" . "\n\n" .
                    "🏨 *Hotel:* " . ($booking->property?->name ?? 'Hotel') . "\n" .
                    "🔖 *Booking Ref:* #" . $booking->booking_reference . "\n" .
                    "👤 *Guest:* " . $booking->guest_name . "\n" .
                    "📅 *Check-in:* " . \Carbon\Carbon::parse($booking->check_in)->format('d M Y') . "\n" .
                    "📅 *Check-out:* " . \Carbon\Carbon::parse($booking->check_out)->format('d M Y') . "\n" .
                    "💳 *Total Fare:* " . CurrencyService::format($cTotal) . "\n\n" .
                    "📄 *Official E-Voucher:* " . $voucherUrl);
                
                $hPhone = preg_replace('/[^0-9]/', '', $booking->property?->contact_phone ?? '8801770887733');
                if (str_starts_with($hPhone, '01')) { $hPhone = '88' . $hPhone; }
            @endphp

            <a href="https://api.whatsapp.com/send?text={{ $waShareText }}" target="_blank" class="action-btn shadow-sm" style="background:#25D366; color:#fff; border:none;">
                <i class="fa-brands fa-whatsapp fs-5"></i> Share Voucher on WhatsApp
            </a>

            <a href="{{ route('hotels.show', $booking->property_id) }}" class="action-btn btn-outline-action">
                <i class="fa-solid fa-hotel"></i> View Property
            </a>

            <a href="{{ route('home') }}" class="action-btn" style="background:#f8fafc; color:#475569; border:2px solid #e2e8f0;">
                <i class="fa-solid fa-house"></i> Home
            </a>
        </div>

        {{-- Policy reminder --}}
        <div style="padding:16px 20px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; font-size:12px; color:#64748b; text-align:center;">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
            Free cancellation available up to 24 hours before check-in. Contact us at
            <a href="mailto:support@primebooking.com" class="text-primary">support@primebooking.com</a>
            or call <strong>+880 1800-PRIME</strong> for assistance.
        </div>

    </div>

    {{-- ── What's next section ── --}}
    <div style="max-width:720px; margin:24px auto 0; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
        <div style="background:#fff; border-radius:12px; padding:16px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <i class="fa-solid fa-envelope" style="font-size:24px; color:#2067e1; margin-bottom:8px;"></i>
            <div style="font-size:12px; font-weight:700; color:#0f172a;">Check Email</div>
            <div style="font-size:11px; color:#64748b;">Confirmation sent to your inbox</div>
        </div>
        <div style="background:#fff; border-radius:12px; padding:16px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <i class="fa-solid fa-ticket" style="font-size:24px; color:#16a34a; margin-bottom:8px;"></i>
            <div style="font-size:12px; font-weight:700; color:#0f172a;">Save Voucher</div>
            <div style="font-size:11px; color:#64748b;">Print or show at hotel check-in</div>
        </div>
        <div style="background:#fff; border-radius:12px; padding:16px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <i class="fa-solid fa-headset" style="font-size:24px; color:#f59e0b; margin-bottom:8px;"></i>
            <div style="font-size:12px; font-weight:700; color:#0f172a;">Need Help?</div>
            <div style="font-size:11px; color:#64748b;">24/7 customer support</div>
        </div>
    </div>

</div>
@endsection
