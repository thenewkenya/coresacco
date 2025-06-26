<!-- Arrears Analysis -->
<div class="space-y-6">
    <!-- Key Arrears Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Overdue Loans') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($arrears['total_overdue']) }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-500">
                {{ __('Requires immediate attention') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Overdue Amount') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ __('KES') }} {{ number_format($arrears['total_overdue_amount'], 2) }}</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-orange-500">
                {{ __('Total at risk') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('90+ Days Overdue') }}</p>
                    <p class="text-2xl font-bold text-red-800 dark:text-red-300">{{ number_format($arrears['by_days_overdue']['90+']['count']) }}</p>
                </div>
                <div class="p-3 bg-red-200 dark:bg-red-800 rounded-lg">
                    <svg class="w-6 h-6 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-600">
                {{ __('KES') }} {{ number_format($arrears['by_days_overdue']['90+']['amount'], 0) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('30-60 Days') }}</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($arrears['by_days_overdue']['31-60']['count']) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-yellow-600">
                {{ __('Monitor closely') }}
            </div>
        </div>
    </div>

    <!-- Aging Analysis -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Aging Analysis') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($arrears['by_days_overdue'] as $period => $data)
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="text-center">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">{{ $period }} {{ __('Days') }}</div>
                        <div class="text-2xl font-bold mb-1 
                            {{ $period === '1-30' ? 'text-orange-600' : 
                               ($period === '31-60' ? 'text-red-500' : 
                                ($period === '61-90' ? 'text-red-600' : 'text-red-800')) }}">
                            {{ number_format($data['count']) }}
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ __('KES') }} {{ number_format($data['amount'], 0) }}
                        </div>
                        <div class="mt-3 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full">
                            <div class="h-2 rounded-full
                                {{ $period === '1-30' ? 'bg-orange-500' : 
                                   ($period === '31-60' ? 'bg-red-500' : 
                                    ($period === '61-90' ? 'bg-red-600' : 'bg-red-800')) }}"
                                style="width: {{ $arrears['total_overdue'] > 0 ? ($data['count'] / $arrears['total_overdue']) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Arrears by Loan Type -->
    @if($arrears['by_loan_type']->isNotEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Arrears by Loan Type') }}</h3>
            <div class="space-y-3">
                @foreach($arrears['by_loan_type'] as $typeName => $typeData)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $typeName ?: __('Unassigned') }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($typeData['count']) }} {{ __('loans') }}</div>
                            <div class="text-xs text-zinc-500">{{ __('KES') }} {{ number_format($typeData['amount'], 0) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Worst Performers -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Worst Performing Loans') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Due Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Days Overdue') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Risk Level') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($arrears['worst_performers'] as $loan)
                        @php
                            $daysOverdue = $loan->due_date->diffInDays(now());
                            $riskLevel = $daysOverdue > 90 ? 'Critical' : ($daysOverdue > 60 ? 'High' : ($daysOverdue > 30 ? 'Medium' : 'Low'));
                            $riskColor = $daysOverdue > 90 ? 'red' : ($daysOverdue > 60 ? 'red' : ($daysOverdue > 30 ? 'yellow' : 'orange'));
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->member->name ?? __('N/A') }}</div>
                                <div class="text-xs text-zinc-500">{{ $loan->member->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $loan->loanType->name ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('KES') }} {{ number_format($loan->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $loan->due_date->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $daysOverdue }} {{ __('days') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $riskColor === 'red' && $daysOverdue > 90 ? 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100' :
                                       ($riskColor === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                        ($riskColor === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                         'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200')) }}">
                                    {{ $riskLevel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-green-600 dark:text-green-400">
                                {{ __('No overdue loans found - excellent portfolio health!') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 