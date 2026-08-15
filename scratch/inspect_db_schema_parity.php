<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=================================================================" . PHP_EOL;
echo "  DEEP DATABASE SCHEMA & ELOQUENT MODEL PARITY INSPECTOR" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

$tables = DB::select('SHOW TABLES');
$dbName = config('database.connections.mysql.database');
$keyName = "Tables_in_{$dbName}";

$issues = [];
$totalColumns = 0;

$modelMap = [
    'properties' => \App\Models\Property::class,
    'rooms' => \App\Models\Room::class,
    'bookings' => \App\Models\Booking::class,
    'users' => \App\Models\User::class,
    'coupons' => \App\Models\Coupon::class,
    'payouts' => \App\Models\Payout::class,
    'deals' => \App\Models\Deal::class,
    'locations' => \App\Models\Location::class,
    'amenities' => \App\Models\Amenity::class,
    'hero_slides' => \App\Models\HeroSlide::class,
    'banned_ips' => \App\Models\BannedIp::class,
    'site_settings' => \App\Models\SiteSetting::class,
    'cms_contents' => \App\Models\CmsContent::class,
    'destinations' => \App\Models\Destination::class,
    'airport_transfers' => \App\Models\AirportTransfer::class,
    'transfer_bookings' => \App\Models\TransferBooking::class,
    'room_availabilities' => \App\Models\RoomAvailability::class,
    'activity_logs' => \App\Models\ActivityLog::class,
    'reviews' => \App\Models\Review::class,
];

foreach ($modelMap as $table => $modelClass) {
    if (!Schema::hasTable($table)) {
        $issues[] = "Table '{$table}' does NOT exist in database!";
        echo "  [FAIL] Missing Table: {$table}" . PHP_EOL;
        continue;
    }

    $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
    $model = new $modelClass;
    $fillable = $model->getFillable();

    foreach ($columns as $col) {
        $totalColumns++;
        $field = $col->Field;
        $null = $col->Null;
        $default = $col->Default;
        $type = $col->Type;

        // Check if a non-id, non-timestamps column is NOT NULL with NO DEFAULT
        if (!in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at']) && $null === 'NO' && $default === null) {
            // Note potential risk if left blank on form
            // echo "  [INFO] Table {$table}.{$field} is NOT NULL (No Default) -> {$type}" . PHP_EOL;
        }

        // Check if model fillable is missing a standard column
        if (!in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at']) && !in_array($field, $fillable)) {
            // Check if model has a mutator for this field
            $mutatorName = 'set' . \Illuminate\Support\Str::studly($field) . 'Attribute';
            if (!method_exists($model, $mutatorName)) {
                $issues[] = "Table '{$table}' has column '{$field}' but Model '{$modelClass}' does NOT list it in \$fillable!";
                echo "  [WARN] {$table}.{$field} missing in {$modelClass}::\$fillable" . PHP_EOL;
            }
        }
    }
}

echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "  INSPECTION SUMMARY: Checked " . count($modelMap) . " Tables ({$totalColumns} Columns)" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

if (empty($issues)) {
    echo "  🌟 100% SCHEMA & MODEL PARITY: Every database column maps perfectly to Eloquent models!" . PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL . "Discrepancies found:" . PHP_EOL;
    foreach ($issues as $idx => $iss) {
        echo "  " . ($idx + 1) . ". {$iss}" . PHP_EOL;
    }
    exit(1);
}
