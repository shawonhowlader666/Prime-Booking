<?php
$html = file_get_contents('https://www.agoda.com/search?city=1390', false, stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
]));

if ($html) {
    preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches);
    if (!empty($matches[1])) {
        $allCss = implode("\n", $matches[1]);
        file_put_contents('public/css/agoda/agoda-critical.css', $allCss);
        echo "Saved agoda-critical.css (" . strlen($allCss) . " bytes)\n";
    }

    // Also extract all external CSS links
    preg_match_all('/<link[^>]+rel=[\'"]stylesheet[\'"][^>]+href=[\'"]([^\'"]+)[\'"]/i', $html, $linkMatches);
    if (!empty($linkMatches[1])) {
        foreach ($linkMatches[1] as $idx => $link) {
            echo "Found external CSS: $link\n";
            $cssContent = @file_get_contents($link, false, stream_context_create([
                'http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]
            ]));
            if ($cssContent) {
                file_put_contents("public/css/agoda/agoda-ext-{$idx}.css", $cssContent);
                echo "Saved agoda-ext-{$idx}.css (" . strlen($cssContent) . " bytes)\n";
            }
        }
    }
} else {
    echo "Failed to load Agoda page\n";
}
