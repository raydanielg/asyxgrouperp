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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\LedgerService;
use Illuminate\Support\Facades\DB;

class BusinessFlowController extends Controller
{
    protected LedgerService $ledger;

    public function __construct()
    {
        $this->ledger = app(LedgerService::class);
        $this->middleware(function ($request, $next) {
            // Decision endpoints are guarded per-action inside the methods
            // (self-approval, designated approver, pending-state checks) so
            // non-admin designated approvers can reach them. Read/write
            // operations still require admin.
            $decisionMethods = ['officeExpenseApprove', 'officeExpenseReject', 'vendorPaymentExecute', 'grnReceive'];
            if (in_array($request->route()->getActionMethod(), $decisionMethods)) {
                return $next($request);
            }
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
        return back()->with('success', 'Tender created successfully.');
    }

    public function tenderShow(Tender $tender)
    {
        $tender->load(['lead.deals', 'assignedTo']);
        return view('admin.business-flow.tenders.show', compact('tender'));
    }

    public function tenderDestroy(Tender $tender)
    {
        $tender->delete();
        return back()->with('success', 'Tender deleted.');
    }

    // Convert Tender → Lead
    public function tenderConvertToLead(Tender $tender)
    {
        if ($tender->lead()->exists()) {
            return redirect()->back()->with('error', 'This tender has already been converted to a lead.');
        }

        $lead = CrmLead::create([
            'lead_number' => 'LEAD-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'company_id' => $tender->company_id,
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

        return back()->with('success', "Tender converted to Lead successfully. Lead: {$lead->lead_number}");
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
            'branch_id' => $data['branch_id'] ?? null,
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

        return back()->with('success', 'Quotation created successfully.');
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
        return back()->with('success', 'Quotation deleted.');
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
            'company_id' => $lead->company_id,
            'title' => 'Deal from Lead: ' . $lead->full_name,
            'lead_id' => $lead->id,
            'value' => 0,
            'stage' => 'negotiation',
            'status' => 'open',
            'assigned_to' => $lead->assigned_to,
            'notes' => 'Converted from Lead: ' . $lead->lead_number,
        ]);

        $lead->update(['status' => 'converted']);

        return back()->with('success', "Lead converted to Deal: {$deal->deal_number}");
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
            'company_id' => $deal->company_id,
            'title' => $deal->title,
            'description' => $deal->notes,
            'status' => 'planning',
            'priority' => 'medium',
            'manager_id' => $deal->assigned_to,
            'budget' => $deal->value,
            'deal_id' => $deal->id,
        ]);

        $deal->update(['status' => 'won', 'project_id' => $project->id]);

        return back()->with('success', "Deal converted to Project: {$project->project_number}");
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
        return back()->with('success', 'Budget created and pending approval.');
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
        return back()->with('success', 'Budget deleted.');
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
            'items.*.product_id' => ['nullable', \Illuminate\Validation\Rule::exists('products','id')->where('company_id', auth()->user()->company_id)],
            'branch_id' => ['nullable', \Illuminate\Validation\Rule::exists('branches','id')->where('company_id', auth()->user()->company_id)],
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
            'branch_id' => $data['branch_id'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            LpoItem::create([
                'lpo_id' => $lpo->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity_ordered' => $item['quantity_ordered'],
                'quantity_received' => 0,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity_ordered'] * $item['unit_price'],
            ]);
        }

        // Submit to the lpo approval workflow when configured
        \App\Services\ApprovalService::submit('lpo', $lpo, "LPO {$lpo->lpo_number}", $lpo->total, auth()->user());

        return back()->with('success', 'LPO created successfully.');
    }

    public function lpoShow(Lpo $lpo)
    {
        $lpo->load(['project', 'supplier', 'items', 'grns.items', 'deliveryNotes', 'vendorInvoices.payments']);
        return view('admin.business-flow.lpos.show', compact('lpo'));
    }

