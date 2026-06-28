<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Transfer;
use App\Models\Plan;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskReply;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\LoginHistory;
use App\Models\BankTransferPayment;
use App\Models\AddOn;
use App\Models\UserActiveModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceReturn;
use App\Models\SalesProposal;
use App\Models\ChMessage;
use App\Models\User;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ErpController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            // Admin users have full access
            if (auth()->user()->isAdmin()) {
                return $next($request);
            }
            // Non-admin users: allow access, individual permission checks
            // can be added per-method if needed
            return $next($request);
        });
    }

    // ─── Warehouses ───
    public function warehouseIndex()
    {
        $warehouses = Warehouse::latest()->paginate(10);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function warehouseStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        Warehouse::create($data);
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function warehouseUpdate(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $warehouse->update($data);
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function warehouseDestroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse deleted.');
    }

    // ─── Transfers ───
    public function transferIndex()
    {
        $transfers = Transfer::with(['fromWarehouse', 'toWarehouse'])->latest()->paginate(10);
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.transfers.index', compact('transfers', 'warehouses'));
    }

    public function transferStore(Request $request)
    {
        $data = $request->validate([
            'from_warehouse' => 'required|exists:warehouses,id',
            'to_warehouse' => 'required|exists:warehouses,id|different:from_warehouse',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'date' => 'nullable|date',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        Transfer::create($data);
        return redirect()->route('admin.transfers.index')->with('success', 'Transfer created successfully.');
    }

    public function transferDestroy(Transfer $transfer)
    {
        $transfer->delete();
        return redirect()->route('admin.transfers.index')->with('success', 'Transfer deleted.');
    }

    // ─── Plans ───
    public function planIndex()
    {
        $plans = Plan::latest()->paginate(10);
        return view('admin.plans.index', compact('plans'));
    }

    public function planStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'number_of_users' => 'required|integer|min:1',
            'package_price_monthly' => 'required|numeric|min:0',
            'package_price_yearly' => 'required|numeric|min:0',
            'storage_limit' => 'nullable|integer|min:0',
            'free_plan' => 'boolean',
            'trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);
        $data['created_by'] = auth()->id();
        $data['free_plan'] = $request->boolean('free_plan');
        $data['trial'] = $request->boolean('trial');
        $data['status'] = $request->boolean('status', true);
        Plan::create($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function planUpdate(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'number_of_users' => 'required|integer|min:1',
            'package_price_monthly' => 'required|numeric|min:0',
            'package_price_yearly' => 'required|numeric|min:0',
            'storage_limit' => 'nullable|integer|min:0',
            'free_plan' => 'boolean',
            'trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);
        $data['free_plan'] = $request->boolean('free_plan');
        $data['trial'] = $request->boolean('trial');
        $data['status'] = $request->boolean('status', true);
        $plan->update($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function planDestroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted.');
    }

    // ─── Orders ───
    public function orderIndex()
    {
        $orders = Order::with('plan')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    // ─── Coupons ───
    public function couponIndex()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function couponStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,flat,fixed',
            'limit' => 'nullable|integer|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_spend' => 'nullable|numeric|min:0',
            'limit_per_user' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'status' => 'boolean',
        ]);
        $data['created_by'] = auth()->id();
        $data['status'] = $request->boolean('status', true);
        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function couponDestroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    // ─── Helpdesk Categories ───
    public function helpdeskCategoryIndex()
    {
        $categories = HelpdeskCategory::withCount('tickets')->latest()->paginate(10);
        return view('admin.helpdesk.categories', compact('categories'));
    }

    public function helpdeskCategoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        HelpdeskCategory::create($data);
        return redirect()->route('admin.helpdesk-categories.index')->with('success', 'Category created.');
    }

    public function helpdeskCategoryDestroy(HelpdeskCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.helpdesk-categories.index')->with('success', 'Category deleted.');
    }

    // ─── Helpdesk Tickets ───
    public function helpdeskTicketIndex()
    {
        $tickets = HelpdeskTicket::with(['category', 'creator'])->latest()->paginate(10);
        $categories = HelpdeskCategory::where('is_active', true)->get();
        return view('admin.helpdesk.tickets', compact('tickets', 'categories'));
    }

    public function helpdeskTicketShow(HelpdeskTicket $ticket)
    {
        $ticket->load(['category', 'creator', 'replies.creator']);
        return view('admin.helpdesk.ticket-show', compact('ticket'));
    }

    public function helpdeskTicketStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:helpdesk_categories,id',
        ]);
        $data['ticket_id'] = 'TKT-' . strtoupper(Str::random(8));
        $data['created_by'] = auth()->id();
        HelpdeskTicket::create($data);
        return redirect()->route('admin.helpdesk-tickets.index')->with('success', 'Ticket created.');
    }

    public function helpdeskReplyStore(Request $request, HelpdeskTicket $ticket)
    {
        $data = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'boolean',
        ]);
        $data['ticket_id'] = $ticket->id;
        $data['created_by'] = auth()->id();
        $data['is_internal'] = $request->boolean('is_internal');
        HelpdeskReply::create($data);
        return redirect()->route('admin.helpdesk-tickets.show', $ticket)->with('success', 'Reply added.');
    }

    public function helpdeskTicketUpdateStatus(Request $request, HelpdeskTicket $ticket)
    {
        $data = $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update([
            'status' => $data['status'],
            'resolved_at' => $data['status'] === 'resolved' ? now() : null,
        ]);
        return redirect()->back()->with('success', 'Ticket status updated.');
    }

    // ─── Purchase Invoices ───
    public function purchaseInvoiceIndex()
    {
        $invoices = PurchaseInvoice::with(['vendor', 'warehouse'])->latest()->paginate(10);
        $vendors = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.purchase-index', compact('invoices', 'vendors', 'warehouses'));
    }

    public function purchaseInvoiceStore(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'vendor_id' => 'required|exists:users,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['balance_amount'] = $data['total_amount'];
        PurchaseInvoice::create($data);
        return redirect()->route('admin.purchase-invoices.index')->with('success', 'Purchase invoice created.');
    }

    public function purchaseInvoiceShow(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['vendor', 'warehouse', 'items']);
        return view('admin.invoices.purchase-show', compact('purchaseInvoice'));
    }

    public function purchaseInvoicePost(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->update(['status' => 'posted']);
        return redirect()->back()->with('success', 'Invoice posted.');
    }

    public function purchaseInvoiceDestroy(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->delete();
        return redirect()->route('admin.purchase-invoices.index')->with('success', 'Invoice deleted.');
    }

    // ─── Purchase Returns ───
    public function purchaseReturnIndex()
    {
        $returns = PurchaseReturn::with(['vendor', 'originalInvoice'])->latest()->paginate(10);
        return view('admin.returns.purchase-index', compact('returns'));
    }

    public function purchaseReturnShow(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['vendor', 'items', 'originalInvoice']);
        return view('admin.returns.purchase-show', compact('purchaseReturn'));
    }

    public function purchaseReturnDestroy(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->delete();
        return redirect()->route('admin.purchase-returns.index')->with('success', 'Return deleted.');
    }

    // ─── Sales Invoices ───
    public function salesInvoiceIndex()
    {
        $invoices = SalesInvoice::with(['customer', 'warehouse'])->latest()->paginate(10);
        $customers = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.sales-index', compact('invoices', 'customers', 'warehouses'));
    }

    public function salesInvoiceStore(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'customer_id' => 'required|exists:users,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'type' => 'required|in:product,service',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_percentage' => 'nullable|numeric|min:0',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['balance_amount'] = $data['total_amount'];
        $invoice = SalesInvoice::create($data);
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
                $lineTax = $lineTotal * (($item['tax_percentage'] ?? 0) / 100);
                $invoice->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_percentage' => $item['tax_percentage'] ?? 0,
                    'tax_amount' => $lineTax,
                    'total_amount' => $lineTotal + $lineTax,
                ]);
            }
        }
        return redirect()->route('admin.sales-invoices.index')->with('success', 'Sales invoice created.');
    }

    public function salesInvoiceShow(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'warehouse', 'items', 'creator']);
        return view('admin.invoices.sales-show', compact('salesInvoice'));
    }

    public function salesInvoicePost(SalesInvoice $salesInvoice)
    {
        $salesInvoice->update(['status' => 'posted']);
        return redirect()->back()->with('success', 'Invoice posted.');
    }

    public function salesInvoiceDestroy(SalesInvoice $salesInvoice)
    {
        $salesInvoice->delete();
        return redirect()->route('admin.sales-invoices.index')->with('success', 'Invoice deleted.');
    }

    public function salesInvoicePdf(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'warehouse', 'items']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $salesInvoice, 'company' => $company]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('invoice-' . $salesInvoice->invoice_number . '.pdf');
    }

    // ─── Sales Returns ───
    public function salesReturnIndex()
    {
        $returns = SalesInvoiceReturn::with(['customer', 'originalInvoice'])->latest()->paginate(10);
        return view('admin.returns.sales-index', compact('returns'));
    }

    public function salesReturnShow(SalesInvoiceReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'items', 'originalInvoice']);
        return view('admin.returns.sales-show', compact('salesReturn'));
    }

    public function salesReturnDestroy(SalesInvoiceReturn $salesReturn)
    {
        $salesReturn->delete();
        return redirect()->route('admin.sales-returns.index')->with('success', 'Return deleted.');
    }

    // ─── Sales Dashboard ───
    public function salesDashboard()
    {
        $stats = [
            'totalProposals' => SalesProposal::count(),
            'totalInvoices' => SalesInvoice::count(),
            'totalRevenue' => SalesInvoice::where('status', 'paid')->sum('total_amount'),
            'totalOutstanding' => SalesInvoice::whereIn('status', ['posted', 'partial', 'overdue'])->sum('balance_amount'),
        ];
        $recentProposals = SalesProposal::with(['customer'])->latest()->limit(5)->get();
        $recentInvoices = SalesInvoice::with(['customer'])->latest()->limit(5)->get();
        return view('admin.sales.dashboard', compact('stats', 'recentProposals', 'recentInvoices'));
    }

    // ─── Sales Proposals ───
    public function salesProposalIndex()
    {
        $proposals = SalesProposal::with(['customer'])->latest()->paginate(10);
        $customers = User::where('role', 'user')->get();
        $stats = [
            'total' => SalesProposal::count(),
            'draft' => SalesProposal::where('status', 'draft')->count(),
            'sent' => SalesProposal::where('status', 'sent')->count(),
            'accepted' => SalesProposal::where('status', 'accepted')->count(),
        ];
        return view('admin.proposals.index', compact('proposals', 'customers', 'stats'));
    }

    public function salesProposalStore(Request $request)
    {
        $data = $request->validate([
            'proposal_number' => 'required|string|max:255',
            'proposal_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:proposal_date',
            'customer_id' => 'required|exists:users,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_percentage' => 'nullable|numeric|min:0',
        ]);
        $data['creator_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $proposal = SalesProposal::create($data);
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
                $lineTax = $lineTotal * (($item['tax_percentage'] ?? 0) / 100);
                $proposal->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_percentage' => $item['tax_percentage'] ?? 0,
                    'tax_amount' => $lineTax,
                    'total_amount' => $lineTotal + $lineTax,
                ]);
            }
        }
        return redirect()->route('admin.sales-proposals.index')->with('success', 'Quotation created.');
    }

    public function salesProposalConvert(SalesProposal $salesProposal)
    {
        if ($salesProposal->converted_to_invoice) {
            return redirect()->back()->with('error', 'This quotation has already been converted.');
        }
        $invoice = SalesInvoice::create([
            'invoice_number' => 'INV-S-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            'invoice_date' => now(),
            'due_date' => $salesProposal->due_date,
            'customer_id' => $salesProposal->customer_id,
            'subtotal' => $salesProposal->subtotal,
            'tax_amount' => $salesProposal->tax_amount ?? 0,
            'discount_amount' => $salesProposal->discount_amount ?? 0,
            'total_amount' => $salesProposal->total_amount,
            'paid_amount' => 0,
            'balance_amount' => $salesProposal->total_amount,
            'status' => 'draft',
            'type' => 'service',
            'payment_terms' => $salesProposal->payment_terms,
            'notes' => $salesProposal->notes,
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);
        foreach ($salesProposal->items as $item) {
            $invoice->items()->create([
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_amount' => $item->discount_amount,
                'discount_percentage' => $item->discount_percentage,
                'tax_percentage' => $item->tax_percentage,
                'tax_amount' => $item->tax_amount,
                'total_amount' => $item->total_amount,
            ]);
        }
        $salesProposal->update(['converted_to_invoice' => true, 'status' => 'accepted']);
        return redirect()->route('admin.sales-invoices.show', $invoice)->with('success', 'Quotation converted to invoice.');
    }

    public function salesProposalShow(SalesProposal $salesProposal)
    {
        $salesProposal->load(['customer', 'items']);
        return view('admin.proposals.show', compact('salesProposal'));
    }

    public function salesProposalStatus(Request $request, SalesProposal $salesProposal)
    {
        $data = $request->validate(['status' => 'required|in:draft,sent,accepted,rejected']);
        $salesProposal->update(['status' => $data['status']]);
        return redirect()->back()->with('success', 'Proposal status updated.');
    }

    public function salesProposalDestroy(SalesProposal $salesProposal)
    {
        $salesProposal->delete();
        return redirect()->route('admin.sales-proposals.index')->with('success', 'Proposal deleted.');
    }

    // ─── Email Templates ───
    public function emailTemplateIndex()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('admin.email-templates.index', compact('templates'));
    }

    public function emailTemplateUpdate(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'from' => 'nullable|email',
            'subject' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $emailTemplate->update($data);
        return redirect()->back()->with('success', 'Template updated.');
    }

    // ─── Settings ───
    public function settingsIndex()
    {
        $settings = Setting::all();
        return view('admin.settings-full', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            Setting::set($key, $value);
        }
        return redirect()->back()->with('success', 'Settings updated.');
    }

    // ─── Login History ───
    public function loginHistory()
    {
        $histories = LoginHistory::with('user')->latest()->paginate(15);
        return view('admin.login-history', compact('histories'));
    }

    // ─── Bank Transfers ───
    public function bankTransferIndex()
    {
        $transfers = BankTransferPayment::with('user')->latest()->paginate(10);
        return view('admin.bank-transfers.index', compact('transfers'));
    }

    public function bankTransferUpdate(Request $request, BankTransferPayment $bankTransfer)
    {
        $data = $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $bankTransfer->update($data);
        return redirect()->back()->with('success', 'Payment status updated.');
    }

    // ─── Add-ons / Modules ───
    public function addOnIndex()
    {
        $addons = AddOn::latest()->get();
        $activeModules = UserActiveModule::where('user_id', auth()->id())->pluck('module')->toArray();
        return view('admin.addons.index', compact('addons', 'activeModules'));
    }

    public function addOnToggle(AddOn $addOn)
    {
        $addOn->update(['is_enable' => !$addOn->is_enable]);
        return redirect()->back()->with('success', 'Add-on status updated.');
    }

    // ─── Messenger ───
    public function messengerIndex()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin.messenger.index', compact('users'));
    }

    // ─── Media Library ───
    public function mediaIndex()
    {
        return view('admin.media.index');
    }

    // ─── Warehouse Edit ───
    public function warehouseEdit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    // ─── Plan Edit ───
    public function planEdit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    // ─── Coupon Edit ───
    public function couponEdit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function couponUpdate(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,flat,fixed',
            'limit' => 'nullable|integer|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_spend' => 'nullable|numeric|min:0',
            'limit_per_user' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $coupon->update($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    // ─── Sales Invoice Create/Edit ───
    public function salesInvoiceCreate()
    {
        $customers = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.sales-create', compact('customers', 'warehouses'));
    }

    public function salesInvoiceEdit(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['items']);
        $customers = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.sales-edit', compact('salesInvoice', 'customers', 'warehouses'));
    }

    public function salesInvoiceUpdate(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'customer_id' => 'required|exists:users,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'type' => 'required|in:product,service',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $salesInvoice->update($data);
        return redirect()->route('admin.sales-invoices.index')->with('success', 'Sales invoice updated.');
    }

    // ─── Purchase Invoice Create/Edit ───
    public function purchaseInvoiceCreate()
    {
        $vendors = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.purchase-create', compact('vendors', 'warehouses'));
    }

    public function purchaseInvoiceEdit(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['items']);
        $vendors = User::where('role', 'user')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.invoices.purchase-edit', compact('purchaseInvoice', 'vendors', 'warehouses'));
    }

    public function purchaseInvoiceUpdate(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'vendor_id' => 'required|exists:users,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $purchaseInvoice->update($data);
        return redirect()->route('admin.purchase-invoices.index')->with('success', 'Purchase invoice updated.');
    }

    // ─── Sales Proposal Create/Edit ───
    public function salesProposalCreate()
    {
        $customers = User::where('role', 'user')->get();
        return view('admin.proposals.create', compact('customers'));
    }

    public function salesProposalEdit(SalesProposal $salesProposal)
    {
        $salesProposal->load(['items']);
        $customers = User::where('role', 'user')->get();
        return view('admin.proposals.edit', compact('salesProposal', 'customers'));
    }

    public function salesProposalUpdate(Request $request, SalesProposal $salesProposal)
    {
        $data = $request->validate([
            'proposal_number' => 'required|string|max:255',
            'proposal_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:proposal_date',
            'customer_id' => 'required|exists:users,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_percentage' => 'nullable|numeric|min:0',
        ]);
        $salesProposal->update($data);
        $salesProposal->items()->delete();
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
                $lineTax = $lineTotal * (($item['tax_percentage'] ?? 0) / 100);
                $salesProposal->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_percentage' => $item['tax_percentage'] ?? 0,
                    'tax_amount' => $lineTax,
                    'total_amount' => $lineTotal + $lineTax,
                ]);
            }
        }
        return redirect()->route('admin.sales-proposals.index')->with('success', 'Quotation updated.');
    }

    // ─── Notification Templates ───
    public function notificationTemplateIndex()
    {
        $templates = NotificationTemplate::latest()->paginate(10);
        return view('admin.notification-templates.index', compact('templates'));
    }

    public function notificationTemplateEdit(NotificationTemplate $notificationTemplate)
    {
        return view('admin.notification-templates.edit', compact('notificationTemplate'));
    }

    public function notificationTemplateUpdate(Request $request, NotificationTemplate $notificationTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $notificationTemplate->update($data);
        return redirect()->route('admin.notification-templates.index')->with('success', 'Notification template updated.');
    }

    // ─── Profile ───
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($data);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    // ─── Users Management ───
    public function userIndex()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users-index', compact('users'));
    }

    public function userCreate()
    {
        return view('admin.users-create');
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('admin.users-index')->with('success', 'User created successfully.');
    }

    public function userEdit(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.users-index')->with('success', 'User updated successfully.');
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'You cannot delete yourself.']);
        }
        $user->delete();
        return redirect()->route('admin.users-index')->with('success', 'User deleted.');
    }
}
