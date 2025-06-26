<x-layouts.app :title="__('Daily Transaction Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Daily Transaction Reports') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive transaction analytics and operational insights') }}
                        </p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <form method="GET" class="flex space-x-3">
                            <input type="hidden" name="type" value="{{ $report_type ?? 'daily_summary' }}">
                            <flux:button variant="outline" type="submit" name="format" value="pdf" icon="document-arrow-down">
                                {{ __('Export PDF') }}
                            </flux:button>
                            <flux:button variant="outline" type="submit" name="format" value="excel" icon="table-cells">
                                {{ __('Export Excel') }}
                            </flux:button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <!-- Report Type -->
                    <div>
                        <flux:select name="type" label="{{ __('Report Type') }}">
                            <option value="daily_summary" {{ ($report_type ?? 'daily_summary') === 'daily_summary' ? 'selected' : '' }}>{{ __('Daily Summary') }}</option>
                            <option value="transactions" {{ ($report_type ?? '') === 'transactions' ? 'selected' : '' }}>{{ __('Transaction Details') }}</option>
                            <option value="hourly_analysis" {{ ($report_type ?? '') === 'hourly_analysis' ? 'selected' : '' }}>{{ __('Hourly Analysis') }}</option>
                            <option value="transaction_types" {{ ($report_type ?? '') === 'transaction_types' ? 'selected' : '' }}>{{ __('Transaction Types') }}</option>
                        </flux:select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <flux:input type="date" name="start_date" label="{{ __('Start Date') }}" value="{{ $start_date ?? now()->startOfWeek()->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <flux:input type="date" name="end_date" label="{{ __('End Date') }}" value="{{ $end_date ?? now()->endOfWeek()->format('Y-m-d') }}" />
                    </div>

                    <!-- Quick Date Filters -->
                    <div class="flex items-end">
                        <div class="grid grid-cols-2 gap-2 w-full">
                            <flux:button type="submit" variant="ghost" size="sm" onclick="setDateRange('today')">{{ __('Today') }}</flux:button>
                            <flux:button type="submit" variant="ghost" size="sm" onclick="setDateRange('week')">{{ __('This Week') }}</flux:button>
                            <flux:button type="submit" variant="ghost" size="sm" onclick="setDateRange('month')">{{ __('This Month') }}</flux:button>
                            <flux:button type="submit" variant="ghost" size="sm" onclick="setDateRange('quarter')">{{ __('This Quarter') }}</flux:button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Report Content -->
            @if(isset($report_type))
                @if($report_type === 'daily_summary' && isset($daily))
                    @include('reports.operational.partials.daily-summary')
                @elseif($report_type === 'transactions' && isset($transactions))
                    @include('reports.operational.partials.transaction-details')
                @elseif($report_type === 'hourly_analysis' && isset($hourlyData))
                    @include('reports.operational.partials.hourly-analysis')
                @elseif($report_type === 'transaction_types' && isset($typeAnalysis))
                    @include('reports.operational.partials.transaction-types')
                @endif
            @else
                <!-- Default Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Today\'s Transactions') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ \App\Models\Transaction::whereDate('created_at', today())->count() }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Today\'s Amount') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format(\App\Models\Transaction::whereDate('created_at', today())->where('status', 'completed')->sum('amount'), 0) }}</p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Pending Approvals') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ \App\Models\Transaction::where('status', 'pending')->count() }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('This Week') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ \App\Models\Transaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Quick Reports') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="?type=daily_summary&start_date={{ now()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Today\'s Summary') }}</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('Complete overview of today\'s transactions') }}</p>
                        </a>
                        <a href="?type=transactions&start_date={{ now()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Transaction Details') }}</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('Detailed transaction listing and analysis') }}</p>
                        </a>
                        <a href="?type=hourly_analysis&start_date={{ now()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Hourly Analysis') }}</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('Transaction patterns by hour of day') }}</p>
                        </a>
                        <a href="?type=transaction_types&start_date={{ now()->startOfWeek()->format('Y-m-d') }}&end_date={{ now()->endOfWeek()->format('Y-m-d') }}" class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Transaction Types') }}</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('Analysis by transaction type breakdown') }}</p>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function setDateRange(period) {
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            const today = new Date();
            
            switch(period) {
                case 'today':
                    const todayStr = today.toISOString().split('T')[0];
                    startDateInput.value = todayStr;
                    endDateInput.value = todayStr;
                    break;
                case 'week':
                    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                    const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    startDateInput.value = startOfWeek.toISOString().split('T')[0];
                    endDateInput.value = endOfWeek.toISOString().split('T')[0];
                    break;
                case 'month':
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    startDateInput.value = startOfMonth.toISOString().split('T')[0];
                    endDateInput.value = endOfMonth.toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarter = Math.floor((today.getMonth() + 3) / 3);
                    const startOfQuarter = new Date(today.getFullYear(), (quarter - 1) * 3, 1);
                    const endOfQuarter = new Date(today.getFullYear(), quarter * 3, 0);
                    startDateInput.value = startOfQuarter.toISOString().split('T')[0];
                    endDateInput.value = endOfQuarter.toISOString().split('T')[0];
                    break;
            }
        }
    </script>
</x-layouts.app> 