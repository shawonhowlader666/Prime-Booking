<?php

$sampleSlugs = [
    'hotel-makkah',
    'bashati-bay-resort',
    'baywatch-coxs-bazar',
    'pan-pacific-sonargaon-dhaka',
    'the-westin-dhaka',
    'the-peninsula-chittagong',
    'grand-sultan-tea-resort-golf',
    'sajek-resort',
    'kuakata-grand-hotel-sea-resort'
];

echo "Testing Live Hotel Detail Pages by SLUG (/hotel/{slug}) on https://primebooking.com.bd:\n\n";

foreach ($sampleSlugs as $slug) {
    $url = "https://primebooking.com.bd/hotel/{$slug}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Extract hotel title
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $titleM);
    $title = trim(strip_tags($titleM[1] ?? 'Not Found'));
    
    // Count carousels
    preg_match_all('/class="[^"]*room-photo-carousel[^"]*"/i', $html, $mC);
    $cCount = count($mC[0]);
    $hasCarouselBtns = strpos($html, 'nextRoomPhoto(this, event)') !== false;
    
    echo sprintf("Slug: %-32s | %-32s | HTTP %d | Carousels: %d | Buttons: %s | Status: %s\n",
        $slug,
        substr($title, 0, 32),
        $code,
        $cCount,
        $hasCarouselBtns ? 'YES' : 'NO',
        ($code === 200 && $cCount > 0 && $hasCarouselBtns) ? '100% OK' : 'CHECK'
    );
}
