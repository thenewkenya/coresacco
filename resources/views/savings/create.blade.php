<x-layouts.app :title="__('Create Savings Account')">
    @role('admin')
    {{-- Admin-only advanced settings section --}}
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Administrator Options</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:field>
                    <flux:label>Override Minimum Balance</flux:label>
                    <flux:input name="override_minimum" type="number" step="0.01" placeholder="Leave empty for default" />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Special Interest Rate (%)</flux:label>
                    <flux:input name="custom_interest_rate" type="number" step="0.01" placeholder="Leave empty for default" />
                </flux:field>
            </div>
        </div>
    </div>
    @endrole

    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Create Savings Account') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            @role('admin')
                                {{ __('Create new savings account with full administrative privileges') }}
                            @elserole('manager')
                                {{ __('Create new savings account for branch members') }}
                            @else
                                {{ __('Create new savings account for SACCO members') }}
                            @endrole
                        </p>
                    </div>
                    <flux:button variant="ghost" :href="route('savings.index')" icon="arrow-left">
                        {{ __('Back to Savings') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <form method="POST" action="{{ route('savings.store') }}" class="space-y-8">
                @csrf
                
                <!-- Member Selection -->
                @roleany('admin', 'manager', 'staff')
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Member Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Select Member') }}</flux:label>
                            <flux:select name="member_id" required>
                                <option value="">{{ __('Choose a member...') }}</option>
                                @foreach($members as $member)
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
                            <flux:label>{{ __('Account Status') }}</flux:label>
                            <flux:select name="status">
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                                <option value="suspended">{{ __('Suspended') }}</option>
                            </flux:select>
                        </flux:field>
                        @endrole
                    </div>
                </div>
                @endroleany

                <!-- Account Type Selection -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Account Type') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach([
                            [
                                'type' => 'savings',
                                'name' => 'Regular Savings',
                                'description' => 'Standard savings account with competitive interest rates',
                                'min_balance' => 1000,
                                'interest_rate' => 8.5,
                                'icon' => 'banknotes'
                            ],
                            [
                                'type' => 'shares',
                                'name' => 'Share Capital',
                                'description' => 'Equity shares in the SACCO with dividends',
                                'min_balance' => 5000,
                                'interest_rate' => 12.0,
                                'icon' => 'chart-pie'
                            ],
                            [
                                'type' => 'deposits',
                                'name' => 'Term Deposits',
                                'description' => 'Fixed term deposit with higher interest rates',
                                'min_balance' => 10000,
                                'interest_rate' => 15.0,
                                'icon' => 'lock-closed'
                            ],
                            [
                                'type' => 'emergency_fund',
                                'name' => 'Emergency Fund',
                                'description' => 'Emergency savings for unexpected expenses',
                                'min_balance' => 500,
                                'interest_rate' => 6.0,
                                'icon' => 'shield-exclamation'
                            ],
                            [
                                'type' => 'holiday_savings',
                                'name' => 'Holiday Savings',
                                'description' => 'Save for holidays and vacation expenses',
                                'min_balance' => 500,
                                'interest_rate' => 7.0,
                                'icon' => 'sun'
                            ],
                            [
                                'type' => 'retirement',
                                'name' => 'Retirement Savings',
                                'description' => 'Long-term savings for retirement planning',
                                'min_balance' => 2000,
                                'interest_rate' => 10.0,
                                'icon' => 'user-group'
                            ],
                            [
                                'type' => 'education',
                                'name' => 'Education Fund',
                                'description' => 'Save for education and training expenses',
                                'min_balance' => 1000,
                                'interest_rate' => 9.0,
                                'icon' => 'academic-cap'
                            ],
                            [
                                'type' => 'development',
                                'name' => 'Development Fund',
                                'description' => 'Community and personal development savings',
                                'min_balance' => 1000,
                                'interest_rate' => 8.0,
                                'icon' => 'arrow-trending-up'
                            ],
                            [
                                'type' => 'welfare',
                                'name' => 'Welfare Fund',
                                'description' => 'Mutual aid and welfare support fund',
                                'min_balance' => 500,
                                'interest_rate' => 6.5,
                                'icon' => 'heart'
                            ],
                            [
                                'type' => 'investment',
                                'name' => 'Investment Account',
                                'description' => 'High-yield investment account',
                                'min_balance' => 25000,
                                'interest_rate' => 18.0,
                                'icon' => 'trending-up',
                                'restricted' => !auth()->user()->hasAnyRole(['admin', 'manager'])
                            ],
                            [
                                'type' => 'loan_guarantee',
                                'name' => 'Loan Guarantee Fund',
                                'description' => 'Funds set aside to guarantee loans',
                                'min_balance' => 5000,
                                'interest_rate' => 5.0,
                                'icon' => 'document-check'
                            ],
                            [
                                'type' => 'insurance',
                                'name' => 'Insurance Fund',
                                'description' => 'Life and credit insurance contributions',
                                'min_balance' => 1000,
                                'interest_rate' => 4.0,
                                'icon' => 'shield-check'
                            ]
                        ] as $accountType)
                        <div class="relative">
                            <input type="radio" 
                                   id="account_type_{{ $accountType['type'] }}" 
                                   name="account_type" 
                                   value="{{ $accountType['type'] }}" 
                                   class="peer sr-only"
                                   {{ old('account_type') == $accountType['type'] ? 'checked' : '' }}
                                   @if($accountType['restricted'] ?? false) disabled @endif>
                            <label for="account_type_{{ $accountType['type'] }}" 
                                   class="block p-4 border-2 border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer 
                                          peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                          hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors
                                          @if($accountType['restricted'] ?? false) opacity-50 cursor-not-allowed @endif">
                                <div class="flex items-start space-x-3">
                                    <flux:icon.{{ $accountType['icon'] }} class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1" />
                                    <div class="flex-1">
                                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $accountType['name'] }}
                                            @if($accountType['restricted'] ?? false)
                                                <span class="text-xs text-red-500">(Admin/Manager Only)</span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                            {{ $accountType['description'] }}
                                        </p>
                                        <div class="mt-2 space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Min. Balance:</span>
                                                <span class="font-medium">KES {{ number_format($accountType['min_balance']) }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-zinc-500">Interest Rate:</span>
                                                <span class="font-medium text-green-600">{{ $accountType['interest_rate'] }}% p.a.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('account_type')
                        <flux:error class="mt-2">{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Initial Deposit -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Initial Deposit') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('Deposit Amount (KES)') }}</flux:label>
                            <flux:input name="initial_deposit" 
                                       type="number" 
                                       step="0.01" 
                                       min="1000" 
                                       value="{{ old('initial_deposit') }}" 
                                       placeholder="Enter amount" 
                                       required />
                            <flux:description>
                                Minimum deposit: KES 1,000
                                @role('admin')
                                    (Can be overridden by admin)
                                @endrole
                            </flux:description>
                            @error('initial_deposit')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        @roleany('admin', 'manager', 'staff')
                        <flux:field>
                            <flux:label>{{ __('Payment Method') }}</flux:label>
                            <flux:select name="payment_method" required>
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="mobile_money">{{ __('Mobile Money') }}</option>
                                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                @role('admin')
                                <option value="internal_transfer">{{ __('Internal Transfer') }}</option>
                                @endrole
                            </flux:select>
                        </flux:field>
                        @endroleany
                    </div>

                    @roleany('admin', 'manager', 'staff')
                    <div class="mt-6">
                        <flux:field>
                            <flux:label>{{ __('Transaction Reference') }}</flux:label>
                            <flux:input name="transaction_reference" 
                                       value="{{ old('transaction_reference') }}" 
                                       placeholder="Enter reference number (optional)" />
                            <flux:description>
                                Reference number for the initial deposit transaction
                            </flux:description>
                        </flux:field>
                    </div>
                    @endroleany
                </div>

                <!-- Account Features -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Account Features') }}
                    </h3>
                    
                    <div class="space-y-4">
                        @roleany('admin', 'manager')
                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('SMS Notifications') }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Receive SMS alerts for transactions</p>
                            </div>
                            <flux:switch name="sms_notifications" value="1" checked />
                        </div>

                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Email Statements') }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Monthly email statements</p>
                            </div>
                            <flux:switch name="email_statements" value="1" checked />
                        </div>

                        @role('admin')
                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Overdraft Facility') }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Allow negative balance up to limit</p>
                            </div>
                            <flux:switch name="overdraft_enabled" value="1" />
                        </div>
                        @endrole
                        @endroleany

                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Auto-Save') }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Automatic monthly savings deduction</p>
                            </div>
                            <flux:switch name="auto_save_enabled" value="1" />
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Additional Information') }}
                    </h3>
                    <flux:field>
                        <flux:label>{{ __('Account Description') }}</flux:label>
                        <flux:textarea name="description" 
                                      rows="3" 
                                      placeholder="Enter account description or notes (optional)">{{ old('description') }}</flux:textarea>
                        @error('description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    @role('admin')
                    <div class="mt-4">
                        <flux:field>
                            <flux:label>{{ __('Internal Notes (Admin Only)') }}</flux:label>
                            <flux:textarea name="admin_notes" 
                                          rows="2" 
                                          placeholder="Internal administrative notes">{{ old('admin_notes') }}</flux:textarea>
                        </flux:field>
                    </div>
                    @endrole
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6">
                    <flux:button variant="ghost" :href="route('savings.index')">
                        {{ __('Cancel') }}
                    </flux:button>
                    <div class="flex items-center space-x-3">
                        @role('admin')
                        <flux:button type="submit" name="action" value="draft" variant="outline">
                            {{ __('Save as Draft') }}
                        </flux:button>
                        @endrole
                        <flux:button type="submit" variant="primary">
                            @roleany('admin', 'manager')
                                {{ __('Create Account') }}
                            @else
                                {{ __('Submit for Approval') }}
                            @endroleany
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Role-based feature toggles
        document.addEventListener('DOMContentLoaded', function() {
            const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
            const initialDepositInput = document.querySelector('input[name="initial_deposit"]');
            
            // Account type specific minimum balances
            const minBalances = {
                'savings': 1000,
                'shares': 5000,
                'fixed_deposit': 10000
            };

            accountTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked) {
                        const minBalance = minBalances[this.value] || 1000;
                        initialDepositInput.setAttribute('min', minBalance);
                        initialDepositInput.setAttribute('placeholder', `Minimum: KES ${minBalance.toLocaleString()}`);
                        
                        // Update description
                        const description = initialDepositInput.nextElementSibling;
                        if (description && description.classList.contains('flux-description')) {
                            description.textContent = `Minimum deposit: KES ${minBalance.toLocaleString()}`;
                            @role('admin')
                            description.textContent += ' (Can be overridden by admin)';
                            @endrole
                        }
                    }
                });
            });

            // Auto-save amount calculation
            const autoSaveSwitch = document.querySelector('input[name="auto_save_enabled"]');
            if (autoSaveSwitch) {
                autoSaveSwitch.addEventListener('change', function() {
                    if (this.checked) {
                        const amount = prompt('Enter monthly auto-save amount (KES):');
                        if (amount && !isNaN(amount) && amount > 0) {
                            // Store the amount in a hidden field
                            let hiddenField = document.querySelector('input[name="auto_save_amount"]');
                            if (!hiddenField) {
                                hiddenField = document.createElement('input');
                                hiddenField.type = 'hidden';
                                hiddenField.name = 'auto_save_amount';
                                this.closest('form').appendChild(hiddenField);
                            }
                            hiddenField.value = amount;
                        } else {
                            this.checked = false;
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app> 