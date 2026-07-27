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
                break;

            case 'projects':
                $data['projects'] = Project::with(['manager', 'employees'])->latest()->paginate(10);
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
                $data['bugs'] = ProjectBug::with(['project', 'assignedTo'])->latest()->paginate(10);
                $data['openBugs'] = ProjectBug::where('status', 'open')->count();
                $data['resolvedBugs'] = ProjectBug::where('status', 'resolved')->count();
                $data['criticalBugs'] = ProjectBug::where('severity', 'critical')->count();
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
                $jcQuery = JobCard::with('project', 'assignedTo', 'createdBy');
                if ($userRole === 'technician') {
                    $jcQuery->where(function ($q) use ($userId) {
                        $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                    });
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
                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('search')) {
                    $search = $request->search;
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
            'team-review' => Project::class,
            'resource-allocation' => Project::class,
            'milestones' => Project::class,
            'tasks' => ProjectTask::class,
            'team-tasks' => ProjectTask::class,
            'team-attendance' => Attendance::class,
            'team-timesheets' => Timesheet::class,
            'team-overview' => Employee::class,
            'team-leaves' => Leave::class,
            'escalations' => HelpdeskTicket::class,
            'incidents' => HelpdeskTicket::class,
            'site-visits' => HelpdeskTicket::class,
            'knowledge-base' => HelpdeskTicket::class,
            'sla-reports' => HelpdeskTicket::class,
            'site-reports' => ProjectTask::class,
            'service-reports' => HelpdeskTicket::class,
            'call-statistics' => Call::class,
            'shift-schedule' => Attendance::class,
            'sales-forecast' => CrmDeal::class,
            'market-analysis' => CrmLead::class,
            'lead-source-reports' => CrmLead::class,
            'project-profitability' => Project::class,
            'campaigns' => \App\Models\Announcement::class,
            'messages' => Message::class,
            'announcements' => Announcement::class,
            'visitors' => Visitor::class,
            'appointments' => Appointment::class,
            'calls' => Call::class,
            'correspondence' => Correspondence::class,
            'parcels' => Parcel::class,
            'departments' => Department::class,
            'overtime' => Attendance::class,
            'onboarding' => Employee::class,
            'credit-limits' => CrmContact::class,
            'overdue-accounts' => SalesInvoice::class,
            'collections' => SalesInvoice::class,
            'receivables-aging' => SalesInvoice::class,
            'payables-aging' => PurchaseInvoice::class,
            'bank-reconciliation' => BankAccount::class,
            'lpos' => PurchaseInvoice::class,
            'grns' => StockMovement::class,
            'stock-count' => StockMovement::class,
            'reorder-levels' => Product::class,
            'batch-tracking' => Product::class,
            'barcodes' => Product::class,
            'inventory-transfers' => Transfer::class,
            'transfers' => Transfer::class,
            'pos-reports' => PosSale::class,
            'assets' => FixedAsset::class,
            'asset-assignment' => FixedAsset::class,
            'asset-disposal' => FixedAsset::class,
        ];

        return $map[$module] ?? null;
    }
}
