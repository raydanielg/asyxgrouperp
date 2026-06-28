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
            session(['current_company_id' => $companyId, 'switched_company_id' => $companyId]);
            view()->share('currentCompany', $user->company);
        }
        return $next($request);
    }
}
