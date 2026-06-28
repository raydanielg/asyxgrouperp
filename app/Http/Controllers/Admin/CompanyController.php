<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\IntercompanyTransaction;
use App\Models\Project;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Tender;
use App\Models\Revenue;
use App\Models\Expense;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with('parent')->orderBy('is_group', 'desc')->orderBy('name')->get();
        $groupCompany = Company::where('is_group', true)->first();
        return view('admin.companies.index', compact('companies', 'groupCompany'));
    }

    public function create()
    {
        $parents = Company::where('is_group', true)->pluck('legal_name', 'id');
        return view('admin.companies.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'short_code' => 'required|string|max:10|unique:companies,short_code',
            'registration_number' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'currency' => 'required|string|max:3',
            'fiscal_year_start' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:companies,id',
            'is_group' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_group'] = $request->boolean('is_group');
        $validated['is_active'] = $request->boolean('is_active', true);

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->load(['parent', 'children']);

        $stats = [
            'users' => $company->users()->count(),
            'employees' => $company->employees()->count(),
            'projects' => $company->projects()->count(),
            'tenders' => $company->tenders()->count(),
            'bank_accounts' => $company->bankAccounts()->count(),
            'revenue_ytd' => Revenue::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('revenue_date', date('Y'))->sum('amount'),
            'expenses_ytd' => Expense::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('expense_date', date('Y'))->sum('amount'),
        ];

        $intercompanyOut = IntercompanyTransaction::where('from_company_id', $company->id)->orderBy('transaction_date', 'desc')->limit(10)->get();
        $intercompanyIn = IntercompanyTransaction::where('to_company_id', $company->id)->orderBy('transaction_date', 'desc')->limit(10)->get();

        return view('admin.companies.show', compact('company', 'stats', 'intercompanyOut', 'intercompanyIn'));
    }

    public function edit(Company $company)
    {
        $parents = Company::where('is_group', true)->where('id', '!=', $company->id)->pluck('legal_name', 'id');
        return view('admin.companies.edit', compact('company', 'parents'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'short_code' => 'required|string|max:10|unique:companies,short_code,' . $company->id,
            'registration_number' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'currency' => 'required|string|max:3',
            'fiscal_year_start' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:companies,id',
            'is_group' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_group'] = $request->boolean('is_group');
        $validated['is_active'] = $request->boolean('is_active', true);

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->users()->exists() || $company->employees()->exists()) {
            return back()->with('error', 'Cannot delete a company with active users or employees.');
        }
        $company->delete();
        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function consolidated()
    {
        $companies = Company::operating()->withCount(['users', 'employees', 'projects', 'tenders'])->get();

        $consolidated = [];
        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalProjects = 0;
        $totalEmployees = 0;

        foreach ($companies as $company) {
            $revenue = Revenue::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('revenue_date', date('Y'))->sum('amount');
            $expenses = Expense::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('expense_date', date('Y'))->sum('amount');
            $profit = $revenue - $expenses;

            $consolidated[] = [
                'company' => $company,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'projects' => $company->projects_count,
                'employees' => $company->employees_count,
            ];

            $totalRevenue += $revenue;
            $totalExpenses += $expenses;
            $totalProjects += $company->projects_count;
            $totalEmployees += $company->employees_count;
        }

        $intercompanyPending = IntercompanyTransaction::where('status', 'pending')->count();
        $intercompanyAmount = IntercompanyTransaction::where('status', 'completed')->whereNull('eliminated_at')->sum('amount');

        return view('admin.companies.consolidated', compact(
            'consolidated', 'companies', 'totalRevenue', 'totalExpenses', 'totalProjects', 'totalEmployees',
            'intercompanyPending', 'intercompanyAmount'
        ));
    }

    public function switchCompany(Request $request)
    {
        $company = $request->get('company', 'all');

        if ($company === 'all') {
            session(['switched_company_id' => null]);
        } else {
            session(['switched_company_id' => (int) $company]);
        }

        return redirect()->back()->with('success', 'Company context switched.');
    }
}
