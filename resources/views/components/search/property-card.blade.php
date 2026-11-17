@props(['item', 'isFirst' => false, 'isSecond' => false, 'userWishlists' => []])

@once
<style>
    .ScreenReaderOnly__ScreenReaderOnlyStyled-sc-szxtre-0, 
    .fuICnK, 
    .ScreenReaderOnly,
    .visually-hidden-agoda {
        display: none !important;
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    .agoda-address-btn,
    button.agoda-address-btn {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        text-align: left !important;
        cursor: pointer !important;
        text-decoration: none !important;
    }
    .agoda-address-btn:focus,
    .agoda-address-btn:active {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
    .PropertyCardBaseJacket {
        transition: box-shadow 0.2s ease, border-color 0.2s ease !important;
    }
    .PropertyCardBaseJacket:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
        border-color: #b0c9f0 !important;
    }
    .PropertyCardBaseJacket:hover .agoda-hotel-title-link h2 {
        color: #2067e1 !important;
        text-decoration: underline !important;
    }

    /* Agoda Heart / Wishlist Button */
    .agoda-heart-wrap {
        position: absolute !important;
        top: 10px !important;
        right: 10px !important;
        z-index: 15 !important;
    }
    .agoda-heart-btn {
        width: 34px !important;
        height: 34px !important;
        border-radius: 50% !important;
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        outline: none !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    }
    .agoda-heart-btn:hover {
        transform: scale(1.08) !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25) !important;
    }

    /* Agoda Slide Counter Pill */
    .agoda-slide-counter-pill {
        position: absolute !important;
        bottom: 10px !important;
        left: 10px !important;
        z-index: 15 !important;
        background: rgba(0, 0, 0, 0.6) !important;
        color: #ffffff !important;
        padding: 2px 8px !important;
        border-radius: 4px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 16px !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        pointer-events: none !important;
    }

    /* Agoda Carousel Arrows */
    .agoda-carousel-arrow-wrap {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 15 !important;
        transition: opacity 0.2s ease !important;
    }
    .agoda-arrow-prev {
        left: 8px !important;
    }
    .agoda-arrow-next {
        right: 8px !important;
    }
    .agoda-carousel-arrow-wrap.is-disabled {
        display: none !important;
    }
    .agoda-carousel-nav-btn {
        width: 28px !important;
        height: 28px !important;
        background: #ffffff !important;
        border-radius: 50% !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
        border: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        outline: none !important;
        transition: transform 0.15s ease !important;
    }
    .agoda-carousel-nav-btn:hover {
        transform: scale(1.08) !important;
    }

    /* Agoda Check Availability Button */
    .agoda-check-avail-btn {
        background-color: #2067e1 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        width: 100% !important;
        cursor: pointer !important;
        text-align: center !important;
        outline: none !important;
        box-shadow: none !important;
        display: block !important;
        line-height: 1.3 !important;
        transition: background-color 0.15s ease !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }
    .agoda-check-avail-btn:hover {
        background-color: #1652b5 !important;
        color: #ffffff !important;
    }

    /* 📱 100% Mobile Responsive Adaptation for Property Card */
    @media (max-width: 767.98px) {
        .PropertyCardItem [data-element-name="PropertyCardBaseJacket"] {
            height: auto !important;
            min-height: unset !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }
        .PropertyCardItem .PropertyCard__Container {
            height: auto !important;
            min-height: unset !important;
            flex-direction: column !important;
            flex-wrap: wrap !important;
        }
        .PropertyCardItem .PropertyCard__MosaicContainer {
            flex: 0 0 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 195px !important;
            min-height: 195px !important;
            max-height: 195px !important;
        }
        .PropertyCardItem .PropertyCard__MosaicContainer .abb64-rounded-tl-8 {
            border-radius: 12px 12px 0 0 !important;
        }
        .PropertyCardItem .PropertyCard__MosaicContainer img {
            border-radius: 12px 12px 0 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 195px !important;
            min-height: 195px !important;
            max-height: 195px !important;
        }
        .PropertyCardItem .PropertyCard__Section--propertyInfo {
            flex: 0 0 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: unset !important;
            height: auto !important;
            padding: 12px 14px 8px 14px !important;
        }
        .PropertyCardItem .PropertyCard__Section--pricingInfo {
            flex: 0 0 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: unset !important;
            height: auto !important;
            border-left: none !important;
            border-top: 1px solid #f0f2f5 !important;
            padding: 10px 14px 12px 14px !important;
            background: #ffffff !important;
        }
        .PropertyCardItem .PropertyCard__Section--pricingInfo .abb64-h-full {
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            min-height: unset !important;
            height: auto !important;
            padding: 0 !important;
        }
        .PropertyCardItem .PropertyCard__PricingHeadliner {
            text-align: left !important;
            width: auto !important;
        }
        .PropertyCardItem .PropertyCard__PricingHeadliner .abb64-items-end {
            align-items: flex-start !important;
            text-align: left !important;
        }
        .PropertyCardItem [data-element-name="property-card-price"] {
            width: auto !important;
            text-align: right !important;
        }
    }
