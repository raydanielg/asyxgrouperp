<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\SalesProposal;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\Project;
use App\Models\HelpdeskTicket;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\PosSale;
use App\Models\Order;
use App\Models\Attendance;
use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $role = $this->getUserRole($user);

        $stats = $this->getStatsForRole($role);
        $recentItems = $this->getRecentItemsForRole($role);
        $kpiCards = $this->getKpiCardsForRole($role, $stats);
        $quickActions = $this->getQuickActionsForRole($role);

        $money = fn($n) => 'TZS ' . number_format($n);

        return view('dashboard.role', compact('role', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money'));
    }

    private function getUserRole($user): string
    {
        if ($user->isAdmin()) return 'admin';

        // Check role_user pivot
        $role = $user->roles()->first();
        if ($role) return $role->name;

        // Fallback to user role column
        return $user->role ?? 'user';
    }

    private function getStatsForRole(string $role): array
    {
        $stats = [];

        switch ($role) {
            case 'admin':
            case 'administrator':
            case 'admin_manager':
                $stats = [
                    'totalUsers' => User::count(),
                    'totalEmployees' => Employee::count(),
                    'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
                    'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
                    'totalExpenses' => Expense::sum('amount') ?? 0,
                    'totalRevenues' => Revenue::sum('amount') ?? 0,
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                ];
                break;

            case 'director':
                $stats = [
                    'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
                    'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
                    'totalExpenses' => Expense::sum('amount') ?? 0,
                    'totalRevenues' => Revenue::sum('amount') ?? 0,
                    'salesBalance' => SalesInvoice::sum('balance_amount') ?? 0,
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'totalEmployees' => Employee::count(),
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'totalProposals' => SalesProposal::count(),
                    'acceptedProposals' => SalesProposal::where('status', 'accepted')->count(),
                ];
                break;

            case 'finance_officer':
                $stats = [
                    'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
                    'salesPaid' => SalesInvoice::sum('paid_amount') ?? 0,
                    'salesBalance' => SalesInvoice::sum('balance_amount') ?? 0,
                    'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
                    'purchaseBalance' => PurchaseInvoice::sum('balance_amount') ?? 0,
                    'totalExpenses' => Expense::sum('amount') ?? 0,
                    'monthExpenses' => Expense::whereMonth('expense_date', date('m'))->whereYear('expense_date', date('Y'))->sum('amount') ?? 0,
                    'totalRevenues' => Revenue::sum('amount') ?? 0,
                    'monthRevenues' => Revenue::whereMonth('revenue_date', date('m'))->whereYear('revenue_date', date('Y'))->sum('amount') ?? 0,
                    'overdueInvoices' => SalesInvoice::where('status', 'overdue')->count(),
                    'draftInvoices' => SalesInvoice::where('status', 'draft')->count(),
                ];
                break;

            case 'auditor':
                $stats = [
                    'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
                    'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
                    'totalExpenses' => Expense::sum('amount') ?? 0,
                    'totalRevenues' => Revenue::sum('amount') ?? 0,
                    'salesInvoiceCount' => SalesInvoice::count(),
                    'purchaseInvoiceCount' => PurchaseInvoice::count(),
                    'expenseCount' => Expense::count(),
                    'revenueCount' => Revenue::count(),
                    'overdueInvoices' => SalesInvoice::where('status', 'overdue')->count(),
                    'posSales' => PosSale::sum('total_amount') ?? 0,
                ];
                break;

            case 'hr_officer':
                $stats = [
                    'totalEmployees' => Employee::count(),
                    'activeEmployees' => Employee::where('status', 'active')->count(),
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                    'absentToday' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
                    'totalPayroll' => Employee::sum('salary') ?? 0,
                ];
                break;

            case 'legal_officer':
                $stats = [
                    'totalContracts' => \App\Models\CrmContract::count(),
                    'activeContracts' => \App\Models\CrmContract::where('status', 'active')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                ];
                break;

            case 'receptionist':
                $stats = [
                    'totalLeads' => CrmLead::count(),
                    'newLeads' => CrmLead::where('status', 'new')->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'totalContacts' => \App\Models\CrmContact::count(),
                ];
                break;

            case 'logistics_officer':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'activeWarehouses' => Warehouse::where('is_active', true)->count(),
                    'pendingTransfers' => \App\Models\Transfer::where('status', 'pending')->count(),
                ];
                break;

            case 'technical_manager':
            case 'ict_engineer':
                $stats = [
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'totalEmployees' => Employee::count(),
                ];
                break;

            case 'technician':
                $stats = [
                    'myTickets' => HelpdeskTicket::where('assigned_to', auth()->id())->count(),
                    'openTickets' => HelpdeskTicket::where('assigned_to', auth()->id())->where('status', 'open')->count(),
                    'inProgressTickets' => HelpdeskTicket::where('assigned_to', auth()->id())->where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('assigned_to', auth()->id())->where('status', 'resolved')->count(),
                ];
                break;

            case 'ict_officer':
                $stats = [
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'totalAssets' => \App\Models\Asset::count(),
                    'totalProjects' => Project::count(),
                    'totalEmployees' => Employee::count(),
                ];
                break;

            case 'project_manager':
                $stats = [
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'completedProjects' => Project::where('status', 'completed')->count(),
                    'openDeals' => CrmDeal::where('status', 'open')->count(),
                    'totalDealValue' => CrmDeal::where('status', 'open')->sum('value') ?? 0,
                    'totalEmployees' => Employee::count(),
                ];
                break;

            case 'operations_manager':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'totalSales' => SalesInvoice::count(),
                    'totalPurchases' => PurchaseInvoice::count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'totalEmployees' => Employee::count(),
                ];
                break;

            case 'call_center_agent':
                $stats = [
                    'totalLeads' => CrmLead::count(),
                    'newLeads' => CrmLead::where('status', 'new')->count(),
                    'qualifiedLeads' => CrmLead::where('status', 'qualified')->count(),
                    'myLeads' => CrmLead::where('assigned_to', auth()->id())->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                ];
                break;

            case 'cashier':
                $stats = [
                    'todaySales' => PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0,
                    'todayCount' => PosSale::whereDate('created_at', today())->count(),
                    'monthSales' => PosSale::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total_amount') ?? 0,
                    'totalInvoices' => SalesInvoice::count(),
                    'totalProducts' => Product::count(),
                ];
                break;

            case 'supervisor':
                $stats = [
                    'totalEmployees' => Employee::count(),
                    'presentToday' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                    'absentToday' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'totalProjects' => Project::count(),
                    'todaySales' => PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0,
                    'totalProducts' => Product::count(),
                ];
                break;

            default:
                $stats = [
                    'totalProjects' => Project::count(),
                    'myTasks' => \App\Models\ProjectTask::where('assigned_to', auth()->id())->count(),
                ];
                break;
        }

        return $stats;
    }

    private function getRecentItemsForRole(string $role): array
    {
        $items = [];

        switch ($role) {
            case 'admin':
            case 'administrator':
            case 'admin_manager':
            case 'director':
                $items['recentUsers'] = User::latest()->take(5)->get();
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                break;

            case 'finance_officer':
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentExpenses'] = Expense::latest()->take(5)->get();
                $items['recentRevenues'] = Revenue::latest()->take(5)->get();
                break;

            case 'auditor':
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentPurchases'] = PurchaseInvoice::with('vendor')->latest()->take(5)->get();
                $items['recentExpenses'] = Expense::latest()->take(5)->get();
                break;

            case 'hr_officer':
                $items['recentEmployees'] = Employee::latest()->take(5)->get();
                $items['pendingLeaves'] = Leave::where('status', 'pending')->latest()->take(5)->get();
                break;

            case 'receptionist':
            case 'call_center_agent':
                $items['recentLeads'] = CrmLead::latest()->take(5)->get();
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                break;

            case 'logistics_officer':
            case 'operations_manager':
                $items['lowStockProducts'] = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->take(5)->get();
                $items['recentTransfers'] = \App\Models\Transfer::latest()->take(5)->get();
                break;

            case 'technical_manager':
            case 'ict_officer':
            case 'ict_engineer':
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'technician':
                $items['myTickets'] = HelpdeskTicket::where('assigned_to', auth()->id())->latest()->take(5)->get();
                break;

            case 'project_manager':
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                $items['openDeals'] = CrmDeal::where('status', 'open')->latest()->take(5)->get();
                break;

            case 'cashier':
                $items['recentSales'] = PosSale::latest()->take(5)->get();
                break;

            case 'supervisor':
                $items['recentAttendance'] = Attendance::whereDate('date', today())->latest()->take(5)->get();
                $items['pendingLeaves'] = Leave::where('status', 'pending')->latest()->take(5)->get();
                break;
        }

        return $items;
    }

    private function getKpiCardsForRole(string $role, array $stats): array
    {
        $money = fn($n) => 'TZS ' . number_format($n);

        return match ($role) {
            'admin', 'administrator', 'admin_manager' => [
                ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald'],
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'sky'],
                ['label' => 'Total Expenses', 'value' => $money($stats['totalExpenses'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'director' => [
                ['label' => 'Total Revenue', 'value' => $money($stats['totalRevenues'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ['label' => 'Total Expenses', 'value' => $money($stats['totalExpenses'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'rose'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'sky'],
            ],
            'finance_officer' => [
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'emerald'],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'rose'],
                ['label' => 'Month Expenses', 'value' => $money($stats['monthExpenses'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Overdue Invoices', 'value' => $stats['overdueInvoices'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'auditor' => [
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'emerald'],
                ['label' => 'Total Purchases', 'value' => $money($stats['totalPurchases'] ?? 0), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'sky'],
                ['label' => 'Total Expenses', 'value' => $money($stats['totalExpenses'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
                ['label' => 'Overdue Invoices', 'value' => $stats['overdueInvoices'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'hr_officer' => [
                ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald'],
                ['label' => 'Active', 'value' => $stats['activeEmployees'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                ['label' => 'Present Today', 'value' => $stats['todayAttendance'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'emerald'],
            ],
            'legal_officer' => [
                ['label' => 'Active Contracts', 'value' => $stats['activeContracts'] ?? 0, 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'emerald'],
                ['label' => 'Total Contracts', 'value' => $stats['totalContracts'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2', 'color' => 'amber'],
            ],
            'receptionist', 'call_center_agent' => [
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'emerald'],
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'sky'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Total Contacts', 'value' => $stats['totalContacts'] ?? 0, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'emerald'],
            ],
            'logistics_officer' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'sky'],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'rose'],
            ],
            'technical_manager', 'ict_engineer' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
            ],
            'technician' => [
                ['label' => 'My Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Total Assigned', 'value' => $stats['myTickets'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
            ],
            'ict_officer' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'Total Assets', 'value' => $stats['totalAssets'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Total Projects', 'value' => $stats['totalProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
            ],
            'project_manager' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'emerald'],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'amber'],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ],
            'operations_manager' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'rose'],
            ],
            'cashier' => [
                ['label' => "Today's Sales", 'value' => $money($stats['todaySales'] ?? 0), 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'emerald'],
                ['label' => "Today's Count", 'value' => $stats['todayCount'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
                ['label' => 'Month Sales', 'value' => $money($stats['monthSales'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'rose'],
            ],
            'supervisor' => [
                ['label' => 'Present Today', 'value' => $stats['presentToday'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                ['label' => "Today's POS Sales", 'value' => $money($stats['todaySales'] ?? 0), 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'sky'],
            ],
            default => [
                ['label' => 'My Tasks', 'value' => $stats['myTasks'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'emerald'],
            ],
        };
    }

    private function getQuickActionsForRole(string $role): array
    {
        return match ($role) {
            'admin', 'administrator', 'admin_manager' => [
                ['label' => 'Manage Users', 'route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
            ],
            'director' => [
                ['label' => 'View Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                ['label' => 'Sales Dashboard', 'route' => 'admin.sales-dashboard', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
            ],
            'finance_officer' => [
                ['label' => 'Sales Invoices', 'route' => 'admin.sales-invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['label' => 'Revenues', 'route' => 'admin.revenues.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ],
            'hr_officer' => [
                ['label' => 'Employees', 'route' => 'admin.employees.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['label' => 'Leaves', 'route' => 'admin.leaves.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ],
            'cashier' => [
                ['label' => 'POS Terminal', 'route' => 'admin.pos.index', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                ['label' => 'Sales Invoices', 'route' => 'admin.sales-invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ],
            'technician', 'technical_manager', 'ict_officer', 'ict_engineer' => [
                ['label' => 'Helpdesk', 'route' => 'admin.helpdesk-tickets.index', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
            ],
            'project_manager' => [
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                ['label' => 'Timesheets', 'route' => 'admin.timesheets.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ],
            'receptionist', 'call_center_agent' => [
                ['label' => 'Leads', 'route' => 'admin.crm-leads.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['label' => 'Contacts', 'route' => 'admin.crm-contacts.index', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ],
            'logistics_officer' => [
                ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Warehouses', 'route' => 'admin.warehouses.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
            ],
            'operations_manager' => [
                ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
            ],
            default => [],
        };
    }
}
