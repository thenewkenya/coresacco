<!-- Transaction Types Analysis Report -->
<div class="space-y-6">
    <!-- Transaction Types Overview -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Transaction Types Overview') }}</h3>
        
        @php
            $totalTransactions = $typeAnalysis->sum('count');
            $totalAmount = $typeAnalysis->sum('total_amount');
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $typeAnalysis->count() }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Transaction Types') }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalTransactions) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Transactions') }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">KES {{ number_format($totalAmount, 0) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Amount') }}</p>
            </div>
        </div>
    </div>

    <!-- Transaction Type Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($typeAnalysis as $type)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $type['display_name'] }}</h4>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $type['type'] === 'deposit' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                        {{ $type['type'] === 'withdrawal' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                        {{ $type['type'] === 'loan_disbursement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                        {{ $type['type'] === 'loan_repayment' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                        {{ !in_array($type['type'], ['deposit', 'withdrawal', 'loan_disbursement', 'loan_repayment']) ? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' : '' }}">
                        {{ $type['percentage_of_total'] }}%
                    </span>
                </div>
                
                <div class="space-y-4">
                    <!-- Transaction Count -->
                    <div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Transaction Count') }}</span>
                            <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($type['count']) }}</span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mt-1">
                            <div class="h-2 rounded-full bg-blue-600 dark:bg-blue-400" style="width: {{ min(100, $type['percentage_of_total']) }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Total Amount -->
                    <div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Amount') }}</span>
                            <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['total_amount'], 0) }}</span>
                        </div>
                        @php
                            $amountPercentage = $totalAmount > 0 ? ($type['total_amount'] / $totalAmount) * 100 : 0;
                        @endphp
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 mt-1">
                            <div class="h-2 rounded-full bg-green-600 dark:bg-green-400" style="width: {{ min(100, $amountPercentage) }}%"></div>
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ round($amountPercentage, 1) }}% of total amount</div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Average') }}</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['avg_amount'], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Daily Avg') }}</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($type['daily_average'], 1) }}</p>
                        </div>
                    </div>
                    
                    <!-- Min/Max -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Minimum') }}</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['min_amount'], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Maximum') }}</p>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['max_amount'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Detailed Analysis Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Detailed Transaction Type Analysis') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Transaction Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Count') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('% of Total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Average Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Min Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Max Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Daily Average') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($typeAnalysis as $type)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $type['type'] === 'deposit' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $type['type'] === 'withdrawal' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        {{ $type['type'] === 'loan_disbursement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $type['type'] === 'loan_repayment' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                        {{ !in_array($type['type'], ['deposit', 'withdrawal', 'loan_disbursement', 'loan_repayment']) ? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' : '' }}">
                                        {{ $type['display_name'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ number_format($type['count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $type['percentage_of_total'] }}%</span>
                                    <div class="ml-2 w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-blue-600 dark:bg-blue-400" style="width: {{ min(100, $type['percentage_of_total']) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($type['total_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($type['avg_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($type['min_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($type['max_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($type['daily_average'], 1) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction Type Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- High Volume Types -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Highest Volume Transaction Types') }}</h3>
            
            @php
                $topByVolume = $typeAnalysis->sortByDesc('count')->take(3)->values();
            @endphp
            
            <div class="space-y-3">
                @foreach($topByVolume as $index => $type)
                    <div class="flex items-center justify-between p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $index + 1 }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $type['display_name'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $type['percentage_of_total'] }}% of all transactions</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($type['count']) }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($type['total_amount'], 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- High Value Types -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Highest Value Transaction Types') }}</h3>
            
            @php
                $topByValue = $typeAnalysis->sortByDesc('total_amount')->take(3)->values();
                $totalAmountForCalc = $typeAnalysis->sum('total_amount');
            @endphp
            
            <div class="space-y-3">
                @foreach($topByValue as $index => $type)
                    @php
                        $valuePercentage = $totalAmountForCalc > 0 ? ($type['total_amount'] / $totalAmountForCalc) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $index + 1 }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $type['display_name'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ round($valuePercentage, 1) }}% of total value</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['total_amount'], 0) }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($type['count']) }} transactions</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Average Transaction Analysis -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Average Transaction Size Analysis') }}</h3>
        
        @php
            $sortedByAverage = $typeAnalysis->sortByDesc('avg_amount');
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($sortedByAverage as $type)
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ $type['display_name'] }}</h4>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Average Size') }}</p>
                            <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($type['avg_amount'], 0) }}</p>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ __('Range:') }}</span>
                            <span class="text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($type['min_amount'], 0) }} - {{ number_format($type['max_amount'], 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ __('Daily Avg:') }}</span>
                            <span class="text-zinc-600 dark:text-zinc-400">{{ number_format($type['daily_average'], 1) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($typeAnalysis->isEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('No Transaction Data') }}</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No transactions found for the selected period.') }}</p>
            </div>
        </div>
    @endif
</div> 