</style>
@endonce

@php
    $isObj             = is_object($item);
    $id                = $isObj ? $item->id : ($item['id'] ?? 1);
    $name              = $isObj ? ($item->name ?? 'Hotel') : ($item['name'] ?? 'Hotel');
    $lowerName         = strtolower($name);
    
    $currentPage       = (int)request('page', 1);
    $isFirstCard       = (!empty($isFirst) && $currentPage <= 1);
    
    // Everything on this card comes from the listing's own data. Nothing is keyed on a hotel's name.
    $get = fn (string $key, $default = null) => $isObj ? ($item->{$key} ?? $default) : ($item[$key] ?? $default);

    // Kept for markup compatibility: no listing gets special-case styling.
    $isSeaPearl = $isRamada = $isSeagull = $isWindy = $isBestWestern = $isSeaCrown = false;

    // Badges
    $ribbonText        = trim((string) $get('ribbon_badge', ''));
    $hasRibbon         = $ribbonText !== '';
    $isSoldOut         = (bool) $get('is_sold_out', false);
    $hasStillAvailable = false;
    $stillAvailableText = '';
    $bookingUrgencyText = $isSoldOut ? 'Sold out on your dates' : null;
    $hasBoosted        = false;
    $hasSeaView        = false;
    $hasCreditCardNote = (bool) $get('no_credit_card_required', false);

    // Address format: "Area, City"
    $cityRaw = trim((string) $get('city', ''));
    $cityStr = $cityRaw;
    $areaRaw = '';
    if ($item instanceof \Illuminate\Database\Eloquent\Model) {
        $areaRaw = trim((string)($item->area ?? $item->zone ?? ($item->relationLoaded('location') ? $item->location?->name : null) ?? ''));
    } elseif (is_object($item)) {
        $areaRaw = trim((string)($item->area ?? $item->zone ?? (is_string($item->location ?? null) ? $item->location : ($item->location->name ?? '')) ?? ''));
    } else {
        $areaRaw = trim((string)($item['area'] ?? $item['zone'] ?? (is_string($item['location'] ?? null) ? $item['location'] : ($item['location']['name'] ?? '')) ?? ''));
    }
    if ($areaRaw === '') {
        $areaRaw = trim((string) $get('nearest_landmark', ''));
    }
    $locationLabel = implode(', ', array_unique(array_filter([$areaRaw, $cityStr]))) ?: 'Bangladesh';

    // Star rating
    $stars = max(0, min(5, (int) $get('star_rating', $get('stars', 0))));

    $currencyCode = \App\Services\CurrencyService::currentCode();
    $isRedPrice = true;

    // Pricing: amounts are stored in BDT and shown in the visitor's currency.
    $priceVal     = (float) $get('price_per_night', $get('price', 0));
    $origPriceVal = (float) $get('original_price', 0);
    $price        = number_format(\App\Services\CurrencyService::convert($priceVal), 0);
    $hasDiscount  = $priceVal > 0 && $origPriceVal > $priceVal;
    $origPrice    = $hasDiscount ? number_format(\App\Services\CurrencyService::convert($origPriceVal), 0) : null;
    $discount     = $hasDiscount ? (int) round((($origPriceVal - $priceVal) / $origPriceVal) * 100) : null;
    $hasValuePick = $hasDiscount && $discount >= 25;

    // Ratings & reviews: a listing without reviews is "New", never an invented score.
    $revCount   = (int) $get('total_reviews', $get('reviews_count', 0));
    $scoreNum   = (float) $get('rating_score', $get('rating', 0));
    $hasReviews = $revCount > 0 && $scoreNum > 0;
    if ($hasReviews) {
        $score = number_format($scoreNum, 1);
        $ratingLabel = match(true) {
            $scoreNum >= 9.0 => 'Exceptional',
            $scoreNum >= 8.5 => 'Superb',
            $scoreNum >= 8.0 => 'Fabulous',
            $scoreNum >= 7.5 => 'Very good',
            $scoreNum >= 7.0 => 'Good',
            default          => 'Review score',
        };
        $reviewsCount = number_format($revCount);
        $reviewsLine  = $reviewsCount . ' ' . ($revCount === 1 ? 'review' : 'reviews');
    } else {
        $score = 'New';
        $ratingLabel = '';
        $reviewsCount = '0';
        $reviewsLine  = 'No reviews yet';
    }
    $locScoreNum = (float) $get('location_score', 0);
    $hasLocScore = $hasReviews && $locScoreNum > 0;
    $locScore    = $hasLocScore ? number_format($locScoreNum, 1) : null;

    // Gallery: this listing's own photos only.
    $rawGallery = $get('gallery_urls', $get('images', $get('gallery', [])));
    if (is_string($rawGallery)) {
        $rawGallery = json_decode($rawGallery, true) ?: [];
    }
    $gallery = [];
    $primary = $get('primary_image') ?: $get('featured_image');
    if (is_string($primary) && $primary !== '') {
        $gallery[] = media_url($primary);
    }
    foreach ((array) $rawGallery as $gImg) {
        $gPath = is_array($gImg) ? ($gImg['url'] ?? $gImg['path'] ?? null) : $gImg;
        if (is_string($gPath) && $gPath !== '') {
            $gallery[] = media_url($gPath);
        }
    }
    $gallery = array_slice(array_values(array_unique($gallery)), 0, 10);
    if (empty($gallery)) {
        $gallery = [asset('images/logo.png')];
    }
    $totalImgs = count($gallery);

    // Detail page URL
    $slug = $get('slug');
    $queryArgs = request()->only(['check_in', 'check_out', 'guests', 'adults', 'children', 'rooms']);
    $hotelDetailUrl = $slug ? route('hotel.show', array_merge(['slug' => $slug], $queryArgs)) : route('hotels.show', array_merge(['id' => $id], $queryArgs));

    // No coupon is "applied" on a search card: coupons are entered at checkout.
    $hasCoupon = false;
    $couponVal = '';

    // Wishlist check
    $isSaved = in_array($id, (array)$userWishlists);

    // Highlight line: the listing's own snippet, else its first amenities.
    $highlightUsp = trim((string) $get('feature_snippet', ''));
    if ($highlightUsp === '') {
        $features = $get('amenities', $get('features', []));
        if (is_string($features)) $features = json_decode($features, true) ?: [];
        $features = array_values(array_filter((array) $features, 'is_string'));
        $highlightUsp = implode(' • ', array_slice($features, 0, 3));
    }
    if ($highlightUsp === '' && (bool) $get('free_cancellation', false)) {
        $highlightUsp = 'Free cancellation';
    }

    $displayName = $name;
