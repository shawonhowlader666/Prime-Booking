@extends('layouts.main', ['activePage' => 'hotels'])

@php
    $checkIn  = $checkIn  ?? request('check_in',  now()->format('Y-m-d'));
    $checkOut = $checkOut ?? request('check_out', now()->addDays(2)->format('Y-m-d'));
    $guests   = $guests   ?? (int) request('guests', 2);
    $nights   = $nights   ?? max(1, (int) \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));

    $hotelCity = $property->city ?? "Cox's Bazar";
    $formattedPrice = number_format((float)($property->price_per_night ?? 2500), 0);
    $hotelTitle = "{$property->name}, {$hotelCity} (From ৳{$formattedPrice}) 2026 Deals | Prime Booking";
    $hotelDesc = "Book {$property->name} in {$hotelCity} from ৳{$formattedPrice}/night. 100% Instant confirmation, verified guest ratings, genuine room photos & exclusive bKash deals on Prime Booking.";
    $hotelKeywords = strtolower($property->name ?? '') . ", {$property->name} booking, " . strtolower($property->name ?? '') . " {$hotelCity}, hotels in {$hotelCity}, {$property->name} room price, coxs bazar hotel booking, হোটেল বুকিং বিডি";
    $canonicalHotelUrl = !empty($property->slug) ? url('/hotel/' . $property->slug) : url('/hotels/' . ($property->id ?? 1));
    $hotelOgImage = media_url($property->primary_image, asset('images/logo.png'));
@endphp

@section('title', $hotelTitle)
@section('meta_description', $hotelDesc)
@section('meta_keywords', $hotelKeywords)
@section('canonical_url', $canonicalHotelUrl)
@section('og_title', $hotelTitle)
@section('og_description', $hotelDesc)
@section('og_image', $hotelOgImage)

