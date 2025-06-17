<x-layouts.app :title="__('Budget Details')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Budget for :month', ['month' => \Carbon\Carbon::createFromDate($budget->year, $budget->month, 1)->format('F Y')]) }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Track and manage your monthly budget') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" :href="route('budget.index')" wire:navigate>
                            {{ __('Back to Budgets') }}
                        </flux:button>
                        <flux:button variant="primary" :href="route('budget.create')" wire:navigate>
                            {{ __('New Budget') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Budget Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Income') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($budget->total_income, 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Expenses') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($budget->total_expenses, 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.piggy-bank class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Savings Target') }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($budget->savings_target, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Categories -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Budget Categories') }}</h2>
                
                <div class="space-y-6">
                    @foreach($budget->items as $item)
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->category }}</h3>
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($item->amount, 2) }}
                            </span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($item->expenses_sum / $item->amount) * 100) }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">
                                {{ __('Spent:') }} KES {{ number_format($item->expenses_sum ?? 0, 2) }}
                            </span>
                            <span class="text-zinc-600 dark:text-zinc-400">
                                {{ __('Remaining:') }} KES {{ number_format($item->amount - ($item->expenses_sum ?? 0), 2) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            @if($budget->notes)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Budget Notes') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 whitespace-pre-line">{{ $budget->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app> 