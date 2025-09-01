<x-layouts.app :title="__('Loan Application')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Application') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Complete loan application with borrowing criteria and guarantors') }}
                        </p>
                    </div>
                    <a href="{{ route('loans.my') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-100">
                        ← {{ __('Back to My Loans') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('loans.apply.store') }}" class="space-y-8" id="loanApplicationForm">
                @csrf

                <!-- Member Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Member Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @roleany('admin', 'manager', 'staff')
                        <flux:field>
                            <flux:label>{{ __('Select Member') }}</flux:label>
                            <flux:select name="member_id" id="member_id" required>
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
                        @else
                        <flux:field>
                            <flux:label>{{ __('Member Name') }}</flux:label>
                            <flux:input type="text" value="{{ auth()->user()->name }}" readonly />
                            <input type="hidden" name="member_id" value="{{ auth()->user()->id }}" />
                        </flux:field>
                        @endroleany

                        <flux:field>
                            <flux:label>{{ __('Member Eligibility Status') }}</flux:label>
                            <div id="memberEligibility" class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    @roleany('admin', 'manager', 'staff')
                                        {{ __('Select a member to check eligibility') }}
                                    @else
                                        {{ __('Checking your eligibility...') }}
                                    @endroleany
                                </p>
                            </div>
                        </flux:field>
                    </div>
                </div>

                <!-- Loan Details -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Loan Type') }}</flux:label>
                            <flux:select name="loan_type_id" id="loan_type_id" required>
                                <option value="">{{ __('Choose loan type...') }}</option>
                                @foreach($loanTypes ?? [] as $loanType)
                                <option value="{{ $loanType->id }}" {{ old('loan_type_id') == $loanType->id ? 'selected' : '' }}>
                                    {{ $loanType->name }} ({{ $loanType->interest_rate }}%)
                                </option>
                                @endforeach
                            </flux:select>
                            @error('loan_type_id')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Loan Amount (KES)') }}</flux:label>
                            <flux:input type="number" name="amount" id="amount" min="1000" step="100" required />
                            @error('amount')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Term Period (Months)') }}</flux:label>
                            <flux:input type="number" name="term_period" id="term_period" min="1" max="60" required />
                            @error('term_period')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Borrowing Criteria -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Borrowing Criteria') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Savings Multiplier') }}</flux:label>
                            <flux:input type="number" name="required_savings_multiplier" id="required_savings_multiplier" 
                                       value="{{ old('required_savings_multiplier', 3.0) }}" min="1" max="10" step="0.1" />
                            <p class="text-xs text-gray-500 mt-1">{{ __('Loan amount must be ≤ savings × this multiplier') }}</p>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Minimum Savings Balance (KES)') }}</flux:label>
                            <flux:input type="number" name="minimum_savings_balance" id="minimum_savings_balance" 
                                       value="{{ old('minimum_savings_balance', 1000) }}" min="0" step="100" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Minimum Membership (Months)') }}</flux:label>
                            <flux:input type="number" name="minimum_membership_months" id="minimum_membership_months" 
                                       value="{{ old('minimum_membership_months', 6) }}" min="0" />
                        </flux:field>
                    </div>

                    <!-- Criteria Evaluation Results -->
                    <div class="mt-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-3">{{ __('Criteria Evaluation') }}</h4>
                        <div id="criteriaEvaluation" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">{{ __('Savings Criteria') }}</span>
                                <span id="savingsCriteria" class="text-sm font-medium">-</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">{{ __('Membership Criteria') }}</span>
                                <span id="membershipCriteria" class="text-sm font-medium">-</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">{{ __('Guarantor Criteria') }}</span>
                                <span id="guarantorCriteria" class="text-sm font-medium">-</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-blue-200 dark:border-blue-800">
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('Overall Eligibility') }}</span>
                                <span id="overallEligibility" class="text-sm font-bold">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guarantor Requirements -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Guarantor Requirements') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Required Guarantors') }}</flux:label>
                            <flux:input type="number" name="required_guarantors" id="required_guarantors" 
                                       value="{{ old('required_guarantors', 2) }}" min="1" max="5" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Required Guarantee Amount (KES)') }}</flux:label>
                            <flux:input type="number" name="required_guarantee_amount" id="required_guarantee_amount" 
                                       value="{{ old('required_guarantee_amount', 0) }}" min="0" step="100" />
                            <p class="text-xs text-gray-500 mt-1">{{ __('Leave 0 to auto-calculate as 50% of loan amount') }}</p>
                        </flux:field>
                    </div>
                </div>

                <!-- Guarantors Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Guarantors') }}
                        </h3>
                        <button type="button" class="px-3 py-1 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm" onclick="addGuarantor()">
                            {{ __('Add Guarantor') }}
                        </button>
                    </div>

                    <div id="guarantorsContainer" class="space-y-6">
                        <!-- Guarantors will be added here dynamically -->
                    </div>

                    <div class="mt-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <p class="font-medium">{{ __('Important') }}</p>
                                <p class="mt-1">{{ __('All guarantors must be approved individually. They will be notified and must confirm their guarantee before the loan can be approved.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" id="submitBtn" disabled>
                        {{ __('Submit Loan Application') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let guarantorCount = 0;
        let currentMember = null;

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
        });

        function initializeForm() {
            // Member selection change
            const memberSelect = document.getElementById('member_id');
            if (memberSelect) {
                memberSelect.addEventListener('change', function() {
                    const memberId = this.value;
                    if (memberId) {
                        checkMemberEligibility(memberId);
                    } else {
                        resetMemberEligibility();
                    }
                });
            } else {
                // For members, automatically check their eligibility
                const memberId = {{ auth()->user()->id }};
                if (memberId) {
                    checkMemberEligibility(memberId);
                }
            }

            // Loan amount change
            document.getElementById('amount').addEventListener('input', function() {
                updateGuaranteeAmount();
                evaluateCriteria();
            });

            // Required guarantors change
            document.getElementById('required_guarantors').addEventListener('change', function() {
                updateGuarantorsDisplay();
            });

            // Auto-calculate guarantee amount when loan amount changes
            document.getElementById('required_guarantee_amount').addEventListener('input', function() {
                if (this.value == 0) {
                    updateGuaranteeAmount();
                }
            });
        }

        function checkMemberEligibility(memberId) {
            console.log('Checking eligibility for member:', memberId);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            fetch(`/test-eligibility/${memberId}`, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Eligibility data:', data);
                    if (data.success) {
                        currentMember = data.data;
                        displayMemberEligibility(data.data);
                        evaluateCriteria();
                    } else {
                        throw new Error(data.message || 'Failed to check eligibility');
                    }
                })
                .catch(error => {
                    console.error('Error checking member eligibility:', error);
                    showNotification('Error checking member eligibility: ' + error.message, 'error');
                    // Show error in the eligibility container
                    document.getElementById('memberEligibility').innerHTML = `
                        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900">
                            <p class="text-sm text-red-600 dark:text-red-400">
                                Error: ${error.message}
                            </p>
                        </div>
                    `;
                });
        }

        function displayMemberEligibility(memberData) {
            const container = document.getElementById('memberEligibility');
            const statusClass = memberData.overall_eligible ? 'text-green-600' : 'text-red-600';
            const statusIcon = memberData.overall_eligible ? '✓' : '✗';
            
            container.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">Status:</span>
                        <span class="text-sm font-bold ${statusClass}">${statusIcon} ${memberData.overall_eligible ? 'Eligible' : 'Not Eligible'}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Savings Balance:</span>
                        <span class="text-sm font-medium">KES ${memberData.savings_balance.toLocaleString()}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Max Loan Amount:</span>
                        <span class="text-sm font-medium">KES ${memberData.max_loan_amount.toLocaleString()}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Months in SACCO:</span>
                        <span class="text-sm font-medium">${memberData.months_in_sacco}</span>
                    </div>
                </div>
            `;
        }

        function resetMemberEligibility() {
            document.getElementById('memberEligibility').innerHTML = `
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Select a member to check eligibility') }}
                </p>
            `;
            currentMember = null;
            resetCriteriaEvaluation();
        }

        function updateGuaranteeAmount() {
            const loanAmount = parseFloat(document.getElementById('amount').value) || 0;
            const guaranteeAmountField = document.getElementById('required_guarantee_amount');
            
            if (guaranteeAmountField.value == 0) {
                guaranteeAmountField.value = Math.round(loanAmount * 0.5);
            }
        }

        function evaluateCriteria() {
            if (!currentMember) return;

            const loanAmount = parseFloat(document.getElementById('amount').value) || 0;
            const multiplier = parseFloat(document.getElementById('required_savings_multiplier').value) || 3.0;
            const minimumBalance = parseFloat(document.getElementById('minimum_savings_balance').value) || 1000;
            const minimumMonths = parseInt(document.getElementById('minimum_membership_months').value) || 6;

            // Evaluate savings criteria
            const maxLoanAmount = currentMember.savings_balance * multiplier;
            const meetsSavings = currentMember.savings_balance >= minimumBalance && loanAmount <= maxLoanAmount;
            
            // Evaluate membership criteria
            const meetsMembership = currentMember.months_in_sacco >= minimumMonths;

            // Update display
            document.getElementById('savingsCriteria').innerHTML = 
                `<span class="${meetsSavings ? 'text-green-600' : 'text-red-600'}">${meetsSavings ? '✓ Met' : '✗ Not Met'}</span>`;
            
            document.getElementById('membershipCriteria').innerHTML = 
                `<span class="${meetsMembership ? 'text-green-600' : 'text-red-600'}">${meetsMembership ? '✓ Met' : '✗ Not Met'}</span>`;

            // Guarantor criteria will be evaluated when guarantors are added
            document.getElementById('guarantorCriteria').innerHTML = 
                `<span class="text-gray-500">Pending</span>`;

            // Overall eligibility
            const overallEligible = meetsSavings && meetsMembership;
            document.getElementById('overallEligibility').innerHTML = 
                `<span class="${overallEligible ? 'text-green-600' : 'text-red-600'}">${overallEligible ? '✓ Eligible' : '✗ Not Eligible'}</span>`;

            // Enable/disable submit button
            document.getElementById('submitBtn').disabled = !overallEligible;
        }

        function resetCriteriaEvaluation() {
            document.getElementById('savingsCriteria').innerHTML = '-';
            document.getElementById('membershipCriteria').innerHTML = '-';
            document.getElementById('guarantorCriteria').innerHTML = '-';
            document.getElementById('overallEligibility').innerHTML = '-';
            document.getElementById('submitBtn').disabled = true;
        }

        function addGuarantor() {
            guarantorCount++;
            const container = document.getElementById('guarantorsContainer');
            
            const guarantorHtml = `
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4" id="guarantor_${guarantorCount}">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">Guarantor ${guarantorCount}</h4>
                        <button type="button" onclick="removeGuarantor(${guarantorCount})" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input type="text" name="guarantors[${guarantorCount}][full_name]" required />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>ID Number</flux:label>
                            <flux:input type="text" name="guarantors[${guarantorCount}][id_number]" pattern="[0-9]{8}" required />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Phone Number</flux:label>
                            <flux:input type="tel" name="guarantors[${guarantorCount}][phone_number]" required />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Employment Status</flux:label>
                            <flux:select name="guarantors[${guarantorCount}][employment_status]" required>
                                <option value="employed">Employed</option>
                                <option value="self_employed">Self Employed</option>
                                <option value="retired">Retired</option>
                                <option value="unemployed">Unemployed</option>
                            </flux:select>
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Monthly Income (KES)</flux:label>
                            <flux:input type="number" name="guarantors[${guarantorCount}][monthly_income]" min="0" step="1000" required />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Relationship to Borrower</flux:label>
                            <flux:input type="text" name="guarantors[${guarantorCount}][relationship_to_borrower]" required />
                        </flux:field>
                        
                        <flux:field class="md:col-span-2">
                            <flux:label>Address</flux:label>
                            <flux:textarea name="guarantors[${guarantorCount}][address]" rows="2" required></flux:textarea>
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Guarantee Amount (KES)</flux:label>
                            <flux:input type="number" name="guarantors[${guarantorCount}][guarantee_amount]" min="0" step="100" required />
                        </flux:field>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', guarantorHtml);
            updateGuarantorsDisplay();
        }

        function removeGuarantor(index) {
            const element = document.getElementById(`guarantor_${index}`);
            if (element) {
                element.remove();
                updateGuarantorsDisplay();
            }
        }

        function updateGuarantorsDisplay() {
            const requiredGuarantors = parseInt(document.getElementById('required_guarantors').value) || 0;
            const currentGuarantors = document.querySelectorAll('[id^="guarantor_"]').length;
            
            // Update guarantor criteria evaluation
            const meetsGuarantorCount = currentGuarantors >= requiredGuarantors;
            document.getElementById('guarantorCriteria').innerHTML = 
                `<span class="${meetsGuarantorCount ? 'text-green-600' : 'text-red-600'}">${meetsGuarantorCount ? '✓ Met' : '✗ Not Met'}</span>`;
            
            // Update overall eligibility
            const meetsSavings = document.getElementById('savingsCriteria').textContent.includes('✓');
            const meetsMembership = document.getElementById('membershipCriteria').textContent.includes('✓');
            const overallEligible = meetsSavings && meetsMembership && meetsGuarantorCount;
            
            document.getElementById('overallEligibility').innerHTML = 
                `<span class="${overallEligible ? 'text-green-600' : 'text-red-600'}">${overallEligible ? '✓ Eligible' : '✗ Not Eligible'}</span>`;
            
            document.getElementById('submitBtn').disabled = !overallEligible;
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-1">
                        <div class="font-medium">${type === 'error' ? 'Error' : 'Info'}</div>
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
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Form submission validation
        document.getElementById('loanApplicationForm').addEventListener('submit', function(e) {
            const requiredGuarantors = parseInt(document.getElementById('required_guarantors').value) || 0;
            const currentGuarantors = document.querySelectorAll('[id^="guarantor_"]').length;
            
            if (currentGuarantors < requiredGuarantors) {
                e.preventDefault();
                showNotification(`Please add at least ${requiredGuarantors} guarantor(s)`, 'error');
                return;
            }
            
            // Show loading state but allow form to submit
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').textContent = 'Submitting...';
            
            // Don't prevent default - let the form submit naturally
        });
    </script>
    @endpush
</x-layouts.app>
