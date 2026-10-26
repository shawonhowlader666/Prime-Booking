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
echo "Wishlist AJAX: " . (str_contains($html, 'toggleCardWishlist') ? 'FOUND' : 'MISSING') . "\n";
echo "Amenity Badges: " . (str_contains($html, 'Free Wi-Fi') || str_contains($html, 'Breakfast Included') ? 'FOUND' : 'MISSING') . "\n";
echo "Prime Preferred / Top Rated: " . (str_contains($html, 'PRIME PREFERRED') || str_contains($html, 'TOP RATED') ? 'FOUND' : 'MISSING') . "\n";

$htmlCox = file_get_contents('https://primebooking.com.bd/search?destination=Cox%27s+Bazar', false, $ctx);
echo "\n--- Cox's Bazar Live Search ---\n";
echo "Cox HTML Length: " . strlen($htmlCox) . "\n";
echo "Sea Pearl: " . (str_contains($htmlCox, 'Sea Pearl') ? 'FOUND' : 'MISSING') . "\n";
echo "Ocean Paradise: " . (str_contains($htmlCox, 'Ocean Paradise') ? 'FOUND' : 'MISSING') . "\n";
echo "Seagull Hotels: " . (str_contains($htmlCox, 'Seagull Hotels') ? 'FOUND' : 'MISSING') . "\n";
echo "VIDEO Badge: " . (str_contains($htmlCox, 'VIDEO') ? 'FOUND' : 'MISSING') . "\n";

