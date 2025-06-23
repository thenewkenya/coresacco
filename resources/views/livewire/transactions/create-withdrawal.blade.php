<?php

use function Livewire\Volt\{state, computed, rules, mount};
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;

// State
state([
    'selectedAccount' => null,
    'amount' => '',
    'purpose' => '',
    'payment_method' => 'cash',
    'reference_number' => '',
    'notes' => '',
    'search' => '',
    'showAccountSelector' => false,
]);

// Rules
rules([
    'selectedAccount' => 'required',
    'amount' => 'required|numeric|min:1|max:500000',
    'purpose' => 'required|string|max:255',
    'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
    'reference_number' => 'nullable|string|max:100',
    'notes' => 'nullable|string|max:500',
]);

// Computed properties
$accounts = computed(function () {
    $query = auth()->user()->accounts()
        ->where('status', 'active')
        ->where('account_type', 'savings')
        ->where('balance', '>', 1000) // Minimum balance requirement
        ->when($this->search, function ($q) {
            $q->where('account_number', 'like', "%{$this->search}%");
        });
    
    return $query->get();
});

$selectedAccountDetails = computed(function () {
    return $this->selectedAccount ? Account::find($this->selectedAccount) : null;
});

$availableBalance = computed(function () {
    if (!$this->selectedAccountDetails) return 0;
    
    // Subtract minimum balance requirement
    return max(0, $this->selectedAccountDetails->balance - 1000);
});

$fees = computed(function () {
    $amount = (float) $this->amount;
    if ($amount <= 0) return 0;
    
    // Fee structure: 1% of withdrawal amount, minimum 50 KES, maximum 500 KES
    $fee = $amount * 0.01;
    return max(50, min(500, $fee));
});

$totalAmount = computed(function () {
    return (float) $this->amount + $this->fees;
});

// Methods
$selectAccount = function ($accountId) {
    $this->selectedAccount = $accountId;
    $this->showAccountSelector = false;
    $this->generateReferenceNumber();
};

$generateReferenceNumber = function () {
    if (empty($this->reference_number)) {
        $this->reference_number = 'WTH' . now()->format('YmdHis') . rand(100, 999);
    }
};

$submit = function (TransactionService $transactionService) {
    $this->validate();
    
    if (!$this->selectedAccountDetails) {
        $this->addError('selectedAccount', 'Please select a valid account.');
        return;
    }
    
    if ($this->totalAmount > $this->availableBalance) {
        $this->addError('amount', 'Insufficient funds. Maximum withdrawal: KES ' . number_format($this->availableBalance - $this->fees, 2));
        return;
    }
    
    try {
        $account = Account::findOrFail($this->selectedAccount);
        
        // First process the withdrawal for the requested amount
        $metadata = [
            'purpose' => $this->purpose,
            'payment_method' => $this->payment_method,
            'reference_number' => $this->reference_number,
            'notes' => $this->notes,
            'withdrawal_amount' => (float) $this->amount,
            'fees' => $this->fees,
            'total_deducted' => $this->totalAmount,
        ];
        
        $description = "Withdrawal for {$this->purpose} (Amount: " . $this->formatCurrency((float) $this->amount) . ", Fee: " . $this->formatCurrency($this->fees) . ")";
        
        // Process withdrawal for the total amount (including fees)
        $transaction = $transactionService->processWithdrawal($account, $this->totalAmount, $description, $metadata);
        
        if ($transaction) {
            session()->flash('success', 'Withdrawal processed successfully!');
            return redirect()->route('transactions.receipt', $transaction);
        } else {
            $this->addError('form', 'Failed to process withdrawal. Please try again.');
        }
    } catch (\Exception $e) {
        $this->addError('form', $e->getMessage());
    }
};

$resetForm = function () {
    $this->reset(['selectedAccount', 'amount', 'purpose', 'reference_number', 'notes']);
    $this->showAccountSelector = false;
};

$formatCurrency = function ($amount) {
    return 'KES ' . number_format($amount, 2);
};

