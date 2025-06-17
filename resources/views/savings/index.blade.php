<x-layouts.app :title="__('Savings Account Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Savings Account Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage member savings accounts, deposits, and withdrawals') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @can('create', App\Models\Account::class)
                        <flux:button variant="primary" icon="plus" :href="route('savings.create')" wire:navigate>
                            {{ __('New Account') }}
                        </flux:button>
                        @endcan
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('savings.report')" wire:navigate>
                            {{ __('Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Accounts</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalAccounts) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Accounts</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($activeAccounts) }}</p>
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
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($totalBalance) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.arrow-trending-up class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">This Month Deposits</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($thisMonthDeposits) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <flux:input 
                            name="search" 
                            placeholder="Search accounts, members..." 
                            value="{{ $search }}"
                        />
                    </div>
                    <div>
                        <flux:select name="status" placeholder="All Statuses">
                            <option value="">All Statuses</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select name="account_type" placeholder="All Types">
                            <option value="">All Types</option>
                            @foreach($accountTypes as $type)
                            <option value="{{ $type }}" {{ $accountType === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex space-x-2">
                        <flux:button type="submit" variant="primary" class="flex-1">
                            {{ __('Filter') }}
                        </flux:button>
                        <flux:button variant="outline" :href="route('savings.index')" wire:navigate>
                            {{ __('Clear') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Accounts Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Savings Accounts') }}
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
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($accounts as $account)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $account->account_number }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            Created {{ $account->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $account->member->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $account->member->email }}
                                        </div>
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

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="sm" variant="outline" :href="route('savings.show', $account)" wire:navigate>
                                            {{ __('View') }}
                                        </flux:button>
                                        @can('manage', $account)
                                        <flux:dropdown>
                                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                                            <flux:menu>
                                                <flux:menu.item icon="plus" wire:click="deposit({{ $account->id }})">
                                                    Deposit
                                                </flux:menu.item>
                                                <flux:menu.item icon="minus" wire:click="withdraw({{ $account->id }})">
                                                    Withdraw
                                                </flux:menu.item>
                                                <flux:menu.separator />
                                                <flux:menu.item icon="calculator" wire:click="calculateInterest({{ $account->id }})">
                                                    Calculate Interest
                                                </flux:menu.item>
                                                <flux:menu.item icon="cog" wire:click="updateStatus({{ $account->id }})">
                                                    Update Status
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $accounts->links() }}
                </div>
                @else
                <div class="p-12 text-center">
                    <flux:icon.banknotes class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Savings Accounts Found') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('No savings accounts match your current filters.') }}
                    </p>
                    @can('create', App\Models\Account::class)
                    <flux:button variant="primary" :href="route('savings.create')" wire:navigate>
                        {{ __('Create First Account') }}
                    </flux:button>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 