<x-layouts.app :title="__('Transfer Money')">
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
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Transfer Money</h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Send money between accounts securely</p>
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
                    <!-- Transfer Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Transfer Information</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Fill in the details below to process your transfer</p>
                            </div>

                            <form action="{{ route('transactions.transfer.store') }}" method="POST" id="transferForm">
                                @csrf
                                
                                <!-- From Account Selection -->
                                <div class="mb-6">
                                    <label for="from_account_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        From Account *
                                    </label>
                                    <select name="from_account_id" id="from_account_id" required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
                                               dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                        <option value="">-- Select source account --</option>
                                        @foreach($fromAccounts as $account)
                                            <option value="{{ $account->id }}" 
                                                {{ (old('from_account_id') == $account->id || ($selectedFromAccount && $selectedFromAccount->id == $account->id)) ? 'selected' : '' }}>
                                                {{ $account->account_number }} - {{ ucfirst($account->account_type) }} (KES {{ number_format($account->balance, 2) }})
                                                @if(auth()->user()->role !== 'member')
                                                    - {{ $account->member->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('from_account_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    
                                    @if($selectedFromAccount)
                                        <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                                                <flux:icon.information-circle class="w-4 h-4 mr-2" />
                                                <span>Pre-selected from account details page</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- To Account Selection -->
                                <div class="mb-6">
                                    <label for="to_account_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        To Account *
                                    </label>
                                    <select name="to_account_id" id="to_account_id" required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
                                               dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                        <option value="">-- Select destination account --</option>
                                        @foreach($toAccounts as $account)
                                            <option value="{{ $account->id }}" 
                                                {{ (old('to_account_id') == $account->id || ($selectedToAccount && $selectedToAccount->id == $account->id)) ? 'selected' : '' }}>
                                                {{ $account->account_number }} - {{ ucfirst($account->account_type) }} - {{ $account->member->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_account_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    
                                    @if($selectedToAccount)
                                        <div class="mt-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                            <div class="flex items-center text-sm text-emerald-700 dark:text-emerald-300">
                                                <flux:icon.information-circle class="w-4 h-4 mr-2" />
                                                <span>Pre-selected as destination account (insufficient funds for transfer out)</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Amount -->
                                <div class="mb-6">
                                    <label for="amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Transfer Amount (KES) *
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
                                                   focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
                                                   dark:bg-zinc-700 dark:text-zinc-100 transition-colors">
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-sm">
                                        <span class="text-zinc-500 dark:text-zinc-400">Minimum: KES 1.00</span>
                                        <span class="text-zinc-500 dark:text-zinc-400">Maximum: KES 500,000.00</span>
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-6">
                                    <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        Description (Optional)
                                    </label>
                                    <textarea name="description" id="description" rows="3" 
                                        placeholder="Enter a description for this transfer..."
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-purple-500 focus:border-purple-500 
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
                                        class="bg-purple-600 hover:bg-purple-700 disabled:bg-zinc-400 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                        <flux:icon.arrows-right-left class="w-5 h-5 mr-2" />
                                        Process Transfer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="space-y-6">
                        <!-- Transaction Info -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Transfer Limits</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Minimum transfer: KES 1.00</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <flux:icon.check-circle class="w-4 h-4 text-emerald-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Maximum transfer: KES 500,000.00</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <flux:icon.clock class="w-4 h-4 text-amber-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Large transfers (KES 50,000+) require approval</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <flux:icon.shield-check class="w-4 h-4 text-blue-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">All transactions are encrypted and secure</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <flux:icon.banknotes class="w-4 h-4 text-purple-500 mr-2" />
                                    <span class="text-zinc-600 dark:text-zinc-400">Minimum balance of KES 1,000 must remain</span>
                                </div>
                            </div>
                        </div>

                        <!-- Help -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800 p-6">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-2">Need Help?</h3>
                            <p class="text-sm text-purple-800 dark:text-purple-200 mb-3">
                                If you have any questions about transferring money, our support team is here to help.
                            </p>
                            <a href="#" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-200 font-medium">
                                Contact Support →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 