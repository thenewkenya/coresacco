<?php

use App\Models\Account;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    
    protected $listeners = [
        'paymentConfirmed' => 'handlePaymentConfirmation',
        'paymentFailed' => 'handlePaymentFailure',
    ];
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
    
    // M-Pesa properties
    public $mobileMoneyProvider = 'mpesa';
    public $phoneNumber = '';
    public $paymentStatus = '';
    public $transactionId = '';
    public $showMobileMoneyForm = false;
    
    // Bank Transfer properties
    public $bank_name = '';
    public $bank_account = '';
    
    public $paymentMethods = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'mpesa' => 'M-Pesa',
    ];
    
    public $mobileMoneyProviders = [
        'mpesa' => ['name' => 'M-Pesa', 'icon' => '', 'enabled' => true],
        'airtel' => ['name' => 'Airtel Money', 'icon' => '❤️', 'enabled' => true],
        'tkash' => ['name' => 'T-Kash', 'icon' => '🧡', 'enabled' => true],
    ];



    public function mount()
    {
        // Check if an account ID was passed in the URL
        $accountId = request()->get('account');
        
        // For now, allow all users to search members - can be restricted later
        $this->loadMembers();
        
        // If user is not admin/staff/manager, auto-select their own account
        if (!auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            $this->member_id = auth()->id();
            $this->loadAccountsForMember();
            $this->phoneNumber = auth()->user()->phone_number ?? '';
            
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
                $this->phoneNumber = $account->member->phone_number ?? '';
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

    public function updatedPaymentMethod()
    {
        $this->showMobileMoneyForm = ($this->payment_method === 'mpesa');
        if ($this->showMobileMoneyForm) {
            $this->resetMobileMoneyState();
        }
    }

    public function resetMobileMoneyState()
    {
        $this->paymentStatus = '';
        $this->transactionId = '';
    }

    public function initiateMobileMoneyPayment()
    {
        $this->validate([
            'member_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'phoneNumber' => 'required|regex:/^(\+254|254|0)[0-9]{9}$/',
            'mobileMoneyProvider' => 'required|in:mpesa,airtel,tkash',
        ]);

        try {
            $account = Account::findOrFail($this->account_id);
            
            // Check authorization
            if ($account->member_id !== auth()->id() && !auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
                $this->addError('general', 'Unauthorized access to this account.');
                return;
            }

            $mobileMoneyService = app(\App\Services\MobileMoneyService::class);
            
            $result = match ($this->mobileMoneyProvider) {
                'mpesa' => $mobileMoneyService->initiateMpesaPayment($account, (float) $this->amount, $this->phoneNumber),
                'airtel' => $mobileMoneyService->initiateAirtelPayment($account, (float) $this->amount, $this->phoneNumber),
                'tkash' => $mobileMoneyService->initiateTkashPayment($account, (float) $this->amount, $this->phoneNumber),
                default => ['success' => false, 'message' => 'Invalid payment provider']
            };

            if ($result['success']) {
                $this->transactionId = $result['transaction_id'];
                $this->paymentStatus = 'pending';
                
                session()->flash('success', $result['message']);
                
                // Start polling for payment status
                $this->dispatch('start-payment-polling', [
                    'transactionId' => $this->transactionId,
                    'provider' => $this->mobileMoneyProvider
                ]);
            } else {
                $this->addError('general', $result['message']);
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    public function handlePaymentConfirmation($data)
    {
        $this->paymentStatus = 'completed';
        session()->flash('success', 'Mobile money payment completed successfully! Funds have been added to your account.');
        
        // Reset form
        $this->reset(['amount', 'description', 'reference_number', 'paymentStatus', 'transactionId']);
        $this->selectedAccount = Account::find($this->account_id); // Refresh account data
    }

    public function handlePaymentFailure($data)
    {
        $this->paymentStatus = 'failed';
        $this->addError('general', $data['message'] ?? 'Mobile money payment failed. Please try again.');
    }

    public function processDeposit()
    {
        // If M-Pesa is selected, use M-Pesa flow instead
        if ($this->payment_method === 'mpesa') {
            $this->initiateMobileMoneyPayment();
            return;
        }

        $validationRules = [
            'member_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255',
            'payment_method' => 'required|in:' . implode(',', array_keys($this->paymentMethods)),
            'reference_number' => 'nullable|string|max:50',
        ];

        // Add conditional validation based on payment method
        if ($this->payment_method === 'bank_transfer') {
            $validationRules['bank_name'] = 'required|string|max:255';
            $validationRules['bank_account'] = 'required|string|max:50';
        } elseif ($this->payment_method === 'mpesa') {
            $validationRules['phoneNumber'] = 'required|string|max:20';
        }

        $this->validate($validationRules);

        try {
            $account = Account::findOrFail($this->account_id);
            
            // Check authorization - simplified
            if ($account->member_id !== auth()->id() && !auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
                $this->addError('general', 'Unauthorized access to this account.');
                return;
            }

            $metadata = [
                'payment_method' => $this->payment_method,
                'external_reference' => $this->reference_number,
                'processed_by' => auth()->id(),
            ];

            // Add payment method specific metadata
            if ($this->payment_method === 'bank_transfer') {
                $metadata['bank_name'] = $this->bank_name;
                $metadata['bank_account'] = $this->bank_account;
            } elseif ($this->payment_method === 'mpesa') {
                $metadata['phone_number'] = $this->phoneNumber;
                $metadata['mobile_provider'] = $this->mobileMoneyProvider;
            }

            $description = $this->description ?: 'Deposit to ' . $account->account_type . ' account';
            
            // Use TransactionService for consistent processing
            $transactionService = app(\App\Services\TransactionService::class);
            $transaction = $transactionService->processDeposit($account, (float) $this->amount, $description, $metadata);

            // Redirect to receipt page
            return $this->redirect(route('transactions.receipt', $transaction), navigate: true);
            
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
                        @if(auth()->user()->hasAnyRole(['admin', 'manager', 'staff']))
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
                        @if($member_id)
                            @if(count($accounts) > 0)
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
                            @else
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <flux:icon.exclamation-triangle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200">
                                                No Active Accounts Found
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                                <p>You need to have at least one active account to make a deposit. Please contact the SACCO office to open an account or check if your existing accounts are active.</p>
                                            </div>
                                            <div class="mt-4">
                                                <a href="{{ route('accounts.create') }}" 
                                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-500 dark:hover:bg-yellow-600">
                                                    <flux:icon.plus class="h-4 w-4 mr-2" />
                                                    Open New Account
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

<!-- Real-time Payment Status Polling for M-Pesa -->
<script>
document.addEventListener('livewire:initialized', () => {
    let pollingInterval;
    
    Livewire.on('start-payment-polling', (data) => {
        // Clear any existing polling
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        // Start polling for payment status every 5 seconds
        pollingInterval = setInterval(() => {
            fetch(`/api/transactions/${data[0].transactionId}/status`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'completed') {
                        clearInterval(pollingInterval);
                        Livewire.dispatch('paymentConfirmed', result);
                    } else if (result.status === 'failed' || result.status === 'cancelled') {
                        clearInterval(pollingInterval);
                        Livewire.dispatch('paymentFailed', result);
                    }
                })
                .catch(error => {
                    console.error('Payment status polling error:', error);
                });
        }, 5000);
        
        // Stop polling after 5 minutes
        setTimeout(() => {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                Livewire.dispatch('paymentFailed', {
                    message: 'Payment timeout. Please check your transaction history.'
                });
            }
        }, 300000);
    });
    
    // Clean up polling when component is destroyed
    Livewire.on('component-destroyed', () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});
</script>

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
                                            wire:model.live="payment_method" 
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

                                    <!-- M-Pesa Payment Details (shown when M-Pesa is selected) -->
                                    @if($payment_method === 'mpesa')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                            <div>
                                                <flux:field>
                                                    <flux:label>M-Pesa Phone Number</flux:label>
                                                    <flux:input 
                                                        wire:model.live.debounce.500ms="phoneNumber"
                                                        type="tel" 
                                                        placeholder="07XXXXXXXX" />
                                                    <flux:error name="phoneNumber" />
                                                </flux:field>
                                            </div>
                                            <div>
                                                <flux:field>
                                                    <flux:label>Mobile Provider</flux:label>
                                                    <flux:select wire:model="mobileMoneyProvider">
                                                        <option value="mpesa">M-Pesa (Safaricom)</option>
                                                    </flux:select>
                                                    <flux:error name="mobileMoneyProvider" />
                                                </flux:field>
                                            </div>
                                        </div>

                                        <!-- Payment Status -->
                                        @if($paymentStatus === 'pending')
                                            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="animate-spin">
                                                        <flux:icon.arrow-path class="w-5 h-5 text-yellow-600" />
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-yellow-900 dark:text-yellow-100">Payment in Progress</div>
                                                        <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                                            Please complete the payment on your phone. You will receive an M-Pesa prompt.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($paymentStatus === 'completed')
                                            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <flux:icon.check-circle class="w-5 h-5 text-green-600" />
                                                    <div>
                                                        <div class="font-medium text-green-900 dark:text-green-100">Payment Completed</div>
                                                        <div class="text-sm text-green-700 dark:text-green-300">
                                                            Your deposit has been processed successfully!
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($paymentStatus === 'failed')
                                            <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <flux:icon.x-circle class="w-5 h-5 text-red-600" />
                                                    <div>
                                                        <div class="font-medium text-red-900 dark:text-red-100">Payment Failed</div>
                                                        <div class="text-sm text-red-700 dark:text-red-300">
                                                            Please try again or use a different payment method.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Bank Transfer Payment Details (shown when Bank Transfer is selected) -->
                                    @if($payment_method === 'bank_transfer')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                            <div>
                                                <flux:field>
                                                    <flux:label>Bank Name</flux:label>
                                                    <flux:input 
                                                        wire:model="bank_name"
                                                        type="text" 
                                                        placeholder="e.g., Equity Bank" />
                                                    <flux:error name="bank_name" />
                                                </flux:field>
                                            </div>
                                            <div>
                                                <flux:field>
                                                    <flux:label>Account Number</flux:label>
                                                    <flux:input 
                                                        wire:model="bank_account"
                                                        type="text" 
                                                        placeholder="e.g., 1234567890" />
                                                    <flux:error name="bank_account" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

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
                            
                            @if($payment_method === 'mpesa')
                                <button 
                                    wire:click="processDeposit" 
                                    type="button"
                                    {{ !$account_id || !$amount || !$phoneNumber || $paymentStatus === 'pending' ? 'disabled' : '' }}
                                    class="bg-green-600 hover:bg-green-700 disabled:bg-zinc-400 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                    @if($paymentStatus === 'pending')
                                        <div class="animate-spin mr-2">
                                            <flux:icon.arrow-path class="w-5 h-5" />
                                        </div>
                                        Processing...
                                    @else
                                        <span class="text-lg mr-2">{{ $mobileMoneyProviders[$mobileMoneyProvider]['icon'] }}</span>
                                        Pay with {{ $mobileMoneyProviders[$mobileMoneyProvider]['name'] }}
                                    @endif
                                </button>
                            @else
                                <button 
                                    wire:click="processDeposit" 
                                    type="button"
                                    {{ !$account_id || !$amount ? 'disabled' : '' }}
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                    <flux:icon.arrow-down class="w-5 h-5 mr-2" />
                                    Process Deposit
                                </button>
                            @endif
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
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($payment_method === 'mpesa')
                                            {{ $mobileMoneyProviders[$mobileMoneyProvider]['icon'] }} {{ $mobileMoneyProviders[$mobileMoneyProvider]['name'] }}
                                        @else
                                            {{ $paymentMethods[$payment_method] ?? 'Cash' }}
                                        @endif
                                    </span>
                                </div>
                                @if($payment_method === 'mpesa' && $phoneNumber)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Phone Number:</span>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $phoneNumber }}</span>
                                    </div>
                                @endif
                                @if($payment_method === 'bank_transfer' && $bank_name)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Bank Name:</span>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $bank_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Number:</span>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $bank_account }}</span>
                                    </div>
                                @endif
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