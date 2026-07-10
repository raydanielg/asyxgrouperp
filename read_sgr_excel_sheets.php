<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'DAILY CAR PARKING REVENUE COLLECTION REPORT-SAMIA SULUHU SGR , STATION DODOMA 05.07. 2026 Rvsd (2) (1).xlsx';
$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $data = $sheet->toArray();
    echo "Sheet: $title (rows: " . count($data) . ")\n";
    for ($i = 0; $i < min(8, count($data)); $i++) {
        echo $i . ': ' . json_encode(array_slice($data[$i], 0, 13)) . "\n";
    }
    echo "\n";
}
