<x-layouts.app :title="__('Member Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Member Reports') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Member analytics, demographics, and activity reports') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray" :href="route('reports.members', array_merge(request()->all(), ['format' => 'pdf']))" wire:navigate>
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('reports.members', array_merge(request()->all(), ['format' => 'excel']))" wire:navigate>
                            {{ __('Export Excel') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('reports.index')" wire:navigate>
                            {{ __('Back to Reports') }}
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
                            <flux:label>{{ __('Report Type') }}</flux:label>
                            <flux:select name="type">
                                <option value="summary" {{ request('type') === 'summary' ? 'selected' : '' }}>{{ __('Member Summary') }}</option>
                                <option value="activity" {{ request('type') === 'activity' ? 'selected' : '' }}>{{ __('Member Activity') }}</option>
                                <option value="demographics" {{ request('type') === 'demographics' ? 'selected' : '' }}>{{ __('Demographics') }}</option>
                                <option value="growth" {{ request('type') === 'growth' ? 'selected' : '' }}>{{ __('Growth Analysis') }}</option>
                                <option value="financial" {{ request('type') === 'financial' ? 'selected' : '' }}>{{ __('Financial Analysis') }}</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Branch') }}</flux:label>
                            <flux:select name="branch_id">
                                <option value="">{{ __('All Branches') }}</option>
                                @if(isset($branches))
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Member Status') }}</flux:label>
                            <flux:select name="status">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Member Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($summary['total_members']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.user-plus class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('New Members') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($summary['new_members']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Members') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($summary['active_members']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Members with Loans') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($summary['members_with_loans']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($report_type === 'summary')
                <!-- Member Summary Report -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Summary Report') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Email') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Joined') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Accounts') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loans') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($members as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $member->member_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $member->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $member->membership_status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                            {{ ucfirst($member->membership_status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $member->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $member->accounts->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $member->loans->count() }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($report_type === 'activity')
                <!-- Member Activity Report -->
                <div class="space-y-6">
                    <!-- Activity Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Members') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($activitySummary['total_active_members']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Transaction Volume') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($activitySummary['total_transaction_volume'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <flux:icon.arrow-trending-up class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Avg Transactions/Member') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($activitySummary['average_transactions_per_member'], 1) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <flux:icon.users class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Most Active') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $activitySummary['most_active_members']->first()['transaction_count'] ?? 0 }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('transactions') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Depositors -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Top Depositors') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($activitySummary['top_depositors'] as $member)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                    {{ $member['member']->initials() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $member['member']->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $member['deposit_count'] }} {{ __('deposits') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                ${{ number_format($member['total_deposits'], 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Top Savers -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Top Net Savers') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($activitySummary['top_savers'] as $member)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                    {{ $member['member']->initials() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $member['member']->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Net savings') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium {{ $member['net_savings'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                ${{ number_format($member['net_savings'], 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Activity Table -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Member Activity Details') }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Transactions') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Deposits') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Withdrawals') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Net Savings') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Account Balance') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Last Activity') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($activity->take(50) as $member)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $member['member']->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $member['member']->member_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $member['transaction_count'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-600 dark:text-emerald-400">
                                            ${{ number_format($member['total_deposits'], 2) }}
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $member['deposit_count'] }} {{ __('deposits') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                                            ${{ number_format($member['total_withdrawals'], 2) }}
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $member['withdrawal_count'] }} {{ __('withdrawals') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $member['net_savings'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            ${{ number_format($member['net_savings'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            ${{ number_format($member['total_account_balance'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $member['last_transaction_date'] ? \Carbon\Carbon::parse($member['last_transaction_date'])->format('M j, Y') : __('No activity') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'demographics')
                <!-- Demographics Report -->
                <div class="space-y-6">
                    <!-- Overview Stats -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Demographics Overview') }}</h3>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalMembers) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Members Analyzed') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Membership Status Distribution -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Membership Status') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($demographics['by_status'] as $status)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded {{ $status['status'] === 'active' ? 'bg-emerald-500' : 'bg-zinc-400' }} mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($status['status']) }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($status['count']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $status['percentage'] }}%)</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Branch Distribution -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Branch Distribution') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($demographics['by_branch'] as $branch)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-blue-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch_name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($branch['count']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $branch['percentage'] }}%)</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Account Distribution -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Account Distribution') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($demographics['account_distribution'] as $distribution)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-purple-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $distribution['range'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($distribution['count']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $distribution['percentage'] }}%)</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Loan Participation -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Loan Participation') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-emerald-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Active Borrowers') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($demographics['loan_participation']['active_borrowers']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $demographics['loan_participation']['active_borrowers_percentage'] }}%)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-orange-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('With Loan History') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($demographics['loan_participation']['with_loans']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $demographics['loan_participation']['with_loans_percentage'] }}%)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-zinc-400 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('No Loans') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($demographics['loan_participation']['without_loans']) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">({{ $demographics['loan_participation']['without_loans_percentage'] }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Timeline -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Registration Timeline (Last 12 Months)') }}</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($demographics['by_registration_period'] as $period)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $period['count'] }}</div>
                                    <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $period['period'] }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'growth')
                <!-- Growth Analysis Report -->
                <div class="space-y-6">
                    <!-- Growth Metrics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.user-plus class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('New Members') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($growthMetrics['new_members_period']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.arrow-trending-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Growth Rate') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $growthMetrics['growth_rate'] }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <flux:icon.calendar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Daily Avg') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $growthMetrics['avg_daily_growth'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <flux:icon.star class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Peak Day') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $growthMetrics['peak_registration_day']['new_members'] ?? 0 }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $growthMetrics['peak_registration_day'] ? \Carbon\Carbon::parse($growthMetrics['peak_registration_day']['date'])->format('M j') : __('No data') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Growth Timeline and Branch Analysis -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Monthly Growth Trend -->
                        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Monthly Growth Trend') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($monthlyGrowth as $month)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-8 bg-blue-500 rounded mr-4" style="height: {{ max(8, ($month['new_members'] / $monthlyGrowth->max('new_members')) * 32) }}px;"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $month['period'] }}</span>
                                        </div>
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $month['new_members'] }} {{ __('new members') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Branch Growth Analysis -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Branch Growth') }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Current period') }}</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($branchGrowth as $branch)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                                    {{ substr($branch['branch_name'], 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch_name'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['new_members'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Growth Details -->
                    @if($dailyGrowth->count() > 0)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Daily Registration Details') }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('New Members') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Running Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($cumulative as $day)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $day['new_members'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($day['total_members']) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            @elseif($report_type === 'financial')
                <!-- Financial Analysis Report -->
                <div class="space-y-6">
                    <!-- Financial Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Savings') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($financialSummary['total_member_savings'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Loan Portfolio') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($financialSummary['total_loan_portfolio'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <flux:icon.scale class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Loan Ratio') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($financialSummary['portfolio_loan_ratio'], 1) }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <flux:icon.exclamation-triangle class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('High Risk') }}</p>
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($financialSummary['high_risk_borrowers']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Categories -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- High Value Savers -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('High Value Savers') }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Top savings account holders') }}</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($memberCategories['high_value_savers'] as $profile)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                    {{ $profile['member']->initials() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $profile['member']->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $profile['account_count'] }} {{ __('accounts') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                ${{ number_format($profile['total_balance'], 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- High Risk Borrowers -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('High Risk Borrowers') }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Members requiring attention') }}</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($memberCategories['high_risk_borrowers'] as $profile)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                                    {{ $profile['member']->initials() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $profile['member']->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Risk Score') }}: {{ number_format($profile['risk_score']) }}%
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                                ${{ number_format($profile['active_loan_amount'], 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Patterns Analysis -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Savings Behavior Distribution -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Savings Behavior') }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-emerald-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Consistent Savers') }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($savingsPatterns['consistent_savers']) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-blue-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Moderate Savers') }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($savingsPatterns['moderate_savers']) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-orange-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Low Savers') }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($savingsPatterns['low_savers']) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded bg-red-500 mr-3"></div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Net Withdrawers') }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($savingsPatterns['net_withdrawers']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Growth Potential Members -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Growth Potential') }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Members with expansion opportunities') }}</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($memberCategories['growth_potential'] as $profile)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                    {{ $profile['member']->initials() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $profile['member']->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Savings Rate') }}: {{ number_format($profile['savings_rate'], 1) }}%
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                ${{ number_format($profile['total_balance'], 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Financial Analysis Table -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Member Financial Analysis') }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Balance') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Active Loans') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Ratio') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Savings Rate') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Risk Score') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Activity') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($financialProfiles->take(50) as $profile)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $profile['member']->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $profile['member']->member_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            ${{ number_format($profile['total_balance'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            ${{ number_format($profile['active_loan_amount'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $profile['loan_to_savings_ratio'] > 80 ? 'text-red-600 dark:text-red-400' : ($profile['loan_to_savings_ratio'] > 60 ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                                            {{ number_format($profile['loan_to_savings_ratio'], 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $profile['savings_rate'] > 50 ? 'text-emerald-600 dark:text-emerald-400' : ($profile['savings_rate'] > 20 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400') }}">
                                            {{ number_format($profile['savings_rate'], 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $profile['risk_score'] > 70 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : ($profile['risk_score'] > 40 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400') }}">
                                                {{ number_format($profile['risk_score']) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $profile['transaction_frequency'] }} {{ __('transactions') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Report Summary -->
            <div class="mt-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Report Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Generated On:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ $generated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Report Type:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ ucfirst(str_replace('_', ' ', $report_type)) }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Period:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Total Records:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ $members->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 