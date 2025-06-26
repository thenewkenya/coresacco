<!-- Transaction Details Report -->
<div class="space-y-6">
    <!-- Summary Statistics -->
    @if(isset($summary))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Transactions') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($summary['total_transactions']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Amount') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($summary['total_amount'], 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Peak Hour') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $summary['peak_hour']['formatted'] ?? 'N/A' }}</p>
                    @if($summary['peak_hour'])
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $summary['peak_hour']['count'] }} transactions</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Members') }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $summary['top_members']->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction Status Breakdown -->
    @if(isset($summary['by_status']))
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Transaction Status Breakdown') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($summary['by_status'] as $status => $data)
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $status === 'pending' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                            {{ $status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                            {{ number_format($data['count']) }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($data['amount'], 0) }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $summary['total_transactions'] > 0 ? round(($data['count'] / $summary['total_transactions']) * 100, 1) : 0 }}% of total
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transaction Type Analysis -->
    @if(isset($summary['by_type']))
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Transaction Type Analysis') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($summary['by_type'] as $type => $data)
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ ucwords(str_replace('_', ' ', $type)) }}</h4>
                    <div class="mt-2 space-y-1">
                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($data['count']) }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">KES {{ number_format($data['amount'], 0) }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Avg: KES {{ number_format($data['avg_amount'], 0) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Active Members -->
    @if(isset($summary['top_members']) && $summary['top_members']->isNotEmpty())
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Most Active Members') }}</h3>
        
        <div class="space-y-3">
            @foreach($summary['top_members']->take(10) as $memberData)
                @if($memberData['member'])
                <div class="flex items-center justify-between p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ substr($memberData['member']->name, 0, 2) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $memberData['member']->name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $memberData['member']->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($memberData['transaction_count']) }} transactions</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($memberData['total_amount'], 0) }}</p>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detailed Transaction List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Transaction Details') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date & Time') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Reference') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $transaction->created_at->format('M j, Y') }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $transaction->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $transaction->type === 'withdrawal' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $transaction->type === 'loan_disbursement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $transaction->type === 'loan_repayment' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                    {{ !in_array($transaction->type, ['deposit', 'withdrawal', 'loan_disbursement', 'loan_repayment']) ? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' : '' }}">
                                    {{ ucwords(str_replace('_', ' ', $transaction->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->member)
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->member->email }}</div>
                                @elseif($transaction->account && $transaction->account->member)
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->account->member->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->account->member->email }}</div>
                                @else
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('N/A') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $transaction->status === 'pending' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                    {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                    {{ ucwords($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $transaction->reference_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ Str::limit($transaction->description ?? 'N/A', 30) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No transactions found for the selected period.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 