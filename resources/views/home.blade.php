@extends('layouts.main', ['activePage' => 'home'])

@section('title', 'Hotel Booking Bangladesh | Best Hotels & 5-Star Resorts 2026 | Prime Booking')
@section('meta_description', 'Online hotel booking in Bangladesh. Find & book 2,000+ hotels, beach resorts & luxury stays in Cox\'s Bazar, Sajek, Sylhet & Dhaka from ৳1,500. Instant confirmation, bKash cashback & free cancellation.')
@section('meta_keywords', 'hotel booking, hotel booking bangladesh, online hotel booking bd, hotels in coxs bazar, coxs bazar hotel booking, cheap hotels coxs bazar, sajek valley resort, sylhet hotel booking, dhaka 5 star hotel, sundarban cruise booking, tanguar haor houseboat, kuakata hotel booking, হোটেল বুকিং বাংলাদেশ, কক্সবাজার হোটেল বুকিং, সাজেক রিসোর্ট, প্রাইম বুকিং')
@section('canonical_url', 'https://primebooking.com.bd')
@section('og_title', 'Hotel Booking Bangladesh | Best Hotels & 5-Star Resorts 2026 | Prime Booking')
@section('og_description', 'Book top hotels and 5-star beach resorts in Cox\'s Bazar, Sajek, Sylhet & Dhaka with instant confirmation and bKash discount.')
@section('og_image', asset('android-chrome-512x512.png'))

@section('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Prime Booking",
  "alternateName": ["Prime Booking Bangladesh", "প্রাইম বুকিং বাংলাদেশ", "হোটেল বুকিং বিডি"],
  "url": "https://primebooking.com.bd",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://primebooking.com.bd/search?destination={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION — slides from DB destinations (or static bg)   --}}
{{-- ============================================================ --}}
<style>
    #bdHeroSliderSection {
        position: relative;
        z-index: 50;
        height: 380px;
        margin-bottom: 135px;
        border-radius: 0 0 0 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        padding-top: 42px;
    }
    @media (max-width: 768px) {
        #bdHeroSliderSection {
            height: auto !important;
            min-height: 430px !important;
            margin-bottom: 100px !important;
            padding-top: 24px !important;
            border-radius: 0 0 0 20px !important;
        }
        #bdHeroTitle {
            font-size: 19px !important;
            line-height: 1.3 !important;
            padding: 0 8px !important;
        }
        .hero-search-wrapper {
            margin-top: 12px !important;
            margin-bottom: -80px !important;
        }
    }
</style>

<section id="bdHeroSliderSection">

    <div style="position:absolute;inset:0;border-radius:inherit;overflow:hidden;z-index:0;">
        {{-- Dynamic hero slides from DB destinations or fallback to static images --}}
        @php
            $destList = collect($destinations ?? []);
            $heroSlides = $destList->filter(fn($d) => !empty($d->image_url))->take(8);
            $heroTitles = $heroSlides->map(fn($d) => strtoupper($d->city . ($d->country && $d->country !== 'Bangladesh' ? ', '.$d->country : '') . ' — BEST HOTELS & STAYS'));

            // Fallback static slides if no destinations in DB yet
            $staticSlides = [
                ['img' => asset('images/bd_hero_slide1.png'), 'title' => 'HOTELS, RESORTS & HOMES — BEST PRICE GUARANTEED'],
                ['img' => asset('images/bd_hero_slide2.png'), 'title' => 'DISCOVER SYLHET TEA GARDENS & RESORTS'],
                ['img' => asset('images/bd_hero_slide3.png'), 'title' => 'SAJEK VALLEY MOUNTAIN & ECO RESORTS'],
                ['img' => asset('images/bd_hero_slide4.png'), 'title' => 'LUXURY RESORT STAYS NEAR YOU'],
                ['img' => asset('images/bd_hero_slide5.png'), 'title' => 'ISLAND ESCAPES & BEACH HOTELS'],
                ['img' => asset('images/bd_hero_slide6.png'), 'title' => 'SUNSET BEACH HOTELS & RESORTS'],
                ['img' => asset('images/bd_hero_slide7.png'), 'title' => 'WILDLIFE & ECO CRUISES'],
                ['img' => asset('images/bd_hero_slide8.png'), 'title' => 'CRYSTAL RIVER & NATURE STAYS'],
            ];
        @endphp

        @if($heroSlides->isNotEmpty())
            @foreach($heroSlides as $i => $dest)
            <div class="bd-hero-slide" style="position:absolute;inset:0;
                 background:linear-gradient(180deg,rgba(15,23,42,.45) 0%,rgba(30,41,59,.82) 100%),
                             url('{{ $dest->image_url }}') center/cover no-repeat;
                 transition:opacity 1.2s ease-in-out;opacity:{{ $i === 0 ? '1' : '0' }};"></div>
            @endforeach
        @else
            @foreach($staticSlides as $i => $s)
            <div class="bd-hero-slide" style="position:absolute;inset:0;
                 background:linear-gradient(180deg,rgba(15,23,42,.45) 0%,rgba(30,41,59,.82) 100%),
                             url('{{ $s['img'] }}') center/cover no-repeat;
                 transition:opacity 1.2s ease-in-out;opacity:{{ $i === 0 ? '1' : '0' }};"></div>
            @endforeach
        @endif
    </div>

    <div class="container text-center" style="position:relative;z-index:50;">
        <h1 id="bdHeroTitle" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:800;font-size:28px;letter-spacing:0.5px;color:#ffffff!important;text-transform:uppercase;margin-bottom:14px;text-shadow:0 2px 12px rgba(0,0,0,0.85);transition:opacity 0.5s ease;">
            {{ $siteSettings['site_name'] ?? 'Prime Booking' }} — Hotels, Resorts & Homes
        </h1>
        <div class="hero-search-wrapper" style="position:relative; z-index:100; margin-top:10px; margin-bottom:-110px;">
            @include('components.search.search-bar')
        </div>
    </div>

</section>

