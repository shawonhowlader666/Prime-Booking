@props([
    'height' => '42px',
    'mode' => 'light',
    'class' => '',
    'style' => ''
])

@php
    $logoPath = asset('images/logo.png') . '?v=2';
    $numericHeight = preg_replace('/[^0-9]/', '', (string)$height);
    $calculatedWidth = $numericHeight ? (int)($numericHeight * 2.1) . 'px' : 'auto';
@endphp

<img src="{{ $logoPath }}" 
     alt="Prime Booking Logo" 
     class="prime-booking-logo {{ $class }}"
     style="height: {{ is_numeric($height) ? $height.'px' : $height }}; width: {{ $calculatedWidth }}; object-fit: contain; vertical-align: middle; {{ $style }}" />
