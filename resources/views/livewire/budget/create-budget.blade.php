<?php

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] class extends Component {
    public $month;
    public $year;
    public $total_income = '';
    public $notes = '';
    public $categories = [];
    public $budgetExists = false;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        
        $this->initializeCategories();
        $this->checkExistingBudget();
    }

    public function initializeCategories()
    {
        // Basic budget categories with recommended percentages
        $this->categories = [
            [
                'name' => 'Housing & Utilities', 
                'amount' => '', 
                'key' => 'housing', 
                'recommended_percentage' => 30,
                'tip' => 'Rent, mortgage, electricity, water, internet'
            ],
            [
                'name' => 'Food & Groceries', 
                'amount' => '', 
                'key' => 'food', 
                'recommended_percentage' => 12,
                'tip' => 'Groceries, dining out, beverages'
            ],
            [
                'name' => 'Transportation', 
                'amount' => '', 
                'key' => 'transportation', 
                'recommended_percentage' => 15,
                'tip' => 'Fuel, matatu fare, car maintenance'
            ],
            [
                'name' => 'Healthcare', 
                'amount' => '', 
                'key' => 'healthcare', 
                'recommended_percentage' => 8,
                'tip' => 'Medical bills, pharmacy, health services'
            ],
            [
                'name' => 'Savings', 
                'amount' => '', 
                'key' => 'savings', 
                'recommended_percentage' => 20,
                'tip' => 'Emergency fund, investments, future goals'
            ],
            [
                'name' => 'Entertainment', 
                'amount' => '', 
                'key' => 'entertainment', 
                'recommended_percentage' => 10,
                'tip' => 'Movies, hobbies, social activities'
            ],
        ];
    }

    public function checkExistingBudget()
    {
        $this->budgetExists = Budget::where('user_id', auth()->id())
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->exists();
    }

    public function updated($property)
    {
        if ($property === 'total_income' && $this->total_income > 0) {
            $this->suggestAmounts();
        }
        
        if ($property === 'month' || $property === 'year') {
            $this->checkExistingBudget();
        }
    }

    public function suggestAmounts()
    {
        $income = (float)$this->total_income;
        if ($income <= 0) return;

        foreach ($this->categories as &$category) {
            if (empty($category['amount'])) {
                $suggestedAmount = ($category['recommended_percentage'] / 100) * $income;
                $category['amount'] = round($suggestedAmount, -2); // Round to nearest 100
            }
        }
    }

    public function applyRecommendations()
    {
        $income = (float)$this->total_income;
        if ($income <= 0) return;

        foreach ($this->categories as &$category) {
            $suggestedAmount = ($category['recommended_percentage'] / 100) * $income;
            $category['amount'] = round($suggestedAmount, -2); // Round to nearest 100
        }

        session()->flash('success', 'Recommended amounts applied to all categories!');
    }

    public function addCategory()
    {
        $this->categories[] = [
            'name' => '',
            'amount' => '',
            'key' => 'custom_' . count($this->categories),
            'recommended_percentage' => 5,
            'tip' => 'Custom category'
        ];
    }

    public function removeCategory($index)
    {
        unset($this->categories[$index]);
        $this->categories = array_values($this->categories);
    }

    public function calculateTotalExpenses()
    {
        return array_sum(array_filter(array_column($this->categories, 'amount'), 'is_numeric'));
    }

    public function getRemainingBudget()
    {
        return (float)$this->total_income - $this->calculateTotalExpenses();
    }

    public function createBudget()
    {
        $this->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:' . (now()->year - 1),
            'total_income' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.amount' => 'required|numeric|min:0',
        ]);

        // Check if budget already exists for this period
        $existingBudget = Budget::where('user_id', auth()->id())
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->first();

        if ($existingBudget) {
            $this->addError('month', 'A budget already exists for this period.');
            return;
        }

        try {
            DB::transaction(function () {
                // Create budget
                $budget = Budget::create([
                    'user_id' => auth()->id(),
                    'month' => $this->month,
                    'year' => $this->year,
                    'total_income' => $this->total_income,
                    'total_expenses' => $this->calculateTotalExpenses(),
                    'savings_target' => $this->getRemainingBudget(),
                    'notes' => $this->notes,
                ]);

                // Create budget items
                foreach ($this->categories as $category) {
                    if (!empty($category['name']) && !empty($category['amount'])) {
                        BudgetItem::create([
                            'budget_id' => $budget->id,
                            'category' => $category['key'],
                            'amount' => $category['amount'],
                            'is_recurring' => false,
                        ]);
                    }
                }
            });

            session()->flash('success', 'Budget created successfully!');
            $this->redirect(route('budget.index'), navigate: true);

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to create budget. Please try again.');
        }
    }
}; ?>

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ __('Create Budget') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Plan your budget for') }} {{ date('F Y', mktime(0, 0, 0, $month ?? now()->month, 1, $year ?? now()->year)) }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <flux:button variant="outline" href="{{ route('budget.index') }}" wire:navigate>
                        {{ __('Back to Budgets') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <form wire:submit="createBudget" class="space-y-8">
            <!-- Budget Period -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    Budget Period
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>{{ __('Month') }}</flux:label>
                        <flux:select wire:model.live="month">
                            @foreach(range(1, 12) as $monthNum)
                                <option value="{{ $monthNum }}">{{ date('F', mktime(0, 0, 0, $monthNum, 1)) }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Year') }}</flux:label>
                        <flux:select wire:model.live="year">
                            @foreach(range(now()->year - 1, now()->year + 2) as $yearNum)
                                <option value="{{ $yearNum }}">{{ $yearNum }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                @if($budgetExists)
                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <flux:icon.exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                            <p class="text-sm text-amber-800 dark:text-amber-200">
                                A budget already exists for {{ date('F Y', mktime(0, 0, 0, $month ?? now()->month, 1, $year ?? now()->year)) }}.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Income Section -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    Monthly Income
                </h3>
                
                <flux:field>
                    <flux:label>{{ __('Expected Income (KES)') }}</flux:label>
                    <flux:input 
                        wire:model.live="total_income"
                        type="number"
                        required
                        min="0"
                        step="1000"
                        placeholder="e.g. 50,000" />
                    <flux:description>
                        {{ __('Enter your total expected monthly income') }}
                    </flux:description>
                </flux:field>

                @if($total_income > 0)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-blue-900 dark:text-blue-100">Budget Guidelines</h4>
                            <flux:button 
                                type="button" 
                                variant="outline" 
                                size="sm" 
                                wire:click="applyRecommendations"
                                class="text-blue-600 border-blue-300 hover:bg-blue-100 dark:text-blue-400 dark:border-blue-600 dark:hover:bg-blue-900/30">
                                Apply Recommendations
                            </flux:button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Needs (50%):</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($total_income * 0.5) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Wants (30%):</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($total_income * 0.3) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Savings (20%):</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($total_income * 0.2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Budget Categories -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        Budget Categories
                    </h3>
                    <flux:button type="button" variant="outline" wire:click="addCategory">
                        Add Category
                    </flux:button>
                </div>

                <div class="space-y-4">
                    @foreach($categories as $index => $category)
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <flux:field>
                                    <flux:label>Category Name</flux:label>
                                    <flux:input 
                                        wire:model="categories.{{ $index }}.name"
                                        placeholder="e.g. Food & Groceries"
                                        required />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Amount (KES)</flux:label>
                                    <flux:input 
                                        wire:model="categories.{{ $index }}.amount"
                                        type="number"
                                        min="0"
                                        step="100"
                                        placeholder="0"
                                        required />
                                </flux:field>

                                <div class="flex items-end">
                                    <flux:button 
                                        type="button" 
                                        variant="danger" 
                                        wire:click="removeCategory({{ $index }})"
                                        class="w-full">
                                        Remove
                                    </flux:button>
                                </div>
                            </div>

                            @if(isset($category['tip']))
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    💡 {{ $category['tip'] }}
                                    @if($total_income > 0)
                                        • Recommended: KES {{ number_format(($category['recommended_percentage'] / 100) * $total_income) }} ({{ $category['recommended_percentage'] }}%)
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($total_income > 0)
                    <div class="mt-6 p-4 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Total Expenses:</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($this->calculateTotalExpenses()) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-2">
                            <span class="text-zinc-600 dark:text-zinc-400">Remaining:</span>
                            <span class="font-bold {{ $this->getRemainingBudget() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                KES {{ number_format($this->getRemainingBudget()) }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Notes -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    Notes (Optional)
                </h3>
                
                <flux:field>
                    <flux:textarea 
                        wire:model="notes"
                        placeholder="Add any notes about this budget..."
                        rows="3" />
                </flux:field>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <flux:button variant="outline" href="{{ route('budget.index') }}" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button type="submit">
                    Create Budget
                </flux:button>
            </div>
        </form>
    </div>
</div> 