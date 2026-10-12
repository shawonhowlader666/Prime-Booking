<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

echo "--- Current Settings ---\n";
$current = SiteSetting::whereIn('key', ['support_phone', 'support_email', 'site_phone', 'site_email', 'phone', 'email', 'hotline'])->get();
foreach ($current as $s) {
    echo "{$s->key} => {$s->value}\n";
}

echo "\n--- Updating Settings ---\n";
SiteSetting::set('support_phone', '01756000111');
SiteSetting::set('support_email', 'primebooking.bd@gmail.com');
SiteSetting::set('site_phone', '01756000111');
SiteSetting::set('site_email', 'primebooking.bd@gmail.com');

Cache::flush();
Cache::forget('site_setting:support_phone');
Cache::forget('site_setting:support_email');
Cache::forget('site_setting:site_phone');
Cache::forget('site_setting:site_email');

echo "\n--- Updated Settings ---\n";
$updated = SiteSetting::whereIn('key', ['support_phone', 'support_email', 'site_phone', 'site_email'])->get();
foreach ($updated as $s) {
    echo "{$s->key} => {$s->value}\n";
}
echo "Done!\n";
