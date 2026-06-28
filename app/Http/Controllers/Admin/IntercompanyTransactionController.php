<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntercompanyTransaction;
use App\Models\IntercompanyLine;
use App\Models\Company;
use Illuminate\Http\Request;

class IntercompanyTransactionController extends Controller
{
    public function index()
    {
        $transactions = IntercompanyTransaction::with(['fromCompany', 'toCompany'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        return view('admin.companies.intercompany-index', compact('transactions'));
    }

    public function create()
    {
        $companies = Company::operating()->pluck('legal_name', 'id');
        return view('admin.companies.intercompany-create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_company_id' => 'required|exists:companies,id|different:to_company_id',
            'to_company_id' => 'required|exists:companies,id',
            'type' => 'required|string|in:invoice,transfer,shared_service,shared_staff,loan',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'lines' => 'nullable|array',
            'lines.*.description' => 'required_with:lines|string',
            'lines.*.quantity' => 'required_with:lines|numeric|min:0',
            'lines.*.unit_price' => 'required_with:lines|numeric|min:0',
        ]);

        $validated['transaction_number'] = 'ICT-' . date('Ym') . '-' . str_pad(IntercompanyTransaction::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'completed';
        $validated['created_by'] = auth()->id();

        $lines = $validated['lines'] ?? [];
        unset($validated['lines']);

        $transaction = IntercompanyTransaction::create($validated);

        foreach ($lines as $line) {
            IntercompanyLine::create([
                'intercompany_transaction_id' => $transaction->id,
                'description' => $line['description'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'line_total' => $line['quantity'] * $line['unit_price'],
            ]);
        }

        return redirect()->route('admin.intercompany.index')
            ->with('success', 'Intercompany transaction created successfully.');
    }

    public function show(IntercompanyTransaction $intercompany)
    {
        $intercompany->load(['fromCompany', 'toCompany', 'lines']);
        return view('admin.companies.intercompany-show', compact('intercompany'));
    }

    public function eliminate(IntercompanyTransaction $intercompany)
    {
        $intercompany->update([
            'status' => 'eliminated',
            'eliminated_at' => now(),
        ]);

        return back()->with('success', 'Transaction marked as eliminated for consolidation.');
    }

    public function destroy(IntercompanyTransaction $intercompany)
    {
        $intercompany->lines()->delete();
        $intercompany->delete();
        return redirect()->route('admin.intercompany.index')
            ->with('success', 'Intercompany transaction deleted.');
    }
}
