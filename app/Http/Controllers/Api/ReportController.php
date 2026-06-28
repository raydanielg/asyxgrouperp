<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Project;
use App\Models\HelpdeskTicket;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboardKpi()
    {
        $weekAgo = now()->subDays(7);
        return response()->json([
            'total_sales' => SalesInvoice::sum('total_amount'),
            'total_sales_balance' => SalesInvoice::sum('balance_amount'),
            'total_purchases' => PurchaseInvoice::sum('total_amount'),
            'total_expenses' => Expense::sum('amount'),
            'total_revenues' => Revenue::sum('amount'),
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'in_progress')->count(),
            'open_tickets' => HelpdeskTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'total_leads' => CrmLead::count(),
            'new_leads_week' => CrmLead::where('created_at', '>=', $weekAgo)->count(),
            'open_deals' => CrmDeal::where('status', 'open')->count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
            'present_today' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
        ]);
    }

    public function dashboardCharts()
    {
        $dailyLabels = [];
        $dailySales = [];
        $dailyPurchases = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailySales[] = (int) (SalesInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
            $dailyPurchases[] = (int) (PurchaseInvoice::whereDate('created_at', $date)->sum('total_amount') ?? 0);
        }

        return response()->json([
            'daily_labels' => $dailyLabels,
            'daily_sales' => $dailySales,
            'daily_purchases' => $dailyPurchases,
        ]);
    }

    public function financialSummary()
    {
        $totalSales = SalesInvoice::sum('total_amount');
        $totalPaid = SalesInvoice::sum('paid_amount');
        $totalBalance = SalesInvoice::sum('balance_amount');
        $totalPurchases = PurchaseInvoice::sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $totalRevenues = Revenue::sum('amount');

        return response()->json([
            'summary' => [
                'total_sales' => $totalSales,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalBalance,
                'total_purchases' => $totalPurchases,
                'total_expenses' => $totalExpenses,
                'total_revenues' => $totalRevenues,
                'net_income' => $totalRevenues - $totalExpenses,
                'collection_rate' => $totalSales > 0 ? round(($totalPaid / $totalSales) * 100, 2) : 0,
            ],
            'sales_by_status' => [
                'draft' => SalesInvoice::where('status', 'draft')->count(),
                'posted' => SalesInvoice::where('status', 'posted')->count(),
                'paid' => SalesInvoice::where('status', 'paid')->count(),
                'overdue' => SalesInvoice::where('status', 'overdue')->count(),
            ],
        ]);
    }

    public function salesSummary()
    {
        return response()->json([
            'total_invoices' => SalesInvoice::count(),
            'total_amount' => SalesInvoice::sum('total_amount'),
            'total_paid' => SalesInvoice::sum('paid_amount'),
            'total_balance' => SalesInvoice::sum('balance_amount'),
            'monthly_sales' => SalesInvoice::selectRaw("strftime('%Y-%m', created_at) as month, sum(total_amount) as total, count(*) as count")
                ->groupBy('month')->orderBy('month', 'desc')->limit(12)->get(),
        ]);
    }

    public function projectSummary()
    {
        return response()->json([
            'total' => Project::count(),
            'by_status' => [
                'planning' => Project::where('status', 'planning')->count(),
                'in_progress' => Project::where('status', 'in_progress')->count(),
                'on_hold' => Project::where('status', 'on_hold')->count(),
                'completed' => Project::where('status', 'completed')->count(),
            ],
            'total_budget' => Project::sum('budget'),
        ]);
    }

    public function employeeSummary()
    {
        return response()->json([
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'by_department' => Employee::selectRaw('department, count(*) as count')->groupBy('department')->get(),
            'total_payroll' => Employee::sum('salary'),
            'present_today' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
        ]);
    }

    public function inventorySummary()
    {
        return response()->json([
            'total_products' => Product::count(),
            'total_value' => Product::sum(\DB::raw('stock_quantity * unit_price')),
            'low_stock' => Product::whereColumn('stock_quantity', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
        ]);
    }
}
