<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a system setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format amount with the system default currency
     *
     * @param float $amount
     * @return string
     */
    function format_currency(float $amount): string
    {
        $currency = setting('default_currency', 'KES');
        
        switch ($currency) {
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'EUR':
                return '€' . number_format($amount, 2);
            case 'GBP':
                return '£' . number_format($amount, 2);
            case 'KES':
            default:
                return 'KSh ' . number_format($amount, 2);
        }
    }
}

if (!function_exists('organization_name')) {
    /**
     * Get the organization name
     *
     * @return string
     */
    function organization_name(): string
    {
        return setting('organization_name', 'Kenya SACCO Limited');
    }
}

if (!function_exists('is_feature_enabled')) {
    /**
     * Check if a feature is enabled
     *
     * @param string $feature
     * @return bool
     */
    function is_feature_enabled(string $feature): bool
    {
        return (bool) setting($feature, false);
    }
} 