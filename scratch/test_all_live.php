<?php

$urls = [
    'https://primebooking.com.bd/',
    'https://primebooking.com.bd/search?destination=Cox%27s+Bazar',
    'https://primebooking.com.bd/hotels/3',
    'https://primebooking.com.bd/book/3',
    'https://primebooking.com.bd/deals',
    'https://primebooking.com.bd/flights',
    'https://primebooking.com.bd/vip',
];

foreach ($urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "{$url} -> HTTP {$httpCode}\n";
}
