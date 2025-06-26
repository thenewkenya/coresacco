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
                            <flux:icon.banknotes class="w-6 h-6 text-purple-600 dark:text-purple-400" />
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

            <!-- Add Expense Form -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Record New Expense') }}</h2>
                
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <p class="text-yellow-700 dark:text-yellow-300">{{ session('warning') }}</p>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <p class="text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
                    </div>
                @endif
                
                <form action="{{ route('budget.expenses.store', $budget) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <flux:field>
                                <flux:label>Category</flux:label>
                                <flux:select name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\Budget::CATEGORIES as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
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
                    
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Description</flux:label>
                            <flux:input name="description" placeholder="What did you spend on?" required />
                        </flux:field>
                    </div>
                    
                    <flux:button type="submit" variant="primary">
                        {{ __('Record Expense') }}
                    </flux:button>
                </form>
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

            <!-- Recent Expenses -->
            @if($budget->expenses->count() > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Recent Expenses') }}</h2>
                
                <div class="space-y-4">
                    @foreach($budget->expenses->sortByDesc('created_at')->take(10) as $expense)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <flux:icon.receipt-percent class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $expense->description }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $expense->category_name }} • {{ $expense->date->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($expense->amount, 2) }}
                            </span>
                            @if($expense->receipt_url)
                                <a href="{{ asset('storage/' . $expense->receipt_url) }}" target="_blank" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    <flux:icon.document-text class="w-4 h-4" />
                                </a>
                            @endif
                            <form action="{{ route('budget.expenses.destroy', [$budget, $expense]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this expense?')"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                    <flux:icon.trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($budget->expenses->count() > 10)
                <div class="mt-4 text-center">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Showing 10 most recent expenses. Total: {{ $budget->expenses->count() }} expenses.
                    </p>
                </div>
                @endif
            </div>
            @endif

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