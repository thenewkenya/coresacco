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
                        <div class="account-card bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-all duration-300" data-color="{{ $color }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="account-icon p-2 rounded-lg transition-colors duration-300">
                                        <flux:icon.{{ $icon }} class="w-6 h-6 transition-colors duration-300" />
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
                                <div class="flex items-center mt-2 text-xs">
                                    <flux:icon.percent-badge class="w-3 h-3 mr-1 account-feature-icon" />
                                    <span class="account-feature-text">{{ $rate }}% p.a. interest</span>
                                </div>
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
                                @php
                                    $color = $accountColors[$account->account_type] ?? 'zinc';
                                    $icon = $accountIcons[$account->account_type] ?? 'banknotes';
                                @endphp
                                <tr class="account-row hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-200" data-color="{{ $color }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="account-icon flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center transition-colors duration-300">
                                                <flux:icon.{{ $icon }} class="h-5 w-5 transition-colors duration-300" />
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

    <style>
        /* Color mappings for account types */
        :root {
            --emerald-400: #34d399; --emerald-500: #10b981; --emerald-600: #059669; --emerald-700: #047857;
            --blue-400: #60a5fa; --blue-500: #3b82f6; --blue-600: #2563eb; --blue-700: #1d4ed8;
            --purple-400: #a78bfa; --purple-500: #8b5cf6; --purple-600: #7c3aed; --purple-700: #6d28d9;
            --red-400: #f87171; --red-500: #ef4444; --red-600: #dc2626; --red-700: #b91c1c;
            --yellow-400: #facc15; --yellow-500: #eab308; --yellow-600: #ca8a04; --yellow-700: #a16207;
            --indigo-400: #818cf8; --indigo-500: #6366f1; --indigo-600: #4f46e5; --indigo-700: #4338ca;
            --cyan-400: #22d3ee; --cyan-500: #06b6d4; --cyan-600: #0891b2; --cyan-700: #0e7490;
            --orange-400: #fb923c; --orange-500: #f97316; --orange-600: #ea580c; --orange-700: #c2410c;
            --pink-400: #f472b6; --pink-500: #ec4899; --pink-600: #db2777; --pink-700: #be185d;
            --gray-400: #9ca3af; --gray-500: #6b7280; --gray-600: #4b5563; --gray-700: #374151;
            --teal-400: #2dd4bf; --teal-500: #14b8a6; --teal-600: #0d9488; --teal-700: #0f766e;
            --amber-400: #fbbf24; --amber-500: #f59e0b; --amber-600: #d97706; --amber-700: #b45309;
        }

        /* Account card icon colors - Light mode */
        .account-card[data-color="emerald"] .account-icon { background-color: rgb(209 250 229); color: var(--emerald-600); }
        .account-card[data-color="blue"] .account-icon { background-color: rgb(219 234 254); color: var(--blue-600); }
        .account-card[data-color="purple"] .account-icon { background-color: rgb(237 233 254); color: var(--purple-600); }
        .account-card[data-color="red"] .account-icon { background-color: rgb(254 226 226); color: var(--red-600); }
        .account-card[data-color="yellow"] .account-icon { background-color: rgb(254 249 195); color: var(--yellow-600); }
        .account-card[data-color="indigo"] .account-icon { background-color: rgb(224 231 255); color: var(--indigo-600); }
        .account-card[data-color="cyan"] .account-icon { background-color: rgb(207 250 254); color: var(--cyan-600); }
        .account-card[data-color="orange"] .account-icon { background-color: rgb(254 215 170); color: var(--orange-600); }
        .account-card[data-color="pink"] .account-icon { background-color: rgb(252 231 243); color: var(--pink-600); }
        .account-card[data-color="gray"] .account-icon { background-color: rgb(241 245 249); color: var(--gray-600); }
        .account-card[data-color="teal"] .account-icon { background-color: rgb(204 251 241); color: var(--teal-600); }
        .account-card[data-color="amber"] .account-icon { background-color: rgb(254 243 199); color: var(--amber-600); }

        /* Account card icon colors - Dark mode */
        .dark .account-card[data-color="emerald"] .account-icon { background-color: rgb(6 78 59 / 0.4); color: var(--emerald-400); }
        .dark .account-card[data-color="blue"] .account-icon { background-color: rgb(30 58 138 / 0.4); color: var(--blue-400); }
        .dark .account-card[data-color="purple"] .account-icon { background-color: rgb(88 28 135 / 0.4); color: var(--purple-400); }
        .dark .account-card[data-color="red"] .account-icon { background-color: rgb(127 29 29 / 0.4); color: var(--red-400); }
        .dark .account-card[data-color="yellow"] .account-icon { background-color: rgb(133 77 14 / 0.4); color: var(--yellow-400); }
        .dark .account-card[data-color="indigo"] .account-icon { background-color: rgb(55 48 163 / 0.4); color: var(--indigo-400); }
        .dark .account-card[data-color="cyan"] .account-icon { background-color: rgb(21 94 117 / 0.4); color: var(--cyan-400); }
        .dark .account-card[data-color="orange"] .account-icon { background-color: rgb(154 52 18 / 0.4); color: var(--orange-400); }
        .dark .account-card[data-color="pink"] .account-icon { background-color: rgb(131 24 67 / 0.4); color: var(--pink-400); }
        .dark .account-card[data-color="gray"] .account-icon { background-color: rgb(75 85 99 / 0.4); color: var(--gray-400); }
        .dark .account-card[data-color="teal"] .account-icon { background-color: rgb(19 78 74 / 0.4); color: var(--teal-400); }
        .dark .account-card[data-color="amber"] .account-icon { background-color: rgb(146 64 14 / 0.4); color: var(--amber-400); }

        /* Account feature icon and text colors */
        .account-card[data-color="emerald"] .account-feature-icon { color: var(--emerald-500); }
        .account-card[data-color="emerald"] .account-feature-text { color: var(--emerald-700); }
        .dark .account-card[data-color="emerald"] .account-feature-text { color: var(--emerald-400); }

        .account-card[data-color="blue"] .account-feature-icon { color: var(--blue-500); }
        .account-card[data-color="blue"] .account-feature-text { color: var(--blue-700); }
        .dark .account-card[data-color="blue"] .account-feature-text { color: var(--blue-400); }

        .account-card[data-color="purple"] .account-feature-icon { color: var(--purple-500); }
        .account-card[data-color="purple"] .account-feature-text { color: var(--purple-700); }
        .dark .account-card[data-color="purple"] .account-feature-text { color: var(--purple-400); }

        .account-card[data-color="red"] .account-feature-icon { color: var(--red-500); }
        .account-card[data-color="red"] .account-feature-text { color: var(--red-700); }
        .dark .account-card[data-color="red"] .account-feature-text { color: var(--red-400); }

        .account-card[data-color="yellow"] .account-feature-icon { color: var(--yellow-500); }
        .account-card[data-color="yellow"] .account-feature-text { color: var(--yellow-700); }
        .dark .account-card[data-color="yellow"] .account-feature-text { color: var(--yellow-400); }

        .account-card[data-color="indigo"] .account-feature-icon { color: var(--indigo-500); }
        .account-card[data-color="indigo"] .account-feature-text { color: var(--indigo-700); }
        .dark .account-card[data-color="indigo"] .account-feature-text { color: var(--indigo-400); }

        .account-card[data-color="cyan"] .account-feature-icon { color: var(--cyan-500); }
        .account-card[data-color="cyan"] .account-feature-text { color: var(--cyan-700); }
        .dark .account-card[data-color="cyan"] .account-feature-text { color: var(--cyan-400); }

        .account-card[data-color="orange"] .account-feature-icon { color: var(--orange-500); }
        .account-card[data-color="orange"] .account-feature-text { color: var(--orange-700); }
        .dark .account-card[data-color="orange"] .account-feature-text { color: var(--orange-400); }

        .account-card[data-color="pink"] .account-feature-icon { color: var(--pink-500); }
        .account-card[data-color="pink"] .account-feature-text { color: var(--pink-700); }
        .dark .account-card[data-color="pink"] .account-feature-text { color: var(--pink-400); }

        .account-card[data-color="gray"] .account-feature-icon { color: var(--gray-500); }
        .account-card[data-color="gray"] .account-feature-text { color: var(--gray-700); }
        .dark .account-card[data-color="gray"] .account-feature-text { color: var(--gray-400); }

        .account-card[data-color="teal"] .account-feature-icon { color: var(--teal-500); }
        .account-card[data-color="teal"] .account-feature-text { color: var(--teal-700); }
        .dark .account-card[data-color="teal"] .account-feature-text { color: var(--teal-400); }

        .account-card[data-color="amber"] .account-feature-icon { color: var(--amber-500); }
        .account-card[data-color="amber"] .account-feature-text { color: var(--amber-700); }
        .dark .account-card[data-color="amber"] .account-feature-text { color: var(--amber-400); }

        /* Table row icon colors - same as cards */
        .account-row[data-color="emerald"] .account-icon { background-color: rgb(209 250 229); color: var(--emerald-600); }
        .account-row[data-color="blue"] .account-icon { background-color: rgb(219 234 254); color: var(--blue-600); }
        .account-row[data-color="purple"] .account-icon { background-color: rgb(237 233 254); color: var(--purple-600); }
        .account-row[data-color="red"] .account-icon { background-color: rgb(254 226 226); color: var(--red-600); }
        .account-row[data-color="yellow"] .account-icon { background-color: rgb(254 249 195); color: var(--yellow-600); }
        .account-row[data-color="indigo"] .account-icon { background-color: rgb(224 231 255); color: var(--indigo-600); }
        .account-row[data-color="cyan"] .account-icon { background-color: rgb(207 250 254); color: var(--cyan-600); }
        .account-row[data-color="orange"] .account-icon { background-color: rgb(254 215 170); color: var(--orange-600); }
        .account-row[data-color="pink"] .account-icon { background-color: rgb(252 231 243); color: var(--pink-600); }
        .account-row[data-color="gray"] .account-icon { background-color: rgb(241 245 249); color: var(--gray-600); }
        .account-row[data-color="teal"] .account-icon { background-color: rgb(204 251 241); color: var(--teal-600); }
        .account-row[data-color="amber"] .account-icon { background-color: rgb(254 243 199); color: var(--amber-600); }

        /* Table row icon colors - Dark mode */
        .dark .account-row[data-color="emerald"] .account-icon { background-color: rgb(6 78 59 / 0.4); color: var(--emerald-400); }
        .dark .account-row[data-color="blue"] .account-icon { background-color: rgb(30 58 138 / 0.4); color: var(--blue-400); }
        .dark .account-row[data-color="purple"] .account-icon { background-color: rgb(88 28 135 / 0.4); color: var(--purple-400); }
        .dark .account-row[data-color="red"] .account-icon { background-color: rgb(127 29 29 / 0.4); color: var(--red-400); }
        .dark .account-row[data-color="yellow"] .account-icon { background-color: rgb(133 77 14 / 0.4); color: var(--yellow-400); }
        .dark .account-row[data-color="indigo"] .account-icon { background-color: rgb(55 48 163 / 0.4); color: var(--indigo-400); }
        .dark .account-row[data-color="cyan"] .account-icon { background-color: rgb(21 94 117 / 0.4); color: var(--cyan-400); }
        .dark .account-row[data-color="orange"] .account-icon { background-color: rgb(154 52 18 / 0.4); color: var(--orange-400); }
        .dark .account-row[data-color="pink"] .account-icon { background-color: rgb(131 24 67 / 0.4); color: var(--pink-400); }
        .dark .account-row[data-color="gray"] .account-icon { background-color: rgb(75 85 99 / 0.4); color: var(--gray-400); }
        .dark .account-row[data-color="teal"] .account-icon { background-color: rgb(19 78 74 / 0.4); color: var(--teal-400); }
        .dark .account-row[data-color="amber"] .account-icon { background-color: rgb(146 64 14 / 0.4); color: var(--amber-400); }

        /* Hover effects for account cards */
        .account-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .dark .account-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</x-layouts.app> 