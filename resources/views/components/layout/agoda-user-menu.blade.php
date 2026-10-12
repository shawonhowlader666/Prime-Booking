{{-- 100% Identical Agoda User Dropdown Menu Content (Exact Match to Agoda Screenshot) --}}
<style>
    .agoda-user-menu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10.5px 20px;
        font-size: 14.5px;
        font-weight: 600;
        color: #1e293b !important;
        text-decoration: none;
        transition: background-color 0.12s ease;
        width: 100%;
        box-sizing: border-box;
    }
    .agoda-user-menu-item:hover {
        background-color: #f8fafc;
        color: #1e293b !important;
    }
</style>

<div style="padding: 0; text-align: left;">
    {{-- Header: My Account (Bold Dark Charcoal, 16px) --}}
    <div style="padding: 16px 20px 8px 20px; font-weight: 700; font-size: 16px; color: #1e293b; letter-spacing: -0.2px;">
        {{ __('My Account') }}
    </div>

    {{-- Menu Items with Generous Agoda-Exact Row Padding & Font Size --}}
    <a href="{{ route('trips') }}" class="agoda-user-menu-item">
        <span>{{ __('My Trips') }}</span>
    </a>

    <a href="{{ route('messages') }}" class="agoda-user-menu-item">
        <span>{{ __('Property messages') }}</span>
    </a>
    
    {{-- PrimeCash row with crisp rectangular purple badge (border-radius: 3px) --}}
    <a href="{{ route('cashback') }}" class="agoda-user-menu-item">
        <span>{{ __('PrimeCash') }}</span>
        <span style="background-color: #5c4cf4; color: #ffffff; font-size: 12px; font-weight: 700; border-radius: 3px; padding: 2.5px 8px; letter-spacing: 0.2px; line-height: 1.2;">
            {{ $currentCurrency ?? 'BDT' }} 0
        </span>
    </a>

    <a href="{{ route('cashback') }}" class="agoda-user-menu-item">
        <span>{{ __('Cashback Rewards') }}</span>
    </a>

    {{-- PrimeVIP row with exact Agoda seamless dual-tone badge (border-radius: 3px) --}}
    <a href="{{ route('vip') }}" class="agoda-user-menu-item">
        <span>{{ __('PrimeVIP') }}</span>
        <div style="display: inline-flex; align-items: center; border-radius: 3px; overflow: hidden; height: 20px; font-size: 11px; line-height: 1; box-shadow: 0 1px 2px rgba(0,0,0,0.08);">
            <div style="background-color: #1e2430; color: #ffffff; padding: 0 6px; height: 100%; display: flex; align-items: center; font-weight: 800; letter-spacing: 0.2px;">
                ★ VIP
            </div>
            <div style="background-color: {{ $headerBadgeColor ?? '#8c522b' }}; color: #ffffff; padding: 0 8px; height: 100%; display: flex; align-items: center; font-weight: 700; letter-spacing: 0.2px;">
                {{ $headerTier ?? 'Bronze' }}
            </div>
        </div>
    </a>

    <a href="{{ route('wishlist') }}" class="agoda-user-menu-item">
        <span>{{ __('Saved properties list') }}</span>
    </a>

    <a href="{{ route('reviews') }}" class="agoda-user-menu-item">
        <span>{{ __('Reviews') }}</span>
    </a>
    
    {{-- PointsMAX row with authentic circular (P) icon --}}
    <a href="{{ route('pointsmax') }}" class="agoda-user-menu-item">
        <span>{{ __('PointsMAX') }}</span>
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 19px; height: 19px; border: 1.5px solid #1e293b; border-radius: 50%; font-size: 11px; font-weight: 800; color: #1e293b; line-height: 1;">
            P
        </span>
    </a>

    <a href="{{ route('profile') }}" class="agoda-user-menu-item">
        <span>{{ __('Profile') }}</span>
    </a>

    {{-- SIGN OUT Outlined Rounded Pill Button (Agoda Exact 1:1) --}}
    <div style="padding: 14px 20px 14px 20px;">
        <form action="{{ route('auth.logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn w-100 fw-bold shadow-none" 
                    style="border: 1px solid #cbd5e1; border-radius: 24px; color: #2067e1; font-size: 14px; font-weight: 700; padding: 8px 16px; background: #ffffff; transition: all 0.2s ease;"
                    onmouseover="this.style.borderColor='#2067e1'; this.style.backgroundColor='#f0f7ff';"
                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#ffffff';">
                SIGN OUT
            </button>
        </form>
    </div>

    {{-- Settings Section --}}
    <div style="border-top: 1px solid #f1f5f9;">
        <div style="padding: 14px 20px 6px 20px; font-weight: 700; font-size: 15px; color: #1e293b;">
            {{ __('Settings') }}
        </div>
        
        <div class="agoda-user-menu-item" data-bs-toggle="modal" data-bs-target="#agodaLanguageModal" style="cursor: pointer; padding: 8px 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img loading="lazy" decoding="async" src="https://flagcdn.com/w40/gb.png" alt="EN" style="width: 20px; height: 14px; border-radius: 2px; object-fit: cover;">
                <span style="font-size: 14px; font-weight: 600; color: #1e293b;">English</span>
            </div>
        </div>

        <div class="agoda-user-menu-item" data-bs-toggle="modal" data-bs-target="#agodaLanguageModal" style="cursor: pointer; padding: 8px 20px 14px 20px;">
            <span style="font-size: 13.5px; font-weight: 600; color: #1e293b;">
                {{ $currentCurrency ?? 'BDT' }} &nbsp; <span style="font-weight: 400; color: #64748b;">{{ ($currentCurrency ?? 'BDT') === 'BDT' ? 'Bangladeshi Taka' : (($currentCurrency ?? 'BDT') === 'USD' ? 'US Dollar' : ($currentCurrency ?? 'BDT')) }}</span>
            </span>
        </div>

        {{-- List your place on Prime Booking (Agoda Exact Clean Design) --}}
        <div style="border-top: 1px solid #f1f5f9; padding: 14px 20px 18px 20px;">
            <div style="font-size: 14px; font-weight: 700; color: #1e293b; line-height: 1.3; margin-bottom: 3px;">
                {{ __('List your place on Prime Booking') }}
            </div>
            <div style="font-size: 12px; color: #64748b; line-height: 1.35; margin-bottom: 8px;">
                {{ __('Earn money to pay for your travel!') }}
            </div>
            <a href="{{ Route::has('admin.properties.create') ? route('admin.properties.create') : (Route::has('vendor.dashboard') ? route('vendor.dashboard') : '#') }}" 
               style="color: #2067e1; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                {{ __('List your place') }} <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
            </a>
        </div>
    </div>
</div>