@endphp

<li data-hotelid="{{ $id }}" data-selenium="hotel-item" class="PropertyCard PropertyCardItem" onclick="if(!event.target.closest('.agoda-carousel-arrow-wrap, .agoda-carousel-nav-btn, .agoda-heart-wrap, .agoda-heart-btn, .agoda-address-btn, button, input, select') && !event.target.closest('a')){ window.open('{{ $hotelDetailUrl }}', '_blank'); }" style="margin-bottom: 16px; list-style: none; cursor: pointer;">
  <div role="group" aria-label="Property Card" class="abb64-box abb64-fill-inherit abb64-text-inherit">
    <a href="{{ $hotelDetailUrl }}" target="_blank" class="PropertyCard__Link" style="cursor: pointer; text-decoration: none; color: inherit; display: block;" property-id="{{ $id }}" data-element-index="{{ $idx ?? 0 }}" data-element-name="property-card-content" data-is-singleroom="false">
      <span class="visually-hidden" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">{{ $displayName }} opens in a new tab</span>
      <div data-element-name="PropertyCardBaseJacket" class="abb64-box Cardstyled__CardStyled-sc-xcq8n9-0 WpdCM {{ ($isBestWestern || $isSeaPearl) ? 'abb64-bg-product-primary-subtle' : 'abb64-bg-generic-base-surface' }} abb64-border-generic-base-default abb64-fill-generic-base-default abb64-text-generic-base-default abb64-border-solid abb64-cursor-pointer abb64-rounded-8 abb64-border-1" style="min-height: 220px; box-sizing: border-box; {{ ($isBestWestern || $isSeaPearl) ? 'background-color: #f5f8ff !important; border-color: #cde0fc !important;' : '' }}">
        <div class="Gridstyled__GridStyled-sc-dfh2k0-0 ewVkEu PropertyCard__Container" direction="row" style="min-height: 220px; display: flex; flex-direction: row; flex-wrap: nowrap; box-sizing: border-box;">
          
          {{-- 1. MOSAIC GALLERY CONTAINER (Exact Agoda Layout) --}}
          <div class="Itemstyled__Item-sc-12uga7p-0 ftJhvu PropertyCard__MosaicContainer" style="position: relative; flex: 0 0 33.3333%; width: 33.3333%; height: 220px; min-height: 220px; max-height: 220px; overflow: visible; box-sizing: border-box;">
            @if($hasRibbon)
              <div class="agoda-ribbon-badge" style="position: absolute !important; top: 10px !important; left: -4px !important; z-index: 25 !important; background-color: #2067e1 !important; color: #ffffff !important; padding: 2px 8px 2px 6px !important; border-radius: 0 3px 3px 0 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.25) !important; display: inline-flex !important; align-items: center !important; height: 22px !important; width: auto !important; max-width: max-content !important; line-height: 22px !important; pointer-events: none !important;">
                <div style="position: absolute; bottom: -4px; left: 0; width: 0; height: 0; border-width: 4px 4px 0 0; border-style: solid; border-color: #123d87 transparent transparent transparent;"></div>
                <svg viewBox="0 0 24 24" width="12" height="12" fill="#fff" style="margin-right: 6px; display: inline-block; vertical-align: -1px;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span class="agoda-ribbon-text" style="color: #ffffff !important; font-size: 11px !important; font-weight: 700 !important; letter-spacing: 0.2px; white-space: nowrap !important;">{{ $ribbonText }}</span>
              </div>
            @endif
            <div data-element-name="property-card-gallery" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-overflow-hidden abb64-p-0 abb64-rounded-tl-8 abb64-rounded-tr-0 abb64-rounded-br-0 abb64-rounded-bl-8 abb64-h-full" style="position: relative; width: 100%; height: 220px; min-height: 220px; max-height: 220px;">
              <div dir="ltr" class="abb64-relative abb64-isolate abb64-h-full" role="region" aria-roledescription="carousel" style="width: 100%; height: 220px; min-height: 220px; max-height: 220px;">
                <ul tabindex="0" aria-live="polite" aria-atomic="false" class="abb64-flex abb64-snap-x abb64-snap-mandatory abb64-overflow-x-scroll abb64-overflow-y-hidden abb64-no-scrollbar abb64-h-full abb64-gap-0 abb64-list-none abb64-pl-0 abb64-m-0 abb64-interactive-style abb64-interactive-generic abb64-focus-ring-interactive-focusonly" id="gallery_track_{{ $id }}" style="width: 100%; height: 220px; min-height: 220px; max-height: 220px;">
                  @foreach($gallery as $gIdx => $gImg)
                    <li class="abb64-min-w-0 abb64-shrink-0 abb64-grow-0 abb64-snap-always abb64-snap-center prop-slide-{{ $id }}" aria-roledescription="slide" style="flex-basis: 100%; height: 220px; min-height: 220px; max-height: 220px; {{ $gIdx > 0 ? 'display: none;' : '' }}" data-slide="{{ $gIdx }}">
                      <img data-property-id="{{ $id }}" data-element-index="{{ $gIdx }}" loading="lazy" alt="{{ $displayName }}" src="{{ $gImg }}" class="sc-eqUAAy Imagestyled__ImageStyled-sc-zu5jhi-1 fnzbqc" sizes="269px" style="width: 100%; height: 220px; min-height: 220px; max-height: 220px; object-fit: cover;">
                    </li>
                  @endforeach
                </ul>

                {{-- Left Carousel Arrow (disabled by default on first slide) with Agoda CDN Mask SVG --}}
                <div class="agoda-carousel-arrow-wrap agoda-arrow-prev is-disabled" id="prev_arrow_{{ $id }}">
                  <button aria-label="Previous property image" onclick="event.stopPropagation(); event.preventDefault(); changeAgodaSlide({{ $id }}, -1);" type="button" class="agoda-carousel-nav-btn">
                    <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; justify-content: center;">
                      <svg viewBox="0 0 1 1" role="presentation" style="width: 16px; height: 16px; fill: #252c38; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/b87316f3.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/b87316f3.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#252c38"></rect></svg>
                    </span>
                  </button>
                </div>

                {{-- Right Carousel Arrow with Agoda CDN Mask SVG --}}
                <div class="agoda-carousel-arrow-wrap agoda-arrow-next {{ $totalImgs <= 1 ? 'is-disabled' : '' }}" id="next_arrow_{{ $id }}">
                  <button aria-label="Next property image" onclick="event.stopPropagation(); event.preventDefault(); changeAgodaSlide({{ $id }}, 1);" type="button" class="agoda-carousel-nav-btn">
                    <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; justify-content: center;">
                      <svg viewBox="0 0 1 1" role="presentation" style="width: 16px; height: 16px; fill: #252c38; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/01ed12e8.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/01ed12e8.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#252c38"></rect></svg>
                    </span>
                  </button>
                </div>

                {{-- Exact Agoda Bottom Photo Counter Pill --}}
                <div class="agoda-slide-counter-pill" id="slide_pill_{{ $id }}">
                  <span id="slide_counter_{{ $id }}">1/{{ $totalImgs }}</span>
                </div>
              </div>

              {{-- Exact Agoda Circular Love / Wishlist Button with Agoda CDN Mask SVG --}}
              <div class="agoda-heart-wrap">
                <button data-testid="save-to-trip-entry-button" aria-label="Save to trip" onclick="event.stopPropagation(); event.preventDefault(); toggleAgodaWishlist({{ $id }}, this);" type="button" class="agoda-heart-btn {{ $isSaved ? 'is-saved' : '' }}">
                  <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 1 1" role="presentation" style="width: 20px; height: 20px; fill: {{ $isSaved ? '#e12d46' : '#252c38' }}; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/72463b93.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/72463b93.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="{{ $isSaved ? '#e12d46' : '#252c38' }}"></rect></svg>
                  </span>
                </button>
              </div>
            </div>
          </div>

          {{-- 2. EXACT MIDDLE INFO SECTION --}}
          <div class="Itemstyled__Item-sc-12uga7p-0 eKqvXe PropertyCard__Section PropertyCard__Section--propertyInfo" style="flex: 0 0 41.6667%; width: 41.6667%; min-height: 220px; box-sizing: border-box;">
            <div data-element-name="property-card-info" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-h-full abb64-flex abb64-flex-col abb64-justify-between abb64-px-12 abb64-pb-8 abb64-pt-0" style="min-height: 220px; height: 100%; box-sizing: border-box;">
              <div data-element-name="property-info-private-host" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-mb-4"></div>
              <header data-element-name="property-info-header" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-mb-4 abb64-pt-4">
                <div class="abb64-box sc-fBWQRz hOlxRE abb64-fill-inherit abb64-text-inherit">
                  <div class="TextLink__TextLinkStyled-sc-upxc4y-1 jwdswZ agoda-hotel-title-link abb64-interactive-style abb64-interactive-generic" data-element-name="ssr-property-card-title">
                    <h2 class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN dTvOjQ TextLink__TextStyled-sc-upxc4y-0 hXIeCX" label="{{ $displayName }}" style="font-size: 18px !important; font-weight: 700 !important; margin: 0; line-height: 1.3;">{{ $displayName }}</h2>
                  </div>
                </div>
                <div class="abb64-mt-4">
                  <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline">
                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline">
                      <div data-element-name="ssr-property-card-star-rating" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline-block abb64-mr-4">
                        <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex">
                          <span class="ScreenReaderOnly" style="display: none !important;">{{ $stars }} stars out of 5</span>
                          @for($s = 0; $s < $stars; $s++)
                            <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; margin-right: 2px;">
                              <svg viewBox="0 0 1 1" role="presentation" class="abb64-fill-product-rating-hotels" style="width: 14px; height: 14px; fill: #ff9800; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/d473e92d.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/d473e92d.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#ff9800"></rect></svg>
                            </span>
                          @endfor
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Location Address with Exact Agoda CDN Mask Pin Icon --}}
                <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-mt-4">
                  <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline">
                    <button data-element-name="ssr-property-card-address" type="button" class="TextLink__TextLinkStyled-sc-upxc4y-1 jwdswZ agoda-address-btn" style="border: 0 !important; background: transparent !important; padding: 0 !important; outline: none !important; box-shadow: none !important; text-align: left; display: inline-flex; align-items: center;" onclick="event.stopPropagation(); event.preventDefault(); openHotelOnMap({{ $id }}, '{{ addslashes($displayName) }}');">
                      <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; margin-right: 6px;">
                        <svg viewBox="0 0 1 1" role="presentation" style="width: 14px; height: 14px; fill: #2067e1; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/75768cdf.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/75768cdf.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#2067e1"></rect></svg>
                      </span>
                      <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN gJjGjY TextLink__TextStyled-sc-upxc4y-0 jgXvXU" label="{{ $locationLabel }} - View on map"><span style="color: #2067e1;">{{ $locationLabel }} - </span><span class="LinkText LinkText--underlined" style="color: #2067e1; text-decoration: underline; margin-left: 2px;">View on map</span></span>
                    </button>
                  </div>
                </div>
              </header>

              <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-overflow-hidden abb64-mb-4" style="max-height: 60px;">
                <aside data-element-name="ssr-property-card-badge" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row abb64-flex-wrap abb64-mr-4" style="gap: 4px; display: flex; align-items: center; flex-wrap: wrap;">
                  @if($hasStillAvailable)
                    <div class="abb64-box abb64-bg-feedback-negative abb64-fill-inherit abb64-text-generic-base-inverted abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; background-color: #e12d2d; padding: 2px 8px; border-radius: 2px; display: inline-flex; align-items: center;">
                      <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN hCiawa" style="color: #fff; font-size: 11px; font-weight: 700;">{{ $stillAvailableText }}</span>
                    </div>
                  @endif

                  @if($bookingUrgencyText)
                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; display: inline-flex; align-items: center;">
                      <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN hryDkd" style="color: #e12d2d; font-size: 11.5px; font-weight: 500;">{{ $bookingUrgencyText }}</span>
                    </div>
                  @endif

                  @if($hasBoosted)
                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; border: 1px solid #c7c7c7; padding: 0 6px; border-radius: 2px; display: inline-flex; align-items: center;">
                      <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bvUylf" style="color: #5a5a5a; font-size: 11px; font-weight: 500;">Boosted</span>
                    </div>
                  @endif

                  @if($hasSeaView)
                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; background-color: #f0f7ff; border: 1px solid #bae0fd; padding: 0 6px; border-radius: 2px; display: inline-flex; align-items: center; gap: 6px;">
                      <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline">
                        <svg viewBox="0 0 1 1" role="presentation" style="width: 13px; height: 13px; fill: #0283df; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/05eb4e81.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/05eb4e81.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#0283df"></rect></svg>
                      </span>
                      <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN" style="color: #0283df; font-size: 11px; font-weight: 600;">Sea View</span>
                    </div>
                  @endif

                  @if($hasCreditCardNote)
                    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; display: inline-flex; align-items: center; gap: 6px;">
                      <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#2067e1" stroke-width="2" style="flex-shrink: 0; margin-right: 2px;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                      <span style="color: #2067e1; font-size: 11px; font-weight: 500;">Book without a credit card</span>
                    </div>
                  @endif
                </aside>
              </div>

              {{-- Agoda USP Feature Highlight Bullet --}}
              <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-mt-auto abb64-mb-2">
                <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-items-center abb64-gap-1">
                  <span style="color: #737373; font-size: 11.5px; line-height: 1.4; display: inline-flex; align-items: center; gap: 6px;">
                    <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline" style="display: inline-flex; align-items: center; margin-right: 6px;"><svg viewBox="0 0 1 1" role="presentation" style="width: 12px; height: 12px; fill: #535b6a; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/68235b99.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/68235b99.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#535b6a"></rect></svg></span>
                    <span>{{ $highlightUsp }}</span>
                  </span>
                </div>
              </div>

            </div>
          </div>

          {{-- 3. EXACT RIGHT PRICING SECTION --}}
          <div class="Itemstyled__Item-sc-12uga7p-0 fTvBdC PropertyCard__Section PropertyCard__Section--pricingInfo" style="flex: 0 0 25%; width: 25%; min-height: 220px; box-sizing: border-box; border-left: 1px solid #f0f2f5;">
            <div class="abb64-h-full abb64-justify-between abb64-items-end abb64-p-8 abb64-flex abb64-flex-col" data-element-name="property-card-pricing-info" style="min-height: 220px; height: 100%; box-sizing: border-box; padding: 12px 14px 10px 14px;">
              
              {{-- Review Score Box with Agoda Typo --}}
              <div class="Box-sc-kv6pi1-0 krcUda PropertyCard__PricingHeadliner" style="text-align: right; width: 100%;">
                <div class="ReviewWithDemographic" data-element-name="ReviewWithDemographic" role="button" tabindex="0">
                  <div class="abb64-items-end abb64-flex abb64-flex-col" role="group" aria-label="{{ $hasReviews ? 'Average rating ' . $ratingLabel . ' ' . $score . ' out of 10 with ' . $reviewsLine : 'No reviews yet' }}" data-element-name="property-card-review" style="text-align: right; display: flex; flex-direction: column; align-items: flex-end;">
                    <div style="display: flex; align-items: baseline; justify-content: flex-end; gap: 5px;">
                      <span style="color: #2067e1; font-weight: 700; font-size: 15px; line-height: 1.2;">{{ $score }}</span>
                      <span style="color: #2067e1; font-weight: 700; font-size: 15px; line-height: 1.2;">{{ $ratingLabel }}</span>
                    </div>
                    <span style="color: #687176; font-size: 11px; margin-top: 2px;">{{ $reviewsLine }}</span>
                    @if($hasLocScore)
                      <span style="color: #687176; font-size: 11px; margin-top: 1px;">{{ $locScore }} Location score</span>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Pricing Section --}}
              <div data-element-name="property-card-price" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-max-w-full" style="text-align: right; width: 100%;">
                <aside class="List PropertyCard__PricingBadges" data-element-name="PropertyCard__PricingBadges">
                  <ul class="List__List List__List--Vertical" style="display: flex; flex-direction: column; align-items: flex-end; gap: 3px; margin-bottom: 4px; list-style: none; padding: 0; margin-top: 0;">
                    @if($hasCoupon)
                      <li>
                        <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-max-w-full">
                          <div data-element-name="consolidated-applied-discount-badge" class="abb64-box abb64-bg-feedback-positive-subtle abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; background: #e8f5e9; padding: 0 8px; border-radius: 2px; display: inline-flex; align-items: center; white-space: nowrap;">
                            <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline abb64-mr-4" style="margin-right: 6px;">
                              <svg viewBox="0 0 1 1" role="presentation" style="width: 14px; height: 14px; fill: #2e7d32; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/e409eac2.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/e409eac2.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#2e7d32"></rect></svg>
                            </span>
                            <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN kTnccA" style="color: #2e7d32; font-size: 11px; font-weight: 600; white-space: nowrap;">{{ $currencyCode }} {{ $couponVal }} applied</span>
                          </div>
                        </div>
                      </li>
                    @endif
                    @if($hasValuePick)
                      <li>
                        <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-max-w-full">
                          <div class="abb64-box abb64-bg-product-price-subtle abb64-fill-inherit abb64-text-inherit abb64-w-full abb64-items-start abb64-self-start abb64-inline-flex abb64-flex-row abb64-justify-center abb64-text-start abb64-p-0 abb64-px-8 abb64-py-4 abb64-rounded-2" style="height: 22px; background: #fdf2f2; padding: 0 8px; border-radius: 2px; display: inline-flex; align-items: center; white-space: nowrap;">
                            <span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline abb64-mr-4" style="margin-right: 6px;">
                              <svg viewBox="0 0 1 1" role="presentation" style="width: 14px; height: 14px; fill: #d32f2f; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/273a5e4f.svg&quot;) left top / contain no-repeat; -webkit-mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/273a5e4f.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%" fill="#d32f2f"></rect></svg>
                            </span>
                            <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bvUylf" style="color: #d32f2f; font-size: 11px; font-weight: 600; white-space: nowrap;">Value Pick</span>
                          </div>
                        </div>
                      </li>
                    @endif
                  </ul>
                </aside>

                <aside class="List PropertyCard__Prices">
                  <ul class="List__List List__List--Vertical" style="margin: 0; padding: 0; list-style: none;">
                    @if($origPrice && $discount)
                      <li style="margin-bottom: 2px;">
                        <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-w-full abb64-items-center abb64-flex abb64-gap-4" style="justify-content: flex-end; align-items: baseline; gap: 6px; display: flex; width: 100%;">
                          <div data-element-name="fpc-cor-price" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-contents">
                            <div class="Box-sc-kv6pi1-0 gbOjPM PropertyCardPrice PropertyCardPrice--CrossOut" style="text-decoration: line-through; text-decoration-color: #687176; color: #687176; font-size: 13px;">
                              <span>{{ $origPrice }}</span>
                            </div>
                          </div>
                          <div class="abb64-box abb64-fill-inherit abb64-text-inherit">
                            <p class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN hryDkd" style="color: #e12d2d; font-size: 13px; font-weight: 700; margin: 0;">-{{ $discount }}%</p>
                          </div>
                        </div>
                      </li>
                    @endif
                    <li>
                      <div data-element-name="fpc-room-price" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-contents">
                        <div data-element-name="final-price" class="Box-sc-kv6pi1-0 gbOjPM PropertyCardPrice PropertyCardPrice--Display" style="direction: ltr; display: flex; align-items: baseline; justify-content: flex-end;">
                          <span data-selenium="hotel-currency" class="PropertyCardPrice__Currency" style="color: #e12d2d !important; font-size: 14px; font-weight: 700; margin-right: 3px;">{{ $currencyCode }}</span>
                          <span data-selenium="display-price" class="PropertyCardPrice__Value" style="color: #e12d2d !important; font-size: 22px; font-weight: 800; line-height: 1;">{{ $price }}</span>
                        </div>
                      </div>
                    </li>
                  </ul>
                </aside>

                <div class="PropertyCard__PricingTopMessages" style="margin-top: 3px; text-align: right;">
                  <span class="Spanstyled__SpanStyled-sc-16tp9kb-0 hbNSOv kite-js-Span" data-selenium="per-night-begins-with" style="color: #687176; font-size: 11px; white-space: nowrap;">Per night before taxes and fees</span>
                </div>

                </div>
            </div>
          </div>

        </div>
      </div>
    </a>
  </div>
</li>
