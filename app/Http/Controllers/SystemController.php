<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display system settings
     */
    public function settings(Request $request)
    {
        $this->authorize('viewSettings');
        
        $activeTab = $request->get('tab', 'general');
        
        // Get all settings grouped by category
        $settings = Setting::getAllGrouped();
        
        // Define setting structure with defaults if not in database
        $settingsStructure = $this->getSettingsStructure();
        
        // Merge database settings with structure, filling in defaults
        foreach ($settingsStructure as $group => $groupSettings) {
            foreach ($groupSettings as $key => $config) {
                if (!isset($settings[$group][$key])) {
                    $settings[$group][$key] = [
                        'value' => $config['default'],
                        'label' => $config['label'],
                        'description' => $config['description'],
                        'type' => $config['type'],
                        'raw_value' => $config['default']
                    ];
                }
            }
        }

        return view('system.settings', [
            'activeTab' => $activeTab,
            'settings' => $settings,
            'settingsStructure' => $settingsStructure
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $this->authorize('updateSettings');
        
        $settingsStructure = $this->getSettingsStructure();
        $rules = [];
        $messages = [];

        // Build validation rules dynamically
        foreach ($settingsStructure as $group => $groupSettings) {
            foreach ($groupSettings as $key => $config) {
                $fieldName = "{$group}.{$key}";
                $rules[$fieldName] = $config['validation'] ?? 'nullable';
                
                if (isset($config['validation_message'])) {
                    $messages[$fieldName] = $config['validation_message'];
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updated = 0;

        // Process each setting group
        foreach ($settingsStructure as $group => $groupSettings) {
            foreach ($groupSettings as $key => $config) {
                $fieldName = "{$group}.{$key}";
                
                if ($request->has($fieldName)) {
                    $value = $request->input($fieldName);
                    
                    // Handle boolean values from checkboxes
                    if ($config['type'] === 'boolean') {
                        $value = $request->has($fieldName) ? 'true' : 'false';
                    }
                    
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'type' => $config['type'],
                            'group' => $group,
                            'label' => $config['label'],
                            'description' => $config['description']
                        ]
                    );
                    
                    $updated++;
                }
            }
        }

        Setting::clearCache();

        return back()->with('success', "Updated {$updated} settings successfully.");
    }

    /**
     * Reset settings to defaults
     */
    public function resetSettings(Request $request)
    {
        $this->authorize('resetSettings');
        
        $group = $request->get('group');
        
        if ($group && $group !== 'all') {
            Setting::where('group', $group)->delete();
            $message = "Reset {$group} settings to defaults.";
        } else {
            Setting::truncate();
            $message = "Reset all settings to defaults.";
        }

        Setting::clearCache();

        return back()->with('success', $message);
    }

    /**
     * Export settings as JSON
     */
    public function exportSettings()
    {
        $this->authorize('exportSettings');
        
        $settings = Setting::getAllGrouped();
        
        $filename = 'sacco-settings-' . date('Y-m-d-H-i-s') . '.json';
        
        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON
     */
    public function importSettings(Request $request)
    {
        $this->authorize('importSettings');
        
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('settings_file');
        $content = file_get_contents($file->getPathname());
        $settings = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['settings_file' => 'Invalid JSON file format.']);
        }

        $imported = 0;

        foreach ($settings as $group => $groupSettings) {
            foreach ($groupSettings as $key => $settingData) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $settingData['raw_value'] ?? $settingData['value'],
                        'type' => $settingData['type'] ?? 'string',
                        'group' => $group,
                        'label' => $settingData['label'] ?? null,
                        'description' => $settingData['description'] ?? null
                    ]
                );
                $imported++;
            }
        }

        Setting::clearCache();

        return back()->with('success', "Imported {$imported} settings successfully.");
    }

    /**
     * Get the settings structure with defaults and validation
     */
    private function getSettingsStructure(): array
    {
        return [
            'general' => [
                'organization_name' => [
                    'label' => 'Organization Name',
                    'description' => 'The name of your SACCO organization',
                    'type' => 'string',
                    'default' => 'Kenya SACCO Limited',
                    'validation' => 'required|string|max:255'
                ],
                'registration_number' => [
                    'label' => 'Registration Number',
                    'description' => 'Official registration number',
                    'type' => 'string',
                    'default' => 'SACCO-001-2020',
                    'validation' => 'required|string|max:100'
                ],
                'contact_email' => [
                    'label' => 'Contact Email',
                    'description' => 'Primary contact email address',
                    'type' => 'string',
                    'default' => 'info@saccocore.co.ke',
                    'validation' => 'required|email'
                ],
                'contact_phone' => [
                    'label' => 'Contact Phone',
                    'description' => 'Primary contact phone number',
                    'type' => 'string',
                    'default' => '+254700000000',
                    'validation' => 'nullable|string|max:20'
                ],
                'default_currency' => [
                    'label' => 'Default Currency',
                    'description' => 'Base currency for all transactions',
                    'type' => 'string',
                    'default' => 'KES',
                    'validation' => 'required|in:KES,USD,EUR,GBP'
                ],
                'timezone' => [
                    'label' => 'Timezone',
                    'description' => 'Default timezone for the system',
                    'type' => 'string',
                    'default' => 'Africa/Nairobi',
                    'validation' => 'required|string'
                ]
            ],
            'financial' => [
                'savings_interest_rate' => [
                    'label' => 'Savings Interest Rate (Annual %)',
                    'description' => 'Annual interest rate for savings accounts',
                    'type' => 'float',
                    'default' => 8.5,
                    'validation' => 'required|numeric|min:0|max:100'
                ],
                'loan_interest_rate' => [
                    'label' => 'Default Loan Interest Rate (Annual %)',
                    'description' => 'Default annual interest rate for loans',
                    'type' => 'float',
                    'default' => 12.5,
                    'validation' => 'required|numeric|min:0|max:100'
                ],
                'emergency_loan_rate' => [
                    'label' => 'Emergency Loan Rate (Monthly %)',
                    'description' => 'Monthly interest rate for emergency loans',
                    'type' => 'float',
                    'default' => 2.5,
                    'validation' => 'required|numeric|min:0|max:50'
                ],
                'late_payment_penalty' => [
                    'label' => 'Late Payment Penalty (%)',
                    'description' => 'Penalty percentage for late loan payments',
                    'type' => 'float',
                    'default' => 5.0,
                    'validation' => 'required|numeric|min:0|max:50'
                ],
                'maximum_loan_amount' => [
                    'label' => 'Maximum Loan Amount',
                    'description' => 'Maximum amount that can be borrowed',
                    'type' => 'integer',
                    'default' => 500000,
                    'validation' => 'required|numeric|min:1000'
                ],
                'minimum_savings_balance' => [
                    'label' => 'Minimum Savings Balance',
                    'description' => 'Minimum balance required in savings account',
                    'type' => 'integer',
                    'default' => 1000,
                    'validation' => 'required|numeric|min:0'
                ],
                'daily_withdrawal_limit' => [
                    'label' => 'Daily Withdrawal Limit',
                    'description' => 'Maximum amount that can be withdrawn per day',
                    'type' => 'integer',
                    'default' => 50000,
                    'validation' => 'required|numeric|min:1000'
                ],
                'loan_term_months' => [
                    'label' => 'Default Loan Term (Months)',
                    'description' => 'Default loan repayment period in months',
                    'type' => 'integer',
                    'default' => 36,
                    'validation' => 'required|numeric|min:1|max:120'
                ]
            ],
            'features' => [
                'enable_sms_notifications' => [
                    'label' => 'Enable SMS Notifications',
                    'description' => 'Send SMS alerts for transactions and updates',
                    'type' => 'boolean',
                    'default' => true,
                    'validation' => 'boolean'
                ],
                'allow_online_loan_applications' => [
                    'label' => 'Allow Online Loan Applications',
                    'description' => 'Members can apply for loans through the portal',
                    'type' => 'boolean',
                    'default' => true,
                    'validation' => 'boolean'
                ],
                'enable_mobile_money' => [
                    'label' => 'Enable Mobile Money Integration',
                    'description' => 'M-Pesa and other mobile money services',
                    'type' => 'boolean',
                    'default' => true,
                    'validation' => 'boolean'
                ],
                'automatic_interest_calculation' => [
                    'label' => 'Automatic Interest Calculation',
                    'description' => 'Calculate interest automatically on savings',
                    'type' => 'boolean',
                    'default' => true,
                    'validation' => 'boolean'
                ],
                'require_two_factor_auth' => [
                    'label' => 'Require Two-Factor Authentication',
                    'description' => 'Require 2FA for sensitive operations',
                    'type' => 'boolean',
                    'default' => false,
                    'validation' => 'boolean'
                ],
                'enable_email_notifications' => [
                    'label' => 'Enable Email Notifications',
                    'description' => 'Send email notifications for important events',
                    'type' => 'boolean',
                    'default' => true,
                    'validation' => 'boolean'
                ]
            ]
        ];
    }
}
