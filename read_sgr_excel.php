<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'DAILY CAR PARKING REVENUE COLLECTION REPORT-SAMIA SULUHU SGR , STATION DODOMA 05.07. 2026 Rvsd (2) (1).xlsx';
if (!file_exists($file)) {
    echo "FILE NOT FOUND: $file\n";
    exit(1);
}

$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);

foreach ($spreadsheet->getAllSheets() as $sheet) {
    echo "Sheet: " . $sheet->getTitle() . "\n";
    $data = $sheet->toArray();
    $rows = min(count($data), 35);
    for ($i = 0; $i < $rows; $i++) {
        echo $i . ': ' . json_encode(array_slice($data[$i], 0, 15)) . "\n";
    }
    echo "\n";
}
