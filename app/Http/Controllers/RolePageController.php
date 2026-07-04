<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContact;
use App\Models\CrmContract;
use App\Models\Project;
use App\Models\HelpdeskTicket;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\PosSale;
use App\Models\Attendance;
use App\Models\Visitor;
use App\Models\Appointment;
use App\Models\Call;
use App\Models\Correspondence;
use App\Models\Parcel;
use App\Models\FrontDesk;
use App\Models\Department;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\SalaryAdvanceRequest;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Transfer;
use App\Models\Bill;
use App\Models\BankAccount;
use App\Models\BankTransferAcc;
use Illuminate\Http\Request;

class RolePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getUserRole($user): string
    {
        if ($user->isAdmin()) return 'admin';
        $role = $user->roles()->first();
        if ($role) return $role->name;
        return $user->role ?? 'user';
    }

    private function getRoleLabel(string $role): string
    {
        $labels = [
            'admin' => 'Administrator', 'director' => 'Director',
            'admin_manager' => 'Admin Manager', 'administrator' => 'Administrator',
            'finance_officer' => 'Finance Officer', 'auditor' => 'Auditor',
            'hr_officer' => 'HR Officer', 'legal_officer' => 'Legal Officer',
            'receptionist' => 'Receptionist', 'logistics_officer' => 'Logistics Officer',
            'technical_manager' => 'Technical Manager', 'technician' => 'Technician',
            'ict_officer' => 'ICT Officer', 'project_manager' => 'Project Manager',
            'operations_manager' => 'Operations Manager', 'call_center_agent' => 'Call Center Agent',
            'cashier' => 'Cashier', 'supervisor' => 'Supervisor', 'ict_engineer' => 'ICT Engineer',
            'managing_director' => 'Managing Director', 'general_manager' => 'General Manager',
            'finance_manager' => 'Finance Manager', 'chief_accountant' => 'Chief Accountant',
            'accountant' => 'Accountant', 'accounts_receivable_officer' => 'Accounts Receivable Officer',
            'accounts_payable_officer' => 'Accounts Payable Officer', 'payroll_officer' => 'Payroll Officer',
            'budget_officer' => 'Budget Officer', 'credit_controller' => 'Credit Controller',
            'procurement_manager' => 'Procurement Manager', 'procurement_officer' => 'Procurement Officer',
            'tender_officer' => 'Tender Officer', 'store_manager' => 'Store Manager',
            'storekeeper' => 'Storekeeper', 'inventory_controller' => 'Inventory Controller',
            'asset_officer' => 'Asset Officer', 'sales_manager' => 'Sales Manager',
            'business_development_manager' => 'Business Development Manager',
            'sales_executive' => 'Sales Executive', 'crm_officer' => 'CRM Officer',
            'marketing_officer' => 'Marketing Officer', 'project_director' => 'Project Director',
            'technical_projects_manager' => 'Technical Projects Manager',
            'project_coordinator' => 'Project Coordinator', 'project_engineer' => 'Project Engineer',
            'site_supervisor' => 'Site Supervisor', 'team_leader' => 'Team Leader',
            'project_accountant' => 'Project Accountant', 'senior_systems_engineer' => 'Senior Systems Engineer',
            'systems_engineer' => 'Systems Engineer', 'support_engineer' => 'Support Engineer / Field Technician',
            'noc_engineer' => 'NOC Engineer', 'service_desk_manager' => 'Service Desk Manager',
            'helpdesk_supervisor' => 'Helpdesk Supervisor', 'helpdesk_officer' => 'Helpdesk Officer',
            'call_center_supervisor' => 'Call Center Supervisor', 'recruitment_officer' => 'Recruitment Officer',
            'training_officer' => 'Training Officer', 'time_and_attendance_officer' => 'Time and Attendance Officer',
            'operations_officer' => 'Operations Officer', 'fleet_manager' => 'Fleet Manager',
            'employee_self_service' => 'Employee Self-Service', 'manager_self_service' => 'Manager Self-Service',
            'erp_super_administrator' => 'ERP Super Administrator', 'erp_administrator' => 'ERP Administrator',
            'ict_administrator' => 'ICT Administrator', 'network_engineer' => 'Network Engineer',
            'software_engineer' => 'Software Engineer', 'cybersecurity_engineer' => 'Cybersecurity Engineer',
            'field_technician' => 'Field Technician',
        ];
        return $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    private function roleSlug(string $role): string
    {
        return str_replace('_', '-', $role);
    }

    private function getAllowedModulesForRole(string $role): array
    {
        return match ($role) {
            'managing_director' => ['dashboard', 'companies', 'reports', 'approvals', 'tenders', 'contracts', 'employees', 'my-account', 'payslips', 'salary'],
            'general_manager' => ['dashboard', 'reports', 'approvals', 'projects', 'leads', 'employees', 'my-account'],
            'technical_manager' => ['dashboard', 'tickets', 'projects', 'timesheets', 'bugs', 'lpos', 'assets', 'employees', 'my-account'],
            'operations_manager' => ['dashboard', 'products', 'warehouses', 'stock-movements', 'sales-invoices', 'purchase-invoices', 'projects', 'reports', 'my-account'],

            'finance_manager' => ['dashboard', 'journal-entries', 'purchase-invoices', 'sales-invoices', 'bank-accounts', 'budgets', 'reports', 'approvals', 'payroll', 'my-account'],
            'chief_accountant' => ['journal-entries', 'bank-reconciliation', 'reports', 'tax-management', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'my-account'],
            'accountant' => ['dashboard', 'journal-entries', 'sales-invoices', 'purchase-invoices', 'expenses', 'cost-centres', 'my-account'],
            'accounts_receivable_officer' => ['sales-invoices', 'receivables-aging', 'revenues', 'credit-notes', 'my-account'],
            'accounts_payable_officer' => ['purchase-invoices', 'acc-transfers', 'payables-aging', 'bills', 'my-account'],
            'payroll_officer' => ['payroll', 'salary-records', 'deductions', 'payslips', 'employees', 'my-account'],
            'budget_officer' => ['budgets', 'budget-vs-actual', 'cost-centres', 'reports', 'expenses', 'my-account'],
            'credit_controller' => ['credit-limits', 'overdue-accounts', 'collections', 'sales-invoices', 'my-account'],

            'procurement_manager' => ['dashboard', 'suppliers', 'rfqs', 'approvals', 'reports', 'lpos', 'purchase-requisitions', 'my-account'],
            'procurement_officer' => ['rfqs', 'purchase-requisitions', 'lpos', 'grns', 'suppliers', 'my-account'],
            'tender_officer' => ['tenders', 'tender-calendar', 'documents', 'tender-costing', 'my-account'],

            'store_manager' => ['dashboard', 'warehouses', 'transfers', 'reorder-levels', 'reports', 'stock-movements', 'products', 'suppliers', 'my-account'],
            'storekeeper' => ['stock-movements', 'grns', 'stock-count', 'products', 'my-account'],
            'inventory_controller' => ['products', 'batch-tracking', 'barcodes', 'reports', 'warehouses', 'my-account'],
            'asset_officer' => ['assets', 'asset-assignment', 'asset-maintenance', 'asset-disposal', 'employees', 'my-account'],

            'sales_manager' => ['dashboard', 'deals', 'sales-forecast', 'quotations', 'reports', 'crm-leads', 'sales-invoices', 'my-account'],
            'business_development_manager' => ['leads', 'deals', 'market-analysis', 'my-account'],
            'sales_executive' => ['leads', 'deals', 'quotations', 'calls', 'my-account'],
            'crm_officer' => ['contacts', 'calls', 'correspondence', 'leads', 'deals', 'my-account'],
            'marketing_officer' => ['campaigns', 'lead-source-reports', 'documents', 'my-account'],

            'project_director' => ['dashboard', 'reports', 'projects', 'budgets', 'timesheets', 'my-account'],
            'project_manager' => ['dashboard', 'projects', 'timesheets', 'bugs', 'employees', 'deals', 'my-account'],
            'technical_projects_manager' => ['projects', 'resource-allocation', 'milestones', 'timesheets', 'bugs', 'my-account'],
            'project_coordinator' => ['tasks', 'documents', 'meetings', 'projects', 'my-account'],
            'project_engineer' => ['tasks', 'site-reports', 'timesheets', 'projects', 'my-account'],
            'site_supervisor' => ['attendance', 'site-reports', 'incidents', 'tickets', 'my-account'],
            'team_leader' => ['team-tasks', 'team-attendance', 'team-timesheets', 'my-account'],
            'project_accountant' => ['budget-vs-actual', 'sales-invoices', 'cost-centres', 'expenses', 'revenues', 'my-account'],

            'senior_systems_engineer' => ['projects', 'documents', 'team-review', 'tickets', 'my-account'],
            'systems_engineer' => ['tickets', 'assets', 'asset-maintenance', 'my-account'],
            'support_engineer' => ['site-visits', 'service-reports', 'assets', 'tickets', 'my-account'],
            'noc_engineer' => ['dashboard', 'tickets', 'escalations', 'assets', 'my-account'],

            'service_desk_manager' => ['dashboard', 'tickets', 'sla-reports', 'reports', 'call-logs', 'my-account'],
            'helpdesk_supervisor' => ['tickets', 'reports', 'escalations', 'call-logs', 'my-account'],
            'helpdesk_officer' => ['tickets', 'knowledge-base', 'calls', 'my-account'],
            'call_center_supervisor' => ['call-statistics', 'shift-schedule', 'sla-reports', 'my-account'],
            'call_center_agent' => ['call-logs', 'leads', 'tickets', 'my-account'],

            'hr_manager' => ['dashboard', 'employees', 'recruitment', 'leaves', 'payroll', 'disciplinary', 'performance', 'my-account'],
            'hr_officer' => ['dashboard', 'employees', 'attendance', 'leaves', 'performance', 'training', 'recruitment', 'assets', 'policies', 'my-account'],
            'recruitment_officer' => ['job-postings', 'applications', 'onboarding', 'my-account'],
            'training_officer' => ['training', 'training-records', 'certifications', 'employees', 'my-account'],
            'time_and_attendance_officer' => ['dashboard', 'attendance', 'shift-schedule', 'overtime', 'employees', 'my-account'],

            'operations_officer' => ['operations-log', 'operations-tasks', 'helpdesk-tickets', 'my-account'],
            'fleet_manager' => ['vehicles', 'driver-assignment', 'fuel-logs', 'trip-schedule', 'my-account'],
            'logistics_officer' => ['deliveries', 'shipments', 'route-planning', 'my-account'],

            'employee_self_service' => ['my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'announcements'],
            'manager_self_service' => ['my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'team-overview', 'team-leaves', 'team-timesheets', 'announcements'],

            'director' => ['reports', 'projects', 'sales-dashboard', 'employees', 'sales-invoices', 'purchase-invoices', 'expenses', 'tickets'],
            'finance_officer' => ['sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'bank-accounts', 'transfers', 'salary-advance', 'reports'],
            'hr_officer' => ['employees', 'attendance', 'payroll', 'leaves', 'performance', 'training', 'recruitment', 'assets', 'policies'],
            'auditor' => ['sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'bank-accounts', 'reports', 'warehouses', 'products', 'stock-movements', 'pos'],
            'admin_manager' => ['users', 'roles', 'employees', 'attendance', 'leaves', 'reports', 'settings'],
            'cashier' => ['pos', 'pos-reports', 'sales-invoices', 'products', 'revenues'],
            'technical_manager' => ['tickets', 'projects', 'timesheets', 'bugs', 'employees'],
            'technician' => ['tickets', 'projects', 'timesheets', 'bugs'],
            'ict_officer' => ['tickets', 'projects', 'bugs', 'assets', 'employees'],
            'ict_engineer' => ['tickets', 'projects', 'bugs', 'assets', 'settings'],
            'project_manager' => ['projects', 'timesheets', 'bugs', 'deals', 'reports'],
            'operations_manager' => ['products', 'warehouses', 'stock-movements', 'sales-invoices', 'purchase-invoices', 'projects', 'reports'],
            'logistics_officer' => ['products', 'warehouses', 'stock-movements', 'suppliers', 'inventory-transfers', 'purchase-invoices'],
            'receptionist' => ['visitors', 'appointments', 'calls', 'correspondence', 'parcels', 'front-desk', 'departments', 'announcements', 'messages', 'salary-advance', 'reports', 'my-account'],
            'call_center_agent' => ['leads', 'contacts', 'tickets'],
            'legal_officer' => ['contracts', 'contacts', 'projects', 'reports'],
            'supervisor' => ['employees', 'attendance', 'leaves', 'projects', 'pos', 'products', 'reports'],
            'administrator' => ['users', 'roles', 'employees', 'projects', 'products', 'settings', 'reports'],

            'erp_super_administrator' => ['dashboard', 'users', 'roles', 'employees', 'projects', 'products', 'settings', 'reports', 'my-account'],
            'erp_administrator' => ['dashboard', 'users', 'roles', 'employees', 'attendance', 'leaves', 'reports', 'settings', 'my-account'],
            'ict_administrator' => ['dashboard', 'tickets', 'projects', 'assets', 'settings', 'employees', 'my-account'],
            'network_engineer' => ['tickets', 'assets', 'projects', 'my-account'],
            'software_engineer' => ['projects', 'bugs', 'timesheets', 'my-account'],
            'cybersecurity_engineer' => ['tickets', 'assets', 'my-account'],
            'field_technician' => ['tickets', 'projects', 'timesheets', 'bugs', 'my-account'],
            default => [],
        };
    }

    public function page(Request $request, string $module)
    {
        try {
            $user = auth()->user();
            $role = $this->getUserRole($user);

            // Role-based access control: admin bypasses, all other roles are limited to their allowed modules
            if (!$user->isAdmin() && !in_array($module, $this->getAllowedModulesForRole($role), true)) {
                abort(403, 'You do not have permission to access this module.');
            }

            $roleLabel = $this->getRoleLabel($role);
            $roleSlug = $this->roleSlug($role);
            $money = fn($n) => 'TZS ' . number_format($n);

            $data = $this->getSafeDataForModule($module);
            $data['role'] = $role;
            $data['roleLabel'] = $roleLabel;
            $data['roleSlug'] = $roleSlug;
            $data['module'] = $module;
            $data['money'] = $money;
            $data['aiInsights'] = $this->getAiInsightsForModule($module, $data);

            $viewName = 'roles.' . $roleSlug . '.pages.' . $module;
            if (view()->exists($viewName)) {
                return view($viewName, $data);
            }

            // Fallback to shared page
            return view('roles.shared.page', $data);
        } catch (\Throwable $e) {
            // Fail-safe: ensure no role page ever breaks for any company
            $data = $this->getFallbackPageData($module);
            $data['role'] = 'user';
            $data['roleLabel'] = 'User';
            $data['roleSlug'] = 'user';
            $data['module'] = $module;
            $data['money'] = fn($n) => 'TZS ' . number_format($n);
            $data['aiInsights'] = ['message' => 'Page loaded in safe mode.', 'suggestions' => []];
            return view('roles.shared.page', $data);
        }
    }

    private function getFallbackPageData(string $module): array
    {
        return [
            'items' => collect([]),
            'error' => false,
            'message' => 'Page loaded with limited data.',
        ];
    }

    private function getSafeDataForModule(string $module): array
    {
        try {
            return $this->getDataForModule($module);
        } catch (\Throwable $e) {
            return $this->getFallbackPageData($module);
        }
    }

    private function getAiInsightsForModule(string $module, array $data): array
    {
        $suggestions = [];
        $message = 'No insights for this module.';

        try {
            switch ($module) {
                case 'sales-invoices':
                case 'sales-dashboard':
                    $message = 'Sales performance overview.';
                    $balance = $data['salesBalance'] ?? 0;
                    if ($balance > 0) $suggestions[] = 'Follow up on TZS ' . number_format($balance) . ' outstanding customer balance.';
                    break;
                case 'expenses':
                    $message = 'Expense tracking.';
                    $suggestions[] = 'Review monthly expenses and identify cost-saving opportunities.';
                    break;
                case 'projects':
                    $message = 'Project delivery status.';
                    $suggestions[] = 'Monitor deadlines and resource allocation.';
                    break;
                case 'employees':
                case 'attendance':
                case 'leaves':
                    $message = 'HR operations.';
                    $pending = $data['pendingLeaves'] ?? 0;
                    if ($pending > 0) $suggestions[] = "Review $pending pending leave requests.";
                    break;
                case 'products':
                    $message = 'Inventory status.';
                    $low = $data['lowStock'] ?? 0;
                    if ($low > 0) $suggestions[] = "Reorder $low low-stock products.";
                    break;
                case 'tickets':
                    $message = 'Helpdesk status.';
                    $open = $data['openTickets'] ?? 0;
                    if ($open > 0) $suggestions[] = 'Resolve open tickets to maintain SLA.';
                    break;
                default:
                    $message = 'Module loaded successfully.';
                    $suggestions[] = 'Use quick actions to manage this module.';
            }
        } catch (\Throwable $e) {
            $message = 'Insights unavailable.';
            $suggestions = [];
        }

        return ['message' => $message, 'suggestions' => $suggestions];
    }

    private function getDataForModule(string $module): array
    {
        $data = [];

        switch ($module) {
            case 'reports':
                $data['totalSales'] = SalesInvoice::sum('total_amount') ?? 0;
                $data['totalPurchases'] = PurchaseInvoice::sum('total_amount') ?? 0;
                $data['totalExpenses'] = Expense::sum('amount') ?? 0;
                $data['totalRevenues'] = Revenue::sum('amount') ?? 0;
                $data['totalEmployees'] = Employee::count() ?? 0;
                $data['totalProjects'] = Project::count() ?? 0;
                $data['totalProducts'] = Product::count() ?? 0;
                $data['totalTickets'] = HelpdeskTicket::count() ?? 0;
                $data['recentSales'] = SalesInvoice::latest()->take(10)->get();
                $data['recentExpenses'] = Expense::latest()->take(10)->get();
                $data['recentRevenues'] = Revenue::latest()->take(10)->get();
                $data['from'] = now()->subDays(30)->toDateString();
                $data['to'] = now()->toDateString();
                break;

            case 'my-account':
                $user = auth()->user();
                $data['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'created_at' => $user->created_at?->toDateTimeString(),
                ];
                break;

            case 'messages':
                $user = auth()->user();
                $data['unreadCount'] = Message::where('recipient_id', $user->id)->where('status', 'unread')->count();
                $data['inboxCount'] = Message::where('recipient_id', $user->id)->count();
                $data['sentCount'] = Message::where('sender_id', $user->id)->count();
                $data['users'] = User::where('company_id', $user->company_id)->select('id', 'name')->orderBy('name')->get();
                break;

            case 'salary-advance':
                $user = auth()->user();
                $data['pendingCount'] = SalaryAdvanceRequest::where('user_id', $user->id)->where('status', 'pending')->count();
                $data['approvedCount'] = SalaryAdvanceRequest::where('user_id', $user->id)->where('status', 'approved')->count();
                $data['totalRequested'] = SalaryAdvanceRequest::where('user_id', $user->id)->sum('amount') ?? 0;
                break;

            case 'projects':
                $data['projects'] = Project::latest()->paginate(10);
                $data['activeProjects'] = Project::where('status', 'in_progress')->count();
                $data['completedProjects'] = Project::where('status', 'completed')->count();
                break;

            case 'employees':
                $data['employees'] = Employee::latest()->paginate(10);
                $data['totalEmployees'] = Employee::count() ?? 0;
                $data['activeEmployees'] = Employee::where('status', 'active')->count() ?? 0;
                break;

            case 'sales-invoices':
                $data['invoices'] = SalesInvoice::latest()->paginate(10);
                $data['totalSales'] = SalesInvoice::sum('total_amount') ?? 0;
                $data['salesBalance'] = SalesInvoice::sum('balance_amount') ?? 0;
                break;

            case 'purchase-invoices':
                $data['invoices'] = PurchaseInvoice::latest()->paginate(10);
                $data['totalPurchases'] = PurchaseInvoice::sum('total_amount') ?? 0;
                break;

            case 'expenses':
                $data['expenses'] = Expense::latest()->paginate(10);
                $data['totalExpenses'] = Expense::sum('amount') ?? 0;
                $data['monthExpenses'] = Expense::whereMonth('expense_date', now()->month)->sum('amount') ?? 0;
                break;

            case 'revenues':
                $data['revenues'] = Revenue::latest()->paginate(10);
                $data['totalRevenues'] = Revenue::sum('amount') ?? 0;
                $data['monthRevenues'] = Revenue::whereMonth('revenue_date', now()->month)->sum('amount') ?? 0;
                break;

            case 'bills':
                $data['bills'] = Bill::latest()->paginate(10);
                $data['totalBills'] = Bill::sum('amount') ?? 0;
                break;

            case 'bank-accounts':
                $data['accounts'] = BankAccount::latest()->paginate(10);
                $data['totalBalance'] = BankAccount::sum('current_balance') ?? 0;
                break;

            case 'transfers':
                $data['transfers'] = BankTransferAcc::latest()->paginate(10);
                $data['transferCount'] = BankTransferAcc::count();
                break;

            case 'attendance':
                $data['records'] = Attendance::latest()->paginate(10);
                $data['presentToday'] = Attendance::whereDate('date', today())->where('status', 'present')->count();
                $data['absentToday'] = Attendance::whereDate('date', today())->where('status', 'absent')->count();
                break;

            case 'payroll':
                $data['employees'] = Employee::latest()->paginate(10);
                $data['totalPayroll'] = Employee::sum('salary') ?? 0;
                break;

            case 'leaves':
                $data['leaves'] = Leave::latest()->paginate(10);
                $data['pendingLeaves'] = Leave::where('status', 'pending')->count();
                $data['approvedLeaves'] = Leave::where('status', 'approved')->count();
                break;

            case 'users':
                $data['users'] = User::latest()->paginate(10);
                $data['totalUsers'] = User::count();
                break;

            case 'roles':
                $data['roles'] = \App\Models\Role::all();
                break;

            case 'tickets':
                $data['tickets'] = HelpdeskTicket::latest()->paginate(10);
                $data['openTickets'] = HelpdeskTicket::where('status', 'open')->count();
                $data['resolvedTickets'] = HelpdeskTicket::where('status', 'resolved')->count();
                break;

            case 'leads':
                $data['leads'] = CrmLead::latest()->paginate(10);
                $data['totalLeads'] = CrmLead::count();
                $data['newLeads'] = CrmLead::where('status', 'new')->count();
                break;

            case 'deals':
                $data['deals'] = CrmDeal::latest()->paginate(10);
                $data['openDeals'] = CrmDeal::where('status', 'open')->count();
                $data['totalDealValue'] = CrmDeal::sum('value') ?? 0;
                break;

            case 'contacts':
                $data['contacts'] = CrmContact::latest()->paginate(10);
                break;

            case 'contracts':
                $data['contracts'] = CrmContract::latest()->paginate(10);
                break;

            case 'products':
                $data['products'] = Product::latest()->paginate(10);
                $data['totalProducts'] = Product::count();
                $data['lowStock'] = Product::where('stock_quantity', '<', 10)->count();
                break;

            case 'warehouses':
                $data['warehouses'] = Warehouse::latest()->paginate(10);
                break;

            case 'stock-movements':
                $data['movements'] = StockMovement::latest()->paginate(10);
                break;

            case 'suppliers':
                $data['suppliers'] = Supplier::latest()->paginate(10);
                break;

            case 'inventory-transfers':
                $data['transfers'] = Transfer::latest()->paginate(10);
                break;

            case 'pos':
                $data['products'] = Product::where('stock_quantity', '>', 0)->get();
                $data['todaySales'] = PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0;
                $data['todayCount'] = PosSale::whereDate('created_at', today())->count();
                break;

            case 'pos-reports':
                $data['sales'] = PosSale::latest()->paginate(15);
                $data['totalSales'] = PosSale::sum('total_amount') ?? 0;
                $data['monthSales'] = PosSale::whereMonth('created_at', now()->month)->sum('total_amount') ?? 0;
                break;

            case 'timesheets':
                $data['projects'] = Project::latest()->paginate(10);
                break;

            case 'bugs':
                $data['projects'] = Project::latest()->paginate(10);
                break;

            case 'assets':
                $data['employees'] = Employee::latest()->paginate(10);
                break;

            case 'policies':
                $data['employees'] = Employee::latest()->paginate(10);
                break;

            case 'settings':
                $data['settings'] = (object) [];
                break;

            case 'sales-dashboard':
                $data['totalProposals'] = \App\Models\SalesProposal::count() ?? 0;
                $data['acceptedProposals'] = \App\Models\SalesProposal::where('status', 'accepted')->count() ?? 0;
                $data['totalInvoices'] = SalesInvoice::count();
                $data['totalSales'] = SalesInvoice::sum('total_amount') ?? 0;
                $data['recentProposals'] = \App\Models\SalesProposal::latest()->take(5)->get();
                $data['recentInvoices'] = SalesInvoice::latest()->take(5)->get();
                break;

            case 'performance':
                $data['employees'] = Employee::latest()->paginate(10);
                break;

            case 'training':
                $data['employees'] = Employee::latest()->paginate(10);
                break;

            case 'recruitment':
                $data['employees'] = Employee::latest()->paginate(10);
                break;

            case 'visitors':
                $data['visitors'] = Visitor::latest()->paginate(15);
                $data['todayCount'] = Visitor::whereDate('check_in_at', today())->count();
                $data['weekCount'] = Visitor::whereBetween('check_in_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Visitor::whereNull('check_out_at')->where('status', 'checked_in')->count();
                $data['totalCount'] = Visitor::count();
                break;

            case 'appointments':
                $data['appointments'] = Appointment::orderBy('appointment_date', 'asc')->paginate(15);
                $data['todayCount'] = Appointment::whereDate('appointment_date', today())->count();
                $data['weekCount'] = Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Appointment::where('status', 'scheduled')->where('appointment_date', '>=', now())->count();
                $data['totalCount'] = Appointment::count();
                break;

            case 'calls':
                $data['calls'] = Call::latest()->paginate(15);
                $data['todayCount'] = Call::whereDate('call_time', today())->count();
                $data['weekCount'] = Call::whereBetween('call_time', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Call::where('status', 'follow_up')->count();
                $data['totalCount'] = Call::count();
                break;

            case 'correspondence':
                $data['correspondence'] = Correspondence::latest()->paginate(15);
                $data['todayCount'] = Correspondence::whereDate('received_date', today())->orWhereDate('dispatched_date', today())->count();
                $data['weekCount'] = Correspondence::whereBetween('received_date', [now()->startOfWeek(), now()->endOfWeek()])->orWhereBetween('dispatched_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Correspondence::where('status', 'pending')->count();
                $data['totalCount'] = Correspondence::count();
                break;

            case 'parcels':
                $data['parcels'] = Parcel::latest()->paginate(15);
                $data['todayCount'] = Parcel::whereDate('received_date', today())->orWhereDate('delivered_date', today())->count();
                $data['weekCount'] = Parcel::whereBetween('received_date', [now()->startOfWeek(), now()->endOfWeek()])->orWhereBetween('delivered_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Parcel::where('status', 'received')->count();
                $data['totalCount'] = Parcel::count();
                break;

            case 'front-desk':
                $data['front_desks'] = FrontDesk::orderBy('check_in_at', 'asc')->paginate(15);
                $data['waitingCount'] = FrontDesk::where('status', 'waiting')->count();
                $data['inProgressCount'] = FrontDesk::where('status', 'in_progress')->count();
                $data['completedCount'] = FrontDesk::where('status', 'completed')->count();
                $data['totalCount'] = FrontDesk::count();
                break;

            case 'departments':
                $data['departments'] = Department::orderBy('name', 'asc')->paginate(15);
                $data['activeCount'] = Department::where('status', 'active')->count();
                $data['inactiveCount'] = Department::where('status', 'inactive')->count();
                $data['totalCount'] = Department::count();
                break;

            case 'announcements':
                $data['announcements'] = Announcement::latest()->paginate(15);
                $data['activeCount'] = Announcement::where('status', 'active')->count();
                $data['highPriorityCount'] = Announcement::where('priority', 'high')->where('status', 'active')->count();
                $data['totalCount'] = Announcement::count();
                break;

            default:
                $data['items'] = collect([]);
                break;
        }

        return $data;
    }
}
