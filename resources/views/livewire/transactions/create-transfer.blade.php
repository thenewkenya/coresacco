<?php

use App\Models\Account;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        $user = auth()->user();

        $fromQuery = Account::with('member');
        if (!$user->hasAnyRole(['admin', 'manager', 'staff'])) {
            $fromQuery->where('member_id', $user->id);
        }
        $fromAccounts = $fromQuery->get();

        $toAccounts = Account::with('member')->get();

        $selectedFromId = old('from_account_id') ?? request()->get('from');
        $selectedToId = old('to_account_id') ?? request()->get('to');

        $selectedFromAccount = $selectedFromId ? Account::with('member')->find($selectedFromId) : null;
        $selectedToAccount = $selectedToId ? Account::with('member')->find($selectedToId) : null;

        return [
            'fromAccounts' => $fromAccounts,
            'toAccounts' => $toAccounts,
            'selectedFromAccount' => $selectedFromAccount,
            'selectedToAccount' => $selectedToAccount,
        ];
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
                    <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Transfer Money</flux:heading>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Send money between accounts securely</flux:subheading>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                <flux:icon.shield-check class="w-4 h-4" />
                <span>Secure Transaction</span>
            </div>
        </div>

        <!-- Content -->
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
                                <flux:field>
                                    <flux:label>From Account *</flux:label>
                                    <select name="from_account_id" id="from_account_id" required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
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
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror

                                    @if($selectedFromAccount)
                                        <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                                                <flux:icon.information-circle class="w-4 h-4 mr-2" />
                                                <span>Pre-selected from account details page</span>
                                            </div>
                                        </div>
                                    @endif
                                </flux:field>
                            </div>

                            <!-- To Account Selection -->
                            <div class="mb-6">
                                <flux:field>
                                    <flux:label>To Account *</flux:label>
                                    <select name="to_account_id" id="to_account_id" required 
                                        class="w-full px-3 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg 
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
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
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror

                                    @if($selectedToAccount)
                                        <div class="mt-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                            <div class="flex items-center text-sm text-emerald-700 dark:text-emerald-300">
                                                <flux:icon.information-circle class="w-4 h-4 mr-2" />
                                                <span>Pre-selected as destination account (insufficient funds for transfer out)</span>
                                            </div>
                                        </div>
                                    @endif
                                </flux:field>
                                <p id="sameAccountError" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden">From and To accounts cannot be the same.</p>
                            </div>

                            <!-- Amount -->
                            <div class="mb-6">
                                <flux:field>
                                    <flux:label>Transfer Amount (KES) *</flux:label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-zinc-500 dark:text-zinc-400">KES</span>
                                        </div>
                                        <flux:input 
                                            type="number" 
                                            name="amount" 
                                            id="amount" 
                                            value="{{ old('amount') }}" 
                                            min="1" 
                                            max="500000" 
                                            step="0.01" 
                                            required
                                            placeholder="0.00"
                                            class="pl-12"
                                        />
                                    </div>
                                    <flux:description>Minimum: KES 1.00 | Maximum: KES 500,000.00</flux:description>
                                    @error('amount')
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <flux:field>
                                    <flux:label>Description (Optional)</flux:label>
                                    <flux:textarea 
                                        name="description" 
                                        id="description" 
                                        rows="3" 
                                        placeholder="Enter a description for this transfer..."
                                    >{{ old('description') }}</flux:textarea>
                                    @error('description')
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between">
                                <flux:button 
                                    variant="ghost" 
                                    :href="route('transactions.index')" 
                                    wire:navigate
                                >
                                    Cancel
                                </flux:button>
                                <flux:button 
                                    type="submit" 
                                    id="submitBtn"
                                    variant="primary"
                                    icon="arrows-right-left"
                                >
                                    Process Transfer
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="space-y-6">
                    <!-- Transaction Summary -->
                    <div id="transferSummary" class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hidden">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Transaction Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">From Account:</span>
                                <span id="summaryFromAccount" class="text-sm font-medium text-zinc-900 dark:text-zinc-100"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">To Account:</span>
                                <span id="summaryToAccount" class="text-sm font-medium text-zinc-900 dark:text-zinc-100"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Transfer Amount:</span>
                                <span id="summaryTransferAmount" class="text-sm font-medium text-blue-600 dark:text-blue-400"></span>
                            </div>
                            <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-700 pt-3">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">From Balance After:</span>
                                <span id="summaryFromNewBalance" class="text-sm font-bold text-zinc-900 dark:text-zinc-100"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">To Balance After:</span>
                                <span id="summaryToNewBalance" class="text-sm font-bold text-emerald-600 dark:text-emerald-400"></span>
                            </div>
                        </div>
                    </div>

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
                                <flux:icon.banknotes class="w-4 h-4 text-blue-500 mr-2" />
                                <span class="text-zinc-600 dark:text-zinc-400">Minimum balance of KES 1,000 must remain</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Need Help?</h3>
                        <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                            If you have any questions about transferring money, our support team is here to help.
                        </p>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                            Contact Support →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTransferSummary() {
            const fromSelect = document.getElementById('from_account_id');
            const toSelect = document.getElementById('to_account_id');
            const amountInput = document.getElementById('amount');
            const summaryDiv = document.getElementById('transferSummary');
            if (!fromSelect || !toSelect || !amountInput) return;

            const submitBtn = document.getElementById('submitBtn');
            const sameError = document.getElementById('sameAccountError');

            const fromOption = fromSelect.options[fromSelect.selectedIndex];
            const toOption = toSelect.options[toSelect.selectedIndex];
            const amount = parseFloat(amountInput.value) || 0;

            // Prevent same account transfer
            const sameAccount = fromOption && toOption && fromOption.value && toOption.value && fromOption.value === toOption.value;
            if (sameAccount) {
                sameError.classList.remove('hidden');
                if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
            } else {
                sameError.classList.add('hidden');
                if (submitBtn) submitBtn.removeAttribute('disabled');
            }

            if (fromOption.value && toOption.value && amount > 0) {
                summaryDiv.classList.remove('hidden');

                const fromBalance = parseFloat(fromOption.textContent.match(/KES ([\d,]+\.[\d]{2})/)?.[1]?.replace(/,/g, '') || 0);
                const toBalance = parseFloat(toOption.textContent.match(/KES ([\d,]+\.[\d]{2})/)?.[1]?.replace(/,/g, '') || 0);

                document.getElementById('summaryFromAccount').textContent = fromOption.textContent.split(' (KES')[0];
                document.getElementById('summaryToAccount').textContent = toOption.textContent.split(' - ')[1];
                document.getElementById('summaryTransferAmount').textContent = 'KES ' + amount.toLocaleString('en-KE', {minimumFractionDigits: 2});

                const fromNewBalance = fromBalance - amount;
                const toNewBalance = toBalance + amount;

                document.getElementById('summaryFromNewBalance').textContent = 'KES ' + fromNewBalance.toLocaleString('en-KE', {minimumFractionDigits: 2});
                document.getElementById('summaryToNewBalance').textContent = 'KES ' + toNewBalance.toLocaleString('en-KE', {minimumFractionDigits: 2});

                const minBalance = 1000; 
                if (fromNewBalance < minBalance) {
                    document.getElementById('summaryFromNewBalance').className = 'text-sm font-bold text-red-600 dark:text-red-400';
                } else {
                    document.getElementById('summaryFromNewBalance').className = 'text-sm font-bold text-zinc-900 dark:text-zinc-100';
                }
            } else {
                summaryDiv.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fromEl = document.getElementById('from_account_id');
            const toEl = document.getElementById('to_account_id');
            const amountEl = document.getElementById('amount');
            if (fromEl) fromEl.addEventListener('change', updateTransferSummary);
            if (toEl) toEl.addEventListener('change', updateTransferSummary);
            if (amountEl) amountEl.addEventListener('input', updateTransferSummary);
            updateTransferSummary();
        });
    </script>
</div>


