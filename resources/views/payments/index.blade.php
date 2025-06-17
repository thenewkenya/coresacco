<x-layouts.app :title="__('Payment Processing')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Payment Processing') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Process member payments, transfers, and manage payment queues') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-path" onclick="window.location.reload()">
                            {{ __('Refresh Queue') }}
                        </flux:button>
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('payments.report')" wire:navigate>
                            {{ __('Reports') }}
                        </flux:button>
                        <flux:button variant="primary" icon="plus" :href="route('payments.create')" wire:navigate>
                            {{ __('New Payment') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Payment Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+12%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Processed Today') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($todayAmount) }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($todayTransactions) }} {{ __('payments') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Urgent</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Pending Queue') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($pendingPayments) }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Needs attention') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon.x-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">Review</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Failed/Declined') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">3</p>
                        <p class="text-xs text-red-600 dark:text-red-400">{{ __('Today') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">This Month</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Transactions') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalTransactions) }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('All time') }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Queue -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Payment Queue') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="funnel">
                                {{ __('Filter') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($transactions->take(10) as $transaction)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 rounded-lg {{ $transaction->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30' : ($transaction->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                    @if($transaction->status === 'pending')
                                        <flux:icon.clock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    @elseif($transaction->status === 'completed')
                                        <flux:icon.check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    @else
                                        <flux:icon.x-circle class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $transaction->member->name ?? 'N/A' }}
                                        </p>
                                        @if($transaction->amount >= 50000)
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            High Amount
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }} • {{ $transaction->metadata['payment_method'] ?? 'Cash' }} • {{ $transaction->reference_number }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        {{ $transaction->created_at->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($transaction->amount, 2) }}
                                    </p>
                                    @if($transaction->status === 'pending')
                                        <flux:badge variant="warning">Pending</flux:badge>
                                    @elseif($transaction->status === 'completed')
                                        <flux:badge variant="success">Completed</flux:badge>
                                    @else
                                        <flux:badge variant="danger">{{ ucfirst($transaction->status) }}</flux:badge>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:button size="sm" variant="outline" :href="route('payments.show', $transaction)" wire:navigate>
                                        {{ __('View') }}
                                    </flux:button>
                                    @if($transaction->status === 'pending')
                                        @can('approve', $transaction)
                                        <flux:dropdown>
                                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                                            <flux:menu>
                                                <flux:menu.item icon="check" onclick="approvePayment({{ $transaction->id }})">
                                                    Approve
                                                </flux:menu.item>
                                                <flux:menu.item icon="x-mark" onclick="rejectPayment({{ $transaction->id }})">
                                                    Reject
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <flux:icon.currency-dollar class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                            {{ __('No Transactions Found') }}
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            {{ __('No payment transactions available at the moment.') }}
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Recent Processed Payments') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['member' => 'Grace Muthoni', 'type' => 'Deposit', 'amount' => '12000', 'method' => 'M-Pesa', 'time' => '2024-12-15 11:30'],
                        ['member' => 'David Ochieng', 'type' => 'Loan Payment', 'amount' => '18500', 'method' => 'Bank Transfer', 'time' => '2024-12-15 11:15'],
                        ['member' => 'Mary Nyambura', 'type' => 'Withdrawal', 'amount' => '5000', 'method' => 'Cash', 'time' => '2024-12-15 10:45'],
                        ['member' => 'James Mwangi', 'type' => 'Transfer', 'amount' => '7500', 'method' => 'Internal', 'time' => '2024-12-15 10:30']
                    ] as $transaction)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $transaction['member'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $transaction['type'] }} • {{ $transaction['method'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($transaction['amount']) }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                    {{ \Carbon\Carbon::parse($transaction['time'])->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function approvePayment(transactionId) {
            if (confirm('Are you sure you want to approve this payment?')) {
                // Implementation for payment approval
                console.log('Approving payment:', transactionId);
                // You would typically make an AJAX call here
            }
        }

        function rejectPayment(transactionId) {
            if (confirm('Are you sure you want to reject this payment?')) {
                // Implementation for payment rejection
                console.log('Rejecting payment:', transactionId);
                // You would typically make an AJAX call here
            }
        }
    </script>
</x-layouts.app> 