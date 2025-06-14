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
                        <flux:button variant="outline" icon="arrow-path">
                            {{ __('Refresh Queue') }}
                        </flux:button>
                        <flux:button variant="primary" icon="plus">
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
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 245K</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('47 payments') }}</p>
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
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">12</p>
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
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Volume') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 2.8M</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('523 payments') }}</p>
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
                    @foreach([
                        [
                            'id' => 'PAY-001',
                            'member' => 'John Mukama',
                            'type' => 'Loan Payment',
                            'amount' => '15000',
                            'method' => 'M-Pesa',
                            'status' => 'pending',
                            'submitted' => '2024-12-15 09:30',
                            'priority' => 'normal'
                        ],
                        [
                            'id' => 'PAY-002',
                            'member' => 'Sarah Wanjiku',
                            'type' => 'Deposit',
                            'amount' => '25000',
                            'method' => 'Bank Transfer',
                            'status' => 'processing',
                            'submitted' => '2024-12-15 08:45',
                            'priority' => 'high'
                        ],
                        [
                            'id' => 'PAY-003',
                            'member' => 'Peter Kimani',
                            'type' => 'Withdrawal',
                            'amount' => '8000',
                            'method' => 'Cash',
                            'status' => 'pending',
                            'submitted' => '2024-12-15 10:15',
                            'priority' => 'normal'
                        ]
                    ] as $payment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 rounded-lg {{ $payment['status'] === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                    @if($payment['status'] === 'pending')
                                        <flux:icon.clock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    @else
                                        <flux:icon.arrow-path class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $payment['member'] }}
                                        </p>
                                        @if($payment['priority'] === 'high')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            High Priority
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $payment['type'] }} • {{ $payment['method'] }} • {{ $payment['id'] }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        {{ \Carbon\Carbon::parse($payment['submitted'])->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KSh {{ number_format($payment['amount']) }}
                                    </p>
                                    <span class="px-2 py-1 text-xs font-medium {{ $payment['status'] === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }} rounded-full">
                                        {{ ucfirst($payment['status']) }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:button variant="outline" size="sm">
                                        {{ __('Review') }}
                                    </flux:button>
                                    <flux:button variant="primary" size="sm">
                                        {{ __('Process') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
</x-layouts.app> 