{{-- ============================================================ --}}
{{-- TRIP SAVINGS & JOURNEY PLANNING SECTION (Agoda 1:1 Parity)   --}}
{{-- ============================================================ --}}
<style>
    .agoda-exact-container {
        max-width: 1180px;
        width: 100%;
        margin: 0 auto;
        padding: 0 20px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    .agoda-connected-outer-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        margin-bottom: 28px;
    }
    .agoda-exact-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px 28px;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .agoda-exact-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }
    .agoda-btn-outline-search {
        display: inline-block;
        border: 1px solid #2067e1;
        color: #2067e1;
        background: #ffffff;
        border-radius: 24px;
        padding: 7px 30px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        line-height: 1.4;
        transition: all 0.15s ease;
    }
    .agoda-btn-outline-search:hover {
        background: #f0f7ff;
        color: #1a56be;
        border-color: #1a56be;
    }

    /* ─── Exact Identical Agoda Continue Planning 2 Cards ─── */
    .agoda-continue-trip-card {
        display: inline-flex;
        align-items: stretch;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 16px;
        overflow: hidden;
        width: 270px;
        min-width: 270px;
        max-width: 270px;
        height: 76px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
        text-decoration: none !important;
        flex-shrink: 0;
    }
    .agoda-continue-trip-card:hover {
        border-color: #9ca3af;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    }
    .agoda-continue-trip-icon-block {
        width: 52px;
        min-width: 52px;
        max-width: 52px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-sizing: border-box;
    }
    .agoda-continue-trip-content {
        padding: 9px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
        min-width: 0;
        flex: 1;
        box-sizing: border-box;
        background: #ffffff;
    }
    .agoda-continue-trip-title {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
        line-height: 1.25;
        margin-bottom: 2px;
        word-break: break-word;
        letter-spacing: -0.1px;
    }
    .agoda-continue-trip-subtitle {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 12.5px;
        color: #6b7280;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
        line-height: 1.2;
    }
</style>

<div class="py-3" style="background: #ffffff; border-bottom: 1px solid #f1f5f9; margin-top: 28px; position: relative; z-index: 1;">
    <div class="agoda-exact-container">
        
        {{-- Continue planning your trip (Exact Agoda Screenshot 1:1 Parity) --}}
        <div id="agodaContinuePlanningSection" style="margin-bottom: 24px; text-align: left;">
            <h5 class="fw-bold mb-2.5" style="color: #111827; font-size: 24px; letter-spacing: -0.3px; line-height: 1.25;">
                Continue planning your trip
            </h5>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                {{-- Card 1: Stays in City (Identical Dimensions, Radius, Padding & Border) --}}
                <a href="{{ route('search.index') }}?destination=Cox%27s+Bazar" id="cpStaysLink" class="agoda-continue-trip-card">
                    <div class="agoda-continue-trip-icon-block" style="background: #fee9e5;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="3.5" y="4.5" width="9" height="15" rx="1" fill="#c53829"/>
                            <rect x="5.5" y="7" width="2" height="2" fill="#fee9e5"/>
                            <rect x="8.5" y="7" width="2" height="2" fill="#fee9e5"/>
                            <rect x="5.5" y="10.5" width="2" height="2" fill="#fee9e5"/>
                            <rect x="8.5" y="10.5" width="2" height="2" fill="#fee9e5"/>
                            <circle cx="15.5" cy="15.5" r="4" stroke="#c53829" stroke-width="2" fill="#fee9e5"/>
                            <path d="M18.5 18.5L21.5 21.5" stroke="#c53829" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="agoda-continue-trip-content">
                        <div id="cpStaysTitle" class="agoda-continue-trip-title">
                            Stays in Cox's Bazar
                        </div>
                        <div class="agoda-continue-trip-subtitle">
                            <span id="cpStaysDates">Oct 22 - Oct 23</span>
                            <span style="color: #9ca3af; margin: 0 1px;">&bull;</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#6b7280" style="margin-top: -1px; flex-shrink: 0;">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                            <span id="cpStaysGuests">2</span>
                        </div>
                    </div>
                </a>

                {{-- Card 2: Find things to do in City (Identical Dimensions, Radius, Padding & Border) --}}
                <a href="{{ route('search.index') }}?destination=Cox%27s+Bazar" id="cpThingsLink" class="agoda-continue-trip-card">
                    <div class="agoda-continue-trip-icon-block" style="background: #f3efd8;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="8" cy="8.5" r="5" stroke="#856404" stroke-width="1.8"/>
                            <path d="M8 5v7M4.5 8.5h7" stroke="#856404" stroke-width="1.5"/>
                            <path d="M4 14l4 4" stroke="#856404" stroke-width="1.8"/>
                            <circle cx="15.5" cy="15.5" r="4" stroke="#856404" stroke-width="2" fill="#f3efd8"/>
                            <path d="M18.5 18.5L21.5 21.5" stroke="#856404" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="agoda-continue-trip-content">
                        <div id="cpThingsTitle" class="agoda-continue-trip-title">
                            Find things to do in Cox's Bazar
                        </div>
                        <div class="agoda-continue-trip-subtitle">
                            <span id="cpThingsDates">Oct 22</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const stored = JSON.parse(localStorage.getItem('pb_recent_searches_v2') || '[]');
                if (stored && stored.length > 0) {
                    const last = stored[0];
                    const rawCity = last.destination || "Cox's Bazar";
                    const city = rawCity.split(',')[0].trim();
                    const titleEl = document.getElementById('cpStaysTitle');
                    const datesEl = document.getElementById('cpStaysDates');
                    const guestsEl = document.getElementById('cpStaysGuests');
                    const staysLink = document.getElementById('cpStaysLink');
                    const thingsTitle = document.getElementById('cpThingsTitle');
                    const thingsDates = document.getElementById('cpThingsDates');
                    const thingsLink = document.getElementById('cpThingsLink');

                    if (titleEl && city) titleEl.textContent = 'Stays in ' + city;
                    if (thingsTitle && city) thingsTitle.textContent = 'Find things to do in ' + city;

                    if (last.checkIn && last.checkOut) {
                        const shortMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        const dIn = new Date(last.checkIn);
                        const dOut = new Date(last.checkOut);
                        if (!isNaN(dIn.getTime()) && !isNaN(dOut.getTime())) {
                            const strIn = shortMonth[dIn.getMonth()] + ' ' + dIn.getDate();
                            const strOut = shortMonth[dOut.getMonth()] + ' ' + dOut.getDate();
                            if (datesEl) datesEl.textContent = strIn + ' - ' + strOut;
                            if (thingsDates) thingsDates.textContent = strIn;
                        }
                    }
                    if (last.guests && guestsEl) {
                        guestsEl.textContent = last.guests;
                    }
                    if (staysLink) {
                        staysLink.href = "{{ route('search.index') }}?destination=" + encodeURIComponent(rawCity) + (last.checkIn ? '&check_in=' + last.checkIn : '') + (last.checkOut ? '&check_out=' + last.checkOut : '') + (last.guests ? '&guests=' + last.guests : '');
                    }
                    if (thingsLink) {
                        thingsLink.href = "{{ route('search.index') }}?destination=" + encodeURIComponent(rawCity);
                    }
                }
            } catch(e) {}
        });
        </script>
        
        {{-- Unified Agoda Connected Trip Outer Card Container --}}
        <div class="agoda-connected-outer-card">
            
            {{-- Top Green Banner (Exact Agoda Soft Fade Gradient & Light Mist Blending) --}}
            <div class="position-relative overflow-hidden" style="background: radial-gradient(ellipse 280px 140px at 90% 50%, #c4efcd 0%, #daf4e2 50%, transparent 80%), linear-gradient(180deg, #c4e7ca 0%, #d8efdc 40%, #edf7ee 78%, #ffffff 100%); min-height: 98px; padding: 24px 32px; display: flex; align-items: center; justify-content: space-between; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div class="position-relative" style="z-index: 2; max-width: 680px;">
                    <h4 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; letter-spacing: -0.3px; line-height: 1.25;">
                        {{ auth()->check() ? auth()->user()->name . '! ' : 'PRIME! ' }}You've unlocked Trip Savings deals!
                    </h4>
                    <p class="mb-0 fw-semibold" style="color: #007a3d; font-size: 15px;">
                        Save more with Trip Savings
                    </p>
                </div>

                {{-- Agoda Phone + Trip Savings Pill Illustration --}}
                <div class="d-none d-md-flex align-items-center position-absolute" style="right: 28px; top: 50%; transform: translateY(-50%); z-index: 1;">
                    <img src="{{ asset('images/connected-trip/trip-savings-phone.png') }}" alt="Trip Savings" style="height: 84px; object-fit: contain;">
                </div>
            </div>

            {{-- Main Container Body --}}
            <div style="padding: 24px 32px 32px 32px;">
                
                {{-- Section 1: Need more hotels for your trip? (Exact Agoda cross-sell carousel) --}}
                <div class="mb-4" data-component="x-sell-need-more-hotels-for-your-trip">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 20px; letter-spacing: -0.2px;">Need more hotels for your trip?</h5>
                            <span style="background: #e6f7ec; color: #007a3d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Up to 5% off</span>
                        </div>
                        <a href="{{ route('search.index') }}" class="small text-decoration-none fw-semibold" style="color: #2067e1;">View all <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></a>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                        <span>{{ request('destination', 'Cox\'s Bazar') }} &bull; {{ date('d M', strtotime('+1 day')) }} - {{ date('d M', strtotime('+2 days')) }} &bull; 2 Adults</span>
                        <a href="{{ route('search.index') }}" style="color: #2067e1;"><i class="fa-solid fa-pencil" style="font-size: 11px;"></i></a>
                    </div>

                    @if(isset($featuredProperties) && $featuredProperties->isNotEmpty())
                    <div class="position-relative">
                        <div class="d-flex gap-3 overflow-auto pb-2" style="scrollbar-width: thin; scroll-snap-type: x mandatory;">
                            @foreach($featuredProperties as $prop)
                                @php
                                    $rawImg = $prop->primary_image ?: ($prop->image_url ?: null);
                                    if (!$rawImg && !empty($prop->images)) {
                                        $parsedImgs = is_string($prop->images) ? json_decode($prop->images, true) : $prop->images;
                                        $rawImg = $parsedImgs[0] ?? null;
                                    }
                                    $cardImg = function_exists('media_url') ? media_url($rawImg) : ($rawImg ? (str_starts_with($rawImg, 'http') ? $rawImg : asset('storage/' . ltrim($rawImg, '/'))) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');

                                    $price = (float)($prop->price_per_night ?? $prop->price ?? 3500);
                                    $score = (float)($prop->rating_score ?? 8.2);
                                    $ratingLabel = match(true) {
                                        $score >= 9.0 => 'Exceptional',
                                        $score >= 8.5 => 'Superb',
                                        $score >= 8.0 => 'Excellent',
                                        $score >= 7.5 => 'Very good',
                                        default       => 'Good',
                                    };
                                    $revCount = (int)($prop->total_reviews ?? $prop->reviews_count ?? 120);
                                @endphp
                                <a href="{{ route('hotels.show', $prop->id) }}" class="text-decoration-none text-dark flex-shrink-0" style="width: 176px; scroll-snap-align: start;">
                                    <div class="card border rounded-4 overflow-hidden h-100 shadow-none" style="border-color: #e2e8f0; transition: transform 0.15s, box-shadow 0.15s; background: #fff;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='';this.style.boxShadow='none';">
                                        <div class="position-relative" style="height: 110px; width: 100%; background: #f1f5f9;">
                                            <img src="{{ $cardImg }}" alt="{{ $prop->name }}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                            @if($loop->iteration % 2 === 0)
                                                <div class="position-absolute" style="top: 6px; left: 6px; background: #d93025; color: #fff; font-size: 9.5px; font-weight: 700; padding: 2px 6px; border-radius: 3px;">
                                                    <i class="fa-solid fa-bolt me-1"></i>Booked {{ $loop->iteration + 2 }} times today
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-2 d-flex flex-column justify-content-between" style="min-height: 130px;">
                                            <div>
                                                <div class="fw-bold text-truncate" style="font-size: 13.5px; color: #2a2a2e;" title="{{ $prop->name }}">{{ $prop->name }}</div>
                                                <div class="d-flex align-items-center gap-1 my-1">
                                                    @for($s = 0; $s < min(5, max(1, (int)($prop->star_rating ?? 4))); $s++)
                                                        <i class="fa-solid fa-star" style="color: #ff9900; font-size: 10px;"></i>
                                                    @endfor
                                                </div>
                                                <div class="small text-muted text-truncate" style="font-size: 11.5px;">
                                                    <i class="fa-solid fa-location-dot me-1"></i>{{ $prop->city ?? 'Cox\'s Bazar' }}
                                                </div>
                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                    <span class="fw-bold" style="font-size: 11px; color: #2067e1;">{{ number_format($score, 1) }}</span>
                                                    <span class="text-muted" style="font-size: 10px;">{{ $ratingLabel }}</span>
                                                    <span class="text-muted" style="font-size: 9.5px;">({{ number_format($revCount) }})</span>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2 pt-1 border-top">
                                                <div class="fw-bold" style="font-size: 14.5px; color: #e12d2d; line-height: 1.1;">
                                                    BDT {{ number_format($price) }}
                                                </div>
                                                <span class="text-muted d-block" style="font-size: 9.5px;">Per night before taxes</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="agoda-exact-card">
                        <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                            <div>
                                <div style="margin-bottom: 12px; line-height: 1;">
                                    <img src="{{ asset('images/connected-trip/hotel-icon.png') }}" alt="Hotel" style="width: 34px; height: 35px; object-fit: contain;">
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span style="background: #e6f7ec; color: #007a3d; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#007a3d"><path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.6-6.3 4.6 2.3-7.2-6-4.6h7.6z"/></svg>
                                        Trip Savings
                                    </span>
                                    <span style="background: #007a3d; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">Up to 5% off</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="color: #111827; font-size: 16.5px;">Book another place to stay</h6>
                                <p class="small mb-2.5" style="color: #6b7280; font-size: 14px;">We have unlocked the best deals with Trip Savings</p>
                                <div class="small fw-semibold d-flex align-items-center gap-1.5" style="font-size: 13.5px; color: #4b5563;">
                                    <span>23 Oct - 24 Oct &bull; 2 Adults</span>
                                    <a href="{{ route('search.index') }}" style="color: #2067e1; text-decoration: none;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i></a>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('search.index') }}" class="agoda-btn-outline-search">Search</a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Section 2: Plan your journey to your hotel --}}
                <div>
                    <h5 class="fw-bold mb-1" style="color: #1e293b; font-size: 22px; letter-spacing: -0.2px;">Plan your journey to your hotel</h5>
                    <p class="small mb-3" style="color: #6b7280; font-size: 14px;">Book your ride in advance for a hassle-free trip</p>

                    <div class="row g-3">
                        {{-- Card A: Airport Transfer --}}
                        <div class="col-md-6">
                            <div class="agoda-exact-card h-100 d-flex flex-column justify-content-between" style="padding: 22px 26px;">
                                <div>
                                    <div style="height: 52px; display: flex; align-items: flex-end; margin-bottom: 12px;">
                                        <img src="{{ asset('images/connected-trip/airport-transfer-car.png') }}" alt="Airport Transfer" style="height: 48px; object-fit: contain;">
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: #111827; font-size: 16.5px;">Book your airport transfer</h6>
                                    <p class="small mb-3" style="color: #6b7280; font-size: 14px; line-height: 1.35;">Get to your hotel easily and securely</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1">
                                    <div class="small fw-semibold" style="font-size: 13.5px; color: #4b5563;">
                                        <span>22 Oct &bull; 2 Adults</span>
                                        <a href="{{ route('transfers.index') }}" style="color: #2067e1; text-decoration: none; margin-left: 3px;">
                                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                                        </a>
                                    </div>
                                    <a href="{{ route('transfers.index') }}" class="agoda-btn-outline-search" style="padding: 6px 26px; font-size: 14px;">
                                        Search
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Card B: Rent a Car --}}
                        <div class="col-md-6">
                            <div class="agoda-exact-card h-100 d-flex flex-column justify-content-between" style="padding: 22px 26px;">
                                <div>
                                    <div style="height: 52px; display: flex; align-items: flex-end; margin-bottom: 12px;">
                                        <img src="{{ asset('images/connected-trip/rent-a-car.png') }}" alt="Rent a Car" style="height: 46px; object-fit: contain;">
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: #111827; font-size: 16.5px;">Rent a car</h6>
                                    <p class="small mb-3" style="color: #6b7280; font-size: 14px; line-height: 1.35;">Find an ideal ride for your trip</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1">
                                    <div class="small fw-semibold" style="font-size: 13.5px; color: #4b5563;">
                                        <span>22 Oct - 23 Oct</span>
                                        <a href="{{ route('transfers.index') }}" style="color: #2067e1; text-decoration: none; margin-left: 3px;">
                                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                                        </a>
                                    </div>
                                    <a href="{{ route('transfers.index') }}" class="agoda-btn-outline-search" style="padding: 6px 26px; font-size: 13.5px;">
                                        Search
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- FLIGHTS & ACTIVITIES PROMOTIONS (Agoda 1:1 Parity)          --}}
        {{-- ============================================================ --}}
        <div class="mb-4" style="text-align: left;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: #2a2a2e; font-size: 24px; letter-spacing: -0.3px;">
                    Flights &amp; Activities Promotions
                </h5>
                <a href="{{ route('transfers.index') }}" class="text-decoration-none" style="color: #5392f9; font-size: 14.5px; font-weight: 600;">
                    View all <i class="fa-solid fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div style="position: relative;">
                <div id="agodaFlightPromoCarousel" style="display: flex; gap: 16px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 4px;">
                    
                    {{-- Card 1: Ready, Set, Go! (Agoda 1:1 Worldwide) --}}
                    <a href="{{ route('transfers.index') }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        {{-- Pink flight badge top-left --}}
                        <div style="position: absolute; top: 12px; left: 12px; z-index: 5; background: #e02474; color: #ffffff; width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(224, 36, 116, 0.4);">
                            <i class="fa-solid fa-plane" style="transform: rotate(-45deg); font-size: 12px;"></i>
                        </div>

                        <div style="display: flex; height: 100%; width: 100%;">
                            {{-- Left side: Sky with airplane and world landmarks --}}
                            <div style="width: 44%; height: 100%; position: relative; background: #cce7ff; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80" alt="Worldwide Flights" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(1.05);">
                                <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(255,255,255,0) 60%, rgba(124, 58, 237, 0.4) 100%);"></div>
                            </div>

                            {{-- Right side: Purple gradient with promotional text --}}
                            <div style="width: 56%; height: 100%; background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%); padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; text-align: left;">
                                <div>
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.22); color: #ffffff; border-radius: 12px; padding: 2px 8px; font-size: 10px; font-weight: 600; margin-bottom: 5px;">
                                        <i class="fa-solid fa-globe" style="font-size: 9px;"></i> Worldwide
                                    </div>
                                    <div style="color: #ffffff; font-size: 15px; font-weight: 800; line-height: 1.2; margin-bottom: 3px;">
                                        Ready, Set, Go!
                                    </div>
                                    <div style="color: #ffffff; font-size: 13px; font-weight: 700; line-height: 1.2;">
                                        Up to <span style="font-size: 17px; font-weight: 900; color: #fde047;">7%</span> off
                                    </div>
                                    <div style="color: rgba(255,255,255,0.9); font-size: 10.5px; font-weight: 500; margin-top: 2px;">
                                        Prices That'll Surprise You!
                                    </div>
                                </div>
                                <div style="color: rgba(255,255,255,0.55); font-size: 8.5px;">
                                    T&amp;Cs apply
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Card 2: Fly to Cox's Bazar --}}
                    <a href="{{ route('search.index') }}?destination=Cox%27s+Bazar" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        <div style="position: absolute; top: 12px; left: 12px; z-index: 5; background: #0284c7; color: #ffffff; width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.4);">
                            <i class="fa-solid fa-plane-departure" style="font-size: 12px;"></i>
                        </div>
                        <div style="display: flex; height: 100%; width: 100%;">
                            <div style="width: 44%; height: 100%; position: relative; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=80" alt="Cox's Bazar" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="width: 56%; height: 100%; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; text-align: left;">
                                <div>
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.22); color: #ffffff; border-radius: 12px; padding: 2px 8px; font-size: 10px; font-weight: 600; margin-bottom: 5px;">
                                        <i class="fa-solid fa-tag" style="font-size: 9px;"></i> Daily Flights
                                    </div>
                                    <div style="color: #ffffff; font-size: 15px; font-weight: 800; line-height: 1.2; margin-bottom: 3px;">
                                        Dhaka &rarr; Cox's Bazar
                                    </div>
                                    <div style="color: #ffffff; font-size: 13px; font-weight: 700;">
                                        From <span style="font-size: 16px; font-weight: 900; color: #fde047;">৳ 3,499</span>
                                    </div>
                                    <div style="color: rgba(255,255,255,0.9); font-size: 10.5px; font-weight: 500; margin-top: 2px;">
                                        Instant Confirmation
                                    </div>
                                </div>
                                <div style="color: rgba(255,255,255,0.55); font-size: 8.5px;">
                                    T&amp;Cs apply
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Card 3: Adventure BD --}}
                    <a href="{{ route('packages') }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        <div style="position: absolute; top: 12px; left: 12px; z-index: 5; background: #059669; color: #ffffff; width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.4);">
                            <i class="fa-solid fa-compass" style="font-size: 12px;"></i>
                        </div>
                        <div style="display: flex; height: 100%; width: 100%;">
                            <div style="width: 44%; height: 100%; position: relative; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?auto=format&fit=crop&w=400&q=80" alt="Activities" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="width: 56%; height: 100%; background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; text-align: left;">
                                <div>
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.22); color: #ffffff; border-radius: 12px; padding: 2px 8px; font-size: 10px; font-weight: 600; margin-bottom: 5px;">
                                        <i class="fa-solid fa-mountain" style="font-size: 9px;"></i> Activities
                                    </div>
                                    <div style="color: #ffffff; font-size: 15px; font-weight: 800; line-height: 1.2; margin-bottom: 3px;">
                                        Sajek &amp; Sylhet Tours
                                    </div>
                                    <div style="color: #ffffff; font-size: 13px; font-weight: 700;">
                                        Save up to <span style="font-size: 16px; font-weight: 900; color: #fde047;">15%</span>
                                    </div>
                                    <div style="color: rgba(255,255,255,0.9); font-size: 10.5px; font-weight: 500; margin-top: 2px;">
                                        Guided Scenic Trips
                                    </div>
                                </div>
                                <div style="color: rgba(255,255,255,0.55); font-size: 8.5px;">
                                    T&amp;Cs apply
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PROPERTIES WE THINK YOU'LL LIKE (Agoda 1:1 Parity)          --}}
        {{-- ============================================================ --}}
        <div class="mb-4" style="text-align: left;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: #2a2a2e; font-size: 24px; letter-spacing: -0.3px;">
                    Properties we think you'll like
                </h5>
                <a href="{{ route('search.index') }}" class="text-decoration-none" style="color: #5392f9; font-size: 14.5px; font-weight: 600;">
                    View all <i class="fa-solid fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div style="position: relative;">
                <div id="agodaPropertiesPromoCarousel" style="display: flex; gap: 16px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 4px;">
                    
                    {{-- Card 1: BWH Hotels (Agoda 1:1 Parity) --}}
                    <a href="{{ route('search.index') }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        {{-- Top-left Flagship Store badge --}}
                        <div style="position: absolute; top: 10px; left: 10px; z-index: 5; background: #fef08a; color: #854d0e; padding: 2.5px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">
                            <i class="fa-solid fa-store" style="font-size: 9px;"></i> Flagship Store
                        </div>

                        <div style="display: flex; height: 100%; width: 100%;">
                            {{-- Left side: Sunny beach with umbrellas and sea --}}
                            <div style="width: 44%; height: 100%; position: relative; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=80" alt="BWH Hotels Beach" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>

                            {{-- Right side: Charcoal/navy with BWH logo & text --}}
                            <div style="width: 56%; height: 100%; background: #242c38; padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; text-align: left;">
                                <div>
                                    <div style="color: #ffffff; font-weight: 800; font-size: 11px; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 5px;">
                                        <span style="border: 1px solid rgba(255,255,255,0.7); padding: 1px 4px; border-radius: 3px; font-size: 9.5px; font-weight: 800;">BWH</span> Hotels
                                    </div>
                                    <div style="color: #ffffff; font-size: 15px; font-weight: 800; line-height: 1.25; margin-bottom: 4px;">
                                        Discover BWH Hotels
                                    </div>
                                    <div style="color: rgba(255,255,255,0.85); font-size: 11px; font-weight: 500; line-height: 1.3;">
                                        Explore Deals Across Bangladesh
                                    </div>
                                </div>
                                <div style="color: rgba(255,255,255,0.5); font-size: 8.5px;">
                                    T&amp;Cs apply
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Card 2: Agoda's Top Stays! (Agoda 1:1 Parity) --}}
                    <a href="{{ route('search.index') }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        {{-- Background night cityscape --}}
                        <div style="position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=600&q=80') center/cover no-repeat;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 27, 75, 0.75) 50%, rgba(15, 23, 42, 0.95) 100%);"></div>
                        </div>

                        <div style="position: relative; z-index: 2; height: 100%; padding: 16px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            {{-- Golden Flagship Deals pill --}}
                            <div style="display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(90deg, #fef08a, #fde047); color: #713f12; padding: 3px 12px; border-radius: 14px; font-size: 10.5px; font-weight: 800; box-shadow: 0 2px 8px rgba(253, 224, 71, 0.35); margin-bottom: 10px;">
                                <i class="fa-solid fa-star" style="font-size: 8px;"></i> Flagship Deals <i class="fa-solid fa-star" style="font-size: 8px;"></i>
                            </div>

                            <div style="color: #ffffff; font-size: 18px; font-weight: 900; letter-spacing: -0.2px; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                                Prime's Top Stays!
                            </div>

                            <div style="color: rgba(255,255,255,0.65); font-size: 9px; margin-top: 6px;">
                                T&amp;Cs apply
                            </div>
                        </div>
                    </a>

                    {{-- Card 3: Last Minute Secret Deals --}}
                    <a href="{{ route('deals') }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 340px; height: 152px; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)';">
                        <div style="position: absolute; top: 10px; left: 10px; z-index: 5; background: #fee2e2; color: #991b1b; padding: 2.5px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-fire text-danger" style="font-size: 9px;"></i> Secret Deals
                        </div>
                        <div style="display: flex; height: 100%; width: 100%;">
                            <div style="width: 44%; height: 100%; position: relative; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80" alt="Secret Deals" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="width: 56%; height: 100%; background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; text-align: left;">
                                <div>
                                    <div style="color: #ffffff; font-size: 15px; font-weight: 800; line-height: 1.25; margin-bottom: 4px; margin-top: 18px;">
                                        Last Minute Deals
                                    </div>
                                    <div style="color: #fde047; font-size: 14px; font-weight: 900;">
                                        Up to 40% OFF
                                    </div>
                                    <div style="color: rgba(255,255,255,0.85); font-size: 10.5px; margin-top: 2px;">
                                        Book Tonight's Best
                                    </div>
                                </div>
                                <div style="color: rgba(255,255,255,0.5); font-size: 8.5px;">
                                    T&amp;Cs apply
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- POPULAR HOLIDAY DESTINATIONS IN BANGLADESH                   --}}
        {{-- ============================================================ --}}
        <div class="mb-4" style="text-align: left;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: #2a2a2e; font-size: 24px; letter-spacing: -0.3px;">
                    Popular holiday destinations in Bangladesh
                </h5>
                <a href="{{ route('search.index') }}" class="text-decoration-none" style="color: #5392f9; font-size: 14.5px; font-weight: 600;">
                    View all <i class="fa-solid fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div style="position: relative;">
                <div id="agodaIntlDestCarousel" style="display: flex; gap: 14px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 6px;">
                    @php
                        $bdPopularDests = [
                            ['name' => 'Cox\'s Bazar', 'region' => 'Chittagong', 'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=300&q=80', 'count' => 'Beach resorts & hotels'],
                            ['name' => 'Sajek Valley', 'region' => 'Rangamati', 'img' => 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=300&q=80', 'count' => 'Cloud view cottages'],
                            ['name' => 'Sreemangal', 'region' => 'Moulvibazar', 'img' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=300&q=80', 'count' => 'Tea garden eco-stays'],
                            ['name' => 'Sylhet', 'region' => 'Sylhet', 'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=300&q=80', 'count' => 'Nature & lake retreats'],
                            ['name' => 'Bandarban', 'region' => 'Hill Tracts', 'img' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=300&q=80', 'count' => 'Mountain peak resorts'],
                            ['name' => 'Saint Martin', 'region' => 'Bay of Bengal', 'img' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=300&q=80', 'count' => 'Coral island beach stays'],
                            ['name' => 'Kuakata', 'region' => 'Patuakhali', 'img' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=300&q=80', 'count' => 'Sunrise & sunset hotels'],
                            ['name' => 'Sundarbans', 'region' => 'Khulna', 'img' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=300&q=80', 'count' => 'Luxury ship cruises'],
                            ['name' => 'Dhaka', 'region' => 'Capital', 'img' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=300&q=80', 'count' => '5-Star city luxury'],
                        ];
                    @endphp
                    @foreach($bdPopularDests as $idst)
                    <a href="{{ route('search.index') }}?destination={{ urlencode($idst['name']) }}" class="text-decoration-none flex-shrink-0" style="display: block; width: 170px;">
                        <div style="background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <img src="{{ $idst['img'] }}" alt="{{ $idst['name'] }}" style="width: 100%; height: 110px; object-fit: cover;">
                            <div style="padding: 10px 12px; text-align: left;">
                                <div style="font-weight: 700; font-size: 14px; color: #1e293b; line-height: 1.2; margin-bottom: 2px;">
                                    {{ $idst['name'] }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; line-height: 1.2;">
                                    {{ $idst['count'] }}
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>


{{-- ============================================================ --}}
{{-- LUXURY BD DESTINATIONS — INFINITE VISUAL CARDS MARQUEE SLIDER --}}
{{-- ============================================================ --}}
<div class="py-4" style="background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%); border-bottom: 1px solid #cbd5e1; margin-top: 0; overflow: hidden; position: relative;">
    <div style="max-width: 1366px; margin: 0 auto; padding: 0 20px;">
        
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 fw-bold" style="font-size: 12px; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(225, 29, 72, 0.3);">
                    <i class="fa-solid fa-fire me-1"></i> TOP DESTINATIONS
                </span>
                <h5 class="fw-bold text-dark mb-0 ms-1" style="font-size: 24px; letter-spacing: -0.3px;">
                    Explore Popular Travel Destinations in Bangladesh
                </h5>
            </div>

            {{-- Arrow Controls --}}
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-white border shadow-xs rounded-circle d-flex align-items-center justify-content-center" id="bdMarqueePrevBtn" style="width: 36px; height: 36px; background: #fff;" title="Previous">
                    <i class="fa-solid fa-chevron-left text-dark fs-6"></i>
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-xs rounded-circle d-flex align-items-center justify-content-center" id="bdMarqueeNextBtn" style="width: 36px; height: 36px; background: #fff;" title="Next">
                    <i class="fa-solid fa-chevron-right text-dark fs-6"></i>
                </button>
            </div>
        </div>

        {{-- Marquee Track Container --}}
        <div id="bdMarqueeTrack" class="d-flex gap-3 overflow-hidden py-2" style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
            @php
                $bdDestinationsList = isset($destinations) && $destinations->isNotEmpty()
                    ? $destinations
                    : \App\Models\FeaturedDestination::where('is_active', true)->orderBy('sort_order', 'asc')->get();
            @endphp

            {{-- Render 2 Duplicate Sets for Seamless Infinite Loop --}}
            @foreach($bdDestinationsList->concat($bdDestinationsList) as $dest)
            <a href="{{ route('search.index') }}?destination={{ urlencode($dest->name) }}" class="text-decoration-none flex-shrink-0" style="width: 230px;">
                <div class="card border-0 rounded-4 overflow-hidden shadow-sm position-relative hover-card-zoom" style="height: 140px; transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s ease; cursor: pointer;">
                    
                    {{-- Destination Photo / Video --}}
                    @if(!empty($dest->video_url))
                        <video src="{{ $dest->video_url }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.6s ease;" autoplay loop muted playsinline></video>
                    @else
                        <img src="{{ $dest->image_url }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.6s ease;" alt="{{ $dest->name }}">
                    @endif
                    
                    {{-- Dark Gradient Overlay --}}
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15, 23, 42, 0.05) 0%, rgba(15, 23, 42, 0.88) 100%);"></div>

                    {{-- Card Badge & Titles --}}
                    <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white">
                        @php
                            $realLiveCount = $dest->live_property_count ?? $dest->properties_count ?? null;
                        @endphp
                        <span class="badge bg-white-20 text-white backdrop-blur rounded-pill px-2.5 py-1 mb-1 fw-bold" style="font-size: 10px; background: rgba(255,255,255,0.25); backdrop-filter: blur(4px);">
                            {{ !empty($realLiveCount) ? $realLiveCount . ' Stays Available' : 'Featured Destination' }}
                        </span>
                        <h6 class="fw-extrabold mb-0 text-white" style="font-size: 16px; font-weight: 800; text-shadow: 0 2px 6px rgba(0,0,0,0.8); line-height: 1.2;">
                            {{ $dest->name }}
                        </h6>
                        <small style="font-size: 11px; color: #cbd5e1; font-weight: 500;">
                            {{ $dest->tagline ?? 'Popular Travel Destination' }}
                        </small>
                    </div>
                </div>
            </a>
            @endforeach

        </div>
    </div>
</div>

{{-- Hover & Auto-Scroll Marquee Script --}}
<style>
    .hover-card-zoom:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22) !important;
    }
    .hover-card-zoom:hover img {
        transform: scale(1.08);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('bdMarqueeTrack');
    const prevBtn = document.getElementById('bdMarqueePrevBtn');
    const nextBtn = document.getElementById('bdMarqueeNextBtn');

    if (track) {
        let isHovered = false;
        const scrollStep = 240;

        // Auto Scroll Interval (25ms step)
        setInterval(() => {
            if (!isHovered) {
                if (track.scrollLeft >= (track.scrollWidth - track.clientWidth - 5)) {
                    track.scrollLeft = 0; // Seamless loop reset
                } else {
                    track.scrollLeft += 1;
                }
            }
        }, 25);

        // Pause on mouse hover
        track.addEventListener('mouseenter', () => isHovered = true);
        track.addEventListener('mouseleave', () => isHovered = false);

        // Manual Arrow Buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -scrollStep, behavior: 'smooth' });
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: scrollStep, behavior: 'smooth' });
            });
        }
    }
});
</script>

