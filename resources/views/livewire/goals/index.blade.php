<?php

use App\Models\Goal;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $goals;
    public $upcomingGoals;
    public $overallProgress;

    public function mount()
    {
        $this->loadGoalsData();
    }

    public function loadGoalsData()
    {
        $user = auth()->user();
        
        // Get all goals grouped by type
        $this->goals = $user->goals()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('type');

        // Get upcoming goals (due within 30 days)
        $this->upcomingGoals = $user->goals()
            ->where('target_date', '<=', now()->addDays(30))
            ->where('status', 'active')
            ->orderBy('target_date', 'asc')
            ->get();

        // Calculate overall progress
        $totalCurrent = $user->goals()->sum('current_amount');
        $totalTarget = $user->goals()->sum('target_amount');
        $this->overallProgress = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;
    }

    public function getSavingsRate()
    {
        $user = auth()->user();
        $monthlyIncome = $user->transactions()->where('type', 'deposit')->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 0;
        $monthlyExpenses = $user->transactions()->where('type', 'withdrawal')->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 0;
        
        return $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100 : 0;
    }

    public function getTotalAutoSave()
    {
        return auth()->user()->goals()->whereNotNull('auto_save_amount')->sum('auto_save_amount');
    }

    public function getOnTrackGoalsCount()
    {
        return auth()->user()->goals()->where('progress_percentage', '>=', 25)->count();
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">My Financial Goals</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Track your progress with intelligent insights and auto-saving</flux:subheading>
        </div>
        <div class="flex items-center space-x-3">
            <flux:button variant="outline" icon="chart-bar">
                Analytics
            </flux:button>
            <flux:button variant="primary" :href="route('goals.create')" icon="sparkles">
                Create Goal
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Overall Progress -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Overall Progress</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Across all your goals</flux:subheading>
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
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Auto-Save</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Active Goals</flux:subheading>
                </div>
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.bolt class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ auth()->user()->goals()->whereNotNull('auto_save_amount')->count() }}
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Urgent</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">< 30 days</flux:subheading>
                </div>
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.clock class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ auth()->user()->goals()->where('target_date', '<=', now()->addDays(30))->where('status', 'active')->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Insights -->
    @if($goals->isNotEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <flux:icon.light-bulb class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Smart Insights</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">AI-powered financial recommendations</flux:subheading>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Savings Rate Insight -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Savings Rate</h4>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ number_format($this->getSavingsRate(), 1) }}%</div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        @if($this->getSavingsRate() >= 20)
                            🎉 Excellent! You're saving well
                        @elseif($this->getSavingsRate() >= 10)
                            👍 Good savings rate
                        @else
                            💡 Consider increasing your savings
                        @endif
                    </p>
                </div>

                <!-- Auto-Save Impact -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Monthly Auto-Save</h4>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">KES {{ number_format($this->getTotalAutoSave()) }}</div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        @if($this->getTotalAutoSave() > 0)
                            ✨ {{ number_format(($this->getTotalAutoSave() / (auth()->user()->transactions()->where('type', 'deposit')->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 1)) * 100, 1) }}% of income automated
                        @else
                            🚀 Enable auto-save to boost progress
                        @endif
                    </p>
                </div>

                <!-- Goal Performance -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">On Track Goals</h4>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                        {{ $this->getOnTrackGoalsCount() }}/{{ $goals->flatten()->count() }}
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
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <flux:icon.exclamation-triangle class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Priority Goals</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Goals due within 30 days</flux:subheading>
                    </div>
                </div>
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

                    <flux:button variant="outline" size="sm" :href="route('goals.show', $goal)" icon="eye" class="w-full">
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
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">
                        @switch($type)
                            @case(\App\Models\Goal::TYPE_EMERGENCY_FUND)
                                🛡️
                                @break
                            @case(\App\Models\Goal::TYPE_HOME_PURCHASE)
                                🏠
                                @break
                            @case(\App\Models\Goal::TYPE_EDUCATION)
                                🎓
                                @break
                            @case(\App\Models\Goal::TYPE_RETIREMENT)
                                🌅
                                @break
                            @default
                                🎯
                        @endswitch
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">
                            {{ \App\Models\Goal::getTypes()[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
                        </flux:heading>
                        <flux:subheading class="dark:text-zinc-400">{{ $typeGoals->count() }} goal{{ $typeGoals->count() !== 1 ? 's' : '' }}</flux:subheading>
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
                    <flux:button variant="outline" size="sm" :href="route('goals.show', $goal)" icon="eye" class="w-full">
                        View Details
                    </flux:button>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-12 text-center border border-zinc-200 dark:border-zinc-700">
            <div class="text-6xl mb-4">🎯</div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-2">Ready to Set Your First Goal?</flux:heading>
            <flux:subheading class="dark:text-zinc-400 mb-8 max-w-md mx-auto">
                Start your financial journey with intelligent goal setting and automatic savings. Our smart system will help you achieve your dreams faster.
            </flux:subheading>
            
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

            <flux:button variant="primary" size="lg" :href="route('goals.create')" icon="sparkles">
                Create Your First Goal
            </flux:button>
        </div>
        @endforelse
    </div>
</div>
