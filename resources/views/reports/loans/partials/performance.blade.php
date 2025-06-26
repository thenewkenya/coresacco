<!-- Performance Metrics -->
<div class="space-y-6">
    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Repayment Rate') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $performance['repayment_rate'] }}%</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($performance['repayment_rate'], 100) }}%"></div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ $performance['repayment_rate'] >= 90 ? __('Excellent') : ($performance['repayment_rate'] >= 75 ? __('Good') : ($performance['repayment_rate'] >= 60 ? __('Fair') : __('Poor'))) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Default Rate') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $performance['default_rate'] }}%</p>
                </div>
                <div class="p-3 {{ $performance['default_rate'] <= 5 ? 'bg-green-100 dark:bg-green-900' : ($performance['default_rate'] <= 10 ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-red-100 dark:bg-red-900') }} rounded-lg">
                    <svg class="w-6 h-6 {{ $performance['default_rate'] <= 5 ? 'text-green-600 dark:text-green-400' : ($performance['default_rate'] <= 10 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $performance['default_rate'] <= 5 ? 'bg-green-500' : ($performance['default_rate'] <= 10 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ min($performance['default_rate'] * 2, 100) }}%"></div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ $performance['default_rate'] <= 5 ? __('Excellent') : ($performance['default_rate'] <= 10 ? __('Acceptable') : __('Concerning')) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Portfolio at Risk') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $performance['portfolio_at_risk'] }}%</p>
                </div>
                <div class="p-3 {{ $performance['portfolio_at_risk'] <= 5 ? 'bg-green-100 dark:bg-green-900' : ($performance['portfolio_at_risk'] <= 15 ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-red-100 dark:bg-red-900') }} rounded-lg">
                    <svg class="w-6 h-6 {{ $performance['portfolio_at_risk'] <= 5 ? 'text-green-600 dark:text-green-400' : ($performance['portfolio_at_risk'] <= 15 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $performance['portfolio_at_risk'] <= 5 ? 'bg-green-500' : ($performance['portfolio_at_risk'] <= 15 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ min($performance['portfolio_at_risk'] * 2, 100) }}%"></div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ $performance['portfolio_at_risk'] <= 5 ? __('Low Risk') : ($performance['portfolio_at_risk'] <= 15 ? __('Medium Risk') : __('High Risk')) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Collection Efficiency') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $performance['collection_efficiency'] }}%</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min($performance['collection_efficiency'], 100) }}%"></div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ $performance['collection_efficiency'] >= 85 ? __('Excellent') : ($performance['collection_efficiency'] >= 70 ? __('Good') : __('Needs Improvement')) }}
            </div>
        </div>
    </div>

    <!-- Loan Processing Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Loan Processing Efficiency') }}</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Applications') }}</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($performance['total_applications']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Approved Applications') }}</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($performance['approved_applications']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Disbursed Loans') }}</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ number_format($performance['disbursed_applications']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Processing Rates') }}</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Approval Rate') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $performance['approval_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $performance['approval_rate'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Disbursement Rate') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $performance['disbursement_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $performance['disbursement_rate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Performance Insights') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 {{ $performance['repayment_rate'] >= 90 ? 'bg-green-50 dark:bg-green-900' : 'bg-yellow-50 dark:bg-yellow-900' }} rounded-lg">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-2 h-2 {{ $performance['repayment_rate'] >= 90 ? 'bg-green-500' : 'bg-yellow-500' }} rounded-full"></div>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Repayment Health') }}</span>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                    @if($performance['repayment_rate'] >= 90)
                        {{ __('Excellent repayment performance indicates strong member commitment and effective loan policies.') }}
                    @else
                        {{ __('Consider reviewing loan terms and implementing stronger follow-up procedures.') }}
                    @endif
                </p>
            </div>

            <div class="p-4 {{ $performance['default_rate'] <= 5 ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900' }} rounded-lg">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-2 h-2 {{ $performance['default_rate'] <= 5 ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Default Management') }}</span>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                    @if($performance['default_rate'] <= 5)
                        {{ __('Low default rate indicates effective credit assessment and risk management.') }}
                    @else
                        {{ __('High default rate requires immediate attention to lending criteria and member evaluation.') }}
                    @endif
                </p>
            </div>

            <div class="p-4 {{ $performance['collection_efficiency'] >= 85 ? 'bg-green-50 dark:bg-green-900' : 'bg-yellow-50 dark:bg-yellow-900' }} rounded-lg">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-2 h-2 {{ $performance['collection_efficiency'] >= 85 ? 'bg-green-500' : 'bg-yellow-500' }} rounded-full"></div>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Collection Process') }}</span>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                    @if($performance['collection_efficiency'] >= 85)
                        {{ __('Efficient collection processes are maintaining healthy cash flow.') }}
                    @else
                        {{ __('Collection efficiency could be improved through better tracking and follow-up systems.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div> 