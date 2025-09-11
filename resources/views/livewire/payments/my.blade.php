<?php

use App\Models\Transaction;
use App\Models\Loan;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $viewMode = 'list'; // 'list' or 'grid'
    public $showViewModal = false;
    public $selectedTransaction = null;

    public $statusOptions = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ];

    public $typeOptions = [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'loan_repayment' => 'Loan Repayment',
        'loan_disbursement' => 'Loan Disbursement',
    ];

    public function with()
    {
        $user = auth()->user();
        
        $query = Transaction::where('member_id', $user->id)
            ->with(['account', 'loan'])
            ->when($this->search, fn($q) => 
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $this->search . '%')
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->when($this->typeFilter, fn($q) => 
                $q->where('type', $this->typeFilter)
            );

        $transactions = $query->latest()->paginate(15);

        // Get payment summary
        $totalPaid = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereIn('type', [Transaction::TYPE_DEPOSIT, Transaction::TYPE_LOAN_REPAYMENT])
            ->sum('amount');

        $thisMonthPaid = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->whereIn('type', [Transaction::TYPE_DEPOSIT, Transaction::TYPE_LOAN_REPAYMENT])
            ->sum('amount');

        // Get pending payments
        $pendingPayments = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_PENDING)
            ->count();

        // Get upcoming loan payments
        $upcomingPayments = [];
        $activeLoans = $user->loans()->where('status', Loan::STATUS_ACTIVE)->get();
        foreach ($activeLoans as $loan) {
            $monthlyPayment = $loan->calculateMonthlyPayment();
            $nextDueDate = now()->addMonth()->startOfMonth();
            
            $upcomingPayments[] = [
                'loan' => $loan,
                'amount' => $monthlyPayment,
                'due_date' => $nextDueDate,
                'type' => 'loan_repayment'
            ];
        }

        $totalTransactions = Transaction::where('member_id', $user->id)->count();
        $completedTransactions = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->count();

        return [
            'transactions' => $transactions,
            'totalPaid' => $totalPaid,
            'thisMonthPaid' => $thisMonthPaid,
            'pendingPayments' => $pendingPayments,
            'upcomingPayments' => $upcomingPayments,
            'totalTransactions' => $totalTransactions,
            'completedTransactions' => $completedTransactions,
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

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function viewTransaction($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['account', 'loan'])->findOrFail($transactionId);
        $this->showViewModal = true;
    }

    public function closeModals()
    {
        $this->showViewModal = false;
        $this->selectedTransaction = null;
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">My Payments</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Track your payment history and manage upcoming payments</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" icon="document-arrow-down">
                Download Statement
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('payments.create')" wire:navigate>
                Make Payment
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Paid</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">KES {{ number_format($totalPaid) }}</flux:heading>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">This Month</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">KES {{ number_format($thisMonthPaid) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Pending</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($pendingPayments) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Transactions</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalTransactions) }}</flux:heading>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
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
                        placeholder="Search payments..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <flux:select wire:model.live="statusFilter" placeholder="All Status">
                    <option value="">All Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="typeFilter" placeholder="All Types">
                    <option value="">All Types</option>
                    @foreach($typeOptions as $value => $label)
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

    <!-- Upcoming Payments -->
    @if(count($upcomingPayments) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Upcoming Payments</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Scheduled loan repayments</flux:subheading>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($upcomingPayments as $payment)
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-medium text-amber-900 dark:text-amber-100">
                                {{ $payment['loan']->loanType->name }}
                            </div>
                            <div class="text-sm text-amber-600 dark:text-amber-400">
                                Due {{ $payment['due_date']->format('M d') }}
                            </div>
                        </div>
                        <div class="text-lg font-bold text-amber-900 dark:text-amber-100">
                            KES {{ number_format($payment['amount']) }}
                        </div>
                        <div class="text-sm text-amber-600 dark:text-amber-400">
                            Monthly payment
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Payments Display -->
    @if($transactions->count())
        @if($viewMode === 'list')
            <!-- List View -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Payment</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Method</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg bg-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-100 dark:bg-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-900/30">
                                            <flux:icon.{{ $transaction->type === 'deposit' ? 'arrow-down' : ($transaction->type === 'withdrawal' ? 'arrow-up' : 'credit-card') }} class="w-4 h-4 text-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-600 dark:text-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-400" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $transaction->description ?? 'No description' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($transaction->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ ucfirst($transaction->metadata['payment_method'] ?? 'Unknown') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $transaction->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="{{ 
                                        $transaction->status === 'completed' ? 'success' : 
                                        ($transaction->status === 'pending' ? 'warning' : 
                                        ($transaction->status === 'failed' ? 'danger' : 'outline')) 
                                    }}">
                                        {{ ucfirst($transaction->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" wire:click="viewTransaction({{ $transaction->id }})">
                                            <flux:icon.eye class="w-4 h-4" />
                                        </flux:button>
                                        <flux:button variant="outline" size="sm" :href="route('payments.show', $transaction)">
                                            <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                        </flux:button>
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
                @foreach($transactions as $transaction)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-lg bg-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-100 dark:bg-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-900/30">
                                    <flux:icon.{{ $transaction->type === 'deposit' ? 'arrow-down' : ($transaction->type === 'withdrawal' ? 'arrow-up' : 'credit-card') }} class="w-5 h-5 text-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-600 dark:text-{{ $transaction->type === 'deposit' ? 'emerald' : ($transaction->type === 'withdrawal' ? 'red' : 'blue') }}-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $transaction->reference_number ?? 'No reference' }}
                                    </div>
                                </div>
                            </div>
                            <flux:badge variant="{{ 
                                $transaction->status === 'completed' ? 'success' : 
                                ($transaction->status === 'pending' ? 'warning' : 
                                ($transaction->status === 'failed' ? 'danger' : 'outline')) 
                            }}">
                                {{ ucfirst($transaction->status) }}
                            </flux:badge>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Description</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $transaction->description ?? 'No description' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Amount</div>
                                <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($transaction->amount, 2) }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Method</div>
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ ucfirst($transaction->metadata['payment_method'] ?? 'Unknown') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Date</div>
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button variant="ghost" size="sm" wire:click="viewTransaction({{ $transaction->id }})">
                                <flux:icon.eye class="w-4 h-4 mr-2" />
                                View
                            </flux:button>
                            <div class="flex space-x-2">
                                <flux:button variant="outline" size="sm" :href="route('payments.show', $transaction)">
                                    <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon.credit-card class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No payments found</h3>
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">You haven't made any payments yet. Get started by making your first payment.</p>
            <flux:button variant="primary" icon="plus" :href="route('payments.create')" wire:navigate>
                Make Payment
            </flux:button>
        </div>
    @endif
</div>

<!-- Transaction View Modal -->
@if($selectedTransaction)
    <flux:modal wire:model="showViewModal" class="md:w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Payment Details</flux:heading>
                <flux:subheading class="dark:text-zinc-400">{{ ucfirst(str_replace('_', ' ', $selectedTransaction->type)) }} - {{ $selectedTransaction->reference_number ?? 'No reference' }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Payment Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Type:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ ucfirst(str_replace('_', ' ', $selectedTransaction->type)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Amount:</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($selectedTransaction->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Method:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ ucfirst($selectedTransaction->metadata['payment_method'] ?? 'Unknown') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status:</span>
                                <flux:badge variant="{{ 
                                    $selectedTransaction->status === 'completed' ? 'success' : 
                                    ($selectedTransaction->status === 'pending' ? 'warning' : 
                                    ($selectedTransaction->status === 'failed' ? 'danger' : 'outline')) 
                                }}">
                                    {{ ucfirst($selectedTransaction->status) }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Details</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Reference:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->reference_number ?? 'No reference' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Description:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->description ?? 'No description' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Date:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="closeModals">Close</flux:button>
                <flux:button variant="outline" :href="route('payments.show', $selectedTransaction)">Open Payment</flux:button>
            </div>
        </div>
    </flux:modal>
@endif

