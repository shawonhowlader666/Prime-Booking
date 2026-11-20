<?php
header('Content-Type: text/plain');

$base = dirname(__DIR__);
echo "Base: $base\n";
echo "routes dir exists: " . (is_dir($base . '/routes') ? 'YES' : 'NO') . "\n";
echo "routes/web.php exists: " . (file_exists($base . '/routes/web.php') ? 'YES' : 'NO') . "\n";
if (is_dir($base . '/routes')) {
    echo "Files in routes/:\n";
    print_r(scandir($base . '/routes'));
} else {
    echo "Files in base:\n";
    print_r(scandir($base));
}