    public function lpoUpdateStatus(Lpo $lpo, Request $request)
    {
        $request->validate(['status' => 'required|in:draft,sent,partially_received,received,closed,cancelled']);

        // The 'sent' transition issues the LPO — requires an approved workflow
        // request when one is configured for the company.
        if ($request->status === 'sent' && $lpo->status === 'draft') {
            $req = \App\Models\ApprovalRequest::where('module', 'lpo')
                ->where('module_id', $lpo->id)->latest()->first();
            if ($req) {
                abort_if($req->status === 'rejected', 403, 'This LPO was rejected and cannot be issued.');
                abort_if($req->status !== 'approved', 403, 'This LPO is still awaiting approval.');
            } elseif (\App\Services\ApprovalService::findWorkflow('lpo', $lpo->company_id)) {
                // Workflow exists but request is missing (legacy record) — submit now
                \App\Services\ApprovalService::submit('lpo', $lpo, "LPO {$lpo->lpo_number}", $lpo->total, auth()->user());
                return back()->with('success', 'LPO submitted for approval.');
            }
        }

        $lpo->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'LPO status updated.');
    }

    public function lpoDestroy(Lpo $lpo)
    {
        abort_unless(in_array($lpo->status, ['draft', 'cancelled']), 403, 'Only draft or cancelled LPOs can be deleted.');
        $lpo->items()->delete();
        $lpo->delete();
        return back()->with('success', 'LPO deleted.');
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
            'warehouse_id' => ['nullable', \Illuminate\Validation\Rule::exists('warehouses','id')->where('company_id', auth()->user()->company_id)],
            'items' => 'required|array|min:1',
            'items.*.lpo_item_id' => 'nullable|exists:lpo_items,id',
            'items.*.product_id' => ['nullable', \Illuminate\Validation\Rule::exists('products','id')->where('company_id', auth()->user()->company_id)],
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

        // When a GRN approval workflow is configured, the GRN is created
        // PENDING — LPO quantities only update on finalization via grnReceive.
        $workflow = \App\Services\ApprovalService::findWorkflow('grn', auth()->user()->company_id);
        $status = $workflow ? 'pending' : ($hasDiscrepancy ? 'discrepant' : 'received');

        $grn = Grn::create([
            'grn_number' => 'GRN-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'lpo_id' => $data['lpo_id'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'received_date' => $data['received_date'],
            'delivery_note_number' => $data['delivery_note_number'] ?? null,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            $disc = ($item['quantity_expected'] ?? $item['quantity_received']) - $item['quantity_received'];
            GrnItem::create([
                'grn_id' => $grn->id,
                'lpo_item_id' => $item['lpo_item_id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity_expected' => $item['quantity_expected'] ?? $item['quantity_received'],
                'quantity_received' => $item['quantity_received'],
                'quantity_discrepant' => abs($disc),
                'unit' => $item['unit'] ?? null,
                'remarks' => $item['remarks'] ?? null,
            ]);
        }

        if ($workflow) {
            \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, auth()->user());
        } else {
            // No workflow — apply receipt effects immediately (documented policy)
            try {
                $this->applyGrnReceipt($grn);
            } catch (\Throwable $e) {
                $grn->items()->delete();
                $grn->delete();
                return back()->withInput()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', $workflow ? "GRN {$grn->grn_number} submitted for approval." : 'GRN created successfully.');
    }

    /**
     * Finalize a GRN — applies LPO received quantities and status updates.
     * Requires approved workflow request when configured; idempotent via
     * row lock + status recheck. Over-receipt is rejected.
     */
    public function grnReceive(Grn $grn)
    {
        $user = auth()->user();
        abort_if($grn->created_by === $user->id, 403, 'You cannot finalize a GRN you created.');

        DB::transaction(function () use ($grn, $user) {
            $locked = Grn::lockForUpdate()->findOrFail($grn->id);
            abort_if($locked->status === 'received' || $locked->status === 'discrepant', 403, 'This GRN is already finalized.');
            abort_if($locked->status === 'cancelled', 403, 'Cancelled GRNs cannot be finalized.');

            $req = \App\Models\ApprovalRequest::where('module', 'grn')
                ->where('module_id', $locked->id)->latest('id')->first();
            if ($req) {
                abort_if($req->status === 'rejected', 403, 'This GRN was rejected.');
                abort_if($req->status !== 'approved', 403, 'This GRN is still awaiting approval.');
            }

            $this->applyGrnReceipt($locked);
            $locked->update(['status' => 'received', 'received_by' => $user->id]);

            \App\Models\AuditLog::log('execute', 'grn', [
                'module_action' => 'grn_received',
                'new_values' => ['grn' => $locked->grn_number, 'lpo' => $locked->lpo_id],
            ]);
        });

        return back()->with('success', "GRN {$grn->grn_number} finalized.");
    }

    /**
     * Apply receipt effects for a finalized GRN:
     * 1. Reject over-receipt + update LPO item quantities (locked)
     * 2. Post stock movements when the GRN targets a warehouse — each item
     *    must resolve a product (direct product_id or via lpo_item) or the
     *    entire receipt fails.
     * 3. Update LPO received status.
     * All changes share the caller's transaction — failure rolls back fully.
     */
    private function applyGrnReceipt(Grn $grn): void
    {
        $inventory = app(\App\Services\InventoryService::class);

        foreach ($grn->items as $item) {
            if (!$item->lpo_item_id) {
                continue;
            }
            $lpoItem = LpoItem::lockForUpdate()->find($item->lpo_item_id);
            if (!$lpoItem) {
                continue;
            }
            // Reject over-receipt — received cannot exceed ordered
            if ($lpoItem->quantity_received + $item->quantity_received > $lpoItem->quantity_ordered + 0.0001) {
                throw new \RuntimeException("Over-receipt on '{$item->description}': ordered {$lpoItem->quantity_ordered}, would reach " . ($lpoItem->quantity_received + $item->quantity_received) . ".");
            }
            $lpoItem->increment('quantity_received', $item->quantity_received);
        }

        // Stock posting — only when a receiving warehouse is set
        if ($grn->warehouse_id) {
            foreach ($grn->items as $item) {
                $productId = $item->product_id
                    ?? ($item->lpo_item_id ? LpoItem::where('id', $item->lpo_item_id)->value('product_id') : null);
                if (!$productId) {
                    throw new \RuntimeException("GRN item '{$item->description}' has no product mapping — cannot post stock. Set a product on the LPO item or GRN item.");
                }
                if ($item->quantity_received > 0) {
                    // addStock is atomic (lock + movement + balance + product sync)
                    $inventory->addStock(
                        $productId, $grn->warehouse_id, $item->quantity_received,
                        "GRN {$grn->grn_number}", auth()->id()
                    );
                }
            }
        }

        // Update LPO status
        if ($grn->lpo_id) {
            $lpo = Lpo::lockForUpdate()->with('items')->find($grn->lpo_id);
            if ($lpo) {
                $allReceived = $lpo->items->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
                $anyReceived = $lpo->items->some(fn($i) => $i->quantity_received > 0);
                if ($allReceived) $lpo->update(['status' => 'received']);
                elseif ($anyReceived) $lpo->update(['status' => 'partially_received']);
            }
        }
    }

    public function grnShow(Grn $grn)
    {
        $grn->load(['lpo', 'supplier', 'items.lpoItem']);
        return view('admin.business-flow.grns.show', compact('grn'));
    }

    public function grnDestroy(Grn $grn)
    {
        // Finalized receipts cannot be deleted — that would orphan the LPO
        // quantity updates. Corrections require a new GRN or void workflow.
        abort_if(in_array($grn->status, ['received', 'discrepant']), 403, 'Finalized GRNs cannot be deleted. Create a corrective entry instead.');
        $grn->items()->delete();
        $grn->delete();
        return back()->with('success', 'GRN deleted.');
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
        return back()->with('success', 'Delivery Note created.');
    }

    public function deliveryNoteShow(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['lpo', 'supplier', 'grn', 'createdBy']);
        $company = $deliveryNote->company ?? auth()->user()->company ?? null;
        return view('admin.business-flow.delivery-notes.show', compact('deliveryNote', 'company'));
    }

    public function deliveryNoteDestroy(DeliveryNote $deliveryNote)
    {
        $deliveryNote->delete();
        return back()->with('success', 'Delivery Note deleted.');
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
        return back()->with('success', 'Vendor Invoice created.');
    }

    public function vendorInvoiceShow(VendorInvoice $invoice)
    {
        $invoice->load(['lpo', 'supplier', 'project', 'payments.createdBy']);
        return view('admin.business-flow.vendor-invoices.show', compact('invoice'));
    }

    public function vendorInvoicePdf(VendorInvoice $invoice)
    {
        $invoice->load(['lpo', 'supplier', 'project', 'payments']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.vendor-invoice', ['invoice' => $invoice, 'company' => $company]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'invoice-' . $invoice->vendor_invoice_number . '.pdf';
        return request()->boolean('inline') ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function vendorInvoiceDestroy(VendorInvoice $invoice)
    {
        $invoice->payments()->delete();
        $invoice->delete();
        return back()->with('success', 'Vendor Invoice deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  10. VENDOR PAYMENTS
    //═══════════════════════════════════════════════════════
    public function vendorPaymentIndex(Request $request)
    {
        $query = VendorPayment::with(['vendorInvoice', 'supplier', 'createdBy']);

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('payment_number', 'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%")
                  ->orWhereHas('supplier', function ($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->get('method'));
        }

        $payments = $query->latest()->paginate(20)->withQueryString();
        $totalPaid = VendorPayment::sum('amount');
        $approvalMap = \App\Models\ApprovalRequest::where('module', 'vendor_payment')
            ->whereIn('module_id', $payments->pluck('id'))->get()->keyBy('module_id');

        return view('admin.business-flow.vendor-payments.index', compact('payments', 'totalPaid', 'approvalMap'));
    }

    public function vendorPaymentStore(Request $request)
    {
        $data = $request->validate([
            'vendor_invoice_id' => 'required|exists:vendor_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch_id' => ['nullable', \Illuminate\Validation\Rule::exists('branches','id')->where('company_id', auth()->user()->company_id)],
        ]);

        $invoice = VendorInvoice::findOrFail($data['vendor_invoice_id']);
        $data['payment_number'] = 'VPAY-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['supplier_id'] = $invoice->supplier_id;
        $data['created_by'] = auth()->id();

        // When a vendor_payment approval workflow is configured, the payment
        // is created PENDING and its financial effects only apply on execute.
        $workflow = \App\Services\ApprovalService::findWorkflow('vendor_payment', $invoice->company_id ?? auth()->user()->company_id);
        $data['status'] = $workflow ? 'pending' : 'completed';

        $payment = null;
        if (!$workflow) {
            DB::transaction(function () use ($data, $invoice, &$payment) {
                $payment = VendorPayment::create($data);
                $this->executeVendorPayment($payment, $invoice);
            });
            return redirect()->back()->with('success', 'Payment recorded successfully.');
        }

        $payment = VendorPayment::create($data);
        \App\Services\ApprovalService::submit(
            'vendor_payment', $payment, "Vendor Payment {$payment->payment_number}",
            $payment->amount, auth()->user()
        );

        return redirect()->back()->with('success', "Payment {$payment->payment_number} submitted for approval.");
    }

    /**
     * Apply the financial effects of a vendor payment — invoice balance,
     * ledger posting, project cost. Called on creation (no workflow) or
     * on explicit execution after approval.
     */
    private function executeVendorPayment(VendorPayment $payment, VendorInvoice $invoice): void
    {
        $invoice->increment('amount_paid', $payment->amount);
        $invoice->decrement('balance', $payment->amount);
        $invoice->update(['status' => $invoice->balance <= 0 ? 'paid' : 'partially_paid']);

        try {
            $this->ledger->postVendorPayment($payment);
        } catch (\Throwable $e) {
            \Log::error('Ledger posting failed for vendor payment: ' . $e->getMessage());
        }

        if ($invoice->project_id) {
            $invoice->project?->increment('actual_cost', $payment->amount);
        }
    }

    /**
     * Execute an approved vendor payment — applies financial effects.
     * Rejected payments can never be executed.
     */
    public function vendorPaymentExecute(VendorPayment $vendorPayment)
    {
        $user = auth()->user();

        abort_if($vendorPayment->status !== 'pending', 403, 'This payment is not awaiting execution.');
        abort_if($vendorPayment->created_by === $user->id, 403, 'You cannot execute a payment you created.');

        // The linked approval request must be fully approved
        $req = \App\Models\ApprovalRequest::where('module', 'vendor_payment')
            ->where('module_id', $vendorPayment->id)->latest()->first();
        abort_if(!$req, 403, 'This payment has no approval request.');
        abort_if($req->status === 'rejected', 403, 'This payment was rejected and cannot be executed.');
        abort_if($req->status !== 'approved', 403, 'This payment is still awaiting approval.');

        DB::transaction(function () use ($vendorPayment) {
            $vendorPayment->update(['status' => 'completed']);
            $this->executeVendorPayment($vendorPayment, $vendorPayment->vendorInvoice);

            \App\Models\AuditLog::log('execute', 'vendor_payments', [
                'module_action' => 'payment_executed',
                'new_values' => ['payment' => $vendorPayment->payment_number, 'amount' => $vendorPayment->amount],
            ]);
        });

        return redirect()->back()->with('success', "Payment {$vendorPayment->payment_number} executed.");
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
            'branch_id' => ['nullable', \Illuminate\Validation\Rule::exists('branches','id')->where('company_id', auth()->user()->company_id)],
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

        $expense = OfficeExpense::create($data);

        // Route to the approval engine when a workflow is configured
        if ($expense->status === 'pending') {
            \App\Services\ApprovalService::submit(
                'office_expense', $expense, "Office Expense {$expense->expense_number}",
                $expense->amount, auth()->user()
            );
        }

        return back()->with('success', 'Office Expense submitted.');
    }

    public function officeExpenseApprove(OfficeExpense $expense)
    {
        $user = auth()->user();

        // Segregation of duties — requester can never approve their own expense
        if ($expense->requested_by === $user->id || $expense->created_by === $user->id) {
            \App\Models\AuditLog::log('deny', 'approvals', [
                'module_action' => 'self_approval_attempt',
                'new_values' => ['expense' => $expense->expense_number, 'attempted' => 'approve'],
            ]);
            abort(403, 'You cannot approve an expense you submitted.');
        }

        abort_if($expense->status !== 'pending', 403, 'This expense is no longer pending.');

        // Workflow-linked expenses are decided through the approval engine
        $req = \App\Models\ApprovalRequest::where('module', 'office_expense')
            ->where('module_id', $expense->id)->latest()->first();
        if ($req) {
            $req = \App\Services\ApprovalService::approve($req, $user);
            if ($req->status !== 'approved') {
                return back()->with('success', "Approved — advanced to approval level {$req->current_level}.");
            }
            // Engine already synced expense status + project cost
            return back()->with('success', 'Office Expense approved.');
        }

        // Legacy path — no configured workflow: require an admin approver
        abort_unless($user->isAdmin(), 403, 'You are not authorized to approve expenses.');
        $expense->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_level' => $expense->approval_level + 1,
        ]);

        if ($expense->project_id) {
            $expense->project->increment('actual_cost', $expense->amount);
        }

        \App\Models\AuditLog::log('approve', 'office_expense', [
            'module_action' => 'expense_approved_legacy',
            'new_values' => ['expense' => $expense->expense_number, 'amount' => $expense->amount],
        ]);

        return redirect()->back()->with('success', 'Office Expense approved.');
    }

    public function officeExpenseReject(OfficeExpense $expense)
    {
        $user = auth()->user();
        if ($expense->requested_by === $user->id || $expense->created_by === $user->id) {
            abort(403, 'You cannot reject an expense you submitted.');
        }
        abort_if($expense->status !== 'pending', 403, 'This expense is no longer pending.');

        $req = \App\Models\ApprovalRequest::where('module', 'office_expense')
            ->where('module_id', $expense->id)->latest()->first();
        if ($req) {
            \App\Services\ApprovalService::reject($req, $user, request('reason', 'Rejected by approver'));
            return back()->with('success', 'Office Expense rejected.');
        }

        abort_unless($user->isAdmin(), 403, 'You are not authorized to reject expenses.');
        $expense->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Office Expense rejected.');
    }

    public function officeExpenseDestroy(OfficeExpense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Office Expense deleted.');
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

        return back()->with('success', 'Client Receipt created.');
    }

    public function clientReceiptDestroy(ClientReceipt $receipt)
    {
        if ($receipt->project_id) {
            $receipt->project->decrement('actual_revenue', $receipt->amount);
        }
        $receipt->delete();
        return back()->with('success', 'Client Receipt deleted.');
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

    public function quotationPdf(Quotation $quotation)
    {
        $quotation->load(['lead', 'items']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'company'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'quotation-' . $quotation->quotation_number . '.pdf';
        return request()->boolean('inline') ? $pdf->stream($filename) : $pdf->download($filename);
    }
}
