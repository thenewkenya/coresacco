<!-- Risk Analysis -->
<div class="space-y-6">
    <!-- Risk Categories Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Low Risk Loans') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($riskCategories['low_risk']) }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-green-600">
                {{ __('Risk Score ≤ 30') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Medium Risk Loans') }}</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($riskCategories['medium_risk']) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-yellow-600">
                {{ __('Risk Score 31-70') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('High Risk Loans') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($riskCategories['high_risk']) }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-600">
                {{ __('Risk Score > 70') }}
            </div>
        </div>
    </div>

    <!-- Risk by Loan Type -->
    @if($riskByType->isNotEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Risk Analysis by Loan Type') }}</h3>
            <div class="space-y-4">
                @foreach($riskByType as $typeData)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $typeData['type_name'] ?: __('Unassigned') }}</span>
                            <span class="text-sm px-2 py-1 rounded
                                {{ $typeData['average_risk'] <= 30 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($typeData['average_risk'] <= 70 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ __('Avg Risk') }}: {{ number_format($typeData['average_risk'], 1) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500">{{ __('Total Loans') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['total_count']) }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('High Risk') }}</span>
                                <div class="font-semibold text-red-600 dark:text-red-400">{{ number_format($typeData['high_risk_count']) }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Total Amount') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format($typeData['total_amount'], 0) }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Risk Percentage') }}</span>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $typeData['total_count'] > 0 ? number_format(($typeData['high_risk_count'] / $typeData['total_count']) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Early Warning Indicators -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Early Warning Indicators') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-orange-900 dark:text-orange-100">{{ number_format($earlyWarnings['loans_30_days_overdue']) }}</div>
                        <div class="text-sm text-orange-700 dark:text-orange-300">{{ __('Loans 30-60 Days Overdue') }}</div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-red-900 dark:text-red-100">{{ number_format($earlyWarnings['loans_with_declining_payments']) }}</div>
                        <div class="text-sm text-red-700 dark:text-red-300">{{ __('Loans with Declining Payments') }}</div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ number_format($earlyWarnings['members_with_multiple_overdue']) }}</div>
                        <div class="text-sm text-purple-700 dark:text-purple-300">{{ __('Members with Multiple Overdue') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Risk Assessment -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Loan Risk Assessment') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Risk Score') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Credit Score') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Performance') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Days Overdue') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($riskAnalysis->sortByDesc('risk_score')->take(50) as $analysis)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $analysis['loan']->member->name ?? __('N/A') }}</div>
                                <div class="text-xs text-zinc-500">{{ $analysis['loan']->member->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $analysis['loan']->loanType->name ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('KES') }} {{ number_format($analysis['loan']->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $analysis['risk_score'] <= 30 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($analysis['risk_score'] <= 70 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                        {{ number_format($analysis['risk_score']) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($analysis['member_credit_score']) }}</div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-1">
                                    <div class="h-1 rounded-full {{ $analysis['member_credit_score'] >= 80 ? 'bg-green-500' : ($analysis['member_credit_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                         style="width: {{ $analysis['member_credit_score'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($analysis['loan_performance'], 1) }}%</div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-1">
                                    <div class="h-1 rounded-full {{ $analysis['loan_performance'] >= 80 ? 'bg-green-500' : ($analysis['loan_performance'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                         style="width: {{ $analysis['loan_performance'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($analysis['days_overdue'] > 0)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        {{ $analysis['days_overdue'] }} {{ __('days') }}
                                    </span>
                                @else
                                    <span class="text-green-600 dark:text-green-400 text-sm">{{ __('Current') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                {{ __('No loan risk data available for the selected criteria') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riskAnalysis->count() > 50)
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 text-center text-sm text-zinc-500">
                {{ __('Showing top 50 highest risk loans of') }} {{ number_format($riskAnalysis->count()) }} {{ __('total. Export for complete data.') }}
            </div>
        @endif
    </div>

    <!-- Risk Methodology -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Risk Scoring Methodology') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Days Overdue (40%)') }}</h4>
                <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                    <li>• {{ __('0 days: 0 points') }}</li>
                    <li>• {{ __('1-30 days: 10 points') }}</li>
                    <li>• {{ __('31-60 days: 20 points') }}</li>
                    <li>• {{ __('61-90 days: 30 points') }}</li>
                    <li>• {{ __('90+ days: 40 points') }}</li>
                </ul>
            </div>
            <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Loan-to-Savings Ratio (30%)') }}</h4>
                <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                    <li>• {{ __('≤50%: 0 points') }}</li>
                    <li>• {{ __('51-100%: 10 points') }}</li>
                    <li>• {{ __('101-150%: 15 points') }}</li>
                    <li>• {{ __('151-200%: 20 points') }}</li>
                    <li>• {{ __('>200%: 30 points') }}</li>
                </ul>
            </div>
            <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Payment History (30%)') }}</h4>
                <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                    <li>• {{ __('≥75%: 0 points') }}</li>
                    <li>• {{ __('50-74%: 10 points') }}</li>
                    <li>• {{ __('25-49%: 20 points') }}</li>
                    <li>• {{ __('<25%: 30 points') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div> 