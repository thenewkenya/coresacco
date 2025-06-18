<x-layouts.app :title="__('Create Budget')">
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
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <form method="POST" action="{{ route('budget.store') }}" class="space-y-8" x-data="{
                    month: {{ old('month_number', now()->month) }},
                    year: {{ old('year', now()->year) }},
                    getFormattedMonth() {
                        return this.year + '-' + this.month.toString().padStart(2, '0');
                    }
                }" @submit.prevent="
                    $el.querySelector('[name=month]').value = getFormattedMonth();
                    $el.submit();
                ">
                    @csrf

                    <!-- Budget Period -->
                    <flux:field>
                        <flux:label>{{ __('Select Budget Period') }}</flux:label>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:select 
                                name="month_number" 
                                required
                                x-model="month">
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ old('month_number', now()->month) == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:select 
                                name="year" 
                                required
                                x-model="year">
                                @foreach(range(now()->year, now()->addYears(2)->year) as $year)
                                    <option value="{{ $year }}" {{ old('year', now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>
                        <input type="hidden" name="month" />
                        <flux:description>
                            {{ __('Choose the month and year for which you want to create this budget plan') }}
                        </flux:description>
                        @error('month')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Income Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('Monthly Income') }}
                        </h3>
                        
                        <flux:field>
                            <flux:label>{{ __('Expected Income (KES)') }}</flux:label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                </div>
                                <flux:input 
                                    name="expected_income"
                                    type="number"
                                    required
                                    min="0"
                                    step="100"
                                    value="{{ old('expected_income') }}"
                                    placeholder="0.00"
                                    class="pl-12" />
                            </div>
                            @error('expected_income')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>

                    <!-- Budget Items -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('Budget Categories') }}
                            </h3>
                            <flux:button 
                                type="button" 
                                id="add-category" 
                                variant="primary">
                                <flux:icon.plus class="w-4 h-4 mr-1" />
                                {{ __('Add Category') }}
                            </flux:button>
                        </div>

                        <div id="budget-categories" class="space-y-4">
                            <!-- Housing Category -->
                            <div class="budget-category bg-zinc-50 dark:bg-zinc-700 p-4 rounded-lg relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <flux:field>
                                            <flux:label>{{ __('Category Name') }}</flux:label>
                                            <flux:input 
                                                name="categories[]"
                                                type="text"
                                                required
                                                value="Housing"
                                                placeholder="{{ __('e.g. Housing') }}" />
                                        </flux:field>
                                    </div>
                                    <div>
                                        <flux:field>
                                            <flux:label>{{ __('Planned Amount (KES)') }}</flux:label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                                </div>
                                                <flux:input 
                                                    name="amounts[]"
                                                    type="number"
                                                    required
                                                    min="0"
                                                    step="100"
                                                    placeholder="0.00"
                                                    class="pl-12" />
                                            </div>
                                        </flux:field>
                                    </div>
                                </div>
                            </div>

                            <!-- Food Category -->
                            <div class="budget-category bg-zinc-50 dark:bg-zinc-700 p-4 rounded-lg relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <flux:field>
                                            <flux:label>{{ __('Category Name') }}</flux:label>
                                            <flux:input 
                                                name="categories[]"
                                                type="text"
                                                required
                                                value="Food & Groceries"
                                                placeholder="{{ __('e.g. Food') }}" />
                                        </flux:field>
                                    </div>
                                    <div>
                                        <flux:field>
                                            <flux:label>{{ __('Planned Amount (KES)') }}</flux:label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                                </div>
                                                <flux:input 
                                                    name="amounts[]"
                                                    type="number"
                                                    required
                                                    min="0"
                                                    step="100"
                                                    placeholder="0.00"
                                                    class="pl-12" />
                                            </div>
                                        </flux:field>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <flux:field>
                        <flux:label>{{ __('Budget Notes') }}</flux:label>
                        <flux:textarea 
                            name="notes"
                            rows="3"
                            placeholder="{{ __('Add any notes or reminders about your budget...') }}">{{ old('notes') }}</flux:textarea>
                        @error('notes')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addButton = document.getElementById('add-category');
            const categoriesContainer = document.getElementById('budget-categories');
            const template = categoriesContainer.children[0];

            addButton.addEventListener('click', function() {
                const newCategory = template.cloneNode(true);
                
                // Clear the input values in the clone
                newCategory.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                // Add remove button if it's not the first category
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'absolute top-2 right-2 text-zinc-400 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400';
                removeButton.innerHTML = `
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="sr-only">${ __('Remove Category') }</span>
                `;
                removeButton.onclick = function() {
                    newCategory.remove();
                };
                newCategory.appendChild(removeButton);
                
                categoriesContainer.appendChild(newCategory);
            });
        });
    </script>
    @endpush
</x-layouts.app> 