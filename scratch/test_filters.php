<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING /api/v1/search/filter-metadata ===" . PHP_EOL;

$agg = app(\App\Services\Search\FilterAggregator::class);
$meta = $agg->getFilterMetadata();

echo json_encode(['success' => true, 'data' => $meta], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
