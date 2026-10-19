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
use App\Models\JobCard;
use App\Models\Document;
use App\Models\ProjectTask;
use App\Models\ProjectBug;
use App\Models\FixedAsset;
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
        return self::roleLabels()[$role] ?? ucwords(str_replace('_', ' ', $role));
    }

    public static function roleLabels(): array
    {
        return [
            'superadmin' => 'ERP Super Administrator',
            'admin' => 'ERP Administrator',
            'ict_administrator' => 'ICT Administrator',
            'auditor' => 'Auditor',
            'managing_director' => 'Managing Director',
            'director' => 'Managing Director',
            'general_manager' => 'General Manager',
            'technical_manager' => 'Technical Manager',
            'operations_manager' => 'Operations Manager',
            'finance_manager' => 'Finance Manager',
            'chief_accountant' => 'Chief Accountant',
            'accountant' => 'Accountant',
            'accounts_receivable_officer' => 'Accounts Receivable Officer',
            'accounts_payable_officer' => 'Accounts Payable Officer',
            'cashier' => 'Cashier',
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
            'network_engineer' => 'Network Engineer',
            'software_engineer' => 'Software Engineer',
            'cybersecurity_engineer' => 'Cybersecurity Engineer',
            'support_engineer' => 'Support Engineer',
            'field_technician' => 'Field Technician',
            'technician' => 'Technician',
            'noc_engineer' => 'NOC Engineer',
            'service_desk_manager' => 'Service Desk Manager',
            'helpdesk_supervisor' => 'Helpdesk Supervisor',
            'helpdesk_officer' => 'Helpdesk Officer',
            'call_center_supervisor' => 'Call Center Supervisor',
            'call_center_agent' => 'Call Center Agent',
            'hr_manager' => 'HR Manager',
            'hr_officer' => 'HR Officer',
            'recruitment_officer' => 'Recruitment Officer',
            'training_officer' => 'Training Officer',
            'time_attendance_officer' => 'Time & Attendance Officer',
            'operations_officer' => 'Operations Officer',
            'fleet_manager' => 'Fleet Manager',
            'logistics_officer' => 'Logistics Officer',
            'receptionist' => 'Receptionist',
            'employee' => 'Employee',
            'employee_self_service' => 'Employee',
            'manager_self_service' => 'Manager Self-Service',
        ];
    }

    private function roleSlug(string $role): string
    {
        return str_replace('_', '-', $role);
    }

    private function getAllowedModulesForRole(string $role): array
    {
        return \App\Support\RoleModules::allowedModules($role, auth()->user());
    }

    // legacyAllowedModulesForRole removed – module list now lives in App\Support\RoleModules.

    public function page(Request $request, string $module)
    {
        $user = auth()->user();
        $role = $this->getUserRole($user);

        // Role-based access control: admin bypasses, all other roles are limited to their allowed modules
        if (!$user->isAdmin() && !in_array($module, $this->getAllowedModulesForRole($role), true)) {
            abort(403, 'You do not have permission to access this module.');
        }

        try {
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
        // AI Insights feature disabled system-wide.
        return [
            'message' => null,
            'suggestions' => [],
        ];
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
                $employee = $user->employee;
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
                $data['employee'] = $employee;
                $data['recentPayslips'] = $employee
                    ? \App\Models\Payroll::where('employee_id', $employee->id)->latest()->take(5)->get()
                    : collect([]);
                $data['pendingLeave'] = $employee
                    ? \App\Models\Leave::where('employee_id', $employee->id)->where('status', 'pending')->count()
                    : 0;
                $data['approvedLeave'] = $employee
                    ? \App\Models\Leave::where('employee_id', $employee->id)->where('status', 'approved')->count()
                    : 0;
                $data['openTickets'] = \App\Models\HelpdeskTicket::where('created_by', $user->id)->where('status', 'open')->count();
                $data['resolvedTickets'] = \App\Models\HelpdeskTicket::where('created_by', $user->id)->where('status', 'resolved')->count();
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

            case 'salary':
            case 'payslips':
                $user = auth()->user();
                $employee = $user->employee;
                $employeeId = $employee?->id;
                $data['employee'] = $employee;
                $data['salary'] = $employee?->salary ?? 0;

                $query = $employeeId ? \App\Models\Payroll::where('employee_id', $employeeId) : null;
                if ($query && request('month')) {
                    $query->where('month', request('month'));
                }
                if ($query && request('year')) {
                    $query->where('year', request('year'));
                }
                if ($query && request('status')) {
                    $query->where('status', request('status'));
                }
                $data['payrolls'] = $query ? $query->latest()->paginate(15)->withQueryString() : collect([]);
                $data['latestPayroll'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->latest()->first()
                    : null;
                $data['yearToDate'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->where('year', now()->year)->sum('net_salary') ?? 0
                    : 0;
                $data['totalOvertime'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->sum('overtime') ?? 0
                    : 0;
                $data['totalPaid'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->where('status', 'paid')->sum('net_salary') ?? 0
                    : 0;
                $data['months'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->select('month')->distinct()->pluck('month')
                    : collect([]);
                $data['years'] = $employeeId
                    ? \App\Models\Payroll::where('employee_id', $employeeId)->select('year')->distinct()->orderBy('year', 'desc')->pluck('year')
                    : collect([]);
                $data['filterMonth'] = request('month');
                $data['filterYear'] = request('year');
                $data['filterStatus'] = request('status');

                $docUser = auth()->user();
                $data['myDocuments'] = Document::where(function ($q) use ($docUser) {
                    $q->where('uploaded_by', $docUser->id)
                      ->orWhere('is_confidential', false);
                })
                ->where(function ($q) use ($docUser) {
                    $q->whereNull('reference_type')
                      ->orWhere('reference_type', 'employee')
                      ->orWhere('reference_id', $docUser->employee?->id);
                })
                ->latest()
                ->take(10)
                ->get();
                $data['totalDocuments'] = Document::where('uploaded_by', $docUser->id)->count();
                break;

            case 'projects':
                $data['projects'] = Project::with(['manager', 'employees'])->withCount('jobCards')->latest()->paginate(10);
                $data['activeProjects'] = Project::where('status', 'in_progress')->count();
                $data['completedProjects'] = Project::where('status', 'completed')->count();
                $data['assignedEmployeesCount'] = \DB::table('employee_project')->where('is_active', true)->distinct('employee_id')->count('employee_id');
                $data['managers'] = User::whereIn('role', ['admin', 'superadmin', 'project_manager', 'technical_manager'])->get();
                $data['employees'] = Employee::where('status', 'active')->orderBy('first_name')->get();
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
                $attQuery = Attendance::query();
                if (!auth()->user()->hasPermission('view-attendance')) {
                    $attQuery->where('employee_id', auth()->user()->employee?->id ?? -1);
                }
                $data['records'] = (clone $attQuery)->latest()->paginate(10);
                $data['presentToday'] = (clone $attQuery)->whereDate('date', today())->where('status', 'present')->count();
                $data['absentToday'] = (clone $attQuery)->whereDate('date', today())->where('status', 'absent')->count();
                break;

            case 'payroll':
                $data['employees'] = Employee::latest()->paginate(10);
                $data['totalPayroll'] = Employee::sum('salary') ?? 0;
                break;

            case 'leaves':
                $leaveQuery = Leave::query();
                if (!auth()->user()->hasPermission('view-leaves')) {
                    $leaveQuery->where('employee_id', auth()->user()->employee?->id ?? -1);
                }
                $data['leaves'] = (clone $leaveQuery)->latest()->paginate(10);
                $data['pendingLeaves'] = (clone $leaveQuery)->where('status', 'pending')->count();
                $data['approvedLeaves'] = (clone $leaveQuery)->where('status', 'approved')->count();
                break;

            case 'users':
                $data['users'] = User::latest()->paginate(10);
                $data['totalUsers'] = User::count();
                break;

            case 'roles':
                $data['roles'] = \App\Models\Role::all();
                break;

            case 'tickets':
                $tkUser = auth()->user();
                $tkManagerRoles = ['admin', 'superadmin', 'project_manager', 'technical_manager', 'director', 'managing_director', 'general_manager', 'operations_manager', 'service_desk_manager', 'helpdesk_supervisor', 'call_center_supervisor', 'ict_administrator', 'technical_projects_manager', 'project_director', 'team_leader', 'senior_systems_engineer'];
                $tkQuery = HelpdeskTicket::with(['category', 'creator', 'assignee']);
                $tkStatsQuery = HelpdeskTicket::query();
                if (!in_array($tkUser->role, $tkManagerRoles, true)) {
                    $tkQuery->where(function ($q) use ($tkUser) {
                        $q->where('assigned_to', $tkUser->id)->orWhere('created_by', $tkUser->id);
                    });
                    $tkStatsQuery->where(function ($q) use ($tkUser) {
                        $q->where('assigned_to', $tkUser->id)->orWhere('created_by', $tkUser->id);
                    });
                }
                $data['tickets'] = $tkQuery->latest()->paginate(10);
                $data['totalTickets'] = (clone $tkStatsQuery)->count();
                $data['openTickets'] = (clone $tkStatsQuery)->where('status', 'open')->count();
                $data['inProgressTickets'] = (clone $tkStatsQuery)->where('status', 'in_progress')->count();
                $data['resolvedTickets'] = (clone $tkStatsQuery)->where('status', 'resolved')->count();
                $data['ticketCategories'] = \App\Models\HelpdeskCategory::where('is_active', true)->get();
                $data['ticketAssignees'] = User::whereIn('role', ['technician', 'technical_manager', 'admin', 'superadmin'])->orderBy('name')->get();
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
                $tsUser = auth()->user();
                $tsQuery = \App\Models\Timesheet::with(['project', 'task', 'employee']);
                $managerRoles = ['admin', 'superadmin', 'project_manager', 'technical_manager', 'director', 'managing_director', 'general_manager', 'project_director', 'technical_projects_manager', 'team_leader', 'ict_administrator'];
                if (!in_array($tsUser->role, $managerRoles, true)) {
                    $tsEmployeeId = $tsUser->employee?->id;
                    $tsQuery->where('employee_id', $tsEmployeeId);
                }
                $data['timesheets'] = $tsQuery->latest('date')->paginate(15);
                $data['totalHours'] = (clone $tsQuery)->sum('hours') ?? 0;
                $data['weekHours'] = (clone $tsQuery)->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('hours') ?? 0;
                $data['monthHours'] = (clone $tsQuery)->whereMonth('date', now()->month)->sum('hours') ?? 0;
                $data['projects'] = Project::orderBy('title')->get();
                break;

            case 'bugs':
                $bugUser = auth()->user();
                $bugManagerRoles = ['admin', 'superadmin', 'project_manager', 'technical_manager', 'director', 'managing_director', 'general_manager', 'project_director', 'technical_projects_manager', 'team_leader', 'ict_administrator'];
                $bugQuery = ProjectBug::with(['project', 'assignedTo']);
                $bugStatsQuery = ProjectBug::query();
                if (!in_array($bugUser->role, $bugManagerRoles, true)) {
                    $bugQuery->where('assigned_to', $bugUser->id);
                    $bugStatsQuery->where('assigned_to', $bugUser->id);
                }
                $data['bugs'] = $bugQuery->latest()->paginate(10);
                $data['openBugs'] = (clone $bugStatsQuery)->where('status', 'open')->count();
                $data['resolvedBugs'] = (clone $bugStatsQuery)->where('status', 'resolved')->count();
                $data['criticalBugs'] = (clone $bugStatsQuery)->where('severity', 'critical')->count();
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

            case 'job-cards':
                $userId = auth()->id();
                $userRole = auth()->user()->role;
                $filterProjectId = request('project_id');
                $jcQuery = JobCard::with('project', 'assignedTo', 'createdBy');
                if ($userRole === 'technician') {
                    $jcQuery->where(function ($q) use ($userId) {
                        $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                    });
                }
                if ($filterProjectId) {
                    $jcQuery->where('project_id', $filterProjectId);
                }
                $data['jobCards'] = $jcQuery->latest()->paginate(15);
                $statsQuery = JobCard::query();
                if ($userRole === 'technician') {
                    $statsQuery->where(function ($q) use ($userId) {
                        $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                    });
                }
                $data['totalCards'] = (clone $statsQuery)->count();
                $data['openCards'] = (clone $statsQuery)->where('status', 'open')->count();
                $data['inProgressCards'] = (clone $statsQuery)->where('status', 'in_progress')->count();
                $data['resolvedCards'] = (clone $statsQuery)->where('status', 'resolved')->count();
                $data['projects'] = Project::where('status', 'in_progress')->orWhere('status', 'planning')->get();
                $data['technicians'] = User::where('role', 'technician')->orWhereHas('roles', fn($q) => $q->where('name', 'technician'))->get();
                $data['filterProjectId'] = $filterProjectId;

                // Technician workload overview — useful for technical_manager / project_manager oversight
                if (in_array($userRole, ['technical_manager', 'project_manager', 'admin', 'superadmin'])) {
                    $data['technicianWorkload'] = $data['technicians']->map(function ($tech) {
                        return [
                            'name' => $tech->name,
                            'open' => JobCard::where('assigned_to', $tech->id)->where('status', 'open')->count(),
                            'in_progress' => JobCard::where('assigned_to', $tech->id)->where('status', 'in_progress')->count(),
                            'resolved' => JobCard::where('assigned_to', $tech->id)->where('status', 'resolved')->count(),
                            'total' => JobCard::where('assigned_to', $tech->id)->count(),
                        ];
                    })->sortByDesc('total')->values();
                }
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

            case 'documents':
                $query = Document::with(['uploadedBy', 'project']);
                if (request()->filled('category')) {
                    $query->where('category', request('category'));
                }
                if (request()->filled('status')) {
                    $query->where('status', request('status'));
                }
                if (request()->filled('search')) {
                    $search = request('search');
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('document_number', 'like', "%{$search}%")
                          ->orWhere('tags', 'like', "%{$search}%");
                    });
                }
                $data['documents'] = $query->latest()->paginate(15)->withQueryString();
                $data['todayCount'] = Document::whereDate('created_at', today())->count();
                $data['weekCount'] = Document::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $data['pendingCount'] = Document::where('status', 'pending_signature')->count();
                $data['totalCount'] = Document::count();
                $data['categories'] = [
                    'policy' => ['label' => 'Company Policy', 'color' => 'blue'],
                    'contract' => ['label' => 'Contract', 'color' => 'purple'],
                    'minutes' => ['label' => 'Meeting Minutes', 'color' => 'amber'],
                    'action_point' => ['label' => 'Action Points', 'color' => 'red'],
                    'project_doc' => ['label' => 'Project Document', 'color' => 'emerald'],
                    'tender' => ['label' => 'Tender', 'color' => 'indigo'],
                    'hr' => ['label' => 'HR Document', 'color' => 'pink'],
                    'legal' => ['label' => 'Legal', 'color' => 'gray'],
                    'financial' => ['label' => 'Financial', 'color' => 'green'],
                    'technical' => ['label' => 'Technical', 'color' => 'cyan'],
                    'other' => ['label' => 'Other', 'color' => 'slate'],
                ];
                break;

            default:
                $modelClass = $this->resolveModelClassForModule($module);
                if ($modelClass && class_exists($modelClass)) {
                    $data['items'] = $modelClass::latest()->paginate(15);
                    $data['totalCount'] = $modelClass::count();
                    if (in_array('status', (new $modelClass)->getFillable(), true) || method_exists($modelClass, 'getTable') && \Schema::hasColumn((new $modelClass)->getTable(), 'status')) {
                        $data['pendingCount'] = $modelClass::whereIn('status', ['pending', 'open', 'in_progress', 'draft', 'pending_signature'])->count();
                    }
                    if (method_exists($modelClass, 'getTable') && \Schema::hasColumn((new $modelClass)->getTable(), 'created_at')) {
                        $data['todayCount'] = $modelClass::whereDate('created_at', today())->count();
                        $data['weekCount'] = $modelClass::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                    }
                } else {
                    $data['items'] = collect([]);
                }
                break;
        }

        return $data;
    }

    private function resolveModelClassForModule(string $module): ?string
    {
        $map = [
            // Projects / teams
            'team-review' => Project::class,
            'resource-allocation' => Employee::class,
            'milestones' => ProjectTask::class,
            'tasks' => ProjectTask::class,
            'team-tasks' => ProjectTask::class,
            'team-attendance' => Attendance::class,
            'team-timesheets' => Timesheet::class,
            'team-overview' => Employee::class,
            'team-leaves' => Leave::class,
            'site-reports' => ProjectTask::class,
            'project-profitability' => Project::class,
            'project-budgets' => \App\Models\ProjectBudget::class,

            // Service desk / technical
            'escalations' => HelpdeskTicket::class,
            'incidents' => HelpdeskTicket::class,
            'site-visits' => JobCard::class,
            'knowledge-base' => HelpdeskTicket::class,
            'sla-reports' => HelpdeskTicket::class,
            'service-reports' => JobCard::class,
            'monitoring-dashboard' => HelpdeskTicket::class,
            'call-statistics' => Call::class,
            'shift-schedule' => Attendance::class,
            'customer-assets' => FixedAsset::class,
            'maintenance' => \App\Models\VehicleMaintenance::class,

            // CRM / sales
            'sales-forecast' => CrmDeal::class,
            'market-analysis' => CrmLead::class,
            'lead-source-reports' => CrmLead::class,
            'campaigns' => Announcement::class,
            'tenders' => \App\Models\Tender::class,
            'quotations' => \App\Models\Quotation::class,
            'sales-proposals' => \App\Models\SalesProposal::class,

            // Reception
            'messages' => Message::class,
            'announcements' => Announcement::class,
            'visitors' => Visitor::class,
            'appointments' => Appointment::class,
            'calls' => Call::class,
            'correspondence' => Correspondence::class,
            'parcels' => Parcel::class,
            'departments' => Department::class,

            // HR
            'overtime' => Attendance::class,
            'onboarding' => \App\Models\JobApplication::class,
            'job-postings' => \App\Models\JobPosting::class,
            'applications' => \App\Models\JobApplication::class,
            'recruitment' => \App\Models\JobPosting::class,
            'training-records' => \App\Models\Training::class,
            'certifications' => \App\Models\Training::class,
            'disciplinary' => \App\Models\PerformanceReview::class,
            'hr-events' => \App\Models\HrEvent::class,
            'salary-records' => Employee::class,
            'deductions' => \App\Models\Payroll::class,
            'payslips-all' => \App\Models\Payroll::class,

            // Finance
            'journal-entries' => \App\Models\JournalEntry::class,
            'petty-cash' => \App\Models\CashAccountTransaction::class,
            'financial-reports' => \App\Models\JournalEntry::class,
            'bank-reconciliation' => BankAccount::class,
            'tax-management' => Expense::class,
            'cost-centres' => \App\Models\CostCenter::class,
            'cost-allocations' => \App\Models\CostAllocation::class,
            'cost-centre-budgets' => \App\Models\CostCenter::class,
            'budgets' => \App\Models\Budget::class,
            'budget-vs-actual' => \App\Models\Budget::class,
            'credit-limits' => CrmContact::class,
            'overdue-accounts' => SalesInvoice::class,
            'collections' => SalesInvoice::class,
            'receivables-aging' => SalesInvoice::class,
            'payables-aging' => PurchaseInvoice::class,
            'credit-notes' => \App\Models\SalesInvoiceReturn::class,
            'client-receipts' => \App\Models\ClientReceipt::class,
            'vendor-invoices' => \App\Models\VendorInvoice::class,
            'vendor-payments' => \App\Models\VendorPayment::class,
            'office-expenses' => \App\Models\OfficeExpense::class,
            'acc-transfers' => BankTransferAcc::class,

            // Procurement / inventory
            'rfqs' => \App\Models\Rfq::class,
            'purchase-requisitions' => \App\Models\ProcurementRequisition::class,
            'lpos' => \App\Models\Lpo::class,
            'grns' => \App\Models\Grn::class,
            'delivery-notes' => \App\Models\DeliveryNote::class,
            'stock-count' => StockMovement::class,
            'reorder-levels' => Product::class,
            'batch-tracking' => Product::class,
            'barcodes' => Product::class,
            'product-categories' => \App\Models\ProductCategory::class,
            'variance-reports' => StockMovement::class,
            'inventory-reports' => Product::class,
            'inventory-transfers' => Transfer::class,
            'transfers' => Transfer::class,
            'pos-reports' => PosSale::class,
            'assets' => FixedAsset::class,
            'asset-assignment' => FixedAsset::class,
            'asset-maintenance' => FixedAsset::class,
            'asset-disposal' => FixedAsset::class,

            // Fleet / logistics
            'vehicles' => \App\Models\Vehicle::class,
            'driver-assignment' => \App\Models\Vehicle::class,
            'fuel-logs' => \App\Models\FuelLog::class,
            'trip-schedule' => \App\Models\Vehicle::class,
            'deliveries' => \App\Models\DeliveryNote::class,
            'shipments' => \App\Models\DeliveryNote::class,
            'route-planning' => \App\Models\DeliveryNote::class,
            'operations-log' => HelpdeskTicket::class,
            'operations-tasks' => ProjectTask::class,

            // Admin / governance
            'companies' => \App\Models\Company::class,
            'intercompany' => \App\Models\IntercompanyTransaction::class,
            'approvals' => \App\Models\ApprovalRequest::class,
            'audit-logs' => \App\Models\AuditLog::class,
            'login-history' => \App\Models\LoginHistory::class,
            'documents' => Document::class,
            'meetings' => \App\Models\Meeting::class,
        ];

        return $map[$module] ?? null;
    }
}
