<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CallCenterActionPointImport implements ToCollection, WithMultipleSheets
{
    private string $sheetName;
    private int $headerRow = 1;

    public function __construct(string $sheetName = '', int $headerRow = 1)
    {
        $this->sheetName = $sheetName;
        $this->headerRow = $headerRow;
    }

    public function collection(Collection $collection): Collection
    {
        return $collection;
    }

    public function sheets(): array
    {
        return [
            $this->sheetName => $this,
        ];
    }
}
