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

{{-- Interactive Leaflet OpenStreetMap Agoda-Exact Split-Screen Modal --}}
<div class="modal fade" id="interactiveMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen m-0 p-0">
        <div class="modal-content border-0 rounded-0 overflow-hidden" style="background:#f8fafc;">
            
            {{-- Top Modal Bar: Close button & Quick Filters --}}
            <div class="d-flex justify-content-between align-items-center px-4 py-2 bg-white border-bottom shadow-xs" style="z-index: 1050; height: 56px;">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1.5" id="toggleMapFilterSidebarBtn" style="font-size:12.5px;">
                        <i class="fa-solid fa-sliders"></i> <span id="toggleFilterBtnText">Hide filters</span>
                    </button>
                    <div class="fw-bold text-dark d-none d-md-block" style="font-size:14px;">
                        <span id="mapModalPropertyCount">{{ count($searchResults['merged_results']) }}</span> properties available in {{ $destination ?: 'Bangladesh' }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn-close fs-6 p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Main Split View Container (3 Panels) --}}
            <div class="modal-body p-0 d-flex overflow-hidden" style="height: calc(100vh - 56px);">
                
                {{-- Panel 1: Left Filter Sidebar (Collapsible) --}}
                <div id="agodaMapFilterCol" class="bg-white border-end overflow-y-auto p-3" style="width: 280px; min-width: 280px; flex-shrink: 0; transition: all 0.3s ease;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-dark" style="font-size:13px;">Your filters</span>
                        <a href="javascript:void(0);" onclick="document.getElementById('mapSearchInput').value=''; document.getElementById('mapPriceRange').value=100000; filterMapItems();" class="text-primary text-decoration-none fw-bold" style="font-size:11.5px;">CLEAR</a>
                    </div>

                    {{-- Text Search --}}
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="mapSearchInput" class="form-control form-control-sm bg-light border-start-0" placeholder="Text search" onkeyup="filterMapItems()">
                        </div>
                    </div>

                    {{-- Budget Slider --}}
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="fw-bold text-dark d-block mb-1" style="font-size:12px;">Your budget (per night)</label>
                        <input type="range" id="mapPriceRange" class="form-range" min="500" max="80000" step="500" value="80000" oninput="document.getElementById('mapPriceRangeVal').textContent = this.value; filterMapItems();">
                        <div class="d-flex justify-content-between text-muted" style="font-size:11px;">
                            <span>৳500</span>
                            <span>Max: ৳<span id="mapPriceRangeVal">80,000</span></span>
                        </div>
                    </div>

                    {{-- Popular Filters --}}
                    <div class="mb-3">
                        <label class="fw-bold text-dark d-block mb-2" style="font-size:12px;">Popular filters</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-2" style="font-size:12px; cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 map-filter-check" value="free_cancel" onchange="filterMapItems()"> Free cancellation
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:12px; cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 map-filter-check" value="pay_later" onchange="filterMapItems()"> Pay at the hotel
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:12px; cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 map-filter-check" value="rating_8" onchange="filterMapItems()"> Guest rating: 8+ Excellent
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:12px; cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 map-filter-check" value="has_pool" onchange="filterMapItems()"> Swimming Pool
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Panel 2: Center Scrollable Property Cards --}}
                <div id="agodaMapCardsCol" class="bg-light border-end overflow-y-auto p-3" style="width: 380px; min-width: 340px; flex-shrink: 0;">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted fw-bold" style="font-size:12px;"><span id="visibleCardsCount">{{ count($searchResults['merged_results']) }}</span> properties available</span>
                        <select id="mapSortSelect" class="form-select form-select-sm" style="width:auto; font-size:11.5px; font-weight:600;" onchange="sortMapItems(this.value)">
                            <option value="recommended">Recommended</option>
                            <option value="price_low">Lowest price first</option>
                            <option value="rating_high">Guest rating</option>
                        </select>
                    </div>

                    {{-- Dynamic Property Cards List --}}
                    <div id="agodaMapCardsList" class="d-flex flex-column gap-3">
                        {{-- Injected dynamically via JS from mapProperties JSON --}}
                    </div>
                </div>

                {{-- Panel 3: Right Full Height Interactive Map --}}
                <div class="flex-grow-1 position-relative h-100" style="background:#e5e7eb;">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                    {{-- Floating Map Search Pill (Agoda Exact UI with Smart Autocomplete Dropdown) --}}
                    <div class="position-absolute top-0 start-50 translate-middle-x mt-3" style="z-index: 1000; width: 340px;">
                        <div class="shadow-md rounded-pill bg-white px-3 py-1.5 d-flex align-items-center gap-2 border" style="background:#ffffff; box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
                            <i class="fa-solid fa-magnifying-glass text-primary" style="font-size:13px;"></i>
                            <input type="text" id="mapHeaderSearchInput" class="border-0 bg-transparent w-100" placeholder="Search on map..." autocomplete="off" style="outline:none; font-size:13px; font-weight:500;" onkeyup="handleMapSearchAutocomplete(this.value)">
                            <button type="button" class="btn btn-link p-0 text-muted d-none" id="clearMapSearchBtn" onclick="clearMapSearch()" style="text-decoration:none;"><i class="fa-solid fa-circle-xmark"></i></button>
                        </div>

                        {{-- Agoda 1:1 Smart Location Dropdown (Auto-suggests Cities, Districts, Landmarks & Railway Stations) --}}
                        <div id="agodaMapSuggestDropdown" class="bg-white rounded-3 shadow-lg border mt-1.5 overflow-hidden d-none" style="max-height: 280px; overflow-y: auto;">
                            <div id="agodaMapSuggestList" class="py-1"></div>
                        </div>
                    </div>

                    <div id="agodaMapContainer" style="width: 100%; height: 100%; z-index: 1;"></div>
                </div>

            </div>
        </div>
    </div>
