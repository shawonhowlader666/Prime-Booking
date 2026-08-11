@extends('layouts.main', ['activePage' => 'bookings'])

@section('title', 'All Bookings | Prime Booking')

@section('content')
{{-- Hero Subheader with Dark Gradient & Hotel Deals Graphic --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-hotel text-warning me-2" style="font-size: 20px;"></i> {{ __('All Bookings & Hotel Reservations') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Manage your upcoming stays, view check-in details, and request hotel services.') }}
            </p>
        </div>

        <!-- Right Side Hotel Graphic/Illustration -->
        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🏨</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Hotel Deals</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Up to 40% OFF</div>
                </div>
            </div>
            <div style="font-size: 40px; transform: rotate(8deg); filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                🏨
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <div class="bg-white border shadow-sm" style="border-color: #cbd5e1 !important; border-radius: 20px !important; padding: 20px 14px 28px 14px;">
                    <div class="d-flex flex-column" style="gap: 4px;">
                        
                        <!-- My Trips -->
                        <a href="{{ route('trips') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px; transition: all 0.15s ease;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-calendar-check text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('My Trips') }}</span>
                        </a>

                        <!-- All bookings (Active Blue Pill) -->
                        <a href="{{ route('bookings') }}" class="text-decoration-none d-flex align-items-center text-white fw-bold active-booking-tab" style="background-color: #2067e1; padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-suitcase text-white" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('All bookings') }}</span>
                        </a>

                        <!-- Hotels -->
                        <a href="{{ route('search.index') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-hotel text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('Hotels') }}</span>
                        </a>

                        <!-- Flights -->
                        <a href="{{ route('services') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-plane text-dark" style="font-size: 17px; transform: rotate(-45deg);"></i>
                            </div>
                            <span>{{ __('Flights') }}</span>
                        </a>

                        <!-- Activities -->
                        <a href="{{ route('search.index') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-icons text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('Activities') }}</span>
                        </a>

                        <!-- Property messages -->
                        <a href="{{ route('messages') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-comment-dots text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('Property messages') }}</span>
                        </a>

                        <!-- Reviews -->
                        <a href="{{ route('reviews') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-star text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('Reviews') }}</span>
                        </a>

                        <!-- PrimeVIP / AgodaVIP -->
                        <a href="{{ route('vip') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;"><i class="fa-solid fa-star"></i></span>
                            </div>
                            <span>PrimeVIP</span>
                        </a>

                        <!-- PrimeCash / AgodaCash -->
                        <a href="{{ route('cashback') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <span class="bg-dark text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">a</span>
                            </div>
                            <span>PrimeCash</span>
                        </a>

                        <!-- Cashback Rewards -->
                        <a href="{{ route('cashback') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-hand-holding-dollar text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>Cashback Rewards</span>
                        </a>

                        <!-- PointsMAX -->
                        <a href="{{ route('pointsmax') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <span class="bg-dark text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">P</span>
                            </div>
                            <span>PointsMAX</span>
                        </a>

                        <!-- Profile -->
                        <a href="{{ route('profile') }}" class="text-decoration-none d-flex align-items-center text-dark fw-bold" style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px;">
                            <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                                <i class="fa-solid fa-user text-dark" style="font-size: 17px;"></i>
                            </div>
                            <span>{{ __('Profile') }}</span>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Right Column: All Bookings Content Area (1:1 Exact Match from Screenshots 2, 3, 4, 5) -->
            <div class="col-lg-9 col-md-8">
                
                <!-- Top Navigation Back Link: < See all trips -->
                <div class="mb-3">
                    <a href="{{ route('trips') }}" class="text-decoration-none fw-bold" style="color: #2067e1; font-size: 14px;">
                        <i class="fa-solid fa-chevron-left me-1 small"></i> {{ __('See all trips') }}
                    </a>
                </div>

                <!-- Filter Tabs Header: Upcoming / Completed / Cancelled -->
                <div class="d-flex align-items-center border-bottom mb-4" style="border-color: #cbd5e1 !important;">
                    <button type="button" class="btn border-0 rounded-0 py-2.5 px-4 fw-bold position-relative active-booking-subtab" id="tabUpcoming" style="color: #2067e1; font-size: 15px; border-bottom: 3px solid #2067e1 !important;" onclick="switchBookingSubtab('upcoming')">
                        {{ __('Upcoming') }}
                    </button>
                    <button type="button" class="btn border-0 rounded-0 py-2.5 px-4 fw-semibold text-secondary position-relative" id="tabCompleted" style="font-size: 15px;" onclick="switchBookingSubtab('completed')">
                        {{ __('Completed') }}
                    </button>
                    <button type="button" class="btn border-0 rounded-0 py-2.5 px-4 fw-semibold text-secondary position-relative" id="tabCancelled" style="font-size: 15px;" onclick="switchBookingSubtab('cancelled')">
                        {{ __('Cancelled') }}
                    </button>
                </div>

                <!-- Control Bar: Sort by Check-in date + Search by booking ID -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <!-- Left: Sort Dropdown Button (1:1 Screenshot 5 Match) -->
                    <div class="dropdown position-relative">
                        <button class="btn btn-white border rounded-pill px-3 py-2 font-semibold text-dark dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="font-size: 13.5px; border-color: #cbd5e1 !important; background-color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <i class="fa-solid fa-sliders text-primary" style="font-size: 14px;"></i>
                            <span id="currentSortLabel" class="fw-semibold">Sort by: Check-in date</span>
                        </button>
                        <div class="dropdown-menu shadow-lg border-0 p-3 mt-2" style="width: 220px; border-radius: 14px; box-shadow: 0 12px 35px rgba(0,0,0,0.18) !important;">
                            <!-- Pointer triangle -->
                            <div style="position: absolute; top: -6px; left: 24px; width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-bottom: 6px solid #ffffff;"></div>
                            
                            <div class="d-flex flex-column gap-2" style="font-size: 14px;">
                                <div class="dropdown-item p-2 rounded-3 d-flex align-items-center gap-2 text-dark" style="cursor: pointer;" onclick="selectSortOption('Booking date')">
                                    <i class="fa-regular fa-circle text-secondary" id="radioBookingDate" style="font-size: 16px;"></i>
                                    <span class="fw-medium">Booking date</span>
                                </div>
                                <div class="dropdown-item p-2 rounded-3 d-flex align-items-center gap-2 text-dark fw-bold" style="cursor: pointer;" onclick="selectSortOption('Check-in date')">
                                    <i class="fa-solid fa-circle-dot text-primary" id="radioCheckinDate" style="font-size: 16px;"></i>
                                    <span>Check-in date</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Search input Search by booking ID -->
                    <div class="position-relative" style="width: 260px;">
                        <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size: 13px;"></i>
                        <input type="text" class="form-control rounded-pill ps-5 pe-3 py-1.5" placeholder="Search by booking ID" style="font-size: 13px; border-color: #cbd5e1;">
                    </div>
                </div>

                <!-- Help Alert Notification Card (Exact Match Screenshot) -->
                <div class="card border rounded-3 p-3 mb-5 shadow-xs position-relative" style="background-color: #f0f9ff; border-color: #bae6fd !important; border-radius: 14px !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 36px; height: 36px; background-color: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13.5px;">Help us reach you when it matters</h6>
                                <p class="mb-0 text-secondary" style="font-size: 12.5px;">Leave us your phone number, so we can reach you faster with urgent updates that may affect your travel plans.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 12.5px; border-color: #2067e1; color: #2067e1;" data-bs-toggle="modal" data-bs-target="#agodaAuthModal">
                                Add Phone number
                            </button>
                            <button type="button" class="btn-close ms-2" onclick="this.closest('.card').remove()"></button>
                        </div>
                    </div>
                </div>

                <!-- Empty State Content Box -->
                <div class="text-center py-4 mb-5">
                    <!-- Globe Graphic with Plane -->
                    <div class="mb-3 d-inline-block">
                        <div style="font-size: 75px; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.08));">
                            ✈️🌍
                        </div>
                    </div>

                    <h4 class="fw-bold mb-1" id="emptyStateTitle" style="font-size: 20px; color: #1e293b;">
                        Shawon, you have no upcoming bookings
                    </h4>
                    <p class="text-secondary mb-4" style="font-size: 13.5px;">
                        Start planning your next trip!
                    </p>

                    <!-- 3-Column Bottom Quick Action Recommendation Cards (1:1 Screenshot 2 Match) -->
                    <div class="row g-3 justify-content-center" style="max-width: 650px; margin: 0 auto;">
                        
                        <!-- Card 1: Place to stay? -->
                        <div class="col-md-4 col-4">
                            <a href="{{ route('home') }}" class="card border text-decoration-none h-100 shadow-xs hover-lift bg-white overflow-hidden" style="border-color: #cbd5e1 !important; border-radius: 14px !important; transition: all 0.2s ease;">
                                <div style="background-color: #eff6ff; padding: 22px 0;" class="d-flex justify-content-center align-items-center">
                                    <i class="fa-solid fa-hotel text-primary" style="font-size: 34px;"></i>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="fw-bold" style="color: #2067e1; font-size: 13px;">Place to stay?</div>
                                </div>
                            </a>
                        </div>

                        <!-- Card 2: Need a flight? -->
                        <div class="col-md-4 col-4">
                            <a href="{{ route('services') }}" class="card border text-decoration-none h-100 shadow-xs hover-lift bg-white overflow-hidden" style="border-color: #cbd5e1 !important; border-radius: 14px !important; transition: all 0.2s ease;">
                                <div style="background-color: #eff6ff; padding: 22px 0;" class="d-flex justify-content-center align-items-center">
                                    <i class="fa-solid fa-plane text-primary" style="font-size: 34px;"></i>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="fw-bold" style="color: #2067e1; font-size: 13px;">Need a flight?</div>
                                </div>
                            </a>
                        </div>

                        <!-- Card 3: Things to do? -->
                        <div class="col-md-4 col-4">
                            <a href="{{ route('search.index') }}" class="card border text-decoration-none h-100 shadow-xs hover-lift bg-white overflow-hidden" style="border-color: #cbd5e1 !important; border-radius: 14px !important; transition: all 0.2s ease;">
                                <div style="background-color: #eff6ff; padding: 22px 0;" class="d-flex justify-content-center align-items-center">
                                    <i class="fa-solid fa-monument text-primary" style="font-size: 34px;"></i>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="fw-bold" style="color: #2067e1; font-size: 13px;">Things to do?</div>
                                </div>
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<script>
function switchBookingSubtab(tabName) {
    const tabs = ['Upcoming', 'Completed', 'Cancelled'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab' + t);
        if (btn) {
            if (t.toLowerCase() === tabName) {
                btn.style.color = '#2067e1';
                btn.style.borderBottom = '3px solid #2067e1';
                btn.classList.add('fw-bold');
                btn.classList.remove('fw-semibold', 'text-secondary');
            } else {
                btn.style.color = '#64748b';
                btn.style.borderBottom = 'none';
                btn.classList.remove('fw-bold');
                btn.classList.add('fw-semibold', 'text-secondary');
            }
        }
    });

    const emptyTitle = document.getElementById('emptyStateTitle');
    if (emptyTitle) {
        if (tabName === 'cancelled') {
            emptyTitle.innerText = 'Shawon, you have no recent cancelled bookings';
        } else if (tabName === 'completed') {
            emptyTitle.innerText = 'Shawon, you have no completed bookings';
        } else {
            emptyTitle.innerText = 'Shawon, you have no upcoming bookings';
        }
    }
}

function selectSortOption(sortText) {
    const label = document.getElementById('currentSortLabel');
    if (label) {
        label.innerText = 'Sort by: ' + sortText;
    }
    const rBooking = document.getElementById('radioBookingDate');
    const rCheckin = document.getElementById('radioCheckinDate');
    if (rBooking && rCheckin) {
        if (sortText === 'Booking date') {
            rBooking.className = 'fa-solid fa-circle-dot text-primary';
            rCheckin.className = 'fa-regular fa-circle text-secondary';
        } else {
            rCheckin.className = 'fa-solid fa-circle-dot text-primary';
            rBooking.className = 'fa-regular fa-circle text-secondary';
        }
    }
}
</script>
@endsection
