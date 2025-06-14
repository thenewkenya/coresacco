<x-layouts.app :title="__('System Settings')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('System Settings') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Configure system-wide settings and preferences') }}
                        </p>
                    </div>
                    <flux:button variant="primary" icon="check">
                        {{ __('Save Changes') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Settings Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6">
                            <nav class="space-y-2">
                                @foreach([
                                    ['name' => 'General', 'icon' => 'cog', 'active' => true],
                                    ['name' => 'Financial', 'icon' => 'currency-dollar', 'active' => false],
                                    ['name' => 'Security', 'icon' => 'lock-closed', 'active' => false],
                                    ['name' => 'Notifications', 'icon' => 'bell', 'active' => false],
                                    ['name' => 'Integrations', 'icon' => 'puzzle-piece', 'active' => false],
                                    ['name' => 'Backup', 'icon' => 'cloud-arrow-up', 'active' => false]
                                ] as $item)
                                <a href="#" class="flex items-center space-x-3 px-3 py-2 text-sm rounded-lg {{ $item['active'] ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
                                    <flux:icon.{{ $item['icon'] }} class="w-4 h-4" />
                                    <span>{{ __($item['name']) }}</span>
                                </a>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- General Settings -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('General Settings') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Organization Name') }}
                                    </label>
                                    <flux:input type="text" value="Kenya SACCO Limited" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Registration Number') }}
                                    </label>
                                    <flux:input type="text" value="SACCO-001-2020" />
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('Contact Email') }}
                                </label>
                                <flux:input type="email" value="info@saccocore.co.ke" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    {{ __('Default Currency') }}
                                </label>
                                <flux:select>
                                    <option value="KES" selected>KES - Kenyan Shilling</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                </flux:select>
                            </div>
                        </div>
                    </div>

                    <!-- Interest Rates -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Interest Rates') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Savings Interest Rate (Annual)') }}
                                    </label>
                                    <flux:input type="number" step="0.01" value="8.5" suffix="%" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Loan Interest Rate (Annual)') }}
                                    </label>
                                    <flux:input type="number" step="0.01" value="12.5" suffix="%" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Emergency Loan Rate (Monthly)') }}
                                    </label>
                                    <flux:input type="number" step="0.01" value="2.5" suffix="%" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Late Payment Penalty') }}
                                    </label>
                                    <flux:input type="number" step="0.01" value="5.0" suffix="%" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Limits -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('System Limits') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Maximum Loan Amount') }}
                                    </label>
                                    <flux:input type="number" value="500000" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Minimum Savings Balance') }}
                                    </label>
                                    <flux:input type="number" value="1000" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Daily Withdrawal Limit') }}
                                    </label>
                                    <flux:input type="number" value="50000" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Loan Term (Months)') }}
                                    </label>
                                    <flux:input type="number" value="36" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Toggles -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Feature Settings') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach([
                                ['name' => 'Enable SMS Notifications', 'description' => 'Send SMS alerts for transactions and updates', 'enabled' => true],
                                ['name' => 'Allow Online Loan Applications', 'description' => 'Members can apply for loans through the portal', 'enabled' => true],
                                ['name' => 'Enable Mobile Money Integration', 'description' => 'M-Pesa and other mobile money services', 'enabled' => true],
                                ['name' => 'Automatic Interest Calculation', 'description' => 'Calculate interest automatically on savings', 'enabled' => true],
                                ['name' => 'Two-Factor Authentication', 'description' => 'Require 2FA for sensitive operations', 'enabled' => false]
                            ] as $feature)
                            <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $feature['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $feature['description'] }}</p>
                                </div>
                                <flux:switch :checked="$feature['enabled']" />
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 