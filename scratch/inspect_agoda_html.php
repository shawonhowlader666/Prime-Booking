<?php
$html = file_get_contents('scratch/agoda_source/agoda_live_search.html');
echo "HTML Length: " . strlen($html) . "\n";

// Look for SSR Search Result elements or Card templates
if (preg_match_all('/<(div|li|article)[^>]+class="[^"]*(?:ssr-search-result|hotel-item-box|property-card|PropertyCard)[^"]*"[^>]*>/i', $html, $m)) {
    echo "Found Card Wrappers:\n";
    foreach (array_unique($m[0]) as $tag) {
        echo "  " . htmlspecialchars($tag) . "\n";
    }
}

// Find key sections in the HTML
$tags = ['ssr-search-result-item', 'hotel-item-box', 'property-card-content', 'hotel-info', 'hotel-price-promo-container', 'staticMapPanel'];
foreach ($tags as $t) {
    $pos = strpos($html, $t);
    echo "$t: " . ($pos !== false ? "FOUND at pos $pos" : "NOT found") . "\n";
}

$css = file_get_contents('public/css/agoda/agoda-critical.css');
preg_match_all('/\.[a-zA-Z0-9_\-\s\>\,\:]*(?:hotel-item-box|property-card|hotel-price|hotel-info|ssr-search)[^\{]*\{[^\}]+\}/i', $css, $m);
echo "=== Extracted Agoda CSS Rules ===\n";
foreach (array_slice($m[0], 0, 15) as $rule) {
    echo $rule . "\n";
}
