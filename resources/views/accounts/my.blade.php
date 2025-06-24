<x-layouts.app :title="__('My Accounts')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Accounts') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage your SACCO accounts and view balances') }}
                        </p>
                    </div>
                    <flux:button href="{{ route('accounts.create') }}" icon="plus" variant="primary">
                        {{ __('Open New Account') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            @if($accounts->count() > 0)
                <!-- Account Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
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
                            $color = $accountColors[$account->account_type] ?? 'zinc';
                            $icon = $accountIcons[$account->account_type] ?? 'banknotes';
                            $rate = $accountRates[$account->account_type] ?? 7.0;
                        @endphp
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 rounded-lg">
                                        <flux:icon.{{ $icon }} class="w-6 h-6 text-{{ $color }}-600" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $account->getDisplayName() }}
                                        </h3>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $account->account_number }}
                                        </p>
                                    </div>
                                </div>
                                @php
                                    $statusColors = [
                                        'active' => 'green',
                                        'dormant' => 'yellow',
                                        'frozen' => 'red',
                                        'closed' => 'zinc'
                                    ];
                                    $statusColor = $statusColors[$account->status] ?? 'zinc';
                                @endphp
                                <flux:badge color="{{ $statusColor }}" size="sm">
                                    {{ ucfirst($account->status) }}
                                </flux:badge>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Current Balance') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($account->balance, 2) }}
                                </p>
                            </div>

                            <div class="flex space-x-2">
                                <flux:button size="sm" variant="outline" class="flex-1" href="{{ route('accounts.show', $account) }}">
                                    {{ __('View Details') }}
                                </flux:button>
                                <flux:button size="sm" variant="primary" href="{{ route('transactions.deposit.create') }}">
                                    {{ __('Transact') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Account Overview Table -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Account Overview') }}
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Account
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Balance
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Last Transaction
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($accounts as $account)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @php
                                                $color = $accountColors[$account->account_type] ?? 'zinc';
                                                $icon = $accountIcons[$account->account_type] ?? 'banknotes';
                                            @endphp
                                            <div class="flex-shrink-0 h-10 w-10 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 rounded-lg flex items-center justify-center">
                                                <flux:icon.{{ $icon }} class="h-5 w-5 text-{{ $color }}-600" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $account->account_number }}
                                                </div>
                                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $account->getDisplayName() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($account->balance, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = $statusColors[$account->status] ?? 'zinc';
                                        @endphp
                                        <flux:badge color="{{ $statusColor }}" size="sm">
                                            {{ ucfirst($account->status) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        @if($account->lastTransaction)
                                            {{ $account->lastTransaction->created_at->diffForHumans() }}
                                        @else
                                            {{ __('No transactions') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <flux:dropdown>
                                            <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                            <flux:menu>
                                                <flux:menu.item icon="eye" href="{{ route('accounts.show', $account) }}">
                                                    {{ __('View Details') }}
                                                </flux:menu.item>
                                                <flux:menu.item icon="currency-dollar" href="{{ route('transactions.deposit.create') }}">
                                                    {{ __('New Transaction') }}
                                                </flux:menu.item>
                                                <flux:menu.item icon="document-text" href="{{ route('accounts.show', $account) }}">
                                                    {{ __('Statement') }}
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <flux:icon.credit-card class="w-6 h-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Accounts') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $accounts->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <flux:icon.banknotes class="w-6 h-6 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Balance') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($accounts->sum('balance'), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                <flux:icon.check-circle class="w-6 h-6 text-emerald-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Accounts') }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $accounts->where('status', 'active')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <flux:icon.building-office class="mx-auto h-12 w-12 text-zinc-400" />
                    <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('No accounts yet') }}</h3>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-400">{{ __('Get started by opening your first SACCO account') }}</p>
                    <div class="mt-6">
                        <flux:button href="{{ route('accounts.create') }}" variant="primary" icon="plus">
                            {{ __('Open New Account') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 