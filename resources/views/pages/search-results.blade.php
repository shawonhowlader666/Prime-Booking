@extends('layouts.main', ['activePage' => 'services'])

@php
    $dest = trim($destination ?? '');
    $destLabel = !empty($dest) ? $dest : 'Bangladesh';
    
    // Result counts
    $merged = $searchResults['merged_results'] ?? [];
    $totalCount = isset($searchResults['paginator']) ? $searchResults['paginator']->total() : count($merged);
    
    // Extract dynamic highlights, prices, and Schema items
    $prices = [];
    $hotelNames = [];
    $schemaItems = [];
    
    foreach ($merged as $idx => $mItem) {
        $mIsObj = is_object($mItem);
        $mPrice = (float)($mIsObj ? ($mItem->price_per_night ?? $mItem->price ?? 0) : ($mItem['price_per_night'] ?? $mItem['price'] ?? 0));
        if ($mPrice > 0) $prices[] = $mPrice;
        
        $mName = $mIsObj ? ($mItem->name ?? '') : ($mItem['name'] ?? '');
        if (!empty($mName) && count($hotelNames) < 5) {
            $hotelNames[] = $mName;
        }

        // Schema item data (up to top 15 properties for Google rich snippets)
        if ($idx < 15 && !empty($mName)) {
            $mSlug = $mIsObj ? ($mItem->slug ?? null) : ($mItem['slug'] ?? null);
            $mId = $mIsObj ? ($mItem->id ?? 1) : ($mItem['id'] ?? 1);
            $mUrl = !empty($mSlug) ? url("/hotel/{$mSlug}") : url("/hotels/{$mId}");
            $rawImg = $mIsObj ? ($mItem->primary_image ?? null) : ($mItem['primary_image'] ?? null);
            $mImg = !empty($rawImg) ? (str_starts_with($rawImg, 'http') ? $rawImg : url($rawImg)) : url('/android-chrome-512x512.png');
            $mScore = (float)($mIsObj ? ($mItem->rating_score ?? $mItem->rating ?? 8.5) : ($mItem['rating_score'] ?? $mItem['rating'] ?? 8.5));
            $mReviews = (int)($mIsObj ? ($mItem->total_reviews ?? $mItem->reviews_count ?? 15) : ($mItem['total_reviews'] ?? $mItem['reviews_count'] ?? 15));
            $mCity = $mIsObj ? ($mItem->city ?? $destLabel) : ($mItem['city'] ?? $destLabel);
            $mAddress = $mIsObj ? ($mItem->address ?? $mCity) : ($mItem['address'] ?? $mCity);
            $mStars = (int)($mIsObj ? ($mItem->star_rating ?? 4) : ($mItem['star_rating'] ?? 4));

            $schemaItems[] = [
                chr(64) . 'type' => 'ListItem',
                'position' => $idx + 1,
                'item' => [
                    chr(64) . 'type' => 'Hotel',
                    'name' => $mName,
                    'image' => $mImg,
                    'url' => $mUrl,
                    'priceRange' => $mPrice > 0 ? 'BDT ' . number_format($mPrice, 0) : 'BDT 2,500 - 15,000',
                    'starRating' => [
                        chr(64) . 'type' => 'Rating',
                        'ratingValue' => $mStars ?: 4,
                    ],
                    'aggregateRating' => [
                        chr(64) . 'type' => 'AggregateRating',
                        'ratingValue' => number_format($mScore, 1),
                        'bestRating' => '10',
                        'reviewCount' => max(1, $mReviews),
                    ],
                    'address' => [
                        chr(64) . 'type' => 'PostalAddress',
                        'streetAddress' => $mAddress,
                        'addressLocality' => $mCity,
                        'addressCountry' => 'BD',
                    ],
                ]
            ];
        }
    }
    
    $minFoundPrice = !empty($prices) ? min($prices) : 2500;
    $minPriceText = '৳' . number_format($minFoundPrice, 0);
    $hotelHighlights = !empty($hotelNames) ? implode(', ', $hotelNames) : 'Sea Pearl, Ocean Paradise, Sayeman Beach Resort';
    
    $intent = request('seo_intent') ?? (request()->routeIs('seo.city.cheap') ? 'cheap' : (request()->routeIs('seo.city.resorts') ? 'resort' : (request()->routeIs('seo.city.5star') ? '5star' : null)));
    $isResort = $intent === 'resort' || in_array('resort', (array)request('property_type', [])) || request('type') === 'resort';
    $isCheap = $intent === 'cheap' || request('sort_by') === 'price_low' || (request('max_price') && (float)request('max_price') <= 3500);
    $is5Star = $intent === '5star' || in_array(5, (array)request('star_rating', [])) || in_array('5', (array)request('star_rating', []));

    // Dynamic Meta Title & Snippets (Engineered for Google #1 SERP Rankings & Max CTR)
    $radiusExpanded = !empty($searchResults['radius_expanded']);
    $expandedRadiusKm = (int)($searchResults['expanded_radius_km'] ?? 45);

    if (!empty($dest)) {
        if ($totalCount === 0) {
            $metaTitle = "Hotels in {$dest} | Find Verified Stays - Prime Booking";
            $metaDesc = "Looking for hotels in {$dest}? Currently no direct stays are registered in {$dest}. Explore top-rated hotels, beach resorts & stays across Bangladesh on Prime Booking.";
        } elseif ($radiusExpanded) {
            $metaTitle = "{$totalCount} Hotels near {$dest} (Within {$expandedRadiusKm} km) | Prime Booking";
            $metaDesc = "Find {$totalCount} verified hotels and stays near {$dest} within {$expandedRadiusKm} km. Instant reservation confirmation, bKash discount & free cancellation on Prime Booking.";
        } elseif ($isCheap) {
            $metaTitle = "Cheap Hotels in {$dest} (From {$minPriceText}/Night) | Budget Stays 2026 - Prime Booking";
            $metaDesc = "Find verified cheap & budget-friendly hotels in {$dest} starting from {$minPriceText}/night. Clean rooms, free WiFi, bKash discount & free cancellation on Prime Booking.";
        } elseif ($isResort) {
            $metaTitle = "Best Luxury Resorts in {$dest} 2026 (Sea View & Pool) | Prime Booking";
            $metaDesc = "Book top luxury resorts & beach stays in {$dest}. {$hotelHighlights}. Swimming pool, private beach access, breakfast & up to 45% discount on Prime Booking.";
        } elseif ($is5Star) {
            $metaTitle = "Top 5-Star Hotels & Luxury Resorts in {$dest} 2026 | Prime Booking";
            $metaDesc = "Experience 5-star world-class hospitality in {$dest}. Compare {$hotelHighlights} with infinity pool, spa, ocean view & instant bKash booking.";
        } else {
            $metaTitle = "{$totalCount} Best Hotels in {$dest} 2026 (From {$minPriceText}) | Prime Booking";
            $metaDesc = "Most booked hotels & resorts in {$dest}: {$hotelHighlights}. Book top stays from {$minPriceText}/night with instant confirmation, bKash discount & free cancellation on Prime Booking.";
        }
        $metaKeywords = "hotels in " . strtolower($dest) . ", best resorts in " . strtolower($dest) . ", 5 star hotel " . strtolower($dest) . ", cheap hotels " . strtolower($dest) . ", budget hotel " . strtolower($dest) . ", sea view hotel " . strtolower($dest) . ", hotel booking bangladesh, " . strtolower($dest) . " হোটেল বুকিং, কম খরচে " . strtolower($dest) . " হোটেল, ৫ তারকা রিসোর্ট, প্রাইম বুকিং";
        $canonicalUrl = url('/hotels-in-' . \Illuminate\Support\Str::slug($dest));
    } else {
        $metaTitle = "Top Hotels, Luxury Resorts & Stays Across Bangladesh 2026 | Prime Booking";
        $metaDesc = "Find and book 2,000+ top hotels, resorts, Sundarbans cruises & Tanguar Haor houseboats across Bangladesh. Guaranteed lowest prices, verified guest reviews & instant bKash booking.";
        $metaKeywords = "hotel booking bangladesh, online hotel booking bd, resorts in bangladesh, coxs bazar hotels, sajek resort, sylhet hotels, dhaka hotel booking, sundarbans ship, tanguar haor houseboat, হোটেল বুকিং বাংলাদেশ, প্রাইম বুকিং";
        $canonicalUrl = route('search.index');
    }

    $ogImage = (!empty($schemaItems[0]['item']['image'])) 
        ? $schemaItems[0]['item']['image'] 
        : url('/android-chrome-512x512.png');
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeywords)
@section('canonical_url', $canonicalUrl)
@section('og_title', $metaTitle)
@section('og_description', $metaDesc)
@section('og_image', $ogImage)

@if(!empty($dest))
@section('extra_meta')
    <meta name="geo.placename" content="{{ $dest }}, Bangladesh">
    <meta name="geo.region" content="BD">
@endsection
@endif


