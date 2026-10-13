@props([
    'height' => '32px',
    'mode' => 'light',
    'class' => '',
    'style' => ''
])

@php
    $logoPath = match($mode) {
        'dark'  => asset('images/logo-dark.png') . '?v=5',
        'icon'  => asset('images/logo-icon.png') . '?v=5',
        default => asset('images/logo-light.png') . '?v=5',
    };
@endphp

<img src="{{ $logoPath }}" 
     alt="Prime Booking" 
     class="prime-booking-logo {{ $class }}"
     style="height: {{ is_numeric($height) ? $height.'px' : $height }}; width: auto; max-height: {{ is_numeric($height) ? $height.'px' : $height }}; object-fit: contain; display: block; {{ $style }}" />

