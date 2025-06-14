<x-layouts.app :title="__('My Insurance')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Insurance') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage your insurance policies and coverage') }}
                        </p>
                    </div>
                    <flux:button variant="primary" icon="plus">
                        {{ __('Apply for Coverage') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Insurance Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.heart class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Active</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Life Insurance') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 500K</p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Coverage Amount') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Monthly</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Premium') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 2,500</p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Auto-deducted') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.calendar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Due</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Next Payment') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Dec 28</p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('2024') }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Policies -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Active Policies') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'type' => 'Life Insurance',
                            'policy_number' => 'LI-2024-001',
                            'coverage' => '500000',
                            'premium' => '2500',
                            'start_date' => '2024-01-15',
                            'status' => 'active',
                            'beneficiary' => 'Jane Doe (Spouse)'
                        ],
                        [
                            'type' => 'Loan Protection',
                            'policy_number' => 'LP-2024-002',
                            'coverage' => '125000',
                            'premium' => '800',
                            'start_date' => '2024-03-01',
                            'status' => 'active',
                            'beneficiary' => 'Outstanding Loan Balance'
                        ]
                    ] as $policy)
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $policy['type'] }}
                                    </h4>
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                        {{ ucfirst($policy['status']) }}
                                    </span>
                                </div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $policy['policy_number'] }} • Started: {{ \Carbon\Carbon::parse($policy['start_date'])->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($policy['coverage']) }}
                                </p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Coverage</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Monthly Premium') }}</p>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($policy['premium']) }}
                                </p>
                            </div>

                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Beneficiary') }}</p>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $policy['beneficiary'] }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end space-x-2">
                                <flux:button variant="outline" size="sm">
                                    {{ __('View Details') }}
                                </flux:button>
                                <flux:button variant="ghost" size="sm">
                                    {{ __('Update') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Premium Payment History') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['policy' => 'Life Insurance', 'amount' => '2500', 'date' => '2024-11-28', 'status' => 'paid'],
                        ['policy' => 'Loan Protection', 'amount' => '800', 'date' => '2024-11-28', 'status' => 'paid'],
                        ['policy' => 'Life Insurance', 'amount' => '2500', 'date' => '2024-10-28', 'status' => 'paid'],
                        ['policy' => 'Loan Protection', 'amount' => '800', 'date' => '2024-10-28', 'status' => 'paid']
                    ] as $payment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $payment['policy'] }} Premium
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
        </div>
    </div>
</x-layouts.app> 