<?php

// Check IDs and test live detail pages
$sampleIds = [12, 14, 20, 31, 32, 33, 50, 64, 75, 84];

echo "Testing Live Hotel Detail Pages by ID on https://primebooking.com.bd:\n\n";

foreach ($sampleIds as $id) {
    $url = "https://primebooking.com.bd/hotels/{$id}";
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
    
    echo sprintf("ID %-3d | %-38s | HTTP %d | Carousels: %d | Buttons: %s | Status: %s\n",
        $id,
        substr($title, 0, 38),
        $code,
        $cCount,
        $hasCarouselBtns ? 'YES' : 'NO',
        ($code === 200 && $cCount > 0 && $hasCarouselBtns) ? '100% OK' : 'CHECK'
    );
}
