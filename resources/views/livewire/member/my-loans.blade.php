<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Loan;
use App\Models\Transaction;

// State
state([
    'filter' => 'all',
    'search' => '',
]);

// Computed properties
$loans = computed(function () {
    $query = auth()->user()->loans()
        ->with(['loanType'])
        ->when($this->search, function ($q) {
            $q->where(function ($query) {
                $query->where('purpose', 'like', "%{$this->search}%")
                    ->orWhereHas('loanType', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            });
        })
        ->when($this->filter !== 'all', function ($q) {
            $q->where('status', $this->filter);
        })
        ->latest();
    
    return $query->get();
});

$summary = computed(function () {
    $userLoans = auth()->user()->loans;
    
    return [
        'total_loans' => $userLoans->count(),
        'active_loans' => $userLoans->where('status', 'active')->count(),
        'pending_loans' => $userLoans->where('status', 'pending')->count(),
        'total_borrowed' => $userLoans->where('status', 'active')->sum('amount'),
        'total_remaining' => $userLoans->where('status', 'active')->sum('remaining_balance'),
        'monthly_payment' => $userLoans->where('status', 'active')->sum('monthly_payment'),
    ];
});

// Methods
$getStatusColor = function ($status) {
    return match($status) {
        'pending' => 'text-yellow-600 bg-yellow-50 border-yellow-200',
        'active' => 'text-green-600 bg-green-50 border-green-200',
        'completed' => 'text-zinc-600 bg-zinc-50 border-zinc-200',
        'rejected' => 'text-red-600 bg-red-50 border-red-200',
        'defaulted' => 'text-red-800 bg-red-100 border-red-300',
        default => 'text-zinc-600 bg-zinc-50 border-zinc-200',
    };
};

$formatCurrency = function ($amount) {
    return 'KES ' . number_format($amount, 2);
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                    My Loans
                </h1>
                <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                    Track your loan applications and manage repayments
                </p>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-3">
                <flux:button variant="primary" size="sm" icon="plus" href="{{ route('loans.create') }}" wire:navigate class="flex-1 sm:flex-none">
                    <span class="hidden sm:inline">Apply for Loan</span>
                    <span class="sm:hidden">Apply</span>
                </flux:button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6">
            <!-- Total Loans -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-2">Total Loans</div>
                <div class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mb-3">
                    {{ $this->summary['total_loans'] }}
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="text-emerald-600">{{ $this->summary['active_loans'] }} Active</span>
                    <span class="text-yellow-600">{{ $this->summary['pending_loans'] }} Pending</span>
                </div>
            </div>

            <!-- Total Borrowed -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-2">Total Borrowed</div>
                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white mb-3">
                    {{ $this->formatCurrency($this->summary['total_borrowed']) }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    Remaining: {{ $this->formatCurrency($this->summary['total_remaining']) }}
                </div>
            </div>

            <!-- Monthly Payment -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-2">Monthly Payment</div>
                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white mb-3">
                    {{ $this->formatCurrency($this->summary['monthly_payment']) }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total obligation</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-4">Search & Filter</h3>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search loans by purpose or type..."
                        type="search"
                        class="w-full"
                    />
                </div>
                
                <div class="flex gap-2">
                    <flux:select wire:model.live="filter" class="min-w-32">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                        <option value="defaulted">Defaulted</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Loans List -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-6">Your Loans</h3>
            
            <div class="space-y-4">
                @forelse($this->loans as $loan)
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $loan->loanType->name }}</div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full border {{ $this->getStatusColor($loan->status) }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="text-zinc-600 dark:text-zinc-400 mb-3">{{ $loan->purpose }}</p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-zinc-500 dark:text-zinc-400">Amount</p>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $this->formatCurrency($loan->amount) }}</p>
                                    </div>
                                    
                                    @if($loan->status === 'active')
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Remaining</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $this->formatCurrency($loan->remaining_balance) }}</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Monthly Payment</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $this->formatCurrency($loan->monthly_payment) }}</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Interest Rate</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                                        </div>
                                    @else
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Interest Rate</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Term</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->term_months }} months</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-zinc-500 dark:text-zinc-400">Applied</p>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->created_at->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($loan->status === 'active' && $loan->remaining_balance > 0)
                                    <!-- Simple Progress Bar -->
                                    <div class="mt-4">
                                        @php
                                            $progress = (($loan->amount - $loan->remaining_balance) / $loan->amount) * 100;
                                        @endphp
                                        <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400 mb-1">
                                            <span>Repayment Progress</span>
                                            <span>{{ number_format($progress, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-2">
                                <flux:button 
                                    variant="outline" 
                                    size="sm"
                                    :href="route('loans.show', $loan)"
                                    wire:navigate
                                >
                                    View Details
                                </flux:button>
                                
                                @if($loan->status === 'active')
                                    <flux:button 
                                        variant="primary" 
                                        size="sm"
                                        :href="route('payments.create', ['loan_id' => $loan->id])"
                                        wire:navigate
                                    >
                                        Make Payment
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <flux:icon.banknotes class="w-8 h-8 text-zinc-400" />
                        </div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">No Loans Found</h3>
                        @if($this->search || $this->filter !== 'all')
                            <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                                No loans match your current filters. Try adjusting your search or filter criteria.
                            </p>
                        @else
                            <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                                You haven't applied for any loans yet.
                            </p>
                            <flux:button variant="primary" href="{{ route('loans.create') }}" wire:navigate>
                                Apply for Your First Loan
                            </flux:button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div> 