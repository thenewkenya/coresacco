<x-layouts.app :title="__('Branch Map View - Static')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('branches.index')" wire:navigate>
                            {{ __('Back to List') }}
                        </flux:button>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ __('Branch Map View (Static)') }}
                            </h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('Static view of branch locations and coordinates') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <flux:button variant="primary" :href="route('branches.map')" wire:navigate>
                            {{ __('Interactive Map') }}
                        </flux:button>
                        <flux:button variant="outline" onclick="if(window.opener && window.opener.reinitMap) { window.opener.reinitMap(); alert('Map reinitialization triggered!'); } else { alert('No parent map window found'); }">
                            {{ __('Reinit Parent Map') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Statistics -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Network Overview') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Branches') }}</span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total_branches'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Mapped Branches') }}</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $stats['mapped_branches'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Active Branches') }}</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $stats['active_branches'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</span>
                            <span class="font-semibold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_members']) }}</span>
                        </div>
                        @if($stats['top_performer'])
                            <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Top Performer') }}</span>
                                <p class="font-semibold text-amber-600 dark:text-amber-400">{{ $stats['top_performer']->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Map Center Info -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Map Center Coordinates') }}</h3>
                    
                    <div class="space-y-2">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            <strong>{{ __('Latitude:') }}</strong> {{ $mapCenter['lat'] }}
                        </p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            <strong>{{ __('Longitude:') }}</strong> {{ $mapCenter['lng'] }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-500">
                            {{ __('Center calculated from available branch coordinates') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Branch List with Coordinates -->
            <div class="mt-8 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Branches with Coordinates') }} ({{ $branchesWithAnalytics->count() }})
                    </h3>
                </div>
                
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Branch') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Location') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Coordinates') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Performance') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Members') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($branchesWithAnalytics as $data)
                                @php $branch = $data['branch'] @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                @if($data['performance_score'] >= 80) bg-emerald-500
                                                @elseif($data['performance_score'] >= 60) bg-blue-500
                                                @elseif($data['performance_score'] >= 40) bg-amber-500
                                                @else bg-red-500
                                                @endif">
                                                <flux:icon.building-office-2 class="w-4 h-4 text-white" />
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $branch->name }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $branch->code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $branch->city }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $branch->address }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                        @if($branch->coordinates)
                                            <div class="space-y-1">
                                                <p><strong>Lat:</strong> {{ $branch->coordinates['latitude'] ?? 'N/A' }}</p>
                                                <p><strong>Lng:</strong> {{ $branch->coordinates['longitude'] ?? 'N/A' }}</p>
                                            </div>
                                        @else
                                            <span class="text-zinc-400">{{ __('Not set') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium 
                                                @if($data['performance_score'] >= 80) text-emerald-600 dark:text-emerald-400
                                                @elseif($data['performance_score'] >= 60) text-blue-600 dark:text-blue-400
                                                @elseif($data['performance_score'] >= 40) text-amber-600 dark:text-amber-400
                                                @else text-red-600 dark:text-red-400
                                                @endif">
                                                {{ number_format($data['performance_score']) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2 mt-1">
                                            <div class="h-2 rounded-full 
                                                @if($data['performance_score'] >= 80) bg-emerald-500
                                                @elseif($data['performance_score'] >= 60) bg-blue-500
                                                @elseif($data['performance_score'] >= 40) bg-amber-500
                                                @else bg-red-500
                                                @endif" 
                                                style="width: {{ $data['performance_score'] }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($data['total_members']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <flux:icon.map class="w-8 h-8 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                                        <p class="text-zinc-500 dark:text-zinc-400">{{ __('No branches with coordinates found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- JSON Data for Debugging -->
            <div class="mt-8 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Debug Information') }}</h3>
                
                <details class="space-y-4">
                    <summary class="cursor-pointer text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('View Branch Data JSON') }}</summary>
                    <pre class="bg-zinc-100 dark:bg-zinc-700 p-4 rounded-lg text-xs overflow-auto"><code>{{ json_encode($branchesWithAnalytics, JSON_PRETTY_PRINT) }}</code></pre>
                </details>

                <details class="space-y-4 mt-4">
                    <summary class="cursor-pointer text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('View Map Center Data') }}</summary>
                    <pre class="bg-zinc-100 dark:bg-zinc-700 p-4 rounded-lg text-xs overflow-auto"><code>{{ json_encode($mapCenter, JSON_PRETTY_PRINT) }}</code></pre>
                </details>
            </div>
        </div>
    </div>
</x-layouts.app> 