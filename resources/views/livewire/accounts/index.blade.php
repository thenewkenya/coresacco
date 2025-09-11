<?php

use App\Models\Account;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $accountTypeFilter = '';
    public $statusFilter = '';
    public $viewMode = 'list'; // 'list' or 'grid'
    public $showViewModal = false;
    public $selectedAccount = null;

    public $accountTypeOptions = [
        'savings' => 'Savings',
        'current' => 'Current',
        'fixed_deposit' => 'Fixed Deposit',
        'loan' => 'Loan',
    ];

    public $statusOptions = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'closed' => 'Closed',
    ];

    public function with()
    {
        $query = Account::query()
            ->with(['member'])
            ->when($this->search, fn($q) => 
                $q->where('account_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('member', fn($q) => 
                      $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->accountTypeFilter, fn($q) => 
                $q->where('account_type', $this->accountTypeFilter)
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            );

        $accounts = $query->latest()->paginate(15);

        // Stats for dashboard
        $totalAccounts = Account::count();
        $activeAccounts = Account::where('status', 'active')->count();
        $inactiveAccounts = Account::where('status', 'inactive')->count();
        $totalBalance = Account::sum('balance');
        $thisMonthAccounts = Account::whereMonth('created_at', now()->month)->count();

        return [
            'accounts' => $accounts,
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'inactiveAccounts' => $inactiveAccounts,
            'totalBalance' => $totalBalance,
            'thisMonthAccounts' => $thisMonthAccounts,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedAccountTypeFilter()
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

    public function viewAccount($accountId)
    {
        $this->selectedAccount = Account::with(['member'])->findOrFail($accountId);
        $this->showViewModal = true;
    }

    public function closeModals()
    {
        $this->showViewModal = false;
        $this->selectedAccount = null;
    }
}

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Account Management</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Manage member accounts and monitor balances</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" icon="arrow-path">
                Refresh
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('accounts.create')" wire:navigate>
                Create Account
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Accounts</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalAccounts) }}</flux:heading>
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
                    <flux:heading size="lg" class="!text-green-600 dark:!text-green-400">{{ number_format($activeAccounts) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Inactive</flux:subheading>
                    <flux:heading size="lg" class="!text-amber-600 dark:!text-amber-400">{{ number_format($inactiveAccounts) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Balance</flux:subheading>
                    <flux:heading size="lg" class="!text-emerald-600 dark:!text-emerald-400">KES {{ number_format($totalBalance) }}</flux:heading>
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
                        placeholder="Search accounts, members..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <flux:select wire:model.live="accountTypeFilter" placeholder="All Types">
                    <option value="">All Types</option>
                    @foreach($accountTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

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

    <!-- Accounts Display -->
    @if($accounts->count())
        @if($viewMode === 'list')
            <!-- List View -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Account</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Member</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Balance</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Interest Rate</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Created</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        @foreach($accounts as $account)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg {{ 
                                            $account->account_type === 'savings' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 
                                            ($account->account_type === 'current' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                                            ($account->account_type === 'fixed_deposit' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-orange-100 dark:bg-orange-900/30')) 
                                        }}">
                                            @if($account->account_type === 'savings')
                                                <flux:icon.banknotes class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                            @elseif($account->account_type === 'current')
                                                <flux:icon.credit-card class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            @elseif($account->account_type === 'fixed_deposit')
                                                <flux:icon.calendar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                            @else
                                                <flux:icon.currency-dollar class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $account->account_number }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $account->account_name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $account->member->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $account->member->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="{{ 
                                        $account->account_type === 'savings' ? 'success' : 
                                        ($account->account_type === 'current' ? 'primary' : 
                                        ($account->account_type === 'fixed_deposit' ? 'secondary' : 'warning')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $account->account_type)) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($account->balance, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $account->interest_rate }}% p.a.
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="{{ 
                                        $account->status === 'active' ? 'success' : 
                                        ($account->status === 'inactive' ? 'warning' : 
                                        ($account->status === 'suspended' ? 'danger' : 'secondary')) 
                                    }}">
                                        {{ ucfirst($account->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $account->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $account->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:dropdown align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        
                                        <flux:menu>
                                            <flux:menu.item wire:click="viewAccount({{ $account->id }})" icon="eye">
                                                View Details
                                            </flux:menu.item>
                                            <flux:menu.item :href="route('accounts.show', $account)" icon="arrow-top-right-on-square">
                                                Open Account
                                            </flux:menu.item>
                                            @can('update', $account)
                                                <flux:menu.item :href="route('accounts.edit', $account)" icon="pencil">
                                                    Edit Account
                                                </flux:menu.item>
                                            @endcan
                                            @if($account->status === 'active')
                                                <flux:menu.item icon="pause">
                                                    Suspend
                                                </flux:menu.item>
                                            @elseif($account->status === 'inactive' || $account->status === 'suspended')
                                                <flux:menu.item icon="play">
                                                    Activate
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
                @foreach($accounts as $account)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-lg {{ 
                                    $account->account_type === 'savings' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 
                                    ($account->account_type === 'current' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                                    ($account->account_type === 'fixed_deposit' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-orange-100 dark:bg-orange-900/30')) 
                                }}">
                                    @if($account->account_type === 'savings')
                                        <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($account->account_type === 'current')
                                        <flux:icon.credit-card class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @elseif($account->account_type === 'fixed_deposit')
                                        <flux:icon.calendar class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    @else
                                        <flux:icon.currency-dollar class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $account->account_number }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ ucfirst(str_replace('_', ' ', $account->account_type)) }}
                                    </div>
                                </div>
                            </div>
                            <flux:badge variant="{{ 
                                $account->status === 'active' ? 'success' : 
                                ($account->status === 'inactive' ? 'warning' : 
                                ($account->status === 'suspended' ? 'danger' : 'secondary')) 
                            }}">
                                {{ ucfirst($account->status) }}
                            </flux:badge>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Member</div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->member->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $account->member->email }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Balance</div>
                                <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($account->balance, 2) }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Interest Rate</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $account->interest_rate }}% p.a.
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">Created</div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $account->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button variant="ghost" size="sm" wire:click="viewAccount({{ $account->id }})">
                                <flux:icon.eye class="w-4 h-4 mr-2" />
                                View
                            </flux:button>
                            <div class="flex space-x-2">
                                <flux:button variant="outline" size="sm" :href="route('accounts.show', $account)">
                                    <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                </flux:button>
                                @can('update', $account)
                                    <flux:button variant="outline" size="sm" :href="route('accounts.edit', $account)">
                                        <flux:icon.pencil class="w-4 h-4" />
                                    </flux:button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $accounts->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon.credit-card class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No accounts found</h3>
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">Get started by creating a new account or adjust your search criteria.</p>
            <flux:button variant="primary" icon="plus" :href="route('accounts.create')" wire:navigate>
                Create Account
            </flux:button>
        </div>
    @endif
