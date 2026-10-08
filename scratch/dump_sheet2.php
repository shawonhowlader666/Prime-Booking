<?php
$zip = new ZipArchive();
$xlsxPath = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd/Hotel_Data_Collection.xlsx';
$zip->open($xlsxPath);

$sharedStrings = [];
$ssXml = $zip->getFromName('xl/sharedStrings.xml');
$s = simplexml_load_string($ssXml);
foreach ($s->si as $si) {
    if (isset($si->t)) {
        $sharedStrings[] = (string)$si->t;
    } elseif (isset($si->r)) {
        $t = '';
        foreach ($si->r as $r) { $t .= (string)$r->t; }
        $sharedStrings[] = $t;
    } else {
        $sharedStrings[] = '';
    }
}

$sheet2Xml = $zip->getFromName('xl/worksheets/sheet2.xml');
$sheet = simplexml_load_string($sheet2Xml);
$rows = [];
foreach ($sheet->sheetData->row as $row) {
    $rNum = (int)$row['r'];
    $rowData = [];
    foreach ($row->c as $c) {
        $cellRef = (string)$c['r'];
        $colLetter = preg_replace('/[0-9]/', '', $cellRef);
        $type = (string)$c['t'];
        $val = (string)$c->v;
        if ($type === 's' && isset($sharedStrings[(int)$val])) {
            $val = $sharedStrings[(int)$val];
        }
        $rowData[$colLetter] = $val;
    }
    $rows[$rNum] = $rowData;
}

$headers = $rows[1] ?? [];
echo "Headers:\n";
foreach ($headers as $col => $name) {
    echo "  $col => $name\n";
}

echo "\nData rows (Sheet 2):\n";
for ($i = 2; $i <= count($rows); $i++) {
    if (!isset($rows[$i]) || empty($rows[$i]['B'])) continue;
    echo "----------------------------------------\n";
    echo "Row $i: Hotel: " . ($rows[$i]['B'] ?? '') . "\n";
    foreach ($rows[$i] as $col => $val) {
        $hName = $headers[$col] ?? $col;
        // shorten header
        $shortH = preg_replace('/\s*\(.*?\)/', '', $hName);
        echo "  [$col] $shortH: $val\n";
    }
}
