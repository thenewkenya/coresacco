<x-layouts.app :title="__('Branch Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Branch Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage SACCO branches and their operations') }}
                        </p>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <flux:button variant="primary" icon="plus" :href="route('branches.create')" wire:navigate>
                            {{ __('Add Branch') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search and Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <flux:input 
                            type="text" 
                            name="search" 
                            placeholder="{{ __('Search branches...') }}" 
                            value="{{ $search }}"
                            icon="magnifying-glass"
                        />
                    </div>
                    
                    <div>
                        <flux:select name="status">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value="under_maintenance" {{ $status === 'under_maintenance' ? 'selected' : '' }}>{{ __('Under Maintenance') }}</option>
                        </flux:select>
                    </div>
                    
                    <div>
                        <flux:select name="city">
                            <option value="">{{ __('All Cities') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ $city === request('city') ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <flux:button type="submit" variant="primary" class="flex-1">
                            {{ __('Filter') }}
                        </flux:button>
                        <flux:button type="button" variant="outline" onclick="window.location.href='{{ route('branches.index') }}'">
                            {{ __('Clear') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Branch Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Branches') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_branches'] }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ $stats['active_branches'] }} {{ __('Active') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Staff</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Staff Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_staff']) }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('All branches') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.user-group class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Members</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_members']) }}</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('All branches') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.star class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Performance</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Top Performer') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['top_performer']->name ?? __('N/A') }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('This month') }}</p>
                    </div>
                </div>
            </div>

            <!-- Branch Listing -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('All Branches') }} ({{ $branchesWithAnalytics->count() }})
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="map" :href="route('branches.map')" wire:navigate>
                                {{ __('Map View') }}
                            </flux:button>
                            <flux:button variant="ghost" size="sm" icon="chart-bar" :href="route('reports.operational', ['type' => 'branch_performance'])" wire:navigate>
                                {{ __('Performance Report') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                @if($branchesWithAnalytics->isEmpty())
                    <div class="p-12 text-center">
                        <flux:icon.building-office-2 class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('No branches found') }}</h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('No branches match your current filter criteria.') }}</p>
                        @if(auth()->user()->hasRole('admin'))
                            <flux:button variant="primary" :href="route('branches.create')" wire:navigate>
                                {{ __('Create First Branch') }}
                            </flux:button>
                        @endif
                    </div>
                @else
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($branchesWithAnalytics as $data)
                            @php $branch = $data['branch'] @endphp
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                            <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                    {{ $branch->name }}
                                                </h4>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($branch->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                                    @elseif($branch->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $branch->status)) }}
                                                </span>
                                                <div class="flex items-center space-x-1">
                                                    <div class="w-16 bg-zinc-200 dark:bg-zinc-600 rounded-full h-1.5">
                                                        <div class="h-1.5 rounded-full 
                                                            @if($data['performance_score'] >= 80) bg-emerald-500
                                                            @elseif($data['performance_score'] >= 60) bg-blue-500
                                                            @elseif($data['performance_score'] >= 40) bg-amber-500
                                                            @else bg-red-500
                                                            @endif" 
                                                            style="width: {{ $data['performance_score'] }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($data['performance_score'], 0) }}%</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $branch->code }} • {{ $branch->city }}
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                                {{ __('Manager:') }} {{ $branch->manager->name ?? __('Unassigned') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="grid grid-cols-4 gap-4 text-center mb-3">
                                            <div>
                                                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($data['total_members']) }}</p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Members') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($data['total_staff']) }}</p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Staff') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ __('KES') }} {{ number_format($data['total_deposits'] / 1000, 0) }}K</p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Deposits') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($data['this_month_transactions']) }}</p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Transactions') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <flux:button variant="outline" size="sm" :href="route('branches.show', $branch)" wire:navigate>
                                                {{ __('View Details') }}
                                            </flux:button>
                                            @if(auth()->user()->hasRole('admin') || auth()->user()->branch_id === $branch->id)
                                                <flux:button variant="ghost" size="sm" :href="route('branches.edit', $branch)" wire:navigate>
                                                    {{ __('Edit') }}
                                                </flux:button>
                                            @endif
                                            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                                                <flux:button variant="ghost" size="sm" :href="route('branches.staff', $branch)" wire:navigate>
                                                    {{ __('Staff') }}
                                                </flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 