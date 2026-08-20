<?php
$content = file_get_contents(__DIR__ . '/../resources/views/pages/search-results.blade.php');
$lines = explode("\n", $content);
$stack = [];
foreach ($lines as $num => $line) {
    $lineNum = $num + 1;
    if (preg_match_all('/@(if|unless|isset|empty|forelse|foreach|for|while|section|can|cannot|auth|guest)\b/', $line, $matches)) {
        foreach ($matches[1] as $m) {
            $stack[] = ['type' => $m, 'line' => $lineNum, 'content' => trim($line)];
        }
    }
    if (preg_match_all('/@(endif|endunless|endisset|endempty|endforelse|endforeach|endfor|endwhile|endsection|endcan|endcannot|endauth|endguest)\b/', $line, $matches)) {
        foreach ($matches[1] as $m) {
            $expected = 'end' . end($stack)['type'];
            if ($m === $expected) {
                array_pop($stack);
            } else {
                echo "MISMATCH at line $lineNum: Got @$m but expected @$expected (opened at line " . end($stack)['line'] . ")\n";
                array_pop($stack);
            }
        }
    }
}

echo "Remaining unclosed directives: " . count($stack) . "\n";
foreach ($stack as $item) {
    echo "Line {$item['line']}: @{$item['type']} -> {$item['content']}\n";
}
