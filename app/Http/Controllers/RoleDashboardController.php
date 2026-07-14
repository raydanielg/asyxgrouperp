<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
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
use App\Models\SalaryAdvanceRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function cacheForRole(string $key, string $role, callable $callback, int $ttl = 300): mixed
    {
        $userId = auth()->id() ?? 0;
        $cacheKey = "role_dashboard_{$key}_{$role}_{$userId}";
        return cache()->remember($cacheKey, $ttl, $callback);
    }

    public function index()
    {
        try {
            $user = auth()->user();
            $role = $this->getUserRole($user);

            $stats = $this->getSafeStatsForRole($role);
            $recentItems = $this->getSafeRecentItemsForRole($role);
            $kpiCards = $this->getSafeKpiCardsForRole($role, $stats);
            $quickActions = $this->getQuickActionsForRole($role);
            $chartData = $this->getSafeChartDataForRole($role);
            $roleLabel = $this->getRoleLabel($role);
            $secondaryKpis = $this->getSafeSecondaryKpisForRole($role, $stats);
            $aiInsights = $this->getAiInsightsForRole($role, $stats);

            $money = fn($n) => 'TZS ' . number_format($n);

            $companies = null;
            $systemMode = null;

            // Salary advance summary for accountant / finance roles
            $salaryAdvancePending = 0;
            $salaryAdvanceApproved = 0;
            $salaryAdvanceTotal = 0;
            $recentSalaryAdvances = collect();
            if (in_array($role, ['accountant', 'finance_manager'])) {
                $salaryAdvancePending = SalaryAdvanceRequest::where('status', 'pending')->count();
                $salaryAdvanceApproved = SalaryAdvanceRequest::where('status', 'approved')->count();
                $salaryAdvanceTotal = SalaryAdvanceRequest::sum('amount') ?? 0;
                $recentSalaryAdvances = SalaryAdvanceRequest::with('user')->latest()->take(5)->get();
            }

            $viewName = 'roles.' . str_replace('_', '-', $role) . '.dashboard';
            if (view()->exists($viewName)) {
                return view($viewName, compact('role', 'roleLabel', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money', 'chartData', 'secondaryKpis', 'aiInsights', 'companies', 'systemMode', 'salaryAdvancePending', 'salaryAdvanceApproved', 'salaryAdvanceTotal', 'recentSalaryAdvances'));
            }

            return view('dashboard.role', compact('role', 'roleLabel', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money', 'chartData', 'secondaryKpis', 'aiInsights', 'companies', 'systemMode', 'salaryAdvancePending', 'salaryAdvanceApproved', 'salaryAdvanceTotal', 'recentSalaryAdvances'));
        } catch (\Throwable $e) {
            // Fail-safe: ensure no company/user ever sees a broken dashboard
            return $this->renderFallbackDashboard($e);
        }
    }

    private function renderFallbackDashboard(\Throwable $e): \Illuminate\View\View
    {
        $role = 'user';
        $roleLabel = 'Dashboard';
        $stats = [];
        $recentItems = [];
        $kpiCards = [
            ['label' => 'Dashboard', 'value' => 'Available', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'emerald'],
        ];
        $quickActions = [];
        $chartData = ['title' => 'Activity', 'labels' => [], 'values' => [], 'secondaryValues' => []];
        $secondaryKpis = [];
        $aiInsights = ['message' => 'Dashboard loaded in safe mode.', 'suggestions' => []];
        $money = fn($n) => 'TZS ' . number_format($n);

        return view('dashboard.role', compact('role', 'roleLabel', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money', 'chartData', 'secondaryKpis', 'aiInsights'));
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
            case 'superadmin':
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

            case 'finance_manager':
            case 'accountant':
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

            case 'hr_manager':
                $stats = [
                    'totalEmployees' => Employee::count(),
                    'activeEmployees' => Employee::where('status', 'active')->count(),
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                    'absentToday' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
                    'totalPayroll' => Employee::sum('salary') ?? 0,
                ];
                break;

            case 'procurement_manager':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'activeWarehouses' => Warehouse::where('is_active', true)->count(),
                    'pendingTransfers' => \App\Models\Transfer::where('status', 'pending')->count(),
                ];
                break;

            case 'technical_manager':
                $stats = [
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
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

            case 'sales_manager':
                $stats = [
                    'totalLeads' => CrmLead::count(),
                    'newLeads' => CrmLead::where('status', 'new')->count(),
                    'qualifiedLeads' => CrmLead::where('status', 'qualified')->count(),
                    'myLeads' => CrmLead::where('assigned_to', auth()->id())->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
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
            case 'superadmin':
            case 'director':
                $items['recentUsers'] = User::with('roles')->latest()->take(5)->get();
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'finance_manager':
            case 'accountant':
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentExpenses'] = Expense::latest()->take(5)->get();
                $items['recentRevenues'] = Revenue::latest()->take(5)->get();
                break;

            case 'hr_manager':
                $items['recentEmployees'] = Employee::latest()->take(5)->get();
                $items['pendingLeaves'] = Leave::where('status', 'pending')->latest()->take(5)->get();
                break;

            case 'procurement_manager':
            case 'operations_manager':
                $items['lowStockProducts'] = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->take(5)->get();
                $items['recentTransfers'] = \App\Models\Transfer::latest()->take(5)->get();
                break;

            case 'technical_manager':
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'project_manager':
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                $items['openDeals'] = CrmDeal::where('status', 'open')->latest()->take(5)->get();
                break;

            case 'sales_manager':
                $items['recentLeads'] = CrmLead::latest()->take(5)->get();
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                break;
        }

        return $items;
    }

    private function getKpiCardsForRole(string $role, array $stats): array
    {
        $money = fn($n) => 'TZS ' . number_format($n);

        return match ($role) {
            'admin', 'superadmin' => [
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
            'finance_manager', 'accountant' => [
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'emerald'],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'rose'],
                ['label' => 'Month Expenses', 'value' => $money($stats['monthExpenses'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Overdue Invoices', 'value' => $stats['overdueInvoices'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'hr_manager' => [
                ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald'],
                ['label' => 'Active', 'value' => $stats['activeEmployees'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                ['label' => 'Present Today', 'value' => $stats['todayAttendance'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'emerald'],
            ],
            'procurement_manager' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'sky'],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'rose'],
            ],
            'sales_manager' => [
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'emerald'],
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'sky'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'My Leads', 'value' => $stats['myLeads'] ?? 0, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'emerald'],
            ],
            'technical_manager' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
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
            default => [
                ['label' => 'My Tasks', 'value' => $stats['myTasks'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'emerald'],
            ],
        };
    }

    /**
     * Quick action links on the role dashboard. Built from RoleModules so every
     * link points to the role's own /role/{module} page (never /admin/...), and
     * so new/custom roles automatically get sensible actions without code changes.
     */
    private function getQuickActionsForRole(string $role): array
    {
        $skip = ['dashboard', 'my-account', 'payslips', 'salary'];
        $modules = array_values(array_diff(\App\Support\RoleModules::allowedModules($role, auth()->user()), $skip));

        $actions = [];
        foreach (array_slice($modules, 0, 6) as $module) {
            $actions[] = [
                'label' => \App\Support\RoleModules::label($module),
                'route' => 'role.page',
                'params' => $module,
                'icon' => \App\Support\RoleModules::icon($module),
            ];
        }

        return $actions;
    }

    // legacyQuickActionsForRole removed – superseded by generic getQuickActionsForRole() above.

    private function getRoleLabel(string $role): string
    {
        $labels = [
            'superadmin' => 'Super Admin',
            'admin' => 'System Admin',
            'director' => 'Director',
            'accountant' => 'Accountant',
            'finance_manager' => 'Finance Manager',
            'procurement_manager' => 'Procurement Manager',
            'sales_manager' => 'Sales Manager',
            'project_manager' => 'Project Manager',
            'technical_manager' => 'Technical Manager',
            'operations_manager' => 'Operations Manager',
            'hr_manager' => 'HR Manager',
        ];
        return $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    public function reportPdf()
    {
        $user = auth()->user();
        $role = $this->getUserRole($user);
        $roleLabel = $this->getRoleLabel($role);
        $stats = $this->getStatsForRole($role);
        $recentItems = $this->getRecentItemsForRole($role);
        $kpiCards = $this->getKpiCardsForRole($role, $stats);
        $money = fn($n) => 'TZS ' . number_format($n);

        $data = array_merge(compact('role', 'roleLabel', 'stats', 'kpiCards', 'money'), $recentItems);
        $data['lowStockProducts'] = \App\Models\Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->take(10)->get();
        $data['activeProjects'] = \App\Models\Project::where('status', 'in_progress')->latest()->take(10)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.role-report', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('role-report-' . $role . '-' . now()->format('Ymd') . '.pdf');
    }

    private function dailyCountsForRange(string $model, string $dateColumn = 'created_at', ?\Closure $filter = null): array
    {
        $start = now()->subDays(13)->startOfDay();
        $end = now()->endOfDay();

        $query = $model::query()
            ->whereDate($dateColumn, '>=', $start)
            ->whereDate($dateColumn, '<=', $end);

        if ($filter) {
            $filter($query);
        }

        $counts = $query->selectRaw("DATE({$dateColumn}) as date, COUNT(*) as total")
            ->groupBy('date')
            ->pluck('total', 'date');

        $values = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $values[] = (int) ($counts[$date] ?? 0);
        }

        return $values;
    }

    private function dailySumsForRange(string $model, string $dateColumn, string $amountColumn, ?\Closure $filter = null): array
    {
        $start = now()->subDays(13)->startOfDay();
        $end = now()->endOfDay();

        $query = $model::query()
            ->whereDate($dateColumn, '>=', $start)
            ->whereDate($dateColumn, '<=', $end);

        if ($filter) {
            $filter($query);
        }

        $sums = $query->selectRaw("DATE({$dateColumn}) as date, SUM({$amountColumn}) as total")
            ->groupBy('date')
            ->pluck('total', 'date');

        $values = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $values[] = (int) ($sums[$date] ?? 0);
        }

        return $values;
    }

    private function getChartDataForRole(string $role): array
    {
        $data = [
            'labels' => [],
            'values' => [],
            'type' => 'bar',
            'title' => '',
            'secondaryLabels' => [],
            'secondaryValues' => [],
            'secondaryTitle' => '',
        ];

        // 14-day trend labels
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data['labels'][] = $date->format('d M');
        }

        switch ($role) {
            case 'admin':
            case 'superadmin':
            case 'director':
                $data['title'] = 'Sales vs Purchases (14 days)';
                $data['values'] = $this->dailySumsForRange(SalesInvoice::class, 'created_at', 'total_amount');
                $data['secondaryValues'] = $this->dailySumsForRange(PurchaseInvoice::class, 'created_at', 'total_amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Purchases';
                break;

            case 'finance_manager':
            case 'accountant':
                $data['title'] = 'Revenue vs Expenses (14 days)';
                $data['values'] = $this->dailySumsForRange(Revenue::class, 'revenue_date', 'amount');
                $data['secondaryValues'] = $this->dailySumsForRange(Expense::class, 'expense_date', 'amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Expenses';
                break;

            case 'hr_manager':
                $data['title'] = 'Attendance Trend (14 days)';
                $data['values'] = $this->dailyCountsForRange(Attendance::class, 'date', fn($q) => $q->where('status', 'present'));
                break;

            case 'technical_manager':
                $data['title'] = 'Tickets Created (14 days)';
                $data['values'] = $this->dailyCountsForRange(HelpdeskTicket::class, 'created_at');
                break;

            case 'sales_manager':
                $data['title'] = 'New Leads (14 days)';
                $data['values'] = $this->dailyCountsForRange(CrmLead::class, 'created_at');
                break;

            case 'project_manager':
            case 'operations_manager':
                $data['title'] = 'Project Activity (14 days)';
                $data['values'] = $this->dailyCountsForRange(Project::class, 'updated_at');
                break;

            case 'procurement_manager':
                $data['title'] = 'Stock Movements (14 days)';
                $data['values'] = $this->dailyCountsForRange(\App\Models\StockMovement::class, 'created_at');
                break;

            default:
                $data['title'] = 'Activity (14 days)';
                $data['values'] = array_fill(0, 14, 0);
                break;
        }

        return $data;
    }

    private function getSecondaryKpisForRole(string $role, array $stats): array
    {
        $money = fn($n) => 'TZS ' . number_format($n);

        return match ($role) {
            'admin', 'superadmin' => [
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
            ],
            'director' => [
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Proposals', 'value' => $stats['totalProposals'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-proposals']],
                ['label' => 'Accepted', 'value' => $stats['acceptedProposals'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'sales-proposals']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
            ],
            'finance_manager', 'accountant' => [
                ['label' => 'Sales Paid', 'value' => $money($stats['salesPaid'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchases', 'value' => $money($stats['totalPurchases'] ?? 0), 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Purchase Balance', 'value' => $money($stats['purchaseBalance'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Month Revenue', 'value' => $money($stats['monthRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'Draft Invoices', 'value' => $stats['draftInvoices'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
            ],
            'hr_manager' => [
                ['label' => 'Active Employees', 'value' => $stats['activeEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'Total Payroll', 'value' => $money($stats['totalPayroll'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'payroll']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
            ],
            'procurement_manager' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'transfers']],
            ],
            'sales_manager' => [
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'My Leads', 'value' => $stats['myLeads'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
            ],
            'technical_manager' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'project_manager' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'operations_manager' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Sales', 'value' => $stats['totalSales'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchases', 'value' => $stats['totalPurchases'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            default => [
                ['label' => 'My Tasks', 'value' => $stats['myTasks'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
            ],
        };
    }

    // ════════════════════════════════════════════════════════════
    // Safe wrappers - ensure no database/model errors reach the UI
    // ════════════════════════════════════════════════════════════

    private function getSafeStatsForRole(string $role): array
    {
        try {
            return $this->cacheForRole('stats', $role, fn() => $this->getStatsForRole($role));
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getSafeRecentItemsForRole(string $role): array
    {
        try {
            // Don't cache Eloquent collections — they can cause serialization errors across deployments
            return $this->getRecentItemsForRole($role);
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getSafeKpiCardsForRole(string $role, array $stats): array
    {
        try {
            return $this->getKpiCardsForRole($role, $stats);
        } catch (\Throwable $e) {
            return [
                ['label' => 'Dashboard', 'value' => 'Ready', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17', 'color' => 'emerald'],
            ];
        }
    }

    private function getSafeChartDataForRole(string $role): array
    {
        try {
            return $this->cacheForRole('chart', $role, fn() => $this->getChartDataForRole($role));
        } catch (\Throwable $e) {
            return [
                'title' => 'Activity',
                'secondaryTitle' => '',
                'labels' => [],
                'values' => [],
                'secondaryValues' => [],
            ];
        }
    }

    private function getSafeSecondaryKpisForRole(string $role, array $stats): array
    {
        try {
            return $this->getSecondaryKpisForRole($role, $stats);
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ════════════════════════════════════════════════════════════
    // AI Insights for each role
    // ════════════════════════════════════════════════════════════

    private function getAiInsightsForRole(string $role, array $stats): array
    {
        // AI Insights feature disabled system-wide to avoid performance and accuracy issues.
        return [
            'message' => null,
            'suggestions' => [],
        ];
    }
}
