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
        $rowData[$colLetter] = trim($val);
    }
    $rows[$rNum] = $rowData;
}

$hotels = [];
for ($i = 2; $i <= 12; $i++) {
    if (!isset($rows[$i]) || empty($rows[$i]['B'])) continue;
    $r = $rows[$i];
    $hotels[] = [
        'sl' => $r['A'] ?? '',
        'name' => $r['B'] ?? '',
        'website' => $r['C'] ?? '',
        'type' => $r['D'] ?? 'Hotel',
        'star' => $r['E'] ?? '4 star',
        'city' => $r['F'] ?? "Cox's Bazar",
        'address' => $r['H'] ?? '',
        'map_link' => $r['I'] ?? '',
        'checkin' => $r['P'] ?? '2:00 PM',
        'checkout' => $r['Q'] ?? '12:00 PM',
        'phone' => $r['U'] ?? '',
    ];
}

echo json_encode($hotels, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
