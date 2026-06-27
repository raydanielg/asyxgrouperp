<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\SalesProposal;
use App\Models\SalesInvoiceReturn;
use App\Models\PurchaseReturn;
use App\Models\Warehouse;
use App\Models\Transfer;
use App\Models\HelpdeskTicket;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\BankTransferPayment;
use App\Models\Employee;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\Project;
use App\Models\Product;
use App\Models\PosSale;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Leave;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

    public function index()
    {
        $weekAgo = now()->subDays(7)->startOfDay();

        // ─── Core ERP Stats ───
        $stats = [
            // Users
            'totalUsers' => User::where('role', 'user')->count(),
            'newUsersThisWeek' => User::where('role', 'user')->where('created_at', '>=', $weekAgo)->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalAllUsers' => User::count(),
            'activeUsers' => User::whereNotNull('email_verified_at')->count(),
            'inactiveUsers' => User::whereNull('email_verified_at')->count(),

            // Sales
            'totalSalesInvoices' => SalesInvoice::count(),
            'totalSalesAmount' => SalesInvoice::sum('total_amount') ?? 0,
            'totalSalesPaid' => SalesInvoice::sum('paid_amount') ?? 0,
            'totalSalesBalance' => SalesInvoice::sum('balance_amount') ?? 0,
            'draftSalesInvoices' => SalesInvoice::where('status', 'draft')->count(),
            'overdueSalesInvoices' => SalesInvoice::where('status', 'overdue')->count(),
            'paidSalesInvoices' => SalesInvoice::where('status', 'paid')->count(),

            // Purchases
            'totalPurchaseInvoices' => PurchaseInvoice::count(),
            'totalPurchaseAmount' => PurchaseInvoice::sum('total_amount') ?? 0,
            'totalPurchaseBalance' => PurchaseInvoice::sum('balance_amount') ?? 0,

            // Proposals
            'totalProposals' => SalesProposal::count(),
            'pendingProposals' => SalesProposal::where('status', 'sent')->count(),
            'acceptedProposals' => SalesProposal::where('status', 'accepted')->count(),
            'draftProposals' => SalesProposal::where('status', 'draft')->count(),

            // Returns
            'totalSalesReturns' => SalesInvoiceReturn::count(),
            'totalSalesReturnAmount' => SalesInvoiceReturn::sum('total_amount') ?? 0,
            'totalPurchaseReturns' => PurchaseReturn::count(),
            'totalPurchaseReturnAmount' => PurchaseReturn::sum('total_amount') ?? 0,

            // Inventory
            'totalWarehouses' => Warehouse::count(),
            'activeWarehouses' => Warehouse::where('is_active', true)->count(),
            'totalTransfers' => Transfer::count(),

            // Helpdesk
            'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
            'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
            'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
            'totalTickets' => HelpdeskTicket::count(),

            // Subscriptions
            'totalOrders' => Order::count(),
            'totalPlans' => Plan::count(),
            'activePlans' => Plan::where('status', true)->count(),
            'totalCoupons' => Coupon::count(),
            'pendingBankTransfers' => BankTransferPayment::where('status', 'pending')->count(),
        ];

        // ─── Recent Records ───
        $recentSales = SalesInvoice::with('customer')->latest()->take(5)->get();
        $recentPurchases = PurchaseInvoice::with('vendor')->latest()->take(5)->get();
        $recentTickets = HelpdeskTicket::with('category')->latest()->take(5)->get();
        $recentProposals = SalesProposal::with('customer')->latest()->take(5)->get();
        $recentUsers = User::where('role', 'user')->orderBy('created_at', 'desc')->take(5)->get();

        // ─── Charts ───
        $dailyLabels = [];
        $dailySales = [];
        $dailyPurchases = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailySales[] = (int) (SalesInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
            $dailyPurchases[] = (int) (PurchaseInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
        }

        // Monthly chart (last 6 months)
        $monthlyLabels = [];
        $monthlySales = [];
        $monthlyPurchases = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlySales[] = (int) (SalesInvoice::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount') ?? 0);
            $monthlyPurchases[] = (int) (PurchaseInvoice::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount') ?? 0);
        }

        // Invoice status distribution
        $salesStatusBreakdown = [
            'draft' => SalesInvoice::where('status', 'draft')->count(),
            'posted' => SalesInvoice::where('status', 'posted')->count(),
            'paid' => SalesInvoice::where('status', 'paid')->count(),
            'overdue' => SalesInvoice::where('status', 'overdue')->count(),
            'partial' => SalesInvoice::where('status', 'partial')->count(),
        ];

        // ─── Extended Module Stats ───
        $stats['totalEmployees'] = Employee::count();
        $stats['activeEmployees'] = Employee::where('status', 'active')->count();
        $stats['pendingLeaves'] = Leave::where('status', 'pending')->count();
        $stats['totalCrmLeads'] = CrmLead::count();
        $stats['newLeads'] = CrmLead::where('status', 'new')->count();
        $stats['qualifiedLeads'] = CrmLead::where('status', 'qualified')->count();
        $stats['totalDeals'] = CrmDeal::count();
        $stats['openDeals'] = CrmDeal::where('status', 'open')->count();
        $stats['totalDealValue'] = CrmDeal::where('status', 'open')->sum('value') ?? 0;
        $stats['totalProjects'] = Project::count();
        $stats['activeProjects'] = Project::where('status', 'in_progress')->count();
        $stats['completedProjects'] = Project::where('status', 'completed')->count();
        $stats['totalProducts'] = Product::count();
        $stats['lowStockProducts'] = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count();
        $stats['posTodaySales'] = PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0;
        $stats['posTodayCount'] = PosSale::whereDate('created_at', today())->count();
        $stats['posMonthSales'] = PosSale::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total_amount') ?? 0;
        $stats['totalExpenses'] = Expense::sum('amount') ?? 0;
        $stats['totalRevenues'] = Revenue::sum('amount') ?? 0;
        $stats['monthExpenses'] = Expense::whereMonth('expense_date', date('m'))->whereYear('expense_date', date('Y'))->sum('amount') ?? 0;
        $stats['monthRevenues'] = Revenue::whereMonth('revenue_date', date('m'))->whereYear('revenue_date', date('Y'))->sum('amount') ?? 0;

        return view('admin.dashboard', compact(
            'stats', 'recentSales', 'recentPurchases', 'recentTickets',
            'recentProposals', 'recentUsers',
            'dailyLabels', 'dailySales', 'dailyPurchases',
            'monthlyLabels', 'monthlySales', 'monthlyPurchases',
            'salesStatusBreakdown'
        ));
    }

    public function users()
    {
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function reports()
    {
        $stats = [
            'totalUsers' => User::where('role', 'user')->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'activeUsers' => User::whereNotNull('email_verified_at')->count(),
            'inactiveUsers' => User::whereNull('email_verified_at')->count(),
            'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
            'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
            'totalSalesReturns' => SalesInvoiceReturn::sum('total_amount') ?? 0,
            'totalPurchaseReturns' => PurchaseReturn::sum('total_amount') ?? 0,
            'totalWarehouses' => Warehouse::count(),
            'totalTickets' => HelpdeskTicket::count(),
            'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
            'totalOrders' => Order::count(),
            'totalPlans' => Plan::count(),
            'totalCoupons' => Coupon::count(),
        ];

        $salesByStatus = [
            'draft' => SalesInvoice::where('status', 'draft')->count(),
            'posted' => SalesInvoice::where('status', 'posted')->count(),
            'paid' => SalesInvoice::where('status', 'paid')->count(),
            'overdue' => SalesInvoice::where('status', 'overdue')->count(),
        ];

        $purchaseByStatus = [
            'draft' => PurchaseInvoice::where('status', 'draft')->count(),
            'posted' => PurchaseInvoice::where('status', 'posted')->count(),
            'paid' => PurchaseInvoice::where('status', 'paid')->count(),
            'overdue' => PurchaseInvoice::where('status', 'overdue')->count(),
        ];

        return view('admin.reports', compact('stats', 'salesByStatus', 'purchaseByStatus'));
    }
}
