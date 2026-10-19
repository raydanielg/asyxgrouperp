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

            // Keep every dashboard link inside the role's allowed modules, and
            // never show payroll amounts to roles without payroll permission.
            if (!$user->isAdmin()) {
                $allowed = \App\Support\RoleModules::allowedModules($role, $user);
                $secondaryKpis = array_values(array_filter($secondaryKpis, function ($kpi) use ($allowed) {
                    if (($kpi['route'] ?? null) === 'role.page' && isset($kpi['params']['module'])) {
                        return in_array($kpi['params']['module'], $allowed, true);
                    }
                    return true;
                }));
                if (!$user->hasPermission('view-payroll')) {
                    $stripPayroll = fn($cards) => array_values(array_filter(
                        $cards,
                        fn($c) => !str_contains(strtolower($c['label'] ?? ''), 'payroll')
                    ));
                    $kpiCards = $stripPayroll($kpiCards);
                    $secondaryKpis = $stripPayroll($secondaryKpis);
                }
            }

            $money = fn($n) => 'TZS ' . number_format($n);

            $companies = null;
            $systemMode = null;

            // Salary advance summary for finance roles
            $salaryAdvancePending = 0;
            $salaryAdvanceApproved = 0;
            $salaryAdvanceTotal = 0;
            $recentSalaryAdvances = collect();
            if ($this->profileFor($role) === 'finance') {
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
        if ($user->isSuperAdmin()) return 'superadmin';
        if ($user->isAdmin()) return 'admin';

        // Check role_user pivot
        $role = $user->roles()->first();
        if ($role) return $role->name;

        // Fallback to user role column
        return $user->role ?? 'user';
    }

    /**
     * Groups the ~60 spec roles into dashboard profiles so every role gets a
     * meaningful dashboard without duplicating stat logic per role.
     */
    private function profileFor(string $role): string
    {
        return match ($role) {
            'superadmin' => 'superadmin',
            'admin' => 'admin',
            'managing_director', 'director', 'general_manager' => 'executive',
            'finance_manager', 'chief_accountant', 'accountant',
            'accounts_receivable_officer', 'accounts_payable_officer', 'cashier',
            'payroll_officer', 'budget_officer', 'credit_controller',
            'project_accountant', 'auditor' => 'finance',
            'hr_manager', 'hr_officer', 'recruitment_officer',
            'training_officer', 'time_attendance_officer' => 'hr',
            'procurement_manager', 'procurement_officer', 'tender_officer' => 'procurement',
            'store_manager', 'storekeeper', 'inventory_controller',
            'asset_officer' => 'inventory',
            'sales_manager', 'business_development_manager', 'sales_executive',
            'crm_officer', 'marketing_officer' => 'sales',
            'project_director', 'project_manager', 'technical_projects_manager',
            'project_coordinator', 'project_engineer', 'site_supervisor',
            'team_leader' => 'projects',
            'technical_manager', 'technician', 'senior_systems_engineer',
            'systems_engineer', 'network_engineer', 'software_engineer',
            'cybersecurity_engineer', 'support_engineer', 'field_technician',
            'noc_engineer', 'ict_administrator' => 'technical',
            'service_desk_manager', 'helpdesk_supervisor', 'helpdesk_officer',
            'call_center_supervisor', 'call_center_agent', 'receptionist' => 'service_desk',
            'operations_manager', 'operations_officer', 'fleet_manager',
            'logistics_officer' => 'operations',
            default => 'self_service',
        };
    }

    private function getStatsForRole(string $role): array
    {
        $stats = [];

        switch ($this->profileFor($role)) {
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
                    'totalCompanies' => \App\Models\Company::count(),
                    'totalProducts' => Product::count(),
                    'totalLeads' => CrmLead::count(),
                    'totalDeals' => CrmDeal::count(),
                    'totalProposals' => SalesProposal::count(),
                    'totalWarehouses' => Warehouse::count(),
                    'totalPosSales' => PosSale::count(),
                    'totalOrders' => Order::count(),
                    'monthRevenues' => Revenue::whereMonth('revenue_date', now()->month)->sum('amount') ?? 0,
                    'monthExpenses' => Expense::whereMonth('expense_date', now()->month)->sum('amount') ?? 0,
                    'salesBalance' => SalesInvoice::sum('balance_amount') ?? 0,
                    'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
                    'completedProjects' => Project::where('status', 'completed')->count(),
                    'activeEmployees' => Employee::where('status', 'active')->count(),
                    'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                    'newLeads' => CrmLead::where('status', 'new')->count(),
                    'qualifiedLeads' => CrmLead::where('status', 'qualified')->count(),
                    'openDeals' => CrmDeal::where('status', 'open')->count(),
                    'totalDealValue' => CrmDeal::sum('value') ?? 0,
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'pendingTransfers' => \App\Models\Transfer::where('status', 'pending')->count() ?? 0,
                    'totalSalesReturns' => \App\Models\SalesInvoiceReturn::count() ?? 0,
                    'totalPurchaseReturns' => \App\Models\PurchaseReturn::count() ?? 0,
                    'totalTransfers' => \App\Models\Transfer::count() ?? 0,
                    'overdueInvoices' => SalesInvoice::where('due_date', '<', now())->where('status', '!=', 'paid')->count(),
                ];
                break;

            case 'admin':
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

            case 'executive':
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
                    'totalCompanies' => \App\Models\Company::count(),
                    'totalLeads' => CrmLead::count(),
                    'openDeals' => CrmDeal::where('status', 'open')->count(),
                    'totalDealValue' => CrmDeal::sum('value') ?? 0,
                    'monthRevenues' => Revenue::whereMonth('revenue_date', now()->month)->sum('amount') ?? 0,
                    'monthExpenses' => Expense::whereMonth('expense_date', now()->month)->sum('amount') ?? 0,
                    'overdueInvoices' => SalesInvoice::where('due_date', '<', now())->where('status', '!=', 'paid')->count(),
                    'totalTenders' => \App\Models\Tender::count(),
                    'totalContracts' => \App\Models\CrmContract::count(),
                ];
                break;

            case 'finance':
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

            case 'hr':
                $stats = [
                    'totalEmployees' => Employee::count(),
                    'activeEmployees' => Employee::where('status', 'active')->count(),
                    'pendingLeaves' => Leave::where('status', 'pending')->count(),
                    'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                    'absentToday' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
                    'totalPayroll' => Employee::sum('salary') ?? 0,
                ];
                break;

            case 'procurement':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'activeWarehouses' => Warehouse::where('is_active', true)->count(),
                    'pendingTransfers' => \App\Models\Transfer::where('status', 'pending')->count(),
                    'totalSuppliers' => \App\Models\Supplier::count(),
                    'pendingLpos' => \App\Models\Lpo::where('status', 'pending')->count(),
                    'totalLpos' => \App\Models\Lpo::count(),
                    'totalGrns' => \App\Models\Grn::count(),
                    'totalTenders' => \App\Models\Tender::count(),
                ];
                break;

            case 'inventory':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'activeWarehouses' => Warehouse::where('is_active', true)->count(),
                    'pendingTransfers' => \App\Models\Transfer::where('status', 'pending')->count(),
                    'totalAssets' => \App\Models\FixedAsset::count(),
                    'totalMovements' => \App\Models\StockMovement::count(),
                    'totalGrns' => \App\Models\Grn::count(),
                ];
                break;

            case 'technical':
                $stats = [
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'totalEmployees' => Employee::count(),
                    'openJobCards' => \App\Models\JobCard::where('status', 'open')->count(),
                    'totalAssets' => \App\Models\FixedAsset::count(),
                ];
                break;

            case 'service_desk':
                $stats = [
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'inProgressTickets' => HelpdeskTicket::where('status', 'in_progress')->count(),
                    'resolvedTickets' => HelpdeskTicket::where('status', 'resolved')->count(),
                    'totalTickets' => HelpdeskTicket::count(),
                    'todayCalls' => \App\Models\Call::whereDate('call_time', today())->count(),
                    'weekCalls' => \App\Models\Call::whereBetween('call_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'todayVisitors' => \App\Models\Visitor::whereDate('check_in_at', today())->count(),
                    'pendingAppointments' => \App\Models\Appointment::where('status', 'scheduled')->count(),
                ];
                break;

            case 'projects':
                $stats = [
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'completedProjects' => Project::where('status', 'completed')->count(),
                    'openDeals' => CrmDeal::where('status', 'open')->count(),
                    'totalDealValue' => CrmDeal::where('status', 'open')->sum('value') ?? 0,
                    'totalEmployees' => Employee::count(),
                    'myTasks' => \App\Models\ProjectTask::where('assigned_to', auth()->id())->count(),
                    'openBugs' => \App\Models\ProjectBug::where('status', 'open')->count(),
                ];
                break;

            case 'operations':
                $stats = [
                    'totalProducts' => Product::count(),
                    'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                    'totalWarehouses' => Warehouse::count(),
                    'totalSales' => SalesInvoice::count(),
                    'totalPurchases' => PurchaseInvoice::count(),
                    'totalProjects' => Project::count(),
                    'activeProjects' => Project::where('status', 'in_progress')->count(),
                    'totalEmployees' => Employee::count(),
                    'totalVehicles' => \App\Models\Vehicle::count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                ];
                break;

            case 'sales':
                $stats = [
                    'totalLeads' => CrmLead::count(),
                    'newLeads' => CrmLead::where('status', 'new')->count(),
                    'qualifiedLeads' => CrmLead::where('status', 'qualified')->count(),
                    'myLeads' => CrmLead::where('assigned_to', auth()->id())->count(),
                    'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                    'openDeals' => CrmDeal::where('status', 'open')->count(),
                    'totalDealValue' => CrmDeal::where('status', 'open')->sum('value') ?? 0,
                    'totalQuotations' => \App\Models\Quotation::count(),
                    'totalContacts' => \App\Models\CrmContact::count(),
                ];
                break;

            default:
                $stats = [
                    'totalProjects' => Project::count(),
                    'myTasks' => \App\Models\ProjectTask::where('assigned_to', auth()->id())->count(),
                    'myPendingLeaves' => Leave::where('employee_id', auth()->user()->employee?->id)->where('status', 'pending')->count(),
                    'announcements' => \App\Models\Announcement::where('status', 'active')->count(),
                ];
                break;
        }

        return $stats;
    }

    private function getRecentItemsForRole(string $role): array
    {
        $items = [];

        switch ($this->profileFor($role)) {
            case 'superadmin':
                $items['recentUsers'] = User::with('roles')->latest()->take(5)->get();
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                $items['recentExpenses'] = Expense::latest()->take(5)->get();
                $items['recentRevenues'] = Revenue::latest()->take(5)->get();
                $items['recentPurchases'] = PurchaseInvoice::with('vendor')->latest()->take(5)->get();
                $items['recentLeads'] = CrmLead::latest()->take(5)->get();
                $items['openDeals'] = CrmDeal::where('status', 'open')->latest()->take(5)->get();
                $items['lowStockProducts'] = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->take(5)->get();
                $items['pendingLeaves'] = Leave::where('status', 'pending')->latest()->take(5)->get();
                $items['recentAttendance'] = Attendance::with('employee')->whereDate('date', today())->latest()->take(5)->get();
                $items['recentTransfers'] = \App\Models\Transfer::latest()->take(5)->get();
                break;

            case 'admin':
            case 'executive':
                $items['recentUsers'] = User::with('roles')->latest()->take(5)->get();
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'finance':
                $items['recentSales'] = SalesInvoice::with('customer')->latest()->take(5)->get();
                $items['recentExpenses'] = Expense::latest()->take(5)->get();
                $items['recentRevenues'] = Revenue::latest()->take(5)->get();
                break;

            case 'hr':
                $items['recentEmployees'] = Employee::latest()->take(5)->get();
                $items['pendingLeaves'] = Leave::where('status', 'pending')->latest()->take(5)->get();
                break;

            case 'procurement':
            case 'inventory':
            case 'operations':
                $items['lowStockProducts'] = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->take(5)->get();
                $items['recentTransfers'] = \App\Models\Transfer::latest()->take(5)->get();
                break;

            case 'technical':
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                break;

            case 'service_desk':
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                $items['recentTickets'] = HelpdeskTicket::latest()->take(5)->get();
                break;

            case 'projects':
                $items['activeProjects'] = Project::where('status', 'in_progress')->latest()->take(5)->get();
                $items['openDeals'] = CrmDeal::where('status', 'open')->latest()->take(5)->get();
                break;

            case 'sales':
                $items['recentLeads'] = CrmLead::latest()->take(5)->get();
                $items['openTickets'] = HelpdeskTicket::where('status', 'open')->latest()->take(5)->get();
                break;
        }

        return $items;
    }

    private function getKpiCardsForRole(string $role, array $stats): array
    {
        $money = fn($n) => 'TZS ' . number_format($n);

        return match ($this->profileFor($role)) {
            'superadmin' => [
                ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald'],
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'sky'],
                ['label' => 'Total Expenses', 'value' => $money($stats['totalExpenses'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'admin' => [
                ['label' => 'Total Revenue', 'value' => $money($stats['totalRevenues'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ['label' => 'Total Expenses', 'value' => $money($stats['totalExpenses'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'rose'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'sky'],
            ],
            'executive' => [
                ['label' => 'Group Revenue', 'value' => $money($stats['totalRevenues'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ['label' => 'Outstanding Receivables', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'rose'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'sky'],
                ['label' => 'Companies', 'value' => $stats['totalCompanies'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'violet'],
            ],
            'finance' => [
                ['label' => 'Total Sales', 'value' => $money($stats['totalSales'] ?? 0), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'emerald'],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'rose'],
                ['label' => 'Month Expenses', 'value' => $money($stats['monthExpenses'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Overdue Invoices', 'value' => $stats['overdueInvoices'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ],
            'hr' => [
                ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'emerald'],
                ['label' => 'Active', 'value' => $stats['activeEmployees'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                ['label' => 'Present Today', 'value' => $stats['todayAttendance'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'emerald'],
            ],
            'procurement' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Pending LPOs', 'value' => $stats['pendingLpos'] ?? 0, 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'sky'],
                ['label' => 'Suppliers', 'value' => $stats['totalSuppliers'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'rose'],
            ],
            'inventory' => [
                ['label' => 'Total Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color' => 'sky'],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'rose'],
            ],
            'service_desk' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Calls Today', 'value' => $stats['todayCalls'] ?? 0, 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'color' => 'sky'],
            ],
            'sales' => [
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'emerald'],
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'sky'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'My Leads', 'value' => $stats['myLeads'] ?? 0, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'emerald'],
            ],
            'technical' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
            ],
            'projects' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'emerald'],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'sky'],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'amber'],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ],
            'operations' => [
                ['label' => 'Total Vehicles', 'value' => $stats['totalVehicles'] ?? 0, 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'emerald'],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'amber'],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2', 'color' => 'sky'],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
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
        return \App\Http\Controllers\RolePageController::roleLabels()[$role]
            ?? ucwords(str_replace('_', ' ', $role));
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

        switch ($this->profileFor($role)) {
            case 'superadmin':
                $data['title'] = 'Revenue vs Expenses (14 days)';
                $data['values'] = $this->dailySumsForRange(Revenue::class, 'revenue_date', 'amount');
                $data['secondaryValues'] = $this->dailySumsForRange(Expense::class, 'expense_date', 'amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Expenses';
                break;

            case 'admin':
            case 'executive':
                $data['title'] = 'Sales vs Purchases (14 days)';
                $data['values'] = $this->dailySumsForRange(SalesInvoice::class, 'created_at', 'total_amount');
                $data['secondaryValues'] = $this->dailySumsForRange(PurchaseInvoice::class, 'created_at', 'total_amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Purchases';
                break;

            case 'finance':
                $data['title'] = 'Revenue vs Expenses (14 days)';
                $data['values'] = $this->dailySumsForRange(Revenue::class, 'revenue_date', 'amount');
                $data['secondaryValues'] = $this->dailySumsForRange(Expense::class, 'expense_date', 'amount');
                $data['secondaryLabels'] = $data['labels'];
                $data['secondaryTitle'] = 'Expenses';
                break;

            case 'hr':
                $data['title'] = 'Attendance Trend (14 days)';
                $data['values'] = $this->dailyCountsForRange(Attendance::class, 'date', fn($q) => $q->where('status', 'present'));
                break;

            case 'technical':
            case 'service_desk':
                $data['title'] = 'Tickets Created (14 days)';
                $data['values'] = $this->dailyCountsForRange(HelpdeskTicket::class, 'created_at');
                break;

            case 'sales':
                $data['title'] = 'New Leads (14 days)';
                $data['values'] = $this->dailyCountsForRange(CrmLead::class, 'created_at');
                break;

            case 'projects':
            case 'operations':
                $data['title'] = 'Project Activity (14 days)';
                $data['values'] = $this->dailyCountsForRange(Project::class, 'updated_at');
                break;

            case 'procurement':
            case 'inventory':
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

        return match ($this->profileFor($role)) {
            'superadmin' => [
                ['label' => 'Companies', 'value' => $stats['totalCompanies'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'companies']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Leads', 'value' => $stats['totalLeads'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'Deals', 'value' => $stats['totalDeals'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'POS Sales', 'value' => $stats['totalPosSales'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'pos']],
                ['label' => 'Orders', 'value' => $stats['totalOrders'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'orders']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Overdue', 'value' => $stats['overdueInvoices'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'Month Revenue', 'value' => $money($stats['monthRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'Month Expenses', 'value' => $money($stats['monthExpenses'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'expenses']],
                ['label' => 'Outstanding', 'value' => $money($stats['salesBalance'] ?? 0), 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Today Present', 'value' => $stats['todayAttendance'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
            ],
            'admin' => [
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
            ],
            'executive' => [
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Proposals', 'value' => $stats['totalProposals'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-proposals']],
                ['label' => 'Tenders', 'value' => $stats['totalTenders'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'tenders']],
                ['label' => 'Contracts', 'value' => $stats['totalContracts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'contracts']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Companies', 'value' => $stats['totalCompanies'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'companies']],
            ],
            'finance' => [
                ['label' => 'Sales Paid', 'value' => $money($stats['salesPaid'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Purchases', 'value' => $money($stats['totalPurchases'] ?? 0), 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Purchase Balance', 'value' => $money($stats['purchaseBalance'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'purchase-invoices']],
                ['label' => 'Month Revenue', 'value' => $money($stats['monthRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
                ['label' => 'Draft Invoices', 'value' => $stats['draftInvoices'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Total Revenues', 'value' => $money($stats['totalRevenues'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'revenues']],
            ],
            'hr' => [
                ['label' => 'Active Employees', 'value' => $stats['activeEmployees'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'employees']],
                ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'attendance']],
                ['label' => 'Total Payroll', 'value' => $money($stats['totalPayroll'] ?? 0), 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'payroll']],
                ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'leaves']],
            ],
            'procurement' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Pending LPOs', 'value' => $stats['pendingLpos'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'lpos']],
                ['label' => 'GRNs', 'value' => $stats['totalGrns'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'grns']],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'transfers']],
            ],
            'inventory' => [
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Low Stock', 'value' => $stats['lowStockProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Pending Transfers', 'value' => $stats['pendingTransfers'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'transfers']],
            ],
            'service_desk' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Calls Today', 'value' => $stats['todayCalls'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'call-statistics']],
            ],
            'sales' => [
                ['label' => 'Total Leads', 'value' => $stats['totalLeads'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'New Leads', 'value' => $stats['newLeads'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'My Leads', 'value' => $stats['myLeads'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'leads']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
            ],
            'technical' => [
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'In Progress', 'value' => $stats['inProgressTickets'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Resolved', 'value' => $stats['resolvedTickets'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Job Cards', 'value' => $stats['openJobCards'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'job-cards']],
            ],
            'projects' => [
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Completed', 'value' => $stats['completedProjects'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Open Deals', 'value' => $stats['openDeals'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Deal Value', 'value' => $money($stats['totalDealValue'] ?? 0), 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'deals']],
                ['label' => 'Employees', 'value' => $stats['totalEmployees'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'employees']],
            ],
            'operations' => [
                ['label' => 'Vehicles', 'value' => $stats['totalVehicles'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'vehicles']],
                ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'color' => 'amber', 'route' => 'role.page', 'params' => ['module' => 'products']],
                ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'] ?? 0, 'color' => 'sky', 'route' => 'role.page', 'params' => ['module' => 'warehouses']],
                ['label' => 'Sales', 'value' => $stats['totalSales'] ?? 0, 'color' => 'emerald', 'route' => 'role.page', 'params' => ['module' => 'sales-invoices']],
                ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'color' => 'violet', 'route' => 'role.page', 'params' => ['module' => 'projects']],
                ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'color' => 'rose', 'route' => 'role.page', 'params' => ['module' => 'tickets']],
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
