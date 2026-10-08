<?php
$base = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd/Website Element';

$hotels = scandir($base);
$report = [];

foreach ($hotels as $h) {
    if ($h === '.' || $h === '..') continue;
    $hPath = $base . '/' . $h;
    if (!is_dir($hPath)) continue;

    $images = [];
    $allImages = [];
    $roomImages = [];
    $videos = [];
    $audio = [];
    $other = [];

    $rdi = new RecursiveDirectoryIterator($hPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $rii = new RecursiveIteratorIterator($rdi);

    foreach ($rii as $f) {
        $path = $f->getPathname();
        $ext = strtolower($f->getExtension());
        $relPath = str_replace($hPath . DIRECTORY_SEPARATOR, '', $path);

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $images[] = $relPath;
            if (str_contains($relPath, 'All Image') || str_contains($relPath, 'All Photo')) {
                $allImages[] = $relPath;
            } else {
                // Find room folder name
                if (preg_match('#Image\\\\([^\\\\]+)#', $relPath, $m)) {
                    $roomImages[$m[1]][] = basename($relPath);
                }
            }
        } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            $videos[] = [
                'name' => basename($relPath),
                'path' => $relPath,
                'size_mb' => round($f->getSize() / (1024 * 1024), 2)
            ];
        } elseif (in_array($ext, ['mp3', 'wav', 'aac'])) {
            $audio[] = basename($relPath);
        } else {
            $other[] = $relPath;
        }
    }

    $report[$h] = [
        'total_images' => count($images),
        'gallery_images' => count($allImages),
        'room_count' => count($roomImages),
        'room_breakdown' => array_map('count', $roomImages),
        'videos' => $videos,
        'audio' => $audio,
        'other' => $other
    ];
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
