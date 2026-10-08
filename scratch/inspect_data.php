<?php
$zip = new ZipArchive();
$xlsxPath = 'C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd/Hotel_Data_Collection.xlsx';

if ($zip->open($xlsxPath) === true) {
    echo "Files in xlsx:\n";
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (str_ends_with($name, '.xml')) {
            echo " - $name\n";
        }
    }

    // Read sharedStrings
    $sharedStrings = [];
    $ssXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($ssXml) {
        $s = simplexml_load_string($ssXml);
        foreach ($s->si as $si) {
            if (isset($si->t)) {
                $sharedStrings[] = (string)$si->t;
            } elseif (isset($si->r)) {
                $t = '';
                foreach ($si->r as $r) {
                    $t .= (string)$r->t;
                }
                $sharedStrings[] = $t;
            } else {
                $sharedStrings[] = '';
            }
        }
    }
    echo "Total shared strings: " . count($sharedStrings) . "\n";

    // Read workbook to find sheets
    $wbXml = $zip->getFromName('xl/workbook.xml');
    if ($wbXml) {
        $wb = simplexml_load_string($wbXml);
        echo "Sheets:\n";
        foreach ($wb->sheets->sheet as $sh) {
            echo "  Sheet: " . (string)$sh['name'] . " (id: " . (string)$sh['sheetId'] . ", r:id: " . (string)$sh->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'] . ")\n";
        }
    }

    // Check sheet2.xml as well
    foreach (['sheet1.xml', 'sheet2.xml'] as $sheetName) {
        $sheetXml = $zip->getFromName("xl/worksheets/$sheetName");
        if ($sheetXml) {
            echo "\n=== $sheetName ===\n";
            $sheet = simplexml_load_string($sheetXml);
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
                if (count($rowData) > 1 || (isset($rowData['A']) && $rowData['A'] !== '' && count($rowData) > 1)) {
                    $rows[$rNum] = $rowData;
                } elseif (count($rowData) == 1 && isset($rowData['A']) && is_numeric($rowData['A'])) {
                    // skip just row number
                } else {
                    $rows[$rNum] = $rowData;
                }
            }
            echo "Non-empty rows count: " . count($rows) . "\n";
            foreach ($rows as $rNum => $rData) {
                if (count($rData) > 1) {
                    echo "Row $rNum: " . json_encode($rData, JSON_UNESCAPED_UNICODE) . "\n";
                }
            }
        }
    }


    $zip->close();
} else {
    echo "Could not open xlsx file: $xlsxPath\n";
}

function traverseFolder($dir, $depth = 0) {
    if ($depth > 3) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $indent = str_repeat("  ", $depth);
        if (is_dir($path)) {
            $subCount = count(scandir($path)) - 2;
            echo "{$indent}[DIR] {$item} ({$subCount} items)\n";
            traverseFolder($path, $depth + 1);
        } else {
            $size = round(filesize($path) / 1024, 1);
            echo "{$indent}[FILE] {$item} ({$size} KB)\n";
        }
    }
}

echo "\n=== WEBSITE ELEMENT STRUCTURE ===\n";
traverseFolder('C:/Users/Shawon/Downloads/primebooking.com.bd/primebooking.com.bd/Website Element');



