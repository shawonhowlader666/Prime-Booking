@extends('layouts.main', ['activePage' => 'services'])

@section('title', 'Search Hotels & Stays in ' . ($destination ?: 'Bangladesh') . ' | PRIME BOOKING')

@section('content')
@include('components.search.loading-skeleton-modal')

{{-- 2. Agoda Compact Subheader Search Bar (Screenshot 1:1 Exact Parity) --}}
@php
    $checkinCarbon = \Carbon\Carbon::parse($checkIn ?: now());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(7));
    $guestCount = intval($guests ?: 2);
    $roomsCount = intval(request('rooms', 1));
@endphp
<div style="background-color: #1d2b45; padding: 12px 0; border-bottom: 1px solid #334155;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 16px;">
        <form action="{{ route('search.index') }}" method="GET" class="row g-2 align-items-center" id="searchHeaderForm" onsubmit="showAgodaSearchLoading();">
            <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">

            {{-- 1. Destination Input Box with Near Me GPS Button --}}
            <div class="col-12 col-lg-3">
                <div class="bg-white rounded-3 d-flex align-items-center px-3 shadow-xs position-relative" style="height: 48px;">
                    <i class="fa-solid fa-magnifying-glass text-secondary me-2 fs-6"></i>
                    <input type="text" name="destination" id="mainDestInput" class="form-control border-0 p-0 fw-bold text-dark" value="{{ $destination }}" placeholder="Enter destination or property" style="font-size: 14px; box-shadow: none;">
                    <input type="hidden" name="lat" id="gpsLatInput" value="{{ request('lat') }}">
                    <input type="hidden" name="lng" id="gpsLngInput" value="{{ request('lng') }}">
                    <button type="button" class="btn btn-link p-0 text-primary ms-1" title="Search Near My Current GPS Location" onclick="useCurrentLocation()" style="font-size: 15px; text-decoration: none;">
                        <i class="fa-solid fa-location-crosshairs" id="gpsCrosshairIcon"></i>
                    </button>
                </div>
            </div>

            {{-- 2. Check-in Date Box (Agoda 2-line Card) --}}
            <div class="col-6 col-md-3 col-lg-2">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center gap-2 shadow-xs position-relative" style="height: 48px; cursor: pointer;" onclick="document.getElementById('checkInNativeInput').showPicker();">
                    <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                    <div style="line-height: 1.15;">
                        <strong class="d-block text-dark" id="checkInDisplayDate" style="font-size: 13px;">{{ $checkinCarbon->format('j M Y') }}</strong>
                        <small class="text-secondary" id="checkInDisplayDay" style="font-size: 11px;">{{ $checkinCarbon->format('l') }}</small>
                    </div>
                    <input type="date" name="check_in" id="checkInNativeInput" value="{{ $checkinCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkIn', this.value);">
                </div>
            </div>

            {{-- 3. Check-out Date Box (Agoda 2-line Card) --}}
            <div class="col-6 col-md-3 col-lg-2">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center gap-2 shadow-xs position-relative" style="height: 48px; cursor: pointer;" onclick="document.getElementById('checkOutNativeInput').showPicker();">
                    <i class="fa-regular fa-calendar text-secondary fs-5"></i>
                    <div style="line-height: 1.15;">
                        <strong class="d-block text-dark" id="checkOutDisplayDate" style="font-size: 13px;">{{ $checkoutCarbon->format('j M Y') }}</strong>
                        <small class="text-secondary" id="checkOutDisplayDay" style="font-size: 11px;">{{ $checkoutCarbon->format('l') }}</small>
                    </div>
                    <input type="date" name="check_out" id="checkOutNativeInput" value="{{ $checkoutCarbon->format('Y-m-d') }}" class="position-absolute opacity-0" style="bottom: 0; left: 0; width: 1px; height: 1px;" onchange="updateSearchDateDisplay('checkOut', this.value);">
                </div>
            </div>

            {{-- 4. Guests & Rooms Box (Agoda 2-line Card with Dropdown) --}}
            <div class="col-12 col-md-4 col-lg-3 position-relative">
                <div class="bg-white rounded-3 px-3 py-1 d-flex align-items-center justify-content-between shadow-xs dropdown-toggle" style="height: 48px; cursor: pointer;" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users text-secondary fs-5"></i>
                        <div style="line-height: 1.15;">
                            <strong class="d-block text-dark" id="guestCountDisplay" style="font-size: 13px;">{{ $guestCount }} adult{{ $guestCount > 1 ? 's' : '' }}</strong>
                            <small class="text-secondary" id="roomCountDisplay" style="font-size: 11px;">{{ $roomsCount }} room{{ $roomsCount > 1 ? 's' : '' }}</small>
                        </div>
                    </div>
                </div>

                {{-- Guests Counter Popover Menu --}}
                <div class="dropdown-menu p-3 shadow-lg border-0 rounded-3 mt-2" style="width: 280px; z-index: 1050;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong class="d-block text-dark" style="font-size: 13px;">Adults</strong>
                            <small class="text-muted" style="font-size: 11px;">Ages 18 or above</small>
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
                            <strong class="d-block text-dark" style="font-size: 13px;">Rooms</strong>
                            <small class="text-muted" style="font-size: 11px;">Total units needed</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', -1);">-</button>
                            <input type="hidden" name="rooms" id="roomsHiddenInput" value="{{ $roomsCount }}">
                            <span class="fw-bold px-1" id="roomsValText">{{ $roomsCount }}</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="adjustGuestCounter('rooms', 1);">+</button>
                        </div>
                    </div>
                    <button type="button" class="btn text-white w-100 btn-sm fw-bold rounded-2" style="background: #2067e1;" onclick="bootstrap.Dropdown.getInstance(this.closest('.position-relative').querySelector('.dropdown-toggle')).hide();">
                        Done
                    </button>
                </div>
            </div>

            {{-- 5. Search Blue Button --}}
            <div class="col-12 col-md-2 col-lg-2">
                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 shadow-sm" style="background-color: #2067e1; height: 48px; font-size: 14px; letter-spacing: 0.5px;">
                    SEARCH
                </button>
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
        const origPlaceholder = destInput.placeholder;
        destInput.placeholder = "Detecting your GPS location...";
        if (icon) icon.className = "fa-solid fa-spinner fa-spin";

        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('gpsLatInput').value = pos.coords.latitude;
            document.getElementById('gpsLngInput').value = pos.coords.longitude;
            destInput.value = "Near My Location";
            if (icon) icon.className = "fa-solid fa-location-crosshairs";
            document.getElementById('searchHeaderForm').submit();
        }, function(err) {
            destInput.placeholder = origPlaceholder;
            if (icon) icon.className = "fa-solid fa-location-crosshairs";
            alert("Could not access your location. Please ensure location permissions are enabled.");
        }, { timeout: 10000 });
    }