{{-- Hero slide JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.bd-hero-slide');
    const heroTitle = document.getElementById('bdHeroTitle');
    const titles = @json(
        $heroSlides->isNotEmpty()
            ? $heroTitles->values()->toArray()
            : collect($staticSlides)->pluck('title')->toArray()
    );
    let cur = 0;
    if (slides.length > 1) {
        setInterval(function () {
            slides[cur].style.opacity = '0';
            cur = (cur + 1) % slides.length;
            slides[cur].style.opacity = '1';
            if (heroTitle && titles[cur]) {
                heroTitle.style.opacity = '0';
                setTimeout(() => { heroTitle.textContent = titles[cur]; heroTitle.style.opacity = '1'; }, 300);
            }
        }, 5000);
    }
});
</script>

{{-- ============================================================ --}}
{{-- CONTINUE PLANNING — user's recent bookings from DB          --}}
{{-- ============================================================ --}}
@if($currentUser && $recentBookings->isNotEmpty())
<section class="py-4 bg-white">
    <div class="container pt-2 pb-2">
        <h5 class="fw-bold mb-3" style="color:#0f172a;font-size:24px;letter-spacing:-0.3px;">
            Continue planning your trip
        </h5>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            @foreach($recentBookings as $booking)
            @if($booking->property)
            <a href="{{ route('hotels.show', $booking->property->id) }}" class="text-decoration-none">
                <div style="background:#fff;border:1px solid #dddfe4;border-radius:16px;height:64px;display:flex;align-items:center;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);min-width:260px;max-width:360px;transition:all 0.2s ease;">
                    @if($booking->property->image_url)
                    <img src="{{ $booking->property->image_url }}" style="width:76px;height:64px;object-fit:cover;flex-shrink:0;border-radius:16px 0 0 16px;" alt="{{ $booking->property->name }}">
                    @else
                    <div style="width:76px;height:64px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;border-radius:16px 0 0 16px;">🏨</div>
                    @endif
                    <div style="padding:0 14px;overflow:hidden;">
                        <h6 style="font-weight:700;font-size:13px;color:#1e293b;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $booking->property->name }}
                        </h6>
                        <small style="font-size:11px;color:#64748b;display:block;margin-top:2px;">
                            {{ $booking->property->city }} &bull;
                            @if($booking->check_in)
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('M j') }}
                                @if($booking->check_out) – {{ \Carbon\Carbon::parse($booking->check_out)->format('M j') }}@endif
                            @endif
                        </small>
                    </div>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- VIP LOYALTY SECTION — 100% dynamic from DB settings         --}}
{{-- ============================================================ --}}
@if($currentUser)
@php
    $tierColors = [
        'Bronze'   => ['bg' => '#a86241', 'text' => '#fff', 'nodeColor' => '#854d0e'],
        'Silver'   => ['bg' => '#94a3b8', 'text' => '#fff', 'nodeColor' => '#475569'],
        'Gold'     => ['bg' => '#f59e0b', 'text' => '#fff', 'nodeColor' => '#b45309'],
        'Platinum' => ['bg' => '#a78bfa', 'text' => '#fff', 'nodeColor' => '#6d28d9'],
        'Diamond'  => ['bg' => '#38bdf8', 'text' => '#fff', 'nodeColor' => '#0284c7'],
    ];
    $tc = $tierColors[$vipTier] ?? $tierColors['Bronze'];
    $tierSteps = [
        ['name'=>'Bronze',   'required'=> 0,                        'key'=>'bronze'],
        ['name'=>'Silver',   'required'=> $vipThresholds['silver'],  'key'=>'silver'],
        ['name'=>'Gold',     'required'=> $vipThresholds['gold'],    'key'=>'gold'],
        ['name'=>'Platinum', 'required'=> $vipThresholds['platinum'],'key'=>'platinum'],
        ['name'=>'Diamond',  'required'=> $vipThresholds['diamond'], 'key'=>'diamond'],
    ];
