<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/register/success', [App\Http\Controllers\Auth\RegisterController::class, 'showSuccess'])->name('register.success');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\DashboardController::class, 'users'])->name('users');
    Route::get('/reports', [App\Http\Controllers\Admin\DashboardController::class, 'reports'])->name('reports');

    // ERP Modules
    $erp = App\Http\Controllers\Admin\ErpController::class;

    // Warehouses
    Route::get('/warehouses', [$erp, 'warehouseIndex'])->name('warehouses.index');
    Route::post('/warehouses', [$erp, 'warehouseStore'])->name('warehouses.store');
    Route::patch('/warehouses/{warehouse}', [$erp, 'warehouseUpdate'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [$erp, 'warehouseDestroy'])->name('warehouses.destroy');

    // Transfers
    Route::get('/transfers', [$erp, 'transferIndex'])->name('transfers.index');
    Route::post('/transfers', [$erp, 'transferStore'])->name('transfers.store');
    Route::delete('/transfers/{transfer}', [$erp, 'transferDestroy'])->name('transfers.destroy');

    // Plans
    Route::get('/plans', [$erp, 'planIndex'])->name('plans.index');
    Route::post('/plans', [$erp, 'planStore'])->name('plans.store');
    Route::patch('/plans/{plan}', [$erp, 'planUpdate'])->name('plans.update');
    Route::delete('/plans/{plan}', [$erp, 'planDestroy'])->name('plans.destroy');

    // Orders
    Route::get('/orders', [$erp, 'orderIndex'])->name('orders.index');
    Route::get('/orders/{order}', [$erp, 'orderShow'])->name('orders.show');

    // Coupons
    Route::get('/coupons', [$erp, 'couponIndex'])->name('coupons.index');
    Route::post('/coupons', [$erp, 'couponStore'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [$erp, 'couponDestroy'])->name('coupons.destroy');

    // Helpdesk
    Route::get('/helpdesk-categories', [$erp, 'helpdeskCategoryIndex'])->name('helpdesk-categories.index');
    Route::post('/helpdesk-categories', [$erp, 'helpdeskCategoryStore'])->name('helpdesk-categories.store');
    Route::delete('/helpdesk-categories/{category}', [$erp, 'helpdeskCategoryDestroy'])->name('helpdesk-categories.destroy');

    Route::get('/helpdesk-tickets', [$erp, 'helpdeskTicketIndex'])->name('helpdesk-tickets.index');
    Route::post('/helpdesk-tickets', [$erp, 'helpdeskTicketStore'])->name('helpdesk-tickets.store');
    Route::get('/helpdesk-tickets/{ticket}', [$erp, 'helpdeskTicketShow'])->name('helpdesk-tickets.show');
    Route::post('/helpdesk-tickets/{ticket}/replies', [$erp, 'helpdeskReplyStore'])->name('helpdesk-replies.store');
    Route::patch('/helpdesk-tickets/{ticket}/status', [$erp, 'helpdeskTicketUpdateStatus'])->name('helpdesk-tickets.status');

    // Purchase Invoices
    Route::get('/purchase-invoices', [$erp, 'purchaseInvoiceIndex'])->name('purchase-invoices.index');
    Route::post('/purchase-invoices', [$erp, 'purchaseInvoiceStore'])->name('purchase-invoices.store');
    Route::get('/purchase-invoices/{purchaseInvoice}', [$erp, 'purchaseInvoiceShow'])->name('purchase-invoices.show');
    Route::post('/purchase-invoices/{purchaseInvoice}/post', [$erp, 'purchaseInvoicePost'])->name('purchase-invoices.post');
    Route::delete('/purchase-invoices/{purchaseInvoice}', [$erp, 'purchaseInvoiceDestroy'])->name('purchase-invoices.destroy');

    // Purchase Returns
    Route::get('/purchase-returns', [$erp, 'purchaseReturnIndex'])->name('purchase-returns.index');
    Route::get('/purchase-returns/{purchaseReturn}', [$erp, 'purchaseReturnShow'])->name('purchase-returns.show');
    Route::delete('/purchase-returns/{purchaseReturn}', [$erp, 'purchaseReturnDestroy'])->name('purchase-returns.destroy');

    // Sales Invoices
    Route::get('/sales-invoices', [$erp, 'salesInvoiceIndex'])->name('sales-invoices.index');
    Route::post('/sales-invoices', [$erp, 'salesInvoiceStore'])->name('sales-invoices.store');
    Route::get('/sales-invoices/{salesInvoice}', [$erp, 'salesInvoiceShow'])->name('sales-invoices.show');
    Route::post('/sales-invoices/{salesInvoice}/post', [$erp, 'salesInvoicePost'])->name('sales-invoices.post');
    Route::delete('/sales-invoices/{salesInvoice}', [$erp, 'salesInvoiceDestroy'])->name('sales-invoices.destroy');

    // Sales Returns
    Route::get('/sales-returns', [$erp, 'salesReturnIndex'])->name('sales-returns.index');
    Route::get('/sales-returns/{salesReturn}', [$erp, 'salesReturnShow'])->name('sales-returns.show');
    Route::delete('/sales-returns/{salesReturn}', [$erp, 'salesReturnDestroy'])->name('sales-returns.destroy');

    // Sales Proposals
    Route::get('/sales-proposals', [$erp, 'salesProposalIndex'])->name('sales-proposals.index');
    Route::post('/sales-proposals', [$erp, 'salesProposalStore'])->name('sales-proposals.store');
    Route::get('/sales-proposals/{salesProposal}', [$erp, 'salesProposalShow'])->name('sales-proposals.show');
    Route::patch('/sales-proposals/{salesProposal}/status', [$erp, 'salesProposalStatus'])->name('sales-proposals.status');
    Route::delete('/sales-proposals/{salesProposal}', [$erp, 'salesProposalDestroy'])->name('sales-proposals.destroy');

    // Email Templates
    Route::get('/email-templates', [$erp, 'emailTemplateIndex'])->name('email-templates.index');
    Route::patch('/email-templates/{emailTemplate}', [$erp, 'emailTemplateUpdate'])->name('email-templates.update');

    // Settings
    Route::get('/settings', [$erp, 'settingsIndex'])->name('settings');
    Route::post('/settings', [$erp, 'settingsUpdate'])->name('settings.update');

    // Login History
    Route::get('/login-history', [$erp, 'loginHistory'])->name('login-history');

    // Bank Transfers
    Route::get('/bank-transfers', [$erp, 'bankTransferIndex'])->name('bank-transfers.index');
    Route::patch('/bank-transfers/{bankTransfer}', [$erp, 'bankTransferUpdate'])->name('bank-transfers.update');

    // Add-ons
    Route::get('/add-ons', [$erp, 'addOnIndex'])->name('add-ons.index');
    Route::patch('/add-ons/{addOn}/toggle', [$erp, 'addOnToggle'])->name('add-ons.toggle');

    // Messenger
    Route::get('/messenger', [$erp, 'messengerIndex'])->name('messenger.index');

    // Media
    Route::get('/media', [$erp, 'mediaIndex'])->name('media.index');
});
