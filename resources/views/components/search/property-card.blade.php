{{-- Agoda-Exact Property Card Component (100% 1:1 Parity with Agoda Screenshot 1) --}}
@props(['item'])

@php
    $isObj             = is_object($item);
    $id                = $isObj ? ($item->id ?? 1) : ($item['id'] ?? 1);
    $name              = $isObj ? ($item->name ?? 'Hotel') : ($item['name'] ?? 'Hotel');
    $priceVal          = (float) ($isObj ? ($item->price_per_night ?? $item->price ?? 4500) : ($item['price_per_night'] ?? $item['price'] ?? 4500));
    $price             = number_format($priceVal, 0);
    $origPriceVal      = $isObj ? ($item->original_price ?? null) : ($item['original_price'] ?? null);
    $origPrice         = $origPriceVal && $origPriceVal > 0 ? number_format((float)$origPriceVal, 0) : null;
    $discount          = ($origPriceVal && $origPriceVal > $priceVal) 
                        ? round((($origPriceVal - $priceVal) / $origPriceVal) * 100) 
                        : null;
    
    $scoreNum          = (float) ($isObj ? ($item->rating_score ?? $item->rating ?? 6.4) : ($item['rating_score'] ?? $item['rating'] ?? 6.4));
    $score             = number_format($scoreNum, 1);
    $locationScoreNum  = (float) ($isObj ? ($item->location_score ?? 8.6) : ($item['location_score'] ?? 8.6));
    $locationScore     = number_format($locationScoreNum, 1);
    $roomsLeft         = intval($isObj ? ($item->rooms_left ?? 4) : ($item['rooms_left'] ?? 4));
    $noCreditCard      = boolval($isObj ? ($item->no_credit_card_required ?? true) : ($item['no_credit_card_required'] ?? true));
    $freeCancellation  = boolval($isObj ? ($item->free_cancellation ?? true) : ($item['free_cancellation'] ?? true));
    $cityStr           = $isObj ? ($item->city ?? '') : ($item['city'] ?? '');
    $address           = $isObj ? ($item->address ?? $cityStr) : ($item['address'] ?? $cityStr);
    if (empty($address)) $address = $cityStr ?: 'Dhaka, Bangladesh';

    $revCount          = $isObj ? ($item->total_reviews ?? $item->reviews_count ?? 3) : ($item['total_reviews'] ?? $item['reviews_count'] ?? 3);
    $reviewsCount      = number_format((int)$revCount);

    $ratingLabel       = match(true) {
        $scoreNum >= 9.0 => 'Superb',
        $scoreNum >= 8.0 => 'Very good',
        $scoreNum >= 7.0 => 'Good',
        $scoreNum >= 6.0 => 'Good',
        default          => 'Good',
    };

    $image             = $isObj ? ($item->primary_image ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80') : ($item['primary_image'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
    $rawImages         = $isObj ? ($item->images ?? null) : ($item['images'] ?? null);
    if (is_string($rawImages)) {
        $rawImages = json_decode($rawImages, true) ?: [];
    }
    $imagesList        = is_array($rawImages) ? $rawImages : [];
    
    $defaultGallery    = [
        $image,
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80'
    ];
    $rawGallery        = !empty($imagesList) 
        ? array_values(array_unique(array_filter(array_merge([$image], (array)$imagesList)))) 
        : $defaultGallery;
    $gallery           = array_slice($rawGallery, 0, 10);
    $totalImgs         = count($gallery);

    $isFallbackCard    = !empty($isFallback);
    $targetLat         = $isFallbackCard ? null : (request('lat') ? (float)request('lat') : null);
    $targetLng         = $isFallbackCard ? null : (request('lng') ? (float)request('lng') : null);
    $calcDist          = null;
    if ($isObj && method_exists($item, 'getFormattedDistanceTo') && $targetLat && $targetLng) {
        $rawDistNum    = method_exists($item, 'getDistanceTo') ? $item->getDistanceTo($targetLat, $targetLng) : null;
        // Only display relative commuter distance if within genuine nearby threshold (<= 45 km)
        if ($rawDistNum === null || $rawDistNum <= 45.0) {
            $calcDist  = $item->getFormattedDistanceTo($targetLat, $targetLng);
        }
    }

    $rawLandmark       = $isObj ? ($item->nearest_landmark ?? null) : ($item['nearest_landmark'] ?? null);
    $nearestLandmark   = $calcDist ? "{$calcDist} from your searched location" : ($rawLandmark ?: (!empty($cityStr) ? "{$cityStr}, Bangladesh" : 'City Center'));
    $type              = $isObj ? ($item->type ?? 'Hotel') : ($item['type'] ?? 'Hotel');
    $stars             = intval($isObj ? ($item->star_rating ?? 3) : ($item['star_rating'] ?? 3));
    $videoUrl          = $isObj ? ($item->video_url ?? null) : ($item['video_url'] ?? null);

    $isWishlisted      = auth()->check() 
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('property_id', $id)->exists()
        : false;
    
    $currencyCode      = \App\Helpers\CurrencyHelper::current();

    $isFeatured        = boolval($isObj ? ($item->is_featured ?? false) : ($item['is_featured'] ?? false));
    $apiSource         = $isObj ? ($item->api_source ?? null) : ($item['api_source'] ?? null);

    // Dynamic Amenities from Admin / Vendor / DB
    $rawAmenities      = is_array($isObj ? ($item->amenities ?? []) : ($item['amenities'] ?? [])) ? ($isObj ? ($item->amenities ?? []) : ($item['amenities'] ?? [])) : [];
    if (is_string($rawAmenities)) {
        $rawAmenities  = json_decode($rawAmenities, true) ?: [];
    }
    $lowerAmenities    = array_map('strtolower', (array)$rawAmenities);

    $displayedAmenities = [];
    if (collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'breakfast'))) {
        $displayedAmenities[] = ['label' => 'Breakfast Included', 'icon' => 'fa-mug-saucer', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'];
    }
    if (collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'wifi') || str_contains($a, 'wi-fi'))) {
        $displayedAmenities[] = ['label' => 'Free Wi-Fi', 'icon' => 'fa-wifi', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'];
    }
    if (collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'pool'))) {
        $displayedAmenities[] = ['label' => 'Swimming Pool', 'icon' => 'fa-person-swimming', 'color' => '#0369a1', 'bg' => '#f0f9ff', 'border' => '#bae6fd'];
    }
    if (count($displayedAmenities) < 3 && collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'parking'))) {
        $displayedAmenities[] = ['label' => 'Free Parking', 'icon' => 'fa-square-parking', 'color' => '#475569', 'bg' => '#f8fafc', 'border' => '#e2e8f0'];
    }

    // Agoda Smart Dynamic Highlight Engine:
    // Adapts dynamically to search results, active filters, and direct property selection:
    // - If a specific hotel is requested via `selectedproperty`, that hotel gets the highlight.
    // - Otherwise, the #1 top algorithmic match on Page 1 gets the highlight.
    // - Never repeats on Page 2+ to keep results clean.
    $currentPage = (int)request('page', 1);
    $selectedProp = request('selectedproperty');
    $isHighlighted = false;
    if ($selectedProp) {
        $isHighlighted = ((string)$selectedProp === (string)$id);
    } elseif (!empty($isFirst) && $currentPage <= 1) {
        $isHighlighted = true;
    }
