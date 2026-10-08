<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
echo "Users count: " . $users->count() . "\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | {$u->name} | {$u->email} | Role: {$u->role}\n";
}
