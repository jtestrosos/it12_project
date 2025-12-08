<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
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
        // Force HTTPS in production or when behind proxy
        if (
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->header('CF-Visitor') ||
            app()->environment('production')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Trust all proxies (Cloudflare)
        request()->setTrustedProxies(
            request()->getClientIps(),
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );

        // Register SystemLogObserver for tracking all changes
        Appointment::observe(SystemLogObserver::class);
        Patient::observe(SystemLogObserver::class);
        Admin::observe(SystemLogObserver::class);
        SuperAdmin::observe(SystemLogObserver::class);
        Inventory::observe(SystemLogObserver::class);

        // Register InventoryTransactionObserver to auto-fix invalid performable_type
        \App\Models\InventoryTransaction::observe(\App\Observers\InventoryTransactionObserver::class);

        // Register morph map to handle legacy 'system' references
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'admin' => \App\Models\Admin::class,
            'super_admin' => \App\Models\SuperAdmin::class,
            'system' => \App\Models\Admin::class, // Map legacy 'system' to Admin
        ]);
    }
}
