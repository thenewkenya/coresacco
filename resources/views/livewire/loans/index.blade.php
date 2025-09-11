<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $loanTypeFilter = '';
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
        $query = Loan::query()
            ->with(['member', 'loanType'])
            ->when($this->search, fn($q) => 
                $q->whereHas('member', fn($q) => 
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->when($this->loanTypeFilter, fn($q) => 
                $q->where('loan_type_id', $this->loanTypeFilter)
            );

        $loans = $query->latest()->paginate(15);
        $loanTypes = LoanType::where('status', 'active')->get();

        // Stats for dashboard
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', 'active')->count();
        $pendingLoans = Loan::where('status', 'pending')->count();
        $totalLoanAmount = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');
        $thisMonthDisbursements = Loan::where('status', 'disbursed')
            ->whereMonth('disbursement_date', now()->month)
            ->sum('amount');

        return [
            'loans' => $loans,
            'loanTypes' => $loanTypes,
            'totalLoans' => $totalLoans,
            'activeLoans' => $activeLoans,
            'pendingLoans' => $pendingLoans,
            'totalLoanAmount' => $totalLoanAmount,
            'thisMonthDisbursements' => $thisMonthDisbursements,
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

    public function updatedLoanTypeFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function viewLoan($loanId)
    {
        $this->selectedLoan = Loan::with(['member', 'loanType'])->findOrFail($loanId);
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
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Loan Management</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Manage member loans, applications, and repayments</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" icon="document-chart-bar" :href="route('loans.report')" wire:navigate>
                Reports
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('loans.create')" wire:navigate>
                New Loan
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
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Active</flux:subheading>
                    <flux:heading size="lg" class="!text-green-600 dark:!text-green-400">{{ number_format($activeLoans) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Pending</flux:subheading>
                    <flux:heading size="lg" class="!text-amber-600 dark:!text-amber-400">{{ number_format($pendingLoans) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Amount</flux:subheading>
                    <flux:heading size="lg" class="!text-emerald-600 dark:!text-emerald-400">KES {{ number_format($totalLoanAmount) }}</flux:heading>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
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
                        placeholder="Search members..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <flux:select wire:model.live="statusFilter" placeholder="All Status">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="loanTypeFilter" placeholder="All Types">
                    <option value="">All Types</option>
                    @foreach($loanTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Member</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Loan Type</th>
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
                                            <flux:icon.user class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $loan->member->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $loan->member->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $loan->loanType->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->loanType->description }}
                                        </div>
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
                                    <flux:dropdown align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        
                                        <flux:menu>
                                            <flux:menu.item wire:click="viewLoan({{ $loan->id }})" icon="eye">
                                                View Details
                                            </flux:menu.item>
                                            <flux:menu.item :href="route('loans.show', $loan)" icon="arrow-top-right-on-square">
                                                Open Loan
                                            </flux:menu.item>
                                            @if($loan->status === 'pending')
                                                <flux:menu.item icon="check">
                                                    Approve
                                                </flux:menu.item>
                                                <flux:menu.item icon="x-mark">
                                                    Reject
                                                </flux:menu.item>
                                            @elseif(in_array($loan->status, ['active', 'disbursed']))
                                                <flux:menu.item icon="currency-dollar">
                                                    Process Repayment
                                                </flux:menu.item>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
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
                                        {{ $loan->member->name }}
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
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Member</div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->member->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $loan->member->email }}</div>
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
                                @if($loan->status === 'pending')
                                    <flux:button variant="success" size="sm">
                                        <flux:icon.check class="w-4 h-4" />
                                    </flux:button>
                                    <flux:button variant="danger" size="sm">
                                        <flux:icon.x-mark class="w-4 h-4" />
                                    </flux:button>
                                @elseif(in_array($loan->status, ['active', 'disbursed']))
                                    <flux:button variant="primary" size="sm">
                                        <flux:icon.currency-dollar class="w-4 h-4" />
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $loans->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon.credit-card class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No loans found</h3>
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">Get started by creating a new loan or adjust your search criteria.</p>
            <flux:button variant="primary" icon="plus" :href="route('loans.create')" wire:navigate>
                New Loan
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
                <flux:subheading class="dark:text-zinc-400">Member: {{ $selectedLoan->member->name }}</flux:subheading>
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
                        <flux:heading size="base" class="dark:text-zinc-100">Member Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Name:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->member->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Email:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedLoan->member->email }}</span>
                            </div>
                        </div>
                    </div>

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
                @if($selectedLoan->status === 'pending')
                    <flux:button variant="success">Approve</flux:button>
                    <flux:button variant="danger">Reject</flux:button>
                @elseif(in_array($selectedLoan->status, ['active', 'disbursed']))
                    <flux:button variant="primary">Process Repayment</flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
@endif

