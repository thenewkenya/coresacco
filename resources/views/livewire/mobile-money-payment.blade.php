<div class="space-y-6">
    <!-- Payment Provider Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <span class="text-2xl mr-2">📱</span>
            Mobile Money Payment
        </h3>
        
        <div class="grid grid-cols-3 gap-4 mb-6">
            <button 
                wire:click="$set('provider', 'mpesa')"
                class="relative flex items-center justify-center p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md {{ $provider === 'mpesa' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">💚</div>
                    <div class="font-medium text-gray-900">M-Pesa</div>
                    <div class="text-xs text-gray-500">Safaricom</div>
                </div>
                @if($provider === 'mpesa')
                    <flux:icon.check class="absolute -top-2 -right-2 w-6 h-6 text-green-500 bg-white rounded-full p-1 border-2 border-green-500" />
                @endif
            </button>
            
            <button 
                wire:click="$set('provider', 'airtel')"
                class="relative flex items-center justify-center p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md {{ $provider === 'airtel' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300' }}"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">❤️</div>
                    <div class="font-medium text-gray-900">Airtel Money</div>
                    <div class="text-xs text-gray-500">Airtel</div>
                </div>
                @if($provider === 'airtel')
                    <flux:icon.check class="absolute -top-2 -right-2 w-6 h-6 text-red-500 bg-white rounded-full p-1 border-2 border-red-500" />
                @endif
            </button>
            
            <button 
                wire:click="$set('provider', 'tkash')"
                class="relative flex items-center justify-center p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md {{ $provider === 'tkash' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-orange-300' }}"
            >
                <div class="text-center">
                    <div class="text-3xl mb-2">🧡</div>
                    <div class="font-medium text-gray-900">T-Kash</div>
                    <div class="text-xs text-gray-500">Telkom</div>
                </div>
                @if($provider === 'tkash')
                    <flux:icon.check class="absolute -top-2 -right-2 w-6 h-6 text-orange-500 bg-white rounded-full p-1 border-2 border-orange-500" />
                @endif
            </button>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form wire:submit="initiatePayment" class="space-y-6">
            <!-- Account Selection -->
            <div class="space-y-2">
                <flux:label for="account">Select Account</flux:label>
                <flux:select 
                    wire:model.live="selectedAccountId" 
                    placeholder="Choose account to deposit to..."
                    id="account"
                >
                    @foreach($accounts as $account)
                        <option value="{{ $account['id'] }}">
                            {{ $account['label'] }} (Balance: KES {{ number_format($account['balance'], 2) }})
                        </option>
                    @endforeach
                </flux:select>
                @error('selectedAccountId') 
                    <flux:error>{{ $message }}</flux:error> 
                @enderror
            </div>

            <!-- Amount -->
            <div class="space-y-2">
                <flux:label for="amount">Amount (KES)</flux:label>
                <flux:input 
                    wire:model.live.debounce.500ms="amount"
                    type="number"
                    min="10"
                    max="500000"
                    step="0.01"
                    placeholder="Enter amount..."
                    id="amount"
                    icon="currency-dollar"
                />
                @error('amount') 
                    <flux:error>{{ $message }}</flux:error> 
                @enderror
                <div class="text-sm text-gray-500">
                    Minimum: KES 10.00 | Maximum: KES 500,000.00
                </div>
            </div>

            <!-- Phone Number -->
            <div class="space-y-2">
                <flux:label for="phone">{{ $this->getProviderName() }} Phone Number</flux:label>
                <flux:input 
                    wire:model.live.debounce.500ms="phoneNumber"
                    type="tel"
                    placeholder="e.g., 0722123456"
                    id="phone"
                    icon="phone"
                />
                @error('phoneNumber') 
                    <flux:error>{{ $message }}</flux:error> 
                @enderror
                <div class="text-sm text-gray-500">
                    Enter the phone number registered with {{ $this->getProviderName() }}
                </div>
            </div>

            <!-- Payment Summary -->
            @if($amount && $selectedAccountId)
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <h4 class="font-medium text-gray-900">Payment Summary</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Provider:</span>
                            <span class="font-medium">{{ $this->getProviderIcon() }} {{ $this->getProviderName() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Amount:</span>
                            <span class="font-medium">KES {{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Phone:</span>
                            <span class="font-medium">{{ $phoneNumber }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <flux:button 
                type="submit" 
                variant="primary" 
                size="lg" 
                class="w-full"
                :disabled="!$canProcess || $isProcessing"
                loading="{{ $isProcessing ? 'true' : 'false' }}"
            >
                @if($isProcessing)
                    <flux:icon.phone class="w-5 h-5 mr-2" />
                    Processing Payment...
                @else
                    <span class="text-lg mr-2">{{ $this->getProviderIcon() }}</span>
                    Pay with {{ $this->getProviderName() }}
                @endif
            </flux:button>
        </form>
    </div>

    <!-- Payment Status -->
    @if($paymentStatus === 'pending')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="animate-spin">
                    <flux:icon.arrow-path class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h4 class="font-medium text-blue-900">Payment in Progress</h4>
                    <p class="text-blue-700">
                        Please complete the payment on your phone. You will receive a {{ $this->getProviderName() }} prompt.
                    </p>
                </div>
            </div>
            
            <div class="mt-4 space-y-2">
                <div class="text-sm text-blue-700">
                    <strong>Transaction ID:</strong> {{ $transactionId }}
                </div>
                @if($checkoutRequestId)
                    <div class="text-sm text-blue-700">
                        <strong>Reference:</strong> {{ $checkoutRequestId }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    <flux:modal wire:model="showSuccessModal" class="max-w-md">
        <div class="text-center p-6">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <flux:icon.check class="w-8 h-8 text-green-600" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Successful!</h3>
            <p class="text-gray-600 mb-6">{{ $successMessage }}</p>
            <flux:button wire:click="resetPayment" variant="primary" class="w-full">
                Make Another Payment
            </flux:button>
        </div>
    </flux:modal>

    <!-- Error Modal -->
    <flux:modal wire:model="showErrorModal" class="max-w-md">
        <div class="text-center p-6">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <flux:icon.x-mark class="w-8 h-8 text-red-600" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Failed</h3>
            <p class="text-gray-600 mb-6">{{ $errorMessage }}</p>
            <flux:button wire:click="resetPayment" variant="primary" class="w-full">
                Try Again
            </flux:button>
        </div>
    </flux:modal>
</div>

<!-- Real-time Payment Status Polling -->
<script>
document.addEventListener('livewire:initialized', () => {
    let pollingInterval;
    
    Livewire.on('start-payment-polling', (data) => {
        // Clear any existing polling
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        // Start polling for payment status every 5 seconds
        pollingInterval = setInterval(() => {
            fetch(`/api/transactions/${data[0].transactionId}/status`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'completed') {
                        clearInterval(pollingInterval);
                        Livewire.dispatch('paymentConfirmed', result);
                    } else if (result.status === 'failed' || result.status === 'cancelled') {
                        clearInterval(pollingInterval);
                        Livewire.dispatch('paymentFailed', result);
                    }
                })
                .catch(error => {
                    console.error('Payment status polling error:', error);
                });
        }, 5000);
        
        // Stop polling after 5 minutes
        setTimeout(() => {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                Livewire.dispatch('paymentFailed', {
                    message: 'Payment timeout. Please check your transaction history.'
                });
            }
        }, 300000);
    });
    
    // Clean up polling when component is destroyed
    Livewire.on('component-destroyed', () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});
</script> 