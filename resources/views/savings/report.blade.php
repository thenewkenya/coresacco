<x-layouts.app :title="__('Savings Report')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Savings Report') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive savings analytics and statistics') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray">
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('savings.index')" wire:navigate>
                            {{ __('Back to Accounts') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Report Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ $startDate }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ $endDate }}" />
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Accounts</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_accounts']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Accounts</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['active_accounts']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Balance</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($stats['total_balance'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Average Balance</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($stats['average_balance'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Statistics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Transaction Summary') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Deposits</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                +KES {{ number_format($stats['deposits_this_period'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Withdrawals</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">
                                -KES {{ number_format($stats['withdrawals_this_period'], 2) }}
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Net Change</span>
                                <span class="font-semibold {{ ($stats['deposits_this_period'] - $stats['withdrawals_this_period']) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ ($stats['deposits_this_period'] - $stats['withdrawals_this_period']) >= 0 ? '+' : '' }}KES {{ number_format($stats['deposits_this_period'] - $stats['withdrawals_this_period'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Account Distribution') }}
                    </h3>
                    <div class="space-y-3">
                        @php
                            $accountTypes = $accounts->groupBy('account_type');
                            $totalAccounts = $accounts->count();
                        @endphp
                        @foreach($accountTypes as $type => $typeAccounts)
                        @php
                            $count = $typeAccounts->count();
                            $percentage = $totalAccounts > 0 ? ($count / $totalAccounts) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $type === 'savings' ? 'bg-blue-500' : ($type === 'shares' ? 'bg-green-500' : 'bg-purple-500') }}"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400 capitalize">{{ $type }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detailed Account List -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Account Details') }}
                    </h3>
                </div>

                @if($accounts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Account
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Balance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Created
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($accounts as $account)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $account->account_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $account->member->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge variant="outline" class="capitalize">
                                        {{ $account->account_type }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($account->balance, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($account->status === 'active')
                                        <flux:badge variant="success">Active</flux:badge>
                                    @elseif($account->status === 'suspended')
                                        <flux:badge variant="warning">Suspended</flux:badge>
                                    @else
                                        <flux:badge variant="danger">{{ ucfirst($account->status) }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $account->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-12 text-center">
                    <flux:icon.banknotes class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Data Available') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        {{ __('No savings accounts found for the selected period.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 