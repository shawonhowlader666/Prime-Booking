<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hotelController = $app->make(App\Http\Controllers\Web\PropertyDetailController::class);
try {
    $req = Illuminate\Http\Request::create('/hotels/1');
    $res = $hotelController->show($req, 1);
    echo "PropertyDetailController::show(1) rendered successfully! Class: " . get_class($res) . "\n";
} catch (\Throwable $e) {
    echo "ERROR in show(1): " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}

$pageController = $app->make(App\Http\Controllers\PageController::class);
try {
    $req = Illuminate\Http\Request::create('/');
    $res = $pageController->index($req);
    echo "PageController::index rendered successfully! Class: " . get_class($res) . "\n";
} catch (\Throwable $e) {
    echo "ERROR in page index: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}

$searchController = $app->make(App\Http\Controllers\Web\SearchController::class);
try {
    $searchReq = new App\Http\Requests\Web\SearchRequest(['city' => "Cox's Bazar"]);
    $res = $searchController->index($searchReq);
    echo "SearchController::index rendered successfully! Class: " . get_class($res) . "\n";
} catch (\Throwable $e) {
    echo "ERROR in search index: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}