</div>

<!-- Account View Modal -->
@if($selectedAccount)
    <flux:modal wire:model="showViewModal" class="md:w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Account Details</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Account: {{ $selectedAccount->account_number }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Account Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Number:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100 font-mono">{{ $selectedAccount->account_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Name:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->account_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Type:</span>
                                <flux:badge variant="{{ 
                                    $selectedAccount->account_type === 'savings' ? 'success' : 
                                    ($selectedAccount->account_type === 'current' ? 'primary' : 
                                    ($selectedAccount->account_type === 'fixed_deposit' ? 'secondary' : 'warning')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $selectedAccount->account_type)) }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status:</span>
                                <flux:badge variant="{{ 
                                    $selectedAccount->status === 'active' ? 'success' : 
                                    ($selectedAccount->status === 'inactive' ? 'warning' : 
                                    ($selectedAccount->status === 'suspended' ? 'danger' : 'secondary')) 
                                }}">
                                    {{ ucfirst($selectedAccount->status) }}
                                </flux:badge>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Interest Rate:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->interest_rate }}% p.a.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Financial Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Current Balance:</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($selectedAccount->balance, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Created:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Last Updated:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Member Information</flux:heading>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Name:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->member->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Email:</span>
                                <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->member->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="closeModals">Close</flux:button>
                <flux:button variant="outline" :href="route('accounts.show', $selectedAccount)">Open Account</flux:button>
                @can('update', $selectedAccount)
                    <flux:button variant="primary" :href="route('accounts.edit', $selectedAccount)">Edit Account</flux:button>
                @endcan
            </div>
        </div>
    </flux:modal>
@endif
