<x-layouts.app :title="__('Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Reports & Analytics') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Generate comprehensive reports and export data') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="calendar">
                            {{ __('Schedule Report') }}
                        </flux:button>
                        <flux:button variant="primary" icon="document-text">
                            {{ __('New Report') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Quick Reports -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:icon.users class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        <flux:button variant="ghost" size="sm">
                            {{ __('Generate') }}
                        </flux:button>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Member Report') }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ __('Member registration, demographics, and activity') }}</p>
                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                        {{ __('Last generated: Yesterday') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:icon.banknotes class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                        <flux:button variant="ghost" size="sm">
                            {{ __('Generate') }}
                        </flux:button>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Financial Report') }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ __('Assets, liabilities, and income statement') }}</p>
                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                        {{ __('Last generated: 2 days ago') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:icon.credit-card class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                        <flux:button variant="ghost" size="sm">
                            {{ __('Generate') }}
                        </flux:button>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Loan Portfolio') }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ __('Loan performance, arrears, and collections') }}</p>
                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                        {{ __('Last generated: Today') }}
                    </div>
                </div>
            </div>

            <!-- Report Categories -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Financial Reports -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Financial Reports') }}
                        </h3>
                    </div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach([
                            ['name' => 'Trial Balance', 'description' => 'Complete trial balance report', 'format' => 'PDF, Excel'],
                            ['name' => 'Income Statement', 'description' => 'Profit and loss statement', 'format' => 'PDF, Excel'],
                            ['name' => 'Balance Sheet', 'description' => 'Assets, liabilities and equity', 'format' => 'PDF, Excel'],
                            ['name' => 'Cash Flow', 'description' => 'Cash flow statement and analysis', 'format' => 'PDF, Excel']
                        ] as $report)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $report['description'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $report['format'] }}</p>
                                </div>
                                <flux:button variant="outline" size="sm">
                                    {{ __('Generate') }}
                                </flux:button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Operational Reports -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Operational Reports') }}
                        </h3>
                    </div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach([
                            ['name' => 'Member Activity', 'description' => 'Member transaction summary', 'format' => 'PDF, Excel'],
                            ['name' => 'Loan Arrears', 'description' => 'Overdue loans and collections', 'format' => 'PDF, Excel'],
                            ['name' => 'Daily Transactions', 'description' => 'Daily transaction summary', 'format' => 'PDF, Excel'],
                            ['name' => 'Branch Performance', 'description' => 'Branch-wise performance metrics', 'format' => 'PDF, Excel']
                        ] as $report)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $report['description'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $report['format'] }}</p>
                                </div>
                                <flux:button variant="outline" size="sm">
                                    {{ __('Generate') }}
                                </flux:button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Recent Reports') }}
                        </h3>
                        <flux:button variant="ghost" size="sm">
                            {{ __('View All') }}
                        </flux:button>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['name' => 'Monthly Financial Summary - December 2024', 'type' => 'Financial', 'generated' => '2024-12-15 10:30', 'size' => '2.4 MB', 'format' => 'PDF'],
                        ['name' => 'Loan Portfolio Analysis - Q4 2024', 'type' => 'Operational', 'generated' => '2024-12-14 14:15', 'size' => '1.8 MB', 'format' => 'Excel'],
                        ['name' => 'Member Growth Report - November 2024', 'type' => 'Membership', 'generated' => '2024-12-13 09:45', 'size' => '892 KB', 'format' => 'PDF'],
                        ['name' => 'Daily Transaction Summary - December 12, 2024', 'type' => 'Operational', 'generated' => '2024-12-12 18:00', 'size' => '456 KB', 'format' => 'Excel']
                    ] as $report)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $report['type'] }} • {{ $report['format'] }} • {{ $report['size'] }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        {{ \Carbon\Carbon::parse($report['generated'])->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <flux:button variant="ghost" size="sm" icon="eye">
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button variant="outline" size="sm" icon="arrow-down-tray">
                                    {{ __('Download') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 