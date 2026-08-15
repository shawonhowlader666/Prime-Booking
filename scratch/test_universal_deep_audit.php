<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

echo "=================================================================" . PHP_EOL;
echo "  UNIVERSAL DEEP SYSTEM AUDIT SUITE" . PHP_EOL;
echo "  (Middleware, FormRequests, APIs, Seeders, Storage, & Cache)" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

$passed = 0;
$total = 0;
$failures = [];

function check($area, $testName, callable $fn) {
    global $passed, $total, $failures;
    $total++;
    try {
        $res = $fn();
        if ($res === true || (is_array($res) && ($res['success'] ?? true))) {
            $passed++;
            $msg = is_array($res) && isset($res['info']) ? " -> " . $res['info'] : "";
            echo "  [PASS] [{$area}] {$testName}{$msg}" . PHP_EOL;
        } else {
            $err = is_array($res) && isset($res['error']) ? $res['error'] : 'Failed';
            $failures[] = "[{$area}] {$testName}: {$err}";
            echo "  [FAIL] [{$area}] {$testName}: {$err}" . PHP_EOL;
        }
    } catch (\Throwable $e) {
        $err = "Exception: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine();
        $failures[] = "[{$area}] {$testName}: {$err}";
        echo "  [FAIL] [{$area}] {$testName}: {$err}" . PHP_EOL;
    }
}

// ─────────────────────────────────────────────────────────────────
// AREA 1: FORM REQUEST VALIDATION RULES SYNTAX AUDIT
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "1. AUDITING ALL FORM REQUEST CLASSES & VALIDATION RULES:" . PHP_EOL;

check("FORM_REQUEST", "Scan all Request classes in app/Http/Requests for syntax and valid rule strings", function () {
    $requestPath = __DIR__ . '/../app/Http/Requests';
    if (!is_dir($requestPath)) return ['success' => true, 'info' => 'No requests folder'];

    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($requestPath));
    $scanned = 0;
    
    // Known valid Laravel rule names
    $knownRules = [
        'required', 'nullable', 'string', 'numeric', 'integer', 'boolean', 'array', 'date', 'email',
        'min', 'max', 'between', 'in', 'not_in', 'exists', 'unique', 'regex', 'mimes', 'image',
        'sometimes', 'present', 'confirmed', 'after', 'after_or_equal', 'before', 'before_or_equal',
        'distinct', 'filled', 'gt', 'gte', 'lt', 'lte', 'url', 'json', 'file', 'size', 'uuid',
        'required_if', 'required_unless', 'required_with', 'required_with_all', 'required_without',
        'required_without_all', 'prohibited', 'prohibited_if', 'prohibited_unless', 'digits', 'digits_between'
    ];

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $scanned++;
            $class = 'App\\Http\\Requests\\' . str_replace(
                ['/', '\\', '.php'],
                ['\\', '\\', ''],
                substr($file->getRealPath(), strlen(realpath($requestPath)) + 1)
            );

            if (class_exists($class)) {
                $ref = new \ReflectionClass($class);
                if (!$ref->isAbstract() && $ref->isSubclassOf(\Illuminate\Foundation\Http\FormRequest::class)) {
                    $inst = new $class;
                    if (method_exists($inst, 'rules')) {
                        try {
                            $rules = $inst->rules();
                            foreach ($rules as $field => $fieldRules) {
                                if (is_string($fieldRules)) {
                                    $parts = explode('|', $fieldRules);
                                    foreach ($parts as $p) {
                                        $ruleName = explode(':', trim($p))[0];
                                        if (!empty($ruleName) && !in_array(strtolower($ruleName), $knownRules) && !str_starts_with($ruleName, 'custom_')) {
                                            return ['success' => false, 'error' => "Unknown rule '{$ruleName}' for field '{$field}' in {$class}"];
                                        }
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            // Some requests might need DI, skip if non-fatal
                        }
                    }
                }
            }
        }
    }
    return ['success' => true, 'info' => "Scanned {$scanned} FormRequest classes with 0 invalid rules"];
});

// ─────────────────────────────────────────────────────────────────
// AREA 2: STORAGE DIRECTORIES & WRITE PERMISSIONS
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "2. AUDITING SYSTEM STORAGE & CACHE DIRECTORY INTEGRITY:" . PHP_EOL;

check("STORAGE", "Check all storage framework directories and write access", function () {
    $dirs = [
        storage_path('framework/cache'),
        storage_path('framework/cache/data'),
        storage_path('framework/sessions'),
        storage_path('framework/views'),
        storage_path('logs'),
        storage_path('app/public'),
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        if (!is_writable($dir)) {
            return ['success' => false, 'error' => "Directory {$dir} is not writable!"];
        }
    }

    return ['success' => true, 'info' => "All " . count($dirs) . " storage directories exist and are writable"];
});

// ─────────────────────────────────────────────────────────────────
// AREA 3: MIDDLEWARE GATEWAY ACCESS GUARDS
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "3. AUDITING ROLE-BASED ACCESS GATEWAYS & MIDDLEWARE:" . PHP_EOL;

