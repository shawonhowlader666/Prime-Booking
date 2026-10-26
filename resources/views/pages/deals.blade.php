@extends('layouts.main', ['activePage' => 'deals'])

@section('title', "Today's Promo Deals & Coupons | Prime Booking")

@section('content')
{{-- Hero Subheader --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 40px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 220px; height: 220px; background: rgba(59, 130, 246, 0.25); filter: blur(45px); border-radius: 50%; pointer-events: none;"></div>

    <div class="container" style="max-width: 1200px; position: relative; z-index: 2;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold" style="border-radius: 4px; font-size: 11px;">
                    <i class="fa-solid fa-bolt me-1"></i> VERIFIED SAVINGS
                </span>
                <h1 class="fw-bold mb-2 text-white" style="font-size: 28px; letter-spacing: -0.3px;">
                    Today's Deals, Flash Sales &amp; Promo Coupons
                </h1>
                <p class="mb-0 text-white-50" style="font-size: 14.5px; max-width: 620px;">
                    Grab exclusive limited-time hotel flash sales and collect voucher codes for instant checkout savings.
                </p>
            </div>

            <div class="d-none d-md-flex align-items-center gap-3">
                <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-3">
                    <span style="font-size: 32px;">🏷️</span>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">PROMO CODES</div>
                        <div style="font-size: 14px; font-weight: 800; color: #fff;">Up to 45% OFF</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-5" style="background-color: #f8fafc; min-height: 80vh;">
    <div class="container" style="max-width: 1200px;">
        
        {{-- Section: Limited-Time Flash Deals --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="font-size: 22px;">
                    <i class="fa-solid fa-fire text-danger me-2"></i> Limited-Time Flash Sales
                </h3>
                <p class="text-muted small mb-0">Deals expire when countdown ends or room allocations sell out.</p>
            </div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold" style="border-radius: 6px;">
                <i class="fa-solid fa-stopwatch me-1"></i> Live Flash Timers
            </span>
        </div>

        {{-- Deals Cards Grid --}}
        <div class="row g-4 mb-5">
            @forelse($deals as $deal)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm overflow-hidden bg-white h-100 position-relative" style="border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                    {{-- Card Header Ribbon --}}
                    <div class="p-3 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);">
                        <div>
                            @if($deal->badge_text)
                            <span class="badge bg-warning text-dark fw-bold mb-1" style="font-size: 10px;">{{ $deal->badge_text }}</span>
                            @endif
                            <h5 class="fw-bold mb-0 text-white" style="font-size: 16px;">{{ $deal->title }}</h5>
                        </div>
                        <div style="font-size: 26px;">⚡</div>
                    </div>

                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <p class="text-secondary small mb-3" style="line-height: 1.5;">{{ $deal->subtitle ?: 'Exclusive special promotion on Prime Booking properties.' }}</p>

                        {{-- Real-time Countdown Box --}}
                        @if($deal->valid_until)
                        <div class="p-2 px-3 rounded-3 mb-3 d-flex align-items-center justify-content-between border" style="background: #fff1f2; border-color: #fecdd3 !important;" data-countdown="{{ $deal->valid_until->format('Y-m-d H:i:s') }}">
                            <div class="d-flex align-items-center gap-2 text-danger small fw-bold">
                                <i class="fa-solid fa-clock fa-spin" style="--fa-animation-duration: 4s;"></i> Ends In:
                            </div>
                            <div class="countdown-display fw-bold font-monospace text-danger small">
                                Calculating...
                            </div>
                        </div>
                        @endif

                        {{-- Price and Action --}}
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                @if($deal->sale_price)
                                <div class="fw-bold text-success fs-5">৳{{ number_format($deal->sale_price) }}</div>
                                @if($deal->original_price)
                                <del class="text-muted small">৳{{ number_format($deal->original_price) }}</del>
                                <span class="badge bg-success-subtle text-success small ms-1">-{{ round((($deal->original_price - $deal->sale_price) / $deal->original_price) * 100) }}%</span>
                                @endif
                                @else
                                <span class="fw-bold text-primary fs-5">{{ $deal->discount_pct > 0 ? $deal->discount_pct.'% OFF' : 'Special Offer' }}</span>
                                @endif
                            </div>
                            <a href="{{ $deal->link_url ?: route('search.index') }}" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 6px;">
                                Book Deal <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No active flash sales right now. Check out coupons below!</p>
            </div>
            @endforelse
        </div>

        {{-- Section: Promo Coupons --}}
        @if(isset($coupons) && $coupons->count() > 0)
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 pt-4 border-top">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="font-size: 22px;">
                    <i class="fa-solid fa-ticket text-primary me-2"></i> Collect Promo Coupons
                </h3>
                <p class="text-muted small mb-0">Apply these voucher codes during hotel checkout to unlock direct discounts.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($coupons as $coupon)
            <div class="col-md-6 col-lg-4">
                <div class="card border border-2 shadow-sm p-3 position-relative overflow-hidden" style="border-radius: 12px; background: #fff; border-style: dashed !important; border-color: #cbd5e1 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" style="border-radius: 4px; font-size: 11px;">
                            {{ $coupon->type === 'percent' ? $coupon->amount.'% DISCOUNT' : '৳'.number_format($coupon->amount).' OFF' }}
                        </span>
                        @if($coupon->expires_at)
                        <small class="text-muted" style="font-size: 11px;">
                            Expires {{ $coupon->expires_at->format('d M Y') }}
                        </small>
                        @else
                        <small class="text-success fw-semibold" style="font-size: 11px;">No Expiry</small>
                        @endif
                    </div>

                    <div class="fw-bold text-dark fs-5 mb-1">{{ $coupon->name ?? 'Hotel Stay Discount' }}</div>
                    <small class="text-muted mb-3 d-block">
                        @if($coupon->min_spend > 0)
                        Min. booking amount: ৳{{ number_format($coupon->min_spend) }}
                        @else
                        Valid on all eligible hotel bookings
                        @endif
                    </small>

                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border bg-light">
                        <span class="font-monospace fw-bold text-primary fs-6 ps-2 coupon-code-text">{{ $coupon->code }}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold copy-coupon-btn" data-code="{{ $coupon->code }}" style="border-radius: 4px; font-size: 11.5px;">
                            <i class="fa-regular fa-copy me-1"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
// Real-time ticking countdown timers
function updateCountdowns() {
    const elements = document.querySelectorAll('[data-countdown]');
    const now = new Date().getTime();

    elements.forEach(el => {
        const targetStr = el.getAttribute('data-countdown');
        const targetTime = new Date(targetStr.replace(/-/g, '/')).getTime();
        const diff = targetTime - now;

        const display = el.querySelector('.countdown-display');

        if (diff <= 0) {
            display.innerHTML = '<span class="text-secondary">Expired</span>';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        if (days > 0) {
            display.innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        } else {
            display.innerText = `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        }
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();

// Copy Coupon Code button
document.querySelectorAll('.copy-coupon-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const code = this.getAttribute('data-code');
        navigator.clipboard.writeText(code).then(() => {
            const original = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-check text-success me-1"></i> Copied!';
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-success');
            setTimeout(() => {
                this.innerHTML = original;
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-primary');
            }, 1500);
        });
    });
});
</script>
@endpush
@endsection
