<?php
$content = file_get_contents(__DIR__ . '/../resources/views/pages/search-results.blade.php');
$lines = explode("\n", $content);
$stack = [];
foreach ($lines as $num => $line) {
    $lineNum = $num + 1;
    // match blade control directives
    if (preg_match('/@(if|unless|isset|empty)\b/', $line, $m)) {
        $stack[] = ['type' => $m[1], 'line' => $lineNum, 'content' => trim($line)];
    }
    if (preg_match('/@(endif|endunless|endisset|endempty)\b/', $line, $m)) {
        $last = array_pop($stack);
    }
}

echo "Remaining unclosed directives: " . count($stack) . "\n";
foreach ($stack as $item) {
    echo "Line {$item['line']}: @{$item['type']} -> {$item['content']}\n";
}
