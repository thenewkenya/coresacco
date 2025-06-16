<x-layouts.app :title="__('My Transactions')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Transactions') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage deposits, withdrawals, and transfers</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button :href="route('transactions.deposit.create')" variant="outline" icon="plus">
                            {{ __('Deposit') }}
                        </flux:button>
                        <flux:button :href="route('transactions.withdrawal.create')" variant="outline" icon="minus">
                            {{ __('Withdrawal') }}
                        </flux:button>
                        <flux:button :href="route('transactions.transfer.create')" variant="primary" icon="arrows-right-left">
                            {{ __('Transfer') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Account Overview -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">My Accounts</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($accounts as $account)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-lg 
                                    @if($account->account_type === 'savings') bg-emerald-100 dark:bg-emerald-900/30
                                    @elseif($account->account_type === 'shares') bg-purple-100 dark:bg-purple-900/30
                                    @else bg-blue-100 dark:bg-blue-900/30 @endif">
                                    @if($account->account_type === 'savings')
                                        <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($account->account_type === 'shares')
                                        <flux:icon.sparkles class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                    @else
                                        <flux:icon.document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                    @endif
                                </div>
                                <span class="text-sm px-2 py-1 rounded-full 
                                    @if($account->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                    {{ ucfirst($account->status) }}
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($account->account_type) }} Account</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $account->account_number }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($account->balance, 2) }}
                                </p>
                            </div>

                            @if(isset($summary[$account->id]))
                                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">30-day Deposits</p>
                                            <p class="font-medium text-emerald-600 dark:text-emerald-400">
                                                +{{ number_format($summary[$account->id]['total_deposits'], 0) }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">30-day Withdrawals</p>
                                            <p class="font-medium text-red-600 dark:text-red-400">
                                                -{{ number_format($summary[$account->id]['total_withdrawals'], 0) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $summary[$account->id]['total_transactions'] }} transactions in 30 days
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('transactions.deposit.create') }}" class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 transition-colors">
                                <flux:icon.plus class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">Make a Deposit</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Add money to your accounts</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('transactions.withdrawal.create') }}" class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                                <flux:icon.arrow-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">Withdraw Funds</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Take money from your accounts</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('transactions.transfer.create') }}" class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 hover:shadow-lg transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                                <flux:icon.arrows-right-left class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">Transfer Money</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Send money to other accounts</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Transactions</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Last 10 transactions</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @if($recentTransactions->count() > 0)
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Account</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($recentTransactions as $transaction)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                            <br>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('g:i A') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($transaction->type === 'deposit')
                                                    <flux:icon.arrow-down class="w-4 h-4 text-emerald-500 mr-2" />
                                                @elseif($transaction->type === 'withdrawal')
                                                    <flux:icon.arrow-up class="w-4 h-4 text-red-500 mr-2" />
                                                @elseif($transaction->type === 'transfer')
                                                    <flux:icon.arrows-right-left class="w-4 h-4 text-blue-500 mr-2" />
                                                @endif
                                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                            @if($transaction->account)
                                                {{ $transaction->account->account_number }}
                                                <br>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($transaction->account->account_type) }}</span>
                                            @elseif(in_array($transaction->type, ['loan_disbursement', 'loan_repayment']))
                                                <span class="text-blue-600 dark:text-blue-400">Loan Transaction</span>
                                                <br>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Direct to member</span>
                                            @else
                                                <span class="text-red-500">Account Deleted</span>
                                                <br>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $transaction->description }}
                                            @if($transaction->reference_number)
                                                <br>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Ref: {{ $transaction->reference_number }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($transaction->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                                @elseif($transaction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                                @elseif($transaction->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                            <span class="
                                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0)) text-emerald-600 dark:text-emerald-400
                                                @elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0)) text-red-600 dark:text-red-400
                                                @else text-blue-600 dark:text-blue-400 @endif">
                                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0))+@elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0))-@endif
                                                KES {{ number_format(abs($transaction->amount), 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('transactions.receipt', $transaction) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                View Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center">
                            <flux:icon.document-text class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No transactions yet</h3>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-6">Start by making your first deposit or transfer.</p>
                            <flux:button :href="route('transactions.deposit.create')" variant="primary" icon="plus">
                                {{ __('Make First Deposit') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 