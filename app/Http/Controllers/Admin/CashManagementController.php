<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\ApprovalTrack;
use App\Models\ApprovalWorkflow;
use App\Models\CashAccount;
use App\Models\CashTopupRequest;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\Project;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashManagementController extends Controller
{
    public function __construct(protected LedgerService $ledger)
    {
    }

    // ═══ Petty Cash ═══

    public function pettyCashIndex()
    {
        $accounts = CashAccount::pettyCash()->with('custodian')->latest()->get();
        return view('admin.accounting.petty-cash.index', compact('accounts'));
    }

    public function pettyCashStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'custodian_id' => 'nullable|exists:users,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
        ]);

        $pettyCashAccount = ChartOfAccount::where('code', '1020')->first();

        CashAccount::create([
            'type' => 'petty_cash',
            'name' => $data['name'],
            'code' => 'PC-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'custodian_id' => $data['custodian_id'] ?? null,
            'chart_of_account_id' => $pettyCashAccount?->id,
            'opening_balance' => $data['opening_balance'] ?? 0,
            'current_balance' => $data['opening_balance'] ?? 0,
            'currency' => $data['currency'] ?? 'TZS',
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.petty-cash.index')->with('success', 'Petty cash account created.');
    }

    public function pettyCashShow(CashAccount $pettyCash)
    {
        $pettyCash->load(['custodian', 'topupRequests.requester']);
        $transactions = $pettyCash->transactions()->with('creator')->paginate(20);
        return view('admin.accounting.petty-cash.show', ['account' => $pettyCash, 'transactions' => $transactions]);
    }

    public function pettyCashTopupStore(Request $request, CashAccount $pettyCash)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'nullable|string',
        ]);

        $this->submitTopupRequest($pettyCash, (float) $data['amount'], $data['purpose'] ?? null, null, 'Petty Cash Top-up - ' . $pettyCash->name);

        return back()->with('success', 'Top-up request submitted for approval.');
    }

    public function pettyCashExpenseStore(Request $request, CashAccount $pettyCash)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            $this->ledger->spendFromCashAccount($pettyCash, (float) $data['amount'], $data['description']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Expense recorded against petty cash.');
    }

    // ═══ Project Account Cards ═══

    public function projectAccountShow(Project $project)
    {
        $account = $this->getOrCreateProjectCashAccount($project);
        $account->load('topupRequests.requester');
        $transactions = $account->transactions()->with('creator')->paginate(20);
        $expenses = Expense::where('project_id', $project->id)->latest('expense_date')->take(25)->get();

        return view('admin.projects.account-card', [
            'project' => $project,
            'account' => $account,
            'transactions' => $transactions,
            'expenses' => $expenses,
        ]);
    }

    public function projectAccountTopupStore(Request $request, Project $project)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'nullable|string',
        ]);

        $account = $this->getOrCreateProjectCashAccount($project);
        $this->submitTopupRequest($account, (float) $data['amount'], $data['purpose'] ?? null, $project->id, 'Project Cash Top-up - ' . $project->title);

        return back()->with('success', 'Cash request submitted for approval.');
    }

    public function projectAccountExpenseStore(Request $request, Project $project)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $account = $this->getOrCreateProjectCashAccount($project);

        try {
            $transaction = $this->ledger->spendFromCashAccount($account, (float) $data['amount'], $data['description']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        Expense::create([
            'expense_number' => 'EXP-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'category' => $data['category'] ?? 'Project Expense',
            'amount' => $data['amount'],
            'expense_date' => now()->toDateString(),
            'project_id' => $project->id,
            'cash_account_id' => $account->id,
            'payment_method' => 'cash',
            'payee' => null,
            'notes' => $data['description'],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Expense recorded against project account card.');
    }

    // ═══ Helpers ═══

    protected function getOrCreateProjectCashAccount(Project $project): CashAccount
    {
        $account = CashAccount::where('type', 'project')->where('project_id', $project->id)->first();
        if ($account) {
            return $account;
        }

        $projectCashParent = ChartOfAccount::where('code', '1030')->first();

        return CashAccount::create([
            'type' => 'project',
            'project_id' => $project->id,
            'name' => $project->title . ' - Account Card',
            'code' => 'PJ-' . $project->id . '-' . strtoupper(Str::random(4)),
            'chart_of_account_id' => $projectCashParent?->id,
            'opening_balance' => 0,
            'current_balance' => 0,
            'currency' => 'TZS',
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);
    }

    protected function submitTopupRequest(CashAccount $account, float $amount, ?string $purpose, ?int $projectId, string $label): CashTopupRequest
    {
        $topup = CashTopupRequest::create([
            'cash_account_id' => $account->id,
            'project_id' => $projectId,
            'requested_by' => auth()->id(),
            'amount' => $amount,
            'purpose' => $purpose,
            'status' => 'pending',
        ]);

        $workflow = ApprovalWorkflow::where('module', 'cash_topup')->where('is_active', true)->first();

        if ($workflow) {
            $approvalRequest = ApprovalRequest::create([
                'request_number' => 'CTR-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'workflow_id' => $workflow->id,
                'module' => 'cash_topup',
                'module_id' => $topup->id,
                'module_label' => $label,
                'amount' => $amount,
                'status' => 'pending',
                'current_level' => 1,
                'requested_by' => auth()->id(),
            ]);

            ApprovalTrack::create([
                'approval_request_id' => $approvalRequest->id,
                'level' => 1,
                'action' => 'pending',
            ]);

            $topup->update(['approval_request_id' => $approvalRequest->id]);
        }

        return $topup;
    }
}
