{{-- Agoda-Exact Trip Savings & Connected Trip Outer Card Banner --}}
@props(['destLabel' => 'Bangladesh'])

<div id="smartPromoBannerRoot" class="mb-4" style="max-width: 790px;">
    {{-- Outer Card --}}
    <div style="background: #ffffff; border: 1px solid #dcdcdc; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
        
        {{-- Top Green Banner (Exact Agoda Soft Fade Gradient with Phone Illustration on Right) --}}
        <div class="position-relative overflow-hidden" style="background: radial-gradient(ellipse 260px 130px at 92% 50%, #c4efcd 0%, #daf4e2 50%, transparent 80%), linear-gradient(180deg, #c4e7ca 0%, #d8efdc 40%, #edf7ee 78%, #ffffff 100%); min-height: 94px; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2ece5;">
            <div class="position-relative" style="z-index: 2; max-width: 540px;">
                <h4 class="fw-bold mb-1" style="color: #1e293b; font-size: 20px; letter-spacing: -0.3px; line-height: 1.25; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    {{ auth()->check() ? auth()->user()->name . '! ' : 'PRIME! ' }}You've unlocked Trip Savings deals!
                </h4>
                <p class="mb-0 fw-semibold" style="color: #007a3d; font-size: 14px;">
                    Save more with Trip Savings
                </p>
            </div>

            {{-- Agoda Phone + Trip Savings Pill Illustration --}}
            <div class="d-none d-sm-flex align-items-center position-absolute" style="right: 18px; top: 50%; transform: translateY(-50%); z-index: 1;">
                <img src="{{ asset('images/connected-trip/trip-savings-phone.png') }}" alt="Trip Savings" style="height: 74px; object-fit: contain;">
            </div>
            
            <button type="button" class="btn btn-link p-0 text-secondary text-decoration-none position-absolute" onclick="dismissSmartBanner()" title="Dismiss" style="font-size: 16px; top: 10px; right: 12px; z-index: 3;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Section: Need more hotels for your trip? --}}
        <div style="padding: 16px 20px 20px 20px;">
            <h5 class="fw-bold mb-2.5" style="color: #1e293b; font-size: 17px; letter-spacing: -0.2px;">Need more hotels for your trip?</h5>
            
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px;">
                <div>
                    {{-- Badges Row --}}
                    <div class="d-flex align-items-center gap-2 mb-1.5">
                        <img src="{{ asset('images/connected-trip/hotel-icon.png') }}" alt="Hotel" style="width: 20px; height: 20px; object-fit: contain;">
                        <span style="background: #e6f7ec; color: #007a3d; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-star" style="font-size: 9px;"></i> Trip Savings
                        </span>
                        <span style="background: #007a3d; color: #ffffff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                            Up to 5% off
                        </span>
                    </div>

                    {{-- Title & Subtitle --}}
                    <div class="fw-bold text-dark" style="font-size: 15px; margin-bottom: 2px;">Book another place to stay</div>
                    <div class="text-secondary small" style="font-size: 12.5px;">We have unlocked the best deals with Trip Savings</div>
                    
                    {{-- Date line with external link icon --}}
                    <div class="small fw-semibold d-flex align-items-center gap-1.5 mt-1" style="font-size: 12px; color: #4b5563;">
                        <span>{{ request('check_in') ? date('j M', strtotime(request('check_in'))) : date('j M') }} - {{ request('check_out') ? date('j M', strtotime(request('check_out'))) : date('j M', strtotime('+1 day')) }} &bull; {{ request('guests', 2) }} Adults</span>
                        <a href="{{ route('search.index', ['destination' => $destLabel]) }}" style="color: #2067e1; text-decoration: none;">
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <button type="button" class="btn btn-sm text-white fw-bold rounded-pill px-3 py-1.5 shadow-xs" onclick="copyPromoCode(this, 'PRIME10')" style="background: #059669; font-size: 12.5px; white-space: nowrap; border: none;">
                        <i class="fa-solid fa-copy me-1"></i> Copy Code PRIME10
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dismissSmartBanner() {
        const root = document.getElementById('smartPromoBannerRoot');
        if (root) {
            root.style.display = 'none';
        }
    }

    function copyPromoCode(btn, code) {
        navigator.clipboard.writeText(code).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
            btn.style.background = '#15803d';
            try { localStorage.setItem('applied_promo_code', code); } catch(e){}
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.style.background = '#059669';
            }, 2500);
        });
    }
</script>
