@extends('layouts.main', ['activePage' => 'services'])

@section('title', 'Search Hotels & Stays in ' . ($destination ?: 'Bangladesh') . ' | PRIME BOOKING')

@section('content')
@include('components.search.loading-skeleton-modal')

{{-- 2. Agoda Compact Subheader Search Bar --}}
<div style="background-color: #1d2b45; padding: 12px 0; border-bottom: 1px solid #334155;">
    <div style="max-width: 1140px; margin: 0 auto; padding: 0 16px;">
        <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center" id="searchHeaderForm" onsubmit="showAgodaSearchLoading();">
            <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 text-secondary"><i class="fa-solid fa-magnifying-glass" style="color: #2067e1;"></i></span>
                    <input type="text" name="destination" class="form-control border-0 rounded-end-3" value="{{ $destination }}" placeholder="Enter destination or property" style="height: 42px; font-weight: 600; font-size: 14px;">
                </div>
            </div>
            <div class="col-md-2">
                <input type="date" name="check_in" class="form-control rounded-3" value="{{ $checkIn }}" style="height: 42px; font-weight: 500; font-size: 14px;">
            </div>
            <div class="col-md-2">
                <input type="date" name="check_out" class="form-control rounded-3" value="{{ $checkOut }}" style="height: 42px; font-weight: 500; font-size: 14px;">
            </div>
            <div class="col-md-3">
                <select name="guests" class="form-select rounded-3" style="height: 42px; font-weight: 500; font-size: 14px;">
                    <option value="1" {{ $guests == 1 ? 'selected' : '' }}>1 Adult, 0 Children (1 room)</option>
                    <option value="2" {{ $guests == 2 ? 'selected' : '' }}>2 Adults, 0 Children (1 room)</option>
                    <option value="4" {{ $guests == 4 ? 'selected' : '' }}>3 Adults, 1 Child (2 rooms)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 shadow-xs" style="background-color: #2067e1; height: 42px; font-size: 14px; letter-spacing: 0.5px;">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Agoda Coupon Deals Strip Banner (Image 2 Parity) --}}
<div style="background: #fff0f3; border-bottom: 1px solid #fecdd3; padding: 10px 0;">
    <div class="d-flex align-items-center justify-content-between" style="max-width: 1140px; margin: 0 auto; padding: 0 16px; font-size: 13px;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fa-solid fa-tag"></i></span>
            <strong class="text-dark">Looking for instant coupons?</strong>
            <span class="text-secondary d-none d-md-inline">Check out our Coupons &amp; Deals page for today's BDT discounts</span>
        </div>
        <a href="{{ route('deals') }}" class="btn btn-sm btn-outline-danger bg-white rounded-pill px-3 py-1 fw-bold" style="font-size: 12px;">See all coupons</a>
    </div>
</div>

