<?php

use App\Models\Account;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $member_id = '';
    public $account_id = '';
    public $amount = '';
    public $description = '';
    public $payment_method = 'cash';
    public $reference_number = '';
    public $member_search = '';
    
    public $members = [];
    public $accounts = [];
    public $selectedMember = null;
    public $selectedAccount = null;
    
    public $paymentMethods = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'mobile_money' => 'Mobile Money',
        'cheque' => 'Cheque',
    ];



    public function mount()
    {
        // Check if an account ID was passed in the URL
        $accountId = request()->get('account');
        
        // For now, allow all users to search members - can be restricted later
        $this->loadMembers();
        
        // If user is not admin, auto-select their own account
        if (!in_array(auth()->user()->email, ['admin@sacco.com'])) { // Simple admin check
            $this->member_id = auth()->id();
            $this->loadAccountsForMember();
            
            // If a specific account was requested and user owns it, pre-select it
            if ($accountId) {
                $account = Account::find($accountId);
                if ($account && $account->member_id === auth()->id()) {
                    $this->account_id = $accountId;
                    $this->selectedAccount = $account;
                }
            }
        } elseif ($accountId) {
            // For admin users, if an account was specified, pre-select the member and account
            $account = Account::with('member')->find($accountId);
            if ($account) {
                $this->member_id = $account->member_id;
                $this->selectedMember = $account->member;
                $this->member_search = $account->member->name;
                $this->loadAccountsForMember();
                $this->account_id = $accountId;
                $this->selectedAccount = $account;
            }
        }
    }

    public function loadMembers($search = '')
    {
        $memberSearchService = app(\App\Services\MemberSearchService::class);
        $this->members = $memberSearchService->searchMembersForTransactions($search, 10);
    }

    public function updatedMemberSearch()
    {
        if (strlen($this->member_search) >= 2) {
            $this->loadMembers($this->member_search);
        }
    }

    public function selectMember($memberId)
    {
        $this->member_id = $memberId;
        $this->selectedMember = User::find($memberId);
        $this->member_search = $this->selectedMember->name;
        $this->members = [];
        $this->loadAccountsForMember();
        $this->account_id = '';
        $this->selectedAccount = null;
    }

    public function loadAccountsForMember()
    {
        if ($this->member_id) {
            $accountLookupService = app(\App\Services\AccountLookupService::class);
            $this->accounts = $accountLookupService->getMemberAccounts($this->member_id, 'active');
        }
    }

    public function selectAccount($accountId)
    {
        $this->account_id = $accountId;
        $accountLookupService = app(\App\Services\AccountLookupService::class);
        $this->selectedAccount = $accountLookupService->getAccountDetails($accountId);
    }

    public function updatedAccountId()
    {
        if ($this->account_id) {
            $accountLookupService = app(\App\Services\AccountLookupService::class);
            $this->selectedAccount = $accountLookupService->getAccountDetails($this->account_id);
        }
    }

    public function generateReference()
    {
        $this->reference_number = 'DEP' . time() . rand(1000, 9999);
    }

    public function processDeposit()
    {
        $this->validate([
            'member_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255',
            'payment_method' => 'required|in:' . implode(',', array_keys($this->paymentMethods)),
            'reference_number' => 'nullable|string|max:50',
        ]);

        try {
            $account = Account::findOrFail($this->account_id);
            
            // Check authorization - simplified
            if ($account->member_id !== auth()->id() && !in_array(auth()->user()->email, ['admin@sacco.com'])) {
                $this->addError('general', 'Unauthorized access to this account.');
                return;
            }

            $metadata = [
                'payment_method' => $this->payment_method,
                'external_reference' => $this->reference_number,
                'processed_by' => auth()->id(),
            ];

            $description = $this->description ?: 'Deposit to ' . $account->account_type . ' account';
            
            // Use TransactionService for consistent processing
            $transactionService = app(\App\Services\TransactionService::class);
            $transaction = $transactionService->processDeposit($account, (float) $this->amount, $description, $metadata);

            session()->flash('success', 'Deposit processed successfully! Transaction ID: ' . $transaction->reference_number);
            
            // Reset form
            $this->reset(['amount', 'description', 'reference_number']);
            $this->selectedAccount = Account::find($this->account_id); // Refresh account data
            
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to process deposit: ' . $e->getMessage());
        }
    }


}; ?>

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('transactions.index') }}" class="p-2 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                        <flux:icon.arrow-left class="w-5 h-5" />
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Process Deposit</h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Add funds to member accounts securely</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <flux:icon.shield-check class="w-4 h-4" />
                    <span>Secure Transaction</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Deposit Form -->
                <div class="lg:col-span-2">
                    <form wire:submit="processDeposit" class="space-y-6">
                        
                        <!-- Member Selection (for admin users) -->
                        @if(in_array(auth()->user()->email, ['admin@sacco.com']))
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                    {{ __('Select Member') }}
                                </h3>

                                <div class="relative">
                                    <flux:field>
                                        <flux:label>{{ __('Search Member') }}</flux:label>
                                        <flux:input 
                                            wire:model.live.debounce.300ms="member_search"
                                            type="text"
                                            placeholder="{{ __('Type member name, email, or member number...') }}" />
                                    </flux:field>

                                    @if(count($members) > 0 && $member_search && !$selectedMember)
                                        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            @foreach($members as $member)
                                                <div 
                                                    wire:click="selectMember({{ $member->id }})"
                                                    class="px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer border-b border-zinc-100 dark:border-zinc-600 last:border-b-0">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $member->name }}</div>
                                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        {{ $member->email }} • {{ $member->member_number ?? 'No member number' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if($selectedMember)
                                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-green-900 dark:text-green-100">
                                                    {{ $selectedMember->name }}
                                                </div>
                                                <div class="text-sm text-green-600 dark:text-green-400">
                                                    {{ $selectedMember->email }} • {{ $selectedMember->member_number ?? 'No member number' }}
                                                </div>
                                            </div>
                                            <flux:button 
                                                type="button"
                                                wire:click="$set('selectedMember', null)"
                                                variant="ghost"
                                                size="sm">
                                                {{ __('Change') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Account Selection -->
                        @if($member_id && count($accounts) > 0)
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                    {{ __('Select Account') }}
                                </h3>

                                <div class="mb-4">
                                    <label for="account_select" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Select Account *
                                    </label>
                                    <select 
                                        wire:model.live="account_id" 
                                        id="account_select" 
                                        required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                               dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                        <option value="">-- Select an account --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $account_id == $account->id ? 'selected' : '' }}>
                                                {{ $account->account_number }} - {{ ucfirst($account->account_type) }} (KES {{ number_format($account->balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if($selectedAccount)
                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-3">Account Details</h4>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-blue-600 dark:text-blue-400">Account Number:</span>
                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $selectedAccount->account_number }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-blue-600 dark:text-blue-400">Account Type:</span>
                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ ucfirst($selectedAccount->account_type) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-blue-600 dark:text-blue-400">Current Balance:</span>
                                                <span class="font-medium text-emerald-600 dark:text-emerald-400">KES {{ number_format($selectedAccount->balance, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Deposit Details -->
                        @if($account_id)
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                                <div class="mb-6">
                                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Deposit Information</h2>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Fill in the details below to process the deposit</p>
                                </div>

                                <div class="space-y-6">
                                    <!-- Amount -->
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            Deposit Amount (KES) *
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-zinc-500 dark:text-zinc-400">KES</span>
                                            </div>
                                            <input 
                                                wire:model.live="amount"
                                                type="number" 
                                                id="amount" 
                                                required
                                                min="1" 
                                                max="1000000" 
                                                step="0.01" 
                                                placeholder="0.00"
                                                class="w-full pl-12 pr-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                                       dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400">Minimum: KES 1.00</span>
                                            <span class="text-zinc-500 dark:text-zinc-400">Maximum: KES 1,000,000.00</span>
                                        </div>
                                        @error('amount')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                        
                                        @if($amount >= 50000)
                                            <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                                <div class="flex">
                                                    <flux:icon.clock class="w-5 h-5 text-amber-400 mr-2 flex-shrink-0" />
                                                    <p class="text-sm text-amber-800 dark:text-amber-200">
                                                        Large deposits (KES 50,000+) require management approval.
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Payment Method -->
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            Payment Method *
                                        </label>
                                        <select 
                                            wire:model="payment_method" 
                                            id="payment_method" 
                                            required
                                            class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                                   dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                            @foreach($paymentMethods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Reference Number -->
                                    <div>
                                        <label for="reference_number" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            Reference Number (Optional)
                                        </label>
                                        <div class="flex gap-2">
                                            <input 
                                                wire:model="reference_number"
                                                type="text" 
                                                id="reference_number" 
                                                placeholder="Optional external reference"
                                                class="flex-1 px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                                       dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                            <button 
                                                type="button"
                                                wire:click="generateReference"
                                                class="px-4 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 
                                                       hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                                                Generate
                                            </button>
                                        </div>
                                        @error('reference_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            Description (Optional)
                                        </label>
                                        <textarea 
                                            wire:model="description"
                                            id="description" 
                                            rows="3" 
                                            placeholder="Enter a description for this deposit..."
                                            class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                                   dark:bg-zinc-700 dark:text-zinc-100 transition-colors">{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('transactions.index') }}" 
                                class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium">
                                Cancel
                            </a>
                            <button 
                                wire:click="processDeposit" 
                                type="button"
                                {{ !$account_id || !$amount ? 'disabled' : '' }}
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                <flux:icon.arrow-down class="w-5 h-5 mr-2" />
                                Process Deposit
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Transaction Summary & Info -->
                <div class="space-y-6">
                    <!-- Transaction Summary -->
                    @if($selectedAccount && $amount)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Transaction Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Number:</span>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedAccount->account_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Type:</span>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($selectedAccount->account_type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Current Balance:</span>
                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">KES {{ number_format($selectedAccount->balance, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Deposit Amount:</span>
                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">+ KES {{ number_format($amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Payment Method:</span>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $paymentMethods[$payment_method] ?? 'Cash' }}</span>
                                </div>
                                <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-700 pt-3">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">Balance After Deposit:</span>
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">KES {{ number_format($selectedAccount->balance + $amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Deposit Limits -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Deposit Limits</h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                <span class="text-zinc-600 dark:text-zinc-400">Minimum deposit: KES 1.00</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                <span class="text-zinc-600 dark:text-zinc-400">Maximum deposit: KES 1,000,000.00</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <flux:icon.clock class="w-4 h-4 text-amber-500 mr-2" />
                                <span class="text-zinc-600 dark:text-zinc-400">Large deposits (KES 50,000+) require approval</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <flux:icon.shield-check class="w-4 h-4 text-blue-500 mr-2" />
                                <span class="text-zinc-600 dark:text-zinc-400">All transactions are encrypted and secure</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Need Help?</h3>
                        <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                            If you have any questions about making deposits, our support team is here to help.
                        </p>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                            Contact Support →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 