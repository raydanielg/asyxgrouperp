<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Project;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class AccountingLedgerController extends Controller
{
    public function __construct(protected LedgerService $ledger)
    {
    }

    // ═══ Chart of Accounts ═══

    public function chartOfAccountsIndex()
    {
        $accounts = ChartOfAccount::with('parent')->orderBy('code')->get()->groupBy('type');
        return view('admin.accounting.chart-of-accounts.index', compact('accounts'));
    }

    public function chartOfAccountsStore(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'subtype' => 'nullable|string|max:100',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
        ]);
        $data['is_system'] = false;
        $data['is_active'] = true;
        ChartOfAccount::create($data);
        return back()->with('success', 'Account created.');
    }

    public function chartOfAccountsDestroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_system) {
            return back()->with('error', 'System accounts cannot be deleted.');
        }
        if ($chartOfAccount->lines()->exists()) {
            return back()->with('error', 'Account has posted transactions and cannot be deleted.');
        }
        $chartOfAccount->delete();
        return back()->with('success', 'Account deleted.');
    }

    // ═══ Journal Entries (General Ledger) ═══

    public function journalEntriesIndex(Request $request)
    {
        $query = JournalEntry::with(['lines.chartOfAccount', 'creator', 'project'])->latest('entry_date')->latest('id');
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }
        $entries = $query->paginate(20)->withQueryString();
        $projects = Project::orderBy('title')->get(['id', 'title']);
        return view('admin.accounting.journal-entries.index', compact('entries', 'projects'));
    }

    public function journalEntriesCreate()
    {
        $accounts = ChartOfAccount::active()->orderBy('code')->get();
        $projects = Project::orderBy('title')->get(['id', 'title']);
        return view('admin.accounting.journal-entries.create', compact('accounts', 'projects'));
    }

    public function journalEntriesStore(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'lines' => 'required|array|min:2',
            'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        try {
            $this->ledger->postEntry([
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'source_type' => 'manual',
                'project_id' => $validated['project_id'] ?? null,
                'created_by' => auth()->id(),
            ], $validated['lines']);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Journal entry posted.');
    }

    public function journalEntriesShow(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.chartOfAccount', 'lines.project', 'creator', 'project']);
        return view('admin.accounting.journal-entries.show', compact('journalEntry'));
    }

    // ═══ Reports ═══

    public function reportsIndex()
    {
        return view('admin.accounting.reports.index');
    }

    public function trialBalance(Request $request)
    {
        $asOf = $request->input('as_of', now()->toDateString());
        $data = $this->ledger->trialBalance($asOf);
        return view('admin.accounting.reports.trial-balance', ['asOf' => $asOf] + $data);
    }

    public function profitLoss(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $projectId = $request->input('project_id');
        $data = $this->ledger->profitAndLoss($from, $to, $projectId ?: null);
        $projects = Project::orderBy('title')->get(['id', 'title']);
        return view('admin.accounting.reports.profit-loss', array_merge($data, ['projects' => $projects, 'projectId' => $projectId]));
    }
}
