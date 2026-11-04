{{-- Agoda-Exact Filter Sidebar Component (100% 1:1 Parity with Agoda Screenshot) --}}
<style>
    /* Agoda Custom Checkbox Styling */
    .agoda-filter-group {
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }
    .agoda-filter-title {
        font-size: 14px;
        font-weight: 700;
        color: #2a2a2e;
        margin-bottom: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .agoda-check-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        cursor: pointer;
        user-select: none;
    }
    .agoda-check-row input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        min-width: 18px;
        border: 1.5px solid #737373;
        border-radius: 3px;
        background: #ffffff;
        margin: 0 10px 0 0;
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .agoda-check-row input[type="checkbox"]:checked {
        background-color: #2067e1;
        border-color: #2067e1;
    }
    .agoda-check-row input[type="checkbox"]:checked::after {
        content: "";
        width: 5px;
        height: 9px;
        border: solid #ffffff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        position: absolute;
        top: 2px;
    }
    .agoda-check-label {
        font-size: 13.5px;
        color: #2a2a2e;
        line-height: 1.3;
        cursor: pointer;
        margin: 0;
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    .agoda-check-count {
        color: #737373;
        font-size: 12.5px;
        margin-left: 4px;
        font-weight: 400;
    }
</style>

<div class="card border-0 rounded-3 overflow-hidden mb-4 bg-white" style="border: 1px solid #e5e7eb !important; border-radius: 6px !important;">
    
    {{-- 1. Official Agoda Map Entry Card (Exact Agoda Screenshot) --}}
    <div class="position-relative text-center p-0 overflow-hidden agoda-map-trigger-card" style="height: 125px; cursor: pointer; border-radius: 6px 6px 0 0; background: #ffffff; border-bottom: 1px solid #e5e7eb;" onclick="openAgodaMapModal()" data-bs-toggle="modal" data-bs-target="#interactiveMapModal" title="Click to search on map">
        <img src="{{ asset('images/agoda/bkg-map-entry.svg') }}" onerror="this.onerror=null; this.src='https://cdn6.agoda.net/images/MAPS-1213/default/bkg-map-entry.svg';" alt="Map" style="width: 100%; height: 85px; object-fit: cover; display: block;">
        {{-- Centered Red Map Pin --}}
        <div class="position-absolute" style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 10; pointer-events: none;">
            <i class="fa-solid fa-location-dot" style="font-size: 30px; color: #e03126; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
        </div>
        <div style="background: #ffffff; height: 40px; display: flex; align-items: center; justify-content: center;">
            <span style="font-weight: 700; font-size: 13.5px; color: #2a2a2e; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                SEARCH ON MAP
            </span>
        </div>
    </div>

    <div class="p-3">
        <form action="{{ route('search.index') }}" method="GET" id="filterSidebarForm">
            <input type="hidden" name="destination" value="{{ request('destination') }}">
            <input type="hidden" name="search_type" value="{{ request('search_type', 'hotel') }}">
            <input type="hidden" name="check_in" value="{{ request('check_in') }}">
            <input type="hidden" name="check_out" value="{{ request('check_out') }}">
            <input type="hidden" name="guests" value="{{ request('guests') }}">
            @if(request('rooms'))
                <input type="hidden" name="rooms" value="{{ request('rooms') }}">
            @endif
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if(request('lat'))
                <input type="hidden" name="lat" value="{{ request('lat') }}">
            @endif
            @if(request('lng'))
                <input type="hidden" name="lng" value="{{ request('lng') }}">
            @endif

            {{-- 2. Agoda Text Search Input (Exact Screenshot Parity) --}}
            <div class="agoda-filter-group" style="padding-bottom: 14px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #687176; font-size: 13px;"></i>
                    <input type="text" name="q" id="outerSearchTextInput" value="{{ request('q') ?: request('name') }}" placeholder="Text search" class="w-100" style="height: 38px; border: 1.5px solid #dcdfe4; border-radius: 4px; padding: 0 12px 0 34px; font-size: 13px; color: #2a2a2e; outline: none; transition: all 0.15s ease;" onfocus="this.style.borderColor='#2067e1'; this.style.boxShadow='0 0 0 2px rgba(32,103,225,0.15)';" onblur="this.style.borderColor='#dcdfe4'; this.style.boxShadow='none';" onkeydown="if(event.key==='Enter'){event.preventDefault(); triggerSidebarSubmit();}">
                </div>
            </div>

            {{-- 3. Budget Range Slider (Dual Handles + MIN/MAX Boxes with Dashed Separator) --}}
            @php
                $dbMinPrice = (int)($priceRange['min'] ?? 500);
                $dbMaxPrice = (int)($priceRange['max'] ?? 50000);
                if ($dbMinPrice <= 0) $dbMinPrice = 500;
                if ($dbMaxPrice <= $dbMinPrice) $dbMaxPrice = 50000;
                $selMin = (int)request('min_price', $dbMinPrice);
                $selMax = (int)request('max_price', $dbMaxPrice);
                $currencyCode = session('currency', 'BDT');
            @endphp
            <div class="agoda-filter-group" id="priceSliderWidget">
                <div class="agoda-filter-title">Your budget (per night)</div>

                {{-- Dual range track --}}
                <div class="position-relative mt-3 mb-3" style="height: 24px;" id="dualRangeContainer">
                    <div id="dualRangeTrack" style="position:absolute;top:50%;left:0;right:0;height:4px;background:#e2e8f0;border-radius:2px;transform:translateY(-50%);"></div>
                    <div id="dualRangeActive" style="position:absolute;top:50%;height:4px;background:#2067e1;border-radius:2px;transform:translateY(-50%);"></div>
                    <input type="range" id="priceMinRange" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}" step="100"
                        value="{{ $selMin }}"
                        style="position:absolute;width:100%;top:50%;transform:translateY(-50%);opacity:0;cursor:pointer;z-index:3;height:24px;"
                        oninput="syncPriceSliderFromRange()">
                    <input type="range" id="priceMaxRange" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}" step="100"
                        value="{{ $selMax }}"
                        style="position:absolute;width:100%;top:50%;transform:translateY(-50%);opacity:0;cursor:pointer;z-index:3;height:24px;"
                        oninput="syncPriceSliderFromRange()">
                    {{-- Visual circular thumb handles --}}
                    <div id="thumbMin" style="position:absolute;top:50%;width:18px;height:18px;background:#ffffff;border:2.5px solid #2067e1;border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 1px 4px rgba(0,0,0,0.25);pointer-events:none;z-index:4;"></div>
                    <div id="thumbMax" style="position:absolute;top:50%;width:18px;height:18px;background:#ffffff;border:2.5px solid #2067e1;border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 1px 4px rgba(0,0,0,0.25);pointer-events:none;z-index:4;"></div>
                </div>

                {{-- Exact Agoda MIN / MAX Input Boxes with Dashed Separator --}}
                <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                    {{-- MIN Box --}}
                    <div class="flex-grow-1">
                        <span class="d-block text-dark text-uppercase mb-1" style="font-size: 10px; font-weight: 700;">MIN</span>
                        <div class="bg-white d-flex align-items-center justify-content-between" style="border: 1px solid #737373; height: 34px; border-radius: 4px; padding: 0 10px;">
                            <span style="font-size: 13px; color: #2a2a2e; font-weight: 500; margin-right: 6px;">{{ $currencyCode }}</span>
                            <input type="number" id="priceMinInput" class="border-0 bg-transparent text-end p-0 m-0 flex-grow-1"
                                style="font-size: 13px; font-weight: 600; color: #2a2a2e; outline: none; width: 60px;"
                                value="{{ $selMin }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                                onchange="syncPriceSliderFromInput(true)">
                        </div>
                    </div>

                    {{-- Dashed Separator --}}
                    <div class="text-muted fw-bold align-self-end flex-shrink-0" style="font-size: 11px; letter-spacing: 0.5px; color: #737373 !important; margin-bottom: 8px;">
                        -------
                    </div>

                    {{-- MAX Box --}}
                    <div class="flex-grow-1">
                        <span class="d-block text-dark text-uppercase mb-1" style="font-size: 10px; font-weight: 700;">MAX</span>
                        <div class="bg-white d-flex align-items-center justify-content-between" style="border: 1px solid #737373; height: 34px; border-radius: 4px; padding: 0 10px;">
                            <span style="font-size: 13px; color: #2a2a2e; font-weight: 500; margin-right: 6px;">{{ $currencyCode }}</span>
                            <input type="number" id="priceMaxInput" class="border-0 bg-transparent text-end p-0 m-0 flex-grow-1"
                                style="font-size: 13px; font-weight: 600; color: #2a2a2e; outline: none; width: 60px;"
                                value="{{ $selMax }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                                onchange="syncPriceSliderFromInput(true)">
                        </div>
                    </div>
                </div>

                {{-- Price range subtitle --}}
                <div class="text-muted small mt-2" style="font-size: 11.5px;">
                    Price range: {{ $currencyCode }} <span id="priceRangeReadout">{{ number_format($selMin) }} to {{ number_format($selMax) }}</span>
                </div>

                <input type="hidden" name="min_price" id="minPriceHidden" value="{{ $selMin }}">
                <input type="hidden" name="max_price" id="maxPriceHidden" value="{{ $selMax }}">
            </div>

            {{-- 4. Dynamic Data Helpers --}}
            @php
                $resultsList = $searchResults['merged_results'] ?? [];
                $gRatings     = (array) request('guest_rating', []);
                $sRatings     = (array) request('star_rating', []);
                $pTypes       = (array) request('property_type', []);
                $selAmenities = (array) request('amenities', []);
                $selAreas     = (array) request('area', []);

                $getStarCount = function($stars) use ($resultsList) {
                    return collect($resultsList)->filter(function($p) use ($stars) {
                        $st = is_object($p) ? ($p->star_rating ?? 0) : ($p['star_rating'] ?? 0);
                        return (int)$st === (int)$stars;
                    })->count();
                };

                $getTypeCount = function($typeStr) use ($resultsList) {
                    return collect($resultsList)->filter(function($p) use ($typeStr) {
                        $t = strtolower(is_object($p) ? ($p->type ?? '') : ($p['type'] ?? ''));
                        return str_contains($t, strtolower($typeStr));
                    })->count();
                };

                $getScoreCount = function($minScore) use ($resultsList) {
                    return collect($resultsList)->filter(function($p) use ($minScore) {
                        $score = is_object($p) ? ($p->rating_score ?? $p->rating ?? 0) : ($p['rating_score'] ?? $p['rating'] ?? 0);
                        return (float)$score >= $minScore;
                    })->count();
                };

                $getAmenityCount = function($amenityStr) use ($resultsList) {
                    return collect($resultsList)->filter(function($p) use ($amenityStr) {
                        $ams = is_object($p) ? ($p->amenities ?? []) : ($p['amenities'] ?? []);
                        if (is_string($ams)) $ams = json_decode($ams, true) ?: [];
                        $low = array_map('strtolower', (array)$ams);
                        return collect($low)->contains(fn($a) => str_contains($a, strtolower($amenityStr)));
                    })->count();
                };

                $destCity = !empty(trim($destination ?? '')) ? trim($destination) : 'Destination';
            @endphp

            {{-- 6. Section: Your filters / Popular filters for {Destination} --}}
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Your filters</div>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Family/child friendly" @checked(in_array('Family/child friendly', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Family/child friendly</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Non-smoking" @checked(in_array('Non-smoking', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Non-smoking</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="free_cancel" value="1" @checked(request('free_cancel')) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Free cancellation</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Breakfast included" @checked(in_array('Breakfast included', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Breakfast included</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Swimming pool" @checked(in_array('Swimming pool', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Swimming pool <span class="agoda-check-count">({{ $getAmenityCount('pool') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Free WiFi" @checked(in_array('Free WiFi', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Free Wi-Fi <span class="agoda-check-count">({{ $getAmenityCount('wifi') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="guest_rating[]" value="9" @checked(in_array(9, $gRatings)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Location: 9+ Exceptional</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="guest_rating[]" value="7" @checked(in_array(7, $gRatings)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Guest rating: 7+ Very good</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Balcony/terrace" @checked(in_array('Balcony/terrace', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Balcony/terrace</span>
                </label>
            </div>

            {{-- 7. Section: Property type (Exact Agoda Inline Counts) --}}
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Property type</div>

                <label class="agoda-check-row">
                    <input type="checkbox" name="property_type[]" value="hotel" @checked(in_array('hotel', $pTypes)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Hotel <span class="agoda-check-count">({{ $getTypeCount('hotel') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="property_type[]" value="resort" @checked(in_array('resort', $pTypes)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Resort <span class="agoda-check-count">({{ $getTypeCount('resort') }})</span></span>
                </label>

                @if($getTypeCount('apartment') > 0)
                <label class="agoda-check-row">
                    <input type="checkbox" name="property_type[]" value="apartment" @checked(in_array('apartment', $pTypes)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Apartment <span class="agoda-check-count">({{ $getTypeCount('apartment') }})</span></span>
                </label>
                @endif

                @if($getTypeCount('villa') > 0)
                <label class="agoda-check-row">
                    <input type="checkbox" name="property_type[]" value="villa" @checked(in_array('villa', $pTypes)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Villa <span class="agoda-check-count">({{ $getTypeCount('villa') }})</span></span>
                </label>
                @endif
            </div>

            {{-- 8. Section: Star rating (Exact Agoda 5, 4, 3, 2, 1 Stars) --}}
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Star rating</div>

                @foreach([
                    5 => ['label' => '5 stars', 'stars' => '★★★★★'],
                    4 => ['label' => '4 stars', 'stars' => '★★★★'],
                    3 => ['label' => '3 stars', 'stars' => '★★★'],
                    2 => ['label' => '2 stars', 'stars' => '★★'],
                    1 => ['label' => '1 star',  'stars' => '★'],
                ] as $starVal => $starInfo)
                @php $sCount = $getStarCount($starVal); @endphp
                <label class="agoda-check-row">
                    <input type="checkbox" name="star_rating[]" value="{{ $starVal }}" @checked(in_array($starVal, $sRatings)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">
                        <span><span style="color: #ffb200; font-size: 12px; margin-right: 2px;">{{ $starInfo['stars'] }}</span> {{ $starInfo['label'] }}</span>
                        <span class="agoda-check-count">({{ $sCount }})</span>
                    </span>
                </label>
                @endforeach
            </div>

            {{-- 9. Section: Facilities --}}
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Facilities</div>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Swimming pool" @checked(in_array('Swimming pool', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Swimming pool <span class="agoda-check-count">({{ $getAmenityCount('pool') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Free WiFi" @checked(in_array('Free WiFi', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Wi-Fi [free] <span class="agoda-check-count">({{ $getAmenityCount('wifi') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Free Parking" @checked(in_array('Free Parking', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Car park <span class="agoda-check-count">({{ $getAmenityCount('parking') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Airport transfer" @checked(in_array('Airport transfer', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Airport transfer <span class="agoda-check-count">({{ $getAmenityCount('airport') }})</span></span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Gym" @checked(in_array('Gym', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Fitness center <span class="agoda-check-count">({{ $getAmenityCount('gym') }})</span></span>
                </label>
            </div>

            {{-- 10. Section: Payment options --}}
            <div class="agoda-filter-group" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                <div class="agoda-filter-title">Payment options</div>

                <label class="agoda-check-row">
                    <input type="checkbox" name="pay_later" value="1" @checked(request('pay_later')) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Book without a credit card</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="pay_at_hotel" value="1" @checked(request('pay_at_hotel')) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Pay at the hotel</span>
                </label>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var minRange = document.getElementById('priceMinRange');
    var maxRange = document.getElementById('priceMaxRange');
    var thumbMin = document.getElementById('thumbMin');
    var thumbMax = document.getElementById('thumbMax');
    var activeBar = document.getElementById('dualRangeActive');
    var minInput = document.getElementById('priceMinInput');
    var maxInput = document.getElementById('priceMaxInput');
    var minHidden = document.getElementById('minPriceHidden');
    var maxHidden = document.getElementById('maxPriceHidden');
    var readout = document.getElementById('priceRangeReadout');
    var debounceTimer;

    window.syncPriceSliderFromRange = function() {
        if (!minRange || !maxRange) return;
        var min = parseInt(minRange.value) || 0;
        var max = parseInt(maxRange.value) || 0;
        var rangeMin = parseInt(minRange.min) || 0;
        var rangeMax = parseInt(minRange.max) || 50000;

        if (min > max - 100) { min = max - 100; minRange.value = min; }
        if (max < min + 100) { max = min + 100; maxRange.value = max; }

        var pMin = ((min - rangeMin) / (rangeMax - rangeMin)) * 100;
        var pMax = ((max - rangeMin) / (rangeMax - rangeMin)) * 100;

        if (thumbMin) thumbMin.style.left = pMin + '%';
        if (thumbMax) thumbMax.style.left = pMax + '%';
        if (activeBar) {
            activeBar.style.left  = pMin + '%';
            activeBar.style.width = (pMax - pMin) + '%';
        }

        if (minInput) minInput.value = min;
        if (maxInput) maxInput.value = max;
        if (minHidden) minHidden.value = min;
        if (maxHidden) maxHidden.value = max;
        if (readout) {
            readout.textContent = min.toLocaleString() + ' to ' + max.toLocaleString();
        }

        triggerDebouncedSearch();
    };

    window.syncPriceSliderFromInput = function(immediate) {
        if (!minInput || !maxInput || !minRange || !maxRange) return;
        var min = parseInt(minInput.value) || 0;
        var max = parseInt(maxInput.value) || 0;
        var rangeMin = parseInt(minRange.min) || 0;
        var rangeMax = parseInt(minRange.max) || 50000;

        if (min < rangeMin) min = rangeMin;
        if (max > rangeMax) max = rangeMax;
        if (min > max) {
            var temp = min; min = max; max = temp;
        }

        minRange.value = min;
        maxRange.value = max;

        var pMin = ((min - rangeMin) / (rangeMax - rangeMin)) * 100;
        var pMax = ((max - rangeMin) / (rangeMax - rangeMin)) * 100;

        if (thumbMin) thumbMin.style.left = pMin + '%';
        if (thumbMax) thumbMax.style.left = pMax + '%';
        if (activeBar) {
            activeBar.style.left  = pMin + '%';
            activeBar.style.width = (pMax - pMin) + '%';
        }

        if (minHidden) minHidden.value = min;
        if (maxHidden) maxHidden.value = max;
        if (readout) {
            readout.textContent = min.toLocaleString() + ' to ' + max.toLocaleString();
        }

        if (immediate) {
            triggerSidebarSubmit();
        } else {
            triggerDebouncedSearch();
        }
    };

    function triggerDebouncedSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            triggerSidebarSubmit();
        }, 500);
    }

    window.triggerSidebarSubmit = function() {
        var form = document.getElementById('filterSidebarForm');
        var resultsContainer = document.getElementById('searchResultsContainer') || document.getElementById('contentContainer') || document.querySelector('.hotel-list-container');
        if (!resultsContainer || !form) {
            if (form) form.submit();
            return;
        }

        resultsContainer.style.opacity = '0.4';
        resultsContainer.style.transition = 'opacity 0.15s ease';

        var formData = new FormData(form);
        var params = new URLSearchParams();
        for (var pair of formData.entries()) {
            if (pair[1] !== '' && pair[1] !== null) {
                params.append(pair[0], pair[1]);
            }
        }

        var targetUrl = form.action + '?' + params.toString();
        fetch(targetUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            
            var newFeed = doc.getElementById('searchResultsContainer') || doc.getElementById('contentContainer') || doc.querySelector('.hotel-list-container');
            if (newFeed && resultsContainer) {
                resultsContainer.innerHTML = newFeed.innerHTML;
            }
            
            var newCount = doc.querySelector('[data-element-name="properties-available-text"]') || doc.getElementById('resultsCount');
            var curCount = document.querySelector('[data-element-name="properties-available-text"]') || document.getElementById('resultsCount');
            if (newCount && curCount) {
                curCount.innerHTML = newCount.innerHTML;
            }
            resultsContainer.style.opacity = '1';
            history.pushState(null, '', targetUrl);
        })
        .catch(function(err) {
            console.error('Sidebar AJAX error:', err);
            resultsContainer.style.opacity = '1';
            form.submit();
        });
    };

    // Initialize slider on load
    if (minRange && maxRange) {
        syncPriceSliderFromRange();
    }
})();
</script>