@endphp
<section class="py-4" style="background:transparent;">
    <div class="container py-2">
        <h4 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color:#002d72;font-size:20px;">
            Welcome back, {{ $currentUser->name ?? $currentUser->email }}! &nbsp;
            <div style="display:inline-flex;align-items:center;border-radius:3px;overflow:hidden;height:20px;font-size:11px;box-shadow:0 1px 2px rgba(0,0,0,0.2);">
                <div style="background:#1b2028;color:#fff;padding:0 14px 0 6px;height:100%;display:flex;align-items:center;gap:3px;font-weight:800;clip-path:polygon(0 0,100% 0,80% 100%,0 100%);">
                    <span style="font-size:9px;">★</span>VIP
                </div>
                <div style="background:linear-gradient(135deg,{{ $tc['bg'] }},{{ $tc['bg'] }}cc);color:#1e293b;padding:0 8px 0 6px;height:100%;display:flex;align-items:center;font-weight:700;margin-left:-5px;">
                    {{ $vipTier }}
                </div>
            </div>
        </h4>

        <div class="bg-white border rounded-3 p-4 shadow-sm" style="border-color:#e2e8f0!important;">
            <div class="row g-4 align-items-start">
                {{-- VIP Card --}}
                <div class="col-lg-3 col-md-4">
                    <div style="background:#212836;color:#fff;border-radius:8px;padding:18px;min-height:145px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                        <div style="position:absolute;top:0;right:0;width:42%;height:100%;background:linear-gradient(135deg,{{ $tc['bg'] }},{{ $tc['bg'] }}aa);clip-path:polygon(45% 0,100% 0,100% 100%,0 100%);z-index:1;"></div>
                        <div style="position:relative;z-index:2;" class="d-flex align-items-center gap-1">
                            <span style="font-size:15px;font-weight:800;color:#fff;">★ VIP {{ $vipTier }}</span>
                        </div>
                        <div style="position:relative;z-index:2;margin-top:35px;">
                            <div style="font-size:11.5px;color:#cbd5e1;">Your bookings in the last 2 years:</div>
                            <div style="font-size:13.5px;font-weight:700;color:#fff;margin-top:2px;">{{ $userBookings }} booking{{ $userBookings != 1 ? 's' : '' }}</div>
                            @if($vipNextTier && $vipNextRequired)
                            <div style="font-size:10px;color:#94a3b8;margin-top:4px;">
                                {{ max(0, $vipNextRequired - $userBookings) }} more to reach {{ $vipNextTier }}
                            </div>
                            @else
                            <div style="font-size:10px;color:#fbbf24;margin-top:4px;">🏆 Max tier reached!</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Benefits + Stepper --}}
                <div class="col-lg-9 col-md-8">
                    <p class="mb-3" style="font-size:13px;color:#334155;line-height:1.5;">
                        Every time you see the VIP badge, it means you are getting special discounts only available to VIP members.
                        @if($vipDiscount > 0)
                            You currently enjoy <strong>{{ $vipDiscount }}% {{ $vipTier }} discount</strong> on all bookings!
                        @endif
                    </p>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div class="d-flex flex-wrap gap-3" style="font-size:13px;font-weight:700;color:#1e293b;">
                            <div class="d-flex align-items-center gap-1"><span style="color:#22c55e;font-size:14px;">✔</span> Best price guarantee</div>
                            @if($vipDiscount > 0)
                            <div class="d-flex align-items-center gap-1"><span style="color:#22c55e;font-size:14px;">✔</span> VIP deals up to {{ $vipDiscount }}% off</div>
                            @endif
                            <div class="d-flex align-items-center gap-1"><span style="color:#22c55e;font-size:14px;">✔</span> Insider deals access</div>
                        </div>
                        <a href="{{ route('vip') }}" class="btn text-white fw-bold px-4 py-2" style="background:#2067e1;border-radius:999px;font-size:13.5px;border:none;">More details</a>
                    </div>

                    {{-- Progress Stepper (dynamic thresholds from DB) --}}
                    <div class="pt-3 border-top" style="border-color:#f1f5f9!important;">
                        <div class="d-flex align-items-center justify-content-between text-center" style="z-index:2;">
                            @foreach($tierSteps as $idx => $step)
                            @php
                                $isActive  = $step['name'] === $vipTier;
                                $isPast    = $userBookings >= $step['required'];
                                $nodeColor = $isPast ? ($tierColors[$step['name']]['nodeColor'] ?? '#64748b') : '#94a3b8';
                            @endphp
                            <div style="flex:1;text-align:center;">
                                <div style="width:22px;height:22px;background:{{ $isPast ? $nodeColor : '#f1f5f9' }};color:{{ $isPast ? '#fff' : '#64748b' }};border:{{ $isPast ? 'none' : '1px solid #cbd5e1' }};border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-bottom:4px;">★</div>
                                <div style="font-size:12px;font-weight:{{ $isActive ? '800' : '700' }};color:{{ $isActive ? $nodeColor : '#64748b' }};line-height:1.1;">{{ $step['name'] }}</div>
                                <div style="font-size:10px;color:{{ $isPast ? '#22c55e' : '#94a3b8' }};">{{ $step['required'] === 0 ? 'Member' : $step['required'].' bookings' }}</div>
                            </div>
                            @if($idx < count($tierSteps) - 1)
                            <div style="flex:1;height:1px;border-top:1.5px dashed {{ $userBookings >= ($tierSteps[$idx+1]['required']) ? $nodeColor : '#cbd5e1' }};margin-bottom:22px;"></div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<style>
