<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'audit' => \App\Http\Middleware\AuditLogMiddleware::class,
            'company' => \App\Http\Middleware\SetCompanyContext::class,
        ]);
        $middleware->redirectTo(
            guests: fn () => route('login'),
            users: fn () => auth()->user()?->isAdmin() ? '/admin/dashboard' : '/dashboard',
        );
        $middleware->appendToGroup('web', \App\Http\Middleware\AuditLogMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetCompanyContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson() || $request->ajax(),
        );

        // Ensure no company/user ever sees a raw error on any page
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->environment('production') ? 'An unexpected error occurred.' : $e->getMessage(),
                ], 500);
            }

            if (!app()->environment('local')) {
                return response()->view('errors.safe', [
                    'message' => 'We encountered an issue while loading this page. Our team has been notified.',
                    'code' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
                ], 500);
            }
        });
    })->create();
