<?php
$base = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd';
$size = 0;
$count = 0;
$rdi = new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS);
$rii = new RecursiveIteratorIterator($rdi);

foreach ($rii as $f) {
    $size += $f->getSize();
    $count++;
}

echo "Total files: $count\n";
echo "Total size: " . round($size / (1024 * 1024), 2) . " MB\n";
