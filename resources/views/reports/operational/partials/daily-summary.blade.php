<!-- Daily Summary Report -->
<div class="space-y-6">
    <!-- Summary Metrics -->
    @if(isset($summaryMetrics))
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Period Summary') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $summaryMetrics['total_days'] }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Days Analyzed') }}</p>
            </div>
            <div class="text-center p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($summaryMetrics['avg_daily_transactions'], 1) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg Daily Transactions') }}</p>
            </div>
            <div class="text-center p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">KES {{ number_format($summaryMetrics['avg_daily_amount'], 0) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg Daily Amount') }}</p>
            </div>
            <div class="text-center p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $summaryMetrics['busiest_day_of_week']['day'] ?? 'N/A' }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Busiest Day') }}</p>
            </div>
        </div>

        @if(isset($summaryMetrics['highest_transaction_day']))
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <h4 class="font-medium text-blue-900 dark:text-blue-100">{{ __('Highest Transaction Day') }}</h4>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    {{ \Carbon\Carbon::parse($summaryMetrics['highest_transaction_day']['date'])->format('M j, Y') }} - 
                    <span class="font-semibold">{{ number_format($summaryMetrics['highest_transaction_day']['total_transactions']) }} transactions</span>
                </p>
            </div>
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <h4 class="font-medium text-green-900 dark:text-green-100">{{ __('Highest Amount Day') }}</h4>
                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                    {{ \Carbon\Carbon::parse($summaryMetrics['highest_amount_day']['date'])->format('M j, Y') }} - 
                    <span class="font-semibold">KES {{ number_format($summaryMetrics['highest_amount_day']['total_amount'], 0) }}</span>
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Daily Breakdown -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Daily Transaction Breakdown') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Day') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Transactions') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Deposits') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Withdrawals') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Net Flow') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Peak Hour') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($daily as $day)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $day['day_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($day['total_transactions']) }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <span class="text-green-600">{{ $day['completed_transactions'] }}</span> / 
                                    <span class="text-orange-600">{{ $day['pending_transactions'] }}</span> / 
                                    <span class="text-red-600">{{ $day['failed_transactions'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($day['total_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-green-600 dark:text-green-400">KES {{ number_format($day['deposits']['amount'], 0) }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['deposits']['count'] }} transactions</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-red-600 dark:text-red-400">KES {{ number_format($day['withdrawals']['amount'], 0) }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['withdrawals']['count'] }} transactions</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $day['net_cash_flow'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $day['net_cash_flow'] >= 0 ? '+' : '' }}KES {{ number_format($day['net_cash_flow'], 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $day['peak_hour']['formatted'] ?? 'N/A' }}
                                @if($day['peak_hour'])
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['peak_hour']['count'] }} txns</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No transaction data available for the selected period.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction Type Breakdown by Day -->
    @if(!empty($daily))
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Transaction Types Over Time') }}</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Loan Transactions -->
            <div>
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Loan Transactions') }}</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($daily as $day)
                        @if($day['loan_disbursements']['count'] > 0 || $day['loan_repayments']['count'] > 0)
                        <div class="flex items-center justify-between p-2 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
                            <div class="text-right">
                                <div class="text-sm text-blue-600 dark:text-blue-400">
                                    Disbursed: KES {{ number_format($day['loan_disbursements']['amount'], 0) }} ({{ $day['loan_disbursements']['count'] }})
                                </div>
                                <div class="text-sm text-green-600 dark:text-green-400">
                                    Repaid: KES {{ number_format($day['loan_repayments']['amount'], 0) }} ({{ $day['loan_repayments']['count'] }})
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Member Activity -->
            <div>
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Member Activity') }}</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($daily as $day)
                        @if($day['unique_members'] > 0)
                        <div class="flex items-center justify-between p-2 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $day['unique_members'] }} {{ __('active members') }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ number_format($day['total_transactions'] / max(1, $day['unique_members']), 1) }} avg txns/member
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div> 