<?php

namespace App\Providers;

use App\Models\ApprovalRequest;
use App\Observers\ApprovalRequestObserver;
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
    }
}
