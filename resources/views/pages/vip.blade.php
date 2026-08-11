@extends('layouts.main', ['activePage' => 'vip'])

@section('title', 'PrimeVIP Loyalty Program | Prime Booking')

@section('content')
<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 260px;">
                <x-user-sidebar activePage="vip" />
            </div>

            <!-- Right Area: PrimeVIP Status & Tier Stepper Track -->
            <div class="col-lg-9 col-md-8">
                
                @php
                    $user = auth()->user();
                    $userBookings = $user ? \App\Models\Booking::where('user_id', $user->id)->where('created_at', '>=', now()->subYears(2))->count() : 0;
                    
                    $silverReq   = $vipThresholds['silver'] ?? 2;
                    $goldReq     = $vipThresholds['gold'] ?? 5;
                    $platReq     = $vipThresholds['platinum'] ?? 10;
                    $diamondReq  = $vipThresholds['diamond'] ?? 15;

                    if ($userBookings >= $diamondReq) {
                        $currentTier = 'Diamond';
                        $nextTierName = null;
                        $remaining = 0;
                        $progressPercent = 100;
                    } elseif ($userBookings >= $platReq) {
                        $currentTier = 'Platinum';
                        $nextTierName = 'Diamond';
                        $remaining = $diamondReq - $userBookings;
                        $progressPercent = 80 + (($userBookings - $platReq) / ($diamondReq - $platReq) * 20);
                    } elseif ($userBookings >= $goldReq) {
                        $currentTier = 'Gold';
                        $nextTierName = 'Platinum';
                        $remaining = $platReq - $userBookings;
                        $progressPercent = 50 + (($userBookings - $goldReq) / ($platReq - $goldReq) * 30);
                    } elseif ($userBookings >= $silverReq) {
                        $currentTier = 'Silver';
                        $nextTierName = 'Gold';
                        $remaining = $goldReq - $userBookings;
                        $progressPercent = 25 + (($userBookings - $silverReq) / ($goldReq - $silverReq) * 25);
                    } else {
                        $currentTier = 'Bronze';
                        $nextTierName = 'Silver';
                        $remaining = $silverReq - $userBookings;
                        $progressPercent = max(5, ($userBookings / $silverReq) * 25);
                    }
                @endphp

                <!-- VIP Welcome Hero Banner (Slim & Compact) -->
                <div class="card border-0 rounded-4 text-white p-3.5 p-md-4 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); border-radius: 16px !important; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);">
                    <div class="position-relative z-1">
                        <div class="d-inline-flex align-items-center mb-2" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border-radius: 999px; padding: 3px 12px; border: 1px solid rgba(255,255,255,0.15);">
                            <span class="badge me-2" style="background: linear-gradient(135deg, #d98662 0%, #bd6c48 100%); color: #fff; font-size: 11px;">★ VIP</span>
                            <span class="fw-bold" style="font-size: 12.5px; color: #f8fafc;">{{ $currentTier }} Member Status</span>
                        </div>

                        <h4 class="fw-bold mb-1" style="color: #ffffff !important; font-size: 20px; letter-spacing: -0.3px; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                            Welcome to <span style="color: #ecc43a;">PrimeVIP</span> Rewards, {{ $user->name ?? 'Traveler' }}!
                        </h4>
                        <p class="mb-3" style="max-width: 580px; font-size: 13.5px; color: #e2e8f0 !important; opacity: 0.95;">
                            Unlock up to {{ $vipDiscounts[strtolower($currentTier)] ?? 20 }}% off hotel bookings, complimentary breakfasts, VIP customer support, and automatic room upgrades as you level up.
                        </p>

                        <!-- Stepper Progress Track -->
                        <div class="bg-dark bg-opacity-50 p-4 rounded-4 border border-secondary border-opacity-25 mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-6">Your Progress: {{ $userBookings }} completed booking{{ $userBookings != 1 ? 's' : '' }}</span>
                                @if($nextTierName)
                                    <span class="text-warning fw-bold">{{ $remaining }} booking{{ $remaining != 1 ? 's' : '' }} left to {{ $nextTierName }} Tier!</span>
                                @else
                                    <span class="text-warning fw-bold">🏆 Highest Diamond Tier Unlocked!</span>
                                @endif
                            </div>
                            
                            <div class="progress" style="height: 10px; background-color: rgba(255,255,255,0.15); border-radius: 999px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $progressPercent }}%; border-radius: 999px;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="d-flex justify-content-between text-center mt-3" style="font-size: 12px;">
                                <div class="{{ $currentTier === 'Bronze' ? 'text-warning fw-bold' : 'text-white-50' }}">{{ $currentTier === 'Bronze' ? '● Bronze (Active)' : '○ Bronze' }}</div>
                                <div class="{{ $currentTier === 'Silver' ? 'text-warning fw-bold' : 'text-white-50' }}">{{ $currentTier === 'Silver' ? '● Silver (Active)' : '○ Silver (' . $silverReq . ' bookings)' }}</div>
                                <div class="{{ $currentTier === 'Gold' ? 'text-warning fw-bold' : 'text-white-50' }}">{{ $currentTier === 'Gold' ? '● Gold (Active)' : '○ Gold (' . $goldReq . ' bookings)' }}</div>
                                <div class="{{ $currentTier === 'Platinum' ? 'text-warning fw-bold' : 'text-white-50' }}">{{ $currentTier === 'Platinum' ? '● Platinum (Active)' : '○ Platinum (' . $platReq . ' bookings)' }}</div>
                                <div class="{{ $currentTier === 'Diamond' ? 'text-warning fw-bold' : 'text-white-50' }}">{{ $currentTier === 'Diamond' ? '● Diamond (Active)' : '○ Diamond (' . $diamondReq . ' bookings)' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIP Perks 4-Column Grid -->
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 rounded-4 p-3.5 h-100 shadow-xs text-center bg-white" style="border-radius: 16px !important;">
                            <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: #fef3c7; color: #d97706; font-size: 22px;">
                                <i class="fa-solid fa-tags"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Up to 25% Off</h6>
                            <p class="text-secondary small mb-0">Exclusive VIP member discounts on 2M+ hotels worldwide.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 rounded-4 p-3.5 h-100 shadow-xs text-center bg-white" style="border-radius: 16px !important;">
                            <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: #dcfce7; color: #16a34a; font-size: 22px;">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Free Breakfast</h6>
                            <p class="text-secondary small mb-0">Complimentary morning breakfast inclusions at Silver+ tiers.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 rounded-4 p-3.5 h-100 shadow-xs text-center bg-white" style="border-radius: 16px !important;">
                            <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: #e0f2fe; color: #0284c7; font-size: 22px;">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">24/7 VIP Hotline</h6>
                            <p class="text-secondary small mb-0">Priority customer service desk for urgent travel inquiries.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 rounded-4 p-3.5 h-100 shadow-xs text-center bg-white" style="border-radius: 16px !important;">
                            <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: #f3e8ff; color: #9333ea; font-size: 22px;">
                                <i class="fa-solid fa-arrow-up-right-dots"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Room Upgrades</h6>
                            <p class="text-secondary small mb-0">Free room class upgrades on availability for Platinum &amp; Diamond.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
