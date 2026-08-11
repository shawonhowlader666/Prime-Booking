@extends('layouts.main', ['activePage' => 'deals'])

@section('title', "Today's Promo Deals & Coupons | Prime Booking")

@section('content')
{{-- Hero Subheader with Dark Gradient & Deals Graphic --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-tags text-warning me-2" style="font-size: 20px;"></i> {{ __('Today\'s Deals & Exclusive Coupons') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Collect promo codes, hotel vouchers, and limited-time discount coupons for instant checkout savings.') }}
            </p>
        </div>

        <!-- Right Side Deals Graphic -->
        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🏷️</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Promo Codes</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Up to 50% OFF</div>
                </div>
            </div>
            <div style="font-size: 40px; transform: rotate(10deg); filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                🎁
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="background-color: #f4f6fa; min-height: 80vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <!-- Active Coupon & Deals Cards Grid -->
        <div class="row g-4">
            @forelse($deals as $deal)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100" style="border-radius: 16px !important;">
                    <div class="p-3 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #2067e1 0%, #1d4ed8 100%);">
                        <div>
                            @if($deal->badge_text)
                            <span class="badge bg-warning text-dark fw-bold mb-1" style="font-size: 10px;">{{ $deal->badge_text }}</span>
                            @endif
                            <h5 class="fw-bold mb-0 text-white" style="font-size: 18px;">{{ $deal->title }}</h5>
                        </div>
                        <div style="font-size: 32px;">🏷️</div>
                    </div>
                    <div class="p-3.5">
                        <p class="text-secondary small mb-3">{{ $deal->subtitle ?: 'Special limited time deal on Prime Booking.' }}</p>
                        @if($deal->valid_until)
                        <div class="small text-muted mb-2"><i class="fa-solid fa-clock text-warning me-1"></i> Valid until {{ $deal->valid_until->format('M j, Y') }}</div>
                        @endif
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div>
                                @if($deal->sale_price)
                                <span class="fw-bold text-success fs-5">৳{{ number_format($deal->sale_price) }}</span>
                                @if($deal->original_price)
                                <del class="text-muted small ms-1">৳{{ number_format($deal->original_price) }}</del>
                                @endif
                                @else
                                <span class="fw-bold text-primary">{{ $deal->discount_pct > 0 ? $deal->discount_pct.'% OFF' : 'Special Offer' }}</span>
                                @endif
                            </div>
                            <a href="{{ $deal->link_url ?: route('search.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">Claim Deal</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No active deals right now. Check back soon!</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
