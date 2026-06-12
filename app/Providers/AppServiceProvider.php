<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind MidtransService ke container
        $this->app->singleton(\App\Services\MidtransService::class);
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
        // Gunakan Bootstrap 5 untuk pagination
        Paginator::useBootstrapFive();

        // Daftarkan Eloquent observers
        \App\Models\Notification::observe(\App\Observers\NotificationObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }
}
