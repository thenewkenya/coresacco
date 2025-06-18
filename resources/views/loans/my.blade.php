<x-layouts.app :title="__('My Loans')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Loans') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Track your loan applications and repayment progress') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" icon="plus" :href="route('loans.create')" wire:navigate>
                            {{ __('Apply for Loan') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Loan Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Active</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Outstanding') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 125,000</p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('2 active loans') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.calendar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Due Soon</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Next Payment') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 8,500</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Due Dec 28, 2024') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">On Track</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Credit Score') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">742</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Excellent') }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Loans -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Active Loans') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'id' => 'LN-001',
                            'type' => 'Personal Loan',
                            'amount' => '75000',
                            'outstanding' => '45000',
                            'rate' => '12.5%',
                            'term' => '24 months',
                            'next_payment' => '2024-12-28',
                            'payment_amount' => '3500',
                            'status' => 'current'
                        ],
                        [
                            'id' => 'LN-002',
                            'type' => 'Emergency Loan',
                            'amount' => '100000',
                            'outstanding' => '80000',
                            'rate' => '15.0%',
                            'term' => '18 months',
                            'next_payment' => '2024-12-30',
                            'payment_amount' => '5000',
                            'status' => 'current'
                        ]
                    ] as $loan)
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $loan['type'] }}
                                    </h4>
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                        {{ ucfirst($loan['status']) }}
                                    </span>
                                </div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $loan['id'] }} • {{ $loan['rate'] }} APR • {{ $loan['term'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($loan['outstanding']) }}
                                </p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    of KSh {{ number_format($loan['amount']) }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Next Payment') }}</p>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($loan['payment_amount']) }}
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ \Carbon\Carbon::parse($loan['next_payment'])->format('M d, Y') }}
                                </p>
                            </div>

                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Progress') }}</p>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2 mb-2">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ 100 - (($loan['outstanding'] / $loan['amount']) * 100) }}%"></div>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ number_format(100 - (($loan['outstanding'] / $loan['amount']) * 100), 1) }}% paid
                                </p>
                            </div>

                            <div class="flex items-center justify-end space-x-2">
                                <flux:button variant="outline" size="sm">
                                    {{ __('View Details') }}
                                </flux:button>
                                <flux:button variant="primary" size="sm">
                                    {{ __('Make Payment') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Recent Payments') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['loan_id' => 'LN-001', 'amount' => '3500', 'date' => '2024-11-28', 'status' => 'completed'],
                        ['loan_id' => 'LN-002', 'amount' => '5000', 'date' => '2024-11-30', 'status' => 'completed'],
                        ['loan_id' => 'LN-001', 'amount' => '3500', 'date' => '2024-10-28', 'status' => 'completed'],
                        ['loan_id' => 'LN-002', 'amount' => '5000', 'date' => '2024-10-30', 'status' => 'completed']
                    ] as $payment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ __('Loan Payment') }} - {{ $payment['loan_id'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}
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

            <!-- Empty State -->
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400 mb-6">You haven't applied for any loans yet.</p>
                <flux:button variant="primary" :href="route('loans.create')" wire:navigate>
                    {{ __('Apply for Your First Loan') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts.app> 