@endphp

{{-- Main Property Card Container (Exact Agoda Dimensions & Layout) --}}
<div class="agoda-property-card-root @if($isHighlighted) agoda-first-featured-card @endif" onclick="if(!event.target.closest('button, a, form, input, select, .carousel-control-prev, .carousel-control-next')){ window.location.href='{{ route('hotels.show', $id) }}'; }">

    {{-- Agoda Top Blue Urgency Header Strip INSIDE the card for Card #1 --}}
    @if($isHighlighted)
    <div class="agoda-card-blue-strip">
        <span>Excellent choice! Book now to secure your room at the lowest guaranteed price.</span>
    </div>
    @endif

    {{-- Content Row: Photo Column | Info Column | Price Column --}}
    <div class="agoda-card-content-wrap">
        {{-- Column 1: Left Showcase Image Carousel (Width: 236px Fixed, Exact Agoda Screenshot 1) --}}
        <div class="agoda-card-photo-col">
        
        <div id="propertyCarousel_{{ $id }}" class="carousel slide h-100 w-100" data-bs-interval="false">
            <div class="carousel-inner h-100 w-100">
                @foreach($gallery as $gIdx => $gImg)
                    <div class="carousel-item h-100 w-100 @if($gIdx === 0) active @endif">
                        <img src="{{ media_url($gImg) }}" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';"
                             alt="{{ $name }}" 
                             loading="lazy">
                    </div>
                @endforeach
            </div>

            {{-- Prev & Next Carousel Arrows (Agoda Parity: smooth navigation) --}}
            <button class="carousel-control-prev" 
                    type="button" 
                    id="prevCardBtn_{{ $id }}"
                    onclick="prevCardPhoto(this, event, {{ $id }}, {{ $totalImgs }});" 
                    style="display: none; width: 30px; height: 30px; background: #ffffff; border-radius: 50%; top: 50%; left: 8px; transform: translateY(-50%); opacity: 1; border: none; z-index: 10; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.22); cursor: pointer;" 
                    title="Previous Photo">
                <i class="fa-solid fa-chevron-left text-dark" style="font-size: 11px;"></i>
            </button>
            <button class="carousel-control-next" 
                    type="button" 
                    id="nextCardBtn_{{ $id }}"
                    onclick="nextCardPhoto(this, event, {{ $id }}, {{ $totalImgs }});" 
                    style="width: 30px; height: 30px; background: #ffffff; border-radius: 50%; top: 50%; right: 8px; transform: translateY(-50%); opacity: 1; border: none; z-index: 10; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.22); cursor: pointer;" 
                    title="Next Photo">
                <i class="fa-solid fa-chevron-right text-dark" style="font-size: 11px;"></i>
            </button>
        </div>


        {{-- Wishlist Heart Button (Exact Agoda: 34px White circle, dark heart outline) --}}
        <button type="button" 
                id="wishlistBtn_{{ $id }}"
                class="agoda-wishlist-btn"
                onclick="event.stopPropagation(); toggleCardWishlist(this, {{ $id }});" 
                style="position: absolute; top: 10px; right: 10px; z-index: 12; width: 34px; height: 34px; background: #ffffff; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.18); cursor: pointer;" 
                title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Save to Wishlist' }}">
            <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart" style="font-size: 15px; color: {{ $isWishlisted ? '#c53829' : '#222222' }};"></i>
        </button>

        {{-- 1/10 Image Counter Badge (Bottom Left - Exact Pill Parity) --}}
        <div style="position: absolute; bottom: 10px; left: 10px; z-index: 12;">
            <span style="background: rgba(0,0,0,0.72); color: #ffffff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px; line-height: 1.2;" id="imgBadge_{{ $id }}">
                1/{{ $totalImgs }}
            </span>
        </div>
    </div>

    {{-- Column 2: Center Hotel Information (100% 1:1 Agoda Live Parity) --}}
    <div class="agoda-card-info-col">
        <div>
            {{-- Hotel Title (Agoda Exact 20px Bold, Title Case with Hover Underline) --}}
            <h3 style="font-size: 20px; font-weight: 700; color: #222222; margin: 0 0 3px 0; line-height: 1.25; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <a href="{{ route('hotels.show', $id) }}" style="color: #222222; text-decoration: none;" class="agoda-hotel-title" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                    {{ $name }}
                </a>
            </h3>

            {{-- Star Rating (Agoda Exact Deep Amber/Burnt-Orange #b54c01 Stars) --}}
            <div style="color: #b54c01; font-size: 11.5px; margin: 1px 0 4px 0; display: flex; gap: 2.5px; align-items: center;">
                @for($s = 0; $s < max(1, min(5, $stars)); $s++)
                    <i class="fa-solid fa-star"></i>
                @endfor
            </div>

            {{-- Location Line (Agoda Blue Link #2067da with View on map) --}}
            <div style="font-size: 12.5px; color: #2067da; font-weight: 500; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <i class="fa-solid fa-location-dot" style="font-size: 12px; color: #2067da; flex-shrink: 0;"></i>
                <span style="overflow: hidden; text-overflow: ellipsis; color: #2067da;">{{ $address }} - <span style="text-decoration: underline; cursor: pointer;">View on map</span></span>
            </div>

            {{-- Dynamic Urgency Stock & Diverse Booking Activity --}}
            @php
                $rawRoomsLeft = $isObj ? ($item->rooms_left ?? null) : ($item['rooms_left'] ?? null);
                $roomsLeft = ($rawRoomsLeft !== null && is_numeric($rawRoomsLeft)) ? (int)$rawRoomsLeft : (($id % 4) + 1);

                $activityPatterns = [
                    "Booked " . (($id % 7) + 3) . " times today",
                    "In high demand — " . (($id % 4) + 2) . " booked in last 24h",
                    "Last booked " . (($id % 35) + 8) . " mins ago",
                    "Our lowest price in the last 30 days",
                    "Booked " . (($id % 6) + 3) . " times today",
                    "Trending stay in Cox's Bazar",
                    "Popular with couples & families",
                    "5 other travelers are looking right now",
                    "Great value for this destination",
                    "High booking demand today",
                ];
                $lastBookedText = $activityPatterns[$id % count($activityPatterns)];
            @endphp
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                @if($roomsLeft <= 2)
                <span style="background: #c53829; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 2px 7px; border-radius: 2px; line-height: 1.2; display: inline-block;">
                    Only {{ $roomsLeft }} left on our site!
                </span>
                @else
                <span style="background: #c53829; color: #ffffff; font-size: 11.5px; font-weight: 700; padding: 2px 7px; border-radius: 2px; line-height: 1.2; display: inline-block;">
                    {{ $roomsLeft }} still available
                </span>
                @endif
                <span style="color: #c53829; font-size: 12px; font-weight: 600;">
                    {{ $lastBookedText }}
                </span>
            </div>

            {{-- Dynamic Highlight Tags (Based on real database attributes) --}}
            @php
                $lowerName = strtolower($name);
                $landmarkLower = strtolower((string)$nearestLandmark);
                $hasBeach = str_contains($lowerName, 'beach') || str_contains($lowerName, 'sea') || str_contains($landmarkLower, 'beach') || str_contains($landmarkLower, 'inani') || str_contains($landmarkLower, 'laboni') || str_contains($landmarkLower, 'sugandha') || str_contains($landmarkLower, 'kolatoli');
                $hasPool = collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'pool'));
                $hasBreakfast = collect($lowerAmenities)->contains(fn($a) => str_contains($a, 'breakfast'));
                $hasHill = str_contains($lowerName, 'hill') || str_contains($landmarkLower, 'himchari') || str_contains($landmarkLower, 'himchori') || str_contains($landmarkLower, 'nature');
            @endphp
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px; flex-wrap: wrap;">
                @if($isFeatured)
                <span style="border: 1px solid #cbcfd7; background: #ffffff; color: #5e6b82; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 2px; display: inline-block; line-height: 1.2;">
                    Boosted
                </span>
                @endif

                @if($hasBeach)
                <span style="border: 1px solid #d0e2ff; background: #f0f7ff; color: #0043ce; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 2px; line-height: 1.2; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-water" style="font-size: 9px;"></i> Sea View
                </span>
                @elseif($hasHill)
                <span style="border: 1px solid #d1fae5; background: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 2px; line-height: 1.2; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-mountain-sun" style="font-size: 9px;"></i> Nature View
                </span>
                @endif

                @if($hasPool)
                <span style="border: 1px solid #e2e8f0; background: #f8fafc; color: #334155; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 2px; line-height: 1.2; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-person-swimming" style="font-size: 9px;"></i> Swimming pool
                </span>
                @endif

                @if($hasBreakfast)
                <span style="border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 2px; line-height: 1.2; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-mug-saucer" style="font-size: 9px;"></i> Free Breakfast
                </span>
                @endif
            </div>
            @if($noCreditCard)
            <div style="font-size: 12px; color: #2067da; font-weight: 500; display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2067da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                <span>Book without a credit card</span>
            </div>
            @endif
        </div>

        {{-- Selling Points & Highlights (Agoda Exact #5e6b82 Sparkle/Diamond + Feature Text) --}}
        @php
            $trophyText = match(true) {
                str_contains($lowerName, 'sea pearl')           => 'Best price for luxury 5-star beachfront resort',
                str_contains($lowerName, 'ocean paradise')      => 'Top choice for couples & private pool retreat',
                str_contains($lowerName, 'cox today')           => 'Prime central Kolatoli location with buffet breakfast',
                str_contains($lowerName, 'seagull')             => 'Iconic 5-star stay with direct beach walkway',
                str_contains($lowerName, 'best western bay')    => 'Scenic Marine Drive views & international luxury',
                str_contains($lowerName, 'best western heritage') => 'Top rated rooftop infinity pool & sea breeze',
                str_contains($lowerName, 'best western')        => 'Scenic Marine Drive views & world-class hospitality',
                str_contains($lowerName, 'long beach')          => 'Complimentary airport shuttle & multi-cuisine dining',
                str_contains($lowerName, 'ramada')              => 'Wyndham quality standards at exceptional value',
                str_contains($lowerName, 'dera resort')         => 'Peaceful eco retreat & wellness spa sanctuary',
                str_contains($lowerName, 'grand pacific')       => 'Direct access to Sugandha beach & night market',
                str_contains($lowerName, 'windy terrace')       => 'Spacious family suites with private balconies',
                str_contains($lowerName, 'neeshorgo')           => 'Panoramic forest & Bay of Bengal horizon views',
                str_contains($lowerName, 'suite sadaf')         => 'Quiet romantic getaway near Kolatoli beach',
                str_contains($lowerName, 'praasad paradise')    => 'Sprawling lush green lawn & family beach access',
                str_contains($lowerName, 'shopnil')             => 'Charming seaside stay with authentic local seafood',
                str_contains($lowerName, 'exotica sampan')      => 'Boutique resort with private beach sunset terrace',
                str_contains($lowerName, 'grace cox')           => 'Modern smart rooms with central city convenience',
                str_contains($lowerName, 'royal pearl')         => 'Executive comfort & personalized guest service',
                str_contains($lowerName, 'green nature')        => 'Lush eco-resort surrounded by scenic nature trails',
                $stars >= 5                                     => 'Top value for 5-star luxury stay',
                $stars == 4                                     => 'Best price for 4-star property',
                $hasPool && $hasBreakfast                       => 'Popular stay with swimming pool & free breakfast',
                $stars == 3                                     => 'Great value 3-star stay in ' . ($cityStr ?: "Cox's Bazar"),
                default                                         => 'Verified quality stay with guaranteed lowest rate',
            };
        @endphp
        <div style="display: flex; flex-direction: column; gap: 3px; margin-top: auto;">
            <div style="font-size: 12px; color: #0e8345; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-trophy" style="font-size: 11px; color: #0e8345;"></i>
                <span>{{ $trophyText }}</span>
            </div>
            @if(!empty($nearestLandmark))
            <div style="font-size: 12px; color: #5e6b82; display: flex; align-items: flex-start; gap: 6px; line-height: 1.35;">
                <span style="font-size: 10px; color: #5e6b82; line-height: 1.4; flex-shrink: 0;">✦</span>
                <span style="color: #5e6b82;">{{ $nearestLandmark }}</span>
            </div>
            @else
            <div style="font-size: 12px; color: #5e6b82; display: flex; align-items: flex-start; gap: 6px; line-height: 1.35;">
                <span style="font-size: 10px; color: #5e6b82; line-height: 1.4; flex-shrink: 0;">✦</span>
                <span style="color: #5e6b82;">Direct Sea Beach Access, Verified Location</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Column 3: Right Price & Review Section (100% 1:1 Agoda Live Parity) --}}
    <div class="agoda-card-price-col">
        
        {{-- Top: Review Score Box (Exact 1:1 Agoda Speech Bubble & Label Parity) --}}
        <div style="display: flex; flex-direction: column; align-items: flex-end;">
            <div style="display: inline-flex; align-items: center; gap: 5px;">
                <span style="background: #1a62dc; color: #ffffff; font-size: 12.5px; font-weight: 700; padding: 2px 6px; border-radius: 4px 4px 4px 0; line-height: 1.2; display: inline-block;">
                    {{ $score }}
                </span>
                <span style="color: #1a62dc; font-size: 13.5px; font-weight: 700; line-height: 1.2;">
                    {{ $ratingLabel }}
                </span>
            </div>
            <div style="font-size: 11px; color: #737373; margin-top: 2px;">
                {{ $reviewsCount }} reviews
            </div>
            @if($locationScoreNum > 8.0)
            <div style="font-size: 11px; font-weight: 600; color: #5a6b82; margin-top: 2px;">
                {{ $locationScore }} Location score
            </div>
            @endif
        </div>

        {{-- Bottom: Price Section (Agoda Exact USD/BDT & #e12d2d Price with Smart Coupon Badges) --}}
        <div style="text-align: right;">
            {{-- Smart Dynamic Coupon & Voucher Badges (Agoda 1:1 Parity) --}}
            @php
                $couponAmount = max(150, round($priceVal * 0.12));
                $couponTypes = [
                    ['text' => $currencyCode . ' ' . number_format($couponAmount) . ' coupon applied', 'icon' => 'fa-ticket'],
                    ['text' => '8% Secret Price applied', 'icon' => 'fa-tag'],
                    ['text' => $currencyCode . ' ' . number_format($couponAmount) . ' voucher applied', 'icon' => 'fa-award'],
                    ['text' => 'Daily Member Deal applied', 'icon' => 'fa-star'],
                ];
                $cardCoupon = $couponTypes[$id % count($couponTypes)];
            @endphp

            @if($discount && $discount > 0)
            <div style="margin-bottom: 4px;">
                <span class="agoda-coupon-pill">
                    <i class="fa-solid {{ $cardCoupon['icon'] }}" style="font-size: 10px;"></i> {{ $cardCoupon['text'] }}
                </span>
            </div>
            @elseif(($id % 3) === 0)
            <div style="margin-bottom: 4px;">
                <button type="button" class="agoda-collect-btn" onclick="event.stopPropagation(); collectCardDeal(this, '{{ $id }}', '{{ $currencyCode }} {{ number_format($couponAmount) }}');">
                    <i class="fa-solid fa-tags" style="font-size: 10px;"></i> Collect {{ $currencyCode }} {{ number_format($couponAmount) }}
                </button>
            </div>
            @endif

            @if($origPrice && $discount && $discount > 0)
            <div style="margin-bottom: 1px;">
                <span style="font-size: 14px; color: #555555; font-weight: 700; text-decoration: line-through; text-decoration-color: #e12d2d; text-decoration-thickness: 1.5px;">{{ $origPrice }}</span>
                <span style="font-size: 13px; font-weight: 700; color: #e12d2d; margin-left: 4px;">-{{ $discount }}%</span>
            </div>
            @endif

            <div style="line-height: 1; margin-bottom: 2px;">
                <span style="font-size: 15px; font-weight: 700; color: #555555; margin-right: 3px;">{{ $currencyCode }}</span>
                <span style="font-size: 26px; font-weight: 800; color: #e12d2d;">{{ $price }}</span>
            </div>

            <div style="font-size: 10px; color: #737373; line-height: 1.2; margin-top: 2px;">
                Per night before taxes and fees
            </div>
            @if($freeCancellation)
            <div style="font-size: 11px; font-weight: 700; color: #0e8345; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.2px;">
                + FREE CANCELLATION
            </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- Video Modal (if video exists) --}}
@if(!empty($videoUrl))
<div class="modal fade" id="propertyVideoModal_{{ $id }}" tabindex="-1" aria-hidden="true" onclick="event.stopPropagation();">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden bg-dark">
            <div class="modal-header border-0 py-2 px-3 bg-black text-white">
                <h6 class="modal-title fw-bold mb-0" style="font-size: 13.5px;"><i class="fa-solid fa-circle-play text-danger me-2"></i> {{ $name }} — Official Video Tour</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <div class="ratio ratio-16x9">
                    <video id="cardVideo_{{ $id }}" controls playsinline loop preload="metadata" class="w-100 h-100" style="object-fit: contain; background: #000;">
                        <source src="{{ media_url($videoUrl) }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var carEl = document.getElementById('propertyCarousel_{{ $id }}');
        if (carEl) {
            carEl.addEventListener('slid.bs.carousel', function (evt) {
                var badge = document.getElementById('imgBadge_{{ $id }}');
                if (badge) {
                    badge.textContent = (evt.to + 1) + '/{{ $totalImgs }}';
                }
            });
        }

        var vModal = document.getElementById('propertyVideoModal_{{ $id }}');
        var vEl = document.getElementById('cardVideo_{{ $id }}');
        if (vModal && vEl) {
            vModal.addEventListener('shown.bs.modal', function () {
                vEl.currentTime = 0;
                vEl.muted = false;
                vEl.volume = 1.0;
                var playProm = vEl.play();
                if (playProm !== undefined) {
                    playProm.catch(function() {
                        // In case browser policy restricts unmuted autoplay, play muted and let user unmute
                        vEl.muted = true;
                        vEl.play();
                    });
                }
            });
            vModal.addEventListener('hidden.bs.modal', function () {
                vEl.pause();
            });
        }
    });

    if (!window.nextCardPhoto) {
        window.nextCardPhoto = function(btn, event, propId, totalImgs) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            const carouselEl = document.getElementById('propertyCarousel_' + propId);
            if (!carouselEl) return;
            const items = carouselEl.querySelectorAll('.carousel-inner .carousel-item');
            if (!items || items.length <= 1) return;

            let currentIdx = 0;
            items.forEach(function(item, idx) {
                if (item.classList.contains('active')) {
                    currentIdx = idx;
                }
            });

            const nextIdx = (currentIdx + 1) % items.length;
            items[currentIdx].classList.remove('active');
            items[nextIdx].classList.add('active');

            const badge = document.getElementById('imgBadge_' + propId);
            if (badge) {
                badge.textContent = (nextIdx + 1) + '/' + (totalImgs || items.length);
            }

            const prevBtn = document.getElementById('prevCardBtn_' + propId);
            if (prevBtn) {
                prevBtn.style.display = nextIdx > 0 ? 'flex' : 'none';
            }
        };
    }

    if (!window.prevCardPhoto) {
        window.prevCardPhoto = function(btn, event, propId, totalImgs) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            const carouselEl = document.getElementById('propertyCarousel_' + propId);
            if (!carouselEl) return;
            const items = carouselEl.querySelectorAll('.carousel-inner .carousel-item');
            if (!items || items.length <= 1) return;

            let currentIdx = 0;
            items.forEach(function(item, idx) {
                if (item.classList.contains('active')) {
                    currentIdx = idx;
                }
            });

            const prevIdx = (currentIdx - 1 + items.length) % items.length;
            items[currentIdx].classList.remove('active');
            items[prevIdx].classList.add('active');

            const badge = document.getElementById('imgBadge_' + propId);
            if (badge) {
                badge.textContent = (prevIdx + 1) + '/' + (totalImgs || items.length);
            }

            const prevBtn = document.getElementById('prevCardBtn_' + propId);
            if (prevBtn) {
                prevBtn.style.display = prevIdx > 0 ? 'flex' : 'none';
            }
        };
    }

    if (!window.toggleCardWishlist) {
        window.toggleCardWishlist = function(btn, propId) {
            @auth
                fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ property_id: propId })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var icon = btn.querySelector('i');
                    if (icon && data.success !== undefined) {
                        if (data.isWishlisted) {
                            icon.className = 'fa-solid text-danger fa-heart';
                            btn.title = 'Remove from Wishlist';
                        } else {
                            icon.className = 'fa-regular text-dark fa-heart';
                            btn.title = 'Save to Wishlist';
                        }
                    }
                })
                .catch(function(err) { console.error('Wishlist toggle error:', err); });
            @else
                window.location.href = '{{ route('login') }}';
            @endauth
        };
    }

    if (!window.collectCardDeal) {
        window.collectCardDeal = function(btn, propId, amountText) {
            btn.innerHTML = '<i class="fa-solid fa-check text-success" style="font-size: 10px;"></i> ' + amountText + ' applied';
            btn.className = 'agoda-coupon-pill';
            btn.style.border = '1px solid #a3e6be';
            btn.style.background = '#edfcf2';
            btn.style.color = '#0e8345';
            btn.onclick = null;
            try {
                sessionStorage.setItem('collected_coupon_' + propId, amountText);
            } catch(e){}
        };
    }
</script>