mount(function () {
    $this->generateReferenceNumber();
    
    // Check if an account ID was passed in the URL
    $accountId = request()->get('account');
    if ($accountId) {
        $account = Account::where('id', $accountId)
            ->where('member_id', auth()->id())
            ->where('status', 'active')
            ->where('balance', '>', 1000) // Minimum balance requirement
            ->first();
            
        if ($account) {
            $this->selectedAccount = $accountId;
        }
    }
});

?>

<div>
    <flux:header>
        <flux:heading size="xl">Withdraw Funds</flux:heading>
        <flux:subheading>Withdraw money from your savings account</flux:subheading>
    </flux:header>

    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <form wire:submit="submit" class="space-y-6">
                        @if($errors->has('form'))
                            <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 mr-2" />
                                    <span class="text-red-700 dark:text-red-400">{{ $errors->first('form') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Account Selection -->
                        <div>
                            <flux:field>
                                <flux:label>Select Account</flux:label>
                                
                                @if($this->selectedAccountDetails)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white">
                                                    Account #{{ $this->selectedAccountDetails->account_number }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst($this->selectedAccountDetails->account_type) }} Account
                                                </p>
                                                <p class="text-lg font-semibold text-green-600 dark:text-green-400 mt-2">
                                                    Balance: {{ $this->formatCurrency($this->selectedAccountDetails->balance) }}
                                                </p>
                                                <p class="text-sm text-blue-600 dark:text-blue-400">
                                                    Available for withdrawal: {{ $this->formatCurrency($this->availableBalance) }}
                                                </p>
                                            </div>
                                            <flux:button 
                                                type="button" 
                                                variant="ghost" 
                                                size="sm"
                                                wire:click="$set('showAccountSelector', true)"
                                            >
                                                Change
                                            </flux:button>
                                        </div>
                                    </div>
                                @else
                                    <flux:button 
                                        type="button" 
                                        variant="outline" 
                                        class="w-full p-4 h-auto justify-start"
                                        wire:click="$set('showAccountSelector', true)"
                                    >
                                        <div class="text-left">
                                            <p class="font-medium">Select Account</p>
                                            <p class="text-sm text-gray-500">Choose an account to withdraw from</p>
                                        </div>
                                    </flux:button>
                                @endif
                                
                                <flux:error name="selectedAccount" />
                            </flux:field>
                        </div>

                        <!-- Account Selector Dropdown -->
                        @if($showAccountSelector)
                            <!-- Invisible overlay for click-outside-to-close -->
                            <div class="fixed inset-0 z-40" wire:click="$set('showAccountSelector', false)"></div>
                            
                            <div class="relative z-50">
                                <!-- Dropdown panel -->
                                <div class="absolute top-2 left-0 w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                    <!-- Header -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                Select Account
                                            </h3>
                                            <flux:button 
                                                type="button" 
                                                variant="ghost" 
                                                size="sm"
                                                wire:click="$set('showAccountSelector', false)"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 -mr-2"
                                            >
                                                <flux:icon.x-mark class="w-4 h-4" />
                                            </flux:button>
                                        </div>
                                        
                                        <!-- Search input -->
                                        <div class="mt-3">
                                            <flux:input 
                                                wire:model.live.debounce.300ms="search" 
                                                placeholder="Search accounts..." 
                                                class="w-full text-sm"
                                                size="sm"
                                                icon="magnifying-glass"
                                            />
                                        </div>
                                    </div>
                                    
                                    <!-- Account list -->
                                    <div class="max-h-72 overflow-y-auto">
                                        @forelse($this->accounts as $account)
                                            <div 
                                                class="p-3 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors duration-150"
                                                wire:click="selectAccount({{ $account->id }})"
                                            >
                                                <div class="flex justify-between items-center">
                                                    <div class="flex-1">
                                                        <div class="flex items-center">
                                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                                                                Account #{{ $account->account_number }}
                                                            </h4>
                                                        </div>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ ucfirst($account->account_type) }} Account
                                                        </p>
                                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                            Available: {{ $this->formatCurrency(max(0, $account->balance - 1000)) }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-semibold text-green-600 dark:text-green-400 text-sm">
                                                            {{ $this->formatCurrency($account->balance) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Balance
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8 px-4">
                                                <flux:icon.exclamation-circle class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No Eligible Accounts</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Accounts need minimum balance of KES 1,000
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Amount -->
                        <flux:field>
                            <flux:label>Withdrawal Amount (KES)</flux:label>
                            <flux:input 
                                wire:model.live="amount" 
                                type="number" 
                                step="0.01" 
                                min="1" 
                                max="{{ $this->availableBalance }}"
                                placeholder="Enter amount to withdraw"
                            />
                            <flux:error name="amount" />
                            
                            @if($this->selectedAccountDetails && $amount > 0)
                                <div class="mt-2 text-sm">
                                    @if($this->totalAmount > $this->availableBalance)
                                        <p class="text-red-600 dark:text-red-400">
                                            ⚠️ Insufficient funds (including fees)
                                        </p>
                                    @else
                                        <p class="text-green-600 dark:text-green-400">
                                            ✓ Sufficient funds available
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </flux:field>

                        <!-- Purpose -->
                        <flux:field>
                            <flux:label>Purpose of Withdrawal</flux:label>
                            <flux:select wire:model.live="purpose">
                                <option value="">Select purpose</option>
                                <option value="personal">Personal Use</option>
                                <option value="medical">Medical Emergency</option>
                                <option value="education">Education Expenses</option>
                                <option value="business">Business Investment</option>
                                <option value="home">Home Improvement</option>
                                <option value="emergency">Emergency Fund</option>
                                <option value="other">Other</option>
                            </flux:select>
                            <flux:error name="purpose" />
                        </flux:field>

                        <!-- Payment Method -->
                        <flux:field>
                            <flux:label>Payment Method</flux:label>
                            <flux:select wire:model.live="payment_method">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cheque">Cheque</option>
                            </flux:select>
                            <flux:error name="payment_method" />
                        </flux:field>

                        <!-- Reference Number -->
                        <flux:field>
                            <flux:label>Reference Number</flux:label>
                            <flux:input 
                                wire:model="reference_number" 
                                placeholder="Auto-generated reference number"
                                readonly
                            />
                            <flux:description>
                                This reference number will be used for tracking the transaction
                            </flux:description>
                        </flux:field>

                        <!-- Notes -->
                        <flux:field>
                            <flux:label>Additional Notes (Optional)</flux:label>
                            <flux:textarea 
                                wire:model="notes" 
                                placeholder="Any additional information about this withdrawal..."
                                rows="3"
                            />
                            <flux:error name="notes" />
                        </flux:field>

                        <!-- Form Actions -->
                        <div class="flex gap-3 pt-4">
                            <flux:button type="submit" variant="primary" class="flex-1">
                                Process Withdrawal
                            </flux:button>
                            <flux:button type="button" variant="ghost" wire:click="resetForm">
                                Reset
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Withdrawal Amount:</span>
                            <span class="font-medium">{{ $this->formatCurrency((float) $amount) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Processing Fee:</span>
                            <span class="font-medium">{{ $this->formatCurrency($this->fees) }}</span>
                        </div>
                        
                        <hr class="border-gray-200 dark:border-gray-700">
                        
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total Deduction:</span>
                            <span>{{ $this->formatCurrency($this->totalAmount) }}</span>
                        </div>
                        
                        @if($this->selectedAccountDetails)
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-sm space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Current Balance:</span>
                                        <span>{{ $this->formatCurrency($this->selectedAccountDetails->balance) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">After Withdrawal:</span>
                                        <span class="font-medium">
                                            {{ $this->formatCurrency($this->selectedAccountDetails->balance - $this->totalAmount) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Fee Information -->
                    <div class="mt-6 p-3 bg-blue-50 dark:bg-blue-900/50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-400 mb-2">Fee Structure</h4>
                        <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• Withdrawal fee: 1% of amount</li>
                            <li>• Minimum fee: KES 50</li>
                            <li>• Maximum fee: KES 500</li>
                            <li>• Minimum balance: KES 1,000</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 