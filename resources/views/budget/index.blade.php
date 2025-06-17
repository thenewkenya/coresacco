<x-layouts.app :title="__('Budget Planner')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Budget Planner') }}</h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Track your monthly finances') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" :href="route('budget.create')" wire:navigate>
                            <flux:icon.plus class="w-4 h-4 mr-1" />
                            {{ __('New Budget') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            @if($currentBudget)
            <!-- Month Selector -->
            <div class="flex items-center space-x-2 mb-8 overflow-x-auto pb-2">
                @foreach(range(-2, 2) as $monthOffset)
                    @php
                        $date = now()->addMonths($monthOffset);
                        $isActive = $date->month === $currentBudget->month && $date->year === $currentBudget->year;
                    @endphp
                    <flux:button 
                        :href="route('budget.index', ['month' => $date->format('Y-m')])"
                        :variant="$isActive ? 'primary' : 'outline'"
                        size="sm"
                        wire:navigate>
                        {{ $date->format('F') }}
                    </flux:button>
                @endforeach
            </div>

            <!-- Budget Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Initial Amount -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Initial Amount') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($currentBudget->total_income) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Expenses') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($currentBudget->total_expenses) }}
                            </p>
                        </div>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-4">
                        <div class="bg-red-600 dark:bg-red-500 h-1 rounded-full" 
                            style="width: {{ min(100, ($currentBudget->total_expenses / $currentBudget->total_income) * 100) }}%">
                        </div>
                    </div>
                </div>

                <!-- Additional Income -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.arrow-trending-up class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Additional Income') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($currentBudget->expenses()->where('amount', '>', 0)->sum('amount')) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Remaining -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.wallet class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Remaining') }}</p>
                            <p class="text-2xl font-bold {{ $currentBudget->remaining_budget >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                KES {{ number_format($currentBudget->remaining_budget) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operations Distribution -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('Operations Distribution') }}</h2>
                    <div class="flex items-center space-x-2">
                        <flux:button variant="primary" size="sm">
                            {{ __('Expenses') }}
                        </flux:button>
                        <flux:button variant="outline" size="sm">
                            {{ __('Income') }}
                        </flux:button>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Pie Chart -->
                    <div class="relative">
                        <canvas id="expenseDistribution" class="max-w-full h-64"></canvas>
                    </div>

                    <!-- Category List -->
                    <div class="space-y-4">
                        @foreach($currentBudget->expense_summary as $category)
                        <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $category['name'] }}</h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($category['progress'], 1) }}%</p>
                                </div>
                                <p class="text-right">
                                    <span class="block text-zinc-900 dark:text-zinc-100 font-medium">
                                        KES {{ number_format($category['actual']) }}
                                    </span>
                                    <span class="block text-sm text-zinc-600 dark:text-zinc-400">
                                        / {{ number_format($category['planned']) }}
                                    </span>
                                </p>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-1">
                                <div class="bg-blue-600 dark:bg-blue-500 h-1 rounded-full" 
                                    style="width: {{ min(100, $category['progress']) }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                <flux:icon.calculator class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                    {{ __('No Budget Set for This Month') }}
                </h3>
                <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                    {{ __('Start planning your monthly budget to track your income, expenses, and savings goals.') }}
                </p>
                <flux:button variant="primary" :href="route('budget.create')" wire:navigate>
                    <flux:icon.plus class="w-4 h-4 mr-1" />
                    {{ __('Create Your First Budget') }}
                </flux:button>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('expenseDistribution');
            if (!ctx) return;

            const categories = @json($currentBudget->expense_summary);
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categories.map(c => c.name),
                    datasets: [{
                        data: categories.map(c => c.actual),
                        backgroundColor: [
                            'rgb(37, 99, 235)', // blue-600
                            'rgb(220, 38, 38)', // red-600
                            'rgb(22, 163, 74)', // green-600
                            'rgb(147, 51, 234)', // purple-600
                            'rgb(234, 88, 12)', // orange-600
                            'rgb(219, 39, 119)', // pink-600
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.app> 