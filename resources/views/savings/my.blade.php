<x-layouts.app :title="__('My Savings')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Savings') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage your savings accounts and track your progress') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" icon="plus">
                            {{ __('New Deposit') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Account Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+12.5%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Balance') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 65,750</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since last month') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">8.5% APY</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Interest Earned (YTD)') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 4,280</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Current rate') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.arrows-right-left class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">This Month</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Transactions') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">23</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Deposits & Withdrawals') }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Recent Transactions') }}
                        </h3>
                        <flux:button variant="ghost" size="sm">
                            {{ __('View All') }}
                        </flux:button>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['type' => 'deposit', 'amount' => '5000', 'date' => '2024-12-15', 'description' => 'Monthly Savings'],
                        ['type' => 'interest', 'amount' => '285', 'date' => '2024-12-01', 'description' => 'Interest Payment'],
                        ['type' => 'withdrawal', 'amount' => '2000', 'date' => '2024-11-28', 'description' => 'Emergency Fund'],
                        ['type' => 'deposit', 'amount' => '3500', 'date' => '2024-11-15', 'description' => 'Salary Contribution']
                    ] as $transaction)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 rounded-lg {{ $transaction['type'] === 'deposit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : ($transaction['type'] === 'interest' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                    @if($transaction['type'] === 'deposit')
                                        <flux:icon.arrow-down class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($transaction['type'] === 'interest')
                                        <flux:icon.sparkles class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    @else
                                        <flux:icon.arrow-up class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $transaction['description'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold {{ $transaction['type'] === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $transaction['type'] === 'withdrawal' ? '-' : '+' }}KSh {{ number_format($transaction['amount']) }}
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