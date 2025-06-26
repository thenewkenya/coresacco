<x-layouts.app :title="$branch->name . ' - Branch Details'">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('branches.index')" wire:navigate>
                            {{ __('Back to Branches') }}
                        </flux:button>
                        <div>
                            <div class="flex items-center space-x-3">
                                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $branch->name }}
                                </h1>
                                <span class="px-3 py-1 text-sm font-medium rounded-full
                                    @if($branch->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @elseif($branch->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $branch->status)) }}
                                </span>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $branch->code }} • {{ $branch->address }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->branch_id === $branch->id)
                            <flux:button variant="outline" icon="pencil" :href="route('branches.edit', $branch)" wire:navigate>
                                {{ __('Edit Branch') }}
                            </flux:button>
                        @endif
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                            <flux:button variant="outline" icon="users" :href="route('branches.staff', $branch)" wire:navigate>
                                {{ __('Manage Staff') }}
                            </flux:button>
                        @endif
                        <flux:button variant="primary" icon="chart-bar" :href="route('branches.analytics', $branch)" wire:navigate>
                            {{ __('Analytics') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Branch Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Branch Information') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Branch Code') }}</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->code }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('City') }}</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->city }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Phone') }}</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->phone }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Email') }}</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Manager') }}</label>
                                        <div class="flex items-center space-x-2">
                                            @if($branch->manager)
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                    {{ $branch->manager->initials() }}
                                                </div>
                                                <div>
                                                    <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->manager->name }}</p>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $branch->manager->email }}</p>
                                                </div>
                                            @else
                                                <p class="text-zinc-500 dark:text-zinc-400">{{ __('No manager assigned') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Opening Date') }}</label>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $branch->opening_date?->format('M d, Y') ?? __('Not set') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Working Hours') }}</label>
                                        @if($branch->working_hours)
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                @foreach($branch->working_hours as $day => $hours)
                                                    <div class="flex justify-between">
                                                        <span class="capitalize">{{ $day }}:</span>
                                                        <span>{{ $hours['open'] ?? 'Closed' }} - {{ $hours['close'] ?? 'Closed' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-zinc-500 dark:text-zinc-400">{{ __('Not set') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Overview -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Analytics Overview') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Members -->
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($analytics['members']['total']) }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400">+{{ $analytics['members']['new_this_month'] }} {{ __('this month') }}</p>
                            </div>

                            <!-- Accounts -->
                            <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($analytics['accounts']['total_balance'] / 1000000, 1) }}M</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Deposits') }}</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($analytics['accounts']['total']) }} {{ __('accounts') }}</p>
                            </div>

                            <!-- Loans -->
                            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/40 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.banknotes class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                </div>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($analytics['loans']['total_portfolio'] / 1000000, 1) }}M</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Portfolio') }}</p>
                                <p class="text-xs text-purple-600 dark:text-purple-400">{{ $analytics['loans']['active_loans'] }} {{ __('active loans') }}</p>
                            </div>

                            <!-- Transactions -->
                            <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                <div class="p-3 bg-amber-100 dark:bg-amber-900/40 rounded-full w-fit mx-auto mb-3">
                                    <flux:icon.arrow-path class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                                </div>
                                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($analytics['transactions']['total_this_month']) }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Transactions') }}</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('This month') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Transactions') }}</h3>
                        </div>
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($recentTransactions as $transaction)
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="p-2 rounded-full 
                                                @if($transaction->type === 'deposit') bg-emerald-100 dark:bg-emerald-900/30
                                                @elseif($transaction->type === 'withdrawal') bg-red-100 dark:bg-red-900/30
                                                @else bg-blue-100 dark:bg-blue-900/30
                                                @endif">
                                                @if($transaction->type === 'deposit')
                                                    <flux:icon.arrow-down class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                                @elseif($transaction->type === 'withdrawal')
                                                    <flux:icon.arrow-up class="w-4 h-4 text-red-600 dark:text-red-400" />
                                                @else
                                                    <flux:icon.arrow-path class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                                </p>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                                    {{ $transaction->account->member->name }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                                    {{ $transaction->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold 
                                                @if($transaction->type === 'deposit') text-emerald-600 dark:text-emerald-400
                                                @elseif($transaction->type === 'withdrawal') text-red-600 dark:text-red-400
                                                @else text-blue-600 dark:text-blue-400
                                                @endif">
                                                {{ $transaction->type === 'withdrawal' ? '-' : '+' }}{{ __('KES') }} {{ number_format($transaction->amount, 2) }}
                                            </p>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                @if($transaction->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                                @elseif($transaction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                                @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center">
                                    <flux:icon.exclamation-triangle class="w-8 h-8 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                                    <p class="text-zinc-500 dark:text-zinc-400">{{ __('No recent transactions') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Staff Members -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Staff Members') }}</h3>
                                @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                                    <flux:button variant="ghost" size="sm" icon="plus" :href="route('branches.staff', $branch)" wire:navigate>
                                        {{ __('Manage') }}
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            @forelse($branch->staff->take(5) as $staff)
                                <div class="flex items-center space-x-3 mb-4 last:mb-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $staff->initials() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $staff->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($staff->role) }}</p>
                                    </div>
                                    @if($staff->id === $branch->manager_id)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                            {{ __('Manager') }}
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-center text-zinc-500 dark:text-zinc-400 py-4">{{ __('No staff assigned') }}</p>
                            @endforelse
                            
                            @if($branch->staff->count() > 5)
                                <div class="mt-4 text-center">
                                    <flux:button variant="ghost" size="sm" :href="route('branches.staff', $branch)" wire:navigate>
                                        {{ __('View all') }} ({{ $branch->staff->count() }})
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Members -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Members') }}</h3>
                        </div>
                        <div class="p-6">
                            @forelse($recentMembers as $member)
                                <div class="flex items-center space-x-3 mb-4 last:mb-0">
                                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $member->initials() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $member->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $member->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                        {{ ucfirst($member->membership_status ?? 'active') }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-center text-zinc-500 dark:text-zinc-400 py-4">{{ __('No recent members') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3">
                            <flux:button variant="outline" size="sm" class="w-full justify-start" icon="chart-bar" :href="route('branches.analytics', $branch)" wire:navigate>
                                {{ __('View Detailed Analytics') }}
                            </flux:button>
                            
                            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                                <flux:button variant="outline" size="sm" class="w-full justify-start" icon="users" :href="route('branches.staff', $branch)" wire:navigate>
                                    {{ __('Manage Staff') }}
                                </flux:button>
                            @endif
                            
                            <flux:button variant="outline" size="sm" class="w-full justify-start" icon="document-text" :href="route('reports.operational', ['type' => 'branch_performance', 'branch_id' => $branch->id])" wire:navigate>
                                {{ __('Generate Report') }}
                            </flux:button>
                            
                            @if(auth()->user()->hasRole('admin') || auth()->user()->branch_id === $branch->id)
                                <flux:button variant="outline" size="sm" class="w-full justify-start" icon="cog" :href="route('branches.edit', $branch)" wire:navigate>
                                    {{ __('Branch Settings') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 