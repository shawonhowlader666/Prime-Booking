<?php
$base = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd';

$rdi = new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS);
$rii = new RecursiveIteratorIterator($rdi);

$nonMediaFiles = [];
$counts = ['images' => 0, 'videos' => 0, 'docs' => 0, 'other' => 0];
$hotelStats = [];

foreach ($rii as $file) {
    $path = $file->getPathname();
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        $counts['images']++;
    } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
        $counts['videos']++;
    } elseif (in_array($ext, ['xlsx', 'xls', 'docx', 'doc', 'csv', 'txt', 'pdf', 'json'])) {
        $counts['docs']++;
        $nonMediaFiles[] = $path;
    } else {
        $counts['other']++;
        $nonMediaFiles[] = $path;
    }

    // Check hotel folder
    if (preg_match('#Website Element\\\\([^\x5c]+)#', $path, $m)) {
        $hotelName = $m[1];
        if (!isset($hotelStats[$hotelName])) {
            $hotelStats[$hotelName] = ['images' => 0, 'videos' => 0, 'rooms' => []];
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $hotelStats[$hotelName]['images']++;
        } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            $hotelStats[$hotelName]['videos']++;
        }
        if (preg_match('#Website Element\\\\'.preg_quote($hotelName).'\\\\Image\\\\([^\x5c]+)#', $path, $rm)) {
            $roomName = $rm[1];
            if ($roomName !== 'All Image' && !in_array($roomName, $hotelStats[$hotelName]['rooms'])) {
                $hotelStats[$hotelName]['rooms'][] = $roomName;
            }
        }
    }
}

echo "Counts: " . json_encode($counts) . "\n\n";
echo "Non-media files:\n";
foreach ($nonMediaFiles as $f) {
    echo "  $f\n";
}

echo "\nHotel Breakdown:\n";
foreach ($hotelStats as $h => $s) {
    echo "----------------------------------------\n";
    echo "Hotel: $h\n";
    echo "  Images: {$s['images']}, Videos: {$s['videos']}\n";
    echo "  Rooms (" . count($s['rooms']) . "): " . implode(', ', $s['rooms']) . "\n";
}
