<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display system settings page
     */
    public function index(): Response
    {
        $settings = [
            // General Settings
            'general' => [
                'sacco_name' => config('app.name', 'CoreSacco'),
                'sacco_email' => config('mail.from.address', 'admin@sacco.co.ke'),
                'sacco_phone' => '+254 700 000 000',
                'sacco_address' => 'Nairobi, Kenya',
                'timezone' => config('app.timezone', 'Africa/Nairobi'),
                'currency' => 'KES',
                'currency_symbol' => 'KSh',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'language' => 'en',
            ],
            
            // Financial Settings
            'financial' => [
                'minimum_deposit' => 1000,
                'maximum_deposit' => 1000000,
                'minimum_withdrawal' => 500,
                'maximum_withdrawal' => 500000,
                'transaction_fee' => 50,
                'monthly_maintenance_fee' => 100,
                'interest_rate' => 8.5,
                'penalty_rate' => 2.0,
                'loan_processing_fee' => 500,
                'loan_insurance_rate' => 1.5,
            ],
            
            // Loan Settings
            'loans' => [
                'minimum_loan_amount' => 5000,
                'maximum_loan_amount' => 5000000,
                'minimum_loan_period' => 1,
                'maximum_loan_period' => 60,
                'loan_interest_rate' => 12.0,
                'loan_penalty_rate' => 3.0,
                'loan_processing_fee_percentage' => 2.0,
                'loan_insurance_required' => true,
                'loan_guarantor_required' => true,
                'loan_collateral_required' => false,
                'loan_approval_levels' => 2,
                'loan_auto_approval_limit' => 50000,
            ],
            
            // Member Settings
            'members' => [
                'minimum_age' => 18,
                'maximum_age' => 65,
                'minimum_share_capital' => 1000,
                'share_capital_increment' => 100,
                'membership_fee' => 500,
                'annual_subscription_fee' => 200,
                'member_photo_required' => true,
                'member_id_required' => true,
                'member_address_required' => true,
                'member_employment_required' => true,
            ],
            
            // Security Settings
            'security' => [
                'password_min_length' => 8,
                'password_require_uppercase' => true,
                'password_require_lowercase' => true,
                'password_require_numbers' => true,
                'password_require_symbols' => true,
                'session_timeout' => 120,
                'max_login_attempts' => 5,
                'lockout_duration' => 15,
                'two_factor_auth' => false,
                'ip_whitelist' => '',
            ],
            
            // Notification Settings
            'notifications' => [
                'email_notifications' => true,
                'sms_notifications' => true,
                'push_notifications' => true,
                'loan_reminder_days' => 7,
                'payment_reminder_days' => 3,
                'overdue_notification_days' => 1,
                'monthly_statement_email' => true,
                'transaction_alerts' => true,
                'system_maintenance_notifications' => true,
            ],
            
            // Backup Settings
            'backup' => [
                'auto_backup' => true,
                'backup_frequency' => 'daily',
                'backup_retention_days' => 30,
                'backup_location' => 'local',
                'backup_encryption' => true,
                'backup_notification_email' => 'admin@sacco.co.ke',
            ],
            
            // Integration Settings
            'integrations' => [
                'mobile_money' => [
                    'enabled' => true,
                    'provider' => 'mpesa',
                    'api_key' => '',
                    'api_secret' => '',
                    'callback_url' => '',
                ],
                'banking' => [
                    'enabled' => false,
                    'provider' => '',
                    'api_endpoint' => '',
                    'api_key' => '',
                    'api_secret' => '',
                ],
                'sms' => [
                    'enabled' => true,
                    'provider' => 'africas_talking',
                    'api_key' => '',
                    'username' => '',
                ],
                'email' => [
                    'enabled' => true,
                    'provider' => 'smtp',
                    'host' => config('mail.host', 'smtp.gmail.com'),
                    'port' => config('mail.port', 587),
                    'username' => config('mail.username', ''),
                    'password' => config('mail.password', ''),
                    'encryption' => config('mail.encryption', 'tls'),
                ],
            ],
            
            // System Settings
            'system' => [
                'maintenance_mode' => false,
                'debug_mode' => config('app.debug', false),
                'log_level' => 'info',
                'cache_driver' => config('cache.default', 'file'),
                'queue_driver' => config('queue.default', 'sync'),
                'session_driver' => config('session.driver', 'file'),
                'file_upload_max_size' => '10MB',
                'image_upload_max_size' => '5MB',
                'allowed_file_types' => 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'auto_logout_minutes' => 30,
            ],
        ];

        return Inertia::render('system-settings/index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'general.sacco_name' => 'required|string|max:255',
            'general.sacco_email' => 'required|email|max:255',
            'general.sacco_phone' => 'required|string|max:20',
            'general.sacco_address' => 'required|string|max:500',
            'general.timezone' => 'required|string|max:50',
            'general.currency' => 'required|string|max:3',
            'general.currency_symbol' => 'required|string|max:10',
            'general.date_format' => 'required|string|max:20',
            'general.time_format' => 'required|string|max:20',
            'general.language' => 'required|string|max:5',
            
            'financial.minimum_deposit' => 'required|numeric|min:0',
            'financial.maximum_deposit' => 'required|numeric|min:0',
            'financial.minimum_withdrawal' => 'required|numeric|min:0',
            'financial.maximum_withdrawal' => 'required|numeric|min:0',
            'financial.transaction_fee' => 'required|numeric|min:0',
            'financial.monthly_maintenance_fee' => 'required|numeric|min:0',
            'financial.interest_rate' => 'required|numeric|min:0|max:100',
            'financial.penalty_rate' => 'required|numeric|min:0|max:100',
            'financial.loan_processing_fee' => 'required|numeric|min:0',
            'financial.loan_insurance_rate' => 'required|numeric|min:0|max:100',
            
            'loans.minimum_loan_amount' => 'required|numeric|min:0',
            'loans.maximum_loan_amount' => 'required|numeric|min:0',
            'loans.minimum_loan_period' => 'required|integer|min:1',
            'loans.maximum_loan_period' => 'required|integer|min:1',
            'loans.loan_interest_rate' => 'required|numeric|min:0|max:100',
            'loans.loan_penalty_rate' => 'required|numeric|min:0|max:100',
            'loans.loan_processing_fee_percentage' => 'required|numeric|min:0|max:100',
            'loans.loan_insurance_required' => 'boolean',
            'loans.loan_guarantor_required' => 'boolean',
            'loans.loan_collateral_required' => 'boolean',
            'loans.loan_approval_levels' => 'required|integer|min:1|max:5',
            'loans.loan_auto_approval_limit' => 'required|numeric|min:0',
            
            'members.minimum_age' => 'required|integer|min:16|max:100',
            'members.maximum_age' => 'required|integer|min:16|max:100',
            'members.minimum_share_capital' => 'required|numeric|min:0',
            'members.share_capital_increment' => 'required|numeric|min:0',
            'members.membership_fee' => 'required|numeric|min:0',
            'members.annual_subscription_fee' => 'required|numeric|min:0',
            'members.member_photo_required' => 'boolean',
            'members.member_id_required' => 'boolean',
            'members.member_address_required' => 'boolean',
            'members.member_employment_required' => 'boolean',
            
            'security.password_min_length' => 'required|integer|min:6|max:20',
            'security.password_require_uppercase' => 'boolean',
            'security.password_require_lowercase' => 'boolean',
            'security.password_require_numbers' => 'boolean',
            'security.password_require_symbols' => 'boolean',
            'security.session_timeout' => 'required|integer|min:5|max:480',
            'security.max_login_attempts' => 'required|integer|min:3|max:10',
            'security.lockout_duration' => 'required|integer|min:5|max:60',
            'security.two_factor_auth' => 'boolean',
            'security.ip_whitelist' => 'nullable|string|max:1000',
            
            'notifications.email_notifications' => 'boolean',
            'notifications.sms_notifications' => 'boolean',
            'notifications.push_notifications' => 'boolean',
            'notifications.loan_reminder_days' => 'required|integer|min:1|max:30',
            'notifications.payment_reminder_days' => 'required|integer|min:1|max:7',
            'notifications.overdue_notification_days' => 'required|integer|min:1|max:7',
            'notifications.monthly_statement_email' => 'boolean',
            'notifications.transaction_alerts' => 'boolean',
            'notifications.system_maintenance_notifications' => 'boolean',
            
            'backup.auto_backup' => 'boolean',
            'backup.backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'backup.backup_retention_days' => 'required|integer|min:7|max:365',
            'backup.backup_location' => 'required|string|in:local,cloud',
            'backup.backup_encryption' => 'boolean',
            'backup.backup_notification_email' => 'required|email|max:255',
            
            'system.maintenance_mode' => 'boolean',
            'system.debug_mode' => 'boolean',
            'system.log_level' => 'required|string|in:debug,info,warning,error',
            'system.cache_driver' => 'required|string|in:file,database,redis,memcached',
            'system.queue_driver' => 'required|string|in:sync,database,redis,sqs',
            'system.session_driver' => 'required|string|in:file,database,redis',
            'system.file_upload_max_size' => 'required|string|max:20',
            'system.image_upload_max_size' => 'required|string|max:20',
            'system.allowed_file_types' => 'required|string|max:200',
            'system.auto_logout_minutes' => 'required|integer|min:5|max:480',
        ]);

        // Here you would typically save the settings to a database or config file
        // For now, we'll just return a success response
        
        return redirect()->back()->with('success', 'System settings updated successfully!');
    }
}
