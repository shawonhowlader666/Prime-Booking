@extends('layouts.main', ['activePage' => 'hotels'])

@php
    use App\Services\CurrencyService;
    use Carbon\Carbon;

    $checkinCarbon = request('checkin') ? Carbon::parse(request('checkin')) : Carbon::tomorrow();
    $checkoutCarbon = request('checkout') ? Carbon::parse(request('checkout')) : Carbon::tomorrow()->addDay();
    $guestStr = request('guests') ?: '3 adults, 1 child';
    $roomsCountStr = request('rooms') ?: '2 rooms';

    $gallery = collect();
    if (!empty($property->primary_image)) $gallery->push($property->primary_image);
    $propGallery = is_array($property->gallery_images) ? $property->gallery_images : (is_array($property->images) ? $property->images : []);
    if (!empty($propGallery)) {
        $gallery = $gallery->merge($propGallery);
    }
    $fallbacks = [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=600&q=80',
    ];
    while ($gallery->count() < 5) { $gallery->push($fallbacks[$gallery->count() % count($fallbacks)]); }
    
    // Dynamic Agoda Photo Categorization Collection
    $galleryCategorized = [
        'all' => [],
        'rooms' => [],
        'property_views' => [],
        'facilities' => [],
        'other' => []
    ];

    if ($property->rooms && $property->rooms->count() > 0) {
        foreach ($property->rooms as $r) {
            if (is_array($r->images)) {
                foreach ($r->images as $rImg) {
                    $item = ['url' => $rImg, 'title' => $r->name . ' - Room Interior', 'category' => 'Rooms', 'cat_key' => 'rooms'];
                    $galleryCategorized['rooms'][] = $item;
                    $galleryCategorized['all'][] = $item;
                }
            }
        }
    }

    foreach ($gallery as $idx => $gUrl) {
        if ($idx % 2 === 0) {
            $item = ['url' => $gUrl, 'title' => $property->name . ' - Property View', 'category' => 'Property views', 'cat_key' => 'property_views'];
            $galleryCategorized['property_views'][] = $item;
        } else {
            $item = ['url' => $gUrl, 'title' => $property->name . ' - Facilities & Dining', 'category' => 'Facilities', 'cat_key' => 'facilities'];
            $galleryCategorized['facilities'][] = $item;
        }
        $galleryCategorized['all'][] = $item;
    }

    if (empty($galleryCategorized['rooms'])) {
        $rFallbacks = [
            ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80', 'title' => 'Deluxe Bedroom', 'category' => 'Rooms', 'cat_key' => 'rooms'],
            ['url' => 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80', 'title' => 'Executive Suite', 'category' => 'Rooms', 'cat_key' => 'rooms']
        ];
        $galleryCategorized['rooms'] = $rFallbacks;
        $galleryCategorized['all'] = array_merge($galleryCategorized['all'], $rFallbacks);
    }

    if (empty($galleryCategorized['other'])) {
        $oFallbacks = [
            ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'title' => 'Lobby & Reception Area', 'category' => 'Other', 'cat_key' => 'other']
        ];
        $galleryCategorized['other'] = $oFallbacks;
        $galleryCategorized['all'] = array_merge($galleryCategorized['all'], $oFallbacks);
    }

    $amenitiesList = is_array($property->amenities) && count($property->amenities) > 0
        ? $property->amenities
        : ['Free Wi-Fi', 'Free parking', 'Pets allowed', 'Air conditioning in public area', 'English', 'Internet services'];
    
    $scoreNum = (float)($property->rating_score ?? 8.7);
    $score = number_format($scoreNum, 1);
    $revCount = number_format($property->total_reviews ?: 6);
    $nights = $checkinCarbon->diffInDays($checkoutCarbon) ?: 1;
@endphp

@section('title', e($property->name) . ' — Book Hotels in ' . ($property->city ?: 'Bangladesh') . ' | PRIME BOOKING')
@section('meta_description', 'Book ' . e($property->name) . ' in ' . e($property->city) . '. ' . $revCount . ' verified guest reviews. Best rate guaranteed.')

@if(isset($seoSchema) && !empty($seoSchema))
    @push('head')
    <script type="application/ld+json">
    {!! $seoSchema !!}
    </script>
    @endpush
