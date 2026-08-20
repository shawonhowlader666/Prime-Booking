<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$blade = $app->make('blade.compiler');
$content = file_get_contents(__DIR__ . '/../resources/views/pages/search-results.blade.php');
$compiled = $blade->compileString($content);

file_put_contents(__DIR__ . '/compiled_search.php', $compiled);

// Syntax check
exec('php -l ' . escapeshellarg(__DIR__ . '/compiled_search.php'), $output, $code);
echo implode("\n", $output) . "\n";
