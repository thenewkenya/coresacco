<!-- Portfolio Overview -->
<div class="space-y-6">
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Loans') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($portfolio['total_loans']) }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ number_format($portfolio['active_loans']) }} {{ __('active') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Portfolio Value') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format($portfolio['total_portfolio_value'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ __('KES') }} {{ number_format($portfolio['active_portfolio_value'], 2) }} {{ __('active') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('New Loans') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($portfolio['new_loans_period']) }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ __('KES') }} {{ number_format($portfolio['new_loans_value'], 2) }} {{ __('value') }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Average Loan') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format($portfolio['average_loan_amount'], 2) }}</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-zinc-500">
                {{ __('Range') }}: {{ __('KES') }} {{ number_format($portfolio['smallest_loan'], 2) }} - {{ __('KES') }} {{ number_format($portfolio['largest_loan'], 2) }}
            </div>
        </div>
    </div>

    <!-- Status Distribution and Type Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Loan Status Distribution') }}</h3>
            <div class="space-y-3">
                @foreach($statusDistribution as $status => $data)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full {{ $status === 'active' ? 'bg-green-500' : ($status === 'completed' ? 'bg-blue-500' : ($status === 'defaulted' ? 'bg-red-500' : 'bg-yellow-500')) }}"></div>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($data['count']) }}</div>
                            <div class="text-xs text-zinc-500">{{ $data['percentage'] }}% • {{ __('KES') }} {{ number_format($data['amount'], 0) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Loan Type Analysis -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Loan Type Analysis') }}</h3>
            <div class="space-y-3">
                @forelse($typeAnalysis as $typeName => $data)
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $typeName ?: __('Unassigned') }}</span>
                            <span class="text-sm text-zinc-500">{{ $data['percentage'] }}%</span>
                        </div>
                        <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400">
                            <span>{{ number_format($data['count']) }} {{ __('loans') }}</span>
                            <span>{{ __('KES') }} {{ number_format($data['avg_amount'], 0) }} {{ __('avg') }}</span>
                        </div>
                        <div class="mt-2 text-xs">
                            <span class="text-green-600 dark:text-green-400">{{ $data['active_count'] }} {{ __('active') }}</span>
                            <span class="text-zinc-500 mx-2">•</span>
                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('KES') }} {{ number_format($data['amount'], 0) }} {{ __('total') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-4">{{ __('No loan type data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detailed Loan Listing -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Loan Details') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Interest Rate') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Due Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Created') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($loans->take(50) as $loan)
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
                                {{ $loan->interest_rate }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $loan->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($loan->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                        ($loan->status === 'defaulted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                         'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                @if($loan->due_date)
                                    {{ $loan->due_date->format('M j, Y') }}
                                    @if($loan->status === 'active' && $loan->due_date < now())
                                        <span class="text-red-600 dark:text-red-400 text-xs block">
                                            {{ $loan->due_date->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                                {{ $loan->created_at->format('M j, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                {{ __('No loans found for the selected criteria') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($loans->count() > 50)
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 text-center text-sm text-zinc-500">
                {{ __('Showing first 50 of') }} {{ number_format($loans->count()) }} {{ __('loans. Export for complete data.') }}
            </div>
        @endif
    </div>
</div> 