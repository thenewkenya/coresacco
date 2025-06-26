<x-layouts.app :title="__('Budget Planner')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Budget') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Track your monthly spending') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if($currentBudget)
                            <flux:button variant="outline" :href="route('budget.show', $currentBudget)" wire:navigate>
                                {{ __('View Details') }}
                            </flux:button>
                        @endif
                        <flux:button variant="primary" :href="route('budget.create')" wire:navigate>
                            {{ __('New Budget') }}
                        </flux:button>
                    </div>
                </div>

                <!-- Month Selection -->
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <flux:button variant="outline" size="sm" href="{{ route('budget.index', ['month' => $prevMonth->format('Y-m')]) }}" wire:navigate>
                                <flux:icon.chevron-left class="w-4 h-4" />
                            </flux:button>
                            
                            <div class="text-center">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $currentMonth->format('F Y') }}
                                </h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    @if($currentBudget)
                                        Budget exists
                                    @else
                                        No budget set
                                    @endif
                                </p>
                            </div>
                            
                            <flux:button variant="outline" size="sm" href="{{ route('budget.index', ['month' => $nextMonth->format('Y-m')]) }}" wire:navigate>
                                <flux:icon.chevron-right class="w-4 h-4" />
                            </flux:button>
                        </div>

                        <!-- Quick Month Navigation -->
                        <div class="hidden md:flex items-center space-x-2">
                            @foreach(range(-2, 2) as $offset)
                                @php
                                    $month = now()->addMonths($offset);
                                    $isSelected = $month->format('Y-m') === $currentMonth->format('Y-m');
                                    $variant = $isSelected ? 'primary' : 'outline';
                                @endphp
                                <flux:button 
                                    variant="{{ $variant }}" 
                                    size="sm"
                                    href="{{ route('budget.index', ['month' => $month->format('Y-m')]) }}" 
                                    wire:navigate>
                                    {{ $month->format('M') }}
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            
            @if($currentBudget)
                <!-- Current Month Summary -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Monthly Income -->
                        <div class="text-center">
                            <div class="p-4 bg-green-100 dark:bg-green-900/30 rounded-lg mb-3">
                                <flux:icon.banknotes class="w-8 h-8 text-green-600 dark:text-green-400 mx-auto" />
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Monthly Income</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($currentBudget->total_income, 0) }}
                            </p>
                        </div>

                        <!-- Total Spent -->
                        <div class="text-center">
                            <div class="p-4 bg-red-100 dark:bg-red-900/30 rounded-lg mb-3">
                                <flux:icon.credit-card class="w-8 h-8 text-red-600 dark:text-red-400 mx-auto" />
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Spent</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($currentBudget->expenses()->sum('amount'), 0) }}
                            </p>
                        </div>

                        <!-- Money Left -->
                        <div class="text-center">
                            @php 
                                $remaining = $currentBudget->total_income - $currentBudget->expenses()->sum('amount');
                                $isPositive = $remaining >= 0;
                                $bgClass = $isPositive ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30';
                                $textClass = $isPositive ? 'text-blue-600 dark:text-blue-400' : 'text-yellow-600 dark:text-yellow-400';
                            @endphp
                            <div class="p-4 {{ $bgClass }} rounded-lg mb-3">
                                @if($isPositive)
                                    <flux:icon.wallet class="w-8 h-8 text-blue-600 dark:text-blue-400 mx-auto" />
                                @else
                                    <flux:icon.wallet class="w-8 h-8 text-yellow-600 dark:text-yellow-400 mx-auto" />
                                @endif
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Money Left</p>
                            <p class="text-2xl font-bold {{ $textClass }}">
                                KES {{ number_format($remaining, 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Budget Breakdown -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            <flux:icon.chart-pie class="w-5 h-5 inline mr-2" />
                            Budget Breakdown
                        </h3>
                        <flux:button variant="outline" size="sm" :href="route('budget.show', $currentBudget)" wire:navigate>
                            View Full Details
                        </flux:button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($currentBudget->items as $item)
                            @php
                                $actualSpent = $currentBudget->expenses()->where('category', $item->category)->sum('amount');
                                $progress = $item->amount > 0 ? min(100, ($actualSpent / $item->amount) * 100) : 0;
                                $remaining = $item->amount - $actualSpent;
                                $isOverBudget = $actualSpent > $item->amount;
                                $isNearLimit = $progress >= 80 && !$isOverBudget;
                            @endphp
                            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg
                                @if($isOverBudget) bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800
                                @elseif($isNearLimit) bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800
                                @else bg-zinc-50 dark:bg-zinc-700
                                @endif">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ \App\Models\Budget::CATEGORIES[$item->category] ?? $item->category }}
                                    </h4>
                                    <span class="text-xs px-2 py-1 rounded-full
                                        @if($isOverBudget) bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200
                                        @elseif($isNearLimit) bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200
                                        @else bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200
                                        @endif">
                                        {{ number_format($progress, 0) }}%
                                    </span>
                                </div>
                                
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Budgeted:</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($item->amount, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Spent:</span>
                                        <span class="font-medium 
                                            @if($isOverBudget) text-red-600 dark:text-red-400
                                            @else text-zinc-900 dark:text-zinc-100
                                            @endif">
                                            KES {{ number_format($actualSpent, 0) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Remaining:</span>
                                        <span class="font-medium 
                                            @if($remaining >= 0) text-green-600 dark:text-green-400
                                            @else text-red-600 dark:text-red-400
                                            @endif">
                                            KES {{ number_format($remaining, 0) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300
                                            @if($isOverBudget) bg-red-500
                                            @elseif($isNearLimit) bg-yellow-500
                                            @else bg-green-500
                                            @endif" 
                                            style="width: {{ min(100, $progress) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Expense Recording -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Add Expense Form -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                            <flux:icon.plus class="w-5 h-5 inline mr-2" />
                            Record Expense
                        </h3>
                        
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="mb-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">{{ session('warning') }}</p>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
                            </div>
                        @endif
                        
                        <form action="{{ route('budget.expenses.store', $currentBudget) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <flux:field>
                                        <flux:label>Category</flux:label>
                                        <flux:select name="category" required>
                                            <option value="">Select Category</option>
                                            @foreach($currentBudget->items as $item)
                                                <option value="{{ $item->category }}">
                                                    {{ \App\Models\Budget::CATEGORIES[$item->category] ?? $item->category }}
                                                </option>
                                            @endforeach
                                        </flux:select>
                                    </flux:field>
                                </div>
                                
                                <div>
                                    <flux:field>
                                        <flux:label>Amount (KES)</flux:label>
                                        <flux:input name="amount" type="number" step="0.01" placeholder="0.00" required />
                                    </flux:field>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <flux:field>
                                        <flux:label>Date</flux:label>
                                        <flux:input name="date" type="date" value="{{ now()->format('Y-m-d') }}" required />
                                    </flux:field>
                                </div>
                                
                                <div>
                                    <flux:field>
                                        <flux:label>Receipt (Optional)</flux:label>
                                        <flux:input name="receipt" type="file" accept="image/*,application/pdf" />
                                    </flux:field>
                                </div>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Description</flux:label>
                                    <flux:input name="description" placeholder="What did you spend on?" required />
                                </flux:field>
                            </div>
                            
                            <flux:button type="submit" variant="primary" class="w-full">
                                Record Expense
                            </flux:button>
                        </form>
                    </div>

                    <!-- Recent Expenses -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                            <flux:icon.clock class="w-5 h-5 inline mr-2" />
                            Recent Expenses
                        </h3>
                        
                        @if($currentBudget->expenses->count() > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($currentBudget->expenses->sortByDesc('created_at')->take(8) as $expense)
                                <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $expense->description }}</p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $expense->category_name }} • {{ $expense->date->format('M j') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($expense->amount, 0) }}
                                        </span>
                                        @if($expense->receipt_url)
                                            <a href="{{ asset('storage/' . $expense->receipt_url) }}" target="_blank" 
                                               class="text-blue-600 dark:text-blue-400">
                                                <flux:icon.document-text class="w-4 h-4" />
                                            </a>
                                        @endif
                                        <form action="{{ route('budget.expenses.destroy', [$currentBudget, $expense]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Delete this expense?')"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800">
                                                <flux:icon.trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            @if($currentBudget->expenses->count() > 8)
                            <div class="mt-4 text-center">
                                <flux:button variant="outline" size="sm" :href="route('budget.show', $currentBudget)" wire:navigate>
                                    View All {{ $currentBudget->expenses->count() }} Expenses
                                </flux:button>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <flux:icon.receipt-percent class="mx-auto h-12 w-12 text-zinc-400 mb-3" />
                                <p class="text-zinc-600 dark:text-zinc-400">No expenses recorded yet.</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-500">Start tracking your spending!</p>
                            </div>
                        @endif
                    </div>
                </div>

            @else
                <!-- No Budget Set -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <flux:icon.calculator class="mx-auto h-16 w-16 text-zinc-400 mb-4" />
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                            {{ __('No Budget Set') }}
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                            {{ __('Create your first budget to start tracking your monthly spending and savings.') }}
                        </p>
                        
                        <flux:button variant="primary" :href="route('budget.create')" wire:navigate>
                            {{ __('Create My First Budget') }}
                        </flux:button>
                    </div>
                </div>
            @endif

            <!-- Previous Budgets -->
            @if($historicalBudgets->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            Previous Budgets
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($historicalBudgets->take(3) as $budget)
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::createFromDate($budget->year, $budget->month, 1)->format('F Y') }}
                        </p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            Income: KES {{ number_format($budget->total_income, 0) }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        @php $spent = $budget->expenses()->sum('amount'); @endphp
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($spent, 0) }} spent
                                        </p>
                                        <flux:button variant="outline" :href="route('budget.show', $budget)" wire:navigate>
                                            View
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 