<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Accounts</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your SACCO accounts and view balances</p>
            </div>
            <flux:button href="{{ route('accounts.create') }}" icon="plus" variant="primary">
                Open New Account
            </flux:button>
        </div>

        @if($accounts->count() > 0)
            <!-- Account Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($accounts as $account)
                    @php
                        $accountColors = [
                            'savings' => 'emerald', 'shares' => 'blue', 'deposits' => 'purple',
                            'emergency_fund' => 'red', 'holiday_savings' => 'yellow', 'retirement' => 'indigo',
                            'education' => 'cyan', 'development' => 'orange', 'welfare' => 'pink',
                            'loan_guarantee' => 'gray', 'insurance' => 'teal', 'investment' => 'amber'
                        ];
                        $accountIcons = [
                            'savings' => 'banknotes', 'shares' => 'building-library', 'deposits' => 'safe',
                            'emergency_fund' => 'shield-check', 'holiday_savings' => 'sun', 'retirement' => 'home',
                            'education' => 'academic-cap', 'development' => 'building-office-2', 'welfare' => 'heart',
                            'loan_guarantee' => 'shield-exclamation', 'insurance' => 'shield-check', 'investment' => 'chart-bar'
                        ];
                        $accountRates = [
                            'savings' => 8.5, 'shares' => 12.0, 'deposits' => 15.0,
                            'emergency_fund' => 6.0, 'holiday_savings' => 7.0, 'retirement' => 10.0,
                            'education' => 9.0, 'development' => 8.0, 'welfare' => 6.5,
                            'loan_guarantee' => 5.0, 'insurance' => 4.0, 'investment' => 18.0
                        ];
                        $color = $accountColors[$account->account_type] ?? 'gray';
                        $icon = $accountIcons[$account->account_type] ?? 'banknotes';
                        $rate = $accountRates[$account->account_type] ?? 7.0;
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Account Header -->
                        <div class="bg-gradient-to-r from-{{ $color }}-500 to-{{ $color }}-600 p-4">
                            <div class="flex items-center justify-between text-white">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                        <flux:icon.{{ $icon }} class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">{{ $account->getDisplayName() }}</h3>
                                        <p class="text-white/80 text-sm">{{ $account->account_number }}</p>
                                    </div>
                                </div>
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-500',
                                        'dormant' => 'bg-yellow-500', 
                                        'frozen' => 'bg-red-500',
                                        'closed' => 'bg-gray-500'
                                    ];
                                    $statusColor = $statusColors[$account->status] ?? 'bg-gray-500';
                                @endphp
                                <div class="h-3 w-3 rounded-full {{ $statusColor }}"></div>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="p-4">
                            <div class="space-y-3">
                                <!-- Balance -->
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Current Balance</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        KES {{ number_format($account->balance, 2) }}
                                    </p>
                                </div>

                                <!-- Account Info -->
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Interest Rate</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ number_format($rate, 1) }}%
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Status</p>
                                        <p class="font-medium text-gray-900 dark:text-white capitalize">
                                            {{ $account->status }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2 pt-2">
                                    <flux:button 
                                        href="{{ route('accounts.show', $account) }}" 
                                        variant="ghost" 
                                        size="sm"
                                        class="flex-1"
                                    >
                                        View Details
                                    </flux:button>
                                    @if($account->status === 'active')
                                        <flux:button 
                                            href="{{ route('transactions.deposit.create') }}" 
                                            variant="primary" 
                                            size="sm"
                                            class="flex-1"
                                        >
                                            Transact
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $accounts->count() }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Accounts</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            KES {{ number_format($accounts->sum('balance'), 2) }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Balance</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $accounts->where('status', 'active')->count() }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active Accounts</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                <flux:icon.banknotes class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Accounts Yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    Get started by opening your first SACCO account. Choose from various account types designed for your financial goals.
                </p>
                <flux:button href="{{ route('accounts.create') }}" variant="primary" size="lg">
                    Open Your First Account
                </flux:button>
            </div>
        @endif
    </div>


</x-layouts.app> 