</script>

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

            {{-- 3.5 Smart Destination Weather & Seasonal Guidance Card --}}
            @if(!empty($destination))
            @php
                $cityInsight = \App\Services\Search\CityInsightService::getInsights($destination);
            @endphp
            <div class="mb-3 p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-xs" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 42px; height: 42px; background: #ffffff; border: 1px solid #86efac;">
                        <i class="{{ $cityInsight['icon'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <strong class="text-dark fw-bold" style="font-size: 14px;">{{ $destination }}</strong>
                            <span class="badge fw-semibold px-2.5 py-1" style="font-size: 11px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 20px;">
                                <i class="fa-solid fa-sparkles me-1 text-success" style="font-size: 10px;"></i>{{ $cityInsight['season_badge'] }}
                            </span>
                            <span class="text-secondary fw-semibold" style="font-size: 12.5px;">
                                <i class="fa-solid fa-cloud-sun text-warning me-1"></i>{{ $cityInsight['temp'] }} · {{ $cityInsight['condition'] }}
                            </span>
                        </div>
                        <div class="text-secondary" style="font-size: 12px; line-height: 1.35; color: #475569;">
                            <i class="fa-solid fa-circle-info text-success me-1 opacity-75"></i>{{ $cityInsight['tip'] }}
                        </div>
                    </div>
                </div>
                <div>
                    <span class="badge bg-white text-dark border px-3 py-1.5 shadow-xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px; border-color: #cbd5e1 !important; border-radius: 20px;">
                        <i class="fa-solid fa-bolt text-warning"></i> Best Rates Guaranteed
                    </span>
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
            @if(!empty($popularAreas))
            <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" style="padding: 4px 0;">
                <span class="small text-muted fw-bold me-1" style="font-size: 12.5px;">Popular areas in {{ $destination ?: 'city' }}:</span>
                @foreach($popularAreas as $hood)
                    <a href="{{ route('search.index', array_merge(request()->query(), ['destination' => $destination, 'q' => $hood])) }}"
                       class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold @if(request('q') == $hood) active bg-primary text-white border-primary @endif"
                       style="font-size: 12px; border-color: #cbd5e1; color: #475569; padding: 5px 14px;">
                        {{ $hood }}
                    </a>
                @endforeach
            </div>
            @endif

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
            <div class="modal-header bg-dark text-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-info fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 16px;">Interactive Property Map — {{ $destination ?: 'Bangladesh' }}</h5>
                        <small class="text-white-50" id="mapSubTitleText" style="font-size: 11px;">Showing {{ count($searchResults['merged_results']) }} verified stays with live rates</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="searchAsMoveMapToggle" checked style="cursor: pointer;">
                        <label class="form-check-label text-white small fw-bold" for="searchAsMoveMapToggle" style="font-size: 11.5px; cursor: pointer;">
                            <i class="fa-solid fa-arrows-up-down-left-right me-1 text-info"></i> Search as I move map
                        </label>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 78vh; min-height: 520px;">
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                <div id="agodaMapContainer" style="width: 100%; height: 100%; z-index: 1;"></div>

                @php
                    // ─── MAP CENTER: 100% from real property GPS in results ──────────
                    $defaultLat = 23.6850; // Bangladesh centre
                    $defaultLng = 90.3563;

                    $mapProperties = collect($searchResults['merged_results'])->map(function($p, $idx) use ($defaultLat, $defaultLng) {
                        $isObj    = is_object($p);
                        $name     = $isObj ? ($p->name ?? 'Property') : ($p['name'] ?? 'Property');
                        $slug     = $isObj ? ($p->slug ?? $p->id ?? 1) : ($p['slug'] ?? $p['id'] ?? 1);
                        $priceVal = $isObj ? ($p->price_per_night ?? $p->price ?? 0) : ($p['price_per_night'] ?? $p['price'] ?? 0);
                        $city     = $isObj ? ($p->city ?? $p->address ?? '') : ($p['city'] ?? $p['address'] ?? '');
                        $image    = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
                        $score    = $isObj ? ($p->rating_score ?? 0) : ($p['rating_score'] ?? 0);
                        $lat      = (float)($isObj ? ($p->latitude  ?? 0) : ($p['latitude']  ?? 0));
                        $lng      = (float)($isObj ? ($p->longitude ?? 0) : ($p['longitude'] ?? 0));

                        return [
                            'name'     => $name,
                            'price'    => $priceVal > 0 ? \App\Services\CurrencyService::format($priceVal) : 'N/A',
                            'city'     => $city,
                            'image'    => $image ?: '',
                            'score'    => $score,
                            'url'      => route('property.show', $slug),
                            'lat'      => $lat,
                            'lng'      => $lng,
                            'has_gps'  => ($lat !== 0.0 && $lng !== 0.0),
                        ];
                    });

                    // Compute map center from real GPS of properties that have coordinates
                    $gpsProps = $mapProperties->where('has_gps', true);
                    if ($gpsProps->count() > 0) {
                        $centerLat = $gpsProps->avg('lat');
                        $centerLng = $gpsProps->avg('lng');
                    } else {
                        $centerLat = $defaultLat;
                        $centerLng = $defaultLng;
                    }

                    // For properties missing GPS, scatter them around the center
                    $mapProperties = $mapProperties->map(function($p, $idx) use ($centerLat, $centerLng) {
                        if (!$p['has_gps']) {
                            $latOffset = ($idx - 2) * 0.007;
                            $lngOffset = ($idx % 2 === 0 ? 1 : -1) * 0.005;
                            $p['lat'] = round($centerLat + $latOffset, 6);
                            $p['lng'] = round($centerLng + $lngOffset, 6);
                        }
                        return $p;
                    });
                @endphp

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var mapModal = document.getElementById('interactiveMapModal');
                        var mapInitialized = false;
                        var map;
                        var markers = [];

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

                                    var m = L.marker([item.lat, item.lng], {icon: customIcon})
                                        .addTo(map)
                                        .bindPopup(popupContent);

                                    markers.push({marker: m, lat: item.lat, lng: item.lng});
                                });

                                // ─── Spatial Bounding Box Filter Event ("Search as I move the map") ───
                                map.on('moveend', function() {
                                    var toggle = document.getElementById('searchAsMoveMapToggle');
                                    if (!toggle || !toggle.checked) return;

                                    var bounds = map.getBounds();
                                    var visibleCount = 0;

                                    markers.forEach(function(item) {
                                        if (bounds.contains([item.lat, item.lng])) {
                                            item.marker.setOpacity(1.0);
                                            visibleCount++;
                                        } else {
                                            item.marker.setOpacity(0.25);
                                        }
                                    });

                                    var subText = document.getElementById('mapSubTitleText');
                                    if (subText) {
                                        subText.textContent = `Showing ${visibleCount} stays in visible map viewport`;
                                    }
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

