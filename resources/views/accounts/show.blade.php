<x-layouts.app>
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Account Details</h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $account->account_number }} - {{ $accountInfo['display_name'] }}</p>
                    </div>
                    <flux:button href="{{ route('accounts.index') }}" variant="ghost" icon="arrow-left">
                        Back to Accounts
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Main Account Info -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Account Overview -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-{{ $accountInfo['color'] }}-500 to-{{ $accountInfo['color'] }}-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="h-16 w-16 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                    <flux:icon.{{ $accountInfo['icon'] }} class="h-8 w-8" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold">{{ $accountInfo['display_name'] }}</h2>
                                    <p class="text-white/80">{{ $account->account_number }}</p>
                                </div>
                            </div>
                            
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-500',
                                    'dormant' => 'bg-yellow-500', 
                                    'frozen' => 'bg-red-500',
                                    'closed' => 'bg-zinc-500'
                                ];
                                $statusColor = $statusColors[$account->status] ?? 'bg-zinc-500';
                            @endphp
                            <div class="text-center">
                                <div class="h-4 w-4 rounded-full {{ $statusColor }} mx-auto mb-1"></div>
                                <p class="text-sm capitalize">{{ $account->status }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Balance -->
                            <div class="text-center md:text-left">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Current Balance</p>
                                <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($account->balance, 2) }}
                                </p>
                            </div>
                            
                            <!-- Interest Rate -->
                            <div class="text-center md:text-left">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Interest Rate</p>
                                <p class="text-3xl font-bold text-{{ $accountInfo['color'] }}-600 dark:text-{{ $accountInfo['color'] }}-400">
                                    {{ number_format($accountInfo['interest_rate'], 1) }}%
                                </p>
                            </div>
                            
                            <!-- Minimum Balance -->
                            <div class="text-center md:text-left">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Minimum Balance</p>
                                <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($accountInfo['minimum_balance']) }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <p class="text-zinc-700 dark:text-zinc-300">{{ $accountInfo['description'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Transactions</h3>
                            <flux:button 
                                href="{{ route('transactions.index') }}" 
                                variant="ghost" 
                                size="sm"
                                icon="arrow-right"
                            >
                                View All
                            </flux:button>
                        </div>
                    </div>

                    @if($account->transactions->count() > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-600">
                            @foreach($account->transactions->take(10) as $transaction)
                                <div class="px-6 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $typeIcons = [
                                                    'deposit' => ['icon' => 'arrow-down-circle', 'color' => 'green'],
                                                    'withdrawal' => ['icon' => 'arrow-up-circle', 'color' => 'red'],
                                                    'transfer' => ['icon' => 'arrow-right-circle', 'color' => 'blue'],
                                                ];
                                                $typeInfo = $typeIcons[$transaction->type] ?? ['icon' => 'circle', 'color' => 'zinc'];
                                            @endphp
                                            <div class="h-10 w-10 rounded-lg bg-{{ $typeInfo['color'] }}-100 dark:bg-{{ $typeInfo['color'] }}-900 flex items-center justify-center">
                                                <flux:icon.{{ $typeInfo['icon'] }} class="h-5 w-5 text-{{ $typeInfo['color'] }}-600 dark:text-{{ $typeInfo['color'] }}-400" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                                </p>
                                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $transaction->created_at->format('M j, Y g:i A') }}
                                                </p>
                                                @if($transaction->description)
                                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $transaction->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <p class="text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $transaction->type === 'deposit' ? '+' : '-' }}KES {{ number_format($transaction->amount, 2) }}
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                Bal: KES {{ number_format($transaction->balance_after, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <flux:icon.document-text class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No Transactions Yet</h3>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-6">This account hasn't had any transactions.</p>
                            @if($account->status === 'active')
                                <flux:button 
                                    href="{{ route('transactions.deposit.create') }}" 
                                    variant="primary"
                                >
                                    Make First Transaction
                                </flux:button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Holder Info -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Account Holder</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                    {{ $account->member->initials() }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->member->name }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $account->member->email }}</p>
                            </div>
                        </div>
                        
                        <div class="pt-3 border-t border-zinc-200 dark:border-zinc-600">
                            <flux:button 
                                href="{{ route('members.index') }}" 
                                variant="ghost" 
                                size="sm"
                                class="w-full"
                            >
                                View Member Directory
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if($account->status === 'active')
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <flux:button 
                                href="{{ route('transactions.deposit.create', ['account' => $account->id]) }}" 
                                variant="primary" 
                                icon="arrow-down-circle"
                                class="w-full justify-start"
                            >
                                Make Deposit
                            </flux:button>
                            <flux:button 
                                href="{{ route('transactions.withdrawal.create', ['account' => $account->id]) }}" 
                                variant="outline" 
                                icon="arrow-up-circle"
                                class="w-full justify-start"
                            >
                                Make Withdrawal
                            </flux:button>
                            <flux:button 
                                href="{{ route('transactions.transfer.create', ['from_account' => $account->id]) }}" 
                                variant="ghost" 
                                icon="arrow-right-circle"
                                class="w-full justify-start"
                            >
                                Transfer Funds
                            </flux:button>
                        </div>
                    </div>
                @endif

                <!-- Account Statistics -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Account Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Deposits</span>
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                KES {{ number_format($account->transactions()->where('type', 'deposit')->sum('amount'), 2) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Withdrawals</span>
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                KES {{ number_format($account->transactions()->where('type', 'withdrawal')->sum('amount'), 2) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Transactions</span>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $account->transactions()->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Age</span>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $account->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Account Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Account Number:</span>
                            <span class="font-mono text-zinc-900 dark:text-zinc-100">{{ $account->account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Account Type:</span>
                            <span class="text-zinc-900 dark:text-zinc-100">{{ $accountInfo['display_name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Currency:</span>
                            <span class="text-zinc-900 dark:text-zinc-100">{{ $account->currency ?? 'KES' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Status:</span>
                            <span class="text-zinc-900 dark:text-zinc-100 capitalize">{{ $account->status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Opened:</span>
                            <span class="text-zinc-900 dark:text-zinc-100">{{ $account->created_at->format('M j, Y') }}</span>
                        </div>
                        @if($account->status_reason)
                            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-600">
                                <span class="text-zinc-600 dark:text-zinc-400">Status Reason:</span>
                                <p class="text-zinc-900 dark:text-zinc-100 mt-1">{{ $account->status_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 