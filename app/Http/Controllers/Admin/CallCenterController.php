<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallCampaign;
use App\Models\CallCenterActionPoint;
use App\Models\CallCenterTicket;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CallCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            $user = auth()->user();
            if ($user->isAdmin() || $user->hasPermission('view-call-center') || $user->hasPermission('view-dashboard')) {
                return $next($request);
            }
            abort(403, 'Unauthorized access.');
        });
    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $stats = [];
        if ($isAdmin || $user->hasPermission('view-call-logs')) {
            $callsQuery = CallLog::query();
            if (!$isAdmin) {
                $callsQuery->where('agent_id', $user->id);
            }
            $stats = [
                'total_calls' => (clone $callsQuery)->count(),
                'inbound' => (clone $callsQuery)->where('call_direction', 'inbound')->count(),
                'outbound' => (clone $callsQuery)->where('call_direction', 'outbound')->count(),
                'missed' => (clone $callsQuery)->where('status', 'missed')->count(),
                'avg_duration' => round((clone $callsQuery)->avg('duration_seconds') ?? 0),
            ];
        }

        // Tickets
        $ticketsQuery = CallCenterTicket::query();
        if (!$isAdmin) {
            $ticketsQuery->where('created_by', $user->id);
        }
        $myTickets = (clone $ticketsQuery)->latest()->limit(10)->get();
        $ticketStats = [
            'total' => (clone $ticketsQuery)->count(),
            'open' => (clone $ticketsQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $ticketsQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $ticketsQuery)->where('status', 'resolved')->count(),
        ];

        // Action Points
        $actionPointsQuery = CallCenterActionPoint::query();
        if (!$isAdmin) {
            $actionPointsQuery->where('created_by', $user->id);
        }
        $apStats = [
            'total' => (clone $actionPointsQuery)->count(),
            'pending' => (clone $actionPointsQuery)->where('approval_status', 'pending')->count(),
            'approved' => (clone $actionPointsQuery)->where('approval_status', 'approved')->count(),
            'overdue' => (clone $actionPointsQuery)->where('approval_status', 'approved')->where('due_date', '<', now()->startOfDay())->count(),
        ];
        $recentActionPoints = (clone $actionPointsQuery)->with('creator')->latest()->limit(10)->get();

        $recentCalls = CallLog::with('agent');
        if (!$isAdmin) {
            $recentCalls->where('agent_id', $user->id);
        }
        $recentCalls = $recentCalls->latest()->limit(10)->get();

        $agents = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['call_center_agent', 'call_center_supervisor']);
        })->get();

        $campaigns = CallCampaign::withCount('callLogs')->latest()->paginate(10, ['*'], 'c_page');

        return view('admin.call-center.index', compact(
            'stats', 'myTickets', 'ticketStats', 'apStats', 'recentActionPoints', 'recentCalls',
            'agents', 'campaigns', 'isAdmin'
        ));
    }

    // ═══════════════════════════════════════════════════════
    //  TICKETS
    // ═══════════════════════════════════════════════════════

    public function tickets(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $query = CallCenterTicket::with(['creator', 'assignee']);
        if (!$isAdmin) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(20)->appends($request->except('page'));
        $agents = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['call_center_agent', 'call_center_supervisor']);
        })->get();

        return view('admin.call-center.tickets', compact('tickets', 'agents', 'isAdmin'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = CallCenterTicket::create([
            'ticket_no' => CallCenterTicket::generateTicketNo(),
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
            'assigned_to' => $validated['assigned_to'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket->load('creator')]);
        }

        return back()->with('success', "Ticket {$ticket->ticket_no} created.");
    }

    public function updateTicket(Request $request, CallCenterTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string',
        ]);

        $data = [
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? $ticket->priority,
            'assigned_to' => array_key_exists('assigned_to', $validated) ? $validated['assigned_to'] : $ticket->assigned_to,
            'resolution_notes' => $validated['resolution_notes'] ?? $ticket->resolution_notes,
        ];

        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket->fresh()->load(['creator', 'assignee'])]);
        }

        return back()->with('success', "Ticket {$ticket->ticket_no} updated.");
    }

    // ═══════════════════════════════════════════════════════
    //  CALL LOGS
    // ═══════════════════════════════════════════════════════

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
        $user = auth()->user();
        $query = CallLog::with(['agent', 'campaign']);
        if (!$user->isAdmin()) {
            $query->where('agent_id', $user->id);
        }
        $calls = $query->latest()->paginate(25);
        return view('admin.call-center.calls', compact('calls'));
    }

    // ═══════════════════════════════════════════════════════
    //  ACTION POINTS EXCEL IMPORT WITH APPROVAL
    // ═══════════════════════════════════════════════════════

    public function actionPointsImport(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $batchesQuery = CallCenterActionPoint::select('import_batch', 'source_filename')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('MAX(created_at) as imported_at');

        if (!$isAdmin) {
            $batchesQuery->where('created_by', $user->id);
        }

        $batches = $batchesQuery->groupBy('import_batch', 'source_filename')
            ->orderByDesc('imported_at')->limit(20)->get();

        $preview = null;
        $filePath = session('call_center_import_path');
        $sheetName = session('call_center_import_sheet');

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $preview = $this->readExcelPreview(Storage::disk('local')->path($filePath), $sheetName);
            session(['call_center_preview_headers' => $preview['headers'] ?? []]);
        }

        $pendingApprovals = $isAdmin ? CallCenterActionPoint::where('approval_status', 'pending')
            ->with('creator')->latest()->limit(20)->get() : collect();

        return view('admin.call-center.action-points-import', compact('batches', 'preview', 'pendingApprovals', 'isAdmin'));
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

        $filePath = session('call_center_import_path');
        $batch = session('call_center_import_batch');
        $sheetName = session('call_center_import_sheet');
        $filename = session('call_center_import_filename');

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

            // Non-admin uploads go to pending, admin uploads go directly to approved
            $approvalStatus = $isAdmin ? 'approved' : 'pending';
            $approvedBy = $isAdmin ? auth()->id() : null;
            $approvedAt = $isAdmin ? now() : null;

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
                'approval_status' => $approvalStatus,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'raw_data' => $row,
            ]);

            $imported++;
        }

        Storage::disk('local')->delete($filePath);
        session()->forget(['call_center_import_path', 'call_center_import_batch', 'call_center_import_sheet', 'call_center_import_filename']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'imported' => $imported,
                'approval_needed' => !$isAdmin,
                'redirect' => route('role.page', ['module' => 'action-points-reports', 'batch' => $batch]),
            ]);
        }

        return redirect()->route('role.page', ['module' => 'action-points-reports', 'batch' => $batch])
            ->with('success', "Imported {$imported} action points.");
    }

    // ═══════════════════════════════════════════════════════
    //  ADMIN APPROVAL
    // ═══════════════════════════════════════════════════════

    public function actionPointsApprove(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'batch' => 'required|string',
            'action' => 'required|in:approve,reject',
        ]);

        $updated = CallCenterActionPoint::where('import_batch', $request->batch)
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
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $pending = CallCenterActionPoint::where('approval_status', 'pending')
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

    // ═══════════════════════════════════════════════════════
    //  REPORTS
    // ═══════════════════════════════════════════════════════

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

        $query = CallCenterActionPoint::query();

        // Non-admin agents only see their own uploads
        if (!$isAdmin) {
            $query->where('created_by', $user->id);
        }

        if ($batch) $query->where('import_batch', $batch);
        if ($responsible) $query->where('responsible_person', 'like', "%{$responsible}%");
        if ($status) $query->where('status', 'like', "%{$status}%");
        if ($approvalStatus) $query->where('approval_status', $approvalStatus);
        if ($dateFrom) $query->whereDate('due_date', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('due_date', '<=', $dateTo);

        // By default, only show approved items to agents
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

        $batches = CallCenterActionPoint::select('import_batch')
            ->distinct()->orderByDesc('import_batch')->pluck('import_batch');

        return view('admin.call-center.action-points-reports', compact(
            'items', 'summary', 'batches', 'batch', 'responsible', 'status',
            'dateFrom', 'dateTo', 'approvalStatus', 'isAdmin'
        ));
    }

    // ═══════════════════════════════════════════════════════
    //  TEMPLATE DOWNLOAD
    // ═══════════════════════════════════════════════════════

    public function downloadTemplate()
    {
        // Check if the template file exists in the project root
        $templatePath = base_path('ACTION POINTS CALL CENTER 2026..xlsx');

        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'ACTION_POINTS_CALL_CENTER_TEMPLATE.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Generate a template on the fly if the original doesn't exist
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('ACTION POINTS');

        // Headers
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

        // Example rows
        $examples = [
            [1, 'Follow up on pending customer invoices', 'John Doe', '31/12/2026', 'Pending', 'Call customers for payment'],
            [2, 'Update call scripts for new product launch', 'Jane Smith', '15/01/2027', 'In Progress', 'Review with marketing team'],
        ];
        foreach ($examples as $r => $row) {
            foreach ($row as $c => $val) {
                $sheet->setCellValue(chr(65 + $c) . ($r + 2), $val);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp-call-center-template.xlsx');
        $writer->save($tempPath);
        $spreadsheet->disconnectWorksheets();

        return response()->download($tempPath, 'ACTION_POINTS_CALL_CENTER_TEMPLATE.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ═══════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════

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

        $header = session('call_center_preview_headers', []);
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
