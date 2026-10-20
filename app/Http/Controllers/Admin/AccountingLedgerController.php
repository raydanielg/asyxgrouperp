<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Project;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'branch_id' => ['nullable', \Illuminate\Validation\Rule::exists('branches','id')->where('company_id', auth()->user()->company_id)],
            'lines' => 'required|array|min:2',
            'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        try {
            $workflow = \App\Services\ApprovalService::findWorkflow('journal_entry', auth()->user()->company_id);

            if ($workflow) {
                // Approval required — create the entry pending approval
                $entry = \DB::transaction(function () use ($validated, $workflow) {
                    $totalDebit = array_sum(array_map(fn($l) => (float)($l['debit'] ?? 0), $validated['lines']));
                    $entry = JournalEntry::create([
                        'entry_number' => 'JE-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                        'entry_date' => $validated['entry_date'],
                        'reference' => $validated['reference'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'source_type' => 'manual',
                        'project_id' => $validated['project_id'] ?? null,
                        'status' => 'pending',
                        'created_by' => auth()->id(),
                        'branch_id' => $validated['branch_id'] ?? null,
                    ]);
                    foreach ($validated['lines'] as $line) {
                        JournalEntryLine::create([
                            'journal_entry_id' => $entry->id,
                            'chart_of_account_id' => $line['chart_of_account_id'],
                            'debit' => $line['debit'] ?? 0,
                            'credit' => $line['credit'] ?? 0,
                            'description' => $line['description'] ?? null,
                            'project_id' => $line['project_id'] ?? $validated['project_id'] ?? null,
                        ]);
                    }
                    \App\Services\ApprovalService::submit(
                        'journal_entry', $entry, "Journal {$entry->entry_number}", $totalDebit, auth()->user()
                    );
                    return $entry;
                });
                return back()->with('success', "Journal entry {$entry->entry_number} submitted for approval.");
            }

            // Documented policy: no journal_entry workflow configured →
            // direct posting permitted for holders of create-journal-entries.
            $this->ledger->postEntry([
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'source_type' => 'manual',
                'project_id' => $validated['project_id'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'created_by' => auth()->id(),
            ], $validated['lines']);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Journal entry posted.');
    }

    /**
     * Post an approved journal entry — applies the final status flip inside
     * a row lock so concurrent requests cannot double-post.
     */
    public function journalEntriesPost(JournalEntry $journalEntry)
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('post-journal-entries'), 403, 'You are not authorized to post journal entries.');

        \DB::transaction(function () use ($journalEntry, $user) {
            $entry = JournalEntry::lockForUpdate()->findOrFail($journalEntry->id);
            abort_if($entry->status === 'posted', 403, 'This entry is already posted.');
            abort_if($entry->status === 'rejected', 403, 'This entry was rejected and cannot be posted.');

            $req = \App\Models\ApprovalRequest::where('module', 'journal_entry')
                ->where('module_id', $entry->id)->latest('id')->first();
            abort_if($req && $req->status === 'rejected', 403, 'This entry was rejected.');
            abort_if($req && $req->status === 'pending', 403, 'This entry is still awaiting approval.');
            abort_if($req && $req->status === 'superseded', 403, 'This entry was modified — pending re-approval.');

            $entry->update(['status' => 'posted', 'posted_at' => now()]);
            \App\Models\AuditLog::log('execute', 'journal_entries', [
                'module_action' => 'journal_posted',
                'new_values' => ['entry' => $entry->entry_number, 'posted_by' => $user->name],
            ]);
        });

        return back()->with('success', "Journal entry {$journalEntry->entry_number} posted.");
    }

    /**
     * Reverse a posted entry — creates a linked reversal entry with
     * swapped debits/credits. The original is preserved.
     */
    public function journalEntriesReverse(JournalEntry $journalEntry)
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('reverse-journal-entries'), 403, 'You are not authorized to reverse journal entries.');
        abort_if($journalEntry->status !== 'posted', 403, 'Only posted entries can be reversed.');
        abort_unless($journalEntry->isReversible(), 403, 'This entry has already been reversed.');

        $reversal = \DB::transaction(function () use ($journalEntry, $user) {
            $original = JournalEntry::lockForUpdate()->with('lines')->findOrFail($journalEntry->id);
            abort_unless($original->isReversible(), 403, 'This entry has already been reversed.');

            $rev = JournalEntry::create([
                'entry_number' => 'JE-REV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'entry_date' => now()->toDateString(),
                'reference' => 'REVERSAL of ' . $original->entry_number,
                'description' => 'Reversal: ' . ($original->description ?? $original->entry_number),
                'source_type' => 'reversal',
                'reversal_of_id' => $original->id,
                'status' => 'posted',
                'created_by' => $user->id,
                'posted_at' => now(),
                'project_id' => $original->project_id,
                'branch_id' => $original->branch_id,
            ]);
            foreach ($original->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $rev->id,
                    'chart_of_account_id' => $line->chart_of_account_id,
                    'debit' => $line->credit,  // swapped
                    'credit' => $line->debit,
                    'description' => 'Reversal: ' . $line->description,
                    'project_id' => $line->project_id,
                ]);
            }

            \App\Models\AuditLog::log('update', 'journal_entries', [
                'module_action' => 'journal_reversed',
                'old_values' => ['entry' => $original->entry_number],
                'new_values' => ['reversal' => $rev->entry_number, 'reversed_by' => $user->name],
            ]);

            return $rev;
        });

        return back()->with('success', "Reversed as {$reversal->entry_number}.");
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
