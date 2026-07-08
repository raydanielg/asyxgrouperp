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

        Contract::create([
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

        return redirect()->route('admin.contracts.index')->with('success', 'Contract created successfully.');
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
            'status' => 'required|in:draft,active,completed,terminated',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $contract->update($data);

        return redirect()->route('admin.contracts.index')->with('success', 'Contract updated.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('admin.contracts.index')->with('success', 'Contract deleted.');
    }
}
