<x-layouts.app :title="__('Loans Report')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loans Report') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive loan portfolio analysis and performance metrics') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray">
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('loans.index')" wire:navigate>
                            {{ __('Back to Loans') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Report Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ $startDate }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ $endDate }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <flux:select name="status">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="defaulted" {{ request('status') == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.document-text class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Loans</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_loans']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Loans</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['active_loans']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Portfolio</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($stats['total_portfolio'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.exclamation-triangle class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Default Rate</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['default_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Performance') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Disbursed</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                KES {{ number_format($stats['total_disbursed'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Repaid</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                KES {{ number_format($stats['total_repaid'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Outstanding</span>
                            <span class="font-semibold text-orange-600 dark:text-orange-400">
                                KES {{ number_format($stats['total_outstanding'], 2) }}
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Recovery Rate</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($stats['recovery_rate'], 1) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Loan Types Distribution') }}
                    </h3>
                    <div class="space-y-3">
                        @php
                            $loansByType = $loans->groupBy('loanType.name');
                            $totalLoans = $loans->count();
                        @endphp
                        @foreach($loansByType as $typeName => $typeLoans)
                        @php
                            $count = $typeLoans->count();
                            $percentage = $totalLoans > 0 ? ($count / $totalLoans) * 100 : 0;
                            $totalAmount = $typeLoans->sum('amount');
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 bg-blue-500"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $typeName }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">({{ number_format($percentage, 1) }}%)</span>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($totalAmount, 0) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Risk Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Risk Metrics') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">30+ Days Past Due</span>
                            <span class="font-semibold text-yellow-600 dark:text-yellow-400">
                                {{ $stats['loans_30_days_overdue'] }} ({{ number_format($stats['loans_30_days_overdue_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">60+ Days Past Due</span>
                            <span class="font-semibold text-orange-600 dark:text-orange-400">
                                {{ $stats['loans_60_days_overdue'] }} ({{ number_format($stats['loans_60_days_overdue_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">90+ Days Past Due</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">
                                {{ $stats['loans_90_days_overdue'] }} ({{ number_format($stats['loans_90_days_overdue_rate'], 1) }}%)
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Provision Required</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">
                                    KES {{ number_format($stats['provision_required'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Monthly Trends') }}
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">New Loans This Month</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ $stats['new_loans_this_month'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Loans Completed</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $stats['completed_loans_this_month'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Average Loan Size</span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($stats['average_loan_size'], 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Approval Rate</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($stats['approval_rate'], 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Loan List -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Loan Details') }}
                    </h3>
                </div>

                @if($loans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Borrower
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Loan Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Outstanding
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Due Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($loans as $loan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->member->name }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->member->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $loan->loanType->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($loan->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        KES {{ number_format($loan->outstanding_balance ?? $loan->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($loan->status === 'pending')
                                        <flux:badge variant="warning">Pending</flux:badge>
                                    @elseif($loan->status === 'approved')
                                        <flux:badge variant="info">Approved</flux:badge>
                                    @elseif($loan->status === 'active')
                                        <flux:badge variant="success">Active</flux:badge>
                                    @elseif($loan->status === 'completed')
                                        <flux:badge>Completed</flux:badge>
                                    @elseif($loan->status === 'defaulted')
                                        <flux:badge variant="danger">Defaulted</flux:badge>
                                    @else
                                        <flux:badge variant="outline">{{ ucfirst($loan->status) }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->due_date ? $loan->due_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:button variant="ghost" size="sm" :href="route('loans.show', $loan)" wire:navigate>
                                        {{ __('View') }}
                                    </flux:button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($loans->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $loans->links() }}
                </div>
                @endif
                @else
                <div class="p-12 text-center">
                    <flux:icon.document-text class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Data Available') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        {{ __('No loans found for the selected criteria.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 