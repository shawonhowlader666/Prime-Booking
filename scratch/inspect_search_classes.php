<?php
$c = file_get_contents('public/css/agoda/search-bundle.css');
preg_match_all('/\.([a-zA-Z0-9_\-]+)/', $c, $matches);
$unique = array_unique($matches[1]);
sort($unique);
$filtered = array_filter($unique, function($name) {
    return stripos($name, 'hotel') !== false 
        || stripos($name, 'card') !== false 
        || stripos($name, 'item') !== false 
        || stripos($name, 'property') !== false
        || stripos($name, 'price') !== false
        || stripos($name, 'score') !== false
        || stripos($name, 'review') !== false;
});
echo "Found " . count($filtered) . " relevant classes in search-bundle.css:\n";
echo implode("\n", array_slice($filtered, 0, 80));
