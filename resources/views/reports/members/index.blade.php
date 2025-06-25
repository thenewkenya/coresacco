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
                <!-- Member Activity (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Activity Report') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.chart-bar class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Member Activity Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show member transaction activity and engagement') }}</p>
                    </div>
                </div>

            @elseif($report_type === 'demographics')
                <!-- Demographics (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Demographics') }}</h2>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.user-group class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Demographics Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show member demographics and distribution') }}</p>
                    </div>
                </div>

            @elseif($report_type === 'growth')
                <!-- Growth Analysis (placeholder for now) -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Growth Analysis') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>
                    
                    <div class="text-center py-12">
                        <flux:icon.chart-bar class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Growth Analysis Coming Soon') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">{{ __('This report will show member growth trends and patterns') }}</p>
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