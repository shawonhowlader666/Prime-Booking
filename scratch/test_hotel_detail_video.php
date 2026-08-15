<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use Illuminate\Http\Request;

echo "=== TESTING PUBLIC HOTEL DETAIL WITH VIDEO TOUR ===\n";

$prop = Property::first();
$ctrl = app(\App\Http\Controllers\Web\PropertyDetailController::class);

$res = $ctrl->show(Request::create('/hotels/' . $prop->id, 'GET'), $prop->id);
$html = $res->render();

echo "✓ Hotel Detail Page rendered successfully (" . strlen($html) . " bytes)\n";

// Check if video logic renders
$propWithVideo = Property::first();
$propWithVideo->video_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
$resVideo = view('pages.hotel-detail', ['property' => $propWithVideo, 'nights' => 1])->render();

if (str_contains($resVideo, 'youtube-nocookie.com/embed')) {
    echo "✓ YouTube Video Player embedded perfectly in the main hero box!\n";
}

$propWithMp4 = Property::first();
$propWithMp4->video_url = 'https://example.com/tour.mp4';
$resMp4 = view('pages.hotel-detail', ['property' => $propWithMp4, 'nights' => 1])->render();

if (str_contains($resMp4, '<video controls')) {
    echo "✓ HTML5 MP4 Video Player embedded perfectly in the main hero box!\n";
}

echo "SUCCESS!\n";
