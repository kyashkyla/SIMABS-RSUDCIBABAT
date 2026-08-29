<?php

namespace App\Providers;

use App\Models\AttendanceRequest;
use App\Observers\AttendanceRequestObserver;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        AttendanceRequest::observe(AttendanceRequestObserver::class);
    }
}
