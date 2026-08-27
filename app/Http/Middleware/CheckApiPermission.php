<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiPermission
{
    protected array $routePermissions = [
        'api.companies*' => 'view-dashboard',
        'api.employees*' => 'view-employees',
        'api.attendance*' => 'view-attendance',
        'api.leaves*' => 'view-leaves',
        'api.payroll*' => 'view-payroll',
        'api.pos*' => 'view-pos',
        'api.fleet*' => 'view-dashboard',
        'api.expenses*' => 'view-expenses',
        'api.revenues*' => 'view-revenues',
        'api.bank-accounts*' => 'view-bank-accounts',
        'api.financial-summary' => 'view-financial-reports',
        'api.crm*' => 'view-crm-leads',
        'api.tenders*' => 'view-dashboard',
        'api.quotations*' => 'view-dashboard',
        'api.lpos*' => 'view-dashboard',
        'api.grns*' => 'view-dashboard',
        'api.delivery-notes*' => 'view-dashboard',
        'api.vendor-invoices*' => 'view-dashboard',
        'api.office-expenses*' => 'view-expenses',
        'api.client-receipts*' => 'view-revenues',
        'api.proposals*' => 'view-sales-invoices',
        'api.budgets*' => 'view-dashboard',
        'api.projects*' => 'view-projects',
        'api.products*' => 'view-products',
        'api.stock-movements*' => 'view-stock-movements',
        'api.sales-invoices*' => 'view-sales-invoices',
        'api.purchase-invoices*' => 'view-purchase-invoices',
        'api.customers*' => 'view-crm-contacts',
        'api.leads*' => 'view-crm-leads',
        'api.deals*' => 'view-crm-deals',
        'api.tickets*' => 'view-helpdesk-tickets',
        'api.reports*' => 'view-reports',
        'api.dashboard*' => 'view-dashboard',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $routeName = $request->route()?->getName();

        if ($routeName) {
            foreach ($this->routePermissions as $pattern => $permission) {
                if (fnmatch($pattern, $routeName)) {
                    if (!$user->hasPermission($permission)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'You do not have permission to access this resource.',
                        ], 403);
                    }
                    break;
                }
            }
        }

        return $next($request);
    }
}
