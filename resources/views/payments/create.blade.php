<x-layouts.app :title="__('Make Payment')">
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto space-y-4 sm:space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                        Make Payment
                    </h1>
                    <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                        Process loan repayments, contributions, and SACCO fees
                    </p>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <flux:button variant="outline" size="sm" icon="arrow-left" :href="route('payments.my')" wire:navigate class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Back to Payments</span>
                        <span class="sm:hidden">Back</span>
                    </flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <!-- Payment Type Selection -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white flex items-center">
                                    Payment Type
                                    <flux:icon.information-circle class="w-4 h-4 text-zinc-400 ml-2" />
                                </h3>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                            @csrf
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="loan_repayment" id="type_loan_repayment" 
                                           class="sr-only peer" {{ $paymentType === 'loan_repayment' ? 'checked' : '' }}>
                                    <label for="type_loan_repayment" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all h-20">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <flux:icon.credit-card class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-900 dark:text-white truncate">Loan Repayment</p>
                                            <p class="text-xs text-zinc-500 truncate">Pay back your loan</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="membership_fee" id="type_membership_fee" 
                                           class="sr-only peer" {{ $paymentType === 'membership_fee' ? 'checked' : '' }}>
                                    <label for="type_membership_fee" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all h-20">
                                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <flux:icon.user-group class="w-4 h-4 text-emerald-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-900 dark:text-white truncate">Membership Fee</p>
                                            <p class="text-xs text-zinc-500 truncate">Annual SACCO dues</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="insurance_premium" id="type_insurance_premium" 
                                           class="sr-only peer" {{ $paymentType === 'insurance_premium' ? 'checked' : '' }}>
                                    <label for="type_insurance_premium" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 transition-all h-20">
                                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <flux:icon.shield-check class="w-4 h-4 text-orange-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-900 dark:text-white truncate">Insurance Premium</p>
                                            <p class="text-xs text-zinc-500 truncate">Life/credit insurance</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="relative">
                                    <input type="radio" name="payment_type" value="loan_processing_fee" id="type_loan_processing_fee" 
                                           class="sr-only peer" {{ $paymentType === 'loan_processing_fee' ? 'checked' : '' }}>
                                    <label for="type_loan_processing_fee" class="flex items-center p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all h-20">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <flux:icon.document-check class="w-4 h-4 text-purple-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-900 dark:text-white truncate">Loan Processing Fee</p>
                                            <p class="text-xs text-zinc-500 truncate">One-time loan fee</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-6">Payment Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Selection -->
                            <div>
                                <flux:field>
                                    <flux:label>Account</flux:label>
                                    <flux:select name="account_id" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_number }} - {{ $account->getDisplayName() }} (KES {{ number_format($account->balance, 2) }})
                                        </option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="account_id" />
                                </flux:field>
                            </div>

                            <!-- Loan Selection (for loan repayment) -->
                            <div id="loan_selection" style="display: none;">
                                <flux:field>
                                    <flux:label>Loan</flux:label>
                                    <flux:select name="loan_id">
                                        <option value="">Select Loan</option>
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
                                    <flux:label>Amount (KES)</flux:label>
                                    <flux:input type="number" name="amount" min="1" step="0.01" required />
                                    <flux:error name="amount" />
                                </flux:field>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <flux:field>
                                    <flux:label>Payment Method</flux:label>
                                    <flux:select name="payment_method" id="payment_method" required>
                                        <option value="">Select Payment Method</option>
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
                                        <flux:label>Mobile Number</flux:label>
                                        <flux:input type="tel" name="mobile_number" placeholder="07XXXXXXXX" />
                                        <flux:error name="mobile_number" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>Mobile Provider</flux:label>
                                        <flux:select name="mobile_provider">
                                            <option value="">Select Provider</option>
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
                                        <flux:label>Bank Name</flux:label>
                                        <flux:input type="text" name="bank_name" />
                                        <flux:error name="bank_name" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>Account Number</flux:label>
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
                                        <flux:label>Cheque Number</flux:label>
                                        <flux:input type="text" name="cheque_number" />
                                        <flux:error name="cheque_number" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>Cheque Date</flux:label>
                                        <flux:input type="date" name="cheque_date" />
                                        <flux:error name="cheque_date" />
                                    </flux:field>
                                </div>
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div class="mt-6">
                            <flux:field>
                                <flux:label>Reference Number (Optional)</flux:label>
                                <flux:input type="text" name="reference_number" placeholder="Transaction reference or receipt number" />
                                <flux:error name="reference_number" />
                            </flux:field>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <flux:field>
                                <flux:label>Description (Optional)</flux:label>
                                <flux:textarea name="description" rows="3" placeholder="Additional notes about this payment..."></flux:textarea>
                                <flux:error name="description" />
                            </flux:field>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                            <flux:button variant="outline" type="button" onclick="history.back()">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Process Payment
                            </flux:button>
                        </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Payment Summary -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-4">Payment Summary</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">Account Balance</span>
                                <span class="font-medium text-zinc-900 dark:text-white" id="account-balance">
                                    @if($accounts->count() > 0)
                                        KSh {{ number_format($accounts->first()->balance, 2) }}
                                    @else
                                        KSh 0.00
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">Outstanding Loans</span>
                                <span class="font-medium text-zinc-900 dark:text-white">
                                    @php
                                        $totalOutstanding = $activeLoans->sum(function($loan) {
                                            return $loan->amount - $loan->amount_paid;
                                        });
                                    @endphp
                                    KSh {{ number_format($totalOutstanding, 2) }}
                                </span>
                            </div>
                            @if($activeLoans->count() > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">Next Payment Due</span>
                                <span class="font-medium text-amber-600">
                                    @php
                                        $nextPayment = $activeLoans->first()->calculateMonthlyPayment();
                                    @endphp
                                    KSh {{ number_format($nextPayment, 2) }}
                                </span>
                            </div>
                            @endif
                            
                            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                <div class="flex justify-between">
                                    <span class="font-medium text-zinc-900 dark:text-white">Payment Amount</span>
                                    <span class="font-bold text-emerald-600" id="payment-amount">KSh 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl sm:rounded-2xl p-4 sm:p-6">
                        <div class="flex items-start">
                            <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" />
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                <p class="font-medium mb-2">Payment Processing Information</p>
                                <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                                    <li class="flex items-start">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        <span>Cash payments are processed immediately</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        <span>Mobile money payments may take up to 5 minutes to reflect</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        <span>Bank transfers may take 1-2 business days to process</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        <span>Large withdrawals may require approval</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white mb-4">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <flux:button variant="outline" class="w-full justify-start text-sm" icon="eye" :href="route('payments.my')" wire:navigate>
                                View Payment History
                            </flux:button>
                            <flux:button variant="outline" class="w-full justify-start text-sm" icon="document-arrow-down">
                                Download Statement
                            </flux:button>
                            <flux:modal.trigger name="payment-calculator-modal">
                                <flux:button variant="outline" class="w-full justify-start text-sm" icon="calculator">
                                    Payment Calculator
                                </flux:button>
                            </flux:modal.trigger>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Calculator Modal -->
    <flux:modal name="payment-calculator-modal" class="max-w-2xl">
        <div class="space-y-6">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.calculator class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="mt-3">
                    <flux:heading size="lg">Payment Calculator</flux:heading>
                    <div class="mt-2">
                        <flux:subheading>
                            Calculate loan payments, interest, and repayment schedules
                        </flux:subheading>
                    </div>
                </div>
            </div>

            <!-- Calculator Type -->
            <div>
                <flux:field>
                    <flux:label>Calculation Type</flux:label>
                    <flux:select id="calc-type">
                        <option value="loan_payment">Loan Payment Breakdown</option>
                        <option value="payment_plan">Payment Plan</option>
                        <option value="interest_calculator">Interest Calculator</option>
                    </flux:select>
                </flux:field>
            </div>

            <!-- Loan Payment Calculator -->
            <div id="loan-calc" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Loan Amount</flux:label>
                        <flux:input type="number" id="calc-loan-amount" placeholder="0.00" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Interest Rate (%)</flux:label>
                        <flux:input type="number" id="calc-interest-rate" placeholder="15.0" step="0.1" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Term (Months)</flux:label>
                        <flux:input type="number" id="calc-term" placeholder="12" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Processing Fee (%)</flux:label>
                        <flux:input type="number" id="calc-processing-fee" placeholder="2.0" step="0.1" />
                    </flux:field>
                </div>

                <div class="flex gap-3">
                    <flux:button variant="primary" class="flex-1" onclick="calculatePayment()">
                        <flux:icon.calculator class="w-4 h-4 mr-2" />
                        Calculate
                    </flux:button>
                    <flux:button variant="outline" onclick="clearCalculator()">
                        Clear
                    </flux:button>
                </div>

                <!-- Results -->
                <div id="calc-results" class="hidden">
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                        <h4 class="font-medium text-zinc-900 dark:text-white mb-3">Payment Breakdown</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="text-center">
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs block">Monthly Payment</span>
                                <div class="font-semibold text-lg text-blue-600" id="calc-monthly-payment">KSh 0</div>
                            </div>
                            <div class="text-center">
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs block">Total Interest</span>
                                <div class="font-semibold text-lg text-orange-600" id="calc-total-interest">KSh 0</div>
                            </div>
                            <div class="text-center">
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs block">Processing Fee</span>
                                <div class="font-semibold text-lg text-purple-600" id="calc-processing-cost">KSh 0</div>
                            </div>
                            <div class="text-center">
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs block">Total Repayment</span>
                                <div class="font-semibold text-lg text-green-600" id="calc-total-repayment">KSh 0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1">
                        Close
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
            const loanSelection = document.getElementById('loan_selection');
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentMethodFields = document.querySelectorAll('.payment-method-fields');
            const amountInput = document.querySelector('input[name="amount"]');
            const accountSelect = document.querySelector('select[name="account_id"]');

            // Handle payment type changes
            paymentTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'loan_repayment' || this.value === 'loan_processing_fee') {
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

            // Update payment amount in summary
            if (amountInput) {
                amountInput.addEventListener('input', function() {
                    const amount = parseFloat(this.value) || 0;
                    document.getElementById('payment-amount').textContent = 'KSh ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                });
            }

            // Update account balance when account changes
            if (accountSelect) {
                accountSelect.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    if (option.value) {
                        // Extract balance from option text (format: "123 - Name (KES 1,234.56)")
                        const balanceMatch = option.text.match(/KES ([\d,]+\.?\d*)/);
                        if (balanceMatch) {
                            const balance = balanceMatch[1];
                            document.getElementById('account-balance').textContent = 'KSh ' + balance;
                        }
                    }
                });
            }

            // Initialize form based on current payment type
            const checkedPaymentType = document.querySelector('input[name="payment_type"]:checked');
            if (checkedPaymentType && (checkedPaymentType.value === 'loan_repayment' || checkedPaymentType.value === 'loan_processing_fee')) {
                loanSelection.style.display = 'block';
                loanSelection.querySelector('select').required = true;
            }
        });

        // Payment Calculator Functions
        function clearCalculator() {
            document.getElementById('calc-loan-amount').value = '';
            document.getElementById('calc-interest-rate').value = '';
            document.getElementById('calc-term').value = '';
            document.getElementById('calc-processing-fee').value = '';
            document.getElementById('calc-results').classList.add('hidden');
        }

        function calculatePayment() {
            const amount = parseFloat(document.getElementById('calc-loan-amount').value) || 0;
            const interestRate = parseFloat(document.getElementById('calc-interest-rate').value) || 15;
            const term = parseInt(document.getElementById('calc-term').value) || 12;
            const processingFeeRate = parseFloat(document.getElementById('calc-processing-fee').value) || 2;

            if (amount <= 0) {
                alert('Please enter a valid loan amount');
                return;
            }

            // Calculate monthly payment using PMT formula
            const monthlyRate = interestRate / 100 / 12;
            const monthlyPayment = amount * (monthlyRate * Math.pow(1 + monthlyRate, term)) / (Math.pow(1 + monthlyRate, term) - 1);
            const totalRepayment = monthlyPayment * term;
            const totalInterest = totalRepayment - amount;
            const processingFee = amount * (processingFeeRate / 100);

            // Update display
            document.getElementById('calc-monthly-payment').textContent = 'KSh ' + monthlyPayment.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('calc-total-interest').textContent = 'KSh ' + totalInterest.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('calc-processing-cost').textContent = 'KSh ' + processingFee.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('calc-total-repayment').textContent = 'KSh ' + (totalRepayment + processingFee).toLocaleString('en-US', {maximumFractionDigits: 0});

            document.getElementById('calc-results').classList.remove('hidden');
        }
    </script>
</x-layouts.app> 