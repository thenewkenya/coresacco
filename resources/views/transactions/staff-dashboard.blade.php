@php
    $user = auth()->user();
@endphp

<x-layouts.app :title="__('Transaction Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Transaction Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Monitor and approve transactions</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('transactions.deposit.create') }}" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                            <flux:icon.plus class="w-4 h-4 inline mr-2" />
                            <span class="hidden sm:inline">New Deposit</span>
                            <span class="sm:hidden">Deposit</span>
                        </a>
                        <a href="{{ route('transactions.withdrawal.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                            <flux:icon.arrow-up class="w-4 h-4 inline mr-2" />
                            <span class="hidden sm:inline">Process Withdrawal</span>
                            <span class="sm:hidden">Withdrawal</span>
                        </a>
                        <a href="{{ route('transactions.transfer.create') }}" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                            <flux:icon.arrows-right-left class="w-4 h-4 inline mr-2" />
                            <span class="hidden sm:inline">Transfer Funds</span>
                            <span class="sm:hidden">Transfer</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Daily Statistics -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Today's Statistics</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 lg:gap-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.document-text class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">Total Transactions</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $todayStats['total_transactions'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.banknotes class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">Total Volume</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                    KES {{ number_format($todayStats['total_amount'], 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.arrow-down class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">Deposits</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                    KES {{ number_format($todayStats['deposits'], 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-red-100 dark:bg-red-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.arrow-up class="w-5 h-5 lg:w-6 lg:h-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">Withdrawals</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                    KES {{ number_format($todayStats['withdrawals'], 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.clock class="w-5 h-5 lg:w-6 lg:h-6 text-amber-600 dark:text-amber-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">Pending Approvals</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $todayStats['pending_approvals'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simple Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Quick Status Filter -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status:</label>
                            <select name="status" onchange="window.location.href='{{ route('transactions.index') }}?status=' + this.value" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        
                        @if($status === 'pending')
                            <!-- Quick Priority Filter -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('transactions.index', ['status' => 'pending', 'priority' => 'high']) }}" 
                                   class="px-3 py-2 text-sm {{ request('priority') === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300' }} rounded-lg transition-colors">
                                    High Value Only
                                </a>
                                <a href="{{ route('transactions.index', ['status' => 'pending']) }}" 
                                   class="px-3 py-2 text-sm {{ !request('priority') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300' }} rounded-lg transition-colors">
                                    Show All
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $pendingTransactions->count() }} of {{ $pendingTransactions->total() }} transactions
                    </div>
                </div>
            </div>

            <!-- Pending Transactions -->
            @if($pendingTransactions->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                    <flux:icon.clock class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $status === 'pending' ? 'Pending Approvals' : ucfirst($status) . ' Transactions' }}
                                    </h2>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $pendingTransactions->count() }} transactions</p>
                                </div>
                            </div>
                            @if($status === 'pending')
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="toggleBulkActions()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Bulk Actions
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Keyboard Shortcuts Help Panel -->
                    <div id="shortcutsHelp" class="hidden bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 flex items-center">
                                <flux:icon.academic-cap class="w-4 h-4 mr-2" />
                                ⌨️ Keyboard Shortcuts & Tips
                            </h3>
                            <button onclick="localStorage.setItem('approvals_tour_completed', 'true')" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                Don't show tips again
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <kbd class="px-2 py-1 bg-zinc-100 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded text-xs font-mono">Ctrl+A</kbd>
                                    <span class="text-blue-700 dark:text-blue-300 ml-2 font-medium">Select All</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Quickly select all visible transactions for bulk actions.</p>
                            </div>
                            <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <kbd class="px-2 py-1 bg-zinc-100 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded text-xs font-mono">Ctrl+Enter</kbd>
                                    <span class="text-blue-700 dark:text-blue-300 ml-2 font-medium">Bulk Approve</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Quick bulk approve for selected transactions.</p>
                            </div>
                            <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <kbd class="px-2 py-1 bg-zinc-100 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded text-xs font-mono">Esc</kbd>
                                    <span class="text-blue-700 dark:text-blue-300 ml-2 font-medium">Close Modals</span>
                                </div>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Quickly close any open modals or dialogs.</p>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20 rounded-lg border border-emerald-200 dark:border-emerald-700">
                            <div class="flex items-start">
                                <flux:icon.light-bulb class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-3 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200 mb-1">💡 Pro Tips</h4>
                                    <ul class="text-xs text-emerald-700 dark:text-emerald-300 space-y-1">
                                        <li>• Use <strong>priority filters</strong> to focus on urgent transactions first</li>
                                        <li>• <strong>Quick rejection reasons</strong> save time when rejecting common issues</li>
                                        <li>• Page <strong>auto-refreshes</strong> every 30 seconds to show latest transactions</li>
                                        <li>• Transactions over <strong>24 hours old</strong> are highlighted in red</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($status === 'pending')
                        <!-- Bulk Actions -->
                        <div id="bulkActions" class="hidden p-4 bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Select All</span>
                                    </label>
                                    <span id="selectedCount" class="text-sm text-zinc-600 dark:text-zinc-400">0 selected</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="showBulkApproveModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" disabled id="bulkApproveBtn">
                                        Bulk Approve
                                    </button>
                                    <button type="button" onclick="showBulkRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" disabled id="bulkRejectBtn">
                                        Bulk Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- User Guide -->
                    @if($status === 'pending')
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-6">
                            <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                                <flux:icon.information-circle class="w-4 h-4 mr-2 flex-shrink-0" />
                                <span><strong>Quick tip:</strong> Click any transaction row to view full details, or use the Approve/Reject buttons for quick actions</span>
                            </div>
                        </div>
                    @endif

                    <!-- Transactions Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    @if($status === 'pending')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            <input type="checkbox" class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 hidden" id="headerCheckbox">
                                        </th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Member</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Account</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($pendingTransactions as $transaction)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer" 
                                        onclick="window.location.href='{{ route('transactions.show', $transaction) }}'"
                                        title="Click to view full transaction details">
                                        @if($status === 'pending')
                                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                                <input type="checkbox" name="transaction_ids[]" value="{{ $transaction->id }}" class="transaction-checkbox rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500">
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                            <br>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('g:i A') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                                            {{ substr($transaction->member->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name }}</p>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->member->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($transaction->type === 'deposit')
                                                    <flux:icon.arrow-down class="w-4 h-4 text-emerald-500 mr-2" />
                                                @elseif($transaction->type === 'withdrawal')
                                                    <flux:icon.arrow-up class="w-4 h-4 text-red-500 mr-2" />
                                                @elseif($transaction->type === 'transfer')
                                                    <flux:icon.arrows-right-left class="w-4 h-4 text-blue-500 mr-2" />
                                                @endif
                                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                                @if($transaction->amount >= 50000)
                                                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                                        High Value
                                                    </span>
                                                @endif
                                                
                                                <!-- Time indicator -->
                                                @php
                                                    $hoursOld = $transaction->created_at->diffInHours(now());
                                                @endphp
                                                @if($hoursOld > 24)
                                                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                                        {{ $hoursOld }}h old
                                                    </span>
                                                @elseif($hoursOld > 8)
                                                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">
                                                        {{ $hoursOld }}h old
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $transaction->account->account_number }}
                                            <br>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($transaction->account->account_type) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $transaction->description }}
                                            @if($transaction->reference_number)
                                                <br>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Ref: {{ $transaction->reference_number }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                            <span class="
                                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0)) text-emerald-600 dark:text-emerald-400
                                                @elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0)) text-red-600 dark:text-red-400
                                                @else text-blue-600 dark:text-blue-400 @endif">
                                                @if($transaction->type === 'deposit' || ($transaction->type === 'transfer' && $transaction->amount > 0))+@elseif($transaction->type === 'withdrawal' || ($transaction->type === 'transfer' && $transaction->amount < 0))-@endif
                                                KES {{ number_format(abs($transaction->amount), 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if($status === 'pending')
                                                    @can('approve', $transaction)
                                                        <!-- Approve Button -->
                                                        <button type="button" 
                                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors"
                                                            onclick="showApproveModal({{ $transaction->id }}, '{{ strtolower($transaction->type) }}', '{{ number_format($transaction->amount, 0) }}')">
                                                            Approve
                                                        </button>
                                                        
                                                        <!-- Reject Button -->
                                                        <button type="button" 
                                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors"
                                                            onclick="showRejectModal({{ $transaction->id }})">
                                                            Reject
                                                        </button>
                                                    @endcan
                                                @else
                                                    <span class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                        Click row to view
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $pendingTransactions->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <div class="p-3 bg-zinc-100 dark:bg-zinc-700 rounded-full w-16 h-16 mx-auto mb-4">
                        <flux:icon.banknotes class="w-10 h-10 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No transactions found</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 text-lg mb-6">No transactions match your current filters.</p>
                    <a href="{{ route('transactions.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Approve Modal -->
    @if($status === 'pending')
        <div id="bulkApproveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Bulk Approve Transactions</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                            Are you sure you want to approve the selected transactions?
                        </p>
                        
                        <form id="bulkApproveForm" method="POST" action="{{ route('transactions.bulk.approve') }}">
                            @csrf
                            <input type="hidden" name="transaction_ids" id="bulkApproveIds">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Comments (Optional)</label>
                                <textarea name="comments" rows="3" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100" placeholder="Add approval comments..."></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeBulkApproveModal()" class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Approve Selected
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Reject Modal -->
        <div id="bulkRejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Bulk Reject Transactions</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                            Are you sure you want to reject the selected transactions?
                        </p>
                        
                        <form id="bulkRejectForm" method="POST" action="{{ route('transactions.bulk.reject') }}">
                            @csrf
                            <input type="hidden" name="transaction_ids" id="bulkRejectIds">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rejection Reason *</label>
                                <textarea name="reason" rows="3" required class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100" placeholder="Enter reason for rejection..."></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeBulkRejectModal()" class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Reject Selected
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
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
                        
                        <form id="rejectForm" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rejection Reason *</label>
                                <textarea id="rejectionReason" name="reason" rows="3" required 
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

    <!-- JavaScript -->
    <script>
        // Bulk actions functionality
                 function toggleBulkActions() {
             const bulkActions = document.getElementById('bulkActions');
             const headerCheckbox = document.getElementById('headerCheckbox');
             
             if (bulkActions.classList.contains('hidden')) {
                 bulkActions.classList.remove('hidden');
                 headerCheckbox.classList.remove('hidden');
             } else {
                 bulkActions.classList.add('hidden');
                 headerCheckbox.classList.add('hidden');
                 // Clear all selections
                 document.querySelectorAll('.transaction-checkbox').forEach(cb => cb.checked = false);
                 document.getElementById('selectAll').checked = false;
                 updateBulkButtons();
             }
         }

         function toggleShortcutsHelp() {
             const helpPanel = document.getElementById('shortcutsHelp');
             if (helpPanel.classList.contains('hidden')) {
                 helpPanel.classList.remove('hidden');
                 helpPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
             } else {
                 helpPanel.classList.add('hidden');
             }
         }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.transaction-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButtons();
        });

        // Individual checkbox functionality
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('transaction-checkbox')) {
                updateBulkButtons();
                
                // Update select all checkbox
                const allCheckboxes = document.querySelectorAll('.transaction-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.transaction-checkbox:checked');
                document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
            }
        });

        function updateBulkButtons() {
            const checkedCheckboxes = document.querySelectorAll('.transaction-checkbox:checked');
            const count = checkedCheckboxes.length;
            
            document.getElementById('selectedCount').textContent = `${count} selected`;
            document.getElementById('bulkApproveBtn').disabled = count === 0;
            document.getElementById('bulkRejectBtn').disabled = count === 0;
        }

        function getSelectedIds() {
            const checkedCheckboxes = document.querySelectorAll('.transaction-checkbox:checked');
            return Array.from(checkedCheckboxes).map(cb => cb.value);
        }

        function showBulkApproveModal() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) return;
            
            document.getElementById('bulkApproveIds').value = JSON.stringify(selectedIds);
            document.getElementById('bulkApproveModal').classList.remove('hidden');
        }

        function closeBulkApproveModal() {
            document.getElementById('bulkApproveModal').classList.add('hidden');
        }

        function showBulkRejectModal() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) return;
            
            document.getElementById('bulkRejectIds').value = JSON.stringify(selectedIds);
            document.getElementById('bulkRejectModal').classList.remove('hidden');
        }

        function closeBulkRejectModal() {
            document.getElementById('bulkRejectModal').classList.add('hidden');
        }

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

        function showRejectModal(transactionId) {
            const form = document.getElementById('rejectForm');
            const modal = document.getElementById('rejectModal');
            const modalContent = modal.querySelector('div > div');
            
            form.action = `/transactions/reject/${transactionId}`;
            document.getElementById('rejectionReason').value = '';
            
            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            const modalContent = modal.querySelector('div > div');
            
            // Hide modal with animation
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function selectReason(reason) {
            const textarea = document.getElementById('rejectionReason');
            textarea.value = reason;
            textarea.focus();
            
            // Highlight the selected quick reason button
            document.querySelectorAll('.quick-reason-btn').forEach(btn => {
                btn.classList.remove('bg-red-100', 'dark:bg-red-900/30', 'border-red-300', 'border-2');
                btn.classList.add('bg-zinc-50', 'dark:bg-zinc-700');
            });
            
            event.target.closest('.quick-reason-btn').classList.remove('bg-zinc-50', 'dark:bg-zinc-700');
            event.target.closest('.quick-reason-btn').classList.add('bg-red-100', 'dark:bg-red-900/30', 'border-red-300', 'border-2');
        }

                 // Close modals when clicking outside
         document.addEventListener('click', function(e) {
             if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                 closeBulkApproveModal();
                 closeBulkRejectModal();
                 closeApproveModal();
                 closeRejectModal();
             }
         });

         // Keyboard shortcuts for improved UX
         document.addEventListener('keydown', function(e) {
             // Escape key closes modals
             if (e.key === 'Escape') {
                 closeBulkApproveModal();
                 closeBulkRejectModal();
                 closeApproveModal();
                 closeRejectModal();
             }
             
             // Ctrl/Cmd + A for select all (when bulk actions are visible)
             if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !document.getElementById('bulkActions').classList.contains('hidden')) {
                 e.preventDefault();
                 document.getElementById('selectAll').checked = true;
                 document.querySelectorAll('.transaction-checkbox').forEach(cb => cb.checked = true);
                 updateBulkButtons();
             }
             
             // Ctrl/Cmd + Enter for quick bulk approve
             if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && !document.getElementById('bulkActions').classList.contains('hidden')) {
                 e.preventDefault();
                 const checkedBoxes = document.querySelectorAll('.transaction-checkbox:checked');
                 if (checkedBoxes.length > 0) {
                     showBulkApproveModal();
                 }
             }
         });

         // Auto-refresh for real-time updates (every 30 seconds)
         let autoRefreshInterval;
         function startAutoRefresh() {
             autoRefreshInterval = setInterval(() => {
                 // Only refresh if we're on pending status and no modals are open
                 const urlParams = new URLSearchParams(window.location.search);
                 const status = urlParams.get('status') || 'pending';
                 
                 if (status === 'pending' && 
                     document.getElementById('bulkApproveModal').classList.contains('hidden') &&
                     document.getElementById('bulkRejectModal').classList.contains('hidden') &&
                     document.getElementById('approveModal').classList.contains('hidden') &&
                     document.getElementById('rejectModal').classList.contains('hidden')) {
                     
                     // Show subtle loading indicator
                     showRefreshIndicator();
                     
                     // Refresh the page to get latest data
                     window.location.reload();
                 }
             }, 30000); // 30 seconds
         }

         function showRefreshIndicator() {
             // Create a subtle refresh indicator
             const indicator = document.createElement('div');
             indicator.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 flex items-center';
             indicator.innerHTML = `
                 <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                 Refreshing...
             `;
             document.body.appendChild(indicator);
             
             setTimeout(() => {
                 if (indicator.parentNode) {
                     indicator.parentNode.removeChild(indicator);
                 }
             }, 2000);
         }

         // Start auto-refresh when page loads
         document.addEventListener('DOMContentLoaded', function() {
             startAutoRefresh();
             
             // Show helpful tooltips for first-time users
             if (!localStorage.getItem('approvals_tour_completed')) {
                 setTimeout(showHelpfulTips, 2000);
             }
         });

         function showHelpfulTips() {
             const tips = [
                 { element: '#bulkActions button', message: 'Use bulk actions to approve multiple transactions at once!' },
                 { element: '.quick-approve-btn', message: 'Click here for quick approval without additional comments' },
                 { element: '.filter-controls', message: 'Use filters to quickly find specific transactions' }
             ];
             
             // Show first tip (you could extend this to show all tips in sequence)
             const firstTip = tips[0];
             const element = document.querySelector(firstTip.element);
             if (element) {
                 showTooltip(element, firstTip.message);
             }
         }

         function showTooltip(element, message) {
             const tooltip = document.createElement('div');
             tooltip.className = 'absolute bg-blue-600 text-white text-sm px-3 py-2 rounded-lg shadow-lg z-50 max-w-xs';
             tooltip.innerHTML = `
                 ${message}
                 <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-blue-600"></div>
             `;
             
             const rect = element.getBoundingClientRect();
             tooltip.style.left = rect.left + 'px';
             tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
             
             document.body.appendChild(tooltip);
             
             setTimeout(() => {
                 if (tooltip.parentNode) {
                     tooltip.parentNode.removeChild(tooltip);
                 }
             }, 5000);
         }

         // Success feedback with sound (optional)
         function playSuccessSound() {
             // Only play if user hasn't disabled sounds
             if (!localStorage.getItem('sounds_disabled')) {
                 const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFApGn+DyvmccBjiS2O/McysEJHfH8N2QQAoUXrTp66hVFA==');
                 audio.volume = 0.3;
                 audio.play().catch(() => {}); // Ignore errors if audio fails
             }
         }

         // Enhanced form submission with better feedback
         document.addEventListener('submit', function(e) {
             if (e.target.closest('#rejectForm, #bulkApproveForm, #bulkRejectForm')) {
                 const submitBtn = e.target.querySelector('button[type="submit"]');
                 if (submitBtn) {
                     submitBtn.disabled = true;
                     submitBtn.innerHTML = `
                         <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                         Processing...
                     `;
                 }
             }
         });
    </script>
</x-layouts.app> 