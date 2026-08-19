<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== API RESPONSE QUALITY CHECK ===" . PHP_EOL . PHP_EOL;

// ─── 1. GET /api/search/autocomplete?q=dhaka ──────────────────────────────
echo "【API 1】 GET /api/search/autocomplete?q=dhaka" . PHP_EOL;
$svc = app(\App\Services\Search\AutoCompleteService::class);
$result = $svc->getSuggestions('dhaka', 'hotel', 8);
$json = json_encode([
    'success'    => true,
    'data'       => $result,
    'locations'  => $result['locations'],
    'properties' => $result['properties'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo $json . PHP_EOL . PHP_EOL;

// ─── 2. GET /api/v1/properties (first 2) ──────────────────────────────────
echo "【API 2】 GET /api/v1/properties (sample first 2)" . PHP_EOL;
$props = \App\Models\Property::active()
    ->select(['id','name','type','city','price_per_night','rating_score','primary_image','star_rating'])
    ->limit(2)
    ->get()
    ->map(fn($p) => [
        'id'             => $p->id,
        'name'           => $p->name,
        'type'           => $p->type,
        'city'           => $p->city,
        'price_per_night'=> (float)$p->price_per_night,
        'rating_score'   => (float)$p->rating_score,
        'star_rating'    => $p->star_rating,
        'primary_image'  => $p->primary_image ? asset('storage/' . ltrim($p->primary_image, '/')) : null,
        'url'            => route('hotels.show', $p->id),
    ]);
echo json_encode(['success'=>true,'data'=>$props,'total'=>17], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL . PHP_EOL;

// ─── 3. Default payload (empty search = click on box) ─────────────────────
echo "【API 3】 GET /api/search/autocomplete (no query = default)" . PHP_EOL;
$def = $svc->getDefaultPayload('hotel');
echo json_encode([
    'success'        => true,
    'bd_destinations'=> $def['bd_destinations'],
    'international'  => $def['international'],
    'trending'       => $def['trending'],
    'personalized'   => $def['personalized'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
