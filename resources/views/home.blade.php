@extends('layouts.main', ['activePage' => 'home'])

@section('title', ($siteSettings['site_name'] ?? 'Prime Booking') . ' | Hotels, Flights & Travel | Free Cancellation & Best Price Guarantee')

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION — slides from DB destinations (or static bg)   --}}
{{-- ============================================================ --}}
<style>
    #bdHeroSliderSection {
        position: relative;
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

    <div class="container text-center" style="position:relative;z-index:2;">
        <h1 id="bdHeroTitle" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-weight:800;font-size:26px;letter-spacing:0.5px;color:#ffffff!important;text-transform:uppercase;margin-bottom:6px;text-shadow:0 2px 12px rgba(0,0,0,0.85);transition:opacity 0.5s ease;">
            {{ $siteSettings['site_name'] ?? 'Prime Booking' }} — Hotels, Resorts & Homes
        </h1>
        <p style="font-size:14px;font-weight:500;color:#f1f5f9;text-shadow:0 1px 6px rgba(0,0,0,0.7);margin-bottom:26px;">
            {{ number_format($stats['total'] ?? 0) }}+ properties &bull; {{ $siteSettings['site_tagline'] ?? 'Best Price Guarantee' }}
        </p>
        <div class="hero-search-wrapper" style="margin-top:20px; margin-bottom:-110px;">
            @include('components.search.search-bar')
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- TRIP SAVINGS & JOURNEY PLANNING SECTION (Agoda 1:1 Parity)   --}}
{{-- ============================================================ --}}
<div class="py-4 pt-4" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; margin-top: 45px;">
    <div style="max-width: 1140px; margin: 0 auto; padding: 0 20px;">
        
        {{-- 1. Top Green Banner: Unlocked Trip Savings Deals --}}
        <div class="card p-3 px-4 mb-4 border-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); border-radius: 12px; border: 1px solid #bbf7d0 !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #14532d; font-size: 18px; letter-spacing: -0.3px;">
                        {{ auth()->check() ? auth()->user()->name : 'Shawon' }}! You've unlocked Trip Savings deals!
                    </h4>
                    <p class="mb-0 fw-semibold" style="color: #15803d; font-size: 13px;">
                        Save more with Trip Savings
                    </p>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge text-white px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #00897b; font-size: 12.5px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,137,123,0.25);">
                        <i class="fa-solid fa-tag"></i> Trip Savings
                    </span>
                </div>
            </div>
        </div>

        {{-- 2. Need more hotels for your trip? --}}
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-2.5" style="font-size: 17.5px; letter-spacing: -0.2px;">Need more hotels for your trip?</h5>
            <div class="card p-3.5 bg-white border" style="border-radius: 12px; border-color: #e2e8f0 !important; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width: 44px; height: 44px; background: #e8f5e9; color: #2e7d32; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                            <i class="fa-solid fa-hotel"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge text-white px-2 py-0.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #00897b; font-size: 10.5px; border-radius: 4px;">
                                    <i class="fa-solid fa-suitcase-rolling" style="font-size: 9px;"></i> Trip Savings
                                </span>
                                <span class="badge text-white px-2 py-0.5 fw-bold" style="background: #16a34a; font-size: 10.5px; border-radius: 4px;">
                                    Up to 5% off
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-0.5" style="font-size: 14.5px;">Book another place to stay</h6>
                            <p class="text-secondary small mb-1.5" style="font-size: 12px;">We have unlocked the best deals with Trip Savings</p>
                            <div class="text-secondary small fw-semibold d-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                <i class="fa-regular fa-calendar text-primary"></i> 15 Sep - 16 Sep &bull; 1 Adult 
                                <a href="{{ route('search.index') }}" class="text-primary text-decoration-none ms-1"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('search.index') }}" class="btn btn-outline-primary px-4 py-1.5 fw-bold rounded-pill" style="font-size: 13.5px; border-width: 1.5px;">
                            Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Plan your journey to your hotel --}}
        <div class="mb-3">
            <h5 class="fw-bold text-dark mb-1" style="font-size: 17.5px; letter-spacing: -0.2px;">Plan your journey to your hotel</h5>
            <p class="text-secondary small mb-3" style="font-size: 12.5px;">Book your ride in advance for a hassle-free trip</p>

            <div class="row g-3">
                {{-- Card A: Airport Transfer --}}
                <div class="col-md-6">
                    <div class="card p-3.5 bg-white border h-100" style="border-radius: 12px; border-color: #e2e8f0 !important; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div style="width: 80px; height: 50px; display: flex; align-items: center; margin-bottom: 8px;">
                                    <img src="{{ asset('images/bd_hero_slide1.png') }}" onerror="this.src='https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=220&q=80'" alt="Airport Transfer" style="max-height: 48px; max-width: 80px; object-fit: contain;">
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px;">Book your airport transfer</h6>
                                <p class="text-secondary small mb-2" style="font-size: 12px; line-height: 1.35;">Get to your hotel easily and securely</p>
                                <div class="text-secondary small fw-semibold" style="font-size: 11.5px;">
                                    8 Sep &bull; 1 Adult <a href="{{ route('transfers.index') }}" class="text-primary text-decoration-none ms-1"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></a>
                                </div>
                            </div>
                            <div class="align-self-end">
                                <a href="{{ route('transfers.index') }}" class="btn btn-outline-primary px-3 py-1 fw-bold rounded-pill" style="font-size: 12.5px; border-width: 1.5px;">
                                    Search
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card B: Rent a Car --}}
                <div class="col-md-6">
                    <div class="card p-3.5 bg-white border h-100" style="border-radius: 12px; border-color: #e2e8f0 !important; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div style="width: 80px; height: 50px; display: flex; align-items: center; margin-bottom: 8px;">
                                    <img src="{{ asset('images/bd_hero_slide4.png') }}" onerror="this.src='https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=220&q=80'" alt="Rent a Car" style="max-height: 48px; max-width: 80px; object-fit: contain;">
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px;">Rent a car</h6>
                                <p class="text-secondary small mb-2" style="font-size: 12px; line-height: 1.35;">Find an ideal ride for your trip</p>
                                <div class="text-secondary small fw-semibold" style="font-size: 11.5px;">
                                    8 Sep - 15 Sep <a href="{{ route('transfers.index') }}" class="text-primary text-decoration-none ms-1"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></a>
                                </div>
                            </div>
                            <div class="align-self-end">
                                <a href="{{ route('transfers.index') }}" class="btn btn-outline-primary px-3 py-1 fw-bold rounded-pill" style="font-size: 12.5px; border-width: 1.5px;">
                                    Search
                                </a>
                            </div>
                        </div>
                    </div>
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
                <h5 class="fw-bold text-dark mb-0 ms-1" style="font-size: 18px; letter-spacing: -0.3px;">
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
                $dbDestinations = \App\Models\FeaturedDestination::where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->get();

                if ($dbDestinations->isEmpty()) {
                    $bdDestinationsList = collect([
                        (object)['name' => "Cox's Bazar", 'tagline' => 'Beach & Sea', 'properties_count' => 118, 'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=500&q=80', 'video_url' => null],
                        (object)['name' => 'Sylhet', 'tagline' => 'Tea Gardens & Water', 'properties_count' => 95, 'image_url' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=500&q=80', 'video_url' => null],
                        (object)['name' => 'Sajek Valley', 'tagline' => 'Cloud Hills & Eco', 'properties_count' => 45, 'image_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=500&q=80', 'video_url' => null],
                        (object)['name' => 'Kuakata', 'tagline' => 'Daughter of Ocean', 'properties_count' => 32, 'image_url' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=500&q=80', 'video_url' => null],
                    ]);
                } else {
                    $bdDestinationsList = $dbDestinations;
                }
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
                        <span class="badge bg-white-20 text-white backdrop-blur rounded-pill px-2.5 py-1 mb-1 fw-bold" style="font-size: 10px; background: rgba(255,255,255,0.25); backdrop-filter: blur(4px);">
                            {{ $dest->properties_count > 0 ? $dest->properties_count . ' Real Hotels' : 'Featured Destination' }}
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
        <h5 class="fw-bold mb-3" style="color:#0f172a;font-size:19px;">
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

{{-- ============================================================ --}}
{{-- ACCOMMODATION PROMOTIONS — Dynamic from Admin panel         --}}
{{-- ============================================================ --}}
@if(isset($accommodationPromos) && $accommodationPromos->isNotEmpty())
<section class="py-4 bg-white">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="font-size:18px;color:#1b2631;">Accommodation Promotions</h5>
            <a href="{{ route('search.index') }}" class="text-decoration-none" style="color:#2067e1;font-size:14px;font-weight:600;">View all <i class="fa-solid fa-chevron-right ms-1" style="font-size:11px;"></i></a>
        </div>
        <div style="position:relative;">
            <div style="position:absolute;right:0;top:0;height:100%;width:100px;background:linear-gradient(to left,#fff 20%,transparent 100%);z-index:5;pointer-events:none;"></div>
            <button onclick="document.getElementById('promoAccCarousel').scrollBy({left:340,behavior:'smooth'})" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);z-index:10;width:36px;height:36px;border-radius:50%;background:#fff;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;">
                <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
            </button>
            <div id="promoAccCarousel" style="display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none;padding-bottom:4px;">
                @foreach($accommodationPromos as $promo)
                <a href="{{ $promo->cta_url }}" class="text-decoration-none flex-shrink-0">
                    <div style="width:340px;height:140px;border-radius:14px;overflow:hidden;position:relative;background:{{ $promo->gradient_css }};color:{{ $promo->text_color }};display:flex;align-items:center;padding:20px 24px;box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                        <div>
                            @if($promo->badge_text)<div style="font-size:11px;font-weight:700;opacity:0.85;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">{{ $promo->icon }} {{ $promo->badge_text }}</div>@endif
                            <div style="font-size:24px;font-weight:900;line-height:1.1;margin-bottom:6px;">{!! nl2br(e($promo->title)) !!}</div>
                            @if($promo->subtitle)<div style="font-size:13px;opacity:0.85;margin-bottom:4px;">{{ $promo->subtitle }}</div>@endif
                            @if($promo->cta_text)<div style="background:{{ $promo->badge_bg ?? '#fbbf24' }};color:#000;font-weight:800;font-size:13px;display:inline-block;padding:3px 10px;border-radius:20px;">{{ $promo->cta_text }}</div>@endif
                        </div>
                        @if($promo->icon)<div style="position:absolute;right:16px;font-size:50px;opacity:0.2;">{{ $promo->icon }}</div>@endif
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
{{-- FLIGHTS & ACTIVITIES PROMOTIONS — Dynamic from Admin panel  --}}
{{-- ============================================================ --}}
@if(isset($flightActivityPromos) && $flightActivityPromos->isNotEmpty())
<section class="py-4 bg-white" style="border-top:1px solid #f1f5f9;">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="font-size:18px;color:#1b2631;">Flights &amp; Activities Promotions</h5>
            <a href="#" class="text-decoration-none" style="color:#2067e1;font-size:14px;font-weight:600;">View all <i class="fa-solid fa-chevron-right ms-1" style="font-size:11px;"></i></a>
        </div>
        <div style="position:relative;">
            <div style="position:absolute;right:0;top:0;height:100%;width:100px;background:linear-gradient(to left,#fff 20%,transparent 100%);z-index:5;pointer-events:none;"></div>
            <button onclick="document.getElementById('promoFlightCarousel').scrollBy({left:340,behavior:'smooth'})" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);z-index:10;width:36px;height:36px;border-radius:50%;background:#fff;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;">
                <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
            </button>
            <div id="promoFlightCarousel" style="display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none;padding-bottom:4px;">
                @foreach($flightActivityPromos as $promo)
                <a href="{{ $promo->cta_url }}" class="text-decoration-none flex-shrink-0">
                    <div style="width:340px;height:140px;border-radius:14px;overflow:hidden;position:relative;background:{{ $promo->gradient_css }};color:{{ $promo->text_color }};display:flex;align-items:center;padding:20px 24px;box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                        <div>
                            @if($promo->badge_text)<div style="font-size:11px;font-weight:700;opacity:0.85;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">{{ $promo->icon }} {{ $promo->badge_text }}</div>@endif
                            <div style="font-size:22px;font-weight:900;line-height:1.2;margin-bottom:6px;">{!! nl2br(e($promo->title)) !!}</div>
                            @if($promo->subtitle)<div style="font-size:13px;font-weight:600;opacity:0.85;margin-bottom:4px;">{{ $promo->subtitle }}</div>@endif
                            @if($promo->cta_text)<div style="margin-top:4px;background:{{ $promo->badge_bg ?? '#fbbf24' }};color:#000;font-weight:800;font-size:13px;display:inline-block;padding:3px 10px;border-radius:20px;">{{ $promo->cta_text }}</div>@endif
                        </div>
                        @if($promo->icon)<div style="position:absolute;right:16px;font-size:50px;opacity:0.2;">{{ $promo->icon }}</div>@endif
                    </div>
                </a>
                @endforeach
                <div style="min-width:80px;flex-shrink:0;"></div>
            </div>
        </div>
    </div>
