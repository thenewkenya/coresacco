<?php

use App\Models\Transaction;
use App\Models\User;
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
    public $isLoading = false;

    public $statusOptions = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ];

    public $typeOptions = [
        'deposit' => 'Deposits',
        'withdrawal' => 'Withdrawals',
        'transfer' => 'Transfers',
    ];

    public function with()
    {
        $query = Transaction::query()
            ->with(['member', 'account'])
            ->when($this->search, fn($q) => 
                $q->where('reference_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('member', fn($q) => 
                      $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->when($this->typeFilter, fn($q) => 
                $q->where('type', $this->typeFilter)
            );

        $transactions = $query->latest()->paginate(15);

        // Stats for dashboard
        $totalTransactions = Transaction::count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $completedTransactions = Transaction::where('status', 'completed')->count();
        $failedTransactions = Transaction::where('status', 'failed')->count();
        $todayVolume = Transaction::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        return [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
            'pendingTransactions' => $pendingTransactions,
            'completedTransactions' => $completedTransactions,
            'failedTransactions' => $failedTransactions,
            'todayVolume' => $todayVolume,
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
        $this->isLoading = true;
        
        $transaction = Transaction::findOrFail($transactionId);
        
        // Check permissions
        $user = auth()->user();
        if ($user->hasRole('member') && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction details.');
        }
        
        // Use instant navigation instead of redirect
        if ($transaction->status === 'completed') {
            // For completed transactions, show the receipt
            $this->js('window.location.href = "' . route('transactions.receipt', $transaction) . '"');
        } else {
            // For pending/failed transactions, show the transaction details page
            $this->js('window.location.href = "' . route('transactions.show', $transaction) . '"');
        }
    }

    public function closeModals()
    {
        $this->showViewModal = false;
        $this->selectedTransaction = null;
    }
}

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Transaction Management</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Monitor and manage all member transactions</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" icon="arrow-down" :href="route('transactions.deposit.create')" class="text-emerald-600 dark:text-emerald-400">
                Deposit
            </flux:button>
            <flux:button variant="outline" icon="arrow-up" :href="route('transactions.withdrawal.create')" class="text-red-600 dark:text-red-400">
                Withdraw
            </flux:button>
            <flux:button variant="primary" icon="arrows-right-left" :href="route('transactions.transfer.create')">
                Transfer
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalTransactions) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Pending</flux:subheading>
                    <flux:heading size="lg" class="!text-amber-600 dark:!text-amber-400">{{ number_format($pendingTransactions) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Completed</flux:subheading>
                    <flux:heading size="lg" class="!text-green-600 dark:!text-green-400">{{ number_format($completedTransactions) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Today's Volume</flux:subheading>
                    <flux:heading size="lg" class="!text-emerald-600 dark:!text-emerald-400">KES {{ number_format($todayVolume) }}</flux:heading>
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
                        placeholder="Search transactions..." 
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

    <!-- Transactions Display -->
    @if($transactions->count())
        @if($viewMode === 'list')
            <!-- List View -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px]">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Transaction</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg {{ 
                                            $transaction->type === 'deposit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 
                                            ($transaction->type === 'withdrawal' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30') 
                                        }}">
                                            @if($transaction->type === 'deposit')
                                                <flux:icon.arrow-down class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                            @elseif($transaction->type === 'withdrawal')
                                                <flux:icon.arrow-up class="w-4 h-4 text-red-600 dark:text-red-400" />
                                            @else
                                                <flux:icon.arrows-right-left class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $transaction->reference_number }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $transaction->account->account_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm {{ 
                                        $transaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                        ($transaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                    }}">
                                        {{ ucfirst($transaction->type) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold {{ 
                                        $transaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                        ($transaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                    }}">
                                        {{ $transaction->type === 'deposit' ? '+' : ($transaction->type === 'withdrawal' ? '-' : '') }}KES {{ number_format($transaction->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="{{ 
                                        $transaction->status === 'completed' ? 'success' : 
                                        ($transaction->status === 'pending' ? 'warning' : 
                                        ($transaction->status === 'failed' ? 'danger' : 'secondary')) 
                                    }}">
                                        {{ ucfirst($transaction->status) }}
                                    </flux:badge>
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
                                    <flux:dropdown align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        
                                        <flux:menu>
                                            <flux:menu.item wire:click="viewTransaction({{ $transaction->id }})" icon="eye" :disabled="$isLoading">
                                                <span wire:loading.remove wire:target="viewTransaction({{ $transaction->id }})">View Details</span>
                                                <span wire:loading wire:target="viewTransaction({{ $transaction->id }})" class="flex items-center">
                                                    <flux:icon.arrow-path class="w-4 h-4 mr-2 animate-spin" />
                                                    Loading...
                                                </span>
                                            </flux:menu.item>
                                            @if($transaction->status === 'pending' && auth()->user()->can('approve', $transaction))
                                                <flux:menu.item icon="check">
                                                    Approve
                                                </flux:menu.item>
                                                <flux:menu.item icon="x-mark">
                                                    Reject
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
            </div>
        @else
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($transactions as $transaction)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-lg {{ 
                                    $transaction->type === 'deposit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 
                                    ($transaction->type === 'withdrawal' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30') 
                                }}">
                                    @if($transaction->type === 'deposit')
                                        <flux:icon.arrow-down class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($transaction->type === 'withdrawal')
                                        <flux:icon.arrow-up class="w-5 h-5 text-red-600 dark:text-red-400" />
                                    @else
                                        <flux:icon.arrows-right-left class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $transaction->reference_number }}
                                    </div>
                                    <div class="text-sm {{ 
                                        $transaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                        ($transaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                    }}">
                                        {{ ucfirst($transaction->type) }}
                                    </div>
                                </div>
                            </div>
                            <flux:badge variant="{{ 
                                $transaction->status === 'completed' ? 'success' : 
                                ($transaction->status === 'pending' ? 'warning' : 
                                ($transaction->status === 'failed' ? 'danger' : 'secondary')) 
                            }}">
                                {{ ucfirst($transaction->status) }}
                            </flux:badge>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Amount</div>
                                <div class="text-xl font-bold {{ 
                                    $transaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                    ($transaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                }}">
                                    {{ $transaction->type === 'deposit' ? '+' : ($transaction->type === 'withdrawal' ? '-' : '') }}KES {{ number_format($transaction->amount, 2) }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Date</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $transaction->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button variant="ghost" size="sm" wire:click="viewTransaction({{ $transaction->id }})" :disabled="$isLoading">
                                <span wire:loading.remove wire:target="viewTransaction({{ $transaction->id }})">
                                    <flux:icon.eye class="w-4 h-4 mr-2" />
                                    View
                                </span>
                                <span wire:loading wire:target="viewTransaction({{ $transaction->id }})" class="flex items-center">
                                    <flux:icon.arrow-path class="w-4 h-4 mr-2 animate-spin" />
                                    Loading...
                                </span>
                            </flux:button>
                            @if($transaction->status === 'pending')
                                <div class="flex space-x-2">
                                    <flux:button variant="primary" size="sm">
                                        <flux:icon.check class="w-4 h-4" />
                                    </flux:button>
                                    <flux:button variant="danger" size="sm">
                                        <flux:icon.x-mark class="w-4 h-4" />
                                    </flux:button>
                                </div>
                            @endif
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
                <flux:icon.document-text class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No transactions found</h3>
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">Get started by creating a new transaction or adjust your search criteria.</p>
            <flux:button variant="primary" icon="arrow-down" :href="route('transactions.deposit.create')">
                Make a Deposit
            </flux:button>
        </div>
    @endif
</div>

<!-- Transaction View Modal -->
@if($selectedTransaction)
    <flux:modal wire:model="showViewModal" class="md:w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Transaction Details</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Reference: {{ $selectedTransaction->reference_number }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Transaction Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Type:</span>
                                <flux:badge variant="{{ 
                                    $selectedTransaction->type === 'deposit' ? 'success' : 
                                    ($selectedTransaction->type === 'withdrawal' ? 'danger' : 'primary') 
                                }}">
                                    {{ ucfirst($selectedTransaction->type) }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Amount:</span>
                                <span class="font-semibold {{ 
                                    $selectedTransaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 
                                    ($selectedTransaction->type === 'withdrawal' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') 
                                }}">
                                    {{ $selectedTransaction->type === 'deposit' ? '+' : ($selectedTransaction->type === 'withdrawal' ? '-' : '') }}KES {{ number_format($selectedTransaction->amount, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status:</span>
                                <flux:badge variant="{{ 
                                    $selectedTransaction->status === 'completed' ? 'success' : 
                                    ($selectedTransaction->status === 'pending' ? 'warning' : 
                                    ($selectedTransaction->status === 'failed' ? 'danger' : 'secondary')) 
                                }}">
                                    {{ ucfirst($selectedTransaction->status) }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Date:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $selectedTransaction->created_at->format('M d, Y g:i A') }}
                                </span>
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
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->member->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Email:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->member->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Account:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedTransaction->account->account_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($selectedTransaction->description)
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Description</flux:heading>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $selectedTransaction->description }}</p>
                </div>
            @endif

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="closeModals">Close</flux:button>
                @if($selectedTransaction->status === 'pending' && auth()->user()->can('approve', $selectedTransaction))
                    <flux:button variant="primary">Approve</flux:button>
                    <flux:button variant="danger">Reject</flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
@endif