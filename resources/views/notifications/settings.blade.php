<x-layouts.app>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ __('Notification Settings') }}
                </h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Manage how you receive notifications about your SACCO activities.') }}
                </p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm rounded-lg">
                <form method="POST" action="{{ route('notifications.settings.update') }}">
                    @csrf

                    <div class="p-6 space-y-6">
                        <!-- Transaction Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Transaction Notifications') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Email Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Receive transaction updates via email') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="email_transactions" 
                                        :checked="$preferences['email_transactions'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Push Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Receive instant notifications in your browser') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="push_transactions" 
                                        :checked="$preferences['push_transactions'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('SMS Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Receive transaction alerts via SMS') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="sms_transactions" 
                                        :checked="$preferences['sms_transactions'] ?? false"
                                    />
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-200 dark:border-zinc-700">

                        <!-- Loan Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Loan Notifications') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Email Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Receive loan updates via email') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="email_loans" 
                                        :checked="$preferences['email_loans'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Push Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Receive loan status updates instantly') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="push_loans" 
                                        :checked="$preferences['push_loans'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('SMS Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Important loan alerts via SMS') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="sms_loans" 
                                        :checked="$preferences['sms_loans'] ?? true"
                                    />
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-200 dark:border-zinc-700">

                        <!-- Large Deposit Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Large Deposit Alerts') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Email Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Compliance alerts for large deposits via email') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="email_large_deposits" 
                                        :checked="$preferences['email_large_deposits'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Push Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Instant alerts for large deposits') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="push_large_deposits" 
                                        :checked="$preferences['push_large_deposits'] ?? true"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('SMS Notifications') }}
                                        </label>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Critical alerts for large deposits via SMS') }}
                                        </p>
                                    </div>
                                    <flux:switch 
                                        name="sms_large_deposits" 
                                        :checked="$preferences['sms_large_deposits'] ?? true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Changes will take effect immediately') }}
                            </div>
                            <div class="flex space-x-3">
                                <flux:button variant="outline" href="{{ route('notifications.index') }}" wire:navigate>
                                    {{ __('Cancel') }}
                                </flux:button>
                                <flux:button type="submit" variant="primary">
                                    {{ __('Save Settings') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-blue-50 dark:bg-zinc-800 border border-blue-200 dark:border-zinc-700 rounded-lg p-4">
                <div class="flex">
                    <flux:icon.information-circle class="h-5 w-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-zinc-100">
                            {{ __('About Notification Types') }}
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-zinc-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>{{ __('Email notifications are sent to your registered email address') }}</li>
                                <li>{{ __('Push notifications appear in your browser when you\'re logged in') }}</li>
                                <li>{{ __('SMS notifications are sent to your registered phone number') }}</li>
                                <li>{{ __('Large deposit alerts help with compliance and security monitoring') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 