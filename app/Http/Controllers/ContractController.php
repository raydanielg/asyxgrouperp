<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $contracts = Contract::with(['project', 'createdBy'])->latest()->paginate(20);
        $projects = Project::orderBy('title')->get(['id', 'title']);
        return view('admin.contracts.index', compact('contracts', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'type' => 'required|in:service,supply,construction,maintenance,consultancy,other',
            'project_id' => 'nullable|exists:projects,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $contract = Contract::create([
            'company_id' => auth()->user()->company_id ?? 1,
            'contract_number' => 'CNT-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'title' => $data['title'],
            'contractor_name' => $data['contractor_name'],
            'type' => $data['type'],
            'project_id' => $data['project_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'contract_value' => $data['contract_value'],
            'status' => 'draft',
            'description' => $data['description'],
            'terms' => $data['terms'],
            'created_by' => auth()->id(),
        ]);

        \App\Services\ApprovalService::submit('contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, auth()->user());

        return back()->with('success', 'Contract created successfully.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['project', 'createdBy']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'type' => 'required|in:service,supply,construction,maintenance,consultancy,other',
            'project_id' => 'nullable|exists:projects,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        // Client-supplied status is never trusted — activation goes through
        // the dedicated activate endpoint with workflow authorization.
        $data['status'] = $contract->status;

        // Material change → invalidate any previous approval
        $materialChanged =
            (float)$contract->contract_value !== (float)$data['contract_value'] ||
            $contract->contractor_name !== $data['contractor_name'];

        $contract->update($data);

        if ($materialChanged) {
            \App\Services\ApprovalService::invalidateAndResubmit(
                'contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, auth()->user()
            );
        }

        return back()->with('success', 'Contract updated.');
    }

    /**
     * Activate a contract — the only path from draft to active.
     * Requires an approved 'contract' workflow request when configured.
     */
    public function activate(Contract $contract)
    {
        $user = auth()->user();
        abort_if($contract->status !== 'draft', 403, 'Only draft contracts can be activated.');
        abort_if($contract->created_by === $user->id, 403, 'You cannot activate a contract you created.');

        $req = \App\Models\ApprovalRequest::where('module', 'contract')
            ->where('module_id', $contract->id)->latest('id')->first();

        if (\App\Services\ApprovalService::findWorkflow('contract', $contract->company_id)) {
            abort_if(!$req, 403, 'This contract has not been submitted for approval.');
            abort_if($req->status === 'rejected', 403, 'This contract was rejected.');
            abort_if($req->status !== 'approved', 403, 'This contract is still awaiting approval.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($contract, $user) {
            $contract->update(['status' => 'active']);
            \App\Models\AuditLog::log('execute', 'contracts', [
                'module_action' => 'contract_activated',
                'new_values' => ['contract' => $contract->contract_number, 'value' => $contract->contract_value],
            ]);
        });

        return back()->with('success', "Contract {$contract->contract_number} activated.");
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return back()->with('success', 'Contract deleted.');
    }
}
