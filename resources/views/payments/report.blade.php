<x-layouts.app :title="__('Payments Report')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Payments Report') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive payment transactions analysis and metrics') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray">
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('payments.index')" wire:navigate>
                            {{ __('Back to Payments') }}
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
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ $startDate }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ $endDate }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Payment Type') }}</flux:label>
                            <flux:select name="type">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                <option value="loan_repayment" {{ request('type') == 'loan_repayment' ? 'selected' : '' }}>Loan Repayment</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <flux:select name="status">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Payments</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_payments']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Amount</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($stats['total_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Average Payment</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($stats['average_payment'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Success Rate</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['success_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Payment Type Breakdown') }}
                    </h3>
                    <div class="space-y-4">
                        @php
                            $paymentsByType = $payments->groupBy('type');
                            $totalPayments = $payments->count();
                        @endphp
                        @foreach($paymentsByType as $type => $typePayments)
                        @php
                            $count = $typePayments->count();
                            $amount = $typePayments->sum('amount');
                            $percentage = $totalPayments > 0 ? ($count / $totalPayments) * 100 : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $type === 'deposit' ? 'bg-emerald-500' : ($type === 'withdrawal' ? 'bg-red-500' : ($type === 'loan_repayment' ? 'bg-blue-500' : 'bg-purple-500')) }}"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($amount, 0) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Payment Methods') }}
                    </h3>
                    <div class="space-y-4">
                        @php
                            $paymentsByMethod = $payments->groupBy('payment_method');
                        @endphp
                        @foreach($paymentsByMethod as $method => $methodPayments)
                        @php
                            $count = $methodPayments->count();
                            $amount = $methodPayments->sum('amount');
                            $percentage = $totalPayments > 0 ? ($count / $totalPayments) * 100 : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                @if($method === 'mpesa')
                                    <div class="w-3 h-3 rounded-full mr-3 bg-green-500"></div>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">M-Pesa</span>
                                @elseif($method === 'bank_transfer')
                                    <div class="w-3 h-3 rounded-full mr-3 bg-blue-500"></div>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Bank Transfer</span>
                                @elseif($method === 'cash')
                                    <div class="w-3 h-3 rounded-full mr-3 bg-yellow-500"></div>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Cash</span>
                                @else
                                    <div class="w-3 h-3 rounded-full mr-3 bg-gray-500"></div>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400 capitalize">{{ str_replace('_', ' ', $method) }}</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($amount, 0) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Financial Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Transaction Status') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Completed</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $stats['completed_payments'] }} ({{ number_format($stats['completed_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Pending</span>
                            <span class="font-semibold text-amber-600 dark:text-amber-400">
                                {{ $stats['pending_payments'] }} ({{ number_format($stats['pending_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Failed</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">
                                {{ $stats['failed_payments'] }} ({{ number_format($stats['failed_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Total Volume</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($stats['total_volume'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Period Comparison') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">This Period</span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($stats['current_period_amount'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Previous Period</span>
                            <span class="font-semibold text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($stats['previous_period_amount'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Growth</span>
                            <span class="font-semibold {{ $stats['growth_percentage'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $stats['growth_percentage'] >= 0 ? '+' : '' }}{{ number_format($stats['growth_percentage'], 1) }}%
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Daily Average</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($stats['daily_average'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Payment List -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Payment Details') }}
                    </h3>
                </div>

                @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Reference
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-zinc-900 dark:text-zinc-100">
                                        {{ $payment->reference_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $payment->member->name }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $payment->member->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge variant="outline" class="capitalize">
                                        {{ str_replace('_', ' ', $payment->type) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100 capitalize">
                                        {{ str_replace('_', ' ', $payment->payment_method) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->status === 'completed')
                                        <flux:badge variant="success">Completed</flux:badge>
                                    @elseif($payment->status === 'pending')
                                        <flux:badge variant="warning">Pending</flux:badge>
                                    @elseif($payment->status === 'failed')
                                        <flux:badge variant="danger">Failed</flux:badge>
                                    @else
                                        <flux:badge variant="outline">{{ ucfirst($payment->status) }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:button variant="ghost" size="sm" :href="route('payments.show', $payment)" wire:navigate>
                                        {{ __('View') }}
                                    </flux:button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $payments->links() }}
                </div>
                @endif
                @else
                <div class="p-12 text-center">
                    <flux:icon.credit-card class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Data Available') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        {{ __('No payments found for the selected criteria.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