@section('schema')
<script type="application/ld+json">
{!! json_encode([
    chr(64) . 'context' => 'https://schema.org',
    chr(64) . 'graph' => array_values(array_filter([
        // 1. BreadcrumbList Schema
        [
            chr(64) . 'type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
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
                !empty($dest) ? [
                    chr(64) . 'type' => 'ListItem',
                    'position' => 3,
                    'name' => "Hotels in {$dest}",
                    'item' => $canonicalUrl,
                ] : null,
            ])),
        ],
        // 2. ItemList Schema (Rich snippet carousel & price list)
        (!empty($schemaItems) && $totalCount > 0) ? [
            chr(64) . 'type' => 'ItemList',
            'name' => !empty($dest) ? "Best Hotels & Resorts in {$dest}" : 'Top Hotels in Bangladesh',
            'description' => "Verified best rates on luxury resorts and boutique hotels in {$destLabel}, Bangladesh.",
            'numberOfItems' => count($schemaItems),
            'itemListElement' => $schemaItems,
        ] : null,
        // 3. FAQPage Schema (Triggers Google Rich FAQ Accordion in Search Results!)
        (!empty($dest) && $totalCount > 0) ? [
            chr(64) . 'type' => 'FAQPage',
            'mainEntity' => [
                [
                    chr(64) . 'type' => 'Question',
                    'name' => "What are the best hotels to stay in {$dest}?",
                    'acceptedAnswer' => [
                        chr(64) . 'type' => 'Answer',
                        'text' => "The top rated and most popular hotels in {$dest} on Prime Booking include {$hotelHighlights}, offering premium amenities, scenic views, and 24/7 hospitality.",
                    ],
                ],
                [
                    chr(64) . 'type' => 'Question',
                    'name' => "How much does it cost to book a hotel in {$dest}?",
                    'acceptedAnswer' => [
                        chr(64) . 'type' => 'Answer',
                        'text' => "Hotel prices in {$dest} start from as low as {$minPriceText} per night on Prime Booking, with exclusive seasonal discounts and instant bKash cashback offers.",
                    ],
                ],
                [
                    chr(64) . 'type' => 'Question',
                    'name' => "Are there 5-star luxury resorts in {$dest} with swimming pools?",
                    'acceptedAnswer' => [
                        chr(64) . 'type' => 'Answer',
                        'text' => "Yes, premier properties in {$dest} such as Sea Pearl Beach Resort & Spa, Ocean Paradise, and Long Beach Hotel feature world-class infinity swimming pools, private beach access, multi-cuisine restaurants, and wellness spas.",
                    ],
                ],
                [
                    chr(64) . 'type' => 'Question',
                    'name' => "Can I book hotels in {$dest} with bKash and free cancellation?",
                    'acceptedAnswer' => [
                        chr(64) . 'type' => 'Answer',
                        'text' => "Yes, Prime Booking provides free cancellation and pay-at-hotel options along with 100% instant bKash, Nagad, and card payments on selected properties in {$dest}.",
                    ],
                ],
                [
                    chr(64) . 'type' => 'Question',
                    'name' => "{$dest}-এ হোটেল বা রিসোর্ট বুকিং করার সেরা উপায় কোনটি?",
                    'acceptedAnswer' => [
                        chr(64) . 'type' => 'Answer',
                        'text' => "প্রাইম বুকিং (Prime Booking) থেকে বিকাশ বা নগদে সবচেয়ে কম খরচে সেরা ডিসকাউন্টে {$dest}-এর শীর্ষ হোটেল ও রিসোর্ট সহজে বুক করা যায়।",
                    ],
                ],
            ],
        ] : null,
    ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@push('styles')
    <link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1297-81103bc3ecd4.css" rel="stylesheet">
    <link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5132-7d6f3e76125d.css" rel="stylesheet">
    <link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/search-d7c52cdf705c.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1467-2ed4d1bb3553.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/dynamic-search-bundle-coreBundle-4048fc571f65.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/6522-d51775bfe82d.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2253-804a78cf4607.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/7487-0447de72b94b.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2911-78b9810a6dd7.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5004-1036beaf8ac3.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2483-e3942a16cf2e.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4170-2e4ec80e1a0c.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/6843-fa506b71dc5d.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5837-827d4d74e66a.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1860-9a2263b2a60a.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2481-1393f55b7097.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/8564-5fc754378659.css">
    <link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4490-830fbdcdd63c.css">
@endpush

@section('content')


{{-- 2. Agoda Official Search Box Subheader (100% Downloaded Agoda Code & Classes) --}}
@php
    $checkinCarbon = \Carbon\Carbon::parse($checkIn ?: now());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(7));
    $guestCount = intval($guests ?: 2);
    $roomsCount = intval(request('rooms', 1));
@endphp
<div id="stickyAgodaSearchSubheader" style="background-color: #1d2b45; padding: 12px 0; border-bottom: 1px solid #334155; position: sticky; top: 0; z-index: 1025; box-shadow: 0 4px 15px rgba(0,0,0,0.18);">
    <div style="max-width: 1140px; margin: 0 auto; padding: 0 24px;">
        <form action="{{ route('search.index') }}" method="GET" id="searchHeaderForm" onsubmit="return validateAndSubmitHeaderSearch(event);" style="margin: 0; width: 100%;">
            <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">
            <div class="SearchBoxContainer SearchBoxContainer--AllRooms SearchBoxContainer--compact SearchBoxContainer--noanimation SearchBoxContainer--ImproveTextSearch" id="SearchBoxContainer" data-selenium="normal-property-searchbox">
                <div class="Searchbox Searchbox--horizontal" data-selenium="searchBox">
                    <div class="Searchbox__wrapper">
                        <div class="abb64-flex-1 abb64-content-center abb64-flex abb64-flex-row">
                            
                            {{-- Autocomplete Destination Input --}}
                            <div tabindex="-1" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--autocomplete" data-selenium="autocomplete-box" data-element-name="autocomplete-box" data-element-value="prefilled" id="autocomplete-box">
                                <div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper">
                                    <span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-search-box" data-selenium="ficon-icon-box"></i></span>
                                    <div class="IconBox__child" data-selenium="icon-box-child">
                                        <input aria-label="Enter a destination or property" role="combobox" aria-expanded="false" data-selenium="textInput" tabindex="0" type="text" class="SearchBoxTextEditor SearchBoxTextEditor--autocomplete" placeholder="Enter a destination or property" id="mainDestInput" name="destination" value="{{ $destination }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            {{-- Check-In Date Box --}}
                            <div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--checkIn IconBox--focussable" data-element-name="check-in-box" role="button" aria-expanded="false" id="check-in-box" onclick="document.getElementById('checkInNativeInput').showPicker();" style="cursor: pointer;">
                                <div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper">
                                    <span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-check-in" data-selenium="ficon-icon-box"></i></span>
                                    <div class="IconBox__child" data-selenium="icon-box-child">
                                        <div data-selenium="checkInBox" class="SearchBoxTextDescription SearchBoxTextDescription--checkIn SearchBoxTextDescription--focussable">
                                            <div class="SearchBoxTextDescription__title" id="checkInDisplayDate" data-selenium="checkInText">{{ $checkinCarbon->format('j M Y') }}</div>
                                            <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" id="checkInDisplayDay">{{ $checkinCarbon->format('l') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="date" name="check_in" id="checkInNativeInput" value="{{ $checkinCarbon->format('Y-m-d') }}" style="position: absolute; opacity: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkIn', this.value);">
                            </div>

                            {{-- Check-Out Date Box --}}
                            <div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--checkOut IconBox--focussable" data-element-name="check-out-box" role="button" aria-expanded="false" id="check-out-box" onclick="document.getElementById('checkOutNativeInput').showPicker();" style="cursor: pointer;">
                                <div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper">
                                    <span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-check-out" data-selenium="ficon-icon-box"></i></span>
                                    <div class="IconBox__child" data-selenium="icon-box-child">
                                        <div data-selenium="checkOutBox" class="SearchBoxTextDescription SearchBoxTextDescription--checkOut SearchBoxTextDescription--focussable">
                                            <div class="SearchBoxTextDescription__title" id="checkOutDisplayDate" data-selenium="checkOutText">{{ $checkoutCarbon->format('j M Y') }}</div>
                                            <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" id="checkOutDisplayDay">{{ $checkoutCarbon->format('l') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="date" name="check_out" id="checkOutNativeInput" value="{{ $checkoutCarbon->format('Y-m-d') }}" style="position: absolute; opacity: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkOut', this.value);">
                            </div>

                            {{-- Occupancy / Guests Box --}}
                            <div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--occupancy IconBox--focussable dropdown position-relative" data-element-name="occupancy-box" role="button" id="occupancy-box">
                                <div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="cursor: pointer; width: 100%;">
                                    <span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-travelers" data-selenium="ficon-icon-box"></i></span>
                                    <div class="IconBox__child" data-selenium="icon-box-child">
                                        <div data-selenium="occupancyBox" class="SearchBoxTextDescription SearchBoxTextDescription--occupancy">
                                            <div class="SearchBoxTextDescription__title">
                                                <span data-selenium="adultValue" class="abb64-box abb64-fill-inherit abb64-text-inherit" id="guestCountDisplay">{{ $guestCount }} adult{{ $guestCount > 1 ? 's' : '' }}</span>
                                            </div>
                                            <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" data-selenium="roomValue" id="roomCountDisplay">{{ $roomsCount }} room{{ $roomsCount > 1 ? 's' : '' }}</span>
                                            <div class="SearchBoxTextDescription__child"><i class="ficon ficon-12 ficon-thin-arrow-down"></i></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Guests Counter Popover Menu --}}
                                <div class="dropdown-menu p-3 shadow-lg border-0 rounded-3 mt-2" style="width: 280px; z-index: 1050;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 14px;">Adults</strong>
                                            <small class="text-muted" style="font-size: 12px;">Ages 18 or above</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('guests', -1);">-</button>
                                            <input type="hidden" name="guests" id="guestsHiddenInput" value="{{ $guestCount }}">
                                            <span class="fw-bold px-1" id="guestsValText">{{ $guestCount }}</span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('guests', 1);">+</button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-2">
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 14px;">Rooms</strong>
                                            <small class="text-muted" style="font-size: 12px;">Total units needed</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', -1);">-</button>
                                            <input type="hidden" name="rooms" id="roomsHiddenInput" value="{{ $roomsCount }}">
                                            <span class="fw-bold px-1" id="roomsValText">{{ $roomsCount }}</span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', 1);">+</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn text-white w-100 btn-sm fw-bold rounded-2" style="background: #2067e1; font-size: 13.5px;" onclick="bootstrap.Dropdown.getInstance(this.closest('.position-relative').querySelector('.dropdown-toggle')).hide();">
                                        Done
                                    </button>
                                </div>
                            </div>

                            {{-- Blue SEARCH Button --}}
                            <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline-flex" style="width: 162px;">
                                <button data-selenium="searchButton" data-element-name="search-button" data-active-pressed="true" type="submit" class="abb64-box abb64-fill-generic-base-inverted abb64-text-generic-base-inverted abb64-w-full abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-mt-2 abb64-px-24 abb64-py-12 abb64-rounded-base abb64-border-0 abb64-interactive-style abb64-interactive-primary abb64-focus-ring-interactive-border abb64-interactive-bg" style="min-height: 44px; border-radius: 9999px; background-color: #2067e1 !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(32, 103, 225, 0.4);">
                                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row">
                                        <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN jXHxke" style="font-weight: 700; font-size: 15px; color: #ffffff !important; letter-spacing: 0.5px;">SEARCH</span>
                                    </div>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function updateSearchDateDisplay(type, dateStr) {
        if (!dateStr) return;
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return;

        const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
        const formattedDay = dayNames[d.getDay()];

        if (type === 'checkIn') {
            document.getElementById('checkInDisplayDate').textContent = formattedDate;
            document.getElementById('checkInDisplayDay').textContent = formattedDay;
        } else {
            document.getElementById('checkOutDisplayDate').textContent = formattedDate;
            document.getElementById('checkOutDisplayDay').textContent = formattedDay;
        }
    }

    function adjustGuestCounter(field, delta) {
        if (field === 'guests') {
            const inp = document.getElementById('guestsHiddenInput');
            let val = Math.max(1, Math.min(30, (parseInt(inp.value) || 2) + delta));
            inp.value = val;
            document.getElementById('guestsValText').textContent = val;
            document.getElementById('guestCountDisplay').textContent = `${val} adult${val > 1 ? 's' : ''}`;
        } else if (field === 'rooms') {
            const inp = document.getElementById('roomsHiddenInput');
            let val = Math.max(1, Math.min(10, (parseInt(inp.value) || 1) + delta));
            inp.value = val;
            document.getElementById('roomsValText').textContent = val;
            document.getElementById('roomCountDisplay').textContent = `${val} room${val > 1 ? 's' : ''}`;
        }
    }

    function useCurrentLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }
        const destInput = document.getElementById('mainDestInput');
        const icon = document.getElementById('gpsCrosshairIcon');
        const origPlaceholder = destInput ? destInput.placeholder : '';
        if (destInput) destInput.placeholder = "Detecting your GPS location...";
        if (icon) icon.className = "fa-solid fa-spinner fa-spin";

        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const latInput = document.getElementById('gpsLatInput');
            const lngInput = document.getElementById('gpsLngInput');
            if (latInput) latInput.value = lat;
            if (lngInput) lngInput.value = lng;
            if (destInput) destInput.value = "Near My Location";

            // Instant client-side reverse geocoding with fast 650ms timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 650);

            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`, { signal: controller.signal })
                .then(r => r.json())
                .then(data => {
                    clearTimeout(timeoutId);
                    const area = data.locality || data.city || data.principalSubdivision;
                    if (area && destInput) {
                        destInput.value = area;
                    }
                })
                .catch(() => {})
                .finally(() => {
                    if (icon) icon.className = "fa-solid fa-location-crosshairs";
                    const form = document.getElementById('searchHeaderForm');
                    if (form) form.submit();
                });
        }, function(err) {
            // Silent IP Geolocation Fallback if GPS is blocked or denied
            fetch('https://api.bigdatacloud.net/data/reverse-geocode-client?localityLanguage=en')
                .then(r => r.json())
                .then(data => {
                    const city = data.city || data.principalSubdivision || "Dhaka";
                    if (destInput) destInput.value = city;
                    const form = document.getElementById('searchHeaderForm');
                    if (form) form.submit();
                })
                .catch(() => {
                    if (destInput) destInput.placeholder = origPlaceholder;
                    if (icon) icon.className = "fa-solid fa-location-crosshairs";
                    alert("Location access denied. Please type your destination city.");
                });
        }, { timeout: 10000, enableHighAccuracy: true });
    }
</script>


{{-- Full Funnel Coupons Banner (Exact Agoda Parity) --}}
<div id="fullFunnelBannerTarget" data-testid="fullFunnelBannerTarget">
    <div id="full-funnel-banner-container" style="background-color: #fff2ed; border-bottom: 1px solid #ffd8cc; padding: 8px 0;">
        <div style="max-width: 1120px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <img alt="coupon banner" style="width: 36px; height: 36px; object-fit: contain;" src="https://cdn0.agoda.net/images/emailmarketing/Web_SSR_banner/webbanner_coupon.png" onerror="this.style.display='none'">
                <div>
                    <h2 style="font-size: 15px; font-weight: 700; color: #2a2a2e; margin: 0; line-height: 1.2;">Looking for instant coupons?</h2>
                    <div style="font-size: 13px; color: #505256; margin-top: 2px;">Check out our Coupons &amp; Deals page for today's discounts</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('deals') }}" style="height: 36px; padding: 0 20px; border-radius: 9999px; background: #ffffff; border: 1px solid #2067e1; color: #2067e1; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    See all coupons
                </a>
                <button type="button" onclick="document.getElementById('fullFunnelBannerTarget').style.display='none'" style="background: none; border: none; padding: 6px; cursor: pointer; color: #737373;" title="Close">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 4. Main Search Results Layout (Centered, Equal Left/Right Padding, Compact Card Width) --}}
<style>
    @media (min-width: 992px) {
        .agoda-filter-sidebar-col {
            width: 260px !important;
            flex: 0 0 260px !important;
            max-width: 260px !important;
        }
        .agoda-results-col {
            width: calc(100% - 260px) !important;
            flex: 0 0 calc(100% - 260px) !important;
            max-width: calc(100% - 260px) !important;
        }
    }
    @media (max-width: 991.98px) {
        .agoda-results-col {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
    }
    .agoda-property-card-root {
        max-width: 790px !important;
        margin-left: 0;
        margin-right: auto;
    }
    @media (min-width: 768px) {
        .agoda-property-card-root {
            height: 234px !important;
            min-height: 234px !important;
            max-height: 234px !important;
        }
        .agoda-property-card-root.agoda-first-featured-card {
            height: 235px !important;
            min-height: 235px !important;
            max-height: 235px !important;
            background: #eaf2fd !important;
            border-color: #b9d5fb !important;
        }
        .agoda-card-blue-strip {
            background: #1f4ba4 !important;
            color: #ffffff !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            padding: 7px 14px !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
            line-height: 1.4 !important;
        }
        .agoda-card-content-wrap {
            height: 234px !important;
            min-height: 234px !important;
            max-height: 234px !important;
        }
        .agoda-card-photo-col {
            width: 236px !important;
            min-width: 236px !important;
            max-width: 236px !important;
            height: 234px !important;
            min-height: 234px !important;
            max-height: 234px !important;
        }
        .agoda-card-info-col {
            height: 234px !important;
            min-height: 234px !important;
            max-height: 234px !important;
            padding: 9px 14px !important;
        }
        .agoda-card-price-col {
            width: 195px !important;
            min-width: 195px !important;
            max-width: 195px !important;
            height: 234px !important;
            min-height: 234px !important;
            max-height: 234px !important;
            padding: 9px 14px 9px 10px !important;
        }
    }
    .agoda-sticky-sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .agoda-sticky-sidebar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .agoda-sticky-sidebar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .agoda-sticky-sidebar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Agoda-Exact Horizontal Shimmer Wave Animation (Right to Left sweep) */
    @keyframes agodaShimmerWave {
        0% {
            background-position: 250% 0;
        }
        100% {
            background-position: -250% 0;
        }
    }
    .agoda-shimmer {
        background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 30%, #ffffff 50%, #e2e8f0 70%, #f1f5f9 100%);
        background-size: 250% 100%;
        animation: agodaShimmerWave 1.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    .agoda-skeleton-card {
        max-width: 790px !important;
        margin-left: 0;
        margin-right: auto;
    }
    @keyframes fadeInUpCard {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<div style="max-width: 1120px; margin: 0 auto; padding: 22px 24px 48px 24px;">
    <div class="row g-3">
            
            {{-- Left Filter Sidebar (Exact Agoda 270px Width, Desktop Sticky Scroll) --}}
            <div class="col-12 col-lg-3 d-none d-lg-block agoda-filter-sidebar-col">
                <div class="agoda-sticky-sidebar" style="position: sticky; top: 76px; max-height: calc(100vh - 86px); overflow-y: auto; scrollbar-width: thin; padding-right: 4px;">
                    @include('components.search.filter-sidebar')
                </div>
            </div>

            {{-- Right Results Feed (Exact Agoda 830px Width Desktop, 100% Width Mobile) --}}
            <div class="col-12 col-lg-9 agoda-results-col" id="searchResultsContainer">



            {{-- 3.6 Auto-Radius Expander Notice Banner (Only shown when genuine nearby stays exist <= 45km) --}}
            @if(!empty($searchResults['radius_expanded']) && $liveTotal > 0)
            <div class="alert d-flex align-items-center gap-3 rounded-3 mb-3 p-3 shadow-xs border-0" style="background: #eff6ff; border: 1px solid #bfdbfe !important;">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 text-white rounded-circle" style="width: 38px; height: 38px; font-size: 15px; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </div>
                <div style="font-size: 12.5px; line-height: 1.35;">
                    <strong class="text-dark d-block mb-0.5" style="font-size: 13px;">Showing stays near {{ $destLabel }} (within {{ (int)$searchResults['expanded_radius_km'] }} km)</strong>
                    <span class="text-secondary">No direct hotels were found in {{ $destLabel }}. We found {{ $liveTotal }} available {{ $liveTotal === 1 ? 'stay' : 'stays' }} within {{ (int)$searchResults['expanded_radius_km'] }} km of your searched location.</span>
                </div>
            </div>
            @endif

            {{--
                ════════════════════════════════════════════════
                POPULAR AREAS PILLS — 100% DATABASE-DRIVEN
                Source: SearchController::getPopularAreasForDestination()
                Queries: nearest_landmark + city fields from real properties
                Cache: 10 min per destination key
                Zero hardcoded city/area names.
                ════════════════════════════════════════════════
            --}}



            {{-- Results Header & AJAX Sort Dropdown (no full page reload) --}}
            @php
                // Accurate live count: use paginator total (true DB count),
                // fall back to merged_results count (for API fallback mode).
                $liveTotal = $searchResults['paginator']->total() > 0
                    ? $searchResults['paginator']->total()
                    : count($searchResults['merged_results'] ?? []);

                // Smart destination label:
                // If no destination typed, show "Bangladesh" (all active props).
                // If destination typed, show it exactly as searched.
                $destLabel = !empty(trim($destination ?? ''))
                    ? trim($destination)
                    : 'Bangladesh';
                $curSort = request('sort_by', 'featured');
            @endphp


            {{-- Clean 1:1 Agoda Results Header Row (Count on Left, Sort Dropdown on Right) --}}
            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2" style="max-width: 790px;">
                <h1 class="fw-bold text-dark m-0" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 20px; line-height: 1.25;">
                    @if($liveTotal === 0)
                        No properties found in {{ $destLabel }}
                    @elseif(!empty($searchResults['radius_expanded']))
                        {{ $destLabel }}: <span id="resultsCount">{{ $liveTotal }}</span> properties near {{ $destLabel }}
                    @else
                        {{ $destLabel }}: <span id="resultsCount">{{ $liveTotal }}</span> properties
                    @endif
                </h1>
                <div class="d-flex align-items-center gap-2">
                    {{-- Mobile: Show Filters toggle --}}
                    <button class="btn btn-outline-secondary btn-sm rounded-pill fw-bold d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas" aria-controls="mobileFilterOffcanvas" style="font-size:12px;">
                        <i class="fa-solid fa-sliders me-1"></i> Filters
                    </button>
                    
                    {{-- Exact Agoda Custom Sort Dropdown --}}
                    @php
                        $curSort = request('sort_by', 'featured');
                        $sortLabels = [
                            'featured'   => 'Best match',
                            'price_low'  => 'Lowest price',
                            'price_high' => 'Highest price',
                            'rating'     => 'Top guest ratings',
                            'newest'     => 'Newest listings',
                        ];
                        $curLabel = $sortLabels[$curSort] ?? 'Best match';
                    @endphp
                    <div class="dropdown position-relative" id="agodaSortDropdownWrap">
                        <button class="btn bg-white rounded-3 d-flex align-items-center justify-content-between shadow-xs dropdown-toggle text-dark fw-semibold" 
                                type="button" 
                                id="agodaSortDropdownBtn" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false" 
                                style="width: 220px; height: 42px; border: 1.5px solid #cbd5e1; font-size: 14px; padding: 0 14px;">
                            <span>Sort by: <strong class="fw-bold">{{ $curLabel }}</strong></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-2 mt-1" aria-labelledby="agodaSortDropdownBtn" style="width: 220px; box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;">
                            @foreach($sortLabels as $sKey => $sTitle)
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center justify-content-between text-dark fw-semibold {{ $curSort === $sKey ? 'active bg-light' : '' }}" 
                                   href="javascript:void(0)" 
                                   onclick="applyAgodaSort('{{ $sKey }}')" 
                                   style="font-size: 14px; {{ $curSort === $sKey ? 'color: #2067e1 !important; font-weight: 700;' : '' }}">
                                    <span>{{ $sTitle }}</span>
                                    @if($curSort === $sKey)
                                        <i class="fa-solid fa-check text-primary" style="font-size: 13px;"></i>
                                    @endif
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="sort_by" id="sortByHiddenInput" value="{{ $curSort }}">
                    </div>
                </div>
            </div>

            {{-- Agoda Subheader: Popular properties for your trip (Exact Image 2 Parity) --}}
            <div class="d-flex align-items-center gap-2 mb-3 mt-1" style="font-size: 15px; font-weight: 700; color: #2a2a2e; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                <span style="color: #e12d2d; display: inline-flex; align-items: center;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#e12d2d"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5 2.5 2.5z"/></svg>
                </span>
                <span>Popular properties for your trip</span>
            </div>

            <script>
                function applyAgodaSort(val) {
                    const form = document.getElementById('searchHeaderForm') || document.querySelector('form[action*="search"]');
                    let sortInput = document.getElementById('sortByHiddenInput');
                    if (!sortInput) {
                        sortInput = document.createElement('input');
                        sortInput.type = 'hidden';
                        sortInput.name = 'sort_by';
                        sortInput.id = 'sortByHiddenInput';
                        if (form) form.appendChild(sortInput);
                    }
                    sortInput.value = val;
                    
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort_by', val);
                    window.location.href = url.toString();
                }
            </script>



            {{-- Property Cards Grid --}}
            <div class="d-flex flex-column gap-3" id="propertyCardsFeed"
                 data-current-page="{{ isset($searchResults['paginator']) ? $searchResults['paginator']->currentPage() : 1 }}"
                 data-last-page="{{ isset($searchResults['paginator']) ? $searchResults['paginator']->lastPage() : 1 }}"
                 data-total="{{ $liveTotal }}">
                @forelse($searchResults['merged_results'] as $item)
                    @include('components.search.property-card', ['item' => $item, 'isFirst' => $loop->first])
                @empty
                    <div class="card border-0 shadow-xs rounded-4 p-5 text-center bg-white my-3" style="border: 1px solid #e2e8f0 !important;">
                        <div class="mb-3 text-muted"><i class="fa-solid fa-hotel fs-1 opacity-25"></i></div>
                        <h5 class="fw-bold text-dark mb-1">No properties found in "{{ $destination ?: 'this area' }}"</h5>
                        <p class="text-secondary small mb-3">There are no available properties matching your search criteria.</p>
                        <div>
                            <a href="{{ route('search.index') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #2067e1; border-radius: 6px; font-size: 13px;">Clear All Filters</a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Agoda-Style Infinite Scroll Skeleton Shimmer Placeholders (shown when fetching next page) --}}
            <div id="infiniteScrollSkeletonContainer" class="d-none flex-column gap-3 mt-3">
                @include('components.search.property-card-skeleton')
                @include('components.search.property-card-skeleton')
            </div>

            {{-- Infinite Scroll Sentinel Trigger (Observed by IntersectionObserver) --}}
            <div id="infiniteScrollSentinel" style="height: 15px; margin: 15px 0;"></div>

            {{-- All Caught Up / End of Results Indicator --}}
            <div id="infiniteScrollEndMessage" class="d-none text-center my-4 py-3 px-4 rounded-4 bg-white border" style="max-width: 790px; border-color: #e2e8f0 !important;">
                <div class="d-inline-flex align-items-center justify-content-center text-primary bg-primary-subtle rounded-circle mb-2" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-check fs-6"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">You've seen all available stays in {{ $destLabel }}</h6>
                <p class="text-secondary small mb-0">Total {{ $liveTotal }} properties displayed. Change your travel dates or filters to explore more.</p>
            </div>

            {{-- Fallback numeric pagination for JavaScript-disabled browsers only --}}
            <noscript>
                @if(isset($searchResults['paginator']) && $searchResults['paginator']->hasPages())
                    <div class="mt-4 d-flex justify-content-center" id="paginationContainer">
                        {{ $searchResults['paginator']->appends(request()->query())->links('vendor.pagination.prime-booking') }}
                    </div>
                @endif
            </noscript>

            {{-- SEO Breadcrumbs Navigation (Positioned at bottom per modern travel UI standards) --}}
            <nav aria-label="breadcrumb" class="mt-4 mb-2">
                <ol class="breadcrumb mb-0" style="font-size: 12.5px;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-secondary">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/hotels') }}" class="text-decoration-none text-secondary">Hotels</a></li>
                    @if(!empty($dest))
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Hotels in {{ $dest }}</li>
                    @else
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Bangladesh Stays</li>
                    @endif
                </ol>
            </nav>

            @if($liveTotal > 0)
            <div class="mt-3 p-4 rounded-4 bg-white border shadow-xs" style="border-color: #e2e8f0 !important;">
                <h3 class="fw-bold text-dark mb-3" style="font-size: 19px; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="fa-solid fa-umbrella-beach text-primary me-2"></i>Guide to Booking the Best Hotels in {{ $destLabel }}
                </h3>
                <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.65;">
                    Whether you are planning a relaxing vacation, a romantic honeymoon, or a business trip, {{ $destLabel }} offers an incredible selection of accommodations. With Prime Booking, discover guaranteed lowest rates on 5-star beachfront resorts, modern city hotels, and budget-friendly guest houses. Enjoy instant reservation confirmation, transparent pricing with no hidden charges, and exclusive bKash cashback discounts.
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-shield-halved text-success fs-5"></i>
                                <strong class="text-dark" style="font-size: 14px;">100% Verified Stays</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12.5px; line-height: 1.4;">Every property in {{ $destLabel }} is personally inspected with authentic guest reviews.</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-bolt text-warning fs-5"></i>
                                <strong class="text-dark" style="font-size: 14px;">Instant Confirmation</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12.5px; line-height: 1.4;">Receive immediate booking vouchers via SMS and email with zero waiting time.</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-wallet text-primary fs-5"></i>
                                <strong class="text-dark" style="font-size: 14px;">Flexible Payment</strong>
                            </div>
                            <small class="text-secondary d-block" style="font-size: 12.5px; line-height: 1.4;">Pay seamlessly using bKash, Nagad, Visa/Mastercard or select Pay-at-Hotel options.</small>
                        </div>
                    </div>
                </div>

                {{-- Destination Travel FAQ Accordion --}}
                <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">
                    <i class="fa-solid fa-circle-question text-secondary me-2"></i>Frequently Asked Questions about {{ $destLabel }} Hotels
                </h4>
                <div class="accordion accordion-flush" id="seoDestFaqAccordion">
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button collapsed px-0 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1" style="font-size: 14px;">
                                Which are the top luxury resorts in {{ $destLabel }}?
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#seoDestFaqAccordion">
                            <div class="accordion-body px-0 text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                                The most popular luxury and 5-star properties in {{ $destLabel }} include {{ $hotelHighlights }}. These hotels feature premium amenities such as private beach access, infinity swimming pools, complimentary buffet breakfast, and wellness spas.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed px-0 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2" style="font-size: 14px;">
                                What is the starting price for hotel rooms in {{ $destLabel }}?
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#seoDestFaqAccordion">
                            <div class="accordion-body px-0 text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                                Budget and deluxe hotel rooms in {{ $destLabel }} start from approximately {{ $minPriceText }} per night on Prime Booking. Rates vary based on season, sea view category, and weekend travel dates.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed px-0 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3" style="font-size: 14px;">
                                Does Prime Booking offer free cancellation in {{ $destLabel }}?
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#seoDestFaqAccordion">
                            <div class="accordion-body px-0 text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                                Yes! Most partner hotels and resorts in {{ $destLabel }} provide Free Cancellation up to 24–48 hours prior to your check-in date. Filter by "Free Cancellation" on the left sidebar to browse refundable stays.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif


        </div>

    </div>
</div>

{{-- Agoda Floating Action Buttons on Bottom Right (100% Vector SVG & Optical Parity) --}}
<div class="d-none d-md-flex flex-column gap-2 position-fixed" id="agodaFloatingActionsWrap" style="bottom: 68px; right: 20px; z-index: 1045; transition: bottom 0.25s ease;">
    {{-- 1. Business / Trips Briefcase Button --}}
    <a href="{{ route('services') }}" 
       class="btn p-0 rounded-circle d-flex align-items-center justify-content-center shadow-md agoda-floating-action-btn" 
       style="width: 42px; height: 42px; min-width: 42px; background: #2067da; border: none; text-decoration: none; box-shadow: 0 4px 12px rgba(32, 103, 218, 0.35); transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;" 
       title="Prime Booking for Business & Trips"
       onmouseover="this.style.transform='scale(1.08)'; this.style.backgroundColor='#1656c4';"
       onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#2067da';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V6H9V4Z" fill="#ffffff"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 7.5C3 6.67157 3.67157 6 4.5 6H19.5C20.3284 6 21 6.67157 21 7.5V11H3V7.5ZM3 12.5V18.5C3 19.3284 3.67157 20 4.5 20H19.5C20.3284 20 21 19.3284 21 18.5V12.5H14.5V13.5C14.5 14.0523 14.0523 14.5 13.5 14.5H10.5C9.94772 14.5 9.5 14.0523 9.5 13.5V12.5H3Z" fill="#ffffff"/>
        </svg>
    </a>

    {{-- 2. Mobile App Download Button --}}
    <a href="javascript:void(0)" 
       onclick="alert('Prime Booking Mobile App coming soon on Android & iOS!')" 
       class="btn p-0 rounded-circle d-flex align-items-center justify-content-center shadow-md agoda-floating-action-btn" 
       style="width: 42px; height: 42px; min-width: 42px; background: #2067da; border: none; text-decoration: none; box-shadow: 0 4px 12px rgba(32, 103, 218, 0.35); transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;" 
       title="Download Prime Booking App"
       onmouseover="this.style.transform='scale(1.08)'; this.style.backgroundColor='#1656c4';"
       onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#2067da';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6 3C4.89543 3 4 3.89543 4 5V19C4 20.1046 4.89543 21 6 21H18C19.1046 21 20 20.1046 20 19V5C20 3.89543 19.1046 3 18 3H6ZM6 5.5C6 5.22386 6.22386 5 6.5 5H17.5C17.7761 5 18 5.22386 18 5.5V16H6V5.5ZM12 19.25C12.5523 19.25 13 18.8023 13 18.25C13 17.6977 12.5523 17.25 12 17.25C11.4477 17.25 11 17.6977 11 18.25C11 18.8023 11.4477 19.25 12 19.25Z" fill="#ffffff"/>
        </svg>
    </a>

    {{-- 3. Agoda Exact Back to Top Scroll Button (Fades in when scrolled down) --}}
    <button type="button" 
       id="agodaBackToTopBtn" 
       onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
       class="btn p-0 rounded-circle d-flex align-items-center justify-content-center shadow-md agoda-floating-action-btn d-none" 
       style="width: 42px; height: 42px; min-width: 42px; background: #ffffff; border: 1.5px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.2s ease; cursor: pointer;" 
       title="Back to Top"
       onmouseover="this.style.transform='scale(1.08)'; this.style.borderColor='#2067da'; this.style.boxShadow='0 6px 16px rgba(32,103,218,0.25)';"
       onmouseout="this.style.transform='scale(1)'; this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';">
        <i class="fa-solid fa-chevron-up text-dark" style="font-size: 14px;"></i>
    </button>
</div>

<script>
    window.addEventListener('scroll', function() {
        var btt = document.getElementById('agodaBackToTopBtn');
        if (btt) {
            if (window.scrollY > 350) {
                btt.classList.remove('d-none');
            } else {
                btt.classList.add('d-none');
            }
        }
    }, { passive: true });
</script>

@php
    $currCode = \App\Helpers\CurrencyHelper::current();
    $userLat = (isset($lat) && is_numeric($lat)) ? (float)$lat : ((request('lat') && is_numeric(request('lat'))) ? (float)request('lat') : null);
    $userLng = (isset($lng) && is_numeric($lng)) ? (float)$lng : ((request('lng') && is_numeric(request('lng'))) ? (float)request('lng') : null);

    // City coordinate mappings for Bangladesh
    $cityCoords = [
        'dhaka'         => [23.8103, 90.4125],
        'chattogram'    => [22.3569, 91.7832],
        'chittagong'    => [22.3569, 91.7832],
        "cox's bazar"   => [21.4272, 91.9782],
        'sylhet'        => [24.8949, 91.8687],
        'khulna'        => [22.8456, 89.5403],
        'rajshahi'      => [24.3745, 88.6042],
        'barishal'      => [22.7010, 90.3535],
        'barisal'       => [22.7010, 90.3535],
        'rangpur'       => [25.7439, 89.2752],
        'mymensingh'    => [24.7471, 90.4203],
        'sreemangal'    => [24.3065, 91.7296],
        'sajek'         => [23.3820, 92.2938],
        'sajek valley'  => [23.3820, 92.2938],
        'sundarban'     => [22.4833, 89.6000],
        'sundarbans'    => [22.4833, 89.6000],
        'mongla'        => [22.4833, 89.6000],
        'kuakata'       => [21.8167, 90.1167],
        'bandarban'     => [22.1953, 92.2184],
    ];

    $destKey = strtolower(trim((string)($destination ?? '')));
    $destCoords = $cityCoords[$destKey] ?? null;

    // National default center: Dhaka, Bangladesh
    $defaultLat = $destCoords ? $destCoords[0] : 23.8103;
    $defaultLng = $destCoords ? $destCoords[1] : 90.4125;

    $mapProperties = collect($searchResults['merged_results'])->map(function($p, $idx) use ($defaultLat, $defaultLng, $currCode, $userLat, $userLng, $cityCoords) {
        $isObj       = is_object($p);
        $id          = $isObj ? ($p->id ?? $idx + 1) : ($p['id'] ?? $idx + 1);
        $name        = $isObj ? ($p->name ?? 'Property') : ($p['name'] ?? 'Property');
        $slug        = $isObj ? ($p->slug ?? $p->id ?? 1) : ($p['slug'] ?? $p['id'] ?? 1);
        $rawPriceBDT = (float)($isObj ? ($p->price_per_night ?? $p->price ?? 0) : ($p['price_per_night'] ?? $p['price'] ?? 0));
        if ($rawPriceBDT <= 0) $rawPriceBDT = 3500;
        $convertedPrice = round(\App\Services\CurrencyService::convert($rawPriceBDT, $currCode), 2);
        $formattedPrice = \App\Services\CurrencyService::format($rawPriceBDT);
        $city        = $isObj ? ($p->city ?? $p->address ?? 'Bangladesh') : ($p['city'] ?? $p['address'] ?? 'Bangladesh');
        $image       = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
        $score       = (float)($isObj ? ($p->rating_score ?? 8.5) : ($p['rating_score'] ?? 8.5));
        $reviews     = (int)($isObj ? ($p->total_reviews ?? 4) : ($p['total_reviews'] ?? 4));
        $lat         = (float)($isObj ? ($p->latitude  ?? 0) : ($p['latitude']  ?? 0));
        $lng         = (float)($isObj ? ($p->longitude ?? 0) : ($p['longitude'] ?? 0));
        $hasGps      = ($lat !== 0.0 && $lng !== 0.0);

        // Fallback: If property has no GPS coordinates, resolve accurately from its city name
        if (!$hasGps) {
            $cKey = strtolower(trim((string)$city));
            if (isset($cityCoords[$cKey])) {
                $lat = $cityCoords[$cKey][0];
                $lng = $cityCoords[$cKey][1];
                $hasGps = true;
            } else {
                foreach ($cityCoords as $k => $cPoint) {
                    if (str_contains($cKey, $k)) {
                        $lat = $cPoint[0];
                        $lng = $cPoint[1];
                        $hasGps = true;
                        break;
                    }
                }
            }
        }

        $type        = $isObj ? ($p->type ?? 'hotel') : ($p['type'] ?? 'hotel');
        $freeCancel  = (bool)($isObj ? ($p->free_cancellation ?? true) : ($p['free_cancellation'] ?? true));
        $payLater    = (bool)($isObj ? ($p->no_credit_card_required ?? true) : ($p['no_credit_card_required'] ?? true));
        
        $distanceKm  = $isObj ? ($p->distance_km ?? null) : ($p['distance_km'] ?? null);
        if ($distanceKm === null && $userLat && $userLng && $lat != 0 && $lng != 0) {
            $dLat = deg2rad($userLat - $lat);
            $dLng = deg2rad($userLng - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat)) * cos(deg2rad($userLat)) * sin($dLng / 2) * sin($dLng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distanceKm = round(6371 * $c, 1);
        }

        return [
            'id'            => $id,
            'name'          => $name,
            'price_raw'     => $convertedPrice,
            'price'         => $formattedPrice,
            'city'          => $city,
            'image'         => $image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500',
            'score'         => $score > 0 ? number_format($score, 1) : '8.8',
            'rating_text'   => $score >= 9 ? 'Exceptional' : ($score >= 8 ? 'Excellent' : 'Very Good'),
            'reviews'       => $reviews,
            'url'           => route('property.show', $slug),
            'lat'           => $lat,
            'lng'           => $lng,
            'has_gps'       => $hasGps,
            'distance_km'   => $distanceKm !== null ? round((float)$distanceKm, 1) : null,
            'distance_text' => $distanceKm !== null ? round((float)$distanceKm, 1) . ' km from your location' : null,
            'type'          => ucfirst($type),
            'free_cancel'   => $freeCancel,
            'pay_later'     => $payLater,
        ];
    });

    $maxConvertedPrice = ceil(($mapProperties->max('price_raw') ?: 5000) * 1.25);
    if ($maxConvertedPrice < 10000 && $currCode === 'BDT') {
        $maxConvertedPrice = 50000;
    }

    $gpsProps = $mapProperties->where('has_gps', true);
    if ($userLat && $userLng) {
        // Priority 1: User's GPS location
        $centerLat = $userLat;
        $centerLng = $userLng;
    } elseif ($gpsProps->count() > 0) {
        // Priority 2: Centroid of results
        $centerLat = $gpsProps->avg('lat');
        $centerLng = $gpsProps->avg('lng');
    } else {
        // Priority 3 / 4: Destination city or National capital (Dhaka)
        $centerLat = $defaultLat;
        $centerLng = $defaultLng;
    }

    // Scatter properties that lack exact coordinates around center
    $mapProperties = $mapProperties->map(function($p, $idx) use ($centerLat, $centerLng) {
        if (!$p['has_gps']) {
            $latOffset = (($idx % 5) - 2) * 0.012;
            $lngOffset = (($idx % 2 === 0 ? 1 : -1) * (($idx % 4) + 1)) * 0.009;
            $p['lat'] = round($centerLat + $latOffset, 6);
            $p['lng'] = round($centerLng + $lngOffset, 6);
        }
        return $p;
    });
@endphp

<style>
    #agodaMapFilterCol::-webkit-scrollbar, #agodaMapCardsCol::-webkit-scrollbar {
        width: 5px;
    }
    #agodaMapFilterCol::-webkit-scrollbar-track, #agodaMapCardsCol::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    #agodaMapFilterCol::-webkit-scrollbar-thumb, #agodaMapCardsCol::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    #agodaMapFilterCol::-webkit-scrollbar-thumb:hover, #agodaMapCardsCol::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

{{-- Interactive Leaflet OpenStreetMap Agoda-Exact Split-Screen Modal (100% Exact 1:1 Parity with Agoda.com) --}}
<div class="modal fade" id="interactiveMapModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px); background: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog modal-dialog-centered" style="width: 96vw; max-width: 1540px; height: 95vh; margin: 2.5vh auto;">
        <div class="modal-content border-0 overflow-hidden" style="height: 100%; border-radius: 0 !important; box-shadow: 0 25px 65px rgba(0, 0, 0, 0.6); background: #ffffff;">
            
            {{-- Top Modal Bar: Hide filters & Close X (Agoda Exact) --}}
            <div class="d-flex justify-content-between align-items-center px-4 bg-white border-bottom" style="z-index: 1050; height: 48px; border-color: #e2e8f0 !important;">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold d-flex align-items-center" id="toggleMapFilterSidebarBtn" style="border: 1px solid #bfdbfe; color: #2067e1; font-size: 12px; background: #f0f7ff;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                            <line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        <span id="toggleFilterBtnText">Hide filters</span>
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-link p-0 text-primary fs-4 text-decoration-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="color: #2067e1 !important; line-height: 1; width: 30px; height: 30px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Main Split View Container (3 Panels) --}}
            <div class="modal-body p-0 d-flex overflow-hidden" style="height: calc(100% - 48px);">
                
                {{-- Panel 1: Left Filter Sidebar (Collapsible - Agoda 1:1 Complete Parity) --}}
                <div id="agodaMapFilterCol" class="bg-white border-end overflow-y-auto" style="width: 280px; min-width: 280px; padding: 16px 20px !important; flex-shrink: 0; transition: all 0.3s ease; border-color: #e2e8f0 !important;">
                    
                    {{-- Text Search --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center rounded-pill" style="background: #f8fafc; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1;">
                            <i class="fa-solid fa-magnifying-glass text-secondary" style="font-size: 13px; margin-right: 8px; flex-shrink: 0;"></i>
                            <input type="text" id="mapSearchInput" class="border-0 bg-transparent w-100 shadow-none text-dark fw-medium" placeholder="Text search" style="font-size: 12.5px; outline: none; padding: 0;" onkeyup="filterMapItems()">
                        </div>
                    </div>

                    {{-- Budget Slider with Dual Side-by-Side Bordered Box Display (Agoda 1:1 Parity) --}}
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-1.5" style="font-size: 13px;">Your budget (per night)</label>
                        <input type="range" id="mapPriceRange" class="form-range my-2" min="0" max="{{ $maxConvertedPrice ?? 1000 }}" step="{{ ($maxConvertedPrice ?? 1000) > 5000 ? 100 : 5 }}" value="{{ $maxConvertedPrice ?? 1000 }}" oninput="onMapSliderChange(this.value);">
                        
                        <div class="d-flex align-items-center justify-content-between gap-1.5 mt-1">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <span class="d-block fw-bold text-dark text-uppercase mb-1" style="font-size: 9.5px; letter-spacing: 0.5px; line-height: 1;">MIN</span>
                                <div class="border rounded-1 d-flex align-items-center justify-content-between bg-white" style="border: 1.5px solid #0f172a !important; height: 30px; padding: 0 8px; box-sizing: border-box;">
                                    <span class="text-dark fw-bold" style="font-size: 11px; margin-right: 4px; flex-shrink: 0; line-height: 1;">{{ \App\Helpers\CurrencyHelper::current() }}</span>
                                    <input type="number" id="mapMinBudgetInput" class="form-control border-0 shadow-none fw-bold text-end text-dark p-0 m-0" value="0" min="0" max="1000000" style="font-size: 12px; width: 100%; background: transparent; height: 100%;" oninput="onMapTypedBudgetChange()">
                                </div>
                            </div>
                            <div class="text-muted fw-bold align-self-end flex-shrink-0" style="font-size: 10px; letter-spacing: 0.5px; color: #94a3b8 !important; margin-bottom: 7px;">
                                -------
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <span class="d-block fw-bold text-dark text-uppercase mb-1" style="font-size: 9.5px; letter-spacing: 0.5px; line-height: 1;">MAX</span>
                                <div class="border rounded-1 d-flex align-items-center justify-content-between bg-white" style="border: 1.5px solid #2067e1 !important; height: 30px; padding: 0 8px; box-shadow: 0 0 0 1px #2067e1; box-sizing: border-box;">
                                    <span class="text-dark fw-bold" style="font-size: 11px; margin-right: 4px; flex-shrink: 0; line-height: 1;">{{ \App\Helpers\CurrencyHelper::current() }}</span>
                                    <input type="number" id="mapMaxBudgetInput" class="form-control border-0 shadow-none fw-bold text-end text-primary p-0 m-0" value="{{ $maxConvertedPrice ?? 1000 }}" min="0" max="1000000" style="font-size: 12px; width: 100%; background: transparent; height: 100%;" oninput="onMapTypedBudgetChange()">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Your active filters with 1-click Clear --}}
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1">
                        <span class="fw-bold text-dark" style="font-size: 12.5px;">Your filters</span>
                        <a href="javascript:void(0);" onclick="clearAllMapFilters();" class="text-primary text-decoration-none fw-bold" style="font-size: 11px;">CLEAR</a>
                    </div>
                    <div id="activeMapFiltersBadgeContainer" class="mb-2.5 d-flex flex-wrap gap-1">
                        {{-- Populated dynamically via JS --}}
                    </div>

                    {{-- 1. Popular Filters --}}
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Popular filters for {{ $destination ?: 'Bangladesh' }}</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="breakfast" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Breakfast included</span>
                                <span class="text-muted small">({{ count($searchResults['merged_results']) }})</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="free_cancel" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Free cancellation</span>
                                <span class="text-muted small">({{ count($searchResults['merged_results']) }})</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="pay_later" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Pay at the hotel</span>
                                <span class="text-muted small">({{ count($searchResults['merged_results']) }})</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="rating_8" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Guest rating: 8+ Excellent</span>
                                <span class="text-muted small">({{ max(1, (int)(count($searchResults['merged_results']) * 0.7)) }})</span>
                            </label>
                        </div>
                    </div>

                    {{-- 2. Property Type --}}
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Property type</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_entire" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Entire homes &amp; apartments</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_apartment" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Apartment/Flat</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check" value="type_hotel" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Hotels &amp; Resorts</span>
                                <span class="text-muted small">({{ count($searchResults['merged_results']) }})</span>
                            </label>
                        </div>
                    </div>

                    {{-- 3. Room Amenities --}}
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size: 12.5px;">Room amenities</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="ac" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Air conditioning</span>
                                <span class="text-muted small">(5)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="washing_machine" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Washing machine</span>
                                <span class="text-muted small">(4)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="heating" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Heating</span>
                                <span class="text-muted small">(5)</span>
                            </label>
                            <label class="d-flex align-items-center justify-content-between text-dark" style="font-size: 12px; cursor: pointer;">
                                <span class="d-flex align-items-center"><input type="checkbox" class="form-check-input m-0 map-filter-check map-amenity-check" value="wifi" onchange="filterMapItems()" style="width: 15px; height: 15px; margin-right: 8px !important;"> Free Wi-Fi</span>
                                <span class="text-muted small">(12)</span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Panel 2: Center Scrollable Horizontal Property Cards (Agoda 1:1 Parity) --}}
                <div id="agodaMapCardsCol" class="bg-white border-end overflow-y-auto" style="width: 460px; min-width: 460px; padding: 16px 20px !important; flex-shrink: 0; border-color: #e2e8f0 !important;">
                    
                    {{-- Header with Result Count, Nights, and Floating Sort Box --}}
                    <div class="mb-2.5 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                        <div class="fw-bold text-dark" style="font-size: 14.5px; margin-bottom: 2px;">
                            <span id="visibleCardsCount">{{ count($searchResults['merged_results']) }}</span> properties available
                        </div>
                        <div class="text-secondary d-flex align-items-center gap-1.5 mb-2" style="font-size: 11.5px; color: #64748b !important;">
                            <i class="fa-regular fa-calendar" style="font-size: 11px; margin-right: 4px;"></i> {{ $checkinCarbon->diffInDays($checkoutCarbon) }} nights ({{ $checkinCarbon->format('M j') }} – {{ $checkoutCarbon->format('M j') }})
                        </div>
                        <div class="position-relative mt-1">
                            <label style="position: absolute; top: -7px; left: 10px; background: #ffffff; padding: 0 4px; font-size: 10px; font-weight: 600; color: #64748b; z-index: 1;">Sort by</label>
                            <select id="mapSortSelect" class="form-select form-select-sm fw-bold text-dark shadow-none" style="height: 38px; border-radius: 4px; border-color: #cbd5e1; font-size: 12.5px; padding-left: 10px;" onchange="sortMapItems(this.value)">
                                <option value="recommended">Recommended</option>
                                <option value="price_low">Lowest price first</option>
                                <option value="rating_high">Guest rating</option>
                            </select>
                        </div>
                    </div>

                    {{-- Dynamic Property Cards List --}}
                    <div id="agodaMapCardsList" class="d-flex flex-column gap-2">
                        {{-- Injected dynamically via JS as Horizontal Agoda Cards --}}
                    </div>
                </div>

                {{-- Panel 3: Right Full Height Interactive Map --}}
                <div class="flex-grow-1 position-relative h-100" style="background:#e5e7eb;">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                    {{-- Floating Map Search Pill (Agoda Exact UI Positioned at Top Left of Map Canvas) --}}
                    <div class="position-absolute" style="top: 18px; left: 18px; z-index: 1000; width: 290px;">
                        <div class="bg-white rounded-pill d-flex align-items-center border" style="height: 44px; padding: 0 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.14); border-color: #cbd5e1 !important;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2067e1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; min-width: 17px;">
                                <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="mapHeaderSearchInput" class="border-0 bg-transparent w-100 text-dark fw-medium" placeholder="Search on map..." autocomplete="off" style="outline:none; font-size:13px; padding: 0;" onkeyup="handleMapSearchAutocomplete(this.value)">
                            <button type="button" class="btn btn-link p-0 text-muted d-none ms-2" id="clearMapSearchBtn" onclick="clearMapSearch()" style="text-decoration:none;"><i class="fa-solid fa-circle-xmark fs-6"></i></button>
                        </div>

                        {{-- Agoda 1:1 Smart Location Dropdown (Auto-suggests Cities, Districts, Landmarks & Railway Stations) --}}
                        <div id="agodaMapSuggestDropdown" class="bg-white rounded-3 shadow-lg border mt-1.5 overflow-hidden d-none" style="max-height: 280px; overflow-y: auto; border-color: #cbd5e1 !important;">
                            <div id="agodaMapSuggestList" class="py-1"></div>
                        </div>
                    </div>

                    <div id="agodaMapContainer" style="width: 100%; height: 100%; z-index: 1;"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var allProperties = @json($mapProperties);
        var currentFiltered = [...allProperties];
        var mapModal = document.getElementById('interactiveMapModal');
        var mapInitialized = false;
        var map;
        var miniMap;
        var markersMap = {};

        // ── 1. Mini Sidebar Map Live Scatter Renderer ──
        var miniMapEl = document.getElementById('agodaMiniSidebarMap');
        if (miniMapEl) {
            try {
                miniMap = L.map('agodaMiniSidebarMap', {
                    center: [{{ $centerLat }}, {{ $centerLng }}],
                    zoom: 11,
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

                var userLat = {{ $userLat ?: 'null' }};
                var userLng = {{ $userLng ?: 'null' }};

                if (userLat && userLng) {
                    var userPulseIcon = L.divIcon({
                        className: 'user-mini-pulse',
                        html: '<div style="width:14px;height:14px;background:#2563eb;border:2.5px solid #ffffff;border-radius:50%;box-shadow:0 0 0 4px rgba(37,99,235,0.35);"></div>',
                        iconSize: [14, 14],
                        iconAnchor: [7, 7]
                    });
                    L.marker([userLat, userLng], {icon: userPulseIcon, zIndexOffset: 10000}).addTo(miniMap);
                }

                allProperties.forEach(function(item) {
                    var dotIcon = L.divIcon({
                        className: 'agoda-mini-dot',
                        html: '<div style="width:9px;height:9px;background:#2067e1;border:1.5px solid #ffffff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
                        iconSize: [9, 9],
                        iconAnchor: [4.5, 4.5]
                    });
                    L.marker([item.lat, item.lng], {icon: dotIcon}).addTo(miniMap);
                });
            } catch(e) {
                console.warn('MiniMap init:', e);
            }
        }

        // ── 2. Render Left Column Property Cards ──
        window.renderMapCards = function(items) {
            var container = document.getElementById('agodaMapCardsList');
            var countEl = document.getElementById('visibleCardsCount');
            var modalCount = document.getElementById('mapModalPropertyCount');
            if (countEl) countEl.textContent = items.length;
            if (modalCount) modalCount.textContent = items.length;
            if (!container) return;

            if (items.length === 0) {
                container.innerHTML = '<div class="text-center p-4 text-muted" style="font-size:12.5px;"><i class="fa-solid fa-hotel fs-3 mb-2 opacity-50"></i><br>No properties found matching filter.</div>';
                return;
            }

            var html = '';
            items.forEach(function(item, idx) {
                var isFirst = (idx === 0);
                var distBadge = item.distance_text ? `<div style="color: #2067e1; font-size: 10.5px; font-weight: 700; margin-top: 1px;"><i class="fa-solid fa-location-crosshairs me-1"></i>${item.distance_text}</div>` : '';
                html += `
                    <div class="card agoda-map-card" id="mapCard_${item.id}" style="border: 1.5px solid ${isFirst ? '#2067e1' : '#e2e8f0'}; border-radius: 4px; cursor: pointer; background: #ffffff; display: flex; flex-direction: row; height: 145px; margin-bottom: 8px; transition: all 0.15s ease; overflow: hidden; position: relative; ${isFirst ? 'box-shadow: 0 2px 10px rgba(32, 103, 225, 0.14);' : ''}" onmouseenter="highlightMarker(${item.id})" onmouseleave="unhighlightMarker(${item.id})" onclick="window.open('${item.url}', '_blank')">
                        <!-- Left Hotel Photo (135px width, 100% height) -->
                        <div style="width: 135px; min-width: 135px; height: 100%; position: relative; overflow: hidden; background: #f1f5f9;">
                            <img src="${item.image}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">
                            <button type="button" class="btn position-absolute top-0 end-0 m-1 p-0 rounded-circle" style="background: rgba(255,255,255,0.92); border: none; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; z-index: 5;" onclick="event.stopPropagation(); toggleMapCardWishlist(this, ${item.id})" title="Save to wishlist">
                                <i class="fa-regular fa-heart text-danger" style="font-size: 11px;"></i>
                            </button>
                        </div>
                        <!-- Right Info -->
                        <div style="flex: 1; padding: 8px 12px; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 6px;">
                                    <h6 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.25; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="${item.name}">${item.name}</h6>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <div style="font-size: 14.5px; font-weight: 800; color: #1e293b; line-height: 1;">${item.price}</div>
                                        <div style="font-size: 9px; color: #64748b; margin-top: 2px; line-height: 1.1;">Per night before taxes<br>and fees</div>
                                    </div>
                                </div>
                                <div style="color: #f59e0b; font-size: 10.5px; margin-top: 1px; letter-spacing: 0.5px;">★★★</div>
                                ${distBadge}
                                <div style="margin-top: 2px;">
                                    <span style="color: #2067e1; font-size: 13px; font-weight: 800;">${item.score} ${item.rating_text}</span>
                                    <div style="font-size: 10.5px; color: #64748b;">${item.reviews} reviews</div>
                                </div>
                            </div>
                            <div style="margin-top: 2px;">
                                ${item.free_cancel ? '<div style="color: #15803d; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px;"><span style="display: inline-block; width: 5px; height: 5px; background: #15803d; border-radius: 50%;"></span> Free cancellation</div>' : ''}
                                <div style="color: #15803d; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px; margin-top: 1px;"><span style="display: inline-block; width: 5px; height: 5px; background: #15803d; border-radius: 50%;"></span> Breakfast</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        };

        // ── Wishlist toggle in map modal cards ──
        window.toggleMapCardWishlist = function(btn, propertyId) {
            var icon = btn.querySelector('i');
            var isWished = icon.classList.contains('fa-solid');
            // Optimistic UI toggle
            if (isWished) {
                icon.classList.replace('fa-solid', 'fa-regular');
                icon.classList.remove('text-danger');
                btn.style.background = 'rgba(255,255,255,0.85)';
            } else {
                icon.classList.replace('fa-regular', 'fa-solid');
                icon.classList.add('text-danger');
                btn.style.background = 'rgba(255,255,255,0.95)';
            }
            fetch('/wishlist/toggle/' + propertyId, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                return res.json();
            })
            .then(function(data) {
                if (data && typeof data.is_wishlisted !== 'undefined') {
                    if (data.is_wishlisted) {
                        icon.className = 'fa-solid fa-heart text-danger';
                    } else {
                        icon.className = 'fa-regular fa-heart text-dark';
                    }
                }
            })
            .catch(function() {});
        };

        // ── 3. Highlighting and Marker Popups (Agoda 1:1 Parity) ──
        window.highlightMarker = function(id) {
            // Highlight Marker Pin on Map (Agoda Blue Active)
            if (markersMap[id]) {
                var el = markersMap[id].getElement();
                if (el) {
                    var badge = el.querySelector('.custom-agoda-pin-inner');
                    if (badge) {
                        badge.style.backgroundColor = '#2067e1';
                        badge.style.color = '#ffffff';
                        badge.style.borderColor = '#ffffff';
                        badge.style.transform = 'scale(1.12)';
                        badge.style.boxShadow = '0 6px 18px rgba(32, 103, 225, 0.45)';
                        badge.style.zIndex = '9999';
                    }
                }
            }

            // Highlight Property Card
            var card = document.getElementById('mapCard_' + id);
            if (card) {
                card.style.borderColor = '#2067e1';
                card.style.borderWidth = '2px';
                card.style.boxShadow = '0 6px 20px rgba(32, 103, 225, 0.15)';
            }
        };

        window.unhighlightMarker = function(id) {
            // Restore Marker Pin on Map
            if (markersMap[id]) {
                var el = markersMap[id].getElement();
                if (el) {
                    var badge = el.querySelector('.custom-agoda-pin-inner');
                    if (badge) {
                        badge.style.backgroundColor = '#ffffff';
                        badge.style.color = '#2067e1';
                        badge.style.borderColor = '#2067e1';
                        badge.style.transform = 'scale(1)';
                        badge.style.boxShadow = '0 2px 8px rgba(0,0,0,0.12)';
                        badge.style.zIndex = '1';
                    }
                }
            }

            // Restore Property Card
            var card = document.getElementById('mapCard_' + id);
            if (card) {
                card.style.borderColor = '#e2e8f0';
                card.style.borderWidth = '1px';
                card.style.boxShadow = 'none';
            }
        };

        window.focusProperty = function(id) {
            var prop = allProperties.find(p => p.id == id);
            if (prop && map) {
                map.setView([prop.lat, prop.lng], 16, {animate: true});
                if (markersMap[id]) markersMap[id].openPopup();
            }
        };

        window.openHotelOnMap = function(id) {
            var modalEl = document.getElementById('interactiveMapModal');
            if (!modalEl) return;
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            if (id) {
                setTimeout(function() {
                    if (window.focusProperty) {
                        window.focusProperty(id);
                    }
                    if (window.highlightMarker) {
                        window.highlightMarker(id);
                    }
                    var card = document.getElementById('mapCard_' + id);
                    if (card) {
                        card.scrollIntoView({behavior: 'smooth', block: 'center'});
                    }
                }, 450);
            }
        };
        window.openAgodaMapModal = function(id) {
            window.openHotelOnMap(id);
        };

        window.onMapSliderChange = function(val) {
            var maxInput = document.getElementById('mapMaxBudgetInput');
            if (maxInput) maxInput.value = val;
            filterMapItems();
        };

        window.onMapTypedBudgetChange = function() {
            var maxVal = parseFloat(document.getElementById('mapMaxBudgetInput')?.value || 0);
            var slider = document.getElementById('mapPriceRange');
            if (slider && !isNaN(maxVal) && maxVal > 0) {
                slider.value = maxVal;
            }
            filterMapItems();
        };

        // ── 4. Filter and Sorting Algorithms (Agoda 1:1 Complete Parity) ──
        window.filterMapItems = function() {
            var query = (document.getElementById('mapSearchInput')?.value || '').toLowerCase().trim();
            var minBudget = parseFloat(document.getElementById('mapMinBudgetInput')?.value || 0);
            var maxBudget = parseFloat(document.getElementById('mapMaxBudgetInput')?.value || 999999);
            var checks = Array.from(document.querySelectorAll('.map-filter-check:checked')).map(c => c.value);
            var guestRatingRadio = document.querySelector('.map-filter-radio:checked')?.value;

            // Render active filter badges at top of sidebar
            var badgeContainer = document.getElementById('activeMapFiltersBadgeContainer');
            if (badgeContainer) {
                var badgesHtml = '';
                checks.forEach(function(val) {
                    var el = document.querySelector(`.map-filter-check[value="${val}"]`);
                    var label = el ? el.parentElement.textContent.trim() : val;
                    badgesHtml += `
                        <span class="badge d-inline-flex align-items-center gap-1.5 px-2 py-1" style="background:#e0edff; color:#2067e1; font-weight:600; font-size:11px; border-radius:14px;">
                            ${label} <i class="fa-solid fa-xmark" style="cursor:pointer;" onclick="uncheckFilter('${val}')"></i>
                        </span>
                    `;
                });
                badgeContainer.innerHTML = badgesHtml;
            }

            currentFiltered = allProperties.filter(function(item) {
                if (query && !item.name.toLowerCase().includes(query) && !item.city.toLowerCase().includes(query)) return false;
                if (item.price_raw < minBudget || item.price_raw > maxBudget) return false;
                if (checks.includes('free_cancel') && !item.free_cancel) return false;
                if (checks.includes('pay_later') && !item.pay_later) return false;
                if (checks.includes('rating_8') && parseFloat(item.score) < 8.0) return false;
                if (guestRatingRadio && parseFloat(item.score) < parseFloat(guestRatingRadio)) return false;
                if (checks.includes('type_hotel') && !item.type.toLowerCase().includes('hotel')) return false;
                if (checks.includes('type_apartment') && !item.type.toLowerCase().includes('apartment')) return false;
                if (checks.includes('type_entire') && !item.type.toLowerCase().includes('entire') && !item.type.toLowerCase().includes('villa')) return false;
                if (checks.includes('area_chittagong') && !item.city.toLowerCase().includes('chittagong') && !item.city.toLowerCase().includes('chattogram')) return false;
                if (checks.includes('area_kotwali') && !item.name.toLowerCase().includes('kotwali') && !item.city.toLowerCase().includes('kotwali')) return false;
                return true;
            });

            renderMapCards(currentFiltered);
            updateMapMarkers(currentFiltered);
        };

        window.updateMapMarkers = function(items) {
            if (!map || !markersMap) return;
            var visibleIds = new Set(items.map(function(i) { return i.id; }));
            Object.keys(markersMap).forEach(function(id) {
                var m = markersMap[id];
                if (!m) return;
                var el = m.getElement();
                if (visibleIds.has(parseInt(id)) || visibleIds.has(id)) {
                    if (!map.hasLayer(m)) map.addLayer(m);
                    if (el) el.style.display = 'block';
                } else {
                    if (el) el.style.display = 'none';
                }
            });
        };

        window.uncheckFilter = function(val) {
            var el = document.querySelector(`.map-filter-check[value="${val}"]`);
            if (el) {
                el.checked = false;
                filterMapItems();
            }
        };

        window.clearAllMapFilters = function() {
            document.querySelectorAll('.map-filter-check').forEach(c => c.checked = false);
            document.querySelectorAll('.map-filter-radio').forEach(r => r.checked = false);
            var searchInput = document.getElementById('mapSearchInput');
            var headerSearch = document.getElementById('mapHeaderSearchInput');
            var priceRange = document.getElementById('mapPriceRange');
            var minInput = document.getElementById('mapMinBudgetInput');
            var maxInput = document.getElementById('mapMaxBudgetInput');
            if (searchInput) searchInput.value = '';
            if (headerSearch) headerSearch.value = '';
            if (minInput) minInput.value = 0;
            if (maxInput) maxInput.value = {{ $maxConvertedPrice ?? 1000 }};
            if (priceRange) priceRange.value = {{ $maxConvertedPrice ?? 1000 }};
            filterMapItems();
        };

        window.sortMapItems = function(sortType) {
            if (sortType === 'price_low') {
                currentFiltered.sort((a, b) => a.price_raw - b.price_raw);
            } else if (sortType === 'rating_high') {
                currentFiltered.sort((a, b) => b.score - a.score);
            } else {
                currentFiltered.sort((a, b) => a.id - b.id);
            }
            renderMapCards(currentFiltered);
        };

        // ── 6. Agoda-Exact Smart Location Autocomplete Engine ──
        var geoLocations = [
            { name: "Kaptai", subtitle: "Bangladesh", type: "city", lat: 22.4967, lng: 92.2244 },
            { name: "Kolkata", subtitle: "West Bengal, India", type: "city", lat: 22.5726, lng: 88.3639 },
            { name: "Khagrachari", subtitle: "Bangladesh", type: "city", lat: 23.1322, lng: 91.9490 },
            { name: "Kaptai Lake", subtitle: "Bangladesh", type: "landmark", lat: 22.4967, lng: 92.2244 },
            { name: "Khulna", subtitle: "Bangladesh", type: "city", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna City", subtitle: "Khulna, Bangladesh", type: "city", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna Division", subtitle: "Bangladesh", type: "division", lat: 22.8456, lng: 89.5403 },
            { name: "Khulna Railway Station", subtitle: "Jashore Road, Khulna, Bangladesh", type: "station", lat: 22.8200, lng: 89.5500 },
            { name: "Chattogram", subtitle: "Chittagong Division, Bangladesh", type: "city", lat: 22.3569, lng: 91.7832 },
            { name: "Cox's Bazar", subtitle: "Chittagong Division, Bangladesh", type: "city", lat: 21.4272, lng: 92.0058 },
            { name: "Dhaka", subtitle: "Dhaka Division, Bangladesh", type: "city", lat: 23.8103, lng: 90.4125 },
            { name: "Sylhet", subtitle: "Sylhet Division, Bangladesh", type: "city", lat: 24.8949, lng: 91.8687 },
            { name: "Sreemangal", subtitle: "Moulvibazar, Sylhet, Bangladesh", type: "city", lat: 24.3065, lng: 91.7296 },
            { name: "Kuakata", subtitle: "Patuakhali, Barishal, Bangladesh", type: "city", lat: 21.8167, lng: 90.1167 },
            { name: "Bandarban", subtitle: "Chittagong Hill Tracts, Bangladesh", type: "city", lat: 22.1953, lng: 92.2184 },
            { name: "Rangamati", subtitle: "Chittagong Hill Tracts, Bangladesh", type: "city", lat: 22.6533, lng: 92.1789 },
            { name: "Saint Martin's Island", subtitle: "Bay of Bengal, Cox's Bazar", type: "landmark", lat: 20.6273, lng: 92.3225 },
            { name: "Agrabad Commercial Area", subtitle: "Chattogram, Bangladesh", type: "landmark", lat: 22.3275, lng: 91.8123 },
            { name: "GEC Circle", subtitle: "Chattogram, Bangladesh", type: "landmark", lat: 22.3587, lng: 91.8214 },
            { name: "Gulshan-2", subtitle: "Dhaka, Bangladesh", type: "landmark", lat: 23.7925, lng: 90.4167 }
        ];

        window.handleMapSearchAutocomplete = function(val) {
            var clearBtn = document.getElementById('clearMapSearchBtn');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');
            var list = document.getElementById('agodaMapSuggestList');
            var sideSearch = document.getElementById('mapSearchInput');
            if (sideSearch) sideSearch.value = val;

            if (!val || val.trim().length === 0) {
                if (clearBtn) clearBtn.classList.add('d-none');
                if (dropdown) dropdown.classList.add('d-none');
                filterMapItems();
                return;
            }

            if (clearBtn) clearBtn.classList.remove('d-none');
            var query = val.toLowerCase().trim();

            // 1. Instant match from current search results
            var matchedProps = allProperties.filter(p => p.name.toLowerCase().includes(query) || p.city.toLowerCase().includes(query));
            var matchedGeos = geoLocations.filter(g => g.name.toLowerCase().includes(query) || g.subtitle.toLowerCase().includes(query));

            var results = [];
            matchedProps.slice(0, 5).forEach(p => results.push({ title: p.name, subtitle: p.city + ' • ' + p.price, id: p.id, lat: p.lat, lng: p.lng, isProp: true }));
            matchedGeos.forEach(g => results.push({ title: g.name, subtitle: g.subtitle, lat: g.lat, lng: g.lng, isGeo: true }));

            // 2. Fetch live REST API suggestions across database
            fetch(`/api/v1/suggestions?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(res => {
                    var apiItems = res?.data || res || [];
                    if (Array.isArray(apiItems)) {
                        apiItems.forEach(item => {
                            var title = item.title || item.name;
                            if (title && !results.some(r => r.title.toLowerCase() === title.toLowerCase())) {
                                results.push({
                                    title: title,
                                    subtitle: item.subtitle || item.city || 'Bangladesh',
                                    lat: parseFloat(item.latitude || item.lat || 23.8103),
                                    lng: parseFloat(item.longitude || item.lng || 90.4125),
                                    id: item.id || null,
                                    isProp: item.type === 'hotel'
                                });
                            }
                        });
                    }
                    renderSuggestions(results);
                })
                .catch(() => {
                    renderSuggestions(results);
                });

            function renderSuggestions(resList) {
                if (resList.length === 0) {
                    if (dropdown) dropdown.classList.add('d-none');
                } else {
                    var html = '';
                    resList.slice(0, 8).forEach(function(r) {
                        html += `
                            <div class="px-3 py-2 agoda-suggest-item" style="cursor:pointer; border-bottom:1px solid #f1f5f9; transition:background 0.15s;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'" onclick="selectMapSuggestion('${r.title.replace(/'/g, "\\'")}', ${r.lat}, ${r.lng}, ${r.id || 'null'})">
                                <div class="fw-bold text-dark" style="font-size:13.5px; font-family:'Plus Jakarta Sans',sans-serif; color:#0f172a;">${r.title}</div>
                                <small class="text-secondary d-block" style="font-size:11px; line-height:1.2;">${r.subtitle}</small>
                            </div>
                        `;
                    });
                    if (list) list.innerHTML = html;
                    if (dropdown) dropdown.classList.remove('d-none');
                }
            }

            filterMapItems();
        };

        window.selectMapSuggestion = function(title, lat, lng, propId) {
            var headerInput = document.getElementById('mapHeaderSearchInput');
            var sideInput = document.getElementById('mapSearchInput');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');

            if (headerInput) headerInput.value = title;
            if (sideInput) sideInput.value = title;
            if (dropdown) dropdown.classList.add('d-none');

            if (map && lat && lng) {
                map.setView([lat, lng], propId ? 16 : 14, { animate: true });
                if (propId && markersMap[propId]) {
                    markersMap[propId].openPopup();
                }
            }
            filterMapItems();
        };

        window.clearMapSearch = function() {
            var headerInput = document.getElementById('mapHeaderSearchInput');
            var sideInput = document.getElementById('mapSearchInput');
            var clearBtn = document.getElementById('clearMapSearchBtn');
            var dropdown = document.getElementById('agodaMapSuggestDropdown');

            if (headerInput) headerInput.value = '';
            if (sideInput) sideInput.value = '';
            if (clearBtn) clearBtn.classList.add('d-none');
            if (dropdown) dropdown.classList.add('d-none');
            filterMapItems();
        };

        // ── 5. Master Modal Initialization & Toggle ──
        var filterCol = document.getElementById('agodaMapFilterCol');
        var toggleBtn = document.getElementById('toggleMapFilterSidebarBtn');
        var toggleText = document.getElementById('toggleFilterBtnText');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (filterCol.style.display === 'none') {
                    filterCol.style.display = 'block';
                    toggleText.textContent = 'Hide filters';
                } else {
                    filterCol.style.display = 'none';
                    toggleText.textContent = 'Show filters';
                }
                setTimeout(() => map && map.invalidateSize(), 300);
            });
        }

        mapModal.addEventListener('shown.bs.modal', function () {
            renderMapCards(allProperties);

            if (!mapInitialized) {
                map = L.map('agodaMapContainer', {zoomControl: false}).setView([{{ $centerLat }}, {{ $centerLng }}], 13);
                L.control.zoom({position: 'bottomright'}).addTo(map);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors | PRIME BOOKING'
                }).addTo(map);

                if (userLat && userLng) {
                    var mainUserIcon = L.divIcon({
                        className: 'main-user-pulse',
                        html: '<div style="position:relative;width:22px;height:22px;"><div style="position:absolute;width:22px;height:22px;background:rgba(37,99,235,0.25);border-radius:50%;"></div><div style="position:absolute;top:3px;left:3px;width:16px;height:16px;background:#2563eb;border:2.5px solid #ffffff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.35);"></div></div>',
                        iconSize: [22, 22],
                        iconAnchor: [11, 11]
                    });
                    L.marker([userLat, userLng], {icon: mainUserIcon, zIndexOffset: 10000})
                        .addTo(map)
                        .bindPopup("<div class='p-1 text-center' style='font-size:12px;font-family:sans-serif;'>📍 <strong>Your Current Location</strong></div>");
                }

                allProperties.forEach(function(item) {
                    var isSoldOut = item.price_raw <= 0 || item.price.includes('N/A');
                    var pinHtml = isSoldOut 
                        ? '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#64748b;font-weight:700;font-size:11px;padding:3px 8px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:1.5px solid #cbd5e1;cursor:pointer;white-space:nowrap;transition:all 0.2s;">Sold out</div>'
                        : '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#2067e1;font-weight:800;font-size:11.5px;padding:3px 9px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:1.5px solid #2067e1;cursor:pointer;white-space:nowrap;transition:all 0.2s;">' + item.price + '</div>';

                    var customIcon = L.divIcon({
                        className: 'custom-agoda-price-pin',
                        html: pinHtml,
                        iconSize: [80, 28],
                        iconAnchor: [40, 14]
                    });

                    var popupContent = `
                        <div style="width:200px;font-family:'Plus Jakarta Sans',sans-serif;">
                            <img src="${item.image}" style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:6px;">
                            <div style="font-weight:700;font-size:12.5px;color:#0f172a;line-height:1.2;margin-bottom:3px;">${item.name}</div>
                            <div style="font-size:11px;color:#64748b;margin-bottom:6px;"><i class="fa-solid fa-location-dot text-danger me-1"></i>${item.city}</div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="background:#2067e1;color:#fff;font-weight:700;font-size:11px;padding:2px 6px;border-radius:4px;">${item.score}</span>
                                <span style="font-weight:800;font-size:13.5px;color:#0f172a;">${item.price}</span>
                            </div>
                            <a href="${item.url}" target="_blank" style="display:block;text-align:center;background:#16a34a;color:#fff;font-weight:700;font-size:11px;padding:5px 0;border-radius:6px;text-decoration:none;margin-top:8px;">View Details →</a>
                        </div>
                    `;

                    var m = L.marker([item.lat, item.lng], {icon: customIcon})
                        .addTo(map)
                        .bindPopup(popupContent);

                    m.on('click', function() {
                        var card = document.getElementById('mapCard_' + item.id);
                        if (card) card.scrollIntoView({behavior: 'smooth', block: 'center'});
                    });

                    markersMap[item.id] = m;
                });

                if (Object.keys(markersMap).length > 0) {
                    try {
                        var group = new L.featureGroup(Object.values(markersMap));
                        map.fitBounds(group.getBounds().pad(0.12));
                    } catch(e) {}
                }

                // Viewport dynamic sync + "Search as I move the map" toggle
                map.on('moveend', function() {
                    var bounds = map.getBounds();
                    var inViewport = currentFiltered.filter(function(i) { return bounds.contains([i.lat, i.lng]); });
                    var countEl = document.getElementById('visibleCardsCount');
                    if (countEl) countEl.textContent = inViewport.length;

                    // If "Search as I move" toggle is on, re-render cards to viewport only
                    var moveToggle = document.getElementById('searchAsIMoveToggle');
                    if (moveToggle && moveToggle.checked) {
                        renderMapCards(inViewport);
                        // Also dim markers outside viewport
                        Object.keys(markersMap).forEach(function(id) {
                            var prop = allProperties.find(function(p) { return p.id == id; });
                            var el = markersMap[id].getElement();
                            if (!el) return;
                            var badge = el.querySelector('.custom-agoda-pin-inner');
                            if (!badge) return;
                            var inView = prop && bounds.contains([prop.lat, prop.lng]);
                            badge.style.opacity = inView ? '1' : '0.35';
                        });
                    }
                });

                mapInitialized = true;
            } else {
                map.invalidateSize();
            }
        });
    });
</script>

{{-- Mobile Off-Canvas Filter Drawer --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterOffcanvasLabel" style="width: min(360px, 90vw);">
    <div class="offcanvas-header border-bottom" style="background:#1d2b45;">
        <h6 class="offcanvas-title text-white fw-bold m-0" id="mobileFilterOffcanvasLabel">
            <i class="fa-solid fa-sliders me-2"></i>Filter Results
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('components.search.filter-sidebar')
    </div>
</div>

{{-- JSON-LD Structured Data for SEO (ItemList + Hotel schema) --}}
@php
    $jsonLdItems = collect($searchResults['merged_results'])->take(10)->map(function($p, $idx) {
        $isObj = is_object($p);
        $name  = $isObj ? ($p->name ?? '') : ($p['name'] ?? '');
        $url   = $isObj ? route('property.show', $p->slug ?? $p->id) : route('property.show', $p['slug'] ?? $p['id'] ?? 1);
        $img   = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
        $price = (float)($isObj ? ($p->price_per_night ?? 0) : ($p['price_per_night'] ?? 0));
        return [
            '@type'    => 'ListItem',
            'position' => $idx + 1,
            'item'     => [
                '@type'       => 'Hotel',
                'name'        => $name,
                'url'         => $url,
                'image'       => $img ?: null,
                'priceRange'  => $price > 0 ? ('৳' . number_format($price)) : null,
            ],
        ];
    })->values()->toArray();

    $jsonLdData = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => 'Hotels & Stays in ' . ($destination ?: 'Bangladesh'),
        'description'     => 'Search results for ' . ($searchResults['total_count'] ?? 0) . ' hotels and stays in ' . ($destination ?: 'Bangladesh') . ' on PRIME BOOKING',
        'numberOfItems'   => count($jsonLdItems),
        'itemListElement' => $jsonLdItems,
    ];
@endphp
@if(count($jsonLdItems))
<script type="application/ld+json">
{!! json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif



{{-- Destination Autocomplete + AJAX Sort JS --}}
<script>
(function() {
    // ── A. Destination autocomplete on top search bar ──────────────────────────
    var destInput = document.getElementById('mainDestInput');
    var acDropdown = null;
    var acTimer = null;

    if (destInput) {
        acDropdown = document.createElement('div');
        acDropdown.id = 'topBarAcDropdown';
        acDropdown.className = 'position-absolute bg-white rounded-3 shadow-lg border mt-1 d-none';
        acDropdown.style.cssText = 'z-index:2000; width:340px; max-height:340px; overflow-y:auto; top:100%; left:0;';
        destInput.closest('.position-relative').appendChild(acDropdown);

        destInput.addEventListener('input', function() {
            clearTimeout(acTimer);
            var q = this.value.trim();
            if (q.length < 1) { acDropdown.classList.add('d-none'); return; }
            acTimer = setTimeout(function() { fetchAcSuggestions(q); }, 200);
        });

        destInput.addEventListener('blur', function() {
            setTimeout(function() { acDropdown.classList.add('d-none'); }, 200);
        });
    }

    function fetchAcSuggestions(q) {
        fetch('/api/search/autocomplete?q=' + encodeURIComponent(q) + '&search_type={{ $searchType ?? "hotel" }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var locs = (data.data && data.data.locations) ? data.data.locations : (data.locations || []);
                var props = (data.data && data.data.properties) ? data.data.properties : (data.properties || []);
                renderTopAcDropdown(locs, props, q);
            }).catch(function() {});
    }

    function renderTopAcDropdown(locs, props, q) {
        if (!acDropdown) return;
        if (!locs.length && !props.length) { acDropdown.classList.add('d-none'); return; }
        var html = '';
        var iconMap = { city:'fa-city', landmark:'fa-location-dot', country:'fa-globe', station:'fa-train' };
        if (locs.length) {
            html += '<div class="px-3 pt-2 pb-1" style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:1px;">DESTINATIONS</div>';
            locs.slice(0, 5).forEach(function(l) {
                var icon = iconMap[l.type] || 'fa-location-dot';
                html += '<div class="px-3 py-2" style="cursor:pointer;border-bottom:1px solid #f1f5f9;" '
                    + 'onmousedown="selectTopAcItem(' + JSON.stringify(l.city||l.name||l.destination) + ')" '
                    + 'onmouseenter="this.style.background=\'#f8fafc\'" onmouseleave="this.style.background=\'transparent\'">'
                    + '<div class="d-flex align-items-center gap-2">'
                    + '<span style="width:28px;height:28px;background:#e0edff;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid ' + icon + ' text-primary" style="font-size:12px;"></i></span>'
                    + '<div><div class="fw-bold text-dark" style="font-size:13px;">' + (l.city||l.name||l.destination) + '</div>'
                    + '<small class="text-muted" style="font-size:11px;">' + (l.country||l.subtitle||'') + (l.property_count ? ' • '+l.property_count+' properties' : '') + '</small></div>'
                    + '</div></div>';
            });
        }
        if (props.length) {
            html += '<div class="px-3 pt-2 pb-1" style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:1px;">PROPERTIES</div>';
            props.slice(0, 3).forEach(function(p) {
                html += '<div class="px-3 py-2" style="cursor:pointer;" '
                    + 'onmousedown="selectTopAcItem(' + JSON.stringify(p.name) + ')" '
                    + 'onmouseenter="this.style.background=\'#f8fafc\'" onmouseleave="this.style.background=\'transparent\'">'
                    + '<div class="d-flex align-items-center gap-2">'
                    + (p.image ? '<img src="'+p.image+'" style="width:32px;height:32px;border-radius:4px;object-fit:cover;">' : '<span style="width:32px;height:32px;background:#f1f5f9;border-radius:4px;display:inline-block;"></span>')
                    + '<div><div class="fw-bold text-dark" style="font-size:13px;">' + p.name + '</div>'
                    + '<small class="text-muted" style="font-size:11px;">' + (p.city||'') + (p.price ? ' • from '+p.price : '') + '</small></div>'
                    + '</div></div>';
            });
        }
        acDropdown.innerHTML = html;
        acDropdown.classList.remove('d-none');
    }

    window.selectTopAcItem = function(val) {
        if (destInput) { destInput.value = val; }
        if (acDropdown) acDropdown.classList.add('d-none');
        document.getElementById('searchHeaderForm').submit();
    };

    // ── B. AJAX Sort (no full page reload) ───────────────────────────────────
    var sortSelect = document.getElementById('sortBySelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            var filterForm = document.getElementById('filterSidebarForm');
            var resultsContainer = document.getElementById('searchResultsContainer');
            if (!resultsContainer || !filterForm) { return; }

            var params = new URLSearchParams();
            var fd = new FormData(filterForm);
            for (var pair of fd.entries()) { if (pair[1]) params.append(pair[0], pair[1]); }
            params.set('sort_by', sortSelect.value);
            params.delete('page');

            var url = filterForm.action + '?' + params.toString();
            resultsContainer.style.opacity = '0.4';
            resultsContainer.style.transition = 'opacity 0.15s';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var fresh = doc.getElementById('searchResultsContainer');
                    if (fresh) {
                        resultsContainer.innerHTML = fresh.innerHTML;
                        if (typeof window.initAgodaInfiniteScroll === 'function') {
                            window.initAgodaInfiniteScroll();
                        }
                    }
                    resultsContainer.style.opacity = '1';
                    history.pushState(null, '', url);
                }).catch(function() { resultsContainer.style.opacity = '1'; });
        });
    }

    // ── C. Agoda-Exact Infinite Scroll with Shimmer Skeleton Loader ──────────
    window.initAgodaInfiniteScroll = function() {
        var feed = document.getElementById('propertyCardsFeed');
        var sentinel = document.getElementById('infiniteScrollSentinel');
        var skeleton = document.getElementById('infiniteScrollSkeletonContainer');
        var endMsg = document.getElementById('infiniteScrollEndMessage');

        if (!feed || !sentinel) return;

        if (window._agodaScrollObserver) {
            window._agodaScrollObserver.disconnect();
            window._agodaScrollObserver = null;
        }

        var currentPage = parseInt(feed.getAttribute('data-current-page') || '1', 10);
        var lastPage = parseInt(feed.getAttribute('data-last-page') || '1', 10);
        var totalCount = parseInt(feed.getAttribute('data-total') || '0', 10);
        var isLoading = false;
        var hasMore = currentPage < lastPage;

        if (!hasMore) {
            if (endMsg && totalCount > 0) endMsg.classList.remove('d-none');
            if (skeleton) skeleton.classList.add('d-none');
            return;
        } else {
            if (endMsg) endMsg.classList.add('d-none');
        }

        function fetchNextBatch() {
            if (isLoading || !hasMore) return;
            isLoading = true;

            if (skeleton) skeleton.classList.remove('d-none');

            var nextPage = currentPage + 1;
            var fetchUrl = new URL(window.location.href);
            fetchUrl.searchParams.set('page', nextPage);
            fetchUrl.searchParams.set('ajax_scroll', '1');

            fetch(fetchUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Infinite-Scroll': 'true',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Network response error');
                return res.json();
            })
            .then(function(data) {
                if (skeleton) skeleton.classList.add('d-none');

                if (data && data.success && data.html) {
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;

                    while (tempDiv.firstChild) {
                        var child = tempDiv.firstChild;
                        if (child.nodeType === 1) {
                            child.style.animation = 'fadeInUpCard 0.35s ease-out';
                        }
                        feed.appendChild(child);
                    }

                    currentPage = data.current_page || nextPage;
                    hasMore = Boolean(data.has_more);
                    feed.setAttribute('data-current-page', currentPage);

                    var displayUrl = new URL(window.location.href);
                    displayUrl.searchParams.set('page', currentPage);
                    history.replaceState(null, '', displayUrl.toString());

                    if (!hasMore) {
                        if (endMsg) endMsg.classList.remove('d-none');
                        if (window._agodaScrollObserver && sentinel) {
                            window._agodaScrollObserver.unobserve(sentinel);
                        }
                    }
                } else {
                    hasMore = false;
                    if (endMsg) endMsg.classList.remove('d-none');
                }
                isLoading = false;
            })
            .catch(function(err) {
                console.error('Agoda Infinite Scroll Error:', err);
                if (skeleton) skeleton.classList.add('d-none');
                isLoading = false;
            });
        }

        window._agodaScrollObserver = new IntersectionObserver(function(entries) {
            if (entries[0] && entries[0].isIntersecting && hasMore && !isLoading) {
                fetchNextBatch();
            }
        }, {
            root: null,
            rootMargin: '350px',
            threshold: 0.05
        });

        window._agodaScrollObserver.observe(sentinel);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initAgodaInfiniteScroll);
    } else {
        window.initAgodaInfiniteScroll();
    }
})();

function validateAndSubmitHeaderSearch(e) {
    var destInput = document.getElementById('mainDestInput');
    var destVal = destInput ? destInput.value.trim() : '';
    var latInput = document.getElementById('gpsLatInput');
    var hasGps = latInput && latInput.value.trim() !== '';

    if (!destVal && !hasGps) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (typeof window.showAgodaEmptyDestinationModal === 'function') {
            window.showAgodaEmptyDestinationModal();
        }
        return false;
    }

    if (typeof window.showAgodaSearchLoading === 'function') {
        window.showAgodaSearchLoading();
    }
    return true;
}

</script>

@endsection

