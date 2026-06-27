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
use App\Models\Project;
use App\Models\HelpdeskTicket;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\PosSale;
use App\Models\Attendance;
use App\Models\CrmContact;
use App\Models\CrmContract;
use Illuminate\Http\Request;

class RolePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getUserRole(): string
    {
        $user = auth()->user();
        if ($user->isAdmin()) return 'admin';
        $role = $user->roles()->first();
        if ($role) return $role->name;
        return $user->role ?? 'user';
    }

    private function roleLabel(string $role): string
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

    private function viewPath(string $role, string $page): string
    {
        $slug = str_replace('_', '-', $role);
        return "roles.{$slug}.{$page}";
    }

    public function page(Request $request, string $page)
    {
        $role = $this->getUserRole();
        $roleLabel = $this->roleLabel($role);
        $money = fn($n) => 'TZS ' . number_format($n);
        $viewName = $this->viewPath($role, $page);

        if (!view()->exists($viewName)) {
            $data = $this->getPageData($role, $page);
            return view('roles.shared-page', compact('role', 'roleLabel', 'page', 'money', 'data'));
        }

        $data = $this->getPageData($role, $page);
        return view($viewName, compact('role', 'roleLabel', 'money', 'data'));
    }

    private function getPageData(string $role, string $page): array
    {
        $data = [];

        switch ($page) {
            case 'reports':
                $data['totalSales'] = SalesInvoice::sum('total_amount') ?? 0;
                $data['totalPurchases'] = PurchaseInvoice::sum('total_amount') ?? 0;
                $data['totalExpenses'] = Expense::sum('amount') ?? 0;
                $data['totalRevenues'] = Revenue::sum('amount') ?? 0;
                $data['totalProfit'] = ($data['totalRevenues'] ?? 0) - ($data['totalExpenses'] ?? 0);
                $data['salesCount'] = SalesInvoice::count();
                $data['purchaseCount'] = PurchaseInvoice::count();
                $data['expenseCount'] = Expense::count();
                $data['revenueCount'] = Revenue::count();
                $data['recentSales'] = SalesInvoice::latest()->take(10)->get();
                $data['recentPurchases'] = PurchaseInvoice::latest()->take(10)->get();
                $data['recentExpenses'] = Expense::latest()->take(10)->get();
                $data['recentRevenues'] = Revenue::latest()->take(10)->get();
                $data['monthlySales'] = SalesInvoice::selectRaw('MONTH(invoice_date) as month, SUM(total_amount) as total')
                    ->whereYear('invoice_date', date('Y'))
                    ->groupByRaw('MONTH(invoice_date)')
                    ->pluck('total', 'month')->toArray();
                $data['monthlyExpenses'] = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                    ->whereYear('expense_date', date('Y'))
                    ->groupByRaw('MONTH(expense_date)')
                    ->pluck('total', 'month')->toArray();
                break;

            case 'projects':
                $data['projects'] = Project::latest()->paginate(10);
                $data['activeProjects'] = Project::where('status', 'in_progress')->count();
                $data['completedProjects'] = Project::where('status', 'completed')->count();
                $data['totalProjects'] = Project::count();
                break;

            case 'employees':
                $data['employees'] = Employee::latest()->paginate(10);
                $data['totalEmployees'] = Employee::count();
                $data['activeEmployees'] = Employee::where('status', 'active')->count() ?? Employee::count();
                $data['presentToday'] = Attendance::whereDate('date', today())->where('status', 'present')->count();
                $data['absentToday'] = Attendance::whereDate('date', today())->where('status', 'absent')->count();
                break;

            case 'sales':
                $data['invoices'] = SalesInvoice::latest()->paginate(10);
                $data['totalSales'] = SalesInvoice::sum('total_amount') ?? 0;
                $data['salesBalance'] = SalesInvoice::sum('balance_amount') ?? 0;
                $data['salesCount'] = SalesInvoice::count();
                $data['draftInvoices'] = SalesInvoice::where('status', 'draft')->count();
                break;

            case 'purchases':
                $data['invoices'] = PurchaseInvoice::latest()->paginate(10);
                $data['totalPurchases'] = PurchaseInvoice::sum('total_amount') ?? 0;
                $data['purchaseCount'] = PurchaseInvoice::count();
                break;

            case 'expenses':
                $data['expenses'] = Expense::latest()->paginate(10);
                $data['totalExpenses'] = Expense::sum('amount') ?? 0;
                $data['monthExpenses'] = Expense::whereMonth('expense_date', date('m'))->sum('amount') ?? 0;
                $data['expenseCount'] = Expense::count();
                break;

            case 'revenues':
                $data['revenues'] = Revenue::latest()->paginate(10);
                $data['totalRevenues'] = Revenue::sum('amount') ?? 0;
                $data['monthRevenues'] = Revenue::whereMonth('revenue_date', date('m'))->sum('amount') ?? 0;
                break;

            case 'tickets':
                $data['tickets'] = HelpdeskTicket::latest()->paginate(10);
                $data['openTickets'] = HelpdeskTicket::where('status', 'open')->count();
                $data['inProgress'] = HelpdeskTicket::where('status', 'in_progress')->count();
                $data['resolved'] = HelpdeskTicket::where('status', 'resolved')->count();
                $data['myTickets'] = HelpdeskTicket::where('assigned_to', auth()->id())->latest()->take(5)->get();
                break;

            case 'leads':
                $data['leads'] = CrmLead::latest()->paginate(10);
                $data['totalLeads'] = CrmLead::count();
                $data['newLeads'] = CrmLead::where('status', 'new')->count();
                break;

            case 'deals':
                $data['deals'] = CrmDeal::latest()->paginate(10);
                $data['openDeals'] = CrmDeal::where('status', 'open')->count() ?? CrmDeal::count();
                $data['totalDealValue'] = CrmDeal::sum('value') ?? 0;
                break;

            case 'contacts':
                $data['contacts'] = CrmContact::latest()->paginate(10);
                $data['totalContacts'] = CrmContact::count();
                break;

            case 'contracts':
                $data['contracts'] = CrmContract::latest()->paginate(10);
                $data['totalContracts'] = CrmContract::count();
                break;

            case 'products':
                $data['products'] = Product::latest()->paginate(10);
                $data['totalProducts'] = Product::count();
                $data['lowStock'] = Product::where('stock_quantity', '<', 10)->count();
                break;

            case 'warehouses':
                $data['warehouses'] = Warehouse::latest()->paginate(10);
                $data['totalWarehouses'] = Warehouse::count();
                break;

            case 'attendance':
                $data['attendance'] = Attendance::latest()->paginate(10);
                $data['presentToday'] = Attendance::whereDate('date', today())->where('status', 'present')->count();
                $data['absentToday'] = Attendance::whereDate('date', today())->where('status', 'absent')->count();
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

            case 'pos':
                $data['todaySales'] = PosSale::whereDate('created_at', today())->sum('total_amount') ?? 0;
                $data['todayCount'] = PosSale::whereDate('created_at', today())->count();
                $data['monthSales'] = PosSale::whereMonth('created_at', date('m'))->sum('total_amount') ?? 0;
                $data['recentSales'] = PosSale::latest()->take(10)->get();
                break;

            default:
                $data = [];
        }

        return $data;
    }
}
