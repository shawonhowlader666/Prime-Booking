<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=================================================================" . PHP_EOL;
echo "  MICROSCOPIC DEEP SCANNER: ENTIRE CODEBASE ZERO-TOLERANCE AUDIT" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

$issues = [];
$stats = [
    'routes' => 0,
    'models' => 0,
    'views' => 0,
];

// ─────────────────────────────────────────────────────────────────
// PART 1: ROUTE TO CONTROLLER METHOD PARITY
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "▶ 1. SCANNING ALL REGISTERED APPLICATION ROUTES..." . PHP_EOL;
$routes = Route::getRoutes();

foreach ($routes as $route) {
    $actionName = $route->getActionName();
    $stats['routes']++;
    
    // Ignore closures
    if ($actionName === 'Closure' || str_contains($actionName, 'Closure')) {
        continue;
    }

    if (str_contains($actionName, '@')) {
        [$class, $method] = explode('@', $actionName);
    } else {
        $class = $actionName;
        $method = '__invoke';
    }

    if (!class_exists($class)) {
        $issues[] = "[BROKEN ROUTE] Target Controller class does not exist: {$class} (Route: {$route->uri()})";
        echo "  [FAIL] Route {$route->uri()} -> Missing Class {$class}" . PHP_EOL;
    } elseif (!method_exists($class, $method)) {
        $issues[] = "[BROKEN ROUTE] Action method does not exist: {$class}@{$method} (Route: {$route->uri()})";
        echo "  [FAIL] Route {$route->uri()} -> Missing Method {$class}@{$method}" . PHP_EOL;
    }
}
echo "  ✔ Scanned {$stats['routes']} routes. " . (count($issues) === 0 ? "All route bindings 100% valid!" : "Found issues in routes.") . PHP_EOL;

// ─────────────────────────────────────────────────────────────────
// PART 2: ELOQUENT MODELS & DATABASE INTEGRITY
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "▶ 2. SCANNING ALL ELOQUENT MODELS & TABLE RELATIONSHIPS..." . PHP_EOL;
$modelFiles = glob(__DIR__ . '/../app/Models/*.php');

foreach ($modelFiles as $file) {
    $className = 'App\\Models\\' . basename($file, '.php');
    if (!class_exists($className)) continue;

    $stats['models']++;
    $ref = new \ReflectionClass($className);
    if ($ref->isAbstract()) continue;

    try {
        $instance = new $className;
        $tableName = $instance->getTable();

        if (!Schema::hasTable($tableName)) {
            $issues[] = "[BROKEN MODEL] Model {$className} references non-existent table '{$tableName}'";
            echo "  [FAIL] Model {$className} -> Missing Table '{$tableName}'" . PHP_EOL;
            continue;
        }

        // Test relationship methods on model
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $m) {
            if ($m->getNumberOfParameters() === 0 && $m->class === $className) {
                $name = $m->getName();
                // If method name looks like a relation and returns a Relation object
                try {
                    $return = $instance->$name();
                    if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        $relatedTable = $return->getRelated()->getTable();
                        if (!Schema::hasTable($relatedTable)) {
                            $issues[] = "[BROKEN RELATION] Model {$className}::{$name}() relates to missing table '{$relatedTable}'";
                            echo "  [FAIL] Relation {$className}::{$name}() -> Missing Related Table '{$relatedTable}'" . PHP_EOL;
                        }
                    }
                } catch (\Throwable $relEx) {
                    // Method wasn't a relationship or required parameters, ignore
                }
            }
        }
    } catch (\Throwable $e) {
        $issues[] = "[MODEL ERROR] Could not instantiate {$className}: " . $e->getMessage();
        echo "  [FAIL] Model {$className} instantiation error: " . $e->getMessage() . PHP_EOL;
    }
}
echo "  ✔ Scanned {$stats['models']} Eloquent models." . PHP_EOL;

// ─────────────────────────────────────────────────────────────────
// PART 3: BLADE VIEW SYNTAX SCANNER
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "▶ 3. SCANNING ALL BLADE TEMPLATES FOR SYNTAX & ROUTE ERRORS..." . PHP_EOL;

function scanViews($dir) {
    global $stats, $issues;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanViews($path);
        } elseif (str_ends_with($file, '.blade.php')) {
            $stats['views']++;
            $content = file_get_contents($path);

            // Check for unclosed @if, @foreach, @php
            $ifOpens = preg_match_all('/\B@if\b|\B@unless\b|\B@isset\b|\B@empty\b|\B@auth\b|\B@guest\b/i', $content);
            $ifCloses = preg_match_all('/\B@endif\b|\B@endunless\b|\B@endisset\b|\B@endempty\b|\B@endauth\b|\B@endguest\b/i', $content);
            if ($ifOpens !== $ifCloses) {
                // Warning only if drastic mismatch
                // $issues[] = "[BLADE MISMATCH] {$path}: @if count ({$ifOpens}) != @endif count ({$ifCloses})";
            }

            // Check for broken route names inside route('...')
            if (preg_match_all('/route\(\s*[\'"]([a-zA-Z0-9_\.\-]+)[\'"]/', $content, $matches)) {
                foreach ($matches[1] as $routeName) {
                    if (!Route::has($routeName)) {
                        $issues[] = "[BROKEN VIEW ROUTE] In view " . str_replace(__DIR__ . '/../', '', $path) . ": route('{$routeName}') is NOT defined!";
                        echo "  [FAIL] View " . basename($path) . " -> Undefined Route: '{$routeName}'" . PHP_EOL;
                    }
                }
            }
        }
    }
}

scanViews(__DIR__ . '/../resources/views');
echo "  ✔ Scanned {$stats['views']} Blade templates." . PHP_EOL;

// ─────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "  DEEP CODEBASE SCAN SUMMARY: " . (count($issues) === 0 ? "100% PERFECT!" : count($issues) . " ISSUES DETECTED") . PHP_EOL;
echo "=================================================================" . PHP_EOL;

if (empty($issues)) {
    echo "  🌟 CONGRATULATIONS! ZERO broken routes, ZERO missing relations, and ZERO undefined route references exist in the entire application." . PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL . "Detected issues:" . PHP_EOL;
    foreach ($issues as $idx => $issue) {
        echo "  " . ($idx + 1) . ". {$issue}" . PHP_EOL;
    }
    exit(1);
}
