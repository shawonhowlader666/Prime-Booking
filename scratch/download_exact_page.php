<?php
$url = "https://www.agoda.com/search?city=1390&checkIn=2026-10-15&los=5&rooms=1&adults=2&children=0&locale=en-us";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
    "Accept-Language: en-US,en;q=0.9"
]);
$html = curl_exec($ch);
curl_close($ch);

if (!is_dir('scratch/agoda_source')) {
    mkdir('scratch/agoda_source', 0755, true);
}

file_put_contents('scratch/agoda_source/agoda_dhaka_viewsource.html', $html);
echo "Successfully downloaded agoda_dhaka_viewsource.html: " . strlen($html) . " bytes\n";
