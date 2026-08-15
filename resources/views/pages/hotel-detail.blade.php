@extends('layouts.main', ['activePage' => 'hotels'])

@php
    use App\Services\CurrencyService;
    $gallery = collect();
    if (!empty($property->primary_image)) $gallery->push($property->primary_image);
    if (is_array($property->images)) $gallery = $gallery->merge($property->images);
    $fallbacks = [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=600&q=80',
    ];
    while ($gallery->count() < 5) { $gallery->push($fallbacks[$gallery->count() % count($fallbacks)]); }
    
    $amenitiesList = is_array($property->amenities) && count($property->amenities) > 0
        ? $property->amenities
        : ['Free Wi-Fi', 'Free parking', 'Pets allowed', 'Air conditioning in public area', 'English', 'Internet services'];
    
    $scoreNum = (float)($property->rating_score ?? 8.7);
    $score = number_format($scoreNum, 1);
    $revCount = number_format($property->total_reviews ?: 6);
    $nights = $nights ?? 1;
@endphp

@section('title', e($property->name) . ' — Book Hotels in ' . ($property->city ?: 'Bangladesh') . ' | PRIME BOOKING')
@section('meta_description', 'Book ' . e($property->name) . ' in ' . e($property->city) . '. ' . $revCount . ' verified guest reviews. Best rate guaranteed.')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    /* Agoda 1:1 Ultra-Premium Detail Page Styling */
    .detail-page-wrapper { background: #f8fafc; min-height: 100vh; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
    
    /* Sticky Top Header Bar & Sticky Nav Bar (Full-Width Edge-to-Edge Agoda Match) */
    .agoda-detail-search-bar { background: linear-gradient(180deg, #1d2b45 0%, #152238 100%); padding: 12px 0; border-bottom: 1px solid #334155; position: sticky; top: 0; z-index: 1100; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .agoda-sticky-nav-bar { position: sticky; top: 68px; z-index: 1050; width: 100%; background: #ffffff !important; border-top: 1px solid #e2e8f0 !important; border-bottom: 1px solid #dddfe2 !important; border-left: none !important; border-right: none !important; border-radius: 0 !important; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06); }
    
    .agoda-nav-item { font-size: 13.5px; font-weight: 600; color: #475569; padding: 15px 18px; border-bottom: 3px solid transparent; text-decoration: none; display: inline-block; white-space: nowrap; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
    .agoda-nav-item:hover, .agoda-nav-item.active { color: #2067e1; border-bottom-color: #2067e1; font-weight: 700; }
    
    .agoda-filter-pill-121 { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 14px; font-size: 12.5px; font-weight: 500; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease; }
    .agoda-filter-pill-121:hover { border-color: #2067e1; color: #2067e1; }
    
    /* Card Aesthetics & Agoda Exact Borders */
    .agoda-card-border { background: #ffffff; border: 1px solid #dddfe2 !important; border-radius: 8px !important; box-shadow: none !important; }
    .agoda-card-border:hover { border-color: #cbd5e1 !important; }

    /* Hero Collage Grid Image Zoom */
    .hero-main-img-box { position: relative; height: 360px; overflow: hidden; border-radius: 8px 0 0 8px; cursor: pointer; }
    .hero-thumb-img-box { position: relative; height: 176px; overflow: hidden; cursor: pointer; }
    .hero-main-img-box img, .hero-thumb-img-box img { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    .hero-main-img-box:hover img, .hero-thumb-img-box:hover img { transform: scale(1.035); }
    
    /* Sub-score Pills & Filter Buttons */
    .subscore-pill { background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .agoda-filter-pill { background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 20px; font-size: 12.5px; font-weight: 600; color: #334155; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
    .agoda-filter-pill:hover { border-color: #2067e1; color: #2067e1; background: #f0f7ff; }

    .room-space-card { background: #ffffff; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; flex: 1; min-width: 140px; }
</style>

<div class="detail-page-wrapper">

    {{-- 1. Agoda Subheader Compact Search Bar (Matching Screenshot 1 Exact Parity) --}}
    <div class="agoda-detail-search-bar">
        <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
            <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3.5" style="width: 32%;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 text-secondary" style="height: 44px; border-radius: 8px 0 0 8px;"><i class="fa-solid fa-magnifying-glass" style="color: #475569;"></i></span>
                        <input type="text" name="destination" class="form-control border-0 rounded-end-3 fw-bold text-dark" value="{{ $property->name }}" placeholder="Enter a destination or property" style="height: 44px; font-size: 14px;">
                    </div>
                </div>

                {{-- Combined Check-in & Check-out Single White Box (Agoda 1:1 Parity) --}}
                <div class="col-md-4" style="width: 36%;">
                    <div class="bg-white rounded-3 d-flex align-items-center overflow-hidden" style="height: 44px; cursor: pointer;">
                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="border-right: 1px solid #cbd5e1;">
                            <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                            <div style="line-height: 1.1;">
                                <strong class="d-block text-dark" style="font-size: 12.5px;">14 Aug 2026</strong>
                                <small class="text-muted" style="font-size: 11px;">Friday</small>
                            </div>
                        </div>
                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2">
                            <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                            <div style="line-height: 1.1;">
                                <strong class="d-block text-dark" style="font-size: 12.5px;">15 Aug 2026</strong>
                                <small class="text-muted" style="font-size: 11px;">Saturday</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2.5" style="width: 20%;">
                    <div class="bg-white rounded-3 px-3 py-1.5 d-flex align-items-center justify-content-between" style="height: 44px; cursor: pointer;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-users text-secondary fs-5"></i>
                            <div style="line-height: 1.1;">
                                <strong class="d-block text-dark" style="font-size: 12px;">3 adults,1 child</strong>
                                <small class="text-muted" style="font-size: 11px;">2 rooms</small>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-2" style="width: 12%;">
                    <button type="submit" class="btn text-white w-100 fw-bold rounded-pill shadow-sm" style="background-color: #2067e1; height: 44px; font-size: 14px; letter-spacing: 0.3px;">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div style="max-width: 1240px; margin: 20px auto 0 auto; padding: 0 16px;">

        {{-- 2. Breadcrumb Strip (Screenshot 1 Parity) --}}
        <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 12.5px;">
            <div class="text-secondary">
                <a href="{{ route('home') }}" class="text-secondary text-decoration-none">Home</a> &gt; 
                <a href="{{ route('search.index') }}?destination=Bangladesh" class="text-secondary text-decoration-none">Bangladesh Hotels</a> <small class="text-muted">(1,851)</small> &gt; 
                <a href="{{ route('search.index') }}?destination={{ urlencode($property->city) }}" class="text-secondary text-decoration-none">{{ $property->city ?: 'Dhaka' }} Hotels</a> <small class="text-muted">(538)</small> &gt; 
                <strong class="text-dark">Book {{ $property->name }}</strong>
            </div>
            <div>
                <a href="{{ route('search.index') }}?destination={{ urlencode($property->city) }}" class="text-primary text-decoration-none fw-bold">See all properties in {{ $property->city ?: 'Dhaka' }}</a>
            </div>
        </div>

        {{-- 3. Hero Photo Collage 5-Grid (Screenshot 1 Parity) --}}
        <div class="mb-4 position-relative" style="border-radius: 12px; overflow: hidden;" data-bs-toggle="modal" data-bs-target="#galleryModal">
            <div class="row g-2">
                {{-- Main Feature Image or Embedded Video Tour (Left 60%) --}}
                <div class="col-lg-7">
                    <div class="hero-main-img-box position-relative" style="background:#000000;">
                        @if(!empty($property->video_url))
                            @php
                                $videoUrl = $property->video_url;
                                $isYoutube = false;
                                $youtubeId = '';
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $videoUrl, $match)) {
                                    $isYoutube = true;
                                    $youtubeId = $match[1];
                                }
                            @endphp
                            @if($isYoutube)
                                <iframe src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?autoplay=0&rel=0&modestbranding=1" 
                                        class="w-100 h-100" 
                                        style="border:0; min-height:360px;" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            @else
                                <video controls class="w-100 h-100" style="object-fit:cover; min-height:360px;" poster="{{ $gallery[0] }}">
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                    Your browser does not support HTML5 video.
                                </video>
                            @endif
                        @else
                            <img src="{{ $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">
                        @endif

                        {{-- Floating Camera / Media Pill Button --}}
                        <div class="position-absolute bottom-0 start-0 m-3" style="z-index: 10;">
                            <button class="btn btn-light btn-sm fw-bold rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-2" style="font-size: 12px; background: rgba(255,255,255,0.95);">
                                <i class="fa-solid fa-camera text-primary"></i> See all {{ $gallery->count() }} photos @if(!empty($property->video_url)) • <i class="fa-solid fa-circle-play text-danger"></i> Video Tour @endif
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 4 Grid Photos (Right 2x2 Grid) --}}
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-top-right-radius: 0;">
                                <img src="{{ $gallery[1] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 2">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-top-right-radius: 12px;">
                                <img src="{{ $gallery[2] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 3">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-bottom-right-radius: 0;">
                                <img src="{{ $gallery[3] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 4">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-bottom-right-radius: 12px;">
                                <img src="{{ $gallery[4] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wishlist Favorite Icon --}}
            <div class="position-absolute top-0 end-0 m-3" style="z-index: 15;">
                <form action="{{ route('wishlist.toggle') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <button type="submit" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #ffffff;" title="Save to Wishlist">
                        <i class="fa-regular fa-heart text-dark fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. Sticky Section Anchor Navigation Bar (Exact Agoda White Box - Full Width 1:1 Parity) --}}
    <div class="agoda-sticky-nav-bar mb-4 bg-white">
        <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1 overflow-x-auto">
                    <a href="#overview" class="agoda-nav-item active">Overview</a>
                    <a href="#rooms" class="agoda-nav-item">Rooms</a>
                    <a href="#recommendations" class="agoda-nav-item">Trip recommendations</a>
                    <a href="#facilities" class="agoda-nav-item">Facilities</a>
                    <a href="#reviews" class="agoda-nav-item">Reviews</a>
                    <a href="#location" class="agoda-nav-item">Location</a>
                    <a href="#policies" class="agoda-nav-item">Policies</a>
                    <button class="btn btn-light rounded-circle shadow-xs border p-0 ms-2 d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 11px;" title="Scroll Tabs">
                        <i class="fa-solid fa-chevron-right text-dark"></i>
                    </button>
                </div>
                <div class="d-none d-md-flex align-items-center gap-3 py-2">
                    <div class="text-end">
                        <small class="text-secondary d-inline" style="font-size: 11.5px;">from </small>
                        <strong style="color: #d93025; font-size: 22px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">{{ CurrencyService::format($property->price_per_night) }}</strong>
                    </div>
                    <a href="#rooms" class="btn text-white fw-bold rounded-pill px-4 py-2" style="background-color: #2067e1; font-size: 13.5px; letter-spacing: 0.3px;">
                        VIEW THIS DEAL
                    </a>
                    <a href="#top" class="text-secondary text-decoration-none small ms-2 fw-semibold" style="font-size: 12px; color: #64748b;">
                        Back to Top <i class="fa-solid fa-arrow-up-long ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
        {{-- 5. Main Overview Section Grid (Exact User Screenshot Parity) --}}
        <div id="overview" class="row g-4 mb-4">
            
            {{-- Left Column: Title Card & Guest Highlights --}}
            <div class="col-lg-8">
                
                {{-- Left Card 1: Badges, Title & Address --}}
                <div class="card agoda-card-border p-4 mb-4">
                    {{-- Badges & Stars --}}
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge text-white fw-bold px-2.5 py-1" style="background-color: #0b2545; font-size: 11px; border-radius: 4px;">
                            {{ $property->is_featured ? '⭐ Featured Property' : 'Top Verified Deal' }}
                        </span>
                        <span class="badge bg-white fw-bold px-2.5 py-1" style="color: #16a34a; border: 1px solid #16a34a; font-size: 11px; border-radius: 4px; text-transform: capitalize;">
                            <i class="fa-solid fa-hotel me-1"></i> {{ ucfirst($property->type ?: 'Hotel & Resort') }}
                        </span>
                        <span class="text-warning" style="font-size: 13px; letter-spacing: 1px;">
                            @for($i = 0; $i < ($property->star_rating ?? 5); $i++)★@endfor
                        </span>
                    </div>

                    {{-- Title --}}
                    <h2 class="fw-bold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; line-height: 1.25;">
                        {{ $property->name }}
                    </h2>
                    <p class="text-secondary small mb-0" style="font-size: 13px;">
                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                        {{ $property->address ?: ($property->city . ', Bangladesh') }}
                        @if(!empty($property->nearest_landmark)) • <span class="text-dark fw-semibold"><i class="fa-solid fa-map-pin text-primary me-0.5"></i> {{ $property->nearest_landmark }}</span> @endif - 
                        <a href="#location" class="fw-bold text-decoration-none" style="color: #2067e1;">SEE MAP</a>
                    </p>
                </div>

                {{-- Left Card 2: Highlights from Guests (Screenshot Exact Parity) --}}
                <div class="card agoda-card-border p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Highlights from guests</h6>
                        <a href="#reviews" class="text-decoration-none fw-semibold small" style="color: #2067e1; font-size: 12.5px;">See details</a>
                    </div>
                    
                    <div class="d-flex flex-column gap-2.5">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #fce8e6; color: #d93025;">
                                    <i class="fa-solid fa-ribbon fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Top Value</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #fef7e0; color: #f29900;">
                                    <i class="fa-solid fa-spray-can-sparkles fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Sparkling clean</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #e6f4ea; color: #137333;">
                                    <i class="fa-solid fa-wifi fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Free Wi-Fi in all rooms!</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #e8f0fe; color: #1a73e8;">
                                    <i class="fa-solid fa-snowflake fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Air conditioning</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #f3e8ff; color: #9333ea;">
                                    <i class="fa-solid fa-building-user fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Balcony/terrace</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>
                    </div>
                </div>

                {{-- Space & Rooms Breakdown (Exact Agoda 1:1 Parity) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    {{-- Header with Select room Link --}}
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2.5 mb-3" style="border-color: #e2e8f0 !important;">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Space &amp; Rooms</h5>
                        <a href="#rooms" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 13px;">Select room</a>
                    </div>

                    {{-- Sub-header Specs Row --}}
                    <div class="mb-3">
                        <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">
                            Apartment/Flat <span class="text-secondary fw-normal" style="font-size: 12.5px;">(Room size: 149 m²/1604 ft²)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold" style="font-size: 12.5px;">
                            <span>Max 8 guests</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>2 bedrooms</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>2 beds</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>Kitchen</span>
                        </div>
                    </div>

                    {{-- Horizontal Scrollable Sub-cards with Floating Chevron --}}
                    <div class="position-relative">
                        <div class="d-flex gap-3 overflow-x-auto pb-2 align-items-stretch" style="scrollbar-width: none;">
                            {{-- Bedroom 1 --}}
                            <div class="d-flex flex-column justify-content-between" style="min-width: 210px; width: 210px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Bedroom 1</strong>
                                    <small class="text-secondary d-block" style="font-size: 12px;">1 double bed</small>
                                </div>
                                <div class="text-secondary mt-3">
                                    <i class="fa-solid fa-bed fs-5" style="color: #64748b;"></i>
                                </div>
                            </div>

                            {{-- Bedroom 2 --}}
                            <div class="d-flex flex-column justify-content-between" style="min-width: 210px; width: 210px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Bedroom 2</strong>
                                    <small class="text-secondary d-block" style="font-size: 12px;">1 king bed</small>
                                </div>
                                <div class="text-secondary mt-3">
                                    <i class="fa-solid fa-bed fs-5" style="color: #64748b;"></i>
                                </div>
                            </div>

                            {{-- Bathroom and Toiletries --}}
                            <div class="d-flex flex-column justify-content-between" style="min-width: 240px; width: 240px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Bathroom and toiletries</strong>
                                    <small class="text-secondary d-block" style="font-size: 11.5px; line-height: 1.4;">Cleaning products, Hair dryer, Shower, Toiletries, Towels</small>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-secondary fs-6 mt-3" style="color: #64748b !important;">
                                    <i class="fa-solid fa-pump-soap"></i>
                                    <i class="fa-solid fa-wind"></i>
                                    <i class="fa-solid fa-shower"></i>
                                    <i class="fa-solid fa-bottle-droplet"></i>
                                    <i class="fa-solid fa-mattress-pillow"></i>
                                </div>
                            </div>

                            {{-- Kitchen --}}
                            <div class="d-flex flex-column justify-content-between" style="min-width: 210px; width: 210px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Kitchen</strong>
                                    <small class="text-secondary d-block" style="font-size: 11.5px; line-height: 1.4;">Coffee maker, Free instant coffee, Refrigerator</small>
                                </div>
                                <div class="text-secondary mt-3">
                                    <i class="fa-solid fa-mug-hot fs-5" style="color: #64748b;"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Chevron Scroll Button (Agoda Parity) --}}
                        <button class="btn btn-light rounded-circle shadow-sm border position-absolute top-50 end-0 translate-middle-y me-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; z-index: 10; background: #ffffff;" title="Scroll Right">
                            <i class="fa-solid fa-chevron-right text-dark"></i>
                        </button>
                    </div>
                </div>

                {{-- Facilities Grid (Screenshot 1:1 Agoda Parity) --}}
                <div id="facilities" class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Facilities</h5>
                        <a href="#facilities" class="fw-bold text-decoration-none small" style="color: #2067e1; font-size: 13px;">See all</a>
                    </div>
                    <div class="row g-3">
                        @foreach($amenitiesList as $am)
                        <div class="col-md-3 d-flex align-items-center gap-2" style="font-size: 13.5px; color: #1e293b;">
                            <i class="fa-solid fa-check text-dark" style="font-size: 14px;"></i> <span>{{ $am }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- About Us Section (Screenshot 1:1 Agoda Parity) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">About us</h5>
                    <p class="text-dark mb-1" style="font-size: 13.5px; line-height: 1.6;">
                        Conveniently situated in the Uttara part of Dhaka, this property puts you close to attractions and interesting dining options.
                    </p>
                    <div>
                        <a href="#" class="fw-bold text-decoration-none small" style="color: #2067e1; font-size: 13px;">Read more</a>
                    </div>
                </div>

                {{-- High Demand Urgency Callout Banner (Screenshot 1:1 Agoda Parity) --}}
                <div class="card agoda-card-border mb-4" style="padding: 16px 20px !important; background-color: #fef2f2 !important; border: 1px solid #fee2e2 !important;">
                    <h6 class="fw-bold mb-1" style="color: #d93025; font-size: 15px;">This property is in high demand!</h6>
                    <p class="text-dark mb-0" style="font-size: 13px;">Booked 4 times in 24h</p>
                </div>

            </div>

            {{-- Right Column Sidebar Widgets (Exact Agoda Match) --}}
            <div class="col-lg-4 d-flex flex-column">
                
                {{-- Right Card 1: Review Score Box (Screenshot Exact Parity) --}}
                <div id="reviews" class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16.5px; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $score }} Excellent</h5>
                        <a href="#reviews" class="text-decoration-none fw-bold flex-shrink-0" style="color: #2067e1; font-size: 12.5px;">Read all reviews</a>
                    </div>
                    <div class="text-start mb-3" style="color: #2067e1; font-size: 12.5px; font-weight: 600;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ $revCount }} reviews
                    </div>

                    {{-- Green Sub-scores Pills (Screenshot 1:1 Agoda Exact Match) --}}
                    <div class="d-flex flex-wrap align-items-center gap-1 justify-content-start" style="font-size: 11px;">
                        <span class="px-2 py-1" style="background-color: #e6f4ea; color: #137333; font-weight: 600; border-radius: 4px; font-size: 11px; display: inline-block;">Cleanliness 9.3</span>
                        <span class="px-2 py-1" style="background-color: #e6f4ea; color: #137333; font-weight: 600; border-radius: 4px; font-size: 11px; display: inline-block;">Value for money 9.3</span>
                        <span class="px-2 py-1" style="background-color: #e6f4ea; color: #137333; font-weight: 600; border-radius: 4px; font-size: 11px; display: inline-block;">Location 8.7</span>
                        <span class="px-2 py-1" style="background-color: #e6f4ea; color: #137333; font-weight: 600; border-radius: 4px; font-size: 11px; display: inline-block;">Service 8.7</span>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 15px; height: 15px; background-color: #5f6368; font-size: 9.5px; font-weight: 700; cursor: pointer; flex-shrink: 0;" title="Rating Breakdown Info">i</span>
                    </div>
                </div>

                {{-- Right Card 2: Check-in / Check-out Box (Screenshot Exact Parity) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex justify-content-between text-secondary mb-2.5" style="font-size: 12.5px;">
                        <div>
                            <strong class="d-block text-dark mb-1" style="font-size: 13px;">Check-in:</strong>
                            <span style="font-size: 12px; color: #475569;">12:00 PM to 11:00 PM</span>
                        </div>
                        <div>
                            <strong class="d-block text-dark mb-1" style="font-size: 13px;">Check-out:</strong>
                            <span style="font-size: 12px; color: #475569;">until 12:00 PM</span>
                        </div>
                    </div>
                    <div class="text-end border-top pt-2.5 mt-2">
                        <a href="#policies" class="text-decoration-none fw-bold" style="color: #2067e1; font-size: 12.5px;">See more info &gt;</a>
                    </div>
                </div>

                {{-- Right Card 3: Combined Interactive Map & Closest Landmarks Single Card (Exact Agoda 1:1 Parity) --}}
                <div id="location" class="card agoda-card-border overflow-hidden mb-4 flex-grow-1">
                    {{-- Map Header Banner --}}
                    <div class="position-relative text-center" style="height: 140px; background: #e8f0fe; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
                        {{-- Stylized Agoda Map Canvas --}}
                        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100%" height="100%" fill="#f1f5f9"/>
                            <path d="M 0 40 L 300 40" stroke="#fef08a" stroke-width="14"/>
                            <path d="M 120 0 L 120 140" stroke="#fef08a" stroke-width="12"/>
                            <path d="M 0 100 L 300 100" stroke="#ffffff" stroke-width="8"/>
                            <path d="M 220 0 L 220 140" stroke="#ffffff" stroke-width="8"/>
                            <path d="M 0 0 L 80 80 L 120 140" fill="#dcfce7" opacity="0.6"/>
                            <circle cx="120" cy="40" r="9" fill="#dc2626" stroke="#ffffff" stroke-width="2.5"/>
                            <path d="M 116 38 L 124 38 M 120 34 L 120 42" stroke="#ffffff" stroke-width="2"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <span class="fw-bold text-dark text-decoration-none d-inline-block" style="font-size: 13px; letter-spacing: 0.5px;">
                                SEE MAP
                            </span>
                        </div>
                    </div>

                    {{-- Card Inner Content --}}
                    <div class="p-3">
                        {{-- Location Score --}}
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">8.7 Excellent</h6>
                            <small class="text-secondary" style="font-size: 12px; color: #64748b;">Location rating score</small>
                        </div>
                        
                        {{-- Excellent Location Badge --}}
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold mb-3" style="font-size: 13px;">
                            <i class="fa-solid fa-award text-dark fs-6"></i> Excellent location
                        </div>

                        {{-- Parking Row --}}
                        <div class="d-flex justify-content-between align-items-center border-top border-bottom py-2.5 my-2" style="font-size: 13px; border-color: #e2e8f0 !important;">
                            <span class="text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-square-parking text-secondary fs-5"></i> Parking
                            </span>
                            <strong style="color: #16a34a; font-size: 13px;">FREE</strong>
                        </div>

                        {{-- Closest Landmarks List --}}
                        <div class="pt-2">
                            <h6 class="fw-bold text-dark mb-3" style="font-size: 13.5px;">Closest landmarks</h6>
                            <div class="d-flex flex-column gap-2.5" style="font-size: 12.5px;">
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-bag-shopping me-2 text-dark"></i> Meena Bazar Uttara-6</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">90 m</span>
                                </div>
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-location-dot me-2 text-dark"></i> Uttara Community Hospital</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">240 m</span>
                                </div>
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-location-dot me-2 text-dark"></i> Uttara University</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">270 m</span>
                                </div>
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-bag-shopping me-2 text-dark"></i> Shop n Save</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">270 m</span>
                                </div>
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-location-dot me-2 text-dark"></i> Dentist's Care</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">270 m</span>
                                </div>
                            </div>
                        </div>

                        {{-- See Nearby Places Link (Agoda 1:1 Parity) --}}
                        <div class="text-end border-top pt-2.5 mt-3" style="border-color: #e2e8f0 !important;">
                            <a href="#location" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 12.5px;">See nearby places</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- 6. Select Your Room Section & Filter Pills (Exact Agoda 1:1 Parity) --}}
        <div id="rooms" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">
                    Select your room
                </h4>
                <a href="#rooms" class="fw-bold text-decoration-none small d-flex align-items-center gap-1.5" style="color: #2067e1; font-size: 13px;">
                    <i class="fa-solid fa-award text-dark fs-6"></i> We price match!
                </a>
            </div>

            {{-- Room Filter Container Box (Exact Agoda 1:1 Parity) --}}
            <div class="p-3 mb-3 rounded-3" style="background: #ffffff; border: 1px solid #dddfe2; border-radius: 12px !important;">
                <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700;">Filter</strong>
                <div class="d-flex flex-wrap gap-2">
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-credit-card text-dark"></i> Book without credit card</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-mug-hot text-dark"></i> Breakfast included</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-shield-halved text-dark"></i> Free cancellation</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-ban-smoking text-dark"></i> Non-smoking</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-utensils text-dark"></i> Kitchen</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-building text-dark"></i> Balcony/terrace</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-bed text-dark"></i> Twin Bed</div>
                    <div class="agoda-filter-pill-121"><i class="fa-solid fa-water text-dark"></i> {{ $property->city == "Cox's Bazar Sea Beach" ? 'Sea view' : 'City view' }}</div>
                </div>
            </div>

            {{-- Red Urgency Callout (Agoda 1:1 Parity) --}}
            <div class="p-2.5 px-3 mb-3 text-white rounded d-flex align-items-center gap-2 shadow-xs" style="background:#d93025; font-size:12.5px; font-weight:700; border-radius:6px;">
                <i class="fa-solid fa-clock"></i> <span>Hurry up! 3 room types have already sold out for your dates!</span>
            </div>

            {{-- Available Rooms List (Agoda 1:1 Exact Card Design - Screenshot Parity) --}}
            <div class="d-flex flex-column gap-4">
                @php
                    $roomItems = $property->rooms->isNotEmpty() ? $property->rooms : [
                        (object)[
                            'id' => 101,
                            'name' => 'Superior Deluxe Room',
                            'max_adults' => 2,
                            'max_children' => 1,
                            'bed_type' => '1 King Bed or 2 Twin Beds',
                            'price_per_night' => $property->price_per_night ?: 16,
                            'room_size_sqm' => 46,
                            'view_type' => 'City View',
                            'bathroom_count' => 1,
                            'bathroom_features' => ['Private Bathroom', 'Hot Water Geyser'],
                            'smoking_policy' => 'Non-Smoking',
                            'balcony_type' => 'Private Balcony',
                            'primary_image' => $gallery[0] ?? ''
                        ],
                        (object)[
                            'id' => 102,
                            'name' => 'Executive Ocean Suite',
                            'max_adults' => 3,
                            'max_children' => 2,
                            'bed_type' => '1 King Bed + Living Lounge',
                            'price_per_night' => round(($property->price_per_night ?: 16) * 1.4),
                            'room_size_sqm' => 68,
                            'view_type' => 'Sea View / Ocean Front',
                            'bathroom_count' => 2,
                            'bathroom_features' => ['Private Bathroom', 'Bathtub / Jacuzzi', 'Hot Water Geyser'],
                            'smoking_policy' => 'Non-Smoking',
                            'balcony_type' => 'Terrace',
                            'primary_image' => $gallery[1] ?? ($gallery[0] ?? '')
                        ]
                    ];
                @endphp

                @foreach($roomItems as $rIdx => $room)
                @php
                    $rAdults = $room->max_adults ?? ($rIdx == 0 ? 2 : 3);
                    $rKids = $room->max_children ?? 1;
                    $rSizeStr = $room->room_size_sqm ? ($room->room_size_sqm . ' m²/' . round($room->room_size_sqm * 10.764) . ' ft²') : ($rIdx == 0 ? '46 m²/495 ft²' : '68 m²/732 ft²');
                    $rBedStr = $room->bed_type ?: ($rIdx == 0 ? '1 double bed or 2 single beds' : '1 King Bed + Living Area');
                    $rView = $room->view_type ?: 'City view';
                    $rSmoking = $room->smoking_policy ?: 'Non-smoking';
                    $rBalcony = $room->balcony_type ?: 'Private Balcony';
                    $rBathrooms = $room->bathroom_count ?? 1;
                @endphp
                <div class="card mb-4 overflow-hidden" style="padding: 16px !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="row g-3">
                        
                        {{-- LEFT COLUMN: Room Image, Title, Specs & Amenities Grid (Exact 1:1 Parity) --}}
                        <div class="col-lg-4">
                            <div class="bg-white p-3 rounded-3 h-100" style="border: 1px solid #dddfe2 !important;">
                                {{-- Image Carousel Box --}}
                                <div class="position-relative mb-2 rounded-3 overflow-hidden" style="height: 175px;">
                                    <img src="{{ $room->primary_image ?: ($gallery[$rIdx % count($gallery)] ?? '') }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $room->name }}">
                                    <span class="badge fw-bold position-absolute top-0 start-0 m-2 px-2.5 py-1" style="background: #fde8e8; color: #991b1b; font-size: 11px; border-radius: 4px;">Our last 2!</span>
                                    <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-2 px-2 py-0.5" style="font-size: 10.5px; border-radius: 4px;">1/1</span>
                                    <button class="btn btn-light rounded-circle shadow-sm border p-0 position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; z-index: 5; background: #fff;" title="Next Photo">
                                        <i class="fa-solid fa-chevron-right text-dark"></i>
                                    </button>
                                </div>
                                
                                <a href="#rooms" class="text-decoration-none fw-bold d-block mb-2" style="color: #2067e1; font-size: 13px;">Room photos and details</a>

                                <h4 class="fw-bold text-dark mb-1" style="font-size: 20px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;">{{ $room->name }}</h4>
                                
                                <div class="mb-2" style="font-size: 12.5px; line-height: 1.5; color: #0f172a;">
                                    <strong style="font-weight: 800;">{{ $rSizeStr }}</strong> 
                                    <span class="text-muted mx-1">|</span> 
                                    <strong style="font-weight: 800;">Max {{ $rAdults }} adults</strong> 
                                    <span class="text-muted mx-1">|</span> 
                                    <strong style="font-weight: 800;">{{ $rBedStr }}</strong>
                                </div>

                                <div class="mb-2.5 d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                        <i class="fa-solid fa-ban-smoking text-danger me-1"></i> {{ $rSmoking }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                        <i class="fa-solid fa-mountain-sun text-primary me-1"></i> {{ $rView }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                        <i class="fa-solid fa-shower text-info me-1"></i> {{ $rBathrooms }} Attached Bath
                                    </span>
                                </div>

                                {{-- 2-Column Amenities List (Agoda 1:1 Icon Parity) --}}
                                <div class="row g-2 text-dark mb-3" style="font-size: 11.5px; color: #334155 !important;">
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-wind me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Air conditioning</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-wifi me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Free Wi-Fi</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-building me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>{{ $rBalcony }}</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-tv me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Smart Flat TV</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-pump-soap me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Free Toiletries</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-bottle-water me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Bottled water</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-box me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Refrigerator</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-door-open me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Interconnecting</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><i class="fa-solid fa-utensils me-2 text-secondary" style="font-size: 11.5px; width: 14px; text-align: center;"></i> <span>Dining area</span></div>
                                    <div class="col-6 d-flex align-items-center text-nowrap"><a href="#rooms" class="text-decoration-none fw-bold" style="color: #2067e1; font-size: 12px;">+ 5 more</a></div>
                                </div>

                                {{-- Bottom Carousel Control Bar --}}
                                <div class="d-flex align-items-center gap-2 pt-2 border-top" style="border-color: #e2e8f0 !important;">
                                    <i class="fa-solid fa-caret-left text-secondary fs-6" style="cursor: pointer;"></i>
                                    <div class="flex-fill" style="height: 8px; background: #e2e8f0; border-radius: 4px;">
                                        <div style="width: 75%; height: 100%; background: #64748b; border-radius: 4px;"></div>
                                    </div>
                                    <i class="fa-solid fa-caret-right text-secondary fs-6" style="cursor: pointer;"></i>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: 3 Stacked Rate Offer Sub-cards (Exact 1:1 Parity) --}}
                        <div class="col-lg-8">
                            <div class="d-flex flex-column gap-3">
                                
                                {{-- OFFER CARD 1: Lowest Price Deal (Red Header Banner) --}}
                                <div class="card overflow-hidden shadow-xs" style="background: #ffffff !important; border: 1px solid #c5221f !important; border-radius: 10px !important;">
                                    <div class="px-3 py-1.5 text-white fw-bold" style="background-color: #c5221f; font-size: 13px; letter-spacing: 0.2px;">
                                        Lowest price available!
                                    </div>
                                    <div class="p-3">
                                        <div class="row g-2 align-items-center">
                                            {{-- Sub-col 1: Benefits List --}}
                                            <div class="col-md-5 border-end pe-3">
                                                <div class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 13px;">
                                                    <i class="fa-solid fa-user text-dark me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">{{ $rCap }} adults &amp; 1 child (0-17 years)</span> 
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #334155; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-child text-success me-2" style="width: 14px; text-align: center;"></i> <span>Your kid stays FREE!</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">Cancel for free before Aug 13, 2026</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #16a34a; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span>No credit card needed</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Parking</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Free WiFi</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #c5221f; font-size: 12px;">
                                                    <i class="fa-solid fa-bolt text-danger me-2" style="width: 14px; text-align: center;"></i> <span>Only 1 left</span>
                                                </div>
                                                <div>
                                                    <a href="#rooms" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 12.5px;">See details</a>
                                                </div>
                                            </div>

                                            {{-- Sub-col 2: Price Breakdown --}}
                                            <div class="col-md-4 text-end border-end px-3">
                                                <div class="fw-bold mb-1" style="color: #c5221f; font-size: 12.5px; line-height: 1.2;">
                                                    Cheapest price you've<br>seen!
                                                </div>
                                                <div class="fw-bold mt-1" style="color: #c5221f; font-size: 28px; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;">
                                                    <span style="font-size: 14px; font-weight: 700;">USD</span> {{ round($room->price_per_night) }}
                                                </div>
                                                <small class="text-secondary d-block mt-1" style="font-size: 11px;">Per night before taxes</small>
                                            </div>

                                            {{-- Sub-col 3: Book Button --}}
                                            <div class="col-md-3 text-end ps-2">
                                                <div class="text-dark d-block mb-1.5 fw-bold" style="font-size: 13px;">1 room</div>
                                                <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}" class="btn text-white w-100 fw-bold py-2 shadow-sm rounded-pill d-flex flex-column align-items-center justify-content-center" style="background-color: #2067e1; font-size: 15px; line-height: 1.1;">
                                                    <span>Book</span>
                                                    <small style="font-size: 10.5px; font-weight: 500; opacity: 0.95;">Pay at hotel</small>
                                                </a>
                                                <div class="fw-bold mt-2 text-end" style="color: #16a34a; font-size: 13px;">
                                                    + Free Cancellation
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- OFFER CARD 2: Standard Capacity Deal --}}
                                <div class="card overflow-hidden shadow-xs" style="background: #ffffff !important; border: 1px solid #dddfe2 !important; border-radius: 10px !important;">
                                    <div class="p-3">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-5 border-end pe-3">
                                                <div class="fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 13px;">
                                                    <i class="fa-solid fa-user text-dark me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">{{ max(1, $rCap - 2) }} adults</span> 
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #334155; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">Cancel for free before Aug 13, 2026</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #16a34a; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span>No credit card needed</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Parking</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Free WiFi</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #c5221f; font-size: 12px;">
                                                    <i class="fa-solid fa-bolt text-danger me-2" style="width: 14px; text-align: center;"></i> <span>Only 1 left</span>
                                                </div>
                                                <div>
                                                    <a href="#rooms" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 12.5px;">See details</a>
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-end border-end px-3">
                                                <div class="fw-bold mt-2" style="color: #c5221f; font-size: 28px; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;">
                                                    <span style="font-size: 14px; font-weight: 700;">USD</span> {{ round($room->price_per_night * 1.3) }}
                                                </div>
                                                <small class="text-secondary d-block mt-1" style="font-size: 11px;">Per night before taxes</small>
                                            </div>

                                            <div class="col-md-3 text-end ps-2">
                                                <div class="text-dark d-block mb-1.5 fw-bold" style="font-size: 13px;">1 room</div>
                                                <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}" class="btn text-white w-100 fw-bold py-2 shadow-sm rounded-pill d-flex flex-column align-items-center justify-content-center" style="background-color: #2067e1; font-size: 15px; line-height: 1.1;">
                                                    <span>Book</span>
                                                    <small style="font-size: 10.5px; font-weight: 500; opacity: 0.95;">Pay at hotel</small>
                                                </a>
                                                <div class="fw-bold mt-2 text-end" style="color: #16a34a; font-size: 13px;">
                                                    + Free Cancellation
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- OFFER CARD 3: 1 Adult Exceeded / Lowest in 180 days Deal --}}
                                <div class="card overflow-hidden shadow-xs" style="background: #ffffff !important; border: 1px solid #dddfe2 !important; border-radius: 10px !important;">
                                    <div class="p-3">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-5 border-end pe-3">
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #c5221f; font-size: 13px;">
                                                    <i class="fa-solid fa-user me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">1 adult Room capacity exceeded</span> 
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #c5221f; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">Cancel for free before Aug 13, 2026</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 13px; height: 13px; background-color: #16a34a; font-size: 8.5px; font-weight: 700;" title="Info">i</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #16a34a; font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-success me-2" style="width: 14px; text-align: center;"></i> <span>No credit card needed</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Parking</span>
                                                </div>
                                                <div class="text-dark mb-1 d-flex align-items-center" style="font-size: 12.5px;">
                                                    <i class="fa-solid fa-check text-dark me-2" style="width: 14px; text-align: center;"></i> <span>Free WiFi</span>
                                                </div>
                                                <div class="fw-bold mb-1 d-flex align-items-center" style="color: #c5221f; font-size: 12px;">
                                                    <i class="fa-solid fa-bolt text-danger me-2" style="width: 14px; text-align: center;"></i> <span>Only 1 left</span>
                                                </div>
                                                <div>
                                                    <a href="#rooms" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 12.5px;">See details</a>
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-end border-end px-3">
                                                <div class="fw-bold" style="color: #c5221f; font-size: 28px; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;">
                                                    <span style="font-size: 14px; font-weight: 700;">USD</span> {{ round($room->price_per_night * 0.6) }}
                                                </div>
                                                <small class="fw-bold d-block mt-1" style="color: #c5221f; font-size: 12px;">
                                                    🔥 Lowest in 180 days
                                                </small>
                                                <small class="text-secondary d-block mt-0.5" style="font-size: 11px;">Per night before taxes</small>
                                            </div>

                                            <div class="col-md-3 text-end ps-2">
                                                <div class="text-dark d-block mb-1.5 fw-bold" style="font-size: 13px;">1 room</div>
                                                <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}" class="btn text-white w-100 fw-bold py-2 shadow-sm rounded-pill d-flex flex-column align-items-center justify-content-center" style="background-color: #2067e1; font-size: 15px; line-height: 1.1;">
                                                    <span>Book</span>
                                                    <small style="font-size: 10.5px; font-weight: 500; opacity: 0.95;">Pay at hotel</small>
                                                </a>
                                                <div class="fw-bold mt-2 text-end" style="color: #16a34a; font-size: 13px;">
                                                    + Free Cancellation
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
          {{-- 7. Plan your journey to your hotel (Agoda 1:1 Screenshot Parity) --}}
        <div class="mb-4 mt-2">
            <h4 class="fw-bold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 800;">
                Plan your journey to your hotel
            </h4>
            <p class="text-secondary small mb-3.5" style="font-size: 13px; color: #64748b;">Book your ride in advance for a hassle-free trip</p>

            <div class="row g-3">
                {{-- Card 1: Airport Transfer --}}
                <div class="col-md-6">
                    <div class="card border rounded-3 p-3.5 bg-white h-100 d-flex flex-column justify-content-between" style="border: 1px solid #dddfe2 !important; border-radius: 12px !important;">
                        <div>
                            {{-- Chauffeur & Car Graphic --}}
                            <div class="mb-2" style="height: 55px;">
                                <svg width="110" height="55" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Chauffeur Standing -->
                                    <circle cx="85" cy="14" r="5" fill="#1e293b"/>
                                    <path d="M85 20 C80 22 78 28 78 38 L81 38 L83 27 L87 27 L89 38 L92 38 C92 28 90 22 85 20 Z" fill="#1e293b"/>
                                    <path d="M82 20 L88 20 L86 26 L84 26 Z" fill="#ffffff"/>
                                    <!-- Black Sedan Car -->
                                    <path d="M5 38 L15 28 C20 24 35 23 48 24 L62 28 C68 30 72 34 72 38 L74 44 C74 46 72 48 70 48 L8 48 C6 48 4 46 4 44 Z" fill="#0f172a"/>
                                    <!-- Windows -->
                                    <path d="M18 29 L26 25 L45 25 L45 29 Z" fill="#64748b" opacity="0.6"/>
                                    <path d="M48 25 L60 29 L48 29 Z" fill="#64748b" opacity="0.6"/>
                                    <!-- Wheels -->
                                    <circle cx="20" cy="46" r="7" fill="#334155"/>
                                    <circle cx="20" cy="46" r="3" fill="#cbd5e1"/>
                                    <circle cx="58" cy="46" r="7" fill="#334155"/>
                                    <circle cx="58" cy="46" r="3" fill="#cbd5e1"/>
                                    <!-- Headlight -->
                                    <circle cx="70" cy="40" r="2" fill="#fef08a"/>
                                </svg>
                            </div>

                            <h6 class="fw-bold text-dark mb-1" style="font-size: 15px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">Book your airport transfer</h6>
                            <p class="text-secondary mb-3" style="font-size: 12px; color: #64748b;">Get to your hotel easily and securely</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <span class="text-secondary fw-normal d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                14 Aug · 3 Adults, 1 Child 
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 11px; color: #2067e1 !important;"></i>
                            </span>
                            <a href="{{ route('services') }}?type=transfer" class="btn fw-bold rounded-pill px-3.5 py-1.5" style="border: 1px solid #cbd5e1; color: #2067e1; font-size: 13px; background: #ffffff;">
                                Search
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Rent a Car --}}
                <div class="col-md-6">
                    <div class="card border rounded-3 p-3.5 bg-white h-100 d-flex flex-column justify-content-between" style="border: 1px solid #dddfe2 !important; border-radius: 12px !important;">
                        <div>
                            {{-- Silver Sedan Graphic --}}
                            <div class="mb-2" style="height: 55px;">
                                <svg width="110" height="55" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Silver Sedan Car -->
                                    <path d="M8 40 L22 28 C30 22 55 22 75 28 L95 35 C102 38 106 40 106 44 L104 47 C104 49 102 50 99 50 L12 50 C9 50 7 49 7 47 Z" fill="#94a3b8"/>
                                    <path d="M12 39 L24 29 C32 24 55 24 72 29 L88 35 Z" fill="#cbd5e1"/>
                                    <!-- Windows -->
                                    <path d="M28 28 L40 24 L60 24 L60 28 Z" fill="#334155" opacity="0.7"/>
                                    <path d="M63 24 L75 28 L63 28 Z" fill="#334155" opacity="0.7"/>
                                    <!-- Wheels -->
                                    <circle cx="26" cy="48" r="8" fill="#1e293b"/>
                                    <circle cx="26" cy="48" r="4" fill="#e2e8f0"/>
                                    <circle cx="82" cy="48" r="8" fill="#1e293b"/>
                                    <circle cx="82" cy="48" r="4" fill="#e2e8f0"/>
                                    <!-- Headlight -->
                                    <path d="M98 40 L104 42 L98 44 Z" fill="#fef08a"/>
                                </svg>
                            </div>

                            <h6 class="fw-bold text-dark mb-1" style="font-size: 15px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">Rent a car</h6>
                            <p class="text-secondary mb-3" style="font-size: 12px; color: #64748b;">Find an ideal ride for your trip</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <span class="text-secondary fw-normal d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                14 Aug - 15 Aug 
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 11px; color: #2067e1 !important;"></i>
                            </span>
                            <a href="{{ route('services') }}?type=car_rental" class="btn fw-bold rounded-pill px-3.5 py-1.5" style="border: 1px solid #cbd5e1; color: #2067e1; font-size: 13px; background: #ffffff;">
                                Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
              {{-- 8. Insider Deals Sign-in Banner (Agoda 1:1 Screenshot Parity) --}}
        <div class="p-3 mb-4" style="background: #f4f4f5; border-radius: 4px !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    {{-- Slanted Black Tag Icon --}}
                    <div class="d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <svg width="40" height="40" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="8" width="30" height="26" rx="5" transform="rotate(-18 22 22)" fill="#18181b"/>
                            <circle cx="15" cy="18" r="2.5" fill="#ffffff"/>
                            <path d="M22 20 L28 18 M23 25 L29 27 M25 21 L31 23" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <strong class="d-block" style="font-size: 14.5px; font-weight: 800; color: #d97706; font-family: 'Plus Jakarta Sans', sans-serif;">
                            Up to 30% off with Prime Booking Insider Deals!
                        </strong>
                        <span class="text-dark fw-bold d-block" style="font-size: 12.5px; color: #18181b !important;">
                            Prices drop the moment you sign in!
                        </span>
                    </div>
                </div>
                <div>
                    @guest
                    <button type="button" class="btn text-dark fw-bold border-0 p-0 bg-transparent d-flex align-items-center gap-1.5" style="font-size: 13.5px; color: #000000 !important;" data-bs-toggle="modal" data-bs-target="#agodaAuthModal">
                        <span>Sign in now</span>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white ms-1" style="width: 22px; height: 22px; font-size: 10px;">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    </button>
                    @else
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 d-flex align-items-center gap-1" style="font-size: 12px; border-radius: 20px;">
                        <i class="fa-solid fa-circle-check"></i> Insider Member Unlocked
                    </span>
                    @endguest
                </div>
            </div>
        </div>   </div>
        {{-- 9. More About Property Feature Card (Screenshot Parity) --}}
        <div class="card border-0 shadow-xs rounded-3 overflow-hidden bg-white mb-4">
            <div class="position-relative" style="height: 220px;">
                <img src="{{ $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">
                <div class="position-absolute bottom-0 start-0 m-3 p-3 text-white rounded-3" style="background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px); max-width: 450px;">
                    <small class="text-uppercase fw-semibold d-block text-white-50" style="font-size: 11px; letter-spacing: 0.5px;">More about</small>
                    <h4 class="fw-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">{{ $property->name }}</h4>
                </div>
            </div>
            <div class="p-4">
                <h5 class="fw-bold text-dark mb-2" style="font-size: 16.5px;">Comfortable Living</h5>
                <p class="text-secondary mb-2" style="font-size: 13.5px; line-height: 1.6;">
                    <strong>{{ $property->name }}</strong> offers a comfortable stay in {{ $property->city ?: 'Dhaka' }} featuring free Wi-Fi, air-conditioning, and private balcony. {{ $property->description }}
                </p>
                <a href="#overview" class="text-primary fw-bold text-decoration-none small">Show more <i class="fa-solid fa-chevron-down ms-1"></i></a>
            </div>
        </div>

        {{-- 10. Categorized Amenities & Facilities Section (Screenshot Parity) --}}
        <div id="facilities" class="card border-0 shadow-xs rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Amenities and facilities</h5>
                <span class="text-primary fw-bold" style="font-size: 13px;">Excellent 8.0 <small class="text-secondary fw-normal">Facilities</small></span>
            </div>
            
            <div class="row g-4" style="font-size: 13px;">
                {{-- Left Sub-column --}}
                <div class="col-md-5">
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">For the kids</strong>
                        <div class="d-flex flex-column gap-2 text-secondary">
                            <div><i class="fa-solid fa-baby me-2 text-muted"></i> Babysitting service</div>
                            <div><i class="fa-solid fa-people-roof me-2 text-muted"></i> Family room</div>
                            <div><i class="fa-solid fa-shapes me-2 text-muted"></i> Kids club</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">Languages spoken</strong>
                        <div class="d-flex align-items-center gap-2 text-secondary">
                            <span class="fi fi-gb me-1"></span> 🇬🇧 English
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">Things to do, ways to relax</strong>
                        <div class="d-flex flex-column gap-2 text-secondary">
                            <div><i class="fa-solid fa-person-swimming me-2 text-info"></i> Swimming pool [indoor]</div>
                            <div><i class="fa-solid fa-water-ladder me-2 text-info"></i> Swimming pool [outdoor]</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">Services and conveniences</strong>
                        <div class="d-flex flex-column gap-2 text-secondary">
                            <div><i class="fa-solid fa-wind me-2 text-secondary"></i> Air conditioning in public area</div>
                            <div><i class="fa-solid fa-smoking me-2 text-secondary"></i> Smoking area</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">Access</strong>
                        <div class="d-flex flex-column gap-2 text-secondary">
                            <div><i class="fa-solid fa-paw me-2 text-success"></i> Pets allowed</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong class="d-block text-dark mb-2" style="font-size: 14px;">Getting around</strong>
                        <div class="d-flex flex-column gap-2 text-secondary">
                            <div><i class="fa-solid fa-square-parking me-2 text-primary"></i> Car park [free of charge]</div>
                            <div><i class="fa-solid fa-taxi me-2 text-primary"></i> Taxi service</div>
                            <div><i class="fa-solid fa-van-shuttle me-2 text-primary"></i> Airport transfer service</div>
                        </div>
                    </div>
                </div>

                {{-- Right Sub-column --}}
                <div class="col-md-7 border-start ps-md-4">
                    <strong class="d-block text-dark mb-3" style="font-size: 14px;">Available in all rooms</strong>
                    <div class="row g-2 text-secondary" style="font-size: 12.5px;">
                        <div class="col-6"><i class="fa-solid fa-elevator me-2 text-primary"></i> Accessible by elevator</div>
                        <div class="col-6"><i class="fa-solid fa-plug me-2 text-secondary"></i> Adapter</div>
                        <div class="col-6"><i class="fa-solid fa-snowflake me-2 text-info"></i> Air conditioning</div>
                        <div class="col-6"><i class="fa-solid fa-wind me-2 text-secondary"></i> Air purifier</div>
                        <div class="col-6"><i class="fa-solid fa-clock me-2 text-secondary"></i> Alarm clock</div>
                        <div class="col-6"><i class="fa-solid fa-baby-carriage me-2 text-secondary"></i> Baby amenities (upon request)</div>
                        <div class="col-6"><i class="fa-solid fa-shield-cat me-2 text-secondary"></i> Baby safety gates</div>
                        <div class="col-6"><i class="fa-solid fa-sun me-2 text-warning"></i> Balcony/terrace</div>
                        <div class="col-6"><i class="fa-solid fa-sensor-on me-2 text-secondary"></i> Carbon monoxide detector</div>
                        <div class="col-6"><i class="fa-solid fa-spray-can-sparkles me-2 text-success"></i> Cleaning products</div>
                        <div class="col-6"><i class="fa-solid fa-shirt me-2 text-secondary"></i> Closet &amp; Clothes rack</div>
                        <div class="col-6"><i class="fa-solid fa-mug-saucer me-2 text-secondary"></i> Coffee/tea maker</div>
                        <div class="col-6"><i class="fa-solid fa-desktop me-2 text-secondary"></i> Desk &amp; Dining table</div>
                        <div class="col-6"><i class="fa-solid fa-fire-extinguisher me-2 text-danger"></i> Fire extinguisher</div>
                        <div class="col-6"><i class="fa-solid fa-bottle-water me-2 text-primary"></i> Free bottled water</div>
                        <div class="col-6"><i class="fa-solid fa-utensils me-2 text-secondary"></i> Full kitchen &amp; Kitchenette</div>
                        <div class="col-6"><i class="fa-solid fa-wind me-2 text-info"></i> Hair dryer</div>
                        <div class="col-6"><i class="fa-solid fa-pump-medical me-2 text-success"></i> Hand sanitizer</div>
                        <div class="col-6"><i class="fa-solid fa-temperature-arrow-up me-2 text-danger"></i> Heating</div>
                        <div class="col-6"><i class="fa-solid fa-feather me-2 text-secondary"></i> Hypoallergenic</div>
                        <div class="col-6"><i class="fa-solid fa-fan me-2 text-info"></i> Individual air conditioning</div>
                        <div class="col-6"><i class="fa-solid fa-shirt me-2 text-secondary"></i> Ironing facilities</div>
                        <div class="col-6"><i class="fa-solid fa-mattress-pillow me-2 text-secondary"></i> Premium Linens</div>
                        <div class="col-6"><i class="fa-solid fa-lock me-2 text-secondary"></i> Locker</div>
                        <div class="col-6"><i class="fa-solid fa-microchip me-2 text-secondary"></i> Microwave</div>
                        <div class="col-6"><i class="fa-solid fa-snowflake me-2 text-primary"></i> Refrigerator</div>
                        <div class="col-6"><i class="fa-solid fa-shield-halved me-2 text-success"></i> Safety/security feature</div>
                        <div class="col-6"><i class="fa-solid fa-tv me-2 text-primary"></i> Satellite/cable channels</div>
                        <div class="col-6"><i class="fa-solid fa-couch me-2 text-secondary"></i> Seating area</div>
                        <div class="col-6"><i class="fa-solid fa-chair me-2 text-secondary"></i> Separate dining area</div>
                        <div class="col-6"><i class="fa-solid fa-shower me-2 text-primary"></i> Shower</div>
                        <div class="col-6"><i class="fa-solid fa-bed me-2 text-secondary"></i> Sleep comfort items</div>
                        <div class="col-6"><i class="fa-solid fa-shoe-prints me-2 text-secondary"></i> Slippers</div>
                        <div class="col-6"><i class="fa-solid fa-smog me-2 text-danger"></i> Smoke detector</div>
                        <div class="col-6"><i class="fa-solid fa-plug-circle-bolt me-2 text-primary"></i> Socket near the bed</div>
                        <div class="col-6"><i class="fa-solid fa-couch me-2 text-secondary"></i> Sofa</div>
                        <div class="col-6"><i class="fa-solid fa-phone me-2 text-secondary"></i> Telephone</div>
                        <div class="col-6"><i class="fa-solid fa-border-all me-2 text-secondary"></i> Tile/marble flooring</div>
                        <div class="col-6"><i class="fa-solid fa-toilet-paper me-2 text-secondary"></i> Toiletries &amp; Towels</div>
                        <div class="col-6"><i class="fa-solid fa-trash-can me-2 text-secondary"></i> Trash cans</div>
                        <div class="col-6"><i class="fa-solid fa-bell me-2 text-secondary"></i> Wake-up service</div>
                        <div class="col-6"><i class="fa-solid fa-wine-glass me-2 text-secondary"></i> Wine glasses</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 11. Location Section Card (Screenshot Parity) --}}
        <div id="location" class="card border-0 shadow-xs rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Location</h5>
                    <small class="text-secondary" style="font-size: 12.5px;">{{ $property->address ?: 'House#06 Road-21, Sector 4, Uttara, Dhaka, Bangladesh, 1230' }}</small>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-primary" style="font-size: 13px;">Excellent 8.7 <small class="text-secondary fw-normal">Location</small></span>
                </div>
            </div>

            <div class="text-center py-4 bg-light rounded-3 my-2" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <svg width="180" height="120" viewBox="0 0 200 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 110 C 60 70, 140 70, 180 110" stroke="#cbd5e1" stroke-width="3" stroke-dasharray="4 4"/>
                    <rect x="75" y="40" width="50" height="60" rx="4" fill="#e2e8f0"/>
                    <rect x="85" y="50" width="10" height="12" fill="#cbd5e1"/>
                    <rect x="105" y="50" width="10" height="12" fill="#cbd5e1"/>
                    <rect x="85" y="70" width="10" height="12" fill="#cbd5e1"/>
                    <rect x="105" y="70" width="10" height="12" fill="#cbd5e1"/>
                    <circle cx="100" cy="30" r="14" fill="#dc2626"/>
                    <circle cx="100" cy="30" r="5" fill="#ffffff"/>
                </svg>
                <div class="mt-2">
                    <a href="https://maps.google.com/?q={{ urlencode($property->address ?: $property->name) }}" target="_blank" class="btn text-white fw-bold px-4 py-2 rounded-pill shadow-xs" style="background-color: #2067e1; font-size: 13px;">
                        Open Interactive Google Map <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- 12. Property Policies Section Card (Screenshot Parity) --}}
        <div id="policies" class="card border-0 shadow-xs rounded-3 p-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Property policies</h5>
            
            <div class="mb-4">
                <strong class="d-block text-dark mb-2" style="font-size: 14px;">Others</strong>
                <ul class="text-secondary mb-2 ps-3" style="font-size: 13px; line-height: 1.6;">
                    <li class="mb-1">When booking more than 5 rooms, different policies and additional supplements may apply.</li>
                    <li>Extra beds, if available, are dependent on the room you choose. Please ask the property for more details.</li>
                </ul>
            </div>

            <div class="border-top pt-4 mb-4">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">Some helpful facts</h6>
                <strong class="d-block text-dark mb-2" style="font-size: 13.5px;">Check-in/Check-out</strong>
                <div class="row g-2 text-secondary" style="font-size: 13px;">
                    <div class="col-md-6"><i class="fa-solid fa-key me-2 text-muted"></i> Check-in from: <strong>12:00 PM</strong></div>
                    <div class="col-md-6"><i class="fa-solid fa-key me-2 text-muted"></i> Check-out until: <strong>12:00 PM</strong></div>
                    <div class="col-md-6"><i class="fa-solid fa-key me-2 text-muted"></i> Check-out from: <strong>08:00 AM</strong></div>
                    <div class="col-md-6"><i class="fa-solid fa-key me-2 text-muted"></i> Check-in until: <strong>11:00 PM</strong></div>
                </div>
            </div>

            <div class="border-top pt-4">
                <h6 class="fw-bold text-dark mb-2" style="font-size: 15px;">Property announcements</h6>
                <p class="text-secondary mb-0" style="font-size: 13px;">Managed by a private host</p>
        {{-- 13. Reviews Section Card (Screenshot Parity) --}}
        <div id="reviews" class="card border-0 shadow-xs rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 19px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Reviews of {{ $property->name }} from real guests <i class="fa-solid fa-circle-info text-muted fs-6"></i>
                    </h5>
                </div>
                <div class="text-end">
                    <small class="text-secondary d-block" style="font-size: 11px;">Verified reviews provided by</small>
                    <span class="fw-bold text-primary" style="font-size: 13px;">agoda · <span class="text-info">Booking.com</span></span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Score Summary Box --}}
                <div class="col-md-3 border-end pe-md-4">
                    <small class="text-secondary d-block mb-1" style="font-size: 11px;">Rating via Booking.com</small>
                    <div class="d-flex align-items-baseline gap-1">
                        <h2 class="fw-bold text-primary mb-0" style="font-size: 34px;">8.9</h2>
                        <span class="text-muted" style="font-size: 14px;">/10</span>
                    </div>
                    <strong class="d-block text-dark mt-1" style="font-size: 15px;">Excellent</strong>
                    <small class="text-primary fw-bold" style="font-size: 12px;"><i class="fa-solid fa-check-circle me-1"></i> From {{ $revCount }} reviews</small>
                </div>

                {{-- Green Sub-score Progress Bars --}}
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Cleanliness</span>
                                <strong class="text-dark">8.8</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 88%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Facilities</span>
                                <strong class="text-dark">8.8</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 88%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Location</span>
                                <strong class="text-dark">10.0</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Room comfort and quality</span>
                                <strong class="text-dark">8.8</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 88%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Service</span>
                                <strong class="text-dark">7.5</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>Value for money</span>
                                <strong class="text-dark">10.0</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Select Dropdowns --}}
            <div class="row g-3 border-top pt-3">
                <div class="col-md-6">
                    <label class="form-label text-secondary small mb-1" style="font-size: 11.5px;">Guest Type</label>
                    <select class="form-select text-dark font-semibold" style="font-size: 13px; height: 40px;">
                        <option selected>All guests ({{ $revCount }})</option>
                        <option>Solo travelers</option>
                        <option>Couples</option>
                        <option>Families with young children</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small mb-1" style="font-size: 11.5px;">Source</label>
                    <select class="form-select text-dark font-semibold" style="font-size: 13px; height: 40px;">
                        <option selected>Booking.com Reviews ({{ $revCount }})</option>
                        <option>Prime Booking Verified Reviews</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 13. Property Facilities & Amenities (Full Dynamic Grid) --}}
        <div id="facilities" class="card agoda-card-border p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px;">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Facilities &amp; Amenities
                </h5>
                <span class="badge bg-light text-secondary border" style="font-size: 12px;">Verified Hotel Features</span>
            </div>

            @php
                $amenitiesList = is_array($property->amenities) ? $property->amenities : (json_decode($property->amenities, true) ?: []);
                $amenityMap = [
                    'wifi' => ['icon' => 'fa-wifi', 'label' => 'Free Wi-Fi in all rooms & areas'],
                    'pool' => ['icon' => 'fa-person-swimming', 'label' => 'Swimming Pool'],
                    'parking' => ['icon' => 'fa-square-parking', 'label' => 'Free Guest Parking'],
                    'ac' => ['icon' => 'fa-snowflake', 'label' => 'Air Conditioning'],
                    'restaurant' => ['icon' => 'fa-utensils', 'label' => 'Multi-Cuisine Restaurant'],
                    'breakfast' => ['icon' => 'fa-mug-hot', 'label' => 'Free Daily Breakfast'],
                    'gym' => ['icon' => 'fa-dumbbell', 'label' => 'Fitness Center / Gym'],
                    'beachfront' => ['icon' => 'fa-water', 'label' => 'Beachfront / Ocean View'],
                    'transfer' => ['icon' => 'fa-van-shuttle', 'label' => 'Airport Shuttle Transfer'],
                    'frontdesk' => ['icon' => 'fa-headset', 'label' => '24/7 Front Desk Reception'],
                    'elevator' => ['icon' => 'fa-elevator', 'label' => 'Elevator / Lift Access'],
                    'spa' => ['icon' => 'fa-spa', 'label' => 'Spa & Wellness Center'],
                ];
            @endphp

            @if(!empty($amenitiesList) && count($amenitiesList) > 0)
            <div class="row g-3">
                @foreach($amenitiesList as $amKey)
                    @php
                        $cleanKey = strtolower(trim($amKey));
                        $item = $amenityMap[$cleanKey] ?? ['icon' => 'fa-circle-check', 'label' => ucfirst(str_replace('_', ' ', $amKey))];
                    @endphp
                    <div class="col-md-3 col-6">
                        <div class="p-2.5 bg-light rounded-3 border d-flex align-items-center gap-2.5 h-100">
                            <i class="fa-solid {{ $item['icon'] }} text-primary" style="width: 18px; font-size: 14px;"></i>
                            <span class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $item['label'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="row g-3">
                <div class="col-md-3 col-6"><div class="p-2.5 bg-light rounded-3 border d-flex align-items-center gap-2"><i class="fa-solid fa-wifi text-primary"></i> <span class="fw-semibold text-dark" style="font-size:12.5px;">Free Wi-Fi</span></div></div>
                <div class="col-md-3 col-6"><div class="p-2.5 bg-light rounded-3 border d-flex align-items-center gap-2"><i class="fa-solid fa-snowflake text-primary"></i> <span class="fw-semibold text-dark" style="font-size:12.5px;">Air Conditioning</span></div></div>
                <div class="col-md-3 col-6"><div class="p-2.5 bg-light rounded-3 border d-flex align-items-center gap-2"><i class="fa-solid fa-square-parking text-primary"></i> <span class="fw-semibold text-dark" style="font-size:12.5px;">Free Parking</span></div></div>
                <div class="col-md-3 col-6"><div class="p-2.5 bg-light rounded-3 border d-flex align-items-center gap-2"><i class="fa-solid fa-headset text-primary"></i> <span class="fw-semibold text-dark" style="font-size:12.5px;">24/7 Front Desk</span></div></div>
            </div>
            @endif
        </div>

        {{-- 14. Check in. Step out. Map Experience Banner (Screenshot Parity) --}}
        <div class="card border-0 shadow-xs rounded-3 overflow-hidden bg-white mb-4">
            <div class="row g-0">
                <div class="col-lg-5 p-4 d-flex flex-column justify-content-center bg-white">
                    <h4 class="fw-bold text-dark mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px;">
                        Check in. Step out.
                    </h4>
                    <p class="text-secondary small mb-3" style="font-size: 13px;">
                        Experiences on Prime Booking. Because your room is just the beginning.
                    </p>
                    <div class="text-success fw-bold small mb-4" style="font-size: 12.5px;">
                        <i class="fa-solid fa-location-dot me-1"></i> Excellent location
                    </div>
                    <div>
                        <a href="{{ route('services') }}" class="btn text-white fw-bold px-4 py-2.5 rounded-pill shadow-xs" style="background-color: #2067e1; font-size: 13px;">
                            SHOW EXPERIENCES
                        </a>
                    </div>
                </div>
                <div class="col-lg-7 position-relative" style="min-height: 200px; background: #e2e8f0;">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100%" height="100%" fill="#cbd5e1"/>
                        <path d="M 0 50 L 300 50 L 300 150 L 0 150 Z" fill="#93c47d" opacity="0.6"/>
                        <line x1="50" y1="0" x2="50" y2="200" stroke="#ffffff" stroke-width="6"/>
                        <line x1="180" y1="0" x2="180" y2="200" stroke="#ffffff" stroke-width="6"/>
                        <line x1="0" y1="100" x2="400" y2="100" stroke="#ffffff" stroke-width="6"/>
                    </svg>

                    {{-- Property Pin Tooltip --}}
                    <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 10;">
                        <div class="bg-white rounded-3 shadow-md p-2 d-flex align-items-center gap-2" style="border: 1px solid #cbd5e1; width: 260px;">
                            <img src="{{ $gallery[0] }}" class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;" alt="Tooltip Thumb">
                            <div>
                                <strong class="d-block text-dark text-truncate" style="font-size: 12px; max-width: 180px;">{{ $property->name }}</strong>
                                <small class="text-primary fw-bold" style="font-size: 11px;">| Excellent 8.9</small>
                            </div>
                        </div>
                        <div class="text-center mt-1">
                            <span class="badge rounded-circle p-2 bg-primary text-white shadow-sm" style="font-size: 14px; background-color: #2067e1 !important;">
                                <i class="fa-solid fa-bed"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 15. Top Destinations Section (Screenshot Parity) --}}
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Top destinations</h5>
            <div class="d-flex gap-3 overflow-x-auto pb-2">
                @php
                    $destinations = [
                        ['name' => 'United States', 'img' => 'https://images.unsplash.com/photo-1506146332389-18140dc7b2fb?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Malaysia', 'img' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'India', 'img' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Philippines', 'img' => 'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Indonesia', 'img' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Singapore', 'img' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Thailand', 'img' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'China', 'img' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?auto=format&fit=crop&w=400&q=80'],
                    ];
                @endphp
                @foreach($destinations as $dest)
                <a href="{{ route('search.index') }}?destination={{ urlencode($dest['name']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none text-white flex-shrink-0" style="width: 170px; height: 120px; position: relative;">
                    <img src="{{ $dest['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $dest['name'] }}">
                    <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                        <strong class="d-block text-white mb-0" style="font-size: 13.5px;">{{ $dest['name'] }}</strong>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- 14. Real-Time Location & Interactive Map Section (Screenshot Parity) --}}
        <div id="location" class="card agoda-card-border p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px;">
                        <i class="fa-solid fa-location-dot text-danger me-2"></i> Location &amp; Nearby Highlights
                    </h5>
                    <small class="text-muted" style="font-size: 12px;">Exact physical address and interactive GPS location</small>
                </div>
                @php
                    $mapsQuery = urlencode(($property->name . ' ' . $property->address . ' ' . $property->city));
                    if (!empty($property->latitude) && !empty($property->longitude)) {
                        $mapsQuery = "{$property->latitude},{$property->longitude}";
                    }
                @endphp
                <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm fw-bold px-3 py-1.5" style="border-radius: 6px; font-size: 12.5px;">
                    <i class="fa-solid fa-diamond-turn-right me-1"></i> Get Directions in Google Maps
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <strong class="d-block text-dark mb-1" style="font-size: 13.5px;"><i class="fa-solid fa-building me-1 text-primary"></i> Full Address:</strong>
                        <p class="text-secondary mb-2" style="font-size: 12.5px; line-height: 1.5;">
                            {{ $property->address ?: ($property->city . ', Bangladesh') }}
                            @if(!empty($property->postal_code)) - {{ $property->postal_code }} @endif
                        </p>

                        @if(!empty($property->nearest_landmark))
                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                            <i class="fa-solid fa-map-pin text-danger fs-6"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Prominent Landmark</small>
                                <strong class="text-dark" style="font-size: 12.5px;">{{ $property->nearest_landmark }}</strong>
                            </div>
                        </div>
                        @endif

                        @if(!empty($property->latitude) && !empty($property->longitude))
                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                            <i class="fa-solid fa-satellite text-info fs-6"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">GPS Coordinates</small>
                                <code class="text-dark fw-bold" style="font-size: 11.5px;">{{ $property->latitude }}, {{ $property->longitude }}</code>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if(!empty($property->contact_phone) || !empty($property->contact_email))
                    <div class="p-3 bg-light rounded-3 border">
                        <strong class="d-block text-dark mb-2" style="font-size: 13px;"><i class="fa-solid fa-headset me-1 text-success"></i> Hotel Direct Contacts:</strong>
                        @if(!empty($property->contact_phone))
                        <div class="d-flex align-items-center gap-2 text-secondary mb-1" style="font-size: 12.5px;">
                            <i class="fa-solid fa-phone text-muted" style="width: 14px;"></i>
                            <a href="tel:{{ $property->contact_phone }}" class="text-dark text-decoration-none fw-semibold">{{ $property->contact_phone }}</a>
                        </div>
                        @endif
                        @if(!empty($property->contact_email))
                        <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 12.5px;">
                            <i class="fa-solid fa-envelope text-muted" style="width: 14px;"></i>
                            <a href="mailto:{{ $property->contact_email }}" class="text-dark text-decoration-none">{{ $property->contact_email }}</a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="col-lg-7">
                    <div class="rounded-3 overflow-hidden border shadow-xs" style="height: 240px; background: #e2e8f0; position: relative;">
                        @if(!empty($property->map_embed_url))
                            <iframe src="{{ $property->map_embed_url }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        @elseif(!empty($property->latitude) && !empty($property->longitude))
                            <iframe width="100%" height="100%" style="border:0;" loading="lazy" src="https://maps.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}&hl=en&z=15&output=embed"></iframe>
                        @else
                            <iframe width="100%" height="100%" style="border:0;" loading="lazy" src="https://maps.google.com/maps?q={{ urlencode(($property->name . ' ' . $property->city . ' Bangladesh')) }}&hl=en&z=14&output=embed"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 15. Property Policies & House Rules Section (Screenshot Parity) --}}
        <div id="policies" class="card agoda-card-border p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px;">
                <i class="fa-solid fa-shield-halved text-primary me-2"></i> Property Policies &amp; House Rules
            </h5>

            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-arrow-right-to-bracket text-primary"></i>
                            <small class="text-muted fw-bold" style="font-size: 11px; text-transform: uppercase;">Check-in</small>
                        </div>
                        <strong class="text-dark d-block" style="font-size: 14px;">From {{ $property->checkin_time ?: '14:00' }}</strong>
                        <small class="text-muted" style="font-size: 11px;">Valid Govt ID required</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i>
                            <small class="text-muted fw-bold" style="font-size: 11px; text-transform: uppercase;">Check-out</small>
                        </div>
                        <strong class="text-dark d-block" style="font-size: 14px;">Until {{ $property->checkout_time ?: '12:00' }}</strong>
                        <small class="text-muted" style="font-size: 11px;">Late check-out on request</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-ban-smoking text-warning"></i>
                            <small class="text-muted fw-bold" style="font-size: 11px; text-transform: uppercase;">Cancellation</small>
                        </div>
                        <strong class="text-dark d-block" style="font-size: 13px;">
                            {{ $property->free_cancellation ? 'Free Cancellation' : 'Non-Refundable' }}
                        </strong>
                        <small class="text-muted" style="font-size: 11px;">{{ $property->free_cancellation ? 'Cancel free before check-in' : 'Policy applies' }}</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-credit-card text-success"></i>
                            <small class="text-muted fw-bold" style="font-size: 11px; text-transform: uppercase;">Payment</small>
                        </div>
                        <strong class="text-dark d-block" style="font-size: 13px;">
                            {{ $property->no_credit_card_required ? 'Pay at Hotel' : 'Online Prepaid' }}
                        </strong>
                        <small class="text-muted" style="font-size: 11px;">bKash, Nagad, Visa &amp; Cash</small>
                    </div>
                </div>
            </div>

            {{-- Agoda Parity: Some Helpful Facts --}}
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">Some helpful facts</h6>
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-clock text-primary fs-5 mt-0.5"></i>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 11.5px; text-transform: uppercase;">Check-in / Check-out</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13px;">Check-in from: {{ $property->checkin_time ?: '14:00' }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">Check-out until: {{ $property->checkout_time ?: '12:00' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-car-side text-success fs-5 mt-0.5"></i>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 11.5px; text-transform: uppercase;">Getting around</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13px;">Distance from center: {{ $property->nearest_landmark ?: '0.5 km' }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">Airport travel time: ~20 mins</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-building text-info fs-5 mt-0.5"></i>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 11.5px; text-transform: uppercase;">The property</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13px;">Total Rooms: {{ $property->total_rooms_count ?: ($property->rooms->count() ?: 10) }} • Floors: {{ $property->total_floors ?: 8 }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">
                                    {{ $property->pets_policy ?: 'Pets Not Allowed' }} • {{ is_array($property->languages_spoken) ? implode(', ', $property->languages_spoken) : 'English, Bengali' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($property->house_rules))
            <div class="mt-3 p-3 bg-light rounded-3 border">
                <strong class="d-block text-dark mb-1" style="font-size: 13px;"><i class="fa-solid fa-list-check me-1 text-primary"></i> Special House Rules:</strong>
                <p class="text-secondary mb-0" style="font-size: 12.5px; line-height: 1.5; white-space: pre-line;">{{ $property->house_rules }}</p>
            </div>
            @endif
        </div>

        {{-- 16. Trending Cities Section (Screenshot Parity) --}}
        <div class="mb-4 position-relative">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Trending Cities</h5>
            
            <div class="position-relative">
                <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: none;">
                    @php
                        $trending = [
                            ['city' => 'Seoul', 'country' => 'South Korea', 'img' => 'https://images.unsplash.com/photo-1538485399081-7191377e8241?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Yilan', 'country' => 'Taiwan', 'img' => 'https://images.unsplash.com/photo-1508248467877-aed3278c2e64?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Pattaya', 'country' => 'Thailand', 'img' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Chiang Mai', 'country' => 'Thailand', 'img' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Paris', 'country' => 'France', 'img' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Taichung', 'country' => 'Taiwan', 'img' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=400&q=80'],
                        ];
                    @endphp
                    @foreach($trending as $trend)
                    <a href="{{ route('search.index') }}?destination={{ urlencode($trend['city']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none bg-white flex-shrink-0" style="width: 180px;">
                        <img src="{{ $trend['img'] }}" class="w-100" style="height: 140px; object-fit: cover;" alt="{{ $trend['city'] }}">
                        <div class="p-2 text-center">
                            <strong class="d-block text-dark mb-0" style="font-size: 13.5px;">{{ $trend['city'] }}</strong>
                            <small class="text-secondary" style="font-size: 11.5px;">{{ $trend['country'] }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Carousel Right Arrow Button --}}
                <button class="btn btn-light rounded-circle shadow-md position-absolute top-50 end-0 translate-middle-y me-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; z-index: 10; border: 1px solid #cbd5e1; background: #fff;" onclick="this.previousElementSibling.scrollBy({left: 200, behavior: 'smooth'});">
                    <i class="fa-solid fa-chevron-right text-dark fs-6"></i>
                </button>
            </div>
        </div>

    </div>

</div>

{{-- 16. Bottom Fixed Sticky Offer Bar (Screenshot Parity) --}}
<div id="agodaDetailStickyBottomBar" class="position-fixed bottom-0 start-0 w-100 bg-white border-top shadow-lg py-2.5 px-4" style="z-index: 1050; border-color: #cbd5e1 !important;">
    <div class="d-flex align-items-center justify-content-between" style="max-width: 1280px; margin: 0 auto;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-bell text-warning fs-5"></i>
            <span class="fw-bold text-dark" style="font-size: 13.5px;">Don't miss out on this amazing property!</span>
        </div>
        <div class="d-flex align-items-center gap-3" style="font-size: 12.5px;">
            <a href="{{ route('search.index') }}" class="text-primary fw-bold text-decoration-none">
                Back to search results
            </a>
            <span class="text-muted">|</span>
            <a href="#rooms" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3 py-1.5" style="font-size: 12.5px; border-color: #cbd5e1; color: #2067e1;">
                Back to room choices
            </a>
            <button type="button" class="btn-close ms-2" onclick="document.getElementById('agodaDetailStickyBottomBar').style.display='none';" aria-label="Dismiss"></button>
        </div>
    </div>
</div>

{{-- Full Screen Photo Gallery Modal --}}
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-dark text-white">
            <div class="modal-header border-0 py-3 px-4 bg-black">
                <h5 class="modal-title fw-bold mb-0" style="font-size: 16px;">{{ $property->name }} — Photo Gallery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="galleryModalCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($gallery as $gIdx => $gImg)
                        <div class="carousel-item @if($gIdx === 0) active @endif">
                            <img src="{{ $gImg }}" class="img-fluid rounded-3" style="max-height: 75vh; object-fit: contain;" alt="Gallery Photo {{ $gIdx+1 }}">
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryModalCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryModalCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

