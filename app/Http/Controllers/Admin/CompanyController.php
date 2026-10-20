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
    public function index(Request $request)
    {
        $companies = Company::with('parent')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', $term)
                      ->orWhere('legal_name', 'like', $term)
                      ->orWhere('short_code', 'like', $term)
                      ->orWhere('email', 'like', $term);
                });
            })
            ->orderBy('is_group', 'desc')->orderBy('name')->get();
        $groupCompany = Company::where('is_group', true)->first();
        return view('admin.companies.index', compact('companies', 'groupCompany'));
    }

    public function export()
    {
        $companies = Company::with('parent')->orderBy('is_group', 'desc')->orderBy('name')->get();

        $rows = [['Company', 'Legal Name', 'Code', 'Type', 'Parent', 'Currency', 'Plan', 'Subscription', 'Billing Cycle', 'MRR', 'Users', 'Status']];
        foreach ($companies as $c) {
            $rows[] = [
                $c->name,
                $c->legal_name,
                $c->short_code,
                $c->is_group ? 'Group' : 'Operating',
                $c->parent?->short_code ?? '',
                $c->currency,
                Company::PLANS[$c->subscription_plan] ?? $c->subscription_plan,
                Company::SUBSCRIPTION_STATUSES[$c->subscription_status] ?? $c->subscription_status,
                $c->billing_cycle ?? '',
                $c->mrr ?? '',
                $c->users()->count(),
                $c->is_active ? 'Active' : 'Inactive',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="companies-' . now()->format('Y-m-d') . '.csv"',
        ]);
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
            'vrn' => 'nullable|string|max:255',
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

        return back()
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
            'vrn' => 'nullable|string|max:255',
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

        return back()
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->users()->exists() || $company->employees()->exists()) {
            return back()->with('error', 'Cannot delete a company with active users or employees.');
        }
        $company->delete();
        return back()
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * SUPER ADMIN ONLY — suspend a company: instantly locks out every user
     * whose current company is this one (login + active sessions).
     */
    public function suspend(Request $request, Company $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Only the ERP Super Administrator can suspend a company.');

        if ($company->is_group) {
            return back()->with('error', 'The group holding company cannot be suspended.');
        }

        $data = $request->validate([
            'suspension_reason' => 'nullable|string|max:255',
        ]);

        $company->update([
            'subscription_status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $data['suspension_reason'] ?? null,
        ]);

        return back()->with('success', $company->name . ' has been suspended. All ' . $company->users()->count() . ' user account(s) are now locked out.');
    }

    /**
     * SUPER ADMIN ONLY — reactivate a suspended company.
     */
    public function activate(Company $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Only the ERP Super Administrator can activate a company.');

        $company->update([
            'is_active' => true,
            'subscription_status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', $company->name . ' reactivated. Users can sign in again.');
    }

    /**
     * SUPER ADMIN ONLY — manage a company's SaaS subscription.
     */
    public function updateSubscription(Request $request, Company $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Only the ERP Super Administrator can manage subscriptions.');

        $validated = $request->validate([
            'subscription_plan' => 'required|in:' . implode(',', array_keys(Company::PLANS)),
            'subscription_status' => 'required|in:' . implode(',', array_keys(Company::SUBSCRIPTION_STATUSES)),
            'billing_cycle' => 'nullable|in:monthly,quarterly,yearly',
            'mrr' => 'nullable|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'subscription_starts_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date|after_or_equal:subscription_starts_at',
        ]);

        if ($company->is_group && $validated['subscription_status'] === 'suspended') {
            return back()->with('error', 'The group holding company cannot be suspended.');
        }

        if ($validated['subscription_status'] === 'suspended' && $company->subscription_status !== 'suspended') {
            $validated['suspended_at'] = now();
        } elseif ($validated['subscription_status'] !== 'suspended') {
            $validated['suspended_at'] = null;
            $validated['suspension_reason'] = null;
            if ($validated['subscription_status'] === 'active') {
                $validated['is_active'] = true;
            }
        }

        $company->update($validated);

        return back()->with('success', 'Subscription updated for ' . $company->name . '.');
    }

    /**
     * Group Executive Dashboard (SRS 2.1): revenue by company, profitability by
     * company, group revenue, group profitability, receivables by company, cash
     * position by company, and company comparisons — all in one place.
     */
    public function consolidated()
    {
        $companies = Company::operating()->withCount(['users', 'employees', 'projects', 'tenders'])->get();

        $consolidated = [];
        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalProjects = 0;
        $totalEmployees = 0;
        $totalReceivables = 0;
        $totalCashPosition = 0;

        foreach ($companies as $company) {
            $revenue = Revenue::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('revenue_date', date('Y'))->sum('amount');
            $expenses = Expense::withoutGlobalScope('company')->where('company_id', $company->id)->whereYear('expense_date', date('Y'))->sum('amount');
            $profit = $revenue - $expenses;
            $receivables = \App\Models\SalesInvoice::withoutGlobalScope('company')->where('company_id', $company->id)->sum('balance_amount');
            $cashPosition = \App\Models\BankAccount::withoutGlobalScope('company')->where('company_id', $company->id)->sum('current_balance');

            $consolidated[] = [
                'company' => $company,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'projects' => $company->projects_count,
                'employees' => $company->employees_count,
                'receivables' => $receivables,
                'cash_position' => $cashPosition,
            ];

            $totalRevenue += $revenue;
            $totalExpenses += $expenses;
            $totalProjects += $company->projects_count;
            $totalEmployees += $company->employees_count;
            $totalReceivables += $receivables;
            $totalCashPosition += $cashPosition;
        }

        $intercompanyPending = IntercompanyTransaction::where('status', 'pending')->count();
        $intercompanyAmount = IntercompanyTransaction::where('status', 'completed')->whereNull('eliminated_at')->sum('amount');

        return view('admin.companies.consolidated', compact(
            'consolidated', 'companies', 'totalRevenue', 'totalExpenses', 'totalProjects', 'totalEmployees',
            'totalReceivables', 'totalCashPosition', 'intercompanyPending', 'intercompanyAmount'
        ));
    }

    public function switchCompany(Request $request)
    {
        $user = auth()->user();
        $company = $request->get('company', 'all');

        if ($company === 'all') {
            if (!$user->isSuperAdmin()) {
                session(['switched_company_id' => $user->company_id]);
                return redirect()->back()->with('error', 'You do not have access to the group view.');
            }
            session(['switched_company_id' => null]);
            return redirect()->back()->with('success', 'Group view enabled.');
        }

        $companyId = (int) $company;
        $companyModel = Company::find($companyId);

        if (!$companyModel || !$companyModel->is_active) {
            return redirect()->back()->with('error', 'Company not found or inactive.');
        }

        // Tenant admins and regular users can only switch to their own company
        if (!$user->isSuperAdmin() && !$user->hasPermission('switch-companies') && $user->company_id !== $companyId) {
            return redirect()->back()->with('error', 'You do not have access to that company.');
        }
        if ($user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Company admins cannot switch companies.');
        }

        session(['switched_company_id' => $companyId]);
        return redirect()->back()->with('success', 'Switched to ' . $companyModel->name . '.');
    }
}
