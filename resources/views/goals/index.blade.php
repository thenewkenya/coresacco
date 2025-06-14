<x-layouts.app :title="__('My Goals')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Financial Goals') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Set and track your savings goals and financial targets') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" icon="plus">
                            {{ __('New Goal') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <div class="text-center">
                    <flux:icon.flag class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('Financial Goal Setting') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('Set personalized savings goals, track progress, and receive reminders to help you achieve your financial objectives.') }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Goal Types') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Emergency Fund') }}</li>
                                <li>• {{ __('Home Purchase') }}</li>
                                <li>• {{ __('Education Fund') }}</li>
                                <li>• {{ __('Retirement') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Features') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Progress Tracking') }}</li>
                                <li>• {{ __('Automatic Transfers') }}</li>
                                <li>• {{ __('Goal Reminders') }}</li>
                                <li>• {{ __('Achievement Milestones') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Benefits') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Better Financial Planning') }}</li>
                                <li>• {{ __('Increased Savings Rate') }}</li>
                                <li>• {{ __('Goal Achievement') }}</li>
                                <li>• {{ __('Financial Discipline') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 