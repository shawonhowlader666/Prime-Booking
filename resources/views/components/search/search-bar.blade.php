<!-- Agoda.com 100% Exact Matching Search Engine Widget with Lowered Floating Capsule Tab Pod -->
<style>
    /* Slim Compact Agoda Tab Buttons */
    .agoda-tab-btn {
        background: transparent;
        border: none;
        outline: none;
        padding: 8px 18px;
        font-weight: 600;
        font-size: 14.5px;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
        flex-shrink: 0;
        text-decoration: none;
    }
    .agoda-tab-btn.active {
        color: #2067e1;
        font-weight: 700;
        border-bottom-color: #2067e1;
    }
    .agoda-tab-btn:hover:not(.active) {
        color: #1e293b;
        background-color: #f8fafc;
        border-radius: 10px;
    }

    /* Floating Capsule Tab Container (Centered) */
    .agoda-tabs-wrapper {
        position: relative;
        top: 14px;
        margin: 0 auto -14px auto;
        z-index: 5;
        display: flex;
        justify-content: center;
        width: 100%;
    }
    .agoda-tabs-container {
        background-color: #ffffff;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        border: 1px solid #d5d9e0;
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .agoda-tabs-container::-webkit-scrollbar {
        display: none;
    }

    /* Main Search Card — Pure Clean White (100% Agoda Matching) */
    .agoda-search-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px 36px 44px 36px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0;
        position: relative;
        z-index: 1;
        text-align: left !important;
    }

    /* Input Buttons (Height: 58px) — Pure Crisp White (btn ektu sada) */
    .agoda-input-btn {
        width: 100%;
        background-color: #ffffff !important;
        border: 1px solid #d5d9e0;
        border-radius: 8px;
        height: 58px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        outline: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .agoda-input-btn:hover {
        border-color: #2067e1;
        box-shadow: 0 2px 8px rgba(32, 103, 225, 0.12);
    }
    .agoda-input-btn.active-border {
        border: 2px solid #2067e1;
        box-shadow: 0 2px 10px rgba(32, 103, 225, 0.16);
    }

    .agoda-nights-pill {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
    }
    @media (max-width: 768px) {
        .agoda-nights-pill {
            display: none !important;
        }
    }

    .agoda-recent-pill {
        background: #f0f5fc;
        border-radius: 10px;
        padding: 12px 14px;
        flex: 1;
        cursor: pointer;
        transition: background 0.15s ease;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }
    .agoda-recent-pill:hover {
        background-color: #e2eeff;
    }

    .agoda-popover-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .agoda-popover-item:hover {
        background-color: #f0f7ff;
    }

    .agoda-stepper-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #2067e1;
        background: #ffffff;
        color: #2067e1;
        font-weight: 700;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        outline: none;
    }
    .agoda-stepper-btn:hover:not(:disabled) {
        background-color: #f0f7ff;
        border-color: #1a56be;
        transform: scale(1.05);
    }
    .agoda-stepper-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        border-color: #cbd5e1;
        color: #94a3b8;
    }

    /* Agoda Blue SEARCH Button */
    .agoda-search-submit-btn {
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #2067e1;
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.8px;
        border-radius: 22px;
        height: 44px;
        width: 380px;
        max-width: 90%; 
        border: none;
        box-shadow: 0 6px 18px rgba(32, 103, 225, 0.4);
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
    }
    .agoda-search-submit-btn:hover {
        background-color: #1a56be;
        box-shadow: 0 8px 22px rgba(32, 103, 225, 0.55);
        transform: translateX(-50%) translateY(-2px);
    }

    /* Mobile Responsive Optimizations (< 768px) */
    @media (max-width: 768px) {
        .agoda-tabs-wrapper {
            margin-left: 0 !important;
            top: 8px !important;
            margin-bottom: -8px !important;
            width: 100% !important;
            display: flex !important;
            justify-content: flex-start !important;
            padding: 0 4px !important;
        }
        .agoda-tabs-container {
            width: 100% !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            border-radius: 12px !important;
            padding: 4px 6px !important;
            gap: 4px !important;
        }
        .agoda-tab-btn {
            padding: 6px 10px !important;
            font-size: 12px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .agoda-search-card {
            padding: 16px 12px 38px 12px !important;
            border-radius: 14px !important;
        }
        .agoda-input-btn {
            height: 52px !important;
            padding: 8px 12px !important;
            gap: 8px !important;
        }
        .agoda-search-submit-btn {
            width: calc(100% - 24px) !important;
            max-width: 100% !important;
            font-size: 14px !important;
            height: 44px !important;
            bottom: -22px !important;
        }
        #agodaDestinationPopoverCard, #agodaCalendarPopoverCard, #agodaGuestsPopoverCard {
            left: 0 !important;
            width: 100% !important;
            max-width: calc(100vw - 20px) !important;
        }
    }
</style>

<!-- Click Outside Dismiss Overlay (Zero Blur, 100% Sharp & Clean) -->
<div id="agodaSearchBackdropOverlay" style="display: none; position: fixed; inset: 0; background: transparent; z-index: 9; cursor: default;"></div>

