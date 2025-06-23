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
$resetFilters = function () {
    $this->filter = 'all';
    $this->search = '';
};

$getStatusColor = function ($status) {
    return match($status) {
        'pending' => 'text-yellow-600 bg-yellow-50 border-yellow-200',
        'active' => 'text-green-600 bg-green-50 border-green-200',
        'completed' => 'text-blue-600 bg-blue-50 border-blue-200',
        'rejected' => 'text-red-600 bg-red-50 border-red-200',
        'defaulted' => 'text-red-800 bg-red-100 border-red-300',
        default => 'text-gray-600 bg-gray-50 border-gray-200',
    };
};

$formatCurrency = function ($amount) {
    return 'KES ' . number_format($amount, 2);
};

?>

<div>
    <flux:header>
        <flux:heading size="xl">My Loans</flux:heading>
        <flux:subheading>Manage your loan applications and repayments</flux:subheading>
        
        <x-slot:actions>
            <flux:button variant="primary" :href="route('loans.create')" wire:navigate>
                Apply for Loan
            </flux:button>
        </x-slot:actions>
    </flux:header>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Loans</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->summary['total_loans'] }}</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/50 rounded-lg">
                    <flux:icon.document-text class="w-6 h-6 text-blue-600" />
                </div>
            </div>
            <div class="mt-4 flex space-x-4 text-sm">
                <span class="text-green-600">{{ $this->summary['active_loans'] }} Active</span>
                <span class="text-yellow-600">{{ $this->summary['pending_loans'] }} Pending</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Borrowed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $formatCurrency($this->summary['total_borrowed']) }}</p>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/50 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-green-600" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Remaining: {{ $formatCurrency($this->summary['total_remaining']) }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Payment</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $formatCurrency($this->summary['monthly_payment']) }}</p>
                </div>
                <div class="p-3 bg-purple-50 dark:bg-purple-900/50 rounded-lg">
                    <flux:icon.calendar class="w-6 h-6 text-purple-600" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Total monthly obligation</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
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
                
                <flux:button variant="ghost" wire:click="resetFilters">
                    Reset
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Loans List -->
    <div class="space-y-4">
        @forelse($this->loans as $loan)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $loan->loanType->name }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full border {{ $getStatusColor($loan->status) }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $loan->purpose }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Amount</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $formatCurrency($loan->amount) }}</p>
                                </div>
                                
                                @if($loan->status === 'active')
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Remaining</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $formatCurrency($loan->remaining_balance) }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Monthly Payment</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $formatCurrency($loan->monthly_payment) }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Interest Rate</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                                    </div>
                                @else
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Interest Rate</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Term</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $loan->term_months }} months</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Applied</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $loan->created_at->format('M d, Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-2">
                            <flux:button 
                                variant="ghost" 
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

                    @if($loan->status === 'active' && $loan->remaining_balance > 0)
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            @php
                                $progress = (($loan->amount - $loan->remaining_balance) / $loan->amount) * 100;
                            @endphp
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Repayment Progress</span>
                                <span>{{ number_format($progress, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                <flux:icon.document-text class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Loans Found</h3>
                @if($this->search || $this->filter !== 'all')
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        No loans match your current filters. Try adjusting your search or filter criteria.
                    </p>
                    <flux:button variant="ghost" wire:click="resetFilters">
                        Clear Filters
                    </flux:button>
                @else
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        You haven't applied for any loans yet.
                    </p>
                    <flux:button variant="primary" :href="route('loans.create')" wire:navigate>
                        Apply for Your First Loan
                    </flux:button>
                @endif
            </div>
        @endforelse
    </div>
</div> 