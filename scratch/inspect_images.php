<?php
$files = [
    'C:/Users/Shawon/.gemini/antigravity-ide/brain/e1680189-52b4-46c7-b0aa-97158fd161a6/.user_uploaded/media_1791837430813.png',
    'C:/Users/Shawon/.gemini/antigravity-ide/brain/e1680189-52b4-46c7-b0aa-97158fd161a6/.user_uploaded/media_1791837311457.png',
    'C:/Users/Shawon/.gemini/antigravity-ide/brain/e1680189-52b4-46c7-b0aa-97158fd161a6/.user_uploaded/media_1791837172412.png',
];

foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $img = imagecreatefrompng($f);
    echo basename($f) . ": " . imagesx($img) . "x" . imagesy($img) . "\n";
}
