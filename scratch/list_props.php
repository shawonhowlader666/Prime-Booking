<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$props = \App\Models\Property::active()->select('id', 'name', 'slug', 'city', 'star_rating', 'price_per_night')->get();
foreach ($props as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Slug: {$p->slug} | Stars: {$p->star_rating} | Price: ৳{$p->price_per_night}\n";
}