#bdDestCarousel::-webkit-scrollbar { display: none; }
#bdDestScrollBtn:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.22) !important; }
</style>

{{-- ============================================================ --}}
{{-- FEATURED DESTINATIONS — From Admin CMS panel               --}}
{{-- ============================================================ --}}
@if($destinations->isNotEmpty())
<section class="py-5" style="background:#f8f9fa;">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small d-block">Explore Top Cities</span>
                <h2 class="fw-bold mb-0" style="color:#1e293b; font-size: 24px; letter-spacing:-0.3px;">Popular Destinations</h2>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                View All <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div style="position:relative;">
            <div style="position:absolute;right:0;top:0;height:100%;width:120px;background:linear-gradient(to left,#f8f9fa 30%,transparent 100%);z-index:5;pointer-events:none;border-radius:0 12px 12px 0;"></div>
            <button id="bdDestScrollBtn" onclick="document.getElementById('bdDestCarousel').scrollBy({left:220,behavior:'smooth'})" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);z-index:10;width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;font-size:14px;transition:box-shadow 0.2s;">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div id="bdDestCarousel" style="display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;padding-bottom:8px;-ms-overflow-style:none;scrollbar-width:none;">
                @foreach($destinations as $dest)
                <a href="{{ route('search.index') }}?destination={{ urlencode($dest->city) }}" class="text-decoration-none text-dark flex-shrink-0" style="width:190px;">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="width:190px;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,0.14)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <img src="{{ $dest->image_url }}" style="height:140px;width:100%;object-fit:cover;" alt="{{ $dest->city }} Hotels" loading="lazy">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $dest->city ?: $dest->name }}</h6>
                            <small class="text-muted" style="font-size:12px;">
                                @if($dest->property_count_override)
                                    {{ number_format($dest->property_count_override) }} accommodations
                                @elseif(!empty($dest->live_property_count))
                                    {{ $dest->live_property_count }} {{ $dest->live_property_count == 1 ? 'accommodation' : 'accommodations' }}
                                @else
                                    Hotels &amp; stays
                                @endif
                            </small>
                        </div>
                    </div>
                </a>
                @endforeach
                <div style="min-width:80px;flex-shrink:0;"></div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- PROPERTY TYPES — Live counts from DB                       --}}
{{-- ============================================================ --}}
@php
$propertyTypeConfig = [
    'hotel'      => ['label'=>'Hotels',               'emoji'=>'🏨', 'img'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'],
    'resort'     => ['label'=>'Resorts',              'emoji'=>'🏖️', 'img'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=400&q=80'],
    'houseboat'  => ['label'=>'Ships & Houseboats',   'emoji'=>'🚢', 'img'=>'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=400&q=80'],
    'homestay'   => ['label'=>'Cottages & Homestays', 'emoji'=>'🏡', 'img'=>'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=400&q=80'],
    'apartment'  => ['label'=>'Apartments',           'emoji'=>'🏢', 'img'=>'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=400&q=80'],
    'villa'      => ['label'=>'Villas',               'emoji'=>'🏡', 'img'=>'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=400&q=80'],
];
$activeTypes = array_filter($propertyTypeConfig, fn($k) => ($propertyTypeCounts[$k] ?? 0) > 0 || true, ARRAY_FILTER_USE_KEY);
@endphp

<section class="py-5 bg-white border-top border-gray-100">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small d-block">Explore Types</span>
                <h2 class="fw-bold mb-0" style="color:#1e293b; font-size: 24px; letter-spacing:-0.3px;">Browse Accommodations by Property Type</h2>
            </div>
        </div>

        <div class="row g-3">
            @foreach($activeTypes as $type => $config)
            @php $count = $propertyTypeCounts[$type] ?? 0; @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('search.index') }}?type={{ $type }}" class="text-decoration-none text-dark">
                    <div class="card border rounded-4 overflow-hidden h-100 bg-white" style="border-radius:14px!important;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <img src="{{ $config['img'] }}" style="height:110px;width:100%;object-fit:cover;" alt="{{ $config['label'] }}" loading="lazy">
                        <div class="card-body p-2 text-center">
                            <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $config['label'] }}</h6>
                            <small class="text-muted" style="font-size:11px;">
                                @if($count > 0)
                                    {{ number_format($count) }} {{ $count == 1 ? 'property' : 'properties' }}
                                @else
                                    Browse all
                                @endif
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURED PROPERTIES — Admin/Vendor added properties        --}}
{{-- ============================================================ --}}
@if($featuredProperties->isNotEmpty())
<section class="py-5 bg-white border-top">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small d-block">Top Picks</span>
                <h2 class="fw-bold mb-0" style="color:#1e293b; font-size: 24px; letter-spacing:-0.3px;">Featured Properties</h2>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">View All</a>
        </div>
        <div class="row g-3">
            @foreach($featuredProperties as $prop)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('hotels.show', $prop->id) }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,0.14)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        @php
                            $rawImg2 = $prop->primary_image ?: ($prop->image_url ?: null);
                            if (!$rawImg2 && !empty($prop->images)) {
                                $parsedImgs2 = is_string($prop->images) ? json_decode($prop->images, true) : $prop->images;
                                $rawImg2 = $parsedImgs2[0] ?? null;
                            }
                            $cardImg2 = function_exists('media_url') ? media_url($rawImg2) : ($rawImg2 ? (str_starts_with($rawImg2, 'http') ? $rawImg2 : asset('storage/' . ltrim($rawImg2, '/'))) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
                        @endphp
                        <img src="{{ $cardImg2 }}" style="height:180px;width:100%;object-fit:cover;" alt="{{ $prop->name }}" loading="lazy">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $prop->name }}</h6>
                                    <small class="text-muted">{{ $prop->city }}{{ $prop->country ? ', '.$prop->country : '' }}</small>
                                </div>
                                @if($prop->star_rating)
                                <div style="color:#f59e0b;font-size:12px;white-space:nowrap;">
                                    @for($s=1;$s<=$prop->star_rating;$s++)★@endfor
                                </div>
                                @endif
                            </div>
                            @if($prop->price_per_night)
                            <div class="mt-2" style="font-size:13px;font-weight:700;color:#2067e1;">
                                From ৳{{ number_format($prop->price_per_night) }}<span style="font-size:11px;font-weight:400;color:#64748b;"> /night</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- TRAVEL STORIES & INSPIRATION (Live from Blog CMS)           --}}
{{-- ============================================================ --}}
@php
    try {
        $recentBlogPosts = (\Illuminate\Support\Facades\Schema::hasTable('blog_posts') && class_exists(\App\Models\BlogPost::class))
            ? \App\Models\BlogPost::where('is_published', true)->latest('published_at')->take(3)->get()
            : collect();
    } catch (\Throwable $e) {
        $recentBlogPosts = collect();
    }
