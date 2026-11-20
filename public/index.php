<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$baseDir = dirname(__DIR__);

// Auto-heal missing .env in fresh Git deployment by finding existing production .env
if (!file_exists($baseDir . '/.env')) {
    $candidates = [
        dirname($baseDir) . '/prime-booking/.env',
        '/home/u345402313/domains/primebooking.com.bd/prime-booking/.env',
        $baseDir . '/.env.production',
        $baseDir . '/.env.example',
    ];
    foreach ($candidates as $cand) {
        if (file_exists($cand)) {
            @copy($cand, $baseDir . '/.env');
            break;
        }
    }
}

// Auto-ensure framework storage directories exist
$storageDirs = [
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
];
foreach ($storageDirs as $sDir) {
    if (!is_dir($sDir)) {
        @mkdir($sDir, 0775, true);
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $baseDir . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
if (file_exists($baseDir . '/vendor/autoload.php')) {
    require $baseDir . '/vendor/autoload.php';
} elseif (file_exists(dirname($baseDir) . '/prime-booking/vendor/autoload.php')) {
    require dirname($baseDir) . '/prime-booking/vendor/autoload.php';
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $baseDir . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
