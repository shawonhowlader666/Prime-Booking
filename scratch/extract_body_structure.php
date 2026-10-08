<?php
$html = file_get_contents('scratch/agoda_source/agoda_dhaka_viewsource.html');

// Find all HTML elements inside body
if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $b)) {
    $body = $b[1];
    echo "Body length: " . strlen($body) . "\n";
    
    // Remove scripts to see visible DOM structure
    $cleanBody = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $body);
    echo "Body without scripts: " . strlen($cleanBody) . "\n";
    
    file_put_contents('scratch/agoda_source/body_structure.html', $cleanBody);
    echo "Saved body_structure.html\n";
    
    $lines = array_filter(array_map('trim', explode("\n", $cleanBody)));
    echo "=== Total lines: " . count($lines) . " ===\n";
    foreach (array_slice($lines, 90, 160) as $l) {
        if (!empty($l)) {
            echo $l . "\n";
        }
    }
}
