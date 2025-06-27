<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share common settings with all views
        View::composer('*', function ($view) {
            try {
                $view->with([
                    'organizationName' => setting('organization_name', 'SACCO'),
                    'defaultCurrency' => setting('default_currency', 'KES'),
                ]);
            } catch (\Exception $e) {
                // If database is not available (e.g., during migration), use defaults
                $view->with([
                    'organizationName' => 'SACCO',
                    'defaultCurrency' => 'KES',
                ]);
            }
        });

        // Set timezone from settings
        try {
            $timezone = setting('timezone', config('app.timezone'));
            if ($timezone) {
                Config::set('app.timezone', $timezone);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Ignore if database is not available
        }
    }
}
