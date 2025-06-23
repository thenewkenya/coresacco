<x-layouts.app :title="__('Apply for Loan')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            @role('member')
                                {{ __('Apply for Loan') }}
                            @else
                                {{ __('Create Loan Application') }}
                            @endrole
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            @role('member')
                                {{ __('Submit your loan application for review') }}
                            @elserole('staff')
                                {{ __('Create loan application on behalf of member') }}
                            @else
                                {{ __('Process new loan applications with advanced options') }}
                            @endrole
                        </p>
                    </div>
                    <flux:button variant="ghost" :href="route('loans.index')" icon="arrow-left">
                        {{ __('Back to Loans') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <form method="POST" action="{{ route('loans.store') }}" class="space-y-8">
                @csrf

                @roleany('admin', 'manager', 'staff')
                <!-- Member Selection (Staff/Admin only) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Member Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Select Member') }}</flux:label>
                            <flux:select name="member_id" required>
                                <option value="">{{ __('Choose a member...') }}</option>
                                @foreach($members ?? [] as $member)
                                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->email }})
                                    @if($member->member_number)
                                        - #{{ $member->member_number }}
                                    @endif
                                </option>
                                @endforeach
                            </flux:select>
                            @error('member_id')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        @role('admin')
                        <flux:field>
                            <flux:label>{{ __('Application Status') }}</flux:label>
                            <flux:select name="status">
                                <option value="pending">{{ __('Pending Review') }}</option>
                                <option value="under_review">{{ __('Under Review') }}</option>
                                <option value="approved">{{ __('Pre-approved') }}</option>
                            </flux:select>
                        </flux:field>
                        @endrole
                    </div>
                </div>
                @endroleany

                <!-- Loan Type Selection -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Type') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($loanTypes as $loanType)
                        @php
                            $isRestricted = in_array($loanType->name, ['Asset Financing', 'Development Loan']) && !auth()->user()->hasAnyRole(['admin', 'manager']);
                            $termOptions = $loanType->term_options ?? [6, 12, 18, 24, 36, 48, 60];
                            $maxTerm = max($termOptions);
                            $requirements = $loanType->requirements ?? [];
                            $collateralRequired = $requirements['collateral_required'] ?? false;
                        @endphp
                        <div class="relative">
                            <input type="radio" 
                                   id="loan_type_{{ $loanType->id }}" 
                                   name="loan_type_id" 
                                   value="{{ $loanType->id }}" 
                                   class="peer sr-only loan-type-radio"
                                   data-max-amount="{{ $loanType->maximum_amount }}"
                                   data-min-amount="{{ $loanType->minimum_amount }}"
                                   data-interest-rate="{{ $loanType->interest_rate }}"
                                   data-max-term="{{ $maxTerm }}"
                                   data-processing-fee="{{ $loanType->processing_fee }}"
                                   data-collateral-required="{{ $collateralRequired ? 'true' : 'false' }}"
                                   {{ old('loan_type_id') == $loanType->id ? 'checked' : '' }}
                                   @if($isRestricted) disabled @endif>
                            <label for="loan_type_{{ $loanType->id }}" 
                                   class="block p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer 
                                          peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                          hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors
                                          @if($isRestricted) opacity-50 cursor-not-allowed @endif">
                                <div class="flex items-start space-x-3">
                                    @if($loanType->name === 'Personal Loan')
                                        <flux:icon.user class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @elseif($loanType->name === 'Emergency Loan')
                                        <flux:icon.exclamation-triangle class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @elseif($loanType->name === 'Development Loan')
                                        <flux:icon.building-office class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @elseif($loanType->name === 'Asset Financing')
                                        <flux:icon.truck class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @elseif($loanType->name === 'School Fees Loan')
                                        <flux:icon.academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @else
                                        <flux:icon.banknotes class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 flex items-center">
                                            {{ $loanType->name }}
                                            @if($loanType->name === 'Emergency Loan')
                                                <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded">Fast</span>
                                            @endif
                                            @if($isRestricted)
                                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded">Admin/Manager Only</span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                            {{ $loanType->description }}
                                        </p>
                                        <div class="mt-2 space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Amount Range:</span>
                                                <span class="font-medium">KES {{ number_format($loanType->minimum_amount) }} - {{ number_format($loanType->maximum_amount) }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Interest Rate:</span>
                                                <span class="font-medium text-orange-600">{{ $loanType->interest_rate }}% p.a.</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Max Term:</span>
                                                <span class="font-medium">{{ $maxTerm }} months</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Processing Fee:</span>
                                                <span class="font-medium">{{ $loanType->processing_fee }}%</span>
                                            </div>
                                            @if($collateralRequired)
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Collateral:</span>
                                                <span class="font-medium text-red-600">Required</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('loan_type_id')
                        <flux:error class="mt-2">{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Loan Details -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Loan Amount (KES)') }}</flux:label>
                            <flux:input name="amount" 
                                       type="number" 
                                       step="1000" 
                                       min="10000" 
                                       max="5000000"
                                       value="{{ old('amount') }}" 
                                       placeholder="Enter loan amount" 
                                       required 
                                       id="loan-amount" />
                            <flux:description id="amount-description">
                                Enter the amount you wish to borrow
                            </flux:description>
                            @error('amount')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Repayment Period (Months)') }}</flux:label>
                            <flux:input name="term_period" 
                                       type="number" 
                                       min="1" 
                                       max="60"
                                       value="{{ old('term_period', 12) }}" 
                                       required 
                                       id="loan-term" />
                            <flux:description id="term-description">
                                Choose repayment period (1-60 months)
                            </flux:description>
                            @error('term_period')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>

                    <!-- Loan Calculator -->
                    <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Loan Calculator') }}</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500">Monthly Payment:</span>
                                <div class="font-semibold text-lg text-blue-600" id="monthly-payment">KES 0</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">Total Interest:</span>
                                <div class="font-semibold text-lg text-orange-600" id="total-interest">KES 0</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">Processing Fee:</span>
                                <div class="font-semibold text-lg text-purple-600" id="processing-fee">KES 0</div>
                            </div>
                            <div>
                                <span class="text-zinc-500">Total Repayment:</span>
                                <div class="font-semibold text-lg text-green-600" id="total-repayment">KES 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan Purpose -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Purpose') }}
                    </h3>
                    <flux:field>
                        <flux:label>{{ __('Purpose of Loan') }}</flux:label>
                        <flux:select name="purpose" required>
                            <option value="">{{ __('Select loan purpose...') }}</option>
                            <option value="business_expansion">{{ __('Business Expansion') }}</option>
                            <option value="education">{{ __('Education') }}</option>
                            <option value="medical">{{ __('Medical Expenses') }}</option>
                            <option value="home_improvement">{{ __('Home Improvement') }}</option>
                            <option value="debt_consolidation">{{ __('Debt Consolidation') }}</option>
                            <option value="emergency">{{ __('Emergency') }}</option>
                            <option value="agriculture">{{ __('Agriculture') }}</option>
                            <option value="vehicle_purchase">{{ __('Vehicle Purchase') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </flux:select>
                        @error('purpose')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <div class="mt-4">
                        <flux:field>
                            <flux:label>{{ __('Detailed Description') }}</flux:label>
                            <flux:textarea name="purpose_description" 
                                          rows="3" 
                                          placeholder="Provide detailed information about how you plan to use the loan funds"
                                          required>{{ old('purpose_description') }}</flux:textarea>
                            @error('purpose_description')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Guarantors -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Guarantors') }}
                    </h3>
                    <div class="space-y-6" id="guarantors-section">
                        <!-- Guarantor 1 -->
                        <div class="guarantor-block p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Guarantor 1') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>{{ __('Full Name') }}</flux:label>
                                    <flux:input name="guarantor_1_name" 
                                               value="{{ old('guarantor_1_name') }}" 
                                               placeholder="Enter guarantor's full name" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('Phone Number') }}</flux:label>
                                    <flux:input name="guarantor_1_phone" 
                                               value="{{ old('guarantor_1_phone') }}" 
                                               placeholder="Enter phone number" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('ID/Passport Number') }}</flux:label>
                                    <flux:input name="guarantor_1_id_number" 
                                               value="{{ old('guarantor_1_id_number') }}" 
                                               placeholder="Enter ID or passport number" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('Relationship') }}</flux:label>
                                    <flux:select name="guarantor_1_relationship" required>
                                        <option value="">{{ __('Select relationship...') }}</option>
                                        <option value="spouse">{{ __('Spouse') }}</option>
                                        <option value="parent">{{ __('Parent') }}</option>
                                        <option value="sibling">{{ __('Sibling') }}</option>
                                        <option value="friend">{{ __('Friend') }}</option>
                                        <option value="colleague">{{ __('Colleague') }}</option>
                                        <option value="business_partner">{{ __('Business Partner') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </flux:select>
                                </flux:field>
                            </div>
                        </div>

                        <!-- Guarantor 2 -->
                        <div class="guarantor-block p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Guarantor 2') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>{{ __('Full Name') }}</flux:label>
                                    <flux:input name="guarantor_2_name" 
                                               value="{{ old('guarantor_2_name') }}" 
                                               placeholder="Enter guarantor's full name" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('Phone Number') }}</flux:label>
                                    <flux:input name="guarantor_2_phone" 
                                               value="{{ old('guarantor_2_phone') }}" 
                                               placeholder="Enter phone number" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('ID/Passport Number') }}</flux:label>
                                    <flux:input name="guarantor_2_id_number" 
                                               value="{{ old('guarantor_2_id_number') }}" 
                                               placeholder="Enter ID or passport number" 
                                               required />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('Relationship') }}</flux:label>
                                    <flux:select name="guarantor_2_relationship" required>
                                        <option value="">{{ __('Select relationship...') }}</option>
                                        <option value="spouse">{{ __('Spouse') }}</option>
                                        <option value="parent">{{ __('Parent') }}</option>
                                        <option value="sibling">{{ __('Sibling') }}</option>
                                        <option value="friend">{{ __('Friend') }}</option>
                                        <option value="colleague">{{ __('Colleague') }}</option>
                                        <option value="business_partner">{{ __('Business Partner') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </flux:select>
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collateral (for certain loan types) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6" id="collateral-section" style="display: none;">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Collateral Information') }}
                    </h3>
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Collateral Type') }}</flux:label>
                            <flux:select name="collateral_type">
                                <option value="">{{ __('Select collateral type...') }}</option>
                                <option value="vehicle">{{ __('Vehicle') }}</option>
                                <option value="property">{{ __('Property/Land') }}</option>
                                <option value="equipment">{{ __('Equipment/Machinery') }}</option>
                                <option value="savings">{{ __('Savings Account') }}</option>
                                <option value="shares">{{ __('Shares/Securities') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Collateral Description') }}</flux:label>
                            <flux:textarea name="collateral_description" 
                                          rows="3" 
                                          placeholder="Provide detailed description of the collateral">{{ old('collateral_description') }}</flux:textarea>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Estimated Value (KES)') }}</flux:label>
                            <flux:input name="collateral_value" 
                                       type="number" 
                                       step="1000" 
                                       value="{{ old('collateral_value') }}" 
                                       placeholder="Enter estimated value" />
                        </flux:field>
                    </div>
                </div>

                @role('admin')
                <!-- Admin-only Advanced Options -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-4">
                        {{ __('Administrator Options') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Custom Interest Rate (%)') }}</flux:label>
                            <flux:input name="custom_interest_rate" 
                                       type="number" 
                                       step="0.1" 
                                       placeholder="Leave empty for default rate" />
                            <flux:description>Override the default interest rate for this loan</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Processing Fee Override (%)') }}</flux:label>
                            <flux:input name="custom_processing_fee" 
                                       type="number" 
                                       step="0.1" 
                                       placeholder="Leave empty for default fee" />
                            <flux:description>Override the default processing fee</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Approval Priority') }}</flux:label>
                            <flux:select name="priority">
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High Priority') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Skip Approval Process') }}</flux:label>
                            <flux:switch name="skip_approval" value="1" />
                            <flux:description>Automatically approve this loan application</flux:description>
                        </flux:field>
                    </div>
                </div>
                @endrole

                <!-- Terms and Conditions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Terms and Conditions') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <flux:checkbox name="terms_accepted" value="1" required />
                            <label class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ __('I agree to the SACCO loan terms and conditions, including repayment schedule and interest rates') }}
                            </label>
                        </div>
                        <div class="flex items-start space-x-3">
                            <flux:checkbox name="guarantor_consent" value="1" required />
                            <label class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ __('I confirm that my guarantors have consented to guarantee this loan') }}
                            </label>
                        </div>
                        <div class="flex items-start space-x-3">
                            <flux:checkbox name="information_accuracy" value="1" required />
                            <label class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ __('I certify that all information provided is accurate and complete') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6">
                    <flux:button variant="ghost" :href="route('loans.index')">
                        {{ __('Cancel') }}
                    </flux:button>
                    <div class="flex items-center space-x-3">
                        @role('member')
                        <flux:button type="submit" name="action" value="draft" variant="outline">
                            {{ __('Save as Draft') }}
                        </flux:button>
                        @endrole
                        <flux:button type="submit" variant="primary">
                            @role('member')
                                {{ __('Submit Application') }}
                            @elseroleany('admin', 'manager')
                                {{ __('Create & Approve') }}
                            @else
                                {{ __('Create Application') }}
                            @endroleany
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Loan form JavaScript loaded');
            
            const loanTypeRadios = document.querySelectorAll('.loan-type-radio');
            const loanAmountInput = document.getElementById('loan-amount');
            const loanTermInput = document.getElementById('loan-term');
            const collateralSection = document.getElementById('collateral-section');
            
            console.log('Found elements:', {
                loanTypeRadios: loanTypeRadios.length,
                loanAmountInput: !!loanAmountInput,
                loanTermInput: !!loanTermInput,
                collateralSection: !!collateralSection
            });
            
            let currentLoanType = null;

            // Loan calculator function
            function calculateLoan() {
                if (!currentLoanType || !loanAmountInput || !loanTermInput) return;

                const amount = parseFloat(loanAmountInput.value) || 0;
                const term = parseInt(loanTermInput.value) || 12;
                const interestRate = currentLoanType.interestRate / 100 / 12; // Monthly rate
                const processingFeeRate = currentLoanType.processingFee / 100;

                if (amount <= 0) return;

                try {
                    // Calculate monthly payment using PMT formula
                    const monthlyPayment = amount * (interestRate * Math.pow(1 + interestRate, term)) / (Math.pow(1 + interestRate, term) - 1);
                    const totalRepayment = monthlyPayment * term;
                    const totalInterest = totalRepayment - amount;
                    const processingFee = amount * processingFeeRate;

                    // Update display elements if they exist
                    const monthlyPaymentEl = document.getElementById('monthly-payment');
                    const totalInterestEl = document.getElementById('total-interest');
                    const processingFeeEl = document.getElementById('processing-fee');
                    const totalRepaymentEl = document.getElementById('total-repayment');

                    if (monthlyPaymentEl) monthlyPaymentEl.textContent = 'KES ' + monthlyPayment.toLocaleString('en-US', {maximumFractionDigits: 0});
                    if (totalInterestEl) totalInterestEl.textContent = 'KES ' + totalInterest.toLocaleString('en-US', {maximumFractionDigits: 0});
                    if (processingFeeEl) processingFeeEl.textContent = 'KES ' + processingFee.toLocaleString('en-US', {maximumFractionDigits: 0});
                    if (totalRepaymentEl) totalRepaymentEl.textContent = 'KES ' + (totalRepayment + processingFee).toLocaleString('en-US', {maximumFractionDigits: 0});
                } catch (error) {
                    console.log('Error calculating loan:', error);
                }
            }

            // Handle loan type selection
            loanTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        currentLoanType = {
                            maxAmount: parseFloat(this.dataset.maxAmount),
                            minAmount: parseFloat(this.dataset.minAmount),
                            interestRate: parseFloat(this.dataset.interestRate),
                            maxTerm: parseInt(this.dataset.maxTerm),
                            processingFee: parseFloat(this.dataset.processingFee)
                        };

                        // Update input constraints
                        loanAmountInput.setAttribute('max', currentLoanType.maxAmount);
                        loanAmountInput.setAttribute('min', currentLoanType.minAmount);
                        loanTermInput.setAttribute('max', currentLoanType.maxTerm);

                        // Update descriptions
                        const amountDescEl = document.getElementById('amount-description');
                        const termDescEl = document.getElementById('term-description');
                        
                        if (amountDescEl) {
                            amountDescEl.textContent = `Amount Range: KES ${currentLoanType.minAmount.toLocaleString()} - ${currentLoanType.maxAmount.toLocaleString()}`;
                        }
                        if (termDescEl) {
                            termDescEl.textContent = `Maximum term: ${currentLoanType.maxTerm} months`;
                        }

                        // Show/hide collateral section
                        const requiresCollateral = this.dataset.collateralRequired === 'true';
                        if (collateralSection) {
                            collateralSection.style.display = requiresCollateral ? 'block' : 'none';
                            
                            // Update required fields in collateral section
                            const collateralInputs = collateralSection.querySelectorAll('select, input, textarea');
                            collateralInputs.forEach(input => {
                                if (requiresCollateral) {
                                    input.setAttribute('required', '');
                                } else {
                                    input.removeAttribute('required');
                                }
                            });
                        }

                        calculateLoan();
                    }
                });
            });

            // Handle amount and term changes
            loanAmountInput.addEventListener('input', calculateLoan);
            loanTermInput.addEventListener('input', calculateLoan);

            // Initialize with first loan type if available
            const firstChecked = document.querySelector('.loan-type-radio:checked');
            if (firstChecked) {
                firstChecked.dispatchEvent(new Event('change'));
            }

            // Guarantor validation
            const guarantorInputs = document.querySelectorAll('[name^="guarantor_"]');
            guarantorInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    // Basic validation for phone numbers
                    if (this.name.includes('phone')) {
                        const phoneRegex = /^(\+254|0)[17]\d{8}$/;
                        if (this.value && !phoneRegex.test(this.value)) {
                            this.setCustomValidity('Please enter a valid Kenyan phone number');
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                    
                    // Basic validation for ID numbers
                    if (this.name.includes('id_number')) {
                        const idRegex = /^\d{8}$/;
                        if (this.value && !idRegex.test(this.value)) {
                            this.setCustomValidity('Please enter a valid 8-digit ID number');
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                });
            });

            // Real-time validation feedback
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                console.log('Form submission attempted');
                
                const requiredFields = form.querySelectorAll('[required]');
                let hasErrors = false;
                let missingFields = [];

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        hasErrors = true;
                        missingFields.push(field.name || field.id || 'Unknown field');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    console.log('Validation failed for fields:', missingFields);
                    showValidationNotification('Please fill in all required fields: ' + missingFields.join(', '));
                } else {
                    console.log('Form validation passed, submitting...');
                    // Allow form to submit
                }
            });
        });

        function showValidationNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-red-500 text-white max-w-sm transform transition-all duration-300 translate-x-full';
            
            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-white flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 6.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium">Validation Error</div>
                        <div class="text-sm opacity-90 mt-1">${message}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 p-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-remove after 5 seconds (longer for validation errors)
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>
    @endpush
</x-layouts.app> 