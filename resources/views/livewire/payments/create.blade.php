<?php

use App\Models\Account;
use App\Models\Loan;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $paymentType = 'loan_repayment';
    public $accountId = '';
    public $loanId = '';
    public $amount = '';
    public $paymentMethod = '';
    public $description = '';
    public $phoneNumber = '';
    public $reference = '';

    public $accounts = [];
    public $activeLoans = [];
    public $paymentMethods = [];

    public function mount()
    {
        $user = auth()->user();
        
        // Get member's accounts
        $this->accounts = $user->accounts()->where('status', Account::STATUS_ACTIVE)->get();
        
        // Get active loans for loan repayment
        $this->activeLoans = $user->loans()->where('status', Loan::STATUS_ACTIVE)->get();

        // Payment methods
        $this->paymentMethods = [
            'cash' => 'Cash',
            'mpesa' => 'M-Pesa',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
        ];

        // Set default values
        if ($this->activeLoans->count() > 0) {
            $this->loanId = $this->activeLoans->first()->id;
        }
        if ($this->accounts->count() > 0) {
            $this->accountId = $this->accounts->first()->id;
        }
    }

    public function getSelectedLoan()
    {
        return $this->activeLoans->find($this->loanId);
    }

    public function getSelectedAccount()
    {
        return $this->accounts->find($this->accountId);
    }

    public function getMonthlyPayment()
    {
        $loan = $this->getSelectedLoan();
        return $loan ? $loan->calculateMonthlyPayment() : 0;
    }

    public function submit()
    {
        $this->validate([
            'paymentType' => 'required|in:loan_repayment,contribution,fee',
            'accountId' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|in:cash,mpesa,bank_transfer,cheque',
            'description' => 'nullable|string|max:500',
            'phoneNumber' => 'required_if:paymentMethod,mpesa|nullable|string|max:15',
            'reference' => 'nullable|string|max:100',
        ]);

        if ($this->paymentType === 'loan_repayment') {
            $this->validate([
                'loanId' => 'required|exists:loans,id',
            ]);
        }

        // Process payment logic would go here
        session()->flash('success', 'Payment processed successfully!');
        return redirect()->route('payments.my');
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Make Payment</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Process loan repayments, contributions, and SACCO fees</flux:subheading>
        </div>
        <flux:button variant="ghost" :href="route('payments.my')" icon="arrow-left">
            Back to Payments
        </flux:button>
    </div>

    <!-- Payment Type Selection -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <flux:icon.credit-card class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <flux:heading size="base" class="dark:text-zinc-100">Payment Type</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Choose the type of payment you want to make</flux:subheading>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="relative cursor-pointer">
                <input type="radio" wire:model="paymentType" value="loan_repayment" class="sr-only peer">
                <div class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                        <flux:icon.credit-card class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Loan Repayment</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Pay back your loan</div>
                    </div>
                </div>
            </label>

            <label class="relative cursor-pointer">
                <input type="radio" wire:model="paymentType" value="contribution" class="sr-only peer">
                <div class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition-all">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                        <flux:icon.banknotes class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Contribution</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Make a savings contribution</div>
                    </div>
                </div>
            </label>

            <label class="relative cursor-pointer">
                <input type="radio" wire:model="paymentType" value="fee" class="sr-only peer">
                <div class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                        <flux:icon.document-text class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">SACCO Fee</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Pay membership or service fees</div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <!-- Payment Form -->
    <form wire:submit="submit" class="space-y-6">
        <!-- Account Selection -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.building-library class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Payment Source</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Choose which account to deduct from</flux:subheading>
                </div>
            </div>

            <flux:field>
                <flux:label>Pay From Account</flux:label>
                <flux:select wire:model="accountId" required>
                    <option value="">Select your account...</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->account_number }} - {{ $account->getDisplayName() }} (KES {{ number_format($account->balance, 2) }})
                        </option>
                    @endforeach
                </flux:select>
                @if($selectedAccount = $this->getSelectedAccount())
                    <flux:subheading class="dark:text-zinc-400">Available balance: KES {{ number_format($selectedAccount->balance, 2) }}</flux:subheading>
                @endif
                @error('accountId')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Loan Selection (for loan repayments) -->
        @if($paymentType === 'loan_repayment')
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <flux:icon.credit-card class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Loan Details</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Select the loan you want to repay</flux:subheading>
                    </div>
                </div>

                <flux:field>
                    <flux:label>Select Loan</flux:label>
                    <flux:select wire:model="loanId" required>
                        <option value="">Choose a loan...</option>
                        @foreach($activeLoans as $loan)
                            <option value="{{ $loan->id }}">
                                {{ $loan->loanType->name }} - KES {{ number_format($loan->amount) }} ({{ $loan->term_period }} months)
                            </option>
                        @endforeach
                    </flux:select>
                    @if($selectedLoan = $this->getSelectedLoan())
                        <div class="mt-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Monthly Payment:</span>
                                    <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($this->getMonthlyPayment()) }}</span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Interest Rate:</span>
                                    <span class="font-bold text-blue-900 dark:text-blue-100">{{ $selectedLoan->interest_rate }}%</span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Remaining Term:</span>
                                    <span class="font-bold text-blue-900 dark:text-blue-100">{{ $selectedLoan->term_period }} months</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('loanId')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        @endif

        <!-- Payment Details -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Payment Details</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Enter payment amount and method</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Payment Amount (KES)</flux:label>
                    <flux:input type="number" wire:model="amount" min="1" step="100" required />
                    @if($paymentType === 'loan_repayment' && $this->getMonthlyPayment() > 0)
                        <flux:subheading class="dark:text-zinc-400">Suggested: KES {{ number_format($this->getMonthlyPayment()) }}</flux:subheading>
                    @endif
                    @error('amount')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Payment Method</flux:label>
                    <flux:select wire:model="paymentMethod" required>
                        <option value="">Select payment method...</option>
                        @foreach($paymentMethods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    @error('paymentMethod')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                @if($paymentMethod === 'mpesa')
                    <flux:field class="md:col-span-2">
                        <flux:label>M-Pesa Phone Number</flux:label>
                        <flux:input type="tel" wire:model="phoneNumber" placeholder="07XXXXXXXX" required />
                        <flux:subheading class="dark:text-zinc-400">Enter your M-Pesa registered phone number</flux:subheading>
                        @error('phoneNumber')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                @endif

                <flux:field class="md:col-span-2">
                    <flux:label>Description (Optional)</flux:label>
                    <flux:textarea wire:model="description" rows="2" placeholder="Add a note about this payment..." />
                    @error('description')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>Reference Number (Optional)</flux:label>
                    <flux:input wire:model="reference" placeholder="Enter reference number if any" />
                    @error('reference')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        </div>

        <!-- Payment Summary -->
        @if($amount && $selectedAccount = $this->getSelectedAccount())
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Payment Summary</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Review your payment details</flux:subheading>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-blue-700 dark:text-blue-300">Payment Amount:</span>
                        <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($amount) }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700 dark:text-blue-300">From Account:</span>
                        <span class="font-bold text-blue-900 dark:text-blue-100">{{ $selectedAccount->getDisplayName() }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700 dark:text-blue-300">Available Balance:</span>
                        <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($selectedAccount->balance, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700 dark:text-blue-300">After Payment:</span>
                        <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($selectedAccount->balance - $amount, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4">
            <flux:button variant="ghost" :href="route('payments.my')">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="credit-card">
                Process Payment
            </flux:button>
        </div>
    </form>
</div>

