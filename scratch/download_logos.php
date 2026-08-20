<?php

$urls = [
    'bkash.png' => 'https://raw.githubusercontent.com/FaisalBudh/bkash-payment-gateway/main/bkash.png',
    'bkash_alt.png' => 'https://freepnglogo.com/images/all_img/1701589391bkash-logo-png.png',
    'nagad_alt.png' => 'https://freepnglogo.com/images/all_img/1701589769nagad-logo-png.png',
    'visa.png' => 'https://cdn.jsdelivr.net/npm/payment-icons@1.3.0/min/flat/visa.svg',
    'mastercard.png' => 'https://cdn.jsdelivr.net/npm/payment-icons@1.3.0/min/flat/mastercard.svg',
    'amex.png' => 'https://cdn.jsdelivr.net/npm/payment-icons@1.3.0/min/flat/amex.svg',
    'bkash_svg.svg' => 'https://cdn.jsdelivr.net/npm/simple-icons@v11/icons/bkash.svg',
];

$dest = __DIR__ . '/../public/images/payments/';

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\nAccept: */*\r\n"
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];
$context = stream_context_create($opts);

foreach ($urls as $name => $url) {
    $content = @file_get_contents($url, false, $context);
    if ($content && strlen($content) > 100) {
        file_put_contents($dest . $name, $content);
        echo "Successfully downloaded {$name} (" . strlen($content) . " bytes)\n";
    } else {
        echo "Failed {$name}\n";
    }
}
