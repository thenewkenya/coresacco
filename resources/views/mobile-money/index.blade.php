<x-layouts.app title="Mobile Money Payments">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-xl p-8 text-white mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 rounded-full p-3">
                    <flux:icon.phone class="w-8 h-8" />
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Mobile Money Payments</h1>
                    <p class="text-green-100 mt-2">
                        Quick and secure deposits using M-Pesa, Airtel Money, and T-Kash
                    </p>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">💚</span>
                        <div>
                            <div class="text-sm text-green-100">M-Pesa Available</div>
                            <div class="font-semibold">{{ setting('mpesa_enabled') ? 'Active' : 'Disabled' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">❤️</span>
                        <div>
                            <div class="text-sm text-green-100">Airtel Money</div>
                            <div class="font-semibold">{{ setting('airtel_enabled') ? 'Active' : 'Disabled' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">🧡</span>
                        <div>
                            <div class="text-sm text-green-100">T-Kash</div>
                            <div class="font-semibold">{{ setting('tkash_enabled') ? 'Active' : 'Disabled' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Money Payment Component -->
        @livewire('mobile-money-payment')

        <!-- Help Section -->
        <div class="mt-8 bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <flux:icon.question-mark-circle class="w-5 h-5 inline mr-2 text-blue-600" />
                How Mobile Money Payments Work
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Step-by-step Process:</h4>
                    <ol class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start space-x-2">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-medium">1</span>
                            <span>Select your mobile money provider</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-medium">2</span>
                            <span>Choose the account to deposit to</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-medium">3</span>
                            <span>Enter amount and phone number</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-medium">4</span>
                            <span>Complete payment on your phone</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="bg-green-100 text-green-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-medium">✓</span>
                            <span>Funds are instantly credited to your account</span>
                        </li>
                    </ol>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Important Notes:</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start space-x-2">
                            <flux:icon.exclamation-triangle class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" />
                            <span>Minimum deposit: KES {{ number_format(setting('mobile_money_min_amount', 10), 2) }}</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <flux:icon.exclamation-triangle class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" />
                            <span>Maximum deposit: KES {{ number_format(setting('mobile_money_max_amount', 500000), 2) }}</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <flux:icon.clock class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" />
                            <span>Payments are processed in real-time</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <flux:icon.shield-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                            <span>All transactions are encrypted and secure</span>
                        </li>
                        @if(setting('mobile_money_transaction_fee', 0) > 0)
                        <li class="flex items-start space-x-2">
                            <flux:icon.currency-dollar class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" />
                            <span>Transaction fee: KES {{ number_format(setting('mobile_money_transaction_fee'), 2) }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.my') }}" class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-3">
                    <flux:icon.list-bullet class="w-5 h-5 text-blue-600" />
                    <span class="font-medium text-gray-900">My Transactions</span>
                </div>
                <flux:icon.chevron-right class="w-4 h-4 text-gray-400" />
            </a>
            
            <a href="{{ route('savings.my') }}" class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-3">
                    <flux:icon.banknotes class="w-5 h-5 text-green-600" />
                    <span class="font-medium text-gray-900">My Accounts</span>
                </div>
                <flux:icon.chevron-right class="w-4 h-4 text-gray-400" />
            </a>
            
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-3">
                    <flux:icon.bell class="w-5 h-5 text-purple-600" />
                    <span class="font-medium text-gray-900">Notifications</span>
                </div>
                <flux:icon.chevron-right class="w-4 h-4 text-gray-400" />
            </a>
        </div>
    </div>
</x-layouts.app> 