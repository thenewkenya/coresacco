<x-layouts.app :title="__('My Goals')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Financial Goals') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Set and track your savings goals and financial targets') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" icon="plus" :href="route('goals.create')" wire:navigate>
                            {{ __('New Goal') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Overall Progress -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('Overall Progress') }}</h2>
                    <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($overallProgress, 1) }}%</span>
                </div>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>

            <!-- Upcoming Goals -->
            @if($upcomingGoals->isNotEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Upcoming Goals') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($upcomingGoals as $goal)
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $goal->title }}</h3>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $goal->days_remaining }} days left</span>
                        </div>
                        <div class="mb-2">
                            <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">KES {{ number_format($goal->current_amount) }}</span>
                            <span class="text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->target_amount) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Goals by Type -->
            <div class="space-y-8">
                @forelse($goals as $type => $typeGoals)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ \App\Models\Goal::getTypes()[$type] }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($typeGoals as $goal)
                        <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $goal->title }}</h3>
                                <flux:button variant="ghost" size="xs" :href="route('goals.show', $goal)" wire:navigate>
                                    {{ __('View Details') }}
                                </flux:button>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ $goal->description }}</p>
                            <div class="mb-2">
                                <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal->progress_percentage }}%"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm mb-4">
                                <span class="text-zinc-600 dark:text-zinc-400">KES {{ number_format($goal->current_amount) }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->target_amount) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('Target Date:') }} {{ $goal->target_date->format('M d, Y') }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $goal->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                    {{ ucfirst($goal->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                    <flux:icon.flag class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('No Financial Goals Yet') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('Start setting your financial goals to track your progress and achieve your targets.') }}
                    </p>
                    <flux:button variant="primary" :href="route('goals.create')" wire:navigate>
                        {{ __('Create Your First Goal') }}
                    </flux:button>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app> 