<x-layouts.app :title="__('Transaction Receipt')">
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
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Transaction Receipt</h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Transaction Reference: {{ $transaction->reference_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.print()" class="bg-zinc-600 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <flux:icon.printer class="w-4 h-4 mr-2" />
                            Print
                        </button>
                        <a href="{{ route('transactions.receipt.download', $transaction) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <flux:icon.arrow-down-tray class="w-4 h-4 mr-2" />
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                        <div class="flex">
                            <flux:icon.check-circle class="w-5 h-5 text-emerald-400 mr-3 flex-shrink-0" />
                            <p class="text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Receipt -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <!-- Receipt Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold">SaccoCore</h2>
                                <p class="text-blue-100">Digital Banking Platform</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">
                                    @if($transaction->status === 'completed')
                                        <flux:icon.check-circle class="w-12 h-12 text-emerald-300" />
                                    @elseif($transaction->status === 'pending')
                                        <flux:icon.clock class="w-12 h-12 text-amber-300" />
                                    @elseif($transaction->status === 'failed')
                                        <flux:icon.x-circle class="w-12 h-12 text-red-300" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Body -->
                    <div class="p-8">
                        <!-- Transaction Status -->
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium mb-4
                                @if($transaction->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                @elseif($transaction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                @elseif($transaction->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                {{ ucfirst($transaction->status) }}
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                @if($transaction->status === 'completed') Completed
                                @elseif($transaction->status === 'pending') Pending Approval
                                @elseif($transaction->status === 'failed') Failed
                                @endif
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ $transaction->created_at->format('l, F j, Y \a\t g:i A') }}
                            </p>
                        </div>

                        <!-- Transaction Amount -->
                        <div class="text-center mb-8 p-6 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Transaction Amount</p>
                            <p class="text-4xl font-bold 
                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0)) text-emerald-600 dark:text-emerald-400
                                @elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0)) text-red-600 dark:text-red-400
                                @else text-blue-600 dark:text-blue-400 @endif">
                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0))+@elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0))-@endif
                                KES {{ number_format(abs($transaction->amount), 2) }}
                            </p>
                        </div>

                        <!-- Transaction Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Transaction Information</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Reference Number:</span>
                                            <span class="text-sm font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->reference_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Transaction Type:</span>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Date & Time:</span>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        @if($transaction->description)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Description:</span>
                                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->description }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($transaction->metadata && isset($transaction->metadata['requires_approval']) && $transaction->metadata['requires_approval'])
                                    <div>
                                        <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Approval Information</h4>
                                        <div class="space-y-2">
                                            @if(isset($transaction->metadata['approved_by']))
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Approved By:</span>
                                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Staff Member</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Approval Date:</span>
                                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->metadata['approved_at'] ?? 'N/A' }}</span>
                                                </div>
                                            @else
                                                <p class="text-sm text-amber-600 dark:text-amber-400">Waiting for management approval</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Account Information</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Number:</span>
                                            <span class="text-sm font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->account->account_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Type:</span>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($transaction->account->account_type) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Account Holder:</span>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Member ID:</span>
                                            <span class="text-sm font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ str_pad($transaction->member->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($transaction->status === 'completed')
                                    <div>
                                        <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Balance Information</h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Balance Before:</span>
                                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($transaction->balance_before, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Transaction Amount:</span>
                                                <span class="text-sm font-medium 
                                                    @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0)) text-emerald-600 dark:text-emerald-400
                                                    @elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0)) text-red-600 dark:text-red-400
                                                    @else text-blue-600 dark:text-blue-400 @endif">
                                                    @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0))+@elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0))-@endif
                                                    KES {{ number_format(abs($transaction->amount), 2) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Balance After:</span>
                                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">KES {{ number_format($transaction->balance_after, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Transfer Details (if applicable) -->
                        @if($transaction->type === 'transfer' && session('transfer_reference'))
                            <div class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Transfer Information</h4>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    This transaction is part of a transfer with reference number: <span class="font-mono font-medium">{{ session('transfer_reference') }}</span>
                                </p>
                            </div>
                        @endif

                        <!-- Security Information -->
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                            <div class="flex items-center justify-center space-x-6 text-sm text-zinc-500 dark:text-zinc-400">
                                <div class="flex items-center">
                                    <flux:icon.shield-check class="w-4 h-4 mr-2" />
                                    <span>Secure Transaction</span>
                                </div>
                                <div class="flex items-center">
                                    <flux:icon.lock-closed class="w-4 h-4 mr-2" />
                                    <span>Encrypted</span>
                                </div>
                                <div class="flex items-center">
                                    <flux:icon.check-badge class="w-4 h-4 mr-2" />
                                    <span>Verified</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center text-xs text-zinc-400 dark:text-zinc-500 mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-700">
                            <p>This is a computer-generated receipt. For any queries, please contact SaccoCore support.</p>
                            <p class="mt-1">Generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('transactions.index') }}" 
                        class="w-full sm:w-auto bg-zinc-600 hover:bg-zinc-700 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                        Back to Transactions
                    </a>
                    
                    @if($transaction->type === 'deposit')
                        <a href="{{ route('transactions.deposit.create') }}" 
                            class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                            Make Another Deposit
                        </a>
                    @elseif($transaction->type === 'withdrawal')
                        <a href="{{ route('transactions.withdrawal.create') }}" 
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                            Make Another Withdrawal
                        </a>
                    @elseif($transaction->type === 'transfer')
                        <a href="{{ route('transactions.transfer.create') }}" 
                            class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                            Make Another Transfer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print:hidden { display: none !important; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</x-layouts.app> 