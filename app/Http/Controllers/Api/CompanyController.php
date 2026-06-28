<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Revenue;
use App\Models\Expense;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::with('parent', 'children')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'short_code' => 'required|string|max:10|unique:companies',
            'registration_number' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'currency' => 'nullable|string|max:3',
            'parent_id' => 'nullable|exists:companies,id',
            'is_group' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $data['created_by'] = auth()->id();
        $company = Company::create($data);
        return response()->json($company, 201);
    }

    public function show(Company $company)
    {
        $company->load('parent', 'children', 'bankAccounts');
        $company->stats = [
            'revenue' => Revenue::where('company_id', $company->id)->sum('amount'),
            'expenses' => Expense::where('company_id', $company->id)->sum('amount'),
            'sales' => SalesInvoice::where('company_id', $company->id)->sum('total_amount'),
            'purchases' => PurchaseInvoice::where('company_id', $company->id)->sum('total_amount'),
            'receivables' => SalesInvoice::where('company_id', $company->id)->sum('balance_amount'),
        ];
        return response()->json($company);
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'legal_name' => 'string|max:255',
            'short_code' => 'string|max:10|unique:companies,short_code,' . $company->id,
            'registration_number' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'boolean',
        ]);
        $company->update($data);
        return response()->json($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['message' => 'Company deleted']);
    }

    public function consolidated(Company $company)
    {
        $children = $company->children()->pluck('id');
        $allIds = $children->push($company->id);

        return response()->json([
            'company' => $company,
            'children' => Company::whereIn('id', $children)->get(),
            'consolidated_revenue' => Revenue::whereIn('company_id', $allIds)->sum('amount'),
            'consolidated_expenses' => Expense::whereIn('company_id', $allIds)->sum('amount'),
            'consolidated_sales' => SalesInvoice::whereIn('company_id', $allIds)->sum('total_amount'),
            'consolidated_receivables' => SalesInvoice::whereIn('company_id', $allIds)->sum('balance_amount'),
        ]);
    }
}
