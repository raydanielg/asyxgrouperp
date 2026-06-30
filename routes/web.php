<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/services', function () {
    return view('pages.services');
})->name('services');

Route::get('/sectors-clients', function () {
    return view('pages.sectors');
})->name('sectors');

Route::get('/why-asyx', function () {
    return view('pages.why-asyx');
})->name('why-asyx');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/hosting', function () {
    return view('pages.hosting');
})->name('hosting');

// Public Careers
Route::get('/careers', [\App\Http\Controllers\Admin\ErpExtendedController::class, 'careersJobsIndex'])->name('careers');
Route::get('/careers/{jobPosting}/apply', [\App\Http\Controllers\Admin\ErpExtendedController::class, 'careersApplyForm'])->name('careers.apply');
Route::post('/careers/{jobPosting}/apply', [\App\Http\Controllers\Admin\ErpExtendedController::class, 'careersApplySubmit'])->name('careers.apply.submit');

Auth::routes(['reset' => false, 'register' => false]);

// Registration disabled — show notice
Route::get('/register', function () {
    return view('auth.register-disabled');
})->name('register');

// Custom password reset with activation code
Route::get('password/reset', [App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetCode'])->name('password.email');
Route::get('password/code', [App\Http\Controllers\Auth\PasswordResetController::class, 'showCodeForm'])->name('password.code');
Route::post('password/code', [App\Http\Controllers\Auth\PasswordResetController::class, 'verifyCode'])->name('password.code.verify');
Route::post('password/resend', [App\Http\Controllers\Auth\PasswordResetController::class, 'resendCode'])->name('password.resend');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->name('password.update');

Route::get('/register/success', [App\Http\Controllers\Auth\RegisterController::class, 'showSuccess'])->name('register.success');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Role-based Dashboard (non-admin users)
Route::get('/dashboard', [App\Http\Controllers\RoleDashboardController::class, 'index'])->name('role.dashboard')->middleware('auth');
Route::get('/dashboard/report-pdf', [App\Http\Controllers\RoleDashboardController::class, 'reportPdf'])->name('role.dashboard.report-pdf')->middleware('auth');

Route::get('/role/{module}', [App\Http\Controllers\RolePageController::class, 'page'])->name('role.page')->middleware('auth');

// Reception AJAX routes (used by receptionist role pages)
Route::prefix('reception')->middleware('auth')->group(function () {
    Route::get('visitors', [App\Http\Controllers\Reception\VisitorController::class, 'index'])->name('reception.visitors.index');
    Route::post('visitors', [App\Http\Controllers\Reception\VisitorController::class, 'store'])->name('reception.visitors.store');
    Route::put('visitors/{visitor}', [App\Http\Controllers\Reception\VisitorController::class, 'update'])->name('reception.visitors.update');
    Route::delete('visitors/{visitor}', [App\Http\Controllers\Reception\VisitorController::class, 'destroy'])->name('reception.visitors.destroy');
    Route::post('visitors/{visitor}/checkout', [App\Http\Controllers\Reception\VisitorController::class, 'checkOut'])->name('reception.visitors.checkout');

    Route::get('appointments', [App\Http\Controllers\Reception\AppointmentController::class, 'index'])->name('reception.appointments.index');
    Route::post('appointments', [App\Http\Controllers\Reception\AppointmentController::class, 'store'])->name('reception.appointments.store');
    Route::put('appointments/{appointment}', [App\Http\Controllers\Reception\AppointmentController::class, 'update'])->name('reception.appointments.update');
    Route::delete('appointments/{appointment}', [App\Http\Controllers\Reception\AppointmentController::class, 'destroy'])->name('reception.appointments.destroy');
    Route::post('appointments/{appointment}/complete', [App\Http\Controllers\Reception\AppointmentController::class, 'complete'])->name('reception.appointments.complete');
    Route::post('appointments/{appointment}/cancel', [App\Http\Controllers\Reception\AppointmentController::class, 'cancel'])->name('reception.appointments.cancel');

    Route::get('calls', [App\Http\Controllers\Reception\CallController::class, 'index'])->name('reception.calls.index');
    Route::post('calls', [App\Http\Controllers\Reception\CallController::class, 'store'])->name('reception.calls.store');
    Route::put('calls/{call}', [App\Http\Controllers\Reception\CallController::class, 'update'])->name('reception.calls.update');
    Route::delete('calls/{call}', [App\Http\Controllers\Reception\CallController::class, 'destroy'])->name('reception.calls.destroy');
    Route::post('calls/{call}/status', [App\Http\Controllers\Reception\CallController::class, 'markStatus'])->name('reception.calls.status');

    Route::get('correspondence', [App\Http\Controllers\Reception\CorrespondenceController::class, 'index'])->name('reception.correspondence.index');
    Route::post('correspondence', [App\Http\Controllers\Reception\CorrespondenceController::class, 'store'])->name('reception.correspondence.store');
    Route::put('correspondence/{correspondence}', [App\Http\Controllers\Reception\CorrespondenceController::class, 'update'])->name('reception.correspondence.update');
    Route::delete('correspondence/{correspondence}', [App\Http\Controllers\Reception\CorrespondenceController::class, 'destroy'])->name('reception.correspondence.destroy');
    Route::post('correspondence/{correspondence}/status', [App\Http\Controllers\Reception\CorrespondenceController::class, 'markStatus'])->name('reception.correspondence.status');

    Route::get('parcels', [App\Http\Controllers\Reception\ParcelController::class, 'index'])->name('reception.parcels.index');
    Route::post('parcels', [App\Http\Controllers\Reception\ParcelController::class, 'store'])->name('reception.parcels.store');
    Route::put('parcels/{parcel}', [App\Http\Controllers\Reception\ParcelController::class, 'update'])->name('reception.parcels.update');
    Route::delete('parcels/{parcel}', [App\Http\Controllers\Reception\ParcelController::class, 'destroy'])->name('reception.parcels.destroy');
    Route::post('parcels/{parcel}/deliver', [App\Http\Controllers\Reception\ParcelController::class, 'markDelivered'])->name('reception.parcels.deliver');

    Route::get('front-desk', [App\Http\Controllers\Reception\FrontDeskController::class, 'index'])->name('reception.front-desk.index');
    Route::post('front-desk', [App\Http\Controllers\Reception\FrontDeskController::class, 'store'])->name('reception.front-desk.store');
    Route::put('front-desk/{frontDesk}', [App\Http\Controllers\Reception\FrontDeskController::class, 'update'])->name('reception.front-desk.update');
    Route::delete('front-desk/{frontDesk}', [App\Http\Controllers\Reception\FrontDeskController::class, 'destroy'])->name('reception.front-desk.destroy');
    Route::post('front-desk/{frontDesk}/status', [App\Http\Controllers\Reception\FrontDeskController::class, 'markStatus'])->name('reception.front-desk.status');

    Route::get('departments', [App\Http\Controllers\Reception\DepartmentController::class, 'index'])->name('reception.departments.index');
    Route::post('departments', [App\Http\Controllers\Reception\DepartmentController::class, 'store'])->name('reception.departments.store');
    Route::put('departments/{department}', [App\Http\Controllers\Reception\DepartmentController::class, 'update'])->name('reception.departments.update');
    Route::delete('departments/{department}', [App\Http\Controllers\Reception\DepartmentController::class, 'destroy'])->name('reception.departments.destroy');

    Route::get('announcements', [App\Http\Controllers\Reception\AnnouncementController::class, 'index'])->name('reception.announcements.index');
    Route::post('announcements', [App\Http\Controllers\Reception\AnnouncementController::class, 'store'])->name('reception.announcements.store');
    Route::put('announcements/{announcement}', [App\Http\Controllers\Reception\AnnouncementController::class, 'update'])->name('reception.announcements.update');
    Route::delete('announcements/{announcement}', [App\Http\Controllers\Reception\AnnouncementController::class, 'destroy'])->name('reception.announcements.destroy');
    Route::post('announcements/{announcement}/toggle', [App\Http\Controllers\Reception\AnnouncementController::class, 'toggleStatus'])->name('reception.announcements.toggle');

    Route::get('reports', [App\Http\Controllers\Reception\ReportController::class, 'index'])->name('reception.reports.index');

    Route::get('my-account', [App\Http\Controllers\Reception\MyAccountController::class, 'index'])->name('reception.my-account.index');
    Route::put('my-account', [App\Http\Controllers\Reception\MyAccountController::class, 'update'])->name('reception.my-account.update');
    Route::post('my-account/password', [App\Http\Controllers\Reception\MyAccountController::class, 'updatePassword'])->name('reception.my-account.password');

    Route::get('messages', [App\Http\Controllers\Reception\MessageController::class, 'index'])->name('reception.messages.index');
    Route::post('messages', [App\Http\Controllers\Reception\MessageController::class, 'store'])->name('reception.messages.store');
    Route::get('messages/{message}', [App\Http\Controllers\Reception\MessageController::class, 'show'])->name('reception.messages.show');
    Route::delete('messages/{message}', [App\Http\Controllers\Reception\MessageController::class, 'destroy'])->name('reception.messages.destroy');
    Route::post('messages/{message}/status', [App\Http\Controllers\Reception\MessageController::class, 'markStatus'])->name('reception.messages.status');

    Route::get('salary-advance', [App\Http\Controllers\SalaryAdvanceController::class, 'index'])->name('reception.salary-advance.index');
    Route::post('salary-advance', [App\Http\Controllers\SalaryAdvanceController::class, 'store'])->name('reception.salary-advance.store');
    Route::delete('salary-advance/{salaryAdvanceRequest}', [App\Http\Controllers\SalaryAdvanceController::class, 'destroy'])->name('reception.salary-advance.destroy');
    Route::post('salary-advance/{salaryAdvanceRequest}/status', [App\Http\Controllers\SalaryAdvanceController::class, 'markStatus'])->name('reception.salary-advance.status');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::match(['put', 'patch'], '/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/users', [App\Http\Controllers\Admin\DashboardController::class, 'users'])->name('users');
    Route::get('/reports', [App\Http\Controllers\Admin\DashboardController::class, 'reports'])->name('reports');

    // ═══ Multi-Company ═══
    $companyCtrl = App\Http\Controllers\Admin\CompanyController::class;
    Route::get('/companies', [$companyCtrl, 'index'])->name('companies.index');
    Route::get('/companies/switch', [$companyCtrl, 'switchCompany'])->name('companies.switch');
    Route::get('/companies/create', [$companyCtrl, 'create'])->name('companies.create');
    Route::post('/companies', [$companyCtrl, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [$companyCtrl, 'show'])->name('companies.show');
    Route::get('/companies/{company}/edit', [$companyCtrl, 'edit'])->name('companies.edit');
    Route::patch('/companies/{company}', [$companyCtrl, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [$companyCtrl, 'destroy'])->name('companies.destroy');
    Route::get('/companies-consolidated', [$companyCtrl, 'consolidated'])->name('companies.consolidated');

    // Intercompany Transactions
    $ictCtrl = App\Http\Controllers\Admin\IntercompanyTransactionController::class;
    Route::get('/intercompany', [$ictCtrl, 'index'])->name('intercompany.index');
    Route::get('/intercompany/create', [$ictCtrl, 'create'])->name('intercompany.create');
    Route::post('/intercompany', [$ictCtrl, 'store'])->name('intercompany.store');
    Route::get('/intercompany/{intercompany}', [$ictCtrl, 'show'])->name('intercompany.show');
    Route::post('/intercompany/{intercompany}/eliminate', [$ictCtrl, 'eliminate'])->name('intercompany.eliminate');
    Route::delete('/intercompany/{intercompany}', [$ictCtrl, 'destroy'])->name('intercompany.destroy');

    // ═══ Approval Workflow Engine ═══
    $awCtrl = App\Http\Controllers\Admin\ApprovalWorkflowController::class;
    Route::get('/approvals', [$awCtrl, 'index'])->name('approvals.index');
    Route::get('/approvals/create', [$awCtrl, 'create'])->name('approvals.create');
    Route::post('/approvals', [$awCtrl, 'store'])->name('approvals.store');
    Route::get('/approvals/{workflow}', [$awCtrl, 'show'])->name('approvals.show');
    Route::delete('/approvals/{workflow}', [$awCtrl, 'destroy'])->name('approvals.destroy');
    Route::get('/approval-requests', [$awCtrl, 'requests'])->name('approvals.requests');
    Route::post('/approval-requests/{approvalRequest}/approve', [$awCtrl, 'approve'])->name('approvals.approve');
    Route::post('/approval-requests/{approvalRequest}/reject', [$awCtrl, 'reject'])->name('approvals.reject');

    // ═══ Fleet Management ═══
    $fleetCtrl = App\Http\Controllers\Admin\FleetController::class;
    Route::get('/fleet', [$fleetCtrl, 'index'])->name('fleet.index');
    Route::get('/fleet/create', [$fleetCtrl, 'create'])->name('fleet.create');
    Route::post('/fleet', [$fleetCtrl, 'store'])->name('fleet.store');
    Route::get('/fleet/{vehicle}', [$fleetCtrl, 'show'])->name('fleet.show');
    Route::get('/fleet/{vehicle}/edit', [$fleetCtrl, 'edit'])->name('fleet.edit');
    Route::patch('/fleet/{vehicle}', [$fleetCtrl, 'update'])->name('fleet.update');
    Route::delete('/fleet/{vehicle}', [$fleetCtrl, 'destroy'])->name('fleet.destroy');
    Route::post('/fleet/{vehicle}/maintenance', [$fleetCtrl, 'storeMaintenance'])->name('fleet.maintenance.store');
    Route::post('/fleet/{vehicle}/fuel', [$fleetCtrl, 'storeFuel'])->name('fleet.fuel.store');

    // ═══ Fixed Assets ═══
    $faCtrl = App\Http\Controllers\Admin\FixedAssetController::class;
    Route::get('/fixed-assets', [$faCtrl, 'index'])->name('fixed-assets.index');
    Route::get('/fixed-assets/create', [$faCtrl, 'create'])->name('fixed-assets.create');
    Route::post('/fixed-assets', [$faCtrl, 'store'])->name('fixed-assets.store');
    Route::get('/fixed-assets/{fixedAsset}', [$faCtrl, 'show'])->name('fixed-assets.show');
    Route::get('/fixed-assets/{fixedAsset}/edit', [$faCtrl, 'edit'])->name('fixed-assets.edit');
    Route::patch('/fixed-assets/{fixedAsset}', [$faCtrl, 'update'])->name('fixed-assets.update');
    Route::delete('/fixed-assets/{fixedAsset}', [$faCtrl, 'destroy'])->name('fixed-assets.destroy');
    Route::post('/fixed-assets/{fixedAsset}/depreciate', [$faCtrl, 'runDepreciation'])->name('fixed-assets.depreciate');
    Route::post('/fixed-assets/{fixedAsset}/dispose', [$faCtrl, 'dispose'])->name('fixed-assets.dispose');

    // ═══ Document Management ═══
    $docCtrl = App\Http\Controllers\Admin\DocumentController::class;
    Route::get('/documents', [$docCtrl, 'index'])->name('documents.index');
    Route::get('/documents/create', [$docCtrl, 'create'])->name('documents.create');
    Route::post('/documents', [$docCtrl, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [$docCtrl, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [$docCtrl, 'download'])->name('documents.download');
    Route::post('/documents/{document}/sign', [$docCtrl, 'sign'])->name('documents.sign');
    Route::post('/documents/{document}/decline', [$docCtrl, 'decline'])->name('documents.decline');
    Route::post('/documents/{document}/archive', [$docCtrl, 'archive'])->name('documents.archive');
    Route::post('/documents/{document}/upload-version', [$docCtrl, 'uploadVersion'])->name('documents.upload-version');
    Route::get('/projects/{project}/documents', [$docCtrl, 'projectDocuments'])->name('projects.documents');
    Route::delete('/documents/{document}', [$docCtrl, 'destroy'])->name('documents.destroy');

    // ═══ Call Center ═══
    $ccCtrl = App\Http\Controllers\Admin\CallCenterController::class;
    Route::get('/call-center', [$ccCtrl, 'index'])->name('call-center.index');
    Route::post('/call-center/campaigns', [$ccCtrl, 'storeCampaign'])->name('call-center.campaigns.store');
    Route::post('/call-center/calls', [$ccCtrl, 'storeCall'])->name('call-center.calls.store');
    Route::get('/call-center/calls', [$ccCtrl, 'calls'])->name('call-center.calls');

    // ═══ Audit Logs ═══
    $alCtrl = App\Http\Controllers\Admin\AuditLogController::class;
    Route::get('/audit-logs', [$alCtrl, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/filter', [$alCtrl, 'filter'])->name('audit-logs.filter');

    // ERP Modules
    $erp = App\Http\Controllers\Admin\ErpController::class;

    // Warehouses
    Route::get('/warehouses', [$erp, 'warehouseIndex'])->name('warehouses.index');
    Route::post('/warehouses', [$erp, 'warehouseStore'])->name('warehouses.store');
    Route::get('/warehouses/{warehouse}/edit', [$erp, 'warehouseEdit'])->name('warehouses.edit');
    Route::patch('/warehouses/{warehouse}', [$erp, 'warehouseUpdate'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [$erp, 'warehouseDestroy'])->name('warehouses.destroy');

    // Transfers
    Route::get('/transfers', [$erp, 'transferIndex'])->name('transfers.index');
    Route::post('/transfers', [$erp, 'transferStore'])->name('transfers.store');
    Route::delete('/transfers/{transfer}', [$erp, 'transferDestroy'])->name('transfers.destroy');

    // Plans
    Route::get('/plans', [$erp, 'planIndex'])->name('plans.index');
    Route::post('/plans', [$erp, 'planStore'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [$erp, 'planEdit'])->name('plans.edit');
    Route::patch('/plans/{plan}', [$erp, 'planUpdate'])->name('plans.update');
    Route::delete('/plans/{plan}', [$erp, 'planDestroy'])->name('plans.destroy');

    // Orders
    Route::get('/orders', [$erp, 'orderIndex'])->name('orders.index');
    Route::get('/orders/{order}', [$erp, 'orderShow'])->name('orders.show');

    // Coupons
    Route::get('/coupons', [$erp, 'couponIndex'])->name('coupons.index');
    Route::post('/coupons', [$erp, 'couponStore'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [$erp, 'couponEdit'])->name('coupons.edit');
    Route::patch('/coupons/{coupon}', [$erp, 'couponUpdate'])->name('coupons.update');
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
    Route::get('/purchase-invoices/create', [$erp, 'purchaseInvoiceCreate'])->name('purchase-invoices.create');
    Route::post('/purchase-invoices', [$erp, 'purchaseInvoiceStore'])->name('purchase-invoices.store');
    Route::get('/purchase-invoices/{purchaseInvoice}', [$erp, 'purchaseInvoiceShow'])->name('purchase-invoices.show');
    Route::get('/purchase-invoices/{purchaseInvoice}/edit', [$erp, 'purchaseInvoiceEdit'])->name('purchase-invoices.edit');
    Route::patch('/purchase-invoices/{purchaseInvoice}', [$erp, 'purchaseInvoiceUpdate'])->name('purchase-invoices.update');
    Route::post('/purchase-invoices/{purchaseInvoice}/post', [$erp, 'purchaseInvoicePost'])->name('purchase-invoices.post');
    Route::delete('/purchase-invoices/{purchaseInvoice}', [$erp, 'purchaseInvoiceDestroy'])->name('purchase-invoices.destroy');

    // Purchase Returns
    Route::get('/purchase-returns', [$erp, 'purchaseReturnIndex'])->name('purchase-returns.index');
    Route::get('/purchase-returns/{purchaseReturn}', [$erp, 'purchaseReturnShow'])->name('purchase-returns.show');
    Route::delete('/purchase-returns/{purchaseReturn}', [$erp, 'purchaseReturnDestroy'])->name('purchase-returns.destroy');

    // Sales Dashboard
    Route::get('/sales-dashboard', [$erp, 'salesDashboard'])->name('sales-dashboard');

    // Sales Invoices
    Route::get('/sales-invoices', [$erp, 'salesInvoiceIndex'])->name('sales-invoices.index');
    Route::get('/sales-invoices/create', [$erp, 'salesInvoiceCreate'])->name('sales-invoices.create');
    Route::post('/sales-invoices', [$erp, 'salesInvoiceStore'])->name('sales-invoices.store');
    Route::get('/sales-invoices/{salesInvoice}', [$erp, 'salesInvoiceShow'])->name('sales-invoices.show');
    Route::get('/sales-invoices/{salesInvoice}/edit', [$erp, 'salesInvoiceEdit'])->name('sales-invoices.edit');
    Route::patch('/sales-invoices/{salesInvoice}', [$erp, 'salesInvoiceUpdate'])->name('sales-invoices.update');
    Route::post('/sales-invoices/{salesInvoice}/post', [$erp, 'salesInvoicePost'])->name('sales-invoices.post');
    Route::delete('/sales-invoices/{salesInvoice}', [$erp, 'salesInvoiceDestroy'])->name('sales-invoices.destroy');
    Route::get('/sales-invoices/{salesInvoice}/pdf', [$erp, 'salesInvoicePdf'])->name('sales-invoices.pdf');
    Route::get('/sales-invoices/{salesInvoice}/receipt', [$erp, 'salesInvoiceReceipt'])->name('sales-invoices.receipt');
    Route::get('/sales-invoices/{salesInvoice}/receipt/pdf', [$erp, 'salesInvoiceReceiptPdf'])->name('sales-invoices.receipt-pdf');

    // Sales Returns
    Route::get('/sales-returns', [$erp, 'salesReturnIndex'])->name('sales-returns.index');
    Route::get('/sales-returns/{salesReturn}', [$erp, 'salesReturnShow'])->name('sales-returns.show');
    Route::delete('/sales-returns/{salesReturn}', [$erp, 'salesReturnDestroy'])->name('sales-returns.destroy');

    // Sales Proposals
    Route::get('/sales-proposals', [$erp, 'salesProposalIndex'])->name('sales-proposals.index');
    Route::get('/sales-proposals/create', [$erp, 'salesProposalCreate'])->name('sales-proposals.create');
    Route::post('/sales-proposals', [$erp, 'salesProposalStore'])->name('sales-proposals.store');
    Route::get('/sales-proposals/{salesProposal}', [$erp, 'salesProposalShow'])->name('sales-proposals.show');
    Route::get('/sales-proposals/{salesProposal}/edit', [$erp, 'salesProposalEdit'])->name('sales-proposals.edit');
    Route::patch('/sales-proposals/{salesProposal}', [$erp, 'salesProposalUpdate'])->name('sales-proposals.update');
    Route::patch('/sales-proposals/{salesProposal}/status', [$erp, 'salesProposalStatus'])->name('sales-proposals.status');
    Route::post('/sales-proposals/{salesProposal}/convert', [$erp, 'salesProposalConvert'])->name('sales-proposals.convert');
    Route::post('/sales-proposals/{proposal}/convert-to-project', [\App\Http\Controllers\Admin\ErpExtendedController::class, 'convertProposalToProject'])->name('sales-proposals.convert-to-project');
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

    // Notification Templates
    Route::get('/notification-templates', [$erp, 'notificationTemplateIndex'])->name('notification-templates.index');
    Route::get('/notification-templates/{notificationTemplate}/edit', [$erp, 'notificationTemplateEdit'])->name('notification-templates.edit');
    Route::patch('/notification-templates/{notificationTemplate}', [$erp, 'notificationTemplateUpdate'])->name('notification-templates.update');

    // Profile
    Route::get('/profile', [$erp, 'profile'])->name('profile');
    Route::patch('/profile', [$erp, 'profileUpdate'])->name('profile.update');
    Route::patch('/profile/password', [$erp, 'passwordUpdate'])->name('password.update');

    // User Management (ERP)
    Route::get('/users-manage', [$erp, 'userIndex'])->name('users-index');
    Route::get('/users-manage/create', [$erp, 'userCreate'])->name('users-create');
    Route::post('/users-manage', [$erp, 'userStore'])->name('users-store');
    Route::get('/users-manage/{user}/edit', [$erp, 'userEdit'])->name('users-edit');
    Route::patch('/users-manage/{user}', [$erp, 'userUpdate'])->name('users-update');
    Route::delete('/users-manage/{user}', [$erp, 'userDestroy'])->name('users-destroy');

    // ═══ Roles & Permissions ═══
    $roleCtrl = App\Http\Controllers\Admin\RoleController::class;
    Route::get('/roles', [$roleCtrl, 'index'])->name('roles.index');
    Route::get('/roles/create', [$roleCtrl, 'create'])->name('roles.create');
    Route::post('/roles', [$roleCtrl, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [$roleCtrl, 'edit'])->name('roles.edit');
    Route::patch('/roles/{role}', [$roleCtrl, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [$roleCtrl, 'destroy'])->name('roles.destroy');

    // ═══ Enhanced User Management ═══
    $userCtrl = App\Http\Controllers\Admin\UserController::class;
    Route::get('/users', [$userCtrl, 'index'])->name('users.index');
    Route::get('/users/create', [$userCtrl, 'create'])->name('users.create');
    Route::post('/users', [$userCtrl, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [$userCtrl, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [$userCtrl, 'update'])->name('users.update');
    Route::delete('/users/{user}', [$userCtrl, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/password', [$userCtrl, 'changePassword'])->name('users.change-password');
    Route::get('/users-login-history', [$userCtrl, 'loginHistory'])->name('users.login-history');
    Route::post('/users/{user}/impersonate', [$userCtrl, 'impersonate'])->name('users.impersonate');
    Route::post('/users/stop-impersonating', [$userCtrl, 'stopImpersonating'])->name('users.stop-impersonating');

    // ═══ Extended ERP Modules ═══
    $ext = App\Http\Controllers\Admin\ErpExtendedController::class;

    // ─── HRM ───
    Route::get('/employees', [$ext, 'employeeIndex'])->name('employees.index');
    Route::get('/employees/create', [$ext, 'employeeCreate'])->name('employees.create');
    Route::post('/employees', [$ext, 'employeeStore'])->name('employees.store');
    Route::get('/employees/{employee}', [$ext, 'employeeShow'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [$ext, 'employeeEdit'])->name('employees.edit');
    Route::patch('/employees/{employee}', [$ext, 'employeeUpdate'])->name('employees.update');
    Route::delete('/employees/{employee}', [$ext, 'employeeDestroy'])->name('employees.destroy');

    // Attendance
    $attCtrl = App\Http\Controllers\Admin\AttendanceController::class;
    Route::get('/attendance', [$attCtrl, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [$attCtrl, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [$attCtrl, 'clockOut'])->name('attendance.clock-out');
    Route::post('/attendance/clock-out-all', [$attCtrl, 'clockOutAll'])->name('attendance.clock-out-all');
    Route::post('/attendance', [$attCtrl, 'store'])->name('attendance.store');
    Route::delete('/attendance/{attendance}', [$attCtrl, 'destroy'])->name('attendance.destroy');

    $payCtrl = App\Http\Controllers\Admin\PayrollController::class;
    Route::get('/payroll', [$payCtrl, 'index'])->name('payroll.index');
    Route::get('/payroll/generate', [$payCtrl, 'generateForm'])->name('payroll.generate-form');
    Route::post('/payroll/generate', [$payCtrl, 'generate'])->name('payroll.generate');
    Route::get('/payroll/{payroll}', [$payCtrl, 'show'])->name('payroll.show');
    Route::post('/payroll', [$payCtrl, 'store'])->name('payroll.store');
    Route::patch('/payroll/{payroll}', [$payCtrl, 'update'])->name('payroll.update');
    Route::delete('/payroll/{payroll}', [$payCtrl, 'destroy'])->name('payroll.destroy');
    Route::get('/payroll/{payroll}/pdf', [$payCtrl, 'pdf'])->name('payroll.pdf');

    Route::get('/leaves', [$ext, 'leaveIndex'])->name('leaves.index');
    Route::post('/leaves', [$ext, 'leaveStore'])->name('leaves.store');
    Route::patch('/leaves/{leave}/approve', [$ext, 'leaveApprove'])->name('leaves.approve');
    Route::patch('/leaves/{leave}/reject', [$ext, 'leaveReject'])->name('leaves.reject');
    Route::delete('/leaves/{leave}', [$ext, 'leaveDestroy'])->name('leaves.destroy');

    Route::get('/performance', [$ext, 'performanceIndex'])->name('performance.index');
    Route::post('/performance', [$ext, 'performanceStore'])->name('performance.store');
    Route::delete('/performance/{review}', [$ext, 'performanceDestroy'])->name('performance.destroy');

    Route::get('/training', [$ext, 'trainingIndex'])->name('training.index');
    Route::post('/training', [$ext, 'trainingStore'])->name('training.store');
    Route::delete('/training/{training}', [$ext, 'trainingDestroy'])->name('training.destroy');

    Route::get('/job-postings', [$ext, 'jobPostingIndex'])->name('job-postings.index');
    Route::post('/job-postings', [$ext, 'jobPostingStore'])->name('job-postings.store');
    Route::delete('/job-postings/{jobPosting}', [$ext, 'jobPostingDestroy'])->name('job-postings.destroy');
    // Applications
    Route::get('/applications', [$ext, 'applicationsIndex'])->name('applications.index');
    Route::get('/applications/create', [$ext, 'applicationCreate'])->name('applications.create');
    Route::post('/applications', [$ext, 'applicationStore'])->name('applications.store');
    Route::get('/job-postings/{jobPosting}/applications', [$ext, 'applicationsForJob'])->name('job-postings.applications');
    Route::get('/applications/{application}', [$ext, 'applicationShow'])->name('applications.show');
    Route::post('/applications/{application}/approve', [$ext, 'applicationApprove'])->name('applications.approve');

    Route::get('/employee-assets', [$ext, 'assetIndex'])->name('assets.index');
    Route::post('/employee-assets', [$ext, 'assetStore'])->name('assets.store');
    Route::delete('/employee-assets/{asset}', [$ext, 'assetDestroy'])->name('assets.destroy');

    Route::get('/hr-events', [$ext, 'hrEventIndex'])->name('hr-events.index');
    Route::post('/hr-events', [$ext, 'hrEventStore'])->name('hr-events.store');
    Route::delete('/hr-events/{hrEvent}', [$ext, 'hrEventDestroy'])->name('hr-events.destroy');

    Route::get('/policies', [$ext, 'policyIndex'])->name('policies.index');
    Route::post('/policies', [$ext, 'policyStore'])->name('policies.store');
    Route::delete('/policies/{policy}', [$ext, 'policyDestroy'])->name('policies.destroy');

    // ─── CRM ───
    Route::get('/crm-leads', [$ext, 'crmLeadIndex'])->name('crm-leads.index');
    Route::post('/crm-leads', [$ext, 'crmLeadStore'])->name('crm-leads.store');
    Route::get('/crm-leads/{lead}/pdf', [$ext, 'crmLeadPdf'])->name('crm-leads.pdf');
    Route::delete('/crm-leads/{lead}', [$ext, 'crmLeadDestroy'])->name('crm-leads.destroy');

    Route::get('/crm-deals', [$ext, 'crmDealIndex'])->name('crm-deals.index');
    Route::post('/crm-deals', [$ext, 'crmDealStore'])->name('crm-deals.store');
    Route::get('/crm-deals/{deal}/pdf', [$ext, 'crmDealPdf'])->name('crm-deals.pdf');
    Route::delete('/crm-deals/{deal}', [$ext, 'crmDealDestroy'])->name('crm-deals.destroy');

    Route::get('/crm-contracts', [$ext, 'crmContractIndex'])->name('crm-contracts.index');
    Route::post('/crm-contracts', [$ext, 'crmContractStore'])->name('crm-contracts.store');
    Route::delete('/crm-contracts/{contract}', [$ext, 'crmContractDestroy'])->name('crm-contracts.destroy');

    Route::get('/crm-contacts', [$ext, 'crmContactIndex'])->name('crm-contacts.index');
    Route::post('/crm-contacts', [$ext, 'crmContactStore'])->name('crm-contacts.store');
    Route::delete('/crm-contacts/{contact}', [$ext, 'crmContactDestroy'])->name('crm-contacts.destroy');

    // ─── Accounting ───
    Route::get('/bank-accounts', [$ext, 'bankAccountIndex'])->name('bank-accounts.index');
    Route::post('/bank-accounts', [$ext, 'bankAccountStore'])->name('bank-accounts.store');
    Route::put('/bank-accounts/{bankAccount}', [$ext, 'bankAccountUpdate'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [$ext, 'bankAccountDestroy'])->name('bank-accounts.destroy');

    Route::get('/acc-transfers', [$ext, 'accTransferIndex'])->name('acc-transfers.index');
    Route::post('/acc-transfers', [$ext, 'accTransferStore'])->name('acc-transfers.store');
    Route::delete('/acc-transfers/{transfer}', [$ext, 'accTransferDestroy'])->name('acc-transfers.destroy');

    Route::get('/expenses', [$ext, 'expenseIndex'])->name('expenses.index');
    Route::post('/expenses', [$ext, 'expenseStore'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [$ext, 'expenseDestroy'])->name('expenses.destroy');

    Route::get('/revenues', [$ext, 'revenueIndex'])->name('revenues.index');
    Route::post('/revenues', [$ext, 'revenueStore'])->name('revenues.store');
    Route::delete('/revenues/{revenue}', [$ext, 'revenueDestroy'])->name('revenues.destroy');

    Route::get('/bills', [$ext, 'billIndex'])->name('bills.index');
    Route::post('/bills', [$ext, 'billStore'])->name('bills.store');
    Route::delete('/bills/{bill}', [$ext, 'billDestroy'])->name('bills.destroy');

    Route::get('/estimates', [$ext, 'estimateIndex'])->name('estimates.index');
    Route::post('/estimates', [$ext, 'estimateStore'])->name('estimates.store');
    Route::delete('/estimates/{estimate}', [$ext, 'estimateDestroy'])->name('estimates.destroy');

    // ─── Projects ───
    Route::get('/projects', [$ext, 'projectIndex'])->name('projects.index');
    Route::post('/projects', [$ext, 'projectStore'])->name('projects.store');
    Route::get('/projects/{project}', [$ext, 'projectShow'])->name('projects.show');
    Route::post('/projects/{project}/generate-invoice', [\App\Http\Controllers\Admin\ErpExtendedController::class, 'generateProjectInvoice'])->name('projects.generate-invoice');
    Route::get('/projects/{project}/pdf', [$ext, 'projectPdf'])->name('projects.pdf');
    Route::delete('/projects/{project}', [$ext, 'projectDestroy'])->name('projects.destroy');
    Route::post('/projects/{project}/tasks', [$ext, 'projectTaskStore'])->name('projects.tasks.store');
    Route::delete('/projects/tasks/{task}', [$ext, 'projectTaskDestroy'])->name('projects.tasks.destroy');

    Route::get('/timesheets', [$ext, 'timesheetIndex'])->name('timesheets.index');
    Route::post('/timesheets', [$ext, 'timesheetStore'])->name('timesheets.store');
    Route::delete('/timesheets/{timesheet}', [$ext, 'timesheetDestroy'])->name('timesheets.destroy');

    Route::get('/bugs', [$ext, 'bugIndex'])->name('bugs.index');
    Route::post('/bugs', [$ext, 'bugStore'])->name('bugs.store');
    Route::delete('/bugs/{bug}', [$ext, 'bugDestroy'])->name('bugs.destroy');

    // ─── Products & Inventory ───
    Route::get('/product-categories', [$ext, 'productCategoryIndex'])->name('product-categories.index');
    Route::post('/product-categories', [$ext, 'productCategoryStore'])->name('product-categories.store');
    Route::delete('/product-categories/{category}', [$ext, 'productCategoryDestroy'])->name('product-categories.destroy');

    Route::get('/products', [$ext, 'productIndex'])->name('products.index');
    Route::post('/products', [$ext, 'productStore'])->name('products.store');
    Route::delete('/products/{product}', [$ext, 'productDestroy'])->name('products.destroy');

    Route::get('/suppliers', [$ext, 'supplierIndex'])->name('suppliers.index');
    Route::post('/suppliers', [$ext, 'supplierStore'])->name('suppliers.store');
    Route::delete('/suppliers/{supplier}', [$ext, 'supplierDestroy'])->name('suppliers.destroy');

    Route::get('/stock-movements', [$ext, 'stockMovementIndex'])->name('stock-movements.index');

    // ─── POS ───
    Route::get('/pos', [$ext, 'posIndex'])->name('pos.index');
    Route::post('/pos', [$ext, 'posStore'])->name('pos.store');
    Route::get('/pos/reports', [$ext, 'posReports'])->name('pos.reports');
    Route::get('/pos/{posSale}', [$ext, 'posSaleShow'])->name('pos.show');
    Route::delete('/pos/{posSale}', [$ext, 'posSaleDestroy'])->name('pos.destroy');

    // ═══ Business Flow ═══
    $bf = App\Http\Controllers\Admin\BusinessFlowController::class;
    Route::get('/business-flow', [$bf, 'dashboard'])->name('business-flow.dashboard');

    // Tenders
    Route::get('/tenders', [$bf, 'tenderIndex'])->name('tenders.index');
    Route::post('/tenders', [$bf, 'tenderStore'])->name('tenders.store');
    Route::get('/tenders/{tender}', [$bf, 'tenderShow'])->name('tenders.show');
    Route::delete('/tenders/{tender}', [$bf, 'tenderDestroy'])->name('tenders.destroy');
    Route::post('/tenders/{tender}/convert-to-lead', [$bf, 'tenderConvertToLead'])->name('tenders.convert-to-lead');

    // Quotations
    Route::get('/quotations', [$bf, 'quotationIndex'])->name('quotations.index');
    Route::post('/quotations', [$bf, 'quotationStore'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [$bf, 'quotationShow'])->name('quotations.show');
    Route::get('/quotations/{quotation}/pdf', [$bf, 'quotationPdf'])->name('quotations.pdf');
    Route::patch('/quotations/{quotation}/status', [$bf, 'quotationUpdateStatus'])->name('quotations.status');
    Route::delete('/quotations/{quotation}', [$bf, 'quotationDestroy'])->name('quotations.destroy');

    // Lead → Deal conversion
    Route::post('/crm-leads/{lead}/convert-to-deal', [$bf, 'leadConvertToDeal'])->name('crm-leads.convert-to-deal');

    // Deal → Project conversion
    Route::post('/crm-deals/{deal}/convert-to-project', [$bf, 'dealConvertToProject'])->name('crm-deals.convert-to-project');

    // Project Budgets
    Route::get('/budgets', [$bf, 'budgetIndex'])->name('budgets.index');
    Route::post('/budgets', [$bf, 'budgetStore'])->name('budgets.store');
    Route::post('/budgets/{budget}/approve', [$bf, 'budgetApprove'])->name('budgets.approve');
    Route::post('/budgets/{budget}/reject', [$bf, 'budgetReject'])->name('budgets.reject');
    Route::delete('/budgets/{budget}', [$bf, 'budgetDestroy'])->name('budgets.destroy');

    // LPOs
    Route::get('/lpos', [$bf, 'lpoIndex'])->name('lpos.index');
    Route::post('/lpos', [$bf, 'lpoStore'])->name('lpos.store');
    Route::get('/lpos/{lpo}', [$bf, 'lpoShow'])->name('lpos.show');
    Route::patch('/lpos/{lpo}/status', [$bf, 'lpoUpdateStatus'])->name('lpos.status');
    Route::delete('/lpos/{lpo}', [$bf, 'lpoDestroy'])->name('lpos.destroy');

    // GRNs
    Route::get('/grns', [$bf, 'grnIndex'])->name('grns.index');
    Route::post('/grns', [$bf, 'grnStore'])->name('grns.store');
    Route::get('/grns/{grn}', [$bf, 'grnShow'])->name('grns.show');
    Route::delete('/grns/{grn}', [$bf, 'grnDestroy'])->name('grns.destroy');

    // Delivery Notes
    Route::get('/delivery-notes', [$bf, 'deliveryNoteIndex'])->name('delivery-notes.index');
    Route::post('/delivery-notes', [$bf, 'deliveryNoteStore'])->name('delivery-notes.store');
    Route::delete('/delivery-notes/{deliveryNote}', [$bf, 'deliveryNoteDestroy'])->name('delivery-notes.destroy');

    // Vendor Invoices
    Route::get('/vendor-invoices', [$bf, 'vendorInvoiceIndex'])->name('vendor-invoices.index');
    Route::post('/vendor-invoices', [$bf, 'vendorInvoiceStore'])->name('vendor-invoices.store');
    Route::get('/vendor-invoices/{invoice}', [$bf, 'vendorInvoiceShow'])->name('vendor-invoices.show');
    Route::delete('/vendor-invoices/{invoice}', [$bf, 'vendorInvoiceDestroy'])->name('vendor-invoices.destroy');

    // Vendor Payments
    Route::post('/vendor-payments', [$bf, 'vendorPaymentStore'])->name('vendor-payments.store');

    // Office Expenses
    Route::get('/office-expenses', [$bf, 'officeExpenseIndex'])->name('office-expenses.index');
    Route::post('/office-expenses', [$bf, 'officeExpenseStore'])->name('office-expenses.store');
    Route::post('/office-expenses/{expense}/approve', [$bf, 'officeExpenseApprove'])->name('office-expenses.approve');
    Route::post('/office-expenses/{expense}/reject', [$bf, 'officeExpenseReject'])->name('office-expenses.reject');
    Route::delete('/office-expenses/{expense}', [$bf, 'officeExpenseDestroy'])->name('office-expenses.destroy');

    // Client Receipts
    Route::get('/client-receipts', [$bf, 'clientReceiptIndex'])->name('client-receipts.index');
    Route::post('/client-receipts', [$bf, 'clientReceiptStore'])->name('client-receipts.store');
    Route::delete('/client-receipts/{receipt}', [$bf, 'clientReceiptDestroy'])->name('client-receipts.destroy');

    // Project Profit
    Route::get('/projects/{project}/profit', [$bf, 'projectProfit'])->name('projects.profit');

    // ═══ Meetings ═══
    $mtgCtrl = App\Http\Controllers\Admin\MeetingController::class;
    Route::get('/meetings', [$mtgCtrl, 'index'])->name('meetings.index');
    Route::get('/meetings/create', [$mtgCtrl, 'create'])->name('meetings.create');
    Route::post('/meetings', [$mtgCtrl, 'store'])->name('meetings.store');
    Route::get('/meetings/{meeting}', [$mtgCtrl, 'show'])->name('meetings.show');
    Route::get('/meetings/{meeting}/edit', [$mtgCtrl, 'edit'])->name('meetings.edit');
    Route::patch('/meetings/{meeting}', [$mtgCtrl, 'update'])->name('meetings.update');
    Route::delete('/meetings/{meeting}', [$mtgCtrl, 'destroy'])->name('meetings.destroy');
    Route::post('/meetings/{meeting}/attendance', [$mtgCtrl, 'recordAttendance'])->name('meetings.attendance');
    Route::patch('/meetings/action-points/{actionPoint}', [$mtgCtrl, 'updateActionPoint'])->name('meetings.action-points.update');

    // Recurring Project Invoice
    Route::post('/projects/{project}/generate-recurring-invoice', [$mtgCtrl, 'generateInvoice'])->name('projects.generate-recurring-invoice');
});
