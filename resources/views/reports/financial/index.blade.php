<x-layouts.app :title="__('Financial Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Financial Reports') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive financial statements and analysis') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray" :href="route('reports.financial', array_merge(request()->all(), ['format' => 'pdf']))" wire:navigate>
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('reports.financial', array_merge(request()->all(), ['format' => 'excel']))" wire:navigate>
                            {{ __('Export Excel') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('reports.index')" wire:navigate>
                            {{ __('Back to Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Report Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Report Type') }}</flux:label>
                            <flux:select name="type">
                                <option value="income_statement" {{ request('type') === 'income_statement' ? 'selected' : '' }}>{{ __('Income Statement') }}</option>
                                <option value="balance_sheet" {{ request('type') === 'balance_sheet' ? 'selected' : '' }}>{{ __('Balance Sheet') }}</option>
                                <option value="cash_flow" {{ request('type') === 'cash_flow' ? 'selected' : '' }}>{{ __('Cash Flow') }}</option>
                                <option value="trial_balance" {{ request('type') === 'trial_balance' ? 'selected' : '' }}>{{ __('Trial Balance') }}</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            @if($report_type === 'income_statement')
                <!-- Income Statement -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Income Statement') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Revenue -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Revenue') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Interest Income') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($income['loan_interest'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Fee Income') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($income['fees'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-zinc-50 dark:bg-zinc-700 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Revenue') }}</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">KSh {{ number_format($total_income, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Expenses') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Operational Expenses') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($total_expenses, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-zinc-50 dark:bg-zinc-700 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Expenses') }}</span>
                                    <span class="font-bold text-red-600 dark:text-red-400">KSh {{ number_format($total_expenses, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Net Income -->
                        <div class="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20 p-6 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Net Income') }}</span>
                                <span class="text-2xl font-bold {{ $net_income >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    KSh {{ number_format($net_income, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'balance_sheet')
                <!-- Balance Sheet (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Balance Sheet') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('As of') }} {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.document-chart-bar class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Balance Sheet Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show assets, liabilities, and equity') }}</p>
                    </div>
                </div>

            @elseif($report_type === 'cash_flow')
                <!-- Cash Flow (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Cash Flow Statement') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.banknotes class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Cash Flow Statement Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show operating, investing, and financing activities') }}</p>
                    </div>
                </div>

            @elseif($report_type === 'trial_balance')
                <!-- Trial Balance (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Trial Balance') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('As of') }} {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.calculator class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Trial Balance Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show all account balances') }}</p>
                    </div>
                </div>
            @endif

            <!-- Report Summary -->
            <div class="mt-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Report Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Generated On:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ $generated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Report Type:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ ucfirst(str_replace('_', ' ', $report_type)) }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Period:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Generated By:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 