<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Tender, Quotation, QuotationItem, Lpo, LpoItem, Grn, GrnItem, DeliveryNote, VendorInvoice, VendorPayment, OfficeExpense, ClientReceipt, ProjectBudget, SalesProposal};
use Illuminate\Http\Request;

class BusinessFlowController extends Controller
{
    // ═══ Tenders ═══
    public function tenders(Request $request)
    {
        $query = Tender::latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeTender(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'reference' => 'nullable|string',
            'client' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'submission_date' => 'nullable|date',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'draft';
        $data['company_id'] = $request->user()->company_id;
        $tender = Tender::create($data);

        return response()->json($tender, 201);
    }

    public function showTender(Tender $tender)
    {
        return response()->json($tender);
    }

    public function destroyTender(Tender $tender)
    {
        $tender->delete();
        return response()->json(['message' => 'Tender deleted']);
    }

    // ═══ Quotations ═══
    public function quotations(Request $request)
    {
        $query = Quotation::with('items')->latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeQuotation(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|string',
            'reference' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $quotation = Quotation::create([
            'client' => $data['client'],
            'reference' => $data['reference'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'total_amount' => collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']),
            'company_id' => $request->user()->company_id,
        ]);

        foreach ($data['items'] as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return response()->json($quotation->load('items'), 201);
    }

    public function destroyQuotation(Quotation $quotation)
    {
        $quotation->items()->delete();
        $quotation->delete();
        return response()->json(['message' => 'Quotation deleted']);
    }

    // ═══ LPOs ═══
    public function lpos(Request $request)
    {
        $query = Lpo::with('items')->latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeLpo(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $lpo = Lpo::create([
            'supplier_id' => $data['supplier_id'],
            'reference' => $data['reference'] ?? 'LPO-' . str_pad(Lpo::count() + 1, 5, '0', STR_PAD_LEFT),
            'delivery_date' => $data['delivery_date'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'total_amount' => collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']),
            'company_id' => $request->user()->company_id,
        ]);

        foreach ($data['items'] as $item) {
            LpoItem::create([
                'lpo_id' => $lpo->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return response()->json($lpo->load('items'), 201);
    }

    public function destroyLpo(Lpo $lpo)
    {
        $lpo->items()->delete();
        $lpo->delete();
        return response()->json(['message' => 'LPO deleted']);
    }

    // ═══ GRNs ═══
    public function grns(Request $request)
    {
        return response()->json(Grn::with('items')->latest()->paginate($request->per_page ?? 20));
    }

    public function storeGrn(Request $request)
    {
        $data = $request->validate([
            'lpo_id' => 'nullable|exists:lpos,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_by' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $grn = Grn::create([
            'lpo_id' => $data['lpo_id'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'received_by' => $data['received_by'] ?? $request->user()->name,
            'notes' => $data['notes'] ?? null,
            'company_id' => $request->user()->company_id,
        ]);

        foreach ($data['items'] as $item) {
            GrnItem::create([
                'grn_id' => $grn->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);

            // Update stock
            \App\Models\Product::where('id', $item['product_id'])->increment('stock_quantity', $item['quantity']);
        }

        return response()->json($grn->load('items'), 201);
    }

    // ═══ Delivery Notes ═══
    public function deliveryNotes(Request $request)
    {
        return response()->json(DeliveryNote::latest()->paginate($request->per_page ?? 20));
    }

    public function storeDeliveryNote(Request $request)
    {
        $data = $request->validate([
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'customer' => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $note = DeliveryNote::create($data);

        return response()->json($note, 201);
    }

    // ═══ Vendor Invoices ═══
    public function vendorInvoices(Request $request)
    {
        return response()->json(VendorInvoice::latest()->paginate($request->per_page ?? 20));
    }

    public function storeVendorInvoice(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'lpo_id' => 'nullable|exists:lpos,id',
            'invoice_number' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['status'] = $data['status'] ?? 'pending';
        $invoice = VendorInvoice::create($data);

        return response()->json($invoice, 201);
    }

    // ═══ Office Expenses ═══
    public function officeExpenses(Request $request)
    {
        $query = OfficeExpense::latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeOfficeExpense(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $data['status'] = 'pending';
        $data['requested_by'] = $request->user()->id;
        $data['company_id'] = $request->user()->company_id;
        $expense = OfficeExpense::create($data);

        return response()->json($expense, 201);
    }

    public function approveOfficeExpense(Request $request, OfficeExpense $expense)
    {
        $expense->update(['status' => 'approved', 'approved_by' => $request->user()->id]);
        return response()->json($expense);
    }

    public function rejectOfficeExpense(Request $request, OfficeExpense $expense)
    {
        $expense->update(['status' => 'rejected', 'approved_by' => $request->user()->id]);
        return response()->json($expense);
    }

    // ═══ Client Receipts ═══
    public function clientReceipts(Request $request)
    {
        return response()->json(ClientReceipt::latest()->paginate($request->per_page ?? 20));
    }

    public function storeClientReceipt(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'date' => 'required|date',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['received_by'] = $request->user()->id;
        $receipt = ClientReceipt::create($data);

        return response()->json($receipt, 201);
    }

    // ═══ Sales Proposals ═══
    public function proposals(Request $request)
    {
        $query = SalesProposal::with('items')->latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeProposal(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'draft';
        $data['company_id'] = $request->user()->company_id;
        $proposal = SalesProposal::create($data);

        return response()->json($proposal, 201);
    }

    // ═══ Project Budgets ═══
    public function budgets(Request $request)
    {
        return response()->json(ProjectBudget::with('project')->latest()->paginate($request->per_page ?? 20));
    }

    public function storeBudget(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'pending';
        $data['company_id'] = $request->user()->company_id;
        $budget = ProjectBudget::create($data);

        return response()->json($budget, 201);
    }

    public function approveBudget(Request $request, ProjectBudget $budget)
    {
        $budget->update(['status' => 'approved']);
        return response()->json($budget);
    }
}
