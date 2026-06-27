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
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Transfer;
use App\Models\Bill;
use App\Models\BankAccount;
use App\Models\AccTransfer;
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

    public function page(Request $request, string $module)
    {
        $user = auth()->user();
        $role = $this->getUserRole($user);
        $roleLabel = $this->getRoleLabel($role);
        $roleSlug = $this->roleSlug($role);
        $money = fn($n) => 'TZS ' . number_format($n);

        $data = $this->getDataForModule($module);
        $data['role'] = $role;
        $data['roleLabel'] = $roleLabel;
        $data['roleSlug'] = $roleSlug;
        $data['module'] = $module;
        $data['money'] = $money;

        $viewName = 'roles.' . $roleSlug . '.pages.' . $module;
        if (view()->exists($viewName)) {
            return view($viewName, $data);
        }

        // Fallback to shared page
        return view('roles.shared.page', $data);
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
                $data['transfers'] = AccTransfer::latest()->paginate(10);
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

            default:
                $data['items'] = collect([]);
                break;
        }

        return $data;
    }
}