{{-- 4. Main Search Results Layout --}}
<div style="max-width: 1140px; margin: 0 auto; padding: 24px 16px;">
    <div class="row g-4">
        
        {{-- Left Filter Sidebar --}}
        <div class="col-lg-3">
            @include('components.search.filter-sidebar')
        </div>

        {{-- Right Results Feed --}}
        <div class="col-lg-9" id="searchResultsContainer">
            
            {{-- Green Prime Homes Promo Banner (Agoda Standard 1:1 Parity) --}}
            <div class="mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 12px !important; padding: 16px 20px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2;">Additional discounts up to 12% on Prime Homes</h6>
                        <small class="text-secondary d-block" style="font-size: 12.5px; line-height: 1.3;">More spacious. More local. More of why you travel.</small>
                    </div>
                </div>
                <div>
                    <a href="{{ route('search.index', array_merge(request()->query(), ['search_type' => 'homes'])) }}" class="btn text-white fw-bold shadow-xs" style="background-color: #2067e1; border-radius: 8px; font-size: 13px; padding: 8px 18px;">
                        Show more Homes
                    </a>
                </div>
            </div>

            {{-- Popular Neighborhood Quick Filter Pills (Dynamic by Destination) --}}
            @php
                $destLowerStr = strtolower($destination ?: '');
                $dynamicHoods = match(true) {
                    str_contains($destLowerStr, 'cox') => ['Kolatoli Beach', 'Inani Beach', 'Laboni Point', 'Marine Drive', 'Sugandha Beach'],
                    str_contains($destLowerStr, 'sylhet') => ['Zindabazar', 'Shahjalal Dargah', 'Jaflong', 'Sreemangal', 'Amberkhana'],
                    str_contains($destLowerStr, 'kuakata') => ['Zero Point', 'Beach Road', 'Gangamati', 'Jhubaura', 'Eco Park'],
                    str_contains($destLowerStr, 'sajek') => ['Ruilui Para', 'Konglak Hill', 'Helipad', 'Eco Valley'],
                    str_contains($destLowerStr, 'chittagong') || str_contains($destLowerStr, 'chatogram') => ['GEC Circle', 'Agrabad', 'Patenga Beach', 'Foy\'s Lake'],
                    default => ['Gulshan', 'Banani', 'Uttara', 'Mirpur', 'Near Airport'],
                };
            @endphp
            <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" style="padding: 4px 0;">
                <span class="small text-muted fw-bold me-1" style="font-size: 12.5px;">Popular areas in {{ $destination ?: 'city' }}:</span>
                @foreach($dynamicHoods as $hood)
                    <a href="{{ route('search.index', array_merge(request()->query(), ['q' => $hood])) }}" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold style-hood-pill @if(request('q') == $hood) active bg-primary text-white border-primary @endif" style="font-size: 12px; border-color: #cbd5e1; color: #475569; padding: 5px 14px;">
                        {{ $hood }}
                    </a>
                @endforeach
            </div>

            {{-- Applied Active Filter Removable Tags Strip --}}
            @php
                $hasFilters = request()->hasAny(['q', 'min_price', 'max_price', 'guest_rating', 'star_rating']);
            @endphp
            @if($hasFilters)
            <div class="d-flex align-items-center gap-1.5 mb-3 pb-2 border-bottom flex-wrap">
                <span class="small text-muted fw-bold me-1" style="font-size: 11.5px;">Active filters:</span>
                
                @if(request('q'))
                    <a href="{{ route('search.index', request()->except('q')) }}" class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        "{{ request('q') }}" <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                @endif

                @if(request('min_price'))
                    <a href="{{ route('search.index', request()->except('min_price')) }}" class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Min: ৳{{ number_format((float)request('min_price')) }} <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                @endif

                @if(request('max_price'))
                    <a href="{{ route('search.index', request()->except('max_price')) }}" class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                        Max: ৳{{ number_format((float)request('max_price')) }} <i class="fa-solid fa-xmark text-danger ms-1"></i>
                    </a>
                @endif

                @if(request('guest_rating'))
                    @foreach((array)request('guest_rating') as $gr)
                        <a href="{{ route('search.index', array_merge(request()->except('guest_rating'), ['guest_rating' => array_diff((array)request('guest_rating'), [$gr])])) }}" class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                            Rating {{ $gr }}+ <i class="fa-solid fa-xmark text-danger ms-1"></i>
                        </a>
                    @endforeach
                @endif

                @if(request('star_rating'))
                    @foreach((array)request('star_rating') as $sr)
                        <a href="{{ route('search.index', array_merge(request()->except('star_rating'), ['star_rating' => array_diff((array)request('star_rating'), [$sr])])) }}" class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill text-decoration-none fw-semibold" style="font-size: 11px;">
                            {{ $sr }} Stars <i class="fa-solid fa-xmark text-danger ms-1"></i>
                        </a>
                    @endforeach
                @endif

                <a href="{{ route('search.index', ['destination' => request('destination'), 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" class="text-decoration-none fw-bold ms-2 text-danger" style="font-size: 11.5px;">
                    Clear all
                </a>
            </div>
            @endif

            {{-- Results Header & Sort Dropdown --}}
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px;">
                        {{ $searchResults['total_count'] }} properties in {{ $destination ?: 'Bangladesh' }}
                    </h4>
                </div>
                <form action="{{ route('search.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                    @foreach(request()->except(['sort_by', 'page']) as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $arrVal)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $arrVal }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label class="small text-muted fw-semibold mb-0 d-none d-sm-inline">Sort by:</label>
                    <select name="sort_by" class="form-select form-select-sm rounded-3 fw-semibold" style="width: 190px; font-size: 13px; border-color: #cbd5e1;" onchange="this.form.submit();">
                        <option value="featured" @selected(request('sort_by') == 'featured')>Best match</option>
                        <option value="price_low" @selected(request('sort_by') == 'price_low')>Lowest price first</option>
                        <option value="price_high" @selected(request('sort_by') == 'price_high')>Highest price first</option>
                        <option value="rating" @selected(request('sort_by') == 'rating')>Highest guest rating</option>
                        <option value="newest" @selected(request('sort_by') == 'newest')>Newest listings</option>
                    </select>
                </form>
            </div>

            {{-- Property Cards Grid --}}
            <div class="d-flex flex-column gap-3">
                @forelse($searchResults['merged_results'] as $item)
                    @include('components.search.property-card', ['item' => $item])
                @empty
                    <div class="card border-0 shadow-xs rounded-4 p-5 text-center bg-white my-3">
                        <div class="mb-3 text-muted"><i class="fa-solid fa-hotel display-3 text-opacity-25"></i></div>
                        <h5 class="fw-bold text-dark mb-1">No properties found matching your criteria</h5>
                        <p class="text-secondary small mb-3">Try adjusting your filters or price range to discover more available stays.</p>
                        <div>
                            <a href="{{ route('search.index') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #2067e1; border-radius: 8px; font-size: 13px;">Clear All Filters</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(isset($searchResults['paginator']) && $searchResults['paginator']->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $searchResults['paginator']->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

{{-- Interactive Leaflet OpenStreetMap Modal (Agoda 1:1 Map Parity) --}}
<div class="modal fade" id="interactiveMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 92vw;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-info fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 16px;">Interactive Property Map — {{ $destination ?: 'Bangladesh' }}</h5>
                        <small class="text-white-50" style="font-size: 11px;">Showing {{ count($searchResults['merged_results']) }} verified stays with live rates</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 78vh; min-height: 520px;">
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                <div id="agodaMapContainer" style="width: 100%; height: 100%; z-index: 1;"></div>

                @php
                    $destLower = strtolower($destination ?: '');
                    $centerLat = 21.4272;
                    $centerLng = 91.9702;
                    if (str_contains($destLower, 'dhaka')) { $centerLat = 23.8103; $centerLng = 90.4125; }
                    elseif (str_contains($destLower, 'sylhet')) { $centerLat = 24.8949; $centerLng = 91.8687; }
                    elseif (str_contains($destLower, 'kuakata')) { $centerLat = 21.8166; $centerLng = 90.1198; }
                    elseif (str_contains($destLower, 'chittagong')) { $centerLat = 22.3569; $centerLng = 91.7832; }

                    $mapProperties = collect($searchResults['merged_results'])->map(function($p, $idx) use ($centerLat, $centerLng) {
                        $isObj    = is_object($p);
                        $name     = $isObj ? ($p->name ?? 'Property') : ($p['name'] ?? 'Property');
                        $slug     = $isObj ? ($p->slug ?? $p->id ?? 1) : ($p['slug'] ?? $p['id'] ?? 1);
                        $priceVal = $isObj ? ($p->price_per_night ?? $p->price ?? 12500) : ($p['price_per_night'] ?? $p['price'] ?? 12500);
                        $city     = $isObj ? ($p->city ?? $p->address ?? 'Destination') : ($p['city'] ?? $p['address'] ?? 'Destination');
                        $image    = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
                        if (!$image) $image = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80';
                        $score    = $isObj ? ($p->rating_score ?? 9.2) : ($p['rating_score'] ?? 9.2);

                        // Offset coordinates slightly per property pin if lat/lng are generic
                        $latOffset = ($idx - 2) * 0.008;
                        $lngOffset = ($idx % 2 == 0 ? 1 : -1) * 0.006;
                        $lat = (float) ($isObj ? ($p->latitude ?? ($centerLat + $latOffset)) : ($p['latitude'] ?? ($centerLat + $latOffset)));
                        $lng = (float) ($isObj ? ($p->longitude ?? ($centerLng + $lngOffset)) : ($p['longitude'] ?? ($centerLng + $lngOffset)));

                        return [
                            'name'     => $name,
                            'price'    => \App\Services\CurrencyService::format($priceVal),
                            'city'     => $city,
                            'image'    => $image,
                            'score'    => $score,
                            'url'      => route('property.show', $slug),
                            'lat'      => $lat,
                            'lng'      => $lng,
                        ];
                    });
                @endphp

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var mapModal = document.getElementById('interactiveMapModal');
                        var mapInitialized = false;
                        var map;

                        mapModal.addEventListener('shown.bs.modal', function () {
                            if (!mapInitialized) {
                                map = L.map('agodaMapContainer').setView([{{ $centerLat }}, {{ $centerLng }}], 13);

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; OpenStreetMap contributors | PRIME BOOKING Map'
                                }).addTo(map);

                                var properties = @json($mapProperties);

                                properties.forEach(function(item) {
                                    var customIcon = L.divIcon({
                                        className: 'custom-agoda-pin',
                                        html: '<div style="background:#2067e1;color:#ffffff;font-weight:800;font-size:11.5px;padding:4px 10px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.3);border:2px solid #ffffff;cursor:pointer;white-space:nowrap;">' + item.price + '</div>',
                                        iconSize: [90, 30],
                                        iconAnchor: [45, 15]
                                    });

                                    var popupContent = '<div style="width:200px;font-family:sans-serif;">' +
                                        '<img src="' + item.image + '" style="width:100%;height:100px;object-fit:cover;border-radius:8px;margin-bottom:6px;">' +
                                        '<div style="font-weight:700;font-size:13px;color:#0f172a;line-height:1.2;margin-bottom:4px;">' + item.name + '</div>' +
                                        '<div style="font-size:11px;color:#64748b;margin-bottom:4px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>' + item.city + '</div>' +
                                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">' +
                                            '<span style="background:#2067e1;color:#fff;font-weight:700;font-size:11px;padding:2px 6px;border-radius:4px;">' + item.score + '</span>' +
                                            '<a href="' + item.url + '" style="background:#16a34a;color:#fff;font-weight:700;font-size:11px;padding:4px 10px;border-radius:6px;text-decoration:none;">Book Stay →</a>' +
                                        '</div>' +
                                    '</div>';

                                    L.marker([item.lat, item.lng], {icon: customIcon})
                                        .addTo(map)
                                        .bindPopup(popupContent);
                                });

                                mapInitialized = true;
                            } else {
                                map.invalidateSize();
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
{{-- Bottom Sticky Floating "Map View" Button --}}
<div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1050;">
    <button type="button" class="btn text-white fw-bold shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-2" style="background-color: #2067e1; font-size: 13.5px; border: 2.5px solid #ffffff; letter-spacing: 0.3px; box-shadow: 0 8px 24px rgba(32, 103, 225, 0.4) !important;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
        <i class="fa-solid fa-map-location-dot fs-5"></i> Map view
    </button>
</div>
@endsection

