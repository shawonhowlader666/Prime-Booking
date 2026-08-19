{{-- Agoda.com 100% Identical Header Navigation — Single Row, Exact Spacing & Gaps --}}
<header class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-0" style="min-height: 72px; height: 72px; z-index: 1050; border-color: #e5e7eb !important; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
    <div class="container-fluid" style="height: 72px; max-width: 1360px; margin: 0 auto; padding-left: 12px; padding-right: 12px; display: flex; align-items: center;">

        {{-- 100% Identical Prime Booking Logo --}}
        <a class="navbar-brand d-flex align-items-center flex-shrink-0" href="{{ route('home') }}" style="text-decoration: none; padding: 0; margin-right: 32px !important;">
            <x-logo height="60" />
        </a>

        {{-- Mobile toggle (Triggers Dark Glass Modal) --}}
        <button class="navbar-toggler border-0 ms-auto shadow-none d-lg-none" type="button" data-bs-toggle="modal" data-bs-target="#agodaMobileDarkGlassModal">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Desktop Nav Row --}}
        <div class="collapse navbar-collapse py-0 d-none d-lg-flex" id="agodaMainNav" style="height: 72px;">

            {{-- Left Nav Links (Professional gaps between all items) --}}
            <ul class="navbar-nav align-items-center flex-nowrap mb-0" style="font-size: 15px; white-space: nowrap; gap: 28px;">
                <li class="nav-item">
                    <a class="nav-link px-0 fw-semibold" href="{{ route('home') }}"
                       style="color: #2d2d2d; height: 72px; display: flex; align-items: center; font-size: 15px;">
                        {{ __('Hotels & Homes') }}
                    </a>
                </li>

                {{-- Transport Dropdown with Red "New!" Badge --}}
                <li class="nav-item dropdown position-relative">
                    <div style="position: absolute; top: 12px; left: -2px; z-index: 10; pointer-events: none;">
                        <div style="background-color: #d91b42; color: #ffffff; font-size: 9px; font-weight: 800; border-radius: 3px; padding: 1px 5px; line-height: 1.1; box-shadow: 0 1px 3px rgba(217, 27, 66, 0.3);">
                            New!
                            <div style="position: absolute; bottom: -3px; left: 4px; width: 0; height: 0; border-left: 3px solid #d91b42; border-bottom: 3px solid transparent;"></div>
                        </div>
                    </div>
                    <a class="nav-link dropdown-toggle px-0" href="#" role="button" data-bs-toggle="dropdown"
                       style="color: #475569; height: 72px; display: flex; align-items: center; padding-top: 4px; font-size: 15px; font-weight: 500;">
                        {{ __('Transport') }}
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg p-2 mt-0" style="min-width: 190px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;">
                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('flights.index') }}">Domestic Flights</a></li>
                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('services') }}#bus">Express Buses</a></li>
                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('services') }}#train">Railway Trains</a></li>
                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('services') }}#ferry">Launch &amp; Ferries</a></li>
                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('transfers.index') }}">Airport Transfer</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-0" href="{{ route('packages') }}"
                       style="color: #475569; height: 72px; display: flex; align-items: center; font-size: 15px; font-weight: 500;">{{ __('Things to do') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0" href="{{ route('services') }}"
                       style="color: #475569; height: 72px; display: flex; align-items: center; font-size: 15px; font-weight: 500;">{{ __('Coupons & Deals') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0" href="{{ route('search.index') }}?type=apartment"
                       style="color: #475569; height: 72px; display: flex; align-items: center; font-size: 15px; font-weight: 500;">{{ __('Apartments') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 fw-bold" href="#"
                       style="color: #475569; height: 72px; display: flex; align-items: center; font-size: 15px; letter-spacing: 1px;">•••</a>
                </li>
            </ul>

            {{-- Right Controls (Agoda Screenshot 100% Exact 1:1 Matching Spacing & Height) --}}
            <div class="d-flex align-items-center ms-auto flex-shrink-0" style="gap: 24px; white-space: nowrap; height: 72px;">

                @php
                    $currentCurrency = \App\Helpers\CurrencyHelper::current();
                    $currencyFlag = match($currentCurrency) {
                        'USD' => 'us',
                        'EUR' => 'eu',
                        'GBP' => 'gb',
                        'SGD' => 'sg',
                        'MYR' => 'my',
                        'THB' => 'th',
                        'INR' => 'in',
                        'AED' => 'ae',
                        'SAR' => 'sa',
                        default => 'bd', // Default Bangladesh Flag 🇧🇩
                    };
                @endphp

                {{-- 1. Flag & Currency Badge (Agoda Image Exact: Flag + Currency Text) --}}
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="modal" data-bs-target="#agodaLanguageModal" style="color: #262626; font-weight: 600; font-size: 14.5px; cursor: pointer; padding: 2px 4px;">
                    @if($currencyFlag === 'bd')
                        <img src="https://flagcdn.com/w40/bd.png" alt="BD" style="width: 22px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                    @elseif($currencyFlag === 'us')
                        <img src="https://flagcdn.com/w40/us.png" alt="US" style="width: 22px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                    @else
                        <img src="https://flagcdn.com/w40/{{ $currencyFlag }}.png" alt="{{ $currencyFlag }}" style="width: 22px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                    @endif
                    <span style="color: #262626; font-weight: 600; font-size: 14.5px;">{{ $currentCurrency }}</span>
                </a>

                @auth
                {{-- Logged in state: Avatar + Name + VIP Badge (Matching Agoda Screenshot 1:1) --}}
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor: pointer; padding: 2px 0;">
                        <div style="width: 34px; height: 34px; background-color: #ff5722; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; box-shadow: 0 1px 3px rgba(255, 87, 34, 0.25);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="line-height: 1.15; flex-shrink: 0; text-align: left;">
                            <span style="font-size: 13.5px; font-weight: 600; color: #262626; display: block;">{{ auth()->user()->name }}</span>
                            <div style="display: inline-flex; align-items: center; border-radius: 3px; overflow: hidden; height: 16px; font-size: 10px; line-height: 1; margin-top: 1px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                <div style="background-color: #1b2028; color: #ffffff; padding: 0 4px 0 4px; height: 100%; display: flex; align-items: center; gap: 2px; font-weight: 800; clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%); padding-right: 9px;">
                                    <span style="font-size: 7px; color: #ffffff;">★</span>VIP
                                </div>
                                <div style="background: linear-gradient(135deg, #d98662 0%, #bd6c48 100%); color: #1e293b; padding: 0 4px 0 4px; height: 100%; display: flex; align-items: center; font-weight: 700; margin-left: -4px;">
                                    Bronze
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-2"
                         style="width: 280px; border-radius: 14px; box-shadow: 0 14px 40px rgba(0,0,0,0.25) !important;">
                        <div class="p-3">
                            <a href="{{ route('trips') }}" class="dropdown-item py-2 fw-bold text-dark fs-6 rounded-2 mb-1"><i class="fa-solid fa-calendar-check text-primary me-2"></i> {{ __('My Trips') }}</a>
                            <a href="{{ route('booking.history') }}" class="dropdown-item py-2 fw-bold text-dark fs-6 rounded-2 mb-1"><i class="fa-solid fa-suitcase text-primary me-2"></i> {{ __('All Bookings') }}</a>
                            <a href="{{ route('vip') }}" class="dropdown-item py-2 fw-semibold text-secondary rounded-2 mb-1"><i class="fa-solid fa-star text-warning me-2"></i> {{ __('PrimeVIP') }}</a>
                            <a href="{{ route('cashback') }}" class="dropdown-item py-2 fw-semibold text-secondary rounded-2 mb-1"><i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> {{ __('PrimeCash & Rewards') }}</a>
                            <a href="{{ route('profile') }}" class="dropdown-item py-2 fw-semibold text-secondary rounded-2 mb-1"><i class="fa-solid fa-user me-2 text-dark"></i> {{ __('Account Profile') }}</a>
                            <hr class="my-2 border-gray-200">
                            <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 fw-bold text-danger rounded-2">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> {{ __('Sign Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Cash Balance Dropdown Badge ((a) BDT 0 ▾) Matching Agoda Screenshot 1:1 --}}
                <div class="dropdown">
                    <button class="btn p-0 border-0 d-flex align-items-center gap-1 dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            style="color: #6366f1; font-weight: 600; font-size: 14px; height: 72px;">
                        <div style="width: 19px; height: 19px; background: #6366f1; color: #ffffff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; margin-right: 2px;">a</div>
                        <span style="color: #6366f1; font-weight: 600; font-size: 14px;">{{ $currentCurrency }} 0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-3 mt-2"
                         style="min-width: 250px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;">
                        <li class="p-2 border-bottom mb-2">
                            <div class="small text-secondary fw-medium">Prime Cash Balance</div>
                            <div class="h6 mb-0 fw-bold text-primary">{{ $currentCurrency }} 0.00</div>
                        </li>
                        <li><a class="dropdown-item py-2 rounded-2 fw-medium" href="{{ route('cashback') }}"><i class="fa-solid fa-wallet me-2 text-success"></i> PrimeCash Rewards</a></li>
                        <li><a class="dropdown-item py-2 rounded-2 fw-medium" href="{{ route('booking.history') }}"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> My Bookings</a></li>
                        <li><a class="dropdown-item py-2 rounded-2 fw-medium" href="{{ route('wishlist') }}"><i class="fa-solid fa-heart me-2 text-danger"></i> Saved Properties</a></li>
                        <li><a class="dropdown-item py-2 rounded-2 fw-medium" href="{{ route('profile') }}"><i class="fa-solid fa-gear me-2 text-secondary"></i> Account Settings</a></li>
                    </ul>
                </div>
                @else
                {{-- 2. "Sign in" Blue Link (Agoda Guest Image Exact) --}}
                <button type="button" class="btn p-0 border-0 fw-bold" data-bs-toggle="modal" data-bs-target="#agodaAuthModal" style="color: #2067e1; font-size: 14.5px; text-decoration: none; cursor: pointer;">
                    Sign in
                </button>

                {{-- 3. "Create account" Outlined Pill Button (Agoda Guest Image Exact) --}}
                <button type="button" class="btn btn-outline-primary rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#agodaAuthModal" style="color: #2067e1; border-color: #cbd5e1; border-width: 1px; padding: 6px 18px; font-size: 14px; background: transparent; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#2067e1'; this.style.backgroundColor='#f0f7ff'" onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='transparent'">
                    Create account
                </button>

                {{-- 4. Hamburger ☰ Menu Button (Only for Guests matching Screenshot 1 & 2) --}}
                <div class="dropdown position-relative">
                    <button class="btn p-0 border-0 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #2067e1; font-size: 18px; width: 30px; height: 30px; cursor: pointer; border-radius: 6px; transition: background 0.15s;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                        <i class="fa-solid fa-bars" style="color: #2067e1; font-size: 18px;"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-2"
                         style="width: 270px; border-radius: 14px; box-shadow: 0 12px 36px rgba(0,0,0,0.18) !important; overflow: hidden; text-align: left;">

                        <!-- Top Notch Speech Pointer -->
                        <div style="position: absolute; top: -6px; right: 12px; width: 12px; height: 12px; background: #ffffff; transform: rotate(45deg); border-top: 1px solid rgba(0,0,0,0.06); border-left: 1px solid rgba(0,0,0,0.06);"></div>

                        <div style="padding: 16px 18px 8px;">
                            <!-- My Trips -->
                            <a href="{{ route('trips') }}" class="d-block text-decoration-none fw-bold" style="color: #1e293b; font-size: 14px; margin-bottom: 16px; padding: 2px 0;">
                                {{ __('My Trips') }}
                            </a>

                            <!-- Sign in Header Text -->
                            <div style="font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 10px;">
                                {{ __('Sign in') }}
                            </div>

                            <!-- Big Outlined Sign In Pill Button -->
                            <button type="button" class="btn w-100 fw-bold mb-2" data-bs-toggle="modal" data-bs-target="#agodaAuthModal"
                                    style="color: #2067e1; border: 1px solid #cbd5e1; border-radius: 24px; padding: 7px 0; font-size: 14px; background: #ffffff; transition: all 0.15s ease;"
                                    onmouseover="this.style.borderColor='#2067e1'; this.style.background='#f0f7ff'"
                                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#ffffff'">
                                {{ __('Sign in') }}
                            </button>

                            <!-- Big Solid Blue Create Account Pill Button -->
                            <button type="button" class="btn w-100 fw-bold mb-3" data-bs-toggle="modal" data-bs-target="#agodaAuthModal"
                                    style="color: #ffffff; background: #2067e1; border-radius: 24px; padding: 8px 0; font-size: 14px; border: none; box-shadow: 0 4px 12px rgba(32, 103, 225, 0.3); transition: background 0.15s ease;"
                                    onmouseover="this.style.background='#1a56be'"
                                    onmouseout="this.style.background='#2067e1'">
                                {{ __('Create account') }}
                            </button>
                        </div>

                        <hr class="my-0" style="border-color: #f1f5f9;">

                        <!-- Settings Section (Agoda Exact) -->
                        <div style="padding: 14px 18px;">
                            <div style="font-weight: 700; color: #1e293b; font-size: 13.5px; margin-bottom: 12px;">
                                {{ __('Settings') }}
                            </div>

                            <!-- Language Row (Flag + English) -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#agodaLanguageModal" class="d-flex align-items-center gap-2 text-decoration-none mb-2" style="color: #1e293b; font-size: 13.5px; font-weight: 500;">
                                <img src="https://flagcdn.com/w40/bd.png" alt="BD" style="width: 22px; height: 15px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                <span>{{ app()->getLocale() == 'bn' ? 'বাংলা (Bengali)' : 'English' }}</span>
                            </a>

                            <!-- Currency Row (e.g. BDT Bangladeshi Taka / USD US Dollar) -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#agodaLanguageModal" class="d-flex align-items-center gap-2 text-decoration-none" style="color: #1e293b; font-size: 13.5px; font-weight: 500;">
                                <span style="font-weight: 700; color: #1e293b;">{{ $currentCurrency }}</span>
                                <span style="color: #64748b; font-size: 13px;">{{ $currentCurrency == 'BDT' ? 'Bangladeshi Taka' : 'US Dollar' }}</span>
                            </a>
                        </div>

                        <hr class="my-0" style="border-color: #f1f5f9;">

                        <!-- List Your Place On Agoda / Prime Booking (Agoda Exact) -->
                        <div style="padding: 14px 18px 16px;">
                            <div style="font-weight: 700; color: #1e293b; font-size: 13.5px; margin-bottom: 4px;">
                                {{ __('List your place on Prime Booking') }}
                            </div>
                            <div style="font-size: 11.5px; color: #64748b; line-height: 1.35; margin-bottom: 6px;">
                                {{ __('Earn money to pay for your travel!') }}
                            </div>
                            <a href="{{ route('vendor.dashboard') }}" class="text-decoration-none fw-bold" style="color: #2067e1; font-size: 13px;">
                                {{ __('List your place') }}
                            </a>
                        </div>

                    </div>
                </div>
                @endauth


            </div>
        </div>
    </header>

    <!-- Dark Glassmorphism Fullscreen Mobile Menu Overlay -->
    <div class="modal fade" id="agodaMobileDarkGlassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0" style="background: rgba(15, 23, 42, 0.92) !important; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
                <!-- Modal Header -->
                <div class="modal-header border-bottom border-secondary border-opacity-25 px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <x-logo mode="dark" height="38" />
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with Glass Cards -->
            <div class="modal-body p-4 overflow-y-auto">
                <!-- User Profile Glass Card -->
                <div class="p-3 mb-4 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; background-color: #ff5722; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">S</div>
                        <div>
                            <h6 class="fw-bold text-white mb-0">Shawon .</h6>
                            <div style="display: inline-flex; align-items: center; border-radius: 3px; overflow: hidden; height: 18px; font-size: 10.5px; line-height: 1; box-shadow: 0 1px 2px rgba(0,0,0,0.2); vertical-align: middle; margin-top: 2px;">
                                <div style="background-color: #1b2028; color: #ffffff; padding: 0 5px 0 5px; height: 100%; display: flex; align-items: center; gap: 2px; font-weight: 800; clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%); padding-right: 12px;">
                                    <span style="font-size: 8px; color: #ffffff;">★</span>VIP
                                </div>
                                <div style="background: linear-gradient(135deg, #d98662 0%, #bd6c48 100%); color: #1e293b; padding: 0 6px 0 6px; height: 100%; display: flex; align-items: center; font-weight: 700; margin-left: -5px;">
                                    Bronze
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links Glass Cards -->
                <h6 class="text-white-50 text-uppercase fw-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">Main Menu Navigation</h6>
                <div class="d-flex flex-column gap-2 mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none p-3 rounded-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="fw-semibold" style="font-size: 15px;"><i class="fa-solid fa-hotel me-3 text-primary"></i> Hotels &amp; Homes</span>
                        <i class="fa-solid fa-chevron-right small text-white-50"></i>
                    </a>
                    <a href="{{ route('packages') }}" class="text-decoration-none p-3 rounded-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="fw-semibold" style="font-size: 15px;"><i class="fa-solid fa-plane-departure me-3 text-warning"></i> Tour &amp; Holiday Packages</span>
                        <i class="fa-solid fa-chevron-right small text-white-50"></i>
                    </a>
                    <a href="{{ route('services') }}" class="text-decoration-none p-3 rounded-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="fw-semibold" style="font-size: 15px;"><i class="fa-solid fa-bus me-3 text-info"></i> Express Buses &amp; Transport</span>
                        <i class="fa-solid fa-chevron-right small text-white-50"></i>
                    </a>
                    <a href="{{ route('services') }}" class="text-decoration-none p-3 rounded-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="fw-semibold" style="font-size: 15px;"><i class="fa-solid fa-tag me-3 text-success"></i> Coupons &amp; Deals</span>
                        <i class="fa-solid fa-chevron-right small text-white-50"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="text-decoration-none p-3 rounded-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="fw-semibold" style="font-size: 15px;"><i class="fa-solid fa-headset me-3 text-danger"></i> Contact &amp; Support</span>
                        <i class="fa-solid fa-chevron-right small text-white-50"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</header>

{{-- ======================================================= --}}
{{-- Agoda-Exact Sticky Dark Navy Search Bar (on scroll)     --}}
{{-- ======================================================= --}}
<div id="agodaStickyBar"
     style="position: fixed; top: 0; left: 0; width: 100%; background-color: #1d2b45;
            z-index: 2000; padding: 10px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            transform: translateY(-100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);">

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">

        <!-- Desktop View (Large Screens >= 992px) -->
        <div class="d-none d-lg-flex align-items-center justify-content-between gap-2">
            {{-- ── Box 1: Destination ── --}}
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    style="flex: 2; min-width: 0; background: #ffffff; border: none; border-radius: 8px;
                           height: 44px; padding: 0 14px;
                           display: flex; align-items: center; gap: 10px;
                           cursor: pointer; text-align: left; outline: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span style="font-size: 14px; font-weight: 400; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ request('destination', 'Enter a destination or property') }}
                </span>
            </button>

            {{-- ── Box 2: Check-in ── --}}
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    style="flex: 1.2; min-width: 0; background: #ffffff; border: none; border-radius: 8px;
                           height: 44px; padding: 0 14px;
                           display: flex; align-items: center; gap: 10px;
                           cursor: pointer; text-align: left; outline: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <div style="line-height: 1.2;">
                    <span style="font-size: 13px; font-weight: 700; color: #1e293b; display: block;">1 Sep 2026</span>
                    <span style="font-size: 10px; color: #64748b;">Tuesday</span>
                </div>
            </button>

            {{-- ── Box 3: Check-out ── --}}
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    style="flex: 1.2; min-width: 0; background: #ffffff; border: none; border-radius: 8px;
                           height: 44px; padding: 0 14px;
                           display: flex; align-items: center; gap: 10px;
                           cursor: pointer; text-align: left; outline: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <div style="line-height: 1.2;">
                    <span style="font-size: 13px; font-weight: 700; color: #1e293b; display: block;">8 Sep 2026</span>
                    <span style="font-size: 10px; color: #64748b;">Tuesday</span>
                </div>
            </button>

            {{-- ── Box 4: Guests ── --}}
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    style="flex: 1; min-width: 0; background: #ffffff; border: none; border-radius: 8px;
                           height: 44px; padding: 0 14px;
                           display: flex; align-items: center; gap: 10px;
                           cursor: pointer; text-align: left; outline: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <div style="line-height: 1.2;">
                    <span style="font-size: 13px; font-weight: 700; color: #1e293b; display: block;">2 adults</span>
                    <span style="font-size: 10px; color: #64748b;">2 rooms</span>
                </div>
            </button>

            {{-- ── SEARCH button ── --}}
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    style="background-color: #2067e1; color: #ffffff; font-weight: 700; font-size: 14px;
                           border: none; border-radius: 8px; height: 44px; padding: 0 28px;
                           cursor: pointer; white-space: nowrap; flex-shrink: 0;
                           letter-spacing: 0.3px; box-shadow: 0 2px 6px rgba(32,103,225,0.3);">
                SEARCH
            </button>
        </div>

        <!-- Mobile & Tablet Native App Search Pill (< 992px) -->
        <div class="d-flex d-lg-none align-items-center justify-content-between gap-2">
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                    class="w-100 btn btn-light rounded-pill py-1.5 px-3 d-flex align-items-center justify-content-between text-start shadow-sm border-0" 
                    style="background: #ffffff;">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <i class="fa-solid fa-magnifying-glass text-primary fs-6"></i>
                    <div style="line-height: 1.2;" class="overflow-hidden text-truncate">
                        <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;">{{ request('destination', 'Search Hotels & Destinations') }}</span>
                        <span class="text-muted text-truncate d-block" style="font-size: 10px;">1 Sep - 8 Sep • 2 Adults, 2 Rooms</span>
                    </div>
                </div>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fw-bold flex-shrink-0" style="font-size: 11px;">Change</span>
            </button>
        </div>

    </div>
</div>

<script>
window.addEventListener('scroll', function () {
    const bar = document.getElementById('agodaStickyBar');
    if (!bar) return;
    bar.style.transform = window.scrollY > 280 ? 'translateY(0%)' : 'translateY(-100%)';
});
</script>

<!-- Select Your Language Modal (100% Agoda Exact Screenshot Parity) -->
<style>
.agoda-lang-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    height: 42px;
    padding: 0 12px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    color: #2d2d2d;
    background: transparent;
    border: 1px solid transparent;
    text-align: left;
    transition: all 0.15s ease;
    cursor: pointer;
}
.agoda-lang-btn:hover {
    background-color: #f1f5f9;
    border-color: #e2e8f0;
    color: #0f172a;
}
.agoda-lang-btn.active {
    border: 2px solid #2d2d2d !important;
    background-color: #ffffff !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.agoda-lang-flag {
    width: 24px;
    height: 16px;
    border-radius: 2px;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
}
.agoda-lang-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<div class="modal fade" id="agodaLanguageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 760px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 20px;">Select your language</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with 3 Column Layout from Screenshot -->
            <div class="modal-body px-4 pb-4 overflow-y-auto" style="max-height: 540px;">
                
                <!-- Select Currency Section (Agoda Image 2: 100% Exact 3-Column Clean Grid) -->
                <div class="mb-4 pb-3 border-bottom">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Select your currency</h6>
                    </div>
                    @php
                        $activeCurr = \App\Helpers\CurrencyHelper::current();
                        $currencies = [
                            ['code' => 'BDT', 'symbol' => '৳',   'name' => 'Bangladeshi Taka',    'flag' => 'bd'],
                            ['code' => 'USD', 'symbol' => '$',   'name' => 'US Dollar',           'flag' => 'us'],
                            ['code' => 'EUR', 'symbol' => '€',   'name' => 'Euro',                'flag' => 'eu'],
                            ['code' => 'GBP', 'symbol' => '£',   'name' => 'British Pound',       'flag' => 'gb'],
                            ['code' => 'SGD', 'symbol' => 'S$',  'name' => 'Singapore Dollar',    'flag' => 'sg'],
                            ['code' => 'MYR', 'symbol' => 'RM',  'name' => 'Malaysian Ringgit',   'flag' => 'my'],
                            ['code' => 'THB', 'symbol' => '฿',   'name' => 'Thai Baht',           'flag' => 'th'],
                            ['code' => 'INR', 'symbol' => '₹',   'name' => 'Indian Rupee',        'flag' => 'in'],
                            ['code' => 'AED', 'symbol' => 'AED', 'name' => 'Emirati Dirham',      'flag' => 'ae'],
                            ['code' => 'SAR', 'symbol' => 'SAR', 'name' => 'Saudi Riyal',         'flag' => 'sa'],
                            ['code' => 'CAD', 'symbol' => 'C$',  'name' => 'Canadian Dollar',     'flag' => 'ca'],
                            ['code' => 'AUD', 'symbol' => 'A$',  'name' => 'Australian Dollar',   'flag' => 'au'],
                            ['code' => 'CHF', 'symbol' => 'CHF', 'name' => 'Swiss Franc',         'flag' => 'ch'],
                            ['code' => 'RMB', 'symbol' => '¥',   'name' => 'Chinese Yuan',        'flag' => 'cn'],
                            ['code' => 'JPY', 'symbol' => '¥',   'name' => 'Japanese Yen',        'flag' => 'jp'],
                        ];
                    @endphp
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        @foreach($currencies as $c)
                        @php $isActive = ($activeCurr === $c['code']); @endphp
                        <a href="{{ route('currency.switch', $c['code']) }}"
                           class="text-decoration-none d-flex align-items-center justify-content-between"
                           style="padding: 10px 14px; border-radius: 8px; border: {{ $isActive ? '2px solid #2067e1' : '1px solid #e2e8f0' }}; background: {{ $isActive ? '#ffffff' : '#ffffff' }}; transition: all 0.15s ease;"
                           onmouseover="if(!{{ $isActive ? 1 : 0 }}) this.style.borderColor='#2067e1'"
                           onmouseout="if(!{{ $isActive ? 1 : 0 }}) this.style.borderColor='#e2e8f0'">
                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                @if($isActive)
                                <i class="fa-solid fa-check text-primary" style="font-size: 13px; flex-shrink: 0;"></i>
                                @endif
                                <span style="color: #2067e1; font-weight: 700; font-size: 13px; min-width: 32px;">{{ $c['code'] }}</span>
                                <span style="color: #475569; font-size: 12.5px; font-weight: {{ $isActive ? '700' : '400' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $c['name'] }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>


                <!-- Suggested languages Section -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">{{ __('Suggested languages') }}</h6>
                    <div class="row g-2">
                        <!-- English -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'en') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                @if(app()->getLocale() == 'en')
                                <i class="fa-solid fa-check text-primary" style="font-size: 12px; margin-right: 2px; flex-shrink: 0;"></i>
                                @endif
                                <img src="https://flagcdn.com/w40/us.png" alt="US" class="agoda-lang-flag">
                                <span class="agoda-lang-text">English</span>
                            </a>
                        </div>
                        <!-- Bengali -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'bn') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'bn' ? 'active' : '' }}">
                                @if(app()->getLocale() == 'bn')
                                <i class="fa-solid fa-check text-primary" style="font-size: 12px; margin-right: 2px; flex-shrink: 0;"></i>
                                @endif
                                <img src="https://flagcdn.com/w40/bd.png" alt="BD" class="agoda-lang-flag">
                                <span class="agoda-lang-text">বাংলা (Bengali)</span>
                            </a>
                        </div>
                        <!-- Korean -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'kr') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'kr' ? 'active' : '' }}">
                                @if(app()->getLocale() == 'kr')
                                <i class="fa-solid fa-check text-primary" style="font-size: 12px; margin-right: 2px; flex-shrink: 0;"></i>
                                @endif
                                <img src="https://flagcdn.com/w40/kr.png" alt="KR" class="agoda-lang-flag">
                                <span class="agoda-lang-text">한국어</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- All languages Section (3 Columns Grid matching screenshot) -->
                <div>
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">{{ __('All languages') }}</h6>
                    <div class="row g-2">
                        <!-- Bengali (BD) -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'bn') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'bn' ? 'active' : '' }}">
                                <img src="https://flagcdn.com/w40/bd.png" alt="BD" class="agoda-lang-flag">
                                <span class="agoda-lang-text">বাংলা (Bengali)</span>
                            </a>
                        </div>
                        <!-- India (Hindi) -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'hi') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'hi' ? 'active' : '' }}">
                                <img src="https://flagcdn.com/w40/in.png" alt="IN" class="agoda-lang-flag">
                                <span class="agoda-lang-text">हिन्दी (Hindi)</span>
                            </a>
                        </div>
                        <!-- Saudi Arabia (Arabic) -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'ar') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                <img src="https://flagcdn.com/w40/sa.png" alt="SA" class="agoda-lang-flag">
                                <span class="agoda-lang-text">العربية (Arabic)</span>
                            </a>
                        </div>
                        <!-- China (Simplified) -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'zh') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'zh' ? 'active' : '' }}">
                                <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="agoda-lang-flag">
                                <span class="agoda-lang-text">简体中文 (Chinese)</span>
                            </a>
                        </div>
                        <!-- Malaysia -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'ms') }}" class="agoda-lang-btn text-decoration-none {{ app()->getLocale() == 'ms' ? 'active' : '' }}">
                                <img src="https://flagcdn.com/w40/my.png" alt="MY" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Bahasa Melayu</span>
                            </a>
                        </div>
                        <!-- Singapore -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'en') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/sg.png" alt="SG" class="agoda-lang-flag">
                                <span class="agoda-lang-text">English (Singapore)</span>
                            </a>
                        </div>
                        <!-- Pakistan (Urdu) -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'ur') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/pk.png" alt="PK" class="agoda-lang-flag">
                                <span class="agoda-lang-text">اردو (Urdu)</span>
                            </a>
                        </div>
                        <!-- Nepal -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'ne') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/np.png" alt="NP" class="agoda-lang-flag">
                                <span class="agoda-lang-text">নেপালী (Nepali)</span>
                            </a>
                        </div>
                        <!-- Sri Lanka -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'en') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/lk.png" alt="LK" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Sri Lanka (සිංහල)</span>
                            </a>
                        </div>
                        <!-- Maldives -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'en') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/mv.png" alt="MV" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Maldives (Dhivehi)</span>
                            </a>
                        </div>
                        <!-- Philippines -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'en') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/ph.png" alt="PH" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Filipino (Tagalog)</span>
                            </a>
                        </div>
                        <!-- Thai -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'th') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/th.png" alt="TH" class="agoda-lang-flag">
                                <span class="agoda-lang-text">ภาษาไทย (Thai)</span>
                            </a>
                        </div>
                        <!-- Bahasa Indonesia -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'id') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/id.png" alt="ID" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Bahasa Indonesia</span>
                            </a>
                        </div>
                        <!-- Vietnamese -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'vn') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/vn.png" alt="VN" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Tiếng Việt</span>
                            </a>
                        </div>
                        <!-- Russian -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'ru') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/ru.png" alt="RU" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Русский</span>
                            </a>
                        </div>
                        <!-- Turkish -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'tr') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Türkçe</span>
                            </a>
                        </div>
                        <!-- French -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'fr') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/fr.png" alt="FR" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Français</span>
                            </a>
                        </div>
                        <!-- German -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'de') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/de.png" alt="DE" class="agoda-lang-flag">
                                <span class="agoda-lang-text">Deutsch</span>
                            </a>
                        </div>
                        <!-- Japanese -->
                        <div class="col-12 col-md-4">
                            <a href="{{ route('lang.switch', 'jp') }}" class="agoda-lang-btn text-decoration-none">
                                <img src="https://flagcdn.com/w40/jp.png" alt="JP" class="agoda-lang-flag">
                                <span class="agoda-lang-text">日本語 (Japanese)</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Agoda Auth Modal — working component with real form POST routes -->
<x-auth-modal />