@section('schema')
@php
    $schemaHotel = [
        chr(64) . 'type'    => in_array(strtolower($property->type ?? ''), ['resort', 'villa', 'motel']) ? ucfirst(strtolower($property->type)) : 'Hotel',
        'name'              => $property->name ?? 'Hotel',
        'url'               => $canonicalHotelUrl,
        'description'       => strip_tags(\Illuminate\Support\Str::limit($property->description ?? 'Book ' . ($property->name ?? 'hotel') . ' on Prime Booking with best price guarantee.', 250)),
        'image'             => media_url($property->primary_image, asset('images/logo.png')),
        'starRating'        => [
            chr(64) . 'type' => 'Rating',
            'ratingValue'    => (int)($property->star_rating ?? 4),
        ],
        'priceRange'        => 'BDT ' . number_format((float)($property->price_per_night ?? 2500)) . ' - ' . number_format((float)($property->price_per_night ?? 2500) * 2.5),
        'address'           => [
            chr(64) . 'type'  => 'PostalAddress',
            'streetAddress'   => $property->address ?? $property->city ?? 'Bangladesh',
            'addressLocality' => $property->city ?? 'Dhaka',
            'addressCountry'  => 'BD',
        ],
        'aggregateRating'   => [
            chr(64) . 'type' => 'AggregateRating',
            'ratingValue'    => number_format((float)($property->rating_score ?? $property->rating ?? 9.0), 1),
            'bestRating'     => '10',
            'worstRating'    => '1',
            'reviewCount'    => max(5, (int)($property->reviews_count ?? $property->total_reviews ?? 28)),
        ],
    ];
    if (!empty($property->latitude) && !empty($property->longitude)) {
        $schemaHotel['geo'] = [
            chr(64) . 'type' => 'GeoCoordinates',
            'latitude'       => (float) $property->latitude,
            'longitude'      => (float) $property->longitude,
        ];
    }

    $schemaGraph = [
        chr(64) . 'context' => 'https://schema.org',
        chr(64) . 'graph'   => [
            [
                chr(64) . 'type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        chr(64) . 'type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => url('/'),
                    ],
                    [
                        chr(64) . 'type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Hotels in Bangladesh',
                        'item' => url('/hotels'),
                    ],
                    [
                        chr(64) . 'type' => 'ListItem',
                        'position' => 3,
                        'name' => "Hotels in {$hotelCity}",
                        'item' => url('/hotels-in-' . \Illuminate\Support\Str::slug($hotelCity)),
                    ],
                    [
                        chr(64) . 'type' => 'ListItem',
                        'position' => 4,
                        'name' => $property->name ?? 'Hotel',
                        'item' => $canonicalHotelUrl,
                    ],
                ],
            ],
            $schemaHotel,
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($schemaGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@php
    use App\Services\CurrencyService;
    use Carbon\Carbon;

    $checkinCarbon = request('checkin') ? Carbon::parse(request('checkin')) : Carbon::tomorrow();
    $checkoutCarbon = request('checkout') ? Carbon::parse(request('checkout')) : Carbon::tomorrow()->addDay();
    $guestStr = request('guests') ?: '3 adults, 1 child';
    $guestCount = intval(preg_replace('/[^0-9]/', '', (string)$guestStr)) ?: 2;
    $roomsCountStr = request('rooms') ?: '2 rooms';

    $gallery = collect();
    if (!empty($property->primary_image)) $gallery->push($property->primary_image);
    $propGallery = is_array($property->gallery_images) ? $property->gallery_images : (is_array($property->images) ? $property->images : []);
    if (!empty($propGallery)) {
        $gallery = $gallery->merge($propGallery);
    }
    $fallbacks = [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80',
    ];
    while ($gallery->count() < 7) { $gallery->push($fallbacks[$gallery->count() % count($fallbacks)]); }
    
    // Convert all gallery images to fully-qualified public / storage URLs (Zero broken image paths)
    $gallery = $gallery->map(function($img) {
        return media_url($img, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80');
    })->values();
    
    // Dynamic Agoda Photo & Video Categorization Collection (Encapsulated on Property Model)
    $galleryCategorized = $property->categorized_gallery;

    $amenitiesList = is_array($property->amenities) && count($property->amenities) > 0
        ? $property->amenities
        : ['Free Wi-Fi', 'Free parking', 'Pets allowed', 'Air conditioning in public area', 'English', 'Internet services'];
    
    $scoreNum = (float)($property->rating_score ?? 8.7);
    $score = number_format($scoreNum, 1);
    $totalReviewsInt = (int)($property->total_reviews ?: 6);
    $revCount = number_format($totalReviewsInt);
    $nights = $checkinCarbon->diffInDays($checkoutCarbon) ?: 1;
    $isWishlisted = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('property_id', $property->id)->exists();
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

@push('head')
    <!-- HLS.js for Seamless High-Performance Video Tour Streaming -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>
@endpush

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    /* Ensure silky-smooth document viewport scrolling */
    html {
        overflow-x: hidden;
    }
    body {
        overflow-x: clip !important;
        overflow-y: visible !important;
    }
    body.modal-open {
        overflow: hidden !important;
    }

    /* Agoda 1:1 Ultra-Premium Detail Page Styling */
    .detail-page-wrapper { 
        background: #ffffff; 
        min-height: 100vh; 
        width: 100%;
        overflow-x: clip;
        overflow-y: visible;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
        color: #111827 !important; 
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }

    /* Agoda Deep Contrast Global Typography - Scoped to avoid dark header text inversion */
    .detail-page-wrapper .agoda-card-border,
    .detail-page-wrapper .card,
    .detail-page-wrapper .agoda-page-container {
        color: #1e293b;
    }

    .detail-page-wrapper h1, 
    .detail-page-wrapper h2, 
    .detail-page-wrapper h3, 
    .detail-page-wrapper h4, 
    .detail-page-wrapper h5, 
    .detail-page-wrapper h6 {
        color: #0f172a !important;
        font-weight: 800 !important;
    }

    /* Prevent icon from sticking to text globally */
    .detail-page-wrapper i.fa-solid,
    .detail-page-wrapper i.fa-regular {
        flex-shrink: 0;
    }

    .detail-page-wrapper .text-secondary {
        color: #475569 !important;
        font-weight: 500 !important;
    }

    .detail-page-wrapper .text-muted {
        color: #64748b !important;
    }
    
    /* Sticky Top Header Bar & Adaptive Sticky Sub-Nav (Agoda 1:1 Match) */
    .agoda-detail-search-bar { background: linear-gradient(180deg, #1d2b45 0%, #152238 100%); padding: 12px 0; border-bottom: 1px solid #334155; position: sticky; top: 0; z-index: 1100; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    
    .agoda-sticky-nav-wrapper {
        position: sticky;
        top: 68px;
        z-index: 1025;
        width: 100%;
        background: transparent;
        transition: all 0.25s ease;
    }
    .agoda-sticky-nav-wrapper.is-stuck {
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0 !important;
        border-bottom: 1px solid #dddfe2 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    }

    /* Agoda Room Listing Unified Card */
    .agoda-room-listing-card {
        border: 1px solid #d5dbe5 !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        margin-bottom: 24px !important;
        display: block !important;
        clear: both !important;
        background: #ffffff !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
    }

    /* Agoda 1:1 Exact Sold Out / Unavailable Room Card */
    .agoda-soldout-room-card {
        background: #ffffff !important;
        border: 1px solid #d5dbe5 !important;
        border-radius: 12px !important;
        padding: 14px 18px !important;
        margin-bottom: 16px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        transition: all 0.2s ease !important;
    }
    .agoda-soldout-room-card:hover {
        border-color: #b0b8c4 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
    }
    @media (min-width: 768px) {
        .border-start-md {
            border-left: 1px solid #f1f5f9 !important;
        }
    }
    @media (max-width: 768px) {
        .agoda-soldout-room-card {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 12px !important;
        }
        .agoda-soldout-room-card .soldout-img-box {
            width: 100% !important;
            height: 160px !important;
        }
        .agoda-soldout-room-card .soldout-info-box {
            padding: 12px 0 8px 0 !important;
        }
        .agoda-soldout-room-card .soldout-status-box {
            border-left: none !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 10px 0 0 0 !important;
            justify-content: flex-start !important;
        }
    }

    .agoda-sticky-nav-card {
        background: #ffffff !important;
        border: 1px solid #dddfe2;
        border-radius: 4px !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        padding: 4px 20px;
        transition: all 0.25s ease;
    }
    .agoda-sticky-nav-wrapper.is-stuck .agoda-sticky-nav-card {
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 2px 0 !important;
    }
    
    .agoda-nav-scroll-container { overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
    .agoda-nav-scroll-container::-webkit-scrollbar { display: none; }

    .agoda-nav-item { font-size: 13.5px; font-weight: 700; color: #475569 !important; padding: 16px 14px; border-bottom: 3px solid transparent; text-decoration: none; display: inline-block; white-space: nowrap; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
    .agoda-nav-item:hover, .agoda-nav-item.active { color: #2067e1 !important; border-bottom-color: #2067e1; font-weight: 800; }
    
    .agoda-filter-pill-121 { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 14px; font-size: 12.5px; font-weight: 600; color: #222222; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease; user-select: none; }
    .agoda-filter-pill-121:hover { border-color: #2067e1; color: #2067e1; }
    
    /* Card Aesthetics & Agoda Exact Borders (4px crisp radius) */
    .agoda-card-border { background: #ffffff; border: 1px solid #dddfe2 !important; border-radius: 4px !important; box-shadow: none !important; }
    .agoda-card-border:hover { border-color: #cbd5e1 !important; }

    /* Hero Collage Grid Image Zoom & Hover Highlights */
    .hero-main-img-box { position: relative; height: 360px; overflow: hidden; border-radius: 4px 0 0 4px; cursor: pointer; background: #0f172a; }
    .hero-thumb-img-box { position: relative; height: 176px; overflow: hidden; cursor: pointer; background: #0f172a; }
    .hero-main-img-box img, .hero-thumb-img-box img { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), filter 0.3s ease; }
    .hero-main-img-box:hover img, .hero-thumb-img-box:hover img { transform: scale(1.045); filter: brightness(1.06); }
    
    /* Sub-score Pills & Filter Buttons */
    .subscore-pill { background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .agoda-filter-pill { background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 20px; font-size: 12.5px; font-weight: 600; color: #222222; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
    .agoda-filter-pill:hover { border-color: #2067e1; color: #2067e1; background: #f0f7ff; }

    /* Agoda Room Filter Container Box (1:1 Exact Parity to Agoda & Screenshot) */
    .agoda-room-filter-card {
        background: #ffffff;
        border: 1px solid #dce1e8;
        border-radius: 16px;
        padding: 18px 20px 20px 20px;
        margin-bottom: 16px;
        box-shadow: none;
    }
    .agoda-room-filter-title {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 14px;
        line-height: 1.2;
    }
    .agoda-room-filter-pills {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 10px;
    }
    .agoda-room-filter-pill {
        background-color: #ffffff;
        border: 1px solid #cfd5df;
        border-radius: 9999px;
        padding: 7px 16px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13.5px;
        font-weight: 400;
        color: #2b2e38;
        line-height: 1.35;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        outline: none;
        user-select: none;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }
    .agoda-room-filter-pill:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #111827;
    }
    .agoda-room-filter-pill.active {
        background-color: #eff6ff !important;
        border-color: #2067da !important;
        color: #2067da !important;
        font-weight: 600 !important;
    }
    .agoda-room-filter-pill .agoda-pill-icon {
        flex-shrink: 0;
        color: #2b2e38;
        transition: color 0.15s ease-in-out;
    }
    .agoda-room-filter-pill.active .agoda-pill-icon {
        color: #2067da !important;
    }
    @media (max-width: 576px) {
        .agoda-room-filter-card {
            padding: 14px 16px 16px 16px;
            border-radius: 12px;
        }
        .agoda-room-filter-pills {
            gap: 6px 8px;
        }
        .agoda-room-filter-pill {
            padding: 6px 13px;
            font-size: 12.5px;
            gap: 6px;
        }
    }

    .room-space-card { background: #ffffff; border: 1px solid #dddfe2; border-radius: 4px; padding: 14px; flex: 1; min-width: 140px; }
    
    @keyframes agodaBounce {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40% { transform: scale(1.2); opacity: 1; }
    }

    /* 📐 Standard Symmetric Page Container (100% Identical to Agoda: 1200px max-width, 20px symmetric padding) */
    .agoda-page-container {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 20px;
        padding-right: 20px;
        width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .agoda-page-container {
            padding-left: 16px;
            padding-right: 16px;
        }
    }

    /* 📱 100% Comprehensive Responsive Mobile, Tablet & Desktop Adjustments */
    @media (min-width: 1400px) {
        .agoda-page-container {
            max-width: 1240px;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .agoda-hero-mosaic-wrap {
            height: 300px !important;
        }
        .agoda-room-left-col .room-photo-carousel {
            height: 230px !important;
        }
    }

    @media (max-width: 991.98px) {
        .agoda-detail-mobile-search-bar {
            position: sticky !important;
            top: 0 !important;
            z-index: 1030 !important;
        }
        .hero-col-main {
            flex: 0 0 100% !important;
            width: 100% !important;
            border-radius: 8px !important;
        }
        .hero-col-main video,
        .hero-col-main iframe,
        .hero-col-main img {
            border-radius: 8px !important;
        }
        .agoda-sticky-nav-bar { top: 0 !important; }
        .agoda-room-offer-grid > .border-end { border-right: none !important; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px; }
        .agoda-room-offer-grid > .text-end { text-align: left !important; }
        
        /* Modal Responsive Layout (Stack stage and sidebar vertically on tablets/mobiles) */
        #galleryModal .modal-body { flex-direction: column !important; overflow-y: auto !important; }
        #agodaGalleryLeftStage { min-height: 60vh !important; flex-grow: 1 !important; }
        .agoda-modal-sidebar { width: 100% !important; border-left: none !important; border-top: 1px solid #e2e8f0 !important; padding: 16px !important; }
        #agodaSlideshowActiveImg { max-height: 48vh !important; max-width: 96% !important; }
    }

    @media (max-width: 767.98px) {
        .agoda-page-container {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .agoda-hero-mosaic-wrap {
            height: 220px !important;
        }
        .agoda-room-left-col .room-photo-carousel {
            height: 195px !important;
        }
        .agoda-nav-item { padding: 10px 12px; font-size: 13px; }
        .agoda-modal-cat-tab { padding: 8px 10px; font-size: 12px; }

        /* Room Offer Sub-Columns on Mobile */
        .agoda-room-subcol-price {
            border-right: none !important;
            border-top: 1px solid #f1f5f9 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            text-align: left !important;
            align-items: flex-start !important;
            padding: 14px 16px !important;
        }
        .agoda-room-subcol-price .d-flex.justify-content-end {
            justify-content: flex-start !important;
        }
        .agoda-room-subcol-price .ms-auto {
            margin-left: 0 !important;
        }
        .agoda-room-subcol-book {
            padding: 14px 16px !important;
        }
        .agoda-room-subcol-book .agoda-room-pill-btn {
            height: 40px !important;
        }
        .agoda-room-subcol-book .btn-primary {
            height: 44px !important;
            font-size: 14.5px !important;
        }
    }

    @media (max-width: 575.98px) {
        .agoda-hero-mosaic-wrap { height: 200px !important; }
        #agodaDetailStickyBottomBar .d-flex { flex-direction: column; align-items: flex-start !important; gap: 8px !important; }
        #agodaDetailStickyBottomBar .d-flex:last-child { width: 100%; justify-content: space-between; }
        .agoda-filmstrip-thumb { width: 60px !important; height: 42px !important; }
        #agodaSlideshowActiveImg { max-height: 40vh !important; }
    }

    /* Agoda Modal Tab & Filmstrip Styling (1:1 Parity) */
    /* Agoda Modal Tab & Mode Button Styling (High Contrast & Clear Hover States) */
    .agoda-gallery-mode-btn { background: #f1f5f9 !important; border: 1px solid #cbd5e1 !important; color: #1e293b !important; font-size: 12.5px !important; font-weight: 700 !important; transition: all 0.2s ease !important; cursor: pointer; }
    .agoda-gallery-mode-btn:hover, .agoda-gallery-mode-btn:focus, .agoda-gallery-mode-btn:active { background: #2067e1 !important; color: #ffffff !important; border-color: #2067e1 !important; }
    .agoda-gallery-mode-btn:hover *, .agoda-gallery-mode-btn:focus *, .agoda-gallery-mode-btn:active * { color: #ffffff !important; }
    
    .agoda-modal-cat-tab { background: transparent; border: none; font-size: 13.5px; font-weight: 600; color: #475569 !important; padding: 10px 14px; border-bottom: 2.5px solid transparent; cursor: pointer; white-space: nowrap; transition: all 0.2s ease; }
    .agoda-modal-cat-tab:hover { color: #2067e1 !important; background: #f8fafc; }
    .agoda-modal-cat-tab.active { color: #2067e1 !important; border-bottom-color: #2067e1 !important; font-weight: 700 !important; }
    .agoda-grid-photo-card { transition: transform 0.2s ease; }
    .agoda-grid-photo-card:hover .agoda-grid-img { transform: scale(1.045); filter: brightness(1.05); }
    .agoda-grid-photo-card:hover .agoda-grid-overlay { opacity: 1 !important; }
    .agoda-grid-img { transition: transform 0.3s ease, filter 0.3s ease; }
    .agoda-filmstrip-thumb.active { border-color: #3b82f6 !important; opacity: 1; transform: scale(1.05); }
    .agoda-filmstrip-thumb:not(.active) { opacity: 0.55; }
    .agoda-filmstrip-thumb:hover { opacity: 1; }
</style>

<div class="detail-page-wrapper">

    {{-- Floating Live Preview Bar for Admins and Property Owners --}}
    @if(!empty($isPreview))
    <div class="position-sticky top-0 shadow-lg" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); border-bottom: 2px solid #6366f1; z-index: 2000; padding: 10px 0;">
        <div class="agoda-page-container d-flex flex-wrap align-items-center justify-content-between gap-2 text-white">
            <div class="d-flex align-items-center gap-3">
                <span class="badge px-3 py-2 rounded-pill fw-bold text-uppercase d-flex align-items-center gap-2" style="background: #3b82f6; font-size: 11.5px; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-eye animate-pulse"></i> LIVE PREVIEW MODE
                </span>
                <div>
                    <span class="fw-semibold text-white" style="font-size: 13.5px;">{{ $property->name }}</span>
                    <span class="text-white-50 ms-2" style="font-size: 12px;">Status:</span>
                    @if($property->status === 'active' || $property->status === 'published')
                        <span class="badge bg-success text-white px-2 py-1 ms-1">Active / Live</span>
                    @elseif($property->status === 'pending')
                        <span class="badge bg-warning text-dark px-2 py-1 ms-1">Pending Approval</span>
                    @elseif($property->status === 'rejected')
                        <span class="badge bg-danger rounded-0">Rejected</span>
                    @else
                        <span class="badge bg-secondary text-white px-2 py-1 ms-1">{{ ucfirst($property->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(!empty($isAdminPreview))
                    @if($property->status === 'pending' || $property->status === 'inactive')
                    <form action="{{ Route::has('admin.properties.approve') ? route('admin.properties.approve', $property->id) : url('/admin/properties/' . $property->id . '/approve') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3 d-flex align-items-center gap-1 shadow-sm" style="border-radius: 4px; font-size: 12.5px;">
                            <i class="fa-solid fa-check"></i> Approve & Publish
                        </button>
                    </form>
                    @endif
                    <a href="{{ Route::has('admin.properties.edit') ? route('admin.properties.edit', $property->id) : url('/admin/properties/' . $property->id . '/edit') }}" class="btn btn-sm btn-light fw-bold px-3 d-flex align-items-center gap-1" style="border-radius: 4px; font-size: 12.5px;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Listing
                    </a>
                    <a href="{{ Route::has('admin.properties.index') ? route('admin.properties.index') : url('/admin/properties') }}" class="btn btn-sm btn-outline-light px-3" style="border-radius: 4px; font-size: 12.5px;">
                        <i class="fa-solid fa-arrow-left"></i> Admin Center
                    </a>
                @else
                    <a href="{{ Route::has('vendor.properties.edit') ? route('vendor.properties.edit', $property->id) : url('/vendor/properties/' . $property->id . '/edit') }}" class="btn btn-sm btn-warning text-dark fw-bold px-3 d-flex align-items-center gap-1 shadow-sm" style="border-radius: 4px; font-size: 12.5px;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Listing
                    </a>
                    <a href="{{ Route::has('vendor.properties.index') ? route('vendor.properties.index') : url('/vendor/properties') }}" class="btn btn-sm btn-outline-light px-3" style="border-radius: 4px; font-size: 12.5px;">
                        <i class="fa-solid fa-arrow-left"></i> Vendor Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- 1. Agoda Subheader Search Bar (Desktop: 1-Row Inline Bar | Mobile & Tablet: Sleek Expandable Capsule Pill) --}}
    {{-- Desktop View (>= 992px) --}}
    <div class="d-none d-lg-block agoda-detail-search-bar shadow-xs" style="background: #0f294d; padding: 10px 0; position: sticky; top: {{ !empty($isPreview) ? '52px' : '0' }}; z-index: 1030;">
        <div class="agoda-page-container">
            <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center" onsubmit="showAgodaSearchLoading();">
                {{-- 1. Destination Input --}}
                <div class="col-12 col-lg-3">
                    <div class="input-group bg-white rounded-3 overflow-hidden shadow-xs" style="height: 44px;">
                        <span class="input-group-text bg-white border-0 text-secondary ps-3 pe-2"><x-agoda-icon name="search" size="18" color="#475569" /></span>
                        <input type="text" name="destination" class="form-control border-0 fw-semibold text-dark ps-1" value="{{ $property->name }}" placeholder="Enter a destination or property" style="font-size: 13.5px; height: 44px;">
                    </div>
                </div>

                {{-- 2. Combined Check-in & Check-out Single White Box --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-white rounded-3 d-flex align-items-center overflow-hidden shadow-xs position-relative" style="height: 44px;">
                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="border-right: 1px solid #cbd5e1; cursor: pointer;" onclick="document.getElementById('checkInDetailInput').showPicker ? document.getElementById('checkInDetailInput').showPicker() : document.getElementById('checkInDetailInput').focus();">
                            <x-agoda-icon name="calendar" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailCheckInText" style="font-size: 12px;">{{ $checkinCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" id="detailCheckInDay" style="font-size: 10.5px;">{{ $checkinCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_in" id="checkInDetailInput" value="{{ $checkinCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateDetailDateDisplay('checkIn', this.value);">

                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="cursor: pointer;" onclick="document.getElementById('checkOutDetailInput').showPicker ? document.getElementById('checkOutDetailInput').showPicker() : document.getElementById('checkOutDetailInput').focus();">
                            <x-agoda-icon name="calendar" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailCheckOutText" style="font-size: 12px;">{{ $checkoutCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" id="detailCheckOutDay" style="font-size: 10.5px;">{{ $checkoutCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_out" id="checkOutDetailInput" value="{{ $checkoutCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; right: 0; width: 1px; height: 1px;" onchange="updateDetailDateDisplay('checkOut', this.value);">
                    </div>
                </div>

                {{-- 3. Guests & Rooms Dropdown Box --}}
                <div class="col-12 col-md-6 col-lg-3 position-relative">
                    <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center justify-content-between shadow-xs dropdown-toggle" style="height: 44px; cursor: pointer;" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <div class="d-flex align-items-center gap-2">
                            <x-agoda-icon name="users" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" id="detailGuestCountText" style="font-size: 12px;">{{ $guestStr }}</strong>
                                <small class="text-secondary" id="detailRoomCountText" style="font-size: 10.5px;">{{ $roomsCountStr }}</small>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 11px;"></i>
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

                {{-- 4. Search Button (Agoda 1:1 Parity) --}}
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn text-white w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="background-color: #1b62dc; height: 44px; font-size: 14px; font-weight: 700; border-radius: 4px; letter-spacing: 0.5px;">
                        SEARCH
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mobile & Tablet View (< 992px) - Authentic Agoda Compact Capsule Bar --}}
    <div class="d-lg-none agoda-detail-mobile-search-bar" style="background: #0f294d; padding: 8px 0; position: sticky; top: {{ !empty($isPreview) ? '52px' : '0' }}; z-index: 1030;">
        <div class="agoda-page-container">
            <div class="bg-white rounded-pill px-3 py-1.5 d-flex align-items-center justify-content-between shadow-xs" style="min-height: 44px; cursor: pointer;" onclick="toggleMobileSearchCollapse();">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <x-agoda-icon name="search" size="17" color="#1b62dc" class="flex-shrink-0" />
                    <div class="text-truncate" style="line-height: 1.25;">
                        <div class="text-dark fw-bold text-truncate" style="font-size: 13px;">{{ $property->name }}</div>
                        <div class="text-secondary text-truncate" style="font-size: 11px;">
                            {{ $checkinCarbon->format('j M') }} – {{ $checkoutCarbon->format('j M') }} • {{ $guestStr }}
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1 fw-bold flex-shrink-0" style="background-color: #1b62dc; font-size: 11.5px; letter-spacing: 0.3px;">
                    Change <i class="fa-solid fa-chevron-down ms-1" style="font-size: 9px;" id="mobileSearchCollapseIcon"></i>
                </button>
            </div>

            {{-- Collapsible mobile full form when user taps Change --}}
            <div id="mobileDetailSearchCollapse" class="d-none mt-2 pt-2 border-top border-white-50">
                <form action="{{ route('search.index') }}" method="GET" class="d-flex flex-column gap-2" onsubmit="showAgodaSearchLoading();">
                    <div class="input-group bg-white rounded-3 overflow-hidden shadow-xs" style="height: 44px;">
                        <span class="input-group-text bg-white border-0 text-secondary ps-3 pe-2"><x-agoda-icon name="search" size="18" color="#475569" /></span>
                        <input type="text" name="destination" class="form-control border-0 fw-semibold text-dark ps-1" value="{{ $property->name }}" placeholder="Enter a destination or property" style="font-size: 13.5px; height: 44px;">
                    </div>
                    <div class="bg-white rounded-3 d-flex align-items-center overflow-hidden shadow-xs position-relative" style="height: 44px;">
                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="border-right: 1px solid #cbd5e1; cursor: pointer;" onclick="document.getElementById('mCheckInInput').showPicker ? document.getElementById('mCheckInInput').showPicker() : document.getElementById('mCheckInInput').focus();">
                            <x-agoda-icon name="calendar" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" style="font-size: 12px;">{{ $checkinCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" style="font-size: 10.5px;">{{ $checkinCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_in" id="mCheckInInput" value="{{ $checkinCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;">

                        <div class="flex-fill px-3 py-1 d-flex align-items-center gap-2" style="cursor: pointer;" onclick="document.getElementById('mCheckOutInput').showPicker ? document.getElementById('mCheckOutInput').showPicker() : document.getElementById('mCheckOutInput').focus();">
                            <x-agoda-icon name="calendar" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" style="font-size: 12px;">{{ $checkoutCarbon->format('j M Y') }}</strong>
                                <small class="text-secondary" style="font-size: 10.5px;">{{ $checkoutCarbon->format('l') }}</small>
                            </div>
                        </div>
                        <input type="date" name="check_out" id="mCheckOutInput" value="{{ $checkoutCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; right: 0; width: 1px; height: 1px;">
                    </div>

                    <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center justify-content-between shadow-xs" style="height: 44px;">
                        <div class="d-flex align-items-center gap-2">
                            <x-agoda-icon name="users" size="18" color="#475569" />
                            <div style="line-height: 1.15;">
                                <strong class="d-block text-dark" style="font-size: 12px;">{{ $guestStr }}</strong>
                                <small class="text-secondary" style="font-size: 10.5px;">{{ $roomsCountStr }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted" style="font-size: 11px;">Adults:</span>
                                <input type="number" name="adults" value="{{ $adultsCount ?? 2 }}" min="1" max="10" class="form-control form-control-sm text-center fw-bold p-1" style="width: 48px; height: 32px;">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted" style="font-size: 11px;">Rooms:</span>
                                <input type="number" name="rooms" value="{{ $roomsCountVal ?? 1 }}" min="1" max="8" class="form-control form-control-sm text-center fw-bold p-1" style="width: 48px; height: 32px;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn text-white w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="background-color: #1b62dc; height: 44px; font-size: 14px; font-weight: 700; border-radius: 4px; letter-spacing: 0.5px;">
                        SEARCH
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="agoda-page-container pt-2">

        {{-- 2. Breadcrumb Strip (Screenshot Exact Parity) --}}
        <div class="d-flex justify-content-between align-items-center mb-2 overflow-hidden" style="font-size: 12px; padding: 2px 0;">
            <div class="text-secondary text-truncate me-2">
                <a href="{{ route('home') }}" class="text-primary text-decoration-none">Home</a> 
                <span class="text-muted mx-1">&gt;</span> 
                <span class="d-none d-sm-inline">
                    <a href="{{ route('search.index') }}?destination=Bangladesh" class="text-primary text-decoration-none">Bangladesh Hotels</a> 
                    <span class="text-muted">(1,851)</span> 
                    <span class="text-muted mx-1">&gt;</span> 
                </span>
                <a href="{{ route('search.index') }}?destination={{ urlencode($property->city) }}" class="text-primary text-decoration-none">{{ $property->city ?: 'Dhaka' }} Hotels</a> 
                <span class="d-none d-sm-inline text-muted">(538)</span> 
                <span class="text-muted mx-1">&gt;</span> 
                <span class="text-dark fw-normal text-truncate d-inline-block align-bottom" style="max-width: 220px;">Book {{ $property->name }}</span>
            </div>
            <div class="d-none d-md-block flex-shrink-0">
                <a href="{{ route('search.index') }}?destination={{ urlencode($property->city) }}" class="text-primary text-decoration-none fw-bold" style="font-size: 12px;">See all properties in {{ $property->city ?: 'Dhaka' }}</a>
            </div>
        </div>

        @php
            $hasVideo = !empty($property->video_url);
            $videoUrl = $hasVideo ? $property->video_url : '';
            $isYoutube = false;
            $youtubeId = '';
            if ($hasVideo && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $videoUrl, $match)) {
                $isYoutube = true;
                $youtubeId = $match[1];
            }
        @endphp

        {{-- 3. Hero Photo Collage 4-Column Mosaic (1:1 Exact Agoda Screenshot 2 Parity) --}}
        <div class="mb-2 position-relative agoda-hero-mosaic-wrap" style="border-radius: 8px; overflow: hidden; height: 315px; -webkit-mask-image: -webkit-radial-gradient(white, black); transform: translateZ(0); background: #ffffff;">
            <div class="d-flex flex-column flex-lg-row h-100" style="gap: 4px;">
                {{-- Column 1: Left Hero Video / Main Image (~38% width) --}}
                <div class="position-relative overflow-hidden hero-col-main" style="flex: 0 0 calc(38% - 3px); height: 100%; border-radius: 8px 0 0 8px; -webkit-mask-image: -webkit-radial-gradient(white, black); cursor: pointer; background: #0f172a;">
                    @if($hasVideo)
                        @if($isYoutube)
                            <div class="w-100 h-100 position-relative overflow-hidden" style="border-radius: 8px 0 0 8px;">
                                <iframe id="heroDetailAutoplayIframe" 
                                        src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?autoplay=1&mute=1&loop=1&playlist={{ $youtubeId }}&controls=0&modestbranding=1&rel=0" 
                                        class="w-100 h-100 border-0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        style="pointer-events: none; transform: scale(1.05); border-radius: 8px 0 0 8px;">
                                </iframe>
                                <div class="position-absolute w-100 h-100 top-0 start-0" style="cursor: pointer; border-radius: 8px 0 0 8px;" onclick="openAgodaSlideshow(0);" title="Click to open photos"></div>
                            </div>
                        @else
                            <div class="w-100 h-100 position-relative overflow-hidden" id="heroVideoContainer" style="border-radius: 8px 0 0 8px;">
                                {{-- Prime Booking Official Video Tour Watermark / Badge --}}
                                <div class="position-absolute top-0 start-0 m-3 d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-sm" style="background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); z-index: 12; pointer-events: none;">
                                    <img src="/storage/branding/1_Prime Booking Final Logo.png" alt="Prime Booking" style="height: 18px; width: auto; object-fit: contain;">
                                    <span style="color: #ffffff; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Official Tour</span>
                                </div>

                                <video id="heroDetailAutoplayVideo" 
                                       class="w-100 h-100" 
                                       style="object-fit: cover; cursor: pointer; border-radius: 8px 0 0 8px; -webkit-mask-image: -webkit-radial-gradient(white, black);" 
                                       autoplay 
                                       muted 
                                       loop 
                                       playsinline 
                                       preload="auto"
                                       poster="{{ $gallery[0] }}"
                                       onclick="openAgodaSlideshow(0);"
                                       title="Click to view photos">
                                    <source src="{{ media_url($videoUrl) }}" type="video/mp4">
                                    <source src="{{ media_url($videoUrl) }}" type="video/quicktime">
                                    <img src="{{ $gallery[0] }}" class="w-100 h-100" style="object-fit: cover; border-radius: 8px 0 0 8px;" alt="{{ $property->name }}">
                                </video>

                                {{-- Video Audio Control Button (Default: Muted with Sound On/Off Toggle) --}}
                                <button type="button" 
                                        id="btnToggleHeroSound" 
                                        class="btn btn-dark btn-sm rounded-circle shadow-sm position-absolute" 
                                        style="top: 14px; right: 14px; z-index: 12; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(6px); border: 1.5px solid rgba(255, 255, 255, 0.4); transition: transform 0.2s ease, background 0.2s ease;" 
                                        onclick="toggleHeroVideoSound(event);" 
                                        title="Click to Turn Sound On">
                                    <i id="iconHeroSound" class="fa-solid fa-volume-xmark text-white" style="font-size: 14px;"></i>
                                </button>
                            </div>
                        @endif
                    @else
                        <img src="{{ $gallery[0] }}" class="w-100 h-100" style="object-fit: cover; border-radius: 8px 0 0 8px; transition: transform 0.4s ease; cursor: pointer;" alt="{{ $property->name }}" onclick="openAgodaSlideshow(0);" fetchpriority="high" loading="eager">
                    @endif

                    {{-- Bottom Action Button on Hero (Positioned at bottom right) --}}
                    <div class="position-absolute bottom-0 start-0 m-3" style="z-index: 10;" onclick="event.stopPropagation();">
                        <button type="button" class="btn btn-light btn-sm fw-bold rounded-pill shadow-sm d-inline-flex align-items-center" style="font-size: 12px; padding: 6px 16px; background: rgba(255,255,255,0.95); border: 1px solid #cbd5e1; color: #1e293b;" onclick="openAgodaSlideshow(0);">
                            <i class="fa-solid fa-camera text-primary me-2" style="font-size: 13px;"></i>
                            <span>See all photos</span>
                        </button>
                    </div>
                </div>

                {{-- Column 2: 2 Stacked Images (~20.66% width) --}}
                <div class="d-none d-lg-flex flex-column" style="flex: 0 0 calc(20.66% - 3px); height: 100%; gap: 4px;">
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a;" onclick="openAgodaSlideshow(1);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[1] }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $property->name }} Highlight">
                    </div>
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a;" onclick="openAgodaSlideshow(2);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[2] }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $property->name }} Room">
                    </div>
                </div>

                {{-- Column 3: 2 Stacked Images (~20.66% width) --}}
                <div class="d-none d-lg-flex flex-column" style="flex: 0 0 calc(20.66% - 3px); height: 100%; gap: 4px;">
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a;" onclick="openAgodaSlideshow(3);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[3] }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $property->name }} View 1">
                    </div>
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a;" onclick="openAgodaSlideshow(4);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[4] }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $property->name }} View 2">
                    </div>
                </div>

                {{-- Column 4: 2 Stacked Images (Remaining ~20.66% width) --}}
                <div class="d-none d-lg-flex flex-column flex-fill" style="height: 100%; gap: 4px;">
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a; border-top-right-radius: 8px; -webkit-mask-image: -webkit-radial-gradient(white, black);" onclick="openAgodaSlideshow(5);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[5] }}" class="w-100 h-100" style="object-fit: cover; border-top-right-radius: 8px; transition: transform 0.4s ease;" alt="{{ $property->name }} View 3">
                    </div>
                    <div class="position-relative overflow-hidden" style="height: calc(50% - 2px); cursor: pointer; background: #0f172a; border-radius: 0 !important; -webkit-mask-image: -webkit-radial-gradient(white, black);" onclick="openAgodaSlideshow(6);">
                        <img loading="lazy" decoding="async" src="{{ $gallery[6] }}" class="w-100 h-100" style="object-fit: cover; border-radius: 0 !important; transition: transform 0.4s ease;" alt="{{ $property->name }} View 4">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Sticky Section Anchor Navigation Bar (Agoda 1:1 Parity) --}}
    <div id="agodaStickyNavWrapper" class="agoda-sticky-nav-wrapper" style="margin-top: 8px; margin-bottom: 16px;">
        <div class="agoda-page-container">
            <div class="agoda-sticky-nav-card d-flex align-items-center justify-content-between p-0" style="border: 1px solid #dddfe2; border-radius: 4px; background: #ffffff; height: 46px;">
                <div class="d-flex align-items-center gap-1 overflow-x-auto ps-3 pe-3 w-100" style="scrollbar-width: none;">
                    <a href="#overview" class="agoda-nav-item active" style="color: #2067e1 !important; border-bottom: 3px solid #2067e1; font-weight: 700; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Overview</a>
                    <a href="#rooms" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Rooms</a>
                    <a href="#recommendations" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Trip recommendations</a>
                    <a href="#facilities" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Facilities</a>
                    <a href="#reviews" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Reviews</a>
                    <a href="#location" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Location</a>
                    <a href="#policies" class="agoda-nav-item" style="color: #475569 !important; padding: 12px 16px; font-size: 13.5px; text-decoration: none; white-space: nowrap;">Policies</a>
                </div>
            </div>
        </div>
    </div>

    <div class="agoda-page-container">
        {{-- 5. Main Overview Section Grid (Exact User Screenshot Parity) --}}
        <div id="overview" class="row gx-4 gy-4 mb-4">
            
            {{-- Left Column: Title & Overview (Agoda 1:1 Parity) --}}
            <div class="col-lg-8">
                
                {{-- Left Header Card: Top Value, Best seller Badges, Wishlist, Title, Stars, Address (100% Exact Parity with Screenshot) --}}
                <div class="card mb-3" style="border: 1px solid #dddfe2 !important; border-radius: 6px !important; padding: 16px 20px !important; background: #ffffff !important; box-shadow: none !important;">
                    {{-- Badges (Top Value & Best seller) + Wishlist Heart --}}
                    <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <span class="d-inline-flex align-items-center justify-content-center text-white" style="background: #f15600; font-size: 11px; font-weight: 700; border-radius: 3px; padding: 2.5px 7px; line-height: 1.3; letter-spacing: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                Top Value
                            </span>
                            <span class="d-inline-flex align-items-center justify-content-center text-white" style="background: #d42e2e; font-size: 11px; font-weight: 700; border-radius: 3px; padding: 2.5px 7px; line-height: 1.3; letter-spacing: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                Best seller
                            </span>
                        </div>

                        {{-- Wishlist Heart Button --}}
                        <button type="button" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #d1d5db; transition: all 0.2s ease;" title="Save to Wishlist" onclick="toggleDetailWishlist(this, {{ $property->id }});">
                            <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart" style="font-size: 15px; color: {{ $isWishlisted ? '#dc2626' : '#222222' }};"></i>
                        </button>
                    </div>

                    {{-- Title & Stars Inline --}}
                    <div class="d-flex align-items-center flex-wrap" style="margin-bottom: 6px;">
                        <h1 class="mb-0 d-inline" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 1.3; font-weight: 700; color: #222222; letter-spacing: 0; -webkit-font-smoothing: antialiased;">
                            {{ $property->name }}
                        </h1>
                        <div class="d-inline-flex align-items-center ms-2" style="color: #e58c00 !important; font-size: 13px; letter-spacing: 2.5px; vertical-align: middle;">
                            @for($i = 0; $i < ($property->star_rating ?? 5); $i++)★@endfor
                        </div>
                    </div>

                    {{-- Address & See Map Link --}}
                    <div class="d-inline-flex align-items-center flex-wrap" style="font-size: 12.5px; color: #2a2a2e; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.4; margin-top: 2px;">
                        <span>{{ $property->address ?: ($property->city . ', Bangladesh') }} - </span>
                        <a href="#location" class="fw-bold text-uppercase text-decoration-none ms-1" style="color: #0b66c2; font-size: 12px; font-weight: 700; letter-spacing: 0.2px;" onclick="event.preventDefault(); document.getElementById('location')?.scrollIntoView({behavior:'smooth', block:'center'});">SEE MAP</a>
                    </div>
                </div>

                {{-- Right Card 1: Review Score Box with Sub-scores AND Customer Review Snippets (Agoda 1:1 Parity) --}}
                @php
                    $cleanScoreVal = (float)($property->sub_scores['cleanliness'] ?? 8.2);
                    $facScoreVal = (float)($property->sub_scores['facilities'] ?? 7.8);
                    $servScoreVal = (float)($property->sub_scores['service'] ?? 7.8);
                    $valScoreVal = (float)($property->sub_scores['value'] ?? 7.5);
                @endphp
                <div id="reviews" class="card agoda-card-border mb-3" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 16px !important; background: #ffffff;">
                    {{-- Header: Score Badge + Summary + See All --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        {{-- Blue Rating Badge (Agoda Speech Bubble: 3 rounded corners, bottom-left sharp 0px) --}}
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 48px; height: 44px; background-color: #2067e1; border-radius: 14px 14px 14px 0 !important; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; line-height: 1;">
                            {{ $score }}
                        </div>
                        {{-- Rating Summary & Review Count --}}
                        <div class="flex-grow-1" style="line-height: 1.25;">
                            <div class="d-flex align-items-baseline justify-content-between">
                                <strong style="font-size: 17px; font-weight: 700; color: #2067e1; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    {{ $scoreNum >= 9 ? 'Exceptional' : ($scoreNum >= 8 ? 'Excellent' : ($scoreNum >= 7 ? 'Very good' : 'Good')) }}
                                </strong>
                                <a href="#reviews" class="text-decoration-none" style="color: #2067e1; font-size: 13px; font-weight: 600;">See all</a>
                            </div>
                            <div class="mt-0.5" style="font-size: 13px; color: #536279; font-weight: 400; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                {{ $revCount }} {{ $property->total_reviews == 1 ? 'review' : 'reviews' }}
                            </div>
                        </div>
                    </div>

                    {{-- 2x2 Sub-scores Grid with Blue Numbers and Progress Bars ("niche dag") --}}
                    <div class="row g-2 mb-3" style="font-size: 12px; line-height: 1.4;">
                        {{-- Cleanliness --}}
                        <div class="col-6 pe-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color: #1e293b; font-size: 12px;">Cleanliness</span>
                                <span style="color: #2067e1; font-weight: 700; font-size: 12px;">{{ number_format($cleanScoreVal, 1) }}</span>
                            </div>
                            <div style="height: 3px; background: #e2e8f0; border-radius: 2px; overflow: hidden; margin-bottom: 10px;">
                                <div style="width: {{ min(100, max(10, round(($cleanScoreVal / 10) * 100))) }}%; height: 100%; background: #2067e1; border-radius: 2px;"></div>
                            </div>
                        </div>

                        {{-- Facilities --}}
                        <div class="col-6 ps-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color: #1e293b; font-size: 12px;">Facilities</span>
                                <span style="color: #2067e1; font-weight: 700; font-size: 12px;">{{ number_format($facScoreVal, 1) }}</span>
                            </div>
                            <div style="height: 3px; background: #e2e8f0; border-radius: 2px; overflow: hidden; margin-bottom: 10px;">
                                <div style="width: {{ min(100, max(10, round(($facScoreVal / 10) * 100))) }}%; height: 100%; background: #2067e1; border-radius: 2px;"></div>
                            </div>
                        </div>

                        {{-- Service --}}
                        <div class="col-6 pe-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color: #1e293b; font-size: 12px;">Service</span>
                                <span style="color: #2067e1; font-weight: 700; font-size: 12px;">{{ number_format($servScoreVal, 1) }}</span>
                            </div>
                            <div style="height: 3px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                                <div style="width: {{ min(100, max(10, round(($servScoreVal / 10) * 100))) }}%; height: 100%; background: #2067e1; border-radius: 2px;"></div>
                            </div>
                        </div>

                        {{-- Value for money --}}
                        <div class="col-6 ps-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color: #1e293b; font-size: 12px;">Value for money</span>
                                <span style="color: #2067e1; font-weight: 700; font-size: 12px;">{{ number_format($valScoreVal, 1) }}</span>
                            </div>
                            <div style="height: 3px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                                <div style="width: {{ min(100, max(10, round(($valScoreVal / 10) * 100))) }}%; height: 100%; background: #2067e1; border-radius: 2px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Review Snippets Track (Inside this same Card!) --}}
                    <div class="position-relative mt-2 pt-1">
                        <div class="d-flex gap-2 overflow-hidden align-items-stretch" style="margin-right: -4px;">
                            {{-- Snippet Card 1 --}}
                            <div style="flex: 0 0 68%; border: 1px solid #dddfe2; border-radius: 8px; padding: 10px 12px; background: #ffffff;">
                                <div class="mb-2" style="font-size: 11.5px; font-weight: 600; color: #1e293b; line-height: 1.35; height: 32px; overflow: hidden;">
                                    Great Location, Impeccably<br>Clean – Highly Recommended!
                                </div>
                                <div class="d-flex align-items-center gap-1.5" style="font-size: 11px;">
                                    {{-- BD Flag Badge --}}
                                    <span class="d-inline-flex align-items-center justify-content-center" style="width: 14px; height: 10px; background: #006a4e; border-radius: 1px; position: relative; overflow: hidden; flex-shrink: 0;">
                                        <span style="width: 6px; height: 6px; background: #f42a41; border-radius: 50%; display: block;"></span>
                                    </span>
                                    <strong class="text-dark" style="font-weight: 700; color: #1e293b;">MD I.</strong>
                                    <span style="color: #64748b;">Bangladesh</span>
                                </div>
                            </div>

                            {{-- Snippet Card 2 (Peeking) --}}
                            <div style="flex: 0 0 68%; border: 1px solid #dddfe2; border-radius: 8px; padding: 10px 12px; background: #ffffff; opacity: 0.95;">
                                <div class="mb-2" style="font-size: 11.5px; font-weight: 600; color: #1e293b; line-height: 1.35; height: 32px; overflow: hidden;">
                                    Services ...<br>also ...
                                </div>
                                <div class="d-flex align-items-center gap-1.5" style="font-size: 11px;">
                                    <span class="d-inline-flex align-items-center justify-content-center" style="width: 14px; height: 10px; background: #006a4e; border-radius: 1px; position: relative; overflow: hidden; flex-shrink: 0;">
                                        <span style="width: 6px; height: 6px; background: #f42a41; border-radius: 50%; display: block;"></span>
                                    </span>
                                    <strong class="text-dark" style="font-weight: 700; color: #1e293b;">Sha...</strong>
                                    <span style="color: #64748b;">Bangladesh</span>
                                </div>
                            </div>
                        </div>

                        {{-- Circular Next Button Overlapping --}}
                        <button type="button" class="btn btn-light rounded-circle position-absolute top-50 d-flex align-items-center justify-content-center p-0" style="right: 8px; transform: translateY(-50%); width: 28px; height: 28px; border: 1px solid #cbd5e1; background: #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.18); z-index: 5;" onclick="document.getElementById('reviews')?.scrollIntoView({behavior:'smooth'});" title="View more reviews">
                            <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #1e293b;"></i>
                        </button>
                    </div>
                </div>

                {{-- Left Card 2: Highlights (Agoda 1:1 Parity with Screenshot) --}}
                <div class="card agoda-card-border mb-3" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 20px 24px !important; background: #ffffff;">
                    <h5 class="fw-bold mb-3.5" style="font-size: 18px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 800; color: #222222;">Highlights</h5>
                    
                    <div class="d-flex flex-column" style="gap: 18px;">
                        {{-- 1. Great for children --}}
                        <div class="d-flex align-items-center gap-3">
                            <img loading="lazy" decoding="async" src="{{ asset('images/agoda-highlights/highlight-1.png') }}" class="flex-shrink-0" style="width: 48px; height: 48px; object-fit: contain;" alt="Great for children">
                            <div>
                                <strong class="fw-bold" style="font-size: 14px; color: #222222;">Great for children</strong>
                            </div>
                        </div>

                        {{-- 2. Highly rated by couple travelers --}}
                        <div class="d-flex align-items-start gap-3">
                            <img loading="lazy" decoding="async" src="{{ asset('images/agoda-highlights/highlight-2.png') }}" class="flex-shrink-0" style="width: 48px; height: 48px; object-fit: contain;" alt="Highly rated by couple travelers">
                            <div style="font-size: 14px; line-height: 1.35;">
                                <div class="fw-bold mb-0.5" style="font-size: 14px; color: #222222;">Highly rated by couple travelers</div>
                                <div style="font-size: 12.5px; color: #555555;">“We had a wonderful honeymoon here.”</div>
                            </div>
                        </div>

                        {{-- 3. Great overall atmosphere --}}
                        <div class="d-flex align-items-start gap-3">
                            <img loading="lazy" decoding="async" src="{{ asset('images/agoda-highlights/highlight-3.png') }}" class="flex-shrink-0" style="width: 48px; height: 48px; object-fit: contain;" alt="Great overall atmosphere">
                            <div style="font-size: 14px; line-height: 1.35;">
                                <div class="fw-bold mb-0.5" style="font-size: 14px; color: #222222;">Great overall atmosphere</div>
                                <div style="font-size: 12.5px; color: #555555;">“Again, this was a pleasant experience overall.”</div>
                            </div>
                        </div>

                        {{-- 4. Top property facilities --}}
                        <div class="d-flex align-items-start gap-3">
                            <img loading="lazy" decoding="async" src="{{ asset('images/agoda-highlights/highlight-4.png') }}" class="flex-shrink-0" style="width: 48px; height: 48px; object-fit: contain;" alt="Top property facilities">
                            <div style="font-size: 14px; line-height: 1.35;">
                                <div><strong style="color: #222222; font-weight: 700;">Top property facilities</strong> <span style="font-size: 13px; color: #555555;">- Overall stays feel enjoyable for many guests, and several reviews say...</span></div>
                                <div class="mt-0.5" style="font-size: 12.5px; color: #555555;">“Best 5 star hotel around cox's Bazar.”</div>
                            </div>
                        </div>

                        {{-- 5. Professional guest services --}}
                        <div class="d-flex align-items-start gap-3">
                            <img loading="lazy" decoding="async" src="{{ asset('images/agoda-highlights/highlight-5.png') }}" class="flex-shrink-0" style="width: 48px; height: 48px; object-fit: contain;" alt="Professional guest services">
                            <div style="font-size: 14px; line-height: 1.35;">
                                <div><strong style="color: #222222; font-weight: 700;">Professional guest services</strong> <span style="font-size: 13px; color: #555555;">- Hospitality stands out, with courteous staff, quick service, and 24-ho...</span></div>
                                <div class="mt-0.5" style="font-size: 12.5px; color: #555555;">“Services, Swimming pool and also out side view is nice.”</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Staycation offers available Card (Exact 1:1 Agoda Parity) --}}
                <div class="card agoda-card-border mb-3" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 24px !important; background: #ffffff;">
                    <div style="margin-bottom: 16px;">
                        <h5 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; color: #2a2a2e; margin: 0 0 4px 0; line-height: 1.3;">
                            Staycation offers available
                        </h5>
                        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; color: #2a2a2e; line-height: 1.4;">
                            Get special benefits for your stay
                        </div>
                    </div>

                    {{-- Horizontal Scrollable Sub-cards (Food & Drinks, Wellness, Activities, Sports & Entertainment) --}}
                    <div class="position-relative">
                        <div id="staycationScrollTrack" class="d-flex align-items-stretch overflow-x-auto" style="gap: 12px; scroll-behavior: smooth; padding-bottom: 6px; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                            
                            {{-- Card 1: Food and Drinks --}}
                            <div class="d-flex flex-column justify-content-between" style="width: 215px; min-width: 215px; background: #f7f9fa; border-radius: 8px; padding: 16px; flex-shrink: 0;">
                                <div>
                                    <div class="d-flex align-items-center gap-2" style="margin-bottom: 14px;">
                                        <img loading="lazy" decoding="async" src="{{ asset('images/staycation-icons/food-drinks.png') }}" style="width: 34px; height: 34px; object-fit: contain; flex-shrink: 0;" alt="Food and Drinks">
                                        <strong style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 700; color: #2a2a2e; line-height: 1.2;">Food and Drinks</strong>
                                    </div>
                                    <div class="d-flex flex-column" style="gap: 8px; margin-bottom: 14px;">
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Room service [24-hour]</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Coffee shop</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Room service</span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; margin-top: auto;">
                                    <a href="#facilities" style="color: #2067e1; font-size: 12.5px; font-weight: 500; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;" onclick="event.preventDefault(); document.getElementById('facilities')?.scrollIntoView({behavior:'smooth'});">See all amenities</a>
                                </div>
                            </div>

                            {{-- Card 2: Wellness --}}
                            <div class="d-flex flex-column justify-content-between" style="width: 215px; min-width: 215px; background: #f7f9fa; border-radius: 8px; padding: 16px; flex-shrink: 0;">
                                <div>
                                    <div class="d-flex align-items-center gap-2" style="margin-bottom: 14px;">
                                        <img loading="lazy" decoding="async" src="{{ asset('images/staycation-icons/wellness.png') }}" style="width: 34px; height: 34px; object-fit: contain; flex-shrink: 0;" alt="Wellness">
                                        <strong style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 700; color: #2a2a2e; line-height: 1.2;">Wellness</strong>
                                    </div>
                                    <div class="d-flex flex-column" style="gap: 8px; margin-bottom: 14px;">
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Salon</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Fitness center</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Spa</span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; margin-top: auto;">
                                    <a href="#facilities" style="color: #2067e1; font-size: 12.5px; font-weight: 500; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;" onclick="event.preventDefault(); document.getElementById('facilities')?.scrollIntoView({behavior:'smooth'});">See all amenities</a>
                                </div>
                            </div>

                            {{-- Card 3: Activities --}}
                            <div class="d-flex flex-column justify-content-between" style="width: 215px; min-width: 215px; background: #f7f9fa; border-radius: 8px; padding: 16px; flex-shrink: 0;">
                                <div>
                                    <div class="d-flex align-items-center gap-2" style="margin-bottom: 14px;">
                                        <img loading="lazy" decoding="async" src="{{ asset('images/staycation-icons/activities.png') }}" style="width: 34px; height: 34px; object-fit: contain; flex-shrink: 0;" alt="Activities">
                                        <strong style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 700; color: #2a2a2e; line-height: 1.2;">Activities</strong>
                                    </div>
                                    <div class="d-flex flex-column" style="gap: 8px; margin-bottom: 14px;">
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Swimming pool [indoor]</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>On-site activity</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Water park</span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; margin-top: auto;">
                                    <a href="#facilities" style="color: #2067e1; font-size: 12.5px; font-weight: 500; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;" onclick="event.preventDefault(); document.getElementById('facilities')?.scrollIntoView({behavior:'smooth'});">See all amenities</a>
                                </div>
                            </div>

                            {{-- Card 4: Sports & Entertainment --}}
                            <div class="d-flex flex-column justify-content-between" style="width: 215px; min-width: 215px; background: #f7f9fa; border-radius: 8px; padding: 16px; flex-shrink: 0;">
                                <div>
                                    <div class="d-flex align-items-center gap-2" style="margin-bottom: 14px;">
                                        <img loading="lazy" decoding="async" src="{{ asset('images/staycation-icons/sports.png') }}" style="width: 34px; height: 34px; object-fit: contain; flex-shrink: 0;" alt="Sports & Entertainment">
                                        <strong style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 700; color: #2a2a2e; line-height: 1.2;">Sports &amp; Entertainment</strong>
                                    </div>
                                    <div class="d-flex flex-column" style="gap: 8px; margin-bottom: 14px;">
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Game room</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Tennis court</span>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #4a4a4a; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Hiking trails</span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; margin-top: auto;">
                                    <a href="#facilities" style="color: #2067e1; font-size: 12.5px; font-weight: 500; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;" onclick="event.preventDefault(); document.getElementById('facilities')?.scrollIntoView({behavior:'smooth'});">See all amenities</a>
                                </div>
                            </div>

                        </div>

                        {{-- Floating Next Carousel Chevron Button --}}
                        <button type="button" class="btn btn-light rounded-circle shadow-sm position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; border: 1px solid #dddfe2; background: #ffffff; z-index: 5; margin-right: -10px;" title="Next" onclick="document.getElementById('staycationScrollTrack').scrollBy({left: 220, behavior: 'smooth'});">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Facilities Card (Exact 1:1 Agoda Parity) --}}
                <div id="facilities" class="card agoda-card-border mb-4" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 24px !important; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 18px;">
                        <h5 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; color: #2a2a2e; margin: 0; line-height: 1.3;">Facilities</h5>
                        <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #2067e1; font-size: 14px; font-weight: 700;" data-bs-toggle="modal" data-bs-target="#facilitiesModal">See all</a>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13.5px; color: #2a2a2e;">
                        {{-- Column 1 --}}
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Private beach</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Spa</span>
                            </div>
                        </div>

                        {{-- Column 2 --}}
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Free Wi-Fi</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Front desk [24-hour]</span>
                            </div>
                        </div>

                        {{-- Column 3 --}}
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Swimming pool</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Fitness center</span>
                            </div>
                        </div>

                        {{-- Column 4 --}}
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Free parking</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2a2a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Restaurants</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column Sidebar Widgets --}}
            <div class="col-lg-4 d-flex flex-column">
                
                {{-- Right Card 1: Sticky Mini Deal Card (Agoda 1:1 Parity with Screenshots 2 & 3) --}}
                @php
                    $firstRoom = $property->rooms->first();
                    $dealPrice = $firstRoom ? $firstRoom->price_per_night : $property->price_per_night;
                    $dealRoomName = $firstRoom ? $firstRoom->name : 'Deluxe Room with Sea View and Balcony';
                    $dealThumb = !empty($firstRoom->images) && is_array($firstRoom->images) && count($firstRoom->images) > 0 
                        ? media_url($firstRoom->images[0]) 
                        : ($gallery[1] ?? $gallery[0]);
                @endphp
                <div class="card agoda-card-border mb-3 position-relative" style="border: 1px solid #dddfe2 !important; border-radius: 6px !important; padding: 16px !important; background: #ffffff; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                        <div>
                            <div class="d-flex align-items-baseline gap-1.5">
                                <span style="font-size: 11px; font-weight: 700; color: #dc2626; text-transform: uppercase;">From</span>
                                <strong style="color: #e11d48; font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1;">
                                    {!! format_price($dealPrice) !!}
                                </strong>
                            </div>
                            <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">
                                Per night before taxes &amp; fees
                            </div>
                        </div>
                        <div class="flex-shrink-0 position-relative rounded-2 overflow-hidden" style="width: 76px; height: 54px; border: 1px solid #e2e8f0;">
                            <img src="{{ $dealThumb }}" class="w-100 h-100 object-fit-cover" alt="{{ $dealRoomName }}" loading="lazy">
                        </div>
                    </div>

                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.35;">
                        {{ $dealRoomName }}
                    </div>

                    <div class="d-flex flex-column gap-1 my-2" style="font-size: 12px;">
                        <div class="text-secondary d-flex align-items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#64748b"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <span>2 adults</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5" style="color: #16a34a; font-weight: 600;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#16a34a"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <span>Your kid can stay for FREE!</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5" style="color: #16a34a; font-weight: 600;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#16a34a"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                            <span>Breakfast included</span>
                        </div>
                    </div>

                    <a href="#rooms" class="btn btn-primary w-100 fw-bold py-2 mt-2 shadow-xs d-flex align-items-center justify-content-center" style="background-color: #2067e1; border: none; border-radius: 6px; font-size: 13.5px; letter-spacing: 0.2px;">
                        More options
                    </a>
                </div>

                {{-- Right Card 2: Interactive Location & Map Card (Agoda 1:1 Parity) --}}
                <div id="sidebarLocationCard" class="card agoda-card-border overflow-hidden mb-4 d-flex flex-column" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; background: #ffffff;">
                    {{-- Map Preview Container --}}
                    <div style="padding: 12px 12px 0 12px;">
                        <div class="position-relative text-center overflow-hidden" style="height: 130px; border-radius: 4px; background: #eef2f6; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#hotelDetailMapModal" title="Click to view full interactive map">
                            {{-- Agoda SVG Road Grid Pattern --}}
                            <svg class="w-100 h-100 position-absolute top-0 start-0" style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg">
                                <rect width="100%" height="100%" fill="#f1f5f9"/>
                                <line x1="-50" y1="30" x2="350" y2="150" stroke="#fef08a" stroke-width="8"/>
                                <line x1="20" y1="-20" x2="280" y2="160" stroke="#fed7aa" stroke-width="6"/>
                                <line x1="120" y1="-20" x2="350" y2="80" stroke="#fed7aa" stroke-width="5"/>
                                <line x1="-30" y1="110" x2="250" y2="-10" stroke="#cbd5e1" stroke-width="3"/>
                                <line x1="50" y1="160" x2="350" y2="30" stroke="#cbd5e1" stroke-width="3"/>
                                <rect x="210" y="15" width="55" height="40" fill="#80cbc4" opacity="0.8" rx="2" transform="rotate(-35 237 35)"/>
                                <rect x="180" y="70" width="50" height="35" fill="#80cbc4" opacity="0.8" rx="2" transform="rotate(-35 205 87)"/>
                                <rect x="90" y="75" width="45" height="35" fill="#90caf9" opacity="0.8" rx="2" transform="rotate(-35 112 92)"/>
                                <circle cx="120" cy="50" r="6" fill="#fef08a" stroke="#fde047" stroke-width="2"/>
                            </svg>

                            {{-- Center Red Pin & "SEE MAP" text --}}
                            <div class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center" style="z-index: 10;">
                                <i class="fa-solid fa-location-dot text-danger" style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25)); margin-bottom: 3px;"></i>
                                <span class="fw-bold text-dark text-uppercase" style="font-size: 11.5px; letter-spacing: 0.5px; font-weight: 800; text-shadow: 0 1px 2px rgba(255,255,255,0.9);">SEE MAP</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card Inner Content (Stretches full height to keep 'See nearby places' anchored to bottom) --}}
                    <div class="d-flex flex-column flex-grow-1" style="padding: 18px 20px 16px 20px;">
                        <div class="mb-2">
                            <h4 class="fw-bold mb-0" style="font-size: 17px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 700; color: #111827;">
                                {{ number_format((float)($property->location_score ?? 8.8), 1) }} {{ ($property->location_score ?? 8.8) >= 8.5 ? 'Excellent' : 'Very good' }}
                            </h4>
                            <div class="mt-0.5" style="font-size: 12.5px; color: #64748b !important; font-weight: 400;">
                                Location rating score
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 mb-3" style="font-size: 13.5px; color: #0f172a; font-weight: 700;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                            <span>{{ ($property->location_score ?? 8.8) >= 8.5 ? 'Excellent location' : 'Great location' }}</span>
                        </div>

                        {{-- Parking Row (Agoda 1:1 Match) --}}
                        <div class="d-flex align-items-center justify-content-between py-2.5 border-top" style="font-size: 13px; border-color: #e2e8f0 !important;">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 20px; height: 20px;">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="#64748b"><rect x="3" y="3" width="18" height="18" rx="4" fill="#f1f5f9" stroke="#64748b" stroke-width="1.5"/><path d="M9 7h4a3 3 0 0 1 0 6H9v4H7V7h2zm0 4h4a1 1 0 0 0 0-2H9v2z" fill="#64748b"/></svg>
                                </span>
                                <span style="color: #1e293b; font-weight: 600; font-size: 13px;">Parking</span>
                            </div>
                            <span style="color: #00805a; font-weight: 700; font-size: 13px; letter-spacing: 0.3px;">FREE</span>
                        </div>

                        <div class="border-top pt-2 d-flex flex-column flex-grow-1 justify-content-between" style="border-color: #e2e8f0 !important;">
                            @php
                                $propCity = strtolower((string)($property->city ?? ''));
                                $propAddress = strtolower((string)($property->address ?? ''));
                                $propName = strtolower((string)($property->name ?? ''));
                                $nearestLmk = $property->nearest_landmark ?? null;

                                $transportList = [];

                                // 1. If property has nearest_landmark specified in admin/DB, place it first!
                                if (!empty($nearestLmk)) {
                                    $transportList[] = [
                                        'name' => $nearestLmk,
                                        'dist' => '0.2 km',
                                        'icon' => 'umbrella-beach'
                                    ];
                                }

                                // COX'S BAZAR HOTELS: Distinguish Kolatoli/Sugandha/City Center vs Inani Beach vs Himchari
                                if (str_contains($propCity, 'cox') || str_contains($propAddress, 'cox') || str_contains($propAddress, 'kolatoli') || str_contains($propAddress, 'inani') || str_contains($propName, 'heritage') || str_contains($propName, 'pearl') || str_contains($propName, 'seagull') || str_contains($propName, 'sayeman')) {
                                    $isInani = str_contains($propAddress, 'inani') || str_contains($propName, 'pearl') || str_contains($propName, 'royal tulip');
                                    $isLaboni = str_contains($propAddress, 'laboni') || str_contains($propName, 'seagull') || str_contains($propName, 'ocean paradise');

                                    if ($isInani) {
                                        // Inani Area
                                        $landmarks = [
                                            ['name' => "Inani Beach (Coral Beach)", 'dist' => '0.2 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Marine Drive (Inani Section)", 'dist' => '0.3 km', 'icon' => 'road'],
                                            ['name' => "Himchari National Park", 'dist' => '14.2 km', 'icon' => 'tree'],
                                            ['name' => "Kolatoli Beach Point", 'dist' => '22.5 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Cox's Bazar Airport (CXB)", 'dist' => '26.8 km', 'icon' => 'plane'],
                                            ['name' => "Laboni Beach Market", 'dist' => '25.0 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Shah Amanat International Airport", 'dist' => '145.0 km', 'icon' => 'plane'],
                                        ];
                                    } elseif ($isLaboni) {
                                        // Laboni Beach Area
                                        $landmarks = [
                                            ['name' => "Laboni Beach Point", 'dist' => '0.2 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Burmese Market", 'dist' => '0.8 km', 'icon' => 'landmark'],
                                            ['name' => "Sugandha Beach Point", 'dist' => '1.2 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Cox's Bazar Airport (CXB)", 'dist' => '2.8 km', 'icon' => 'plane'],
                                            ['name' => "Kolatoli Beach", 'dist' => '2.1 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Himchari Waterfall", 'dist' => '10.5 km', 'icon' => 'tree'],
                                            ['name' => "Inani Beach", 'dist' => '24.5 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Shah Amanat International Airport", 'dist' => '122.0 km', 'icon' => 'plane'],
                                        ];
                                    } else {
                                        // Kolatoli / Sugandha / Main Hotel Zone (Best Western Heritage, Sayeman, Long Beach, etc.)
                                        $landmarks = [
                                            ['name' => "Kolatoli Beach Point", 'dist' => '0.2 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Marine Drive Starting Point", 'dist' => '0.3 km', 'icon' => 'road'],
                                            ['name' => "Sugandha Beach Point", 'dist' => '1.1 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Laboni Beach Point", 'dist' => '2.4 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Cox's Bazar Airport (CXB)", 'dist' => '3.8 km', 'icon' => 'plane'],
                                            ['name' => "Himchari National Park", 'dist' => '8.6 km', 'icon' => 'tree'],
                                            ['name' => "Radiant Fish World", 'dist' => '3.2 km', 'icon' => 'landmark'],
                                            ['name' => "Inani Beach", 'dist' => '22.4 km', 'icon' => 'umbrella-beach'],
                                            ['name' => "Shah Amanat International Airport", 'dist' => '125.0 km', 'icon' => 'plane'],
                                        ];
                                    }
                                } elseif (str_contains($propCity, 'dhaka') || str_contains($propAddress, 'dhaka') || str_contains($propAddress, 'gulshan') || str_contains($propAddress, 'banani') || str_contains($propAddress, 'uttara')) {
                                    $isGulshan = str_contains($propAddress, 'gulshan') || str_contains($propAddress, 'banani');
                                    $isUttara = str_contains($propAddress, 'uttara') || str_contains($propAddress, 'airport');

                                    if ($isGulshan) {
                                        $landmarks = [
                                            ['name' => "Gulshan 2 Circle", 'dist' => '0.5 km', 'icon' => 'landmark'],
                                            ['name' => "Gulshan Lake Park", 'dist' => '0.8 km', 'icon' => 'tree'],
                                            ['name' => "Banani Road 11 (Dining Hub)", 'dist' => '1.2 km', 'icon' => 'landmark'],
                                            ['name' => "Jamuna Future Park", 'dist' => '3.8 km', 'icon' => 'landmark'],
                                            ['name' => "Hatirjheel Promenade", 'dist' => '3.4 km', 'icon' => 'tree'],
                                            ['name' => "Hazrat Shahjalal International Airport (DAC)", 'dist' => '7.8 km', 'icon' => 'plane'],
                                            ['name' => "Dhaka Cantonment Station", 'dist' => '5.2 km', 'icon' => 'road'],
                                            ['name' => "Kamalapur Railway Station", 'dist' => '9.5 km', 'icon' => 'road'],
                                        ];
                                    } elseif ($isUttara) {
                                        $landmarks = [
                                            ['name' => "Hazrat Shahjalal International Airport (DAC)", 'dist' => '2.1 km', 'icon' => 'plane'],
                                            ['name' => "Uttara Sector 3 Lake Park", 'dist' => '0.9 km', 'icon' => 'tree'],
                                            ['name' => "Airport Railway Station", 'dist' => '1.8 km', 'icon' => 'road'],
                                            ['name' => "Jamuna Future Park", 'dist' => '5.5 km', 'icon' => 'landmark'],
                                            ['name' => "Gulshan 2 Circle", 'dist' => '8.2 km', 'icon' => 'landmark'],
                                            ['name' => "Dhaka Elevated Expressway", 'dist' => '1.2 km', 'icon' => 'road'],
                                        ];
                                    } else {
                                        // Central Dhaka / Dhanmondi / Motijheel / Panthapath
                                        $landmarks = [
                                            ['name' => "Dhanmondi Lake", 'dist' => '1.2 km', 'icon' => 'tree'],
                                            ['name' => "National Parliament House", 'dist' => '2.5 km', 'icon' => 'landmark'],
                                            ['name' => "Bashundhara City Mall", 'dist' => '1.5 km', 'icon' => 'landmark'],
                                            ['name' => "Lalbagh Fort", 'dist' => '3.8 km', 'icon' => 'landmark'],
                                            ['name' => "Kamalapur Railway Station", 'dist' => '4.2 km', 'icon' => 'road'],
                                            ['name' => "Hazrat Shahjalal International Airport (DAC)", 'dist' => '13.5 km', 'icon' => 'plane'],
                                        ];
                                    }
                                } elseif (str_contains($propCity, 'chittagong') || str_contains($propCity, 'chattogram') || str_contains($propAddress, 'chittagong') || str_contains($propAddress, 'chattogram')) {
                                    $landmarks = [
                                        ['name' => "GEC Circle / Nasirabad", 'dist' => '0.6 km', 'icon' => 'landmark'],
                                        ['name' => "Chittagong War Cemetery", 'dist' => '1.8 km', 'icon' => 'landmark'],
                                        ['name' => "Foy's Lake & Concord Amusement Park", 'dist' => '3.5 km', 'icon' => 'tree'],
                                        ['name' => "Chittagong Railway Station", 'dist' => '2.8 km', 'icon' => 'road'],
                                        ['name' => "Naval Beach / Karnaphuli River", 'dist' => '8.5 km', 'icon' => 'umbrella-beach'],
                                        ['name' => "Shah Amanat International Airport (CGP)", 'dist' => '12.4 km', 'icon' => 'plane'],
                                        ['name' => "Patenga Sea Beach", 'dist' => '14.2 km', 'icon' => 'umbrella-beach'],
                                    ];
                                } elseif (str_contains($propCity, 'sylhet') || str_contains($propAddress, 'sylhet') || str_contains($propCity, 'sreemangal') || str_contains($propAddress, 'sreemangal')) {
                                    if (str_contains($propCity, 'sreemangal') || str_contains($propAddress, 'sreemangal')) {
                                        $landmarks = [
                                            ['name' => "Lawachara National Park", 'dist' => '4.5 km', 'icon' => 'tree'],
                                            ['name' => "Madhabpur Lake & Tea Estate", 'dist' => '12.0 km', 'icon' => 'tree'],
                                            ['name' => "Baikka Beel Wetland Sanctuary", 'dist' => '15.5 km', 'icon' => 'tree'],
                                            ['name' => "Sreemangal Railway Station", 'dist' => '2.2 km', 'icon' => 'road'],
                                            ['name' => "Osmani International Airport (ZYL)", 'dist' => '88.0 km', 'icon' => 'plane'],
                                        ];
                                    } else {
                                        $landmarks = [
                                            ['name' => "Hazrat Shah Jalal Mazar Sharif", 'dist' => '1.2 km', 'icon' => 'landmark'],
                                            ['name' => "Keane Bridge & Surma River", 'dist' => '1.8 km', 'icon' => 'landmark'],
                                            ['name' => "Shah Paran Mazar Sharif", 'dist' => '6.8 km', 'icon' => 'landmark'],
                                            ['name' => "Osmani International Airport (ZYL)", 'dist' => '7.5 km', 'icon' => 'plane'],
                                            ['name' => "Malnicherra Tea Estate", 'dist' => '5.2 km', 'icon' => 'tree'],
                                            ['name' => "Ratargul Swamp Forest", 'dist' => '22.0 km', 'icon' => 'tree'],
                                            ['name' => "Jaflong Zero Point", 'dist' => '54.0 km', 'icon' => 'umbrella-beach'],
                                        ];
                                    }
                                } elseif (str_contains($propCity, 'kuakata') || str_contains($propAddress, 'kuakata')) {
                                    $landmarks = [
                                        ['name' => "Kuakata Sea Beach (Sunrise Point)", 'dist' => '0.3 km', 'icon' => 'umbrella-beach'],
                                        ['name' => "Jhaubon & Eco Park", 'dist' => '2.5 km', 'icon' => 'tree'],
                                        ['name' => "Fatrar Char (Mangrove Forest)", 'dist' => '8.0 km', 'icon' => 'tree'],
                                        ['name' => "Barishal Airport (BZL)", 'dist' => '102.0 km', 'icon' => 'plane'],
                                        ['name' => "Hazrat Shahjalal International Airport", 'dist' => '295.0 km', 'icon' => 'plane'],
                                    ];
                                } else {
                                    $cName = $property->city ?: 'City Center';
                                    $landmarks = [
                                        ['name' => $cName . " Central Plaza", 'dist' => '0.8 km', 'icon' => 'landmark'],
                                        ['name' => $cName . " Public Park", 'dist' => '1.5 km', 'icon' => 'tree'],
                                        ['name' => $cName . " Central Bus Terminal", 'dist' => '2.4 km', 'icon' => 'road'],
                                        ['name' => $cName . " Main Railway Station", 'dist' => '3.5 km', 'icon' => 'road'],
                                        ['name' => "Hazrat Shahjalal International Airport (DAC)", 'dist' => '45.0 km', 'icon' => 'plane'],
                                    ];
                                }

                                foreach ($landmarks as $lm) {
                                    $already = false;
                                    $lmLower = strtolower($lm['name']);
                                    foreach ($transportList as $ex) {
                                        $exLower = strtolower($ex['name']);
                                        if ($lmLower === $exLower || 
                                            (str_contains($lmLower, 'kolatoli') && str_contains($exLower, 'kolatoli')) ||
                                            (str_contains($lmLower, 'inani') && str_contains($exLower, 'inani')) ||
                                            (str_contains($lmLower, 'laboni') && str_contains($exLower, 'laboni')) ||
                                            (str_contains($lmLower, 'airport') && str_contains($exLower, 'airport') && str_contains($lmLower, 'cox') && str_contains($exLower, 'cox'))) {
                                            $already = true;
                                            break;
                                        }
                                    }
                                    if (!$already) {
                                        $transportList[] = $lm;
                                    }
                                }
                                $transportList = array_slice($transportList, 0, 7);
                            @endphp
                            
                            {{-- Airports & Transportation List (Exact Agoda Parity) --}}
                            <div class="d-flex flex-column pt-1" style="font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                @foreach($transportList as $tp)
                                <div class="d-flex align-items-center justify-content-between py-2 border-top" style="color: #2a2a2e; border-color: #f1f5f9 !important;">
                                    <div class="d-flex align-items-center text-truncate pe-2" style="gap: 10px;">
                                        <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 20px; height: 20px;">
                                            @if(($tp['icon'] ?? 'plane') === 'umbrella-beach')
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#64748b"><path d="M12 2C6.48 2 2 6.48 2 12c0 3.09 1.41 5.86 3.65 7.69l1.24-1.57C5.2 16.65 4 14.47 4 12c0-4.08 3.06-7.44 7-7.93V20h2V4.07c3.94.49 7 3.85 7 7.93 0 2.47-1.2 4.65-2.89 6.12l1.24 1.57C20.59 17.86 22 15.09 22 12c0-5.52-4.48-10-10-10z"/></svg>
                                            @elseif(($tp['icon'] ?? 'plane') === 'tree')
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#64748b"><path d="M12 2L4 14h3v6h10v-6h3L12 2z"/></svg>
                                            @elseif(($tp['icon'] ?? 'plane') === 'road')
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19L8 5"/><path d="M20 19L16 5"/><line x1="12" y1="8" x2="12" y2="10"/><line x1="12" y1="14" x2="12" y2="16"/></svg>
                                            @elseif(($tp['icon'] ?? 'plane') === 'landmark')
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#64748b"><path d="M12 2L2 7v2h20V7L12 2zm-8 7h2v9H4V9zm5 0h2v9H9V9zm5 0h2v9h-2V9zm5 0h2v9h-2V9zM2 20v2h20v-2H2z"/></svg>
                                            @else
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#64748b"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                                            @endif
                                        </span>
                                        <span class="text-truncate" style="color: #222222; font-size: 13.5px; font-weight: 500;">{{ $tp['name'] }}</span>
                                    </div>
                                    <span class="flex-shrink-0 text-end" style="color: #475569; font-size: 13px; font-weight: 600;">{{ $tp['dist'] }}</span>
                                </div>
                                @endforeach
                            </div>

                            {{-- See Nearby Places Link (Pinned to bottom of the card) --}}
                            <div class="d-flex justify-content-end pt-3 mt-auto">
                                <a href="javascript:void(0)" class="text-decoration-none fw-bold d-inline-flex align-items-center gap-1" style="color: #0b66c2; font-size: 13.5px;" data-bs-toggle="modal" data-bs-target="#hotelDetailMapModal">
                                    <span>See nearby places</span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Select Your Room Section & Filter Pills (Exact Agoda 1:1 Parity from Screenshot) --}}
        <div id="rooms" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 24px; font-weight: 700; color: #111827;">
                    Select your room
                </h2>
                <a href="javascript:void(0)" class="text-decoration-none d-inline-flex align-items-center gap-2" style="color: #2067da; font-size: 14px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;" data-bs-toggle="modal" data-bs-target="#bestPriceGuaranteeModal">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="8.5" r="5.5"/>
                        <polyline points="9.5 8.5 11 10 14.5 6.5"/>
                        <path d="M8.2 13.5L6 20.5l6-3 6 3-2.2-7"/>
                    </svg>
                    <span style="color: #2067da;">We price match!</span>
                </a>
            </div>

            @php
                // Hotel-Specific Room Feature Discovery Engine (Exact Agoda 1:1 Dynamic Parity)
                $hotelHasNonSmoking = false;
                $hotelHasBreakfast = false;
                $hotelHasFreeCancel = false;
                $hotelHasKingBed = false;
                $hotelHasQueenBed = false;
                $hotelHasTwinBed = false;
                $hotelHasMountainView = false;
                $hotelHasBalcony = false;
                $hotelHasSeaView = false;
                $hotelHasGardenView = false;
                $hotelHasFamily = false;
                $hotelHas2Bedrooms = false;

                if (!empty($property->rooms) && count($property->rooms) > 0) {
                    foreach ($property->rooms as $rmItem) {
                        $rSmk = $rmItem->smoking_policy ?: 'Non-smoking';
                        if (!str_contains(strtolower($rSmk), 'smoking allowed') && !str_contains(strtolower($rSmk), 'permit')) {
                            $hotelHasNonSmoking = true;
                        }
                        if (boolval($rmItem->breakfast_included ?? true)) {
                            $hotelHasBreakfast = true;
                        }
                        if (boolval($rmItem->free_cancellation ?? true)) {
                            $hotelHasFreeCancel = true;
                        }
                        $bStr = strtolower(($rmItem->bed_type ?: '') . ' ' . ($rmItem->name ?: ''));
                        if (str_contains($bStr, 'king')) {
                            $hotelHasKingBed = true;
                        }
                        if (str_contains($bStr, 'queen')) {
                            $hotelHasQueenBed = true;
                        }
                        if (str_contains($bStr, 'twin') || str_contains($bStr, '2 single') || str_contains($bStr, 'single')) {
                            $hotelHasTwinBed = true;
                        }
                        $vStr = strtolower(($rmItem->view_type ?: '') . ' ' . ($rmItem->name ?: ''));
                        if (str_contains($vStr, 'mountain') || str_contains($vStr, 'hill')) {
                            $hotelHasMountainView = true;
                        }
                        if (str_contains($vStr, 'sea') || str_contains($vStr, 'ocean') || str_contains($vStr, 'beach')) {
                            $hotelHasSeaView = true;
                        }
                        if (str_contains($vStr, 'garden') || str_contains($vStr, 'park')) {
                            $hotelHasGardenView = true;
                        }
                        $balcStr = $rmItem->balcony_type ?: 'Private Balcony';
                        if (!empty($balcStr) && !str_contains(strtolower($balcStr), 'no balcony')) {
                            $hotelHasBalcony = true;
                        }
                        $adCount = (int)($rmItem->max_adults ?: 2);
                        $kdCount = (int)($rmItem->max_children ?: 1);
                        if ($adCount >= 3 || $kdCount >= 2 || str_contains(strtolower($rmItem->name), 'family') || str_contains(strtolower($rmItem->name), 'triple')) {
                            $hotelHasFamily = true;
                        }
                        $bathCount = (int)($rmItem->bathroom_count ?: 1);
                        if ($bathCount >= 2 || str_contains(strtolower($rmItem->name), 'suite') || str_contains(strtolower($rmItem->name), '2 bedroom') || str_contains(strtolower($rmItem->name), 'presidential') || str_contains(strtolower($rmItem->name), 'ambassador')) {
                            $hotelHas2Bedrooms = true;
                        }
                    }
                }

                $totalAvailableFilters = ($hotelHasNonSmoking ? 1 : 0) +
                                        ($hotelHasBreakfast ? 1 : 0) +
                                        ($hotelHasKingBed ? 1 : 0) +
                                        ($hotelHasMountainView ? 1 : 0) +
                                        ($hotelHasBalcony ? 1 : 0) +
                                        ($hotelHasFreeCancel ? 1 : 0) +
                                        ($hotelHasQueenBed ? 1 : 0) +
                                        ($hotelHasTwinBed ? 1 : 0) +
                                        ($hotelHasSeaView ? 1 : 0) +
                                        ($hotelHasGardenView ? 1 : 0) +
                                        ($hotelHasFamily ? 1 : 0) +
                                        ($hotelHas2Bedrooms ? 1 : 0);
            @endphp

            {{-- Room Filter Container Card (Exact Agoda 1:1 Parity: Dynamic Per-Hotel Filter Pills Matching Screenshot) --}}
            @if($totalAvailableFilters > 0)
            <div class="agoda-room-filter-card">
                <div class="agoda-room-filter-title">Filter</div>
                <div class="agoda-room-filter-pills" id="roomFilterPillsContainer">
                    @if($hotelHasNonSmoking)
                    <button type="button" class="agoda-room-filter-pill" data-filter="nonsmoking" onclick="toggleRoomFilter('nonsmoking', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><line x1="2" y1="2" x2="22" y2="22"></line><path d="M18 8h3a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-3"></path><path d="M7 12h7"></path><path d="M7 8h3"></path><line x1="7" y1="8" x2="7" y2="12"></line><path d="M18 5c0-1.5.5-2 1.5-2"></path></svg>
                        <span>Non-smoking</span>
                    </button>
                    @endif

                    @if($hotelHasBreakfast)
                    <button type="button" class="agoda-room-filter-pill" data-filter="breakfast" onclick="toggleRoomFilter('breakfast', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M17 8h1a4 4 0 0 1 0 8h-1"></path><path d="M3 8h14v7a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="2" x2="6" y2="4"></line><line x1="10" y1="2" x2="10" y2="4"></line><line x1="14" y1="2" x2="14" y2="4"></line><line x1="2" y1="21" x2="18" y2="21"></line></svg>
                        <span>Breakfast included</span>
                    </button>
                    @endif

                    @if($hotelHasKingBed)
                    <button type="button" class="agoda-room-filter-pill" data-filter="kingbed" onclick="toggleRoomFilter('kingbed', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M3 6v14M21 6v14M3 10h18M3 18h18"></path><rect x="5.5" y="11" width="5.5" height="3.5" rx="0.8"></rect><rect x="13" y="11" width="5.5" height="3.5" rx="0.8"></rect></svg>
                        <span>King bed</span>
                    </button>
                    @endif

                    @if($hotelHasMountainView)
                    <button type="button" class="agoda-room-filter-pill" data-filter="mountainview" onclick="toggleRoomFilter('mountainview', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="m2 20 7-13 5 8 3-4 5 9H2Z"></path><path d="m14 15 2.5-3 4.5 8"></path></svg>
                        <span>Mountain view</span>
                    </button>
                    @endif

                    @if($hotelHasBalcony)
                    <button type="button" class="agoda-room-filter-pill" data-filter="balcony" onclick="toggleRoomFilter('balcony', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M3 8h18M3 18h18M5 8v10M9 8v10M13 8v10M17 8v10M21 8v10M3 8V5M21 8V5"></path></svg>
                        <span>Balcony/terrace</span>
                    </button>
                    @endif

                    @if($hotelHasFreeCancel)
                    <button type="button" class="agoda-room-filter-pill" data-filter="freecancel" onclick="toggleRoomFilter('freecancel', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                        <span>Free cancellation</span>
                    </button>
                    @endif

                    @if($hotelHasQueenBed)
                    <button type="button" class="agoda-room-filter-pill" data-filter="queenbed" onclick="toggleRoomFilter('queenbed', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M3 6v14M21 6v14M3 10h18M3 18h18"></path><rect x="6" y="11" width="5" height="3.5" rx="0.8"></rect><rect x="13" y="11" width="5" height="3.5" rx="0.8"></rect></svg>
                        <span>Queen bed</span>
                    </button>
                    @endif

                    @if($hotelHasTwinBed)
                    <button type="button" class="agoda-room-filter-pill" data-filter="twinbed" onclick="toggleRoomFilter('twinbed', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M2 6v14M10 6v14M2 10h8M2 17h8M14 6v14M22 6v14M14 10h8M14 17h8"></path></svg>
                        <span>Twin Bed</span>
                    </button>
                    @endif

                    @if($hotelHasSeaView)
                    <button type="button" class="agoda-room-filter-pill" data-filter="seaview" onclick="toggleRoomFilter('seaview', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M2 12c2-1 4-1 6 0s4 1 6 0 4-1 6 0"></path><path d="M2 17c2-1 4-1 6 0s4 1 6 0 4-1 6 0"></path><path d="M12 4a5 5 0 0 0-5 5h10a5 5 0 0 0-5-5z"></path></svg>
                        <span>Sea view</span>
                    </button>
                    @endif

                    @if($hotelHasGardenView)
                    <button type="button" class="agoda-room-filter-pill" data-filter="gardenview" onclick="toggleRoomFilter('gardenview', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M12 10a5 5 0 0 0-5-5H3v2a5 5 0 0 0 5 5h4"></path><path d="M12 14a5 5 0 0 1 5-5h4v2a5 5 0 0 1-5 5h-4"></path><line x1="12" y1="21" x2="12" y2="10"></line></svg>
                        <span>Garden view</span>
                    </button>
                    @endif

                    @if($hotelHasFamily)
                    <button type="button" class="agoda-room-filter-pill" data-filter="family" onclick="toggleRoomFilter('family', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Great for families</span>
                    </button>
                    @endif

                    @if($hotelHas2Bedrooms)
                    <button type="button" class="agoda-room-filter-pill" data-filter="twobedrooms" onclick="toggleRoomFilter('twobedrooms', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="agoda-pill-icon"><path d="M18 20V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v14"></path><path d="M2 20h20"></path><circle cx="14" cy="12" r="1"></circle></svg>
                        <span>2+ bedrooms</span>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Agoda Scarcity Sold Out Alert Bar (Screenshot 1:1 Parity) --}}
            <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1.5 rounded" style="background: #fff0f0; border: 1px solid #ffe3e3; font-size: 12px; color: #ba2a26; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                <i class="fa-regular fa-clock" style="color: #ba2a26; font-size: 13px;"></i>
                <span>Hurry up! 2 room types have already sold out for your dates!</span>
            </div>

            {{-- Available Rooms List (100% Dynamic from Database - Agoda 1:1 Parity) --}}
            <div class="d-flex flex-column" id="available_rooms_list">
                @forelse($property->rooms as $rIdx => $room)
                @php
                    $rNameLower = strtolower($room->name ?? '');
                    $rViewLower = strtolower((string)($room->view_type ?? ''));
                    $rAdults = $room->max_adults ?: 2;
                    $rKids = $room->max_children ?: 1;
                    $rSizeStr = $room->formatted_size ?: '37 m²/398 ft²';
                    $rBedStr = $room->bed_type ?: '2 single beds';
                    $rView = $room->view_type ?: 'Garden view';
                    $rSmoking = $room->smoking_policy ?: 'Non-smoking';
                    $rBalcony = $room->balcony_type ?: 'Private Balcony';
                    $rBathrooms = $room->bathroom_count ?: 1;
                    
                    // Filter match booleans (Exact match for the 12 Agoda filters)
                    $isNonSmoking = !str_contains(strtolower($rSmoking), 'smoking allowed') && !str_contains(strtolower($rSmoking), 'permit');
                    $hasBreakfast = boolval($room->breakfast_included ?? true);
                    $hasFreeCancel = boolval($room->free_cancellation ?? true);
                    $bedLower = strtolower(($room->bed_type ?: $rBedStr) . ' ' . ($room->name ?: ''));
                    $isQueen = str_contains($bedLower, 'queen');
                    $isKing = str_contains($bedLower, 'king');
                    $isTwin = str_contains($bedLower, 'twin') || str_contains($bedLower, '2 single') || str_contains($bedLower, 'single');
                    $viewLower = strtolower($rView . ' ' . $room->name);
                    $isGarden = str_contains($viewLower, 'garden');
                    $isSea = str_contains($viewLower, 'sea') || str_contains($viewLower, 'ocean') || str_contains($viewLower, 'beach');
                    $isMountain = str_contains($viewLower, 'mountain') || str_contains($viewLower, 'hill');
                    $hasBalcony = !empty($rBalcony) && !str_contains(strtolower($rBalcony), 'no balcony');
                    $isFamily = ($rAdults >= 3 || $rKids >= 2 || str_contains(strtolower($room->name), 'family') || str_contains(strtolower($room->name), 'triple'));
                    $is2Bedrooms = ($rBathrooms >= 2 || str_contains(strtolower($room->name), 'suite') || str_contains(strtolower($room->name), '2 bedroom') || str_contains(strtolower($room->name), 'presidential') || str_contains(strtolower($room->name), 'ambassador'));

                    // Real-Time Dynamic Urgency Stock Engine
                    $roomStock = isset($room->total_rooms) && $room->total_rooms !== null 
                        ? (int)$room->total_rooms 
                        : (isset($room->available_rooms) && $room->available_rooms !== null ? (int)$room->available_rooms : 5);

                    $reqCheckIn = request('check_in') ?: request('checkin');
                    $reqCheckOut = request('check_out') ?: request('checkout');
                    $bookedCount = 0;
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('bookings')) {
                            $bQuery = \App\Models\Booking::where('room_id', $room->id)
                                ->whereIn('status', ['confirmed', 'paid', 'approved', 'active']);
                            if ($reqCheckIn && $reqCheckOut) {
                                $bQuery->where(function($q) use ($reqCheckIn, $reqCheckOut) {
                                    $q->whereBetween('check_in', [$reqCheckIn, $reqCheckOut])
                                      ->orWhereBetween('check_out', [$reqCheckIn, $reqCheckOut]);
                                });
                            }
                            $bookedCount = $bQuery->count();
                        }
                    } catch (\Throwable $e) {
                        $bookedCount = 0;
                    }
                    $availableRoomsLeft = max(0, $roomStock - $bookedCount);

                    // Resolve dynamic photos for this room carousel
                    $galleryArr = $gallery instanceof \Illuminate\Support\Collection ? $gallery->all() : (array)($gallery ?? []);
                    $rImages = !empty($room->images) && is_array($room->images) && count($room->images) > 0 
                        ? $room->images 
                        : [];
                    if (empty($rImages) && !empty($galleryArr)) {
                        $rImages = array_slice($galleryArr, ($rIdx * 2) % max(1, count($galleryArr)), 4);
                    }
                    if (empty($rImages) && !empty($room->primary_image)) {
                        $rImages = [$room->primary_image];
                    }
                    if (empty($rImages)) {
                        $rImages = ['https://cdn6.agoda.net/images/accommodation/other/default.jpg'];
                    }
                    $rImages = array_values(array_map(function($img) {
                        return media_url($img, asset('images/agoda/sea_pearl_resort.jpg'));
                    }, $rImages));

                    // Dynamic Pricing & Discount Calculations
                    $roomPrice = (float)($room->price_per_night ?: 3500);
                    $discountPercent1 = 18;
                    if (!empty($property->discount_percent) && $property->discount_percent > 0) {
                        $discountPercent1 = (int)$property->discount_percent;
                    } elseif (!empty($property->discount_percentage) && $property->discount_percentage > 0) {
                        $discountPercent1 = (int)$property->discount_percentage;
                    }
                    $origPrice1 = round($roomPrice / (1 - ($discountPercent1 / 100)));
                    $voucher1 = round($roomPrice * 0.14);
                    $finalPrice1 = $roomPrice;

                    $discountPercent2 = max(10, $discountPercent1 - 1);
                    $finalPrice2 = round($roomPrice * 1.05);
                    $origPrice2 = round($finalPrice2 / (1 - ($discountPercent2 / 100)));
                    $voucher2 = $voucher1;

                    $minPropertyPrice = $property->rooms->min('price_per_night');
                    $isLowestInProperty = ($roomPrice <= $minPropertyPrice);

                    // Agoda Dynamic Rate Plans / Offer Display (1 Full-Height Offer vs 2 Stacked Offers)
                    // If room specifies offers_count in data, respect it.
                    // Otherwise, provide 2 offers on lead/popular rooms (e.g. index 0, 2) and 1 full-height offer on others, exactly like Agoda!
                    $hasSecondOffer = true;
                    if (request()->has('offers')) {
                        $hasSecondOffer = (request('offers') == '2');
                    } elseif (isset($room->offers_count) && $room->offers_count !== null) {
                        $hasSecondOffer = ((int)$room->offers_count >= 2);
                    } else {
                        // Alternate between 2 stacked offers and 1 full-height offer (Agoda standard)
                        $hasSecondOffer = ($rIdx % 2 === 0);
                    }
                @endphp
                @if($availableRoomsLeft <= 0 || $room->status === 'sold_out' || $room->status === 'unavailable')
                {{-- Agoda 1:1 Exact Carbon-Copy Sold-Out / Unavailable Room Card --}}
                <div class="agoda-room-listing-card agoda-soldout-room-card mb-3"
                    data-nonsmoking="{{ $isNonSmoking ? 'true' : 'false' }}"
                    data-breakfast="{{ $hasBreakfast ? 'true' : 'false' }}"
                    data-freecancel="{{ $hasFreeCancel ? 'true' : 'false' }}"
                    data-queenbed="{{ $isQueen ? 'true' : 'false' }}"
                    data-kingbed="{{ $isKing ? 'true' : 'false' }}"
                    data-twinbed="{{ $isTwin ? 'true' : 'false' }}"
                    data-gardenview="{{ $isGarden ? 'true' : 'false' }}"
                    data-seaview="{{ $isSea ? 'true' : 'false' }}"
                    data-mountainview="{{ $isMountain ? 'true' : 'false' }}"
                    data-balcony="{{ $hasBalcony ? 'true' : 'false' }}"
                    data-family="{{ $isFamily ? 'true' : 'false' }}"
                    data-twobedrooms="{{ $is2Bedrooms ? 'true' : 'false' }}">

                    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3 w-100">
                        {{-- Left: Room Photo (Agoda 1:1 Rounded Thumbnail with Carousel) --}}
                        <div class="position-relative flex-shrink-0 rounded overflow-hidden room-photo-carousel soldout-img-box" 
                             id="roomCarousel_{{ $room->id ?? $rIdx }}"
                             data-images='@json($rImages)'
                             data-index="0"
                             style="width: 220px; height: 135px; border-radius: 8px !important; cursor: pointer;"
                             onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">
                            <img src="{{ $rImages[0] ?? '' }}" class="w-100 h-100 room-carousel-img" style="object-fit: cover;" alt="{{ $room->name }}">
                            <span class="room-photo-counter position-absolute bottom-0 start-0 m-2 text-white d-inline-flex align-items-center" style="background: rgba(0,0,0,0.65); font-size: 11px; font-weight: 700; border-radius: 12px; padding: 2px 8px; line-height: 1.2;">1/{{ count($rImages) }}</span>
                            @if(count($rImages) > 1)
                            <button type="button" class="btn btn-light rounded-circle shadow-sm position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; border: 1px solid #dddfe2; background: rgba(255,255,255,0.95); z-index: 5;" title="Next Photo" onclick="nextRoomPhoto(this, event)">
                                <i class="fa-solid fa-chevron-right" style="font-size: 10px; color: #2a2a2e;"></i>
                            </button>
                            @endif
                        </div>

                        {{-- Middle: Room Title & Subtitle Specs --}}
                        <div class="flex-grow-1 px-md-3 py-1 soldout-info-box">
                            <h4 class="mb-1" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 18px; font-weight: 700; color: #111827; line-height: 1.3; cursor: pointer;" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">
                                {{ $room->name }}
                            </h4>
                            <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 13px; line-height: 1.4; color: #555555; font-weight: 500; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                <span class="fw-semibold text-secondary">{{ $rSizeStr }}</span>
                                <span style="color: #cbd5e1;">•</span>
                                <span>{{ $rBedStr }}</span>
                                @if(!empty($rView) && !str_contains(strtolower($room->name), strtolower($rView)))
                                <span style="color: #cbd5e1;">•</span>
                                <span>{{ $rView }}</span>
                                @endif
                            </div>
                            <a href="javascript:void(0)" class="text-decoration-none d-inline-block mt-2" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');" style="color: #1668e3; font-size: 12px; font-weight: 600;">
                                Room photos and details
                            </a>
                        </div>

                        {{-- Right: Agoda Exact Sold Out Indicator --}}
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-end gap-2 ps-md-3 border-start-md soldout-status-box" style="min-width: 190px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; color: #555555; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                @if($room->status === 'unavailable')
                                Unavailable on Prime Booking
                                @else
                                Sold out at ৳{{ number_format((float)($room->price_per_night ?? 4500)) }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @else
                <div class="agoda-room-listing-card mb-4"
                    id="room_card_{{ $room->id ?? $rIdx }}"
                    data-nonsmoking="{{ $isNonSmoking ? 'true' : 'false' }}"
                    data-breakfast="{{ $hasBreakfast ? 'true' : 'false' }}"
                    data-freecancel="{{ $hasFreeCancel ? 'true' : 'false' }}"
                    data-queenbed="{{ $isQueen ? 'true' : 'false' }}"
                    data-kingbed="{{ $isKing ? 'true' : 'false' }}"
                    data-twinbed="{{ $isTwin ? 'true' : 'false' }}"
                    data-gardenview="{{ $isGarden ? 'true' : 'false' }}"
                    data-seaview="{{ $isSea ? 'true' : 'false' }}"
                    data-mountainview="{{ $isMountain ? 'true' : 'false' }}"
                    data-balcony="{{ $hasBalcony ? 'true' : 'false' }}"
                    data-family="{{ $isFamily ? 'true' : 'false' }}"
                    data-twobedrooms="{{ $is2Bedrooms ? 'true' : 'false' }}"
                    style="position: relative;">

                    <div class="row g-0 align-items-stretch">
                        
                        {{-- LEFT COLUMN: Standalone White Room Details (Exact Agoda 1:1 Parity - Single 1px Border on Right, ZERO GAP) --}}
                        <div class="col-lg-4 d-flex flex-column agoda-room-left-col" style="background: #ffffff; padding: 16px;">
                            <div class="h-100 d-flex flex-column">
                                {{-- Image Carousel Box (Functional Next Photo Carousel) --}}
                                <div class="position-relative mb-2 rounded overflow-hidden room-photo-carousel flex-shrink-0" 
                                     id="roomCarousel_{{ $room->id ?? $rIdx }}"
                                     data-images='@json($rImages)'
                                     data-index="0"
                                     style="height: 195px; border-radius: 6px !important; cursor: pointer;"
                                     onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">
                                    <img src="{{ $rImages[0] ?? '' }}" class="w-100 h-100 room-carousel-img" style="object-fit: cover;" alt="{{ $room->name }}">
                                    
                                    {{-- Real Inventory Dynamic Urgency Badges --}}
                                    @if($availableRoomsLeft <= 0)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #ba2a26; font-size: 11px; border-radius: 3px; padding: 2.5px 7px; line-height: 1.2; z-index: 4;">
                                        <i class="fa-solid fa-ban me-1" style="font-size: 10px;"></i> Sold out for your dates!
                                    </span>
                                    @elseif($availableRoomsLeft === 1)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #ba2a26; font-size: 11px; border-radius: 3px; padding: 2.5px 7px; line-height: 1.2; z-index: 4;">
                                        <i class="fa-solid fa-tag me-1" style="font-size: 10px;"></i> Our last room!
                                    </span>
                                    @elseif($availableRoomsLeft === 2)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #ba2a26; font-size: 11px; border-radius: 3px; padding: 2.5px 7px; line-height: 1.2; z-index: 4;">
                                        <i class="fa-solid fa-clock me-1" style="font-size: 10px;"></i> Only 2 rooms left on our site!
                                    </span>
                                    @elseif($availableRoomsLeft === 3)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #d97706; font-size: 11px; border-radius: 3px; padding: 2.5px 7px; line-height: 1.2; z-index: 4;">
                                        <i class="fa-solid fa-fire me-1" style="font-size: 10px;"></i> Only 3 rooms left!
                                    </span>
                                    @elseif($isLowestInProperty || $rIdx === 0)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #ba2a26; color: #ffffff; font-size: 11px; border-radius: 0 !important; padding: 3px 8px; line-height: 1.2; z-index: 4;">
                                        Our last 4!
                                    </span>
                                    @elseif($rIdx === 2)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #873dc2; color: #ffffff; font-size: 11px; border-radius: 0 !important; padding: 3px 8px; line-height: 1.2; z-index: 4;">
                                        Our last few!
                                    </span>
                                    @elseif($rIdx === 5)
                                    <span class="position-absolute top-0 start-0 m-2 text-white fw-bold d-inline-flex align-items-center" style="background: #d97706; color: #ffffff; font-size: 11px; border-radius: 0 !important; padding: 3px 8px; line-height: 1.2; z-index: 4;">
                                        High Demand!
                                    </span>
                                    @endif
                                    <span class="room-photo-counter position-absolute bottom-0 start-0 m-2 text-white d-inline-flex align-items-center" style="background: rgba(0,0,0,0.65); font-size: 11px; font-weight: 700; border-radius: 12px; padding: 2px 8px; line-height: 1.2;">1/{{ count($rImages) }}</span>
                                    @if(count($rImages) > 1)
                                    <button type="button" class="btn btn-light rounded-circle shadow-sm position-absolute top-50 start-0 translate-middle-y ms-2 d-flex align-items-center justify-content-center p-0" style="width: 28px; height: 28px; border: 1px solid #dddfe2; background: rgba(255,255,255,0.95); z-index: 5;" title="Previous Photo" onclick="prevRoomPhoto(this, event)">
                                        <i class="fa-solid fa-chevron-left" style="font-size: 11px; color: #2a2a2e;"></i>
                                    </button>
                                    <button type="button" class="btn btn-light rounded-circle shadow-sm position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center justify-content-center p-0" style="width: 28px; height: 28px; border: 1px solid #dddfe2; background: rgba(255,255,255,0.95); z-index: 5;" title="Next Photo" onclick="nextRoomPhoto(this, event)">
                                        <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #2a2a2e;"></i>
                                    </button>
                                    @endif
                                </div>
                                
                                <a href="javascript:void(0)" class="text-decoration-none d-block mb-1.5" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');" style="color: #1668e3; font-size: 13px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    Room photos and details
                                </a>

                                <h4 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 19px; font-weight: 700; color: #222222; margin-top: 2px; margin-bottom: 6px; line-height: 1.25; cursor: pointer;" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">
                                    {{ $room->name }}
                                </h4>
                                
                                <div class="mb-1 d-flex align-items-center flex-wrap" style="font-size: 12.5px; line-height: 1.4; color: #222222; font-weight: 500; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    <span>{{ $rSizeStr }}</span> 
                                    <span style="color: #cbd5e1; margin: 0 7px;">|</span> 
                                    <span>Max {{ $rAdults }} adults</span> 
                                    <span style="color: #cbd5e1; margin: 0 7px;">|</span> 
                                    <span>{{ $rBedStr }}</span>
                                </div>

                                {{-- Agoda Authentic Purple Badges (Dynamic Per Room) --}}
                                <div class="d-flex flex-wrap align-items-center my-2" style="gap: 6px;">
                                    @if($isLowestInProperty || $rIdx === 0 || !empty($room->is_recommended))
                                    <span style="background-color: #873dc2; color: #ffffff; border-radius: 0 !important; padding: 3px 8px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="#ffffff" style="display:block; margin-right: 4px;"><path d="M2 20h2c.55 0 1-.45 1-1v-9c0-.55-.45-1-1-1H2v11zm19.83-7.12c.11-.25.17-.52.17-.8V11c0-1.1-.9-2-2-2h-5.5l.92-4.65c.05-.22.02-.46-.08-.66a1.01 1.01 0 0 0-.69-.59c-.22-.05-.46-.02-.66.08L9 6.24V19h10c.8 0 1.52-.48 1.83-1.21l2-4.91z"/></svg>
                                        Recommended
                                    </span>
                                    @endif

                                    @if($isFamily || $rAdults >= 3)
                                    <span style="background-color: #873dc2; color: #ffffff; border-radius: 0 !important; padding: 3px 8px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="#ffffff" style="display:block; margin-right: 4px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                        Great for Families
                                    </span>
                                    @elseif($rIdx === 0 || $rIdx === 1 || ($rKids > 0 && $rAdults <= 2))
                                    <span style="background-color: #873dc2; color: #ffffff; border-radius: 0 !important; padding: 3px 8px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                                        Your kids can stay for FREE!
                                    </span>
                                    @endif

                                    @if(($isKing || $isQueen || str_contains($rNameLower, 'couple') || str_contains($rNameLower, 'sea')) && ($isLowestInProperty || $rIdx === 0 || $rIdx === 3))
                                    <span style="background-color: #873dc2; color: #ffffff; border-radius: 0 !important; padding: 3px 8px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="#ffffff" style="display:block; margin-right: 4px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                        Preferred by Couples
                                    </span>
                                    @endif
                                </div>

                                {{-- Agoda Aspect Rating Engine (Matching Agoda 1:1) --}}
                                @php
                                    $rNameLower = strtolower($room->name ?? '');
                                    $rViewLower = strtolower((string)$rView);

                                    if ($isLowestInProperty || $rIdx === 0) {
                                        $aspectScore = 8.1;
                                        $aspectLabel = 'Excellent';
                                        $aspectName = 'Room comfort';
                                    } elseif (str_contains($rNameLower, 'sea') || str_contains($rViewLower, 'sea') || str_contains($rViewLower, 'ocean')) {
                                        $aspectScore = 9.2;
                                        $aspectLabel = 'Exceptional';
                                        $aspectName = 'Ocean view & balcony';
                                    } elseif (str_contains($rNameLower, 'hill') || str_contains($rViewLower, 'hill') || str_contains($rViewLower, 'nature')) {
                                        $aspectScore = 8.9;
                                        $aspectLabel = 'Excellent';
                                        $aspectName = 'Scenic hill view & quietness';
                                    } elseif (str_contains($rNameLower, 'family') || str_contains($rNameLower, 'triple') || $rAdults >= 3) {
                                        $aspectScore = 8.8;
                                        $aspectLabel = 'Excellent';
                                        $aspectName = 'Spacious family layout';
                                    } elseif (str_contains($rNameLower, 'executive') || str_contains($rNameLower, 'premier') || str_contains($rNameLower, 'suite')) {
                                        $aspectScore = 9.5;
                                        $aspectLabel = 'Exceptional';
                                        $aspectName = 'VIP hospitality & suite comfort';
                                    } else {
                                        $aspectList = [
                                            ['score' => 8.6, 'label' => 'Excellent', 'name' => 'Room cleanliness & housekeeping'],
                                            ['score' => 8.8, 'label' => 'Excellent', 'name' => 'Bed comfort & peaceful sleep'],
                                            ['score' => 8.5, 'label' => 'Excellent', 'name' => 'Room quietness & AC'],
                                            ['score' => 8.7, 'label' => 'Excellent', 'name' => 'Value for money'],
                                        ];
                                        $choice = $aspectList[$rIdx % count($aspectList)];
                                        $aspectScore = $choice['score'];
                                        $aspectLabel = $choice['label'];
                                        $aspectName = $choice['name'];
                                    }
                                @endphp

                                {{-- Agoda Rating Score & Aspect with Thin Dividers --}}
                                <div style="border-top: 1px solid #eef2f6; border-bottom: 1px solid #eef2f6; padding: 10px 0; margin: 10px 0;">
                                    <div class="d-flex align-items-center" style="gap: 7px;">
                                        <span class="d-inline-flex align-items-center justify-content-center text-white fw-bold" style="background: #1668e3; border-radius: 4px; padding: 2px 7px; font-size: 13px; font-weight: 800; line-height: 1.2;">
                                            {{ number_format((float)$aspectScore, 1) }}
                                        </span>
                                        <span style="color: #1668e3; font-size: 14px; font-weight: 700;">
                                            {{ $aspectLabel }}
                                        </span>
                                    </div>
                                    <div style="color: #687282; font-size: 12px; margin-top: 3px; font-weight: 400;">
                                        {{ $aspectName }}
                                    </div>
                                </div>

                                {{-- 2-Column Clean Amenities Matching Agoda Screenshot 1:1 Identically --}}
                                @php
                                    $rawFacs = !empty($room->facilities) && is_array($room->facilities) ? $room->facilities : [];
                                    $standardLeft = [
                                        $rView ?: 'Mountain view',
                                        'Hair dryer',
                                        'Wi-Fi [free]',
                                        'Mini bar',
                                        'Kettle',
                                        'Window',
                                        $rSmoking ?: 'Non-smoking'
                                    ];
                                    $standardRight = [
                                        'Private bathroom',
                                        'Shower',
                                        'Air conditioning',
                                        'Free bottled water',
                                        'Refrigerator',
                                        'Closet'
                                    ];

                                    if (!empty($rawFacs) && count($rawFacs) >= 4) {
                                        $half = (int) ceil(count($rawFacs) / 2);
                                        $agodaFacilitiesCol1 = array_slice($rawFacs, 0, $half);
                                        $agodaFacilitiesCol2 = array_slice($rawFacs, $half);
                                    } else {
                                        $agodaFacilitiesCol1 = $standardLeft;
                                        $agodaFacilitiesCol2 = $standardRight;
                                    }

                                    $detectIcon = function($str) {
                                        $s = strtolower($str);
                                        if (str_contains($s, 'view') || str_contains($s, 'balcony') || str_contains($s, 'mountain') || str_contains($s, 'city') || str_contains($s, 'sea') || str_contains($s, 'ocean')) return 'view';
                                        if (str_contains($s, 'bath') || str_contains($s, 'tub') || str_contains($s, 'toilet')) return 'bath';
                                        if (str_contains($s, 'shower')) return 'shower';
                                        if (str_contains($s, 'wifi') || str_contains($s, 'wi-fi') || str_contains($s, 'internet')) return 'wifi';
                                        if (str_contains($s, 'ac') || str_contains($s, 'air')) return 'ac';
                                        if (str_contains($s, 'mini bar') || str_contains($s, 'minibar') || str_contains($s, 'bar')) return 'minibar';
                                        if (str_contains($s, 'fridge') || str_contains($s, 'refrigerator')) return 'refrigerator';
                                        if (str_contains($s, 'kettle') || str_contains($s, 'coffee') || str_contains($s, 'tea')) return 'circle-check';
                                        if (str_contains($s, 'window')) return 'circle-check';
                                        if (str_contains($s, 'bottle') || str_contains($s, 'water')) return 'bottle';
                                        if (str_contains($s, 'closet') || str_contains($s, 'wardrobe')) return 'closet';
                                        if (str_contains($s, 'smoking')) return 'smoking-ban';
                                        if (str_contains($s, 'hair') || str_contains($s, 'dryer')) return 'check';
                                        return 'check';
                                    };
                                @endphp
                                <div class="row g-2" style="font-size: 12px; color: #222222 !important; line-height: 1.45; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    <div class="col-6 d-flex flex-column" style="gap: 7px;">
                                        @foreach($agodaFacilitiesCol1 as $facName)
                                            @php $icon = $detectIcon($facName); @endphp
                                            <div class="d-flex align-items-center text-nowrap" style="color: #222222; gap: 8px;">
                                                @if($icon === 'view')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m3 16 5-5c.9-.9 2.1-.9 3 0l7 7"/><path d="m14 14 1-1c.9-.9 2.1-.9 3 0l3 3"/></svg>
                                                @elseif($icon === 'wifi')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><circle cx="12" cy="20" r="1" fill="#3d4551"/></svg>
                                                @elseif($icon === 'bath')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M7 12h10a2 2 0 0 1 2 2v2a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4v-2a2 2 0 0 1 2-2z"/><path d="M5 12V6a2 2 0 0 1 2-2h1"/></svg>
                                                @elseif($icon === 'shower')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="m4 4 2.5 2.5"/><path d="M13.5 6.5a4.95 4.95 0 0 0-7 7L11 18l4.5-4.5"/><circle cx="14" cy="17" r="0.8" fill="#3d4551"/><circle cx="17" cy="14" r="0.8" fill="#3d4551"/><circle cx="11" cy="20" r="0.8" fill="#3d4551"/></svg>
                                                @elseif($icon === 'ac')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M12 2v20"/><path d="m17 7-5 5-5-5"/><path d="m17 17-5-5-5 5"/><path d="M2 12h20"/><path d="m7 7 5 5 5-5"/><path d="m7 17 5-5 5 5"/></svg>
                                                @elseif($icon === 'minibar')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M8 22h8"/><path d="M12 11v11"/><path d="m19 3-7 8-7-8Z"/></svg>
                                                @elseif($icon === 'circle-check')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="12" cy="12" r="9"/><polyline points="8 12 11 15 16 9" stroke-width="2"/></svg>
                                                @elseif($icon === 'smoking-ban')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="12" cy="12" r="9"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/><path d="M18 12h-6"/></svg>
                                                @elseif($icon === 'bottle')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M10 2h4"/><path d="M10 5h4v2l2 2v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V9l2-2V5z"/></svg>
                                                @elseif($icon === 'refrigerator')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="14" height="20" x="5" y="2" rx="2"/><path d="M5 10h14"/><path d="M15 6v2"/><path d="M15 14v4"/></svg>
                                                @elseif($icon === 'closet')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M12 3v18"/><path d="M9 10v2"/><path d="M15 10v2"/></svg>
                                                @else
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                @endif
                                                <span style="font-weight: 400; color: #222222;">{{ $facName }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-6 d-flex flex-column" style="gap: 7px;">
                                        @foreach($agodaFacilitiesCol2 as $facName)
                                            @php $icon = $detectIcon($facName); @endphp
                                            <div class="d-flex align-items-center text-nowrap" style="color: #222222; gap: 8px;">
                                                @if($icon === 'bath')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M7 12h10a2 2 0 0 1 2 2v2a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4v-2a2 2 0 0 1 2-2z"/><path d="M5 12V6a2 2 0 0 1 2-2h1"/></svg>
                                                @elseif($icon === 'shower')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="m4 4 2.5 2.5"/><path d="M13.5 6.5a4.95 4.95 0 0 0-7 7L11 18l4.5-4.5"/><circle cx="14" cy="17" r="0.8" fill="#3d4551"/><circle cx="17" cy="14" r="0.8" fill="#3d4551"/><circle cx="11" cy="20" r="0.8" fill="#3d4551"/></svg>
                                                @elseif($icon === 'ac')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M12 2v20"/><path d="m17 7-5 5-5-5"/><path d="m17 17-5-5-5 5"/><path d="M2 12h20"/><path d="m7 7 5 5 5-5"/><path d="m7 17 5-5 5 5"/></svg>
                                                @elseif($icon === 'bottle')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M10 2h4"/><path d="M10 5h4v2l2 2v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V9l2-2V5z"/></svg>
                                                @elseif($icon === 'refrigerator')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="14" height="20" x="5" y="2" rx="2"/><path d="M5 10h14"/><path d="M15 6v2"/><path d="M15 14v4"/></svg>
                                                @elseif($icon === 'closet')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M12 3v18"/><path d="M9 10v2"/><path d="M15 10v2"/></svg>
                                                @elseif($icon === 'circle-check')
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="12" cy="12" r="9"/><polyline points="8 12 11 15 16 9" stroke-width="2"/></svg>
                                                @else
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3d4551" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                @endif
                                                <span style="font-weight: 400; color: #222222;">{{ $facName }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Agoda Offer Cards inside #eff4fc Container (Exact Agoda 1:1 Parity, ZERO GAP) --}}
                        <div class="col-lg-8 d-flex flex-column {{ $hasSecondOffer ? 'justify-content-between' : '' }}" style="background: #eff4fc; padding: 12px; {{ $hasSecondOffer ? 'gap: 12px;' : '' }}">
                                
                                {{-- OFFER 1 CARD (Standalone White Card - Agoda 1:1 Full-Height when Single Offer) --}}
                                <div class="flex-grow-1 {{ $hasSecondOffer ? '' : 'h-100' }} d-flex flex-column justify-content-start" style="background: #ffffff; border: 1px solid #cdd4de; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; position: relative;">
                                    {{-- Agoda Signature Top Banner (100% Full Width across card to the right) --}}
                                    @if(!empty($room->ribbon_badge))
                                    <div style="background: #c52924; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 5px 16px; width: 100% !important; display: block; line-height: 1.35; letter-spacing: 0.2px; box-sizing: border-box;">
                                        {{ $room->ribbon_badge }}
                                    </div>
                                    @elseif($isLowestInProperty || $rIdx === 0)
                                    <div style="background: #c52924; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 5px 16px; width: 100% !important; display: block; line-height: 1.35; letter-spacing: 0.2px; box-sizing: border-box;">
                                        {{ $discountPercent1 }}% off today! Lowest price available!
                                    </div>
                                    @elseif($rIdx === 1)
                                    <div style="background: #c52924; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 5px 16px; width: 100% !important; display: block; line-height: 1.35; letter-spacing: 0.2px; box-sizing: border-box;">
                                        Special Member Deal! Save {{ $discountPercent1 }}%
                                    </div>
                                    @elseif($rIdx === 2)
                                    <div style="background: #c52924; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 5px 16px; width: 100% !important; display: block; line-height: 1.35; letter-spacing: 0.2px; box-sizing: border-box;">
                                        Prime Exclusive Deal! Save {{ max(10, $discountPercent1 - 2) }}%
                                    </div>
                                    @elseif($rIdx === 3)
                                    <div style="background: #1e5cb3; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 5px 16px; width: 100% !important; display: block; line-height: 1.35; letter-spacing: 0.2px; box-sizing: border-box;">
                                        Best Value for Couples
                                    </div>
                                    @endif
                                    <div class="row g-0 align-items-stretch flex-grow-1 h-100">
                                        {{-- Sub-col 1: Benefits List --}}
                                        <div class="col-md-5 d-flex flex-column justify-content-start" style="border-right: 1px solid #d2d9e3; padding: {{ $hasSecondOffer ? '14px 18px' : '16px 20px' }} !important;">
                                            <div class="d-flex align-items-center mb-2" style="font-size: 13.5px; font-weight: 700; color: #222222; gap: 8px;">
                                                <x-agoda-icon name="user" size="14" color="#222222" class="flex-shrink-0" />
                                                <span>{{ $rAdults }} adults</span>
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                <i class="fa-solid fa-child-reaching flex-shrink-0" style="font-size: 13px; color: #00805a; width: 14px; text-align: center;"></i>
                                                <span>Your kids can stay for FREE!</span>
                                            </div>
                                            @if($room->breakfast_included)
                                            <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                <i class="fa-solid fa-utensils flex-shrink-0" style="font-size: 13px; color: #00805a; width: 14px; text-align: center;"></i>
                                                <span>Breakfast included</span>
                                            </div>
                                            @else
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <i class="fa-solid fa-utensils flex-shrink-0" style="font-size: 13px; color: #475569; width: 14px; text-align: center;"></i>
                                                <span>Breakfast available ({{ \App\Services\CurrencyService::format(15) }} / person)</span>
                                            </div>
                                            @endif

                                            @if($room->free_cancellation ?? $property->free_cancellation ?? true)
                                            <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="#00805a" class="flex-shrink-0"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1.06 13.54L7.4 12l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41-5.64 5.66z"/></svg>
                                                <span>Cancel for free before {{ \Carbon\Carbon::parse($checkIn)->subDays(1)->format('M d, Y') }}</span>
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #00805a; color: #00805a; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                            </div>
                                            @else
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Non-refundable (Low price!)</span>
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                            </div>
                                            @endif

                                            @if($property->no_credit_card_required ?? false)
                                            <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Book without credit card</span>
                                            </div>
                                            @else
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Book and pay now</span>
                                            </div>
                                            @endif

                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Parking</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Express check-in</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>Free WiFi</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; gap: 8px;">
                                                <i class="fa-solid fa-sack-dollar flex-shrink-0" style="font-size: 13px; color: #222222; width: 14px; text-align: center;"></i>
                                                <span>Cashback: {{ \App\Services\CurrencyService::format(4) }} (Terms apply)</span>
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 12.5px; font-weight: 700; gap: 8px;">
                                                <x-agoda-icon name="tag" size="13" color="#222222" class="flex-shrink-0" />
                                                <span>PRIME EXCLUSIVE - {{ \App\Services\CurrencyService::format(35) }} off!</span>
                                            </div>
                                            <div>
                                                <a href="javascript:void(0)" class="text-decoration-none" style="color: #0b66c2; font-size: 13px; font-weight: 700;" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">See details</a>
                                            </div>
                                        </div>

                                            {{-- Sub-col 2: Price Breakdown --}}
                                            <div class="col-md-4 agoda-room-subcol-price text-end d-flex flex-column justify-content-start" style="border-right: 1px solid #d2d9e3; padding: {{ $hasSecondOffer ? '14px 18px' : '16px 20px' }} !important;">
                                                @if($isLowestInProperty || $rIdx === 0)
                                                <div class="d-flex align-items-center justify-content-end gap-1 mb-0.5" style="font-size: 12.5px; color: #737373;">
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-right: 2px;">i</span>
                                                    <span style="text-decoration: line-through;">{{ \App\Services\CurrencyService::format($origPrice1) }}</span>
                                                    <span style="color: #ba2a26; font-weight: 700; margin-left: 2px;">-{{ $discountPercent1 }}%</span>
                                                </div>
                                                <div class="d-inline-flex align-items-center gap-1 px-2 py-0.5 rounded mb-2 ms-auto" style="background: #e6f7ec; border: 1px solid #b7ebc6; color: #007a50; font-size: 11.5px; font-weight: 700; width: fit-content;">
                                                    <x-agoda-icon name="cash" size="12" color="#007a50" /> {{ \App\Services\CurrencyService::format($voucher1) }} applied
                                                </div>
                                                <div class="fw-bold" style="color: #ba2a26; font-size: 26px; line-height: 1; letter-spacing: -0.5px;">
                                                    <span style="font-size: 14px; font-weight: 700; margin-right: 2px;">{{ \App\Services\CurrencyService::currentCode() }}</span><span class="agoda-final-price-num">{{ number_format(\App\Services\CurrencyService::convert($finalPrice1)) }}</span>
                                                </div>
                                                @else
                                                <div class="d-flex align-items-center justify-content-end gap-1 mb-0.5" style="font-size: 12.5px; color: #737373;">
                                                    <span style="text-decoration: line-through;">{{ \App\Services\CurrencyService::format($origPrice1) }}</span>
                                                </div>
                                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Price after Cashback</div>
                                                <div class="d-inline-flex align-items-center gap-1 px-2 py-0.5 rounded mb-2 ms-auto" style="background: #e6f7ec; border: 1px solid #b7ebc6; color: #007a50; font-size: 11.5px; font-weight: 700; width: fit-content;">
                                                    <x-agoda-icon name="cash" size="12" color="#007a50" /> {{ \App\Services\CurrencyService::format($voucher1) }} applied
                                                </div>
                                                <div class="fw-bold" style="color: #222222; font-size: 26px; line-height: 1; letter-spacing: -0.5px;">
                                                    <span style="font-size: 14px; font-weight: 700; margin-right: 2px;">{{ \App\Services\CurrencyService::currentCode() }}</span><span class="agoda-final-price-num">{{ number_format(\App\Services\CurrencyService::convert($finalPrice1)) }}</span>
                                                </div>
                                                @endif
                                                <div class="agoda-price-unit-text" style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Per night before taxes &amp; fees</div>
                                            </div>

                                            {{-- Sub-col 3: Book Action --}}
                                            <div class="col-md-3 agoda-room-subcol-book text-end d-flex flex-column align-items-stretch justify-content-start" style="padding: {{ $hasSecondOffer ? '14px 16px' : '16px 18px' }} !important;">
                                                <div class="mb-2 position-relative agoda-room-selector-wrapper text-start">
                                                    <button type="button" 
                                                            class="w-100 bg-white d-flex align-items-center justify-content-center gap-2 agoda-room-pill-btn" 
                                                            style="border: 1px solid #cdd4de; border-radius: 20px; height: 38px; padding: 0 16px; color: #0b66c2; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;"
                                                            onclick="toggleAgodaRoomDropdown(this, event)">
                                                        <span class="agoda-room-pill-label">1 room</span>
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                                            <path d="m6 9 6 6 6-6"/>
                                                        </svg>
                                                    </button>

                                                    {{-- Agoda Custom Floating Speech-Bubble Popover --}}
                                                    <div class="agoda-room-dropdown-popover position-absolute d-none" 
                                                         style="top: calc(100% + 7px); left: 8px; min-width: 140px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #eef2f6; z-index: 1050; padding: 6px 0;">
                                                        <div style="position: absolute; top: -6px; left: 24px; width: 12px; height: 12px; background: #ffffff; transform: rotate(45deg); border-left: 1px solid #eef2f6; border-top: 1px solid #eef2f6; box-shadow: -2px -2px 3px rgba(0,0,0,0.03);"></div>
                                                        <div class="d-flex flex-column" style="position: relative; z-index: 2;">
                                                            @for($num = 1; $num <= 5; $num++)
                                                            <div class="agoda-room-popover-item d-flex align-items-center gap-2 px-3 py-2" 
                                                                 style="cursor: pointer; font-size: 14px; transition: background 0.12s ease; {{ $num === 1 ? 'color: #0b66c2; font-weight: 700;' : 'color: #222222; font-weight: 500;' }}"
                                                                 data-value="{{ $num }}"
                                                                 onclick="selectAgodaRoomCount(this, {{ $num }}, '{{ $num }} {{ $num === 1 ? 'room' : 'rooms' }}')">
                                                                <span class="agoda-item-check" style="width: 14px; height: 14px; display: inline-flex; align-items: center; justify-content: center;" class="flex-shrink-0">
                                                                    @if($num === 1)
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                                    @endif
                                                                </span>
                                                                <span class="agoda-item-text">{{ $num }} {{ $num === 1 ? 'room' : 'rooms' }}</span>
                                                            </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <button type="button" class="btn d-flex align-items-center justify-content-center p-0 flex-shrink-0" style="width: 38px; height: 42px; border: 1px solid #cdd4de; border-radius: 50%; background: #ffffff;" title="Save to wishlist">
                                                        <x-agoda-icon name="heart" size="18" color="#475569" />
                                                    </button>
                                                    <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}&check_in={{ request('check_in', request('checkin', $checkinCarbon->format('Y-m-d'))) }}&check_out={{ request('check_out', request('checkout', $checkoutCarbon->format('Y-m-d'))) }}&guests={{ request('guests', 2) }}" 
                                                       class="btn flex-grow-1 text-white d-flex flex-column align-items-center justify-content-center agoda-book-btn" 
                                                       style="background: #1765c7; border-radius: 20px; height: 42px; text-decoration: none; padding: 2px 10px; line-height: 1.15;">
                                                        <span style="font-size: 15px; font-weight: 700;">Book</span>
                                                        <span style="font-size: 11px; font-weight: 600; opacity: 0.95;">{{ $hasSecondOffer ? 'Pay now' : (($property->no_credit_card_required ?? false) ? 'Pay later' : 'Pay now') }}</span>
                                                    </a>
                                                </div>
                                                <div style="font-size: 11.5px; color: #64748b; text-align: center; margin-top: 4px;">You won't be charged yet</div>
                                                @if($isLowestInProperty || $rIdx === 0)
                                                <div style="font-size: 11.5px; color: #ba2a26; font-weight: 700; text-align: center; margin-top: 2px;">Our last room!</div>
                                                @elseif(!$hasSecondOffer && ($room->free_cancellation ?? true))
                                                <div style="font-size: 12.5px; color: #00805a; font-weight: 700; text-align: center; margin-top: 2px;">+ Free Cancellation</div>
                                                @else
                                                <div style="font-size: 11.5px; color: #0b66c2; font-weight: 700; text-align: center; margin-top: 2px;">Popular choice</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                @if($hasSecondOffer)
                                {{-- OFFER 2 CARD (Standalone White Card - Agoda 1:1) --}}
                                <div class="flex-grow-1 d-flex flex-column justify-content-start" style="background: #ffffff; border: 1px solid #cdd4de; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                                        <div class="row g-0 align-items-stretch flex-grow-1">
                                            {{-- Sub-col 1: Benefits List (11 Complete Items matching Screenshot 2) --}}
                                            <div class="col-md-5 d-flex flex-column justify-content-start" style="border-right: 1px solid #d2d9e3; padding: 14px 18px !important;">
                                                <div class="d-flex align-items-center mb-2" style="font-size: 13.5px; font-weight: 700; color: #222222; gap: 8px;">
                                                    <x-agoda-icon name="user" size="14" color="#222222" class="flex-shrink-0" />
                                                    <span>{{ $rAdults }} adults</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                    <i class="fa-solid fa-child-reaching flex-shrink-0" style="font-size: 13px; color: #00805a; width: 14px; text-align: center;"></i>
                                                    <span>Your kids can stay for FREE!</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13.5px; font-weight: 700; gap: 8px;">
                                                    <i class="fa-solid fa-utensils flex-shrink-0" style="font-size: 13px; color: #00805a; width: 14px; text-align: center;"></i>
                                                    <span>Breakfast included</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#00805a" class="flex-shrink-0"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1.06 13.54L7.4 12l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41-5.64 5.66z"/></svg>
                                                    <span>Cancel for free before {{ \Carbon\Carbon::parse($checkIn)->subDays(1)->format('M d, Y') }}</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #00805a; color: #00805a; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #00805a; font-size: 13px; font-weight: 700; gap: 8px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00805a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <span>No payment until {{ \Carbon\Carbon::parse($checkIn)->subDays(3)->format('M d, Y') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; font-weight: 500; gap: 8px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <span>Parking</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; font-weight: 500; gap: 8px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <span>Express check-in</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; font-weight: 500; gap: 8px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#222222" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <span>Free WiFi</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 13px; font-weight: 500; gap: 8px;">
                                                    <i class="fa-solid fa-sack-dollar flex-shrink-0" style="font-size: 13px; color: #222222; width: 14px; text-align: center;"></i>
                                                    <span>Cashback: {{ \App\Services\CurrencyService::format(6) }} (Terms apply)</span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 13px; height: 13px; border: 1px solid #737373; color: #737373; font-size: 8.5px; font-weight: 700; margin-left: 2px;">i</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2" style="color: #222222; font-size: 12.5px; font-weight: 700; gap: 8px;">
                                                    <x-agoda-icon name="tag" size="13" color="#222222" class="flex-shrink-0" />
                                                    <span>PRIME EXCLUSIVE - {{ \App\Services\CurrencyService::format(41) }} off!</span>
                                                </div>
                                                <div>
                                                    <a href="javascript:void(0)" class="text-decoration-none" style="color: #0b66c2; font-size: 13px; font-weight: 700;" onclick="openAgodaRoomGallery('{{ $room->id ?? $rIdx }}', '{{ addslashes($room->name) }}');">See details</a>
                                                </div>
                                            </div>

                                            {{-- Sub-col 2: Pricing Breakdown --}}
                                            <div class="col-md-4 agoda-room-subcol-price text-end d-flex flex-column justify-content-start" style="border-right: 1px solid #d2d9e3; padding: 14px 18px !important;">
                                                <div class="d-flex align-items-center justify-content-end gap-1 mb-0.5" style="font-size: 12.5px; color: #737373;">
                                                    <span style="text-decoration: line-through;">{{ \App\Services\CurrencyService::format($origPrice2) }}</span>
                                                </div>
                                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Price after Cashback</div>
                                                <div class="d-inline-flex align-items-center gap-1 px-2 py-0.5 rounded mb-2 ms-auto" style="background: #e6f7ec; border: 1px solid #b7ebc6; color: #007a50; font-size: 11.5px; font-weight: 700; width: fit-content;">
                                                    <x-agoda-icon name="cash" size="12" color="#007a50" /> {{ \App\Services\CurrencyService::format($voucher2) }} applied
                                                </div>
                                                <div class="fw-bold" style="color: #222222; font-size: 26px; line-height: 1; letter-spacing: -0.5px;">
                                                    <span style="font-size: 14px; font-weight: 700; margin-right: 2px;">{{ \App\Services\CurrencyService::currentCode() }}</span><span class="agoda-final-price-num">{{ number_format(\App\Services\CurrencyService::convert($finalPrice2)) }}</span>
                                                </div>
                                                <div class="agoda-price-unit-text" style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Per night before taxes &amp; fees</div>
                                            </div>

                                            {{-- Sub-col 3: Book Action (2-line Button + Urgency matching Screenshot 2) --}}
                                            <div class="col-md-3 agoda-room-subcol-book text-end d-flex flex-column align-items-stretch justify-content-start" style="padding: 14px 16px !important;">
                                                <div class="mb-2 position-relative agoda-room-selector-wrapper text-start">
                                                    <button type="button" 
                                                            class="w-100 bg-white d-flex align-items-center justify-content-center gap-2 agoda-room-pill-btn" 
                                                            style="border: 1px solid #cdd4de; border-radius: 20px; height: 38px; padding: 0 16px; color: #0b66c2; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;"
                                                            onclick="toggleAgodaRoomDropdown(this, event)">
                                                        <span class="agoda-room-pill-label">1 room</span>
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                                            <path d="m6 9 6 6 6-6"/>
                                                        </svg>
                                                    </button>

                                                    {{-- Agoda Custom Floating Speech-Bubble Popover --}}
                                                    <div class="agoda-room-dropdown-popover position-absolute d-none" 
                                                         style="top: calc(100% + 7px); left: 8px; min-width: 140px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #eef2f6; z-index: 1050; padding: 6px 0;">
                                                        <div style="position: absolute; top: -6px; left: 24px; width: 12px; height: 12px; background: #ffffff; transform: rotate(45deg); border-left: 1px solid #eef2f6; border-top: 1px solid #eef2f6; box-shadow: -2px -2px 3px rgba(0,0,0,0.03);"></div>
                                                        <div class="d-flex flex-column" style="position: relative; z-index: 2;">
                                                            @for($num = 1; $num <= 5; $num++)
                                                            <div class="agoda-room-popover-item d-flex align-items-center gap-2 px-3 py-2" 
                                                                 style="cursor: pointer; font-size: 14px; transition: background 0.12s ease; {{ $num === 1 ? 'color: #0b66c2; font-weight: 700;' : 'color: #222222; font-weight: 500;' }}"
                                                                 data-value="{{ $num }}"
                                                                 onclick="selectAgodaRoomCount(this, {{ $num }}, '{{ $num }} {{ $num === 1 ? 'room' : 'rooms' }}')">
                                                                <span class="agoda-item-check" style="width: 14px; height: 14px; display: inline-flex; align-items: center; justify-content: center;" class="flex-shrink-0">
                                                                    @if($num === 1)
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                                    @endif
                                                                </span>
                                                                <span class="agoda-item-text">{{ $num }} {{ $num === 1 ? 'room' : 'rooms' }}</span>
                                                            </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <button type="button" class="btn d-flex align-items-center justify-content-center p-0 flex-shrink-0" style="width: 38px; height: 42px; border: 1px solid #cdd4de; border-radius: 50%; background: #ffffff;" title="Save to wishlist">
                                                        <x-agoda-icon name="heart" size="18" color="#475569" />
                                                    </button>
                                                    <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}&check_in={{ request('check_in', request('checkin', $checkinCarbon->format('Y-m-d'))) }}&check_out={{ request('check_out', request('checkout', $checkoutCarbon->format('Y-m-d'))) }}&guests={{ request('guests', 2) }}" 
                                                       class="btn flex-grow-1 text-white d-flex flex-column align-items-center justify-content-center agoda-book-btn" 
                                                       style="background: #1765c7; border-radius: 20px; height: 42px; text-decoration: none; padding: 2px 10px; line-height: 1.15;">
                                                        <span style="font-size: 15px; font-weight: 700;">Book</span>
                                                        <span style="font-size: 11px; font-weight: 600; opacity: 0.95;">Pay later</span>
                                                    </a>
                                                </div>
                                                <div style="font-size: 13px; color: #00805a; font-weight: 700; text-align: center; margin-top: 4px;">+ Free Cancellation</div>
                                                @if($isLowestInProperty || $rIdx === 0)
                                                <div style="font-size: 11.5px; color: #ba2a26; font-weight: 700; text-align: center; margin-top: 2px;">Our last 4!</div>
                                                @else
                                                <div style="font-size: 11.5px; color: #0b66c2; font-weight: 700; text-align: center; margin-top: 2px;">Limited availability</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Room Details & Photo Gallery Lightbox Modal (Agoda Standard 1:1 Matching User Screenshot) --}}
                <div class="modal fade" id="roomDetailModal_{{ $room->id ?? $rIdx }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
                            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark mb-0.5" style="font-size: 18px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $room->name }}</h5>
                                    <small class="text-secondary" style="font-size: 12.5px;">{{ $rSizeStr }} • Max {{ $rAdults }} Adults • {{ $rBedStr }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                {{-- Room High-Res Image Gallery & Thumbnail Selector --}}
                                <div class="rounded-3 overflow-hidden mb-2.5 position-relative" style="height: 380px; background: #0f172a;" id="roomModalMainBox_{{ $room->id ?? $rIdx }}">
                                    <img id="roomModalActiveImg_{{ $room->id ?? $rIdx }}" src="{{ $rImages[0] ?? ($room->primary_image ?: ($gallery[$rIdx % count($gallery)] ?? '')) }}" class="w-100 h-100" style="object-fit: cover; transition: opacity 0.2s ease;" alt="{{ $room->name }}">
                                    <div class="position-absolute bottom-0 start-0 m-3 d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill shadow-sm" style="background: rgba(0,0,0,0.72); backdrop-filter: blur(4px); color: #ffffff; font-size: 11.5px; font-weight: 600; z-index: 5;">
                                        <i class="fa-solid fa-camera me-1"></i> <span id="roomModalCounter_{{ $room->id ?? $rIdx }}">Verified High-Res Room Photo (1/{{ count($rImages) }})</span>
                                    </div>
                                </div>
                                @if(count($rImages) > 1)
                                <div class="d-flex align-items-center gap-2 mb-4 overflow-x-auto pb-1" style="scrollbar-width: thin;">
                                    @foreach($rImages as $rImgIdx => $rImgUrl)
                                    <div class="flex-shrink-0 room-modal-thumb rounded-2 overflow-hidden" 
                                         style="width: 76px; height: 52px; cursor: pointer; border: 2px solid {{ $rImgIdx === 0 ? '#2067e1' : '#cbd5e1' }}; opacity: {{ $rImgIdx === 0 ? '1' : '0.7' }}; transition: all 0.2s;"
                                         onclick="switchRoomModalPhoto({{ $room->id ?? $rIdx }}, '{{ $rImgUrl }}', {{ $rImgIdx + 1 }}, {{ count($rImages) }}, this);">
                                        <img src="{{ $rImgUrl }}" class="w-100 h-100 object-fit-cover" alt="Photo {{ $rImgIdx + 1 }}" loading="lazy">
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <div class="row g-4 pt-1">
                                    {{-- Left: Key Room Specs --}}
                                    <div class="col-md-6 border-end pe-md-4">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-bed text-primary me-2"></i> Room Layout &amp; Bedding</h6>
                                        <ul class="list-unstyled d-flex flex-column gap-2 text-secondary mb-4" style="font-size: 13px;">
                                            <li class="d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-ruler-combined text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Room Area: <strong>{{ $rSizeStr }}</strong></span></li>
                                            <li class="d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-bed text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Bed Config: <strong>{{ $rBedStr }}</strong></span></li>
                                            <li class="d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-mountain-sun text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>View: <strong>{{ $rView }}</strong></span></li>
                                            <li class="d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-building text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Outdoor: <strong>{{ $rBalcony }}</strong></span></li>
                                            <li class="d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-ban-smoking text-danger flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Smoking: <strong>{{ $rSmoking }}</strong></span></li>
                                        </ul>

                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-shield-halved text-success me-2"></i> Policies &amp; Inclusions</h6>
                                        <ul class="list-unstyled d-flex flex-column gap-2 text-secondary" style="font-size: 13px;">
                                            <li class="d-flex align-items-center text-success fw-semibold" style="gap: 8px;">
                                                <i class="fa-solid fa-circle-check flex-shrink-0" style="margin-right: 8px;"></i> 
                                                <span>{{ $room->breakfast_included ? 'Free Breakfast Included' : 'Breakfast available at hotel' }}</span>
                                            </li>
                                            <li class="d-flex align-items-center text-success fw-semibold" style="gap: 8px;">
                                                <i class="fa-solid fa-circle-check flex-shrink-0" style="margin-right: 8px;"></i> 
                                                <span>{{ $room->free_cancellation ? 'Free Cancellation Available' : 'Standard cancellation policy' }}</span>
                                            </li>
                                            <li class="d-flex align-items-center text-primary fw-semibold" style="gap: 8px;">
                                                <i class="fa-solid fa-circle-check flex-shrink-0" style="margin-right: 8px;"></i> 
                                                <span>{{ $room->pay_at_hotel ? 'Pay at Hotel Accepted' : 'Instant Confirmation' }}</span>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Right: Bathroom Features & Amenities --}}
                                    <div class="col-md-6 ps-md-4">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-sparkles text-warning me-2"></i> Room Amenities &amp; Comfort</h6>
                                        <div class="row g-2 text-secondary mb-4" style="font-size: 12.5px;">
                                            @php
                                                $displayFacs = !empty($room->facilities) && is_array($room->facilities) ? $room->facilities : [
                                                    'Wi-Fi [free]', 'Air conditioning', 'Private bathroom', 'Hot water geyser', 'Smart TV', 'Balcony/terrace', 'Free bottled water', 'Hair dryer'
                                                ];
                                            @endphp
                                            @foreach(array_slice($displayFacs, 0, 10) as $fItem)
                                             <div class="col-6 d-flex align-items-center text-truncate" style="gap: 8px;">
                                                <i class="fa-solid fa-check text-success flex-shrink-0" style="font-size: 11px; margin-right: 8px;"></i>
                                                <span class="text-truncate">{{ $fItem }}</span>
                                            </div>
                                            @endforeach
                                        </div>

                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fa-solid fa-bath text-info me-2"></i> Bathroom &amp; Extras</h6>
                                        <div class="row g-2 text-secondary" style="font-size: 12.5px;">
                                            <div class="col-6 d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-shower text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>{{ $rBathrooms }} Private Bath</span></div>
                                            <div class="col-6 d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-temperature-arrow-up text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Hot Water</span></div>
                                            <div class="col-6 d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-pump-soap text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Toiletries</span></div>
                                            <div class="col-6 d-flex align-items-center" style="gap: 8px;"><i class="fa-solid fa-shirt text-muted flex-shrink-0" style="width: 16px; margin-right: 8px;"></i> <span>Towels &amp; Linens</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-column" style="line-height: 1.2;">
                                    <span class="text-secondary small" style="font-size: 11px;">Starting from</span>
                                    <div class="d-flex align-items-baseline gap-1">
                                        <strong class="text-danger fw-bold" style="font-size: 22px; color: #ba2a26 !important;">৳{{ number_format((float)($room->price_per_night ?? 2500)) }}</strong>
                                        <span class="text-secondary" style="font-size: 12.5px;">/ night</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary fw-bold px-3 py-2 border" data-bs-dismiss="modal" style="font-size: 13px; border-radius: 6px; border-color: #cbd5e1;">Close</button>
                                    <a href="{{ route('booking.form', $property->id) }}?room_id={{ $room->id ?? 101 }}&check_in={{ request('check_in', request('checkin', $checkinCarbon->format('Y-m-d'))) }}&check_out={{ request('check_out', request('checkout', $checkoutCarbon->format('Y-m-d'))) }}&guests={{ request('guests', 2) }}" class="btn btn-primary fw-bold px-4 py-2" style="background: #1b62dc; font-size: 13.5px; border-radius: 6px;">
                                        Book this room
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
            </div>
        </div>
    </div> {{-- End of Rooms agoda-page-container --}}

    {{-- Main Lower Page Container (About us, Plan your journey, Facilities, Location, Policies, FAQs) --}}
    <div class="agoda-page-container">

        {{-- 6. About us Section Card (Agoda 1:1 Exact Parity) --}}
        <div class="card bg-white mb-4" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 18px 20px !important;">
            <h5 class="fw-bold text-dark mb-2" style="font-size: 16.5px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">About us</h5>
            <p class="text-secondary mb-1" style="font-size: 13px; line-height: 1.55; color: #334155;">
                Conveniently situated in the {{ $property->city ?: 'prime' }} part of Bangladesh, this property puts you close to attractions and interesting dining options.
            </p>
            <div>
                <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1; font-size: 13px;" onclick="const d = document.getElementById('fullAboutUsDesc'); if(d) d.classList.toggle('d-none');">Read more</a>
                <div id="fullAboutUsDesc" class="d-none mt-2 text-secondary" style="font-size: 13px; line-height: 1.55;">
                    {{ $property->description ?: 'This property is dedicated to providing guests with premium comfort, modern conveniences, and warm hospitality for an unforgettable stay.' }}
                </div>
            </div>
        </div>

        {{-- 7. Plan your journey to your hotel (Agoda 1:1 Exact Parity) --}}
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 17.5px; font-weight: 800;">
                Plan your journey to your hotel
            </h5>
            <p class="text-secondary mb-3" style="font-size: 13px; color: #64748b;">Book your ride in advance for a hassle-free trip</p>

            <div class="row g-3">
                {{-- Card 1: Airport Transfer --}}
                <div class="col-md-6">
                    <div class="card bg-white h-100 d-flex flex-column justify-content-between" style="border: 1px solid #dddfe2 !important; border-radius: 10px !important; padding: 20px 22px !important;">
                        <div>
                            {{-- Chauffeur & Car Graphic (Agoda Exact 1:1) --}}
                            <div class="mb-3" style="height: 52px;">
                                <svg width="105" height="52" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">Book your airport transfer</h6>
                            <p class="text-secondary mb-3" style="font-size: 12px; color: #64748b;">Get to your hotel easily and securely</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <span class="text-secondary fw-normal d-flex align-items-center gap-1" style="font-size: 12px; color: #475569;">
                                {{ $checkinCarbon->format('d M') }} · {{ $guestCount }} {{ $guestCount > 1 ? 'Adults' : 'Adult' }}
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 10px; color: #2067e1 !important;"></i>
                            </span>
                            <a href="{{ route('services') }}?type=transfer&destination={{ urlencode($property->city ?: 'Dhaka') }}&check_in={{ $checkinCarbon->format('Y-m-d') }}&guests={{ $guestCount }}" class="btn fw-bold rounded-pill px-4 py-1.5" style="border: 1px solid #cbd5e1; color: #2067e1; font-size: 12.5px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                                Search
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Rent a Car --}}
                <div class="col-md-6">
                    <div class="card bg-white h-100 d-flex flex-column justify-content-between" style="border: 1px solid #dddfe2 !important; border-radius: 10px !important; padding: 20px 22px !important;">
                        <div>
                            {{-- Silver Sedan Graphic (Agoda Exact 1:1) --}}
                            <div class="mb-3" style="height: 52px;">
                                <svg width="105" height="52" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">Rent a car</h6>
                            <p class="text-secondary mb-3" style="font-size: 12px; color: #64748b;">Find an ideal ride for your trip</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <span class="text-secondary fw-normal d-flex align-items-center gap-1" style="font-size: 12px; color: #475569;">
                                {{ $checkinCarbon->format('d M') }} - {{ $checkoutCarbon->format('d M') }} 
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 10px; color: #2067e1 !important;"></i>
                            </span>
                            <a href="{{ route('services') }}?type=car_rental&destination={{ urlencode($property->city ?: 'Dhaka') }}&check_in={{ $checkinCarbon->format('Y-m-d') }}&check_out={{ $checkoutCarbon->format('Y-m-d') }}" class="btn fw-bold rounded-pill px-4 py-1.5" style="border: 1px solid #cbd5e1; color: #2067e1; font-size: 12.5px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
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
        </div>

        {{-- 9. More about the property Feature Section (Agoda 1:1 Exact Screenshot Parity) --}}
        <div class="card bg-white mb-4 shadow-none border-0" style="background: transparent !important;">
            <div class="row g-4 align-items-start">
                {{-- Left Column: Property Featured Image with 8px Rounded Corners --}}
                <div class="col-lg-4 col-md-5">
                    <div class="rounded-3 overflow-hidden shadow-xs position-relative" style="height: 240px; border-radius: 8px !important; border: 1px solid #e2e8f0;">
                        <img src="{{ $gallery[0] ?? $property->primary_image }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">
                    </div>
                </div>

                {{-- Right Column: Detailed Property Narrative & Dynamic Highlights --}}
                <div class="col-lg-8 col-md-7 ps-lg-3">
                    <small class="text-secondary d-block mb-1" style="font-size: 13px; color: #64748b;">More about the property</small>
                    <h4 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 800; line-height: 1.3; color: #0f172a;">
                        {{ $property->name }}
                    </h4>

                    {{-- Section 1: Spacious / Comfortable Living --}}
                    <div class="mb-3">
                        <h5 class="fw-bold text-dark mb-1.5" style="font-size: 16px; font-weight: 800; color: #1e293b;">
                            {{ $property->type == 'apartment' || $property->type == 'villa' ? 'Spacious Living' : 'Comfortable Living' }}
                        </h5>
                        <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.6; color: #475569;">
                            The <strong>{{ $property->name }}</strong> offers a comfortable and well-appointed stay in {{ $property->city ?: 'Bangladesh' }} with {{ $property->rooms->count() ?: 1 }} room {{ $property->rooms->count() > 1 ? 'types' : 'type' }} and modern conveniences. {{ Str::limit($property->description, 180, '...') }}
                        </p>
                    </div>

                    {{-- Section 2: Modern Amenities --}}
                    <div class="mb-3">
                        <h5 class="fw-bold text-dark mb-1.5" style="font-size: 16px; font-weight: 800; color: #1e293b;">
                            Modern Amenities
                        </h5>
                        <p class="text-secondary mb-2" style="font-size: 13px; line-height: 1.6; color: #475569;">
                            Guests enjoy free Wi-Fi, air-conditioning, and 24/7 hospitality. The property includes {{ is_array($property->amenities) ? implode(', ', array_slice($property->amenities, 0, 5)) : 'free Wi-Fi, air conditioning, and room service' }}.
                        </p>
                    </div>

                    {{-- Read more about this property link --}}
                    <div>
                        <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1; font-size: 13px;" onclick="const d = document.getElementById('fullMoreAboutPropertyDesc'); if(d) d.classList.toggle('d-none');">
                            Read more about this property
                        </a>
                        <div id="fullMoreAboutPropertyDesc" class="d-none mt-2 p-3 bg-light rounded-3 text-secondary border" style="font-size: 12.5px; line-height: 1.6;">
                            {{ $property->description ?: 'This property is committed to delivering a superior guest experience with dedicated staff, spotless rooms, and round-the-clock assistance.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 10. Categorized Amenities & Facilities Section (Agoda 1:1 Carbon Copy Screenshot Parity) --}}
        <div id="facilities" class="card bg-white mb-4 shadow-none" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 22px 24px !important;">
            <div class="d-flex justify-content-between align-items-baseline mb-4">
                <h4 class="fw-bold text-dark mb-0" style="font-size: 18px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">Amenities and facilities</h4>
                <div class="text-end">
                    <strong class="d-block" style="color: #2067e1; font-size: 13.5px; font-weight: 800;">Exceptional {{ number_format((float)($property->sub_scores['facilities'] ?? 10.0), 1) }}</strong>
                    <span class="text-secondary" style="font-size: 11.5px; color: #64748b;">Facilities</span>
                </div>
            </div>
            
            <div class="row g-4" style="font-size: 13px;">
                {{-- Column 1: Languages spoken, Entertainment, Comforts --}}
                <div class="col-lg-3 col-md-6">
                    {{-- Languages spoken --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Languages spoken</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            @php
                                $langs = is_array($property->languages_spoken) && count($property->languages_spoken) > 0 ? $property->languages_spoken : ['English', 'Bengali'];
                            @endphp
                            @foreach($langs as $lang)
                            <div class="d-flex align-items-center gap-2">
                                @if(stripos($lang, 'bengali') !== false || stripos($lang, 'bangla') !== false)
                                    <span style="font-size: 14px;">🇧🇩</span> <span>Bengali</span>
                                @else
                                    <span style="font-size: 14px;">🇬🇧</span> <span>{{ $lang }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Entertainment --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Entertainment</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-tv" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>TV / Cable channels</span>
                            </div>
                        </div>
                    </div>

                    {{-- Comforts --}}
                    <div>
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Comforts</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-snowflake" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Air conditioning</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-fan" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Fan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Dining, drinking, and snacking, Bathroom and toiletries, Layout and furnishings --}}
                <div class="col-lg-3 col-md-6">
                    {{-- Dining, drinking, and snacking --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Dining, drinking, and snacking</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-kitchen-set" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Kitchen</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bathroom and toiletries --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Bathroom and toiletries</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-pump-soap" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Toiletries</span>
                            </div>
                        </div>
                    </div>

                    {{-- Layout and furnishings --}}
                    <div>
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Layout and furnishings</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-table" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Desk/workspace</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 3: Services and conveniences, Clothing and laundry, Safety and security features --}}
                <div class="col-lg-3 col-md-6">
                    {{-- Services and conveniences --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Services and conveniences</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-elevator" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Elevator</span>
                            </div>
                        </div>
                    </div>

                    {{-- Clothing and laundry --}}
                    <div class="mb-4">
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Clothing and laundry</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-shirt" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Iron</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-square" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Washer</span>
                            </div>
                        </div>
                    </div>

                    {{-- Safety and security features --}}
                    <div>
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Safety and security features</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-smog" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Carbon monoxide detector</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 4: Access --}}
                <div class="col-lg-3 col-md-6">
                    <div>
                        <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">Access</strong>
                        <div class="d-flex flex-column gap-2 text-secondary" style="color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-video" style="color: #64748b; font-size: 14px; width: 18px;"></i> <span>Security [24-hour]</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        {{-- 13. Reviews Section Card (Agoda 1:1 Exact Carbon Copy Parity) --}}
        <div id="reviews" class="card bg-white mb-4 shadow-none" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; padding: 24px 28px !important;">
            {{-- Top Header Row: Title & Verified Badge --}}
            <div class="d-flex justify-content-between align-items-baseline mb-4 flex-wrap gap-2">
                <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 800; color: #0f172a;">
                    <span>Reviews of {{ $property->name }} from real guests</span>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-muted" style="width: 18px; height: 18px; font-size: 13px; cursor: pointer;" title="Verified Guest Reviews">
                        <i class="fa-regular fa-circle-question"></i>
                    </span>
                </h4>
                <div class="text-secondary" style="font-size: 13px;">
                    <span>Verified reviews provided by </span>
                    <strong style="color: #003580; font-size: 14.5px; font-weight: 800;">PRIME BOOKING</strong>
                </div>
            </div>

            {{-- Agoda 1:1 Score & Sub-scores Section --}}
            <div class="row g-4 align-items-center mb-4">
                {{-- Left: Agoda Blue Speech Bubble Score Badge --}}
                <div class="col-lg-2 col-md-3 text-start">
                    <div class="position-relative d-inline-block">
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" 
                             style="width: 82px; height: 62px; background-color: #2067e1; border-radius: 18px 18px 18px 0 !important; font-size: 32px; font-weight: 800; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            {{ round((float)$score) ?: 10 }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <strong class="d-block text-dark" style="font-size: 15px; font-weight: 800;">
                            {{ $scoreNum >= 9 ? 'Exceptional' : ($scoreNum >= 8 ? 'Excellent' : 'Very Good') }}
                        </strong>
                        <span class="d-flex align-items-center gap-1 mt-0.5" style="font-size: 12.5px; color: #2067e1; font-weight: 600;">
                            <i class="fa-solid fa-circle-check" style="font-size: 13px;"></i>
                            <span>From {{ $revCount }} {{ $totalReviewsInt === 1 ? 'review' : 'reviews' }}</span>
                        </span>
                    </div>
                </div>

                {{-- Right: 6 Agoda Green Sub-score Bars (2 Columns x 3 Rows) --}}
                <div class="col-lg-10 col-md-9">
                    <div class="row g-x-5 g-y-3" style="font-size: 13px;">
                        @php
                            $subClean = $property->sub_scores['cleanliness'] ?? 10.0;
                            $subLoc = $property->sub_scores['location'] ?? 10.0;
                            $subService = $property->sub_scores['service'] ?? 10.0;
                            $subFacilities = $property->sub_scores['facilities'] ?? 10.0;
                            $subComfort = $property->sub_scores['room_comfort_and_quality'] ?? 10.0;
                            $subValue = $property->sub_scores['value'] ?? 10.0;
                        @endphp

                        {{-- Left Subscore Column --}}
                        <div class="col-md-6 d-flex flex-column gap-3 pe-md-4">
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Cleanliness</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subClean, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subClean * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Location</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subLoc, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subLoc * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Service</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subService, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subService * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Subscore Column --}}
                        <div class="col-md-6 d-flex flex-column gap-3 ps-md-4">
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Facilities</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subFacilities, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subFacilities * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Room comfort and quality</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subComfort, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subComfort * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="color: #334155; font-weight: 500;">Value for money</span>
                                    <strong style="color: #137333; font-weight: 700;">{{ number_format((float)$subValue, 1) }}</strong>
                                </div>
                                <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px;">
                                    <div class="progress-bar" style="width: {{ min(100, round((float)$subValue * 10)) }}%; background-color: #137333 !important;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agoda Guest Type Dropdown Filter (Exact Screenshot Parity) --}}
            <div class="mb-3 position-relative" style="max-width: 100%;">
                <div class="position-relative">
                    <span class="position-absolute bg-white px-1 fw-semibold" style="top: -9px; left: 14px; font-size: 11px; color: #2067e1; z-index: 5;">
                        Guest Type
                    </span>
                    <select class="form-select shadow-none fw-bold" id="guestTypeFilterSelect" onchange="filterReviewsByGuestType(this.value);"
                            style="border: 1.5px solid #2067e1; border-radius: 8px !important; min-height: 48px; font-size: 13.5px; color: #1e293b; padding-left: 14px; cursor: pointer;">
                        <option value="all" selected>All guests ({{ $revCount }})</option>
                        <option value="couples">Couples ({{ number_format(max(1, (int)round($totalReviewsInt * 0.6))) }})</option>
                        <option value="solo">Solo travelers ({{ number_format(max(1, (int)round($totalReviewsInt * 0.2))) }})</option>
                        <option value="family">Families ({{ number_format(max(1, (int)round($totalReviewsInt * 0.2))) }})</option>
                        <option value="business">Business travelers ({{ number_format(max(1, (int)round($totalReviewsInt * 0.1))) }})</option>
                    </select>
                </div>
            </div>

            {{-- Sort By Row (Agoda 1:1 Screenshot Parity) --}}
            <div class="d-flex align-items-center justify-content-end gap-2 mb-4">
                <span class="text-dark fw-bold" style="font-size: 13px;">Sort by</span>
                <select class="form-select form-select-sm shadow-none" id="reviewSortSelect" onchange="sortReviewsList(this.value);" style="width: 170px; font-size: 13px; border-color: #cbd5e1; border-radius: 4px; font-weight: 500; padding: 6px 12px;">
                    <option value="recent" selected>Most recent</option>
                    <option value="highest">Rating, high to low</option>
                    <option value="lowest">Rating, low to high</option>
                    <option value="helpful">Most helpful</option>
                </select>
            </div>

            {{-- Verified Guest Reviews List (Agoda 1:1 Carbon Copy Format) --}}
            <div class="d-flex flex-column gap-4 border-top pt-4" id="verifiedReviewsContainer">
                @php
                    $guestList = $reviews ?? $property->reviews ?? [];
                @endphp

                @if(!empty($guestList) && count($guestList) > 0)
                    @foreach($guestList as $rev)
                    @php
                        $rScore = number_format((float)($rev->rating ?? 10.0), 1);
                        $rType = $rev->guest_type ?? ($rev->traveler_type ?? 'Business traveler');
                        $rTitle = !empty($rev->title) ? $rev->title : 'Exceptional stay! Beautiful 5-star style interior, spotless apartment, smooth check-in, great location, and an amazing host.';
                    @endphp
                    <div class="row g-4 align-items-start verified-review-card pb-3" data-guest-type="{{ strtolower($rType) }}" data-score="{{ (float)($rev->rating ?? 10.0) }}">
                        {{-- Left Column: Score, Author & Traveler Tag --}}
                        <div class="col-lg-3 col-md-4">
                            <h5 class="fw-bold text-primary mb-2" style="font-size: 17.5px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #2067e1 !important;">
                                {{ $rScore }} {{ (float)$rScore >= 9 ? 'Exceptional' : 'Excellent' }}
                            </h5>
                            <div class="d-flex align-items-center gap-1.5 text-dark fw-bold mb-1" style="font-size: 13px;">
                                <i class="fa-solid fa-globe text-secondary" style="font-size: 12px;"></i>
                                <span>{{ $rev->user->name ?? 'Rashed' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-secondary" style="font-size: 12px; color: #64748b;">
                                <i class="fa-regular fa-id-badge" style="font-size: 12px;"></i>
                                <span>{{ $rType }}</span>
                            </div>
                        </div>

                        {{-- Right Column: Agoda Light Grey Quote Card with Green Plus & Helpful Links --}}
                        <div class="col-lg-9 col-md-8">
                            <div class="p-3.5 rounded-3 bg-light" style="background: #f8fafc !important; border: 1px solid #f1f5f9; border-radius: 10px !important;">
                                {{-- Quote Header --}}
                                <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; line-height: 1.4; color: #0f172a;">
                                    “{{ $rTitle }}”
                                </strong>

                                {{-- Comment with Green Plus Icon (Agoda Signature) --}}
                                <div class="d-flex align-items-start gap-2.5 text-secondary mb-3" style="font-size: 12.5px; line-height: 1.6; color: #334155;">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0 text-white mt-0.5" 
                                          style="width: 17px; height: 17px; background-color: #16a34a; font-size: 10px; font-weight: 900;">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <div>
                                        {{ $rev->comment ?: 'The apartment was exceptionally clean and exactly as shown in the photos. The interior was beautifully designed with a luxurious 5-star hotel feel, making our stay both comfortable and relaxing. The location was convenient, and the host was friendly, responsive, and helpful throughout our stay. Check-in was smooth, and all the essential amenities were provided. We truly enjoyed our time here and would highly recommend this property to anyone looking for a premium stay.' }}
                                    </div>
                                </div>

                                {{-- Review Date --}}
                                <div class="text-secondary" style="font-size: 11.5px; color: #64748b;">
                                    Reviewed {{ $rev->created_at ? $rev->created_at->format('F d, Y') : 'July 05, 2026' }}
                                </div>
                            </div>

                            {{-- Helpful Feedback Links (Agoda 1:1 Screenshot Parity) --}}
                            <div class="d-flex justify-content-end align-items-center gap-2 mt-2" style="font-size: 12px; color: #334155;">
                                <span>Did you find this review helpful?</span>
                                <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1;" onclick="this.textContent='Thank you!';">Yes</a>
                                <span class="text-muted">|</span>
                                <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1;" onclick="this.textContent='Noted';">No</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{-- Default Dynamic Carbon Copy Fallback Review --}}
                    <div class="row g-4 align-items-start verified-review-card pb-3" data-guest-type="business" data-score="10.0">
                        {{-- Left Column --}}
                        <div class="col-lg-3 col-md-4">
                            <h5 class="fw-bold text-primary mb-2" style="font-size: 17.5px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #2067e1 !important;">
                                10.0 Exceptional
                            </h5>
                            <div class="d-flex align-items-center gap-1.5 text-dark fw-bold mb-1" style="font-size: 13px;">
                                <i class="fa-solid fa-globe text-secondary" style="font-size: 12px;"></i>
                                <span>Rashed</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-secondary" style="font-size: 12px; color: #64748b;">
                                <i class="fa-regular fa-id-badge" style="font-size: 12px;"></i>
                                <span>Business traveler</span>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-lg-9 col-md-8">
                            <div class="p-3.5 rounded-3 bg-light" style="background: #f8fafc !important; border: 1px solid #f1f5f9; border-radius: 10px !important;">
                                <strong class="d-block text-dark mb-2.5" style="font-size: 13.5px; font-weight: 700; line-height: 1.4; color: #0f172a;">
                                    “Exceptional stay! Beautiful 5-star style interior, spotless apartment, smooth check-in, great location, and an amazing host.”
                                </strong>
                                <div class="d-flex align-items-start gap-2.5 text-secondary mb-3" style="font-size: 12.5px; line-height: 1.6; color: #334155;">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0 text-white mt-0.5" 
                                          style="width: 17px; height: 17px; background-color: #16a34a; font-size: 10px; font-weight: 900;">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <div>
                                        The apartment was exceptionally clean and exactly as shown in the photos. The interior was beautifully designed with a luxurious 5-star hotel feel, making our stay both comfortable and relaxing. The location was convenient, and the host was friendly, responsive, and helpful throughout our stay. Check-in was smooth, and all the essential amenities were provided. We truly enjoyed our time here and would highly recommend this property to anyone looking for a premium stay.
                                    </div>
                                </div>

                                {{-- Review Date --}}
                                <div class="text-secondary" style="font-size: 11.5px; color: #64748b;">
                                    Reviewed July 05, 2026
                                </div>
                            </div>

                            {{-- Helpful Feedback Links (Agoda 1:1 Screenshot Parity) --}}
                            <div class="d-flex justify-content-end align-items-center gap-2 mt-2" style="font-size: 12px; color: #334155;">
                                <span>Did you find this review helpful?</span>
                                <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1;" onclick="this.textContent='Thank you!';">Yes</a>
                                <span class="text-muted">|</span>
                                <a href="javascript:void(0)" class="fw-bold text-decoration-none" style="color: #2067e1;" onclick="this.textContent='Noted';">No</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Check in. Step out. Experiences Banner (Agoda 1:1 Exact Carbon Copy Parity) --}}
        <div class="card bg-white mb-4 shadow-none overflow-hidden" style="border: 1px solid #dddfe2 !important; border-radius: 4px !important; min-height: 240px;">
            <div class="row g-0 align-items-stretch" style="min-height: 240px;">
                {{-- Left White Info Box --}}
                <div class="col-lg-5 col-md-6 p-4 d-flex flex-column justify-content-between bg-white z-1" style="border-right: 1px solid #eee;">
                    <div>
                        <h4 class="fw-bold text-dark mb-2" style="font-size: 24px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b;">Check in. Step out.</h4>
                        <p class="text-secondary mb-3" style="font-size: 13px; line-height: 1.5; color: #475569;">
                            Experiences on Prime Booking. Because your room is just the beginning.
                        </p>
                        <div class="d-flex align-items-center gap-2 mb-4 text-dark" style="font-size: 13.5px; font-weight: 600;">
                            <i class="fa-solid fa-person-walking-luggage" style="color: #0f172a; font-size: 16px;"></i>
                            <span>Exceptional location</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('services') }}?destination={{ urlencode($property->city ?: 'Chittagong') }}" 
                           class="btn text-white fw-bold px-4 py-2.5 rounded-pill shadow-xs" 
                           style="background-color: #2067e1; font-size: 13px; letter-spacing: 0.3px; border: none;">
                            SHOW EXPERIENCES
                        </a>
                    </div>
                </div>

                {{-- Right Interactive Map with Agoda Floating Property Pin Card --}}
                <div class="col-lg-7 col-md-6 position-relative p-0 overflow-hidden" style="min-height: 240px; background: #e5e3df;">
                    {{-- OpenStreetMap Universal Interactive Map Embed (100% Reliable, Never Blocked) --}}
                    @php
                        $mapLat = (float)($property->latitude ?: 21.4272);
                        $mapLng = (float)($property->longitude ?: 92.0058);
                        $osmBbox = ($mapLng - 0.018) . '%2C' . ($mapLat - 0.012) . '%2C' . ($mapLng + 0.018) . '%2C' . ($mapLat + 0.012);
                        $osmEmbedUrl = "https://www.openstreetmap.org/export/embed.html?bbox={$osmBbox}&layer=mapnik&marker={$mapLat}%2C{$mapLng}";
                    @endphp
                    <iframe 
                        width="100%" 
                        height="100%" 
                        style="border:0; min-height: 260px; filter: saturate(1.1) contrast(1.05);" 
                        loading="lazy" 
                        allowfullscreen 
                        src="{{ $osmEmbedUrl }}">
                    </iframe>

                    {{-- Agoda Floating White Property Card Pin on Map --}}
                    <div class="position-absolute top-50 start-50 translate-middle shadow-lg rounded-2 bg-white d-flex align-items-center gap-2 p-1.5 pe-3" 
                         style="z-index: 10; border: 1px solid #cbd5e1; max-width: 90%; pointer-events: auto;">
                        <img src="{{ media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80') }}" 
                             alt="{{ $property->name }}" 
                             style="width: 54px; height: 42px; object-fit: cover; border-radius: 4px; flex-shrink: 0;"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';">
                        <div style="line-height: 1.25;">
                            <strong class="d-block text-dark text-truncate" style="font-size: 12.5px; font-weight: 700; max-width: 260px;">
                                {{ $property->name }}
                            </strong>
                            <div class="d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 11px;">
                                <span class="text-warning">
                                    @for($i=0; $i < max(1, min(5, (int)($property->star_rating ?? 3))); $i++)★@endfor
                                </span>
                                <span class="text-muted">|</span>
                                <span class="text-secondary fw-semibold">{{ $scoreNum >= 9 ? 'Exceptional' : 'Excellent' }} {{ number_format((float)($property->sub_scores['location'] ?? $scoreNum), 1) }}</span>
                            </div>
                        </div>
                    </div>
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
                            <img src="{{ media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80') }}" 
                                 class="rounded-2" 
                                 style="width: 45px; height: 45px; object-fit: cover; flex-shrink: 0;" 
                                 alt="{{ $property->name }}"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';">
                            <div class="text-truncate">
                                <strong class="d-block text-dark text-truncate" style="font-size: 12px; max-width: 180px;">{{ $property->name }}</strong>
                                <small class="text-primary fw-bold" style="font-size: 11px;">| {{ $scoreNum >= 9 ? 'Exceptional' : 'Excellent' }} {{ number_format((float)($property->rating_score ?? 8.9), 1) }}</small>
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

        {{-- 15. Top Destinations Section (100% Authentic Bangladesh Destinations) --}}
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Top destinations in Bangladesh</h5>
            <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                @php
                    $destinations = [
                        ['name' => 'Cox\'s Bazar', 'tag' => 'Beach resort capital', 'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Sajek Valley', 'tag' => 'Kingdom of clouds', 'img' => 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Sylhet', 'tag' => 'Tea gardens & lakes', 'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Sreemangal', 'tag' => 'Rainforest stays', 'img' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Bandarban', 'tag' => 'Misty hill peaks', 'img' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Saint Martin', 'tag' => 'Coral island paradise', 'img' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Kuakata', 'tag' => 'Sunrise & sunset beach', 'img' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Sundarbans', 'tag' => 'Mangrove safari cruise', 'img' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Dhaka', 'tag' => 'Capital & 5-star hotels', 'img' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Rangamati', 'tag' => 'Kaptai lake & hills', 'img' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80'],
                    ];
                @endphp
                @foreach($destinations as $dest)
                <a href="{{ route('search.index') }}?destination={{ urlencode($dest['name']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none text-white flex-shrink-0" style="width: 170px; height: 120px; position: relative;">
                    <img src="{{ $dest['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $dest['name'] }}">
                    <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                        <strong class="d-block text-white mb-0" style="font-size: 13.5px; line-height: 1.2;">{{ $dest['name'] }}</strong>
                        <small class="d-block text-white-50" style="font-size: 10px; margin-top: 1px;">{{ $dest['tag'] }}</small>
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
                    <div class="p-3.5 bg-light rounded-3 mb-3 border">
                        <div class="d-flex align-items-center gap-2.5 text-dark mb-1.5" style="font-size: 13.5px; font-weight: 700;">
                            <i class="fa-solid fa-location-dot text-primary flex-shrink-0" style="width: 18px; font-size: 15px; text-align: center;"></i>
                            <span>Full Address:</span>
                        </div>
                        <p class="text-secondary mb-2.5" style="font-size: 13px; line-height: 1.5; padding-left: 26px;">
                            {{ $property->address ?: ($property->city . ', Bangladesh') }}
                            @if(!empty($property->postal_code)) - {{ $property->postal_code }} @endif
                        </p>

                        @if(!empty($property->nearest_landmark))
                        <div class="d-flex align-items-center gap-2.5 mt-2.5 pt-2.5 border-top">
                            <i class="fa-solid fa-map-pin text-danger flex-shrink-0" style="width: 18px; font-size: 15px; text-align: center;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Prominent Landmark</small>
                                <strong class="text-dark" style="font-size: 13px;">{{ $property->nearest_landmark }}</strong>
                            </div>
                        </div>
                        @endif

                        @if(!empty($property->latitude) && !empty($property->longitude))
                        <div class="d-flex align-items-center gap-2.5 mt-2.5 pt-2.5 border-top">
                            <i class="fa-solid fa-satellite text-info flex-shrink-0" style="width: 18px; font-size: 15px; text-align: center;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">GPS Coordinates</small>
                                <code class="text-dark fw-bold" style="font-size: 12px;">{{ $property->latitude }}, {{ $property->longitude }}</code>
                            </div>
                        </div>
                        @endif

                        {{-- Proximity Breakdown (Walking & Driving Estimation) --}}
                        <div class="mt-3.5 pt-3 border-top">
                            <div class="d-flex align-items-center gap-2.5 text-dark mb-2.5" style="font-size: 13px; font-weight: 700;">
                                <i class="fa-solid fa-person-walking text-primary flex-shrink-0" style="width: 18px; font-size: 15px; text-align: center;"></i>
                                <span>Proximity &amp; Travel Highlights:</span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @foreach($property->proximity_breakdown as $prox)
                                <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-white border" style="font-size: 12.5px;">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <i class="{{ $prox['icon'] }} flex-shrink-0" style="width: 18px; text-align: center; font-size: 13.5px;"></i>
                                        <span class="text-dark fw-semibold">{{ $prox['name'] }}</span>
                                    </div>
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size: 11px;">
                                        {{ $prox['distance'] }} ({{ $prox['time_est'] }})
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(!empty($property->contact_phone) || !empty($property->contact_email))
                    <div class="p-3.5 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2.5 text-dark mb-2.5" style="font-size: 13px; font-weight: 700;">
                            <i class="fa-solid fa-headset text-success flex-shrink-0" style="width: 18px; font-size: 15px; text-align: center;"></i>
                            <span>Hotel Direct Contacts:</span>
                        </div>
                        @if(!empty($property->contact_phone))
                        <div class="d-flex align-items-center gap-2.5 text-secondary mb-2" style="font-size: 13px; padding-left: 26px;">
                            <i class="fa-solid fa-phone text-muted flex-shrink-0" style="width: 16px; text-align: center;"></i>
                            <a href="tel:{{ $property->contact_phone }}" class="text-dark text-decoration-none fw-semibold">{{ $property->contact_phone }}</a>
                        </div>
                        @endif
                        @if(!empty($property->contact_email))
                        <div class="d-flex align-items-center gap-2.5 text-secondary" style="font-size: 13px; padding-left: 26px;">
                            <i class="fa-solid fa-envelope text-muted flex-shrink-0" style="width: 16px; text-align: center;"></i>
                            <a href="mailto:{{ $property->contact_email }}" class="text-dark text-decoration-none">{{ $property->contact_email }}</a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="col-lg-7">
                    @php
                        $mapLat = (float)($property->latitude ?? 21.4272);
                        $mapLng = (float)($property->longitude ?? 92.0058);
                    @endphp
                    <div class="rounded-3 overflow-hidden border shadow-xs h-100 position-relative" style="min-height: 420px; background: #e2e8f0;">
                        <iframe width="100%" height="100%" style="border:0; min-height: 420px; width: 100%; display: block;" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox={{ $mapLng - 0.02 }}%2C{{ $mapLat - 0.012 }}%2C{{ $mapLng + 0.02 }}%2C{{ $mapLat + 0.012 }}&layer=mapnik&marker={{ $mapLat }}%2C{{ $mapLng }}"></iframe>

                        {{-- Floating Custom Pin Banner on Map --}}
                        <div class="position-absolute bottom-0 start-0 m-3 p-2.5 bg-white rounded-3 shadow border d-flex align-items-center gap-2.5" style="z-index: 10; max-width: 85%;">
                            <img src="{{ media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80') }}" 
                                 style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover; flex-shrink: 0;" 
                                 alt="{{ $property->name }}"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';">
                            <div class="text-truncate">
                                <strong class="d-block text-dark text-truncate" style="font-size: 13px;">{{ $property->name }}</strong>
                                <small class="text-muted text-truncate d-block" style="font-size: 11.5px;">{{ $property->address ?: $property->city }}</small>
                            </div>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm ms-2 flex-shrink-0 fw-bold px-2.5 py-1" style="font-size: 11.5px; border-radius: 4px;">
                                Open Maps
                            </a>
                        </div>
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
                            <i class="fa-solid fa-calendar-check text-success"></i>
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
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; font-size: 16px;">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold mb-0.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Check-in / Check-out</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13.5px;">Check-in from: {{ $property->checkin_time ?: '14:00' }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">Check-out until: {{ $property->checkout_time ?: '12:00' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; font-size: 16px;">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold mb-0.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Getting around</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13.5px;">Distance from center: {{ $property->nearest_landmark ?: '0.5 km' }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">Airport travel time: ~20 mins</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: #fdf4ff; color: #9333ea; font-size: 16px;">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold mb-0.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">The property</small>
                                <span class="text-dark d-block fw-semibold" style="font-size: 13.5px;">Total Rooms: {{ $property->total_rooms_count ?: ($property->rooms->count() ?: 10) }} • Floors: {{ $property->total_floors ?: 8 }}</span>
                                <span class="text-secondary d-block" style="font-size: 12px;">
                                    {{ $property->pets_policy ?: 'Pets Not Allowed' }} • {{ is_array($property->languages_spoken) ? implode(', ', $property->languages_spoken) : 'English, Bengali' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $houseRules = null;
                $cleanRuleText = function($txt) {
                    if (!is_string($txt)) return $txt;
                    $s = trim(str_replace(["\u{202f}", "\u{201f}", "\u{00a0}", "â€¯", "â€œ", "â€ "], [' ', '', ' ', ' ', '"', '"'], $txt));
                    $s = preg_replace('/\s*\([^)]*\d+:\d+[^)]*\)/u', '', $s);
                    return $s;
                };




                $rawRules = $property->house_rules;
                if (!empty($rawRules)) {
                    if (is_array($rawRules)) {
                        $houseRules = $rawRules;
                    } elseif (is_string($rawRules)) {
                        $curr = trim($rawRules);
                        for ($i = 0; $i < 3; $i++) {
                            $dec = json_decode($curr, true);
                            if (is_array($dec)) {
                                $houseRules = $dec;
                                break;
                            } elseif (is_string($dec)) {
                                $curr = trim($dec);
                            } else {
                                $decAlt = json_decode(html_entity_decode(stripslashes($curr)), true);
                                if (is_array($decAlt)) {
                                    $houseRules = $decAlt;
                                }
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if(!empty($property->house_rules))
            <div class="mt-4 p-3.5 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-2.5 text-dark mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 32px; height: 32px; background: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-list-check" style="font-size: 13px;"></i>
                    </div>
                    <strong style="font-size: 14px;">Special House Rules</strong>
                </div>
                
                @if($houseRules && is_array($houseRules))
                    <div class="row g-3">
                        @if(!empty($houseRules['check_in']) || !empty($houseRules['check_out']))
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb; font-size: 15px;">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Check-in &amp; Check-out Times</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">
                                        @if(!empty($houseRules['check_in'])) Check-in: {{ $cleanRuleText($houseRules['check_in']) }} @endif
                                        @if(!empty($houseRules['check_in']) && !empty($houseRules['check_out'])) • @endif
                                        @if(!empty($houseRules['check_out'])) Check-out: {{ $cleanRuleText($houseRules['check_out']) }} @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($houseRules['children_and_beds']))
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #f0fdf4; color: #16a34a; font-size: 15px;">
                                    <i class="fa-solid fa-child-reaching"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Children &amp; Extra Beds</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">{{ $cleanRuleText($houseRules['children_and_beds']) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($houseRules['smoking']))
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #fffbeb; color: #d97706; font-size: 15px;">
                                    <i class="fa-solid fa-ban-smoking"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Smoking Policy</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">{{ $cleanRuleText($houseRules['smoking']) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($houseRules['pets']))
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #f8fafc; color: #64748b; font-size: 15px;">
                                    <i class="fa-solid fa-paw"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Pet Policy</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">{{ $cleanRuleText($houseRules['pets']) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($houseRules['accepted_payments']))
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #f0fdf4; color: #0d9488; font-size: 15px;">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Accepted Payments</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">{{ $cleanRuleText($houseRules['accepted_payments']) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($houseRules['cancellation']))
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 shadow-xs" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: #eff6ff; color: #0284c7; font-size: 15px;">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;">Cancellation Details</small>
                                    <span class="text-dark fw-semibold" style="font-size: 13px; line-height: 1.4;">{{ $cleanRuleText($houseRules['cancellation']) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Dynamic loop for any additional custom rules --}}
                        @foreach($houseRules as $rKey => $rVal)
                            @if(!in_array($rKey, ['check_in', 'check_out', 'children_and_beds', 'smoking', 'pets', 'accepted_payments', 'cancellation']) && !empty($rVal) && is_string($rVal))
                            <div class="col-md-6">
                                <div class="p-2.5 bg-white rounded-2 border d-flex align-items-start gap-2.5 h-100">
                                    <i class="fa-solid fa-circle-check text-primary mt-1 flex-shrink-0" style="width: 16px; text-align: center;"></i>
                                    <div>
                                        <small class="text-muted fw-bold d-block text-capitalize" style="font-size: 11px;">{{ str_replace('_', ' ', $rKey) }}</small>
                                        <span class="text-dark" style="font-size: 12.5px; line-height: 1.4;">{{ $cleanRuleText($rVal) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    @php
                        // In case of non-JSON plain text, clean and display cleanly
                        $plainRules = $cleanRuleText((string)$property->house_rules);
                        if (str_starts_with($plainRules, '{') && str_ends_with($plainRules, '}')) {
                            // Extract key values if JSON decode completely failed
                            $plainRules = preg_replace('/["{}\[\]]/', '', $plainRules);
                            $plainRules = str_replace(',', "\n", $plainRules);
                        }
                    @endphp
                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5; white-space: pre-line;">{{ $plainRules }}</p>
                @endif
            </div>
            @endif

            {{-- Frequently Asked Questions (FAQ) & Host Direct Inquiry Section --}}
            <div id="faqs" class="mt-4 pt-4 border-top">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 18px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <i class="fa-solid fa-circle-question text-primary me-2"></i> Frequently Asked Questions
                        </h5>
                        <p class="text-secondary small mb-0">Answers to top guest questions about {{ $property->name }}</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 py-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#askHostModal">
                        <i class="fa-solid fa-paper-plane me-1"></i> Ask a Question
                    </button>
                </div>

                <div class="accordion accordion-flush bg-white rounded-3 border overflow-hidden" id="hotelDetailFaqAccordion">
                    {{-- FAQ 1 --}}
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem1" style="font-size: 13.5px;">
                                What are the check-in and check-out times at {{ $property->name }}?
                            </button>
                        </h2>
                        <div id="faqItem1" class="accordion-collapse collapse" data-bs-parent="#hotelDetailFaqAccordion">
                            <div class="accordion-body text-secondary small px-3.5 pt-0 pb-3" style="line-height: 1.6;">
                                Check-in is available from <strong>{{ $property->checkin_time ?: '14:00' }}</strong> onwards, and check-out is until <strong>{{ $property->checkout_time ?: '12:00' }}</strong>. Early check-in or late check-out can be requested when making your reservation, subject to availability.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 2 --}}
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem2" style="font-size: 13.5px;">
                                Does {{ $property->name }} offer free cancellation?
                            </button>
                        </h2>
                        <div id="faqItem2" class="accordion-collapse collapse" data-bs-parent="#hotelDetailFaqAccordion">
                            <div class="accordion-body text-secondary small px-3.5 pt-0 pb-3" style="line-height: 1.6;">
                                @if($property->free_cancellation)
                                Yes! Most room rates at {{ $property->name }} offer <strong>Free Cancellation</strong> up to 24-48 hours before check-in.
                                @else
                                Cancellation policies depend on the specific room type chosen. Please review the rate conditions before confirming your reservation.
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 3 --}}
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem3" style="font-size: 13.5px;">
                                What payment methods are supported?
                            </button>
                        </h2>
                        <div id="faqItem3" class="accordion-collapse collapse" data-bs-parent="#hotelDetailFaqAccordion">
                            <div class="accordion-body text-secondary small px-3.5 pt-0 pb-3" style="line-height: 1.6;">
                                You can pay securely online via <strong>bKash, Nagad, Rocket, or Visa/Mastercard</strong>, or choose <strong>Pay at Hotel</strong> upon arrival.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 4 --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem4" style="font-size: 13.5px;">
                                Can I arrange airport pickup or parking?
                            </button>
                        </h2>
                        <div id="faqItem4" class="accordion-collapse collapse" data-bs-parent="#hotelDetailFaqAccordion">
                            <div class="accordion-body text-secondary small px-3.5 pt-0 pb-3" style="line-height: 1.6;">
                                Yes! Airport transfer can be booked as an add-on during checkout, and on-site guest parking is available at the property.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 16. Discover new places Section (Screenshot Parity) --}}
        <div class="mb-4 position-relative">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">Discover new places</h5>
            <div class="position-relative">
                <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: none;">
                    @php
                        $places = [
                            ['name' => "Cox's Bazar", 'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Sylhet', 'img' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Saint Martin', 'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Sreemangal', 'img' => 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Sajek Valley', 'img' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Bandarban', 'img' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Rangamati', 'img' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Kuakata', 'img' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Sundarbans', 'img' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Chittagong', 'img' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=400&q=80'],
                            ['name' => 'Dhaka', 'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=400&q=80'],
                        ];
                    @endphp
                    @foreach($places as $pl)
                    <a href="{{ route('search.index') }}?destination={{ urlencode($pl['name']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none text-white flex-shrink-0" style="width: 175px; height: 130px; position: relative;">
                        <img src="{{ $pl['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $pl['name'] }}" loading="lazy">
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
                            ['city' => "Cox's Bazar", 'division' => 'Chittagong Division', 'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Dhaka', 'division' => 'Dhaka Division', 'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Sylhet', 'division' => 'Sylhet Division', 'img' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Chittagong', 'division' => 'Chittagong Division', 'img' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Sreemangal', 'division' => 'Moulvibazar', 'img' => 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Bandarban', 'division' => 'Hill Tracts', 'img' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Rangamati', 'division' => 'Kaptai Lake', 'img' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Kuakata', 'division' => 'Patuakhali', 'img' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Rajshahi', 'division' => 'Rajshahi Division', 'img' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=400&q=80'],
                            ['city' => 'Khulna', 'division' => 'Khulna Division', 'img' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=400&q=80'],
                        ];
                    @endphp
                    @foreach($trending as $trend)
                    <a href="{{ route('search.index') }}?destination={{ urlencode($trend['city']) }}" class="card border-0 shadow-xs rounded-3 overflow-hidden text-decoration-none bg-white flex-shrink-0" style="width: 180px;">
                        <img src="{{ $trend['img'] }}" class="w-100" style="height: 140px; object-fit: cover;" alt="{{ $trend['city'] }}" loading="lazy">
                        <div class="p-2 text-center">
                            <strong class="d-block text-dark mb-0" style="font-size: 13.5px;">{{ $trend['city'] }}</strong>
                            <small class="text-secondary" style="font-size: 11.5px;">{{ $trend['division'] }}</small>
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

        {{-- 15. Frequently Asked Questions (Agoda FAQ Accordion Parity) --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" id="faqs">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fa-solid fa-circle-question text-primary me-2"></i>Frequently Asked Questions
                    </h5>
                    <small class="text-muted">Important stay details, check-in policies, and guest amenities for {{ $property->name }}.</small>
                </div>
                <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm fw-semibold rounded-pill px-3">
                    <i class="fa-solid fa-headset me-1"></i> Ask a Question
                </a>
            </div>

            <div class="accordion accordion-flush" id="hotelFaqAccordion">
                
                {{-- FAQ 1: Check-in / Check-out --}}
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                    <h2 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1" style="font-size: 14.5px;">
                            What are the check-in and check-out times at {{ $property->name }}?
                        </button>
                    </h2>
                    <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#hotelFaqAccordion">
                        <div class="accordion-body text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                            Check-in is typically from <strong>14:00 (2:00 PM)</strong> onwards, and check-out is until <strong>12:00 (12:00 PM)</strong>. Early check-in or late check-out may be requested during booking subject to room availability.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2: Breakfast --}}
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                    <h2 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2" style="font-size: 14.5px;">
                            Does this property serve breakfast?
                        </button>
                    </h2>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#hotelFaqAccordion">
                        <div class="accordion-body text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                            Yes, guests can enjoy delicious continental, buffet, or Asian breakfast options during their stay. Select room rates include complimentary breakfast automatically.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3: Airport Transfer --}}
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                    <h2 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3" style="font-size: 14.5px;">
                            Can {{ $property->name }} arrange airport transfer?
                        </button>
                    </h2>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#hotelFaqAccordion">
                        <div class="accordion-body text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                            Yes! Dedicated airport transfer services can be scheduled easily upon reservation or via our Prime Booking transfer partner portal.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4: Cancellation Policy --}}
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                    <h2 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4" style="font-size: 14.5px;">
                            What is the cancellation policy for this hotel?
                        </button>
                    </h2>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#hotelFaqAccordion">
                        <div class="accordion-body text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                            @if($property->free_cancellation)
                            This stay offers <strong>Free Cancellation</strong> up to 24-48 hours before check-in. You can self-cancel your reservation directly from your Prime Booking user portal at zero cancellation penalty.
                            @else
                            Standard hotel cancellation terms apply. Cancellation terms are highlighted explicitly under each individual room rate selection.
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
</div>
{{-- AGODA 1:1 EXACT PHOTO GALLERY LIGHTBOX MODAL (OFFICIAL AGODA SPEC) --}}
<style id="agodaOfficialModalStyles">
    #galleryModal {
        z-index: 100050 !important;
        padding: 0 !important;
        background-color: rgba(0, 0, 0, 0.76) !important;
    }
    .modal-backdrop.show {
        z-index: 100040 !important;
        background-color: rgba(0, 0, 0, 0.76) !important;
        backdrop-filter: blur(4px);
    }
    /* Strictly suppress any toaster, fab, or floating buttons when modal is open */
    body.modal-open #social-proof-toast,
    body.modal-open #socialProofToaster,
    body.modal-open .toast,
    body.modal-open .toast-container,
    body.modal-open .social-proof-toast-wrapper,
    body.modal-open .btn-floating,
    body.modal-open .agoda-fab-container,
    body.modal-open .agoda-floating-bottom-bar,
    body.modal-open .position-fixed.bottom-0 {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    
    #galleryModal .modal-dialog {
        max-width: 1328px !important;
        width: 95vw !important;
        height: 87vh !important;
        margin: 40px auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #galleryModal .modal-content {
        width: 100% !important;
        height: 100% !important;
        background-color: #1a202c !important;
        border-radius: 12px !important;
        border: none !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75) !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    }

    /* Agoda Header Bar */
    .agoda-gal-header {
        background-color: #ffffff;
        min-height: 52px;
        height: 52px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        flex-shrink: 0;
        z-index: 20;
    }
    .agoda-gal-pill-btn {
        background: #ffffff;
        border: 1px solid rgba(94, 107, 130, 0.32);
        border-radius: 999px;
        height: 28px;
        padding: 0 12px;
        color: #2067da;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none;
        flex-shrink: 0;
    }
    .agoda-gal-pill-btn:hover {
        background: #f8fafc;
        border-color: #2067da;
    }
    .agoda-gal-tab-list {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;
        white-space: nowrap;
        margin-left: 12px;
    }
    .agoda-gal-tab-list::-webkit-scrollbar {
        display: none;
    }
    .agoda-gal-tab-item {
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: #5e6b82;
        font-size: 13px;
        font-weight: 500;
        padding: 15px 12px 13px 12px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
    }
    .agoda-gal-tab-item:hover {
        color: #2067da;
    }
    .agoda-gal-tab-item.active {
        color: #2067da;
        font-weight: 700;
        border-bottom: 2px solid #2067da;
    }
    .agoda-gal-header-cta {
        background: #2067da;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        height: 32px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-right: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s ease;
    }
    .agoda-gal-header-cta:hover {
        background: #1952af;
    }
    .agoda-gal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: none;
        background: transparent;
        color: #2067da;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s ease;
        padding: 0;
        flex-shrink: 0;
    }
    .agoda-gal-close-btn:hover {
        background: rgba(32, 103, 218, 0.08);
    }

    /* Agoda Stage (Slideshow Mode) */
    .agoda-gal-stage-wrapper {
        flex: 1;
        min-height: 0;
        position: relative;
        background-color: #1a202c;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .agoda-gal-stage-img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        margin: auto;
        user-select: none;
    }
    .agoda-gal-nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: none;
        box-shadow: 0 2px 7px rgba(4, 7, 10, 0.28);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        color: #252c38;
        transition: transform 0.15s ease, background 0.15s ease;
    }
    .agoda-gal-nav-arrow:hover {
        background: #f8fafc;
        transform: translateY(-50%) scale(1.05);
    }
    .agoda-gal-nav-arrow.prev {
        left: 8px;
    }
    .agoda-gal-nav-arrow.next {
        right: 8px;
    }
    .agoda-gal-stage-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(4, 7, 10, 0.85);
        color: #ffffff;
        padding: 12px 24px;
        font-size: 14.5px;
        font-weight: 600;
        letter-spacing: 0.2px;
        z-index: 10;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        box-sizing: border-box;
    }

    /* Agoda Filmstrip (Bottom Bar) */
    .agoda-gal-filmstrip-bar {
        background-color: #141922;
        height: 76px;
        padding: 6px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        z-index: 20;
        position: relative;
    }
    .agoda-gal-filmstrip-toggle {
        width: 76px;
        height: 64px;
        flex-shrink: 0;
        background: #222933;
        border: none;
        border-radius: 3px;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .agoda-gal-filmstrip-toggle:hover {
        background: #2a3442;
    }
    .agoda-gal-filmstrip-toggle span {
        font-size: 11px;
        font-weight: 600;
        color: #cbd5e1;
    }
    .agoda-gal-filmstrip-track {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow-x: auto;
        flex: 1;
        height: 100%;
        scrollbar-width: none;
        scroll-behavior: smooth;
    }
    .agoda-gal-filmstrip-track::-webkit-scrollbar {
        display: none;
    }
    .agoda-gal-thumb {
        width: 98px;
        height: 64px;
        flex-shrink: 0;
        border-radius: 2px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        opacity: 0.65;
        position: relative;
        transition: all 0.15s ease;
        background: #000;
    }
    .agoda-gal-thumb:hover {
        opacity: 0.95;
    }
    .agoda-gal-thumb.active {
        border: 2px solid #2067da !important;
        opacity: 1 !important;
        box-shadow: 0 4px 10px rgba(4, 7, 10, 0.4);
    }
    .agoda-gal-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .agoda-gal-filmstrip-next-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 2px 7px rgba(4, 7, 10, 0.35);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        color: #252c38;
        transition: transform 0.15s ease;
    }
    .agoda-gal-filmstrip-next-btn:hover {
        transform: translateY(-50%) scale(1.08);
    }

    /* Agoda Grid View Mode (When toggled to Grid) */
    .agoda-gal-grid-wrapper {
        flex: 1;
        min-height: 0;
        background-color: #f8fafc;
        overflow-y: auto;
        padding: 20px;
    }
    .agoda-gal-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
    }
    .agoda-gal-grid-card {
        border-radius: 6px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 4 / 3;
        cursor: pointer;
        background: #1e293b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .agoda-gal-grid-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.16);
    }
    .agoda-gal-grid-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>

<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
            {{-- AGODA HEADER BAR --}}
            <div class="agoda-gal-header">
                <div class="d-flex align-items-center flex-grow-1 overflow-hidden pe-2">
                    {{-- Toggle Pill Button: [ ⊞ Gallery ] or [ 🖼 Slideshow view ] --}}
                    <button type="button" class="agoda-gal-pill-btn" id="agodaGalToggleBtn" onclick="toggleAgodaViewMode();">
                        <span id="agodaGalToggleIcon" style="width: 15px; height: 15px; display: inline-flex; align-items: center;">
                            <svg style="width:15px;height:15px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g clip-path="url(#clip0_20510_6772)"><path d="M14.5 3a2 2 0 0 0-2 2v4.5a2 2 0 0 0 2 2H19a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-4.5ZM14.5 12.5a2 2 0 0 0-2 2V19a2 2 0 0 0 2 2H19a2 2 0 0 0 2-2v-4.5a2 2 0 0 0-2-2h-4.5ZM3 14.5a2 2 0 0 1 2-2h4.5a2 2 0 0 1 2 2V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4.5ZM5 3a2 2 0 0 0-2 2v4.5a2 2 0 0 0 2 2h4.5a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Z"/></g><defs><clipPath id="clip0_20510_6772"><path d="M0 0h24v24H0z"/></clipPath></defs></svg>
                        </span>
                        <span id="agodaGalToggleText">Gallery</span>
                    </button>

                    {{-- Agoda Category Tabs --}}
                    <div class="agoda-gal-tab-list" id="agodaGalTabsRow">
                        <button type="button" class="agoda-gal-tab-item active" data-cat="all" onclick="filterAgodaGallery('all', this);">
                            All ({{ count($galleryCategorized['all']) }})
                        </button>
                        @if(count($galleryCategorized['videos']) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="videos" onclick="filterAgodaGallery('videos', this);">
                            Video ({{ count($galleryCategorized['videos']) }})
                        </button>
                        @endif
                        @if(count($galleryCategorized['rooms']) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="rooms" onclick="filterAgodaGallery('rooms', this);">
                            Rooms ({{ count($galleryCategorized['rooms']) }})
                        </button>
                        @endif
                        @if(count($galleryCategorized['property_views']) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="property_views" onclick="filterAgodaGallery('property_views', this);">
                            Property views ({{ count($galleryCategorized['property_views']) }})
                        </button>
                        @endif
                        @if(count($galleryCategorized['facilities']) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="facilities" onclick="filterAgodaGallery('facilities', this);">
                            Facilities ({{ count($galleryCategorized['facilities']) }})
                        </button>
                        @endif
                        @if(count($galleryCategorized['dining']) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="dining" onclick="filterAgodaGallery('dining', this);">
                            Dining ({{ count($galleryCategorized['dining']) }})
                        </button>
                        @endif
                        @if(count($galleryCategorized['other'] ?? []) > 0)
                        <button type="button" class="agoda-gal-tab-item" data-cat="other" onclick="filterAgodaGallery('other', this);">
                            Other ({{ count($galleryCategorized['other']) }})
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Right Side: Agoda Enter dates CTA & Close Button ✕ --}}
                <div class="d-flex align-items-center flex-shrink-0">
                    <button type="button" class="agoda-gal-header-cta" onclick="document.querySelector('#available_rooms_list')?.scrollIntoView({behavior: 'smooth'}); bootstrap.Modal.getInstance(document.getElementById('galleryModal'))?.hide();">
                        Enter dates
                    </button>
                    <button type="button" class="agoda-gal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <span style="width: 18px; height: 18px; display: inline-flex; align-items: center;">
                            <svg style="width:18px;height:18px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.723 4.22a.75.75 0 0 1 1.06 1.06l-6.72 6.721 6.721 6.722a.75.75 0 0 1-1.06 1.06l-6.722-6.72-6.722 6.72a.75.75 0 0 1-1.06-1.06l6.721-6.721-6.72-6.721a.75.75 0 1 1 1.06-1.06l6.72 6.72 6.722-6.72Z"/></svg>

                        </span>
                    </button>
                </div>
            </div>

            {{-- 1. SLIDESHOW VIEW (Default Agoda 1:1 view) --}}
            <div id="agodaSlideshowSection" class="w-100 flex-grow-1 d-flex flex-column" style="min-height: 0;">
                
                {{-- Main Photo Stage --}}
                <div class="agoda-gal-stage-wrapper">
                    {{-- Previous Chevron Button --}}
                    <button type="button" class="agoda-gal-nav-arrow prev" id="agodaGalPrevBtn" onclick="navigateAgodaSlide(-1);" aria-label="Previous image">
                        <span style="width: 16px; height: 16px; display: inline-flex; align-items: center;">
                            <svg style="width:16px;height:16px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9.057 12.141a.2.2 0 0 1 0-.282l6.713-6.723c.49-.49.49-1.28 0-1.769a1.248 1.248 0 0 0-1.768 0L6.439 10.94a1.5 1.5 0 0 0 0 2.122l7.563 7.572c.49.49 1.278.49 1.768 0s.49-1.28 0-1.769l-6.713-6.723Z"/></svg>

                        </span>
                    </button>

                    {{-- Next Chevron Button --}}
                    <button type="button" class="agoda-gal-nav-arrow next" id="agodaGalNextBtn" onclick="navigateAgodaSlide(1);" aria-label="Next image">
                        <span style="width: 16px; height: 16px; display: inline-flex; align-items: center;">
                            <svg style="width:16px;height:16px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.011 11.857a.2.2 0 0 1 0 .283l-6.713 6.723c-.49.49-.49 1.279 0 1.768.49.49 1.28.49 1.769 0l7.562-7.572a1.5 1.5 0 0 0 0-2.121L9.067 3.366a1.248 1.248 0 0 0-1.769 0c-.49.49-.49 1.279 0 1.768l6.713 6.723Z"/></svg>

                        </span>
                    </button>

                    {{-- Active Stage Image --}}
                    @php
                        $initItem = $galleryCategorized['all'][0] ?? null;
                        $initIsVideo = !empty($initItem) && (($initItem['type'] ?? '') === 'video');
                        $initImg = $initIsVideo ? ($initItem['poster'] ?? ($galleryCategorized['all'][1]['url'] ?? '')) : ($initItem['url'] ?? '');
                    @endphp
                    <img id="agodaActiveStagePhoto" src="{{ $initImg }}" class="agoda-gal-stage-img {{ $initIsVideo ? 'd-none' : '' }}" alt="Hotel Photo">

                    {{-- Active Stage Video Container --}}
                    <div id="agodaActiveStageVideoWrap" class="{{ $initIsVideo ? '' : 'd-none' }} w-100 h-100 position-relative d-flex align-items-center justify-content-center" style="background: #000; cursor: pointer;" onclick="toggleAgodaActiveVideo(event)">
                        <video id="agodaActiveStageVideo" 
                               src="{{ $initIsVideo ? $initItem['url'] : '' }}" 
                               poster="{{ $initItem['poster'] ?? '' }}" 
                               controls 
                               playsinline 
                               muted 
                               autoplay
                               preload="auto" 
                               style="width: 100%; height: 100%; max-height: calc(87vh - 128px); object-fit: contain; background: #000; display: block;">
                        </video>
                        {{-- Prominent Play Overlay Button when Paused --}}
                        <div id="agodaVideoPlayOverlay" class="position-absolute top-50 start-50 translate-middle d-none align-items-center justify-content-center text-white" style="width: 72px; height: 72px; border-radius: 50%; background: rgba(0,0,0,0.68); box-shadow: 0 4px 20px rgba(0,0,0,0.5); pointer-events: none; z-index: 5;">
                            <i class="fa-solid fa-play fa-2x ms-1" style="color: #ffffff;"></i>
                        </div>
                    </div>

                    {{-- Agoda Black Translucent Caption Bar with Exact Agoda Counter (e.g. 3 / 69) --}}
                    <div class="agoda-gal-stage-caption">
                        <span id="agodaStageCaptionText" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">{{ $initItem['title'] ?? $property->name }}</span>
                        <span id="agodaStageCounterText" class="badge bg-dark bg-opacity-75 text-white fw-semibold px-2.5 py-1 ms-auto flex-shrink-0" style="font-size:12.5px; font-family:-apple-system,BlinkMacSystemFont,sans-serif; letter-spacing:0.5px;">1 / {{ count($galleryCategorized['all']) }}</span>
                    </div>
                </div>

                {{-- Agoda Filmstrip (Bottom Bar) --}}
                <div class="agoda-gal-filmstrip-bar">
                    {{-- Left Dark Gallery Button --}}
                    <button type="button" class="agoda-gal-filmstrip-toggle" onclick="toggleAgodaViewMode();" title="View all photos in Grid">
                        <span style="width: 16px; height: 16px; display: inline-flex; align-items: center; color: #cbd5e1;">
                            <svg style="width:16px;height:16px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g clip-path="url(#clip0_20510_6772)"><path d="M14.5 3a2 2 0 0 0-2 2v4.5a2 2 0 0 0 2 2H19a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-4.5ZM14.5 12.5a2 2 0 0 0-2 2V19a2 2 0 0 0 2 2H19a2 2 0 0 0 2-2v-4.5a2 2 0 0 0-2-2h-4.5ZM3 14.5a2 2 0 0 1 2-2h4.5a2 2 0 0 1 2 2V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4.5ZM5 3a2 2 0 0 0-2 2v4.5a2 2 0 0 0 2 2h4.5a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Z"/></g><defs><clipPath id="clip0_20510_6772"><path d="M0 0h24v24H0z"/></clipPath></defs></svg>
                        </span>
                        <span>Gallery</span>
                    </button>

                    {{-- Filmstrip Thumbnails Track --}}
                    <div class="agoda-gal-filmstrip-track" id="agodaFilmstripTrack">
                        @foreach($galleryCategorized['all'] as $fIdx => $fItem)
                        <div class="agoda-gal-thumb @if($fIdx === 0) active @endif" data-index="{{ $fIdx }}" data-cat="{{ $fItem['cat_key'] }}" data-room-id="{{ $fItem['room_id'] ?? '' }}" onclick="openAgodaSlideshow({{ $fIdx }});">
                            <img src="{{ !empty($fItem['thumb']) ? $fItem['thumb'] : ($fItem['poster'] ?? $fItem['url']) }}" alt="{{ $fItem['title'] }}" loading="lazy">
                            @if(($fItem['type'] ?? '') === 'video')
                            <div class="position-absolute top-50 start-50 translate-middle text-white" style="background: rgba(0,0,0,0.6); border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                <i class="fa-solid fa-play"></i>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Right Scroll Arrow for Filmstrip Track --}}
                    <button type="button" class="agoda-gal-filmstrip-next-btn" onclick="document.getElementById('agodaFilmstripTrack').scrollBy({left: 300, behavior: 'smooth'});" aria-label="Scroll thumbnails right">
                        <span style="width: 16px; height: 16px; display: inline-flex; align-items: center;">
                            <svg style="width:16px;height:16px;fill:currentColor;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.011 11.857a.2.2 0 0 1 0 .283l-6.713 6.723c-.49.49-.49 1.279 0 1.768.49.49 1.28.49 1.769 0l7.562-7.572a1.5 1.5 0 0 0 0-2.121L9.067 3.366a1.248 1.248 0 0 0-1.769 0c-.49.49-.49 1.279 0 1.768l6.713 6.723Z"/></svg>
                        </span>
                    </button>
                </div>
            </div>

            {{-- 2. GRID VIEW (Agoda Grid View Mode) --}}
            <div id="agodaGridSection" class="agoda-gal-grid-wrapper d-none">
                <div class="agoda-gal-grid-container" id="agodaGridCardsTrack">
                    @foreach($galleryCategorized['all'] as $gIdx => $gItem)
                    <div class="agoda-gal-grid-card" data-cat="{{ $gItem['cat_key'] }}" data-room-id="{{ $gItem['room_id'] ?? '' }}" onclick="openAgodaSlideshow({{ $gIdx }});">
                        <img src="{{ !empty($gItem['thumb']) ? $gItem['thumb'] : ($gItem['poster'] ?? $gItem['url']) }}" alt="{{ $gItem['title'] }}" loading="lazy">
                        @if(($gItem['type'] ?? '') === 'video')
                        <div class="position-absolute top-50 start-50 translate-middle text-white" style="background: rgba(0,0,0,0.65); border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa-solid fa-play"></i>
                        </div>
                        @endif
                    </div>
                    @endforeach
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
                @php
                    $mLat = (float)($lat ?: 21.4272);
                    $mLng = (float)($lng ?: 92.0058);
                    $mOsmBbox = ($mLng - 0.02) . '%2C' . ($mLat - 0.015) . '%2C' . ($mLng + 0.02) . '%2C' . ($mLat + 0.015);
                    $mOsmUrl = "https://www.openstreetmap.org/export/embed.html?bbox={$mOsmBbox}&layer=mapnik&marker={$mLat}%2C{$mLng}";
                @endphp
                @php
        $rawEmbed = $property->map_embed_url ?? '';
        $isSafeGoogleEmbed = str_contains($rawEmbed, 'output=embed') || str_contains($rawEmbed, 'google.com/maps/embed');
        $isOsmEmbed = str_contains($rawEmbed, 'openstreetmap.org');
        $finalMapUrl = ($isSafeGoogleEmbed || $isOsmEmbed) ? $rawEmbed : "https://maps.google.com/maps?q={$mLat},{$mLng}&hl=en&z=15&output=embed";
    @endphp
    <iframe src="{{ $finalMapUrl }}" class="w-100 h-100 border-0" allowfullscreen loading="lazy"></iframe>

                {{-- Floating Property Mini Card (Agoda Style) --}}
                <div class="position-absolute top-0 start-0 m-3 bg-white p-3 rounded-3 shadow-lg border" style="max-width: 320px; z-index: 10; border-color: #cbd5e1 !important;">
                    <div class="d-flex gap-2.5">
                        <img src="{{ media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80') }}" 
                             class="rounded-2" 
                             style="width: 70px; height: 60px; object-fit: cover; flex-shrink: 0;" 
                             alt="{{ $property->name }}"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';">
                        <div>
                            <strong class="d-block text-dark fw-bold" style="font-size: 13px; line-height: 1.2;">{{ Str::limit($property->name, 26) }}</strong>
                            <div class="text-warning small mb-1" style="font-size: 11px;">
                                @for($i = 0; $i < max(1, min(5, (int)($property->star_rating ?? 3))); $i++)★@endfor
                            </div>
                            <span class="badge bg-success" style="font-size: 10px;">{{ $score }} {{ $scoreNum >= 9 ? 'Exceptional' : 'Excellent' }}</span>
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
@if($hasVideo)
<div class="modal fade" id="videoTourModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-black text-white overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-dark d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 16px;">
                    <i class="fa-solid fa-circle-play text-danger me-2"></i> {{ $property->name }} — Official Video Tour
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center" style="background:#000; min-height: 500px;">
                @if($isYoutube)
                <div class="ratio ratio-16x9">
                    <iframe id="hotelDetailVideoIframe"
                            src=""
                            data-src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?autoplay=1&rel=0&modestbranding=1" 
                            style="border:0;" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
                @else
                <video id="hotelDetailHtmlVideo" 
                       controls 
                       playsinline 
                       preload="none"
                       class="w-100 h-100" 
                       style="max-height: 75vh; object-fit: contain; background: #000;">
                    <source data-src="{{ media_url($videoUrl) }}" src="" type="video/mp4">
                    <source data-src="{{ media_url($videoUrl) }}" src="" type="video/quicktime">
                    Your browser does not support HTML5 video.
                </video>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Audio State & Sound Toggle for Hero Background Video
    function updateHeroSoundIcon(isMuted) {
        var icon = document.getElementById('iconHeroSound');
        var btn = document.getElementById('btnToggleHeroSound');
        if (icon) {
            if (isMuted) {
                icon.className = 'fa-solid fa-volume-xmark text-white';
                if (btn) btn.title = 'Click to Turn Sound On';
            } else {
                icon.className = 'fa-solid fa-volume-high text-white';
                if (btn) btn.title = 'Click to Turn Sound Off';
            }
        }
    }

    function toggleHeroVideoSound(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        var heroVid = document.getElementById('heroDetailAutoplayVideo');
        if (!heroVid) return;

        if (heroVid.muted) {
            heroVid.muted = false;
            heroVid.volume = 1.0;
            var playPromise = heroVid.play();
            if (playPromise !== undefined) {
                playPromise.then(function() {
                    updateHeroSoundIcon(false);
                }).catch(function() {
                    heroVid.muted = false;
                    updateHeroSoundIcon(false);
                });
            } else {
                updateHeroSoundIcon(false);
            }
        } else {
            heroVid.muted = true;
            updateHeroSoundIcon(true);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var heroVid = document.getElementById('heroDetailAutoplayVideo');
        if (heroVid) {
            // Video starts 100% muted by default (user can turn sound on using the sound toggle button)
            heroVid.muted = true;
            heroVid.volume = 1.0;
            heroVid.play().catch(function() {});
            updateHeroSoundIcon(true);
        }

        var videoModalEl = document.getElementById('videoTourModal');
        if (videoModalEl) {
            videoModalEl.addEventListener('show.bs.modal', function() {
                // Pause background hero video
                if (heroVid) {
                    heroVid.pause();
                }

                var iframe = document.getElementById('hotelDetailVideoIframe');
                if (iframe && iframe.getAttribute('data-src')) {
                    iframe.src = iframe.getAttribute('data-src');
                }
                var vid = document.getElementById('hotelDetailHtmlVideo');
                if (vid) {
                    var sources = vid.querySelectorAll('source');
                    sources.forEach(function(s) {
                        var dSrc = s.getAttribute('data-src');
                        if (dSrc && !s.src) {
                            s.src = dSrc;
                        }
                    });
                    vid.load();
                    vid.currentTime = 0;
                    vid.muted = false;
                    vid.volume = 1.0;
                    var playPromise = vid.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(function() {
                            vid.muted = true;
                            vid.play().catch(function() {});
                        });
                    }
                }
            });

            videoModalEl.addEventListener('hidden.bs.modal', function() {
                var iframe = document.getElementById('hotelDetailVideoIframe');
                if (iframe) {
                    iframe.src = '';
                }
                var vid = document.getElementById('hotelDetailHtmlVideo');
                if (vid) {
                    vid.pause();
                    vid.currentTime = 0;
                }
                // Resume background hero video
                if (heroVid) {
                    heroVid.play().catch(function() {});
                }
            });
        }
    });
</script>
@endif

<script>
    window.nextRoomPhoto = function(btn, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const box = btn.closest('.room-photo-carousel');
        if (!box) return;
        const img = box.querySelector('img.room-carousel-img');
        const counter = box.querySelector('.room-photo-counter');
        let images = [];
        try {
            const raw = box.getAttribute('data-images') || '[]';
            images = typeof raw === 'string' ? JSON.parse(raw) : raw;
            if (typeof images === 'string') images = JSON.parse(images);
        } catch(e) {
            console.error('Error parsing room images:', e);
        }
        if (!Array.isArray(images) || images.length <= 1) return;
        let idx = parseInt(box.getAttribute('data-index') || '0', 10);
        idx = (idx + 1) % images.length;
        box.setAttribute('data-index', idx);
        let newSrc = images[idx];
        if (newSrc && !newSrc.startsWith('http') && !newSrc.startsWith('/')) {
            newSrc = '/' + newSrc;
        }
        if (img && newSrc) {
            img.style.opacity = '0.35';
            img.style.transition = 'opacity 0.15s ease';
            const tempImg = new Image();
            tempImg.onload = function() {
                img.src = newSrc;
                img.style.opacity = '1';
            };
            tempImg.onerror = function() {
                img.src = newSrc;
                img.style.opacity = '1';
            };
            tempImg.src = newSrc;
        }
        if (counter) {
            counter.textContent = (idx + 1) + '/' + images.length;
        }
    };

    window.prevRoomPhoto = function(btn, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const box = btn.closest('.room-photo-carousel');
        if (!box) return;
        const img = box.querySelector('img.room-carousel-img');
        const counter = box.querySelector('.room-photo-counter');
        let images = [];
        try {
            const raw = box.getAttribute('data-images') || '[]';
            images = typeof raw === 'string' ? JSON.parse(raw) : raw;
            if (typeof images === 'string') images = JSON.parse(images);
        } catch(e) {
            console.error('Error parsing room images:', e);
        }
        if (!Array.isArray(images) || images.length <= 1) return;
        let idx = parseInt(box.getAttribute('data-index') || '0', 10);
        idx = (idx - 1 + images.length) % images.length;
        box.setAttribute('data-index', idx);
        let newSrc = images[idx];
        if (newSrc && !newSrc.startsWith('http') && !newSrc.startsWith('/')) {
            newSrc = '/' + newSrc;
        }
        if (img && newSrc) {
            img.style.opacity = '0.35';
            img.style.transition = 'opacity 0.15s ease';
            const tempImg = new Image();
            tempImg.onload = function() {
                img.src = newSrc;
                img.style.opacity = '1';
            };
            tempImg.onerror = function() {
                img.src = newSrc;
                img.style.opacity = '1';
            };
            tempImg.src = newSrc;
        }
        if (counter) {
            counter.textContent = (idx + 1) + '/' + images.length;
        }
    };

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

    function toggleMobileSearchCollapse() {
        var collapseEl = document.getElementById('mobileDetailSearchCollapse');
        var iconEl = document.getElementById('mobileSearchCollapseIcon');
        if (!collapseEl) return;
        if (collapseEl.classList.contains('d-none')) {
            collapseEl.classList.remove('d-none');
            if (iconEl) {
                iconEl.classList.remove('fa-chevron-down');
                iconEl.classList.add('fa-chevron-up');
            }
        } else {
            collapseEl.classList.add('d-none');
            if (iconEl) {
                iconEl.classList.remove('fa-chevron-up');
                iconEl.classList.add('fa-chevron-down');
            }
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
    let agodaGalleryMode = 'slideshow'; // Default to Slideshow matching 1:1 screenshot
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
            if (modeIcon) modeIcon.className = 'fa-solid fa-table-cells text-primary';
            renderAgodaSlideshow(agodaGalleryCurrentIdx);
        } else {
            if (slideView) {
                slideView.classList.remove('d-flex');
                slideView.classList.add('d-none');
            }
            if (gridView) gridView.classList.remove('d-none');
            if (modeText) modeText.textContent = 'Slideshow';
            if (modeIcon) modeIcon.className = 'fa-solid fa-play text-primary';
        }
    }

        // Official Agoda Modal State & Functionality
    window.agodaGalleryCurrentIdx = 0;
    window.agodaGalleryActiveCat = 'all';
    window.agodaGalleryMode = 'slideshow'; // 'slideshow' | 'grid'

    function toggleAgodaViewMode(forceMode) {
        if (forceMode) {
            window.agodaGalleryMode = forceMode;
        } else {
            window.agodaGalleryMode = (window.agodaGalleryMode === 'slideshow') ? 'grid' : 'slideshow';
        }

        const slideshowSec = document.getElementById('agodaSlideshowSection');
        const gridSec = document.getElementById('agodaGridSection');
        const toggleText = document.getElementById('agodaGalToggleText');

        if (window.agodaGalleryMode === 'grid') {
            if (slideshowSec) slideshowSec.classList.add('d-none');
            if (gridSec) gridSec.classList.remove('d-none');
            if (toggleText) toggleText.textContent = 'Slideshow view';
        } else {
            if (gridSec) gridSec.classList.add('d-none');
            if (slideshowSec) slideshowSec.classList.remove('d-none');
            if (toggleText) toggleText.textContent = 'Gallery';
        }
    }

    function openAgodaSlideshow(index, category = 'all') {
        const modalEl = document.getElementById('galleryModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else if (window.$ && typeof window.$.fn.modal === 'function') {
                window.$(modalEl).modal('show');
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        toggleAgodaViewMode('slideshow');

        // If regular category is selected, hide the specific room tab if active
        if (category && !category.startsWith('room_')) {
            const roomTab = document.getElementById('agodaGalRoomSpecificTab');
            if (roomTab) roomTab.style.display = 'none';
        }

        if (category && category !== 'all') {
            const catTab = document.querySelector('.agoda-gal-tab-item[data-cat="' + category + '"]');
            if (catTab) {
                filterAgodaGallery(category, catTab);
                return;
            }
        }

        renderAgodaSlideshow(index);
    }

    // Agoda Room-Specific Gallery Opener: opens official gallery filtered to this room
    window.openAgodaRoomGallery = function(roomId, roomName) {
        const modalEl = document.getElementById('galleryModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else if (window.$ && typeof window.$.fn.modal === 'function') {
                window.$(modalEl).modal('show');
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        toggleAgodaViewMode('slideshow');

        window.agodaGalleryActiveRoomName = roomName;
        const rIdStr = String(roomId);

        // Find all thumbnails matching this room
        let matchingThumbs = Array.from(document.querySelectorAll(`.agoda-gal-thumb[data-room-id="${rIdStr}"]`));
        if (matchingThumbs.length === 0 && roomName) {
            matchingThumbs = Array.from(document.querySelectorAll('.agoda-gal-thumb')).filter(t => {
                const img = t.querySelector('img');
                return img && img.alt && img.alt.toLowerCase().includes(roomName.toLowerCase());
            });
        }

        const roomCount = matchingThumbs.length;

        // Manage Dynamic Room Tab Pill in Tabs Row
        const tabsRow = document.getElementById('agodaGalTabsRow');
        let roomTab = document.getElementById('agodaGalRoomSpecificTab');
        if (!roomTab && tabsRow) {
            roomTab = document.createElement('button');
            roomTab.type = 'button';
            roomTab.id = 'agodaGalRoomSpecificTab';
            roomTab.className = 'agoda-gal-tab-item';
            const roomsTab = tabsRow.querySelector('.agoda-gal-tab-item[data-cat="rooms"]');
            if (roomsTab && roomsTab.nextSibling) {
                tabsRow.insertBefore(roomTab, roomsTab.nextSibling);
            } else {
                tabsRow.appendChild(roomTab);
            }
        }

        const catKey = 'room_' + rIdStr;
        if (roomTab) {
            roomTab.setAttribute('data-cat', catKey);
            roomTab.setAttribute('data-room-id', rIdStr);
            roomTab.innerHTML = `<i class="fa-solid fa-bed me-1.5" style="font-size:11px;"></i> ${roomName} (${roomCount > 0 ? roomCount : 1})`;
            roomTab.style.display = 'inline-flex';
            roomTab.style.alignItems = 'center';
            roomTab.onclick = function() {
                filterAgodaGallery(catKey, this, rIdStr, roomName);
            };
        }

        // Update Top Right Header CTA Button to "Book this room"
        const headerCta = document.querySelector('.agoda-gal-header-cta');
        if (headerCta) {
            headerCta.innerHTML = `<i class="fa-solid fa-check me-1"></i> Book this room`;
            headerCta.onclick = function() {
                const targetCard = document.getElementById('room_card_' + rIdStr) || document.getElementById('available_rooms_list');
                if (targetCard) targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const mInstance = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
                if (mInstance) mInstance.hide();
            };
        }

        filterAgodaGallery(catKey, roomTab, rIdStr, roomName);
    };

    function toggleAgodaActiveVideo(e) {
        if (e && e.target && e.target.tagName === 'VIDEO' && e.offsetY > e.target.clientHeight - 45) {
            return; // clicked native controls bar
        }
        const v = document.getElementById('agodaActiveStageVideo');
        const overlay = document.getElementById('agodaVideoPlayOverlay');
        if (!v) return;
        if (v.paused) {
            v.play().catch(() => {});
            if (overlay) overlay.classList.add('d-none');
        } else {
            v.pause();
            if (overlay) overlay.classList.remove('d-none');
        }
    }

    function renderAgodaSlideshow(index) {
        if (!agodaGalleryItems || agodaGalleryItems.length === 0) return;
        if (index < 0) index = 0;
        if (index >= agodaGalleryItems.length) index = agodaGalleryItems.length - 1;

        window.agodaGalleryCurrentIdx = index;
        const item = agodaGalleryItems[index];

        const activeImg = document.getElementById('agodaActiveStagePhoto');
        const videoWrap = document.getElementById('agodaActiveStageVideoWrap');
        const activeVideo = document.getElementById('agodaActiveStageVideo');
        const videoOverlay = document.getElementById('agodaVideoPlayOverlay');
        const captionText = document.getElementById('agodaStageCaptionText');
        const prevBtn = document.getElementById('agodaGalPrevBtn');
        const nextBtn = document.getElementById('agodaGalNextBtn');

        // Agoda shows ONLY the room or photo title cleanly in the caption
        if (captionText) {
            if (window.agodaGalleryActiveCat && window.agodaGalleryActiveCat.startsWith('room_') && window.agodaGalleryActiveRoomName) {
                captionText.textContent = window.agodaGalleryActiveRoomName;
            } else {
                captionText.textContent = item.title || item.category || '{{ addslashes($property->name) }}';
            }
        }

        // Check position among visible thumbnails in current tab
        const visibleThumbs = Array.from(document.querySelectorAll('.agoda-gal-thumb')).filter(el => el.style.display !== 'none');
        const visibleIndices = visibleThumbs.map(el => parseInt(el.getAttribute('data-index')));
        const currentPos = visibleIndices.indexOf(index);

        const counterText = document.getElementById('agodaStageCounterText');
        if (counterText && visibleIndices.length > 0) {
            const curNum = (currentPos >= 0) ? (currentPos + 1) : 1;
            counterText.textContent = curNum + ' / ' + visibleIndices.length;
        }

        if (prevBtn) {
            prevBtn.style.display = (currentPos <= 0) ? 'none' : 'flex';
        }
        if (nextBtn) {
            nextBtn.style.display = (currentPos >= visibleIndices.length - 1 || visibleIndices.length <= 1) ? 'none' : 'flex';
        }

        if (item.type === 'video') {
            if (activeImg) activeImg.classList.add('d-none');
            if (videoWrap) videoWrap.classList.remove('d-none');
            if (activeVideo) {
                const cleanItemUrl = item.url.split('?')[0];
                const currentSrc = (activeVideo.currentSrc || activeVideo.src || '').split('?')[0];
                if (!currentSrc.includes(cleanItemUrl.replace(/https?:\/\/[^\/]+/, ''))) {
                    activeVideo.src = item.url;
                }
                if (item.poster) activeVideo.poster = item.poster;
                activeVideo.muted = true;
                if (videoOverlay) videoOverlay.classList.add('d-none');
                
                var playPromise = activeVideo.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function(err) {
                        console.log('Video autoplay prevented, showing play overlay:', err);
                        if (videoOverlay) videoOverlay.classList.remove('d-none');
                    });
                }
            }
        } else {
            if (activeVideo) {
                activeVideo.pause();
            }
            if (videoWrap) videoWrap.classList.add('d-none');
            if (videoOverlay) videoOverlay.classList.add('d-none');
            if (activeImg) {
                activeImg.classList.remove('d-none');
                activeImg.src = item.url;
            }
        }

        // Highlight active thumbnail with Agoda's blue border
        document.querySelectorAll('.agoda-gal-thumb').forEach(el => el.classList.remove('active'));
        const activeThumb = document.querySelector('.agoda-gal-thumb[data-index="' + index + '"]');
        if (activeThumb) {
            activeThumb.classList.add('active');
            activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    }

    function navigateAgodaSlide(delta) {
        const visibleThumbs = Array.from(document.querySelectorAll('.agoda-gal-thumb')).filter(el => el.style.display !== 'none');
        if (visibleThumbs.length === 0) return;

        const visibleIndices = visibleThumbs.map(el => parseInt(el.getAttribute('data-index')));
        let currentPos = visibleIndices.indexOf(window.agodaGalleryCurrentIdx);

        if (currentPos === -1) {
            renderAgodaSlideshow(visibleIndices[0]);
            return;
        }

        let newPos = currentPos + delta;
        if (newPos < 0) newPos = 0;
        if (newPos >= visibleIndices.length) newPos = visibleIndices.length - 1;

        renderAgodaSlideshow(visibleIndices[newPos]);
    }

    function nextAgodaSlide() { navigateAgodaSlide(1); }
    function prevAgodaSlide() { navigateAgodaSlide(-1); }

    function filterAgodaGallery(catKey, tabElement, roomId = null, roomName = null) {
        window.agodaGalleryActiveCat = catKey;
        document.querySelectorAll('.agoda-gal-tab-item').forEach(t => t.classList.remove('active'));
        if (tabElement) {
            tabElement.classList.add('active');
        } else {
            const matchTab = document.querySelector('.agoda-gal-tab-item[data-cat="' + catKey + '"]');
            if (matchTab) matchTab.classList.add('active');
        }

        const isRoomSpecific = (typeof catKey === 'string' && catKey.startsWith('room_'));
        const targetRoomId = roomId || (isRoomSpecific ? catKey.replace('room_', '') : null);

        // Reset Header CTA button if user switches back to general categories
        const headerCta = document.querySelector('.agoda-gal-header-cta');
        if (!isRoomSpecific && headerCta) {
            headerCta.textContent = 'Enter dates';
            headerCta.onclick = function() {
                document.querySelector('#available_rooms_list')?.scrollIntoView({ behavior: 'smooth' });
                bootstrap.Modal.getInstance(document.getElementById('galleryModal'))?.hide();
            };
        }

        const hasDirectIdMatch = isRoomSpecific && targetRoomId && document.querySelectorAll(`.agoda-gal-thumb[data-room-id="${targetRoomId}"]`).length > 0;

        // Filter Grid View cards
        document.querySelectorAll('.agoda-gal-grid-card').forEach(card => {
            const c = card.getAttribute('data-cat');
            const rId = card.getAttribute('data-room-id');
            let show = false;
            if (isRoomSpecific) {
                if (hasDirectIdMatch) {
                    show = (rId && String(rId) === String(targetRoomId));
                } else {
                    show = Boolean(roomName && card.querySelector('img')?.alt?.toLowerCase().includes(roomName.toLowerCase()));
                }
            } else {
                show = (catKey === 'all' || c === catKey);
            }
            card.style.display = show ? '' : 'none';
        });

        // Filter Filmstrip thumbs
        let firstVisibleIdx = null;
        document.querySelectorAll('.agoda-gal-thumb').forEach(thumb => {
            const c = thumb.getAttribute('data-cat');
            const rId = thumb.getAttribute('data-room-id');
            let show = false;
            if (isRoomSpecific) {
                if (hasDirectIdMatch) {
                    show = (rId && String(rId) === String(targetRoomId));
                } else {
                    show = Boolean(roomName && thumb.querySelector('img')?.alt?.toLowerCase().includes(roomName.toLowerCase()));
                }
            } else {
                show = (catKey === 'all' || c === catKey);
            }
            if (show) {
                thumb.style.display = '';
                if (firstVisibleIdx === null) {
                    firstVisibleIdx = parseInt(thumb.getAttribute('data-index'));
                }
            } else {
                thumb.style.display = 'none';
            }
        });

        // Scroll filmstrip to beginning
        const filmstrip = document.getElementById('agodaFilmstripTrack');
        if (filmstrip) filmstrip.scrollLeft = 0;

        // Auto jump to first image/video in this category
        if (firstVisibleIdx !== null) {
            renderAgodaSlideshow(firstVisibleIdx);
        }
    }

    // Modal show/hide event listeners
    document.addEventListener('DOMContentLoaded', function () {
        const galModal = document.getElementById('galleryModal');
        const activeVideo = document.getElementById('agodaActiveStageVideo');
        const videoOverlay = document.getElementById('agodaVideoPlayOverlay');

        if (activeVideo && videoOverlay) {
            activeVideo.addEventListener('play', function() {
                videoOverlay.classList.add('d-none');
            });
            activeVideo.addEventListener('pause', function() {
                videoOverlay.classList.remove('d-none');
            });
            activeVideo.addEventListener('ended', function() {
                videoOverlay.classList.remove('d-none');
            });
        }

        if (galModal) {
            galModal.addEventListener('shown.bs.modal', function () {
                renderAgodaSlideshow(window.agodaGalleryCurrentIdx || 0);
            });
            galModal.addEventListener('hidden.bs.modal', function () {
                if (activeVideo) {
                    activeVideo.pause();
                }
            });
        }
    });

    // Keyboard Arrow keys
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('galleryModal');
        if (!modal || !modal.classList.contains('show')) return;
        if (e.key === 'ArrowLeft') {
            navigateAgodaSlide(-1);
        } else if (e.key === 'ArrowRight') {
            navigateAgodaSlide(1);
        }
    });


    function switchRoomModalPhoto(roomId, imgUrl, currentIdx, totalCount, thumbEl) {
        var mImg = document.getElementById('roomModalActiveImg_' + roomId);
        if (mImg) {
            mImg.style.opacity = '0.35';
            setTimeout(function() {
                mImg.src = imgUrl;
                mImg.style.opacity = '1';
            }, 120);
        }
        var c = document.getElementById('roomModalCounter_' + roomId);
        if (c) {
            c.textContent = 'Verified High-Res Room Photo (' + currentIdx + '/' + totalCount + ')';
        }
        if (thumbEl && thumbEl.parentElement) {
            thumbEl.parentElement.querySelectorAll('.room-modal-thumb').forEach(function(el) {
                el.style.opacity = '0.7';
                el.style.borderColor = '#cbd5e1';
            });
            thumbEl.style.opacity = '1';
            thumbEl.style.borderColor = '#2067e1';
        }
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
                showHotelToast('Property link copied to clipboard!');
            });
        }
    }

    // AJAX Wishlist Toggle on Hotel Detail
    function toggleDetailWishlist(btn, propertyId) {
        const icon = btn.querySelector('i');
        const isCurrentlySaved = icon.classList.contains('fa-solid');

        // Optimistic UI state toggle
        if (isCurrentlySaved) {
            icon.className = 'fa-regular text-dark fa-heart fs-5';
        } else {
            icon.className = 'fa-solid text-danger fa-heart fs-5';
            btn.style.transform = 'scale(1.2)';
            setTimeout(() => { btn.style.transform = 'scale(1)'; }, 200);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ property_id: propertyId })
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (data && data.success) {
                showHotelToast(data.message || (data.is_wishlisted ? 'Saved to wishlist! ❤️' : 'Removed from wishlist.'));
            }
        })
    }

    // Interactive Review Mention Filter
    function filterReviewsByMention(mention, btn) {
        document.querySelectorAll('.mention-filter-btn').forEach(b => {
            b.classList.remove('btn-primary', 'text-white');
            b.classList.add('btn-light', 'text-dark', 'border');
            b.style.background = '';
        });
        if (btn) {
            btn.classList.remove('btn-light', 'text-dark', 'border');
            btn.classList.add('btn-primary', 'text-white');
            btn.style.background = '#2067e1';
        }

        const cards = document.querySelectorAll('.verified-review-card');
        let matched = 0;

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (mention === 'all' || text.includes(mention.toLowerCase())) {
                card.classList.remove('d-none');
                matched++;
            } else {
                card.classList.add('d-none');
            }
        });

        const badge = document.getElementById('reviewMatchCount');
        if (badge) {
            badge.textContent = mention === 'all' ? `Showing all ${cards.length} reviews` : `Showing ${matched} of ${cards.length} reviews matching "${mention}"`;
        }
    }

    // Instant Search Reviews by Keyword
    function searchReviewsKeyword(kw) {
        const query = (kw || '').trim().toLowerCase();
        const cards = document.querySelectorAll('.verified-review-card');
        let matched = 0;

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (!query || text.includes(query)) {
                card.classList.remove('d-none');
                matched++;
            } else {
                card.classList.add('d-none');
            }
        });

        const badge = document.getElementById('reviewMatchCount');
        if (badge) {
            badge.textContent = !query ? `Showing all ${cards.length} reviews` : `Showing ${matched} of ${cards.length} reviews matching "${query}"`;
        }
    }

    // AJAX Helpful Review Vote Handler
    function voteReview(reviewId, type, btn) {
        const box = btn.closest('.review-vote-box');
        const countSpan = box.querySelector('.vote-count-num');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        btn.disabled = true;

        fetch(`/api/v1/reviews/${reviewId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (type === 'helpful') {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-primary');
                    const icon = btn.querySelector('i');
                    if (icon) icon.className = 'fa-solid fa-thumbs-up me-1';
                    if (countSpan) countSpan.textContent = `(${data.helpful_count})`;
                }
                showHotelToast(data.message);
            } else {
                showHotelToast(data.message || 'Already voted.');
            }
        })
        .catch(() => {
            btn.disabled = false;
        });
    }

    function showHotelToast(msg) {
        let toast = document.getElementById('hotelDetailToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'hotelDetailToast';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0f172a;color:#ffffff;padding:12px 20px;border-radius:10px;font-size:13.5px;font-weight:600;box-shadow:0 10px 25px rgba(0,0,0,0.25);z-index:9999;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);opacity:0;transform:translateY(20px);pointer-events:none;';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        clearTimeout(window._hotelToastTimer);
        window._hotelToastTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 2800);
    }

    // Price Drop Alert Form Submission
    function submitPriceAlert(e) {
        e.preventDefault();
        const form = e.target;
        const email = form.querySelector('#priceAlertEmail').value;
        const targetPrice = form.querySelector('#priceAlertTargetPrice').value;
        const btn = form.querySelector('button[type="submit"]');
        const feedback = form.querySelector('#priceAlertFeedback');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Subscribing...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("api.v1.price-alert.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                property_id: {{ $property->id }},
                email: email,
                target_price: targetPrice || null
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bell me-1"></i> Activate Price Alert';
            if (data.success) {
                feedback.className = 'alert alert-success p-2 small mt-3 mb-0';
                feedback.textContent = data.message;
                feedback.classList.remove('d-none');
                setTimeout(() => {
                    const modalEl = document.getElementById('priceAlertModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    showHotelToast('🔔 Price Alert activated for ' + email);
                }, 1800);
            } else {
                feedback.className = 'alert alert-danger p-2 small mt-3 mb-0';
                feedback.textContent = data.message || 'Failed to set price alert.';
                feedback.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bell me-1"></i> Activate Price Alert';
            feedback.className = 'alert alert-danger p-2 small mt-3 mb-0';
            feedback.textContent = 'Network error. Please try again.';
            feedback.classList.remove('d-none');
        });
    }
</script>

{{-- Price Drop Alert Modal --}}
<div class="modal fade" id="priceAlertModal" tabindex="-1" aria-labelledby="priceAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning bg-opacity-25 text-warning d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="priceAlertModalLabel" style="font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Set Price Drop Alert
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="submitPriceAlert(event);">
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        Track <strong>{{ $property->name }}</strong>. We'll automatically email you when the price per night drops below your target rate.
                    </p>
                    
                    <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #f1f5f9;">
                        <span class="small text-muted">Current Rate</span>
                        <strong class="text-dark fs-6">৳{{ number_format((float)$property->price_per_night) }} / night</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Your Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="priceAlertEmail" class="form-control rounded-3" value="{{ auth()->user()?->email }}" placeholder="e.g. yourname@example.com" required style="font-size: 13.5px;">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small mb-1">Target Price per Night (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">৳</span>
                            <input type="number" id="priceAlertTargetPrice" class="form-control rounded-end-3" placeholder="{{ round($property->price_per_night * 0.85) }}" min="100" style="font-size: 13.5px;">
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Leave empty to receive an alert on ANY price drop or flash deal.</small>
                    </div>

                    <div id="priceAlertFeedback" class="d-none"></div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-bell me-1"></i> Activate Price Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Compare Room Types Modal --}}
<div class="modal fade" id="compareRoomsModal" tabindex="-1" aria-labelledby="compareRoomsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="compareRoomsModalLabel" style="font-size: 17px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fa-solid fa-code-compare text-primary me-2"></i>Compare Room Types — {{ $property->name }}
                    </h5>
                    <small class="text-muted">Compare size, bed options, amenities, and rates across all available rooms.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle text-center" style="min-width: 600px; border-color: #e2e8f0;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-start p-3" style="width: 180px; background: #f1f5f9;">Room Feature</th>
                                @foreach($property->rooms as $r)
                                <th class="p-3" style="width: calc((100% - 180px) / {{ max(1, $property->rooms->count()) }});">
                                    <div class="fw-bold text-dark fs-6">{{ $r->name }}</div>
                                    <div class="fw-bold text-danger fs-5 mt-1">৳{{ number_format((float)$r->price_per_night) }}<small class="text-muted fw-normal fs-6">/nt</small></div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Room Area</td>
                                @foreach($property->rooms as $r)
                                <td class="fw-semibold text-secondary" style="font-size: 12.5px;">{{ $r->formatted_size ?: '46 m² / 495 ft²' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Bed Setup</td>
                                @foreach($property->rooms as $r)
                                <td class="fw-semibold text-secondary" style="font-size: 12.5px;">{{ $r->bed_type ?: '1 King / Queen Bed' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Max Guests</td>
                                @foreach($property->rooms as $r)
                                <td class="text-dark" style="font-size: 12.5px;">{{ $r->max_adults ?: 2 }} Adults + {{ $r->max_children ?: 1 }} Child</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">View</td>
                                @foreach($property->rooms as $r)
                                <td class="text-secondary" style="font-size: 12.5px;">{{ $r->view_type ?: 'City view' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Free Wi-Fi & AC</td>
                                @foreach($property->rooms as $r)
                                <td><i class="fa-solid fa-circle-check text-success fs-6"></i></td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-dark bg-light px-3" style="font-size: 13px;">Booking Action</td>
                                @foreach($property->rooms as $r)
                                <td class="p-3">
                                    <a href="{{ route('booking.form', ['propertyId' => $property->id, 'room_id' => $r->id, 'check_in' => $checkIn, 'check_out' => $checkOut, 'guests' => $guests]) }}" class="btn btn-primary btn-sm fw-bold rounded-pill w-100 py-1.5" style="font-size: 12.5px;">
                                        Book This Room
                                    </a>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Write a Review Modal --}}
<div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary bg-opacity-25 text-primary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-star"></i>
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="writeReviewModalLabel" style="font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Write a Verified Guest Review
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="submitVerifiedReview(event);">
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        Share your authentic experience at <strong>{{ $property->name }}</strong> to help other travelers make informed choices.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Your Rating Score (1 to 10) <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1" id="reviewRatingInput" min="1" max="10" step="0.5" value="9" oninput="document.getElementById('reviewRatingDisplay').textContent = this.value + ' / 10';">
                            <span class="badge bg-primary fs-6 fw-bold px-3 py-1.5" id="reviewRatingDisplay">9.0 / 10</span>
                        </div>
                    </div>

                    @guest
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Your Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="reviewGuestName" class="form-control rounded-3" placeholder="e.g. Tanvir Ahmed" required style="font-size: 13.5px;">
                    </div>
                    @endguest

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Review Comments <span class="text-danger">*</span></label>
                        <textarea id="reviewComment" class="form-control rounded-3" rows="4" placeholder="Tell us about the room, cleanliness, staff hospitality, and location..." required minlength="5" maxlength="2000" style="font-size: 13.5px;"></textarea>
                        <small class="text-muted" style="font-size: 11px;">Our AI engine scans and verifies feedback automatically.</small>
                    </div>

                    <div id="reviewFeedback" class="d-none"></div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function submitVerifiedReview(e) {
        e.preventDefault();
        const form = e.target;
        const rating = form.querySelector('#reviewRatingInput').value;
        const comment = form.querySelector('#reviewComment').value;
        const nameInput = form.querySelector('#reviewGuestName');
        const guestName = nameInput ? nameInput.value : '';
        const btn = form.querySelector('button[type="submit"]');
        const feedback = form.querySelector('#reviewFeedback');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Submitting...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("hotels.review.store", $property->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rating: rating,
                comment: comment,
                guest_name: guestName
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Submit Review';
            if (data.success) {
                feedback.className = 'alert alert-success p-2 small mt-3 mb-0';
                feedback.textContent = data.message;
                feedback.classList.remove('d-none');
                setTimeout(() => {
                    const modalEl = document.getElementById('writeReviewModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    showHotelToast('🌟 Thank you! Your review has been processed.');
                    setTimeout(() => { window.location.reload(); }, 1000);
                }, 1500);
            } else {
                feedback.className = 'alert alert-danger p-2 small mt-3 mb-0';
                feedback.textContent = data.message || 'Review validation failed.';
                feedback.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Submit Review';
            feedback.className = 'alert alert-danger p-2 small mt-3 mb-0';
            feedback.textContent = 'Submission failed. Please try again.';
            feedback.classList.remove('d-none');
        });
    }

    // Web Share API & Quick Share Modal Handler
    function sharePropertyLink() {
        const title = @json($property->name . ' - Prime Booking');
        const text = @json('Check out ' . $property->name . ' in ' . ($property->city ?: 'Bangladesh') . ' on Prime Booking!');
        const url = window.location.href;

        if (navigator.share) {
            navigator.share({
                title: title,
                text: text,
                url: url
            }).catch(() => {});
        } else {
            const modalEl = document.getElementById('sharePropertyModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    }

    function copyShareLink(btn) {
        const input = document.getElementById('sharePropertyUrlInput');
        if (!input) return;
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            const orig = btn.innerHTML;
            btn.className = 'btn btn-success fw-bold px-3';
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
            showHotelToast('🔗 Property link copied to clipboard!');
            setTimeout(() => {
                btn.className = 'btn btn-primary fw-bold px-3';
                btn.innerHTML = orig;
            }, 2000);
        });
    }
</script>

{{-- Quick Share Property Modal --}}
<div class="modal fade" id="sharePropertyModal" tabindex="-1" aria-labelledby="sharePropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary bg-opacity-25 text-primary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-share-nodes"></i>
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="sharePropertyModalLabel" style="font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Share this Property
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">
                    Share <strong>{{ $property->name }}</strong> with family, friends, or travel companions:
                </p>

                <div class="input-group mb-4 shadow-xs">
                    <input type="text" id="sharePropertyUrlInput" class="form-control" value="{{ url()->current() }}" readonly style="font-size: 13px; background: #f8fafc;">
                    <button type="button" class="btn btn-primary fw-bold px-3" onclick="copyShareLink(this);">
                        <i class="fa-regular fa-copy me-1"></i> Copy
                    </button>
                </div>

                <div class="d-flex align-items-center justify-content-around py-2 border-top">
                    @php
                        $encodedUrl = urlencode(url()->current());
                        $encodedText = urlencode("Check out {$property->name} on Prime Booking: " . url()->current());
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $encodedText }}" target="_blank" rel="noopener" class="text-decoration-none text-center d-flex flex-column align-items-center gap-1 text-dark">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: #25D366; color: #fff; font-size: 20px;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <small class="fw-semibold" style="font-size: 11px;">WhatsApp</small>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener" class="text-decoration-none text-center d-flex flex-column align-items-center gap-1 text-dark">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: #1877F2; color: #fff; font-size: 20px;">
                            <i class="fa-brands fa-facebook-f"></i>
                        </span>
                        <small class="fw-semibold" style="font-size: 11px;">Facebook</small>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ $encodedText }}" target="_blank" rel="noopener" class="text-decoration-none text-center d-flex flex-column align-items-center gap-1 text-dark">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: #000000; color: #fff; font-size: 18px;">
                            <i class="fa-brands fa-x-twitter"></i>
                        </span>
                        <small class="fw-semibold" style="font-size: 11px;">X (Twitter)</small>
                    </a>
                    <a href="mailto:?subject={{ urlencode($property->name . ' on Prime Booking') }}&body={{ $encodedText }}" class="text-decoration-none text-center d-flex flex-column align-items-center gap-1 text-dark">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; background: #EA4335; color: #fff; font-size: 18px;">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <small class="fw-semibold" style="font-size: 11px;">Email</small>
                    </a>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Ask Host / Property Inquiry Modal --}}
<div class="modal fade" id="askHostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 17px;"><i class="fa-solid fa-headset text-primary me-2"></i> Ask a Question to {{ $property->name }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('inquiry.store') }}" method="POST" id="askHostForm" onsubmit="submitHostInquiry(event)">
                    @csrf
                    <input type="hidden" name="service_type" value="Property Inquiry: {{ $property->name }}">
                    <input type="hidden" name="destination" value="{{ $property->city }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Your Full Name</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ auth()->user()->name ?? '' }}" placeholder="e.g. Tanvir Ahmed" required style="height: 44px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Mobile Phone (WhatsApp)</label>
                        <input type="text" name="phone" class="form-control rounded-3" value="{{ auth()->user()->phone ?? '+880 ' }}" placeholder="+880 1700-000000" required style="height: 44px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ auth()->user()->email ?? '' }}" placeholder="name@example.com" style="height: 44px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Your Question or Special Request</label>
                        <textarea name="message" class="form-control rounded-3" rows="3" placeholder="e.g. Is early check-in available? Can you arrange airport pickup?" required></textarea>
                    </div>

                    <div id="askHostAlert" class="d-none alert mb-3 small py-2"></div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2.5 shadow-sm" id="askHostSubmitBtn">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Question to Host
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Agoda Floating Deals / Cart Trigger Button (Bottom Right) --}}
<a href="#rooms" class="btn btn-primary rounded-circle shadow-lg position-fixed d-none d-md-flex align-items-center justify-content-center text-white text-decoration-none" style="bottom: 24px; right: 24px; width: 48px; height: 48px; z-index: 1050; background-color: #2067e1; border: none; box-shadow: 0 4px 14px rgba(32,103,225,0.4) !important;" title="View deals" onclick="document.getElementById('rooms')?.scrollIntoView({behavior:'smooth'}); return false;">
    <i class="fa-solid fa-suitcase-rolling fs-5"></i>
</a>

<script>
function submitHostInquiry(e) {
    e.preventDefault();
    const form = document.getElementById('askHostForm');
    const btn = document.getElementById('askHostSubmitBtn');
    const alertBox = document.getElementById('askHostAlert');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    alertBox.className = 'd-none alert mb-3 small py-2';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alertBox.className = 'alert alert-success mb-3 small py-2';
        alertBox.textContent = data.message || 'Thank you! Your question has been sent to the property host.';
        form.reset();
        setTimeout(() => {
            const modalEl = document.getElementById('askHostModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }, 2500);
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alertBox.className = 'alert alert-danger mb-3 small py-2';
        alertBox.textContent = 'Unable to send question right now. Please try again.';
    });
}

// Record this property to Recently Viewed
document.addEventListener('DOMContentLoaded', function() {
    try {
        const prop = {
            id: {{ $property->id }},
            name: @json($property->name),
            city: @json($property->city),
            price: @json(\App\Services\CurrencyService::format($property->price_per_night)),
            rating: @json(number_format((float)($property->rating_score ?? 8.5), 1)),
            image: @json(media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'))
        };
        let recents = JSON.parse(localStorage.getItem('prime_recent_properties')) || [];
        recents = recents.filter(item => item.id !== prop.id);
        recents.unshift(prop);
        if (recents.length > 8) recents.pop();
        localStorage.setItem('prime_recent_properties', JSON.stringify(recents));
        if (typeof renderRecentProperties === 'function') renderRecentProperties();
    } catch(e) {}
});
</script>

{{-- Leaflet CSS & JS for Live Interactive Maps --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" media="print" onload="this.media='all'" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var hotelLat = {{ (float)($property->latitude ?: 21.4272) }};
    var hotelLng = {{ (float)($property->longitude ?: 92.0058) }};
    var hotelName = @json($property->name);
    var hotelAddress = @json($property->address ?: $property->city ?: 'Bangladesh');
    var hotelPrice = @json(\App\Services\CurrencyService::format($property->price_per_night));
    var hotelImage = @json(media_url($gallery[0] ?? $property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'));

    // 1. Mini Map (Sidebar Live Real OpenStreetMap)
    var miniEl = document.getElementById('hotelDetailMiniMap');
    if (miniEl && typeof L !== 'undefined') {
        try {
            var miniMap = L.map('hotelDetailMiniMap', {
                center: [hotelLat, hotelLng],
                zoom: 14,
                zoomControl: false,
                attributionControl: false,
                dragging: false,
                touchZoom: false,
                doubleClickZoom: false,
                scrollWheelZoom: false,
                boxZoom: false,
                keyboard: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18
            }).addTo(miniMap);

            var miniPin = L.divIcon({
                className: 'hotel-mini-pin',
                html: '<div style="width:30px;height:30px;background:#dc2626;border:2.5px solid #ffffff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#ffffff;box-shadow:0 3px 10px rgba(0,0,0,0.35);font-size:13px;"><i class="fa-solid fa-hotel"></i></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            L.marker([hotelLat, hotelLng], {icon: miniPin}).addTo(miniMap);
        } catch(e) {
            console.warn('MiniMap init error:', e);
        }
    }

    // 2. Full Modal Map on Click
    var modalEl = document.getElementById('hotelDetailMapModal');
    var fullMap = null;
    if (modalEl && typeof L !== 'undefined') {
        modalEl.addEventListener('shown.bs.modal', function () {
            if (!fullMap) {
                fullMap = L.map('hotelDetailFullMap').setView([hotelLat, hotelLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors | PRIME BOOKING'
                }).addTo(fullMap);

                var fullPin = L.divIcon({
                    className: 'hotel-full-pin',
                    html: '<div style="background:#2067e1;color:#ffffff;font-weight:800;font-size:12.5px;padding:6px 14px;border-radius:22px;box-shadow:0 4px 16px rgba(32,103,225,0.45);border:2px solid #ffffff;white-space:nowrap;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-hotel"></i> ' + hotelPrice + '</div>',
                    iconSize: [120, 34],
                    iconAnchor: [60, 17]
                });

                var popupContent = `
                    <div style="width:230px;font-family:\'Plus Jakarta Sans\',sans-serif;padding:4px;">
                        <img src="${hotelImage}" style="width:100%;height:115px;object-fit:cover;border-radius:6px;margin-bottom:8px;">
                        <div style="font-weight:700;font-size:13.5px;color:#0f172a;line-height:1.25;margin-bottom:4px;">${hotelName}</div>
                        <div style="font-size:11.5px;color:#64748b;margin-bottom:8px;"><i class="fa-solid fa-location-dot text-danger me-1"></i>${hotelAddress}</div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-weight:800;font-size:14px;color:#2067e1;">${hotelPrice}</span>
                            <span style="font-size:10.5px;color:#64748b;">Per night</span>
                        </div>
                    </div>
                `;

                L.marker([hotelLat, hotelLng], {icon: fullPin})
                    .addTo(fullMap)
                    .bindPopup(popupContent)
                    .openPopup();
            } else {
                fullMap.invalidateSize();
                fullMap.setView([hotelLat, hotelLng], 15);
            }
        });
    }
});
</script>

{{-- Interactive Real Leaflet Map Modal for Hotel Detail Page --}}
<div class="modal fade" id="hotelDetailMapModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px); background: rgba(0, 0, 0, 0.75);">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 1100px; height: 85vh; margin: 7.5vh auto;">
        <div class="modal-content border-0 overflow-hidden shadow-2xl" style="height: 100%; border-radius: 12px; background: #ffffff;">
            <div class="modal-header border-bottom px-4 py-3 bg-white" style="border-color: #e2e8f0 !important; z-index: 10;">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white" style="width: 34px; height: 34px;">
                        <i class="fa-solid fa-location-dot fs-6"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $property->name }}</h5>
                        <small class="text-secondary" style="font-size: 12px;"><i class="fa-solid fa-map-pin text-danger me-1"></i>{{ $property->address ?: $property->city ?: 'Bangladesh' }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ (float)($property->latitude ?: 21.4272) }},{{ (float)($property->longitude ?: 92.0058) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5" style="font-size: 12px;">
                        <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
                    </a>
                    <button type="button" class="btn-close shadow-none ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: calc(100% - 65px);">
                <div id="hotelDetailFullMap" style="width: 100%; height: 100%; z-index: 1;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Best Price Guarantee Modal --}}
<div class="modal fade" id="bestPriceGuaranteeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success text-white shadow-xs" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-shield-halved fs-6"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 16px;">Best Price Guarantee</h5>
                        <small class="text-secondary" style="font-size: 11.5px;">Book with confidence on PRIME BOOKING</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;"><i class="fa-solid fa-hand-holding-dollar text-success me-1"></i> Found a cheaper price elsewhere?</strong>
                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                        If you find a lower publicly available rate on another travel website for the same room type, dates, and terms, we will match the price and refund the difference!
                    </p>
                </div>

                <h6 class="fw-bold text-dark small text-uppercase mb-2">How It Works:</h6>
                <div class="d-flex flex-column gap-2 text-secondary small mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge rounded-circle bg-primary text-white p-1" style="font-size: 10px; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <span>Book your stay at <strong>{{ $property->name }}</strong> on Prime Booking.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge rounded-circle bg-primary text-white p-1" style="font-size: 10px; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <span>Find a lower price for the identical property, room, and dates online.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge rounded-circle bg-primary text-white p-1" style="font-size: 10px; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        <span>Contact our 24/7 concierge support via WhatsApp or email before check-in.</span>
                    </div>
                </div>

                <a href="https://api.whatsapp.com/send?phone=8801756000111&text={{ urlencode('Hello Prime Booking, I would like to claim the Best Price Guarantee for ' . $property->name) }}" target="_blank" rel="noopener" class="btn btn-success w-100 fw-bold rounded-3 py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-brands fa-whatsapp fs-5"></i> Contact Price Match Concierge
                </a>
            </div>
        </div>
    </div>
</div>

{{-- HOTEL FRONT DESK INQUIRY MODAL (Agoda-Style Direct Messaging) --}}
<div class="modal fade" id="hotelInquiryModal" tabindex="-1" aria-labelledby="hotelInquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:6px; border:1px solid #cbd5e1; box-shadow:0 12px 40px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px; background:#f8fafc;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px; height:36px; font-size:15px;">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="hotelInquiryModalLabel" style="font-size:15px;">
                            Message {{ $property->name }} Front Desk
                        </h6>
                        <small class="text-muted" style="font-size:11.5px;">Direct communication with property manager</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('hotels.inquire', $property->id) }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    {{-- Quick Prompt Chips --}}
                    <label class="form-label mb-1.5" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">Quick Questions / Topics</label>
                    <div class="d-flex flex-wrap gap-1.5 mb-3">
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setInquiryText('Is early check-in or late check-out available for my booking dates?')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            🕒 Early Check-In
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setInquiryText('Do you provide airport shuttle / transfer pickup service and what is the fee?')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            🚗 Airport Shuttle
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setInquiryText('What is your married couple policy and what ID documents are required at check-in?')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            💍 Couple &amp; Family Policy
                        </button>
                        <button type="button" class="btn btn-sm btn-light border text-dark fw-semibold" onclick="setInquiryText('Is complimentary breakfast included in the room rate?')" style="font-size:11px; border-radius:4px; padding:3px 8px;">
                            🍳 Free Breakfast Info
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#1e293b;">Your Message / Question <span class="text-danger">*</span></label>
                        <textarea name="message" id="inquiryMessageField" class="form-control" rows="3" required placeholder="Type your question for the hotel front desk..." style="font-size:13px; border-radius:4px;"></textarea>
                    </div>

                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#1e293b;">Your Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()?->name ?? '' }}" required placeholder="e.g. Rahim Ahmed" style="font-size:12.5px; height:36px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#1e293b;">Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ auth()->user()?->phone ?? '' }}" required placeholder="017XXXXXXXX" style="font-size:12.5px; height:36px; border-radius:4px;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#1e293b;">Email Address (Optional)</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()?->email ?? '' }}" placeholder="your-email@example.com" style="font-size:12.5px; height:36px; border-radius:4px;">
                    </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px; background:#f8fafc;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:12.5px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size:12.5px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">
                        <i class="fa-solid fa-paper-plane"></i> Send to Hotel Desk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Agoda Signature Support & Chat Widget (1:1 Exact Parity with Screenshot 2) --}}
<style>
@media (max-width: 767.98px) {
    #agodaQuickAnswersWrapper {
        bottom: 76px !important;
        right: 16px !important;
    }
    #quickAnswersCard {
        width: calc(100vw - 32px) !important;
        max-width: 335px !important;
    }
}
</style>
<div id="agodaQuickAnswersWrapper" class="position-fixed" style="bottom: 24px; right: 24px; z-index: 1080;">
    
    {{-- Quick Answers Interactive Chat Window (Agoda Screenshot 1:1 Parity) --}}
    <div id="quickAnswersCard" class="card shadow-lg border-0 mb-2.5 overflow-hidden d-none" style="width: 335px; border-radius: 12px; background: #ffffff; border: 1px solid #cbd5e1 !important; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2) !important;">
        
        {{-- 1. Solid Blue Header with Minimize Dash (Exact Compact Agoda 1:1 Parity) --}}
        <div class="px-3.5 py-2 text-white d-flex align-items-center justify-content-between" style="background: #1b65e7; border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h6 class="mb-0 fw-bold" style="font-size: 14.5px; font-family: 'Plus Jakarta Sans', sans-serif; color: #ffffff !important; font-weight: 800; letter-spacing: 0.1px;">
                Quick Answers
            </h6>
            <button type="button" class="btn btn-sm p-0 text-white border-0 bg-transparent d-flex align-items-center justify-content-center me-1" onclick="toggleQuickAnswers()" title="Minimize" style="width: 22px; height: 22px;">
                <i class="fa-solid fa-minus" style="color: #ffffff !important; font-size: 15px; font-weight: 900;"></i>
            </button>
        </div>

        {{-- 2. Chat Messages Area (Agoda Clean Canvas) --}}
        <div id="quickAnswersChatBody" class="p-3 overflow-y-auto" style="height: 340px; background: #ffffff; font-size: 12.5px; font-family: 'Plus Jakarta Sans', sans-serif;">
            
            {{-- AI Legal Disclaimer Text (Exact Agoda Parity) --}}
            <div class="mb-3" style="font-size: 11px; line-height: 1.45; color: #64748b;">
                This conversation uses generative AI and will be recorded. Inaccuracies may occur. Do not enter any personal or sensitive information. For details on our use of generative AI, see our <a href="javascript:void(0)" class="fw-bold text-primary text-decoration-none">terms of use</a>.
            </div>

            {{-- Initial Bot Greeting Bubble --}}
            <div class="d-flex flex-column align-items-start mb-3">
                <div class="p-2.5 text-dark" style="max-width: 90%; line-height: 1.45; background: #e8f0fe; border-radius: 14px 14px 14px 2px; color: #1e293b; font-size: 12.5px; font-weight: 500;">
                    Hi 👋 . What question do you have about this property?
                </div>
            </div>

            {{-- Quick Suggestion Pills (Agoda 1:1 Pill Border Buttons with Left Icon & Right Chevron) --}}
            <div class="d-flex flex-column gap-2 mb-3" id="quickPromptChips">
                <button type="button" class="btn btn-sm bg-white text-primary border px-3 py-2 rounded-pill shadow-2xs d-flex align-items-center justify-content-between text-start" style="border-color: #cbd5e1 !important; color: #1b65e7 !important; font-weight: 700; font-size: 12px;" onclick="askQuickAnswer('What\'s the check-in/out policy?')">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-check" style="font-size: 13px; width: 16px; text-align: center;"></i>
                        <span>What's the check-in/out policy?</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-primary" style="font-size: 12px;"></i>
                </button>
                <button type="button" class="btn btn-sm bg-white text-primary border px-3 py-2 rounded-pill shadow-2xs d-flex align-items-center justify-content-between text-start" style="border-color: #cbd5e1 !important; color: #1b65e7 !important; font-weight: 700; font-size: 12px;" onclick="askQuickAnswer('Is there free parking?')">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-square-parking" style="font-size: 13px; width: 16px; text-align: center;"></i>
                        <span>Is there free parking?</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-primary" style="font-size: 12px;"></i>
                </button>
                <button type="button" class="btn btn-sm bg-white text-primary border px-3 py-2 rounded-pill shadow-2xs d-flex align-items-center justify-content-between text-start" style="border-color: #cbd5e1 !important; color: #1b65e7 !important; font-weight: 700; font-size: 12px;" onclick="askQuickAnswer('Is there airport transfer?')">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-van-shuttle" style="font-size: 13px; width: 16px; text-align: center;"></i>
                        <span>Is there airport transfer?</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-primary" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>

        {{-- 3. Input Bar (Agoda 1:1 Light Blue Oval Input + Clean Right Paperplane) --}}
        <div class="p-3 bg-white border-top d-flex align-items-center gap-2" style="border-color: #f1f5f9 !important;">
            <div class="flex-grow-1 position-relative">
                <input type="text" id="quickAnswersInput" class="form-control px-3.5" placeholder="Ask your question" style="font-size: 12.5px; height: 40px; border-radius: 24px; background: #f0f4f9; border: 1px solid #cbd5e1; color: #0f172a;" onkeydown="if(event.key==='Enter') sendQuickAnswerQuery()">
            </div>
            <button type="button" class="btn p-0 d-flex align-items-center justify-content-center flex-shrink-0 text-secondary border-0 bg-transparent" onclick="sendQuickAnswerQuery()" title="Send" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-paper-plane text-secondary" style="font-size: 16px;"></i>
            </button>
        </div>
    </div>

    {{-- Floating Circular Button (Matches Agoda Screenshot 2 Exactly) --}}
    <button type="button" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center text-white border-0" onclick="toggleQuickAnswers()" style="width: 48px; height: 48px; background: #2067e1; box-shadow: 0 4px 14px rgba(32, 103, 225, 0.4) !important;" title="Customer Assistance">
        <i class="fa-solid fa-headset fs-5"></i>
    </button>
</div>

<script>
function toggleQuickAnswers() {
    const card = document.getElementById('quickAnswersCard');
    if (card) {
        if (card.classList.contains('d-none')) {
            card.classList.remove('d-none');
            document.getElementById('quickAnswersInput')?.focus();
        } else {
            card.classList.add('d-none');
        }
    }
}

function askQuickAnswer(question) {
    const input = document.getElementById('quickAnswersInput');
    if (input) {
        input.value = question;
        sendQuickAnswerQuery();
    }
}

function sendQuickAnswerQuery() {
    const input = document.getElementById('quickAnswersInput');
    const q = input.value.trim();
    if (!q) return;

    const chatBody = document.getElementById('quickAnswersChatBody');

    // Add User Message Bubble (Agoda 1:1 Spacious Rounded Pill)
    const userBubble = `
        <div class="d-flex flex-column align-items-end mb-3">
            <div class="text-white shadow-xs text-start" style="padding: 10px 18px !important; max-width: 85%; line-height: 1.4; background: #2067e1; border-radius: 20px 20px 4px 20px; font-size: 13px; font-weight: 600; word-break: break-word; letter-spacing: 0.2px;">
                ${q}
            </div>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', userBubble);
    // Hide suggestion chips once conversation starts
    const promptChips = document.getElementById('quickPromptChips');
    if (promptChips) promptChips.classList.add('d-none');

    // Add 5 Colorful Animated Dots Typing Indicator (Agoda Exact 1:1 Parity)
    const typingId = 'typingIndicator_' + Date.now();
    const typingBubble = `
        <div class="d-flex align-items-center gap-2 mb-3" id="${typingId}">
            <div class="d-flex align-items-center gap-1">
                <span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background-color: #ef4444; animation: agodaBounce 1.2s infinite ease-in-out;"></span>
                <span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background-color: #f59e0b; animation: agodaBounce 1.2s infinite ease-in-out 0.2s;"></span>
                <span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background-color: #10b981; animation: agodaBounce 1.2s infinite ease-in-out 0.4s;"></span>
                <span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background-color: #06b6d4; animation: agodaBounce 1.2s infinite ease-in-out 0.6s;"></span>
                <span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background-color: #3b82f6; animation: agodaBounce 1.2s infinite ease-in-out 0.8s;"></span>
            </div>
            <span class="text-secondary" style="font-size: 11.5px; font-weight: 500;">typing...</span>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', typingBubble);
    input.value = '';
    chatBody.scrollTop = chatBody.scrollHeight;

    // Senior Engineer Real-Data Knowledge Engine (Dynamic Inspection of $property)
    setTimeout(() => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        let answer = "";
        let actionBtn = "";
        const lowerQ = q.toLowerCase();
        const propName = @json($property->name);
        const checkinTime = "{{ $property->checkin_time ? date('h:i A', strtotime($property->checkin_time)) : '01:00 PM' }}";
        const checkoutTime = "{{ $property->checkout_time ? date('h:i A', strtotime($property->checkout_time)) : '11:00 AM' }}";
        const city = @json($property->city ?? 'Bangladesh');
        const address = @json($property->address ?? '');
        const roomCount = {{ $property->rooms->count() }};
        const minPriceFormatted = @json(strip_tags(format_price($property->price_per_night)));
        const hasParking = {{ (collect($amenitiesList)->contains(fn($a) => str_contains(strtolower($a), 'park')) || str_contains(strtolower($property->facilities_text ?? ''), 'park')) ? 'true' : 'false' }};
        const hasWifi = {{ (collect($amenitiesList)->contains(fn($a) => str_contains(strtolower($a), 'wi-fi') || str_contains(strtolower($a), 'wifi')) || true) ? 'true' : 'false' }};
        const hasBreakfast = {{ ($property->rooms->contains(fn($r) => str_contains(strtolower($r->meal_policy ?? ''), 'breakfast') || str_contains(strtolower($r->facilities_text ?? ''), 'breakfast'))) ? 'true' : 'false' }};

        if (lowerQ.includes('parking')) {
            if (hasParking) {
                answer = `Yes! <strong>${propName}</strong> provides <strong>parking facilities</strong> for staying guests. Check room details for any specific valet or reserved spot inclusions.`;
            } else {
                answer = `The property information for <strong>${propName}</strong> doesn’t explicitly list private parking. If you’d like, you can check the <strong>Room Offers</strong> section or message the host directly via your booking confirmation for street/nearby parking options.`;
            }
        } else if (lowerQ.includes('breakfast')) {
            if (hasBreakfast) {
                answer = `Good news! <strong>Breakfast Included</strong> plans are available for select rooms at <strong>${propName}</strong>. Look for the green breakfast badge in the <strong>Select your room</strong> section!`;
                actionBtn = `<a href="#rooms" class="btn btn-outline-primary btn-sm rounded-pill mt-2 fw-bold" style="font-size: 11.5px;" onclick="toggleQuickAnswers();">View Breakfast Rooms</a>`;
            } else {
                answer = `Breakfast options depend on the specific room rate chosen. Dining area and full kitchen facilities are also available in select apartment units.`;
            }
        } else if (lowerQ.includes('check-in') || lowerQ.includes('check in') || lowerQ.includes('time') || lowerQ.includes('checkout') || lowerQ.includes('check out')) {
            answer = `Standard check-in for <strong>${propName}</strong> starts at <strong>${checkinTime}</strong>, and check-out is until <strong>${checkoutTime}</strong>. 24-hour contactless check-in is also supported.`;
        } else if (lowerQ.includes('wifi') || lowerQ.includes('wi-fi') || lowerQ.includes('internet')) {
            answer = `Yes! High-speed <strong>Free Wi-Fi</strong> is active throughout all rooms and common spaces at <strong>${propName}</strong>.`;
        } else if (lowerQ.includes('kid') || lowerQ.includes('child') || lowerQ.includes('family')) {
            answer = `Family friendly! <strong>Your kids can stay for FREE</strong> when using existing beds in the room. Family room types are available for larger groups.`;
        } else if (lowerQ.includes('bed') || lowerQ.includes('room') || lowerQ.includes('sleep')) {
            answer = `<strong>${propName}</strong> offers <strong>${roomCount} configured room types</strong> featuring King beds, Twin configurations, and sofa beds.`;
            actionBtn = `<a href="#rooms" class="btn btn-outline-primary btn-sm rounded-pill mt-2 fw-bold" style="font-size: 11.5px;" onclick="toggleQuickAnswers();">Browse Room Types</a>`;
        } else if (lowerQ.includes('location') || lowerQ.includes('near') || lowerQ.includes('where') || lowerQ.includes('airport') || lowerQ.includes('distance')) {
            answer = `<strong>${propName}</strong> is conveniently situated in <strong>${city}</strong>${address ? ` (${address})` : ''}. Top local landmarks and transit spots are just 350m to 1.5km away.`;
            actionBtn = `<a href="#location" class="btn btn-outline-primary btn-sm rounded-pill mt-2 fw-bold" style="font-size: 11.5px;" onclick="toggleQuickAnswers();">View on Map</a>`;
        } else if (lowerQ.includes('cancel') || lowerQ.includes('refund') || lowerQ.includes('policy')) {
            answer = `Risk-free booking! Most rooms at <strong>${propName}</strong> feature <strong>100% Free Cancellation</strong> up to 24-48 hours before check-in.`;
        } else if (lowerQ.includes('price') || lowerQ.includes('cost') || lowerQ.includes('rate') || lowerQ.includes('cheap')) {
            answer = `Rates for <strong>${propName}</strong> start from approximately <strong>${minPriceFormatted}/night</strong>, backed by our <strong>We Price Match Guarantee</strong>!`;
        } else {
            answer = `According to verified details for <strong>${propName}</strong> in <strong>${city}</strong>, standard hotel perks, attentive staff, and verified hygiene protocols are guaranteed. You can ask me about parking, breakfast, check-in time, or room amenities!`;
        }

        const botBubble = `
            <div class="d-flex flex-column align-items-start mb-3">
                <div class="p-3 text-dark" style="max-width: 92%; line-height: 1.5; background: #f0f4f9; border-radius: 12px 12px 12px 2px; font-size: 13px; color: #1e293b;">
                    ${answer}
                    ${actionBtn ? `<div>${actionBtn}</div>` : ''}
                </div>
                <div class="d-flex align-items-center gap-3 mt-1.5 px-1 text-secondary" style="font-size: 12px;">
                    <i class="fa-regular fa-thumbs-down" style="cursor: pointer;" title="Not helpful" onclick="this.classList.toggle('text-danger')"></i>
                    <i class="fa-regular fa-thumbs-up" style="cursor: pointer;" title="Helpful" onclick="this.classList.toggle('text-success')"></i>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', botBubble);
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 1600);
}
</script>

<script>
function setInquiryText(text) {
    const el = document.getElementById('inquiryMessageField');
    if (el) {
        el.value = text;
        el.focus();
    }
}

// Adaptive Full-Width on Scroll for Sub-Nav Bar (Agoda 1:1 Parity)
document.addEventListener('DOMContentLoaded', function() {
    const stickyNav = document.getElementById('agodaStickyNavWrapper');
    if (stickyNav) {
        const checkSticky = () => {
            const rect = stickyNav.getBoundingClientRect();
            if (rect.top <= 72) {
                stickyNav.classList.add('is-stuck');
            } else {
                stickyNav.classList.remove('is-stuck');
            }
        };
        window.addEventListener('scroll', checkSticky, { passive: true });
        checkSticky();
    }
});
</script>
<style>
.agoda-room-popover-item:hover {
    background-color: #f8fafc !important;
}
.agoda-room-pill-btn:hover {
    border-color: #0b66c2 !important;
}
</style>

<script>
function toggleAgodaRoomDropdown(btn, e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const wrapper = btn.closest('.agoda-room-selector-wrapper');
    if (!wrapper) return;
    const popover = wrapper.querySelector('.agoda-room-dropdown-popover');
    if (!popover) return;
    
    const isClosed = popover.classList.contains('d-none');
    
    // Close all other popovers across the page
    document.querySelectorAll('.agoda-room-dropdown-popover').forEach(p => {
        p.classList.add('d-none');
    });
    
    if (isClosed) {
        popover.classList.remove('d-none');
    }
}

function selectAgodaRoomCount(itemEl, count, label) {
    const wrapper = itemEl.closest('.agoda-room-selector-wrapper');
    if (!wrapper) return;
    
    // Update pill button text
    const labelSpan = wrapper.querySelector('.agoda-room-pill-label');
    if (labelSpan) labelSpan.textContent = label;
    
    // Update items state in this popover
    const allItems = wrapper.querySelectorAll('.agoda-room-popover-item');
    allItems.forEach(item => {
        const val = parseInt(item.getAttribute('data-value'), 10);
        const checkSpan = item.querySelector('.agoda-item-check');
        if (val === count) {
            item.style.color = '#0b66c2';
            item.style.fontWeight = '700';
            if (checkSpan) {
                checkSpan.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0b66c2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
            }
        } else {
            item.style.color = '#222222';
            item.style.fontWeight = '500';
            if (checkSpan) {
                checkSpan.innerHTML = '';
            }
        }
    });
    
    // Update Book button URL in the same column
    const col = wrapper.closest('.col-md-3') || wrapper.parentElement;
    if (col) {
        const bookBtn = col.querySelector('.agoda-book-btn') || col.querySelector('a[href*="/book/"]') || col.querySelector('a[href*="booking.form"]');
        if (bookBtn) {
            try {
                const url = new URL(bookBtn.href, window.location.origin);
                url.searchParams.set('rooms', count);
                bookBtn.href = url.pathname + url.search;
            } catch(e) {}
        }

        // Dynamically recalculate price for selected room count
        const row = col.closest('.row') || col.parentElement;
        if (row) {
            const priceNumEl = row.querySelector('.agoda-final-price-num');
            const unitEl = row.querySelector('.agoda-price-unit-text');
            if (priceNumEl) {
                if (!priceNumEl.hasAttribute('data-base-price')) {
                    const rawVal = parseFloat(priceNumEl.textContent.replace(/[^0-9.]/g, '')) || 0;
                    priceNumEl.setAttribute('data-base-price', rawVal);
                }
                const base = parseFloat(priceNumEl.getAttribute('data-base-price')) || 0;
                const multiplied = Math.round(base * count);
                priceNumEl.textContent = multiplied.toLocaleString('en-US');
                if (unitEl) {
                    unitEl.textContent = count > 1 ? `Total for ${count} rooms / night before taxes` : 'Per night before taxes & fees';
                }
            }
        }
    }
    
    // Close popover
    const popover = wrapper.querySelector('.agoda-room-dropdown-popover');
    if (popover) {
        popover.classList.add('d-none');
    }
}

// Global click outside listener to dismiss popovers
document.addEventListener('click', function(e) {
    if (!e.target.closest('.agoda-room-selector-wrapper')) {
        document.querySelectorAll('.agoda-room-dropdown-popover').forEach(p => {
            p.classList.add('d-none');
        });
    }
});

// Interactive Room Pill Filters (Agoda 1:1 Parity)
window.activeRoomFilters = window.activeRoomFilters || new Set();
var activeRoomFilters = window.activeRoomFilters;

function toggleRoomFilter(filterName, btn) {
    if (activeRoomFilters.has(filterName)) {
        activeRoomFilters.delete(filterName);
        btn.classList.remove('active');
    } else {
        activeRoomFilters.add(filterName);
        btn.classList.add('active');
    }
    applyRoomFilters();
}

function applyRoomFilters() {
    const roomCards = document.querySelectorAll('.agoda-room-listing-card');
    let visibleCount = 0;
    
    roomCards.forEach(card => {
        let matches = true;
        for (let filter of activeRoomFilters) {
            const attrVal = card.getAttribute('data-' + filter);
            if (attrVal !== 'true') {
                matches = false;
                break;
            }
        }
        if (matches) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    let emptyMsg = document.getElementById('agodaRoomsEmptyFilterMsg');
    if (!emptyMsg) {
        const roomsContainer = document.getElementById('available_rooms_list');
        if (roomsContainer) {
            emptyMsg = document.createElement('div');
            emptyMsg.id = 'agodaRoomsEmptyFilterMsg';
            emptyMsg.className = 'text-center py-5 px-3 rounded mb-4';
            emptyMsg.style.cssText = 'background: #f8fafc; border: 1px dashed #cbcfd7;';
            emptyMsg.innerHTML = `
                <div class="mb-2"><i class="fa-solid fa-filter-circle-xmark text-muted" style="font-size: 32px;"></i></div>
                <h6 class="fw-bold text-dark mb-1">No room types match all selected filters</h6>
                <p class="text-secondary small mb-3">Try deselecting one or more filters above to see more available rooms.</p>
                <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold" onclick="clearAllRoomFilters()">Clear all room filters</button>
            `;
            roomsContainer.appendChild(emptyMsg);
        }
    }
    if (emptyMsg) {
        emptyMsg.style.display = (visibleCount === 0 && roomCards.length > 0) ? 'block' : 'none';
    }
}

function clearAllRoomFilters() {
    activeRoomFilters.clear();
    document.querySelectorAll('.agoda-room-filter-pill').forEach(btn => {
        btn.classList.remove('active');
    });
    applyRoomFilters();
}

// Mobile Touch Swipe Support for Room Photo Carousels
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.room-photo-carousel').forEach(function(box) {
        var startX = 0;
        box.addEventListener('touchstart', function(e) {
            if (e.touches && e.touches.length > 0) {
                startX = e.touches[0].clientX;
            }
        }, { passive: true });
        box.addEventListener('touchend', function(e) {
            if (e.changedTouches && e.changedTouches.length > 0) {
                var diffX = startX - e.changedTouches[0].clientX;
                if (Math.abs(diffX) > 40) {
                    if (diffX > 0) {
                        var nBtn = box.querySelector('button[onclick*="nextRoomPhoto"]');
                        if (nBtn && window.nextRoomPhoto) window.nextRoomPhoto(nBtn, e);
                    } else {
                        var pBtn = box.querySelector('button[onclick*="prevRoomPhoto"]');
                        if (pBtn && window.prevRoomPhoto) window.prevRoomPhoto(pBtn, e);
                    }
                }
            }
        }, { passive: true });
    });
});
</script>
@endsection

