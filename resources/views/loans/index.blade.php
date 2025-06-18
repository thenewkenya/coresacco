<x-layouts.app :title="__('Loan Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage member loans, applications, and repayments') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('loans.report')" wire:navigate>
                            {{ __('Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Loans</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalLoans) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Loans</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($activeLoans) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($pendingLoans) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Amount</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($totalLoanAmount) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.arrow-trending-up class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">This Month</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($thisMonthDisbursements) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <flux:input 
                            name="search" 
                            placeholder="Search members..." 
                            value="{{ $search }}"
                        />
                    </div>
                    <div>
                        <flux:select name="status" placeholder="All Statuses">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="disbursed" {{ $status === 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select name="loan_type" placeholder="All Types">
                            <option value="">All Types</option>
                            @foreach($loanTypes as $type)
                            <option value="{{ $type->id }}" {{ $loanType == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="flex space-x-2">
                        <flux:button type="submit" variant="primary" class="flex-1">
                            {{ __('Filter') }}
                        </flux:button>
                        <flux:button variant="outline" :href="route('loans.index')" wire:navigate>
                            {{ __('Clear') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Loans Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Loan Applications & Management') }}
                    </h3>
                </div>

                @if($loans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Loan Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Term
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Interest Rate
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Applied
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($loans as $loan)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $loan->member->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->member->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->loanType->name }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->loanType->description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($loan->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->term_period }} months
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->interest_rate }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="sm" variant="outline" :href="route('loans.show', $loan)" wire:navigate>
                                            {{ __('View') }}
                                        </flux:button>
                                        
                                        @if($loan->status === 'pending')
                                            @can('approve', $loan)
                                            <flux:dropdown>
                                                <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                                                <flux:menu>
                                                    <flux:menu.item icon="check" onclick="approveLoan({{ $loan->id }})">
                                                        Approve
                                                    </flux:menu.item>
                                                    <flux:menu.item icon="x-mark" onclick="rejectLoan({{ $loan->id }})">
                                                        Reject
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                            @endcan
                                        @elseif(in_array($loan->status, ['active', 'disbursed']))
                                            <flux:button size="sm" variant="outline" onclick="processRepayment({{ $loan->id }})">
                                                {{ __('Repayment') }}
                                            </flux:button>
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
                    {{ $loans->links() }}
                </div>
                @else
                <div class="p-12 text-center">
                <flux:icon.credit-card class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Loans Found') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('No loan applications match your current filters.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function approveLoan(loanId) {
            if (confirm('Are you sure you want to approve this loan?')) {
                // Implementation for loan approval
                console.log('Approving loan:', loanId);
            }
        }

        function rejectLoan(loanId) {
            if (confirm('Are you sure you want to reject this loan?')) {
                // Implementation for loan rejection
                console.log('Rejecting loan:', loanId);
            }
        }

        function processRepayment(loanId) {
            // Implementation for processing repayment
            console.log('Processing repayment for loan:', loanId);
        }
    </script>
</x-layouts.app> 