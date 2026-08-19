@props([
    'height' => '38px',
    'mode' => 'light',
    'class' => '',
    'style' => ''
])

@php
    $logoPath = asset('images/logo.png') . '?v=3';
@endphp

<img src="{{ $logoPath }}" 
     alt="Prime Booking" 
     class="prime-booking-logo {{ $class }}"
     style="height: {{ is_numeric($height) ? $height.'px' : $height }}; width: auto; max-height: {{ is_numeric($height) ? $height.'px' : $height }}; object-fit: contain; display: block; {{ $style }}" />

