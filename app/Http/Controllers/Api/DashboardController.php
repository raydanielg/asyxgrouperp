<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{User, SalesInvoice, PurchaseInvoice, SalesProposal, Expense, Revenue, Employee, Leave, CrmLead, CrmDeal, CrmContact, CrmContract, Project, HelpdeskTicket, Product, Warehouse, PosSale, Order, Attendance, Payroll, StockMovement, Transfer, Vehicle, FixedAsset, Document, AuditLog, CallLog, CallCampaign, Lpo, Grn, DeliveryNote, Quotation, Tender, VendorInvoice, VendorPayment, OfficeExpense, ClientReceipt, ProjectBudget};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function roleDashboard(Request $request)
    {
        $user = $request->user();
        $role = $this->getUserRole($user);

        return response()->json([
            'role' => $role,
            'roleLabel' => $this->getRoleLabel($role),
            'stats' => $this->getStatsForRole($role, $user),
            'kpiCards' => $this->getKpiCardsForRole($role, $user),
            'quickActions' => $this->getQuickActionsForRole($role),
            'chartData' => $this->getChartDataForRole($role),
            'recentActivity' => $this->getRecentActivity($role, $user),
            'notifications' => $this->getNotifications($user),
        ]);
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'notifications' => $user->notifications ?? collect([]),
            'unread_count' => $user->unreadNotifications->count() ?? 0,
        ]);
    }

    public function markNotificationRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
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

    private function getStatsForRole(string $role, $user): array
    {
        $stats = [];
        try {
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
                        'totalProducts' => Product::count(),
                        'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                        'totalWarehouses' => Warehouse::count(),
                        'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                        'netProfit' => (Revenue::sum('amount') ?? 0) - (Expense::sum('amount') ?? 0),
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
                        'completedProjects' => Project::where('status', 'completed')->count(),
                        'totalEmployees' => Employee::count(),
                        'pendingLeaves' => Leave::where('status', 'pending')->count(),
                        'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                        'totalProposals' => SalesProposal::count(),
                        'acceptedProposals' => SalesProposal::where('status', 'accepted')->count(),
                        'netProfit' => (Revenue::sum('amount') ?? 0) - (Expense::sum('amount') ?? 0),
                        'totalDeals' => CrmDeal::count(),
                        'openDeals' => CrmDeal::where('status', 'open')->count(),
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
                        'netProfit' => (Revenue::sum('amount') ?? 0) - (Expense::sum('amount') ?? 0),
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
                        'approvedLeaves' => Leave::where('status', 'approved')->count(),
                        'todayAttendance' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
                        'absentToday' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
                        'totalPayroll' => Employee::sum('salary') ?? 0,
                        'lateToday' => Attendance::whereDate('date', today())->where('status', 'late')->count(),
                    ];
                    break;

                case 'legal_officer':
                    $stats = [
                        'totalContracts' => CrmContract::count(),
                        'activeContracts' => CrmContract::where('status', 'active')->count(),
                        'totalProjects' => Project::count(),
                        'activeProjects' => Project::where('status', 'in_progress')->count(),
                        'totalDeals' => CrmDeal::count(),
                    ];
                    break;

                case 'receptionist':
                case 'call_center_agent':
                    $stats = [
                        'totalLeads' => CrmLead::count(),
                        'newLeads' => CrmLead::where('status', 'new')->count(),
                        'qualifiedLeads' => CrmLead::where('status', 'qualified')->count(),
                        'myLeads' => CrmLead::where('assigned_to', $user->id)->count(),
                        'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
                        'totalContacts' => CrmContact::count(),
                    ];
                    break;

                case 'logistics_officer':
                    $stats = [
                        'totalProducts' => Product::count(),
                        'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
                        'totalWarehouses' => Warehouse::count(),
                        'activeWarehouses' => Warehouse::where('is_active', true)->count(),
                        'pendingTransfers' => Transfer::where('status', 'pending')->count(),
                        'totalStockMovements' => StockMovement::count(),
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
                        'myTickets' => HelpdeskTicket::where('assigned_to', $user->id)->count(),
                        'openTickets' => HelpdeskTicket::where('assigned_to', $user->id)->where('status', 'open')->count(),
                        'inProgressTickets' => HelpdeskTicket::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
                        'resolvedTickets' => HelpdeskTicket::where('assigned_to', $user->id)->where('status', 'resolved')->count(),
                    ];
                    break;

                case 'ict_officer':
                    $stats = [
                        'openTickets' => HelpdeskTicket::where('status', 'open')->count(),
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
                        'myTasks' => \App\Models\ProjectTask::where('assigned_to', $user->id)->count(),
                    ];
                    break;
            }
        } catch (\Exception $e) {
            $stats = ['error' => false];
        }

        return $stats;
    }

    private function getKpiCardsForRole(string $role, $user): array
    {
        $stats = $this->getStatsForRole($role, $user);
        $cards = [];

        switch ($role) {
            case 'admin':
            case 'administrator':
            case 'admin_manager':
                $cards = [
                    ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'icon' => 'people', 'color' => '#10B981'],
                    ['label' => 'Total Sales', 'value' => $stats['totalSales'] ?? 0, 'icon' => 'receipt_long', 'color' => '#0EA5E9', 'isMoney' => true],
                    ['label' => 'Total Expenses', 'value' => $stats['totalExpenses'] ?? 0, 'icon' => 'payments', 'color' => '#F59E0B', 'isMoney' => true],
                    ['label' => 'Net Profit', 'value' => $stats['netProfit'] ?? 0, 'icon' => 'trending_up', 'color' => '#8B5CF6', 'isMoney' => true],
                    ['label' => 'Open Tickets', 'value' => $stats['openTickets'] ?? 0, 'icon' => 'confirmation_number', 'color' => '#EF4444'],
                    ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'folder_open', 'color' => '#06B6D4'],
                ];
                break;

            case 'director':
                $cards = [
                    ['label' => 'Total Revenue', 'value' => $stats['totalRevenues'] ?? 0, 'icon' => 'account_balance', 'color' => '#10B981', 'isMoney' => true],
                    ['label' => 'Net Profit', 'value' => $stats['netProfit'] ?? 0, 'icon' => 'trending_up', 'color' => '#8B5CF6', 'isMoney' => true],
                    ['label' => 'Outstanding', 'value' => $stats['salesBalance'] ?? 0, 'icon' => 'pending_actions', 'color' => '#EF4444', 'isMoney' => true],
                    ['label' => 'Active Projects', 'value' => $stats['activeProjects'] ?? 0, 'icon' => 'folder_open', 'color' => '#0EA5E9'],
                    ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'people', 'color' => '#F59E0B'],
                    ['label' => 'Proposals', 'value' => $stats['totalProposals'] ?? 0, 'icon' => 'description', 'color' => '#06B6D4'],
                ];
                break;

            case 'finance_officer':
                $cards = [
                    ['label' => 'Total Sales', 'value' => $stats['totalSales'] ?? 0, 'icon' => 'receipt_long', 'color' => '#10B981', 'isMoney' => true],
                    ['label' => 'Outstanding', 'value' => $stats['salesBalance'] ?? 0, 'icon' => 'pending_actions', 'color' => '#EF4444', 'isMoney' => true],
                    ['label' => 'Month Expenses', 'value' => $stats['monthExpenses'] ?? 0, 'icon' => 'payments', 'color' => '#F59E0B', 'isMoney' => true],
                    ['label' => 'Month Revenue', 'value' => $stats['monthRevenues'] ?? 0, 'icon' => 'trending_up', 'color' => '#8B5CF6', 'isMoney' => true],
                    ['label' => 'Overdue', 'value' => $stats['overdueInvoices'] ?? 0, 'icon' => 'warning', 'color' => '#EF4444'],
                    ['label' => 'Net Profit', 'value' => $stats['netProfit'] ?? 0, 'icon' => 'account_balance', 'color' => '#10B981', 'isMoney' => true],
                ];
                break;

            case 'hr_officer':
                $cards = [
                    ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0, 'icon' => 'people', 'color' => '#10B981'],
                    ['label' => 'Active', 'value' => $stats['activeEmployees'] ?? 0, 'icon' => 'verified', 'color' => '#0EA5E9'],
                    ['label' => 'Pending Leaves', 'value' => $stats['pendingLeaves'] ?? 0, 'icon' => 'event_busy', 'color' => '#F59E0B'],
                    ['label' => 'Present Today', 'value' => $stats['todayAttendance'] ?? 0, 'icon' => 'check_circle', 'color' => '#10B981'],
                    ['label' => 'Absent Today', 'value' => $stats['absentToday'] ?? 0, 'icon' => 'cancel', 'color' => '#EF4444'],
                    ['label' => 'Total Payroll', 'value' => $stats['totalPayroll'] ?? 0, 'icon' => 'payments', 'color' => '#8B5CF6', 'isMoney' => true],
                ];
                break;

            case 'cashier':
                $cards = [
                    ['label' => "Today's Sales", 'value' => $stats['todaySales'] ?? 0, 'icon' => 'point_of_sale', 'color' => '#10B981', 'isMoney' => true],
                    ['label' => "Today's Count", 'value' => $stats['todayCount'] ?? 0, 'icon' => 'receipt', 'color' => '#0EA5E9'],
                    ['label' => 'Month Sales', 'value' => $stats['monthSales'] ?? 0, 'icon' => 'trending_up', 'color' => '#F59E0B', 'isMoney' => true],
                    ['label' => 'Products', 'value' => $stats['totalProducts'] ?? 0, 'icon' => 'inventory_2', 'color' => '#8B5CF6'],
                ];
                break;

            default:
                $cards = [
                    ['label' => 'Projects', 'value' => Project::count(), 'icon' => 'folder', 'color' => '#10B981'],
                ];
                break;
        }

        return $cards;
    }

    private function getQuickActionsForRole(string $role): array
    {
        return match ($role) {
            'admin', 'administrator', 'admin_manager' => [
                ['label' => 'Manage Users', 'route' => '/users', 'icon' => 'people'],
                ['label' => 'Reports', 'route' => '/reports', 'icon' => 'bar_chart'],
                ['label' => 'Projects', 'route' => '/projects', 'icon' => 'folder'],
                ['label' => 'Employees', 'route' => '/employees', 'icon' => 'badge'],
                ['label' => 'Sales', 'route' => '/sales-invoices', 'icon' => 'receipt'],
                ['label' => 'Settings', 'route' => '/settings', 'icon' => 'settings'],
            ],
            'director' => [
                ['label' => 'Reports', 'route' => '/reports', 'icon' => 'bar_chart'],
                ['label' => 'Projects', 'route' => '/projects', 'icon' => 'folder'],
                ['label' => 'Sales Dashboard', 'route' => '/sales-dashboard', 'icon' => 'dashboard'],
                ['label' => 'Employees', 'route' => '/employees', 'icon' => 'badge'],
                ['label' => 'Proposals', 'route' => '/proposals', 'icon' => 'description'],
                ['label' => 'Approvals', 'route' => '/approvals', 'icon' => 'check_circle'],
            ],
            'finance_officer' => [
                ['label' => 'Sales Invoices', 'route' => '/sales-invoices', 'icon' => 'receipt'],
                ['label' => 'Expenses', 'route' => '/expenses', 'icon' => 'payments'],
                ['label' => 'Revenues', 'route' => '/revenues', 'icon' => 'trending_up'],
                ['label' => 'Purchases', 'route' => '/purchase-invoices', 'icon' => 'shopping_bag'],
                ['label' => 'Bank Accounts', 'route' => '/bank-accounts', 'icon' => 'account_balance'],
                ['label' => 'Reports', 'route' => '/reports', 'icon' => 'bar_chart'],
            ],
            'hr_officer' => [
                ['label' => 'Employees', 'route' => '/employees', 'icon' => 'people'],
                ['label' => 'Attendance', 'route' => '/attendance', 'icon' => 'event_available'],
                ['label' => 'Leaves', 'route' => '/leaves', 'icon' => 'event_busy'],
                ['label' => 'Payroll', 'route' => '/payroll', 'icon' => 'payments'],
                ['label' => 'Performance', 'route' => '/performance', 'icon' => 'assessment'],
                ['label' => 'Recruitment', 'route' => '/recruitment', 'icon' => 'person_add'],
            ],
            'cashier' => [
                ['label' => 'POS Terminal', 'route' => '/pos', 'icon' => 'point_of_sale'],
                ['label' => 'POS Reports', 'route' => '/pos-reports', 'icon' => 'bar_chart'],
                ['label' => 'Products', 'route' => '/products', 'icon' => 'inventory'],
                ['label' => 'Sales Invoices', 'route' => '/sales-invoices', 'icon' => 'receipt'],
            ],
            'supervisor' => [
                ['label' => 'Attendance', 'route' => '/attendance', 'icon' => 'event_available'],
                ['label' => 'Leaves', 'route' => '/leaves', 'icon' => 'event_busy'],
                ['label' => 'Employees', 'route' => '/employees', 'icon' => 'people'],
                ['label' => 'POS Reports', 'route' => '/pos-reports', 'icon' => 'bar_chart'],
                ['label' => 'Projects', 'route' => '/projects', 'icon' => 'folder'],
            ],
            default => [
                ['label' => 'Dashboard', 'route' => '/dashboard', 'icon' => 'dashboard'],
                ['label' => 'Profile', 'route' => '/profile', 'icon' => 'person'],
            ],
        };
    }

    private function getChartDataForRole(string $role): array
    {
        $labels = [];
        $values = [];
        $secondaryValues = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');

            switch ($role) {
                case 'admin':
                case 'administrator':
                case 'admin_manager':
                case 'director':
                    $values[] = (int)(SalesInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
                    $secondaryValues[] = (int)(PurchaseInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
                    break;
                case 'finance_officer':
                case 'auditor':
                    $values[] = (int)(Revenue::whereDate('revenue_date', $date)->sum('amount') ?? 0);
                    $secondaryValues[] = (int)(Expense::whereDate('expense_date', $date)->sum('amount') ?? 0);
                    break;
                case 'hr_officer':
                case 'supervisor':
                    $values[] = Attendance::whereDate('date', $date)->where('status', 'present')->count();
                    $secondaryValues[] = Attendance::whereDate('date', $date)->where('status', 'absent')->count();
                    break;
                case 'cashier':
                    $values[] = (int)(PosSale::whereDate('created_at', $date)->sum('total_amount') ?? 0);
                    break;
                default:
                    $values[] = 0;
                    break;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'secondaryValues' => $secondaryValues,
        ];
    }

    private function getRecentActivity(string $role, $user): array
    {
        $items = [];
        try {
            switch ($role) {
                case 'admin':
                case 'administrator':
                case 'admin_manager':
                case 'director':
                    $items = [
                        'recentSales' => SalesInvoice::latest()->take(5)->get()->toArray(),
                        'recentTickets' => HelpdeskTicket::latest()->take(5)->get()->toArray(),
                        'recentProjects' => Project::where('status', 'in_progress')->latest()->take(5)->get()->toArray(),
                    ];
                    break;
                case 'finance_officer':
                    $items = [
                        'recentSales' => SalesInvoice::latest()->take(5)->get()->toArray(),
                        'recentExpenses' => Expense::latest()->take(5)->get()->toArray(),
                        'recentRevenues' => Revenue::latest()->take(5)->get()->toArray(),
                    ];
                    break;
                case 'hr_officer':
                    $items = [
                        'recentEmployees' => Employee::latest()->take(5)->get()->toArray(),
                        'recentLeaves' => Leave::latest()->take(5)->get()->toArray(),
                        'todayAttendance' => Attendance::whereDate('date', today())->latest()->take(10)->get()->toArray(),
                    ];
                    break;
                case 'cashier':
                    $items = [
                        'recentSales' => PosSale::latest()->take(10)->get()->toArray(),
                    ];
                    break;
                default:
                    $items = [];
                    break;
            }
        } catch (\Exception $e) {
            $items = [];
        }
        return $items;
    }

    private function getNotifications($user): array
    {
        return [];
    }
}
