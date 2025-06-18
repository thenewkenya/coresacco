<x-layouts.app :title="__('My Payments')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Payments') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('View and manage your payment history') }}
                        </p>
                    </div>
                    <flux:button variant="primary" icon="plus" :href="route('payments.create')">
                        {{ __('Make Payment') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Payment Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">This Month</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Paid') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 28,500</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('8 payments') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Outstanding') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 5,000</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Due Dec 28') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Score</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Payment History') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">98%</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('On-time rate') }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Payment History') }}
                        </h3>
                        <flux:button variant="ghost" size="sm">
                            {{ __('Download Statement') }}
                        </flux:button>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['type' => 'Loan Payment', 'amount' => '8500', 'status' => 'completed', 'method' => 'M-Pesa', 'date' => '2024-12-15'],
                        ['type' => 'Deposit', 'amount' => '15000', 'status' => 'completed', 'method' => 'Bank Transfer', 'date' => '2024-12-10'],
                        ['type' => 'Insurance Premium', 'amount' => '2500', 'status' => 'completed', 'method' => 'Auto-debit', 'date' => '2024-12-05'],
                        ['type' => 'Loan Payment', 'amount' => '8500', 'status' => 'completed', 'method' => 'Cash', 'date' => '2024-11-28']
                    ] as $payment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $payment['type'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $payment['method'] }} • {{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($payment['amount']) }}
                                </p>
                                <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                    {{ ucfirst($payment['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 