<?php

use App\Models\Budget;
use App\Models\BudgetItem;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] class extends Component {
    public $month;
    public $year;
    public $total_income = '';
    public $savings_target = '';
    public $notes = '';
    public $categories = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        
        // Initialize with default categories
        $this->categories = [
            ['name' => 'Housing & Utilities', 'amount' => '', 'key' => 'housing'],
            ['name' => 'Food & Groceries', 'amount' => '', 'key' => 'food'],
            ['name' => 'Transportation', 'amount' => '', 'key' => 'transportation'],
            ['name' => 'Healthcare', 'amount' => '', 'key' => 'healthcare'],
        ];
    }

    public function addCategory()
    {
        $this->categories[] = [
            'name' => '',
            'amount' => '',
            'key' => 'custom_' . time()
        ];
    }

    public function removeCategory($index)
    {
        unset($this->categories[$index]);
        $this->categories = array_values($this->categories);
    }

    public function calculateTotalExpenses()
    {
        return array_sum(array_column($this->categories, 'amount'));
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
            'savings_target' => 'required|numeric|min:0',
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
                    'savings_target' => $this->savings_target,
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
                        {{ __('Create Monthly Budget') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Plan your monthly income and expenses') }}
                    </p>
                </div>
                <flux:button variant="ghost" :href="route('budget.index')" wire:navigate>
                    {{ __('Back') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <form wire:submit="createBudget" class="space-y-8">
                
                <!-- Budget Period -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Budget Period') }}
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Month') }}</flux:label>
                            <flux:select wire:model.live="month" required>
                                @foreach(range(1, 12) as $monthNum)
                                    <option value="{{ $monthNum }}">
                                        {{ date('F', mktime(0, 0, 0, $monthNum, 1)) }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Year') }}</flux:label>
                            <flux:select wire:model.live="year" required>
                                @foreach(range(now()->year - 1, now()->addYears(2)->year) as $yearNum)
                                    <option value="{{ $yearNum }}">{{ $yearNum }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <!-- Income Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Monthly Income') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Expected Income (KES)') }}</flux:label>
                            <flux:input 
                                wire:model.live="total_income"
                                type="number"
                                required
                                min="0"
                                step="100"
                                placeholder="0.00" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Savings Target (KES)') }}</flux:label>
                            <flux:input 
                                wire:model.live="savings_target"
                                type="number"
                                required
                                min="0"
                                step="100"
                                placeholder="0.00" />
                        </flux:field>
                    </div>
                </div>

                <!-- Budget Categories -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('Budget Categories') }}
                        </h3>
                        <flux:button 
                            type="button" 
                            wire:click="addCategory"
                            variant="primary"
                            size="sm">
                            <flux:icon.plus class="w-4 h-4 mr-1" />
                            {{ __('Add Category') }}
                        </flux:button>
                    </div>

                    <div class="space-y-4">
                        @foreach($categories as $index => $category)
                            <div class="bg-zinc-50 dark:bg-zinc-700 p-4 rounded-lg relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>{{ __('Category Name') }}</flux:label>
                                        <flux:input 
                                            wire:model="categories.{{ $index }}.name"
                                            type="text"
                                            required
                                            placeholder="{{ __('e.g. Housing') }}" />
                                    </flux:field>

                                    <div class="flex gap-2">
                                        <flux:field class="flex-1">
                                            <flux:label>{{ __('Planned Amount (KES)') }}</flux:label>
                                            <flux:input 
                                                wire:model.live="categories.{{ $index }}.amount"
                                                type="number"
                                                required
                                                min="0"
                                                step="100"
                                                placeholder="0.00" />
                                        </flux:field>

                                        @if($index > 3)
                                            <div class="flex items-end">
                                                <flux:button 
                                                    type="button"
                                                    wire:click="removeCategory({{ $index }})"
                                                    variant="danger"
                                                    size="sm">
                                                    <flux:icon.trash class="w-4 h-4" />
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Budget Summary -->
                    @if($total_income)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-zinc-600 dark:text-zinc-400">Total Income:</span>
                                    <div class="font-semibold text-green-600 dark:text-green-400">
                                        KES {{ number_format($total_income) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-zinc-600 dark:text-zinc-400">Total Expenses:</span>
                                    <div class="font-semibold text-red-600 dark:text-red-400">
                                        KES {{ number_format($this->calculateTotalExpenses()) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-zinc-600 dark:text-zinc-400">Remaining:</span>
                                    <div class="font-semibold {{ $this->getRemainingBudget() >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        KES {{ number_format($this->getRemainingBudget()) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:field>
                        <flux:label>{{ __('Budget Notes') }}</flux:label>
                        <flux:textarea 
                            wire:model="notes"
                            rows="3"
                            placeholder="{{ __('Add any notes or reminders about your budget...') }}"></flux:textarea>
                    </flux:field>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <flux:button variant="ghost" :href="route('budget.index')" wire:navigate>
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ __('Create Budget') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div> 