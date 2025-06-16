<x-layouts.app :title="__('Transactions')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Transaction Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Monitor and manage all member transactions') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @roleany('admin', 'manager', 'staff')
                        <flux:button variant="outline" icon="arrow-path">
                            {{ __('Refresh Queue') }}
                        </flux:button>
                        <flux:button variant="primary" icon="plus">
                            {{ __('New Transaction') }}
                        </flux:button>
                        @endroleany
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Transaction Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+8%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Today\'s Volume') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 1.2M</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('156 transactions') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Review</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Pending Approval') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">23</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Needs attention') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">Alert</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Failed/Rejected') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">5</p>
                        <p class="text-xs text-red-600 dark:text-red-400">{{ __('This week') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">This Month</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Volume') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 18.5M</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('2,145 transactions') }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-6">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" size="sm">{{ __('All') }}</flux:button>
                        <flux:button variant="outline" size="sm">{{ __('Pending') }}</flux:button>
                        <flux:button variant="outline" size="sm">{{ __('Approved') }}</flux:button>
                        <flux:button variant="outline" size="sm">{{ __('Deposits') }}</flux:button>
                        <flux:button variant="outline" size="sm">{{ __('Withdrawals') }}</flux:button>
                        <flux:button variant="outline" size="sm">{{ __('Transfers') }}</flux:button>
                    </div>
                </div>
            </div>

            <!-- Pending Transactions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Pending Approval') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="outline" size="sm" icon="check">
                                {{ __('Bulk Approve') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'id' => 'TXN-001',
                            'member' => 'John Mukama',
                            'member_email' => 'john.mukama@email.com',
                            'type' => 'deposit',
                            'amount' => '25000',
                            'account' => 'SAV-001234',
                            'description' => 'Monthly savings deposit via M-Pesa',
                            'reference' => 'MP001234567',
                            'submitted' => '2024-12-15 09:30',
                            'status' => 'pending',
                            'priority' => 'normal'
                        ],
                        [
                            'id' => 'TXN-002',
                            'member' => 'Sarah Wanjiku',
                            'member_email' => 'sarah.wanjiku@email.com',
                            'type' => 'withdrawal',
                            'amount' => '50000',
                            'account' => 'SAV-001235',
                            'description' => 'Emergency withdrawal for medical expenses',
                            'reference' => 'WD001234567',
                            'submitted' => '2024-12-15 08:45',
                            'status' => 'pending',
                            'priority' => 'high'
                        ],
                        [
                            'id' => 'TXN-003',
                            'member' => 'Peter Kimani',
                            'member_email' => 'peter.kimani@email.com',
                            'type' => 'transfer',
                            'amount' => '15000',
                            'account' => 'SAV-001236',
                            'description' => 'Transfer to loan repayment account',
                            'reference' => 'TR001234567',
                            'submitted' => '2024-12-15 10:15',
                            'status' => 'pending',
                            'priority' => 'normal'
                        ]
                    ] as $transaction)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 rounded-lg {{ 
                                    $transaction['type'] === 'deposit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 
                                    ($transaction['type'] === 'withdrawal' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30') 
                                }}">
                                    @if($transaction['type'] === 'deposit')
                                        <flux:icon.arrow-down class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($transaction['type'] === 'withdrawal')
                                        <flux:icon.arrow-up class="w-5 h-5 text-red-600 dark:text-red-400" />
                                    @else
                                        <flux:icon.arrows-right-left class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ $transaction['member'] }}
                                        </p>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-500">•</span>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ ucfirst($transaction['type']) }}
                                        </p>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-500">•</span>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                            {{ \Carbon\Carbon::parse($transaction['submitted'])->format('M d, g:i A') }}
                                        </p>
                                        @if($transaction['priority'] === 'high')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            High Priority
                                        </span>
                                        @endif
                                        @if($transaction['amount'] >= 50000)
                                        <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 rounded-full">
                                            High Value
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $transaction['description'] }} • {{ $transaction['account'] }} • {{ $transaction['id'] }}
                                    </p>
                                    @if($transaction['reference'])
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        Ref: {{ $transaction['reference'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-xl font-bold {{ 
                                        $transaction['type'] === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                        ($transaction['type'] === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                    }}">
                                        {{ $transaction['type'] === 'deposit' ? '+' : ($transaction['type'] === 'withdrawal' ? '-' : '') }}KSh {{ number_format($transaction['amount']) }}
                                    </p>
                                    <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full">
                                        {{ ucfirst($transaction['status']) }}
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
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Recent Transactions') }}
                        </h3>
                        <flux:button variant="outline" size="sm" icon="arrow-right">
                            {{ __('View All') }}
                        </flux:button>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['member' => 'Grace Muthoni', 'type' => 'deposit', 'amount' => '12000', 'account' => 'SAV-001237', 'time' => '2024-12-15 11:30', 'status' => 'completed'],
                        ['member' => 'David Ochieng', 'type' => 'withdrawal', 'amount' => '8500', 'account' => 'SAV-001238', 'time' => '2024-12-15 11:15', 'status' => 'completed'],
                        ['member' => 'Mary Nyambura', 'type' => 'transfer', 'amount' => '5000', 'account' => 'SAV-001239', 'time' => '2024-12-15 10:45', 'status' => 'completed'],
                        ['member' => 'James Mwangi', 'type' => 'deposit', 'amount' => '22000', 'account' => 'SAV-001240', 'time' => '2024-12-15 10:30', 'status' => 'completed']
                    ] as $transaction)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $transaction['member'] }}
                                        </p>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-500">•</span>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ ucfirst($transaction['type']) }}
                                        </p>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-500">•</span>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                            {{ \Carbon\Carbon::parse($transaction['time'])->format('g:i A') }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $transaction['account'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold {{ 
                                    $transaction['type'] === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                    ($transaction['type'] === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                }}">
                                    {{ $transaction['type'] === 'deposit' ? '+' : ($transaction['type'] === 'withdrawal' ? '-' : '') }}KSh {{ number_format($transaction['amount']) }}
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