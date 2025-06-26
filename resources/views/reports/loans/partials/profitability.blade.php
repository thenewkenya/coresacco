<!-- Profitability Analysis -->
<div class="space-y-6">
    <!-- Key Profitability Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Interest Income') }}</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ __('KES') }} {{ number_format($profitabilityMetrics['total_interest_income'], 0) }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Expected ROI') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($profitabilityMetrics['expected_roi'], 1) }}%</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Actual ROI') }}</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($profitabilityMetrics['actual_roi'], 1) }}%</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Profit Margin') }}</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($profitabilityMetrics['profit_margin'], 1) }}%</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Profitability by Loan Type -->
    @if($profitabilityByType->isNotEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Profitability by Loan Type') }}</h3>
            <div class="space-y-4">
                @foreach($profitabilityByType as $typeData)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $typeData['type_name'] ?: __('Unassigned') }}</span>
                            <span class="text-sm px-2 py-1 rounded
                                {{ $typeData['profit_margin'] >= 15 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($typeData['profit_margin'] >= 10 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ number_format($typeData['profit_margin'], 1) }}% {{ __('Margin') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500">{{ __('Total Loans') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['total_count']) }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Interest Income') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format($typeData['interest_income'], 0) }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Expected ROI') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['expected_roi'], 1) }}%</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Actual ROI') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['actual_roi'], 1) }}%</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Completion Rate') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['completion_rate'], 1) }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Top and Poor Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performers -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Top Performing Loans') }}</h3>
            <div class="space-y-3">
                @forelse($topPerformers->take(10) as $loan)
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan['member_name'] }}</div>
                            <div class="text-sm text-zinc-500">{{ $loan['loan_type'] }} - {{ __('KES') }} {{ number_format($loan['amount'], 0) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-green-600 dark:text-green-400">{{ number_format($loan['roi'], 1) }}% {{ __('ROI') }}</div>
                            <div class="text-sm text-zinc-500">{{ __('KES') }} {{ number_format($loan['profit'], 0) }} {{ __('profit') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-4">{{ __('No performance data available') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Poor Performers -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Poor Performing Loans') }}</h3>
            <div class="space-y-3">
                @forelse($poorPerformers->take(10) as $loan)
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan['member_name'] }}</div>
                            <div class="text-sm text-zinc-500">{{ $loan['loan_type'] }} - {{ __('KES') }} {{ number_format($loan['amount'], 0) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-red-600 dark:text-red-400">{{ number_format($loan['roi'], 1) }}% {{ __('ROI') }}</div>
                            <div class="text-sm text-zinc-500">{{ $loan['days_overdue'] }} {{ __('days overdue') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-4">{{ __('No performance data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detailed Profitability Analysis -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Detailed Profitability Analysis') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Principal') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Interest Earned') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('ROI') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Performance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($profitabilityAnalysis->sortByDesc('roi')->take(50) as $analysis)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $analysis['member_name'] ?? __('N/A') }}</div>
                                <div class="text-xs text-zinc-500">{{ $analysis['member_number'] ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $analysis['loan_type'] ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('KES') }} {{ number_format($analysis['principal'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('KES') }} {{ number_format($analysis['interest_earned'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $analysis['roi'] >= 15 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($analysis['roi'] >= 10 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ number_format($analysis['roi'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $analysis['status'] === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($analysis['status'] === 'active' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200') }}">
                                    {{ ucfirst($analysis['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($analysis['completion_percentage'], 1) }}%</div>
                                    <div class="w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $analysis['completion_percentage'] >= 80 ? 'bg-green-500' : ($analysis['completion_percentage'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $analysis['completion_percentage'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                {{ __('No profitability data available for the selected criteria') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($profitabilityAnalysis->count() > 50)
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 text-center text-sm text-zinc-500">
                {{ __('Showing top 50 most profitable loans of') }} {{ number_format($profitabilityAnalysis->count()) }} {{ __('total. Export for complete data.') }}
            </div>
        @endif
    </div>
</div> 