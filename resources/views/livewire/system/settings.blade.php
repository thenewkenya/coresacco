<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $activeTab = 'general';
    public $settings = [];
    public $generalSettings = [];
    public $financialSettings = [];
    public $featureSettings = [];

    public function mount()
    {
        $this->loadSettingsData();
    }

    public function loadSettingsData()
    {
        // Mock data for demonstration - in real app, this would come from the controller
        $this->generalSettings = [
            'organization_name' => 'eSacco Cooperative Society',
            'organization_code' => 'ESC001',
            'default_currency' => 'KES',
            'timezone' => 'Africa/Nairobi',
            'address' => '123 Main Street, Nairobi, Kenya',
            'phone' => '+254 700 000 000',
            'email' => 'info@esacco.co.ke'
        ];

        $this->financialSettings = [
            'savings_interest_rate' => 8.5,
            'loan_interest_rate' => 12.5,
            'emergency_loan_rate' => 2.5,
            'late_payment_penalty' => 5.0,
            'maximum_loan_amount' => 500000,
            'minimum_savings_balance' => 1000,
            'daily_withdrawal_limit' => 50000,
            'loan_term_months' => 36
        ];

        $this->featureSettings = [
            'enable_sms_notifications' => true,
            'allow_online_loan_applications' => true,
            'enable_mobile_money' => true,
            'automatic_interest_calculation' => true,
            'require_two_factor_auth' => false,
            'enable_email_notifications' => true
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveSettings()
    {
        // In a real app, this would save the settings to the database
        session()->flash('success', 'Settings saved successfully!');
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">System Settings</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Configure your SACCO system preferences and settings</flux:subheading>
        </div>
        <div class="flex gap-3">
            <flux:button variant="outline" icon="arrow-path">
                Reset Settings
            </flux:button>
            <flux:button variant="primary" icon="check" wire:click="saveSettings">
                Save Changes
            </flux:button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-2 border border-zinc-200 dark:border-zinc-700">
        <nav class="flex space-x-1 overflow-x-auto">
            <button wire:click="setActiveTab('general')" 
                    class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'general' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                <flux:icon.cog class="w-4 h-4" />
                <span>General</span>
            </button>
            <button wire:click="setActiveTab('financial')" 
                    class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'financial' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                <flux:icon.currency-dollar class="w-4 h-4" />
                <span>Financial</span>
            </button>
            <button wire:click="setActiveTab('features')" 
                    class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'features' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                <flux:icon.puzzle-piece class="w-4 h-4" />
                <span>Features</span>
            </button>
        </nav>
    </div>

    @if($activeTab === 'general')
        <!-- General Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.cog class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">General Settings</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Basic organization information and system preferences</flux:subheading>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Organization Name</flux:label>
                    <flux:input type="text" wire:model="generalSettings.organization_name" />
                    <flux:description>Your SACCO's official name</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Organization Code</flux:label>
                    <flux:input type="text" wire:model="generalSettings.organization_code" />
                    <flux:description>Unique identifier for your SACCO</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Default Currency</flux:label>
                    <flux:select wire:model="generalSettings.default_currency">
                        <option value="KES">KES - Kenyan Shilling</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                    </flux:select>
                    <flux:description>Primary currency for transactions</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Timezone</flux:label>
                    <flux:select wire:model="generalSettings.timezone">
                        <option value="Africa/Nairobi">Africa/Nairobi</option>
                        <option value="Africa/Lagos">Africa/Lagos</option>
                        <option value="Africa/Cairo">Africa/Cairo</option>
                        <option value="UTC">UTC</option>
                    </flux:select>
                    <flux:description>System timezone for date/time display</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Address</flux:label>
                    <flux:textarea wire:model="generalSettings.address" rows="3" />
                    <flux:description>Physical address of your SACCO</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input type="tel" wire:model="generalSettings.phone" />
                    <flux:description>Contact phone number</flux:description>
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>Email Address</flux:label>
                    <flux:input type="email" wire:model="generalSettings.email" />
                    <flux:description>Primary contact email address</flux:description>
                </flux:field>
            </div>
        </div>

    @elseif($activeTab === 'financial')
        <!-- Financial Settings -->
        <div class="space-y-6">
            <!-- Important Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <flux:heading size="sm" class="dark:text-blue-100 mb-1">Important Notice</flux:heading>
                        <flux:subheading class="dark:text-blue-200">
                            All financial settings are required for the system to function properly. Fields marked with <span class="text-red-500 font-medium">*</span> must be filled in.
                        </flux:subheading>
                    </div>
                </div>
            </div>

            <!-- Interest Rates -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon.currency-dollar class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Interest Rates</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Configure interest rates for savings and loans</flux:subheading>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Savings Interest Rate (Annual %) <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            step="0.01"
                            min="0"
                            max="100"
                            wire:model="financialSettings.savings_interest_rate"
                            suffix="%"
                        />
                        <flux:description>Annual interest rate for savings accounts (0-100%)</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Default Loan Interest Rate (Annual %) <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            step="0.01"
                            min="0"
                            max="100"
                            wire:model="financialSettings.loan_interest_rate"
                            suffix="%"
                        />
                        <flux:description>Default annual interest rate for loans (0-100%)</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Emergency Loan Rate (Monthly %) <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            step="0.01"
                            min="0"
                            max="50"
                            wire:model="financialSettings.emergency_loan_rate"
                            suffix="%"
                        />
                        <flux:description>Monthly interest rate for emergency loans (0-50%)</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Late Payment Penalty (%) <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            step="0.01"
                            min="0"
                            max="50"
                            wire:model="financialSettings.late_payment_penalty"
                            suffix="%"
                        />
                        <flux:description>Penalty percentage for late loan payments (0-50%)</flux:description>
                    </flux:field>
                </div>
            </div>

            <!-- System Limits -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon.scale class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">System Limits</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Set financial limits and constraints</flux:subheading>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Maximum Loan Amount <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            min="1000"
                            wire:model="financialSettings.maximum_loan_amount"
                            prefix="KES"
                        />
                        <flux:description>Maximum amount that can be borrowed (minimum KES 1,000)</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Minimum Savings Balance <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            min="0"
                            wire:model="financialSettings.minimum_savings_balance"
                            prefix="KES"
                        />
                        <flux:description>Minimum balance required in savings account</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Daily Withdrawal Limit <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            min="1000"
                            wire:model="financialSettings.daily_withdrawal_limit"
                            prefix="KES"
                        />
                        <flux:description>Maximum amount that can be withdrawn per day (minimum KES 1,000)</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Default Loan Term (Months) <span class="text-red-500">*</span></flux:label>
                        <flux:input 
                            type="number" 
                            min="1"
                            max="120"
                            wire:model="financialSettings.loan_term_months"
                        />
                        <flux:description>Default loan repayment period in months (1-120 months)</flux:description>
                    </flux:field>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'features')
        <!-- Feature Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <flux:icon.puzzle-piece class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Feature Settings</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Enable or disable system features</flux:subheading>
                </div>
            </div>
            
            <div class="space-y-4">
                <!-- SMS Notifications -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Enable SMS Notifications</flux:label>
                        <flux:description class="mt-1">Send SMS alerts for transactions and updates</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.enable_sms_notifications" />
                    </div>
                </div>

                <!-- Online Loan Applications -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Allow Online Loan Applications</flux:label>
                        <flux:description class="mt-1">Members can apply for loans through the portal</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.allow_online_loan_applications" />
                    </div>
                </div>

                <!-- Mobile Money Integration -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Enable Mobile Money Integration</flux:label>
                        <flux:description class="mt-1">M-Pesa and other mobile money services</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.enable_mobile_money" />
                    </div>
                </div>

                <!-- Automatic Interest Calculation -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Automatic Interest Calculation</flux:label>
                        <flux:description class="mt-1">Calculate interest automatically on savings</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.automatic_interest_calculation" />
                    </div>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Require Two-Factor Authentication</flux:label>
                        <flux:description class="mt-1">Require 2FA for sensitive operations</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.require_two_factor_auth" />
                    </div>
                </div>

                <!-- Email Notifications -->
                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                    <div class="flex-1">
                        <flux:label class="!mb-0">Enable Email Notifications</flux:label>
                        <flux:description class="mt-1">Send email notifications for important events</flux:description>
                    </div>
                    <div class="ml-4">
                        <flux:checkbox wire:model="featureSettings.enable_email_notifications" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

