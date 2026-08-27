<?php

namespace App\Providers;

use App\Models\ApprovalRequest;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Project;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Observers\ApprovalRequestObserver;
use App\Policies\DocumentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\PayrollPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\PurchaseInvoicePolicy;
use App\Policies\SalesInvoicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ApprovalRequest::observe(ApprovalRequestObserver::class);

        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(SalesInvoice::class, SalesInvoicePolicy::class);
        Gate::policy(PurchaseInvoice::class, PurchaseInvoicePolicy::class);
        Gate::policy(Payroll::class, PayrollPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
    }
}
