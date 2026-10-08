<?php
$c = file_get_contents('public/css/agoda/search-bundle.css');
$terms = ['PropertyCardItem', 'PropertyCardPrice--Display', 'ReviewSection-score', 'hotel-item-box'];
foreach ($terms as $t) {
    $pos = strpos($c, $t);
    if ($pos !== false) {
        echo "=== Found $t ===\n";
        echo substr($c, max(0, $pos - 40), 400) . "\n\n";
    }
}