@endif

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    /* Agoda 1:1 Ultra-Premium Detail Page Styling */
    .detail-page-wrapper { background: #f8fafc; min-height: 100vh; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
    
    /* Sticky Top Header Bar & Sticky Nav Bar (Full-Width Edge-to-Edge Agoda Match) */
    .agoda-detail-search-bar { background: linear-gradient(180deg, #1d2b45 0%, #152238 100%); padding: 12px 0; border-bottom: 1px solid #334155; position: sticky; top: 0; z-index: 1100; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .agoda-sticky-nav-bar { position: sticky; top: 68px; z-index: 1050; width: 100%; background: #ffffff !important; border-top: 1px solid #e2e8f0 !important; border-bottom: 1px solid #dddfe2 !important; border-left: none !important; border-right: none !important; border-radius: 0 !important; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06); }
    
    .agoda-nav-scroll-container { overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
    .agoda-nav-scroll-container::-webkit-scrollbar { display: none; }

    .agoda-nav-item { font-size: 13.5px; font-weight: 600; color: #475569; padding: 15px 18px; border-bottom: 3px solid transparent; text-decoration: none; display: inline-block; white-space: nowrap; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
    .agoda-nav-item:hover, .agoda-nav-item.active { color: #2067e1; border-bottom-color: #2067e1; font-weight: 700; }
    
    .agoda-filter-pill-121 { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 14px; font-size: 12.5px; font-weight: 500; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease; user-select: none; }
    .agoda-filter-pill-121:hover { border-color: #2067e1; color: #2067e1; }
    
    /* Card Aesthetics & Agoda Exact Borders */
    .agoda-card-border { background: #ffffff; border: 1px solid #dddfe2 !important; border-radius: 8px !important; box-shadow: none !important; }
    .agoda-card-border:hover { border-color: #cbd5e1 !important; }

    /* Hero Collage Grid Image Zoom & Hover Highlights */
    .hero-main-img-box { position: relative; height: 360px; overflow: hidden; border-radius: 8px 0 0 8px; cursor: pointer; background: #0f172a; }
    .hero-thumb-img-box { position: relative; height: 176px; overflow: hidden; cursor: pointer; background: #0f172a; }
    .hero-main-img-box img, .hero-thumb-img-box img { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), filter 0.3s ease; }
    .hero-main-img-box:hover img, .hero-thumb-img-box:hover img { transform: scale(1.045); filter: brightness(1.06); }
    
    /* Sub-score Pills & Filter Buttons */
    .subscore-pill { background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .agoda-filter-pill { background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 20px; font-size: 12.5px; font-weight: 600; color: #334155; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
    .agoda-filter-pill:hover { border-color: #2067e1; color: #2067e1; background: #f0f7ff; }

    .room-space-card { background: #ffffff; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; flex: 1; min-width: 140px; }

    /* 📐 Standard Symmetric Page Container (Equal left and right padding) */
    .agoda-page-container {
        max-width: 1240px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 24px;
        padding-right: 24px;
        width: 100%;
        box-sizing: border-box;
    }

    /* 📱 100% Comprehensive Responsive Mobile, Tablet & Desktop Adjustments */
    @media (max-width: 991.98px) {
        .hero-main-img-box { height: 260px; border-radius: 12px; }
        .hero-thumb-img-box { height: 130px; }
        .agoda-sticky-nav-bar { top: 0 !important; }
        .agoda-room-offer-grid > .border-end { border-right: none !important; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px; }
        .agoda-room-offer-grid > .text-end { text-align: left !important; }
        
        /* Modal Responsive Layout (Stack stage and sidebar vertically on tablets/mobiles) */
        #galleryModal .modal-body { flex-direction: column !important; overflow-y: auto !important; }
        #agodaGalleryLeftStage { min-height: 60vh !important; flex-grow: 1 !important; }
        .agoda-modal-sidebar { width: 100% !important; border-left: none !important; border-top: 1px solid #e2e8f0 !important; padding: 16px !important; }
        #agodaSlideshowActiveImg { max-height: 48vh !important; max-width: 96% !important; }
    }

    @media (max-width: 768px) {
        .agoda-page-container {
            padding-left: 14px;
            padding-right: 14px;
        }
        .hero-main-img-box { height: 230px; }
        .agoda-detail-search-bar { padding: 8px 0; }
        .agoda-nav-item { padding: 12px 14px; font-size: 12.5px; }
        .agoda-modal-cat-tab { padding: 8px 10px; font-size: 12px; }
    }

    @media (max-width: 575.98px) {
        .hero-main-img-box { height: 210px; border-radius: 10px; }
        #agodaDetailStickyBottomBar .d-flex { flex-direction: column; align-items: flex-start !important; gap: 8px !important; }
        #agodaDetailStickyBottomBar .d-flex:last-child { width: 100%; justify-content: space-between; }
        .agoda-filmstrip-thumb { width: 60px !important; height: 42px !important; }
        #agodaSlideshowActiveImg { max-height: 40vh !important; }
    }

    /* Agoda Modal Tab & Filmstrip Styling (1:1 Parity) */
    .agoda-modal-cat-tab { background: transparent; border: none; font-size: 13.5px; font-weight: 600; color: #64748b; padding: 10px 14px; border-bottom: 2.5px solid transparent; cursor: pointer; white-space: nowrap; transition: all 0.2s ease; }
    .agoda-modal-cat-tab:hover, .agoda-modal-cat-tab.active { color: #2067e1; border-bottom-color: #2067e1; font-weight: 700; }
    .agoda-grid-photo-card { transition: transform 0.2s ease; }
    .agoda-grid-photo-card:hover .agoda-grid-img { transform: scale(1.045); filter: brightness(1.05); }
    .agoda-grid-photo-card:hover .agoda-grid-overlay { opacity: 1 !important; }
    .agoda-grid-img { transition: transform 0.3s ease, filter 0.3s ease; }
    .agoda-filmstrip-thumb.active { border-color: #3b82f6 !important; opacity: 1; transform: scale(1.05); }
    .agoda-filmstrip-thumb:not(.active) { opacity: 0.55; }
    .agoda-filmstrip-thumb:hover { opacity: 1; }
</style>

<div class="detail-page-wrapper">

    {{-- 1. Agoda Subheader Compact Search Bar (Matching Screenshot 1 Exact 1:1 Parity - Single Clean Row) --}}
    <div class="agoda-detail-search-bar shadow-sm" style="background: #132968; padding: 12px 0; position: sticky; top: 0; z-index: 1030;">
        <div class="agoda-page-container">
            <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- 1. Destination Input --}}
                <div class="col-12 col-lg-3">
                    <div class="input-group bg-white rounded-3 overflow-hidden shadow-xs" style="height: 46px;">
                        <span class="input-group-text bg-white border-0 text-secondary ps-3 pe-2"><i class="fa-solid fa-magnifying-glass" style="color: #475569;"></i></span>
                        <input type="text" name="destination" class="form-control border-0 fw-semibold text-dark ps-1" value="{{ $property->name }}" placeholder="Enter a destination or property" style="font-size: 13.5px; height: 46px;">
                    </div>
                </div>

                {{-- 2. Combined Check-in & Check-out Single White Box --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-white rounded-3 d-flex align-items-center overflow-hidden shadow-xs position-relative" style="height: 46px;">
                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="border-right: 1px solid #cbd5e1; cursor: pointer;" onclick="document.getElementById('checkInDetailInput').showPicker ? document.getElementById('checkInDetailInput').showPicker() : document.getElementById('checkInDetailInput').focus();">
                            <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailCheckInText" style="font-size: 12.5px;">{{ $checkinCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" id="detailCheckInDay" style="font-size: 11px;">{{ $checkinCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_in" id="checkInDetailInput" value="{{ $checkinCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateDetailDateDisplay('checkIn', this.value);">

                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="cursor: pointer;" onclick="document.getElementById('checkOutDetailInput').showPicker ? document.getElementById('checkOutDetailInput').showPicker() : document.getElementById('checkOutDetailInput').focus();">
                            <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailCheckOutText" style="font-size: 12.5px;">{{ $checkoutCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" id="detailCheckOutDay" style="font-size: 11px;">{{ $checkoutCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_out" id="checkOutDetailInput" value="{{ $checkoutCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; right: 0; width: 1px; height: 1px;" onchange="updateDetailDateDisplay('checkOut', this.value);">
                    </div>
                </div>

                {{-- 3. Guests & Rooms Dropdown Box --}}
                <div class="col-12 col-md-6 col-lg-3 position-relative">
                    <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center justify-content-between shadow-xs dropdown-toggle" style="height: 46px; cursor: pointer;" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-users text-secondary fs-5"></i>
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailGuestCountText" style="font-size: 12.5px;">{{ $guestStr }}</strong>
                                <small class="text-secondary" id="detailRoomCountText" style="font-size: 11px;">{{ $roomsCountStr }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Guests Popover Counter --}}
                    <div class="dropdown-menu p-3 shadow-lg border-0 rounded-3 mt-2" style="width: 280px; z-index: 1060;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong class="d-block text-dark" style="font-size: 13px;">Adults</strong>
                                <small class="text-muted" style="font-size: 11px;">Ages 18 or above</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustDetailCounter('adults', -1);">-</button>
                                <input type="hidden" name="adults" id="detailAdultsInput" value="{{ $adultsCount ?? 2 }}">
                                <span class="fw-bold px-1" id="detailAdultsValText">{{ $adultsCount ?? 2 }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustDetailCounter('adults', 1);">+</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-2">
                            <div>
                                <strong class="d-block text-dark" style="font-size: 13px;">Rooms</strong>
                                <small class="text-muted" style="font-size: 11px;">Total units needed</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustDetailCounter('rooms', -1);">-</button>
                                <input type="hidden" name="rooms" id="detailRoomsInput" value="{{ $roomsCountVal ?? 1 }}">
                                <span class="fw-bold px-1" id="detailRoomsValText">{{ $roomsCountVal ?? 1 }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustDetailCounter('rooms', 1);">+</button>
                            </div>
                        </div>
                        <button type="button" class="btn text-white w-100 btn-sm fw-bold rounded-2" style="background: #2067e1;" onclick="bootstrap.Dropdown.getInstance(this.closest('.position-relative').querySelector('.dropdown-toggle')).hide();">
                            Done
                        </button>
                    </div>
                </div>

                {{-- 4. SEARCH Button --}}
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn text-white w-100 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #2067e1; height: 46px; font-size: 14px; letter-spacing: 0.5px;">
                        SEARCH
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="agoda-page-container pt-3">

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

        {{-- 3. Hero Photo Collage 5-Grid (Agoda Screenshot 1 Parity with Clean Modal Controls) --}}
        <div class="mb-4 position-relative" style="border-radius: 12px; overflow: hidden;">
            <div class="row g-2">
                {{-- Main Feature Image (Left 60%) --}}
                <div class="col-lg-7">
                    <div class="hero-main-img-box position-relative" style="background:#0f172a;" data-bs-toggle="modal" data-bs-target="#galleryModal">
                        <img src="{{ $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">

                        {{-- Action Buttons on Bottom of Main Photo --}}
                        <div class="position-absolute bottom-0 end-0 m-3 d-flex align-items-center gap-2" style="z-index: 10;" onclick="event.stopPropagation();">
                            @if(!empty($property->video_url))
                            <button type="button" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 12.5px;" data-bs-toggle="modal" data-bs-target="#videoTourModal">
                                <i class="fa-solid fa-circle-play"></i> Video Tour
                            </button>
                            @endif
                            <button type="button" class="btn btn-light btn-sm fw-bold rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 12.5px; background: rgba(255,255,255,0.95); border: 1px solid #cbd5e1;" data-bs-toggle="modal" data-bs-target="#galleryModal">
                                <i class="fa-solid fa-camera text-primary"></i> See all photos
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 4 Grid Photos (Right 2x2 Grid) --}}
                <div class="col-lg-5 d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#galleryModal">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-top-right-radius: 0;">
                                <img src="{{ $gallery[1] ?? $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 2">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-top-right-radius: 12px;">
                                <img src="{{ $gallery[2] ?? $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 3">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-bottom-right-radius: 0;">
                                <img src="{{ $gallery[3] ?? $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 4">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-thumb-img-box" style="border-bottom-right-radius: 12px;">
                                <img src="{{ $gallery[4] ?? $gallery[0] }}" class="w-100 h-100" style="object-fit: cover;" alt="Gallery 5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Icons (Wishlist & Share) Top Right --}}
            <div class="position-absolute top-0 end-0 m-3 d-flex align-items-center gap-2" style="z-index: 15;">
                <button type="button" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #ffffff;" title="Share Property" onclick="sharePropertyLink();">
                    <i class="fa-solid fa-share-nodes text-dark fs-6"></i>
                </button>
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
    <div class="agoda-sticky-nav-bar mb-4 bg-white border-bottom shadow-xs" style="position: sticky; top: 70px; z-index: 1025;">
        <div class="agoda-page-container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1 overflow-x-auto" style="scrollbar-width: none;">
                    <a href="#overview" class="agoda-nav-item active">Overview</a>
                    <a href="#rooms" class="agoda-nav-item">Rooms</a>
                    <a href="#recommendations" class="agoda-nav-item">Trip recommendations</a>
                    <a href="#facilities" class="agoda-nav-item">Facilities</a>
                    <a href="#reviews" class="agoda-nav-item">Reviews</a>
                    <a href="#location" class="agoda-nav-item">Location</a>
                    <a href="#policies" class="agoda-nav-item">Policies</a>
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

    <div class="agoda-page-container">
        {{-- 5. Main Overview Section Grid (Exact User Screenshot Parity) --}}
        <div id="overview" class="row g-4 mb-4">
            
            {{-- Left Column: Title Card & Guest Highlights --}}
            <div class="col-lg-8">
                
                {{-- Left Card 1: Badges, Title & Address (Screenshot Exact Parity) --}}
                <div class="card agoda-card-border p-4 mb-4">
                    {{-- Badges & Stars (Exact Agoda Parity) --}}
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge text-white fw-bold px-2.5 py-1" style="background-color: #d93025; font-size: 11.5px; border-radius: 4px;">
                            Best seller
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1.5 border" style="font-size: 11px; border-radius: 4px; border-color: #cbd5e1 !important;">
                            <span style="letter-spacing: 0.5px; color: #2067e1; font-weight: 800;">PRIME</span> PREFERRED
                        </span>
                        @if(isset($socialProof) && $socialProof['is_popular'])
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2.5 py-1 border border-danger border-opacity-25" style="font-size: 11px; border-radius: 4px;">
                            <i class="fa-solid fa-fire text-danger me-1"></i> HIGH DEMAND
                        </span>
                        @endif
                    </div>

                    @if(isset($socialProof))
                    <div class="mb-3 p-2 px-3 rounded-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #fff1f2; border: 1px solid #ffe4e6; font-size: 12px; font-weight: 600; color: #9f1239;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-fire text-danger"></i>
                            <span>{{ $socialProof['urgency_text'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 text-secondary" style="font-size: 11.5px;">
                            <i class="fa-solid fa-eye text-primary"></i> {{ $socialProof['viewing_now'] }} looking right now
                        </div>
                    </div>
                    @endif

                    {{-- Title & Address --}}
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h2 class="fw-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; line-height: 1.25;">
                            {{ $property->name }}
                        </h2>
                        <span class="text-warning fs-6" style="letter-spacing: 1px;">
                            @for($i = 0; $i < ($property->star_rating ?? 3); $i++)★@endfor
                        </span>
                    </div>

                    <p class="text-secondary small mb-0" style="font-size: 13px;">
                        {{ $property->address ?: ($property->city . ', Bangladesh, 1230') }}- 
                        <a href="#location" class="fw-bold text-decoration-none" style="color: #2067e1;">SEE MAP</a>
                    </p>
                </div>

                {{-- Left Card 2: Highlights from Guests (Dynamic from DB / Model) --}}
                <div class="card agoda-card-border p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Highlights from guests</h6>
                        <a href="#reviews" class="text-decoration-none fw-semibold small" style="color: #2067e1; font-size: 12.5px;">See details</a>
                    </div>
                    
                    <div class="d-flex flex-column gap-2.5">
                        @php
                            $highlightIcons = [
                                'location'    => ['icon' => 'fa-location-dot', 'bg' => '#fce8e6', 'color' => '#d93025'],
                                'host'        => ['icon' => 'fa-user-tie', 'bg' => '#fef7e0', 'color' => '#f29900'],
                                'cleanliness' => ['icon' => 'fa-spray-can-sparkles', 'bg' => '#e6f4ea', 'color' => '#137333'],
                                'airport'     => ['icon' => 'fa-plane-arrival', 'bg' => '#e8f0fe', 'color' => '#1a73e8'],
                                'value'       => ['icon' => 'fa-ribbon', 'bg' => '#f3e8ff', 'color' => '#9333ea'],
                                'atmosphere'  => ['icon' => 'fa-heart', 'bg' => '#fee2e2', 'color' => '#dc2626'],
                            ];
                        @endphp
                        @foreach($property->ai_highlights as $hKey => $highlight)
                        @php
                            $cfg = $highlightIcons[$hKey] ?? ['icon' => 'fa-star', 'bg' => '#f1f5f9', 'color' => '#475569'];
                        @endphp
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                                    <i class="fa-solid {{ $cfg['icon'] }} fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $highlight['title'] }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted fs-6"></i>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Space & Rooms Breakdown (100% Dynamic from Database) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2.5 mb-3" style="border-color: #e2e8f0 !important;">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">Space &amp; Rooms</h5>
                        <a href="#rooms" class="text-decoration-none fw-bold small" style="color: #2067e1; font-size: 13px;">Select room</a>
                    </div>

                    @php
                        $roomsCount = $property->rooms->count();
                        $maxGuests = $property->rooms->max('max_adults') ?: 2;
                        $maxSizeSqm = $property->rooms->max('room_size_sqm') ?: 46;
                        $maxSizeSqft = round($maxSizeSqm * 10.764);
                    @endphp

                    {{-- Sub-header Specs Row --}}
                    <div class="mb-3">
                        <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">
                            {{ $property->type ? ucfirst($property->type) : 'Hotel / Resort' }} 
                            <span class="text-secondary fw-normal" style="font-size: 12.5px;">(Room size: {{ $maxSizeSqm }} m²/{{ $maxSizeSqft }} ft²)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold" style="font-size: 12.5px;">
                            <span>Max {{ $maxGuests }} guests</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>{{ $roomsCount > 0 ? $roomsCount : 1 }} room types</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>Attached Baths</span>
                            <span class="text-muted fw-normal">|</span>
                            <span>Balcony &amp; Views</span>
                        </div>
                    </div>

                    {{-- Horizontal Scrollable Sub-cards --}}
                    <div class="position-relative">
                        <div class="d-flex gap-3 overflow-x-auto pb-2 align-items-stretch" style="scrollbar-width: none;">
                            @forelse($property->rooms as $rIdx => $rm)
                            <div class="d-flex flex-column justify-content-between" style="min-width: 210px; width: 210px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">{{ $rm->name }}</strong>
                                    <small class="text-secondary d-block" style="font-size: 12px;">{{ $rm->bed_type ?: '1 King / Queen Bed' }}</small>
                                    <small class="text-muted d-block mt-1" style="font-size: 11px;">{{ $rm->formatted_size }} • {{ $rm->view_type ?: 'City view' }}</small>
                                </div>
                                <div class="text-secondary mt-3 d-flex align-items-center justify-content-between">
                                    <i class="fa-solid fa-bed fs-5" style="color: #64748b;"></i>
                                    <span class="badge bg-light text-dark border" style="font-size: 10.5px;">Max {{ $rm->max_adults }} adults</span>
                                </div>
                            </div>
                            @empty
                            <div class="d-flex flex-column justify-content-between" style="min-width: 210px; width: 210px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Standard Room</strong>
                                    <small class="text-secondary d-block" style="font-size: 12px;">1 King Bed</small>
                                </div>
                                <div class="text-secondary mt-3">
                                    <i class="fa-solid fa-bed fs-5" style="color: #64748b;"></i>
                                </div>
                            </div>
                            @endforelse

                            {{-- Bathroom and Toiletries --}}
                            <div class="d-flex flex-column justify-content-between" style="min-width: 240px; width: 240px; border: 1px solid #dddfe2; border-radius: 8px; padding: 14px; background: #ffffff; flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;">Bathroom &amp; Toiletries</strong>
                                    <small class="text-secondary d-block" style="font-size: 11.5px; line-height: 1.4;">
                                        {{ !empty($property->rooms->first()->bathroom_features) && is_array($property->rooms->first()->bathroom_features) ? implode(', ', $property->rooms->first()->bathroom_features) : 'Private Bathroom, Hot Water Geyser, Free Toiletries, Towels' }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-secondary fs-6 mt-3" style="color: #64748b !important;">
                                    <i class="fa-solid fa-pump-soap"></i>
                                    <i class="fa-solid fa-shower"></i>
                                    <i class="fa-solid fa-bottle-droplet"></i>
                                    <i class="fa-solid fa-temperature-arrow-up"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Chevron Scroll Button --}}
                        <button class="btn btn-light rounded-circle shadow-sm border position-absolute top-50 end-0 translate-middle-y me-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; z-index: 10; background: #ffffff;" title="Scroll Right">
                            <i class="fa-solid fa-chevron-right text-dark"></i>
                        </button>
                    </div>
                </div>

                {{-- Facilities Grid (Dynamic from Model / DB) --}}
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

                {{-- About Us Section (Dynamic from DB) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">About us</h5>
                    <p class="text-dark mb-1" style="font-size: 13.5px; line-height: 1.6;">
                        {{ $property->description ?: ('Conveniently situated in ' . ($property->city ?: 'Bangladesh') . ', ' . $property->name . ' offers comfortable accommodations with excellent amenities, scenic views, and attentive hospitality.') }}
                    </p>
                    <div>
                        <a href="#overview" class="fw-bold text-decoration-none small" style="color: #2067e1; font-size: 13px;">Read more</a>
                    </div>
                </div>

                {{-- High Demand Urgency Callout Banner --}}
                <div class="card agoda-card-border mb-4" style="padding: 16px 20px !important; background-color: #fef2f2 !important; border: 1px solid #fee2e2 !important;">
                    <h6 class="fw-bold mb-1" style="color: #d93025; font-size: 15px;">This property is in high demand!</h6>
                    <p class="text-dark mb-0" style="font-size: 13px;">Popular choice in {{ $property->city ?: 'Bangladesh' }}</p>
                </div>

            </div>

            {{-- Right Column Sidebar Widgets --}}
            <div class="col-lg-4 d-flex flex-column">
                
                {{-- Right Card 1: Review Score Box (Dynamic Sub-scores) --}}
                <div id="reviews" class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16.5px; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $score }} {{ $scoreNum >= 9 ? 'Exceptional' : ($scoreNum >= 8 ? 'Excellent' : 'Very Good') }}</h5>
                        <a href="#reviews" class="text-decoration-none fw-bold flex-shrink-0" style="color: #2067e1; font-size: 12.5px;">Read all reviews</a>
                    </div>
                    <div class="text-start mb-3" style="color: #2067e1; font-size: 12.5px; font-weight: 600;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ $revCount }} reviews
                    </div>

                    {{-- Dynamic Green Sub-scores Pills --}}
                    <div class="d-flex flex-wrap align-items-center gap-1 justify-content-start" style="font-size: 11px;">
                        @foreach($property->sub_scores as $subKey => $subVal)
                        <span class="px-2 py-1" style="background-color: #e6f4ea; color: #137333; font-weight: 600; border-radius: 4px; font-size: 11px; display: inline-block;">
                            {{ ucfirst(str_replace('_', ' ', $subKey)) }} {{ number_format((float)$subVal, 1) }}
                        </span>
                        @endforeach
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white ms-1" style="width: 15px; height: 15px; background-color: #5f6368; font-size: 9.5px; font-weight: 700; cursor: pointer; flex-shrink: 0;" title="Rating Breakdown Info">i</span>
                    </div>
                </div>

                {{-- Right Card 2: Check-in / Check-out Box (Dynamic from DB) --}}
                <div class="card agoda-card-border mb-4" style="padding: 20px !important;">
                    <div class="d-flex justify-content-between text-secondary mb-2.5" style="font-size: 12.5px;">
                        <div>
                            <strong class="d-block text-dark mb-1" style="font-size: 13px;">Check-in:</strong>
                            <span style="font-size: 12px; color: #475569;">{{ $property->checkin_time ?: '14:00' }} onwards</span>
                        </div>
                        <div>
                            <strong class="d-block text-dark mb-1" style="font-size: 13px;">Check-out:</strong>
                            <span style="font-size: 12px; color: #475569;">until {{ $property->checkout_time ?: '12:00' }}</span>
                        </div>
                    </div>
                    <div class="text-end border-top pt-2.5 mt-2">
                        <a href="#policies" class="text-decoration-none fw-bold" style="color: #2067e1; font-size: 12.5px;">See more info &gt;</a>
                    </div>
                </div>

                {{-- Right Card 3: Combined Interactive Map & Closest Landmarks Single Card --}}
                <div id="location" class="card agoda-card-border overflow-hidden mb-4 flex-grow-1">
                    {{-- Map Header Banner --}}
                    <div class="position-relative text-center" style="height: 140px; background: #e8f0fe; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
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
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">{{ $score }} Excellent</h6>
                            <small class="text-secondary" style="font-size: 12px; color: #64748b;">Location rating score</small>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold mb-3" style="font-size: 13px;">
                            <i class="fa-solid fa-award text-dark fs-6"></i> Excellent location in {{ $property->city ?: 'Bangladesh' }}
                        </div>

                        {{-- Parking Row --}}
                        <div class="d-flex justify-content-between align-items-center border-top border-bottom py-2.5 my-2" style="font-size: 13px; border-color: #e2e8f0 !important;">
                            <span class="text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-square-parking text-secondary fs-5"></i> Parking
                            </span>
                            <strong style="color: #16a34a; font-size: 13px;">{{ in_array('Free parking', $amenitiesList) || in_array('parking', $amenitiesList) ? 'FREE' : 'Available' }}</strong>
                        </div>

                        {{-- Dynamic Closest Landmarks List from DB --}}
                        <div class="pt-2">
                            <h6 class="fw-bold text-dark mb-3" style="font-size: 13.5px;">Closest landmarks</h6>
                            <div class="d-flex flex-column gap-2.5" style="font-size: 12.5px;">
                                @forelse($property->nearby_landmarks_list as $landmark)
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-location-dot me-2 text-dark"></i> {{ $landmark['name'] }}</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">{{ $landmark['distance'] }}</span>
                                </div>
                                @empty
                                <div class="d-flex justify-content-between text-dark">
                                    <span><i class="fa-solid fa-location-dot me-2 text-dark"></i> {{ $property->city ?: 'City' }} Center</span>
                                    <span class="text-dark font-monospace" style="font-size: 12px;">500 m</span>
                                </div>
                                @endforelse
                            </div>
                        </div>

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
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <strong class="text-dark" style="font-size: 13.5px; font-weight: 700;">Filter</strong>
                    <span id="roomFilterCount" class="badge bg-light text-secondary border px-2 py-1" style="font-size:11px;">Showing all room types</span>
                </div>
                <div class="d-flex flex-wrap gap-2" id="roomFilterPillsContainer">
                    <div class="agoda-filter-pill-121" data-filter="creditcard" onclick="toggleRoomFilter('creditcard', this)"><i class="fa-solid fa-credit-card text-dark"></i> Book without credit card</div>
                    <div class="agoda-filter-pill-121" data-filter="breakfast" onclick="toggleRoomFilter('breakfast', this)"><i class="fa-solid fa-mug-hot text-dark"></i> Breakfast included</div>
                    <div class="agoda-filter-pill-121" data-filter="freecancel" onclick="toggleRoomFilter('freecancel', this)"><i class="fa-solid fa-shield-halved text-dark"></i> Free cancellation</div>
                    <div class="agoda-filter-pill-121" data-filter="nonsmoking" onclick="toggleRoomFilter('nonsmoking', this)"><i class="fa-solid fa-ban-smoking text-dark"></i> Non-smoking</div>
                    <div class="agoda-filter-pill-121" data-filter="kitchen" onclick="toggleRoomFilter('kitchen', this)"><i class="fa-solid fa-utensils text-dark"></i> Kitchen</div>
                    <div class="agoda-filter-pill-121" data-filter="balcony" onclick="toggleRoomFilter('balcony', this)"><i class="fa-solid fa-building text-dark"></i> Balcony/terrace</div>
                    <div class="agoda-filter-pill-121" data-filter="twinbed" onclick="toggleRoomFilter('twinbed', this)"><i class="fa-solid fa-bed text-dark"></i> Twin Bed</div>
                    <div class="agoda-filter-pill-121" data-filter="view" onclick="toggleRoomFilter('view', this)"><i class="fa-solid fa-water text-dark"></i> {{ $property->city == "Cox's Bazar Sea Beach" ? 'Sea view' : 'City view' }}</div>
                </div>
            </div>

            {{-- Red Urgency Callout (Agoda 1:1 Parity) --}}
            <div class="p-2.5 px-3 mb-3 text-white rounded d-flex align-items-center gap-2 shadow-xs" style="background:#d93025; font-size:12.5px; font-weight:700; border-radius:6px;">
                <i class="fa-solid fa-clock"></i> <span>Hurry up! 3 room types have already sold out for your dates!</span>
            </div>

            {{-- Available Rooms List (100% Dynamic from Database) --}}
            <div class="d-flex flex-column gap-4" id="availableRoomsContainer">
                @forelse($property->rooms as $rIdx => $room)
                @php
                    $rAdults = $room->max_adults ?: 2;
                    $rKids = $room->max_children ?: 1;
                    $rSizeStr = $room->formatted_size ?: '46 m²/495 ft²';
                    $rBedStr = $room->bed_type ?: '1 King / Queen Bed';
                    $rView = $room->view_type ?: 'City view';
                    $rSmoking = $room->smoking_policy ?: 'Non-smoking';
                    $rBalcony = $room->balcony_type ?: 'Private Balcony';
                    $rBathrooms = $room->bathroom_count ?: 1;
                    $isTwin = str_contains(strtolower($rBedStr), 'twin') || str_contains(strtolower($rBedStr), '2 single');
                @endphp
                <div class="card mb-4 overflow-hidden agoda-room-listing-card"
                    data-breakfast="true"
                    data-creditcard="true"
                    data-freecancel="true"
                    data-nonsmoking="{{ str_contains(strtolower($rSmoking), 'non-smoking') ? 'true' : 'false' }}"
                    data-balcony="{{ !empty($rBalcony) && !str_contains(strtolower($rBalcony), 'no balcony') ? 'true' : 'false' }}"
                    data-twinbed="{{ $isTwin ? 'true' : 'false' }}"
                    data-view="true"
                    data-kitchen="true"
                    style="padding: 16px !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; border-radius: 12px !important;">
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
                                
                                <a href="javascript:void(0)" class="text-decoration-none fw-bold d-block mb-2" data-bs-toggle="modal" data-bs-target="#roomDetailModal_{{ $room->id ?? $rIdx }}" style="color: #2067e1; font-size: 13px;">
                                    <i class="fa-solid fa-expand me-1"></i> Room photos and details
                                </a>

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
                                                    <i class="fa-solid fa-user text-dark me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">{{ $rAdults }} adults &amp; 1 child (0-17 years)</span> 
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
                                                    <i class="fa-solid fa-user text-dark me-2" style="width: 14px; text-align: center;"></i> <span class="me-1">{{ max(1, $rAdults - 2) }} adults</span> 
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

                {{-- Room Details & Photo Gallery Lightbox Modal (Agoda Standard) --}}
                <div class="modal fade" id="roomDetailModal_{{ $room->id ?? $rIdx }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header border-bottom py-3 px-4 bg-light">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 17px; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $room->name }}</h5>
                                    <small class="text-muted" style="font-size: 12px;">{{ $rSizeStr }} • Max {{ $rAdults }} Adults • {{ $rBedStr }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                {{-- Room High-Res Image Carousel --}}
                                <div class="rounded-3 overflow-hidden mb-4 position-relative" style="height: 280px; background: #000;">
                                    <img src="{{ $room->primary_image ?: ($gallery[$rIdx % count($gallery)] ?? '') }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $room->name }}">
                                    <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-3 px-3 py-1" style="font-size: 12px; border-radius: 20px;">
                                        <i class="fa-solid fa-camera me-1"></i> Verified High-Res Room Photo
                                    </span>
                                </div>

                                <div class="row g-4">
                                    {{-- Left: Key Room Specs --}}
                                    <div class="col-md-6 border-end pe-md-4">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-bed text-primary me-2"></i> Room Layout &amp; Bedding</h6>
                                        <ul class="list-unstyled d-flex flex-column gap-2 text-secondary mb-4" style="font-size: 13px;">
                                            <li class="d-flex align-items-center gap-2"><i class="fa-solid fa-ruler-combined text-muted" style="width: 16px;"></i> <span>Room Area: <strong>{{ $rSizeStr }}</strong></span></li>
                                            <li class="d-flex align-items-center gap-2"><i class="fa-solid fa-bed text-muted" style="width: 16px;"></i> <span>Bed Config: <strong>{{ $rBedStr }}</strong></span></li>
                                            <li class="d-flex align-items-center gap-2"><i class="fa-solid fa-mountain-sun text-muted" style="width: 16px;"></i> <span>View: <strong>{{ $rView }}</strong></span></li>
                                            <li class="d-flex align-items-center gap-2"><i class="fa-solid fa-building text-muted" style="width: 16px;"></i> <span>Outdoor: <strong>{{ $rBalcony }}</strong></span></li>
                                            <li class="d-flex align-items-center gap-2"><i class="fa-solid fa-ban-smoking text-danger" style="width: 16px;"></i> <span>Smoking: <strong>{{ $rSmoking }}</strong></span></li>
                                        </ul>

                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-shield-halved text-success me-2"></i> Policies &amp; Inclusions</h6>
                                        <ul class="list-unstyled d-flex flex-column gap-2 text-secondary" style="font-size: 13px;">
                                            <li class="d-flex align-items-center gap-2 text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> Free Breakfast Included</li>
                                            <li class="d-flex align-items-center gap-2 text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> Free Cancellation Available</li>
                                            <li class="d-flex align-items-center gap-2 text-primary fw-semibold"><i class="fa-solid fa-circle-check"></i> Pay at Hotel Accepted</li>
                                        </ul>
                                    </div>

                                    {{-- Right: Bathroom Features & Amenities --}}
                                    <div class="col-md-6 ps-md-4">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-bath text-info me-2"></i> Bathroom &amp; Toiletries</h6>
                                        <div class="row g-2 text-secondary mb-4" style="font-size: 12.5px;">
                                            <div class="col-6"><i class="fa-solid fa-shower text-muted me-1.5"></i> {{ $rBathrooms }} Private Bath</div>
                                            <div class="col-6"><i class="fa-solid fa-temperature-arrow-up text-muted me-1.5"></i> Hot Water Geyser</div>
                                            <div class="col-6"><i class="fa-solid fa-bath text-muted me-1.5"></i> Bathtub / Shower</div>
                                            <div class="col-6"><i class="fa-solid fa-wind text-muted me-1.5"></i> Hairdryer</div>
                                            <div class="col-6"><i class="fa-solid fa-pump-soap text-muted me-1.5"></i> Free Toiletries</div>
                                            <div class="col-6"><i class="fa-solid fa-shirt text-muted me-1.5"></i> Towels &amp; Slippers</div>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-tv text-secondary me-2"></i> Media &amp; Technology</h6>
                                        <div class="row g-2 text-secondary" style="font-size: 12.5px;">
                                            <div class="col-6"><i class="fa-solid fa-wifi text-muted me-1.5"></i> Free High-Speed WiFi</div>
                                            <div class="col-6"><i class="fa-solid fa-tv text-muted me-1.5"></i> Smart Flat TV</div>
                                            <div class="col-6"><i class="fa-solid fa-snowflake text-muted me-1.5"></i> Air conditioning</div>
                                            <div class="col-6"><i class="fa-solid fa-box text-muted me-1.5"></i> Mini Refrigerator</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-secondary small d-block" style="font-size: 11px;">Starting from</span>
                                    <strong class="text-danger fw-bold" style="font-size: 20px;">USD {{ $room->price_per_night }} <small class="text-secondary fw-normal" style="font-size: 12px;">/ night</small></strong>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light fw-bold px-3 py-2 border" data-bs-dismiss="modal" style="font-size: 13px;">Close</button>
                                    <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}" class="btn btn-primary fw-bold px-4 py-2" style="background: #2067e1; font-size: 13px; border-radius: 6px;">
                                        Book this Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info border-0 rounded-3 p-4 text-center my-3" style="background: #f0f7ff; color: #1e3a8a; border-radius: 12px !important;">
                    <i class="fa-solid fa-hotel fs-3 mb-2 text-primary"></i>
                    <h5 class="fw-bold mb-1">No Rooms Currently Available</h5>
                    <p class="small mb-3 text-secondary">There are currently no active room types listed for this property.</p>
                    <a href="{{ route('search.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold" style="background: #2067e1;">Explore Other Hotels in {{ $property->city ?: 'Bangladesh' }}</a>
                </div>
                @endforelse
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
                <p class="text-secondary mb-0" style="font-size: 13px;">Managed by verified partner host</p>
            </div>
        </div>

        {{-- 13. Reviews Section Card (100% Dynamic from Database) --}}
        <div id="reviews" class="card border-0 shadow-xs rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 19px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Reviews of {{ $property->name }} from real guests <i class="fa-solid fa-circle-info text-muted fs-6"></i>
                    </h5>
                </div>
                <div class="text-end">
                    <small class="text-secondary d-block" style="font-size: 11px;">Verified reviews</small>
                    <span class="fw-bold text-primary" style="font-size: 13px;">PRIME BOOKING Verified</span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Score Summary Box --}}
                <div class="col-md-3 border-end pe-md-4">
                    <small class="text-secondary d-block mb-1" style="font-size: 11px;">Overall Guest Rating</small>
                    <div class="d-flex align-items-baseline gap-1">
                        <h2 class="fw-bold text-primary mb-0" style="font-size: 34px;">{{ $score }}</h2>
                        <span class="text-muted" style="font-size: 14px;">/10</span>
                    </div>
                    <strong class="d-block text-dark mt-1" style="font-size: 15px;">{{ $scoreNum >= 9 ? 'Exceptional' : ($scoreNum >= 8 ? 'Excellent' : 'Very Good') }}</strong>
                    <small class="text-primary fw-bold" style="font-size: 12px;"><i class="fa-solid fa-check-circle me-1"></i> From {{ $revCount }} verified reviews</small>
                </div>

                {{-- Green Sub-score Progress Bars --}}
                <div class="col-md-9">
                    <div class="row g-3">
                        @foreach($property->sub_scores as $subName => $subVal)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                                <span>{{ ucfirst(str_replace('_', ' ', $subName)) }}</span>
                                <strong class="text-dark">{{ number_format((float)$subVal, 1) }}</strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, round((float)$subVal * 10)) }}%; background-color: #16a34a !important;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- AI-Powered Summary Box --}}
            <div class="p-3 mb-4 rounded-3" style="background: #faf5ff; border: 1.5px solid #d8b4fe;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="d-flex align-items-center gap-1.5" style="color: #6b21a8; font-size: 14px; font-weight: 700;">
                        <i class="fa-solid fa-wand-magic-sparkles text-purple"></i> What guests liked
                    </strong>
                    <span class="text-muted small" style="font-size: 11.5px;">Summarized by AI</span>
                </div>
                <div class="row g-3" style="font-size: 12.5px; color: #334155; line-height: 1.55;">
                    @php
                        $aiHighlightsList = array_values($property->ai_highlights);
                        $halfCount = ceil(count($aiHighlightsList) / 2);
                    @endphp
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            @foreach(array_slice($aiHighlightsList, 0, (int)$halfCount) as $hl)
                            <li><strong>• {{ $hl['title'] }}:</strong> {{ $hl['desc'] }} <span class="text-muted">({{ $hl['count'] }} mentions)</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            @foreach(array_slice($aiHighlightsList, (int)$halfCount) as $hl)
                            <li><strong>• {{ $hl['title'] }}:</strong> {{ $hl['desc'] }} <span class="text-muted">({{ $hl['count'] }} mentions)</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Show reviews that mention chips (Interactive Client Filter) --}}
            <div class="mb-4">
                <small class="text-muted d-block fw-bold mb-2" style="font-size: 12px;">Show reviews that mention</small>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold mention-filter-btn" onclick="filterReviewsByMention('all', this)" style="font-size: 11.5px; background: #2067e1;">All Reviews</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('service', this)" style="font-size: 11.5px;">Service</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('atmosphere', this)" style="font-size: 11.5px;">Atmosphere</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('clean', this)" style="font-size: 11.5px;">Cleanliness</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('location', this)" style="font-size: 11.5px;">Location</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('breakfast', this)" style="font-size: 11.5px;">Breakfast</button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 text-dark border mention-filter-btn" onclick="filterReviewsByMention('value', this)" style="font-size: 11.5px;">Value for money</button>
                </div>
            </div>

            {{-- Verified Guest Reviews List (Dynamic from Database) --}}
            <div class="d-flex flex-column gap-3 border-top pt-4" id="verifiedReviewsContainer">
                @forelse($reviews ?? $property->reviews ?? [] as $rev)
                <div class="row g-3 p-3 rounded-3 bg-light border verified-review-card">
                    <div class="col-md-4 border-end pe-md-3">
                        <strong class="text-primary d-block fw-bold" style="font-size: 17px;">{{ number_format((float)($rev->rating ?? 9.0), 1) }} {{ ($rev->rating ?? 9) >= 9 ? 'Exceptional' : 'Excellent' }}</strong>
                        <div class="text-dark fw-bold mt-1" style="font-size: 13px;">{{ $rev->user->name ?? 'Verified Guest' }}</div>
                        <small class="text-muted d-block" style="font-size: 11.5px;">🧳 Verified Stay</small>
                        <small class="text-secondary d-block mt-1" style="font-size: 11px;">📅 {{ $rev->created_at ? $rev->created_at->diffForHumans() : 'Recent review' }}</small>
                    </div>
                    <div class="col-md-8">
                        @if(!empty($rev->title))
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px;">"{{ $rev->title }}"</h6>
                        @endif
                        <p class="text-secondary mb-2" style="font-size: 12.5px; line-height: 1.6;">
                            {{ $rev->comment }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small" style="font-size: 11px;">
                            <span>Verified guest review</span>
                            <span>Did you find this review helpful? <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none">Yes</a> / <a href="javascript:void(0)" class="text-secondary text-decoration-none">No</a></span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 bg-light rounded-3 my-2 border">
                    <i class="fa-solid fa-star text-warning fs-3 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">No written reviews yet</h6>
                    <p class="text-secondary small mb-3">Be the first verified guest to share your experience staying at {{ $property->name }}!</p>
                    <a href="{{ route('booking.form', $property->id) }}" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold" style="background: #2067e1;">Book Your Stay Now</a>
                </div>
                @endforelse
            </div>
        </div>
            </div>
        </div>

        {{-- Check in. Step out. Experiences Banner (Agoda 1:1 Parity) --}}
        <div class="card border-0 shadow-xs rounded-3 overflow-hidden bg-white mb-4 position-relative" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 1px solid #cbd5e1 !important;">
            <div class="row g-0 align-items-center">
                <div class="col-md-6 p-4">
                    <h4 class="fw-bold text-dark mb-1" style="font-size: 22px; font-family: 'Plus Jakarta Sans', sans-serif;">Check in. Step out.</h4>
                    <p class="text-secondary mb-3" style="font-size: 13px;">
                        Experiences on Prime Booking. Because your room is just the beginning.
                    </p>
                    <div class="d-flex align-items-center gap-2 mb-3 text-dark fw-bold" style="font-size: 12.5px;">
                        <i class="fa-solid fa-location-dot text-danger"></i> Excellent location in {{ $property->city }}
                    </div>
                    <a href="#location" class="btn btn-primary fw-bold px-4 py-2" style="background: #2067e1; border-radius: 6px; font-size: 13px;">
                        SHOW EXPERIENCES
                    </a>
                </div>
                <div class="col-md-6 p-3 text-center">
                    <img src="{{ $gallery[0] ?? '' }}" class="img-fluid rounded-3 shadow-xs" style="max-height: 180px; width: 100%; object-fit: cover;" alt="Experiences in {{ $property->city }}">
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

                        {{-- Proximity Breakdown (Walking & Driving Estimation) --}}
                        <div class="mt-3 pt-3 border-top">
                            <strong class="d-block text-dark mb-2" style="font-size: 12.5px;"><i class="fa-solid fa-person-walking me-1 text-primary"></i> Proximity &amp; Travel Highlights:</strong>
                            <div class="d-flex flex-column gap-1.5">
                                @foreach($property->proximity_breakdown as $prox)
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-white border" style="font-size: 11.5px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="{{ $prox['icon'] }}"></i>
                                        <span class="text-dark fw-semibold">{{ $prox['name'] }}</span>
                                    </div>
                                    <span class="badge bg-light text-dark border fw-bold" style="font-size: 10.5px;">
                                        {{ $prox['distance'] }} ({{ $prox['time_est'] }})
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
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

        {{-- 16. Discover new places Section (Screenshot Parity) --}}
        <div class="mb-4 position-relative">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Discover new places</h5>
            <div class="position-relative">
                <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: none;">
                    @php
                        $places = [
                            ['name' => 'United States', 'img' => 'https://images.unsplash.com/photo-1506146332389-18140dc7b2fb?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Malaysia', 'img' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'India', 'img' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Philippines', 'img' => 'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Indonesia', 'img' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Singapore', 'img' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Thailand', 'img' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'China', 'img' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?auto=format&fit=crop&w=400&q=80'],
                            ['name' => "Cox's Bazar", 'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Sylhet', 'img' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=400&q=80'],
                        ];
                    @endphp
                    @foreach($places as $pl)
                    <a href="{{ route('search.index') }}?destination={{ urlencode($pl['name']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none text-white flex-shrink-0" style="width: 175px; height: 130px; position: relative;">
                        <img src="{{ $pl['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $pl['name'] }}">
                        <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                            <strong class="d-block text-white mb-0" style="font-size: 13.5px; font-weight: 700;">{{ $pl['name'] }}</strong>
                        </div>
                    </a>
                    @endforeach
                </div>
                <button class="btn btn-light rounded-circle shadow-md position-absolute top-50 end-0 translate-middle-y me-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; z-index: 10; border: 1px solid #cbd5e1; background: #fff;" onclick="this.previousElementSibling.scrollBy({left: 200, behavior: 'smooth'});">
                    <i class="fa-solid fa-chevron-right text-dark fs-6"></i>
                </button>
            </div>
        </div>

        {{-- 17. Trending Cities Section (Screenshot Parity) --}}
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
<div id="agodaDetailStickyBottomBar" class="position-fixed bottom-0 start-0 w-100 bg-white border-top shadow-lg py-2.5" style="z-index: 1050; border-color: #cbd5e1 !important;">
    <div class="agoda-page-container d-flex align-items-center justify-content-between">
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

{{-- Agoda 1:1 Dual-Mode Full-Screen Photo Gallery Modal (Grid & Slideshow Views) --}}
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 rounded-0 bg-white d-flex flex-column" style="height: 100vh;">
            
            {{-- 1. Top Header Bar with Mode Switch, Category Tabs, and Close Button --}}
            <div class="modal-header border-bottom py-2.5 px-4 bg-white d-flex align-items-center justify-content-between flex-shrink-0" style="min-height: 56px; z-index: 1055;">
                <div class="d-flex align-items-center gap-3">
                    {{-- Mode Toggle Button: [Slideshow] in Grid Mode, [Gallery] in Slideshow Mode --}}
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-xs" id="agodaGalleryModeToggleBtn" onclick="toggleAgodaGalleryMode();" style="font-size: 12.5px; border-color: #cbd5e1; color: #2067e1;">
                        <i class="fa-solid fa-play" id="agodaGalleryModeIcon"></i>
                        <span id="agodaGalleryModeText">Slideshow</span>
                    </button>

                    {{-- Category Navigation Tabs --}}
                    <div class="d-flex align-items-center gap-1 overflow-x-auto" style="scrollbar-width: none;">
                        <button type="button" class="agoda-modal-cat-tab active" data-cat="all" onclick="filterAgodaGallery('all', this);">
                            All ({{ count($galleryCategorized['all']) }})
                        </button>
                        <button type="button" class="agoda-modal-cat-tab" data-cat="rooms" onclick="filterAgodaGallery('rooms', this);">
                            Rooms ({{ count($galleryCategorized['rooms']) }})
                        </button>
                        <button type="button" class="agoda-modal-cat-tab" data-cat="property_views" onclick="filterAgodaGallery('property_views', this);">
                            Property views ({{ count($galleryCategorized['property_views']) }})
                        </button>
                        <button type="button" class="agoda-modal-cat-tab" data-cat="facilities" onclick="filterAgodaGallery('facilities', this);">
                            Facilities ({{ count($galleryCategorized['facilities']) }})
                        </button>
                        <button type="button" class="agoda-modal-cat-tab" data-cat="other" onclick="filterAgodaGallery('other', this);">
                            Other ({{ count($galleryCategorized['other']) }})
                        </button>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- 2. Modal Body: 75% Left Main Stage + 25% Right Sidebar --}}
            <div class="modal-body p-0 d-flex flex-grow-1 overflow-hidden">
                
                {{-- Left Stage Viewport --}}
                <div class="flex-grow-1 d-flex flex-column position-relative overflow-hidden" style="background: #f1f5f9;" id="agodaGalleryLeftStage">
                    
                    {{-- VIEW A: Grid View (Default: 3-column scrollable thumbnail grid) --}}
                    <div id="agodaGalleryGridView" class="p-3 overflow-y-auto w-100 h-100">
                        <div class="row g-2.5" id="agodaGalleryGridItemsContainer">
                            @foreach($galleryCategorized['all'] as $gIdx => $gItem)
                            <div class="col-6 col-md-4 agoda-grid-photo-card" data-category="{{ $gItem['cat_key'] }}" onclick="openAgodaSlideshow({{ $gIdx }});">
                                <div class="rounded-3 overflow-hidden shadow-xs bg-dark position-relative ratio ratio-4x3" style="cursor: pointer;">
                                    <img src="{{ $gItem['url'] }}" class="w-100 h-100 object-fit-cover agoda-grid-img" alt="{{ $gItem['title'] }}" loading="lazy">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-white small opacity-0 agoda-grid-overlay" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); transition: opacity 0.2s;">
                                        <span class="badge bg-dark bg-opacity-75" style="font-size: 10px;">{{ $gItem['category'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- VIEW B: Slideshow View (Large Centered Stage + Filmstrip) --}}
                    <div id="agodaGallerySlideshowView" class="w-100 h-100 d-none flex-column position-relative" style="background: #0f172a;">
                        
                        {{-- Centered Large Photo Stage --}}
                        <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative p-3 overflow-hidden">
                            <img id="agodaSlideshowActiveImg" src="{{ $galleryCategorized['all'][0]['url'] ?? '' }}" class="img-fluid rounded-2 shadow-lg" style="max-height: 68vh; max-width: 90%; object-fit: contain; transition: all 0.25s ease;" alt="Slideshow Active">

                            {{-- Left Prev Arrow Button --}}
                            <button type="button" class="btn btn-light rounded-circle shadow-lg position-absolute top-50 start-0 translate-middle-y ms-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; z-index: 10; border: 1px solid #cbd5e1;" onclick="prevAgodaSlide();">
                                <i class="fa-solid fa-chevron-left text-dark fs-5"></i>
                            </button>

                            {{-- Right Next Arrow Button --}}
                            <button type="button" class="btn btn-light rounded-circle shadow-lg position-absolute top-50 end-0 translate-middle-y me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; z-index: 10; border: 1px solid #cbd5e1;" onclick="nextAgodaSlide();">
                                <i class="fa-solid fa-chevron-right text-dark fs-5"></i>
                            </button>

                            {{-- Bottom Left Category Tag --}}
                            <div class="position-absolute bottom-0 start-0 m-3" style="z-index: 10;">
                                <span class="badge bg-black bg-opacity-75 text-white px-3 py-1.5 rounded-pill shadow-xs fw-semibold" id="agodaSlideshowCategoryBadge" style="font-size: 12px;">
                                    Facilities
                                </span>
                            </div>
                        </div>

                        {{-- Bottom Thumbnail Filmstrip Carousel --}}
                        <div class="py-2 px-3 bg-black bg-opacity-90 border-top border-secondary border-opacity-25 flex-shrink-0 d-flex align-items-center gap-2 overflow-x-auto position-relative" id="agodaFilmstripContainer" style="scrollbar-width: thin;">
                            @foreach($galleryCategorized['all'] as $fIdx => $fItem)
                            <div class="agoda-filmstrip-thumb flex-shrink-0 @if($fIdx === 0) active @endif" data-index="{{ $fIdx }}" data-category="{{ $fItem['cat_key'] }}" onclick="openAgodaSlideshow({{ $fIdx }});" style="width: 76px; height: 52px; cursor: pointer; border-radius: 4px; overflow: hidden; border: 2px solid transparent; transition: all 0.2s;">
                                <img src="{{ $fItem['url'] }}" class="w-100 h-100 object-fit-cover" alt="Thumb {{ $fIdx }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar Panel (Agoda Parity: Things you'll love + Price + Booking CTA) --}}
                <div class="agoda-modal-sidebar bg-white border-start p-4 d-flex flex-column justify-content-between flex-shrink-0 overflow-y-auto" style="width: 320px; z-index: 1050;">
                    <div>
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            Things you'll love
                        </h6>
                        <div class="d-flex flex-column gap-2.5 mb-4" style="font-size: 13px; color: #334155;">
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fa-solid fa-wifi text-secondary" style="width: 16px;"></i>
                                <span>Free Wi-Fi in all rooms!</span>
                            </div>
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fa-solid fa-hot-tub-person text-secondary" style="width: 16px;"></i>
                                <span>Swimming pool &amp; Hot tub</span>
                            </div>
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fa-solid fa-snowflake text-secondary" style="width: 16px;"></i>
                                <span>Air conditioning</span>
                            </div>
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fa-solid fa-door-open text-secondary" style="width: 16px;"></i>
                                <span>Balcony/terrace</span>
                            </div>
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fa-solid fa-utensils text-secondary" style="width: 16px;"></i>
                                <span>Breakfast &amp; Restaurant</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Pricing & CTA Area --}}
                    <div class="pt-3 border-top">
                        @if(!empty($property->original_price) && $property->original_price > $property->price_per_night)
                        @php
                            $discountPct = round((($property->original_price - $property->price_per_night) / $property->original_price) * 100);
                        @endphp
                        <span class="badge bg-danger fw-bold px-2 py-1 mb-1.5" style="font-size: 11px;">
                            {{ $discountPct }}% OFF TODAY
                        </span>
                        @endif
                        <div class="d-flex align-items-baseline justify-content-between mb-3">
                            <span class="text-secondary small" style="font-size: 12px;">Starts from</span>
                            <div class="text-end">
                                <strong style="color: #d93025; font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    {{ CurrencyService::format($property->price_per_night) }}
                                </strong>
                                <small class="text-muted d-block" style="font-size: 11px;">per night</small>
                            </div>
                        </div>
                        <a href="#rooms" class="btn btn-primary w-100 fw-bold rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center" style="background-color: #2067e1; font-size: 14px;" data-bs-dismiss="modal">
                            See available rooms
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Interactive Real-Time Location & Google Maps Modal (Agoda Parity) --}}
<div class="modal fade" id="interactiveMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 17px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fa-solid fa-map-location-dot text-primary me-2"></i> {{ $property->name }} — Location &amp; Map
                    </h5>
                    <small class="text-muted" style="font-size: 12px;">{{ $property->address ?: ($property->city . ', Bangladesh') }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @php
                        $lat = $property->latitude ?: '23.8759';
                        $lng = $property->longitude ?: '90.3795';
                        $googleDirUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}";
                    @endphp
                    <a href="{{ $googleDirUrl }}" target="_blank" class="btn btn-primary btn-sm fw-bold px-3 py-1.5 rounded-pill" style="background:#2067e1; font-size:12px;">
                        <i class="fa-solid fa-diamond-turn-right me-1"></i> Get Directions
                    </a>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 550px; background: #e2e8f0;">
                @if(!empty($property->map_embed_url))
                    <iframe src="{{ $property->map_embed_url }}" class="w-100 h-100 border-0" allowfullscreen loading="lazy"></iframe>
                @else
                    <iframe src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&hl=en&z=15&output=embed" class="w-100 h-100 border-0" allowfullscreen loading="lazy"></iframe>
                @endif

                {{-- Floating Property Mini Card (Agoda Style) --}}
                <div class="position-absolute top-0 start-0 m-3 bg-white p-3 rounded-3 shadow-lg border" style="max-width: 320px; z-index: 10; border-color: #cbd5e1 !important;">
                    <div class="d-flex gap-2.5">
                        <img src="{{ $gallery[0] ?? '' }}" class="rounded-2" style="width: 70px; height: 60px; object-fit: cover;" alt="{{ $property->name }}">
                        <div>
                            <strong class="d-block text-dark fw-bold" style="font-size: 13px; line-height: 1.2;">{{ Str::limit($property->name, 26) }}</strong>
                            <div class="text-warning small mb-1" style="font-size: 11px;">
                                @for($i = 0; $i < ($property->star_rating ?? 4); $i++)★@endfor
                            </div>
                            <span class="badge bg-success" style="font-size: 10px;">{{ $score }} Excellent</span>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-top text-secondary small" style="font-size: 11.5px;">
                        <i class="fa-solid fa-person-walking text-primary me-1"></i> <strong>{{ $property->nearest_landmark ?: 'Near central transit point' }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex align-items-center justify-content-between">
                <small class="text-muted" style="font-size: 12px;">GPS Coordinates: <code>{{ $lat }}, {{ $lng }}</code></small>
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Dedicated High-Definition Video Tour Modal (Agoda & Booking.com Parity) --}}
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
<div class="modal fade" id="videoTourModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-black text-white overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-dark d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 16px;">
                    <i class="fa-solid fa-circle-play text-danger me-2"></i> {{ $property->name }} — Official Video Tour
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="const iframe = this.closest('.modal-content').querySelector('iframe'); if(iframe) iframe.src = iframe.src;"></button>
            </div>
            <div class="modal-body p-0 text-center" style="background:#000; min-height: 500px;">
                @if($isYoutube)
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?autoplay=1&rel=0&modestbranding=1" 
                            style="border:0;" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
                @else
                <video controls autoplay class="w-100 h-100" style="max-height: 75vh; object-fit: contain;">
                    <source src="{{ $videoUrl }}" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function updateDetailDateDisplay(type, dateStr) {
        if (!dateStr) return;
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return;

        const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
        const dayOfWeek = dayNames[d.getDay()];

        if (type === 'checkIn') {
            document.getElementById('detailCheckInText').textContent = formattedDate;
            document.getElementById('detailCheckInDay').textContent = dayOfWeek;
        } else {
            document.getElementById('detailCheckOutText').textContent = formattedDate;
            document.getElementById('detailCheckOutDay').textContent = dayOfWeek;
        }
    }

    function adjustDetailCounter(type, delta) {
        if (type === 'adults') {
            let input = document.getElementById('detailAdultsInput');
            let val = parseInt(input.value) || 2;
            val = Math.max(1, Math.min(20, val + delta));
            input.value = val;
            document.getElementById('detailAdultsValText').textContent = val;
            document.getElementById('detailGuestCountText').textContent = `${val} adult${val > 1 ? 's' : ''}`;
        } else if (type === 'rooms') {
            let input = document.getElementById('detailRoomsInput');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, Math.min(10, val + delta));
            input.value = val;
            document.getElementById('detailRoomsValText').textContent = val;
            document.getElementById('detailRoomCountText').textContent = `${val} room${val > 1 ? 's' : ''}`;
        }
    }

    // ─── Instant Client-Side Zero-Latency Room Filtering (Agoda Standard) ───
    const activeRoomFilters = new Set();

    function toggleRoomFilter(filterKey, element) {
        if (activeRoomFilters.has(filterKey)) {
            activeRoomFilters.delete(filterKey);
            element.style.background = '#ffffff';
            element.style.color = '#334155';
            element.style.borderColor = '#cbd5e1';
        } else {
            activeRoomFilters.add(filterKey);
            element.style.background = '#e0f2fe';
            element.style.color = '#0284c7';
            element.style.borderColor = '#0284c7';
        }

        applyRoomFilters();
    }

    function applyRoomFilters() {
        const cards = document.querySelectorAll('.agoda-room-listing-card');
        let visibleCount = 0;

        cards.forEach(card => {
            let isMatch = true;

            activeRoomFilters.forEach(f => {
                const val = card.getAttribute('data-' + f);
                if (val !== 'true') {
                    isMatch = false;
                }
            });

            if (isMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const countBadge = document.getElementById('roomFilterCount');
        if (countBadge) {
            if (activeRoomFilters.size === 0) {
                countBadge.textContent = 'Showing all room types';
            } else {
                countBadge.textContent = `Showing ${visibleCount} matching room${visibleCount === 1 ? '' : 's'}`;
            }
        }
    }

    // ─── Review Mention Filter Chips ───
    function filterReviewsByMention(keyword, element) {
        document.querySelectorAll('.mention-filter-btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-light', 'text-dark', 'border');
            btn.style.background = '';
        });

        element.classList.remove('btn-light', 'text-dark', 'border');
        element.classList.add('btn-primary');
        element.style.background = '#2067e1';

        const reviewCards = document.querySelectorAll('.verified-review-card');
        reviewCards.forEach(rc => {
            if (!keyword || keyword === 'all') {
                rc.style.display = 'flex';
            } else {
                const text = rc.innerText.toLowerCase();
                if (text.includes(keyword.toLowerCase())) {
                    rc.style.display = 'flex';
                } else {
                    rc.style.display = 'none';
                }
            }
        });
    }

    // ─── Smooth Scroll Spy for Sticky Sub-Nav Bar ───
    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.agoda-nav-item');
        const sections = document.querySelectorAll('div[id]');

        window.addEventListener('scroll', function () {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 140;
                if (window.pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    });

    // ─── Agoda 1:1 Dual-Mode Gallery Manager ───
    const agodaGalleryItems = @json($galleryCategorized['all']);
    let agodaGalleryCurrentIdx = 0;
    let agodaGalleryMode = 'grid'; // 'grid' or 'slideshow'
    let agodaGalleryActiveCat = 'all';

    function toggleAgodaGalleryMode() {
        if (agodaGalleryMode === 'grid') {
            setAgodaGalleryMode('slideshow');
        } else {
            setAgodaGalleryMode('grid');
        }
    }

    function setAgodaGalleryMode(mode) {
        agodaGalleryMode = mode;
        const gridView = document.getElementById('agodaGalleryGridView');
        const slideView = document.getElementById('agodaGallerySlideshowView');
        const modeText = document.getElementById('agodaGalleryModeText');
        const modeIcon = document.getElementById('agodaGalleryModeIcon');

        if (mode === 'slideshow') {
            if (gridView) gridView.classList.add('d-none');
            if (slideView) {
                slideView.classList.remove('d-none');
                slideView.classList.add('d-flex');
            }
            if (modeText) modeText.textContent = 'Gallery';
            if (modeIcon) modeIcon.className = 'fa-solid fa-table-cells';
            renderAgodaSlideshow(agodaGalleryCurrentIdx);
        } else {
            if (slideView) {
                slideView.classList.remove('d-flex');
                slideView.classList.add('d-none');
            }
            if (gridView) gridView.classList.remove('d-none');
            if (modeText) modeText.textContent = 'Slideshow';
            if (modeIcon) modeIcon.className = 'fa-solid fa-play';
        }
    }

    function openAgodaSlideshow(index) {
        agodaGalleryCurrentIdx = index;
        setAgodaGalleryMode('slideshow');
        renderAgodaSlideshow(index);
    }

    function renderAgodaSlideshow(index) {
        if (!agodaGalleryItems || agodaGalleryItems.length === 0) return;
        if (index < 0) index = agodaGalleryItems.length - 1;
        if (index >= agodaGalleryItems.length) index = 0;

        agodaGalleryCurrentIdx = index;
        const item = agodaGalleryItems[index];

        const activeImg = document.getElementById('agodaSlideshowActiveImg');
        const badge = document.getElementById('agodaSlideshowCategoryBadge');
        if (activeImg) activeImg.src = item.url;
        if (badge) badge.textContent = item.category || 'Photo';

        // Update active filmstrip thumbnail
        document.querySelectorAll('.agoda-filmstrip-thumb').forEach(thumb => {
            const tIdx = parseInt(thumb.getAttribute('data-index'));
            if (tIdx === index) {
                thumb.classList.add('active');
                thumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            } else {
                thumb.classList.remove('active');
            }
        });
    }

    function prevAgodaSlide() {
        renderAgodaSlideshow(agodaGalleryCurrentIdx - 1);
    }

    function nextAgodaSlide() {
        renderAgodaSlideshow(agodaGalleryCurrentIdx + 1);
    }

    function filterAgodaGallery(catKey, tabElement) {
        agodaGalleryActiveCat = catKey;
        document.querySelectorAll('.agoda-modal-cat-tab').forEach(t => t.classList.remove('active'));
        if (tabElement) tabElement.classList.add('active');

        // Filter Grid View items
        document.querySelectorAll('.agoda-grid-photo-card').forEach(card => {
            const c = card.getAttribute('data-category');
            if (catKey === 'all' || c === catKey) {
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });

        // Filter Filmstrip items
        document.querySelectorAll('.agoda-filmstrip-thumb').forEach(thumb => {
            const c = thumb.getAttribute('data-category');
            if (catKey === 'all' || c === catKey) {
                thumb.classList.remove('d-none');
            } else {
                thumb.classList.add('d-none');
            }
        });
    }

    // Keyboard Arrow Keys Navigation for Agoda Slideshow
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('galleryModal');
        if (!modal || !modal.classList.contains('show')) return;

        if (agodaGalleryMode === 'slideshow') {
            if (e.key === 'ArrowLeft') prevAgodaSlide();
            if (e.key === 'ArrowRight') nextAgodaSlide();
        }
    });

    // Mobile Touch Swipe Gesture Support
    let touchStartX = 0;
    let touchEndX = 0;
    const slideshowContainer = document.getElementById('agodaGallerySlideshowView');
    if (slideshowContainer) {
        slideshowContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        slideshowContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipeGesture();
        }, { passive: true });
    }

    function handleSwipeGesture() {
        const threshold = 45;
        if (touchEndX < touchStartX - threshold) {
            nextAgodaSlide(); // Swiped Left -> Next Image
        }
        if (touchEndX > touchStartX + threshold) {
            prevAgodaSlide(); // Swiped Right -> Prev Image
        }
    }

    // Share Property Link Helper
    function sharePropertyLink() {
        if (navigator.share) {
            navigator.share({
                title: '{{ addslashes($property->name) }}',
                text: 'Check out this hotel on Prime Booking!',
                url: window.location.href
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Property link copied to clipboard!');
            });
        }
    }
</script>
@endsection

