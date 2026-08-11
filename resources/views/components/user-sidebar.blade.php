@props(['activePage' => 'profile'])

@php
    $menuItems = [
        [
            'key'   => 'trips',
            'label' => 'My Trips',
            'icon'  => 'fa-solid fa-calendar-check',
            'route' => route('trips'),
        ],
        [
            'key'   => 'bookings',
            'label' => 'All bookings',
            'icon'  => 'fa-solid fa-suitcase',
            'route' => route('booking.history'),
        ],
        [
            'key'   => 'hotels',
            'label' => 'Hotels',
            'icon'  => 'fa-solid fa-hotel',
            'route' => route('search.index'),
        ],
        [
            'key'   => 'flights',
            'label' => 'Flights',
            'icon'  => 'fa-solid fa-plane',
            'route' => route('search.index'),
        ],
        [
            'key'   => 'activities',
            'label' => 'Activities',
            'icon'  => 'fa-solid fa-icons',
            'route' => route('packages'),
        ],
        [
            'key'   => 'messages',
            'label' => 'Property messages',
            'icon'  => 'fa-solid fa-comment-dots',
            'route' => route('messages'),
        ],
        [
            'key'   => 'reviews',
            'label' => 'Reviews',
            'icon'  => 'fa-solid fa-star',
            'route' => route('reviews'),
        ],
        [
            'key'   => 'vip',
            'label' => 'PrimeVIP',
            'custom_icon' => '<span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;"><i class="fa-solid fa-star"></i></span>',
            'route' => route('vip'),
        ],
        [
            'key'   => 'primecash',
            'label' => 'PrimeCash',
            'custom_icon' => '<span class="bg-dark text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">a</span>',
            'route' => route('cashback'),
        ],
        [
            'key'   => 'cashback',
            'label' => 'Cashback Rewards',
            'icon'  => 'fa-solid fa-hand-holding-dollar',
            'route' => route('cashback'),
        ],
        [
            'key'   => 'pointsmax',
            'label' => 'PointsMAX',
            'custom_icon' => '<span class="bg-dark text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 11px;">P</span>',
            'route' => route('pointsmax'),
        ],
        [
            'key'   => 'profile',
            'label' => 'Profile',
            'icon'  => 'fa-solid fa-user',
            'route' => route('profile'),
        ],
    ];
@endphp

<div class="bg-white border shadow-sm" style="border-color: #cbd5e1 !important; border-radius: 20px !important; padding: 20px 14px 28px 14px;">
    <div class="d-flex flex-column" style="gap: 4px;">
        @foreach($menuItems as $item)
            @php
                $isActive = $activePage === $item['key'];
            @endphp
            <a href="{{ $item['route'] }}"
               class="text-decoration-none d-flex align-items-center fw-bold"
               style="padding: 11px 16px; border-radius: 12px; gap: 14px; font-size: 14px; transition: all 0.15s ease;
                      background-color: {{ $isActive ? '#2067e1' : 'transparent' }};
                      color: {{ $isActive ? '#ffffff' : '#1e293b' }};">
                <div style="width: 24px; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                    @if(isset($item['custom_icon']))
                        {!! $isActive ? str_replace('bg-dark text-white', 'bg-white text-primary', $item['custom_icon']) : $item['custom_icon'] !!}
                    @else
                        <i class="{{ $item['icon'] }} {{ $isActive ? 'text-white' : 'text-dark' }}" style="font-size: 17px; {{ $item['key'] === 'flights' ? 'transform: rotate(-45deg);' : '' }}"></i>
                    @endif
                </div>
                <span>{{ __($item['label']) }}</span>
            </a>
        @endforeach
    </div>
</div>
