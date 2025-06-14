<x-layouts.app :title="__('Pending Approvals')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Pending Approvals') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Review and approve member requests and applications') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-sm font-medium">
                            {{ __('12 Pending') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Approval Queue -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Approval Queue') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="funnel">
                                {{ __('Filter') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'id' => 'APP-001',
                            'type' => 'Loan Application',
                            'member' => 'John Mukama',
                            'amount' => '150000',
                            'submitted' => '2024-12-15 09:30',
                            'priority' => 'high',
                            'status' => 'pending'
                        ],
                        [
                            'id' => 'APP-002',
                            'type' => 'Account Opening',
                            'member' => 'Sarah Wanjiku',
                            'amount' => null,
                            'submitted' => '2024-12-14 14:20',
                            'priority' => 'normal',
                            'status' => 'under_review'
                        ],
                        [
                            'id' => 'APP-003',
                            'type' => 'Withdrawal Request',
                            'member' => 'Peter Kimani',
                            'amount' => '50000',
                            'submitted' => '2024-12-14 11:45',
                            'priority' => 'normal',
                            'status' => 'pending'
                        ]
                    ] as $approval)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 rounded-lg {{ $approval['priority'] === 'high' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-amber-100 dark:bg-amber-900/30' }}">
                                    <flux:icon.clock class="w-4 h-4 {{ $approval['priority'] === 'high' ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $approval['type'] }}
                                        </p>
                                        @if($approval['priority'] === 'high')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            High Priority
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $approval['member'] }} • {{ $approval['id'] }}
                                        @if($approval['amount'])
                                        • KSh {{ number_format($approval['amount']) }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        {{ \Carbon\Carbon::parse($approval['submitted'])->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <flux:button variant="outline" size="sm">
                                    {{ __('Review') }}
                                </flux:button>
                                <flux:button variant="primary" size="sm">
                                    {{ __('Approve') }}
                                </flux:button>
                                <flux:button variant="ghost" size="sm" class="text-red-600 hover:text-red-700">
                                    {{ __('Reject') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 