<x-layouts.app :title="__('Make a Deposit')">
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
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Make a Deposit</h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Add money to your account securely</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <flux:icon.shield-check class="w-4 h-4" />
                        <span>Secure Transaction</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Deposit Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Deposit Information</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Fill in the details below to process your deposit</p>
                            </div>

                            <form action="{{ route('transactions.deposit.store') }}" method="POST" id="depositForm">
                                @csrf
                                
                                <!-- Account Selection -->
                                <div class="mb-6">
                                    <label for="account_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Select Account *
                                    </label>
                                    <select name="account_id" id="account_id" required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 
                                               dark:bg-zinc-700 dark:text-zinc-100 transition-colors"
                                        onchange="updateAccountDetails()">
                                        <option value="">-- Select an account --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}
                                                data-balance="{{ $account->balance }}"
                                                data-type="{{ $account->account_type }}"
                                                data-number="{{ $account->account_number }}"
                                                data-currency="{{ $account->currency }}"
                                                @if(auth()->user()->role !== 'member')
                                                    data-member="{{ $account->member->name }}"
                                                @endif>
                                                {{ $account->account_number }} - {{ ucfirst($account->account_type) }} (KES {{ number_format($account->balance, 2) }})
                                                @if(auth()->user()->role !== 'member')
                                                    - {{ $account->member->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Amount -->
                                <div class="mb-6">
                                    <label for="amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Deposit Amount (KES) *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-zinc-500 dark:text-zinc-400">KES</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" 
                                            value="{{ old('amount') }}" 
                                            min="1" max="500000" step="0.01" required
                                            placeholder="0.00"
                                            class="w-full pl-12 pr-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 
                                                   dark:bg-zinc-700 dark:text-zinc-100 transition-colors"
                                            oninput="validateAmount()">
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-sm">
                                        <span class="text-zinc-500 dark:text-zinc-400">Minimum: KES 1.00</span>
                                        <span class="text-zinc-500 dark:text-zinc-400">Maximum: KES 500,000.00</span>
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <div id="amountWarning" class="hidden mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                        <div class="flex">
                                            <flux:icon.exclamation-triangle class="w-5 h-5 text-amber-400 mr-2 flex-shrink-0" />
                                            <p class="text-sm text-amber-800 dark:text-amber-200">
                                                Large deposits (KES 50,000+) require approval from management.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-6">
                                    <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Description (Optional)
                                    </label>
                                    <textarea name="description" id="description" rows="3" 
                                        placeholder="Enter a description for this deposit..."
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 
                                               dark:bg-zinc-700 dark:text-zinc-100 transition-colors">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('transactions.index') }}" 
                                        class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium">
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn"
                                        class="bg-emerald-600 hover:bg-emerald-700 disabled:bg-zinc-400 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <flux:icon.banknotes class="w-5 h-5 mr-2" />
                                        Process Deposit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Details & Info -->
                    <div class="space-y-6">
                        <!-- Selected Account Details -->
                        <div id="accountDetails" class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hidden">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Account Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Number:</span>
                                    <span id="accountNumber" class="text-sm font-medium text-zinc-900 dark:text-zinc-100"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Type:</span>
                                    <span id="accountType" class="text-sm font-medium text-zinc-900 dark:text-zinc-100"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Current Balance:</span>
                                    <span id="currentBalance" class="text-sm font-medium text-emerald-600 dark:text-emerald-400"></span>
                                </div>
                                <div id="memberInfo" class="flex justify-between hidden">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Holder:</span>
                                    <span id="memberName" class="text-sm font-medium text-zinc-900 dark:text-zinc-100"></span>
                                </div>
                                <div id="newBalance" class="flex justify-between pt-3 border-t border-zinc-200 dark:border-zinc-700 hidden">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">New Balance:</span>
                                    <span id="newBalanceAmount" class="text-sm font-bold text-emerald-600 dark:text-emerald-400"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Info -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Transaction Limits</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Minimum deposit: KES 1.00</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Maximum deposit: KES 500,000.00</span>
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
                                If you have any questions about making a deposit, our support team is here to help.
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

    <script>
        function updateAccountDetails() {
            const select = document.getElementById('account_id');
            const selectedOption = select.options[select.selectedIndex];
            const detailsDiv = document.getElementById('accountDetails');
            
            if (selectedOption.value) {
                // Show account details
                detailsDiv.classList.remove('hidden');
                
                // Update fields
                document.getElementById('accountNumber').textContent = selectedOption.dataset.number;
                document.getElementById('accountType').textContent = selectedOption.dataset.type.charAt(0).toUpperCase() + selectedOption.dataset.type.slice(1);
                document.getElementById('currentBalance').textContent = 'KES ' + parseFloat(selectedOption.dataset.balance).toLocaleString('en-KE', {minimumFractionDigits: 2});
                
                // Show member info for staff
                if (selectedOption.dataset.member) {
                    document.getElementById('memberInfo').classList.remove('hidden');
                    document.getElementById('memberName').textContent = selectedOption.dataset.member;
                } else {
                    document.getElementById('memberInfo').classList.add('hidden');
                }
                
                // Update new balance if amount is entered
                updateNewBalance();
            } else {
                detailsDiv.classList.add('hidden');
            }
        }

        function updateNewBalance() {
            const select = document.getElementById('account_id');
            const selectedOption = select.options[select.selectedIndex];
            const amountInput = document.getElementById('amount');
            const newBalanceDiv = document.getElementById('newBalance');
            const newBalanceSpan = document.getElementById('newBalanceAmount');
            
            if (selectedOption.value && amountInput.value && parseFloat(amountInput.value) > 0) {
                const currentBalance = parseFloat(selectedOption.dataset.balance);
                const depositAmount = parseFloat(amountInput.value);
                const newBalance = currentBalance + depositAmount;
                
                newBalanceSpan.textContent = 'KES ' + newBalance.toLocaleString('en-KE', {minimumFractionDigits: 2});
                newBalanceDiv.classList.remove('hidden');
            } else {
                newBalanceDiv.classList.add('hidden');
            }
        }

        function validateAmount() {
            const amountInput = document.getElementById('amount');
            const warningDiv = document.getElementById('amountWarning');
            const submitBtn = document.getElementById('submitBtn');
            const amount = parseFloat(amountInput.value);
            
            // Update new balance
            updateNewBalance();
            
            // Show warning for large amounts
            if (amount >= 50000) {
                warningDiv.classList.remove('hidden');
            } else {
                warningDiv.classList.add('hidden');
            }
            
            // Validate amount
            if (amount > 0 && amount <= 500000) {
                submitBtn.disabled = false;
                amountInput.classList.remove('border-red-300', 'dark:border-red-600');
                amountInput.classList.add('border-zinc-300', 'dark:border-zinc-600');
            } else {
                submitBtn.disabled = true;
                if (amount > 500000) {
                    amountInput.classList.add('border-red-300', 'dark:border-red-600');
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateAccountDetails();
            validateAmount();
        });
    </script>
</x-layouts.app> 