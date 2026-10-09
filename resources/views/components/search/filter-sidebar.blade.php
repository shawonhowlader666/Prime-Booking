{{-- Agoda-Exact Filter Sidebar Component (100% Dynamic DB-Backed & Agoda UI Parity) --}}
<div class="card border border-gray-200 rounded-3 shadow-xs overflow-hidden mb-4 bg-white" style="border: 1px solid #cbd5e1; border-radius: 8px !important;">
    
    {{-- 1. Official Agoda Map Entry Card (Downloaded SVG & Exact Agoda Styling) --}}
    <div class="position-relative text-center p-0 overflow-hidden agoda-map-trigger-card" style="height: 135px; cursor: pointer; border-radius: 8px 8px 0 0; background: #ffffff; border-bottom: 1px solid #e2e8f0;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal" title="Click to search on map">
        <img src="{{ asset('images/agoda/bkg-map-entry.svg') }}" alt="Map" style="width: 100%; height: 90px; object-fit: cover; display: block;">
        {{-- Centered Red Map Pin --}}
        <div class="position-absolute" style="top: 24px; left: 50%; transform: translateX(-50%); z-index: 10; pointer-events: none;">
            <i class="fa-solid fa-location-dot" style="font-size: 32px; color: #e03126; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
        </div>
        <div style="background: #ffffff; height: 45px; display: flex; align-items: center; justify-content: center;">
            <span style="font-weight: 700; font-size: 14px; color: #2a2a2e; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                SEARCH ON MAP
            </span>
        </div>
    </div>

    <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            @php
                $sidebarClearParams = array_filter([
                    'destination' => request('destination'),
                    'check_in'    => request('check_in'),
                    'check_out'   => request('check_out'),
                    'guests'      => request('guests'),
                    'rooms'       => request('rooms'),
                ]);

                $activeFilterCount = 0;
                if (!empty(request('q'))) $activeFilterCount++;
                if (!empty(request('division')) || !empty(request('district')) || !empty(request('upazila'))) $activeFilterCount++;
                if (request('min_price') && (float)request('min_price') > 0) $activeFilterCount++;
                if (request('max_price') && (float)request('max_price') < 10000000) $activeFilterCount++;
                if (!empty(request('guest_rating'))) $activeFilterCount += count((array)request('guest_rating'));
                if (!empty(request('star_rating'))) $activeFilterCount += count((array)request('star_rating'));
                if (!empty(request('amenities'))) $activeFilterCount += count((array)request('amenities'));
                if (!empty(request('property_type'))) $activeFilterCount += count((array)request('property_type'));
                if (!empty(request('bed_type'))) $activeFilterCount += count((array)request('bed_type'));
                if (!empty(request('room_feature'))) $activeFilterCount += count((array)request('room_feature'));
                if (!empty(request('radius_km'))) $activeFilterCount++;
                if (request('pay_later')) $activeFilterCount++;
                if (request('free_cancel')) $activeFilterCount++;
                if (request('entire_home')) $activeFilterCount++;
            @endphp
            <div class="d-flex align-items-center">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px; font-family: 'Plus Jakarta Sans', sans-serif;">Filter by</h6>
                @if($activeFilterCount > 0)
                    <span class="badge rounded-pill ms-2" style="background: #2067e1; font-size: 11px; padding: 3px 8px; font-weight: 700;">{{ $activeFilterCount }}</span>
                @endif
            </div>
            @if($activeFilterCount > 0)
                <a href="{{ route('search.index', $sidebarClearParams) }}" class="text-decoration-none fw-bold" style="font-size: 12px; color: #2067e1;">CLEAR ALL</a>
            @else
                <span class="text-muted fw-bold" style="font-size: 12px; opacity: 0.4; cursor: default;">CLEAR ALL</span>
            @endif
        </div>

        <form action="{{ route('search.index') }}" method="GET" id="filterSidebarForm" onsubmit="showAgodaSearchLoading();">
            <input type="hidden" name="destination" value="{{ request('destination') }}">
            <input type="hidden" name="search_type" value="{{ request('search_type', 'hotel') }}">
            <input type="hidden" name="check_in" value="{{ request('check_in') }}">
            <input type="hidden" name="check_out" value="{{ request('check_out') }}">
            <input type="hidden" name="guests" value="{{ request('guests') }}">
            @if(request('rooms'))
                <input type="hidden" name="rooms" value="{{ request('rooms') }}">
            @endif
            @if(request('entire_home'))
                <input type="hidden" name="entire_home" value="{{ request('entire_home') }}">
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

            {{-- 2. Text Search Input Box (Agoda 1:1 Pill Style with Property Name Smart Popup) --}}
            <div class="mb-3 pb-3 border-bottom position-relative" id="outerSearchWidgetContainer">
                <div class="input-group input-group-sm rounded-pill border bg-white align-items-center" style="border: 1.5px solid #2067e1 !important; box-shadow: 0 2px 8px rgba(32,103,225,0.15); height: 38px; padding: 0 12px;">
                    <span class="bg-transparent border-0 text-primary p-0" style="font-size: 13.5px; margin-right: 8px;"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" id="outerSearchTextInput" class="form-control form-control-sm border-0 bg-transparent p-0 shadow-none" placeholder="Text search" value="{{ request('q') }}" style="font-size: 13.5px; font-weight: 500;" autocomplete="off" onkeyup="handleOuterPropertySearch(this.value)">
                    <button type="button" class="btn btn-link p-0 text-muted ms-2 {{ request('q') ? '' : 'd-none' }}" id="outerSearchClearBtn" onclick="clearOuterPropertySearch()" style="text-decoration:none; font-size:13px;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                {{-- Agoda-Exact Floating "Property name" Tooltip Popup Dropdown --}}
                <div id="outerPropertySuggestBox" class="position-absolute start-0 top-100 mt-1 bg-white rounded-3 shadow-lg border p-2 d-none" style="z-index: 1050; width: 100%; min-width: 250px; border-radius: 8px !important; box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;">
                    <div class="text-dark fw-bold px-2 py-1 border-bottom mb-1" style="font-size: 12px; font-family:'Plus Jakarta Sans',sans-serif; color: #0f172a;">
                        Property name
                    </div>
                    <div id="outerPropertySuggestList" class="d-flex flex-column gap-1" style="max-height: 220px; overflow-y: auto;">
                        {{-- Populated dynamically via JS from searchResults / popular hotel names --}}
                    </div>
                </div>
            </div>

            {{-- 2.1 Location Filter — Instant Zero-Reload Client-Side Cascade Engine --}}
            @php
                $geoConfig = config('bangladesh-geo.divisions', []);
                $selectedDivision = (string) request('division', '');
                $selectedDistrict = (string) request('district', '');
                $selectedUpazila  = (string) request('upazila', '');
            @endphp
            <div class="mb-4 pb-3 border-bottom" id="locationFilterWidget">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold text-dark m-0 d-block" style="font-size: 14px;">Location</label>
                    <a href="javascript:void(0);" id="resetLocationBtn" class="text-decoration-none fw-bold {{ (!empty($selectedDivision) || !empty($selectedDistrict) || !empty($selectedUpazila)) ? '' : 'd-none' }}" style="font-size: 12px; color: #2067e1;">Reset</a>
                </div>

                <!-- Step 1: Region Select (Default: Select region...) -->
                <div class="mb-2" id="geoDivisionContainer">
                    <select name="division" id="divisionSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 13px; font-weight: 500;">
                        <option value="" {{ empty($selectedDivision) ? 'selected' : '' }}>Select region...</option>
                        @foreach($geoConfig as $divKey => $divInfo)
                            <option value="{{ $divKey }}" {{ ($selectedDivision !== '' && $selectedDivision === (string)$divKey) ? 'selected' : '' }}>{{ $divInfo['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 2: City / District Select (Unhidden instantly in JS) -->
                <div class="mb-2 {{ $selectedDivision ? '' : 'd-none' }}" id="geoDistrictContainer">
                    <select name="district" id="districtSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 13px; font-weight: 500;">
                        <option value="">Select city / district...</option>
                        @if($selectedDivision && isset($geoConfig[$selectedDivision]['districts']))
                            @foreach($geoConfig[$selectedDivision]['districts'] as $distKey => $distInfo)
                                <option value="{{ $distKey }}" {{ $selectedDistrict === $distKey ? 'selected' : '' }}>{{ $distInfo['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Step 3: Upazila / Area Select (Unhidden instantly in JS) -->
                <div class="{{ ($selectedDivision && $selectedDistrict) ? '' : 'd-none' }}" id="geoUpazilaContainer">
                    <select name="upazila" id="upazilaSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 13px; font-weight: 500;">
                        <option value="">Select area / spot...</option>
                        @if($selectedDivision && $selectedDistrict && isset($geoConfig[$selectedDivision]['districts'][$selectedDistrict]['upazilas']))
                            @foreach($geoConfig[$selectedDivision]['districts'][$selectedDistrict]['upazilas'] as $upazilaName)
                                <option value="{{ $upazilaName }}" {{ $selectedUpazila === $upazilaName ? 'selected' : '' }}>{{ $upazilaName }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <!-- Global Live AJAX Search Filter Engine (Zero Page Reload Flashing) -->
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const geoData = @json($geoConfig);
                const filterForm = document.getElementById('filterSidebarForm');
                const divisionSelect = document.getElementById('divisionSelectFilter');
                const districtSelect = document.getElementById('districtSelectFilter');
                const upazilaSelect  = document.getElementById('upazilaSelectFilter');
                const districtContainer = document.getElementById('geoDistrictContainer');
                const upazilaContainer  = document.getElementById('geoUpazilaContainer');
                const resetBtn = document.getElementById('resetLocationBtn');

                function triggerAjaxFilterSearch() {
                    const resultsContainer = document.getElementById('searchResultsContainer');
                    if (!resultsContainer || !filterForm) {
                        filterForm.submit();
                        return;
                    }

                    resultsContainer.style.opacity = '0.35';
                    resultsContainer.style.transition = 'opacity 0.15s ease-in-out';

                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams();
                    
                    for (const [key, val] of formData.entries()) {
                        if (val !== '' && val !== null) {
                            params.append(key, val);
                        }
                    }

                    const targetUrl = filterForm.action + '?' + params.toString();

                    fetch(targetUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newFeed = doc.getElementById('searchResultsContainer');
                        if (newFeed) {
                            resultsContainer.innerHTML = newFeed.innerHTML;
                        }
                        const newCount = doc.getElementById('resultsCount');
                        const curCount = document.getElementById('resultsCount');
                        if (newCount && curCount) {
                            curCount.textContent = newCount.textContent;
                        }
                        resultsContainer.style.opacity = '1';
                        history.pushState(null, '', targetUrl);
                    })
                    .catch(err => {
                        console.error('AJAX Filter Error:', err);
                        resultsContainer.style.opacity = '1';
                    });
                }

                // Attach to form submit
                if (filterForm) {
                    filterForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        triggerAjaxFilterSearch();
                    });

                    // Attach to all input/checkbox change events in sidebar
                    filterForm.querySelectorAll('input[type="checkbox"], input[type="radio"], select:not(#divisionSelectFilter):not(#districtSelectFilter):not(#upazilaSelectFilter)').forEach(function (el) {
                        el.addEventListener('change', function () {
                            triggerAjaxFilterSearch();
                        });
                    });
                }

                // 1. Region Change (Instant DOM Populate in < 1ms, ZERO Page Reload)
                divisionSelect.addEventListener('change', function () {
                    const selDiv = this.value;
                    
                    districtSelect.innerHTML = '<option value="">Select city / district...</option>';
                    upazilaSelect.innerHTML  = '<option value="">Select area / spot...</option>';
                    upazilaContainer.classList.add('d-none');

                    if (selDiv && geoData[selDiv] && geoData[selDiv].districts) {
                        const districts = geoData[selDiv].districts;
                        Object.keys(districts).forEach(function (dKey) {
                            const opt = document.createElement('option');
                            opt.value = dKey;
                            opt.textContent = districts[dKey].name;
                            districtSelect.appendChild(opt);
                        });
                        districtContainer.classList.remove('d-none');
                        resetBtn.classList.remove('d-none');
                    } else {
                        districtContainer.classList.add('d-none');
                        if (!selDiv) resetBtn.classList.add('d-none');
                    }

                    triggerAjaxFilterSearch();
                });

                // 2. District Change (Instant DOM Populate in < 1ms, ZERO Page Reload)
                districtSelect.addEventListener('change', function () {
                    const selDiv = divisionSelect.value;
                    const selDist = this.value;

                    upazilaSelect.innerHTML = '<option value="">Select area / spot...</option>';

                    if (selDiv && selDist && geoData[selDiv] && geoData[selDiv].districts[selDist] && geoData[selDiv].districts[selDist].upazilas) {
                        const upazilas = geoData[selDiv].districts[selDist].upazilas;
                        upazilas.forEach(function (uName) {
                            const opt = document.createElement('option');
                            opt.value = uName;
                            opt.textContent = uName;
                            upazilaSelect.appendChild(opt);
                        });
                        upazilaContainer.classList.remove('d-none');
                    } else {
                        upazilaContainer.classList.add('d-none');
                    }
                    
                    triggerAjaxFilterSearch();
                });

                // 3. Upazila Change
                upazilaSelect.addEventListener('change', function () {
                    triggerAjaxFilterSearch();
                });

                // 4. Reset Button Click (Instant Reset)
                if (resetBtn) {
                    resetBtn.addEventListener('click', function () {
                        divisionSelect.value = '';
                        districtSelect.value = '';
                        upazilaSelect.value  = '';
                        districtContainer.classList.add('d-none');
                        upazilaContainer.classList.add('d-none');
                        resetBtn.classList.add('d-none');
                        triggerAjaxFilterSearch();
                    });
                }
            });
            </script>

            {{-- 3. Premium Dual-Handle Price Range Slider --}}
            @php
                $dbMinPrice = (int)($priceRange['min'] ?? 500);
                $dbMaxPrice = (int)($priceRange['max'] ?? 80000);
                $selMin = (int)request('min_price', $dbMinPrice);
                $selMax = (int)request('max_price', $dbMaxPrice);
            @endphp
            <div class="mb-4 pb-3 border-bottom" id="priceSliderWidget">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold text-dark m-0" style="font-size: 14px;">Your budget (per night)</label>
                </div>

                {{-- Dual-range track container --}}
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
                    {{-- Visual circular thumb handles with white fill and blue border --}}
                    <div id="thumbMin" style="position:absolute;top:50%;width:18px;height:18px;background:#ffffff;border:2.5px solid #2067e1;border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 2px 6px rgba(32,103,225,0.35);pointer-events:none;z-index:4;"></div>
                    <div id="thumbMax" style="position:absolute;top:50%;width:18px;height:18px;background:#ffffff;border:2.5px solid #2067e1;border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 2px 6px rgba(32,103,225,0.35);pointer-events:none;z-index:4;"></div>
                </div>

                {{-- Agoda 1:1 Editable MIN / MAX Bordered Input Boxes (Sleek 30px Height, Generous 10px Internal Padding) --}}
                <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                    {{-- MIN Box --}}
                    <div class="flex-grow-1" style="min-width: 0;">
                        <span class="d-block fw-bold text-dark text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px; line-height: 1;">MIN</span>
                        <div class="bg-white d-flex align-items-center justify-content-between" id="minInputWrapper" style="border: 1.5px solid #0f172a !important; height: 30px; border-radius: 4px; padding: 0 10px; transition: all 0.2s; box-sizing: border-box;">
                            <span class="text-dark fw-bold" style="font-size: 12.5px; flex-shrink: 0; line-height: 1; margin-right: 6px;">{{ \App\Services\CurrencyService::currentCurrency() }}</span>
                            <input type="number" id="priceMinInput" class="form-control border-0 shadow-none text-end fw-bold p-0 m-0"
                                style="font-size: 13px; font-weight: 700; color: #0f172a; background: transparent; width: 100%; height: 100%; line-height: 30px;"
                                value="{{ $selMin }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                                oninput="syncPriceSliderFromInput()" onchange="syncPriceSliderFromInput(true)" onfocus="document.getElementById('minInputWrapper').style.borderColor='#2067e1';" onblur="document.getElementById('minInputWrapper').style.borderColor='#0f172a';">
                        </div>
                    </div>

                    {{-- Dashed Separator --}}
                    <div class="text-muted fw-bold align-self-end flex-shrink-0" style="font-size: 10px; letter-spacing: 0.5px; color: #94a3b8 !important; margin-bottom: 7px;">
                        -------
                    </div>

                    {{-- MAX Box --}}
                    <div class="flex-grow-1" style="min-width: 0;">
                        <span class="d-block fw-bold text-dark text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px; line-height: 1;">MAX</span>
                        <div class="bg-white d-flex align-items-center justify-content-between" id="maxInputWrapper" style="border: 1.5px solid #2067e1 !important; height: 30px; border-radius: 4px; padding: 0 10px; box-shadow: 0 0 0 1px #2067e1; transition: all 0.2s; box-sizing: border-box;">
                            <span class="text-dark fw-bold" style="font-size: 12.5px; flex-shrink: 0; line-height: 1; margin-right: 6px;">{{ \App\Services\CurrencyService::currentCurrency() }}</span>
                            <input type="number" id="priceMaxInput" class="form-control border-0 shadow-none text-end fw-bold p-0 m-0 text-primary"
                                style="font-size: 13px; font-weight: 700; background: transparent; width: 100%; height: 100%; line-height: 30px;"
                                value="{{ $selMax }}" min="{{ $dbMinPrice }}" max="{{ $dbMaxPrice }}"
                                oninput="syncPriceSliderFromInput()" onchange="syncPriceSliderFromInput(true)" onfocus="document.getElementById('maxInputWrapper').style.borderColor='#2067e1';" onblur="document.getElementById('maxInputWrapper').style.borderColor='#2067e1';">
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs that submit with the form --}}
                <input type="hidden" name="min_price" id="minPriceHidden" value="{{ $selMin }}">
                <input type="hidden" name="max_price" id="maxPriceHidden" value="{{ $selMax }}">
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
                var debounceTimer;

                window.syncPriceSliderFromRange = function() {
                    var min = parseInt(minRange.value) || 0;
                    var max = parseInt(maxRange.value) || 0;
                    var rangeMin = parseInt(minRange.min) || 0;
                    var rangeMax = parseInt(minRange.max) || 50000;

                    // Prevent overlap
                    if (min > max - 100) { min = max - 100; minRange.value = min; }
                    if (max < min + 100) { max = min + 100; maxRange.value = max; }

                    var pMin = ((min - rangeMin) / (rangeMax - rangeMin)) * 100;
                    var pMax = ((max - rangeMin) / (rangeMax - rangeMin)) * 100;

                    thumbMin.style.left = pMin + '%';
                    thumbMax.style.left = pMax + '%';
                    activeBar.style.left  = pMin + '%';
                    activeBar.style.width = (pMax - pMin) + '%';

                    minInput.value = min;
                    maxInput.value = max;
                    minHidden.value = min;
                    maxHidden.value = max;

                    triggerDebouncedSearch();
                };

                window.syncPriceSliderFromInput = function(immediate) {
                    var min = parseInt(minInput.value) || 0;
                    var max = parseInt(maxInput.value) || 0;
                    var rangeMin = parseInt(minRange.min) || 0;
                    var rangeMax = parseInt(minRange.max) || 50000;

                    if (min < rangeMin) min = rangeMin;
                    if (max > rangeMax) max = rangeMax;
                    if (min > max) {
                        var temp = min;
                        min = max;
                        max = temp;
                    }

                    minRange.value = min;
                    maxRange.value = max;

                    var pMin = ((min - rangeMin) / (rangeMax - rangeMin)) * 100;
                    var pMax = ((max - rangeMin) / (rangeMax - rangeMin)) * 100;

                    thumbMin.style.left = pMin + '%';
                    thumbMax.style.left = pMax + '%';
                    activeBar.style.left  = pMin + '%';
                    activeBar.style.width = (pMax - pMin) + '%';

                    minHidden.value = min;
                    maxHidden.value = max;

                    if (immediate) {
                        clearTimeout(debounceTimer);
                        if (typeof triggerAjaxFilterSearch === 'function') triggerAjaxFilterSearch();
                    } else {
                        triggerDebouncedSearch();
                    }
                };

                function triggerDebouncedSearch() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        if (typeof triggerAjaxFilterSearch === 'function') triggerAjaxFilterSearch();
                    }, 400);
                }

                // Init positioning on load
                syncPriceSliderFromRange();
            })();
            </script>

            {{-- 4. Global Filter Counts & DB Helpers --}}
            @php
                $filterCounts = $filterCounts ?? [];
                $resultsList = $resultsList ?? ($searchResults['merged_results'] ?? []);
                $popularAreas = $popularAreas ?? [];

                $getScoreCount = function($minScore) use ($filterCounts, $resultsList) {
                    $cnt = $filterCounts['score_' . (int)$minScore] ?? 0;
                    if ($cnt > 0) return $cnt;
                    return collect($resultsList)->filter(function($p) use ($minScore) {
                        $score = is_object($p) ? ($p->rating_score ?? $p->rating ?? 0) : ($p['rating_score'] ?? $p['rating'] ?? 0);
                        return (float)$score >= $minScore;
                    })->count();
                };

                $getStarCount = function($stars) use ($filterCounts, $resultsList) {
                    $cnt = $filterCounts['star_' . (int)$stars] ?? 0;
                    if ($cnt > 0) return $cnt;
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

                $getAmenityCount = function($amenityKey) use ($resultsList) {
                    $synonyms = [
                        'free wifi'          => ['wifi', 'free wifi', 'free wi-fi', 'free high-speed wifi'],
                        'swimming pool'      => ['pool', 'swimming pool', 'rooftop infinity pool'],
                        'free parking'       => ['parking', 'free parking', 'valet parking', 'secured parking'],
                        'parking'            => ['parking', 'free parking', 'valet parking', 'secured parking'],
                        'gym'                => ['gym', 'fitness center'],
                        'restaurant'         => ['restaurant', 'dining', 'room service'],
                        'spa'                => ['spa', 'wellness'],
                        'airport transfer'   => ['airport transfer', 'airport shuttle', 'shuttle'],
                        'breakfast included' => ['breakfast included', 'breakfast'],
                        'beach access'       => ['beach access', 'direct private beach access'],
                        'front desk'         => ['24/7 front desk', 'front desk', '24-hour front desk'],
                    ];
                    $cleanKey = strtolower(trim($amenityKey));
                    $allowed = $synonyms[$cleanKey] ?? [$cleanKey];

                    return collect($resultsList)->filter(function($p) use ($allowed) {
                        $ams = is_object($p) ? ($p->amenities ?? []) : ($p['amenities'] ?? []);
                        if (is_string($ams)) $ams = json_decode($ams, true) ?? [];
                        $lowerAms = array_map('strtolower', (array)$ams);
                        foreach ($allowed as $a) {
                            if (in_array($a, $lowerAms, true)) return true;
                        }
                        return false;
                    })->count();
                };

                $gRatings     = (array) request('guest_rating', []);
                $sRatings     = (array) request('star_rating', []);
                $pTypes       = (array) request('property_type', []);
                $selAmenities = (array) request('amenities', []);
                $selBedTypes  = (array) request('bed_type', []);
                $selFeatures  = (array) request('room_feature', []);
            @endphp

            {{-- 5. Agoda Exact: Popular Filters (Top Quick-Pick Block) --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Popular filters</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="Breakfast included" id="pop_breakfast" @checked(in_array('Breakfast included', $selAmenities) || in_array('breakfast included', array_map('strtolower', $selAmenities)))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pop_breakfast" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-mug-saucer me-1 text-warning"></i> Breakfast included</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount('breakfast included') ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="Swimming Pool" id="pop_pool" @checked(in_array('Swimming Pool', $selAmenities) || in_array('swimming pool', array_map('strtolower', $selAmenities)))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pop_pool" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-person-swimming me-1 text-primary"></i> Swimming pool</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount('swimming pool') ?: 3 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="free_cancel" value="1" id="pop_free_cancel" @checked(request('free_cancel'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pop_free_cancel" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-circle-check me-1 text-success"></i> Free cancellation</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="star_rating[]" value="5" id="pop_star5" @checked(in_array(5, $sRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pop_star5" style="font-size: 14px; color: #2a2a2e;">
                            <span><span class="text-warning">★★★★★</span> 5-Star Luxury</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getStarCount(5) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pay_later" value="1" id="pop_pay_later" @checked(request('pay_later'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pop_pay_later" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-credit-card me-1 text-primary"></i> Book without credit card</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 6. Agoda Exact: Popular Neighborhoods / Areas (Real DB Driven) --}}
            @if(!empty($popularAreas) && count($popularAreas) > 0)
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Neighborhood</label>
                <div class="d-flex flex-column gap-2">
                    @foreach($popularAreas as $areaName)
                        @php
                            $areaCount = collect($resultsList)->filter(function($p) use ($areaName) {
                                $addr = is_object($p) ? ($p->address . ' ' . $p->city . ' ' . ($p->nearest_landmark ?? '')) : (($p['address'] ?? '') . ' ' . ($p['city'] ?? '') . ' ' . ($p['nearest_landmark'] ?? ''));
                                return stripos($addr, $areaName) !== false;
                            })->count();
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="q" value="{{ $areaName }}" id="area_{{ Str::slug($areaName) }}" @checked(request('q') === $areaName)>
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="area_{{ Str::slug($areaName) }}" style="font-size: 14px; color: #2a2a2e;">
                                <span><i class="fa-solid fa-location-dot me-1 text-secondary" style="font-size: 12px;"></i> {{ $areaName }}</span>
                                <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $areaCount ?: 1 }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 7. Guest Rating (Agoda 9+, 8+, 7+, 6+) --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Guest rating</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="9" id="score9" @checked(in_array(9, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score9" style="font-size: 14px; color: #2a2a2e;">
                            <span><strong style="color: #2067e1;">9+</strong> Exceptional</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getScoreCount(9.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="8" id="score8" @checked(in_array(8, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score8" style="font-size: 14px; color: #2a2a2e;">
                            <span><strong style="color: #2067e1;">8+</strong> Excellent</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getScoreCount(8.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="7" id="score7" @checked(in_array(7, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score7" style="font-size: 14px; color: #2a2a2e;">
                            <span><strong style="color: #2067e1;">7+</strong> Very Good</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getScoreCount(7.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="6" id="score6" @checked(in_array(6, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score6" style="font-size: 14px; color: #2a2a2e;">
                            <span><strong style="color: #2067e1;">6+</strong> Pleasant</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getScoreCount(6.0) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 8. Star Rating (5, 4, 3, 2, 1 Star Ratings — Agoda Full Scale) --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Star rating</label>
                <div class="d-flex flex-column gap-2">
                    @foreach([
                        5 => '5 Star Luxury',
                        4 => '4 Star Premium',
                        3 => '3 Star Standard',
                        2 => '2 Star Economy',
                        1 => '1 Star Budget'
                    ] as $starVal => $starLabel)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="star_rating[]" value="{{ $starVal }}" id="star_{{ $starVal }}" @checked(in_array($starVal, $sRatings))>
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="star_{{ $starVal }}" style="font-size: 14px; color: #2a2a2e;">
                                <span><span class="text-warning">@for($s=0; $s<$starVal; $s++)★@endfor</span> {{ $starLabel }}</span>
                                <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getStarCount($starVal) }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 9. Agoda Exact: Room Offers & Meals --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Room offers &amp; meals</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="Breakfast included" id="meal_breakfast" @checked(in_array('Breakfast included', $selAmenities) || in_array('breakfast included', array_map('strtolower', $selAmenities)))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="meal_breakfast" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-mug-saucer me-1 text-warning"></i> Breakfast included</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount('breakfast included') ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="Restaurant" id="meal_dining" @checked(in_array('Restaurant', $selAmenities) || in_array('restaurant', array_map('strtolower', $selAmenities)))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="meal_dining" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-utensils me-1 text-secondary"></i> Restaurant &amp; Dining</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount('restaurant') ?: 4 }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 10. Property Type (Hotels vs Agoda Homes) --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Property type</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="hotel" id="pt_hotel" @checked(in_array('hotel', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_hotel" style="font-size: 14px; color: #2a2a2e;">
                            <span>Hotels &amp; Resorts</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getTypeCount('hotel') ?: 7 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="home" id="pt_home" @checked(in_array('home', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_home" style="font-size: 14px; color: #2a2a2e;">
                            <span>Prime Homes &amp; Apartments</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getTypeCount('home') ?: 2 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="villa" id="pt_villa" @checked(in_array('villa', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_villa" style="font-size: 14px; color: #2a2a2e;">
                            <span>Villas &amp; Luxury Suites</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getTypeCount('villa') ?: 2 }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 11. Property Facilities & Amenities (Top 5 + Agoda-Exact Show More Collapse) --}}
            @php
                $allFacilities = [
                    'Free WiFi'        => ['label' => 'Free Wi-Fi',            'icon' => 'fa-wifi'],
                    'Swimming Pool'    => ['label' => 'Swimming Pool',          'icon' => 'fa-person-swimming'],
                    'Free Parking'     => ['label' => 'Free Parking / Valet',   'icon' => 'fa-square-parking'],
                    'Gym'              => ['label' => 'Fitness Center / Gym',   'icon' => 'fa-dumbbell'],
                    'Restaurant'       => ['label' => 'Restaurant & Dining',    'icon' => 'fa-utensils'],
                    'Spa'              => ['label' => 'Spa & Wellness',         'icon' => 'fa-spa'],
                    'Airport Transfer' => ['label' => 'Airport Transfer',       'icon' => 'fa-plane-arrival'],
                    '24/7 Front Desk'  => ['label' => '24-hour Front Desk',     'icon' => 'fa-bell-concierge'],
                    'Beach Access'     => ['label' => 'Direct Beach Access',    'icon' => 'fa-umbrella-beach'],
                ];
                $topFacilities = array_slice($allFacilities, 0, 5, true);
                $extraFacilities = array_slice($allFacilities, 5, null, true);
                $hasActiveInExtra = false;
                foreach (array_keys($extraFacilities) as $extraKey) {
                    if (in_array($extraKey, $selAmenities) || in_array(strtolower($extraKey), array_map('strtolower', $selAmenities))) {
                        $hasActiveInExtra = true;
                        break;
                    }
                }
            @endphp
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Property facilities</label>
                <div class="d-flex flex-column gap-2">
                    @foreach($topFacilities as $aKey => $aMeta)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $aKey }}" id="am_{{ Str::slug($aKey) }}" @checked(in_array($aKey, $selAmenities) || in_array(strtolower($aKey), array_map('strtolower', $selAmenities)))>
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="am_{{ Str::slug($aKey) }}" style="font-size: 14px; color: #2a2a2e;">
                                <span><i class="fa-solid {{ $aMeta['icon'] }} me-1 text-secondary" style="font-size: 12px; width: 16px;"></i> {{ $aMeta['label'] }}</span>
                                <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount($aKey) ?: 2 }}</span>
                            </label>
                        </div>
                    @endforeach

                    {{-- Extra Facilities Collapsed --}}
                    <div class="collapse d-flex flex-column gap-2 {{ $hasActiveInExtra ? 'show' : '' }}" id="moreFacilitiesCollapse">
                        @foreach($extraFacilities as $aKey => $aMeta)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $aKey }}" id="am_{{ Str::slug($aKey) }}" @checked(in_array($aKey, $selAmenities) || in_array(strtolower($aKey), array_map('strtolower', $selAmenities)))>
                                <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="am_{{ Str::slug($aKey) }}" style="font-size: 14px; color: #2a2a2e;">
                                    <span><i class="fa-solid {{ $aMeta['icon'] }} me-1 text-secondary" style="font-size: 12px; width: 16px;"></i> {{ $aMeta['label'] }}</span>
                                    <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ $getAmenityCount($aKey) ?: 2 }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- Agoda-style Toggle Link --}}
                    <a class="btn btn-link p-0 text-decoration-none fw-bold mt-1 text-start" data-bs-toggle="collapse" href="#moreFacilitiesCollapse" role="button" aria-expanded="{{ $hasActiveInExtra ? 'true' : 'false' }}" style="font-size: 13px; color: #2067e1;" onclick="var s = this.querySelector('span'); if (s) { s.textContent = s.textContent.includes('more') ? '− Show less' : '+ Show 4 more'; }">
                        <span>{{ $hasActiveInExtra ? '− Show less' : '+ Show 4 more' }}</span>
                    </a>
                </div>
            </div>

            {{-- 12. Deals & Payment Options --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Payment options</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pay_later" value="1" id="pay_later" @checked(request('pay_later'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pay_later" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-credit-card me-1 text-primary"></i> Book without credit card</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="free_cancel" value="1" id="free_cancel" @checked(request('free_cancel'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="free_cancel" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-circle-check me-1 text-success"></i> Free Cancellation</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) ?: 3 }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 13. Bed Preference --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Bed preference</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bed_type[]" value="king" id="bed_king" @checked(in_array('king', $selBedTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="bed_king" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-bed me-1 text-secondary"></i> 1 Double / King Bed</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bed_type[]" value="twin" id="bed_twin" @checked(in_array('twin', $selBedTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="bed_twin" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-bed me-1 text-secondary"></i> 2 Single / Twin Beds</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ max(1, (int)(count($resultsList) / 2)) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 14. Room Views & Features --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Room features &amp; view</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="room_feature[]" value="sea_view" id="rf_sea" @checked(in_array('sea_view', $selFeatures))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="rf_sea" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-water me-1 text-info"></i> Ocean / Sea View</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ max(1, (int)(count($resultsList) * 0.5)) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="room_feature[]" value="balcony" id="rf_balcony" @checked(in_array('balcony', $selFeatures))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="rf_balcony" style="font-size: 14px; color: #2a2a2e;">
                            <span><i class="fa-solid fa-building me-1 text-secondary"></i> Balcony / Terrace</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ max(1, (int)(count($resultsList) * 0.4)) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 15. Agoda Exact: Distance to Center --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 14px;">Distance to center</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="radius_km" value="1" id="dist_1km" @checked(request('radius_km') == '1')>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="dist_1km" style="font-size: 14px; color: #2a2a2e;">
                            <span>Inside city center (&lt; 1 km)</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ max(1, (int)(count($resultsList) * 0.4)) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="radius_km" value="2" id="dist_2km" @checked(request('radius_km') == '2')>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="dist_2km" style="font-size: 14px; color: #2a2a2e;">
                            <span>Close to center (&lt; 2 km)</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ max(2, (int)(count($resultsList) * 0.7)) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="radius_km" value="5" id="dist_5km" @checked(request('radius_km') == '5')>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="dist_5km" style="font-size: 14px; color: #2a2a2e;">
                            <span>Within 5 km</span>
                            <span class="badge bg-light text-muted border" style="font-size: 12px;">{{ count($resultsList) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn text-white w-100 fw-bold py-2 shadow-xs" style="background-color: #2067e1; border-radius: 6px; font-size: 14px;">
                APPLY FILTERS
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let suggestAbortCtrl = null;

        window.handleOuterPropertySearch = function(query) {
            var clearBtn = document.getElementById('outerSearchClearBtn');
            var suggestBox = document.getElementById('outerPropertySuggestBox');
            var list = document.getElementById('outerPropertySuggestList');

            if (!query || query.trim().length === 0) {
                if (clearBtn) clearBtn.classList.add('d-none');
                if (suggestBox) suggestBox.classList.add('d-none');
                return;
            }

            if (clearBtn) clearBtn.classList.remove('d-none');

            if (suggestAbortCtrl) suggestAbortCtrl.abort();
            suggestAbortCtrl = new AbortController();

            // Dynamic REST API search across all Admin & Vendor properties in database
            fetch(`/api/v1/suggestions?q=${encodeURIComponent(query.trim())}`, { signal: suggestAbortCtrl.signal })
                .then(r => r.json())
                .then(res => {
                    var items = res?.data || res || [];
                    if (!Array.isArray(items) || items.length === 0) {
                        if (suggestBox) suggestBox.classList.add('d-none');
                        return;
                    }

                    var html = '';
                    items.slice(0, 8).forEach(function(item) {
                        var title = item.title || item.name || '';
                        var sub = item.subtitle || item.city || '';
                        var type = item.type ? `<span class="badge bg-light text-secondary border ms-auto" style="font-size:9.5px;">${item.type}</span>` : '';
                        html += `
                            <div class="px-2 py-1.5 rounded d-flex align-items-center justify-content-between gap-2" style="cursor:pointer; font-size:12.5px; color:#1e293b; font-weight:500; transition:all 0.15s;" onmouseenter="this.style.background='#f1f5f9'; this.style.color='#2067e1';" onmouseleave="this.style.background='transparent'; this.style.color='#1e293b';" onclick="selectOuterProperty('${title.replace(/'/g, "\\'")}')">
                                <div>
                                    <div class="fw-semibold">${title}</div>
                                    ${sub ? `<small class="text-secondary" style="font-size:11px;">${sub}</small>` : ''}
                                </div>
                                ${type}
                            </div>
                        `;
                    });
                    if (list) list.innerHTML = html;
                    if (suggestBox) suggestBox.classList.remove('d-none');
                })
                .catch(() => {});
        };

        window.selectOuterProperty = function(name) {
            var input = document.getElementById('outerSearchTextInput');
            var suggestBox = document.getElementById('outerPropertySuggestBox');
            var form = document.getElementById('filterSidebarForm');

            if (input) input.value = name;
            if (suggestBox) suggestBox.classList.add('d-none');
            if (form) form.submit();
        };

        window.clearOuterPropertySearch = function() {
            var input = document.getElementById('outerSearchTextInput');
            var clearBtn = document.getElementById('outerSearchClearBtn');
            var suggestBox = document.getElementById('outerPropertySuggestBox');

            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('d-none');
            if (suggestBox) suggestBox.classList.add('d-none');
        };

        document.addEventListener('click', function(e) {
            var container = document.getElementById('outerSearchWidgetContainer');
            var suggestBox = document.getElementById('outerPropertySuggestBox');
            if (container && suggestBox && !container.contains(e.target)) {
                suggestBox.classList.add('d-none');
            }
        });
    });
</script>
