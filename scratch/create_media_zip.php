<?php
$zipFile = __DIR__ . '/hotels_images.zip';
if (file_exists($zipFile)) unlink($zipFile);

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Cannot create zip");
}

$hotelsDir = realpath(__DIR__ . '/../storage/app/public/hotels');
$rdi = new RecursiveDirectoryIterator($hotelsDir, RecursiveDirectoryIterator::SKIP_DOTS);
$rii = new RecursiveIteratorIterator($rdi);

$count = 0;
foreach ($rii as $f) {
    $path = $f->getPathname();
    $ext = strtolower($f->getExtension());
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $rel = 'hotels/' . str_replace('\\', '/', substr($path, strlen($hotelsDir) + 1));
        $zip->addFile($path, $rel);
        $count++;
    }
}

// Add branding images too
$brandDir = realpath(__DIR__ . '/../storage/app/public/branding');
if (is_dir($brandDir)) {
    foreach (scandir($brandDir) as $bf) {
        if ($bf === '.' || $bf === '..') continue;
        $bPath = $brandDir . '/' . $bf;
        if (is_file($bPath)) {
            $zip->addFile($bPath, 'branding/' . $bf);
        }
    }
}

$zip->close();
echo "Created $zipFile with $count images! Size: " . round(filesize($zipFile) / (1024 * 1024), 2) . " MB\n";