@endphp
@if($recentBlogPosts->count() > 0)
<section class="py-5" style="background:#fff;">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small d-block">Travel Journal</span>
                <h2 class="fw-bold mb-0" style="color:#1e293b; font-size: 24px; letter-spacing:-0.3px;">Stories &amp; Inspiration</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Explore All Stories</a>
        </div>
        <div class="row g-4">
            @foreach($recentBlogPosts as $bPost)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden" style="transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <a href="{{ route('blog.show', $bPost->slug) }}" class="d-block overflow-hidden" style="height:190px;">
                        <img src="{{ $bPost->featured_image ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80' }}" style="height:100%;width:100%;object-fit:cover;" alt="{{ $bPost->title }}" loading="lazy">
                    </a>
                    <div class="card-body p-3.5 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-primary border" style="font-size:11px;">{{ $bPost->category }}</span>
                            <small class="text-muted" style="font-size:11.5px;">{{ $bPost->created_at->format('d M Y') }}</small>
                        </div>
                        <h6 class="fw-bold mb-2">
                            <a href="{{ route('blog.show', $bPost->slug) }}" class="text-dark text-decoration-none" style="line-height:1.4;">
                                {{ \Illuminate\Support\Str::limit($bPost->title, 60) }}
                            </a>
                        </h6>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height:1.5;">
                            {{ $bPost->excerpt ?: strip_tags(\Illuminate\Support\Str::limit($bPost->content, 85)) }}
                        </p>
                        <a href="{{ route('blog.show', $bPost->slug) }}" class="text-primary fw-bold text-decoration-none small mt-auto">
                            Read Guide <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- STATS TICKER — Live from DB                                --}}
{{-- ============================================================ --}}
@if(isset($stats) && (($stats['total_properties'] ?? $stats['total'] ?? 0) > 0))
<section class="py-4" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">
    <div class="container">
        <div class="row g-4 text-center text-white">
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ number_format($stats['total_properties'] ?? $stats['total'] ?? 29) }}+</div>
                <div style="font-size:13px;opacity:0.8;">Properties Listed</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ number_format($stats['destinations'] ?? $destinations->count() ?? 9) }}+</div>
                <div style="font-size:13px;opacity:0.8;">Destinations</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ !empty($stats['avg_rating']) ? number_format($stats['avg_rating'], 1) : '8.7' }}</div>
                <div style="font-size:13px;opacity:0.8;">Avg. Rating</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">Free</div>
                <div style="font-size:13px;opacity:0.8;">Cancellation Available</div>
            </div>
        </div>
    </div>
