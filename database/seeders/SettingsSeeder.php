<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'organization_name',
                'value' => 'Kenya SACCO Limited',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Organization Name',
                'description' => 'The name of your SACCO organization'
            ],
            [
                'key' => 'registration_number',
                'value' => 'SACCO-001-2020',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Registration Number',
                'description' => 'Official registration number'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@saccocore.co.ke',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+254700000000',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number'
            ],
            [
                'key' => 'default_currency',
                'value' => 'KES',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Default Currency',
                'description' => 'Base currency for all transactions'
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Nairobi',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Default timezone for the system'
            ],

            // Financial Settings
            [
                'key' => 'savings_interest_rate',
                'value' => '8.5',
                'type' => 'float',
                'group' => 'financial',
                'label' => 'Savings Interest Rate (Annual %)',
                'description' => 'Annual interest rate for savings accounts'
            ],
            [
                'key' => 'loan_interest_rate',
                'value' => '12.5',
                'type' => 'float',
                'group' => 'financial',
                'label' => 'Default Loan Interest Rate (Annual %)',
                'description' => 'Default annual interest rate for loans'
            ],
            [
                'key' => 'emergency_loan_rate',
                'value' => '2.5',
                'type' => 'float',
                'group' => 'financial',
                'label' => 'Emergency Loan Rate (Monthly %)',
                'description' => 'Monthly interest rate for emergency loans'
            ],
            [
                'key' => 'late_payment_penalty',
                'value' => '5.0',
                'type' => 'float',
                'group' => 'financial',
                'label' => 'Late Payment Penalty (%)',
                'description' => 'Penalty percentage for late loan payments'
            ],
            [
                'key' => 'maximum_loan_amount',
                'value' => '500000',
                'type' => 'integer',
                'group' => 'financial',
                'label' => 'Maximum Loan Amount',
                'description' => 'Maximum amount that can be borrowed'
            ],
            [
                'key' => 'minimum_savings_balance',
                'value' => '1000',
                'type' => 'integer',
                'group' => 'financial',
                'label' => 'Minimum Savings Balance',
                'description' => 'Minimum balance required in savings account'
            ],
            [
                'key' => 'daily_withdrawal_limit',
                'value' => '50000',
                'type' => 'integer',
                'group' => 'financial',
                'label' => 'Daily Withdrawal Limit',
                'description' => 'Maximum amount that can be withdrawn per day'
            ],
            [
                'key' => 'loan_term_months',
                'value' => '36',
                'type' => 'integer',
                'group' => 'financial',
                'label' => 'Default Loan Term (Months)',
                'description' => 'Default loan repayment period in months'
            ],

            // Feature Settings
            [
                'key' => 'enable_sms_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Enable SMS Notifications',
                'description' => 'Send SMS alerts for transactions and updates'
            ],
            [
                'key' => 'allow_online_loan_applications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Allow Online Loan Applications',
                'description' => 'Members can apply for loans through the portal'
            ],
            [
                'key' => 'enable_mobile_money',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Enable Mobile Money Integration',
                'description' => 'M-Pesa and other mobile money services'
            ],
            [
                'key' => 'automatic_interest_calculation',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Automatic Interest Calculation',
                'description' => 'Calculate interest automatically on savings'
            ],
            [
                'key' => 'require_two_factor_auth',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Require Two-Factor Authentication',
                'description' => 'Require 2FA for sensitive operations'
            ],
            [
                'key' => 'enable_email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Enable Email Notifications',
                'description' => 'Send email notifications for important events'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Default system settings have been seeded.');
    }
}
