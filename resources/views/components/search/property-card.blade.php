{{-- Agoda-Exact Property Card Component (100% Dynamic Image Carousel & Video Tour Parity) --}}
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
    $roomsLeft         = intval($isObj ? ($item->rooms_left ?? 5) : ($item['rooms_left'] ?? 5));
    $noCreditCard      = boolval($isObj ? ($item->no_credit_card_required ?? true) : ($item['no_credit_card_required'] ?? true));
    $freeCancellation  = boolval($isObj ? ($item->free_cancellation ?? true) : ($item['free_cancellation'] ?? true));
    $cityStr           = $isObj ? ($item->city ?? '') : ($item['city'] ?? '');
    $address           = $isObj ? ($item->address ?? $cityStr) : ($item['address'] ?? $cityStr);
    if (empty($address)) $address = $cityStr ?: 'Bangladesh';

    $revCount          = $isObj ? ($item->total_reviews ?? $item->reviews_count ?? 0) : ($item['total_reviews'] ?? $item['reviews_count'] ?? 0);
    $reviewsCount      = number_format((int)$revCount);

    $ratingLabel       = match(true) {
        $scoreNum >= 9.0 => 'Superb',
        $scoreNum >= 8.0 => 'Very good',
        $scoreNum >= 7.0 => 'Good',
        $scoreNum >= 6.0 => 'Pleasant',
        default          => 'Rated',
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
    $nearestLandmark   = $calcDist ? "{$calcDist} from your searched location" : ($rawLandmark ?: (!empty($cityStr) ? "Convenient location in {$cityStr}" : ''));
    $type              = $isObj ? ($item->type ?? 'Hotel') : ($item['type'] ?? 'Hotel');
    $stars             = intval($isObj ? ($item->star_rating ?? 4) : ($item['star_rating'] ?? 4));
    $videoUrl          = $isObj ? ($item->video_url ?? null) : ($item['video_url'] ?? null);
    $finalVideoUrl     = !empty($videoUrl) ? $videoUrl : 'https://www.youtube.com/embed/dQw4w9WgXcQ';

    $amenitiesList     = is_array($isObj ? ($item->amenities ?? null) : ($item['amenities'] ?? null)) ? ($isObj ? $item->amenities : $item['amenities']) : ['Free WiFi', 'Swimming pool', 'Breakfast included'];

    $isWishlisted      = auth()->check() 
        ? \App\Models\Wishlist::where('user_id', auth()->id())->where('property_id', $id)->exists()
        : false;
@endphp

<style>
    .agoda-3d-property-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        background-color: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06), 0 2px 5px rgba(0, 0, 0, 0.03);
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
    }
    .agoda-3d-property-card:hover {
        transform: translateY(-5px);
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
        box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.16), 0 8px 16px -4px rgba(15, 23, 42, 0.10) !important;
    }
</style>

