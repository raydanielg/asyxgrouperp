<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoutePermission
{
    protected array $routePermissions = [
        'admin.companies*' => 'view-dashboard',
        'admin.companies.consolidated*' => 'view-dashboard',
        'admin.intercompany*' => 'view-dashboard',
        'admin.approvals*' => 'view-dashboard',
        'admin.fleet*' => 'view-dashboard',
        'admin.fixed-assets*' => 'view-dashboard',
        'admin.documents*' => 'view-dashboard',
        'admin.call-center*' => 'view-dashboard',
        'admin.audit-logs*' => 'view-dashboard',
        'admin.business-flow*' => 'view-dashboard',
        'admin.tenders*' => 'view-dashboard',
        'admin.quotations*' => 'view-dashboard',
        'admin.budgets*' => 'view-dashboard',
        'admin.lpos*' => 'view-dashboard',
        'admin.grns*' => 'view-dashboard',
        'admin.delivery-notes*' => 'view-dashboard',
        'admin.vendor-invoices*' => 'view-dashboard',
        'admin.office-expenses*' => 'view-expenses',
        'admin.client-receipts*' => 'view-revenues',
        'admin.meetings*' => 'view-dashboard',
        'admin.settlements*' => 'view-projects',
        'admin.users*' => 'view-users',
        'admin.roles*' => 'view-roles',
        'admin.users.login-history*' => 'view-login-history',
        'admin.profile*' => 'view-dashboard',
        'admin.employees*' => 'view-employees',
        'admin.attendance*' => 'view-attendance',
        'admin.payroll*' => 'view-payroll',
        'admin.leaves*' => 'view-leaves',
        'admin.performance*' => 'view-performance',
        'admin.training*' => 'view-training',
        'admin.job-postings*' => 'view-recruitment',
        'admin.applications*' => 'view-recruitment',
        'admin.assets*' => 'view-assets',
        'admin.hr-events*' => 'view-events',
        'admin.policies*' => 'view-policies',
        'admin.bonuses*' => 'view-employees',
        'admin.crm-leads*' => 'view-crm-leads',
        'admin.crm-deals*' => 'view-crm-deals',
        'admin.crm-contracts*' => 'view-crm-contracts',
        'admin.crm-contacts*' => 'view-crm-contacts',
        'admin.bank-accounts*' => 'view-bank-accounts',
        'admin.acc-transfers*' => 'view-acc-transfers',
        'admin.expenses*' => 'view-expenses',
        'admin.revenues*' => 'view-revenues',
        'admin.bills*' => 'view-bills',
        'admin.estimates*' => 'view-dashboard',
        'admin.projects*' => 'view-projects',
        'admin.timesheets*' => 'view-timesheets',
        'admin.bugs*' => 'view-bugs',
        'admin.products*' => 'view-products',
        'admin.product-categories*' => 'view-product-categories',
        'admin.suppliers*' => 'view-suppliers',
        'admin.stock-movements*' => 'view-stock-movements',
        'admin.warehouses*' => 'view-warehouses',
        'admin.transfers*' => 'view-acc-transfers',
        'admin.sales-dashboard*' => 'view-dashboard',
        'admin.sales-proposals*' => 'view-sales-invoices',
        'admin.sales-invoices*' => 'view-sales-invoices',
        'admin.sales-returns*' => 'view-sales-invoices',
        'admin.purchase-invoices*' => 'view-purchase-invoices',
        'admin.purchase-returns*' => 'view-purchase-invoices',
        'admin.pos*' => 'view-pos',
        'admin.plans*' => 'view-dashboard',
        'admin.orders*' => 'view-dashboard',
        'admin.coupons*' => 'view-dashboard',
        'admin.bank-transfers*' => 'view-dashboard',
        'admin.add-ons*' => 'view-settings',
        'admin.email-templates*' => 'view-settings',
        'admin.notification-templates*' => 'view-settings',
        'admin.media*' => 'view-settings',
        'admin.messenger*' => 'view-dashboard',
        'admin.reports*' => 'view-reports',
        'admin.settings*' => 'view-settings',
        'admin.system-mode' => 'view-settings',
        'admin.backup.*' => 'view-settings',
        'admin.documentation*' => 'view-dashboard',
        'admin.documentation.*' => 'view-dashboard',
        'role.page' => 'view-dashboard',
        'role.dashboard' => 'view-dashboard',
        'reception.*' => 'view-dashboard',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'admin' || $user->hasRole('admin')) {
            return $next($request);
        }

        $currentRoute = $request->route()->getName() ?? '';

        foreach ($this->routePermissions as $pattern => $permission) {
            if (str_ends_with($pattern, '*')) {
                $prefix = substr($pattern, 0, -1);
                if (str_starts_with($currentRoute, $prefix)) {
                    if (!$user->hasPermission($permission)) {
                        abort(403, 'You do not have permission to access this page.');
                    }
                    return $next($request);
                }
            } elseif ($currentRoute === $pattern) {
                if (!$user->hasPermission($permission)) {
                    abort(403, 'You do not have permission to access this page.');
                }
                return $next($request);
            }
        }

        return $next($request);
    }
}
