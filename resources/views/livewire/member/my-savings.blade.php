<?php

use Livewire\Volt\Component;
use App\Models\Account;
use App\Models\Transaction;

new class extends Component
{
    public $accounts;
    public $totalBalance;
    public $recentTransactions;

    public function mount()
    {
        $this->accounts = auth()->user()->hasMany(Account::class, 'member_id')
            ->where('account_type', Account::TYPE_SAVINGS)
            ->get();
            
        $this->totalBalance = $this->accounts->sum('balance');
        
        $this->recentTransactions = Transaction::where('member_id', auth()->id())
            ->whereHas('account', function($query) {
                $query->where('account_type', Account::TYPE_SAVINGS);
            })
            ->with('account')
            ->latest()
            ->take(10)
            ->get();
    }
}; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Savings</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your savings accounts and track your progress</p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('transactions.deposit.create')">
            New Deposit
        </flux:button>
    </div>

    <!-- Account Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Savings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Savings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($totalBalance, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.currency-dollar class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                    <flux:icon.arrow-trending-up class="h-4 w-4 mr-1" />
                    <span>{{ $accounts->count() }} Active Account{{ $accounts->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($recentTransactions->where('type', 'deposit')->where('created_at', '>=', now()->startOfMonth())->sum('amount'), 2) }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.calendar class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $recentTransactions->where('type', 'deposit')->where('created_at', '>=', now()->startOfMonth())->count() }} deposits made</span>
                </div>
            </div>
        </div>

        <!-- Average Monthly -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Monthly</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($recentTransactions->where('type', 'deposit')->avg('amount') ?? 0, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <flux:icon.chart-bar class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>Based on last 12 months</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings Accounts -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Savings Accounts</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($accounts as $account)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                                <flux:icon.banknotes class="h-5 w-5 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $account->account_number }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Savings Account</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($account->balance, 2) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if($account->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 @endif">
                                    {{ ucfirst($account->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <flux:icon.banknotes class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Savings Accounts</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have any savings accounts yet.</p>
                        <flux:button variant="primary">Open Savings Account</flux:button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                <flux:button variant="ghost" size="sm">View All</flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $transaction->created_at->format('M j, Y') }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $transaction->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->description }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Ref: {{ $transaction->reference_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $transaction->account->account_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span class="@if($transaction->type === 'deposit') text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                                    @if($transaction->type === 'deposit')+@else-@endif
                                    KES {{ number_format($transaction->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon.document-text class="h-8 w-8 mx-auto mb-2" />
                                    <p>No transactions found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 