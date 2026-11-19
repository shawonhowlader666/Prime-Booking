
<style>
#priceMinInput::-webkit-outer-spin-button,
#priceMinInput::-webkit-inner-spin-button,
#priceMaxInput::-webkit-outer-spin-button,
#priceMaxInput::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
#priceMinInput, #priceMaxInput {
    -moz-appearance: textfield !important;
}
</style>
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

{{-- The search page is a standalone (non-Bootstrap) layout, so the few utilities this sidebar needs are defined here. --}}
<style>
    .pb-filter { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; margin-bottom: 24px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .pb-filter .d-none { display: none !important; }
    .pb-filter-body { padding: 16px; }
    .pb-map-card { position: relative; display: block; width: 100%; height: 125px; padding: 0; border: 0; border-bottom: 1px solid #e5e7eb; background: #fff; cursor: pointer; text-align: center; overflow: hidden; }
    .pb-map-card img { width: 100%; height: 85px; object-fit: cover; display: block; }
    .pb-map-card .pb-map-pin { position: absolute; top: 18px; left: 50%; transform: translateX(-50%); pointer-events: none; filter: drop-shadow(0 2px 4px rgba(0,0,0,.3)); }
    .pb-map-card .pb-map-label { height: 40px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13.5px; color: #2a2a2e; letter-spacing: .5px; }
    .pb-map-card:hover .pb-map-label { color: #2067e1; }
    .pb-filter-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }
    .pb-filter-head h6 { margin: 0; font-size: 14px; font-weight: 700; color: #2a2a2e; }
    .pb-link { font-size: 11.5px; font-weight: 700; color: #2067e1; text-decoration: none; background: none; border: 0; padding: 0; cursor: pointer; }
    .pb-text-search { position: relative; }
    .pb-text-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); }
    .pb-text-search input { width: 100%; box-sizing: border-box; height: 38px; border: 1.5px solid #dcdfe4; border-radius: 4px; padding: 0 12px 0 34px; font-size: 13px; color: #2a2a2e; outline: none; }
    .pb-text-search input:focus, .pb-geo-select:focus { border-color: #2067e1; box-shadow: 0 0 0 2px rgba(32,103,225,.15); }
    .pb-geo-select { width: 100%; box-sizing: border-box; height: 36px; border: 1.5px solid #dcdfe4; border-radius: 4px; padding: 0 10px; font-size: 12.5px; font-weight: 500; color: #2a2a2e; background: #fff; outline: none; margin-bottom: 8px; cursor: pointer; }
</style>

<div class="pb-filter" id="filterSidebarRoot">

    {{-- 1. Map entry card: opens the map of the hotels on this page --}}
    <button type="button" class="pb-map-card agoda-map-trigger-card" onclick="if (window.openAgodaMapModal) openAgodaMapModal();" title="Search on map" aria-label="Search on map">
        <img src="{{ asset('images/agoda/bkg-map-entry.svg') }}" alt="">
        <svg class="pb-map-pin" width="26" height="34" viewBox="0 0 24 32" aria-hidden="true"><path fill="#e03126" d="M12 0C5.4 0 0 5.3 0 11.9 0 20.8 12 32 12 32s12-11.2 12-20.1C24 5.3 18.6 0 12 0z"/><circle cx="12" cy="12" r="4.5" fill="#fff"/></svg>
        <span class="pb-map-label">SEARCH ON MAP</span>
    </button>

    <div class="pb-filter-body">
        <div class="pb-filter-head">
            <h6>Filter by</h6>
            <a href="{{ route('search.index', array_filter(request()->only(['destination', 'search_type', 'check_in', 'check_out', 'guests', 'rooms', 'children']))) }}" class="pb-link">CLEAR ALL</a>
        </div>

        <form action="{{ route('search.index') }}" method="GET" id="filterSidebarForm">
            <input type="hidden" name="destination" id="sidebarDestinationInput" value="{{ request('destination') }}">
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
            @if(request('children'))
                <input type="hidden" name="children" value="{{ request('children') }}">
            @endif

            {{-- 2. Text search: hotel name, area or address --}}
            <div class="agoda-filter-group" style="padding-bottom: 14px; margin-bottom: 16px;">
                <div class="pb-text-search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#687176" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
                    <input type="search" name="q" id="outerSearchTextInput" value="{{ request('q') }}" maxlength="100" placeholder="Text search" aria-label="Search by hotel name or area"
                           onkeydown="if (event.key === 'Enter') { event.preventDefault(); triggerSidebarSubmit(); }"
                           onsearch="triggerSidebarSubmit()">
                </div>
            </div>

            {{-- 2.1 Location / Area: cascading division → district → upazila picker --}}
            @php
                $geoConfig = config('bangladesh-geo.divisions', []);
                $selectedDivision = (string) request('division', '');
                $selectedDistrict = (string) request('district', '');
                $selectedUpazila  = (string) request('upazila', '');
                $hasGeo = $selectedDivision !== '' || $selectedDistrict !== '' || $selectedUpazila !== '';
            @endphp
            <div class="agoda-filter-group" id="locationFilterWidget" style="padding-bottom: 10px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span class="agoda-filter-title" style="margin: 0;">Location / Area</span>
                    <button type="button" id="resetLocationBtn" class="pb-link {{ $hasGeo ? '' : 'd-none' }}">Reset</button>
                </div>

                <select name="division" id="divisionSelectFilter" class="pb-geo-select" aria-label="Select region">
                    <option value="">Select region...</option>
                    @foreach($geoConfig as $divKey => $divInfo)
                        <option value="{{ $divKey }}" @selected($selectedDivision === (string) $divKey)>{{ $divInfo['name'] ?? $divKey }}</option>
                    @endforeach
                </select>

                <div id="geoDistrictContainer" class="{{ $selectedDivision !== '' ? '' : 'd-none' }}">
                    <select name="district" id="districtSelectFilter" class="pb-geo-select" aria-label="Select city or district">
                        <option value="">Select city / district...</option>
                        @foreach(($geoConfig[$selectedDivision]['districts'] ?? []) as $distKey => $distInfo)
                            <option value="{{ $distKey }}" @selected($selectedDistrict === (string) $distKey)>{{ $distInfo['name'] ?? $distKey }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="geoUpazilaContainer" class="{{ ($selectedDivision !== '' && $selectedDistrict !== '') ? '' : 'd-none' }}">
                    <select name="upazila" id="upazilaSelectFilter" class="pb-geo-select" aria-label="Select area">
                        <option value="">Select area / spot...</option>
                        @foreach(($geoConfig[$selectedDivision]['districts'][$selectedDistrict]['upazilas'] ?? []) as $upazilaName)
                            <option value="{{ $upazilaName }}" @selected($selectedUpazila === (string) $upazilaName)>{{ $upazilaName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 3. Budget Range Box (100% Exact Agoda Floating MIN / MAX Parity) --}}
            @php
                $dbMinPrice = (int)($priceRange['min'] ?? 500);
                $dbMaxPrice = (int)($priceRange['max'] ?? 50000);
                if ($dbMinPrice <= 0) $dbMinPrice = 500;
                if ($dbMaxPrice <= $dbMinPrice) $dbMaxPrice = 50000;
                $selMin = (int)request('min_price', $dbMinPrice);
                $selMax = (int)request('max_price', $dbMaxPrice);
                $currencyCode = session('currency', 'BDT');
            @endphp
            <div class="agoda-filter-group" id="PriceFilterRange">
                <div class="agoda-filter-title" style="font-size: 14px; font-weight: 700; color: #2a2a2e; margin-bottom: 14px;">Your budget (per night)</div>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    {{-- MIN Box with Floating Label --}}
                    <div style="position: relative; background: #ffffff; border: 1px solid #94a3b8; border-radius: 8px; height: 38px; display: flex; align-items: center; padding: 0 12px; box-sizing: border-box;">
                        <div style="position: absolute; top: -8px; left: 10px; background: #ffffff; padding: 0 4px; font-size: 10px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; line-height: 1;">MIN</div>
                        <span style="font-size: 13.5px; font-weight: 600; color: #64748b; margin-right: 8px; flex-shrink: 0;">{{ $currencyCode }}</span>
                        <input type="number" id="priceMinInput" name="min_price" value="{{ $selMin }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                               style="border: none; outline: none; background: transparent; width: 100%; font-size: 14px; font-weight: 600; color: #1e293b; padding: 0;"
                               onchange="document.getElementById('filterSidebarForm').submit();">
                    </div>

                    {{-- MAX Box with Floating Label --}}
                    <div style="position: relative; background: #ffffff; border: 1px solid #94a3b8; border-radius: 8px; height: 38px; display: flex; align-items: center; padding: 0 12px; box-sizing: border-box;">
                        <div style="position: absolute; top: -8px; left: 10px; background: #ffffff; padding: 0 4px; font-size: 10px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; line-height: 1;">MAX</div>
                        <span style="font-size: 13.5px; font-weight: 600; color: #64748b; margin-right: 8px; flex-shrink: 0;">{{ $currencyCode }}</span>
                        <input type="number" id="priceMaxInput" name="max_price" value="{{ $selMax }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                               style="border: none; outline: none; background: transparent; width: 100%; font-size: 14px; font-weight: 600; color: #1e293b; padding: 0;"
                               onchange="document.getElementById('filterSidebarForm').submit();">
                    </div>
                </div>

                {{-- Price Range Summary Text --}}
                <div style="margin-top: 10px;">
                    <p style="font-size: 12.5px; color: #64748b; margin: 0;">Price range: {{ $currencyCode }} {{ number_format($selMin) }} to {{ $currencyCode }} {{ number_format($selMax) }}</p>
                </div>
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

            {{-- 6. Section: Popular filters for {Destination} (Exact Agoda Image 2 Parity) --}}
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Popular filters for {{ $destCity }}</div>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Family/child friendly" @checked(in_array('Family/child friendly', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Family/child friendly</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Non-smoking" @checked(in_array('Non-smoking', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Non-smoking</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="amenities[]" value="Pets allowed" @checked(in_array('Pets allowed', $selAmenities)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Pets allowed</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="guest_rating[]" value="7" @checked(in_array(7, $gRatings)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">Location: 7+ Very good</span>
                </label>

                <label class="agoda-check-row">
                    <input type="checkbox" name="area[]" value="{{ $destCity }}" @checked(in_array($destCity, $selAreas)) onchange="triggerSidebarSubmit()">
                    <span class="agoda-check-label">{{ $destCity }}</span>
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

            {{-- 5. Section: Neighborhood / Area Selection (Exact Agoda Parity) --}}
            @php
                $displayAreas = $popularAreas ?? [];
                if (empty($displayAreas)) {
                    $extracted = collect($resultsList)->map(function($p) {
                        return is_object($p) ? ($p->nearest_landmark ?? null) : ($p['nearest_landmark'] ?? null);
                    })->filter()->unique()->take(6)->values()->all();
                    if (!empty($extracted)) {
                        $displayAreas = $extracted;
                    }
                }
            @endphp
            @if(!empty($displayAreas) && count($displayAreas) > 0)
            <div class="agoda-filter-group">
                <div class="agoda-filter-title">Neighborhood</div>
                @foreach($displayAreas as $areaName)
                    @php
                        $cleanAreaName = trim(preg_replace('/\(.*?\)|\d+\.?\d*\s*(km|m)\b/i', '', $areaName));
                        $areaCount = collect($resultsList)->filter(function($p) use ($cleanAreaName) {
                            $addr = is_object($p) ? ($p->address . ' ' . $p->city . ' ' . ($p->nearest_landmark ?? '')) : (($p['address'] ?? '') . ' ' . ($p['city'] ?? '') . ' ' . ($p['nearest_landmark'] ?? ''));
                            return stripos($addr, $cleanAreaName) !== false;
                        })->count();
                        $isChecked = in_array($cleanAreaName, $selAreas) || in_array($areaName, $selAreas) || (request('q') === $cleanAreaName);
                    @endphp
                    <label class="agoda-check-row">
                        <input type="checkbox" name="area[]" value="{{ $cleanAreaName }}" @checked($isChecked) onchange="triggerSidebarSubmit()">
                        <span class="agoda-check-label">{{ $cleanAreaName }} <span class="agoda-check-count">({{ $areaCount ?: 1 }})</span></span>
                    </label>
                @endforeach
            </div>
            @endif

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

    // Regions of the page that depend on the filters. Each is swapped in place after an AJAX filter change.
    var SWAP_IDS = ['searchPageRightColumn', 'breadcrumb', 'resultsMapData'];
    var inFlight = null;

    window.triggerSidebarSubmit = function() {
        var form = document.getElementById('filterSidebarForm');
        if (!form) return;
        var main = document.getElementById('searchPageRightColumn');
        if (!main || !window.fetch || !window.DOMParser) { form.submit(); return; }

        var params = new URLSearchParams();
        new FormData(form).forEach(function(value, key) {
            if (value !== '' && value !== null) params.append(key, value);
        });
        var targetUrl = form.action + '?' + params.toString();

        if (inFlight) inFlight.abort();
        inFlight = window.AbortController ? new AbortController() : null;
        main.style.transition = 'opacity 0.15s ease';
        main.style.opacity = '0.4';

        fetch(targetUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: inFlight ? inFlight.signal : undefined })
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(function(html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            SWAP_IDS.forEach(function(id) {
                var fresh = doc.getElementById(id);
                var current = document.getElementById(id);
                if (fresh && current) current.innerHTML = fresh.innerHTML;
            });
            if (doc.title) document.title = doc.title;
            main.style.opacity = '1';
            history.pushState(null, '', targetUrl);
            document.dispatchEvent(new CustomEvent('search:results-updated'));
        })
        .catch(function(err) {
            if (err && err.name === 'AbortError') return;
            main.style.opacity = '1';
            form.submit();
        });
    };

    // Back/forward after AJAX filtering: reload so results match the URL.
    window.addEventListener('popstate', function() { window.location.reload(); });

    // ── Location / Area cascading picker (division → district → upazila) ──
    var geoData = @json(config('bangladesh-geo.divisions', []));
    var divisionSelect = document.getElementById('divisionSelectFilter');
    var districtSelect = document.getElementById('districtSelectFilter');
    var upazilaSelect  = document.getElementById('upazilaSelectFilter');
    var districtBox    = document.getElementById('geoDistrictContainer');
    var upazilaBox     = document.getElementById('geoUpazilaContainer');
    var resetGeoBtn    = document.getElementById('resetLocationBtn');
    var destInput      = document.getElementById('sidebarDestinationInput');
    var originalDest   = destInput ? destInput.value : '';

    function fillSelect(select, placeholder, entries) {
        select.innerHTML = '';
        var first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder;
        select.appendChild(first);
        entries.forEach(function(e) {
            var opt = document.createElement('option');
            opt.value = e.value;
            opt.textContent = e.label;
            select.appendChild(opt);
        });
    }

    // The picked area replaces the typed destination, otherwise "Cox's Bazar" + "Dhaka" would match nothing.
    function syncDestinationWithGeo() {
        if (!destInput) return;
        var div = divisionSelect.value, dist = districtSelect.value, upz = upazilaSelect.value;
        if (upz) {
            destInput.value = upz;
        } else if (dist) {
            destInput.value = dist;
        } else if (div) {
            destInput.value = '';
        } else {
            destInput.value = originalDest;
        }
        if (resetGeoBtn) resetGeoBtn.classList.toggle('d-none', !div);
    }

    if (divisionSelect && districtSelect && upazilaSelect) {
        divisionSelect.addEventListener('change', function() {
            var div = this.value;
            var districts = (geoData[div] && geoData[div].districts) || {};
            fillSelect(districtSelect, 'Select city / district...', Object.keys(districts).map(function(k) {
                return { value: k, label: districts[k].name || k };
            }));
            fillSelect(upazilaSelect, 'Select area / spot...', []);
            districtBox.classList.toggle('d-none', !div);
            upazilaBox.classList.add('d-none');
            syncDestinationWithGeo();
            triggerSidebarSubmit();
        });

        districtSelect.addEventListener('change', function() {
            var div = divisionSelect.value, dist = this.value;
            var upazilas = (dist && geoData[div] && geoData[div].districts[dist] && geoData[div].districts[dist].upazilas) || [];
            fillSelect(upazilaSelect, 'Select area / spot...', upazilas.map(function(u) { return { value: u, label: u }; }));
            upazilaBox.classList.toggle('d-none', !upazilas.length);
            syncDestinationWithGeo();
            triggerSidebarSubmit();
        });

        upazilaSelect.addEventListener('change', function() {
            syncDestinationWithGeo();
            triggerSidebarSubmit();
        });

        if (resetGeoBtn) {
            resetGeoBtn.addEventListener('click', function() {
                divisionSelect.value = '';
                fillSelect(districtSelect, 'Select city / district...', []);
                fillSelect(upazilaSelect, 'Select area / spot...', []);
                districtBox.classList.add('d-none');
                upazilaBox.classList.add('d-none');
                syncDestinationWithGeo();
                triggerSidebarSubmit();
            });
        }
    }

    // Initialize slider on load
    if (minRange && maxRange) {
        syncPriceSliderFromRange();
    }
})();
</script>
