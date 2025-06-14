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
                    <flux:button variant="primary" icon="plus">
                        {{ __('Add Branch') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Branch Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Active</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Branches') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">8</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Operational') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Total</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Staff Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">45</p>
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
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">2,847</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('All branches') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Performance</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Top Performer') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Nairobi</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('This month') }}</p>
                    </div>
                </div>
            </div>

            <!-- Branch Listing -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('All Branches') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="map">
                                {{ __('Map View') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'name' => 'Nairobi Main Branch',
                            'code' => 'NRB-001',
                            'location' => 'Nairobi CBD, Kenya',
                            'manager' => 'Sarah Wanjiku',
                            'members' => '847',
                            'staff' => '12',
                            'status' => 'active',
                            'performance' => '98%'
                        ],
                        [
                            'name' => 'Mombasa Branch',
                            'code' => 'MSA-002',
                            'location' => 'Mombasa, Kenya',
                            'manager' => 'John Mukama',
                            'members' => '425',
                            'staff' => '8',
                            'status' => 'active',
                            'performance' => '94%'
                        ],
                        [
                            'name' => 'Kisumu Branch',
                            'code' => 'KSM-003',
                            'location' => 'Kisumu, Kenya',
                            'manager' => 'Mary Atieno',
                            'members' => '312',
                            'staff' => '6',
                            'status' => 'active',
                            'performance' => '91%'
                        ],
                        [
                            'name' => 'Nakuru Branch',
                            'code' => 'NKR-004',
                            'location' => 'Nakuru, Kenya',
                            'manager' => 'Peter Kimani',
                            'members' => '267',
                            'staff' => '5',
                            'status' => 'active',
                            'performance' => '89%'
                        ]
                    ] as $branch)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ $branch['name'] }}
                                        </h4>
                                        <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                            {{ ucfirst($branch['status']) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $branch['code'] }} • {{ $branch['location'] }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        Manager: {{ $branch['manager'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="grid grid-cols-3 gap-4 text-center mb-2">
                                    <div>
                                        <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $branch['members'] }}</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Members</p>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $branch['staff'] }}</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Staff</p>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $branch['performance'] }}</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Performance</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:button variant="outline" size="sm">
                                        {{ __('View Details') }}
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm">
                                        {{ __('Edit') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 