</section>
@endif
{{-- ============================================================ --}}
{{-- POPULAR HOTEL SEARCHES & KEYWORD HUB (Agoda & Booking.com Exact SEO Semantic Architecture) --}}
{{-- ============================================================ --}}
<section class="py-5" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="agoda-exact-container">
        <div class="mb-4 text-start">
            <h2 class="fw-bold mb-1 text-dark" style="font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
                Popular Hotel Booking Searches in Bangladesh
            </h2>
            <p class="text-secondary mb-0" style="font-size: 13px;">
                Explore top destinations, 5-star beach resorts, and verified accommodations at guaranteed lowest prices.
            </p>
        </div>

        <div class="row g-4 mb-4 text-start">
            {{-- Col 1: Top Destinations --}}
            <div class="col-12 col-md-4">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">
                    <i class="fa-solid fa-location-dot text-primary me-2"></i>Top Hotel Destinations
                </h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13px;">
                    <li><a href="{{ url('/hotels-in-coxs-bazar') }}" class="text-decoration-none text-secondary hover-primary">Hotels in Cox's Bazar (19+ Stays)</a></li>
                    <li><a href="{{ url('/hotels-in-kolatoli') }}" class="text-decoration-none text-secondary hover-primary">Kolatoli Beach Hotels &amp; Suites</a></li>
                    <li><a href="{{ url('/hotels-in-inani') }}" class="text-decoration-none text-secondary hover-primary">Inani Beach Luxury Resorts</a></li>
                    <li><a href="{{ url('/hotels-in-dhaka') }}" class="text-decoration-none text-secondary hover-primary">Hotels in Dhaka (Gulshan &amp; Banani)</a></li>
                    <li><a href="{{ url('/hotels-in-sylhet') }}" class="text-decoration-none text-secondary hover-primary">Sylhet Tea Garden Resorts</a></li>
                    <li><a href="{{ url('/hotels-in-sajek') }}" class="text-decoration-none text-secondary hover-primary">Sajek Valley Cloud View Cottages</a></li>
                    <li><a href="{{ url('/hotels-in-kuakata') }}" class="text-decoration-none text-secondary hover-primary">Kuakata Sunset Sea Beach Hotels</a></li>
                    <li><a href="{{ url('/hotels-in-sreemangal') }}" class="text-decoration-none text-secondary hover-primary">Sreemangal Eco &amp; Nature Stays</a></li>
                    <li><a href="{{ url('/hotels-in-saint-martin') }}" class="text-decoration-none text-secondary hover-primary">Saint Martin Island Beach Resorts</a></li>
                    <li><a href="{{ url('/hotels-in-sundarban') }}" class="text-decoration-none text-secondary hover-primary">Sundarbans Ship Cruise Booking</a></li>
                </ul>
            </div>

            {{-- Col 2: Top 5-Star Resorts --}}
            <div class="col-12 col-md-4">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">
                    <i class="fa-solid fa-hotel text-primary me-2"></i>Top 5-Star Resorts &amp; Hotels
                </h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13px;">
                    <li><a href="{{ url('/hotel/sea-pearl-beach-resort-spa') }}" class="text-decoration-none text-secondary hover-primary">Sea Pearl Beach Resort &amp; Spa (5-Star)</a></li>
                    <li><a href="{{ url('/hotel/ocean-paradise-hotel-resorts') }}" class="text-decoration-none text-secondary hover-primary">Ocean Paradise Hotel &amp; Resorts</a></li>
                    <li><a href="{{ url('/hotel/ramada-by-wyndham-coxs-bazar') }}" class="text-decoration-none text-secondary hover-primary">Ramada by Wyndham Cox's Bazar</a></li>
                    <li><a href="{{ url('/hotel/long-beach-hotel-coxs-bazar') }}" class="text-decoration-none text-secondary hover-primary">Long Beach Hotel Cox's Bazar</a></li>
                    <li><a href="{{ url('/hotel/hotel-the-cox-today') }}" class="text-decoration-none text-secondary hover-primary">Hotel The Cox Today (Kolatoli)</a></li>
                    <li><a href="{{ url('/hotel/seagull-hotels-ltd') }}" class="text-decoration-none text-secondary hover-primary">Seagull Hotels Ltd (Sugandha)</a></li>
                    <li><a href="{{ url('/hotel/dera-resort-spa-coxs-bazar') }}" class="text-decoration-none text-secondary hover-primary">Dera Resort &amp; Spa (Inani Beach)</a></li>
                    <li><a href="{{ url('/hotel/hotel-suite-sadaf') }}" class="text-decoration-none text-secondary hover-primary">Hotel Suite Sadaf (Marine Drive)</a></li>
                    <li><a href="{{ url('/hotel/green-nature-resort-suites') }}" class="text-decoration-none text-secondary hover-primary">Green Nature Resort &amp; Suites</a></li>
                    <li><a href="{{ url('/hotel/grace-cox-smart-hotel') }}" class="text-decoration-none text-secondary hover-primary">Grace Cox Smart Hotel &amp; Stays</a></li>
                </ul>
            </div>

            {{-- Col 3: Popular Long-Tail Hotel Queries --}}
            <div class="col-12 col-md-4">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">
                    <i class="fa-solid fa-magnifying-glass text-primary me-2"></i>Popular Hotel Booking Queries
                </h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13px;">
                    <li><a href="{{ route('search.index', ['destination' => 'Cox\'s Bazar', 'min_price' => 1500, 'max_price' => 4000]) }}" class="text-decoration-none text-secondary hover-primary">Cheap hotels in Cox's Bazar near sea beach</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Cox\'s Bazar', 'star_rating' => [5]]) }}" class="text-decoration-none text-secondary hover-primary">5-star luxury resorts in Cox's Bazar 2026</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Cox\'s Bazar']) }}" class="text-decoration-none text-secondary hover-primary">Couple friendly hotels in Cox's Bazar</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Cox\'s Bazar', 'amenities' => ['swimming_pool']]) }}" class="text-decoration-none text-secondary hover-primary">Hotels with swimming pool &amp; private beach</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Sajek']) }}" class="text-decoration-none text-secondary hover-primary">Sajek Valley best wooden resort booking</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Sylhet']) }}" class="text-decoration-none text-secondary hover-primary">Luxury tea garden resorts in Sreemangal</a></li>
                    <li><a href="{{ route('search.index', ['destination' => 'Dhaka', 'star_rating' => [5]]) }}" class="text-decoration-none text-secondary hover-primary">5-star hotel in Gulshan &amp; airport transfer</a></li>
                    <li><a href="{{ url('/hotels-in-coxs-bazar') }}" class="text-decoration-none text-secondary hover-primary">Last minute hotel deals in Cox's Bazar</a></li>
                    <li><a href="{{ route('deals') }}" class="text-decoration-none text-secondary hover-primary">Hotel booking with bKash discount &amp; coupon</a></li>
                    <li><a href="{{ route('services') }}?type=houseboat" class="text-decoration-none text-secondary hover-primary">Tanguar Haor luxury houseboat booking</a></li>
                </ul>
            </div>
        </div>

        {{-- Authoritative SEO Content Paragraph (Guaranteed Google Quality Score & Context Authority) --}}
        <div class="p-4 rounded-3 bg-white border text-start" style="border-color: #e2e8f0 !important;">
            <h3 class="fw-bold text-dark mb-2" style="font-size: 16px;">
                Online Hotel Booking in Bangladesh — Guaranteed Lowest Rates on Prime Booking
            </h3>
            <p class="text-secondary mb-2" style="font-size: 13.5px; line-height: 1.65;">
                Welcome to <strong>Prime Booking</strong> (প্রাইম বুকিং), Bangladesh's most trusted online travel platform for booking hotels, luxury beach resorts, eco-cottages, domestic flights, and Sundarbans ship cruises. Whether you are searching for a <strong>5-star luxury resort in Cox's Bazar</strong> like Sea Pearl Beach Resort or Ocean Paradise, a budget-friendly hotel near Kolatoli beach, a scenic cloud-view wooden cottage in <strong>Sajek Valley</strong>, or a tranquil tea garden retreat in <strong>Sylhet &amp; Sreemangal</strong>, Prime Booking offers instant confirmation with transparent pricing and zero hidden fees.
            </p>
            <p class="text-secondary mb-0" style="font-size: 13.5px; line-height: 1.65;">
                Enjoy hassle-free payment via <strong>bKash, Nagad, Visa, and Mastercard</strong>, flexible pay-at-hotel policies, free cancellation on selected stays, and round-the-clock 24/7 dedicated traveler support. For instant reservation assistance, contact our official support hotline at <strong>+880 1606-352642</strong>.
            </p>
        </div>
    </div>
</section>

{{-- Scroll reveal --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const obs = new IntersectionObserver((entries, o) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('active'); o.unobserve(e.target); }});
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.scroll-reveal').forEach(el => obs.observe(el));
});
</script>

@endsection
