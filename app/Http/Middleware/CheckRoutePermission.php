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
        'admin.sgr.parking-revenue*' => 'view-sgr-parking-revenue',
        'admin.sgr*' => 'view-sgr',
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
        'admin.vendor-payments*' => 'view-expenses',
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
        'admin.chart-of-accounts*' => 'view-journal-entries',
        'admin.journal-entries*' => 'view-journal-entries',
        'admin.financial-reports*' => 'view-financial-reports',
        'admin.petty-cash*' => 'view-petty-cash',
        'admin.projects.account*' => 'view-projects',
        'admin.projects*' => 'view-projects',
        'admin.timesheets*' => 'view-timesheets',
        'admin.bugs*' => 'view-bugs',
        'admin.products*' => 'view-products',
        'admin.product-categories*' => 'view-product-categories',
        'admin.suppliers*' => 'view-suppliers',
        'admin.stock-movements*' => 'view-stock-movements',
        'admin.warehouses*' => 'view-warehouses',
        'admin.transfers*' => 'view-acc-transfers',
        'admin.sales-dashboard*' => 'view-sales-invoices',
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

    /**
     * Routes considered finance accounts. These get a stronger denial message.
     */
    protected array $financeRoutes = [
        'admin.bank-accounts',
        'admin.acc-transfers',
        'admin.transfers',
        'admin.expenses',
        'admin.revenues',
        'admin.bills',
        'admin.client-receipts',
        'admin.financial-reports',
        'admin.chart-of-accounts',
        'admin.journal-entries',
        'admin.petty-cash',
        'admin.sales-invoices',
        'admin.sales-returns',
        'admin.sales-proposals',
        'admin.sales-dashboard',
        'admin.purchase-invoices',
        'admin.purchase-returns',
        'admin.office-expenses',
        'admin.vendor-payments',
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
        $isFinanceRoute = $this->isFinanceRoute($currentRoute);

        foreach ($this->routePermissions as $pattern => $permission) {
            $matches = false;
            if (str_ends_with($pattern, '*')) {
                $matches = str_starts_with($currentRoute, substr($pattern, 0, -1));
            } else {
                $matches = $currentRoute === $pattern;
            }

            if ($matches) {
                if (!$user->hasPermission($permission)) {
                    $message = $isFinanceRoute
                        ? 'You are not allowed to access this finance account.'
                        : 'You are not allowed to access this page.';
                    abort(403, $message);
                }
                return $next($request);
            }
        }

        // If user is trying to access an admin route without explicit permission, deny access.
        if (str_starts_with($currentRoute, 'admin.')) {
            $message = $isFinanceRoute
                ? 'You are not allowed to access this finance account.'
                : 'You are not allowed to access this page.';
            abort(403, $message);
        }

        return $next($request);
    }

    private function isFinanceRoute(string $routeName): bool
    {
        foreach ($this->financeRoutes as $prefix) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix . '.')) {
                return true;
            }
        }
        return false;
    }
}
