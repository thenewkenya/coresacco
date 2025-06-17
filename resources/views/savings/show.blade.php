<x-layouts.app :title="__('Account Details - :number', ['number' => $account->account_number])">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Account Details') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $account->account_number }} • {{ $account->member->name }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @can('manage', $account)
                        <flux:button variant="outline" icon="plus">
                            {{ __('Deposit') }}
                        </flux:button>
                        <flux:button variant="outline" icon="minus">
                            {{ __('Withdraw') }}
                        </flux:button>
                        @endcan
                        <flux:button variant="ghost" :href="route('savings.index')" wire:navigate>
                            {{ __('Back to Accounts') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Account Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Current Balance</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($account->balance, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.sparkles class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Interest Earned</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($interestEarned, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.tag class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Account Type</p>
                            <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 capitalize">{{ $account->account_type }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.shield-check class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Status</p>
                            <div class="mt-1">
                                @if($account->status === 'active')
                                    <flux:badge variant="success">Active</flux:badge>
                                @elseif($account->status === 'suspended')
                                    <flux:badge variant="warning">Suspended</flux:badge>
                                @else
                                    <flux:badge variant="danger">{{ ucfirst($account->status) }}</flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2">
                    <!-- Transaction History -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Transaction History') }}
                            </h3>
                        </div>

                        @if($transactions->count() > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($transactions as $transaction)
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-2 rounded-lg {{ $transaction->type === 'deposit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : ($transaction->type === 'interest' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                            @if($transaction->type === 'deposit')
                                                <flux:icon.arrow-down class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                            @elseif($transaction->type === 'interest')
                                                <flux:icon.sparkles class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            @else
                                                <flux:icon.arrow-up class="w-4 h-4 text-red-600 dark:text-red-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $transaction->description }}
                                            </p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $transaction->created_at->format('M d, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold {{ $transaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $transaction->type === 'withdrawal' ? '-' : '+' }}KES {{ number_format($transaction->amount, 2) }}
                                        </p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            Balance: KES {{ number_format($transaction->balance_after, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                            {{ $transactions->links() }}
                        </div>
                        @else
                        <div class="p-12 text-center">
                            <flux:icon.arrows-right-left class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ __('No Transactions Yet') }}
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('This account has no transaction history.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Account Details -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Account Details') }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Account Number</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->account_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Account Holder</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->member->name }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $account->member->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Account Type</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100 capitalize">{{ $account->account_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Currency</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->currency }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Created</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Summary -->
                    @if(isset($summary))
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('90-Day Summary') }}
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Deposits</span>
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                    +KES {{ number_format($summary['total_deposits'] ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Withdrawals</span>
                                <span class="font-medium text-red-600 dark:text-red-400">
                                    -KES {{ number_format($summary['total_withdrawals'] ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-700 pt-2">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Net Change</span>
                                <span class="font-semibold {{ ($summary['net_change'] ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ ($summary['net_change'] ?? 0) >= 0 ? '+' : '' }}KES {{ number_format($summary['net_change'] ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    @can('manage', $account)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Quick Actions') }}
                        </h3>
                        <div class="space-y-3">
                            <flux:button variant="outline" class="w-full justify-start" icon="plus">
                                {{ __('Make Deposit') }}
                            </flux:button>
                            <flux:button variant="outline" class="w-full justify-start" icon="minus">
                                {{ __('Make Withdrawal') }}
                            </flux:button>
                            @if(auth()->user()->role !== 'member')
                            <flux:button variant="outline" class="w-full justify-start" icon="calculator">
                                {{ __('Calculate Interest') }}
                            </flux:button>
                            <flux:button variant="outline" class="w-full justify-start" icon="cog">
                                {{ __('Update Status') }}
                            </flux:button>
                            @endif
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 