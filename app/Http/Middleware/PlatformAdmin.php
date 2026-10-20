<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlatformAdmin
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'This area is restricted to the platform administrator.');
        }

        return $next($request);
    }
}