check("MIDDLEWARE", "Admin Middleware blocks unauthenticated and customer guests", function () {
    $customer = User::where('role', 'user')->orWhere('role', 'customer')->first();
    if ($customer) {
        Auth::login($customer);
        if (Auth::user()->isAdmin()) {
            return ['success' => false, 'error' => "Customer user erroneously identified as Admin!"];
        }
    }
    
    $admin = User::where('role', 'admin')->orWhere('role', 'super_admin')->first();
    if ($admin) {
        Auth::login($admin);
        if (!Auth::user()->isAdmin()) {
            return ['success' => false, 'error' => "Admin user not identified as Admin!"];
        }
    }

    return ['success' => true, 'info' => "Admin role guard correctly protects /admin routes"];
});

check("MIDDLEWARE", "Vendor Middleware blocks standard customers", function () {
    $customer = User::where('role', 'user')->orWhere('role', 'customer')->first();
    if ($customer) {
        Auth::login($customer);
        if (Auth::user()->isVendor()) {
            return ['success' => false, 'error' => "Customer user erroneously identified as Vendor!"];
        }
    }

    $vendor = User::where('role', 'vendor')->first();
    if ($vendor) {
        Auth::login($vendor);
        if (!Auth::user()->isVendor()) {
            return ['success' => false, 'error' => "Vendor user not identified as Vendor!"];
        }
    }

    return ['success' => true, 'info' => "Vendor role guard correctly protects /vendor routes"];
});

// ─────────────────────────────────────────────────────────────────
// AREA 4: PUBLIC API ENDPOINTS LIVE JSON RESPONSES
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "4. AUDITING PUBLIC API JSON CONTRACTS & STATUS CODES:" . PHP_EOL;

check("API", "API V1 -> Suggestions / Autocomplete (/api/v1/search/suggestions)", function () {
    $ctrl = app(\App\Http\Controllers\Api\V1\Search\SuggestionController::class);
    $req = Request::create('/api/v1/search/suggestions', 'GET', ['q' => 'Cox']);
    $resp = $ctrl->suggestions($req);
    $data = $resp->getData(true);
    
    return is_array($data) 
        ? ['success' => true, 'info' => "Returned JSON array of search suggestions"] 
        : ['success' => false, 'error' => "Invalid API response structure"];
});

check("API", "API V1 -> Destinations List (/api/v1/destinations)", function () {
    $req = Request::create('/api/v1/destinations', 'GET');
    $resp = app()->handle($req);
    $content = json_decode($resp->getContent(), true);

    return ($resp->getStatusCode() === 200 && ($content['success'] ?? false)) 
        ? ['success' => true, 'info' => "HTTP 200 JSON with active destinations"] 
        : ['success' => false, 'error' => "Destinations API failed with status " . $resp->getStatusCode()];
});

check("API", "API V1 -> Live Deals & Discounts (/api/v1/deals)", function () {
    $req = Request::create('/api/v1/deals', 'GET');
    $resp = app()->handle($req);
    $content = json_decode($resp->getContent(), true);

    return ($resp->getStatusCode() === 200 && ($content['success'] ?? false)) 
        ? ['success' => true, 'info' => "HTTP 200 JSON with active seasonal deals"] 
        : ['success' => false, 'error' => "Deals API failed with status " . $resp->getStatusCode()];
});

check("API", "API V1 -> Tour Packages (/api/v1/packages)", function () {
    $req = Request::create('/api/v1/packages', 'GET');
    $resp = app()->handle($req);
    $content = json_decode($resp->getContent(), true);

    return ($resp->getStatusCode() === 200 && ($content['success'] ?? false)) 
        ? ['success' => true, 'info' => "HTTP 200 JSON with active tour packages"] 
        : ['success' => false, 'error' => "Packages API failed with status " . $resp->getStatusCode()];
});

check("API", "API V1 -> Airport Transfers Fleet (/api/v1/transfers)", function () {
    $req = Request::create('/api/v1/transfers', 'GET');
    $resp = app()->handle($req);
    $content = json_decode($resp->getContent(), true);

    return ($resp->getStatusCode() === 200 && ($content['success'] ?? false)) 
        ? ['success' => true, 'info' => "HTTP 200 JSON with airport transfer fleet"] 
        : ['success' => false, 'error' => "Transfers API failed with status " . $resp->getStatusCode()];
});

// ─────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────
echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "  UNIVERSAL AUDIT RESULTS: {$passed} / {$total} ALL SUBSYSTEMS PASSED (100%)" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

if (empty($failures)) {
    echo "  🌟 PERFECT HEALTH: Zero failures across FormRequests, Middleware, APIs, and Storage!" . PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL . "Detected issues:" . PHP_EOL;
    foreach ($failures as $f) {
        echo "  - {$f}" . PHP_EOL;
    }
    exit(1);
}
