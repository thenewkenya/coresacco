<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $viewMode = 'list'; // 'list' or 'grid'
    public $showViewModal = false;
    public $selectedLoan = null;

    public $statusOptions = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'disbursed' => 'Disbursed',
        'active' => 'Active',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ];

    public function with()
    {
        $user = auth()->user();
        
        $query = $user->loans()
            ->with(['loanType', 'transactions'])
            ->when($this->search, fn($q) => 
                $q->where('purpose', 'like', '%' . $this->search . '%')
                  ->orWhereHas('loanType', fn($q) => 
                      $q->where('name', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            );

        $loans = $query->latest()->get();

        // Calculate summary statistics
        $totalBorrowed = $loans->sum('amount');
        $activeLoan = $loans->where('status', 'active')->first();
        $totalRepaid = $user->transactions()
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        
        $canApplyForLoan = !$activeLoan;
        $totalLoans = $loans->count();
        $activeLoans = $loans->where('status', 'active')->count();
        $pendingLoans = $loans->where('status', 'pending')->count();

        return [
            'loans' => $loans,
            'totalBorrowed' => $totalBorrowed,
            'activeLoan' => $activeLoan,
            'totalRepaid' => $totalRepaid,
            'canApplyForLoan' => $canApplyForLoan,
            'totalLoans' => $totalLoans,
            'activeLoans' => $activeLoans,
            'pendingLoans' => $pendingLoans,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function viewLoan($loanId)
    {
        $this->selectedLoan = auth()->user()->loans()->with(['loanType', 'transactions'])->findOrFail($loanId);
        $this->showViewModal = true;
    }

    public function closeModals()
    {
        $this->showViewModal = false;
        $this->selectedLoan = null;
    }
}

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">My Loans</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Track your loan applications and repayment progress</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="primary" icon="plus" :href="route('loans.apply')" wire:navigate>
                Apply for Loan
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Loans</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalLoans) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Borrowed</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">KES {{ number_format($totalBorrowed) }}</flux:heading>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Repaid</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">KES {{ number_format($totalRepaid) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Can Apply</flux:subheading>
                    <flux:heading size="lg" class="!text-{{ $canApplyForLoan ? 'emerald' : 'red' }}-600 dark:!text-{{ $canApplyForLoan ? 'emerald' : 'red' }}-400">
                        {{ $canApplyForLoan ? 'Yes' : 'No' }}
                    </flux:heading>
                </div>
                <div class="p-3 bg-{{ $canApplyForLoan ? 'emerald' : 'red' }}-100 dark:bg-{{ $canApplyForLoan ? 'emerald' : 'red' }}-900/20 rounded-lg">
                    <flux:icon.{{ $canApplyForLoan ? 'check' : 'x-mark' }} class="w-6 h-6 text-{{ $canApplyForLoan ? 'emerald' : 'red' }}-600 dark:text-{{ $canApplyForLoan ? 'emerald' : 'red' }}-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live="search" 
                        placeholder="Search loans..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <flux:select wire:model.live="statusFilter" placeholder="All Status">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- View Toggle -->
            <div class="flex items-center gap-3">
                <!-- View Mode Toggle -->
                <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-600 p-1">
                    <flux:button 
                        variant="{{ $viewMode === 'list' ? 'primary' : 'ghost' }}" 
                        size="sm"
                        wire:click="setViewMode('list')"
                        icon="list-bullet"
                    />
                    <flux:button 
                        variant="{{ $viewMode === 'grid' ? 'primary' : 'ghost' }}" 
                        size="sm"
                        wire:click="setViewMode('grid')"
                        icon="squares-2x2"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Display -->
    @if($loans->count())
        @if($viewMode === 'list')
            <!-- List View -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Loan Type</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Purpose</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Term</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Interest Rate</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Applied</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        @foreach($loans as $loan)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                            <flux:icon.credit-card class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $loan->loanType->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                #{{ $loan->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->purpose ?? 'No purpose specified' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($loan->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->term_period }} months
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->interest_rate }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="{{ 
                                        $loan->status === 'active' ? 'success' : 
                                        ($loan->status === 'pending' ? 'warning' : 
                                        ($loan->status === 'approved' ? 'info' : 
                                        ($loan->status === 'disbursed' ? 'success' : 
                                        ($loan->status === 'completed' ? 'secondary' : 
                                        ($loan->status === 'rejected' ? 'danger' : 'outline'))))) 
                                    }}">
                                        {{ ucfirst($loan->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" wire:click="viewLoan({{ $loan->id }})">
                                            <flux:icon.eye class="w-4 h-4" />
                                        </flux:button>
                                        <flux:button variant="outline" size="sm" :href="route('loans.show', $loan)">
                                            <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                        </flux:button>
                                        @if($loan->status === 'active')
                                            <flux:button variant="primary" size="sm" :href="route('loans.repayment', $loan)">
                                                <flux:icon.currency-dollar class="w-4 h-4" />
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($loans as $loan)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                    <flux:icon.credit-card class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->loanType->name }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        #{{ $loan->id }}
                                    </div>
                                </div>
                            </div>
                            <flux:badge variant="{{ 
                                $loan->status === 'active' ? 'success' : 
                                ($loan->status === 'pending' ? 'warning' : 
                                ($loan->status === 'approved' ? 'info' : 
                                ($loan->status === 'disbursed' ? 'success' : 
                                ($loan->status === 'completed' ? 'secondary' : 
                                ($loan->status === 'rejected' ? 'danger' : 'outline'))))) 
                            }}">
                                {{ ucfirst($loan->status) }}
                            </flux:badge>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Purpose</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $loan->purpose ?? 'No purpose specified' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Loan Amount</div>
                                <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($loan->amount, 2) }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Term</div>
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->term_period }} months
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Interest Rate</div>
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->interest_rate }}%
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Applied</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $loan->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button variant="ghost" size="sm" wire:click="viewLoan({{ $loan->id }})">
                                <flux:icon.eye class="w-4 h-4 mr-2" />
                                View
                            </flux:button>
                            <div class="flex space-x-2">
                                <flux:button variant="outline" size="sm" :href="route('loans.show', $loan)">
                                    <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                </flux:button>
                                @if($loan->status === 'active')
                                    <flux:button variant="primary" size="sm" :href="route('loans.repayment', $loan)">
                                        <flux:icon.currency-dollar class="w-4 h-4" />
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon.credit-card class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No loans found</h3>
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">You haven't applied for any loans yet. Get started by applying for your first loan.</p>
            <flux:button variant="primary" icon="plus" :href="route('loans.apply')" wire:navigate>
                Apply for Loan
            </flux:button>
        </div>
    @endif
