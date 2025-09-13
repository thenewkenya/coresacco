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
    public $showPaymentModal = false;
    
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
            'phoneNumber' => 'required|string|min:10|max:15',
            'mobileMoneyProvider' => 'required|in:mpesa,airtel,tkash',
        ]);

        try {
            $account = Account::findOrFail($this->account_id);
            
            // Check authorization
            if ($account->member_id !== auth()->id() && !auth()->user()->hasAnyRole(['admin', 'manager', 'staff'])) {
                $this->addError('general', 'Unauthorized access to this account.');
                return;
            }

            // Initiate real mobile money payment
            $mobileMoneyService = app(\App\Services\MobileMoneyService::class);

            $result = match ($this->mobileMoneyProvider) {
                'mpesa' => $mobileMoneyService->initiateMpesaPayment($account, (float) $this->amount, $this->phoneNumber),
                'airtel' => ['success' => false, 'message' => 'Airtel Money not enabled'],
                'tkash' => ['success' => false, 'message' => 'T-Kash not enabled'],
                default => ['success' => false, 'message' => 'Invalid mobile money provider']
            };

            if (!($result['success'] ?? false)) {
                $this->addError('general', $result['message'] ?? 'Failed to initiate payment.');
                return;
            }

            // Use real transaction id for polling
            $this->transactionId = (string) ($result['transaction_id'] ?? '');
            $this->paymentStatus = 'pending';
            $this->showPaymentModal = true;

            session()->flash('success', $result['message'] ?? 'M-Pesa payment initiated. Please check your phone for the payment prompt.');

            // Start polling for payment status
            $this->dispatch('start-payment-polling', [
                'transactionId' => $this->transactionId,
                'provider' => $this->mobileMoneyProvider
            ]);
            
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    public function completeMockPayment()
    {
        $this->paymentStatus = 'completed';
        $this->showPaymentModal = false;
        session()->flash('success', 'Payment completed successfully!');
    }

    public function cancelMockPayment()
    {
        $this->paymentStatus = 'failed';
        $this->transactionId = '';
        $this->showPaymentModal = false;
        session()->flash('error', 'Payment was cancelled.');
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function preventModalClose()
    {
        // Do nothing - this prevents the modal from closing
        // The modal should only close via explicit actions
    }

    public function handlePaymentConfirmation($data = [])
    {
        $this->paymentStatus = 'completed';
        session()->flash('success', 'Mobile money payment completed successfully! Funds have been added to your account.');
        
        // Get the transaction to redirect to receipt
        if ($this->transactionId) {
            $transaction = \App\Models\Transaction::find($this->transactionId);
            if ($transaction && $transaction->status === 'completed') {
                // Only redirect to receipt for completed transactions
                return $this->redirect(route('transactions.receipt', $transaction), navigate: true);
            } elseif ($transaction && $transaction->status === 'pending') {
                // For pending transactions, show success message but don't redirect to receipt
                session()->flash('info', 'Payment received! Transaction is pending approval and will be processed shortly.');
            }
        }
        
        // Fallback: reset form if no transaction found
        $this->reset(['amount', 'description', 'reference_number', 'paymentStatus', 'transactionId']);
        $this->selectedAccount = Account::find($this->account_id); // Refresh account data
    }

    public function handlePaymentFailure($data = [])
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

<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('transactions.index') }}" class="p-2 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                    <flux:icon.arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Process Deposit</flux:heading>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Add funds to member accounts securely</flux:subheading>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                <flux:icon.shield-check class="w-4 h-4" />
                <span>Secure Transaction</span>
            </div>
        </div>
        
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
                                        <flux:field>
                                            <flux:label for="account_select">Select Account *</flux:label>
                                            <flux:select 
                                                wire:model.live="account_id" 
                                                id="account_select" 
                                                placeholder="Select an account"
                                            >
                                                <option value="">-- Select an account --</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ $account_id == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_number }} - {{ ucfirst($account->account_type) }} (KES {{ number_format($account->balance, 2) }})
                                                    </option>
                                                @endforeach
                                            </flux:select>
                                            <flux:error name="account_id" />
                                        </flux:field>
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
            fetch(`/transactions/${data[0].transactionId}/status`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
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
                // Don't stop polling on network errors, just log them
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
                                        <flux:field>
                                            <flux:label for="amount">Deposit Amount (KES) *</flux:label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-zinc-500 dark:text-zinc-400">KES</span>
                                                </div>
                                                <flux:input 
                                                    wire:model.live.debounce.300ms="amount"
                                                    type="number" 
                                                    id="amount" 
                                                    required
                                                    min="1" 
                                                    max="1000000" 
                                                    step="0.01" 
                                                    placeholder="0.00"
                                                    class="pl-12"
                                                />
                                            </div>
                                            <flux:description>Minimum: KES 1.00 • Maximum: KES 1,000,000.00</flux:description>
                                            <flux:error name="amount" />
                                        </flux:field>
                                        
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
                                        <flux:field>
                                            <flux:label for="payment_method">Payment Method *</flux:label>
                                            <flux:select 
                                                wire:model.live="payment_method" 
                                                id="payment_method" 
                                                placeholder="Select payment method"
                                            >
                                                @foreach($paymentMethods as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </flux:select>
                                            <flux:error name="payment_method" />
                                        </flux:field>
                                    </div>

                                    <!-- M-Pesa Payment Details (shown when M-Pesa is selected) -->
                                    @if($payment_method === 'mpesa')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                            <div>
                                                <flux:field>
                                                    <flux:label>M-Pesa Phone Number</flux:label>
                                                    <flux:input 
                                                        wire:model.live.debounce.300ms="phoneNumber"
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
                                                        wire:model.live.debounce.300ms="bank_name"
                                                        type="text" 
                                                        placeholder="e.g., Equity Bank" />
                                                    <flux:error name="bank_name" />
                                                </flux:field>
                                            </div>
                                            <div>
                                                <flux:field>
                                                    <flux:label>Account Number</flux:label>
                                                    <flux:input 
                                                        wire:model.live.debounce.300ms="bank_account"
                                                        type="text" 
                                                        placeholder="e.g., 1234567890" />
                                                    <flux:error name="bank_account" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Reference Number -->
                                    <div>
                                        <flux:field>
                                            <flux:label for="reference_number">Reference Number (Optional)</flux:label>
                                            <div class="flex gap-2">
                                                <flux:input 
                                                    wire:model.blur="reference_number"
                                                    type="text" 
                                                    id="reference_number" 
                                                    placeholder="Optional external reference"
                                                    class="flex-1"
                                                />
                                                <flux:button 
                                                    type="button"
                                                    variant="outline"
                                                    wire:click="generateReference"
                                                >
                                                    Generate
                                                </flux:button>
                                            </div>
                                            <flux:error name="reference_number" />
                                        </flux:field>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <flux:field>
                                            <flux:label for="description">Description (Optional)</flux:label>
                                            <flux:textarea 
                                                wire:model.blur="description"
                                                id="description" 
                                                rows="3" 
                                                placeholder="Enter a description for this deposit..."
                                            >{{ old('description') }}</flux:textarea>
                                            <flux:error name="description" />
                                        </flux:field>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between">
                            <flux:button variant="ghost" :href="route('transactions.index')">
                                Cancel
                            </flux:button>
                            
                            @if($payment_method === 'mpesa')
                                <flux:button 
                                    wire:click="processDeposit" 
                                    type="button"
                                    :disabled="!$account_id || !$amount || !$phoneNumber || $paymentStatus === 'pending'"
                                    variant="primary"
                                    icon="phone"
                                >
                                    @if($paymentStatus === 'pending')
                                        Processing...
                                    @else
                                        Pay with M-Pesa
                                    @endif
                                </flux:button>
                            @else
                                <flux:button 
                                    wire:click="processDeposit" 
                                    type="button"
                                    :disabled="!$account_id || !$amount"
                                    variant="primary"
                                    icon="arrow-down"
                                >
                                    Process Deposit
                                </flux:button>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Transaction Summary & Info -->
                <div class="space-y-6">
                    <!-- Transaction Summary -->
                    @if($selectedAccount && $amount)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Transaction Summary</h3>
                                <flux:badge variant="primary">Draft</flux:badge>
                            </div>
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
                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">+ KES {{ number_format((float) $amount, 2) }}</span>
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
                                <div class="flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-3">
                                    <div class="flex items-center space-x-2">
                                        <flux:badge variant="success">Calculated</flux:badge>
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">Balance After Deposit</span>
                                    </div>
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">KES {{ number_format($selectedAccount->balance + (float) $amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Deposit Limits -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Deposit Limits</h3>
                            <flux:badge variant="secondary">Info</flux:badge>
                        </div>
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
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Need Help?</h3>
                            <flux:badge variant="primary">Support</flux:badge>
                        </div>
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

    <!-- M-Pesa Payment Modal -->
    <flux:modal wire:model="showPaymentModal" class="md:w-7xl max-h-[80vh]" @click.away="preventModalClose">
        <div class="space-y-4">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <flux:icon.phone class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <flux:heading size="lg" class="dark:text-zinc-100">M-Pesa Payment</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Please check your phone for the payment prompt</flux:subheading>
            </div>

            <!-- Payment Information -->
            <div class="space-y-4">
                <flux:heading size="base" class="dark:text-zinc-100">Payment Details</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Amount Card - Most Important -->
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Payment Amount</div>
                            <flux:icon.currency-dollar class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-200">KES {{ number_format((float) $amount, 2) }}</div>
                    </div>
                    
                    <!-- Phone Number Card -->
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">M-Pesa Number</div>
                            <flux:icon.phone class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                        </div>
                        <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $phoneNumber }}</div>
                    </div>
                    
                    <!-- Account Card -->
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Destination Account</div>
                            <flux:icon.banknotes class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                        </div>
                        <div class="space-y-1">
                            <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                @if($selectedAccount)
                                    {{ $selectedAccount->account_name }}
                                @endif
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400 font-mono">
                                @if($selectedAccount)
                                    {{ $selectedAccount->account_number }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction ID Card -->
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Transaction ID</div>
                            <flux:icon.document-text class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                        </div>
                        <div class="font-mono text-base font-semibold text-zinc-900 dark:text-zinc-100 break-all">{{ $transactionId }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                        <div class="text-base font-medium text-zinc-900 dark:text-zinc-100">Waiting for Payment</div>
                    </div>
                </div>
                <div class="text-center mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Check your phone and enter your M-Pesa PIN to complete the payment
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button 
                    wire:click="cancelMockPayment" 
                    variant="ghost">
                    Cancel Payment
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div> 