<div id="agodaSearchBarWrapper" style="max-width: 1140px; margin: 0 auto 38px auto; position: relative; z-index: 1000; text-align: left !important;">

    <!-- Top White Floating Capsule Tab Pod Container -->
    <div class="agoda-tabs-wrapper">
        <div class="agoda-tabs-container">
            @php
                $activeTabType = strtolower((string)request('search_type', request('type', 'hotel')));
            @endphp
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['hotel', 'all', '']) ? 'active' : '' }}" id="tabHotels">
                <i class="fa-solid fa-building text-primary" style="font-size: 13px;"></i> {{ __('Hotels') }}
            </button>
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['homes', 'home', 'apartment', 'villa']) ? 'active' : '' }}" id="tabHomes">
                <i class="fa-solid fa-house-chimney text-primary" style="font-size: 13px;"></i> {{ __('Homes & Apts') }}
            </button>
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['sundarban', 'sundarbans', 'sundarban_ship', 'sundarbans_ship', 'ship']) ? 'active' : '' }}" id="tabSundarbanShip">
                <i class="fa-solid fa-ship text-primary" style="font-size: 13px;"></i> {{ __('Sundarbans Ship') }}
            </button>
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['tanguar', 'tanguar_haor', 'tanguar_houseboat', 'houseboat']) ? 'active' : '' }}" id="tabTanguarHouseboat">
                <i class="fa-solid fa-sailboat text-primary" style="font-size: 13px;"></i> {{ __('Tanguar Haor Houseboat') }}
            </button>
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['homestay', 'homestays', 'home_stay']) ? 'active' : '' }}" id="tabHomeStay">
                <i class="fa-solid fa-house-user text-primary" style="font-size: 13px;"></i> {{ __('Home Stay') }}
            </button>
            <button type="button" class="agoda-tab-btn {{ in_array($activeTabType, ['airport', 'transfer']) ? 'active' : '' }}" id="tabAirport">
                <i class="fa-solid fa-car text-primary" style="font-size: 13px;"></i> {{ __('Airport Transfer') }}
            </button>
        </div>
    </div>

    <!-- Main Search Card Body -->
    <div class="agoda-search-card">
        
        @php
            $rawCheckIn = request('check_in', date('Y-m-d'));
            $rawCheckOut = request('check_out', date('Y-m-d', strtotime('+1 day')));
            if ($rawCheckOut <= $rawCheckIn) {
                $rawCheckOut = date('Y-m-d', strtotime($rawCheckIn . ' +1 day'));
            }
            $initNights = max(1, (int)round((strtotime($rawCheckOut) - strtotime($rawCheckIn)) / 86400));
            $defaultAdults = (int)request('guests', 2);
            $defaultRooms = (int)request('rooms', 1);
            $defaultChildren = (int)request('children', 0);
        @endphp

        <!-- Form 1: Standard Hotel / Homes / Long Stays Search Form -->
        <form action="{{ route('search.index') }}" method="GET" id="agodaFormStandard" onsubmit="return validateAndSubmitAgodaSearch(event);">
            <input type="hidden" name="search_type" id="agodaSearchTypeInput" value="{{ request('search_type', request('type', 'hotel')) }}">
            <input type="hidden" name="check_in" id="inputCheckIn" value="{{ $rawCheckIn }}">
            <input type="hidden" name="check_out" id="inputCheckOut" value="{{ $rawCheckOut }}">
            <input type="hidden" name="guests" id="inputGuests" value="{{ $defaultAdults }}">
            <input type="hidden" name="rooms" id="inputRooms" value="{{ $defaultRooms }}">
            <input type="hidden" name="children" id="inputChildren" value="{{ $defaultChildren }}">
            <input type="hidden" name="entire_home" id="inputEntireHome" value="{{ request('entire_home', 0) }}">
                
                <!-- Row 1: Destination Input Button Box (Agoda 1:1 Clean Parity) -->
                <div id="agodaDestinationRowWrapper" style="margin-top: 14px; margin-bottom: 22px; position: relative; z-index: 10000;">
                    <div class="agoda-input-btn active-border" id="agodaDestinationBoxTrigger">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 1.3rem; color: #64748b;"></i>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <input type="text" id="agodaDestinationInput" name="destination" style="width: 100%; border: none; outline: none; background: transparent; font-size: 16px; font-weight: 600; color: #1e293b;" placeholder="{{ __('Enter a destination or property') }}" value="{{ request('destination', '') }}" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">
                            <input type="hidden" name="lat" id="homeGpsLatInput" value="{{ request('lat') }}">
                            <input type="hidden" name="lng" id="homeGpsLngInput" value="{{ request('lng') }}">
                            <i class="fa-solid fa-circle-xmark text-secondary" id="agodaClearDestinationBtn" style="cursor: pointer; font-size: 1.1rem; display: {{ request('destination') ? 'inline-block' : 'none' }};" title="Clear destination"></i>
                        </div>
                    </div>

                    <!-- Destination Autocomplete Popover Card — Agoda 1:1 Exact Parity (100% Full Width) -->
                    <div id="agodaDestinationPopoverCard" style="display: none; position: absolute; top: 68px; left: 0; right: 0; width: 100%; background: #ffffff; border-radius: 12px; box-shadow: 0 12px 36px rgba(0, 0, 0, 0.16); padding: 0; z-index: 10001; border: 1px solid #e2e8f0; text-align: left !important;">
                        <!-- Agoda Speech/SMS Bubble Pointer Notch -->
                        <div style="position: absolute; top: -8px; left: 36px; width: 14px; height: 14px; background: #ffffff; transform: rotate(45deg); border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; z-index: 10002;"></div>
                        <!-- DEFAULT STATE: Smart Popover — localStorage pills + DB city grid + "Book another place" CTA -->
                        <div id="agodaStaticSearchSuggestions" style="text-align: left !important;">

                            <!-- ⓪ Agoda 1:1 GPS Current Location Direct Pick Item -->
                            <div onclick="useHomeCurrentLocation(event)" class="agoda-popover-item" style="padding: 14px 28px 10px; cursor: pointer; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid #f1f5f9; transition: background 0.12s ease;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #e0edff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fa-solid fa-location-crosshairs text-primary" style="font-size: 17px;"></i>
                                </div>
                                <div style="flex: 1; text-align: left;">
                                    <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; line-height: 1.25;">Near My Current Location</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Find stays around your real-time GPS location</div>
                                </div>
                                <span style="font-size: 11px; font-weight: 700; color: #2067e1; background: #eef5ff; padding: 3px 8px; border-radius: 6px;">GPS</span>
                            </div>

                            <!-- ① Recent Search Pills — hydrated from localStorage by JS -->
                            <div id="pbRecentSearchSection" style="padding: 16px 28px 0; display: none;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">Recent searches</span>
                                    <button type="button" onclick="pbClearRecentSearches(event)" style="border: none; background: transparent; font-size: 11.5px; color: #ef4444; cursor: pointer; font-weight: 600;">
                                        <i class="fa-regular fa-trash-can me-1"></i> Clear
                                    </button>
                                </div>
                                <div id="pbRecentSearchPills" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;"></div>
                            </div>

                            <!-- ② Destinations Grid — 2 column (BD left + International right) -->
                            <div id="pbDestinationsGrid" style="padding: 20px 28px; text-align: left !important;">

                                <!-- Left: Destinations in Bangladesh (loaded from API / fallback static) -->
                                <div style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px;">

                                    <div style="border-right: 1px solid #edf2f7; padding-right: 20px;">
                                        <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 14px;">Destinations in Bangladesh</div>
                                        <div id="pbBdCitiesGrid" style="display: grid; grid-template-columns: 1fr 1fr; row-gap: 14px; column-gap: 16px;">
                                            @php
                                            // 100% Real Live Database Cities & Counts (Zero Hardcoded Mock Data)
                                            $allDbCities = \Illuminate\Support\Facades\Cache::remember('home_real_db_cities_v10', 60, function() {
                                                return \App\Models\Property::active()
                                                    ->select('city')
                                                    ->selectRaw('COUNT(*) as count')
                                                    ->selectRaw('MIN(primary_image) as img')
                                                    ->whereNotNull('city')
                                                    ->where('city', '!=', '')
                                                    ->groupBy('city')
                                                    ->orderByDesc('count')
                                                    ->get()
                                                    ->map(function($r) {
                                                        $rawImg = $r->img ? trim($r->img) : null;
                                                        if (!$rawImg) {
                                                            $img = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';
                                                        } elseif (str_starts_with($rawImg, 'http')) {
                                                            $img = $rawImg;
                                                        } elseif (str_starts_with(ltrim($rawImg, '/'), 'storage/')) {
                                                            $img = asset(ltrim($rawImg, '/'));
                                                        } else {
                                                            $img = asset('storage/' . ltrim($rawImg, '/'));
                                                        }
                                                        return ['name' => $r->city, 'img' => $img, 'count' => (int)$r->count];
                                                    })
                                                    ->toArray();
                                            });

                                            $intlCityNames = ['Singapore', 'Kuala Lumpur', 'Bangkok', 'Dubai', 'Makkah'];
                                            $bdDbCities = array_values(array_filter($allDbCities, fn($c) => !in_array($c['name'], $intlCityNames)));
                                            $liveIntlCities = array_values(array_filter($allDbCities, fn($c) => in_array($c['name'], $intlCityNames)));

                                            $liveBdCities = array_slice($bdDbCities, 0, 6);
                                            $remainingBdCities = array_slice($bdDbCities, 6);

                                            $natureTags = [
                                                'Sajek'       => 'Cloud valley &bull; Scenic hills',
                                                'Bandarban'   => 'Mountain peaks &bull; Trekking',
                                                'Rangamati'   => 'Kaptai lake &bull; Island stays',
                                                'Sundarban'   => 'Mangrove forest &bull; River safari',
                                                'Sundarbans'  => 'Mangrove forest &bull; River cruise',
                                                'Tanguar Haor'=> 'Haor wetlands &bull; Luxury houseboat',
                                                'Sunamganj'   => 'Tanguar Haor &bull; Houseboats',
                                                'Kuakata'     => 'Panoramic beach &bull; Sunrise &amp; Sunset',
                                                'Sylhet'      => 'Tea gardens &bull; Nature lakes',
                                                'Cox\'s Bazar'=> 'Longest beach &bull; Ocean view',
                                                'Dhaka'       => 'Capital city &bull; Business &amp; Luxury',
                                                'Chittagong'  => 'Port city &bull; Foy\'s Lake &amp; Hills',
                                                'Sreemangal'  => 'Scenic nature &bull; Holiday stay',
                                            ];

                                            $allLiveCitiesForJs = array_map(function($c) use ($intlCityNames) {
                                                return [
                                                    'city' => $c['name'],
                                                    'country' => in_array($c['name'], $intlCityNames) ? ($c['name'] === 'Singapore' ? 'Singapore' : 'Malaysia') : 'Bangladesh',
                                                    'type' => 'City'
                                                ];
                                            }, $allDbCities);
                                            @endphp
                                            @foreach($liveBdCities as $c)
                                            <div data-destination="{{ $c['name'] }}" onclick="selectDestination(this.getAttribute('data-destination'))" class="agoda-popover-item" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 6px 8px; border-radius: 6px; transition: background 0.15s ease;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                                <img src="{{ $c['img'] }}" style="width: 42px; height: 42px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="{{ $c['name'] }}">
                                                <div style="font-size: 13.5px; color: #1e293b; line-height: 1.25;"><span style="font-weight: 700;">{{ $c['name'] }}</span> <span style="font-weight: 400; color: #64748b; font-size: 12.5px;">({{ $c['count'] }})</span></div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Right Column: 100% Real Database Driven Escapes + Real Intl -->
                                    <div style="display: flex; flex-direction: column; justify-content: space-between;">
                                        <div>
                                            <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 12px;">Scenic &amp; Hill Escapes</div>
                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                @foreach($remainingBdCities as $rem)
                                                <div data-destination="{{ $rem['name'] }}" onclick="selectDestination(this.getAttribute('data-destination'))" class="agoda-popover-item" style="cursor: pointer; padding: 6px 8px; border-radius: 6px; transition: background 0.15s ease;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                                    <div style="font-size: 13.5px; color: #1e293b; line-height: 1.25;"><span style="font-weight: 700;">{{ $rem['name'] }}</span> <span style="color: #64748b; font-weight: 400; font-size: 12.5px;">({{ number_format($rem['count']) }})</span></div>
                                                    <div style="font-size: 11px; color: #78716c; margin-top: 1px;">{!! $natureTags[$rem['name']] ?? 'Scenic nature &bull; Holiday stay' !!}</div>
                                                </div>
                                                @endforeach
                                            </div>

                                            @if(!empty($liveIntlCities))
                                            <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-top: 14px; margin-bottom: 8px;">International Destinations</div>
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                @foreach($liveIntlCities as $intl)
                                                <div data-destination="{{ $intl['name'] }}" onclick="selectDestination(this.getAttribute('data-destination'))" class="agoda-popover-item" style="cursor: pointer; padding: 6px 8px; border-radius: 6px; transition: background 0.15s ease;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                                    <div style="font-size: 13.5px; color: #1e293b; line-height: 1.25;"><span style="font-weight: 700;">{{ $intl['name'] }}</span> <span style="color: #64748b; font-weight: 400; font-size: 12.5px;">({{ number_format($intl['count']) }})</span></div>
                                                    <div style="font-size: 11px; color: #78716c; margin-top: 1px;">{{ $intl['name'] === 'Singapore' ? 'Singapore' : 'Malaysia' }} &bull; Luxury stays</div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        <div style="text-align: right; margin-top: 12px;">
                                            <a href="{{ route('search.index') }}" style="font-size: 12.5px; font-weight: 600; color: #2067e1; text-decoration: none;">View all {{ array_sum(array_column($allDbCities, 'count')) }} stays <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ③ "Book another place to stay" CTA — Agoda Exact (shown if prior search in localStorage) -->
                            <div id="pbBookAnotherCta" style="display: none; border-top: 1px solid #f1f5f9; padding: 14px 28px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                                <div style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Book another place to stay</div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 6px;">We have unlocked the best deals with Trip Savings</div>
                                <div id="pbBookAnotherDetails" style="font-size: 12px; color: #2067e1; font-weight: 600;">
                                    <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i>
                                    <span id="pbBookAnotherDates"></span> &bull; <span id="pbBookAnotherGuests"></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="margin-left: 6px; font-size: 10px;"></i>
                                </div>
                            </div>

                        </div>

                        <!-- LIVE TYPING STATE: Shown when user types -->
                        <div id="agodaLiveSearchResultsContainer" style="display: none; max-height: 480px; overflow-y: auto; padding: 6px 0;"></div>

                    </div>

                </div>

            <!-- Row 2: Split Columns with 2 SEPARATE DATE BUTTONS & CONNECTING NIGHTS PILL -->
            <div class="row g-3" style="margin-bottom: 22px; position: relative;">
                
                <!-- Box 1: Date Pickers Container -->
                <div class="col-md-7" style="position: relative;">
                    <div style="display: flex; gap: 10px; width: 100%; position: relative; align-items: center;">
                        
                        <!-- Separate Button 1: Check-in Date Button -->
                        <button type="button" class="agoda-input-btn" id="btnCheckInDate" style="flex: 1; min-width: 0;">
                            <i class="fa-solid fa-calendar-days" style="font-size: 1.25rem; color: #2067e1; flex-shrink: 0;"></i>
                            <div style="overflow: hidden; text-align: left;">
                                <span id="displayCheckInDate" style="font-weight: 700; color: #1e293b; display: block; font-size: 15px; line-height: 1.2; white-space: nowrap; text-overflow: ellipsis;">{{ date('j M Y', strtotime($rawCheckIn)) }}</span>
                                <small id="displayCheckInDay" style="color: #64748b; font-size: 12px;">{{ date('l', strtotime($rawCheckIn)) }}</small>
                            </div>
                        </button>

                        <!-- Agoda Central Connecting Floating Nights Pill -->
                        <div class="agoda-nights-pill" id="agodaNightsPill" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 5; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 700; color: #1e293b; box-shadow: 0 2px 8px rgba(0,0,0,0.08); pointer-events: none; white-space: nowrap;">
                            <i class="fa-regular fa-moon" style="color: #2067e1; font-size: 11px;"></i>
                            <span id="agodaNightsCount">{{ $initNights }} {{ $initNights === 1 ? 'night' : 'nights' }}</span>
                        </div>

                        <!-- Separate Button 2: Check-out Date Button -->
                        <button type="button" class="agoda-input-btn" id="btnCheckOutDate" style="flex: 1; min-width: 0;">
                            <i class="fa-solid fa-calendar-days" style="font-size: 1.25rem; color: #2067e1; flex-shrink: 0;"></i>
                            <div style="overflow: hidden; text-align: left;">
                                <span id="displayCheckOutDate" style="font-weight: 700; color: #1e293b; display: block; font-size: 15px; line-height: 1.2; white-space: nowrap; text-overflow: ellipsis;">{{ date('j M Y', strtotime($rawCheckOut)) }}</span>
                                <small id="displayCheckOutDay" style="color: #64748b; font-size: 12px;">{{ date('l', strtotime($rawCheckOut)) }}</small>
                            </div>
                        </button>

                    </div>

                    <!-- Dual Month Interactive Calendar Popover Card (Anchored 6px below date buttons) -->
                    <div id="agodaCalendarPopoverCard" style="display: none; position: absolute; top: 68px; left: 0; width: 680px; max-width: 94vw; background: #ffffff !important; opacity: 1 !important; border-radius: 16px; box-shadow: 0 14px 40px rgba(0,0,0,0.22); padding: 24px; z-index: 99999; border: 1px solid #e2e8f0;">
                        <!-- Dynamic Triangle Notch (Agoda SMS/Speech Bubble Notch) -->
                        <div id="calendarPointerTriangle" style="position: absolute; top: -8px; left: 60px; width: 14px; height: 14px; background: #ffffff; transform: rotate(45deg); border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; z-index: 100000; transition: left 0.2s ease;"></div>

                        <!-- Top Tabs: Calendar | I'm flexible (Agoda Screenshot Parity) -->
                        <div style="display: flex; gap: 32px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                            <button type="button" id="tabCalendarMode" onclick="switchCalendarTab('calendar')" style="background: transparent; border: none; color: #2067e1; font-weight: 700; font-size: 15px; border-bottom: 3px solid #2067e1; padding-bottom: 10px; cursor: pointer; transition: all 0.15s ease;">{{ __('Calendar') }}</button>
                            <button type="button" id="tabFlexibleMode" onclick="switchCalendarTab('flexible')" style="background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 15px; border-bottom: 3px solid transparent; padding-bottom: 10px; cursor: pointer; transition: all 0.15s ease;">{{ __("I'm flexible") }}</button>
                        </div>

                        <!-- View 1: Calendar Dual Month Grid -->
                        <div id="calendarModeView">
                            <!-- Navigation Header -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                                <button type="button" id="btnPrevMonth" onclick="prevCalendarMonth()" style="border: none; background: transparent; color: #64748b; font-size: 1.1rem; cursor: pointer; padding: 4px 10px;"><i class="fa-solid fa-chevron-left"></i></button>
                                <div style="display: flex; width: 90%; justify-content: space-around;" id="agodaCalendarNavTitle">
                                    <!-- Dynamic Month Titles -->
                                </div>
                                <button type="button" id="btnNextMonth" onclick="nextCalendarMonth()" style="border: none; background: transparent; color: #64748b; font-size: 1.1rem; cursor: pointer; padding: 4px 10px;"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>

                            <!-- Calendar Dynamic Grid Section -->
                            <div style="display: flex; gap: 32px;" id="agodaCalendarDynamicGrid">
                                <!-- Dynamically generated via renderAgodaDynamicCalendar() -->
                            </div>
                        </div>

                        <!-- View 2: I'm flexible View (Agoda Screenshot 1:1 Parity) -->
                        <div id="flexibleModeView" style="display: none; text-align: left !important;">
                            <!-- Section 1: How long do you want to stay? -->
                            <div style="margin-bottom: 24px;">
                                <h6 style="font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 14px;">{{ __('How long do you want to stay?') }}</h6>
                                <div style="display: flex; gap: 10px;" id="flexibleStayLengthOptions">
                                    <button type="button" class="flexible-stay-btn" data-days="3" onclick="setFlexibleStayLength(3, this)" style="border-radius: 24px; padding: 7px 20px; font-size: 13.5px; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; cursor: pointer; transition: all 0.15s ease;">
                                        3 nights
                                    </button>
                                    <button type="button" class="flexible-stay-btn active" data-days="7" onclick="setFlexibleStayLength(7, this)" style="border-radius: 24px; padding: 7px 20px; font-size: 13.5px; font-weight: 700; border: 2px solid #2067e1; background: #ffffff; color: #2067e1; cursor: pointer; transition: all 0.15s ease;">
                                        1 week
                                    </button>
                                    <button type="button" class="flexible-stay-btn" data-days="30" onclick="setFlexibleStayLength(30, this)" style="border-radius: 24px; padding: 7px 20px; font-size: 13.5px; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; cursor: pointer; transition: all 0.15s ease;">
                                        1 month
                                    </button>
                                </div>
                            </div>

                            <!-- Section 2: When do you want to travel? -->
                            <div style="margin-bottom: 26px;">
                                <div style="margin-bottom: 12px;">
                                    <h6 style="font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 2px;">{{ __('When do you want to travel?') }}</h6>
                                    <small style="color: #64748b; font-size: 12.5px;">{{ __('Select your preferred month') }}</small>
                                </div>
                                
                                <!-- Horizontal Scrollable Month Cards with Floating Circular Arrow -->
                                <div style="position: relative; display: flex; align-items: center;">
                                    <div id="flexibleMonthsContainer" style="display: flex; gap: 12px; overflow-x: auto; scroll-behavior: smooth; padding: 4px 2px; width: 100%; -ms-overflow-style: none; scrollbar-width: none;">
                                        <!-- Dynamically populated via renderFlexibleMonths() -->
                                    </div>
                                    <!-- Floating Circular Arrow Right -->
                                    <button type="button" id="btnFlexibleMonthNext" onclick="scrollFlexibleMonths(1)" style="position: absolute; right: -8px; width: 34px; height: 34px; border-radius: 50%; background: #ffffff; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 5; color: #1e293b; font-size: 13px;" title="Next months">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Bottom Actions: Clear & Select -->
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 16px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                                <button type="button" onclick="clearFlexibleSelection()" style="background: transparent; border: none; color: #2067e1; font-weight: 700; font-size: 14px; cursor: pointer; padding: 6px 14px;">
                                    {{ __('Clear') }}
                                </button>
                                <button type="button" onclick="applyFlexibleSelection()" style="background: #2067e1; border: none; color: #ffffff; border-radius: 24px; padding: 8px 26px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 4px 12px rgba(32, 103, 225, 0.3);">
                                    {{ __('Select') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box 2: Guests & Rooms Separate Button Container -->
                <div class="col-md-5" style="position: relative;">
                    <button type="button" class="agoda-input-btn" id="agodaGuestBoxTrigger">
                        <i class="fa-solid fa-user-group" style="font-size: 1.25rem; color: #2067e1; flex-shrink: 0;"></i>
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; text-align: left;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; display: block; font-size: 15px; line-height: 1.2;" id="displayAdultsCount">{{ $defaultAdults }} adults{{ $defaultChildren > 0 ? ', ' . $defaultChildren . ' children' : '' }}</span>
                                <small style="color: #64748b; font-size: 12px;" id="displayRoomsCount">{{ $defaultRooms }} {{ $defaultRooms > 1 ? 'rooms' : 'room' }}</small>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="color: #94a3b8; font-size: 0.8rem;"></i>
                        </div>
                    </button>

                    <!-- Interactive Occupancy Stepper Popover Card (Anchored 6px below guest button) -->
                    <div id="agodaGuestsPopoverCard" style="display: none; position: absolute; top: 68px; right: 0; width: 320px; background: #ffffff; border-radius: 12px; box-shadow: 0 14px 36px rgba(0,0,0,0.22); padding: 22px; z-index: 99999; border: 1px solid #e2e8f0;">
                        <!-- Triangle Notch (Agoda SMS/Speech Bubble Notch) -->
                        <div style="position: absolute; top: -8px; right: 28px; width: 14px; height: 14px; background: #ffffff; transform: rotate(45deg); border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; z-index: 100000;"></div>

                        <!-- Item 1: Rooms -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; position: relative;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">{{ __('Rooms') }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusRoom" {{ $defaultRooms <= 1 ? 'disabled' : '' }}>-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valRooms">{{ $defaultRooms }}</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusRoom">+</button>
                            </div>

                            <!-- Agoda Tooltip Callout next to Room row (Image 5 Exact Parity) -->
                            <div class="d-none d-lg-block" style="position: absolute; left: calc(100% + 14px); top: 0; width: 220px; background: #1e293b; color: #ffffff; padding: 11px 14px; border-radius: 6px; font-size: 12px; line-height: 1.35; font-weight: 500; box-shadow: 0 4px 14px rgba(0,0,0,0.25); z-index: 100001; pointer-events: none; text-align: left;">
                                <div style="position: absolute; left: -5px; top: 14px; width: 10px; height: 10px; background: #1e293b; transform: rotate(45deg);"></div>
                                Search for 1 room to see all multi-bedroom properties that can fit your entire group
                            </div>
                        </div>

                        <!-- Item 2: Adults -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">{{ __('Adults') }}</span>
                                <small style="color: #64748b; font-size: 12px;">{{ __('Ages 18 or above') }}</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusAdult" {{ $defaultAdults <= 1 ? 'disabled' : '' }}>-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valAdults">{{ $defaultAdults }}</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusAdult">+</button>
                            </div>
                        </div>

                        <!-- Item 3: Children -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">{{ __('Children') }}</span>
                                <small style="color: #64748b; font-size: 12px;">{{ __('Ages 0-17') }}</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusChild" {{ $defaultChildren <= 0 ? 'disabled' : '' }}>-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valChildren">{{ $defaultChildren }}</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusChild">+</button>
                            </div>
                        </div>

                        <!-- Dynamic Child Age Selector -->
                        <div id="childAgeContainer" style="display: none; margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 12px;"></div>

                        <!-- Bottom Done / Confirm Button -->
                        <div style="margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 14px; text-align: right;">
                            <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" onclick="hideAllSearchPopovers()" style="border-radius: 8px; font-size: 13px; background: #2067e1; border: none; height: 36px; padding-left: 20px; padding-right: 20px;">
                                {{ __('Done') }}
                            </button>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Row 3: Agoda Checkbox (Exact 1:1 Parity) -->
            <div style="display: flex; align-items: center; margin-left: 2px; margin-top: 10px; margin-bottom: 22px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="entireHomesAgodaMatchExact100" {{ request('entire_home') ? 'checked' : '' }} onchange="document.getElementById('inputEntireHome').value = this.checked ? 1 : 0;" style="width: 18px; height: 18px; border: 1px solid #94a3b8; border-radius: 4px; cursor: pointer; accent-color: #2067e1;">
                    <label for="entireHomesAgodaMatchExact100" style="font-size: 13.5px; color: #475569; font-weight: 400; cursor: pointer; margin: 0; user-select: none;">
                        {{ __('Show me only entire homes and apartments') }}
                    </label>
                </div>
            </div>

            <!-- Row 4: Agoda Blue SEARCH Button -->
            <button type="submit" class="agoda-search-submit-btn">
                <i class="fa-solid fa-magnifying-glass" style="margin-right: 8px;"></i> SEARCH
            </button>

        </form>

        <!-- Form 2: Dedicated Airport Transfer Search Form (Exact Screenshot 4 Parity) -->
        <form action="{{ route('services') }}" method="GET" id="agodaFormAirport" style="display: none;" onsubmit="return validateAndSubmitAgodaTransfer(event);">
            <input type="hidden" name="type" value="transfer">

            <!-- Sub Toggle Pills: From Airport | To Airport -->
            <div style="display: flex; gap: 12px; margin-top: 10px; margin-bottom: 20px;">
                <button type="button" class="btn rounded-pill px-4 py-1.5 fw-bold" id="btnFromAirport" style="border: 2px solid #2067e1; color: #2067e1; background-color: #ffffff; font-size: 13px;">
                    From airport
                </button>
                <button type="button" class="btn rounded-pill px-4 py-1.5 fw-bold" id="btnToAirport" style="border: 1px solid #cbd5e1; color: #64748b; background-color: #ffffff; font-size: 13px;">
                    To airport
                </button>
            </div>

            <!-- Row 1: Pick-up airport + High Priority Overlapping Swap Icon + Destination location -->
            <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 18px; position: relative;">
                <!-- Left: Pick-up airport (Full Rounded Border Radius) -->
                <div class="agoda-input-btn" style="flex: 1; border-radius: 10px;">
                    <i class="fa-solid fa-plane-arrival" style="font-size: 1.2rem; color: #2067e1;"></i>
                    <input type="text" id="agodaPickupInput" name="pickup" style="width: 100%; border: none; outline: none; background: transparent; font-size: 14px; font-weight: 500; color: #1e293b;" placeholder="Pick-up airport (e.g. Hazrat Shahjalal International)">
                </div>

                <!-- Center Swap Button ⇄ (High Priority z-index: 10, Floating Center) -->
                <button type="button" id="agodaSwapBtn" style="width: 38px; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #2067e1; font-weight: 700; font-size: 14px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10; margin-left: -20px; margin-right: -20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12); transition: all 0.2s ease;" title="Swap Pick-up and Destination">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </button>

                <!-- Right: Destination location (Full Rounded Border Radius) -->
                <div class="agoda-input-btn" style="flex: 1; border-radius: 10px;">
                    <i class="fa-solid fa-location-dot" style="font-size: 1.2rem; color: #2067e1;"></i>
                    <input type="text" id="agodaDropoffInput" name="dropoff" style="width: 100%; border: none; outline: none; background: transparent; font-size: 14px; font-weight: 500; color: #1e293b;" placeholder="Destination location or hotel name">
                </div>
            </div>

            <!-- Row 2: Pick-up date & time + Passenger counter -->
            <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                <!-- Left: Date & Time -->
                <div class="agoda-input-btn" style="flex: 1;">
                    <i class="fa-solid fa-calendar-days" style="font-size: 1.2rem; color: #64748b;"></i>
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div>
                            <span style="font-weight: 700; color: #1e293b; display: block; font-size: 13px; line-height: 1.2;">{{ __('Pick-up date') }}</span>
                            <small style="color: #64748b; font-size: 11px;">{{ date('j M Y') }}</small>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 12px;">
                            <span style="font-weight: 700; color: #1e293b; display: block; font-size: 13px; line-height: 1.2;"><i class="fa-regular fa-clock me-1"></i> {{ date('g:i A', strtotime('+2 hours')) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Passenger Counter -->
                <div class="agoda-input-btn" style="flex: 1;">
                    <i class="fa-solid fa-user-group" style="font-size: 1.2rem; color: #64748b;"></i>
                    <div>
                        <span style="font-weight: 700; color: #1e293b; display: block; font-size: 14px; line-height: 1.2;">1 passenger</span>
                    </div>
                </div>
            </div>

            <!-- Row 3: Blue SEARCH button -->
            <button type="submit" class="agoda-search-submit-btn">
                Search
            </button>
        </form>

    </div>
    </div>

</div>

<!-- Vanilla JS Handlers for Focus Dark Blur Overlay & All Popovers -->
<script>

// ════════════════════════════════════════════════════════════════
// PB_SEARCH_HISTORY — localStorage-based recent search engine
// Stores up to 5 recent searches per browser (no server needed)
// ════════════════════════════════════════════════════════════════
const PB_SEARCH_KEY = 'pb_recent_searches_v2';
const PB_MAX_HISTORY = 5;

function pbGetRecentSearches() {
    try {
        return JSON.parse(localStorage.getItem(PB_SEARCH_KEY) || '[]');
    } catch(e) { return []; }
}

function pbSaveSearch(destination, checkIn, checkOut, guests) {
    try {
        let searches = pbGetRecentSearches();
        // Remove duplicate
        searches = searches.filter(s => s.destination !== destination);
        // Prepend new
        searches.unshift({ destination, checkIn, checkOut, guests, ts: Date.now() });
        // Keep only latest 5
        searches = searches.slice(0, PB_MAX_HISTORY);
        localStorage.setItem(PB_SEARCH_KEY, JSON.stringify(searches));
    } catch(e) {}
}

function pbFormatDate(dateStr) {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    } catch(e) { return dateStr; }
}

// Hydrate recent pills + "Book another place" CTA from localStorage
function pbHydrateRecentSearches() {
    const searches = pbGetRecentSearches();
    const section  = document.getElementById('pbRecentSearchSection');
    const pillsEl  = document.getElementById('pbRecentSearchPills');
    const ctaEl    = document.getElementById('pbBookAnotherCta');

    if (!pillsEl || !section) return;

    if (searches.length === 0) {
        section.style.display = 'none';
        if (ctaEl) ctaEl.style.display = 'none';
        return;
    }

    // Show recent pills (up to 3)
    const recent3 = searches.slice(0, 3);
    pillsEl.innerHTML = recent3.map(s => {
        const dateRange = (s.checkIn && s.checkOut)
            ? `${pbFormatDate(s.checkIn)} - ${pbFormatDate(s.checkOut)}`
            : 'Flexible dates';
        const guestText = s.guests ? `<i class="fa-solid fa-user" style="font-size: 10px;"></i> ${s.guests}` : '';
        const safeName  = (s.destination || '').replace(/'/g, "\\'");
        return `
            <div onclick="selectDestination('${safeName}')"
                 style="background: #f0f5fc; border-radius: 8px; padding: 10px 12px; cursor: pointer; transition: background 0.15s ease;"
                 onmouseover="this.style.background='#e2eeff'" onmouseout="this.style.background='#f0f5fc'">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 4px;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 13px; line-height: 1.25;
                                 display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        ${s.destination}
                    </span>
                    <span style="font-size: 11.5px; color: #64748b; flex-shrink: 0;">${guestText}</span>
                </div>
                <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">${dateRange}</div>
            </div>`;
    }).join('');

    section.style.display = 'block';

    // "Book another place to stay" CTA — show if there's at least 1 prior search
    if (ctaEl && searches.length > 0) {
        const last = searches[0];
        const datesEl   = document.getElementById('pbBookAnotherDates');
        const guestsEl  = document.getElementById('pbBookAnotherGuests');
        if (datesEl && last.checkIn && last.checkOut) {
            datesEl.textContent = `${pbFormatDate(last.checkIn)} - ${pbFormatDate(last.checkOut)}`;
        }
        if (guestsEl && last.guests) {
            guestsEl.textContent = `${last.guests} Adult${last.guests > 1 ? 's' : ''}`;
        }
        ctaEl.onclick = () => selectDestination(last.destination || '');
        ctaEl.style.display = 'block';
    }
}

// Clear recent searches from localStorage and refresh UI
window.pbClearRecentSearches = function(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    try {
        localStorage.removeItem(PB_SEARCH_KEY);
    } catch(err) {}
    pbHydrateRecentSearches();
};

// ════════════════════════════════════════════════════════════════
// selectDestination — Called when user clicks any suggestion
// Saves to history + logs to server + fills input
// ════════════════════════════════════════════════════════════════
function selectDestination(name) {
    if (!name) return;
    const input    = document.getElementById('agodaDestinationInput');
    const popover  = document.getElementById('agodaDestinationPopoverCard');
    const backdrop = document.getElementById('agodaSearchBackdropOverlay');
    const clearBtn = document.getElementById('agodaClearDestinationBtn');

    if (input) {
        input.value = name;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    if (popover) popover.style.display = 'none';
    if (backdrop) backdrop.style.display = 'none';
    if (clearBtn) clearBtn.style.display = 'inline-block';

    // Save to localStorage (capture current date values from form)
    try {
        const checkIn  = document.getElementById('inputCheckIn')?.value  || '';
        const checkOut = document.getElementById('inputCheckOut')?.value || '';
        const guests   = document.getElementById('inputGuests')?.value   || 1;
        if (typeof pbSaveSearch === 'function') {
            pbSaveSearch(name, checkIn, checkOut, guests);
        }

        // Log to server (async, fire-and-forget, never blocks UI)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        fetch('/api/search/log-query', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ query: name, city: name.split(',')[0]?.trim(), guests, check_in: checkIn, check_out: checkOut }),
            keepalive: true,
        }).catch(() => {});
    } catch(e) {}

    // Advance focus to Dates Picker smoothly (Agoda Exact 1:1 Flow as requested!)
    setTimeout(() => {
        const btnIn = document.getElementById('btnCheckInDate');
        if (btnIn) {
            btnIn.click();
        }
    }, 100);
}

let isSelectingCheckOut = false;
let checkInVal  = "{{ $rawCheckIn }}";
let checkOutVal = "{{ $rawCheckOut }}";

let currentNavYear  = new Date(checkInVal).getFullYear() || new Date().getFullYear();
let currentNavMonth = new Date(checkInVal).getMonth() || new Date().getMonth();

function updateNightsPill() {
    const pill = document.getElementById('agodaNightsPill');
    const countEl = document.getElementById('agodaNightsCount');
    if (!pill || !countEl || !checkInVal || !checkOutVal) return;

    const dIn = new Date(checkInVal);
    const dOut = new Date(checkOutVal);
    const diffTime = dOut - dIn;
    const nights = Math.max(1, Math.round(diffTime / (1000 * 60 * 60 * 24)));
    countEl.textContent = nights + (nights === 1 ? ' night' : ' nights');
}

window.setQuickDateRange = function(type) {
    const today = new Date();
    let inDate = new Date();
    let outDate = new Date();

    if (type === 'today') {
        inDate = new Date();
        outDate.setDate(today.getDate() + 1);
    } else if (type === 'tomorrow') {
        inDate.setDate(today.getDate() + 1);
        outDate.setDate(today.getDate() + 2);
    } else if (type === 'weekend') {
        const dayOfWeek = today.getDay();
        let daysUntilFriday = (5 - dayOfWeek + 7) % 7;
        if (daysUntilFriday === 0 && dayOfWeek !== 5) daysUntilFriday = 7;
        inDate.setDate(today.getDate() + daysUntilFriday);
        outDate.setDate(inDate.getDate() + 2);
    } else if (type === 'next_weekend') {
        const dayOfWeek = today.getDay();
        let daysUntilFriday = (5 - dayOfWeek + 7) % 7;
        inDate.setDate(today.getDate() + daysUntilFriday + 7);
        outDate.setDate(inDate.getDate() + 2);
    }

    const pad = n => String(n).padStart(2, '0');
    const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    const inStr = fmt(inDate);
    const outStr = fmt(outDate);
    const inDisplay = `${inDate.getDate()} ${monthNames[inDate.getMonth()]} ${inDate.getFullYear()}`;
    const outDisplay = `${outDate.getDate()} ${monthNames[outDate.getMonth()]} ${outDate.getFullYear()}`;

    checkInVal = inStr;
    checkOutVal = outStr;
    isSelectingCheckOut = false;

    const inputIn = document.getElementById('inputCheckIn');
    const inputOut = document.getElementById('inputCheckOut');
    const btnIn = document.getElementById('btnCheckInDate');
    const btnOut = document.getElementById('btnCheckOutDate');

    if (inputIn) inputIn.value = inStr;
    if (inputOut) inputOut.value = outStr;

    if (btnIn) {
        const s = btnIn.querySelector('#displayCheckInDate') || btnIn.querySelector('span');
        const sm = btnIn.querySelector('#displayCheckInDay') || btnIn.querySelector('small');
        if (s) s.textContent = inDisplay;
        if (sm) sm.textContent = dayNames[inDate.getDay()];
        btnIn.classList.remove('active-border');
    }
    if (btnOut) {
        const s = btnOut.querySelector('#displayCheckOutDate') || btnOut.querySelector('span');
        const sm = btnOut.querySelector('#displayCheckOutDay') || btnOut.querySelector('small');
        if (s) s.textContent = outDisplay;
        if (sm) sm.textContent = dayNames[outDate.getDay()];
        btnOut.classList.remove('active-border');
    }

    updateNightsPill();
    renderAgodaDynamicCalendar();

    const calendarCard = document.getElementById('agodaCalendarPopoverCard');
    const backdrop = document.getElementById('agodaSearchBackdropOverlay');
    if (calendarCard) calendarCard.style.display = 'none';
    if (backdrop) backdrop.style.display = 'none';
};

function prevCalendarMonth() {
    currentNavMonth--;
    if (currentNavMonth < 0) {
        currentNavMonth = 11;
        currentNavYear--;
    }
    renderAgodaDynamicCalendar();
}

function nextCalendarMonth() {
    currentNavMonth++;
    if (currentNavMonth > 11) {
        currentNavMonth = 0;
        currentNavYear++;
    }
    renderAgodaDynamicCalendar();
}

function renderAgodaDynamicCalendar() {
    const headerTitle = document.getElementById('agodaCalendarNavTitle');
    const calendarGrid = document.getElementById('agodaCalendarDynamicGrid');
    if (!calendarGrid) return;

    const month1Date = new Date(currentNavYear, currentNavMonth, 1);
    const month2Date = new Date(currentNavYear, currentNavMonth + 1, 1);

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const month1Name = monthNames[month1Date.getMonth()] + " " + month1Date.getFullYear();
    const month2Name = monthNames[month2Date.getMonth()] + " " + month2Date.getFullYear();

    if (headerTitle) {
        headerTitle.innerHTML = `<span style="font-weight: 700; font-size: 16px; color: #1e293b;">${month1Name}</span><span style="font-weight: 700; font-size: 16px; color: #1e293b;">${month2Name}</span>`;
    }

    calendarGrid.innerHTML = `
        <div style="flex: 1;">${buildSingleMonthHtml(month1Date.getFullYear(), month1Date.getMonth())}</div>
        <div style="flex: 1;">${buildSingleMonthHtml(month2Date.getFullYear(), month2Date.getMonth())}</div>
    `;
}

function buildSingleMonthHtml(year, month) {
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const shortMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    const todayStr = new Date().toISOString().split('T')[0];

    let offset = firstDayIndex; // Sunday is 0 (Exact Agoda Screenshot 2 Parity)

    let html = `
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: 700; color: #475569; font-size: 13px; margin-bottom: 10px;">
            <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; gap: 4px; font-size: 14px; font-weight: 600;">
    `;

    for (let i = 0; i < offset; i++) {
        html += `<div></div>`;
    }

    for (let day = 1; day <= totalDays; day++) {
        const dateObj = new Date(year, month, day);
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(day).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;

        const formattedStr = `${day} ${shortMonth[month]} ${yyyy}`;
        const dayNameStr = dayNames[dateObj.getDay()];

        const isPast = dateStr < todayStr;
        const isCheckIn = (dateStr === checkInVal);
        const isCheckOut = (dateStr === checkOutVal);
        const isInRange = (checkInVal && checkOutVal && dateStr > checkInVal && dateStr < checkOutVal);

        let bgStyle = 'background: transparent; border-radius: 6px;';
        let textStyle = 'color: #1e293b;';

        if (isCheckIn || isCheckOut) {
            bgStyle = 'background: #2067e1; color: white; border-radius: 50%; width: 34px; height: 34px; line-height: 34px; margin: 0 auto;';
            textStyle = 'color: white;';
        } else if (isInRange) {
            bgStyle = 'background: #e0edff; border-radius: 4px;';
        }

        if (isPast) {
            html += `<button type="button" style="opacity: 0.35; border: none; background: transparent; cursor: not-allowed; line-height: 34px;" disabled>${day}</button>`;
        } else {
            html += `<button type="button" class="calendar-day-btn" data-date="${dateStr}" onclick="selectCalendarDay('${dateStr}', '${formattedStr}', '${dayNameStr}')" style="${bgStyle} ${textStyle} border: none; line-height: 34px; cursor: pointer;">${day}</button>`;
        }
    }

    html += `</div>`;
    return html;
}

function selectCalendarDay(dateStr, formattedStr, dayName) {
    const inputIn = document.getElementById('inputCheckIn');
    const inputOut = document.getElementById('inputCheckOut');
    const btnIn = document.getElementById('btnCheckInDate');
    const btnOut = document.getElementById('btnCheckOutDate');
    const calendarCard = document.getElementById('agodaCalendarPopoverCard');
    const backdrop = document.getElementById('agodaSearchBackdropOverlay');

    if (!isSelectingCheckOut || dateStr <= checkInVal) {
        checkInVal = dateStr;
        isSelectingCheckOut = true;
        if (inputIn) inputIn.value = dateStr;
        if (btnIn) {
            const s = btnIn.querySelector('#displayCheckInDate') || btnIn.querySelector('span');
            const sm = btnIn.querySelector('#displayCheckInDay') || btnIn.querySelector('small');
            if (s) s.textContent = formattedStr;
            if (sm) sm.textContent = dayName;
            btnIn.classList.remove('active-border');
        }
        if (btnOut) btnOut.classList.add('active-border');
    } else {
        checkOutVal = dateStr;
        isSelectingCheckOut = false;
        if (inputOut) inputOut.value = dateStr;
        if (btnOut) {
            const s = btnOut.querySelector('#displayCheckOutDate') || btnOut.querySelector('span');
            const sm = btnOut.querySelector('#displayCheckOutDay') || btnOut.querySelector('small');
            if (s) s.textContent = formattedStr;
            if (sm) sm.textContent = dayName;
            btnOut.classList.remove('active-border');
        }
        if (btnIn) btnIn.classList.remove('active-border');
        if (calendarCard) calendarCard.style.display = 'none';

        // AUTOMATIC ADVANCE (Agoda Exact 1:1 Flow as requested!):
        // Automatically open the Guests & Rooms popover!
        setTimeout(() => {
            if (guestTrigger) {
                guestTrigger.click();
            }
        }, 120);
    }
    updateNightsPill();
    renderAgodaDynamicCalendar();
}

// ════════════════════════════════════════════════════════════════
// Agoda "I'm flexible" Tab Handlers — 100% Screenshot Parity
// ════════════════════════════════════════════════════════════════
let flexibleStayDays = 7; // Default 1 week
let selectedFlexibleMonths = []; // Array of { year, month }

window.switchCalendarTab = function(mode) {
    const tabCal = document.getElementById('tabCalendarMode');
    const tabFlex = document.getElementById('tabFlexibleMode');
    const viewCal = document.getElementById('calendarModeView');
    const viewFlex = document.getElementById('flexibleModeView');
    if (!tabCal || !tabFlex || !viewCal || !viewFlex) return;

    if (mode === 'flexible') {
        tabCal.style.color = '#64748b';
        tabCal.style.borderBottom = '3px solid transparent';
        tabCal.style.fontWeight = '600';

        tabFlex.style.color = '#2067e1';
        tabFlex.style.borderBottom = '3px solid #2067e1';
        tabFlex.style.fontWeight = '700';

        viewCal.style.display = 'none';
        viewFlex.style.display = 'block';
        renderFlexibleMonths();
    } else {
        tabCal.style.color = '#2067e1';
        tabCal.style.borderBottom = '3px solid #2067e1';
        tabCal.style.fontWeight = '700';

        tabFlex.style.color = '#64748b';
        tabFlex.style.borderBottom = '3px solid transparent';
        tabFlex.style.fontWeight = '600';

        viewCal.style.display = 'block';
        viewFlex.style.display = 'none';
    }
};

window.setFlexibleStayLength = function(days, btn) {
    flexibleStayDays = days;
    const parent = document.getElementById('flexibleStayLengthOptions');
    if (parent) {
        parent.querySelectorAll('.flexible-stay-btn').forEach(b => {
            b.style.border = '1px solid #cbd5e1';
            b.style.color = '#1e293b';
            b.style.fontWeight = '500';
        });
    }
    if (btn) {
        btn.style.border = '2px solid #2067e1';
        btn.style.color = '#2067e1';
        btn.style.fontWeight = '700';
    }
};

window.renderFlexibleMonths = function() {
    const container = document.getElementById('flexibleMonthsContainer');
    if (!container) return;

    const now = new Date();
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    // Default select next month if none selected
    if (selectedFlexibleMonths.length === 0) {
        const nextMonthDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        selectedFlexibleMonths.push({
            year: nextMonthDate.getFullYear(),
            month: nextMonthDate.getMonth()
        });
    }

    let html = '';
    for (let i = 0; i < 12; i++) {
        const d = new Date(now.getFullYear(), now.getMonth() + i, 1);
        const y = d.getFullYear();
        const m = d.getMonth();
        const mName = monthNames[m];
        const isSelected = selectedFlexibleMonths.some(item => item.year === y && item.month === m);

        const borderStyle = isSelected ? 'border: 2px solid #2067e1; box-shadow: 0 2px 8px rgba(32, 103, 225, 0.15);' : 'border: 1px solid #cbd5e1;';
        const iconColor = isSelected ? '#2067e1' : '#64748b';
        const textColor = isSelected ? '#1e293b' : '#475569';
        const titleWeight = isSelected ? '700' : '500';

        html += `
            <div class="flexible-month-card" onclick="toggleFlexibleMonth(${y}, ${m}, this)" style="width: 96px; height: 86px; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; cursor: pointer; flex-shrink: 0; background: #ffffff; transition: all 0.15s ease; ${borderStyle}" data-year="${y}" data-month="${m}">
                <i class="fa-regular fa-calendar-days" style="color: ${iconColor}; font-size: 15px;"></i>
                <span style="font-size: 12px; font-weight: ${titleWeight}; color: ${textColor}; line-height: 1.2;">${mName}</span>
                <span style="font-size: 11px; color: #64748b; font-weight: 500;">${y}</span>
            </div>
        `;
    }
    container.innerHTML = html;
};

window.toggleFlexibleMonth = function(year, month, el) {
    const idx = selectedFlexibleMonths.findIndex(item => item.year === year && item.month === month);
    if (idx >= 0) {
        if (selectedFlexibleMonths.length > 1) {
            selectedFlexibleMonths.splice(idx, 1);
        }
    } else {
        selectedFlexibleMonths = [{ year, month }];
    }
    renderFlexibleMonths();
};

window.scrollFlexibleMonths = function(dir) {
    const container = document.getElementById('flexibleMonthsContainer');
    if (container) {
        container.scrollBy({ left: dir * 180, behavior: 'smooth' });
    }
};

window.clearFlexibleSelection = function() {
    selectedFlexibleMonths = [];
    flexibleStayDays = 7;
    const weekBtn = document.querySelector('.flexible-stay-btn[data-days="7"]');
    if (weekBtn) setFlexibleStayLength(7, weekBtn);
    renderFlexibleMonths();
};

window.applyFlexibleSelection = function() {
    if (selectedFlexibleMonths.length === 0) {
        const now = new Date();
        const nextM = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        selectedFlexibleMonths.push({ year: nextM.getFullYear(), month: nextM.getMonth() });
    }

    const first = selectedFlexibleMonths[0];
    const now = new Date();
    let startDate;

    if (first.year === now.getFullYear() && first.month === now.getMonth()) {
        startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
    } else {
        startDate = new Date(first.year, first.month, 1);
    }

    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + flexibleStayDays);

    const pad = (n) => String(n).padStart(2, '0');
    const startStr = `${startDate.getFullYear()}-${pad(startDate.getMonth() + 1)}-${pad(startDate.getDate())}`;
    const endStr = `${endDate.getFullYear()}-${pad(endDate.getMonth() + 1)}-${pad(endDate.getDate())}`;

    const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    const formattedStart = `${startDate.getDate()} ${shortMonths[startDate.getMonth()]} ${startDate.getFullYear()}`;
    const dayStart = dayNames[startDate.getDay()];

    const formattedEnd = `${endDate.getDate()} ${shortMonths[endDate.getMonth()]} ${endDate.getFullYear()}`;
    const dayEnd = dayNames[endDate.getDay()];

    checkInVal = startStr;
    checkOutVal = endStr;
    currentNavYear = startDate.getFullYear();
    currentNavMonth = startDate.getMonth();

    const inputIn = document.getElementById('inputCheckIn');
    const inputOut = document.getElementById('inputCheckOut');
    const btnIn = document.getElementById('btnCheckInDate');
    const btnOut = document.getElementById('btnCheckOutDate');

    if (inputIn) inputIn.value = startStr;
    if (inputOut) inputOut.value = endStr;

    if (btnIn) {
        const s = btnIn.querySelector('#displayCheckInDate');
        const sm = btnIn.querySelector('#displayCheckInDay');
        if (s) s.textContent = formattedStart;
        if (sm) sm.textContent = dayStart;
    }
    if (btnOut) {
        const s = btnOut.querySelector('#displayCheckOutDate');
        const sm = btnOut.querySelector('#displayCheckOutDay');
        if (s) s.textContent = formattedEnd;
        if (sm) sm.textContent = dayEnd;
    }

    updateNightsPill();
    renderAgodaDynamicCalendar();

    const popover = document.getElementById('agodaCalendarPopoverCard');
    if (popover) popover.style.display = 'none';

    // Automatic Advance to Guests on flexible select
    const guestTrigger = document.getElementById('agodaGuestBoxTrigger');
    setTimeout(() => {
        if (guestTrigger) guestTrigger.click();
    }, 120);
};

document.addEventListener('DOMContentLoaded', function() {
    updateNightsPill();
    renderAgodaDynamicCalendar();
    const backdrop = document.getElementById('agodaSearchBackdropOverlay');
    const searchWrapper = document.getElementById('agodaSearchBarWrapper');

    const destTrigger = document.getElementById('agodaDestinationBoxTrigger');
    const destInput = document.getElementById('agodaDestinationInput');
    const destCard = document.getElementById('agodaDestinationPopoverCard');

    const btnCheckIn = document.getElementById('btnCheckInDate');
    const btnCheckOut = document.getElementById('btnCheckOutDate');
    const guestTrigger = document.getElementById('agodaGuestBoxTrigger');

    // Swap Button Handler (Pick-up ⇄ Drop-off)
    const swapBtn = document.getElementById('agodaSwapBtn');
    const pickupInput = document.getElementById('agodaPickupInput');
    const dropoffInput = document.getElementById('agodaDropoffInput');

    if (swapBtn && pickupInput && dropoffInput) {
        swapBtn.addEventListener('click', function() {
            const tempVal = pickupInput.value;
            pickupInput.value = dropoffInput.value;
            dropoffInput.value = tempVal;

            const icon = swapBtn.querySelector('i');
            if (icon) {
                icon.style.transition = 'transform 0.35s ease';
                icon.style.transform = (icon.style.transform === 'rotate(180deg)') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    }

    const calendarCard = document.getElementById('agodaCalendarPopoverCard');
    const guestsCard = document.getElementById('agodaGuestsPopoverCard');
    const calendarTriangle = document.getElementById('calendarPointerTriangle');

    let currentRooms = {{ $defaultRooms }};
    let currentAdults = {{ $defaultAdults }};
    let currentChildren = {{ $defaultChildren }};

    const valRooms = document.getElementById('valRooms');
    const valAdults = document.getElementById('valAdults');
    const valChildren = document.getElementById('valChildren');
    const displayAdultsCount = document.getElementById('displayAdultsCount');
    const displayRoomsCount = document.getElementById('displayRoomsCount');

    const btnMinusRoom = document.getElementById('btnMinusRoom');
    const btnPlusRoom = document.getElementById('btnPlusRoom');
    const btnMinusAdult = document.getElementById('btnMinusAdult');
    const btnPlusAdult = document.getElementById('btnPlusAdult');
    const btnMinusChild = document.getElementById('btnMinusChild');
    const btnPlusChild = document.getElementById('btnPlusChild');

    function showBackdrop() {
        if (backdrop) backdrop.style.display = 'block';
    }

    function hideBackdrop() {
        if (backdrop) backdrop.style.display = 'none';
        if (destCard) destCard.style.display = 'none';
        if (calendarCard) calendarCard.style.display = 'none';
        if (guestsCard) guestsCard.style.display = 'none';
        if (btnCheckIn) btnCheckIn.classList.remove('active-border');
        if (btnCheckOut) btnCheckOut.classList.remove('active-border');
        if (guestTrigger) guestTrigger.classList.remove('active-border');
    }

    window.hideAllSearchPopovers = function() {
        hideBackdrop();
    };

    window.applyGuestPreset = function(adults, rooms, children) {
        currentAdults = adults;
        currentRooms = rooms;
        currentChildren = children;
        updateUI();
    };

    function updateUI() {
        if (valRooms) valRooms.textContent = currentRooms;
        if (valAdults) valAdults.textContent = currentAdults;
        if (valChildren) valChildren.textContent = currentChildren;

        if (displayAdultsCount) displayAdultsCount.textContent = currentAdults + ' adults' + (currentChildren > 0 ? ', ' + currentChildren + ' children' : '');
        if (displayRoomsCount) displayRoomsCount.textContent = currentRooms + ' rooms';

        const inputGuests = document.getElementById('inputGuests');
        const inputRooms = document.getElementById('inputRooms');
        const inputChildren = document.getElementById('inputChildren');
        if (inputGuests) inputGuests.value = currentAdults + currentChildren;
        if (inputRooms) inputRooms.value = currentRooms;
        if (inputChildren) inputChildren.value = currentChildren;

        if (btnMinusRoom) btnMinusRoom.disabled = (currentRooms <= 1);
        if (btnMinusAdult) btnMinusAdult.disabled = (currentAdults <= 1);
        if (btnMinusChild) btnMinusChild.disabled = (currentChildren <= 0);

        // Agoda Image 5 Dynamic Child Age Selector
        const childContainer = document.getElementById('childAgeContainer');
        if (childContainer) {
            if (currentChildren > 0) {
                childContainer.style.display = 'block';
                let childHtml = '<div style="font-size: 11px; color: #64748b; margin-bottom: 8px; font-weight: 500;">To find a stay that fits your group, select child\'s age:</div>';
                for (let i = 1; i <= currentChildren; i++) {
                    childHtml += `
                        <div style="margin-bottom: 8px;">
                            <label style="font-size: 11.5px; font-weight: 700; color: #334155; display: block; margin-bottom: 3px;">Age of Child ${i}</label>
                            <select class="form-select form-select-sm" style="font-size: 12.5px; border-radius: 6px; border-color: #cbd5e1;">
                                <option value="0">&lt;1 year old</option>
                                <option value="1">1 year old</option>
                                <option value="2">2 years old</option>
                                <option value="3">3 years old</option>
                                <option value="4">4 years old</option>
                                <option value="5" selected>5 years old</option>
                                <option value="6">6 years old</option>
                                <option value="7">7 years old</option>
                                <option value="8">8 years old</option>
                                <option value="9">9 years old</option>
                                <option value="10">10 years old</option>
                                <option value="11">11 years old</option>
                                <option value="12">12 years old</option>
                                <option value="13">13 years old</option>
                                <option value="14">14 years old</option>
                                <option value="15">15 years old</option>
                                <option value="16">16 years old</option>
                                <option value="17">17 years old</option>
                            </select>
                        </div>
                    `;
                }
                childContainer.innerHTML = childHtml;
            } else {
                childContainer.style.display = 'none';
            }
        }

        // Re-position guests popover if open to fit inside screen bounds
        if (guestsCard && guestsCard.style.display === 'block' && guestTrigger) {
            positionPopover(guestsCard, guestTrigger);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // SENIOR BEST PRACTICE: Universal Outside Click Dismissal (pointerdown + contains)
    // 0ms instant dismissal on any surface (hero background, navbar, page body, card)
    // ════════════════════════════════════════════════════════════════
    document.addEventListener('pointerdown', function(e) {
        const isDestOpen  = destCard && destCard.style.display === 'block';
        const isCalOpen   = calendarCard && calendarCard.style.display === 'block';
        const isGuestOpen = guestsCard && guestsCard.style.display === 'block';

        if (!isDestOpen && !isCalOpen && !isGuestOpen) return;

        // Check if pointer event occurred inside destination trigger / input / popover
        const insideDest = (destTrigger && destTrigger.contains(e.target)) ||
                           (destCard && destCard.contains(e.target)) ||
                           (destInput && destInput.contains(e.target));

        // Check if pointer event occurred inside calendar trigger / popover
        const dateBoxTrigger = document.getElementById('agodaDateBoxTrigger');
        const insideCal = (btnCheckIn && btnCheckIn.contains(e.target)) ||
                          (btnCheckOut && btnCheckOut.contains(e.target)) ||
                          (dateBoxTrigger && dateBoxTrigger.contains(e.target)) ||
                          (calendarCard && calendarCard.contains(e.target));

        // Check if pointer event occurred inside guests trigger / popover
        const insideGuest = (guestTrigger && guestTrigger.contains(e.target)) ||
                            (guestsCard && guestsCard.contains(e.target));

        if (isDestOpen && !insideDest) {
            destCard.style.display = 'none';
        }

        if (isCalOpen && !insideCal) {
            calendarCard.style.display = 'none';
            if (btnCheckIn) btnCheckIn.classList.remove('active-border');
            if (btnCheckOut) btnCheckOut.classList.remove('active-border');
        }

        if (isGuestOpen && !insideGuest) {
            guestsCard.style.display = 'none';
        }

        // Hide overlay backdrop if everything is closed
        const anyOpen = (destCard && destCard.style.display === 'block') ||
                        (calendarCard && calendarCard.style.display === 'block') ||
                        (guestsCard && guestsCard.style.display === 'block');
        if (!anyOpen && backdrop) {
            backdrop.style.display = 'none';
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', hideBackdrop);
    }

    // Global Escape Key Handler (Closes all popovers on ESC press)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideBackdrop();
        }
    });

    // Position popover attached directly under trigger element
    function positionPopover(card, triggerEl) {
        if (!card || !triggerEl) return;
        if (card === destCard) {
            card.style.top = '66px';
            card.style.left = '0';
            return;
        }
        card.style.top = '';
        card.style.left = '';
    }

    if (destTrigger && destCard) {
        destTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            showBackdrop();
            positionPopover(destCard, destTrigger);
            destCard.style.display = 'block';
            if (destInput) destInput.focus();
            if (calendarCard) calendarCard.style.display = 'none';
            if (guestsCard) guestsCard.style.display = 'none';
        });
    }
    if (destInput && destCard) {
        destInput.addEventListener('focus', function(e) {
            e.stopPropagation();
            showBackdrop();
            positionPopover(destCard, destTrigger);
            destCard.style.display = 'block';
            if (calendarCard) calendarCard.style.display = 'none';
            if (guestsCard)   guestsCard.style.display   = 'none';
            // Hydrate localStorage pills + "Book another place" CTA
            pbHydrateRecentSearches();
        });
    }

    // DOM Element Declarations
    const searchTypeInput = document.getElementById('agodaSearchTypeInput');
    const formHotels = document.getElementById('agodaFormStandard');
    const formAirport = document.getElementById('agodaFormAirport');

    // Agoda-Exact Live Typing Autocomplete Handler with AbortController for Lightning Speed
    let searchDebounce = null;
    let searchAbortController = null;
    if (destInput) {
        // Client-side In-Memory Cache (LRU-like Map) for 0ms nanosecond instant response
        const clientSearchMemoryCache = new Map();

        destInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounce);
            if (searchAbortController) {
                searchAbortController.abort(); // Cancel previous in-flight request immediately
            }
            const query = e.target.value.trim();
            const currentType = searchTypeInput ? searchTypeInput.value : 'hotel';
            const staticBox = document.getElementById('agodaStaticSearchSuggestions');
            const liveBox   = document.getElementById('agodaLiveSearchResultsContainer');

            if (query.length === 0) {
                if (staticBox) staticBox.style.display = 'block';
                if (liveBox)   liveBox.style.display   = 'none';
                pbHydrateRecentSearches();
                return;
            }

            const cacheKey = `${currentType}:${query.toLowerCase()}`;
            if (clientSearchMemoryCache.has(cacheKey)) {
                // 0ms Instant Load from Memory
                const cachedData = clientSearchMemoryCache.get(cacheKey);
                if (staticBox) staticBox.style.display = 'none';
                if (liveBox)   liveBox.style.display   = 'block';
                currentKeyboardIndex = -1;
                renderLiveSuggestions(cachedData, query);
                return;
            }

            searchDebounce = setTimeout(() => {
                searchAbortController = new AbortController();
                fetch('/api/search/autocomplete?q=' + encodeURIComponent(query) + '&search_type=' + currentType, {
                    signal: searchAbortController.signal
                })
                    .then(res => res.json())
                    .then(res => {
                        const apiData = res.data || res;
                        if (clientSearchMemoryCache.size > 100) {
                            const firstKey = clientSearchMemoryCache.keys().next().value;
                            clientSearchMemoryCache.delete(firstKey);
                        }
                        clientSearchMemoryCache.set(cacheKey, apiData);

                        if (staticBox) staticBox.style.display = 'none';
                        if (liveBox)   liveBox.style.display   = 'block';
                        currentKeyboardIndex = -1;
                        renderLiveSuggestions(apiData, query);
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') return; // Ignore aborted requests
                        currentKeyboardIndex = -1;
                        renderLiveSuggestions({ locations: [], properties: [] }, query);
                    });
            }, 120); // 120ms ultra-fast snappy debounce
        });

        // Keyboard Navigation (Arrow Down / Up / Enter) — Agoda / Google Standard
        let currentKeyboardIndex = -1;
        destInput.addEventListener('keydown', function(e) {
            const liveBox = document.getElementById('agodaLiveSearchResultsContainer');
            if (!liveBox || liveBox.style.display === 'none') return;

            const items = liveBox.querySelectorAll('.agoda-popover-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentKeyboardIndex = (currentKeyboardIndex + 1) % items.length;
                updateKeyboardActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentKeyboardIndex = (currentKeyboardIndex - 1 + items.length) % items.length;
                updateKeyboardActiveItem(items);
            } else if (e.key === 'Enter') {
                if (currentKeyboardIndex >= 0 && items[currentKeyboardIndex]) {
                    e.preventDefault();
                    items[currentKeyboardIndex].click();
                }
            }
        });

        function updateKeyboardActiveItem(items) {
            items.forEach((it, idx) => {
                if (idx === currentKeyboardIndex) {
                    it.style.background = '#e9f2ff';
                    it.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    it.style.background = 'transparent';
                }
            });
        }
    }


    window.selectDestination = selectDestination;

    window.useHomeCurrentLocation = function(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }
        const destInput = document.getElementById('agodaDestinationInput');
        const icon = document.getElementById('homeGpsIcon');
        const origPlaceholder = destInput ? destInput.placeholder : '';
        if (destInput) destInput.placeholder = "Detecting your GPS location...";
        if (icon) icon.className = "fa-solid fa-spinner fa-spin text-primary";

        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const latInput = document.getElementById('homeGpsLatInput');
            const lngInput = document.getElementById('homeGpsLngInput');
            if (latInput) latInput.value = lat;
            if (lngInput) lngInput.value = lng;
            if (destInput) destInput.value = "Near My Location";

            // Smart Instant Reverse Geocoding with 600ms timeout fallback
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 650);

            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`, { signal: controller.signal })
                .then(r => r.json())
                .then(data => {
                    clearTimeout(timeoutId);
                    const area = data.locality || data.city || data.principalSubdivision;
                    if (area && destInput) {
                        destInput.value = area;
                    }
                })
                .catch(() => {})
                .finally(() => {
                    if (icon) icon.className = "fa-solid fa-location-crosshairs text-primary";
                    const popover = document.getElementById('agodaDestinationPopoverCard');
                    if (popover) popover.style.display = 'none';
                    const form = document.getElementById('agodaFormStandard');
                    if (form) form.submit();
                });
        }, function(err) {
            // Silent IP Geolocation Fallback if GPS is blocked or denied
            fetch('https://api.bigdatacloud.net/data/reverse-geocode-client?localityLanguage=en')
                .then(r => r.json())
                .then(data => {
                    const city = data.city || data.principalSubdivision || "Dhaka";
                    if (destInput) destInput.value = city;
                    const form = document.getElementById('agodaFormStandard');
                    if (form) form.submit();
                })
                .catch(() => {
                    if (destInput) destInput.placeholder = origPlaceholder;
                    if (icon) icon.className = "fa-solid fa-location-crosshairs text-primary";
                    alert("Location access denied. Please select your destination city.");
                });
        }, { timeout: 10000, enableHighAccuracy: true });
    };

    // ============================================================
    // CLIENT-SIDE Destinations Database — 100% Dynamic from DB Cache
    // ============================================================
    const BD_CITIES = @json($allLiveCitiesForJs ?? []);

    function matchClientCities(query) {
        if (!query || !query.trim()) return BD_CITIES.slice(0, 8);
        const lq = query.trim().toLowerCase();
        return BD_CITIES.filter(c => 
            c.city.toLowerCase().includes(lq) || 
            c.country.toLowerCase().includes(lq) ||
            (c.type && c.type.toLowerCase().includes(lq))
        ).slice(0, 8);
    }

    function highlightMatch(text, query) {
        if (!text) return '';
        if (!query || !query.trim()) return text;
        const q = query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${q})`, 'gi');
        return text.replace(regex, '<strong style="font-weight: 700; color: #000000;">$1</strong>');
    }

    function renderLiveSuggestions(data, query) {
        const container = document.getElementById('agodaLiveSearchResultsContainer');
        const card = document.getElementById('agodaDestinationPopoverCard');
        const staticBox = document.getElementById('agodaStaticSearchSuggestions');
        if (!container || !card) return;

        // Ensure popover card is displayed and floating
        card.style.display = 'block';
        if (staticBox) staticBox.style.display = 'none';
        container.style.display = 'block';

        let html = '';
        const lq = query.trim().toLowerCase();

        // ── 0. Matching Recent Searches from localStorage (Agoda Image 2 Parity) ──
        try {
            const stored = JSON.parse(localStorage.getItem(PB_SEARCH_KEY) || '[]');
            const matchingRecent = stored.filter(item => {
                const dest = (item.destination || '').toLowerCase();
                return dest.includes(lq);
            }).slice(0, 2);

            matchingRecent.forEach(rec => {
                const safeName = rec.destination.replace(/'/g, "\\'");
                const highlighted = highlightMatch(rec.destination, query);
                html += `
                    <div class="agoda-popover-item" onclick="selectDestination('${safeName}')" style="padding: 10px 22px; cursor: pointer; display: flex; align-items: center; gap: 16px; transition: background 0.12s ease;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                        <i class="fa-regular fa-clock" style="font-size: 16px; color: #2a2a2e; width: 22px; text-align: center; flex-shrink: 0;"></i>
                        <div style="flex: 1; min-width: 0; text-align: left;">
                            <div style="font-size: 14px; color: #2a2a2e; font-weight: 400; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${highlighted}
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch(e) {}

        // ── 1. City / Location Suggestions (100% Live DB from API — Agoda Image 2 & 3) ───────
        let allLocations = data.locations || [];
        if (allLocations.length === 0) {
            allLocations = matchClientCities(query || '');
        }

        if (allLocations.length > 0) {
            allLocations.slice(0, 6).forEach(loc => {
                const cityName  = loc.city || loc.title || '';
                const country   = loc.country || 'Bangladesh';
                const locType   = loc.type || loc.loc_type || 'City';
                const fullTitle = cityName + (country && !cityName.includes(country) ? ', ' + country : '');
                const safeTitle = fullTitle.replace(/'/g, "\\'");
                const highlightedFullTitle = highlightMatch(fullTitle, query);

                html += `
                    <div class="agoda-popover-item" onclick="selectDestination('${safeTitle}')" style="padding: 10px 22px; cursor: pointer; display: flex; align-items: center; gap: 16px; transition: background 0.12s ease;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                        <i class="fa-solid fa-location-dot" style="font-size: 16px; color: #2a2a2e; width: 22px; text-align: center; flex-shrink: 0;"></i>
                        <div style="flex: 1; min-width: 0; text-align: left;">
                            <div style="font-size: 14px; color: #2a2a2e; font-weight: 400; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${highlightedFullTitle}
                            </div>
                            <div style="color: #737373; font-size: 12px; font-weight: 400; margin-top: 1px;">${locType}</div>
                        </div>
                    </div>
                `;
            });
        }

        // ── 2. Matching Properties (Agoda Image 2 & 3: Bed Icon, clean row, subtitle "Property") ───
        const properties = data.properties || [];
        if (properties.length > 0) {
            properties.slice(0, 8).forEach(p => {
                const cityPart = p.city ? ', ' + p.city + ', Bangladesh' : '';
                const fullPropTitle = (p.name || '') + cityPart;
                const safeFullTitle = fullPropTitle.replace(/'/g, "\\'");
                const highlightedPropTitle = highlightMatch(fullPropTitle, query);

                html += `
                    <div class="agoda-popover-item" onclick="selectDestination('${safeFullTitle}')" style="padding: 10px 22px; cursor: pointer; display: flex; align-items: center; gap: 16px; transition: background 0.12s ease;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                        <i class="fa-solid fa-bed" style="font-size: 15px; color: #2a2a2e; width: 22px; text-align: center; flex-shrink: 0;"></i>
                        <div style="flex: 1; min-width: 0; text-align: left;">
                            <div style="font-size: 14px; color: #2a2a2e; font-weight: 400; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${highlightedPropTitle}
                            </div>
                            <div style="color: #737373; font-size: 12px; font-weight: 400; margin-top: 1px;">Property</div>
                        </div>
                    </div>
                `;
            });
        }

        if (html === '') {
            html = `<div style="padding: 24px; text-align: center; color: #737373; font-size: 13.5px;">No matching destinations or properties found for "<strong>${query}</strong>".</div>`;
        }

        container.innerHTML = html;
    }


    if (btnCheckIn && calendarCard) {
        btnCheckIn.addEventListener('click', function(e) {
            e.stopPropagation();
            showBackdrop();
            positionPopover(calendarCard, btnCheckIn);
            if (calendarTriangle) calendarTriangle.style.left = '60px';
            btnCheckIn.classList.add('active-border');
            if (btnCheckOut) btnCheckOut.classList.remove('active-border');
            calendarCard.style.display = 'block';
            if (destCard) destCard.style.display = 'none';
            if (guestsCard) guestsCard.style.display = 'none';
        });
    }

    if (btnCheckOut && calendarCard) {
        btnCheckOut.addEventListener('click', function(e) {
            e.stopPropagation();
            showBackdrop();
            positionPopover(calendarCard, btnCheckOut);
            if (calendarTriangle) calendarTriangle.style.left = '60px';
            btnCheckOut.classList.add('active-border');
            if (btnCheckIn) btnCheckIn.classList.remove('active-border');
            calendarCard.style.display = 'block';
            if (destCard) destCard.style.display = 'none';
            if (guestsCard) guestsCard.style.display = 'none';
        });
    }

    if (guestTrigger && guestsCard) {
        guestTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            showBackdrop();
            positionPopover(guestsCard, guestTrigger);
            guestsCard.style.display = 'block';
            guestTrigger.classList.add('active-border');
            if (btnCheckIn) btnCheckIn.classList.remove('active-border');
            if (btnCheckOut) btnCheckOut.classList.remove('active-border');
            if (destCard) destCard.style.display = 'none';
            if (calendarCard) calendarCard.style.display = 'none';
        });
    }

    if (btnPlusRoom) btnPlusRoom.addEventListener('click', function(e) { e.stopPropagation(); currentRooms++; updateUI(); });
    if (btnMinusRoom) btnMinusRoom.addEventListener('click', function(e) { e.stopPropagation(); if (currentRooms > 1) { currentRooms--; updateUI(); } });

    if (btnPlusAdult) btnPlusAdult.addEventListener('click', function(e) { e.stopPropagation(); currentAdults++; updateUI(); });
    if (btnMinusAdult) btnMinusAdult.addEventListener('click', function(e) { e.stopPropagation(); if (currentAdults > 1) { currentAdults--; updateUI(); } });

    if (btnPlusChild) btnPlusChild.addEventListener('click', function(e) { e.stopPropagation(); currentChildren++; updateUI(); });
    if (btnMinusChild) btnMinusChild.addEventListener('click', function(e) { e.stopPropagation(); if (currentChildren > 0) { currentChildren--; updateUI(); } });

    // Tab switching script matching Agoda 100% exact screenshots
    const tabHotels = document.getElementById('tabHotels');
    const tabHomes = document.getElementById('tabHomes');
    const tabSundarbanShip = document.getElementById('tabSundarbanShip');
    const tabTanguarHouseboat = document.getElementById('tabTanguarHouseboat');
    const tabHomeStay = document.getElementById('tabHomeStay');
    const tabAirport = document.getElementById('tabAirport');

    const heroTitle = document.getElementById('bdHeroTitle');

    function setActiveTab(activeBtn, searchType, titleText, isAirport = false) {
        [tabHotels, tabHomes, tabSundarbanShip, tabTanguarHouseboat, tabHomeStay, tabAirport].forEach(btn => {
            if (btn) btn.classList.remove('active');
        });
        if (activeBtn) activeBtn.classList.add('active');

        if (searchTypeInput) searchTypeInput.value = searchType;

        if (heroTitle) {
            heroTitle.style.opacity = '0';
            setTimeout(() => {
                heroTitle.textContent = titleText;
                heroTitle.style.opacity = '1';
            }, 200);
        }

        if (isAirport) {
            if (formHotels) formHotels.style.display = 'none';
            if (formAirport) formAirport.style.display = 'block';
        } else {
            if (formHotels) formHotels.style.display = 'block';
            if (formAirport) formAirport.style.display = 'none';
        }
    }

    if (tabHotels) tabHotels.addEventListener('click', function() {
        setActiveTab(tabHotels, 'hotel', 'HOTELS, RESORTS & HOSTELS IN BANGLADESH', false);
        const destInput = document.getElementById('agodaDestinationInput');
        if (destInput) destInput.placeholder = "{{ __('Enter a destination or property') }}";
    });
    if (tabHomes) tabHomes.addEventListener('click', function() {
        setActiveTab(tabHomes, 'homes', 'VACATION HOMES & APARTMENTS IN BANGLADESH', false);
        const destInput = document.getElementById('agodaDestinationInput');
        if (destInput) destInput.placeholder = "{{ __('Enter destination, apartment, or villa') }}";
        const checkbox = document.getElementById('entireHomesAgodaMatchExact100');
        const inputEntire = document.getElementById('inputEntireHome');
        if (checkbox) checkbox.checked = true;
        if (inputEntire) inputEntire.value = 1;
    });
    if (tabSundarbanShip) tabSundarbanShip.addEventListener('click', function() {
        setActiveTab(tabSundarbanShip, 'sundarban_ship', 'SUNDARBANS SHIP & CRUISE PACKAGES', false);
        const destInput = document.getElementById('agodaDestinationInput');
        const clearBtn = document.getElementById('agodaClearDestinationBtn');
        const latInput = document.getElementById('homeGpsLatInput');
        const lngInput = document.getElementById('homeGpsLngInput');
        if (latInput) latInput.value = '';
        if (lngInput) lngInput.value = '';
        if (destInput) {
            destInput.value = 'Sundarbans';
            destInput.placeholder = "{{ __('Search Sundarbans cruise or luxury ship') }}";
            destInput.focus();
        }
        if (clearBtn) clearBtn.style.display = 'inline-block';
        const destCard = document.getElementById('agodaDestinationPopoverCard');
        if (destCard) destCard.style.display = 'none';
    });
    if (tabTanguarHouseboat) tabTanguarHouseboat.addEventListener('click', function() {
        setActiveTab(tabTanguarHouseboat, 'tanguar_houseboat', 'TANGUAR HAOR LUXURY HOUSEBOATS', false);
        const destInput = document.getElementById('agodaDestinationInput');
        const clearBtn = document.getElementById('agodaClearDestinationBtn');
        const latInput = document.getElementById('homeGpsLatInput');
        const lngInput = document.getElementById('homeGpsLngInput');
        if (latInput) latInput.value = '';
        if (lngInput) lngInput.value = '';
        if (destInput) {
            destInput.value = 'Tanguar Haor';
            destInput.placeholder = "{{ __('Search Tanguar Haor premium houseboat') }}";
            destInput.focus();
        }
        if (clearBtn) clearBtn.style.display = 'inline-block';
        const destCard = document.getElementById('agodaDestinationPopoverCard');
        if (destCard) destCard.style.display = 'none';
    });
    if (tabHomeStay) tabHomeStay.addEventListener('click', function() {
        setActiveTab(tabHomeStay, 'homestay', 'VILLAGE HOMESTAYS & LOCAL COTTAGES', false);
        const destInput = document.getElementById('agodaDestinationInput');
        if (destInput) {
            destInput.placeholder = "{{ __('Enter city, village, or area for homestay') }}";
            destInput.focus();
        }
    });

    if (tabAirport) tabAirport.addEventListener('click', function() {
        setActiveTab(tabAirport, 'airport', 'BOOK YOUR AIRPORT TRANSFER', true);
    });

    // Airport sub-pill toggles: From airport | To airport
    const btnFromAirport = document.getElementById('btnFromAirport');
    const btnToAirport = document.getElementById('btnToAirport');

    if (btnFromAirport && btnToAirport) {
        btnFromAirport.addEventListener('click', function() {
            btnFromAirport.style.border = '2px solid #2067e1';
            btnFromAirport.style.color = '#2067e1';
            btnToAirport.style.border = '1px solid #cbd5e1';
            btnToAirport.style.color = '#64748b';
        });
        btnToAirport.addEventListener('click', function() {
            btnToAirport.style.border = '2px solid #2067e1';
            btnToAirport.style.color = '#2067e1';
            btnFromAirport.style.border = '1px solid #cbd5e1';
            btnFromAirport.style.color = '#64748b';
        });
    }

    updateUI();
});

/* ─── Agoda Empty Destination Interceptor & Validator (Exact Screenshot Parity) ─── */
function validateAndSubmitAgodaSearch(e) {
    const destInput = document.getElementById('agodaDestinationInput');
    const destVal = destInput ? destInput.value.trim() : '';
    const latInput = document.getElementById('homeGpsLatInput');
    const hasGps = latInput && latInput.value.trim() !== '';

    if (!destVal && !hasGps) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (typeof window.showAgodaEmptyDestinationModal === 'function') {
            window.showAgodaEmptyDestinationModal();
        }
        return false;
    }

    if (typeof window.showAgodaSearchLoading === 'function') {
        window.showAgodaSearchLoading();
    }
    return true;
}

function validateAndSubmitAgodaTransfer(e) {
    const pickup = document.getElementById('agodaPickupInput')?.value.trim();
    const dropoff = document.getElementById('agodaDropoffInput')?.value.trim();

    if (!pickup && !dropoff) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (typeof window.showAgodaEmptyDestinationModal === 'function') {
            window.showAgodaEmptyDestinationModal();
        }
        return false;
    }

    if (typeof window.showAgodaSearchLoading === 'function') {
        window.showAgodaSearchLoading();
    }
    return true;
}
</script>
