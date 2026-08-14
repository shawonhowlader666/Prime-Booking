<!-- Agoda.com 100% Exact Matching Search Engine Widget with Lowered Floating Capsule Tab Pod -->
<style>
    /* Slim Compact Agoda Tab Buttons (Screenshot 1 Parity) */
    .agoda-tab-btn {
        background: transparent;
        border: none;
        outline: none;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border-bottom: 2px solid transparent;
    }
    .agoda-tab-btn.active {
        color: #2067e1;
        font-weight: 700;
        border-bottom-color: #2067e1;
    }
    .agoda-tab-btn:hover:not(.active) {
        color: #1e293b;
        background-color: #f8fafc;
        border-radius: 12px;
    }

    /* Input Buttons with Increased Inside Top Padding (Height: 60px) */
    .agoda-input-btn {
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        height: 60px;
        padding: 10px 18px 8px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        outline: none;
    }
    .agoda-input-btn:hover {
        border-color: #2067e1;
        box-shadow: 0 4px 12px rgba(32, 103, 225, 0.12);
    }
    .agoda-input-btn.active-border {
        border: 2px solid #2067e1;
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

    /* Slim Agoda Blue SEARCH Button (Height: 44px) */
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
</style>

<!-- Fixed Dark Backdrop Blur Overlay (Agoda Official Focus Blur) -->
<div id="agodaSearchBackdropOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); z-index: 9990; transition: opacity 0.25s ease;"></div>

<div id="agodaSearchBarWrapper" style="max-width: 1140px; margin: 0 auto 38px auto; position: relative; z-index: 10; text-align: left !important;">

    <!-- Top White Floating Capsule Tab Pod Container (Customized for Bangladesh Travel Parity) -->
    <div style="position: relative; top: 16px; margin-left: 24px; margin-bottom: -16px; z-index: 5; display: inline-flex;">
        <div style="background-color: #ffffff; border-radius: 14px; display: inline-flex; align-items: center; gap: 2px; padding: 4px 10px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1); border: 1px solid #cbd5e1;">
            <button type="button" class="agoda-tab-btn active" id="tabHotels">
                <i class="fa-solid fa-building" style="font-size: 12px;"></i> {{ __('Hotels & Resorts') }}
            </button>
            <button type="button" class="agoda-tab-btn" id="tabHomes">
                <i class="fa-solid fa-ship" style="font-size: 12px; color: #0284c7;"></i> {{ __('Sundarbans Ship & Tanguar Haor Houseboat') }}
            </button>
            <button type="button" class="agoda-tab-btn" id="tabLongStays">
                <i class="fa-solid fa-house-user" style="font-size: 12px; color: #16a34a;"></i> {{ __('Home Stay / Long stays') }}
            </button>
            <button type="button" class="agoda-tab-btn" id="tabAirport">
                <i class="fa-solid fa-car" style="font-size: 12px;"></i> {{ __('Airport transfer') }}
            </button>
        </div>
    </div>

    <!-- Main Search Card Body (Agoda 100% 1:1 Exact Match) -->
    <div style="background-color: #ffffff; border-radius: 16px; padding: 24px 40px 44px 40px; box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12); border: 1px solid #cbd5e1; position: relative; z-index: 1; text-align: left !important;">
        
        <!-- Form 1: Standard Hotel / Homes / Long Stays Search Form -->
        <form action="{{ route('search.index') }}" method="GET" id="agodaFormStandard" onsubmit="showAgodaSearchLoading();">
            <input type="hidden" name="search_type" id="agodaSearchTypeInput" value="{{ request('search_type', request('type', 'hotel')) }}">
            <input type="hidden" name="check_in" id="inputCheckIn" value="{{ request('check_in', date('Y-m-d')) }}">
            <input type="hidden" name="check_out" id="inputCheckOut" value="{{ request('check_out', date('Y-m-d', strtotime('+7 days'))) }}">
            <input type="hidden" name="guests" id="inputGuests" value="{{ request('guests', 2) }}">
            <input type="hidden" name="rooms" id="inputRooms" value="{{ request('rooms', 1) }}">
                <input type="hidden" name="children" id="inputChildren" value="{{ request('children', 0) }}">
                <input type="hidden" name="entire_home" id="inputEntireHome" value="{{ request('entire_home', 0) }}">
                
                <!-- Row 1: Destination Input Button Box -->
                <div id="agodaDestinationRowWrapper" style="margin-top: 14px; margin-bottom: 22px; position: relative; z-index: 10000;">
                    <div class="agoda-input-btn active-border" id="agodaDestinationBoxTrigger">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 1.3rem; color: #2067e1;"></i>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: space-between;">
                            <input type="text" id="agodaDestinationInput" name="destination" style="width: 100%; border: none; outline: none; background: transparent; font-size: 15px; font-weight: 500; color: #1e293b;" placeholder="{{ __('Enter a destination or property') }}" value="{{ request('destination', '') }}" autocomplete="off">
                            <i class="fa-solid fa-circle-xmark text-secondary ms-2" id="agodaClearDestinationBtn" style="cursor: pointer; font-size: 1.1rem; display: {{ request('destination') ? 'inline-block' : 'none' }};" title="Clear destination"></i>
                        </div>
                    </div>

                    <!-- Destination Autocomplete Popover Card — Agoda 1:1 Exact Parity (Screenshots Match) -->
                    <div id="agodaDestinationPopoverCard" style="display: none; position: absolute; top: 66px; left: 0; width: 620px; max-width: 95vw; background: #ffffff; border-radius: 12px; box-shadow: 0 16px 48px rgba(0,0,0,0.32); padding: 0; z-index: 10001; border: 1px solid #cbd5e1; text-align: left !important; overflow: hidden;">
                        <!-- Triangle Notch Pointer -->
                        <div style="position: absolute; top: -8px; left: 40px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 8px solid #ffffff;"></div>

                        <!-- DEFAULT STATE: Shown when input is clicked (Agoda Exact Match) -->
                        <div id="agodaStaticSearchSuggestions" style="padding: 20px 24px; text-align: left !important;">
                            <!-- Recent search header -->
                            <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 12px; text-align: left !important;">Recent search</div>

                            <!-- Recent search 3 horizontal pill cards -->
                            <div style="display: flex; gap: 12px; overflow-x: auto; padding-bottom: 16px; scrollbar-width: none; text-align: left !important;">
                                <div onclick="selectDestination('Dhaka, Bangladesh')" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px 16px; min-width: 220px; flex-shrink: 0; cursor: pointer; background: #f5f8ff; transition: border-color 0.15s; text-align: left !important;" onmouseover="this.style.borderColor='#2067e1'" onmouseout="this.style.borderColor='#e0e0e0'">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                        <span style="font-weight: 700; color: #1a1a1a; font-size: 14px; line-height: 1.2;">Dhaka, Bangladesh</span>
                                        <span style="font-size: 12px; color: #64748b; margin-left: 8px;"><i class="fa-solid fa-user-group" style="font-size: 11px;"></i> 2</span>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">8 Sep 2026 - 15 Sep 2026</div>
                                </div>

                                <div onclick="selectDestination('Dhaka, Bangladesh')" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px 16px; min-width: 220px; flex-shrink: 0; cursor: pointer; background: #f5f8ff; transition: border-color 0.15s; text-align: left !important;" onmouseover="this.style.borderColor='#2067e1'" onmouseout="this.style.borderColor='#e0e0e0'">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                        <span style="font-weight: 700; color: #1a1a1a; font-size: 14px; line-height: 1.2;">Dhaka, Bangladesh</span>
                                        <span style="font-size: 12px; color: #64748b; margin-left: 8px;"><i class="fa-solid fa-user-group" style="font-size: 11px;"></i> 2</span>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">1 Sep 2026 - 30 Sep 2026</div>
                                </div>

                                <div onclick="selectDestination('Jatra Flagship Khulna City Centre, Khulna, Bangladesh')" style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px 16px; min-width: 260px; flex-shrink: 0; cursor: pointer; background: #f5f8ff; transition: border-color 0.15s; text-align: left !important;" onmouseover="this.style.borderColor='#2067e1'" onmouseout="this.style.borderColor='#e0e0e0'">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                        <span style="font-weight: 700; color: #1a1a1a; font-size: 14px; line-height: 1.2;">Jatra Flagship Khulna City Centre, Khulna, Bangladesh</span>
                                        <span style="font-size: 12px; color: #64748b; margin-left: 8px;"><i class="fa-solid fa-user-group" style="font-size: 11px;"></i> 2</span>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">1 Sep 2026 - 8 Sep 2026</div>
                                </div>
                            </div>

                            <!-- Section: Well-known properties in Dhaka (Sponsored) -->
                            <div style="margin-top: 14px; margin-bottom: 20px; text-align: left !important;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="font-size: 13px; font-weight: 500; color: #64748b;">Well-known properties in Dhaka</span>
                                    <span style="font-size: 11px; color: #64748b; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px 8px; cursor: pointer;">Sponsored <i class="fa-solid fa-circle-info" style="font-size: 10px; margin-left: 2px;"></i></span>
                                </div>
                                <div onclick="selectDestination('BWH Hotels, Dhaka, Bangladesh')" style="display: inline-flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px 18px; gap: 14px; background: #ffffff; cursor: pointer; transition: border-color 0.15s;" onmouseover="this.style.borderColor='#2067e1'" onmouseout="this.style.borderColor='#e0e0e0'">
                                    <div style="border-right: 1px solid #f1f5f9; padding-right: 12px; display: flex; align-items: center; justify-content: center;">
                                        <span style="font-weight: 900; font-size: 12px; color: #1e293b; letter-spacing: -0.5px;">BWH <small style="font-size: 8px; font-weight: 600; display: block;">Hotels</small></span>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 13.5px; color: #1a1a1a; line-height: 1.2;">BWH Hotels</div>
                                        <div style="font-size: 12px; color: #b48308; font-weight: 600; margin-top: 2px;">Inspiring Every Journey</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Two column section: Bangladesh (Left) + International (Right) -->
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; display: flex; gap: 32px; text-align: left !important;">

                                <!-- Left column: Destinations in Bangladesh with 6 city photos -->
                                <div style="flex: 1.4; min-width: 0; border-right: 1px solid #e2e8f0; padding-right: 28px; text-align: left !important;">
                                    <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 16px; text-align: left !important;">Destinations in Bangladesh</div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; row-gap: 18px; column-gap: 20px; text-align: left !important;">
                                        <div onclick="selectDestination('Chittagong, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=100&q=75" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Chittagong">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Chittagong</strong> <span style="font-weight: 400; color: #757575;">(59)</span></span>
                                        </div>
                                        <div onclick="selectDestination('Cox\'s Bazar, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&q=75" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Cox's Bazar">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Cox's Bazar</strong> <span style="font-weight: 400; color: #757575;">(118)</span></span>
                                        </div>
                                        <div onclick="selectDestination('Sreemangal Upazila, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=100&q=75" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Sreemangal">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Sreemangal Upazila</strong> <span style="font-weight: 400; color: #757575;">(25)</span></span>
                                        </div>
                                        <div onclick="selectDestination('Sylhet, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=100&q=75" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Sylhet">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Sylhet</strong> <span style="font-weight: 400; color: #757575;">(95)</span></span>
                                        </div>
                                        <div onclick="selectDestination('Dhaka, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100&q=75" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Dhaka">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Dhaka</strong> <span style="font-weight: 400; color: #757575;">(538)</span></span>
                                        </div>
                                        <div onclick="selectDestination('Rajshahi, Bangladesh')" class="agoda-popover-item" style="display: flex; align-items: center; gap: 12px; padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <img src="https://images.unsplash.com/photo-1587061949409-02df41d5e562?w=100&q=75" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover; flex-shrink: 0;" loading="lazy" alt="Rajshahi">
                                            <span style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Rajshahi</strong> <span style="font-weight: 400; color: #757575;">(11)</span></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column: International destinations -->
                                <div style="flex: 1; min-width: 0; text-align: left !important;">
                                    <div style="font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 16px; text-align: left !important;">International destinations</div>
                                    <div style="display: flex; flex-direction: column; gap: 16px; text-align: left !important;">
                                        <div onclick="selectDestination('Singapore')" class="agoda-popover-item" style="padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <div style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Singapore</strong> <span style="color: #757575; font-weight: 400;">(1,326)</span></div>
                                            <div style="font-size: 11.5px; color: #b05e29; margin-top: 2px; text-align: left !important;">shopping, restaurants</div>
                                        </div>
                                        <div onclick="selectDestination('Bangkok, Thailand')" class="agoda-popover-item" style="padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <div style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Bangkok</strong> <span style="color: #757575; font-weight: 400;">(12,048)</span></div>
                                            <div style="font-size: 11.5px; color: #b05e29; margin-top: 2px; text-align: left !important;">shopping, restaurants</div>
                                        </div>
                                        <div onclick="selectDestination('Kuala Lumpur, Malaysia')" class="agoda-popover-item" style="padding: 4px 6px; border-radius: 6px; cursor: pointer; text-align: left !important;">
                                            <div style="font-size: 13.5px; color: #1a1a1a; text-align: left !important;"><strong>Kuala Lumpur</strong> <span style="color: #757575; font-weight: 400;">(19,902)</span></div>
                                            <div style="font-size: 11.5px; color: #b05e29; margin-top: 2px; text-align: left !important;">shopping, restaurants</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- LIVE TYPING STATE: Shown when user types (Image 3 Exact Match) -->
                        <div id="agodaLiveSearchResultsContainer" style="display: none; max-height: 480px; overflow-y: auto; padding: 6px 0;"></div>

                    </div>

                </div>

<!-- Row 2: Split Columns with 2 SEPARATE DATE BUTTONS -->

            <!-- Row 2: Split Columns with 2 SEPARATE DATE BUTTONS -->
            <div class="row g-3" style="margin-bottom: 22px; position: relative;">
                
                <!-- Box 1: Date Pickers Container -->
                <div class="col-md-7" style="position: relative;">
                    <div style="display: flex; gap: 12px; width: 100%;">
                        
                        <!-- Separate Button 1: Check-in Date Button -->
                        <button type="button" class="agoda-input-btn" id="btnCheckInDate" style="flex: 1;">
                            <i class="fa-solid fa-calendar-days" style="font-size: 1.25rem; color: #2067e1;"></i>
                            <div>
                                <span style="font-weight: 700; color: #1e293b; display: block; font-size: 14px; line-height: 1.2;">1 Sep 2026</span>
                                <small style="color: #64748b; font-size: 11px;">Tuesday</small>
                            </div>
                        </button>

                        <!-- Separate Button 2: Check-out Date Button -->
                        <button type="button" class="agoda-input-btn" id="btnCheckOutDate" style="flex: 1;">
                            <i class="fa-solid fa-calendar-days" style="font-size: 1.25rem; color: #2067e1;"></i>
                            <div>
                                <span style="font-weight: 700; color: #1e293b; display: block; font-size: 14px; line-height: 1.2;">8 Sep 2026</span>
                                <small style="color: #64748b; font-size: 11px;">Tuesday</small>
                            </div>
                        </button>

                    </div>

                    <!-- Dual Month Interactive Calendar Popover Card (Anchored 6px below date buttons) -->
                    <div id="agodaCalendarPopoverCard" style="display: none; position: absolute; top: 66px; left: 0; width: 680px; max-width: 94vw; background: #ffffff; border-radius: 16px; box-shadow: 0 14px 40px rgba(0,0,0,0.28); padding: 24px; z-index: 99999; border: 1px solid #e2e8f0;">
                        <!-- Dynamic Triangle Notch -->
                        <div id="calendarPointerTriangle" style="position: absolute; top: -8px; left: 60px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 8px solid #ffffff; transition: left 0.2s ease;"></div>

                        <!-- Top Tabs -->
                        <div style="display: flex; gap: 32px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                            <button type="button" style="background: transparent; border: none; color: #2067e1; font-weight: 700; font-size: 15px; border-bottom: 3px solid #2067e1; padding-bottom: 10px; cursor: pointer;">Calendar</button>
                            <button type="button" style="background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 15px; cursor: pointer;">I'm flexible</button>
                        </div>

                        <!-- Navigation Header -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                            <button type="button" id="btnPrevMonth" onclick="prevCalendarMonth()" style="border: none; background: transparent; color: #2067e1; font-size: 1.2rem; cursor: pointer; padding: 4px 12px;"><i class="fa-solid fa-chevron-left"></i></button>
                            <div style="display: flex; width: 90%; justify-content: space-around;" id="agodaCalendarNavTitle">
                                <!-- Dynamic Month Titles (e.g., August 2026 / September 2026) -->
                            </div>
                            <button type="button" id="btnNextMonth" onclick="nextCalendarMonth()" style="border: none; background: transparent; color: #2067e1; font-size: 1.2rem; cursor: pointer; padding: 4px 12px;"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>

                        <!-- Calendar Dynamic Grid Section -->
                        <div style="display: flex; gap: 32px;" id="agodaCalendarDynamicGrid">
                            <!-- Dynamically generated via renderAgodaDynamicCalendar() -->
                        </div>

                        <div style="margin-top: 20px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b; font-weight: 500;">
                            Approximate prices (in BDT ৳) for 1-night stay in the searched property
                        </div>
                    </div>
                </div>

                <!-- Box 2: Guests & Rooms Separate Button Container -->
                <div class="col-md-5" style="position: relative;">
                    <button type="button" class="agoda-input-btn" id="agodaGuestBoxTrigger">
                        <i class="fa-solid fa-user-group" style="font-size: 1.25rem; color: #2067e1;"></i>
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; display: block; font-size: 14px; line-height: 1.2;" id="displayAdultsCount">2 adults</span>
                                <small style="color: #64748b; font-size: 11px;" id="displayRoomsCount">2 rooms</small>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="color: #94a3b8; font-size: 0.8rem;"></i>
                        </div>
                    </button>

                    <!-- Interactive Occupancy Stepper Popover Card (Anchored 6px below guest button) -->
                    <div id="agodaGuestsPopoverCard" style="display: none; position: absolute; top: 66px; right: 0; width: 320px; background: #ffffff; border-radius: 12px; box-shadow: 0 14px 36px rgba(0,0,0,0.25); padding: 20px; z-index: 99999; border: 1px solid #e2e8f0;">
                        <!-- Triangle Notch -->
                        <div style="position: absolute; top: -8px; right: 24px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 8px solid #ffffff;"></div>

                        <!-- Item 1: Rooms -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">Rooms</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusRoom">-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valRooms">2</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusRoom">+</button>
                            </div>
                        </div>

                        <!-- Item 2: Adults -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">Adults</span>
                                <small style="color: #64748b; font-size: 12px;">Ages 18 or above</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusAdult">-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valAdults">2</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusAdult">+</button>
                            </div>
                        </div>

                        <!-- Item 3: Children -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="font-weight: 700; color: #1e293b; font-size: 15px; display: block;">Children</span>
                                <small style="color: #64748b; font-size: 12px;">Ages 0-17</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <button type="button" class="agoda-stepper-btn" id="btnMinusChild" disabled>-</button>
                                <span style="font-weight: 700; font-size: 16px; color: #1e293b; width: 16px; text-align: center;" id="valChildren">0</span>
                                <button type="button" class="agoda-stepper-btn" id="btnPlusChild">+</button>
                            </div>
                        </div>

                        <!-- Dynamic Child Age Selector (Agoda Screenshot 5 Exact Parity) -->
                        <div id="childAgeContainer" style="display: none; margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 12px;"></div>

                    </div>
                </div>

            </div>

            <!-- Row 3: Checkbox Row -->
            <div style="display: flex; align-items: center; gap: 8px; margin-left: 2px; margin-top: 6px; margin-bottom: 18px;">
                <input type="checkbox" id="entireHomesAgodaMatchExact100" {{ request('entire_home') ? 'checked' : '' }} onchange="document.getElementById('inputEntireHome').value = this.checked ? 1 : 0;" style="width: 18px; height: 18px; border: 1px solid #94a3b8; border-radius: 3px; cursor: pointer;">
                <label for="entireHomesAgodaMatchExact100" style="font-size: 14px; color: #475569; font-weight: 500; cursor: pointer; margin: 0;">
                    Show me only entire homes and apartments
                </label>
            </div>

            <!-- Row 4: Agoda Blue SEARCH Button -->
            <button type="submit" class="agoda-search-submit-btn">
                SEARCH
            </button>

        </form>

        <!-- Form 2: Dedicated Airport Transfer Search Form (Exact Screenshot 4 Parity) -->
        <form action="{{ route('services') }}" method="GET" id="agodaFormAirport" style="display: none;" onsubmit="showAgodaSearchLoading();">
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
                            <span style="font-weight: 700; color: #1e293b; display: block; font-size: 13px; line-height: 1.2;">Pick-up date</span>
                            <small style="color: #64748b; font-size: 11px;">1 Sep 2026</small>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 12px;">
                            <span style="font-weight: 700; color: #1e293b; display: block; font-size: 13px; line-height: 1.2;"><i class="fa-regular fa-clock me-1"></i> 12:00 PM</span>
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
function selectDestination(name) {
    const input = document.getElementById('agodaDestinationInput');
    const popover = document.getElementById('agodaDestinationPopoverCard');
    const backdrop = document.getElementById('agodaSearchBackdropOverlay');
    if (input) input.value = name;
    if (popover) popover.style.display = 'none';
    if (backdrop) backdrop.style.display = 'none';
}

let isSelectingCheckOut = false;
let checkInVal = "{{ request('check_in', date('Y-m-d')) }}";
let checkOutVal = "{{ request('check_out', date('Y-m-d', strtotime('+7 days'))) }}";

let currentNavYear = new Date().getFullYear();
let currentNavMonth = new Date().getMonth();

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

    let offset = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

    let html = `
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: 700; color: #475569; font-size: 13px; margin-bottom: 10px;">
            <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
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
            btnIn.querySelector('span').textContent = formattedStr;
            btnIn.querySelector('small').textContent = dayName;
            btnIn.classList.remove('active-border');
        }
        if (btnOut) btnOut.classList.add('active-border');
    } else {
        checkOutVal = dateStr;
        isSelectingCheckOut = false;
        if (inputOut) inputOut.value = dateStr;
        if (btnOut) {
            btnOut.querySelector('span').textContent = formattedStr;
            btnOut.querySelector('small').textContent = dayName;
            btnOut.classList.remove('active-border');
        }
        if (calendarCard) calendarCard.style.display = 'none';
        if (backdrop) backdrop.style.display = 'none';
    }
    renderAgodaDynamicCalendar();
}

document.addEventListener('DOMContentLoaded', function() {
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

    let currentRooms = 2;
    let currentAdults = 2;
    let currentChildren = 0;

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
    }

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
            if (guestsCard) guestsCard.style.display = 'none';
        });
    }

    // DOM Element Declarations
    const searchTypeInput = document.getElementById('agodaSearchTypeInput');
    const formHotels = document.getElementById('agodaFormStandard');
    const formAirport = document.getElementById('agodaFormAirport');

    // Agoda-Exact Live Typing Autocomplete Handler
    let searchDebounce = null;
    if (destInput) {
        destInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounce);
            const query = e.target.value.trim();
            const currentType = searchTypeInput ? searchTypeInput.value : 'hotel';
            const staticBox = document.getElementById('agodaStaticSearchSuggestions');
            const liveBox = document.getElementById('agodaLiveSearchResultsContainer');

            if (query.length === 0) {
                if (staticBox) staticBox.style.display = 'block';
                if (liveBox) liveBox.style.display = 'none';
                return;
            }

            searchDebounce = setTimeout(() => {
                fetch('/api/v1/search/suggestions?q=' + encodeURIComponent(query) + '&search_type=' + currentType)
                    .then(res => res.json())
                    .then(res => {
                        const apiData = res.data || res;
                        if (staticBox) staticBox.style.display = 'none';
                        if (liveBox) liveBox.style.display = 'block';
                        renderLiveSuggestions(apiData, query);
                    })
                    .catch(err => {
                        // Still show client-side results even if API fails
                        renderLiveSuggestions({ locations: [], properties: [] }, query);
                    });
            }, 180);
        });
    }

    // ============================================================
    // CLIENT-SIDE Bangladesh Cities — always works, no server needed
    // ============================================================
    // ============================================================
    // CLIENT-SIDE Destinations Database — Agoda 1:1 Parity
    // ============================================================
    const BD_CITIES = [
        { city: 'Dhaka',                  country: 'Bangladesh', type: 'City' },
        { city: "Cox's Bazar",            country: 'Bangladesh', type: 'City' },
        { city: 'Sylhet',                 country: 'Bangladesh', type: 'City' },
        { city: 'Chittagong',             country: 'Bangladesh', type: 'City' },
        { city: 'Khulna',                 country: 'Bangladesh', type: 'City' },
        { city: 'Sreemangal',             country: 'Bangladesh', type: 'City' },
        { city: 'Sajek Valley',           country: 'Bangladesh', type: 'Area' },
        { city: 'Sundarbans',             country: 'Bangladesh', type: 'Region' },
        { city: 'Kuakata',                country: 'Bangladesh', type: 'City' },
        { city: 'Bandarban',              country: 'Bangladesh', type: 'Region' },
        { city: 'Tanguar Haor',           country: 'Bangladesh', type: 'Region' },
        { city: "Saint Martin's Island",  country: 'Bangladesh', type: 'Island' },
        { city: 'Rajshahi',               country: 'Bangladesh', type: 'City' },
        { city: 'Barisal',                country: 'Bangladesh', type: 'City' },
        { city: 'Rangamati',              country: 'Bangladesh', type: 'City' },
        { city: 'Bagla',                  country: 'India',      type: 'City' },
        { city: 'Baglarbasi',             country: 'Yalova',     type: 'Area' },
        { city: 'Baglio Messina',         country: 'Custonaci',  type: 'Area' },
        { city: 'Bangkok',                country: 'Thailand',   type: 'City' },
        { city: 'Kuala Lumpur',           country: 'Malaysia',   type: 'City' },
        { city: 'Singapore',              country: 'Singapore',  type: 'City' },
    ];

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

        // ── 1. City / Location Suggestions (Agoda 1:1 Match) ───────
        const clientCities = matchClientCities(query || '');
        const serverLocs = (data.locations || []).filter(sl =>
            !clientCities.some(cc => cc.city.toLowerCase() === (sl.city || '').toLowerCase())
        );
        const allLocations = [...clientCities, ...serverLocs].slice(0, 8);

        if (allLocations.length > 0) {
            allLocations.forEach(loc => {
                const cityName  = loc.city || loc.title || '';
                const country   = loc.country || 'Bangladesh';
                const locType   = loc.type || loc.loc_type || 'City';
                const fullTitle = cityName + ', ' + country;
                const safeTitle = fullTitle.replace(/'/g, "\\'");
                const highlightedFullTitle = highlightMatch(fullTitle, query);

                html += `
                    <div class="agoda-popover-item" onclick="selectDestination('${safeTitle}')" style="padding: 12px 24px; cursor: pointer; display: flex; align-items: center; gap: 16px; transition: background 0.12s ease;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                        <i class="fa-solid fa-location-dot" style="font-size: 19px; color: #262626; width: 22px; text-align: center; flex-shrink: 0;"></i>
                        <div style="flex: 1; min-width: 0; text-align: left;">
                            <div style="font-size: 14.5px; color: #262626; font-weight: 400; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                ${highlightedFullTitle}
                            </div>
                            <div style="color: #737373; font-size: 12px; font-weight: 400; text-align: left; margin-top: 2px;">${locType}</div>
                        </div>
                    </div>
                `;
            });
        }

        // ── 2. Matching Properties ───────────────────────────────────────────────
        const properties = data.properties || [];
        if (properties.length > 0) {
            html += `
                <div style="padding: 12px 24px 6px; font-size: 11px; font-weight: 700; color: #737373; letter-spacing: 0.8px; text-transform: uppercase; margin-top: 6px; border-top: 1px solid #f1f5f9; text-align: left;">
                    MATCHING PROPERTIES & CRUISES
                </div>
            `;
            properties.forEach(p => {
                const img      = p.primary_image || null;
                const safeName = (p.name || '').replace(/'/g, "\\'");
                const propType = p.type ? (p.type.charAt(0).toUpperCase() + p.type.slice(1)) : 'Property';
                const fullPropTitle = p.name + (p.city ? ', ' + p.city + ', Bangladesh' : '');
                const highlightedPropTitle = highlightMatch(fullPropTitle, query);

                let leftIconHtml = `<i class="fa-solid fa-hotel" style="font-size: 18px; color: #262626; width: 22px; text-align: center; flex-shrink: 0;"></i>`;
                if (img) {
                    leftIconHtml = `<img src="${img}" style="width: 38px; height: 38px; border-radius: 6px; object-fit: cover; flex-shrink: 0;">`;
                }

                html += `
                    <div class="agoda-popover-item" onclick="selectDestination('${safeName}')" style="padding: 12px 24px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 16px; transition: background 0.12s ease;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                        <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0; text-align: left;">
                            ${leftIconHtml}
                            <div style="flex: 1; min-width: 0; text-align: left;">
                                <div style="font-size: 14.5px; color: #262626; font-weight: 400; line-height: 1.3; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${highlightedPropTitle}</div>
                                <div style="color: #737373; font-size: 12px; font-weight: 400; margin-top: 2px; text-align: left;">${propType}</div>
                            </div>
                        </div>
                        ${p.price_per_night ? `
                            <div style="text-align: right; flex-shrink: 0;">
                                <span style="font-size: 14px; font-weight: 700; color: #2067e1; display: block;">BDT ${Number(p.price_per_night).toLocaleString()}</span>
                                <span style="font-size: 10px; color: #737373; font-weight: 400; display: block;">/ night</span>
                            </div>
                        ` : ''}
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
    const tabLongStays = document.getElementById('tabLongStays');
    const tabAirport = document.getElementById('tabAirport');

    const heroTitle = document.getElementById('bdHeroTitle');

    function setActiveTab(activeBtn, searchType, titleText, isAirport = false) {
        [tabHotels, tabHomes, tabLongStays, tabAirport].forEach(btn => {
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
        setActiveTab(tabHotels, 'hotel', 'HOTELS, RESORTS & HOMES IN BANGLADESH', false);
    });
    if (tabHomes) tabHomes.addEventListener('click', function() {
        setActiveTab(tabHomes, 'boat', 'SUNDARBAN SHIPS & TANGUAR HAOR HOUSEBOATS', false);
        const destInput = document.getElementById('agodaDestinationInput');
        if (destInput && !destInput.value) destInput.value = 'Sundarbans, Bangladesh';
    });
    if (tabLongStays) tabLongStays.addEventListener('click', function() {
        setActiveTab(tabLongStays, 'homestay', 'BOOK A HOME STAY IN BANGLADESH', false);
        const checkbox = document.getElementById('entireHomesAgodaMatchExact100');
        const inputEntire = document.getElementById('inputEntireHome');
        if (checkbox) checkbox.checked = true;
        if (inputEntire) inputEntire.value = 1;
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
</script>
