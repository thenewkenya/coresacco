<?php

use App\Models\Account;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $member_id = '';
    public $account_id = '';
    public $amount = '';
    public $purpose = '';
    public $payment_method = 'cash';
    public $reference_number = '';
    public $notes = '';
    public $member_search = '';
    
    public $members = [];
    public $accounts = [];
    public $selectedMember = null;
    public $selectedAccount = null;
    
    public $paymentMethods = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'mpesa' => 'M-Pesa',
    ];

    public function mount()
    {
        // Check if an account ID was passed in the URL
        $accountId = request()->get('account');
        
        // For now, allow all users to search members - can be restricted later
        $this->loadMembers();
        
        // If user is not admin, auto-select their own account
        if (!auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
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
        
        $this->generateReference();
    }

    public function loadMembers($search = '')
    {
        $this->members = User::where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('member_number', 'like', '%' . $search . '%')
            ->limit(10)
            ->get();
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
            $this->accounts = Account::where('member_id', $this->member_id)
                ->where('status', 'active')
                ->where('balance', '>', 1000) // Minimum balance requirement
                ->get();
        }
    }

    public function selectAccount($accountId)
    {
        $this->account_id = $accountId;
        $this->selectedAccount = Account::find($accountId);
    }

    public function updatedAccountId()
    {
        if ($this->account_id) {
            $this->selectedAccount = Account::find($this->account_id);
        }
    }

    public function generateReference()
    {
        $this->reference_number = 'WTH' . time() . rand(1000, 9999);
    }

    public function getAvailableBalance()
    {
        if (!$this->selectedAccount) return 0;
        return max(0, $this->selectedAccount->balance - 1000); // Subtract minimum balance
    }

    public function getWithdrawalFees()
    {
        $amount = (float) $this->amount;
        if ($amount <= 0) return 0;
        
        // Fee structure: 1% of withdrawal amount, minimum 50 KES, maximum 500 KES
        $fee = $amount * 0.01;
        return max(50, min(500, $fee));
    }

    public function getTotalAmount()
    {
        return (float) $this->amount + $this->getWithdrawalFees();
    }

    public function processWithdrawal()
    {
        $this->validate([
            'member_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:500000',
            'purpose' => 'required|string|max:255',
            'payment_method' => 'required|in:' . implode(',', array_keys($this->paymentMethods)),
            'reference_number' => 'nullable|string|max:50',
        ]);

        try {
            $account = Account::findOrFail($this->account_id);
            
            // Check authorization - simplified
            if ($account->member_id !== auth()->id() && !auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
                $this->addError('general', 'Unauthorized access to this account.');
                return;
            }

            // Check sufficient balance
            if ($this->getTotalAmount() > $this->getAvailableBalance()) {
                $this->addError('amount', 'Insufficient funds. Maximum withdrawal: KES ' . number_format($this->getAvailableBalance() - $this->getWithdrawalFees(), 2));
                return;
            }

            $metadata = [
                'purpose' => $this->purpose,
                'payment_method' => $this->payment_method,
                'external_reference' => $this->reference_number,
                'processed_by' => auth()->id(),
                'withdrawal_amount' => (float) $this->amount,
                'fees' => $this->getWithdrawalFees(),
                'total_deducted' => $this->getTotalAmount(),
                'notes' => $this->notes,
            ];

            $description = "Withdrawal for {$this->purpose} (Amount: KES " . number_format((float) $this->amount, 2) . ", Fee: KES " . number_format($this->getWithdrawalFees(), 2) . ")";
            
            // Use TransactionService for consistent processing
            $transactionService = app(\App\Services\TransactionService::class);
            $transaction = $transactionService->processWithdrawal($account, $this->getTotalAmount(), $description, $metadata);

            // Redirect to receipt page
            $this->js('window.location.href = "' . route('transactions.receipt', $transaction) . '"');
            
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('transactions.index') }}" class="p-2 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                    <flux:icon.arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Process Withdrawal</flux:heading>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Withdraw funds from member accounts securely</flux:subheading>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                <flux:icon.shield-check class="w-4 h-4" />
                <span>Secure Transaction</span>
            </div>
        </div>
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Withdrawal Form -->
                <div class="lg:col-span-2">
                    <form wire:submit="processWithdrawal" class="space-y-6">
                        
                        <!-- Member Selection (for admin users) -->
                        @if(auth()->user()->hasAnyRole(['admin', 'manager', 'staff']))
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                    {{ __('Select Member') }}
                                </h3>

                                <!-- Member Search -->
                                <div class="relative">
                                    <flux:input 
                                        wire:model.live="member_search" 
                                        placeholder="{{ __('Search by name, email, or member number...') }}"
                                        class="w-full"
                                    />
                                    
                                    @if($members->count() > 0 && $member_search)
                                        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            @foreach($members as $member)
                                                <button 
                                                    type="button"
                                                    wire:click="selectMember({{ $member->id }})"
                                                    class="w-full text-left px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 border-b border-zinc-100 dark:border-zinc-700 last:border-b-0"
                                                >
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                            <span class="text-sm font-bold text-white">
                                                                {{ substr($member->name, 0, 1) }}{{ substr(explode(' ', $member->name)[1] ?? '', 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $member->name }}</p>
                                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->email }}</p>
                                                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $member->member_number }}</p>
                                                        </div>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if($selectedMember)
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-lg font-bold text-white">
                                                    {{ substr($selectedMember->name, 0, 1) }}{{ substr(explode(' ', $selectedMember->name)[1] ?? '', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->name }}</p>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $selectedMember->email }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-500">Member #{{ $selectedMember->member_number }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @error('member_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Account Selection -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Select Account') }}
                            </h3>

                            @if($accounts->count() > 0)
                                <div>
                                    <flux:field>
                                        <flux:label for="account_id">{{ __('Account') }}</flux:label>
                                        <flux:select 
                                            id="account_id"
                                            wire:model.live="account_id" 
                                            wire:change="selectAccount($event.target.value)"
                                            placeholder="{{ __('Select an account for withdrawal') }}"
                                        >
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_number }} - {{ ucfirst(str_replace('_', ' ', $account->account_type)) }} 
                                                    (Balance: KES {{ number_format($account->balance, 2) }})
                                                </option>
                                            @endforeach
                                        </flux:select>
                                        <flux:error name="account_id" />
                                    </flux:field>

                                    @if($selectedAccount)
                                        <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                                            <div class="flex items-start space-x-4">
                                                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                                    <flux:icon.credit-card class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ $selectedAccount->account_number }}
                                                    </h4>
                                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                                        {{ ucfirst(str_replace('_', ' ', $selectedAccount->account_type)) }} Account
                                                    </p>
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        <div>
                                                            <span class="text-zinc-500 dark:text-zinc-400">Current Balance:</span>
                                                            <p class="font-semibold text-emerald-600 dark:text-emerald-400">
                                                                KES {{ number_format($selectedAccount->balance, 2) }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <span class="text-zinc-500 dark:text-zinc-400">Available for Withdrawal:</span>
                                                            <p class="font-semibold text-blue-600 dark:text-blue-400">
                                                                KES {{ number_format($this->getAvailableBalance(), 2) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <flux:icon.credit-card class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ $member_id ? 'No eligible accounts found for withdrawal' : 'Please select a member first' }}
                                    </p>
                                    @if($member_id)
                                        <p class="text-sm text-zinc-500 dark:text-zinc-500 mt-2">
                                            Accounts must have a minimum balance of KES 1,000 to be eligible for withdrawal
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Withdrawal Details -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Withdrawal Details') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Amount -->
                                <div>
                                    <flux:field>
                                        <flux:label for="amount">{{ __('Withdrawal Amount') }}</flux:label>
                                        <flux:input 
                                            id="amount"
                                            wire:model.live.debounce.300ms="amount" 
                                            type="number" 
                                            step="0.01"
                                            min="1"
                                            max="500000"
                                            placeholder="0.00"
                                        />
                                        <flux:error name="amount" />
                                    </flux:field>
                                </div>

                                <!-- Purpose -->
                                <div>
                                    <flux:field>
                                        <flux:label for="purpose">{{ __('Purpose of Withdrawal') }}</flux:label>
                                        <flux:input 
                                            id="purpose"
                                            wire:model="purpose" 
                                            placeholder="Emergency medical expenses, school fees, etc."
                                        />
                                        <flux:error name="purpose" />
                                    </flux:field>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <flux:field>
                                        <flux:label for="payment_method">{{ __('Payment Method') }}</flux:label>
                                        <flux:select wire:model="payment_method" placeholder="{{ __('Select payment method') }}">
                                            @foreach($paymentMethods as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </flux:select>
                                        <flux:error name="payment_method" />
                                    </flux:field>
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <flux:field>
                                        <flux:label for="reference_number">{{ __('Reference Number') }}</flux:label>
                                        <div class="flex space-x-2">
                                            <flux:input 
                                                id="reference_number"
                                                wire:model="reference_number" 
                                                placeholder="Auto-generated"
                                                class="flex-1"
                                            />
                                            <flux:button 
                                                type="button" 
                                                variant="outline" 
                                                wire:click="generateReference"
                                                size="sm"
                                            >
                                                {{ __('Generate') }}
                                            </flux:button>
                                        </div>
                                        <flux:error name="reference_number" />
                                    </flux:field>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mt-6">
                                <flux:field>
                                    <flux:label for="notes">{{ __('Additional Notes') }} ({{ __('Optional') }})</flux:label>
                                    <flux:textarea 
                                        id="notes"
                                        wire:model="notes" 
                                        rows="3"
                                        placeholder="Any additional information about this withdrawal..."
                                    />
                                    <flux:error name="notes" />
                                </flux:field>
                            </div>
                        </div>

                        <!-- Error Messages -->
                        @if($errors->has('general'))
                            <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-xl p-4">
                                <div class="flex items-center">
                                    <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 mr-3" />
                                    <span class="text-red-700 dark:text-red-400">{{ $errors->first('general') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <flux:button variant="ghost" :href="route('transactions.index')">{{ __('Cancel') }}</flux:button>
                            <div class="flex gap-3">
                                <flux:button 
                                    type="button" 
                                    variant="outline"
                                    wire:click="$refresh"
                                >
                                    {{ __('Reset Form') }}
                                </flux:button>
                                <flux:button 
                                    type="submit" 
                                    variant="primary"
                                    :disabled="!$selectedAccount || !$amount || !$purpose"
                                >
                                    {{ __('Process Withdrawal') }}
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Transaction Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 sticky top-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('Transaction Summary') }}
                            </h3>
                            <flux:badge variant="primary">Draft</flux:badge>
                        </div>

                        @if($selectedAccount)
                            <!-- Account Info -->
                            <div class="mb-6 p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                    {{ $selectedAccount->account_number }}
                                </h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $selectedAccount->account_type)) }} Account
                                </p>
                                <p class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">
                                    Balance: KES {{ number_format($selectedAccount->balance, 2) }}
                                </p>
                                <p class="text-sm text-blue-600 dark:text-blue-400">
                                    Available: KES {{ number_format($this->getAvailableBalance(), 2) }}
                                </p>
                            </div>
                        @endif

                        <!-- Amount Breakdown -->
                        @if($amount > 0)
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Withdrawal Amount') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format((float) $amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Processing Fee') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($this->getWithdrawalFees(), 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-3">
                                    <div class="flex items-center space-x-2">
                                        <flux:badge variant="secondary">Calculated</flux:badge>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100 text-sm">{{ __('Total Deduction') }}</span>
                                    </div>
                                    <span class="font-bold text-red-600 dark:text-red-400">KES {{ number_format($this->getTotalAmount(), 2) }}</span>
                                </div>
                                
                                @if($selectedAccount)
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-3">
                                        <span>{{ __('Remaining Balance') }}: </span>
                                        <span class="font-medium">KES {{ number_format($selectedAccount->balance - $this->getTotalAmount(), 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Security Notice -->
                        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <div class="flex items-start space-x-3">
                                <flux:icon.shield-check class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300 mb-1">
                                        {{ __('Security Notice') }}
                                    </h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-400">
                                        {{ __('All withdrawals are subject to verification and approval. A minimum balance of KES 1,000 must be maintained.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Structure -->
                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                                {{ __('Fee Structure') }}
                            </h4>
                            <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                                <li>• 1% of withdrawal amount</li>
                                <li>• Minimum fee: KES 50</li>
                                <li>• Maximum fee: KES 500</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 