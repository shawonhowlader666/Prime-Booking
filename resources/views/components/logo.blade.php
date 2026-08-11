@props([
    'height' => '42px',
    'mode' => 'light',
    'class' => '',
    'style' => ''
])

@php
    $logoPath = ($mode === 'dark') ? asset('images/logo-dark.svg') : asset('images/logo.svg');
    $numericHeight = preg_replace('/[^0-9]/', '', (string)$height);
    $calculatedWidth = $numericHeight ? (int)($numericHeight * 2.15) . 'px' : 'auto';
@endphp

<img src="{{ $logoPath }}" 
     alt="Prime Booking Logo" 
     class="prime-booking-logo {{ $class }}"
     style="height: {{ is_numeric($height) ? $height.'px' : $height }}; width: {{ $calculatedWidth }}; object-fit: contain; vertical-align: middle; {{ $style }}" />
