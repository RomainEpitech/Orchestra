<?php

namespace App\Providers;

use App\Models\Enterprise;
use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use App\Models\EnterpriseModule;
use App\Observers\EnterpriseObserver;
use App\Observers\UserObserver;
use App\Observers\RoleObserver;
use App\Observers\ModuleObserver;
use App\Observers\EnterpriseModuleObserver;
use App\Services\SystemLoggerService;
use Illuminate\Support\ServiceProvider;

class SystemLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SystemLoggerService::class, function ($app) {
            return new SystemLoggerService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        Enterprise::observe(EnterpriseObserver::class);
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Module::observe(ModuleObserver::class);
        EnterpriseModule::observe(EnterpriseModuleObserver::class);
    }
}