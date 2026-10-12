<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;

echo "Seeding Agoda 1:1 Promotions...\n";

// 1. Flights & Activities Promotions
Promotion::updateOrCreate(
    ['title' => 'Ready, Set, Go!'],
    [
        'subtitle'     => 'Prices That\'ll Surprise You!',
        'badge_text'   => 'Worldwide',
        'cta_text'     => 'Up to 7% off',
        'cta_link'     => '/airport-transfer',
        'icon'         => '✈️',
        'bg_color'     => '#7c3aed',
        'bg_color_end' => '#4f46e5',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#a855f7',
        'type'         => 'flights',
        'is_active'    => true,
        'is_featured'  => true,
        'sort_order'   => 1,
    ]
);

Promotion::updateOrCreate(
    ['title' => 'Fly to Cox\'s Bazar'],
    [
        'subtitle'     => 'Daily Direct Flights from Dhaka',
        'badge_text'   => 'Special Fares',
        'cta_text'     => 'From ৳ 3,499',
        'cta_link'     => '/search?destination=Cox%27s+Bazar',
        'icon'         => '🛫',
        'bg_color'     => '#0284c7',
        'bg_color_end' => '#0369a1',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#38bdf8',
        'type'         => 'flights',
        'is_active'    => true,
        'is_featured'  => true,
        'sort_order'   => 2,
    ]
);

Promotion::updateOrCreate(
    ['title' => 'Sajek & Sylhet Adventure'],
    [
        'subtitle'     => 'Activities & Sightseeing Tours',
        'badge_text'   => 'Explore BD',
        'cta_text'     => 'Save up to 15%',
        'cta_link'     => '/tour-packages',
        'icon'         => '🌄',
        'bg_color'     => '#059669',
        'bg_color_end' => '#047857',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#10b981',
        'type'         => 'activities',
        'is_active'    => true,
        'is_featured'  => false,
        'sort_order'   => 3,
    ]
);

// 2. Properties we think you'll like (Accommodation Promos)
Promotion::updateOrCreate(
    ['title' => 'Discover BWH Hotels'],
    [
        'subtitle'     => 'Explore Deals Across Thailand & Asia',
        'badge_text'   => 'Flagship Store',
        'cta_text'     => 'View Deals',
        'cta_link'     => '/search',
        'icon'         => '🏨',
        'bg_color'     => '#1e293b',
        'bg_color_end' => '#0f172a',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#f59e0b',
        'type'         => 'accommodation',
        'is_active'    => true,
        'is_featured'  => true,
        'sort_order'   => 1,
    ]
);

Promotion::updateOrCreate(
    ['title' => 'Prime Booking\'s Top Stays!'],
    [
        'subtitle'     => 'Curated 5-Star & Beachfront Luxury',
        'badge_text'   => 'Flagship Deals',
        'cta_text'     => 'Explore Stays',
        'cta_link'     => '/hotels',
        'icon'         => '✨',
        'bg_color'     => '#1e1b4b',
        'bg_color_end' => '#0f172a',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#fbbf24',
        'type'         => 'accommodation',
        'is_active'    => true,
        'is_featured'  => true,
        'sort_order'   => 2,
    ]
);

Promotion::updateOrCreate(
    ['title' => 'Last Minute Hotel Deals'],
    [
        'subtitle'     => 'Up to 40% Off Tonight\'s Stays',
        'badge_text'   => 'Secret Deals',
        'cta_text'     => 'Grab Deal',
        'cta_link'     => '/deals',
        'icon'         => '🔥',
        'bg_color'     => '#b91c1c',
        'bg_color_end' => '#991b1b',
        'text_color'   => '#ffffff',
        'badge_bg'     => '#f87171',
        'type'         => 'accommodation',
        'is_active'    => true,
        'is_featured'  => false,
        'sort_order'   => 3,
    ]
);

Cache::forget('promotions:accommodation');
Cache::forget('promotions:flights_activities');
Cache::flush();

echo "Seeded successfully! Total promotions: " . Promotion::count() . "\n";
foreach (Promotion::all() as $p) {
    echo " - [{$p->type}] {$p->title} ({$p->badge_text})\n";
}
