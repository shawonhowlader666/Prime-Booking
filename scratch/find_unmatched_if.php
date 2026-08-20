<?php
$lines = file(__DIR__ . '/compiled_search.php');
$ifStack = [];
foreach ($lines as $num => $line) {
    $lineNum = $num + 1;
    // count if statements
    if (preg_match('/\bif\s*\(/', $line)) {
        $ifStack[] = ['line' => $lineNum, 'content' => trim($line)];
    }
    if (preg_match('/\bendif\s*;/', $line)) {
        array_pop($ifStack);
    }
}

echo "Unmatched if statements in compiled PHP: " . count($ifStack) . "\n";
foreach ($ifStack as $item) {
    echo "Line {$item['line']}: {$item['content']}\n";
}
