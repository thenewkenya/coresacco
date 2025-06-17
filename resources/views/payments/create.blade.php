<x-layouts.app :title="__('Make Payment')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Make Payment') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Process deposits, withdrawals, and loan repayments') }}
                        </p>
                    </div>
                    <flux:button variant="ghost" :href="route('payments.my')" wire:navigate>
                        {{ __('Back to Payments') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-3xl mx-auto">
                <!-- Payment Form -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                        @csrf
                        
                        <!-- Payment Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">
                                {{ __('Payment Type') }}
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="deposit" id="type_deposit" 
                                           class="sr-only peer" {{ $paymentType === 'deposit' ? 'checked' : '' }}>
                                    <label for="type_deposit" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg mr-3">
                                            <flux:icon.arrow-down class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Deposit') }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Add money to account') }}</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="withdrawal" id="type_withdrawal" 
                                           class="sr-only peer" {{ $paymentType === 'withdrawal' ? 'checked' : '' }}>
                                    <label for="type_withdrawal" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                                            <flux:icon.arrow-up class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Withdrawal') }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Take money out') }}</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="loan_repayment" id="type_loan_repayment" 
                                           class="sr-only peer" {{ $paymentType === 'loan_repayment' ? 'checked' : '' }}>
                                    <label for="type_loan_repayment" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                            <flux:icon.credit-card class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Loan Repayment') }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Pay back loan') }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Selection -->
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Account') }}</flux:label>
                                    <flux:select name="account_id" required>
                                        <option value="">{{ __('Select Account') }}</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_number }} - {{ ucfirst($account->account_type) }} (KES {{ number_format($account->balance, 2) }})
                                        </option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="account_id" />
                                </flux:field>
                            </div>

                            <!-- Loan Selection (for loan repayment) -->
                            <div id="loan_selection" style="display: none;">
                                <flux:field>
                                    <flux:label>{{ __('Loan') }}</flux:label>
                                    <flux:select name="loan_id">
                                        <option value="">{{ __('Select Loan') }}</option>
                                        @foreach($activeLoans as $loan)
                                        <option value="{{ $loan->id }}">
                                            {{ $loan->loanType->name }} - KES {{ number_format($loan->amount, 2) }} 
                                            ({{ ucfirst($loan->status) }})
                                        </option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="loan_id" />
                                </flux:field>
                            </div>

                            <!-- Amount -->
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Amount (KES)') }}</flux:label>
                                    <flux:input type="number" name="amount" min="1" step="0.01" required />
                                    <flux:error name="amount" />
                                </flux:field>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Payment Method') }}</flux:label>
                                    <flux:select name="payment_method" id="payment_method" required>
                                        <option value="">{{ __('Select Payment Method') }}</option>
                                        @foreach($paymentMethods as $key => $method)
                                        <option value="{{ $key }}">{{ $method }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="payment_method" />
                                </flux:field>
                            </div>
                        </div>

                        <!-- Payment Method Specific Fields -->
                        
                        <!-- Mobile Money Fields -->
                        <div id="mobile_money_fields" class="payment-method-fields" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Mobile Number') }}</flux:label>
                                        <flux:input type="tel" name="mobile_number" placeholder="07XXXXXXXX" />
                                        <flux:error name="mobile_number" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Mobile Provider') }}</flux:label>
                                        <flux:select name="mobile_provider">
                                            <option value="">{{ __('Select Provider') }}</option>
                                            <option value="mpesa">M-Pesa (Safaricom)</option>
                                            <option value="airtel_money">Airtel Money</option>
                                            <option value="tkash">T-Kash (Telkom)</option>
                                        </flux:select>
                                        <flux:error name="mobile_provider" />
                                    </flux:field>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Fields -->
                        <div id="bank_transfer_fields" class="payment-method-fields" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Bank Name') }}</flux:label>
                                        <flux:input type="text" name="bank_name" />
                                        <flux:error name="bank_name" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Account Number') }}</flux:label>
                                        <flux:input type="text" name="bank_account" />
                                        <flux:error name="bank_account" />
                                    </flux:field>
                                </div>
                            </div>
                        </div>

                        <!-- Cheque Fields -->
                        <div id="cheque_fields" class="payment-method-fields" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Cheque Number') }}</flux:label>
                                        <flux:input type="text" name="cheque_number" />
                                        <flux:error name="cheque_number" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>{{ __('Cheque Date') }}</flux:label>
                                        <flux:input type="date" name="cheque_date" />
                                        <flux:error name="cheque_date" />
                                    </flux:field>
                                </div>
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div class="mt-6">
                            <flux:field>
                                <flux:label>{{ __('Reference Number (Optional)') }}</flux:label>
                                <flux:input type="text" name="reference_number" placeholder="Transaction reference or receipt number" />
                                <flux:error name="reference_number" />
                            </flux:field>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <flux:field>
                                <flux:label>{{ __('Description (Optional)') }}</flux:label>
                                <flux:textarea name="description" rows="3" placeholder="Additional notes about this payment..."></flux:textarea>
                                <flux:error name="description" />
                            </flux:field>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <flux:button variant="ghost" type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                {{ __('Process Payment') }}
                            </flux:button>
                        </div>
                    </form>
                </div>

                <!-- Payment Information -->
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" />
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium mb-1">{{ __('Payment Processing Information') }}</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-300">
                                <li>{{ __('Cash payments are processed immediately') }}</li>
                                <li>{{ __('Mobile money payments may take up to 5 minutes to reflect') }}</li>
                                <li>{{ __('Bank transfers may take 1-2 business days to process') }}</li>
                                <li>{{ __('Large withdrawals may require approval') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
            const loanSelection = document.getElementById('loan_selection');
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentMethodFields = document.querySelectorAll('.payment-method-fields');

            // Handle payment type changes
            paymentTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'loan_repayment') {
                        loanSelection.style.display = 'block';
                        loanSelection.querySelector('select').required = true;
                    } else {
                        loanSelection.style.display = 'none';
                        loanSelection.querySelector('select').required = false;
                    }
                });
            });

            // Handle payment method changes
            paymentMethodSelect.addEventListener('change', function() {
                // Hide all payment method fields
                paymentMethodFields.forEach(field => {
                    field.style.display = 'none';
                    // Remove required attribute from all fields in hidden sections
                    field.querySelectorAll('input, select').forEach(input => {
                        input.required = false;
                    });
                });

                // Show relevant fields based on selected payment method
                if (this.value) {
                    const targetFields = document.getElementById(this.value + '_fields');
                    if (targetFields) {
                        targetFields.style.display = 'block';
                        // Add required attribute to relevant fields
                        if (this.value === 'mobile_money') {
                            targetFields.querySelector('input[name="mobile_number"]').required = true;
                            targetFields.querySelector('select[name="mobile_provider"]').required = true;
                        } else if (this.value === 'bank_transfer') {
                            targetFields.querySelector('input[name="bank_name"]').required = true;
                            targetFields.querySelector('input[name="bank_account"]').required = true;
                        } else if (this.value === 'cheque') {
                            targetFields.querySelector('input[name="cheque_number"]').required = true;
                            targetFields.querySelector('input[name="cheque_date"]').required = true;
                        }
                    }
                }
            });

            // Initialize form based on current payment type
            const checkedPaymentType = document.querySelector('input[name="payment_type"]:checked');
            if (checkedPaymentType && checkedPaymentType.value === 'loan_repayment') {
                loanSelection.style.display = 'block';
                loanSelection.querySelector('select').required = true;
            }
        });
    </script>
</x-layouts.app> 