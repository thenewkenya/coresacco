<x-layouts.app :title="__('My Goals')">
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                            {{ __('My Financial Goals') }}
                        </h1>
                        <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                            {{ __('Track your progress with intelligent insights and auto-saving') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <flux:button variant="outline" icon="chart-bar">
                            {{ __('Analytics') }}
                        </flux:button>
                        <flux:button variant="primary" icon="sparkles" :href="route('goals.create')" wire:navigate>
                            {{ __('Create Goal') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto space-y-6">
            <!-- Overview Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- Overall Progress -->
                <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Overall Progress</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Across all your goals</p>
                        </div>
                        <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($overallProgress, 1) }}%</div>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3 mb-2">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $overallProgress }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                        <span>KES {{ number_format(auth()->user()->goals()->sum('current_amount')) }}</span>
                        <span>KES {{ number_format(auth()->user()->goals()->sum('target_amount')) }}</span>
                    </div>
                </div>

                <!-- Active Auto-Save Goals -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg mr-3">
                            <flux:icon.bolt class="h-5 w-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">Auto-Save</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Active Goals</p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ auth()->user()->goals()->whereNotNull('auto_save_amount')->count() }}
                    </div>
                </div>

                <!-- Upcoming Deadlines -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-lg mr-3">
                            <flux:icon.clock class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">Urgent</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">< 30 days</p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ auth()->user()->goals()->where('target_date', '<=', now()->addDays(30))->where('status', 'active')->count() }}
                    </div>
                </div>
            </div>

            <!-- Smart Insights -->
            @php
                $user = auth()->user();
                $monthlyIncome = $user->transactions()->where('type', 'deposit')->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 0;
                $monthlyExpenses = $user->transactions()->where('type', 'withdrawal')->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 0;
                $savingsRate = $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100 : 0;
                $totalAutoSave = $user->goals()->whereNotNull('auto_save_amount')->sum('auto_save_amount');
            @endphp

            @if($goals->isNotEmpty())
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center mb-4">
                        <flux:icon.light-bulb class="h-6 w-6 text-yellow-500 mr-2" />
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Smart Insights</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Savings Rate Insight -->
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Savings Rate</h4>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ number_format($savingsRate, 1) }}%</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                @if($savingsRate >= 20)
                                    🎉 Excellent! You're saving well
                                @elseif($savingsRate >= 10)
                                    👍 Good savings rate
                                @else
                                    💡 Consider increasing your savings
                                @endif
                            </p>
                        </div>

                        <!-- Auto-Save Impact -->
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Monthly Auto-Save</h4>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">KES {{ number_format($totalAutoSave) }}</div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                @if($totalAutoSave > 0)
                                    ✨ {{ number_format(($totalAutoSave / $monthlyIncome) * 100, 1) }}% of income automated
                                @else
                                    🚀 Enable auto-save to boost progress
                                @endif
                            </p>
                        </div>

                        <!-- Goal Performance -->
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">On Track Goals</h4>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                                {{ $goals->flatten()->where('progress_percentage', '>=', 25)->count() }}/{{ $goals->flatten()->count() }}
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Goals with good progress
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Priority Goals (Due Soon) -->
            @if($upcomingGoals->isNotEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Priority Goals</h3>
                    <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-xs font-medium rounded-full">
                        Due Soon
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($upcomingGoals as $goal)
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $goal->title }}</h4>
                            <span class="text-xs font-medium px-2 py-1 bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-200 rounded-full">
                                {{ $goal->days_remaining }} days
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-sm mb-3">
                            <span class="text-zinc-600 dark:text-zinc-400">KES {{ number_format($goal->current_amount) }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->target_amount) }}</span>
                        </div>

                        @if($goal->auto_save_amount)
                            <div class="flex items-center text-xs text-green-700 dark:text-green-300 mb-2">
                                <flux:icon.bolt class="h-3 w-3 mr-1" />
                                Auto-save: KES {{ number_format($goal->auto_save_amount) }} {{ $goal->auto_save_frequency }}
                            </div>
                        @endif

                        <flux:button variant="outline" size="xs" :href="route('goals.show', $goal)" wire:navigate class="w-full">
                            View Details
                        </flux:button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Goals by Type -->
            <div class="space-y-6">
                @forelse($goals as $type => $typeGoals)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="text-2xl mr-3">
                                @if($type === \App\Models\Goal::TYPE_EMERGENCY_FUND)
                                    🛡️
                                @elseif($type === \App\Models\Goal::TYPE_HOME_PURCHASE)
                                    🏠
                                @elseif($type === \App\Models\Goal::TYPE_EDUCATION)
                                    🎓
                                @elseif($type === \App\Models\Goal::TYPE_RETIREMENT)
                                    🌅
                                @else
                                    🎯
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ \App\Models\Goal::getTypes()[$type] }}
                                </h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $typeGoals->count() }} goal{{ $typeGoals->count() !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        
                        <!-- Type Progress -->
                        <div class="text-right">
                            @php
                                $typeProgress = $typeGoals->sum('current_amount') / max($typeGoals->sum('target_amount'), 1) * 100;
                            @endphp
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($typeProgress, 1) }}%</div>
                            <div class="w-24 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $typeProgress }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($typeGoals as $goal)
                        <div class="bg-zinc-50 dark:bg-zinc-700 rounded-xl p-5 hover:shadow-md transition-shadow duration-200 border border-zinc-200 dark:border-zinc-600">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $goal->title }}</h4>
                                
                                <!-- Goal Status & Auto-save Indicator -->
                                <div class="flex items-center space-x-2">
                                    @if($goal->auto_save_amount)
                                        <div class="p-1 bg-green-100 dark:bg-green-800 rounded-full" title="Auto-save enabled">
                                            <flux:icon.bolt class="h-3 w-3 text-green-600 dark:text-green-400" />
                                        </div>
                                    @endif
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ 
                                        $goal->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                        ($goal->progress_percentage >= 75 ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
                                        'bg-zinc-100 text-zinc-800 dark:bg-zinc-600 dark:text-zinc-100')
                                    }}">
                                        @if($goal->status === 'completed')
                                            ✅ Done
                                        @elseif($goal->progress_percentage >= 75)
                                            🎯 Almost there
                                        @else
                                            📈 In progress
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if($goal->description)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 line-clamp-2">{{ $goal->description }}</p>
                            @endif
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-zinc-600 dark:text-zinc-400">Progress</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($goal->progress_percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Amount Progress -->
                            <div class="flex justify-between text-sm mb-4">
                                <span class="text-zinc-600 dark:text-zinc-400">KES {{ number_format($goal->current_amount) }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->target_amount) }}</span>
                            </div>

                            <!-- Goal Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 dark:text-zinc-400">Target Date:</span>
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $goal->target_date->format('M d, Y') }}</span>
                                </div>
                                
                                @if($goal->auto_save_amount)
                                    <div class="flex justify-between text-xs">
                                        <span class="text-zinc-500 dark:text-zinc-400">Auto-save:</span>
                                        <span class="text-green-600 dark:text-green-400 font-medium">
                                            KES {{ number_format($goal->auto_save_amount) }} {{ $goal->auto_save_frequency }}
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500 dark:text-zinc-400">Remaining:</span>
                                    <span class="text-zinc-700 dark:text-zinc-300">KES {{ number_format($goal->remaining_amount) }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <flux:button variant="outline" size="sm" :href="route('goals.show', $goal)" wire:navigate class="w-full">
                                <flux:icon.eye class="h-4 w-4 mr-1" />
                                {{ __('View Details') }}
                            </flux:button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <div class="text-6xl mb-4">🎯</div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('Ready to Set Your First Goal?') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-8 max-w-md mx-auto">
                        {{ __('Start your financial journey with intelligent goal setting and automatic savings. Our smart system will help you achieve your dreams faster.') }}
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto mb-8">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <div class="text-2xl mb-2">🧠</div>
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Smart Recommendations</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Get personalized advice based on your finances</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <div class="text-2xl mb-2">⚡</div>
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Auto-Save Magic</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Automatically save towards your goals</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                            <div class="text-2xl mb-2">📊</div>
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Progress Tracking</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Visual insights and milestone celebrations</p>
                        </div>
                    </div>

                    <flux:button variant="primary" size="lg" :href="route('goals.create')" wire:navigate>
                        <flux:icon.sparkles class="h-5 w-5 mr-2" />
                        {{ __('Create Your First Goal') }}
                    </flux:button>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app> 