<?php
$ctx = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
$html = file_get_contents('https://primebooking.com.bd/search', false, $ctx);
echo "HTML Length: " . strlen($html) . "\n";
echo "2 Star Economy: " . (str_contains($html, '2 Star Economy') ? 'FOUND' : 'MISSING') . "\n";
echo "1 Star Budget: " . (str_contains($html, '1 Star Budget') ? 'FOUND' : 'MISSING') . "\n";
echo "Popular filters: " . (str_contains($html, 'Popular filters') ? 'FOUND' : 'MISSING') . "\n";
echo "Distance to center: " . (str_contains($html, 'Distance to center') ? 'FOUND' : 'MISSING') . "\n";
echo "Room offers: " . (str_contains($html, 'Room offers') ? 'FOUND' : 'MISSING') . "\n";
echo "Sticky Sidebar: " . (str_contains($html, 'agoda-sticky-sidebar') ? 'FOUND' : 'MISSING') . "\n";
echo "Facilities Collapse: " . (str_contains($html, 'moreFacilitiesCollapse') ? 'FOUND' : 'MISSING') . "\n";

