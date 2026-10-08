<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/',
    '/hotels/1',
    '/search?city=Cox%27s+Bazar',
    '/destinations'
];

foreach ($urls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo "URL: $url -> Status: $status\n";
    if ($status >= 400 && $status != 404) {
        echo "  Error Content Snippet: " . substr($response->getContent(), 0, 300) . "\n";
    }
}
