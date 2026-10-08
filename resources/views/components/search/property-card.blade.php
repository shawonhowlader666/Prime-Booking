{{-- Agoda-Exact Property Card Component (100% 1:1 Parity with Agoda Screenshot 1) --}}
@props(['item'])

@php
    $isObj             = is_object($item);
    $id                = $isObj ? ($item->id ?? 1) : ($item['id'] ?? 1);
    $name              = $isObj ? ($item->name ?? 'Luxury Resort') : ($item['name'] ?? 'Luxury Resort');
    $priceVal          = (float) ($isObj ? ($item->price_per_night ?? $item->price ?? 12500) : ($item['price_per_night'] ?? $item['price'] ?? 12500));
    $price             = number_format($priceVal, 0);
    $origPriceVal      = $isObj ? ($item->original_price ?? null) : ($item['original_price'] ?? null);
    $origPrice         = $origPriceVal && $origPriceVal > 0 ? number_format((float)$origPriceVal, 0) : null;
    $discount          = ($origPriceVal && $origPriceVal > $priceVal) 
                        ? round((($origPriceVal - $priceVal) / $origPriceVal) * 100) 
                        : null;
    
    $scoreNum          = (float) ($isObj ? ($item->rating_score ?? $item->rating ?? 8.5) : ($item['rating_score'] ?? $item['rating'] ?? 8.5));
    $score             = number_format($scoreNum, 1);
    $locationScoreNum  = (float) ($isObj ? ($item->location_score ?? 8.8) : ($item['location_score'] ?? 8.8));
    $locationScore     = number_format($locationScoreNum, 1);
    $roomsLeft         = intval($isObj ? ($item->rooms_left ?? 4) : ($item['rooms_left'] ?? 4));
    $noCreditCard      = boolval($isObj ? ($item->no_credit_card_required ?? true) : ($item['no_credit_card_required'] ?? true));
    $freeCancellation  = boolval($isObj ? ($item->free_cancellation ?? true) : ($item['free_cancellation'] ?? true));
    $cityStr           = $isObj ? ($item->city ?? '') : ($item['city'] ?? '');
    $address           = $isObj ? ($item->address ?? $cityStr) : ($item['address'] ?? $cityStr);
    if (empty($address)) $address = $cityStr ?: 'Bangladesh';

    $revCount          = $isObj ? ($item->total_reviews ?? $item->reviews_count ?? 12) : ($item['total_reviews'] ?? $item['reviews_count'] ?? 12);
    $reviewsCount      = number_format((int)$revCount);

    $ratingLabel       = match(true) {
        $scoreNum >= 9.0 => 'Superb',
        $scoreNum >= 8.0 => 'Very good',
        $scoreNum >= 7.0 => 'Good',
        $scoreNum >= 6.0 => 'Pleasant',
        default          => 'Good',
    };

    $image             = $isObj ? ($item->primary_image ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80') : ($item['primary_image'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
    $imagesList        = is_array($isObj ? ($item->images ?? null) : ($item['images'] ?? null)) ? ($isObj ? $item->images : $item['images']) : [];
    
    $defaultGallery    = [
        $image,
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80'
    ];
    $gallery           = (!empty($imagesList) && count($imagesList) > 1) ? $imagesList : $defaultGallery;
    $totalImgs         = count($gallery);

    $targetLat         = request('lat') ? (float)request('lat') : null;
    $targetLng         = request('lng') ? (float)request('lng') : null;
    $calcDist          = null;
    if ($isObj && method_exists($item, 'getFormattedDistanceTo') && $targetLat && $targetLng) {
        $calcDist      = $item->getFormattedDistanceTo($targetLat, $targetLng);
    }

    $rawLandmark       = $isObj ? ($item->nearest_landmark ?? null) : ($item['nearest_landmark'] ?? null);
    $nearestLandmark   = $calcDist ? "{$calcDist} from your searched location" : ($rawLandmark ?: (!empty($cityStr) ? "3.0 km from {$cityStr} Center" : 'Near city center'));
    $type              = $isObj ? ($item->type ?? 'Hotel') : ($item['type'] ?? 'Hotel');
    $stars             = intval($isObj ? ($item->star_rating ?? 4) : ($item['star_rating'] ?? 4));
    $videoUrl          = $isObj ? ($item->video_url ?? null) : ($item['video_url'] ?? null);

    $isWishlisted      = auth()->check() 
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('property_id', $id)->exists()
        : false;
    
    $currencyCode      = \App\Helpers\CurrencyHelper::current();
@endphp

{{-- Main Property Card Container (Exact Agoda Screenshot 1 Dimensions & Layout) --}}
<div class="agoda-property-card-root" style="background: #ffffff; border: 1px solid #e0e0e0; border-radius: 4px; min-height: 215px; margin-bottom: 14px; display: flex; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow 0.2s ease-in-out; position: relative;" onclick="if(!event.target.closest('button, a, form, input, select, .carousel-control-prev, .carousel-control-next')){ window.location.href='{{ route('hotels.show', $id) }}'; }">
    <a href="{{ route('hotels.show', $id) }}" class="stretched-link" aria-label="{{ $name }}" style="z-index: 1;"></a>

    {{-- Column 1: Left Showcase Image Carousel (Width: 246px Fixed, Exact Agoda Dimensions) --}}
    <div style="width: 246px; min-width: 246px; height: auto; min-height: 215px; position: relative; overflow: hidden; background: #e9ebee; flex-shrink: 0;">
        
        <div id="propertyCarousel_{{ $id }}" class="carousel slide h-100 w-100" data-bs-interval="false">
            <div class="carousel-inner h-100 w-100">
                @foreach($gallery as $gIdx => $gImg)
                    <div class="carousel-item h-100 w-100 @if($gIdx === 0) active @endif">
                        <img src="{{ media_url($gImg) }}" style="width: 100%; height: 100%; min-height: 215px; object-fit: cover; display: block;" alt="{{ $name }}" loading="lazy">
                    </div>
                @endforeach
            </div>

            {{-- Prev Arrow --}}
            <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel_{{ $id }}" data-bs-slide="prev" style="width: 24px; height: 24px; background: rgba(255,255,255,0.9); border-radius: 50%; top: 50%; left: 8px; transform: translateY(-50%); opacity: 0.9; border: none; z-index: 10; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-chevron-left text-dark" style="font-size: 10px;"></i>
            </button>

            {{-- Next Arrow (Exact Agoda White Circle on Right Edge) --}}
            <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel_{{ $id }}" data-bs-slide="next" style="width: 24px; height: 24px; background: rgba(255,255,255,0.9); border-radius: 50%; top: 50%; right: 8px; transform: translateY(-50%); opacity: 0.9; border: none; z-index: 10; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-chevron-right text-dark" style="font-size: 10px;"></i>
            </button>
        </div>

        {{-- Wishlist Heart Button (Top Right of Image - Exact Screenshot 1 Parity) --}}
        <form action="{{ route('wishlist.toggle') }}" method="POST" style="position: absolute; top: 8px; right: 8px; z-index: 12; margin: 0;">
            @csrf
            <input type="hidden" name="property_id" value="{{ $id }}">
            <button type="submit" style="width: 28px; height: 28px; background: rgba(255,255,255,0.95); border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 4px rgba(0,0,0,0.18); cursor: pointer;" title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Save to Wishlist' }}">
                <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-dark' }} fa-heart" style="font-size: 12px;"></i>
            </button>
        </form>

        {{-- Video Tour Button (when available, Top Left) --}}
        @if(!empty($videoUrl))
        <div style="position: absolute; top: 8px; left: 8px; z-index: 12;">
            <button type="button" class="btn btn-dark btn-sm rounded-pill px-2 py-0.5 shadow-sm d-flex align-items-center gap-1" style="font-size: 9px; background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(255,255,255,0.4);" data-bs-toggle="modal" data-bs-target="#propertyVideoModal_{{ $id }}">
                <i class="fa-solid fa-play text-danger" style="font-size: 8px;"></i>
                <span class="fw-bold text-white">VIDEO</span>
            </button>
        </div>
        @endif

        {{-- 1/10 Image Counter Badge (Bottom Left - Exact Screenshot 1 Parity) --}}
        <div style="position: absolute; bottom: 8px; left: 8px; z-index: 12;">
            <span style="background: rgba(0,0,0,0.7); color: #ffffff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 2px;" id="imgBadge_{{ $id }}">
                1/{{ $totalImgs }}
            </span>
        </div>
    </div>

    {{-- Column 2: Center Hotel Information (Exact Agoda Screenshot 1 Parity) --}}
    <div style="flex: 1; padding: 12px 16px; display: flex; flex-direction: column; justify-content: flex-start; text-align: left;">
        <div>
            {{-- Hotel Title --}}
            <h3 style="font-size: 17px; font-weight: 700; color: #0283df; margin: 0 0 3px 0; line-height: 1.25; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                <a href="{{ route('hotels.show', $id) }}" style="color: #0283df; text-decoration: none;" class="hover-underline">
                    {{ $name }}
                </a>
            </h3>

            {{-- Star Rating --}}
            <div style="color: #ffa726; font-size: 11px; margin-bottom: 5px;">
                @for($s = 0; $s < $stars; $s++)
                    <i class="fa-solid fa-star"></i>
                @endfor
            </div>

            {{-- Location Line --}}
            <div style="font-size: 12px; color: #0283df; font-weight: 500; margin-bottom: 3px; display: flex; align-items: center; gap: 4px;">
                <i class="fa-solid fa-location-dot" style="font-size: 11px;"></i>
                <span>{{ $address }} - <span style="text-decoration: underline; cursor: pointer;">View on map</span></span>
            </div>

            {{-- Distance from Landmark --}}
            @if(!empty($nearestLandmark))
            <div style="font-size: 11.5px; color: #777777; margin-bottom: 7px;">
                {{ $nearestLandmark }}
            </div>
            @endif

            {{-- Urgency Stock Tag (Screenshot 1: "4 still available Last booked 11 hrs ago") --}}
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; flex-wrap: wrap;">
                @if($roomsLeft <= 5)
                <span style="background: #e03126; color: #ffffff; font-size: 10.5px; font-weight: 700; padding: 1px 5px; border-radius: 2px;">
                    {{ $roomsLeft }} still available
                </span>
                <span style="color: #e03126; font-size: 11px; font-weight: 600;">
                    Last booked 11 hrs ago
                </span>
                @else
                <span style="background: #09b057; color: #ffffff; font-size: 10.5px; font-weight: 700; padding: 1px 5px; border-radius: 2px;">
                    Popular Choice
                </span>
                @endif
            </div>

            {{-- Payment Policy (Screenshot 1: "Book without a credit card") --}}
            @if($noCreditCard)
            <div style="font-size: 11.5px; color: #0283df; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                <i class="fa-regular fa-credit-card" style="font-size: 11px;"></i>
                <span>Book without a credit card</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Column 3: Right Price & Review Section (Width: 190px Fixed, Exact Agoda Screenshot 1 Parity) --}}
    <div style="width: 190px; min-width: 190px; padding: 12px 16px; text-align: right; display: flex; flex-direction: column; justify-content: space-between; border-left: 1px solid #f0f0f0; background: #ffffff; flex-shrink: 0;">
        
        {{-- Top: Review Score Box --}}
        <div style="text-align: right;">
            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 8px;">
                <div style="text-align: right; line-height: 1.15;">
                    <div style="font-size: 13px; font-weight: 700; color: #0283df;">{{ $ratingLabel }}</div>
                    <div style="font-size: 11px; color: #777777;">{{ $reviewsCount }} reviews</div>
                </div>
                <div style="background: #0283df; color: #ffffff; font-size: 15px; font-weight: 700; padding: 4px 7px; border-radius: 4px; min-width: 36px; text-align: center;">
                    {{ $score }}
                </div>
            </div>
            @if($locationScoreNum > 0)
            <div style="font-size: 11px; font-weight: 600; color: #555555; margin-top: 4px;">
                {{ $locationScore }} Location score
            </div>
            @endif
        </div>

        {{-- Bottom: Price Section (Exact Agoda Red Price + Tax Note + Free Cancellation) --}}
        <div style="text-align: right;">
            <div style="line-height: 1.1; margin-bottom: 2px;">
                <span style="font-size: 13px; font-weight: 700; color: #333333;">{{ $currencyCode }}</span>
                <span style="font-size: 24px; font-weight: 700; color: #e03126;">{{ $price }}</span>
            </div>

            <div style="font-size: 10px; color: #777777; margin-bottom: 2px;">
                Per night before taxes and fees
            </div>

            @if($freeCancellation)
            <div style="font-size: 10.5px; font-weight: 700; color: #09b057; letter-spacing: 0.3px;">
                + FREE CANCELLATION
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Video Modal (if video exists) --}}
@if(!empty($videoUrl))
<div class="modal fade" id="propertyVideoModal_{{ $id }}" tabindex="-1" aria-hidden="true">
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
    });
</script>
