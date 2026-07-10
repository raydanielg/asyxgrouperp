<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SgrParkingRevenueCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class SgrParkingRevenueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function isAdmin(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('approve-sgr-parking-revenue');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = SgrParkingRevenueCollection::query();

        if (!$this->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $companyId = $user->activeCompanyId();
        if ($companyId && !$this->isAdmin()) {
            $query->where('company_id', $companyId);
        }

        $stats = [
            'total_collections' => (clone $query)->count(),
            'total_collected' => (clone $query)->sum('amount_collected') ?? 0,
            'total_deposited' => (clone $query)->sum('amount_deposited') ?? 0,
            'total_difference' => (clone $query)->sum('difference') ?? 0,
            'total_cashiers' => (clone $query)->distinct('cashier_name')->count('cashier_name'),
            'total_batches' => (clone $query)->distinct('import_batch')->count('import_batch'),
        ];

        $recent = (clone $query)->latest()->take(10)->get();

        $byCashier = (clone $query)
            ->selectRaw('cashier_name, SUM(amount_collected) as collected, SUM(amount_deposited) as deposited, SUM(difference) as diff, COUNT(*) as total')
            ->whereNotNull('cashier_name')
            ->groupBy('cashier_name')
            ->orderByDesc('collected')
            ->take(10)
            ->get();

        $byBooth = (clone $query)
            ->selectRaw('comments as booth, SUM(amount_collected) as collected, SUM(amount_deposited) as deposited, SUM(difference) as diff, COUNT(*) as total')
            ->whereNotNull('comments')
            ->groupBy('comments')
            ->orderByDesc('collected')
            ->take(10)
            ->get();

        $batches = (clone $query)
            ->selectRaw('import_batch, source_filename, MAX(created_at) as uploaded_at, COUNT(*) as total')
            ->groupBy('import_batch', 'source_filename')
            ->orderByDesc('uploaded_at')
            ->take(10)
            ->get();

        return view('admin.sgr.parking-revenue.index', compact('stats', 'recent', 'byCashier', 'byBooth', 'batches'));
    }

    public function import(Request $request)
    {
        $user = auth()->user();
        $query = SgrParkingRevenueCollection::query();

        if (!$this->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $batches = (clone $query)
            ->selectRaw('import_batch, source_filename, MAX(created_at) as imported_at, COUNT(*) as total')
            ->groupBy('import_batch', 'source_filename')
            ->orderByDesc('imported_at')
            ->take(10)
            ->get();

        $preview = session('sgr_parking_preview');
        $preview = $preview ?: null;

        return view('admin.sgr.parking-revenue.import', compact('batches', 'preview'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'sheet_name' => 'nullable|string',
        ]);

        $file = $request->file('excel_file');
        $batch = (string) Str::uuid();
        $filename = $file->getClientOriginalName();
        $path = $file->storeAs('temp/sgr-parking', $batch . '_' . $filename, 'local');
        $fullPath = Storage::disk('local')->path($path);

        $sheetName = $request->input('sheet_name', '');
        $preview = $this->readExcelPreview($fullPath, $sheetName);
        $preview['batch'] = $batch;
        $preview['filename'] = $filename;
        $preview['stored_path'] = $path;

        $request->session()->put('sgr_parking_preview', $preview);

        return redirect()->route('admin.sgr.parking-revenue.import')
            ->with('success', 'Upload successful. Found ' . $preview['total_rows'] . ' data rows in sheet: ' . $preview['sheet_name']);
    }

    public function store(Request $request)
    {
        $preview = $request->session()->get('sgr_parking_preview');
        if (!$preview) {
            return redirect()->route('admin.sgr.parking-revenue.import')->with('error', 'No preview found. Please upload again.');
        }

        $request->validate([
            'header_row' => 'required|integer|min:1',
        ]);

        $headerRow = (int) $request->input('header_row', 1);
        $fullPath = Storage::disk('local')->path($preview['stored_path']);

        $user = auth()->user();
        $companyId = $user->activeCompanyId() ?? $user->company_id;
        $batch = $preview['batch'];
        $filename = $preview['filename'];
        $sheetName = $preview['sheet_name'];

        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($fullPath);
        $sheet = $sheetName ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();
        $allRows = $sheet->toArray();
        $spreadsheet->disconnectWorksheets();

        $headers = $allRows[$headerRow - 1] ?? [];
        $headers = array_map(fn($h) => trim(strtoupper((string) $h)), $headers);
        $dataRows = array_slice($allRows, $headerRow);

        $inserted = 0;
        foreach ($dataRows as $index => $row) {
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

            if ($this->isEmptyRow($row, $headers)) {
                continue;
            }

            $sn = $this->getValue($row, $headers, 'SN');
            if (!is_numeric($sn) && empty($sn)) {
                continue;
            }

            $dateIn = $this->getValue($row, $headers, 'DATE IN');
            $dateOut = $this->getValue($row, $headers, 'DATE OUT');
            $timeIn = $this->getValue($row, $headers, 'TIME IN');
            $timeOut = $this->getValue($row, $headers, 'TIME OUT');
            $amountCollected = $this->getValue($row, $headers, 'AMOUNT COLLECTED');
            $amountDeposited = $this->getValue($row, $headers, 'AMOUNT DEPOSITED');
            $difference = $this->getValue($row, $headers, 'DIFFERENCE');
            $controlNo = $this->getValue($row, $headers, 'CONTROL NO.');
            $receiptNo = $this->getValue($row, $headers, 'RECEIPT NO.');
            $cashierName = $this->getValue($row, $headers, 'CASHIER NAME');
            $controlStatus = $this->getValue($row, $headers, 'CONTROL NO. STATUS');
            $comments = $this->getValue($row, $headers, 'COMMENTS');

            SgrParkingRevenueCollection::create([
                'company_id' => $companyId,
                'created_by' => $user->id,
                'import_batch' => $batch,
                'source_filename' => $filename,
                'sheet_name' => $sheetName,
                'row_number' => $index + 1,
                'sn' => $sn,
                'date_in' => $this->parseExcelDate($dateIn),
                'date_out' => $this->parseExcelDate($dateOut),
                'time_in' => $this->parseExcelTime($timeIn),
                'time_out' => $this->parseExcelTime($timeOut),
                'amount_collected' => $this->parseAmount($amountCollected),
                'amount_deposited' => $this->parseAmount($amountDeposited),
                'difference' => $this->parseAmount($difference),
                'control_no' => $controlNo,
                'receipt_no' => $receiptNo,
                'cashier_name' => $cashierName,
                'control_status' => $controlStatus,
                'comments' => $comments,
                'raw_data' => $row,
            ]);
            $inserted++;
        }

        $request->session()->forget('sgr_parking_preview');
        Storage::disk('local')->delete($preview['stored_path']);

        return redirect()->route('admin.sgr.parking-revenue.reports', ['batch' => $batch])
            ->with('success', "Imported {$inserted} parking revenue records successfully.");
    }

    public function reports(Request $request)
    {
        $user = auth()->user();
        $query = SgrParkingRevenueCollection::query();

        if (!$this->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $batch = $request->input('batch', '');
        $cashier = $request->input('cashier', '');
        $booth = $request->input('booth', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($batch) {
            $query->where('import_batch', $batch);
        }
        if ($cashier) {
            $query->where('cashier_name', 'like', "%{$cashier}%");
        }
        if ($booth) {
            $query->where('comments', 'like', "%{$booth}%");
        }
        if ($dateFrom) {
            $query->where('date_in', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('date_in', '<=', $dateTo);
        }

        $summary = [
            'total' => (clone $query)->count(),
            'total_collected' => (clone $query)->sum('amount_collected') ?? 0,
            'total_deposited' => (clone $query)->sum('amount_deposited') ?? 0,
            'total_difference' => (clone $query)->sum('difference') ?? 0,
            'by_cashier' => (clone $query)
                ->selectRaw('cashier_name, SUM(amount_collected) as collected, SUM(amount_deposited) as deposited, SUM(difference) as diff, COUNT(*) as total')
                ->whereNotNull('cashier_name')
                ->groupBy('cashier_name')
                ->orderByDesc('collected')
                ->get(),
            'by_booth' => (clone $query)
                ->selectRaw('comments as booth, SUM(amount_collected) as collected, SUM(amount_deposited) as deposited, SUM(difference) as diff, COUNT(*) as total')
                ->whereNotNull('comments')
                ->groupBy('comments')
                ->orderByDesc('collected')
                ->get(),
            'by_date' => (clone $query)
                ->selectRaw('date_in, SUM(amount_collected) as collected, SUM(amount_deposited) as deposited, SUM(difference) as diff, COUNT(*) as total')
                ->whereNotNull('date_in')
                ->groupBy('date_in')
                ->orderBy('date_in')
                ->get(),
        ];

        $items = (clone $query)->latest()->paginate(25)->withQueryString();

        $batchesQuery = SgrParkingRevenueCollection::query();
        if (!$this->isAdmin()) {
            $batchesQuery->where('created_by', $user->id);
        }
        $batches = $batchesQuery->distinct()->orderByDesc('created_at')->pluck('import_batch');

        $isAdmin = $this->isAdmin();

        return view('admin.sgr.parking-revenue.reports', compact(
            'summary', 'items', 'batches', 'batch', 'cashier', 'booth', 'dateFrom', 'dateTo', 'isAdmin'
        ));
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        $headers = ['SN', 'DATE IN', 'DATE OUT', 'TIME IN', 'TIME OUT', 'AMOUNT COLLECTED', 'AMOUNT DEPOSITED', 'DIFFERENCE', 'CONTROL NO.', 'RECEIPT NO.', 'CASHIER NAME', 'CONTROL NO. STATUS', 'COMMENTS'];
        $sheet->fromArray($headers, null, 'A1');

        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', '2026-07-05');
        $sheet->setCellValue('C2', '2026-07-05');
        $sheet->setCellValue('D2', '08:00');
        $sheet->setCellValue('E2', '20:00');
        $sheet->setCellValue('F2', '500000');
        $sheet->setCellValue('G2', '500000');
        $sheet->setCellValue('H2', '0');
        $sheet->setCellValue('I2', '987200000000');
        $sheet->setCellValue('J2', '925300000000');
        $sheet->setCellValue('K2', 'Juma Rajab');
        $sheet->setCellValue('L2', 'Provided');
        $sheet->setCellValue('M2', 'Booth 1');

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="sgr-parking-revenue-template.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    private function readExcelPreview(string $filePath, string $sheetName = ''): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $sheetName ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();
        $sheetName = $sheet?->getTitle() ?? '';

        $allRows = $sheet->toArray();
        $spreadsheet->disconnectWorksheets();

        $headerRow = 0;
        foreach ($allRows as $i => $row) {
            $upper = array_map(fn($h) => strtoupper(trim((string) $h)), $row);
            if (in_array('SN', $upper) || in_array('DATE IN', $upper)) {
                $headerRow = $i;
                break;
            }
        }

        $headers = array_map(fn($h) => trim((string) $h), $allRows[$headerRow] ?? []);
        $dataRows = array_slice($allRows, $headerRow + 1);
        $dataRows = array_values(array_filter($dataRows, fn($r) => !$this->isEmptyRow($r, $headers)));

        $preview = [];
        foreach (array_slice($dataRows, 0, 10) as $row) {
            $mapped = [];
            foreach ($headers as $i => $header) {
                $mapped[$header] = $row[$i] ?? '';
            }
            $preview[] = $mapped;
        }

        return [
            'sheet_name' => $sheetName,
            'header_row' => $headerRow + 1,
            'headers' => $headers,
            'total_rows' => count($dataRows),
            'preview' => $preview,
        ];
    }

    private function isEmptyRow(array $row, array $headers): bool
    {
        foreach ($row as $i => $cell) {
            $header = $headers[$i] ?? '';
            if (in_array(strtoupper($header), ['SN', 'DATE IN', 'AMOUNT COLLECTED', 'CASHIER NAME'])) {
                if ($cell !== null && $cell !== '' && $cell !== '0') {
                    return false;
                }
            }
        }
        return true;
    }

    private function getValue(array $row, array $headers, string $column): mixed
    {
        $column = strtoupper($column);
        foreach ($headers as $i => $header) {
            if (strtoupper($header) === $column) {
                return $row[$i] ?? null;
            }
        }
        return null;
    }

    private function parseExcelDate(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '#VALUE!' || $value === ' - ') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float) $value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $value = (string) $value;
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'd.m.Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseExcelTime(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '#VALUE!' || $value === ' - ') {
            return null;
        }

        if (is_numeric($value) && $value >= 0 && $value < 1) {
            $totalSeconds = (int) round($value * 86400);
            $hours = intdiv($totalSeconds, 3600);
            $minutes = intdiv($totalSeconds % 3600, 60);
            return sprintf('%02d:%02d', $hours, $minutes);
        }

        $value = (string) $value;
        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return $value ?: null;
        }
    }

    private function parseAmount(mixed $value): float
    {
        if ($value === null || $value === '' || $value === ' ' || $value === '#VALUE!' || $value === ' - ') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d.-]/', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : 0;
    }
}
