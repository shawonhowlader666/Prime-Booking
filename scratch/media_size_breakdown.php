<?php
$hotelsDir = 'c:/Users/Shawon/Desktop/Prime Booking/storage/app/public/hotels';
$imgSize = 0;
$vidSize = 0;
$imgCount = 0;
$vidCount = 0;

$rdi = new RecursiveDirectoryIterator($hotelsDir, RecursiveDirectoryIterator::SKIP_DOTS);
$rii = new RecursiveIteratorIterator($rdi);

foreach ($rii as $f) {
    $ext = strtolower($f->getExtension());
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $imgSize += $f->getSize();
        $imgCount++;
    } elseif (in_array($ext, ['mp4', 'mov', 'webm'])) {
        $vidSize += $f->getSize();
        $vidCount++;
    }
}

echo "Images: $imgCount (" . round($imgSize / (1024 * 1024), 2) . " MB)\n";
echo "Videos: $vidCount (" . round($vidSize / (1024 * 1024), 2) . " MB)\n";
