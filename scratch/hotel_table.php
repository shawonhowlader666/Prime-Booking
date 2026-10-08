<?php
$base = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd/Website Element';

$hotels = scandir($base);
echo sprintf("%-4s | %-42s | %-7s | %-8s | %-6s | %-6s\n", "No", "Hotel Name", "Photos", "Gallery", "Rooms", "Videos");
echo str_repeat("-", 80) . "\n";

$i = 0;
foreach ($hotels as $h) {
    if ($h === '.' || $h === '..') continue;
    $hPath = $base . '/' . $h;
    if (!is_dir($hPath)) continue;
    $i++;

    $images = 0;
    $gallery = 0;
    $rooms = [];
    $videos = [];

    $rdi = new RecursiveDirectoryIterator($hPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $rii = new RecursiveIteratorIterator($rdi);

    foreach ($rii as $f) {
        $ext = strtolower($f->getExtension());
        $relPath = str_replace($hPath . DIRECTORY_SEPARATOR, '', $f->getPathname());
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $images++;
            if (str_contains($relPath, 'All Image') || str_contains($relPath, 'All Photo')) {
                $gallery++;
            } else {
                if (preg_match('#Image\\\\([^\\\\]+)#', $relPath, $m)) {
                    $rooms[$m[1]] = true;
                }
            }
        } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            $videos[] = basename($relPath);
        }
    }

    echo sprintf("%-4d | %-42s | %-7d | %-8d | %-6d | %-6d\n", $i, $h, $images, $gallery, count($rooms), count($videos));
    if (!empty($videos)) {
        echo "     └─ Videos: " . implode(', ', $videos) . "\n";
    }
}
