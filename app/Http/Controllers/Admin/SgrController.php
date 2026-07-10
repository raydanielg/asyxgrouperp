<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SgrActionPoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SgrController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            $user = auth()->user();
            if ($user->isAdmin() || $user->hasPermission('view-sgr') || $user->hasPermission('view-dashboard')) {
                return $next($request);
            }
            abort(403, 'Unauthorized access.');
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $query = SgrActionPoint::query();
        if (!$isAdmin) {
            $query->where('created_by', $user->id);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('approval_status', 'pending')->count(),
            'approved' => (clone $query)->where('approval_status', 'approved')->count(),
            'overdue' => (clone $query)->where('approval_status', 'approved')->where('due_date', '<', now()->startOfDay())->count(),
            'by_status' => (clone $query)->select('status')->selectRaw('COUNT(*) as total')
                ->groupBy('status')->orderByDesc('total')->get(),
        ];

        $recent = (clone $query)->with('creator')->latest()->limit(10)->get();

        $pendingBatches = $isAdmin
            ? SgrActionPoint::where('approval_status', 'pending')
                ->select('import_batch', 'source_filename')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('MAX(created_at) as uploaded_at')
                ->groupBy('import_batch', 'source_filename')
                ->orderByDesc('uploaded_at')
                ->limit(10)
                ->get()
            : collect();

        $agents = User::whereHas('roles', function ($q) {
            $q->where('name', 'sgr_agent');
        })->get();

        return view('admin.sgr.index', compact('stats', 'recent', 'pendingBatches', 'agents', 'isAdmin'));
    }

    public function actionPointsImport(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $batchesQuery = SgrActionPoint::select('import_batch', 'source_filename')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('MAX(created_at) as imported_at');

        if (!$isAdmin) {
            $batchesQuery->where('created_by', $user->id);
        }

        $batches = $batchesQuery->groupBy('import_batch', 'source_filename')
            ->orderByDesc('imported_at')->limit(20)->get();

        $preview = null;
        $filePath = session('sgr_import_path');
        $sheetName = session('sgr_import_sheet');

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $preview = $this->readExcelPreview(Storage::disk('local')->path($filePath), $sheetName);
            session(['sgr_preview_headers' => $preview['headers'] ?? []]);
        }

        $pendingApprovals = $isAdmin ? SgrActionPoint::where('approval_status', 'pending')
            ->with('creator')->latest()->limit(20)->get() : collect();

        return view('admin.sgr.action-points-import', compact('batches', 'preview', 'pendingApprovals', 'isAdmin'));
    }

    public function actionPointsUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'sheet_name' => 'nullable|string',
        ]);

        $file = $request->file('excel_file');
        $batch = 'SGR-' . now()->format('Ymd-His') . '-' . Str::random(4);
        $path = $file->storeAs('sgr-imports', $batch . '.' . $file->getClientOriginalExtension(), 'local');

        $sheetName = $request->input('sheet_name', '');
        if (!$sheetName) {
            $reader = IOFactory::createReaderForFile(Storage::disk('local')->path($path));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(Storage::disk('local')->path($path));
            $sheetName = $spreadsheet->getSheet(0)->getTitle();
            $spreadsheet->disconnectWorksheets();
        }

        session([
            'sgr_import_path' => $path,
            'sgr_import_batch' => $batch,
            'sgr_import_sheet' => $sheetName,
            'sgr_import_filename' => $file->getClientOriginalName(),
        ]);

        if ($request->ajax()) {
            $preview = $this->readExcelPreview(Storage::disk('local')->path($path), $sheetName);
            return response()->json([
                'success' => true,
                'batch' => $batch,
                'sheet_name' => $sheetName,
                'filename' => $file->getClientOriginalName(),
                'preview' => $preview,
            ]);
        }

        return back()->with('success', 'File uploaded. Map the columns below.');
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

        $filePath = session('sgr_import_path');
        $batch = session('sgr_import_batch');
        $sheetName = session('sgr_import_sheet');
        $filename = session('sgr_import_filename');

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Upload file first.'], 400)
                : back()->with('error', 'Upload file first.');
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
        $isAdmin = auth()->user()->isAdmin();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            if ($rowNumber <= $headerRow) continue;
            if (empty($row) || $this->isEmptyRow($row)) continue;

            $activity = $this->getMappedValue($row, $mapping['activity_column']);
            $responsible = $this->getMappedValue($row, $mapping['responsible_column']);

            if (empty($activity) && empty($responsible)) continue;

            $dueDate = $this->parseDate($this->getMappedValue($row, $mapping['due_date_column'] ?? ''));
            $status = $this->getMappedValue($row, $mapping['status_column'] ?? '');

            $approvalStatus = $isAdmin ? 'approved' : 'pending';
            $approvedBy = $isAdmin ? auth()->id() : null;
            $approvedAt = $isAdmin ? now() : null;

            SgrActionPoint::create([
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
                'approval_status' => $approvalStatus,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'raw_data' => $row,
            ]);

            $imported++;
        }

        Storage::disk('local')->delete($filePath);
        session()->forget(['sgr_import_path', 'sgr_import_batch', 'sgr_import_sheet', 'sgr_import_filename', 'sgr_preview_headers']);

        $redirect = $isAdmin
            ? route('admin.sgr.action-points.reports', ['batch' => $batch])
            : route('sgr.action-points.reports', ['batch' => $batch]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'imported' => $imported,
                'approval_needed' => !$isAdmin,
                'redirect' => $redirect,
            ]);
        }

        return redirect()->to($redirect)->with('success', "Imported {$imported} SGR action points.");
    }

    public function actionPointsApprove(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('approve-sgr-action-points')) {
            abort(403);
        }

        $request->validate([
            'batch' => 'required|string',
            'action' => 'required|in:approve,reject',
        ]);

        $updated = SgrActionPoint::where('import_batch', $request->batch)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'action' => $request->action,
                'count' => $updated,
            ]);
        }

        $msg = $request->action === 'approve' ? "Approved {$updated} items." : "Rejected {$updated} items.";
        return back()->with('success', $msg);
    }

    public function actionPointsPending()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('approve-sgr-action-points')) {
            abort(403);
        }

        $pending = SgrActionPoint::where('approval_status', 'pending')
            ->with('creator')
            ->select('import_batch', 'source_filename')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('MAX(created_at) as uploaded_at')
            ->selectRaw('MAX(created_by) as creator_id')
            ->groupBy('import_batch', 'source_filename')
            ->orderByDesc('uploaded_at')
            ->get();

        return response()->json(['pending' => $pending]);
    }

    public function actionPointsReports(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $batch = $request->input('batch');
        $responsible = $request->input('responsible');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $approvalStatus = $request->input('approval_status');

        $query = SgrActionPoint::query();

        if (!$isAdmin) {
            $query->where('created_by', $user->id);
        }

        if ($batch) $query->where('import_batch', $batch);
        if ($responsible) $query->where('responsible_person', 'like', "%{$responsible}%");
        if ($status) $query->where('status', 'like', "%{$status}%");
        if ($approvalStatus) $query->where('approval_status', $approvalStatus);
        if ($dateFrom) $query->whereDate('due_date', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('due_date', '<=', $dateTo);

        if (!$isAdmin && !$approvalStatus) {
            $query->where('approval_status', 'approved');
        }

        $items = $query->with('creator')->orderByDesc('created_at')->paginate(50)->appends($request->except('page'));

        $summary = [
            'total' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status')
                ->selectRaw('COUNT(*) as total')->groupBy('status')->orderByDesc('total')->get(),
            'by_responsible' => (clone $query)->select('responsible_person')
                ->selectRaw('COUNT(*) as total')->groupBy('responsible_person')->orderByDesc('total')->limit(15)->get(),
            'overdue' => (clone $query)->where('approval_status', 'approved')->where('due_date', '<', now()->startOfDay())->count(),
            'by_approval' => (clone $query)->select('approval_status')
                ->selectRaw('COUNT(*) as total')->groupBy('approval_status')->get(),
        ];

        $batches = SgrActionPoint::select('import_batch')
            ->distinct()->orderByDesc('import_batch')->pluck('import_batch');

        return view('admin.sgr.action-points-reports', compact(
            'items', 'summary', 'batches', 'batch', 'responsible', 'status',
            'dateFrom', 'dateTo', 'approvalStatus', 'isAdmin'
        ));
    }

    public function downloadTemplate()
    {
        $templatePath = base_path('ACTION POINTS SGR 2026..xlsx');

        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'ACTION_POINTS_SGR_TEMPLATE.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('ACTION POINTS');

        $headers = ['S/N', 'ACTIVITY', 'RESPONSIBLE PERSON', 'DUE DATE', 'STATUS', 'NOTES'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('024938');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $examples = [
            [1, 'Inspect track section A', 'Engineer A. Temu', '31/12/2026', 'Pending', 'Before rainy season'],
            [2, 'Submit weekly passenger report', 'Officer B. Mushi', '15/01/2027', 'In Progress', 'Cover all stations'],
        ];
        foreach ($examples as $r => $row) {
            foreach ($row as $c => $val) {
                $sheet->setCellValue(chr(65 + $c) . ($r + 2), $val);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp-sgr-template.xlsx');
        $writer->save($tempPath);
        $spreadsheet->disconnectWorksheets();

        return response()->download($tempPath, 'ACTION_POINTS_SGR_TEMPLATE.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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
        if (empty($column)) return null;
        if (is_numeric($column)) return $row[(int) $column] ?? null;

        $header = session('sgr_preview_headers', []);
        $index = array_search($column, $header);
        return $index !== false ? ($row[$index] ?? null) : null;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (!empty($cell) && trim((string) $cell) !== '') return false;
        }
        return true;
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) return null;
        if ($value instanceof \DateTimeInterface) return $value->format('Y-m-d');

        $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, (string) $value);
            if ($date && $date->format($format) === (string) $value) return $date->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
