<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Inventory;
use App\Observers\SystemLogObserver;

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
        // Register SystemLogObserver for tracking all changes
        Appointment::observe(SystemLogObserver::class);
        User::observe(SystemLogObserver::class);
        Inventory::observe(SystemLogObserver::class);
    }
}
