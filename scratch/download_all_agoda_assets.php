<?php
echo "=== Starting Full Agoda Design Code & Assets Downloader ===\n";

$dirs = [
    'public/css/agoda',
    'public/images/agoda',
    'scratch/agoda_source'
];
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        mkdir($d, 0755, true);
    }
}

// 1. List of official Agoda CSS and Asset URLs
$assets = [
    // CSS Bundles
    'public/css/agoda/3596-bundle.css' => 'https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/3596-0bb2c0e49d90.css',
    'public/css/agoda/7664-bundle.css' => 'https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/7664-1746c591b6fa.css',
    'public/css/agoda/search-bundle.css' => 'https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/search-d7c52cdf705c.css',
    
    // Core Images & Sprites
    'public/images/agoda/bg-sprite-flags.png' => 'https://cdn6.agoda.net/images/desktop/bg-sprite-flags.png',
    'public/images/agoda/bg-picture.png' => 'https://cdn5.agoda.net/images/mvc/default/bg-picture.png',
    'public/images/agoda/agoda-logo-v2.png' => 'https://cdn6.agoda.net/images/MVC/default/agoda-logo-v2.png',
    'public/images/agoda/qrcode-agoda.png' => 'https://cdn6.agoda.net/images/CW-7/default/qrcode-agoda.png',
    'public/images/agoda/qrcode-wechat.png' => 'https://cdn6.agoda.net/images/CW-271/default/qrcode-wechat-v1.png',
];

$ctx = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\nAccept: */*\r\n",
        'timeout' => 30
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

foreach ($assets as $dest => $url) {
    echo "Downloading: $url -> $dest ... ";
    $data = @file_get_contents($url, false, $ctx);
    if ($data !== false && strlen($data) > 0) {
        file_put_contents($dest, $data);
        echo "OK (" . number_format(strlen($data)) . " bytes)\n";
    } else {
        echo "FAILED\n";
    }
}

// 2. Fetch Live Agoda HTML Search Source
echo "\nFetching Live Agoda Search HTML source...\n";
$htmlUrl = 'https://www.agoda.com/search?city=1390';
$html = @file_get_contents($htmlUrl, false, $ctx);
if ($html) {
    file_put_contents('scratch/agoda_source/agoda_live_search.html', $html);
    echo "Saved agoda_live_search.html (" . number_format(strlen($html)) . " bytes)\n";

    // Extract all inline <style> blocks
    preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches);
    if (!empty($matches[1])) {
        $extractedCss = implode("\n/* --- Style Block --- */\n", $matches[1]);
        file_put_contents('public/css/agoda/agoda-all-inline-styles.css', $extractedCss);
        echo "Saved agoda-all-inline-styles.css (" . number_format(strlen($extractedCss)) . " bytes)\n";
    }
} else {
    echo "Live HTML fetch failed.\n";
}

echo "\n=== Download Complete ===\n";
