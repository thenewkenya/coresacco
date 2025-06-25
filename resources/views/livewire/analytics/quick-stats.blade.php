<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Loan;

new class extends Component {
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_members' => User::count(),
            'active_members' => User::where('membership_status', 'active')->count(),
            'total_assets' => Account::where('status', 'active')->sum('balance'),
            'monthly_transactions' => Transaction::whereMonth('created_at', now()->month)
                                               ->whereYear('created_at', now()->year)
                                               ->where('status', 'completed')
                                               ->count(),
            'active_loans' => Loan::whereIn('status', ['disbursed', 'active'])->count(),
            'loan_value' => Loan::whereIn('status', ['disbursed', 'active'])->sum('amount'),
        ];
    }

    public function refresh()
    {
        $this->loadStats();
    }
}; ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Total Members -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</p>
                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_members']) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Assets -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center">
            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg mr-3">
                <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Total Assets') }}</p>
                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                    @if($stats['total_assets'] >= 1000000)
                        KSh {{ number_format($stats['total_assets'] / 1000000, 1) }}M
                    @else
                        KSh {{ number_format($stats['total_assets']) }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Monthly Transactions -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3">
                <flux:icon.arrows-right-left class="w-5 h-5 text-purple-600 dark:text-purple-400" />
            </div>
            <div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('This Month') }}</p>
                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['monthly_transactions']) }}</p>
            </div>
        </div>
    </div>

    <!-- Active Loans -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center">
            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg mr-3">
                <flux:icon.credit-card class="w-5 h-5 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Active Loans') }}</p>
                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['active_loans']) }}</p>
            </div>
        </div>
    </div>

    <!-- Loan Portfolio Value -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg mr-3">
                <flux:icon.chart-pie class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Loan Portfolio') }}</p>
                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                    @if($stats['loan_value'] >= 1000000)
                        KSh {{ number_format($stats['loan_value'] / 1000000, 1) }}M
                    @else
                        KSh {{ number_format($stats['loan_value']) }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- View Full Analytics -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ __('Full Analytics') }}</p>
                <flux:button variant="primary" size="sm" :href="route('analytics.index')" wire:navigate class="mt-2">
                    {{ __('View Dashboard') }}
                </flux:button>
            </div>
            <flux:icon.chart-bar class="w-8 h-8 text-blue-600 dark:text-blue-400" />
        </div>
    </div>
</div> 