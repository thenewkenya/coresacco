<x-layouts.app :title="__('Loan Details - :amount', ['amount' => 'KES ' . number_format($loan->amount)])">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Details') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $loan->loanType->name }} • {{ $loan->member->name }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if(in_array($loan->status, ['active', 'disbursed']))
                        <flux:button variant="outline" icon="credit-card" :href="route('payments.create', ['loan_id' => $loan->id])">
                            {{ __('Make Repayment') }}
                        </flux:button>
                        @endif
                        <flux:button variant="ghost" :href="route('loans.index')" wire:navigate>
                            {{ __('Back to Loans') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Loan Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Loan Amount</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($loan->amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Amount Paid</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($totalRepaid, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Remaining Balance</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($remainingBalance, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.calendar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Monthly Payment</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($monthlyPayment, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2">
                    <!-- Repayment Schedule -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-6">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Repayment Schedule') }}
                            </h3>
                        </div>

                        @if(count($repaymentSchedule) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Payment #
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Due Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Principal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Interest
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Total Payment
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            Balance
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($repaymentSchedule as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $payment['month'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($payment['principal'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($payment['interest'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($payment['total_payment'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($payment['remaining_balance'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-12 text-center">
                            <flux:icon.calendar class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ __('No Schedule Available') }}
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('Repayment schedule will be generated once the loan is approved.') }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Transaction History -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Payment History') }}
                            </h3>
                        </div>

                        @if($loan->transactions->count() > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($loan->transactions as $transaction)
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-2 rounded-lg {{ $transaction->type === 'loan_repayment' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                            @if($transaction->type === 'loan_repayment')
                                                <flux:icon.arrow-up class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                            @else
                                                <flux:icon.arrow-down class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $transaction->description }}
                                            </p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $transaction->created_at->format('M d, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold {{ $transaction->type === 'loan_repayment' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400' }}">
                                            {{ $transaction->type === 'loan_repayment' ? '+' : '' }}KES {{ number_format($transaction->amount, 2) }}
                                        </p>
                                        <flux:badge variant="{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </flux:badge>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-12 text-center">
                            <flux:icon.credit-card class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ __('No Payments Yet') }}
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('No payments have been made on this loan yet.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Loan Details -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Loan Details') }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Borrower</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->member->name }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $loan->member->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Loan Type</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->loanType->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Interest Rate</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->interest_rate }}% per annum</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Term Period</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->term_period }} months</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Status</p>
                                <div class="mt-1">
                                    @if($loan->status === 'pending')
                                        <flux:badge variant="warning">Pending</flux:badge>
                                    @elseif($loan->status === 'approved')
                                        <flux:badge variant="info">Approved</flux:badge>
                                    @elseif($loan->status === 'disbursed')
                                        <flux:badge variant="success">Disbursed</flux:badge>
                                    @elseif($loan->status === 'active')
                                        <flux:badge variant="success">Active</flux:badge>
                                    @elseif($loan->status === 'completed')
                                        <flux:badge>Completed</flux:badge>
                                    @elseif($loan->status === 'rejected')
                                        <flux:badge variant="danger">Rejected</flux:badge>
                                    @else
                                        <flux:badge variant="outline">{{ ucfirst($loan->status) }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Applied On</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->created_at->format('M d, Y') }}</p>
                            </div>
                            @if($loan->disbursement_date)
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Disbursed On</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->disbursement_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                            @if($loan->due_date)
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Due Date</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->due_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Loan Progress -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Repayment Progress') }}
                        </h3>
                        @php
                            $totalLoan = $loan->amount + ($loan->amount * $loan->interest_rate / 100 * $loan->term_period / 12);
                            $progressPercentage = $totalLoan > 0 ? ($totalRepaid / $totalLoan) * 100 : 0;
                        @endphp
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-zinc-600 dark:text-zinc-400">Progress</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($progressPercentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">Paid: KES {{ number_format($totalRepaid, 2) }}</span>
                                <span class="text-zinc-600 dark:text-zinc-400">Total: KES {{ number_format($totalLoan, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if(in_array($loan->status, ['active', 'disbursed']))
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Actions') }}
                        </h3>
                        <div class="space-y-3">
                            <flux:button variant="outline" class="w-full justify-start" icon="credit-card">
                                {{ __('Make Repayment') }}
                            </flux:button>
                            <flux:button variant="outline" class="w-full justify-start" icon="document-text">
                                {{ __('Download Statement') }}
                            </flux:button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 