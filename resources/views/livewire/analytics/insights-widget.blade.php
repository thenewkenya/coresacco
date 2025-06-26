<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Budget;

new class extends Component {
    public $refreshInterval = 30000; // 30 seconds
    public $insights = [];

    public function mount()
    {
        $this->loadInsights();
    }

    public function loadInsights()
    {
        $this->insights = $this->generateInsights();
    }

    private function generateInsights()
    {
        $insights = [];

        // Member Growth Insight
        $currentMonthMembers = User::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
        $lastMonthMembers = User::whereMonth('created_at', now()->subMonth()->month)
                               ->whereYear('created_at', now()->subMonth()->year)
                               ->count();
        
        if ($currentMonthMembers > $lastMonthMembers) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Member Growth Accelerating',
                'description' => "Added {$currentMonthMembers} new members this month vs {$lastMonthMembers} last month",
                'icon' => 'arrow-up'
            ];
        }

        // High-Value Transactions
        $highValueTransactions = Transaction::where('amount', '>', 100000)
                                           ->where('created_at', '>=', now()->subDays(7))
                                           ->count();
        
        if ($highValueTransactions > 0) {
            $insights[] = [
                'type' => 'info',
                'title' => 'High-Value Activity',
                'description' => "{$highValueTransactions} transactions over KSh 100,000 in the past week",
                'icon' => 'banknotes'
            ];
        }

        // Budget Adoption
        $budgetUsers = Budget::distinct('user_id')->count('user_id');
        $totalMembers = User::where('membership_status', 'active')->count();
        $adoptionRate = $totalMembers > 0 ? round(($budgetUsers / $totalMembers) * 100) : 0;

        if ($adoptionRate < 30) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Budget Tool Adoption',
                'description' => "Only {$adoptionRate}% of members are using budget planning tools",
                'icon' => 'exclamation-circle'
            ];
        }

        // Loan Portfolio Health
        $totalLoans = Loan::whereIn('status', ['disbursed', 'active'])->count();
        $defaultedLoans = Loan::where('status', 'defaulted')->count();
        $defaultRate = $totalLoans > 0 ? round(($defaultedLoans / $totalLoans) * 100, 1) : 0;

        if ($defaultRate < 5) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Excellent Portfolio Health',
                'description' => "Default rate is only {$defaultRate}%, well below industry standards",
                'icon' => 'check-circle'
            ];
        } elseif ($defaultRate > 10) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Portfolio Risk Alert',
                'description' => "Default rate at {$defaultRate}% requires attention",
                'icon' => 'exclamation-circle'
            ];
        }

        // Account Balance Distribution
        $lowBalanceAccounts = Account::where('balance', '<', 1000)
                                   ->where('status', 'active')
                                   ->count();
        $totalActiveAccounts = Account::where('status', 'active')->count();
        $lowBalancePercentage = $totalActiveAccounts > 0 ? round(($lowBalanceAccounts / $totalActiveAccounts) * 100) : 0;

        if ($lowBalancePercentage > 40) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Many Low-Balance Accounts',
                'description' => "{$lowBalancePercentage}% of accounts have balances under KSh 1,000",
                'icon' => 'information-circle'
            ];
        }

        return collect($insights)->take(5)->toArray();
    }

    public function refresh()
    {
        $this->loadInsights();
        $this->dispatch('insights-refreshed');
    }
}; ?>

<div wire:poll.{{ $refreshInterval }}ms="refresh" class="space-y-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
            {{ __('AI Insights') }}
        </h3>
        <div class="flex items-center space-x-2">
            <flux:badge color="emerald" size="sm">{{ __('Live') }}</flux:badge>
            <flux:button variant="outline" size="sm" icon="arrow-path" wire:click="refresh">
                {{ __('Refresh') }}
            </flux:button>
        </div>
    </div>

    @if(empty($insights))
        <div class="text-center py-8">
            <flux:icon.chart-bar class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Analyzing data for insights...') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($insights as $insight)
                <div class="p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 
                          @if($insight['type'] === 'positive') bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800
                          @elseif($insight['type'] === 'warning') bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800
                          @else bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 @endif">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($insight['type'] === 'positive')
                                <flux:icon.{{ $insight['icon'] }} class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            @elseif($insight['type'] === 'warning')
                                <flux:icon.{{ $insight['icon'] }} class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                            @else
                                <flux:icon.{{ $insight['icon'] }} class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium 
                                     @if($insight['type'] === 'positive') text-emerald-900 dark:text-emerald-100
                                     @elseif($insight['type'] === 'warning') text-amber-900 dark:text-amber-100
                                     @else text-blue-900 dark:text-blue-100 @endif">
                                {{ $insight['title'] }}
                            </h4>
                            <p class="text-sm 
                                    @if($insight['type'] === 'positive') text-emerald-700 dark:text-emerald-300
                                    @elseif($insight['type'] === 'warning') text-amber-700 dark:text-amber-300
                                    @else text-blue-700 dark:text-blue-300 @endif">
                                {{ $insight['description'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
        {{ __('Auto-refreshes every 30 seconds') }}
    </div>
</div> 