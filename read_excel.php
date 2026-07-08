<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$reader = IOFactory::createReaderForFile('ACTION POINTS CALL CENTER 2026..xlsx');
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load('ACTION POINTS CALL CENTER 2026..xlsx');

$sheetCount = $spreadsheet->getSheetCount();
echo "Sheets: $sheetCount\n";

for ($i = 0; $i < $sheetCount; $i++) {
    $sheet = $spreadsheet->getSheet($i);
    echo "\n=== Sheet {$i}: '{$sheet->getTitle()}' ===\n";
    $rows = $sheet->toArray(null, true, true, false);
    echo "Total rows: " . count($rows) . "\n";
    // Show first 15 rows
    for ($r = 0; $r < min(15, count($rows)); $r++) {
        $rowNum = $r + 1;
        $cells = array_map(function($v) { return is_null($v) ? '' : (is_string($v) ? substr($v, 0, 50) : $v); }, $rows[$r]);
        echo "  Row {$rowNum}: " . implode(' | ', $cells) . "\n";
    }
}
$spreadsheet->disconnectWorksheets();
