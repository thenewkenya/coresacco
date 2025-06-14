<x-layouts.app :title="__('Analytics')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Analytics Dashboard') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive insights and analytics for SACCO operations') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-path">
                            {{ __('Refresh Data') }}
                        </flux:button>
                        <flux:button variant="primary" icon="chart-bar">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+12.5%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Active Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">1,456</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+8.2%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Assets') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 45.2M</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+15.3%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Active Loans') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">234</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+5.7%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Portfolio Performance') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">92.3%</p>
                    </div>
                </div>
            </div>

            <!-- Analytics Content -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <div class="text-center">
                    <flux:icon.chart-bar class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('Advanced Analytics') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('Comprehensive business intelligence and performance analytics for informed decision-making.') }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Financial Analytics') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Asset Growth Trends') }}</li>
                                <li>• {{ __('Portfolio Performance') }}</li>
                                <li>• {{ __('Risk Assessment') }}</li>
                                <li>• {{ __('Profitability Analysis') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Member Analytics') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Member Growth') }}</li>
                                <li>• {{ __('Engagement Metrics') }}</li>
                                <li>• {{ __('Savings Patterns') }}</li>
                                <li>• {{ __('Loan Utilization') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Operational Analytics') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Transaction Volume') }}</li>
                                <li>• {{ __('Processing Efficiency') }}</li>
                                <li>• {{ __('Service Quality') }}</li>
                                <li>• {{ __('Branch Performance') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 