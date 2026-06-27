<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\Project;
use App\Models\ProjectBudget;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\DeliveryNote;
use App\Models\VendorInvoice;
use App\Models\VendorPayment;
use App\Models\OfficeExpense;
use App\Models\ClientReceipt;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }
            return $next($request);
        });
    }

    // ═══════════════════════════════════════════════════════
    //  BUSINESS FLOW DASHBOARD
    //═══════════════════════════════════════════════════════
    public function dashboard()
    {
        $stats = [
            'tenders' => Tender::count(),
            'activeLeads' => CrmLead::whereNotIn('status', ['converted', 'rejected'])->count(),
            'openDeals' => CrmDeal::where('status', 'open')->count(),
            'activeProjects' => Project::whereNotIn('status', ['completed', 'cancelled'])->count(),
            'pendingLpos' => Lpo::where('status', 'draft')->count(),
            'pendingGrns' => Grn::where('status', 'discrepant')->count(),
            'unpaidVendorInvoices' => VendorInvoice::where('status', 'unpaid')->count(),
            'pendingExpenses' => OfficeExpense::where('status', 'pending')->count(),
            'totalProcurementValue' => Lpo::sum('total'),
            'totalVendorPaid' => VendorPayment::sum('amount'),
            'totalClientReceipts' => ClientReceipt::sum('amount'),
            'totalOfficeExpenses' => OfficeExpense::where('status', 'approved')->sum('amount'),
        ];

        $recentTenders = Tender::latest()->take(5)->get();
        $recentLpos = Lpo::with('project', 'supplier')->latest()->take(5)->get();
        $recentProjects = Project::with('deal')->latest()->take(5)->get();

        return view('admin.business-flow.dashboard', compact('stats', 'recentTenders', 'recentLpos', 'recentProjects'));
    }

    // ═══════════════════════════════════════════════════════
    //  1. TENDERS
    //═══════════════════════════════════════════════════════
    public function tenderIndex(Request $request)
    {
        $query = Tender::with(['lead', 'assignedTo']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")->orWhere('tender_number', 'like', "%{$q}%")->orWhere('client_name', 'like', "%{$q}%");
            });
        }
        $tenders = $query->latest()->paginate(15)->appends($request->except('page'));
        return view('admin.business-flow.tenders.index', compact('tenders'));
    }

    public function tenderStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_organization' => 'nullable|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string|max:20',
            'submission_date' => 'nullable|date',
            'closing_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['tender_number'] = 'TND-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['status'] = 'received';
        $data['created_by'] = auth()->id();
        Tender::create($data);
        return redirect()->route('admin.tenders.index')->with('success', 'Tender created successfully.');
    }

    public function tenderShow(Tender $tender)
    {
        $tender->load(['lead.deals', 'assignedTo']);
        return view('admin.business-flow.tenders.show', compact('tender'));
    }

    public function tenderDestroy(Tender $tender)
    {
        $tender->delete();
        return redirect()->route('admin.tenders.index')->with('success', 'Tender deleted.');
    }

    // Convert Tender → Lead
    public function tenderConvertToLead(Tender $tender)
    {
        if ($tender->lead()->exists()) {
            return redirect()->back()->with('error', 'This tender has already been converted to a lead.');
        }

        $lead = CrmLead::create([
            'lead_number' => 'LEAD-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'tender_id' => $tender->id,
            'first_name' => $tender->client_name,
            'email' => $tender->client_email,
            'phone' => $tender->client_phone,
            'company' => $tender->client_organization,
            'source' => 'Tender',
            'status' => 'qualified',
            'notes' => 'Converted from Tender: ' . $tender->tender_number,
            'assigned_to' => $tender->assigned_to,
            'created_by' => auth()->id(),
        ]);

        $tender->update(['status' => 'converted']);

        return redirect()->route('admin.crm-leads.index')->with('success', "Tender converted to Lead successfully. Lead: {$lead->lead_number}");
    }

    // ═══════════════════════════════════════════════════════
    //  2. QUOTATIONS
    //═══════════════════════════════════════════════════════
    public function quotationIndex()
    {
        $quotations = Quotation::with(['lead', 'items'])->latest()->paginate(15);
        $leads = CrmLead::whereNotIn('status', ['converted', 'rejected'])->get();
        return view('admin.business-flow.quotations.index', compact('quotations', 'leads'));
    }

    public function quotationStore(Request $request)
    {
        $data = $request->validate([
            'lead_id' => 'nullable|exists:crm_leads,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_percentage' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0; $tax = 0; $discount = 0;
        foreach ($data['items'] as $item) {
            $lineSub = $item['quantity'] * $item['unit_price'];
            $lineDisc = $item['discount_amount'] ?? 0;
            $lineTax = ($lineSub - $lineDisc) * ($item['tax_percentage'] ?? 0) / 100;
            $subtotal += $lineSub;
            $discount += $lineDisc;
            $tax += $lineTax;
        }

        $quotation = Quotation::create([
            'quotation_number' => 'QUO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'lead_id' => $data['lead_id'] ?? null,
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'] ?? null,
            'quotation_date' => $data['quotation_date'],
            'valid_until' => $data['valid_until'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total' => $subtotal - $discount + $tax,
            'terms' => $data['terms'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            $lineSub = $item['quantity'] * $item['unit_price'];
            $lineDisc = $item['discount_amount'] ?? 0;
            $lineTax = ($lineSub - $lineDisc) * ($item['tax_percentage'] ?? 0) / 100;
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'],
                'discount_amount' => $lineDisc,
                'tax_percentage' => $item['tax_percentage'] ?? 0,
                'line_total' => $lineSub - $lineDisc + $lineTax,
            ]);
        }

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function quotationShow(Quotation $quotation)
    {
        $quotation->load(['lead', 'items']);
        return view('admin.business-flow.quotations.show', compact('quotation'));
    }

    public function quotationUpdateStatus(Quotation $quotation, Request $request)
    {
        $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,expired']);
        $quotation->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Quotation status updated.');
    }

    public function quotationDestroy(Quotation $quotation)
    {
        $quotation->items()->delete();
        $quotation->delete();
        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  3. LEAD → DEAL CONVERSION
    //═══════════════════════════════════════════════════════
    public function leadConvertToDeal(CrmLead $lead)
    {
        if ($lead->deals()->exists()) {
            return redirect()->back()->with('error', 'This lead already has a deal.');
        }

        $deal = CrmDeal::create([
            'deal_number' => 'DEAL-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'title' => 'Deal from Lead: ' . $lead->full_name,
            'lead_id' => $lead->id,
            'value' => 0,
            'stage' => 'negotiation',
            'status' => 'open',
            'assigned_to' => $lead->assigned_to,
            'notes' => 'Converted from Lead: ' . $lead->lead_number,
        ]);

        $lead->update(['status' => 'converted']);

        return redirect()->route('admin.crm-deals.index')->with('success', "Lead converted to Deal: {$deal->deal_number}");
    }

    // ═══════════════════════════════════════════════════════
    //  4. DEAL → PROJECT CONVERSION
    //═══════════════════════════════════════════════════════
    public function dealConvertToProject(CrmDeal $deal)
    {
        if ($deal->project_id) {
            return redirect()->back()->with('error', 'This deal has already been converted to a project.');
        }

        $project = Project::create([
            'project_number' => 'PRJ-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'title' => $deal->title,
            'description' => $deal->notes,
            'status' => 'planning',
            'priority' => 'medium',
            'manager_id' => $deal->assigned_to,
            'budget' => $deal->value,
            'deal_id' => $deal->id,
        ]);

        $deal->update(['status' => 'won', 'project_id' => $project->id]);

        return redirect()->route('admin.projects.show', $project)->with('success', "Deal converted to Project: {$project->project_number}");
    }

    // ═══════════════════════════════════════════════════════
    //  5. PROJECT BUDGETS
    //═══════════════════════════════════════════════════════
    public function budgetIndex()
    {
        $budgets = ProjectBudget::with(['project', 'approvedBy'])->latest()->paginate(15);
        $projects = Project::whereNotIn('status', ['completed', 'cancelled'])->get();
        return view('admin.business-flow.budgets.index', compact('budgets', 'projects'));
    }

    public function budgetStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'total_budget' => 'required|numeric|min:0',
            'procurement_budget' => 'nullable|numeric|min:0',
            'office_expense_budget' => 'nullable|numeric|min:0',
            'labor_budget' => 'nullable|numeric|min:0',
            'contingency' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $data['budget_number'] = 'BUD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        ProjectBudget::create($data);
        return redirect()->route('admin.budgets.index')->with('success', 'Budget created and pending approval.');
    }

    public function budgetApprove(ProjectBudget $budget)
    {
        $budget->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        // Update project budget
        $budget->project->update(['budget' => $budget->total_budget]);
        return redirect()->back()->with('success', 'Budget approved.');
    }

    public function budgetReject(ProjectBudget $budget)
    {
        $budget->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Budget rejected.');
    }

    public function budgetDestroy(ProjectBudget $budget)
    {
        $budget->delete();
        return redirect()->route('admin.budgets.index')->with('success', 'Budget deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  6. LPO (Local Purchase Orders)
    //═══════════════════════════════════════════════════════
    public function lpoIndex()
    {
        $lpos = Lpo::with(['project', 'supplier', 'items'])->latest()->paginate(15);
        $projects = Project::whereNotIn('status', ['completed', 'cancelled'])->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.business-flow.lpos.index', compact('lpos', 'projects', 'suppliers'));
    }

    public function lpoStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'lpo_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity_ordered' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity_ordered'] * $item['unit_price'];
        }
        $tax = $subtotal * 0.18; // 18% VAT
        $total = $subtotal + $tax;

        $supplier = Supplier::find($data['supplier_id'] ?? null);

        $lpo = Lpo::create([
            'lpo_number' => 'LPO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'project_id' => $data['project_id'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'supplier_name' => $supplier?->name,
            'lpo_date' => $data['lpo_date'],
            'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $total,
            'terms' => $data['terms'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            LpoItem::create([
                'lpo_id' => $lpo->id,
                'description' => $item['description'],
                'quantity_ordered' => $item['quantity_ordered'],
                'quantity_received' => 0,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity_ordered'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.lpos.index')->with('success', 'LPO created successfully.');
    }

    public function lpoShow(Lpo $lpo)
    {
        $lpo->load(['project', 'supplier', 'items', 'grns.items', 'deliveryNotes', 'vendorInvoices.payments']);
        return view('admin.business-flow.lpos.show', compact('lpo'));
    }

    public function lpoUpdateStatus(Lpo $lpo, Request $request)
    {
        $request->validate(['status' => 'required|in:draft,sent,partially_received,received,closed,cancelled']);
        $lpo->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'LPO status updated.');
    }

    public function lpoDestroy(Lpo $lpo)
    {
        $lpo->items()->delete();
        $lpo->delete();
        return redirect()->route('admin.lpos.index')->with('success', 'LPO deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  7. GRN (Goods Received Note)
    //═══════════════════════════════════════════════════════
    public function grnIndex()
    {
        $grns = Grn::with(['lpo', 'supplier', 'items'])->latest()->paginate(15);
        $lpos = Lpo::where('status', 'sent')->orWhere('status', 'partially_received')->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.business-flow.grns.index', compact('grns', 'lpos', 'suppliers'));
    }

    public function grnStore(Request $request)
    {
        $data = $request->validate([
            'lpo_id' => 'nullable|exists:lpos,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'received_date' => 'required|date',
            'delivery_note_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.lpo_item_id' => 'nullable|exists:lpo_items,id',
            'items.*.description' => 'required|string',
            'items.*.quantity_expected' => 'nullable|numeric|min:0',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.remarks' => 'nullable|string',
        ]);

        $hasDiscrepancy = false;
        foreach ($data['items'] as $item) {
            if (isset($item['quantity_expected']) && $item['quantity_expected'] != $item['quantity_received']) {
                $hasDiscrepancy = true;
            }
        }

        $grn = Grn::create([
            'grn_number' => 'GRN-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'lpo_id' => $data['lpo_id'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'received_date' => $data['received_date'],
            'delivery_note_number' => $data['delivery_note_number'] ?? null,
            'status' => $hasDiscrepancy ? 'discrepant' : 'received',
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            $disc = ($item['quantity_expected'] ?? $item['quantity_received']) - $item['quantity_received'];
            GrnItem::create([
                'grn_id' => $grn->id,
                'lpo_item_id' => $item['lpo_item_id'] ?? null,
                'description' => $item['description'],
                'quantity_expected' => $item['quantity_expected'] ?? $item['quantity_received'],
                'quantity_received' => $item['quantity_received'],
                'quantity_discrepant' => abs($disc),
                'unit' => $item['unit'] ?? null,
                'remarks' => $item['remarks'] ?? null,
            ]);

            // Update LPO item received quantity
            if (!empty($item['lpo_item_id'])) {
                $lpoItem = LpoItem::find($item['lpo_item_id']);
                if ($lpoItem) {
                    $lpoItem->increment('quantity_received', $item['quantity_received']);
                }
            }
        }

        // Update LPO status
        if (!empty($data['lpo_id'])) {
            $lpo = Lpo::find($data['lpo_id']);
            $allReceived = $lpo->items->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
            $anyReceived = $lpo->items->some(fn($i) => $i->quantity_received > 0);
            if ($allReceived) $lpo->update(['status' => 'received']);
            elseif ($anyReceived) $lpo->update(['status' => 'partially_received']);
        }

        return redirect()->route('admin.grns.index')->with('success', 'GRN created successfully.');
    }

    public function grnShow(Grn $grn)
    {
        $grn->load(['lpo', 'supplier', 'items.lpoItem']);
        return view('admin.business-flow.grns.show', compact('grn'));
    }

    public function grnDestroy(Grn $grn)
    {
        $grn->items()->delete();
        $grn->delete();
        return redirect()->route('admin.grns.index')->with('success', 'GRN deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  8. DELIVERY NOTES
    //═══════════════════════════════════════════════════════
    public function deliveryNoteIndex()
    {
        $deliveryNotes = DeliveryNote::with(['lpo', 'supplier', 'grn'])->latest()->paginate(15);
        $lpos = Lpo::whereIn('status', ['sent', 'partially_received', 'received'])->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.business-flow.delivery-notes.index', compact('deliveryNotes', 'lpos', 'suppliers'));
    }

    public function deliveryNoteStore(Request $request)
    {
        $data = $request->validate([
            'lpo_id' => 'nullable|exists:lpos,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'grn_id' => 'nullable|exists:grns,id',
            'delivery_date' => 'required|date',
            'delivered_by' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $data['delivery_note_number'] = 'DN-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['status'] = 'delivered';
        $data['created_by'] = auth()->id();
        DeliveryNote::create($data);
        return redirect()->route('admin.delivery-notes.index')->with('success', 'Delivery Note created.');
    }

    public function deliveryNoteDestroy(DeliveryNote $deliveryNote)
    {
        $deliveryNote->delete();
        return redirect()->route('admin.delivery-notes.index')->with('success', 'Delivery Note deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  9. VENDOR INVOICES
    //═══════════════════════════════════════════════════════
    public function vendorInvoiceIndex()
    {
        $invoices = VendorInvoice::with(['lpo', 'supplier', 'project', 'payments'])->latest()->paginate(15);
        $lpos = Lpo::whereIn('status', ['sent', 'partially_received', 'received', 'closed'])->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $projects = Project::whereNotIn('status', ['completed', 'cancelled'])->get();
        return view('admin.business-flow.vendor-invoices.index', compact('invoices', 'lpos', 'suppliers', 'projects'));
    }

    public function vendorInvoiceStore(Request $request)
    {
        $data = $request->validate([
            'lpo_id' => 'nullable|exists:lpos,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'project_id' => 'nullable|exists:projects,id',
            'supplier_invoice_ref' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $data['vendor_invoice_number'] = 'VINV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['amount_paid'] = 0;
        $data['balance'] = $data['total'];
        $data['status'] = 'unpaid';
        $data['created_by'] = auth()->id();
        VendorInvoice::create($data);
        return redirect()->route('admin.vendor-invoices.index')->with('success', 'Vendor Invoice created.');
    }

    public function vendorInvoiceShow(VendorInvoice $invoice)
    {
        $invoice->load(['lpo', 'supplier', 'project', 'payments']);
        return view('admin.business-flow.vendor-invoices.show', compact('invoice'));
    }

    public function vendorInvoiceDestroy(VendorInvoice $invoice)
    {
        $invoice->payments()->delete();
        $invoice->delete();
        return redirect()->route('admin.vendor-invoices.index')->with('success', 'Vendor Invoice deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  10. VENDOR PAYMENTS
    //═══════════════════════════════════════════════════════
    public function vendorPaymentStore(Request $request)
    {
        $data = $request->validate([
            'vendor_invoice_id' => 'required|exists:vendor_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $invoice = VendorInvoice::findOrFail($data['vendor_invoice_id']);
        $data['payment_number'] = 'VPAY-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['supplier_id'] = $invoice->supplier_id;
        $data['status'] = 'completed';
        $data['created_by'] = auth()->id();

        VendorPayment::create($data);

        // Update invoice
        $invoice->increment('amount_paid', $data['amount']);
        $invoice->decrement('balance', $data['amount']);
        if ($invoice->balance <= 0) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partially_paid']);
        }

        // Update project actual cost
        if ($invoice->project_id) {
            $project = $invoice->project;
            $project->increment('actual_cost', $data['amount']);
        }

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    // ═══════════════════════════════════════════════════════
    //  11. OFFICE EXPENSES (with approval workflow)
    //═══════════════════════════════════════════════════════
    public function officeExpenseIndex()
    {
        $expenses = OfficeExpense::with(['project', 'requestedBy', 'approvedBy'])->latest()->paginate(15);
        $projects = Project::whereNotIn('status', ['completed', 'cancelled'])->get();
        return view('admin.business-flow.office-expenses.index', compact('expenses', 'projects'));
    }

    public function officeExpenseStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'category' => 'nullable|in:transport,supplies,meals,utilities,misc',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,cheque,mobile_money',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['expense_number'] = 'OEXP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['status'] = 'pending';
        $data['requested_by'] = auth()->id();
        $data['created_by'] = auth()->id();

        // Auto-approve if amount is small (under 100,000 TZS)
        if ($data['amount'] <= 100000) {
            $data['status'] = 'approved';
            $data['approved_by'] = auth()->id();
            $data['approved_at'] = now();
            $data['approval_level'] = 1;
        }

        OfficeExpense::create($data);
        return redirect()->route('admin.office-expenses.index')->with('success', 'Office Expense submitted.');
    }

    public function officeExpenseApprove(OfficeExpense $expense)
    {
        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_level' => $expense->approval_level + 1,
        ]);

        // Add to project cost
        if ($expense->project_id) {
            $expense->project->increment('actual_cost', $expense->amount);
        }

        return redirect()->back()->with('success', 'Office Expense approved.');
    }

    public function officeExpenseReject(OfficeExpense $expense)
    {
        $expense->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Office Expense rejected.');
    }

    public function officeExpenseDestroy(OfficeExpense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.office-expenses.index')->with('success', 'Office Expense deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  12. CLIENT RECEIPTS
    //═══════════════════════════════════════════════════════
    public function clientReceiptIndex()
    {
        $receipts = ClientReceipt::with(['project', 'receivedBy'])->latest()->paginate(15);
        $projects = Project::whereNotIn('status', ['cancelled'])->get();
        return view('admin.business-flow.client-receipts.index', compact('receipts', 'projects'));
    }

    public function clientReceiptStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,bank_transfer,cheque,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'invoice_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['receipt_number'] = 'CR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['received_by'] = auth()->id();
        $data['created_by'] = auth()->id();
        ClientReceipt::create($data);

        // Update project revenue
        if (!empty($data['project_id'])) {
            $project = Project::find($data['project_id']);
            $project->increment('actual_revenue', $data['amount']);
        }

        return redirect()->route('admin.client-receipts.index')->with('success', 'Client Receipt created.');
    }

    public function clientReceiptDestroy(ClientReceipt $receipt)
    {
        if ($receipt->project_id) {
            $receipt->project->decrement('actual_revenue', $receipt->amount);
        }
        $receipt->delete();
        return redirect()->route('admin.client-receipts.index')->with('success', 'Client Receipt deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  PROJECT PROFIT TRACKING
    //═══════════════════════════════════════════════════════
    public function projectProfit(Project $project)
    {
        $project->load(['lpos.items', 'vendorInvoices.payments', 'officeExpenses', 'clientReceipts', 'budgets']);
        $totalProcurement = $project->totalProcurementCost();
        $totalOfficeExp = $project->totalOfficeExpenses();
        $totalRevenue = $project->totalRevenue();
        $totalCost = $totalProcurement + $totalOfficeExp;
        $profit = $totalRevenue - $totalCost;
        $margin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

        return view('admin.business-flow.project-profit', compact('project', 'totalProcurement', 'totalOfficeExp', 'totalRevenue', 'totalCost', 'profit', 'margin'));
    }
}
