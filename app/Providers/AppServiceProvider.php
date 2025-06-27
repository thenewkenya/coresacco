<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Account;
use App\Models\Loan;
use App\Models\Role;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\AccountPolicy;
use App\Policies\LoanPolicy;
use App\Policies\RolePolicy;
use App\Policies\SystemPolicy;

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
        // Register policies
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(Loan::class, LoanPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        
        // Register system settings gates
        Gate::define('viewSettings', [SystemPolicy::class, 'viewSettings']);
        Gate::define('updateSettings', [SystemPolicy::class, 'updateSettings']);
        Gate::define('resetSettings', [SystemPolicy::class, 'resetSettings']);
        Gate::define('exportSettings', [SystemPolicy::class, 'exportSettings']);
        Gate::define('importSettings', [SystemPolicy::class, 'importSettings']);
        
        // Custom Blade directives for permissions
        Blade::if('can', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        Blade::if('canany', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('roleany', function (...$roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
    }
}
