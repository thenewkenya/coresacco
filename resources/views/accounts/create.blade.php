<x-layouts.app>
    <div class="space-y-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Open New Account</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    Choose from our range of SACCO account types to meet your financial goals.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('accounts.store') }}" id="accountForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Account Type Selection -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Account Types Grid -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Account Type</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($accountTypes as $type)
                                <div class="account-type-card relative p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600"
                                     data-type="{{ $type['value'] }}"
                                     data-color="{{ $type['color'] }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-lg bg-{{ $type['color'] }}-100 dark:bg-{{ $type['color'] }}-900 flex items-center justify-center">
                                                <flux:icon.{{ $type['icon'] }} class="h-5 w-5 text-{{ $type['color'] }}-600 dark:text-{{ $type['color'] }}-400" />
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $type['label'] }}
                                                </h3>
                                                <div class="selection-indicator w-4 h-4 rounded-full border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full opacity-0 transition-opacity bg-{{ $type['color'] }}-500"></div>
                                                </div>
                                            </div>
                                            
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $type['description'] }}
                                            </p>
                                            
                                            <div class="grid grid-cols-2 gap-4 mt-3 text-xs">
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Interest:</span>
                                                    <span class="font-medium text-{{ $type['color'] }}-600 dark:text-{{ $type['color'] }}-400">
                                                        {{ number_format($type['interest_rate'], 1) }}%
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Min Balance:</span>
                                                    <span class="font-medium text-gray-900 dark:text-white">
                                                        KES {{ number_format($type['minimum_balance']) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            @if($type['value'] === 'investment')
                                                <div class="mt-2">
                                                    <flux:badge color="amber" size="sm">Admin Only</flux:badge>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <input type="hidden" name="account_type" id="accountTypeInput" />
                        @error('account_type')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Member Selection (Staff Only) -->
                    @unless(auth()->user()->hasRole('member'))
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Member</h2>
                            
                            <flux:field>
                                <flux:label>Member</flux:label>
                                <flux:select name="member_id" id="memberSelect" required>
                                    <option value="">Choose a member...</option>
                                    @foreach($members as $memberOption)
                                        <option value="{{ $memberOption->id }}" 
                                                data-name="{{ $memberOption->name }}" 
                                                data-email="{{ $memberOption->email }}">
                                            {{ $memberOption->name }} ({{ $memberOption->email }})
                                        </option>
                                    @endforeach
                                </flux:select>
                                @error('member_id')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        </div>
                    @endunless
                </div>

                <!-- Account Summary & Actions -->
                <div class="space-y-6">
                    <!-- Selection Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Summary</h3>
                        
                        <div id="accountSummary" class="hidden space-y-4">
                            <!-- Dynamic content will be inserted here -->
                        </div>

                        <div id="noSelection" class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <flux:icon.credit-card class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-2" />
                            <p>Select an account type to see details</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <flux:button 
                            type="submit" 
                            variant="primary" 
                            class="w-full"
                            id="submitButton"
                            disabled
                        >
                            Open Account
                        </flux:button>
                        
                        <flux:button 
                            href="{{ route('accounts.index') }}" 
                            variant="ghost" 
                            class="w-full"
                        >
                            Cancel
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountTypes = @json($accountTypes);
            const members = @json($members ?? []);
            const isStaff = @json(!auth()->user()->hasRole('member'));
            
            let selectedType = '';
            let selectedMember = '';
            
            // Account type selection
            document.querySelectorAll('.account-type-card').forEach(card => {
                card.addEventListener('click', function() {
                    const typeValue = this.dataset.type;
                    const color = this.dataset.color;
                    
                    // Remove previous selection
                    document.querySelectorAll('.account-type-card').forEach(c => {
                        c.classList.remove('border-' + c.dataset.color + '-500', 'bg-' + c.dataset.color + '-50');
                        c.classList.add('border-gray-200', 'dark:border-gray-700');
                        const indicator = c.querySelector('.selection-indicator div');
                        indicator.classList.remove('opacity-100');
                        indicator.classList.add('opacity-0');
                    });
                    
                    // Add selection to clicked card
                    this.classList.remove('border-gray-200', 'dark:border-gray-700');
                    this.classList.add('border-' + color + '-500', 'bg-' + color + '-50');
                    const indicator = this.querySelector('.selection-indicator div');
                    indicator.classList.remove('opacity-0');
                    indicator.classList.add('opacity-100');
                    
                    selectedType = typeValue;
                    document.getElementById('accountTypeInput').value = selectedType;
                    
                    updateSummary();
                    updateSubmitButton();
                });
            });
            
            // Member selection (for staff)
            if (isStaff) {
                document.getElementById('memberSelect').addEventListener('change', function() {
                    selectedMember = this.value;
                    updateSummary();
                    updateSubmitButton();
                });
            }
            
            function updateSummary() {
                const summaryDiv = document.getElementById('accountSummary');
                const noSelectionDiv = document.getElementById('noSelection');
                
                if (!selectedType) {
                    summaryDiv.classList.add('hidden');
                    noSelectionDiv.classList.remove('hidden');
                    return;
                }
                
                const accountType = accountTypes.find(type => type.value === selectedType);
                if (!accountType) return;
                
                const color = accountType.color;
                
                let memberInfo = '';
                if (isStaff && selectedMember) {
                    const member = members.find(m => m.id == selectedMember);
                    if (member) {
                        memberInfo = `
                            <div class="space-y-2">
                                <h5 class="font-medium text-gray-900 dark:text-white">Account Holder</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${member.name}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${member.email}</p>
                            </div>
                        `;
                    }
                } else if (!isStaff) {
                    memberInfo = `
                        <div class="space-y-2">
                            <h5 class="font-medium text-gray-900 dark:text-white">Account Holder</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                    `;
                }
                
                summaryDiv.innerHTML = `
                    <div class="p-4 bg-${color}-50 dark:bg-${color}-900/20 rounded-lg border border-${color}-200 dark:border-${color}-800">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="h-8 w-8 rounded-lg bg-${color}-100 dark:bg-${color}-900 flex items-center justify-center">
                                <i class="h-4 w-4 text-${color}-600 dark:text-${color}-400"></i>
                            </div>
                            <h4 class="font-semibold text-${color}-900 dark:text-${color}-100">
                                ${accountType.label}
                            </h4>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-${color}-700 dark:text-${color}-300">Interest Rate:</span>
                                <span class="font-medium text-${color}-900 dark:text-${color}-100">${accountType.interest_rate}% p.a.</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-${color}-700 dark:text-${color}-300">Minimum Balance:</span>
                                <span class="font-medium text-${color}-900 dark:text-${color}-100">KES ${accountType.minimum_balance.toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-${color}-700 dark:text-${color}-300">Initial Balance:</span>
                                <span class="font-medium text-${color}-900 dark:text-${color}-100">KES 0.00</span>
                            </div>
                        </div>
                    </div>

                    ${memberInfo}

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <p class="mb-2">By opening this account, you agree to:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>SACCO terms and conditions</li>
                            <li>Maintain minimum balance requirements</li>
                            <li>Account management fees policy</li>
                        </ul>
                    </div>
                `;
                
                summaryDiv.classList.remove('hidden');
                noSelectionDiv.classList.add('hidden');
            }
            
            function updateSubmitButton() {
                const submitButton = document.getElementById('submitButton');
                const canSubmit = selectedType && (!isStaff || selectedMember);
                
                if (canSubmit) {
                    submitButton.removeAttribute('disabled');
                } else {
                    submitButton.setAttribute('disabled', 'disabled');
                }
            }
        });
    </script>
</x-layouts.app> 