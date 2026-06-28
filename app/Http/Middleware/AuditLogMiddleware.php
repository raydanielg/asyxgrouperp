<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    protected array $skipRoutes = [
        'admin.audit-logs*',
        'livewire*',
        'horizon*',
        'telescope*',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $this->shouldLog($request)) {
            $this->logAction($request);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        $route = $request->route()?getName() ?? '';

        foreach ($this->skipRoutes as $pattern) {
            if (fnmatch($pattern, $route)) {
                return false;
            }
        }

        return true;
    }

    protected function logAction(Request $request): void
    {
        $method = $request->method();
        $route = $request->route()?->getName() ?? $request->path();

        $action = match ($method) {
            'POST' => 'create',
            'PATCH', 'PUT' => 'update',
            'DELETE' => 'delete',
            'GET' => str_starts_with($route, 'admin.') ? 'view' : 'view',
            default => 'view',
        };

        $module = null;
        if ($route && str_starts_with($route, 'admin.')) {
            $parts = explode('.', $route);
            $module = $parts[1] ?? null;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'action' => $action,
            'module' => $module,
            'module_action' => $route,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $method,
            'company_id' => auth()->user()?->company_id,
        ]);
    }
}