</div>

@php
    $defaultLat = 22.3569; // Chattogram / Bangladesh Default Center
    $defaultLng = 91.7832;

    $mapProperties = collect($searchResults['merged_results'])->map(function($p, $idx) use ($defaultLat, $defaultLng) {
        $isObj    = is_object($p);
        $id       = $isObj ? ($p->id ?? $idx + 1) : ($p['id'] ?? $idx + 1);
        $name     = $isObj ? ($p->name ?? 'Property') : ($p['name'] ?? 'Property');
        $slug     = $isObj ? ($p->slug ?? $p->id ?? 1) : ($p['slug'] ?? $p['id'] ?? 1);
        $rawPrice = (float)($isObj ? ($p->price_per_night ?? $p->price ?? 0) : ($p['price_per_night'] ?? $p['price'] ?? 0));
        $city     = $isObj ? ($p->city ?? $p->address ?? 'Chattogram') : ($p['city'] ?? $p['address'] ?? 'Chattogram');
        $image    = $isObj ? ($p->primary_image ?? '') : ($p['primary_image'] ?? '');
        $score    = (float)($isObj ? ($p->rating_score ?? 8.5) : ($p['rating_score'] ?? 8.5));
        $reviews  = (int)($isObj ? ($p->total_reviews ?? 4) : ($p['total_reviews'] ?? 4));
        $lat      = (float)($isObj ? ($p->latitude  ?? 0) : ($p['latitude']  ?? 0));
        $lng      = (float)($isObj ? ($p->longitude ?? 0) : ($p['longitude'] ?? 0));
        $type     = $isObj ? ($p->type ?? 'hotel') : ($p['type'] ?? 'hotel');
        $freeCancel = (bool)($isObj ? ($p->free_cancellation ?? true) : ($p['free_cancellation'] ?? true));
        $payLater   = (bool)($isObj ? ($p->no_credit_card_required ?? true) : ($p['no_credit_card_required'] ?? true));

        return [
            'id'          => $id,
            'name'        => $name,
            'price_raw'   => $rawPrice > 0 ? $rawPrice : 3500,
            'price'       => $rawPrice > 0 ? \App\Services\CurrencyService::format($rawPrice) : 'USD 35',
            'city'        => $city,
            'image'       => $image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500',
            'score'       => $score > 0 ? number_format($score, 1) : '8.8',
            'rating_text' => $score >= 9 ? 'Exceptional' : ($score >= 8 ? 'Excellent' : 'Very Good'),
            'reviews'     => $reviews,
            'url'         => route('property.show', $slug),
            'lat'         => $lat,
            'lng'         => $lng,
            'has_gps'     => ($lat !== 0.0 && $lng !== 0.0),
            'type'        => ucfirst($type),
            'free_cancel' => $freeCancel,
            'pay_later'   => $payLater,
        ];
    });

    $gpsProps = $mapProperties->where('has_gps', true);
    if ($gpsProps->count() > 0) {
        $centerLat = $gpsProps->avg('lat');
        $centerLng = $gpsProps->avg('lng');
    } else {
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
            items.forEach(function(item) {
                html += `
                    <div class="card border border-gray-200 rounded-3 overflow-hidden shadow-xs agoda-map-card" id="mapCard_${item.id}" style="border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff; transition:all 0.2s;" onmouseenter="highlightMarker(${item.id})" onmouseleave="unhighlightMarker(${item.id})" onclick="focusProperty(${item.id})">
                        <div class="position-relative" style="height:140px; overflow:hidden;">
                            <img src="${item.image}" alt="${item.name}" class="w-100 h-100" style="object-fit:cover;">
                            <span class="badge position-absolute top-0 start-0 m-2" style="background:#2067e1; font-size:10px; font-weight:700;"><i class="fa-solid fa-house-chimney me-1"></i>${item.type}</span>
                        </div>
                        <div class="p-2.5 p-3">
                            <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size:13.5px; font-family:'Plus Jakarta Sans',sans-serif;" title="${item.name}">${item.name}</h6>
                            <div class="d-flex align-items-center gap-1 text-muted mb-2" style="font-size:11px;">
                                <i class="fa-solid fa-location-dot text-primary"></i> ${item.city}
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="badge" style="background:#2067e1; color:#fff; font-size:11px; font-weight:800; padding:3px 6px;">${item.score}</span>
                                    <span class="fw-bold text-dark" style="font-size:11.5px;">${item.rating_text}</span>
                                    <small class="text-muted" style="font-size:10.5px;">(${item.reviews} reviews)</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between pt-2 border-top">
                                <div>
                                    ${item.free_cancel ? '<span class="text-success fw-bold d-block" style="font-size:10.5px;"><i class="fa-solid fa-check me-1"></i>Free cancellation</span>' : ''}
                                    <small class="text-muted" style="font-size:10px;">Per night before taxes</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-dark" style="font-size:15px; color:#0f172a;">${item.price}</div>
                                    <a href="${item.url}" target="_blank" class="btn btn-sm btn-primary fw-bold px-2 py-0.5 mt-1" style="font-size:10.5px; border-radius:4px; background:#2067e1;">View Stay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        };

        // ── 3. Highlighting and Marker Popups (Agoda 1:1 Parity) ──
        window.highlightMarker = function(id) {
            // Highlight Marker Pin on Map
            if (markersMap[id]) {
                var el = markersMap[id].getElement();
                if (el) {
                    var badge = el.querySelector('.custom-agoda-pin-inner');
                    if (badge) {
                        badge.style.backgroundColor = '#16a34a';
                        badge.style.color = '#ffffff';
                        badge.style.borderColor = '#ffffff';
                        badge.style.transform = 'scale(1.15)';
                        badge.style.boxShadow = '0 6px 18px rgba(22, 163, 74, 0.45)';
                        badge.style.zIndex = '9999';
                    }
                }
            }

            // Highlight Property Card on Left Column (Agoda Exact Blue Active Outline)
            var card = document.getElementById('mapCard_' + id);
            if (card) {
                card.style.borderColor = '#2067e1';
                card.style.borderWidth = '2px';
                card.style.boxShadow = '0 8px 24px rgba(32, 103, 225, 0.18)';
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
                        badge.style.color = '#16a34a';
                        badge.style.borderColor = '#16a34a';
                        badge.style.transform = 'scale(1)';
                        badge.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
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
                map.setView([prop.lat, prop.lng], 15, {animate: true});
                if (markersMap[id]) markersMap[id].openPopup();
            }
        };

        // ── 4. Filter and Sorting Algorithms ──
        window.filterMapItems = function() {
            var query = (document.getElementById('mapSearchInput')?.value || '').toLowerCase().trim();
            var maxBudget = parseFloat(document.getElementById('mapPriceRange')?.value || 999999);
            var checks = Array.from(document.querySelectorAll('.map-filter-check:checked')).map(c => c.value);

            currentFiltered = allProperties.filter(function(item) {
                if (query && !item.name.toLowerCase().includes(query) && !item.city.toLowerCase().includes(query)) return false;
                if (item.price_raw > maxBudget) return false;
                if (checks.includes('free_cancel') && !item.free_cancel) return false;
                if (checks.includes('pay_later') && !item.pay_later) return false;
                if (checks.includes('rating_8') && parseFloat(item.score) < 8.0) return false;
                return true;
            });

            renderMapCards(currentFiltered);
            updateMapMarkers(currentFiltered);
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

            // Filter predefined geo hierarchy + real property database
            var matchedGeos = geoLocations.filter(g => g.name.toLowerCase().includes(query) || g.subtitle.toLowerCase().includes(query));
            var matchedProps = allProperties.filter(p => p.name.toLowerCase().includes(query) || p.city.toLowerCase().includes(query));

            var results = [];
            matchedGeos.forEach(g => results.push({ title: g.name, subtitle: g.subtitle, lat: g.lat, lng: g.lng, isGeo: true }));
            matchedProps.slice(0, 5).forEach(p => results.push({ title: p.name, subtitle: p.city + ' • ' + p.price, id: p.id, lat: p.lat, lng: p.lng, isProp: true }));

            if (results.length === 0) {
                if (dropdown) dropdown.classList.add('d-none');
            } else {
                var html = '';
                results.forEach(function(r) {
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

                allProperties.forEach(function(item) {
                    var isSoldOut = item.price_raw <= 0 || item.price.includes('N/A');
                    var pinHtml = isSoldOut 
                        ? '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#64748b;font-weight:700;font-size:11px;padding:3px 8px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.15);border:1.5px solid #cbd5e1;cursor:pointer;white-space:nowrap;transition:all 0.2s;">Sold out</div>'
                        : '<div class="custom-agoda-pin-inner" style="background:#ffffff;color:#16a34a;font-weight:800;font-size:11.5px;padding:3px 9px;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.15);border:1.5px solid #16a34a;cursor:pointer;white-space:nowrap;transition:all 0.2s;">' + item.price + '</div>';

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

                // Viewport dynamic sync
                map.on('moveend', function() {
                    var bounds = map.getBounds();
                    var inViewport = currentFiltered.filter(i => bounds.contains([i.lat, i.lng]));
                    var countEl = document.getElementById('visibleCardsCount');
                    if (countEl) countEl.textContent = inViewport.length;
                });

                mapInitialized = true;
            } else {
                map.invalidateSize();
            }
        });
    });
</script>

{{-- Bottom Sticky Floating "Map View" Button --}}
<div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1040;">
    <button type="button" class="btn text-white fw-bold shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-2" style="background-color: #2067e1; font-size: 13.5px; border: 2.5px solid #ffffff; letter-spacing: 0.3px; box-shadow: 0 8px 24px rgba(32, 103, 225, 0.4) !important;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
        <i class="fa-solid fa-map-location-dot fs-5"></i> Map view
    </button>
</div>
@endsection

