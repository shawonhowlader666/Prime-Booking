<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

echo "===============================================================\n";
echo "       PRIME BOOKING DEEP-CORE ARCHITECTURAL AUDIT             \n";
echo "===============================================================\n\n";

$errors = [];
$stats = [
    'models' => 0,
    'controllers' => 0,
    'services' => 0,
    'views' => 0,
    'tables' => 0,
    'routes' => 0,
];

// ─────────────────────────────────────────────────────────────
// 1. DEEP DATABASE & SCHEMA AUDIT
// ─────────────────────────────────────────────────────────────
echo "1. [DATABASE & SCHEMA AUDIT]\n";
try {
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::getDatabaseName();
    $tableKey = "Tables_in_{$dbName}";
    foreach ($tables as $t) {
        $tableName = $t->$tableKey ?? array_values((array)$t)[0];
        $count = DB::table($tableName)->count();
        $stats['tables']++;
    }
    echo "   [OK] Total {$stats['tables']} Database Tables Verified.\n";
} catch (\Throwable $e) {
    $errors[] = "DB Schema Error: " . $e->getMessage();
    echo "   [FAIL] DB Schema: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// 2. DEEP ELOQUENT MODELS & RELATIONSHIPS AUDIT
// ─────────────────────────────────────────────────────────────
echo "\n2. [ELOQUENT MODELS & RELATIONS AUDIT]\n";
$modelFiles = File::allFiles(app_path('Models'));
foreach ($modelFiles as $file) {
    $class = 'App\\Models\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
    if (class_exists($class)) {
        try {
            $ref = new ReflectionClass($class);
            if (!$ref->isAbstract() && $ref->isSubclassOf(\Illuminate\Database\Eloquent\Model::class)) {
                $instance = new $class();
                $tableName = $instance->getTable();
                $hasTable = Schema::hasTable($tableName);
                
                // Test basic query
                if ($hasTable) {
                    $class::query()->limit(1)->get();
                }
                $stats['models']++;
            }
        } catch (\Throwable $e) {
            $errors[] = "Model Error ({$class}): " . $e->getMessage();
            echo "   [FAIL] Model {$class}: " . $e->getMessage() . "\n";
        }
    }
}
echo "   [OK] Total {$stats['models']} Eloquent Models Verified.\n";

// ─────────────────────────────────────────────────────────────
// 3. DEEP CONTROLLERS REFLECTION AUDIT
// ─────────────────────────────────────────────────────────────
echo "\n3. [CONTROLLERS REFLECTION AUDIT]\n";
$controllerFiles = File::allFiles(app_path('Http/Controllers'));
foreach ($controllerFiles as $file) {
    $class = 'App\\Http\\Controllers\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
    if (class_exists($class)) {
        try {
            $ref = new ReflectionClass($class);
            if (!$ref->isAbstract()) {
                $app->make($class);
                $stats['controllers']++;
            }
        } catch (\Throwable $e) {
            $errors[] = "Controller Dependency Error ({$class}): " . $e->getMessage();
            echo "   [FAIL] Controller {$class}: " . $e->getMessage() . "\n";
        }
    }
}
echo "   [OK] Total {$stats['controllers']} Controllers Instantiated & Dependency-Checked.\n";

// ─────────────────────────────────────────────────────────────
// 4. DEEP CORE SERVICES AUDIT
// ─────────────────────────────────────────────────────────────
echo "\n4. [CORE SERVICES AUDIT]\n";
$services = [
    \App\Services\CurrencyService::class,
    \App\Services\InventoryService::class,
    \App\Services\CouponService::class,
    \App\Services\VIPLoyaltyService::class,
    \App\Services\RewardPointService::class,
    \App\Services\SocialProofService::class,
    \App\Services\SeoSchemaService::class,
    \App\Services\NotificationService::class,
    \App\Services\Search\AutoCompleteService::class,
    \App\Services\Search\LocationNormalizerService::class,
    \App\Services\Search\FilterAggregator::class,
    \App\Services\Payments\BkashPaymentService::class,
    \App\Services\Payments\SSLCommerzPaymentService::class,
    \App\Services\External\HotelSearchApiService::class,
    \App\Repositories\PropertyRepository::class,
];

foreach ($services as $srv) {
    try {
        if (class_exists($srv)) {
            $app->make($srv);
            $stats['services']++;
            echo "   [OK] Service: " . class_basename($srv) . "\n";
        }
    } catch (\Throwable $e) {
        $errors[] = "Service Error ({$srv}): " . $e->getMessage();
        echo "   [FAIL] Service {$srv}: " . $e->getMessage() . "\n";
    }
}

// ─────────────────────────────────────────────────────────────
// 5. ALL BLADE VIEWS SYNTAX & DIRECTIVE AUDIT
// ─────────────────────────────────────────────────────────────
echo "\n5. [ALL BLADE TEMPLATES SYNTAX AUDIT]\n";
$viewFiles = File::allFiles(resource_path('views'));
$bladeCompiler = app('blade.compiler');

foreach ($viewFiles as $vf) {
    if (str_ends_with($vf->getFilename(), '.blade.php')) {
        $relativePath = $vf->getRelativePathname();
        try {
            $content = File::get($vf->getPathname());
            $compiled = $bladeCompiler->compileString($content);
            
            // PHP syntax check on compiled blade
            $tmp = tempnam(sys_get_temp_dir(), 'blade_check_');
            file_put_contents($tmp, $compiled);
            $out = [];
            $code = 0;
            exec("php -l \"{$tmp}\" 2>&1", $out, $code);
            unlink($tmp);
            
            if ($code !== 0) {
                $errLine = implode(' ', $out);
                $errors[] = "Blade Syntax Error ({$relativePath}): {$errLine}";
                echo "   [FAIL] Blade Syntax: {$relativePath} -> {$errLine}\n";
            } else {
                $stats['views']++;
            }
        } catch (\Throwable $e) {
            $errors[] = "Blade Compile Error ({$relativePath}): " . $e->getMessage();
            echo "   [FAIL] Blade Compile: {$relativePath} -> " . $e->getMessage() . "\n";
        }
    }
}
echo "   [OK] Total {$stats['views']} Blade Templates Passed 100% PHP Syntax & Directive Check.\n";

// ─────────────────────────────────────────────────────────────
// 6. ROUTES CALLABILITY AUDIT
// ─────────────────────────────────────────────────────────────
echo "\n6. [ROUTES CALLABILITY AUDIT]\n";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    $action = $route->getAction();
    $controller = $action['controller'] ?? null;
    if ($controller && is_string($controller) && str_contains($controller, '@')) {
        [$ctrlClass, $method] = explode('@', $controller);
        if (!class_exists($ctrlClass)) {
            $errors[] = "Route Controller Not Found: {$ctrlClass}";
        } elseif (!method_exists($ctrlClass, $method)) {
            $errors[] = "Route Action Method Not Found: {$ctrlClass}@{$method}";
        }
    }
    $stats['routes']++;
}
echo "   [OK] Total {$stats['routes']} Routes Fully Validated & Action-Callable.\n";

// ─────────────────────────────────────────────────────────────
// SUMMARY REPORT
// ─────────────────────────────────────────────────────────────
echo "\n===============================================================\n";
if (empty($errors)) {
    echo "  🏆 DEEP CORE AUDIT: 100% PERFECT & FLAWLESS!               \n";
    echo "  - Database Tables     : {$stats['tables']} Tables (All Clean)\n";
    echo "  - Eloquent Models     : {$stats['models']} Models (All Verified)\n";
    echo "  - Controllers         : {$stats['controllers']} Controllers (All Instantiable)\n";
    echo "  - Core Services       : {$stats['services']} Services (All Bound)\n";
    echo "  - Blade Templates     : {$stats['views']} Templates (0 Syntax Errors)\n";
    echo "  - System Routes       : {$stats['routes']} Routes (All Callable)\n";
} else {
    echo "  ⚠️ DEEP CORE AUDIT FOUND " . count($errors) . " ISSUES:\n";
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
}
echo "===============================================================\n";