</section>
@endif

<style>
#promoAccCarousel::-webkit-scrollbar,
#promoFlightCarousel::-webkit-scrollbar,
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
                <h2 class="fw-bold mb-0" style="color:#002d72; font-size: 20px;">Popular Destinations</h2>
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
                            <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $dest->city }}</h6>
                            <small class="text-muted" style="font-size:12px;">
                                @if($dest->property_count_override)
                                    {{ number_format($dest->property_count_override) }} accommodations
                                @elseif(isset($propertyTypeCounts['_total']) && $propertyTypeCounts['_total'] > 0)
                                    {{ $dest->live_property_count ?? 'Many' }} accommodations
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
    'hotel'      => ['label'=>'Hotels',      'emoji'=>'🏨', 'img'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'],
    'resort'     => ['label'=>'Resorts',     'emoji'=>'🏖️', 'img'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=400&q=80'],
    'apartment'  => ['label'=>'Apartments',  'emoji'=>'🏢', 'img'=>'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=400&q=80'],
    'villa'      => ['label'=>'Villas',      'emoji'=>'🏡', 'img'=>'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=400&q=80'],
    'hostel'     => ['label'=>'Hostels',     'emoji'=>'🛏️', 'img'=>'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=400&q=80'],
    'guesthouse' => ['label'=>'Guesthouses', 'emoji'=>'🏠', 'img'=>'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=400&q=80'],
];
$activeTypes = array_filter($propertyTypeConfig, fn($k) => ($propertyTypeCounts[$k] ?? 0) > 0 || true, ARRAY_FILTER_USE_KEY);
@endphp

<section class="py-5 bg-white border-top border-gray-100">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small d-block">Explore Types</span>
                <h2 class="fw-bold mb-0" style="color:#002d72; font-size: 20px;">Browse Accommodations by Property Type</h2>
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
                                @if($count > 0){{ number_format($count) }} properties@else Browse all @endif
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
                <h2 class="fw-bold mb-0" style="color:#002d72; font-size: 20px;">Featured Properties</h2>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">View All</a>
        </div>
        <div class="row g-3">
            @foreach($featuredProperties as $prop)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('hotels.show', $prop->id) }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,0.14)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        @php
                            $cardImg = $prop->primary_image ?: ($prop->image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
                        @endphp
                        <img src="{{ $cardImg }}" style="height:180px;width:100%;object-fit:cover;" alt="{{ $prop->name }}" loading="lazy">
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
{{-- STATS TICKER — Live from DB                                --}}
{{-- ============================================================ --}}
@if(isset($stats) && ($stats['total'] ?? 0) > 0)
<section class="py-4" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">
    <div class="container">
        <div class="row g-4 text-center text-white">
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ number_format($stats['total'] ?? 0) }}+</div>
                <div style="font-size:13px;opacity:0.8;">Properties Listed</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ number_format($stats['destinations'] ?? $destinations->count()) }}+</div>
                <div style="font-size:13px;opacity:0.8;">Destinations</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:28px;font-weight:900;">{{ $stats['avg_rating'] ? number_format($stats['avg_rating'],1) : '4.8' }}</div>
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

{{-- Scroll reveal --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const obs = new IntersectionObserver((entries, o) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('active'); o.unobserve(e.target); }});
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.scroll-reveal').forEach(el => obs.observe(el));
});
{{-- Prime Booking Floating Rewards & QR App Popups (Agoda 1:1) --}}
@include('components.floating-marketing-widgets')

@endsection
