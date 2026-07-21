<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceReturn;
use App\Models\SalesProposal;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContact;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectBug;
use App\Models\ProjectBudget;
use App\Models\Timesheet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Transfer;
use App\Models\PosSale;
use App\Models\Order;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskCategory;
use App\Models\Appointment;
use App\Models\Visitor;
use App\Models\FrontDesk;
use App\Models\Vehicle;
use App\Models\FuelLog;
use App\Models\VehicleMaintenance;
use App\Models\FixedAsset;
use App\Models\DepreciationRecord;
use App\Models\Tender;
use App\Models\Quotation;
use App\Models\Lpo;
use App\Models\Grn;
use App\Models\DeliveryNote;
use App\Models\VendorInvoice;
use App\Models\VendorPayment;
use App\Models\Bill;
use App\Models\ClientReceipt;
use App\Models\BankAccount;
use App\Models\CashAccount;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Contract;
use App\Models\EmployeeBonus;
use App\Models\OfficeExpense;
use App\Models\CostCenter;
use App\Models\Budget;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) {
                return $next($request);
            }
            if (auth()->user()->hasPermission('view-reports') || auth()->user()->hasPermission('view-dashboard')) {
                return $next($request);
            }
            abort(403, 'You do not have permission to access reports.');
        });
    }

    private function money($n): string
    {
        return 'TZS ' . number_format($n ?? 0);
    }

    private function fmt($n): string
    {
        return number_format($n ?? 0);
    }

    // ═══════════════════════════════════════════════
    // REPORTS INDEX — categorized landing page
    // ═══════════════════════════════════════════════
    public function index()
    {
        $categories = [
            [
                'title' => 'Finance & Accounting',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'emerald',
                'reports' => [
                    ['label' => 'Profit & Loss Statement', 'route' => 'admin.reports.profit-loss', 'desc' => 'Revenue, expenses and net profit'],
                    ['label' => 'Revenue Report', 'route' => 'admin.reports.revenue', 'desc' => 'All revenue entries by period'],
                    ['label' => 'Expense Report', 'route' => 'admin.reports.expenses', 'desc' => 'All expenses by category and period'],
                    ['label' => 'Cash Flow Statement', 'route' => 'admin.reports.cash-flow', 'desc' => 'Cash inflows and outflows'],
                    ['label' => 'Balance Sheet', 'route' => 'admin.reports.balance-sheet', 'desc' => 'Assets, liabilities and equity'],
                    ['label' => 'Bank Accounts Summary', 'route' => 'admin.reports.bank-accounts', 'desc' => 'Bank balances and transactions'],
                    ['label' => 'Chart of Accounts', 'route' => 'admin.reports.chart-accounts', 'desc' => 'Account structure and balances'],
                    ['label' => 'Journal Entries Report', 'route' => 'admin.reports.journal-entries', 'desc' => 'All journal entries by period'],
                    ['label' => 'Bills & Payments', 'route' => 'admin.reports.bills', 'desc' => 'Outstanding and paid bills'],
                    ['label' => 'Client Receipts', 'route' => 'admin.reports.client-receipts', 'desc' => 'Receipts collected from clients'],
                    ['label' => 'Vendor Invoices & Payments', 'route' => 'admin.reports.vendor-invoices', 'desc' => 'Vendor invoices and payment status'],
                    ['label' => 'Cost Centers', 'route' => 'admin.reports.cost-centers', 'desc' => 'Cost allocation by center'],
                    ['label' => 'Office Expenses', 'route' => 'admin.reports.office-expenses', 'desc' => 'Office operational expenses'],
                    ['label' => 'Budget vs Actual', 'route' => 'admin.reports.budget', 'desc' => 'Budget planning vs actual spending'],
                ],
            ],
            [
                'title' => 'Sales & Revenue',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color' => 'sky',
                'reports' => [
                    ['label' => 'Sales Invoice Report', 'route' => 'admin.reports.sales-invoices', 'desc' => 'All sales invoices by status and period'],
                    ['label' => 'Sales Returns Report', 'route' => 'admin.reports.sales-returns', 'desc' => 'Returned sales and refund amounts'],
                    ['label' => 'Sales Proposals Report', 'route' => 'admin.reports.sales-proposals', 'desc' => 'Proposals by status (sent, accepted, rejected)'],
                    ['label' => 'Sales by Customer', 'route' => 'admin.reports.sales-by-customer', 'desc' => 'Sales grouped by customer'],
                    ['label' => 'Sales by Status', 'route' => 'admin.reports.sales-by-status', 'desc' => 'Invoice status breakdown'],
                    ['label' => 'Outstanding Receivables', 'route' => 'admin.reports.receivables', 'desc' => 'Outstanding customer balances'],
                    ['label' => 'Overdue Invoices', 'route' => 'admin.reports.overdue-invoices', 'desc' => 'Past due invoices and aging'],
                ],
            ],
            [
                'title' => 'Purchases & Procurement',
                'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                'color' => 'amber',
                'reports' => [
                    ['label' => 'Purchase Invoice Report', 'route' => 'admin.reports.purchase-invoices', 'desc' => 'All purchase invoices by status'],
                    ['label' => 'Purchase Returns Report', 'route' => 'admin.reports.purchase-returns', 'desc' => 'Returned purchases'],
                    ['label' => 'Tenders Report', 'route' => 'admin.reports.tenders', 'desc' => 'All tenders by status'],
                    ['label' => 'Quotations Report', 'route' => 'admin.reports.quotations', 'desc' => 'Quotations issued and status'],
                    ['label' => 'LPOs Report', 'route' => 'admin.reports.lpos', 'desc' => 'Local Purchase Orders'],
                    ['label' => 'GRNs Report', 'route' => 'admin.reports.grns', 'desc' => 'Goods Received Notes'],
                    ['label' => 'Delivery Notes Report', 'route' => 'admin.reports.delivery-notes', 'desc' => 'Delivery notes issued'],
                    ['label' => 'Outstanding Payables', 'route' => 'admin.reports.payables', 'desc' => 'Outstanding vendor balances'],
                ],
            ],
            [
                'title' => 'Inventory & Warehouses',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5',
                'color' => 'violet',
                'reports' => [
                    ['label' => 'Product List & Stock', 'route' => 'admin.reports.products', 'desc' => 'All products with stock levels'],
                    ['label' => 'Low Stock Report', 'route' => 'admin.reports.low-stock', 'desc' => 'Products below reorder level'],
                    ['label' => 'Stock Movements', 'route' => 'admin.reports.stock-movements', 'desc' => 'All stock in/out transactions'],
                    ['label' => 'Warehouse Report', 'route' => 'admin.reports.warehouses', 'desc' => 'Warehouse capacity and stock'],
                    ['label' => 'Transfers Report', 'route' => 'admin.reports.transfers', 'desc' => 'Inter-warehouse transfers'],
                    ['label' => 'Product Categories', 'route' => 'admin.reports.product-categories', 'desc' => 'Products grouped by category'],
                ],
            ],
            [
                'title' => 'HR & Payroll',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'color' => 'rose',
                'reports' => [
                    ['label' => 'Employee Directory', 'route' => 'admin.reports.employees', 'desc' => 'All employees with details'],
                    ['label' => 'Attendance Report', 'route' => 'admin.reports.attendance', 'desc' => 'Daily attendance summary'],
                    ['label' => 'Leave Report', 'route' => 'admin.reports.leaves', 'desc' => 'Leave requests by status'],
                    ['label' => 'Payroll Report', 'route' => 'admin.reports.payroll', 'desc' => 'Payroll by period'],
                    ['label' => 'Performance Reviews', 'route' => 'admin.reports.performance', 'desc' => 'Employee performance reviews'],
                    ['label' => 'Training Report', 'route' => 'admin.reports.training', 'desc' => 'Training programs and attendance'],
                    ['label' => 'Employee Bonuses', 'route' => 'admin.reports.bonuses', 'desc' => 'Bonuses given to employees'],
                    ['label' => 'Recruitment Report', 'route' => 'admin.reports.recruitment', 'desc' => 'Job postings and applications'],
                ],
            ],
            [
                'title' => 'CRM & Sales Pipeline',
                'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color' => 'sky',
                'reports' => [
                    ['label' => 'Leads Report', 'route' => 'admin.reports.leads', 'desc' => 'All leads by status and source'],
                    ['label' => 'Deals Report', 'route' => 'admin.reports.deals', 'desc' => 'Deals by stage and value'],
                    ['label' => 'Contacts Report', 'route' => 'admin.reports.contacts', 'desc' => 'All CRM contacts'],
                    ['label' => 'Sales Pipeline', 'route' => 'admin.reports.pipeline', 'desc' => 'Pipeline conversion rates'],
                    ['label' => 'Contracts Report', 'route' => 'admin.reports.contracts', 'desc' => 'Active and expired contracts'],
                ],
            ],
            [
                'title' => 'Projects',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'color' => 'emerald',
                'reports' => [
                    ['label' => 'Projects Overview', 'route' => 'admin.reports.projects', 'desc' => 'All projects by status'],
                    ['label' => 'Project Tasks', 'route' => 'admin.reports.project-tasks', 'desc' => 'Tasks across all projects'],
                    ['label' => 'Project Bugs', 'route' => 'admin.reports.project-bugs', 'desc' => 'Bug tracking report'],
                    ['label' => 'Project Budgets', 'route' => 'admin.reports.project-budgets', 'desc' => 'Budget vs actual per project'],
                    ['label' => 'Timesheets Report', 'route' => 'admin.reports.timesheets', 'desc' => 'Time logged by employee/project'],
                ],
            ],
            [
                'title' => 'POS & Retail',
                'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                'color' => 'amber',
                'reports' => [
                    ['label' => 'POS Sales Report', 'route' => 'admin.reports.pos-sales', 'desc' => 'Daily POS transactions'],
                    ['label' => 'POS Summary', 'route' => 'admin.reports.pos-summary', 'desc' => 'POS sales by day/month'],
                    ['label' => 'Orders Report', 'route' => 'admin.reports.orders', 'desc' => 'Customer orders and status'],
                ],
            ],
            [
                'title' => 'Bookings & Appointments',
                'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color' => 'violet',
                'reports' => [
                    ['label' => 'Appointments Report', 'route' => 'admin.reports.appointments', 'desc' => 'All appointments by date'],
                    ['label' => 'Visitors Log', 'route' => 'admin.reports.visitors', 'desc' => 'Visitor check-in/out records'],
                    ['label' => 'Front Desk Report', 'route' => 'admin.reports.front-desk', 'desc' => 'Front desk activities'],
                ],
            ],
            [
                'title' => 'Fleet & Assets',
                'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z',
                'color' => 'sky',
                'reports' => [
                    ['label' => 'Vehicles Report', 'route' => 'admin.reports.vehicles', 'desc' => 'All vehicles and status'],
                    ['label' => 'Fuel Logs Report', 'route' => 'admin.reports.fuel-logs', 'desc' => 'Fuel consumption by vehicle'],
                    ['label' => 'Vehicle Maintenance', 'route' => 'admin.reports.maintenance', 'desc' => 'Maintenance records and costs'],
                    ['label' => 'Fixed Assets Report', 'route' => 'admin.reports.fixed-assets', 'desc' => 'All fixed assets and values'],
                    ['label' => 'Depreciation Report', 'route' => 'admin.reports.depreciation', 'desc' => 'Asset depreciation schedule'],
                ],
            ],
            [
                'title' => 'Helpdesk & Support',
                'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'rose',
                'reports' => [
                    ['label' => 'Tickets Summary', 'route' => 'admin.reports.tickets', 'desc' => 'Tickets by status and priority'],
                    ['label' => 'Tickets by Category', 'route' => 'admin.reports.tickets-by-category', 'desc' => 'Tickets grouped by category'],
                ],
            ],
            [
                'title' => 'User Management',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'color' => 'emerald',
                'reports' => [
                    ['label' => 'User List', 'route' => 'admin.reports.users', 'desc' => 'All system users'],
                    ['label' => 'User Activity', 'route' => 'admin.reports.user-activity', 'desc' => 'Active vs inactive users'],
                ],
            ],
        ];

        return view('admin.reports.index', compact('categories'));
    }

    // ═══════════════════════════════════════════════
    // FINANCE REPORTS
    // ═══════════════════════════════════════════════
    public function profitLoss()
    {
        $totalRevenues = Revenue::sum('amount') ?? 0;
        $totalExpenses = Expense::sum('amount') ?? 0;
        $totalSales = SalesInvoice::sum('total_amount') ?? 0;
        $totalPurchases = PurchaseInvoice::sum('total_amount') ?? 0;
        $monthRevenues = Revenue::whereMonth('revenue_date', now()->month)->sum('amount') ?? 0;
        $monthExpenses = Expense::whereMonth('expense_date', now()->month)->sum('amount') ?? 0;
        $grossProfit = ($totalRevenues + $totalSales) - ($totalExpenses + $totalPurchases);
        $monthProfit = $monthRevenues - $monthExpenses;

        $recentRevenues = Revenue::latest()->take(10)->get();
        $recentExpenses = Expense::latest()->take(10)->get();

        return view('admin.reports.finance.profit-loss', compact(
            'totalRevenues', 'totalExpenses', 'totalSales', 'totalPurchases',
            'monthRevenues', 'monthExpenses', 'grossProfit', 'monthProfit',
            'recentRevenues', 'recentExpenses'
        ));
    }

    public function revenue()
    {
        $revenues = Revenue::with('account')->latest()->paginate(25);
        $total = Revenue::sum('amount') ?? 0;
        $monthTotal = Revenue::whereMonth('revenue_date', now()->month)->sum('amount') ?? 0;
        $byMonth = Revenue::selectRaw('YEAR(revenue_date) as year, MONTH(revenue_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')->orderByDesc('year')->orderByDesc('month')->take(12)->get();

        return view('admin.reports.finance.revenue', compact('revenues', 'total', 'monthTotal', 'byMonth'));
    }

    public function expenses()
    {
        $expenses = Expense::latest()->paginate(25);
        $total = Expense::sum('amount') ?? 0;
        $monthTotal = Expense::whereMonth('expense_date', now()->month)->sum('amount') ?? 0;
        $byMonth = Expense::selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')->orderByDesc('year')->orderByDesc('month')->take(12)->get();

        return view('admin.reports.finance.expenses', compact('expenses', 'total', 'monthTotal', 'byMonth'));
    }

    public function cashFlow()
    {
        $inflows = Revenue::selectRaw('DATE(revenue_date) as date, SUM(amount) as amount')
            ->groupBy('date')->orderByDesc('date')->take(30)->get();
        $outflows = Expense::selectRaw('DATE(expense_date) as date, SUM(amount) as amount')
            ->groupBy('date')->orderByDesc('date')->take(30)->get();
        $totalIn = Revenue::sum('amount') ?? 0;
        $totalOut = Expense::sum('amount') ?? 0;
        $netFlow = $totalIn - $totalOut;

        return view('admin.reports.finance.cash-flow', compact('inflows', 'outflows', 'totalIn', 'totalOut', 'netFlow'));
    }

    public function balanceSheet()
    {
        $assets = FixedAsset::sum('value') ?? 0;
        $bankBalance = BankAccount::sum('balance') ?? 0;
        $cashBalance = CashAccount::sum('balance') ?? 0;
        $receivables = SalesInvoice::sum('balance_amount') ?? 0;
        $payables = PurchaseInvoice::sum('balance_amount') ?? 0;
        $inventoryValue = Product::sum(\DB::raw('stock_quantity * cost_price')) ?? 0;
        $totalAssets = $assets + $bankBalance + $cashBalance + $receivables + $inventoryValue;
        $totalLiabilities = $payables;
        $equity = $totalAssets - $totalLiabilities;

        return view('admin.reports.finance.balance-sheet', compact(
            'assets', 'bankBalance', 'cashBalance', 'receivables', 'payables',
            'inventoryValue', 'totalAssets', 'totalLiabilities', 'equity'
        ));
    }

    public function bankAccounts()
    {
        $accounts = BankAccount::latest()->get();
        $totalBalance = $accounts->sum('balance');
        return view('admin.reports.finance.bank-accounts', compact('accounts', 'totalBalance'));
    }

    public function chartAccounts()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        return view('admin.reports.finance.chart-accounts', compact('accounts'));
    }

    public function journalEntries()
    {
        $entries = JournalEntry::with('lines.account')->latest()->paginate(25);
        return view('admin.reports.finance.journal-entries', compact('entries'));
    }

    public function bills()
    {
        $bills = Bill::latest()->paginate(25);
        $totalOutstanding = Bill::where('status', '!=', 'paid')->sum('amount') ?? 0;
        $totalPaid = Bill::where('status', 'paid')->sum('amount') ?? 0;
        return view('admin.reports.finance.bills', compact('bills', 'totalOutstanding', 'totalPaid'));
    }

    public function clientReceipts()
    {
        $receipts = ClientReceipt::latest()->paginate(25);
        $total = ClientReceipt::sum('amount') ?? 0;
        return view('admin.reports.finance.client-receipts', compact('receipts', 'total'));
    }

    public function vendorInvoices()
    {
        $invoices = VendorInvoice::latest()->paginate(25);
        $totalOutstanding = VendorInvoice::where('status', '!=', 'paid')->sum('amount') ?? 0;
        $totalPaid = VendorPayment::sum('amount') ?? 0;
        return view('admin.reports.finance.vendor-invoices', compact('invoices', 'totalOutstanding', 'totalPaid'));
    }

    public function costCenters()
    {
        $centers = CostCenter::with('allocations')->get();
        return view('admin.reports.finance.cost-centers', compact('centers'));
    }

    public function officeExpenses()
    {
        $expenses = OfficeExpense::latest()->paginate(25);
        $total = OfficeExpense::sum('amount') ?? 0;
        return view('admin.reports.finance.office-expenses', compact('expenses', 'total'));
    }

    public function budget()
    {
        $budgets = Budget::latest()->paginate(25);
        return view('admin.reports.finance.budget', compact('budgets'));
    }

    // ═══════════════════════════════════════════════
    // SALES REPORTS
    // ═══════════════════════════════════════════════
    public function salesInvoices()
    {
        $invoices = SalesInvoice::with('customer')->latest()->paginate(25);
        $total = SalesInvoice::sum('total_amount') ?? 0;
        $paid = SalesInvoice::sum('paid_amount') ?? 0;
        $outstanding = SalesInvoice::sum('balance_amount') ?? 0;
        return view('admin.reports.sales.invoices', compact('invoices', 'total', 'paid', 'outstanding'));
    }

    public function salesReturns()
    {
        $returns = SalesInvoiceReturn::latest()->paginate(25);
        $total = SalesInvoiceReturn::sum('total_amount') ?? 0;
        return view('admin.reports.sales.returns', compact('returns', 'total'));
    }

    public function salesProposals()
    {
        $proposals = SalesProposal::with('customer')->latest()->paginate(25);
        $byStatus = [
            'draft' => SalesProposal::where('status', 'draft')->count(),
            'sent' => SalesProposal::where('status', 'sent')->count(),
            'accepted' => SalesProposal::where('status', 'accepted')->count(),
            'rejected' => SalesProposal::where('status', 'rejected')->count(),
        ];
        return view('admin.reports.sales.proposals', compact('proposals', 'byStatus'));
    }

    public function salesByCustomer()
    {
        $customers = SalesInvoice::selectRaw('customer_id, COUNT(*) as invoice_count, SUM(total_amount) as total_amount, SUM(balance_amount) as outstanding')
            ->with('customer')->groupBy('customer_id')->orderByDesc('total_amount')->paginate(25);
        return view('admin.reports.sales.by-customer', compact('customers'));
    }

    public function salesByStatus()
    {
        $statuses = [
            'draft' => SalesInvoice::where('status', 'draft')->count(),
            'posted' => SalesInvoice::where('status', 'posted')->count(),
            'paid' => SalesInvoice::where('status', 'paid')->count(),
            'overdue' => SalesInvoice::where('status', 'overdue')->count(),
            'partial' => SalesInvoice::where('status', 'partial')->count(),
        ];
        $total = array_sum($statuses);
        return view('admin.reports.sales.by-status', compact('statuses', 'total'));
    }

    public function receivables()
    {
        $receivables = SalesInvoice::with('customer')->where('balance_amount', '>', 0)->latest()->paginate(25);
        $total = SalesInvoice::sum('balance_amount') ?? 0;
        return view('admin.reports.sales.receivables', compact('receivables', 'total'));
    }

    public function overdueInvoices()
    {
        $overdues = SalesInvoice::with('customer')->where('due_date', '<', now())->where('status', '!=', 'paid')->latest()->paginate(25);
        $total = $overdues->sum('total_amount');
        return view('admin.reports.sales.overdue', compact('overdues', 'total'));
    }

    // ═══════════════════════════════════════════════
    // PURCHASE & PROCUREMENT REPORTS
    // ═══════════════════════════════════════════════
    public function purchaseInvoices()
    {
        $invoices = PurchaseInvoice::with('vendor')->latest()->paginate(25);
        $total = PurchaseInvoice::sum('total_amount') ?? 0;
        $paid = PurchaseInvoice::sum('paid_amount') ?? 0;
        $outstanding = PurchaseInvoice::sum('balance_amount') ?? 0;
        return view('admin.reports.purchases.invoices', compact('invoices', 'total', 'paid', 'outstanding'));
    }

    public function purchaseReturns()
    {
        $returns = PurchaseReturn::latest()->paginate(25);
        $total = PurchaseReturn::sum('total_amount') ?? 0;
        return view('admin.reports.purchases.returns', compact('returns', 'total'));
    }

    public function tenders()
    {
        $tenders = Tender::latest()->paginate(25);
        $byStatus = [
            'open' => Tender::where('status', 'open')->count(),
            'closed' => Tender::where('status', 'closed')->count(),
            'awarded' => Tender::where('status', 'awarded')->count(),
            'cancelled' => Tender::where('status', 'cancelled')->count(),
        ];
        return view('admin.reports.purchases.tenders', compact('tenders', 'byStatus'));
    }

    public function quotations()
    {
        $quotations = Quotation::latest()->paginate(25);
        $total = Quotation::sum('total_amount') ?? 0;
        return view('admin.reports.purchases.quotations', compact('quotations', 'total'));
    }

    public function lpos()
    {
        $lpos = Lpo::latest()->paginate(25);
        return view('admin.reports.purchases.lpos', compact('lpos'));
    }

    public function grns()
    {
        $grns = Grn::latest()->paginate(25);
        return view('admin.reports.purchases.grns', compact('grns'));
    }

    public function deliveryNotes()
    {
        $notes = DeliveryNote::latest()->paginate(25);
        return view('admin.reports.purchases.delivery-notes', compact('notes'));
    }

    public function payables()
    {
        $payables = PurchaseInvoice::with('vendor')->where('balance_amount', '>', 0)->latest()->paginate(25);
        $total = PurchaseInvoice::sum('balance_amount') ?? 0;
        return view('admin.reports.purchases.payables', compact('payables', 'total'));
    }

    // ═══════════════════════════════════════════════
    // INVENTORY REPORTS
    // ═══════════════════════════════════════════════
    public function products()
    {
        $products = Product::with('category')->latest()->paginate(25);
        $totalValue = Product::sum(\DB::raw('stock_quantity * cost_price')) ?? 0;
        return view('admin.reports.inventory.products', compact('products', 'totalValue'));
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->paginate(25);
        return view('admin.reports.inventory.low-stock', compact('products'));
    }

    public function stockMovements()
    {
        $movements = StockMovement::with('product')->latest()->paginate(25);
        return view('admin.reports.inventory.stock-movements', compact('movements'));
    }

    public function warehouses()
    {
        $warehouses = Warehouse::withCount('products')->get();
        return view('admin.reports.inventory.warehouses', compact('warehouses'));
    }

    public function transfers()
    {
        $transfers = Transfer::latest()->paginate(25);
        return view('admin.reports.inventory.transfers', compact('transfers'));
    }

    public function productCategories()
    {
        $categories = ProductCategory::withCount('products')->get();
        return view('admin.reports.inventory.product-categories', compact('categories'));
    }

    // ═══════════════════════════════════════════════
    // HR & PAYROLL REPORTS
    // ═══════════════════════════════════════════════
    public function employees()
    {
        $employees = Employee::with('department')->latest()->paginate(25);
        $total = Employee::count();
        $active = Employee::where('status', 'active')->count();
        return view('admin.reports.hr.employees', compact('employees', 'total', 'active'));
    }

    public function attendance()
    {
        $records = Attendance::with('employee')->latest()->paginate(25);
        $present = Attendance::today()->where('status', 'present')->count();
        $late = Attendance::today()->where('status', 'late')->count();
        $absent = Attendance::today()->where('status', 'absent')->count();
        return view('admin.reports.hr.attendance', compact('records', 'present', 'late', 'absent'));
    }

    public function leaves()
    {
        $leaves = Leave::with('employee')->latest()->paginate(25);
        $pending = Leave::where('status', 'pending')->count();
        $approved = Leave::where('status', 'approved')->count();
        $rejected = Leave::where('status', 'rejected')->count();
        return view('admin.reports.hr.leaves', compact('leaves', 'pending', 'approved', 'rejected'));
    }

    public function payroll()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(25);
        $total = Payroll::sum('net_salary') ?? 0;
        return view('admin.reports.hr.payroll', compact('payrolls', 'total'));
    }

    public function performance()
    {
        $reviews = PerformanceReview::with('employee')->latest()->paginate(25);
        return view('admin.reports.hr.performance', compact('reviews'));
    }

    public function training()
    {
        $trainings = Training::latest()->paginate(25);
        return view('admin.reports.hr.training', compact('trainings'));
    }

    public function bonuses()
    {
        $bonuses = EmployeeBonus::with('employee')->latest()->paginate(25);
        $total = EmployeeBonus::sum('amount') ?? 0;
        return view('admin.reports.hr.bonuses', compact('bonuses', 'total'));
    }

    public function recruitment()
    {
        $postings = JobPosting::latest()->get();
        $applications = JobApplication::latest()->paginate(25);
        $openPositions = JobPosting::where('status', 'open')->count();
        $totalApps = JobApplication::count();
        return view('admin.reports.hr.recruitment', compact('postings', 'applications', 'openPositions', 'totalApps'));
    }

    // ═══════════════════════════════════════════════
    // CRM REPORTS
    // ═══════════════════════════════════════════════
    public function leads()
    {
        $leads = CrmLead::latest()->paginate(25);
        $byStatus = [
            'new' => CrmLead::where('status', 'new')->count(),
            'qualified' => CrmLead::where('status', 'qualified')->count(),
            'contacted' => CrmLead::where('status', 'contacted')->count(),
            'converted' => CrmLead::where('status', 'converted')->count(),
            'lost' => CrmLead::where('status', 'lost')->count(),
        ];
        return view('admin.reports.crm.leads', compact('leads', 'byStatus'));
    }

    public function deals()
    {
        $deals = CrmDeal::latest()->paginate(25);
        $totalValue = CrmDeal::sum('value') ?? 0;
        $openDeals = CrmDeal::where('status', 'open')->count();
        $wonDeals = CrmDeal::where('status', 'won')->count();
        return view('admin.reports.crm.deals', compact('deals', 'totalValue', 'openDeals', 'wonDeals'));
    }

    public function contacts()
    {
        $contacts = CrmContact::latest()->paginate(25);
        return view('admin.reports.crm.contacts', compact('contacts'));
    }

    public function pipeline()
    {
        $stages = CrmDeal::selectRaw('status, COUNT(*) as count, SUM(value) as total_value')
            ->groupBy('status')->get();
        $totalDeals = CrmDeal::count();
        $totalValue = CrmDeal::sum('value') ?? 0;
        return view('admin.reports.crm.pipeline', compact('stages', 'totalDeals', 'totalValue'));
    }

    public function contracts()
    {
        $contracts = Contract::latest()->paginate(25);
        return view('admin.reports.crm.contracts', compact('contracts'));
    }

    // ═══════════════════════════════════════════════
    // PROJECT REPORTS
    // ═══════════════════════════════════════════════
    public function projects()
    {
        $projects = Project::latest()->paginate(25);
        $byStatus = [
            'planning' => Project::where('status', 'planning')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count(),
        ];
        return view('admin.reports.projects.overview', compact('projects', 'byStatus'));
    }

    public function projectTasks()
    {
        $tasks = ProjectTask::with('project')->latest()->paginate(25);
        return view('admin.reports.projects.tasks', compact('tasks'));
    }

    public function projectBugs()
    {
        $bugs = ProjectBug::with('project')->latest()->paginate(25);
        return view('admin.reports.projects.bugs', compact('bugs'));
    }

    public function projectBudgets()
    {
        $budgets = ProjectBudget::with('project')->latest()->paginate(25);
        return view('admin.reports.projects.budgets', compact('budgets'));
    }

    public function timesheets()
    {
        $timesheets = Timesheet::with(['employee', 'project'])->latest()->paginate(25);
        return view('admin.reports.projects.timesheets', compact('timesheets'));
    }

    // ═══════════════════════════════════════════════
    // POS & RETAIL REPORTS
    // ═══════════════════════════════════════════════
    public function posSales()
    {
        $sales = PosSale::latest()->paginate(25);
        $total = PosSale::sum('total_amount') ?? 0;
        $todayTotal = PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0;
        $todayCount = PosSale::whereDate('created_at', today())->count();
        return view('admin.reports.pos.sales', compact('sales', 'total', 'todayTotal', 'todayCount'));
    }

    public function posSummary()
    {
        $daily = PosSale::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('date')->orderByDesc('date')->take(30)->get();
        $monthly = PosSale::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('year', 'month')->orderByDesc('year')->orderByDesc('month')->take(12)->get();
        return view('admin.reports.pos.summary', compact('daily', 'monthly'));
    }

    public function orders()
    {
        $orders = Order::latest()->paginate(25);
        $total = Order::count();
        return view('admin.reports.pos.orders', compact('orders', 'total'));
    }

    // ═══════════════════════════════════════════════
    // BOOKINGS & APPOINTMENTS REPORTS
    // ═══════════════════════════════════════════════
    public function appointments()
    {
        $appointments = Appointment::latest()->paginate(25);
        $today = Appointment::whereDate('appointment_date', today())->count();
        return view('admin.reports.bookings.appointments', compact('appointments', 'today'));
    }

    public function visitors()
    {
        $visitors = Visitor::latest()->paginate(25);
        $today = Visitor::whereDate('created_at', today())->count();
        return view('admin.reports.bookings.visitors', compact('visitors', 'today'));
    }

    public function frontDesk()
    {
        $records = FrontDesk::latest()->paginate(25);
        return view('admin.reports.bookings.front-desk', compact('records'));
    }

    // ═══════════════════════════════════════════════
    // FLEET & ASSETS REPORTS
    // ═══════════════════════════════════════════════
    public function vehicles()
    {
        $vehicles = Vehicle::latest()->get();
        $active = Vehicle::where('status', 'active')->count();
        return view('admin.reports.fleet.vehicles', compact('vehicles', 'active'));
    }

    public function fuelLogs()
    {
        $logs = FuelLog::with('vehicle')->latest()->paginate(25);
        $totalCost = FuelLog::sum('cost') ?? 0;
        return view('admin.reports.fleet.fuel-logs', compact('logs', 'totalCost'));
    }

    public function maintenance()
    {
        $records = VehicleMaintenance::with('vehicle')->latest()->paginate(25);
        $totalCost = VehicleMaintenance::sum('cost') ?? 0;
        return view('admin.reports.fleet.maintenance', compact('records', 'totalCost'));
    }

    public function fixedAssets()
    {
        $assets = FixedAsset::latest()->paginate(25);
        $totalValue = FixedAsset::sum('value') ?? 0;
        return view('admin.reports.fleet.fixed-assets', compact('assets', 'totalValue'));
    }

    public function depreciation()
    {
        $records = DepreciationRecord::with('asset')->latest()->paginate(25);
        return view('admin.reports.fleet.depreciation', compact('records'));
    }

    // ═══════════════════════════════════════════════
    // HELPDESK REPORTS
    // ═══════════════════════════════════════════════
    public function tickets()
    {
        $tickets = HelpdeskTicket::latest()->paginate(25);
        $byStatus = [
            'open' => HelpdeskTicket::where('status', 'open')->count(),
            'in_progress' => HelpdeskTicket::where('status', 'in_progress')->count(),
            'resolved' => HelpdeskTicket::where('status', 'resolved')->count(),
            'closed' => HelpdeskTicket::where('status', 'closed')->count(),
        ];
        $byPriority = [
            'urgent' => HelpdeskTicket::where('priority', 'urgent')->count(),
            'high' => HelpdeskTicket::where('priority', 'high')->count(),
            'medium' => HelpdeskTicket::where('priority', 'medium')->count(),
            'low' => HelpdeskTicket::where('priority', 'low')->count(),
        ];
        return view('admin.reports.helpdesk.tickets', compact('tickets', 'byStatus', 'byPriority'));
    }

    public function ticketsByCategory()
    {
        $categories = HelpdeskCategory::withCount('tickets')->get();
        return view('admin.reports.helpdesk.by-category', compact('categories'));
    }

    // ═══════════════════════════════════════════════
    // USER MANAGEMENT REPORTS
    // ═══════════════════════════════════════════════
    public function users()
    {
        $users = User::latest()->paginate(25);
        $total = User::count();
        $active = User::whereNotNull('email_verified_at')->count();
        return view('admin.reports.users.list', compact('users', 'total', 'active'));
    }

    public function userActivity()
    {
        $active = User::whereNotNull('email_verified_at')->count();
        $inactive = User::whereNull('email_verified_at')->count();
        $admins = User::where('role', 'admin')->orWhere('role', 'superadmin')->count();
        $recentLogins = User::orderByDesc('last_login_at')->take(15)->get();
        return view('admin.reports.users.activity', compact('active', 'inactive', 'admins', 'recentLogins'));
    }
}
