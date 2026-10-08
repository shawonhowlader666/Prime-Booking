<?php
$files = glob('public/css/agoda/*.css');
foreach ($files as $file) {
    $content = file_get_contents($file);
    echo "=== File: $file ===\n";
    $terms = ['hotel-item-box', 'PropertyCard', 'map-static-placeholder', 'FilterPlaceholder', 'container-agoda'];
    foreach ($terms as $t) {
        $pos = strpos($content, $t);
        if ($pos !== false) {
            echo "Found '$t' at pos $pos:\n";
            echo substr($content, max(0, $pos - 50), 300) . "\n---\n";
        }
    }
}
