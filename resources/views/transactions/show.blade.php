<x-layouts.app :title="'Transaction Details'">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('transactions.index') }}" class="p-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-blue-600 rounded-xl transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                            <flux:icon.arrow-left class="w-5 h-5" />
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Transaction Details</h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Review transaction information and take action
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if($transaction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                            @elseif($transaction->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                            @elseif($transaction->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400 @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Transaction Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Overview -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Transaction Overview</h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Transaction Type</label>
                                    <div class="flex items-center">
                                        @if($transaction->type === 'deposit')
                                            <flux:icon.arrow-down class="w-4 h-4 text-emerald-500 mr-2" />
                                        @elseif($transaction->type === 'withdrawal')
                                            <flux:icon.arrow-up class="w-4 h-4 text-red-500 mr-2" />
                                        @elseif($transaction->type === 'transfer')
                                            <flux:icon.arrows-right-left class="w-4 h-4 text-blue-500 mr-2" />
                                        @endif
                                        <span class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Amount</label>
                                    <p class="text-2xl font-bold
                                        @if($transaction->type === 'deposit') text-emerald-600 dark:text-emerald-400
                                        @elseif($transaction->type === 'withdrawal') text-red-600 dark:text-red-400
                                        @else text-blue-600 dark:text-blue-400 @endif">
                                        @if($transaction->type === 'deposit')+@elseif($transaction->type === 'withdrawal')-@endif
                                        KES {{ number_format(abs($transaction->amount), 2) }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Date & Time</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $transaction->created_at->format('M d, Y') }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $transaction->created_at->format('g:i A') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Reference Number</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $transaction->reference_number }}</p>
                                </div>

                                @if($transaction->description)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $transaction->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Member Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Member Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="h-12 w-12 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                                    <span class="text-lg font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ substr($transaction->member->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name }}</h3>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $transaction->member->email }}</p>
                                    @if($transaction->member->phone)
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $transaction->member->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Account Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Account Number</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $transaction->account->account_number }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Account Type</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ ucfirst($transaction->account->account_type) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Current Balance</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">KES {{ number_format($transaction->account->balance, 2) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Account Status</label>
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                        {{ ucfirst($transaction->account->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Transactions (for transfers) -->
                    @if($relatedTransactions->count() > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Related Transactions</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($relatedTransactions as $relatedTransaction)
                                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                                    <flux:icon.arrows-right-left class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ $relatedTransaction->account->member->name }}
                                                    </p>
                                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                                        {{ $relatedTransaction->account->account_number }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                    +KES {{ number_format($relatedTransaction->amount, 2) }}
                                                </p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Credit</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Member Transaction History -->
                    @if($memberHistory->count() > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Member Activity</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($memberHistory->take(5) as $history)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-2 h-2 rounded-full
                                                    @if($history->status === 'completed') bg-emerald-500
                                                    @elseif($history->status === 'pending') bg-amber-500
                                                    @else bg-red-500 @endif">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ ucfirst($history->type) }}
                                                    </p>
                                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                                        {{ $history->created_at->format('M d, Y g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium 
                                                    @if($history->type === 'deposit') text-emerald-600 dark:text-emerald-400
                                                    @else text-red-600 dark:text-red-400 @endif">
                                                    @if($history->type === 'deposit')+@else-@endif
                                                    KES {{ number_format($history->amount, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions Sidebar -->
                <div class="space-y-6">
                    @if($transaction->status === 'pending' && auth()->user()->can('approve', $transaction))
                        <!-- Approval Actions -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                    Approval Actions
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Approve Button -->
                                <button type="button" 
                                    onclick="showApproveModal({{ $transaction->id }}, '{{ strtolower($transaction->type) }}', '{{ number_format($transaction->amount, 0) }}')"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                                    <flux:icon.check class="w-5 h-5 inline mr-2" />
                                    Approve Transaction
                                </button>
                                
                                <!-- Reject Button -->
                                <button type="button" 
                                    onclick="showRejectModal()"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                                    <flux:icon.x-mark class="w-5 h-5 inline mr-2" />
                                    Reject Transaction
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Transaction Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                Transaction Details
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Transaction ID</label>
                                <p class="text-zinc-900 dark:text-zinc-100 font-mono text-sm">{{ $transaction->id }}</p>
                            </div>

                            @if($transaction->metadata && isset($transaction->metadata['requires_approval']) && $transaction->metadata['requires_approval'])
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Requires Approval</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $transaction->metadata['requires_approval'] ? 'Yes' : 'No' }}</p>
                                </div>
                            @endif

                            @if($transaction->metadata && isset($transaction->metadata['channel']))
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Channel</label>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ ucfirst($transaction->metadata['channel']) }}</p>
                                </div>
                            @endif

                            @if($transaction->metadata && isset($transaction->metadata['approved_by']))
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Approval Date</label>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ $transaction->updated_at->format('M d, Y g:i A') }}</p>
                                </div>
                            @elseif($transaction->status === 'pending')
                                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                    <p class="text-sm text-amber-600 dark:text-amber-400">Waiting for management approval</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                Quick Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('transactions.receipt', $transaction) }}" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <flux:icon.document-text class="w-4 h-4 mr-2" />
                                View Receipt
                            </a>
                            
                            <a href="{{ route('transactions.index') }}" 
                                class="w-full bg-zinc-600 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                                Back to Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    @if($transaction->status === 'pending' && auth()->user()->can('approve', $transaction))
        <div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full mr-4">
                                <flux:icon.check class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Approve Transaction</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400" id="approveTransactionDetails"></p>
                            </div>
                        </div>
                        
                        <form id="approveForm" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Approval Comments (Optional)</label>
                                <textarea name="comments" rows="3" 
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100" 
                                    placeholder="Add any comments about this approval..."></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeApproveModal()" 
                                    class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                                    Cancel
                                </button>
                                <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Approve Transaction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Reject Transaction</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                            Please provide a reason for rejecting this transaction.
                        </p>
                        
                        <form method="POST" action="{{ route('transactions.reject', $transaction) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rejection Reason *</label>
                                <textarea name="reason" rows="3" required 
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100" 
                                    placeholder="Enter reason for rejection..."></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeRejectModal()" 
                                    class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                                    Cancel
                                </button>
                                <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Reject Transaction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function showApproveModal(transactionId, transactionType, amount) {
            const form = document.getElementById('approveForm');
            const modal = document.getElementById('approveModal');
            const details = document.getElementById('approveTransactionDetails');
            
            form.action = `/transactions/approve/${transactionId}`;
            details.textContent = `Approve this ${transactionType} of KES ${amount}`;
            
            // Clear previous comments
            form.querySelector('textarea[name="comments"]').value = '';
            
            // Show modal
            modal.classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                closeApproveModal();
                closeRejectModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });
    </script>
</x-layouts.app> 