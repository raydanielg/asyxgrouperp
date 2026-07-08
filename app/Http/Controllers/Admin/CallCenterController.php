<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallCampaign;
use App\Models\CallCenterActionPoint;
use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CallCenterController extends Controller
{
    public function index()
    {
        $campaigns = CallCampaign::withCount('callLogs')->latest()->paginate(10, ['*'], 'c_page');
        $recentCalls = CallLog::with(['agent', 'campaign'])->latest()->limit(20)->get();
        $stats = [
            'total_calls' => CallLog::count(),
            'inbound' => CallLog::where('call_direction', 'inbound')->count(),
            'outbound' => CallLog::where('call_direction', 'outbound')->count(),
            'missed' => CallLog::where('status', 'missed')->count(),
            'avg_duration' => round(CallLog::avg('duration_seconds') ?? 0),
        ];
        return view('admin.call-center.index', compact('campaigns', 'recentCalls', 'stats'));
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();
        CallCampaign::create($validated);

        return back()->with('success', 'Campaign created.');
    }

    public function storeCall(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|exists:call_campaigns,id',
            'call_direction' => 'required|string',
            'caller_name' => 'nullable|string',
            'caller_phone' => 'required|string',
            'callee_name' => 'nullable|string',
            'callee_phone' => 'nullable|string',
            'call_start' => 'required|date',
            'call_end' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'disposition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['call_end']) && empty($validated['duration_seconds'])) {
            $validated['duration_seconds'] = strtotime($validated['call_end']) - strtotime($validated['call_start']);
        }

        $validated['agent_id'] = auth()->id();
        $validated['company_id'] = auth()->user()?->company_id;
        CallLog::create($validated);

        return back()->with('success', 'Call logged.');
    }

    public function calls()
    {
        $calls = CallLog::with(['agent', 'campaign'])->latest()->paginate(25);
        return view('admin.call-center.calls', compact('calls'));
    }

    // ═══════════════════════════════════════════════════════
    //  ACTION POINTS EXCEL IMPORT
    // ═══════════════════════════════════════════════════════

    public function actionPointsImport(Request $request)
    {
        $batches = CallCenterActionPoint::select('import_batch', 'source_filename')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('MAX(created_at) as imported_at')
            ->groupBy('import_batch', 'source_filename')
            ->orderByDesc('imported_at')
            ->limit(20)
            ->get();

        $preview = null;
        $filePath = session('call_center_import_path');
        $sheetName = session('call_center_import_sheet');

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $preview = $this->readExcelPreview(Storage::disk('local')->path($filePath), $sheetName);
            session(['call_center_preview_headers' => $preview['headers'] ?? []]);
        }

        return view('admin.call-center.action-points-import', compact('batches', 'preview'));
    }

    public function actionPointsUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'sheet_name' => 'nullable|string',
        ]);

        $file = $request->file('excel_file');
        $batch = 'CC-' . now()->format('Ymd-His') . '-' . Str::random(4);
        $path = $file->storeAs('call-center-imports', $batch . '.' . $file->getClientOriginalExtension(), 'local');

        $sheetName = $request->input('sheet_name', '');
        if (!$sheetName) {
            $reader = IOFactory::createReaderForFile(Storage::disk('local')->path($path));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(Storage::disk('local')->path($path));
            $sheetName = $spreadsheet->getSheet(0)->getTitle();
            $spreadsheet->disconnectWorksheets();
        }

        session([
            'call_center_import_path' => $path,
            'call_center_import_batch' => $batch,
            'call_center_import_sheet' => $sheetName,
            'call_center_import_filename' => $file->getClientOriginalName(),
        ]);

        return redirect()->route('admin.call-center.action-points.import')->with('success', 'File uploaded. Map the columns below.');
    }

    public function actionPointsStore(Request $request)
    {
        $request->validate([
            'activity_column' => 'required|string',
            'responsible_column' => 'required|string',
            'due_date_column' => 'nullable|string',
            'status_column' => 'nullable|string',
            'header_row' => 'required|integer|min:1',
        ]);

        $filePath = session('call_center_import_path');
        $batch = session('call_center_import_batch');
        $sheetName = session('call_center_import_sheet');
        $filename = session('call_center_import_filename');

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return redirect()->route('admin.call-center.action-points.import')->with('error', 'Upload file first.');
        }

        $mapping = $request->only(['activity_column', 'responsible_column', 'due_date_column', 'status_column']);
        $headerRow = (int) $request->input('header_row', 1);

        $reader = IOFactory::createReaderForFile(Storage::disk('local')->path($filePath));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(Storage::disk('local')->path($filePath));
        $sheet = $spreadsheet->getSheetByName($sheetName) ?? $spreadsheet->getSheet(0);
        $rows = $sheet->toArray(null, true, true, false);
        $spreadsheet->disconnectWorksheets();

        $imported = 0;
        $companyId = auth()->user()?->company_id;
        $createdBy = auth()->id();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            if ($rowNumber <= $headerRow) {
                continue;
            }

            if (empty($row) || $this->isEmptyRow($row)) {
                continue;
            }

            $activity = $this->getMappedValue($row, $mapping['activity_column']);
            $responsible = $this->getMappedValue($row, $mapping['responsible_column']);

            if (empty($activity) && empty($responsible)) {
                continue;
            }

            $dueDate = $this->parseDate($this->getMappedValue($row, $mapping['due_date_column'] ?? ''));
            $status = $this->getMappedValue($row, $mapping['status_column'] ?? '');

            CallCenterActionPoint::create([
                'company_id' => $companyId,
                'created_by' => $createdBy,
                'import_batch' => $batch,
                'source_filename' => $filename,
                'sheet_name' => $sheetName,
                'row_number' => $rowNumber,
                'activity' => $activity,
                'responsible_person' => $responsible,
                'due_date' => $dueDate,
                'status' => $status,
                'raw_data' => $row,
            ]);

            $imported++;
        }

        session()->forget(['call_center_import_path', 'call_center_import_batch', 'call_center_import_sheet', 'call_center_import_filename']);

        return redirect()->route('admin.call-center.action-points.reports', ['batch' => $batch])
            ->with('success', "Imported {$imported} action points.");
    }

    public function actionPointsReports(Request $request)
    {
        $batch = $request->input('batch');
        $responsible = $request->input('responsible');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = CallCenterActionPoint::query();

        if ($batch) {
            $query->where('import_batch', $batch);
        }
        if ($responsible) {
            $query->where('responsible_person', 'like', "%{$responsible}%");
        }
        if ($status) {
            $query->where('status', 'like', "%{$status}%");
        }
        if ($dateFrom) {
            $query->whereDate('due_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('due_date', '<=', $dateTo);
        }

        $items = $query->orderByDesc('created_at')->paginate(50)->appends($request->except('page'));

        $summary = [
            'total' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->orderByDesc('total')
                ->get(),
            'by_responsible' => (clone $query)->select('responsible_person')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('responsible_person')
                ->orderByDesc('total')
                ->limit(15)
                ->get(),
            'overdue' => (clone $query)->where('due_date', '<', now()->startOfDay())->count(),
        ];

        $batches = CallCenterActionPoint::select('import_batch')->distinct()->orderByDesc('import_batch')->pluck('import_batch');

        return view('admin.call-center.action-points-reports', compact('items', 'summary', 'batches', 'batch', 'responsible', 'status', 'dateFrom', 'dateTo'));
    }

    private function readExcelPreview(string $filePath, string $sheetName = ''): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $sheetName ? ($spreadsheet->getSheetByName($sheetName) ?? $spreadsheet->getSheet(0)) : $spreadsheet->getSheet(0);
        $rows = $sheet->toArray(null, true, true, false);
        $spreadsheet->disconnectWorksheets();

        return [
            'sheet_name' => $sheet->getTitle(),
            'total_rows' => count($rows),
            'headers' => $rows[0] ?? [],
            'preview' => array_slice($rows, 1, 5),
        ];
    }

    private function getMappedValue(array $row, string $column): mixed
    {
        if (empty($column)) {
            return null;
        }

        if (is_numeric($column)) {
            return $row[(int) $column] ?? null;
        }

        $header = session('call_center_preview_headers', []);
        $index = array_search($column, $header);
        if ($index !== false) {
            return $row[$index] ?? null;
        }

        return null;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (!empty($cell) && trim((string) $cell) !== '') {
                return false;
            }
        }
        return true;
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, (string) $value);
            if ($date && $date->format($format) === (string) $value) {
                return $date->format('Y-m-d');
            }
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