<div class="card overflow-hidden mb-3 agoda-3d-property-card property-card-clickable" onclick="if(!event.target.closest('button, a, form, input, select, .carousel-control-prev, .carousel-control-next')){ window.location.href='{{ route('hotels.show', $id) }}'; }">
    <a href="{{ route('hotels.show', $id) }}" class="stretched-link" aria-label="{{ $name }}" style="z-index: 1;"></a>
    <div class="row g-0">

        {{-- Column 1: Interactive Image Carousel Slider & Video Button --}}
        <div class="col-md-4 position-relative style-image-box overflow-hidden" style="min-height: 220px;">
            
            {{-- Carousel Slider Container --}}
            <div id="propertyCarousel_{{ $id }}" class="carousel slide h-100 w-100" data-bs-interval="false">
                <div class="carousel-inner h-100 w-100">
                    @foreach($gallery as $gIdx => $gImg)
                        <div class="carousel-item h-100 w-100 @if($gIdx === 0) active @endif">
                            <a href="{{ route('hotels.show', $id) }}" class="d-block w-100 h-100">
                                <img src="{{ $gImg }}" class="w-100 h-100" style="object-fit: cover; position: absolute; top: 0; left: 0;" alt="{{ $name }}">
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Left Arrow Button --}}
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel_{{ $id }}" data-bs-slide="prev" style="width: 30px; height: 30px; background: rgba(0,0,0,0.65); border-radius: 50%; top: 50%; left: 8px; transform: translateY(-50%); opacity: 0.9; border: none; z-index: 10;">
                    <span class="carousel-control-prev-icon" style="width: 12px; height: 12px;" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                {{-- Right Arrow Button --}}
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel_{{ $id }}" data-bs-slide="next" style="width: 30px; height: 30px; background: rgba(0,0,0,0.65); border-radius: 50%; top: 50%; right: 8px; transform: translateY(-50%); opacity: 0.9; border: none; z-index: 10;">
                    <span class="carousel-control-next-icon" style="width: 12px; height: 12px;" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            
            {{-- Wishlist Heart Circle Icon --}}
            <form action="{{ route('wishlist.toggle') }}" method="POST" class="position-absolute top-0 start-0 m-2" style="z-index: 12;">
                @csrf
                <input type="hidden" name="property_id" value="{{ $id }}">
                <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255,255,255,0.92); border: none;" title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Save to Wishlist' }}">
                    <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-dark' }} fa-heart" style="font-size: 14px;"></i>
                </button>
            </form>

            {{-- Video Tour Button (Dynamic when available in DB) --}}
            @if(!empty($videoUrl))
            <div class="position-absolute top-0 end-0 m-2" style="z-index: 12;">
                <button type="button" class="btn btn-dark btn-sm rounded-pill px-2.5 py-1 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 10px; background: rgba(15, 23, 42, 0.88); border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(4px);" data-bs-toggle="modal" data-bs-target="#propertyVideoModal_{{ $id }}">
                    <i class="fa-solid fa-play text-danger" style="font-size: 9px;"></i>
                    <span class="fw-bold text-white" style="font-size: 9.5px; letter-spacing: 0.3px;">VIDEO TOUR</span>
                </button>
            </div>
            @endif

            {{-- Dynamic Image Counter Pill Badge (Updates on Carousel Slide) --}}
            <div class="position-absolute bottom-0 start-0 m-2" style="z-index: 12;">
                <span class="badge bg-dark bg-opacity-75 text-white fw-bold px-2 py-1" id="imgBadge_{{ $id }}" style="font-size: 10.5px; border-radius: 4px; letter-spacing: 0.5px;">
                    1/{{ $totalImgs }}
                </span>
            </div>
        </div>

        {{-- Column 2: Details & Content Badges --}}
        <div class="col-md-5 p-3 d-flex flex-column justify-content-between border-end" style="border-color: #f1f5f9 !important;">
            <div>
                {{-- Title & Star Rating --}}
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h5 class="fw-bold mb-0" style="font-size: 16.5px; line-height: 1.3; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <a href="{{ route('hotels.show', $id) }}" class="text-decoration-none text-dark hover-primary" style="color: #1d2b45 !important;">
                            {{ $name }}
                        </a>
                    </h5>
                    <div class="text-warning flex-shrink-0" style="font-size: 11px;">
                        @for($i = 0; $i < $stars; $i++)
                            <i class="fa-solid fa-star"></i>
                        @endfor
                    </div>
                </div>

                {{-- Location Link (Entire Line Clickable to Open and Center Hotel on Map) --}}
                <div class="small mb-1 d-inline-flex align-items-center flex-wrap" style="font-size: 12.5px; color: #2067e1 !important; font-weight: 500; cursor: pointer; position: relative; z-index: 20;" onclick="event.preventDefault(); event.stopPropagation(); openHotelOnMap({{ $id }});" title="Click to view {{ $name }} on interactive map">
                    <i class="fa-solid fa-location-dot text-primary me-1.5 flex-shrink-0"></i>
                    <span style="text-decoration: underline; text-underline-offset: 2px;">{{ $address }}</span>
                    <span class="mx-1 text-secondary">—</span>
                    <span class="fw-bold" style="text-decoration: underline; text-underline-offset: 2px;">View on map</span>
                </div>

                {{-- Nearest Landmark / Distance Content --}}
                @if(!empty($nearestLandmark))
                <div class="text-secondary mb-2" style="font-size: 11.5px; color: #64748b !important;">
                    <i class="fa-solid fa-train me-1 text-muted"></i> {{ $nearestLandmark }}
                </div>
                @endif

                {{-- Agoda 1:1 Content Badges (Urgent Stock + Payment Policy) --}}
                <div class="d-flex flex-column gap-1.5 mb-2">

                    {{-- 1. Urgent Stock Counter Badge --}}
                    @if($roomsLeft <= 5)
                    <div>
                        <span class="badge bg-danger text-white fw-bold px-2 py-1" style="font-size: 11px; border-radius: 3px;">
                            Only {{ $roomsLeft }} left
                        </span>
                    </div>
                    @endif

                    {{-- 2. Book without a credit card Tag --}}
                    @if($noCreditCard)
                    <div style="color: #2067e1; font-weight: 500; font-size: 12px;">
                        <i class="fa-solid fa-credit-card me-1"></i> Book without a credit card
                    </div>
                    @endif
                </div>

            </div>

            {{-- Bottom Amenities Strip --}}
            <div class="pt-2 border-top d-flex align-items-center gap-3 text-secondary" style="border-color: #f8fafc !important; font-size: 11.5px;">
                @if(in_array('Free WiFi', $amenitiesList))
                <span title="Free WiFi"><i class="fa-solid fa-wifi me-1 text-primary"></i> Free WiFi</span>
                @endif
                @if($freeCancellation)
                <span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Free cancellation</span>
                @endif
            </div>
        </div>

        {{-- Column 3: Review Score & Price Block --}}
        <div class="col-md-3 p-3 bg-white d-flex flex-column justify-content-between text-end">
            
            {{-- Review Rating & Location Score Content Block --}}
            <div>
                <div class="d-flex align-items-center justify-content-end gap-2 mb-1">
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 13.5px; color: #1d2b45 !important; line-height: 1.1;">{{ $ratingLabel }}</div>
                        <small class="text-secondary" style="font-size: 11px;">{{ $reviewsCount }} reviews</small>
                    </div>
                    <div class="text-white fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #2067e1; border-radius: 8px 8px 8px 0; font-size: 15px;">
                        {{ $score }}
                    </div>
                </div>

                {{-- Location Score & Reward Cashback Content --}}
                <div class="text-secondary" style="font-size: 11px; font-weight: 600; color: #475569 !important;">
                    {{ $locationScore }} Location score
                </div>
                <div style="font-size: 11px; font-weight: 600; color: #2067e1; margin-top: 2px;">
                    <i class="fa-solid fa-gift me-1 text-primary"></i> Earn 250 Prime Points
                </div>
            </div>

            {{-- Price Section --}}
            <a href="{{ route('hotels.show', $id) }}" class="mt-2 text-decoration-none d-block">
                @auth
                <div class="mb-1">
                    <span class="badge bg-warning bg-opacity-10 text-dark fw-bold border border-warning-subtle rounded px-2 py-1" style="font-size: 10.5px;">
                        <i class="fa-solid fa-crown text-warning me-1"></i> VIP Member Price
                    </span>
                </div>
                @else
                <div class="mb-1">
                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold border border-danger-subtle rounded px-2 py-0.5" style="font-size: 10.5px;">
                        <i class="fa-solid fa-lock me-1"></i> Secret Deal: 10% Off
                    </span>
                </div>
                @endauth

                <small class="text-muted d-block" style="font-size: 10px; line-height: 1.2;">Per night before taxes &amp; fees</small>

                <div class="fw-bold text-dark mb-0" style="font-size: 22px; color: #1d2b45 !important; line-height: 1.1; font-family: 'Plus Jakarta Sans', sans-serif;">
                    {{ \App\Services\CurrencyService::format($priceVal) }}
                </div>

                @if($freeCancellation)
                <div class="fw-bold text-success mt-1" style="font-size: 11px; letter-spacing: 0.2px;">
                    + FREE CANCELLATION
                </div>
                @endif
            </a>

        </div>

    </div>
</div>

{{-- Interactive Hotel Video Tour Modal (Rendered only when video exists in DB) --}}
@if(!empty($videoUrl))
<div class="modal fade" id="propertyVideoModal_{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden bg-dark">
            <div class="modal-header border-0 py-2.5 px-3 bg-black text-white">
                <h6 class="modal-title fw-bold mb-0" style="font-size: 14px;"><i class="fa-solid fa-circle-play text-danger me-2"></i> {{ $name }} — Official Video Tour</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <div class="ratio ratio-16x9">
                    <iframe src="" data-src="{{ $videoUrl }}" title="Hotel Video Tour" allowfullscreen class="w-100 h-100 border-0"></iframe>
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
                var activeIndex = evt.to + 1;
                var badge = document.getElementById('imgBadge_{{ $id }}');
                if (badge) {
                    badge.textContent = activeIndex + '/{{ $totalImgs }}';
                }
            });
        }
    });
</script>