</div>

<!-- Loan View Modal -->
@if($selectedLoan)
    <flux:modal wire:model="showViewModal" class="md:w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Loan Details</flux:heading>
                <flux:subheading class="dark:text-zinc-400">{{ $selectedLoan->loanType->name }} #{{ $selectedLoan->id }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Loan Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Loan Type:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->loanType->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Amount:</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($selectedLoan->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Purpose:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->purpose ?? 'No purpose specified' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Term:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->term_period }} months</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Interest Rate:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->interest_rate }}% p.a.</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status:</span>
                                <flux:badge variant="{{ 
                                    $selectedLoan->status === 'active' ? 'success' : 
                                    ($selectedLoan->status === 'pending' ? 'warning' : 
                                    ($selectedLoan->status === 'approved' ? 'info' : 
                                    ($selectedLoan->status === 'disbursed' ? 'success' : 
                                    ($selectedLoan->status === 'completed' ? 'secondary' : 
                                    ($selectedLoan->status === 'rejected' ? 'danger' : 'outline'))))) 
                                }}">
                                    {{ ucfirst($selectedLoan->status) }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Timeline</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Applied:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @if($selectedLoan->disbursement_date)
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Disbursed:</span>
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->disbursement_date->format('M d, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="closeModals">Close</flux:button>
                <flux:button variant="outline" :href="route('loans.show', $selectedLoan)">Open Loan</flux:button>
                @if($selectedLoan->status === 'active')
                    <flux:button variant="primary" :href="route('loans.repayment', $selectedLoan)">Make Payment</flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
@endif

