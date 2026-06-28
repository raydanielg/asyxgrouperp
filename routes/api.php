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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ═══ Role-Based Dashboard ═══
    Route::get('/dashboard/role', [DashboardController::class, 'roleDashboard']);
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);
    Route::post('/dashboard/notifications/{id}/read', [DashboardController::class, 'markNotificationRead']);

    // ═══ Multi-Company ═══
    Route::apiResource('companies', CompanyController::class);
    Route::get('/companies/{company}/consolidated', [CompanyController::class, 'consolidated']);

    // ═══ Employees ═══
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance']);
    Route::get('/employees/{employee}/payroll', [EmployeeController::class, 'payroll']);
    Route::get('/employees/{employee}/leaves', [EmployeeController::class, 'leaves']);

    // ═══ Attendance ═══
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy']);

    // ═══ Leaves ═══
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve']);
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject']);
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy']);

    // ═══ Payroll ═══
    Route::get('/payroll', [PayrollController::class, 'index']);
    Route::get('/payroll/{payroll}', [PayrollController::class, 'show']);
    Route::post('/payroll/generate', [PayrollController::class, 'generate']);
    Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve']);

    // ═══ POS ═══
    Route::get('/pos/products', [PosController::class, 'products']);
    Route::post('/pos/sell', [PosController::class, 'sell']);
    Route::get('/pos/sales', [PosController::class, 'sales']);
    Route::get('/pos/today-summary', [PosController::class, 'todaySummary']);
    Route::get('/pos/sales/{posSale}', [PosController::class, 'show']);

    // ═══ Fleet Management ═══
    Route::get('/fleet', [FleetController::class, 'index']);
    Route::get('/fleet/{vehicle}', [FleetController::class, 'show']);
    Route::post('/fleet', [FleetController::class, 'store']);
    Route::put('/fleet/{vehicle}', [FleetController::class, 'update']);
    Route::post('/fleet/{vehicle}/maintenance', [FleetController::class, 'addMaintenance']);
    Route::post('/fleet/{vehicle}/fuel', [FleetController::class, 'addFuelLog']);
    Route::delete('/fleet/{vehicle}', [FleetController::class, 'destroy']);

    // ═══ Expenses & Revenues ═══
    Route::get('/expenses', [ExpenseRevenueController::class, 'expenses']);
    Route::post('/expenses', [ExpenseRevenueController::class, 'storeExpense']);
    Route::delete('/expenses/{expense}', [ExpenseRevenueController::class, 'destroyExpense']);
    Route::get('/revenues', [ExpenseRevenueController::class, 'revenues']);
    Route::post('/revenues', [ExpenseRevenueController::class, 'storeRevenue']);
    Route::delete('/revenues/{revenue}', [ExpenseRevenueController::class, 'destroyRevenue']);
    Route::get('/bank-accounts', [ExpenseRevenueController::class, 'bankAccounts']);
    Route::post('/bank-accounts', [ExpenseRevenueController::class, 'storeBankAccount']);
    Route::get('/financial-summary', [ExpenseRevenueController::class, 'financialSummary']);

    // ═══ CRM ═══
    Route::get('/crm/leads', [CrmController::class, 'leads']);
    Route::post('/crm/leads', [CrmController::class, 'storeLead']);
    Route::put('/crm/leads/{lead}', [CrmController::class, 'updateLead']);
    Route::post('/crm/leads/{lead}/convert', [CrmController::class, 'convertLeadToDeal']);
    Route::delete('/crm/leads/{lead}', [CrmController::class, 'destroyLead']);
    Route::get('/crm/deals', [CrmController::class, 'deals']);
    Route::post('/crm/deals', [CrmController::class, 'storeDeal']);
    Route::put('/crm/deals/{deal}', [CrmController::class, 'updateDeal']);
    Route::delete('/crm/deals/{deal}', [CrmController::class, 'destroyDeal']);
    Route::get('/crm/contacts', [CrmController::class, 'contacts']);
    Route::post('/crm/contacts', [CrmController::class, 'storeContact']);
    Route::delete('/crm/contacts/{contact}', [CrmController::class, 'destroyContact']);
    Route::get('/crm/contracts', [CrmController::class, 'contracts']);
    Route::post('/crm/contracts', [CrmController::class, 'storeContract']);
    Route::delete('/crm/contracts/{contract}', [CrmController::class, 'destroyContract']);

    // ═══ Business Flow ═══
    Route::get('/tenders', [BusinessFlowController::class, 'tenders']);
    Route::post('/tenders', [BusinessFlowController::class, 'storeTender']);
    Route::get('/tenders/{tender}', [BusinessFlowController::class, 'showTender']);
    Route::delete('/tenders/{tender}', [BusinessFlowController::class, 'destroyTender']);
    Route::get('/quotations', [BusinessFlowController::class, 'quotations']);
    Route::post('/quotations', [BusinessFlowController::class, 'storeQuotation']);
    Route::delete('/quotations/{quotation}', [BusinessFlowController::class, 'destroyQuotation']);
    Route::get('/lpos', [BusinessFlowController::class, 'lpos']);
    Route::post('/lpos', [BusinessFlowController::class, 'storeLpo']);
    Route::delete('/lpos/{lpo}', [BusinessFlowController::class, 'destroyLpo']);
    Route::get('/grns', [BusinessFlowController::class, 'grns']);
    Route::post('/grns', [BusinessFlowController::class, 'storeGrn']);
    Route::get('/delivery-notes', [BusinessFlowController::class, 'deliveryNotes']);
    Route::post('/delivery-notes', [BusinessFlowController::class, 'storeDeliveryNote']);
    Route::get('/vendor-invoices', [BusinessFlowController::class, 'vendorInvoices']);
    Route::post('/vendor-invoices', [BusinessFlowController::class, 'storeVendorInvoice']);
    Route::get('/office-expenses', [BusinessFlowController::class, 'officeExpenses']);
    Route::post('/office-expenses', [BusinessFlowController::class, 'storeOfficeExpense']);
    Route::post('/office-expenses/{expense}/approve', [BusinessFlowController::class, 'approveOfficeExpense']);
    Route::post('/office-expenses/{expense}/reject', [BusinessFlowController::class, 'rejectOfficeExpense']);
    Route::get('/client-receipts', [BusinessFlowController::class, 'clientReceipts']);
    Route::post('/client-receipts', [BusinessFlowController::class, 'storeClientReceipt']);
    Route::get('/proposals', [BusinessFlowController::class, 'proposals']);
    Route::post('/proposals', [BusinessFlowController::class, 'storeProposal']);
    Route::get('/budgets', [BusinessFlowController::class, 'budgets']);
    Route::post('/budgets', [BusinessFlowController::class, 'storeBudget']);
    Route::post('/budgets/{budget}/approve', [BusinessFlowController::class, 'approveBudget']);

    // ═══ Projects ═══
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('/projects/{project}/budget', [ProjectController::class, 'budget']);
    Route::get('/projects/{project}/profitability', [ProjectController::class, 'profitability']);

    // ═══ Products & Inventory ═══
    Route::apiResource('products', ProductController::class);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/stock-movements', [ProductController::class, 'stockMovements']);

    // ═══ Invoices ═══
    Route::get('/sales-invoices', [InvoiceController::class, 'salesInvoices']);
    Route::get('/sales-invoices/{invoice}', [InvoiceController::class, 'salesInvoiceShow']);
    Route::get('/purchase-invoices', [InvoiceController::class, 'purchaseInvoices']);
    Route::get('/purchase-invoices/{invoice}', [InvoiceController::class, 'purchaseInvoiceShow']);

    // ═══ CRM (legacy) ═══
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/leads', [CustomerController::class, 'leads']);
    Route::get('/deals', [CustomerController::class, 'deals']);

    // ═══ Helpdesk ═══
    Route::apiResource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply']);

    // ═══ Reports ═══
    Route::get('/reports/financial-summary', [ReportController::class, 'financialSummary']);
    Route::get('/reports/sales-summary', [ReportController::class, 'salesSummary']);
    Route::get('/reports/project-summary', [ReportController::class, 'projectSummary']);
    Route::get('/reports/employee-summary', [ReportController::class, 'employeeSummary']);
    Route::get('/reports/inventory-summary', [ReportController::class, 'inventorySummary']);

    // ═══ Dashboard KPI ═══
    Route::get('/dashboard/kpi', [ReportController::class, 'dashboardKpi']);
    Route::get('/dashboard/charts', [ReportController::class, 'dashboardCharts']);
});
