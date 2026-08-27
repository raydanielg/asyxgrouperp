<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\FleetController;
use App\Http\Controllers\Api\ExpenseRevenueController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\BusinessFlowController;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware(['auth:sanctum', 'api-permission'])->group(function () {
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // ═══ Role-Based Dashboard ═══
    Route::get('/dashboard/role', [DashboardController::class, 'roleDashboard'])->name('api.dashboard.role');
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications'])->name('api.dashboard.notifications');
    Route::post('/dashboard/notifications/{id}/read', [DashboardController::class, 'markNotificationRead'])->name('api.dashboard.notifications.read');

    // ═══ Multi-Company ═══
    Route::apiResource('companies', CompanyController::class)->names(['index' => 'api.companies.index', 'store' => 'api.companies.store', 'show' => 'api.companies.show', 'update' => 'api.companies.update', 'destroy' => 'api.companies.destroy']);
    Route::get('/companies/{company}/consolidated', [CompanyController::class, 'consolidated'])->name('api.companies.consolidated');

    // ═══ Employees ═══
    Route::apiResource('employees', EmployeeController::class)->names(['index' => 'api.employees.index', 'store' => 'api.employees.store', 'show' => 'api.employees.show', 'update' => 'api.employees.update', 'destroy' => 'api.employees.destroy']);
    Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('api.employees.attendance');
    Route::get('/employees/{employee}/payroll', [EmployeeController::class, 'payroll'])->name('api.employees.payroll');
    Route::get('/employees/{employee}/leaves', [EmployeeController::class, 'leaves'])->name('api.employees.leaves');

    // ═══ Attendance ═══
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('api.attendance.index');
    Route::get('/attendance/today', [AttendanceController::class, 'today'])->name('api.attendance.today');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('api.attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('api.attendance.clock-out');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('api.attendance.store');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('api.attendance.destroy');

    // ═══ Leaves ═══
    Route::get('/leaves', [LeaveController::class, 'index'])->name('api.leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('api.leaves.store');
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('api.leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('api.leaves.reject');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('api.leaves.destroy');

    // ═══ Payroll ═══
    Route::get('/payroll', [PayrollController::class, 'index'])->name('api.payroll.index');
    Route::get('/payroll/{payroll}', [PayrollController::class, 'show'])->name('api.payroll.show');
    Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('api.payroll.generate');
    Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('api.payroll.approve');

    // ═══ POS ═══
    Route::get('/pos/products', [PosController::class, 'products'])->name('api.pos.products');
    Route::post('/pos/sell', [PosController::class, 'sell'])->name('api.pos.sell');
    Route::get('/pos/sales', [PosController::class, 'sales'])->name('api.pos.sales');
    Route::get('/pos/today-summary', [PosController::class, 'todaySummary'])->name('api.pos.today-summary');
    Route::get('/pos/sales/{posSale}', [PosController::class, 'show'])->name('api.pos.sales.show');

    // ═══ Fleet Management ═══
    Route::get('/fleet', [FleetController::class, 'index'])->name('api.fleet.index');
    Route::get('/fleet/{vehicle}', [FleetController::class, 'show'])->name('api.fleet.show');
    Route::post('/fleet', [FleetController::class, 'store'])->name('api.fleet.store');
    Route::put('/fleet/{vehicle}', [FleetController::class, 'update'])->name('api.fleet.update');
    Route::post('/fleet/{vehicle}/maintenance', [FleetController::class, 'addMaintenance'])->name('api.fleet.maintenance');
    Route::post('/fleet/{vehicle}/fuel', [FleetController::class, 'addFuelLog'])->name('api.fleet.fuel');
    Route::delete('/fleet/{vehicle}', [FleetController::class, 'destroy'])->name('api.fleet.destroy');

    // ═══ Expenses & Revenues ═══
    Route::get('/expenses', [ExpenseRevenueController::class, 'expenses'])->name('api.expenses.index');
    Route::post('/expenses', [ExpenseRevenueController::class, 'storeExpense'])->name('api.expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseRevenueController::class, 'destroyExpense'])->name('api.expenses.destroy');
    Route::get('/revenues', [ExpenseRevenueController::class, 'revenues'])->name('api.revenues.index');
    Route::post('/revenues', [ExpenseRevenueController::class, 'storeRevenue'])->name('api.revenues.store');
    Route::delete('/revenues/{revenue}', [ExpenseRevenueController::class, 'destroyRevenue'])->name('api.revenues.destroy');
    Route::get('/bank-accounts', [ExpenseRevenueController::class, 'bankAccounts'])->name('api.bank-accounts.index');
    Route::post('/bank-accounts', [ExpenseRevenueController::class, 'storeBankAccount'])->name('api.bank-accounts.store');
    Route::get('/financial-summary', [ExpenseRevenueController::class, 'financialSummary'])->name('api.financial-summary');

    // ═══ CRM ═══
    Route::get('/crm/leads', [CrmController::class, 'leads'])->name('api.crm.leads');
    Route::post('/crm/leads', [CrmController::class, 'storeLead'])->name('api.crm.leads.store');
    Route::put('/crm/leads/{lead}', [CrmController::class, 'updateLead'])->name('api.crm.leads.update');
    Route::post('/crm/leads/{lead}/convert', [CrmController::class, 'convertLeadToDeal'])->name('api.crm.leads.convert');
    Route::delete('/crm/leads/{lead}', [CrmController::class, 'destroyLead'])->name('api.crm.leads.destroy');
    Route::get('/crm/deals', [CrmController::class, 'deals'])->name('api.crm.deals');
    Route::post('/crm/deals', [CrmController::class, 'storeDeal'])->name('api.crm.deals.store');
    Route::put('/crm/deals/{deal}', [CrmController::class, 'updateDeal'])->name('api.crm.deals.update');
    Route::delete('/crm/deals/{deal}', [CrmController::class, 'destroyDeal'])->name('api.crm.deals.destroy');
    Route::get('/crm/contacts', [CrmController::class, 'contacts'])->name('api.crm.contacts');
    Route::post('/crm/contacts', [CrmController::class, 'storeContact'])->name('api.crm.contacts.store');
    Route::delete('/crm/contacts/{contact}', [CrmController::class, 'destroyContact'])->name('api.crm.contacts.destroy');
    Route::get('/crm/contracts', [CrmController::class, 'contracts'])->name('api.crm.contracts');
    Route::post('/crm/contracts', [CrmController::class, 'storeContract'])->name('api.crm.contracts.store');
    Route::delete('/crm/contracts/{contract}', [CrmController::class, 'destroyContract'])->name('api.crm.contracts.destroy');

    // ═══ Business Flow ═══
    Route::get('/tenders', [BusinessFlowController::class, 'tenders'])->name('api.tenders.index');
    Route::post('/tenders', [BusinessFlowController::class, 'storeTender'])->name('api.tenders.store');
    Route::get('/tenders/{tender}', [BusinessFlowController::class, 'showTender'])->name('api.tenders.show');
    Route::delete('/tenders/{tender}', [BusinessFlowController::class, 'destroyTender'])->name('api.tenders.destroy');
    Route::get('/quotations', [BusinessFlowController::class, 'quotations'])->name('api.quotations.index');
    Route::post('/quotations', [BusinessFlowController::class, 'storeQuotation'])->name('api.quotations.store');
    Route::delete('/quotations/{quotation}', [BusinessFlowController::class, 'destroyQuotation'])->name('api.quotations.destroy');
    Route::get('/lpos', [BusinessFlowController::class, 'lpos'])->name('api.lpos.index');
    Route::post('/lpos', [BusinessFlowController::class, 'storeLpo'])->name('api.lpos.store');
    Route::delete('/lpos/{lpo}', [BusinessFlowController::class, 'destroyLpo'])->name('api.lpos.destroy');
    Route::get('/grns', [BusinessFlowController::class, 'grns'])->name('api.grns.index');
    Route::post('/grns', [BusinessFlowController::class, 'storeGrn'])->name('api.grns.store');
    Route::get('/delivery-notes', [BusinessFlowController::class, 'deliveryNotes'])->name('api.delivery-notes.index');
    Route::post('/delivery-notes', [BusinessFlowController::class, 'storeDeliveryNote'])->name('api.delivery-notes.store');
    Route::get('/vendor-invoices', [BusinessFlowController::class, 'vendorInvoices'])->name('api.vendor-invoices.index');
    Route::post('/vendor-invoices', [BusinessFlowController::class, 'storeVendorInvoice'])->name('api.vendor-invoices.store');
    Route::get('/office-expenses', [BusinessFlowController::class, 'officeExpenses'])->name('api.office-expenses.index');
    Route::post('/office-expenses', [BusinessFlowController::class, 'storeOfficeExpense'])->name('api.office-expenses.store');
    Route::post('/office-expenses/{expense}/approve', [BusinessFlowController::class, 'approveOfficeExpense'])->name('api.office-expenses.approve');
    Route::post('/office-expenses/{expense}/reject', [BusinessFlowController::class, 'rejectOfficeExpense'])->name('api.office-expenses.reject');
    Route::get('/client-receipts', [BusinessFlowController::class, 'clientReceipts'])->name('api.client-receipts.index');
    Route::post('/client-receipts', [BusinessFlowController::class, 'storeClientReceipt'])->name('api.client-receipts.store');
    Route::get('/proposals', [BusinessFlowController::class, 'proposals'])->name('api.proposals.index');
    Route::post('/proposals', [BusinessFlowController::class, 'storeProposal'])->name('api.proposals.store');
    Route::get('/budgets', [BusinessFlowController::class, 'budgets'])->name('api.budgets.index');
    Route::post('/budgets', [BusinessFlowController::class, 'storeBudget'])->name('api.budgets.store');
    Route::post('/budgets/{budget}/approve', [BusinessFlowController::class, 'approveBudget'])->name('api.budgets.approve');

    // ═══ Projects ═══
    Route::apiResource('projects', ProjectController::class)->names(['index' => 'api.projects.index', 'store' => 'api.projects.store', 'show' => 'api.projects.show', 'update' => 'api.projects.update', 'destroy' => 'api.projects.destroy']);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks'])->name('api.projects.tasks');
    Route::get('/projects/{project}/budget', [ProjectController::class, 'budget'])->name('api.projects.budget');
    Route::get('/projects/{project}/profitability', [ProjectController::class, 'profitability'])->name('api.projects.profitability');

    // ═══ Products & Inventory ═══
    Route::apiResource('products', ProductController::class)->names(['index' => 'api.products.index', 'store' => 'api.products.store', 'show' => 'api.products.show', 'update' => 'api.products.update', 'destroy' => 'api.products.destroy']);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('api.products.low-stock');
    Route::get('/stock-movements', [ProductController::class, 'stockMovements'])->name('api.stock-movements.index');

    // ═══ Invoices ═══
    Route::get('/sales-invoices', [InvoiceController::class, 'salesInvoices'])->name('api.sales-invoices.index');
    Route::get('/sales-invoices/{invoice}', [InvoiceController::class, 'salesInvoiceShow'])->name('api.sales-invoices.show');
    Route::get('/purchase-invoices', [InvoiceController::class, 'purchaseInvoices'])->name('api.purchase-invoices.index');
    Route::get('/purchase-invoices/{invoice}', [InvoiceController::class, 'purchaseInvoiceShow'])->name('api.purchase-invoices.show');

    // ═══ CRM (legacy) ═══
    Route::get('/customers', [CustomerController::class, 'index'])->name('api.customers.index');
    Route::get('/leads', [CustomerController::class, 'leads'])->name('api.leads.index');
    Route::get('/deals', [CustomerController::class, 'deals'])->name('api.deals.index');

    // ═══ Helpdesk ═══
    Route::apiResource('tickets', TicketController::class)->names(['index' => 'api.tickets.index', 'store' => 'api.tickets.store', 'show' => 'api.tickets.show', 'update' => 'api.tickets.update', 'destroy' => 'api.tickets.destroy']);
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('api.tickets.reply');

    // ═══ Reports ═══
    Route::get('/reports/financial-summary', [ReportController::class, 'financialSummary'])->name('api.reports.financial-summary');
    Route::get('/reports/sales-summary', [ReportController::class, 'salesSummary'])->name('api.reports.sales-summary');
    Route::get('/reports/project-summary', [ReportController::class, 'projectSummary'])->name('api.reports.project-summary');
    Route::get('/reports/employee-summary', [ReportController::class, 'employeeSummary'])->name('api.reports.employee-summary');
    Route::get('/reports/inventory-summary', [ReportController::class, 'inventorySummary'])->name('api.reports.inventory-summary');

    // ═══ Dashboard KPI ═══
    Route::get('/dashboard/kpi', [ReportController::class, 'dashboardKpi'])->name('api.dashboard.kpi');
    Route::get('/dashboard/charts', [ReportController::class, 'dashboardCharts'])->name('api.dashboard.charts');
});
