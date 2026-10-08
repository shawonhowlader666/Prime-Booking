<?php
$c = file_get_contents('public/css/agoda/7664-bundle.css');
echo "Length of 7664-bundle.css: " . strlen($c) . "\n";
preg_match_all('/\.([a-zA-Z0-9_\-]+Property[a-zA-Z0-9_\-]*)/', $c, $matches);
$unique = array_unique($matches[0]);
echo "Property matches: " . implode(', ', array_slice($unique, 0, 30)) . "\n";

preg_match_all('/\.([a-zA-Z0-9_\-]+Card[a-zA-Z0-9_\-]*)/', $c, $matches2);
$unique2 = array_unique($matches2[0]);
echo "Card matches: " . implode(', ', array_slice($unique2, 0, 30)) . "\n";

preg_match_all('/\.([a-zA-Z0-9_\-]+hotel[a-zA-Z0-9_\-]*)/i', $c, $matches3);
$unique3 = array_unique($matches3[0]);
echo "Hotel matches: " . implode(', ', array_slice($unique3, 0, 30)) . "\n";
