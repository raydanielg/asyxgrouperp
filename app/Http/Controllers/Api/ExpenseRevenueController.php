<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class ExpenseRevenueController extends Controller
{
    // ═══ Expenses ═══
    public function expenses(Request $request)
    {
        $query = Expense::latest();

        if ($request->from && $request->to) {
            $query->whereBetween('expense_date', [$request->from, $request->to]);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['created_by'] = $request->user()->id;
        $expense = Expense::create($data);

        return response()->json($expense, 201);
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();
        return response()->json(['message' => 'Expense deleted']);
    }

    // ═══ Revenues ═══
    public function revenues(Request $request)
    {
        $query = Revenue::latest();

        if ($request->from && $request->to) {
            $query->whereBetween('revenue_date', [$request->from, $request->to]);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeRevenue(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'revenue_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['created_by'] = $request->user()->id;
        $revenue = Revenue::create($data);

        return response()->json($revenue, 201);
    }

    public function destroyRevenue(Revenue $revenue)
    {
        $revenue->delete();
        return response()->json(['message' => 'Revenue deleted']);
    }

    // ═══ Bank Accounts ═══
    public function bankAccounts(Request $request)
    {
        return response()->json(BankAccount::latest()->paginate($request->per_page ?? 20));
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate([
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'balance' => 'nullable|numeric',
            'currency' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $account = BankAccount::create($data);

        return response()->json($account, 201);
    }

    // ═══ Financial Summary ═══
    public function financialSummary(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        return response()->json([
            'total_revenue' => Revenue::whereMonth('revenue_date', $month)->whereYear('revenue_date', $year)->sum('amount') ?? 0,
            'total_expenses' => Expense::whereMonth('expense_date', $month)->whereYear('expense_date', $year)->sum('amount') ?? 0,
            'total_sales' => \App\Models\SalesInvoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('total_amount') ?? 0,
            'total_purchases' => \App\Models\PurchaseInvoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('total_amount') ?? 0,
            'net_profit' => (Revenue::whereMonth('revenue_date', $month)->whereYear('revenue_date', $year)->sum('amount') ?? 0) - (Expense::whereMonth('expense_date', $month)->whereYear('expense_date', $year)->sum('amount') ?? 0),
            'bank_balance' => BankAccount::sum('balance') ?? 0,
            'outstanding_receivables' => \App\Models\SalesInvoice::sum('balance_amount') ?? 0,
            'outstanding_payables' => \App\Models\PurchaseInvoice::sum('balance_amount') ?? 0,
        ]);
    }
}
