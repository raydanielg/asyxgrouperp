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
                $data['totalBalance'] = BankAccount::sum('balance') ?? 0;
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
