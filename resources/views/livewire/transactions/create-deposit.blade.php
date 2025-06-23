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
        // For now, allow all users to search members - can be restricted later
        $this->loadMembers();
        
        // If user is not admin, auto-select their own account
        if (!in_array(auth()->user()->email, ['admin@sacco.com'])) { // Simple admin check
            $this->member_id = auth()->id();
            $this->loadAccountsForMember();
        }
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

            // Create transaction record
            $transaction = \App\Models\Transaction::create([
                'member_id' => $account->member_id,
                'account_id' => $account->id,
                'type' => 'deposit',
                'amount' => $this->amount,
                'balance_before' => $account->balance,
                'balance_after' => $account->balance + $this->amount,
                'description' => $this->description ?: 'Deposit to ' . $account->account_type . ' account',
                'reference_number' => 'DEP' . time() . rand(1000, 9999),
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'metadata' => $metadata,
            ]);

            // Update account balance
            $account->increment('balance', $this->amount);

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
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ __('Process Deposit') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Add funds to member accounts') }}
                    </p>
                </div>
                <flux:button variant="ghost" :href="route('transactions.index')" wire:navigate>
                    {{ __('Back') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto">
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

                        <div class="space-y-3">
                            @foreach($accounts as $account)
                                <div 
                                    wire:click="selectAccount({{ $account->id }})"
                                    class="border border-zinc-200 dark:border-zinc-600 rounded-lg p-4 cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors {{ $account_id == $account->id ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full border-2 border-zinc-300 dark:border-zinc-600 {{ $account_id == $account->id ? 'border-blue-500 dark:border-blue-400' : '' }} flex items-center justify-center">
                                            @if($account_id == $account->id)
                                                <div class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ ucfirst($account->account_type) }} Account
                                            </div>
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $account->account_number }} • Balance: KES {{ number_format($account->balance) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Deposit Details -->
                @if($account_id)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Deposit Details') }}
                        </h3>

                        <div class="space-y-4">
                            <flux:field>
                                <flux:label>{{ __('Amount (KES)') }}</flux:label>
                                <flux:input 
                                    wire:model.live="amount"
                                    type="number"
                                    required
                                    min="1"
                                    max="1000000"
                                    step="1"
                                    placeholder="0.00" />
                                <flux:description>
                                    {{ __('Enter the amount to deposit') }}
                                </flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Payment Method') }}</flux:label>
                                <flux:select wire:model="payment_method" required>
                                    @foreach($paymentMethods as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Reference Number') }}</flux:label>
                                <div class="flex gap-2">
                                    <flux:input 
                                        wire:model="reference_number"
                                        type="text"
                                        placeholder="{{ __('Optional external reference') }}"
                                        class="flex-1" />
                                    <flux:button 
                                        type="button"
                                        wire:click="generateReference"
                                        variant="outline"
                                        size="sm">
                                        {{ __('Generate') }}
                                    </flux:button>
                                </div>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Description') }}</flux:label>
                                <flux:textarea 
                                    wire:model="description"
                                    rows="3"
                                    placeholder="{{ __('Optional description for this deposit...') }}"></flux:textarea>
                            </flux:field>
                        </div>
                    </div>
                @endif

                <!-- Transaction Summary -->
                @if($selectedAccount && $amount)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                            {{ __('Transaction Summary') }}
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-600 dark:text-blue-400">Account:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-100">
                                    {{ ucfirst($selectedAccount->account_type) }} - {{ $selectedAccount->account_number }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-600 dark:text-blue-400">Current Balance:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($selectedAccount->balance) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-600 dark:text-blue-400">Deposit Amount:</span>
                                <span class="font-medium text-green-600 dark:text-green-400">
                                    + KES {{ number_format($amount) }}
                                </span>
                            </div>
                            <div class="flex justify-between border-t border-blue-200 dark:border-blue-700 pt-3">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">New Balance:</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($selectedAccount->balance + $amount) }}
                                </span>
                            </div>
                        </div>

                        @if($amount >= 50000)
                            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <strong>⚠ Large Deposit Notice:</strong> 
                                    This deposit requires management approval and may take additional processing time.
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <flux:button variant="ghost" :href="route('transactions.index')" wire:navigate>
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button 
                        variant="primary" 
                        type="submit" 
                        :disabled="!$account_id || !$amount">
                        {{ __('Process Deposit') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div> 