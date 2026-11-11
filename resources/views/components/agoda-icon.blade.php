@props([
    'name' => '',
    'size' => 16,
    'color' => 'currentColor',
    'class' => '',
])

@php
    $s = $size;
    $c = $color;
@endphp

@switch($name)
    @case('bed')
        {{-- Agoda King / Queen Bed SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 19h20M2 15h20M2 10v9M22 10v9M6 10V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/>
            <rect x="6" y="9" width="4" height="3" rx="1"/>
            <rect x="14" y="9" width="4" height="3" rx="1"/>
        </svg>
        @break

    @case('user')
    @case('guests')
        {{-- Agoda Guest / User Silhouette SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        @break

    @case('users')
    @case('user-group')
        {{-- Agoda Multi-guest Silhouette SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        @break

    @case('child')
    @case('kid')
        {{-- Agoda Child SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="5" r="3"/>
            <path d="M9 13v8M15 13v8M12 8v5M8 10l4 2 4-2"/>
        </svg>
        @break

    @case('size')
    @case('ruler')
        {{-- Agoda Room Size Area / Plan SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21h18M3 21V3M3 8h4M3 13h2M3 18h4M8 21v-4M13 21v-2M18 21v-4"/>
            <rect x="8" y="3" width="13" height="13" rx="1.5"/>
        </svg>
        @break

    @case('bath')
    @case('bathroom')
        {{-- Agoda Bathtub SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 12h16a2 2 0 0 1 2 2v2a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4v-2a2 2 0 0 1 2-2zM5 12V6a2 2 0 0 1 2-2h2M5 20v2M19 20v2"/>
        </svg>
        @break

    @case('shower')
        {{-- Agoda Shower Head SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h7a4 4 0 0 1 4 4v1M15 9l-4 4M15 9l4 4M13 13l-2 2M15 15l-2 2M17 13l-2 2M19 15l-2 2"/>
        </svg>
        @break

    @case('wifi')
        {{-- Agoda Wi-Fi SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/>
        </svg>
        @break

    @case('tv')
        {{-- Agoda TV SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="13" rx="2"/>
            <path d="m17 3-5 4-5-4M12 20v2"/>
        </svg>
        @break

    @case('ac')
    @case('snowflake')
        {{-- Agoda Air-conditioning / Snowflake SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M19.07 4.93 4.93 19.07"/>
            <path d="m9 3 3 2 3-2M9 21l3-2 3 2M3 9l2 3-2 3M21 9l-2 3 2 3"/>
        </svg>
        @break

    @case('balcony')
        {{-- Agoda Balcony / Outdoor Terrace Railing SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 11h18"/>
            <path d="M4 11v8a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-8"/>
            <path d="M8 11v9M12 11v9M16 11v9"/>
            <path d="M6 11V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/>
            <path d="M12 4v7"/>
        </svg>
        @break

    @case('smoking-ban')
    @case('nonsmoking')
        {{-- Agoda Non-smoking SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/>
            <path d="M5.64 5.64 18.36 18.36M18 12h1M7 15h6"/>
        </svg>
        @break

    @case('check')
        {{-- Agoda Crisp Checkmark SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        @break

    @case('check-circle')
        {{-- Agoda Green Check in Circle SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/>
            <polyline points="8 12 11 15 16 9"/>
        </svg>
        @break

    @case('cash')
    @case('pay-at-hotel')
        {{-- Agoda Cash / Pay at Hotel Banknote with Dollar SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="6" width="20" height="12" rx="2"/>
            <path d="M12 9v6M10.5 10.5a1.5 1.5 0 0 1 1.5-1.5h.5a1.5 1.5 0 0 1 0 3h-1a1.5 1.5 0 0 0 0 3h.5a1.5 1.5 0 0 0 1.5-1.5"/>
            <line x1="5" y1="9" x2="5" y2="15"/>
            <line x1="19" y1="9" x2="19" y2="15"/>
        </svg>
        @break

    @case('credit-card')
        {{-- Agoda Credit Card SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
            <line x1="6" y1="15" x2="9" y2="15"/>
        </svg>
        @break

    @case('location-pin')
    @case('pin')
        {{-- Agoda Blue Map Pin SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>
        @break

    @case('calendar')
        {{-- Agoda Calendar SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        @break

    @case('search')
        {{-- Agoda Search Loupe SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        @break

    @case('breakfast')
    @case('coffee')
        {{-- Agoda Breakfast / Coffee Cup SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/>
        </svg>
        @break

    @case('bolt')
    @case('lightning')
        {{-- Agoda Red Hot Deal Lightning SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="{{ $c }}" stroke="{{ $c }}" stroke-width="1">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        @break

    @case('shield')
        {{-- Agoda Price Match / Shield SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
        </svg>
        @break

    @case('cutlery')
    @case('utensils')
    @case('fork-knife')
        {{-- Agoda Cutlery SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 2v20M21 2v6a3 3 0 0 1-3 3M18 11v11M4 2v6a3 3 0 0 0 3 3v11M7 2v6M10 2v6a3 3 0 0 1-3 3"/>
        </svg>
        @break

    @case('thumb-up')
    @case('thumbs-up')
        {{-- Agoda Thumbs Up SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 10v12M15 10.5V6.7a2.7 2.7 0 0 0-.8-1.9l-2-2L9.5 5.5A2 2 0 0 0 9 7v15h9a2 2 0 0 0 2-1.7l1.3-8a2 2 0 0 0-2-2.3H15zM2 10h5v12H2z"/>
        </svg>
        @break

    @case('heart')
        {{-- Agoda Heart / Wishlist SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
        </svg>
        @break

    @case('info')
    @case('info-circle')
        {{-- Agoda Info Circle SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        @break

    @case('tag')
        {{-- Agoda Deal Tag SVG --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
            <circle cx="7" cy="7" r="1"/>
        </svg>
        @break

    @default
        {{-- Default Check Circle --}}
        <svg class="{{ $class }}" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="16 10 11 15 8 12"/>
        </svg>
@endswitch
