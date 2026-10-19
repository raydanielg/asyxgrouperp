<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $companyId = session('switched_company_id', $user->company_id);
            $company = $companyId ? \App\Models\Company::find($companyId) : null;

            // Suspended/deactivated/expired company → hard lockout for everyone
            // (super admin stays immune so the platform can never lock itself out).
            if ($company && $company->isLockedOut()) {
                if ($user->isSuperAdmin()) {
                    // Super admin pointing at a locked company just reverts to own company.
                    session(['switched_company_id' => null, 'current_company_id' => $user->company_id]);
                    view()->share('currentCompany', $user->company);
                    return $next($request);
                }

                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->with('error', $company->suspensionMessage());
            }

            session(['current_company_id' => $companyId, 'switched_company_id' => $companyId]);
            view()->share('currentCompany', $user->company);
        }
        return $next($request);
    }
}
