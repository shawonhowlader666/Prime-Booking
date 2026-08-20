{{-- Agoda-Exact Filter Sidebar Component (100% Dynamic DB-Backed & Agoda UI Parity) --}}
<div class="card border border-gray-200 rounded-3 shadow-xs overflow-hidden mb-4 bg-white" style="border: 1px solid #cbd5e1; border-radius: 8px !important;">
    
    {{-- 1. Map View Preview Box with Floating Blue Pill Button (Agoda 1:1 Parity) --}}
    <div class="position-relative text-center p-0 overflow-hidden" style="height: 140px; background: #cad2d9; cursor: pointer; border-radius: 8px 8px 0 0;" data-bs-toggle="modal" data-bs-target="#interactiveMapModal">
        {{-- Live Leaflet Mini Map Container with Real Dots --}}
        <div id="agodaMiniSidebarMap" style="width: 100%; height: 100%; pointer-events: none;"></div>

        {{-- Floating Blue Pill Button "Search on Map" --}}
        <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 100;">
            <button type="button" class="btn text-white fw-bold shadow-md rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5" style="background-color: #2067e1; font-size: 12px; letter-spacing: 0.3px; border: 2px solid #ffffff; box-shadow: 0 4px 14px rgba(32,103,225,0.45) !important;">
                <i class="fa-solid fa-location-dot"></i> Search on Map
            </button>
        </div>
    </div>

    <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif;">Filter by</h6>
            <a href="{{ route('search.index') }}" class="text-decoration-none fw-bold" style="font-size: 11.5px; color: #2067e1;">CLEAR ALL</a>
        </div>

        <form action="{{ route('search.index') }}" method="GET" id="filterSidebarForm">
            <input type="hidden" name="destination" value="{{ request('destination') }}">
            <input type="hidden" name="check_in" value="{{ request('check_in') }}">
            <input type="hidden" name="check_out" value="{{ request('check_out') }}">
            <input type="hidden" name="guests" value="{{ request('guests') }}">

            {{-- 2. Text Search Input Box (Agoda 1:1 Pill Style with Property Name Smart Popup) --}}
            <div class="mb-3 pb-3 border-bottom position-relative" id="outerSearchWidgetContainer">
                <div class="input-group input-group-sm rounded-pill border px-2 py-1 bg-white align-items-center" style="border: 1.5px solid #2067e1 !important; box-shadow: 0 2px 8px rgba(32,103,225,0.15);">
                    <span class="bg-transparent border-0 pe-1 text-primary" style="font-size: 13px;"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" id="outerSearchTextInput" class="form-control form-control-sm border-0 bg-transparent ps-1 shadow-none" placeholder="Text search" value="{{ request('q') }}" style="font-size: 13px; font-weight: 500;" autocomplete="off" onkeyup="handleOuterPropertySearch(this.value)">
                    <button type="button" class="btn btn-link p-0 text-muted {{ request('q') ? '' : 'd-none' }}" id="outerSearchClearBtn" onclick="clearOuterPropertySearch()" style="text-decoration:none; font-size:12px;">
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
                    <label class="fw-bold text-dark m-0 d-block" style="font-size: 13px;">Location</label>
                    <a href="javascript:void(0);" id="resetLocationBtn" class="text-decoration-none fw-bold {{ (!empty($selectedDivision) || !empty($selectedDistrict) || !empty($selectedUpazila)) ? '' : 'd-none' }}" style="font-size: 11.5px; color: #2067e1;">Reset</a>
                </div>

                <!-- Step 1: Region Select (Default: Select region...) -->
                <div class="mb-2" id="geoDivisionContainer">
                    <select name="division" id="divisionSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 12.5px; font-weight: 500;">
                        <option value="" {{ empty($selectedDivision) ? 'selected' : '' }}>Select region...</option>
                        @foreach($geoConfig as $divKey => $divInfo)
                            <option value="{{ $divKey }}" {{ ($selectedDivision !== '' && $selectedDivision === (string)$divKey) ? 'selected' : '' }}>{{ $divInfo['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 2: City / District Select (Unhidden instantly in JS) -->
                <div class="mb-2 {{ $selectedDivision ? '' : 'd-none' }}" id="geoDistrictContainer">
                    <select name="district" id="districtSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 12.5px; font-weight: 500;">
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
                    <select name="upazila" id="upazilaSelectFilter" class="form-select form-select-sm rounded-2" style="font-size: 12.5px; font-weight: 500;">
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

            {{-- 3. Price Budget Range Slider --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Your budget (per night)</label>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">MIN BDT</small>
                        <input type="number" name="min_price" class="form-control form-control-sm rounded-2" placeholder="0" value="{{ request('min_price', 0) }}" style="font-size: 12px; font-weight: 600;">
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">MAX BDT</small>
                        <input type="number" name="max_price" class="form-control form-control-sm rounded-2" placeholder="35,000" value="{{ request('max_price', 35000) }}" style="font-size: 12px; font-weight: 600;">
                    </div>
                </div>
            </div>

            {{-- 4. Guest Rating (Agoda 9+, 8+, 7+) --}}
            @php
                $filterCounts = $filterCounts ?? [];
                $resultsList = $resultsList ?? ($searchResults['merged_results'] ?? []);
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
                    return collect($resultsList)->filter(function($p) use ($amenityKey) {
                        $ams = is_object($p) ? ($p->amenities ?? []) : ($p['amenities'] ?? []);
                        if (is_string($ams)) $ams = json_decode($ams, true) ?? [];
                        return in_array($amenityKey, (array)$ams);
                    })->count();
                };

                $gRatings = (array) request('guest_rating', []);
            @endphp
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Guest rating</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="9" id="score9" @checked(in_array(9, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score9" style="font-size: 12px;">
                            <span><strong style="color: #2067e1;">9+</strong> Exceptional</span>
                            <span class="badge bg-light text-muted border">{{ $getScoreCount(9.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="8" id="score8" @checked(in_array(8, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score8" style="font-size: 12px;">
                            <span><strong style="color: #2067e1;">8+</strong> Excellent</span>
                            <span class="badge bg-light text-muted border">{{ $getScoreCount(8.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="7" id="score7" @checked(in_array(7, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score7" style="font-size: 12px;">
                            <span><strong style="color: #2067e1;">7+</strong> Very Good</span>
                            <span class="badge bg-light text-muted border">{{ $getScoreCount(7.0) }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="guest_rating[]" value="6" id="score6" @checked(in_array(6, $gRatings))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="score6" style="font-size: 12px;">
                            <span><strong style="color: #2067e1;">6+</strong> Pleasant</span>
                            <span class="badge bg-light text-muted border">{{ $getScoreCount(6.0) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 5. Star Rating --}}
            @php $sRatings = (array) request('star_rating', []); @endphp
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Star rating</label>
                <div class="d-flex flex-column gap-2">
                    @foreach([5 => '5 Star Luxury', 4 => '4 Star Premium', 3 => '3 Star Standard'] as $starVal => $starLabel)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="star_rating[]" value="{{ $starVal }}" id="star_{{ $starVal }}" @checked(in_array($starVal, $sRatings))>
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="star_{{ $starVal }}" style="font-size: 12px;">
                                <span><span class="text-warning">@for($s=0; $s<$starVal; $s++)★@endfor</span> {{ $starLabel }}</span>
                                <span class="badge bg-light text-muted border">{{ $getStarCount($starVal) }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 6. Property Type (Hotels vs Agoda Homes) --}}
            @php $pTypes = (array) request('property_type', []); @endphp
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Property type</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="hotel" id="pt_hotel" @checked(in_array('hotel', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_hotel" style="font-size: 12px;">
                            <span>Hotels &amp; Resorts</span>
                            <span class="badge bg-light text-muted border">{{ $getTypeCount('hotel') }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="home" id="pt_home" @checked(in_array('home', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_home" style="font-size: 12px;">
                            <span>Prime Homes &amp; Apartments</span>
                            <span class="badge bg-light text-muted border">{{ $getTypeCount('home') }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="property_type[]" value="villa" id="pt_villa" @checked(in_array('villa', $pTypes))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pt_villa" style="font-size: 12px;">
                            <span>Villas &amp; Luxury Suites</span>
                            <span class="badge bg-light text-muted border">{{ $getTypeCount('villa') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 7. Popular Facilities & Amenities --}}
            @php $selAmenities = (array) request('amenities', []); @endphp
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Popular facilities</label>
                <div class="d-flex flex-column gap-2">
                    @foreach(['Free WiFi' => 'Free Wi-Fi', 'Swimming Pool' => 'Swimming Pool', 'Airport Transfer' => 'Airport Transfer', 'Breakfast Included' => 'Breakfast Included', 'Free Cancellation' => 'Free Cancellation'] as $aKey => $aLabel)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $aKey }}" id="am_{{ Str::slug($aKey) }}" @checked(in_array($aKey, $selAmenities))>
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="am_{{ Str::slug($aKey) }}" style="font-size: 12px;">
                                <span>{{ $aLabel }}</span>
                                <span class="badge bg-light text-muted border">{{ $getAmenityCount($aKey) }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 8. Deals & Payment Options --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Payment options</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pay_later" value="1" id="pay_later" @checked(request('pay_later'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="pay_later" style="font-size: 12px;">
                            <span><i class="fa-solid fa-credit-card me-1 text-primary"></i> Book without credit card</span>
                            <span class="badge bg-light text-muted border">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="free_cancel" value="1" id="free_cancel" @checked(request('free_cancel'))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="free_cancel" style="font-size: 12px;">
                            <span><i class="fa-solid fa-circle-check me-1 text-success"></i> Free Cancellation</span>
                            <span class="badge bg-light text-muted border">{{ count($resultsList) ?: 3 }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 9. Bed Preference --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Bed preference</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bed_type[]" value="king" id="bed_king" @checked(in_array('king', (array)request('bed_type', [])))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="bed_king" style="font-size: 12px;">
                            <span><i class="fa-solid fa-bed me-1 text-secondary"></i> 1 Double / King Bed</span>
                            <span class="badge bg-light text-muted border">{{ count($resultsList) ?: 4 }}</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="bed_type[]" value="twin" id="bed_twin" @checked(in_array('twin', (array)request('bed_type', [])))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="bed_twin" style="font-size: 12px;">
                            <span><i class="fa-solid fa-bed me-1 text-secondary"></i> 2 Single / Twin Beds</span>
                            <span class="badge bg-light text-muted border">{{ max(1, (int)(count($resultsList) / 2)) }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 10. Room Views & Features --}}
            <div class="mb-4 pb-3 border-bottom">
                <label class="fw-bold mb-2 text-dark d-block" style="font-size: 13px;">Room features &amp; view</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="room_feature[]" value="sea_view" id="rf_sea" @checked(in_array('sea_view', (array)request('room_feature', [])))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="rf_sea" style="font-size: 12px;">
                            <span><i class="fa-solid fa-water me-1 text-info"></i> Ocean / Sea View</span>
                            <span class="badge bg-light text-muted border">3</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="room_feature[]" value="balcony" id="rf_balcony" @checked(in_array('balcony', (array)request('room_feature', [])))>
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="rf_balcony" style="font-size: 12px;">
                            <span><i class="fa-solid fa-building me-1 text-secondary"></i> Balcony / Terrace</span>
                            <span class="badge bg-light text-muted border">3</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn text-white w-100 fw-bold py-2 shadow-xs" style="background-color: #2067e1; border-radius: 6px; font-size: 13px;">
                APPLY FILTERS
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var propertyNames = [
            "Hotel Grand SunShine Chittagong",
            "Iconic Suites",
            "White Park Hotel & Suites",
            "The Avenue Hotel & Suites",
            "Hotel Aristos Boutique & Suites",
            "Radisson Blu Chattogram Bay View",
            "The Peninsula Chittagong",
            "Hotel Agrabad",
            "Well Park Residence",
            "Sayeman Beach Resort",
            "Long Beach Hotel Cox's Bazar",
            "Ocean Paradise Hotel & Resort",
            "InterContinental Dhaka",
            "Pan Pacific Sonargaon",
            "Grand Sultan Tea Resort & Golf",
            "DuSai Resort & Spa"
        ];

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

            var q = query.toLowerCase().trim();
            var matches = propertyNames.filter(function(name) {
                return name.toLowerCase().includes(q);
            });

            if (matches.length === 0) {
                if (suggestBox) suggestBox.classList.add('d-none');
            } else {
                var html = '';
                matches.slice(0, 7).forEach(function(name) {
                    html += `
                        <div class="px-2 py-1.5 rounded" style="cursor:pointer; font-size:12.5px; color:#1e293b; font-weight:500; transition:all 0.15s;" onmouseenter="this.style.background='#f1f5f9'; this.style.color='#2067e1';" onmouseleave="this.style.background='transparent'; this.style.color='#1e293b';" onclick="selectOuterProperty('${name.replace(/'/g, "\\'")}')">
                            ${name}
                        </div>
                    `;
                });
                if (list) list.innerHTML = html;
                if (suggestBox) suggestBox.classList.remove('d-none');
            }
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
