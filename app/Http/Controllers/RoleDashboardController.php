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
            if ($role === 'erp_super_administrator') {
                $companies = Company::withCount('users')->orderBy('is_group', 'desc')->orderBy('name')->get();
                $systemMode = config('app.maintenance_mode', false) ? 'Maintenance' : 'Online';
            }

            $viewName = 'roles.' . str_replace('_', '-', $role) . '.dashboard';
            if (view()->exists($viewName)) {
                return view($viewName, compact('role', 'roleLabel', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money', 'chartData', 'secondaryKpis', 'aiInsights', 'companies', 'systemMode'));
            }

            return view('dashboard.role', compact('role', 'roleLabel', 'stats', 'recentItems', 'kpiCards', 'quickActions', 'money', 'chartData', 'secondaryKpis', 'aiInsights', 'companies', 'systemMode'));
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
            case 'erp_super_administrator':
                $stats = [
                    'totalCompanies' => Company::count(),
                    'totalUsers' => User::count(),
                    'totalRoles' => Role::count(),
                    'totalPermissions' => Permission::count(),
                    'totalEmployees' => Employee::count(),
                    'totalSales' => SalesInvoice::sum('total_amount') ?? 0,
                    'totalPurchases' => PurchaseInvoice::sum('total_amount') ?? 0,
                    'totalExpenses' => Expense::sum('amount') ?? 0,
                    'totalRevenues' => Revenue::sum('amount') ?? 0,
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'groupCompanies' => Company::where('is_group', true)->count(),
                    'subsidiaryCompanies' => Company::where('is_group', false)->count(),
                ];
                break;

            case 'admin':
            case 'administrator':
            case 'admin_manager':
            case 'managing_director':
            case 'general_manager':
            case 'erp_administrator':
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
            case 'finance_manager':
            case 'chief_accountant':
            case 'accountant':
            case 'accounts_receivable_officer':
            case 'accounts_payable_officer':
            case 'payroll_officer':
            case 'budget_officer':
            case 'credit_controller':
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
            case 'hr_manager':
            case 'recruitment_officer':
            case 'training_officer':
            case 'time_and_attendance_officer':
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
            case 'procurement_manager':
            case 'procurement_officer':
            case 'tender_officer':
            case 'store_manager':
            case 'storekeeper':
            case 'inventory_controller':
            case 'asset_officer':
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
            case 'senior_systems_engineer':
            case 'systems_engineer':
            case 'support_engineer':
            case 'noc_engineer':
            case 'service_desk_manager':
            case 'helpdesk_supervisor':
            case 'helpdesk_officer':
            case 'ict_administrator':
            case 'network_engineer':
            case 'software_engineer':
            case 'cybersecurity_engineer':
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
            case 'field_technician':
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
                    'totalAssets' => 0,
                    'totalProjects' => Project::count(),
                    'totalEmployees' => Employee::count(),
                ];
                break;

            case 'project_manager':
            case 'project_director':
            case 'technical_projects_manager':
            case 'project_coordinator':
            case 'project_engineer':
            case 'site_supervisor':
            case 'project_accountant':
            case 'team_leader':
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
            case 'operations_officer':
            case 'fleet_manager':
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

            case 'sgr_agent':
                $userId = auth()->id();
                $stats = [
                    'totalActionPoints' => \App\Models\SgrActionPoint::where('created_by', $userId)->count(),
                    'pendingApproval' => \App\Models\SgrActionPoint::where('created_by', $userId)->where('approval_status', 'pending')->count(),
                    'approvedPoints' => \App\Models\SgrActionPoint::where('created_by', $userId)->where('approval_status', 'approved')->count(),
                    'rejectedPoints' => \App\Models\SgrActionPoint::where('created_by', $userId)->where('approval_status', 'rejected')->count(),
                    'overduePoints' => \App\Models\SgrActionPoint::where('created_by', $userId)->where('due_date', '<', now())->where('status', '!=', 'Completed')->count(),
                    'completedPoints' => \App\Models\SgrActionPoint::where('created_by', $userId)->where('status', 'Completed')->count(),
                ];
                break;

            case 'call_center_agent':
            case 'sales_manager':
            case 'business_development_manager':
            case 'sales_executive':
            case 'crm_officer':
            case 'marketing_officer':
            case 'call_center_supervisor':
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

            case 'employee_self_service':
            case 'manager_self_service':
                $employee = Employee::where('user_id', auth()->id())->first();
                $stats = [
                    'myPendingLeaves' => $employee ? Leave::where('employee_id', $employee->id)->where('status', 'pending')->count() : 0,
                    'myApprovedLeaves' => $employee ? Leave::where('employee_id', $employee->id)->where('status', 'approved')->count() : 0,
                    'myAttendanceThisMonth' => $employee ? Attendance::where('employee_id', $employee->id)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->where('status', 'present')->count() : 0,
                    'myLatestPayroll' => $employee ? (\App\Models\Payroll::where('employee_id', $employee->id)->latest()->value('net_salary') ?? 0) : 0,
                    'myTasks' => \App\Models\ProjectTask::where('assigned_to', auth()->id())->count(),
                ];
                if ($role === 'manager_self_service') {
                    $stats['teamPendingLeaves'] = Leave::where('status', 'pending')->count();
                    $stats['teamPresentToday'] = Attendance::whereDate('date', today())->where('status', 'present')->count();
                }
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
            case 'erp_super_administrator':
                $items['recentCompanies'] = Company::latest()->take(5)->get();
                $items['recentUsers'] = User::with('roles')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'admin':
            case 'administrator':
            case 'admin_manager':
            case 'director':
            case 'erp_administrator':
                $items['recentUsers'] = User::with('roles')->latest()->take(5)->get();
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
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

            case 'sgr_agent':
                $userId = auth()->id();
                $items['recentActionPoints'] = \App\Models\SgrActionPoint::where('created_by', $userId)->latest()->take(5)->get();
                $items['pendingActionPoints'] = \App\Models\SgrActionPoint::where('created_by', $userId)->where('approval_status', 'pending')->latest()->take(5)->get();
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
            case 'ict_administrator':
            case 'network_engineer':
            case 'software_engineer':
            case 'cybersecurity_engineer':
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'technician':
            case 'field_technician':
                $items['myTickets'] = HelpdeskTicket::where('assigned_to', auth()->id())->latest()->take(5)->get();
                break;

            case 'project_manager':
            case 'team_leader':
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                $items['openDeals'] = CrmDeal::where('status', 'open')->latest()->take(5)->get();
                break;

            case 'employee_self_service':
            case 'manager_self_service':
                $emp = Employee::where('user_id', auth()->id())->first();
                $items['myLeaves'] = $emp ? Leave::where('employee_id', $emp->id)->latest()->take(5)->get() : collect();
                $items['myAttendance'] = $emp ? Attendance::where('employee_id', $emp->id)->latest()->take(5)->get() : collect();
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
            'erp_super_administrator' => [
                ['label' => 'Companies', 'value' => $stats['totalCompanies'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'emerald'],
                ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'sky'],
                ['label' => 'Roles', 'value' => $stats['totalRoles'] ?? 0, 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'violet'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'admin', 'administrator', 'admin_manager', 'managing_director', 'general_manager', 'erp_administrator' => [
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
            'finance_officer', 'finance_manager', 'chief_accountant', 'accountant', 'accounts_receivable_officer', 'accounts_payable_officer', 'payroll_officer', 'budget_officer', 'credit_controller' => [
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
            'hr_officer', 'hr_manager', 'recruitment_officer', 'training_officer', 'time_and_attendance_officer' => [
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
            'sgr_agent' => [
                ['label' => 'Total Uploaded', 'value' => $stats['totalActionPoints'] ?? 0, 'icon' => 'M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3', 'color' => 'emerald'],
                ['label' => 'Pending Approval', 'value' => $stats['pendingApproval'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Approved', 'value' => $stats['approvedPoints'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Overdue', 'value' => $stats['overduePoints'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z', 'color' => 'rose'],
            ],
            'receptionist', 'call_center_agent', 'sales_manager', 'business_development_manager', 'sales_executive', 'crm_officer', 'marketing_officer', 'call_center_supervisor' => [
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'emerald'],
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'sky'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Total Contacts', 'value' => $stats['totalContacts'] ?? 0, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'emerald'],
            ],
            'logistics_officer', 'procurement_manager', 'procurement_officer', 'tender_officer', 'store_manager', 'storekeeper', 'inventory_controller', 'asset_officer' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'sky'],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'rose'],
            ],
            'technical_manager', 'ict_engineer', 'senior_systems_engineer', 'systems_engineer', 'support_engineer', 'noc_engineer', 'service_desk_manager', 'helpdesk_supervisor', 'helpdesk_officer', 'ict_administrator', 'network_engineer', 'software_engineer', 'cybersecurity_engineer' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
            ],
            'technician', 'field_technician' => [
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
            'project_manager', 'project_director', 'technical_projects_manager', 'project_coordinator', 'project_engineer', 'site_supervisor', 'project_accountant', 'team_leader' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'emerald'],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'amber'],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ],
            'operations_manager', 'operations_officer', 'fleet_manager' => [
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
            'employee_self_service', 'manager_self_service' => [
                ['label' => 'Pending Leaves', 'value' => $stats['myPendingLeaves'] ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                ['label' => 'Attendance (Month)', 'value' => $stats['myAttendanceThisMonth'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Latest Payslip', 'value' => $money($stats['myLatestPayroll'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'sky'],
                ['label' => 'My Tasks', 'value' => $stats['myTasks'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'violet'],
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

    /**
     * @deprecated superseded by the generic getQuickActionsForRole() above; kept for reference only.
     */
    private function legacyQuickActionsForRole(string $role): array
    {
        return match ($role) {
            'erp_super_administrator' => [
                ['label' => 'Manage Companies', 'route' => 'admin.companies.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
                ['label' => 'Manage Users', 'route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                ['label' => 'Intercompany', 'route' => 'admin.intercompany.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                ['label' => 'Consolidated', 'route' => 'admin.companies.consolidated', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ],
            'admin', 'administrator', 'admin_manager', 'erp_administrator' => [
                ['label' => 'Manage Users', 'route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
            ],
            'director' => [
                ['label' => 'View Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                ['label' => 'Sales Dashboard', 'route' => 'admin.sales-dashboard', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
            ],
            'finance_officer', 'finance_manager', 'chief_accountant', 'accountant', 'accounts_receivable_officer', 'accounts_payable_officer', 'payroll_officer', 'budget_officer', 'credit_controller' => [
                ['label' => 'Sales Invoices', 'route' => 'admin.sales-invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Purchase Invoices', 'route' => 'admin.purchase-invoices.index', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['label' => 'Revenues', 'route' => 'admin.revenues.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Bank Accounts', 'route' => 'admin.bank-accounts.index', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
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
            'technician', 'technical_manager', 'ict_officer', 'ict_engineer', 'ict_administrator', 'network_engineer', 'software_engineer', 'cybersecurity_engineer', 'field_technician' => [
                ['label' => 'Helpdesk', 'route' => 'admin.helpdesk-tickets.index', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
            ],
            'project_manager', 'team_leader' => [
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                ['label' => 'Timesheets', 'route' => 'admin.timesheets.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ],
            'employee_self_service', 'manager_self_service' => [
                ['label' => 'My Attendance', 'route' => 'admin.attendance.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                ['label' => 'My Leaves', 'route' => 'admin.leaves.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'My Payslips', 'route' => 'admin.payroll.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
            ],
            'sgr_agent' => [
                ['label' => 'Upload Action Points', 'route' => 'role.page', 'params' => ['module' => 'import-action-points'], 'icon' => 'M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3'],
                ['label' => 'My Reports', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports'], 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'My Account', 'route' => 'role.page', 'params' => ['module' => 'my-account'], 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label' => 'Payslips', 'route' => 'role.page', 'params' => ['module' => 'payslips'], 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
            ],
            'receptionist', 'call_center_agent' => [
                ['label' => 'Leads', 'route' => 'admin.crm-leads.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['label' => 'Contacts', 'route' => 'admin.crm-contacts.index', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label' => 'Payroll (Salary)', 'route' => 'admin.payroll.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
                ['label' => 'Contracts', 'route' => 'admin.crm-contracts.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                ['label' => 'Leaves', 'route' => 'admin.leaves.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                ['label' => 'Timetable', 'route' => 'admin.timesheets.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
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

    private function getRoleLabel(string $role): string
    {
        $labels = [
            'admin' => 'Administrator',
            'director' => 'Director',
            'admin_manager' => 'Admin Manager',
            'administrator' => 'Administrator',
            'finance_officer' => 'Finance Officer',
            'auditor' => 'Auditor',
            'hr_officer' => 'HR Officer',
            'legal_officer' => 'Legal Officer',
            'receptionist' => 'Receptionist',
            'sgr_agent' => 'SGR Agent',
            'logistics_officer' => 'Logistics Officer',
            'technical_manager' => 'Technical Manager',
            'technician' => 'Technician',
            'ict_officer' => 'ICT Officer',
            'project_manager' => 'Project Manager',
            'operations_manager' => 'Operations Manager',
            'call_center_agent' => 'Call Center Agent',
            'cashier' => 'Cashier',
            'supervisor' => 'Supervisor',
            'ict_engineer' => 'ICT Engineer',
            'managing_director' => 'Managing Director',
            'general_manager' => 'General Manager',
            'finance_manager' => 'Finance Manager',
            'chief_accountant' => 'Chief Accountant',
            'accountant' => 'Accountant',
            'accounts_receivable_officer' => 'Accounts Receivable Officer',
            'accounts_payable_officer' => 'Accounts Payable Officer',
            'payroll_officer' => 'Payroll Officer',
            'budget_officer' => 'Budget Officer',
            'credit_controller' => 'Credit Controller',
            'procurement_manager' => 'Procurement Manager',
            'procurement_officer' => 'Procurement Officer',
            'tender_officer' => 'Tender Officer',
            'store_manager' => 'Store Manager',
            'storekeeper' => 'Storekeeper',
            'inventory_controller' => 'Inventory Controller',
            'asset_officer' => 'Asset Officer',
            'sales_manager' => 'Sales Manager',
            'business_development_manager' => 'Business Development Manager',
            'sales_executive' => 'Sales Executive',
            'crm_officer' => 'CRM Officer',
            'marketing_officer' => 'Marketing Officer',
            'project_director' => 'Project Director',
            'project_manager' => 'Project Manager',
            'technical_projects_manager' => 'Technical Projects Manager',
            'project_coordinator' => 'Project Coordinator',
            'project_engineer' => 'Project Engineer',
            'site_supervisor' => 'Site Supervisor',
            'team_leader' => 'Team Leader',
            'project_accountant' => 'Project Accountant',
            'senior_systems_engineer' => 'Senior Systems Engineer',
            'systems_engineer' => 'Systems Engineer',
            'support_engineer' => 'Support Engineer / Field Technician',
            'noc_engineer' => 'NOC Engineer',
            'service_desk_manager' => 'Service Desk Manager',
            'helpdesk_supervisor' => 'Helpdesk Supervisor',
            'helpdesk_officer' => 'Helpdesk Officer',
            'call_center_supervisor' => 'Call Center Supervisor',
            'hr_manager' => 'HR Manager',
            'hr_officer' => 'HR Officer',
            'recruitment_officer' => 'Recruitment Officer',
            'training_officer' => 'Training Officer',
            'time_and_attendance_officer' => 'Time and Attendance Officer',
            'operations_officer' => 'Operations Officer',
            'fleet_manager' => 'Fleet Manager',
            'employee_self_service' => 'Employee Self-Service',
            'manager_self_service' => 'Manager Self-Service',
            'erp_super_administrator' => 'ERP Super Administrator',
            'erp_administrator' => 'ERP Administrator',
            'ict_administrator' => 'ICT Administrator',
            'network_engineer' => 'Network Engineer',
            'software_engineer' => 'Software Engineer',
            'cybersecurity_engineer' => 'Cybersecurity Engineer',
            'field_technician' => 'Field Technician',
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
            case 'erp_super_administrator':
                $data['title'] = 'New Users vs Companies (14 days)';
                $data['values'] = $this->dailyCountsForRange(User::class, 'created_at');
                $data['secondaryValues'] = $this->dailyCountsForRange(Company::class, 'created_at');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Companies';
                break;

            case 'admin':
            case 'administrator':
            case 'admin_manager':
            case 'director':
            case 'erp_administrator':
                $data['title'] = 'Sales vs Purchases (14 days)';
                $data['values'] = $this->dailySumsForRange(SalesInvoice::class, 'created_at', 'total_amount');
                $data['secondaryValues'] = $this->dailySumsForRange(PurchaseInvoice::class, 'created_at', 'total_amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Purchases';
                break;

            case 'finance_officer':
            case 'finance_manager':
            case 'chief_accountant':
            case 'accountant':
            case 'accounts_receivable_officer':
            case 'accounts_payable_officer':
            case 'payroll_officer':
            case 'budget_officer':
            case 'credit_controller':
            case 'auditor':
                $data['title'] = 'Revenue vs Expenses (14 days)';
                $data['values'] = $this->dailySumsForRange(Revenue::class, 'revenue_date', 'amount');
                $data['secondaryValues'] = $this->dailySumsForRange(Expense::class, 'expense_date', 'amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Expenses';
                break;

            case 'hr_officer':
            case 'supervisor':
                $data['title'] = 'Attendance Trend (14 days)';
                $data['values'] = $this->dailyCountsForRange(Attendance::class, 'date', fn($q) => $q->where('status', 'present'));
                break;

            case 'cashier':
                $data['title'] = 'POS Sales (14 days)';
                $data['values'] = $this->dailySumsForRange(PosSale::class, 'created_at', 'total_amount');
                break;

            case 'technical_manager':
            case 'ict_officer':
            case 'ict_engineer':
            case 'technician':
            case 'ict_administrator':
            case 'network_engineer':
            case 'software_engineer':
            case 'cybersecurity_engineer':
            case 'field_technician':
                $data['title'] = 'Tickets Created (14 days)';
                $data['values'] = $this->dailyCountsForRange(HelpdeskTicket::class, 'created_at');
                break;

            case 'sgr_agent':
                $data['title'] = 'Action Points Uploads (14 days)';
                $data['values'] = $this->dailyCountsForRange(\App\Models\SgrActionPoint::class, 'created_at');
                break;

            case 'receptionist':
            case 'call_center_agent':
                $data['title'] = 'New Leads (14 days)';
                $data['values'] = $this->dailyCountsForRange(CrmLead::class, 'created_at');
                break;

            case 'project_manager':
            case 'operations_manager':
            case 'team_leader':
                $data['title'] = 'Project Activity (14 days)';
                $data['values'] = $this->dailyCountsForRange(Project::class, 'updated_at');
                break;

            case 'logistics_officer':
                $data['title'] = 'Stock Movements (14 days)';
                $data['values'] = $this->dailyCountsForRange(\App\Models\StockMovement::class, 'created_at');
                break;

            case 'employee_self_service':
            case 'manager_self_service':
                $data['title'] = 'My Attendance Trend (14 days)';
                $emp = Employee::where('user_id', auth()->id())->first();
                if ($emp) {
                    $data['values'] = $this->dailyCountsForRange(Attendance::class, 'date', fn($q) => $q->where('employee_id', $emp->id)->where('status', 'present'));
                } else {
                    $data['values'] = array_fill(0, 14, 0);
                }
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
            'erp_super_administrator' => [
                ['label' => 'Group Companies', 'value' => $stats['groupCompanies'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'companies']],
                ['label' => 'Subsidiaries', 'value' => $stats['subsidiaryCompanies'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'companies']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
            ],
            'admin', 'administrator', 'admin_manager', 'erp_administrator' => [
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
            'finance_officer', 'finance_manager', 'chief_accountant', 'accountant', 'accounts_receivable_officer', 'accounts_payable_officer', 'payroll_officer', 'budget_officer', 'credit_controller' => [
                ['label' => 'Sales Paid', 'value' => $money($stats['salesPaid'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchases', 'value' => $money($stats['totalPurchases'] ?? 0), 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Purchase Balance', 'value' => $money($stats['purchaseBalance'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Month Revenue', 'value' => $money($stats['monthRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'Draft Invoices', 'value' => $stats['draftInvoices'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
            ],
            'auditor' => [
                ['label' => 'Sales Invoices', 'value' => $stats['salesInvoiceCount'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchase Invoices', 'value' => $stats['purchaseInvoiceCount'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Expense Records', 'value' => $stats['expenseCount'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'expenses']],
                ['label' => 'Revenue Records', 'value' => $stats['revenueCount'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'POS Sales', 'value' => $money($stats['posSales'] ?? 0), 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'pos']],
                ['label' => 'Overdue', 'value' => $stats['overdueInvoices'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
            ],
            'hr_officer' => [
                ['label' => 'Active Employees', 'value' => $stats['activeEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'Total Payroll', 'value' => $money($stats['totalPayroll'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'payroll']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
            ],
            'legal_officer' => [
                ['label' => 'Total Contracts', 'value' => $stats['totalContracts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'contracts']],
                ['label' => 'Total Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
            ],
            'sgr_agent' => [
                ['label' => 'Total Uploaded', 'value' => $stats['totalActionPoints'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
                ['label' => 'Pending', 'value' => $stats['pendingApproval'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
                ['label' => 'Approved', 'value' => $stats['approvedPoints'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
                ['label' => 'Overdue', 'value' => $stats['overduePoints'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
                ['label' => 'Completed', 'value' => $stats['completedPoints'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
                ['label' => 'Rejected', 'value' => $stats['rejectedPoints'] ?? 0, 'color' => 'gray', 'route' => 'role.page', 'params' => ['module' => 'action-points-reports']],
            ],
            'receptionist', 'call_center_agent' => [
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Contacts', 'value' => $stats['totalContacts'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'contacts']],
            ],
            'logistics_officer' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'transfers']],
            ],
            'technical_manager', 'ict_engineer', 'ict_administrator', 'network_engineer', 'software_engineer', 'cybersecurity_engineer' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'technician', 'field_technician' => [
                ['label' => 'My Tickets', 'value' => $stats['myTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Open', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
            ],
            'ict_officer' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'project_manager', 'team_leader' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'employee_self_service', 'manager_self_service' => [
                ['label' => 'Pending Leaves', 'value' => $stats['myPendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Approved Leaves', 'value' => $stats['myApprovedLeaves'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Attendance (Month)', 'value' => $stats['myAttendanceThisMonth'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'My Tasks', 'value' => $stats['myTasks'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'projects']],
            ],
            'operations_manager' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Sales', 'value' => $stats['totalSales'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchases', 'value' => $stats['totalPurchases'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'cashier' => [
                ['label' => "Today's Count", 'value' => $stats['todayCount'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'pos']],
                ['label' => 'Month Sales', 'value' => $money($stats['monthSales'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'pos']],
                ['label' => 'Invoices', 'value' => $stats['totalInvoices'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
            ],
            'supervisor' => [
                ['label' => 'Present Today', 'value' => $stats['presentToday'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'POS Sales', 'value' => $money($stats['todaySales'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'pos']],
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'products']],
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
