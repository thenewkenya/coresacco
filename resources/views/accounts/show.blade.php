<x-layouts.app :title="$accountInfo['display_name'] . ' - ' . $account->account_number">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <flux:button href="{{ route('accounts.index') }}" variant="ghost" icon="arrow-left" size="sm">
                            {{ __('Back') }}
                        </flux:button>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ __('Account Details') }}
                            </h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $account->account_number }} - {{ $accountInfo['display_name'] }}
                            </p>
                        </div>
                    </div>
                    
                    @if($account->status === 'active')
                    <div class="flex items-center space-x-3">
                        <flux:button href="{{ route('transactions.deposit.create') }}" icon="plus" variant="primary">
                            {{ __('New Transaction') }}
                        </flux:button>
                        <flux:dropdown>
                            <flux:button icon="ellipsis-horizontal" variant="ghost" />
                            <flux:menu>
                                <flux:menu.item icon="document-text">{{ __('Download Statement') }}</flux:menu.item>
                                <flux:menu.item icon="printer">{{ __('Print Details') }}</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="cog-6-tooth">{{ __('Account Settings') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Account Overview Card -->
                    <div class="account-overview-card bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden" data-color="{{ $accountInfo['color'] }}">
                        <!-- Header with Account Info -->
                        <div class="account-header p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    <div class="account-icon-large p-4 rounded-xl">
                                        <flux:icon.{{ $accountInfo['icon'] }} class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-white">{{ $accountInfo['display_name'] }}</h2>
                                        <p class="text-white/80">{{ $account->account_number }}</p>
                                        <p class="text-white/60 text-sm mt-1">{{ $account->member->name }}</p>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="account-status-indicator w-4 h-4 rounded-full mx-auto mb-2" data-status="{{ $account->status }}"></div>
                                    <p class="text-sm text-white/80 capitalize">{{ $account->status }}</p>
                                </div>
                            </div>

                            <!-- Balance Display -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-white/60 text-sm mb-1">{{ __('Current Balance') }}</p>
                                    <p class="text-4xl font-bold text-white mb-2">
                                        KES {{ number_format($account->balance, 2) }}
                                    </p>
                                    <div class="flex items-center justify-center space-x-4 text-white/80 text-sm">
                                        <div class="flex items-center">
                                            <flux:icon.percent-badge class="w-4 h-4 mr-1" />
                                            <span>{{ number_format($accountInfo['interest_rate'], 1) }}% p.a.</span>
                                        </div>
                                        <div class="flex items-center">
                                            <flux:icon.shield-check class="w-4 h-4 mr-1" />
                                            <span>Min: KES {{ number_format($accountInfo['minimum_balance']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Stats Grid -->
                        <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-white dark:bg-zinc-800 rounded-lg">
                                    <div class="account-stat-icon w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" data-type="deposits">
                                        <flux:icon.arrow-trending-up class="w-4 h-4" />
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Deposits') }}</p>
                                    <p class="font-bold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($account->transactions()->where('type', 'deposit')->sum('amount'), 2) }}
                                    </p>
                                </div>
                                <div class="text-center p-4 bg-white dark:bg-zinc-800 rounded-lg">
                                    <div class="account-stat-icon w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" data-type="withdrawals">
                                        <flux:icon.arrow-trending-down class="w-4 h-4" />
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Withdrawals') }}</p>
                                    <p class="font-bold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($account->transactions()->where('type', 'withdrawal')->sum('amount'), 2) }}
                                    </p>
                                </div>
                                <div class="text-center p-4 bg-white dark:bg-zinc-800 rounded-lg">
                                    <div class="account-stat-icon w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" data-type="transactions">
                                        <flux:icon.document-text class="w-4 h-4" />
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Transactions') }}</p>
                                    <p class="font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $account->transactions()->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Description -->
                        <div class="p-6">
                            <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-4">
                                <p class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed">{{ $accountInfo['description'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Transactions') }}</h3>
                                <flux:button 
                                    href="{{ route('transactions.index') }}" 
                                    variant="ghost" 
                                    size="sm"
                                    icon="arrow-right"
                                >
                                    {{ __('View All') }}
                                </flux:button>
                            </div>
                        </div>

                        @if($account->transactions->count() > 0)
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($account->transactions->take(10) as $transaction)
                                    <div class="px-6 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="transaction-icon h-10 w-10 rounded-lg flex items-center justify-center" data-type="{{ $transaction->type }}">
                                                    @if($transaction->type === 'deposit')
                                                        <flux:icon.arrow-down-circle class="h-5 w-5" />
                                                    @elseif($transaction->type === 'withdrawal')
                                                        <flux:icon.arrow-up-circle class="h-5 w-5" />
                                                    @elseif($transaction->type === 'transfer')
                                                        <flux:icon.arrow-right-circle class="h-5 w-5" />
                                                    @else
                                                        <flux:icon.banknotes class="h-5 w-5" />
                                                    @endif
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
                                                <p class="text-sm font-semibold transaction-amount" data-type="{{ $transaction->type }}">
                                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}KES {{ number_format($transaction->amount, 2) }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Bal: KES :balance', ['balance' => number_format($transaction->balance_after, 2)]) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mb-4">
                                    <flux:icon.document-text class="w-8 h-8 text-zinc-400" />
                                </div>
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('No Transactions Yet') }}</h3>
                                <p class="text-zinc-500 dark:text-zinc-400 mb-6">{{ __('This account hasn\'t had any transactions.') }}</p>
                                @if($account->status === 'active')
                                    <flux:button 
                                        href="{{ route('transactions.deposit.create') }}" 
                                        variant="primary"
                                    >
                                        {{ __('Make First Transaction') }}
                                    </flux:button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Account Holder Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Account Holder') }}</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <flux:icon.user class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->member->name }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $account->member->email }}</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('Member since :date', ['date' => $account->member->created_at->format('M Y')]) }}</p>
                                </div>
                            </div>
                            
                            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:button 
                                    href="{{ route('members.show', $account->member) }}" 
                                    variant="ghost" 
                                    size="sm"
                                    class="w-full"
                                    icon="user"
                                >
                                    {{ __('View Member Profile') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if($account->status === 'active')
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Quick Actions') }}</h3>
                            <div class="space-y-3">
                                <flux:button 
                                    href="{{ route('transactions.deposit.create') }}" 
                                    variant="primary" 
                                    icon="arrow-down-circle"
                                    class="w-full justify-start"
                                >
                                    {{ __('Make Deposit') }}
                                </flux:button>
                                <flux:button 
                                    href="{{ route('transactions.withdrawal.create') }}" 
                                    variant="outline" 
                                    icon="arrow-up-circle"
                                    class="w-full justify-start"
                                >
                                    {{ __('Make Withdrawal') }}
                                </flux:button>
                                <flux:button 
                                    href="{{ route('transactions.transfer.create') }}" 
                                    variant="ghost" 
                                    icon="arrow-right-circle"
                                    class="w-full justify-start"
                                >
                                    {{ __('Transfer Funds') }}
                                </flux:button>
                            </div>
                        </div>
                    @endif

                    <!-- Account Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Account Information') }}</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Account Number') }}</span>
                                <span class="font-mono text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded">{{ $account->account_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Account Type') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ $accountInfo['display_name'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Currency') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ $account->currency ?? 'KES' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100 capitalize">{{ $account->status }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Opened') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ $account->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Account Age') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ $account->created_at->diffForHumans() }}</span>
                            </div>
                            @if($account->status_reason)
                                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Status Reason') }}</span>
                                    <p class="text-zinc-900 dark:text-zinc-100 mt-1">{{ $account->status_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Performance Metrics') }}</h3>
                        <div class="space-y-4">
                            @php
                                $avgMonthlyDeposit = $account->transactions()->where('type', 'deposit')->avg('amount') ?? 0;
                                $lastTransaction = $account->transactions()->latest()->first();
                            @endphp
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg Monthly Deposit') }}</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($avgMonthlyDeposit, 2) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Last Activity') }}</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $lastTransaction ? $lastTransaction->created_at->diffForHumans() : __('No activity') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Interest Earned') }}</span>
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                    KES {{ number_format($account->balance * ($accountInfo['interest_rate'] / 100) / 12, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        /* Account overview header styling */
        .account-overview-card[data-color="emerald"] .account-header { background: linear-gradient(135deg, var(--emerald-500) 0%, var(--emerald-600) 100%); }
        .account-overview-card[data-color="blue"] .account-header { background: linear-gradient(135deg, var(--blue-500) 0%, var(--blue-600) 100%); }
        .account-overview-card[data-color="purple"] .account-header { background: linear-gradient(135deg, var(--purple-500) 0%, var(--purple-600) 100%); }
        .account-overview-card[data-color="red"] .account-header { background: linear-gradient(135deg, var(--red-500) 0%, var(--red-600) 100%); }
        .account-overview-card[data-color="yellow"] .account-header { background: linear-gradient(135deg, var(--yellow-500) 0%, var(--yellow-600) 100%); }
        .account-overview-card[data-color="indigo"] .account-header { background: linear-gradient(135deg, var(--indigo-500) 0%, var(--indigo-600) 100%); }
        .account-overview-card[data-color="cyan"] .account-header { background: linear-gradient(135deg, var(--cyan-500) 0%, var(--cyan-600) 100%); }
        .account-overview-card[data-color="orange"] .account-header { background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%); }
        .account-overview-card[data-color="pink"] .account-header { background: linear-gradient(135deg, var(--pink-500) 0%, var(--pink-600) 100%); }
        .account-overview-card[data-color="gray"] .account-header { background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%); }
        .account-overview-card[data-color="teal"] .account-header { background: linear-gradient(135deg, var(--teal-500) 0%, var(--teal-600) 100%); }
        .account-overview-card[data-color="amber"] .account-header { background: linear-gradient(135deg, var(--amber-500) 0%, var(--amber-600) 100%); }

        /* Account icon styling */
        .account-icon-large { background-color: rgba(255, 255, 255, 0.2); color: white; }

        /* Status indicator styling */
        .account-status-indicator[data-status="active"] { background-color: #10b981; }
        .account-status-indicator[data-status="dormant"] { background-color: #f59e0b; }
        .account-status-indicator[data-status="frozen"] { background-color: #ef4444; }
        .account-status-indicator[data-status="closed"] { background-color: #6b7280; }

        /* Stat icons */
        .account-stat-icon[data-type="deposits"] { background-color: rgb(34 197 94 / 0.1); color: #16a34a; }
        .account-stat-icon[data-type="withdrawals"] { background-color: rgb(239 68 68 / 0.1); color: #dc2626; }
        .account-stat-icon[data-type="transactions"] { background-color: rgb(59 130 246 / 0.1); color: #2563eb; }

        /* Transaction icons */
        .transaction-icon[data-type="deposit"] { background-color: rgb(34 197 94 / 0.1); color: #16a34a; }
        .transaction-icon[data-type="withdrawal"] { background-color: rgb(239 68 68 / 0.1); color: #dc2626; }
        .transaction-icon[data-type="transfer"] { background-color: rgb(59 130 246 / 0.1); color: #2563eb; }

        /* Transaction amounts */
        .transaction-amount[data-type="deposit"] { color: #16a34a; }
        .transaction-amount[data-type="withdrawal"] { color: #dc2626; }
        .transaction-amount[data-type="transfer"] { color: #2563eb; }

        /* Dark mode adjustments */
        .dark .account-stat-icon[data-type="deposits"] { background-color: rgb(34 197 94 / 0.2); color: #4ade80; }
        .dark .account-stat-icon[data-type="withdrawals"] { background-color: rgb(239 68 68 / 0.2); color: #f87171; }
        .dark .account-stat-icon[data-type="transactions"] { background-color: rgb(59 130 246 / 0.2); color: #60a5fa; }

        .dark .transaction-icon[data-type="deposit"] { background-color: rgb(34 197 94 / 0.2); color: #4ade80; }
        .dark .transaction-icon[data-type="withdrawal"] { background-color: rgb(239 68 68 / 0.2); color: #f87171; }
        .dark .transaction-icon[data-type="transfer"] { background-color: rgb(59 130 246 / 0.2); color: #60a5fa; }

        .dark .transaction-amount[data-type="deposit"] { color: #4ade80; }
        .dark .transaction-amount[data-type="withdrawal"] { color: #f87171; }
        .dark .transaction-amount[data-type="transfer"] { color: #60a5fa; }
    </style>
</x-layouts.app> 