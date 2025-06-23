<x-layouts.app :title="__('Payment Details')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Payment Details') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            @role('member')
                                {{ __('View your payment transaction details') }}
                            @else
                                {{ __('Review payment transaction and manage status') }}
                            @endrole
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @roleany('admin', 'manager', 'staff')
                        <flux:button variant="outline" :href="route('payments.receipt', $transaction)" icon="printer">
                            {{ __('Print Receipt') }}
                        </flux:button>
                        @endroleany
                        <flux:button variant="ghost" :href="route('payments.index')" icon="arrow-left">
                            {{ __('Back to Payments') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Status Banner -->
            <div class="mb-6">
                @if($transaction->status === 'completed')
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" />
                        <div>
                            <h3 class="font-medium text-green-800 dark:text-green-200">{{ __('Payment Completed') }}</h3>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                {{ __('This payment has been successfully processed and completed.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($transaction->status === 'pending')
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <flux:icon.clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" />
                            <div>
                                <h3 class="font-medium text-yellow-800 dark:text-yellow-200">{{ __('Payment Pending') }}</h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    {{ __('This payment is awaiting approval or processing.') }}
                                </p>
                            </div>
                        </div>
                        @roleany('admin', 'manager', 'staff')
                        <div class="flex items-center space-x-2">
                            <flux:button variant="primary" size="sm" 
                                        onclick="document.getElementById('approve-form').submit()">
                                {{ __('Approve') }}
                            </flux:button>
                            <flux:button variant="danger" size="sm" 
                                        onclick="document.getElementById('reject-form').submit()">
                                {{ __('Reject') }}
                            </flux:button>
                        </div>
                        @endroleany
                    </div>
                </div>
                @elseif($transaction->status === 'failed')
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <flux:icon.x-circle class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" />
                        <div>
                            <h3 class="font-medium text-red-800 dark:text-red-200">{{ __('Payment Failed') }}</h3>
                            <p class="text-sm text-red-700 dark:text-red-300">
                                {{ __('This payment could not be processed. Please contact support for assistance.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Payment Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Payment Overview -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Payment Overview') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Transaction ID') }}</flux:label>
                                    <div class="flex items-center space-x-2">
                                        <code class="px-3 py-2 bg-zinc-100 dark:bg-zinc-700 rounded text-sm font-mono">
                                            {{ $transaction->reference_number }}
                                        </code>
                                        <flux:button variant="ghost" size="sm" onclick="copyToClipboard('{{ $transaction->reference_number }}')">
                                            <flux:icon.clipboard class="w-4 h-4" />
                                        </flux:button>
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Amount') }}</flux:label>
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        KES {{ number_format($transaction->amount, 2) }}
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Payment Type') }}</flux:label>
                                    <div class="flex items-center space-x-2">
                                        @if($transaction->type === 'deposit')
                                            <flux:icon.arrow-down-circle class="w-5 h-5 text-green-600" />
                                            <span class="font-medium text-green-600">{{ __('Deposit') }}</span>
                                        @elseif($transaction->type === 'withdrawal')
                                            <flux:icon.arrow-up-circle class="w-5 h-5 text-red-600" />
                                            <span class="font-medium text-red-600">{{ __('Withdrawal') }}</span>
                                        @elseif($transaction->type === 'loan_repayment')
                                            <flux:icon.credit-card class="w-5 h-5 text-blue-600" />
                                            <span class="font-medium text-blue-600">{{ __('Loan Repayment') }}</span>
                                        @elseif($transaction->type === 'transfer')
                                            <flux:icon.arrows-right-left class="w-5 h-5 text-purple-600" />
                                            <span class="font-medium text-purple-600">{{ __('Transfer') }}</span>
                                        @else
                                            <flux:icon.banknotes class="w-5 h-5 text-zinc-600" />
                                            <span class="font-medium text-zinc-600">{{ ucfirst($transaction->type) }}</span>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Payment Method') }}</flux:label>
                                    <div class="flex items-center space-x-2">
                                        @if(($transaction->metadata['payment_method'] ?? null) === 'cash')
                                            <flux:icon.banknotes class="w-5 h-5 text-green-600" />
                                            <span class="font-medium">{{ __('Cash') }}</span>
                                        @elseif(($transaction->metadata['payment_method'] ?? null) === 'mobile_money')
                                            <flux:icon.device-phone-mobile class="w-5 h-5 text-blue-600" />
                                            <span class="font-medium">{{ __('Mobile Money') }}</span>
                                        @elseif(($transaction->metadata['payment_method'] ?? null) === 'bank_transfer')
                                            <flux:icon.building-library class="w-5 h-5 text-purple-600" />
                                            <span class="font-medium">{{ __('Bank Transfer') }}</span>
                                        @elseif(($transaction->metadata['payment_method'] ?? null) === 'cheque')
                                            <flux:icon.document-text class="w-5 h-5 text-orange-600" />
                                            <span class="font-medium">{{ __('Cheque') }}</span>
                                        @else
                                            <span class="font-medium">{{ ucfirst($transaction->metadata['payment_method'] ?? 'Unknown') }}</span>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Date & Time') }}</flux:label>
                                    <div class="space-y-1">
                                        <div class="font-medium">{{ $transaction->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-zinc-500">{{ $transaction->created_at->format('g:i A') }}</div>
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Status') }}</flux:label>
                                    <div>
                                        @if($transaction->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <flux:icon.check class="w-3 h-3 mr-1" />
                                                {{ __('Completed') }}
                                            </span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <flux:icon.clock class="w-3 h-3 mr-1" />
                                                {{ __('Pending') }}
                                            </span>
                                        @elseif($transaction->status === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                <flux:icon.x-mark class="w-3 h-3 mr-1" />
                                                {{ __('Failed') }}
                                            </span>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>
                        </div>

                        @if($transaction->description)
                        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:field>
                                <flux:label>{{ __('Description') }}</flux:label>
                                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $transaction->description }}</p>
                            </flux:field>
                        </div>
                        @endif
                    </div>

                    <!-- Transaction Timeline -->
                    @roleany('admin', 'manager', 'staff')
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Transaction Timeline') }}
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach([
                                [
                                    'timestamp' => $transaction->created_at,
                                    'title' => 'Payment Initiated',
                                    'description' => 'Payment request submitted by ' . ($transaction->member->name ?? 'system'),
                                    'status' => 'completed',
                                    'icon' => 'plus-circle'
                                ],
                                [
                                    'timestamp' => $transaction->updated_at,
                                    'title' => $transaction->status === 'completed' ? 'Payment Processed' : 'Status Updated',
                                    'description' => 'Payment ' . $transaction->status . ($transaction->processed_by ? ' by ' . $transaction->processed_by : ''),
                                    'status' => $transaction->status === 'completed' ? 'completed' : ($transaction->status === 'failed' ? 'failed' : 'pending'),
                                    'icon' => $transaction->status === 'completed' ? 'check-circle' : ($transaction->status === 'failed' ? 'x-circle' : 'clock')
                                ]
                            ] as $event)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @if($event['status'] === 'completed')
                                        <flux:icon.{{ $event['icon'] }} class="w-6 h-6 text-green-600 dark:text-green-400" />
                                    @elseif($event['status'] === 'failed')
                                        <flux:icon.{{ $event['icon'] }} class="w-6 h-6 text-red-600 dark:text-red-400" />
                                    @else
                                        <flux:icon.{{ $event['icon'] }} class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $event['title'] }}</h4>
                                        <span class="text-sm text-zinc-500">{{ $event['timestamp']->format('M d, g:i A') }}</span>
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $event['description'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endroleany

                    <!-- Additional Details -->
                    @if($transaction->metadata && count($transaction->metadata) > 0)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Additional Details') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($transaction->metadata as $key => $value)
                            <div>
                                <flux:field>
                                    <flux:label>{{ ucfirst(str_replace('_', ' ', $key)) }}</flux:label>
                                    <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $value }}</div>
                                </flux:field>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Member Information -->
                    @roleany('admin', 'manager', 'staff')
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Member Information') }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <flux:icon.user class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-zinc-500">{{ $transaction->member->email ?? 'N/A' }}</div>
                                </div>
                            </div>

                            @if($transaction->member->member_number)
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Member Number') }}</flux:label>
                                    <div class="text-sm text-zinc-700 dark:text-zinc-300">#{{ $transaction->member->member_number }}</div>
                                </flux:field>
                            </div>
                            @endif

                            @if($transaction->member->phone)
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Phone Number') }}</flux:label>
                                    <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $transaction->member->phone }}</div>
                                </flux:field>
                            </div>
                            @endif

                            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:button variant="outline" size="sm" class="w-full" 
                                            :href="route('members.show', $transaction->member)">
                                    {{ __('View Member Profile') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endroleany

                    <!-- Account Information -->
                    @if($transaction->account)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Account Information') }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Account Number') }}</flux:label>
                                    <div class="text-sm font-mono text-zinc-700 dark:text-zinc-300">{{ $transaction->account->account_number }}</div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Account Type') }}</flux:label>
                                    <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ ucfirst($transaction->account->account_type) }}</div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Current Balance') }}</flux:label>
                                    <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                        KES {{ number_format($transaction->account->balance, 2) }}
                                    </div>
                                </flux:field>
                            </div>

                            @roleany('admin', 'manager', 'staff')
                            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:button variant="outline" size="sm" class="w-full" 
                                            :href="route('savings.show', $transaction->account)">
                                    {{ __('View Account Details') }}
                                </flux:button>
                            </div>
                            @endroleany
                        </div>
                    </div>
                    @endif

                    <!-- Loan Information -->
                    @if($transaction->loan)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Loan Information') }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Loan ID') }}</flux:label>
                                    <div class="text-sm font-mono text-zinc-700 dark:text-zinc-300">#{{ $transaction->loan->id }}</div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Loan Amount') }}</flux:label>
                                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        KES {{ number_format($transaction->loan->amount, 2) }}
                                    </div>
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>{{ __('Outstanding Balance') }}</flux:label>
                                    <div class="text-sm font-medium text-orange-600 dark:text-orange-400">
                                        KES {{ number_format($transaction->loan->outstanding_balance ?? 0, 2) }}
                                    </div>
                                </flux:field>
                            </div>

                            @roleany('admin', 'manager', 'staff')
                            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:button variant="outline" size="sm" class="w-full" 
                                            :href="route('loans.show', $transaction->loan)">
                                    {{ __('View Loan Details') }}
                                </flux:button>
                            </div>
                            @endroleany
                        </div>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    @roleany('admin', 'manager', 'staff')
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Quick Actions') }}
                        </h3>
                        
                        <div class="space-y-3">
                            <flux:button variant="outline" size="sm" class="w-full justify-start" 
                                        :href="route('payments.receipt', $transaction)">
                                <flux:icon.printer class="w-4 h-4 mr-2" />
                                {{ __('Print Receipt') }}
                            </flux:button>

                            @if($transaction->status === 'pending')
                            <flux:button variant="primary" size="sm" class="w-full justify-start" 
                                        onclick="document.getElementById('approve-form').submit()">
                                <flux:icon.check class="w-4 h-4 mr-2" />
                                {{ __('Approve Payment') }}
                            </flux:button>

                            <flux:button variant="danger" size="sm" class="w-full justify-start" 
                                        onclick="document.getElementById('reject-form').submit()">
                                <flux:icon.x-mark class="w-4 h-4 mr-2" />
                                {{ __('Reject Payment') }}
                            </flux:button>
                            @endif

                            @role('admin')
                            <flux:button variant="ghost" size="sm" class="w-full justify-start text-red-600" 
                                        onclick="showReversalModal()">
                                <flux:icon.arrow-uturn-left class="w-4 h-4 mr-2" />
                                {{ __('Reverse Payment') }}
                            </flux:button>
                            @endrole
                        </div>
                    </div>
                    @endroleany
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Actions -->
    @roleany('admin', 'manager', 'staff')
    @if($transaction->status === 'pending')
    <form id="approve-form" method="POST" action="{{ route('payments.approve', $transaction) }}" style="display: none;">
        @csrf
    </form>

    <form id="reject-form" method="POST" action="{{ route('payments.reject', $transaction) }}" style="display: none;">
        @csrf
    </form>
    @endif

    @role('admin')
    <form id="reverse-form" method="POST" action="{{ route('payments.reverse', $transaction) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @endrole
    @endroleany

    <!-- Reverse Payment Confirmation Modal -->
    @role('admin')
    <flux:modal name="reverse-payment-modal" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="mt-3">
                    <flux:heading size="lg">{{ __('Reverse Payment') }}</flux:heading>
                    <div class="mt-2">
                        <flux:subheading>
                            {{ __('Are you sure you want to reverse this payment? This action cannot be undone and will create a reversal transaction.') }}
                        </flux:subheading>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Transaction:') }}</span>
                        <span class="font-mono text-zinc-900 dark:text-zinc-100">{{ $transaction->reference_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Amount:') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($transaction->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Type:') }}</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ ucwords(str_replace('_', ' ', $transaction->type)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Member:') }}</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $transaction->member->name }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <flux:field>
                    <flux:label>{{ __('Reversal Reason (Optional)') }}</flux:label>
                    <flux:textarea 
                        id="reversal-reason"
                        name="reason"
                        placeholder="{{ __('Enter reason for reversal...') }}"
                        rows="3" />
                </flux:field>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="danger" class="flex-1" x-on:click="submitReversalForm()">
                    <flux:icon.arrow-uturn-left class="w-4 h-4 mr-2" />
                    {{ __('Reverse Payment') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endrole

    <!-- Simple Custom Modal (Fallback) -->
    @role('admin')
    <div id="simple-reverse-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="hideSimpleModal(event)">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 max-w-md mx-4" onclick="event.stopPropagation()">
            <div class="text-center mb-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 6.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Reverse Payment') }}</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">
                    {{ __('Are you sure you want to reverse this payment? This action cannot be undone.') }}
                </p>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Transaction:') }}</span>
                        <span class="font-mono text-zinc-900 dark:text-zinc-100">{{ $transaction->reference_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Amount:') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($transaction->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                    {{ __('Reversal Reason (Optional)') }}
                </label>
                <textarea 
                    id="simple-reversal-reason"
                    class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100"
                    placeholder="{{ __('Enter reason for reversal...') }}"
                    rows="3"></textarea>
            </div>

            <div class="flex gap-3">
                <button onclick="hideSimpleModal()" class="flex-1 px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600">
                    {{ __('Cancel') }}
                </button>
                <button onclick="submitSimpleReversalForm()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    {{ __('Reverse Payment') }}
                </button>
                         </div>
         </div>
     </div>
     @endrole

     <script>
        function showReversalModal() {
            console.log('showReversalModal called');
            
            // Try to show Flux modal first
            document.dispatchEvent(new CustomEvent('open-modal', { 
                detail: 'reverse-payment-modal',
                bubbles: true
            }));
            
            // Fallback to simple modal after a short delay
            setTimeout(() => {
                const fluxModal = document.querySelector('[name="reverse-payment-modal"]');
                const isFluxModalVisible = fluxModal && !fluxModal.classList.contains('hidden') && fluxModal.style.display !== 'none';
                
                if (!isFluxModalVisible) {
                    console.log('Flux modal not visible, showing simple modal');
                    showSimpleModal();
                }
            }, 100);
        }

        function showSimpleModal() {
            const modal = document.getElementById('simple-reverse-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function hideSimpleModal(event) {
            if (event && event.target !== event.currentTarget) return;
            const modal = document.getElementById('simple-reverse-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function submitSimpleReversalForm() {
            const reason = document.getElementById('simple-reversal-reason').value;
            const form = document.getElementById('reverse-form');
            
            // Add reason to form if provided
            if (reason.trim()) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);
            }
            
            // Hide modal and submit
            hideSimpleModal();
            form.submit();
        }

        function submitReversalForm() {
            const reason = document.getElementById('reversal-reason').value;
            const form = document.getElementById('reverse-form');
            
            // Add reason to form if provided
            if (reason.trim()) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);
            }
            
            // Close modal by dispatching close-modal event
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reverse-payment-modal' }));
            
            // Submit form
            form.submit();
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Transaction ID copied to clipboard!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Auto-refresh for pending payments
        @if($transaction->status === 'pending')
        setInterval(function() {
            // Check if payment status has changed
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== '{{ $transaction->status }}') {
                    // Reload page if status changed
                    window.location.reload();
                }
            })
            .catch(error => console.log('Status check failed:', error));
        }, 30000); // Check every 30 seconds
        @endif
    </script>
</x-layouts.app> 