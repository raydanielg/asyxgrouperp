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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Multi-Company
    Route::apiResource('companies', CompanyController::class);
    Route::get('/companies/{company}/consolidated', [CompanyController::class, 'consolidated']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance']);
    Route::get('/employees/{employee}/payroll', [EmployeeController::class, 'payroll']);
    Route::get('/employees/{employee}/leaves', [EmployeeController::class, 'leaves']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('/projects/{project}/budget', [ProjectController::class, 'budget']);
    Route::get('/projects/{project}/profitability', [ProjectController::class, 'profitability']);

    // Products & Inventory
    Route::apiResource('products', ProductController::class);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/stock-movements', [ProductController::class, 'stockMovements']);

    // Invoices
    Route::get('/sales-invoices', [InvoiceController::class, 'salesInvoices']);
    Route::get('/sales-invoices/{invoice}', [InvoiceController::class, 'salesInvoiceShow']);
    Route::get('/purchase-invoices', [InvoiceController::class, 'purchaseInvoices']);
    Route::get('/purchase-invoices/{invoice}', [InvoiceController::class, 'purchaseInvoiceShow']);

    // CRM
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/leads', [CustomerController::class, 'leads']);
    Route::get('/deals', [CustomerController::class, 'deals']);

    // Helpdesk
    Route::apiResource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply']);

    // Reports
    Route::get('/reports/financial-summary', [ReportController::class, 'financialSummary']);
    Route::get('/reports/sales-summary', [ReportController::class, 'salesSummary']);
    Route::get('/reports/project-summary', [ReportController::class, 'projectSummary']);
    Route::get('/reports/employee-summary', [ReportController::class, 'employeeSummary']);
    Route::get('/reports/inventory-summary', [ReportController::class, 'inventorySummary']);

    // Dashboard KPI
    Route::get('/dashboard/kpi', [ReportController::class, 'dashboardKpi']);
    Route::get('/dashboard/charts', [ReportController::class, 'dashboardCharts']);
});
