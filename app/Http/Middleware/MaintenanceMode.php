<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.maintenance_mode', false) && !auth()->check()) {
            return response()->view('errors.maintenance', [], 503);
        }

        if (config('app.maintenance_mode', false) && auth()->check()) {
            $user = auth()->user();
            $allowed = $user->role === 'admin' || $user->hasRole('admin');
            if (!$allowed) {
                auth()->logout();
                return response()->view('errors.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
