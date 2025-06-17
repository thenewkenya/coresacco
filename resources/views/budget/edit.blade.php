<x-layouts.app :title="__('Edit Budget')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Edit Budget') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Update your monthly budget plan') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <form action="{{ route('budget.update', $budget) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Budget Period -->
                    <div>
                        <label for="month" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Budget Month') }}
                        </label>
                        <input type="month" name="month" id="month" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                            value="{{ old('month', $budget->month->format('Y-m')) }}">
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Income Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('Monthly Income') }}
                        </h3>
                        
                        <div>
                            <label for="expected_income" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ __('Expected Income (KES)') }}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                </div>
                                <input type="number" name="expected_income" id="expected_income" 
                                    class="pl-12 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                    min="0"
                                    step="100"
                                    value="{{ old('expected_income', $budget->expected_income) }}"
                                    placeholder="0.00">
                            </div>
                            @error('expected_income')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Budget Items -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                                {{ __('Budget Categories') }}
                            </h3>
                            <button type="button" id="add-category" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                {{ __('Add Category') }}
                            </button>
                        </div>

                        <div id="budget-categories" class="space-y-4">
                            @foreach($budget->items as $index => $item)
                            <div class="budget-category bg-zinc-50 dark:bg-zinc-700 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Category Name') }}
                                        </label>
                                        <input type="text" name="categories[]" 
                                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                            required
                                            value="{{ old('categories.' . $index, $item->category) }}"
                                            placeholder="{{ __('e.g. Housing') }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ __('Planned Amount (KES)') }}
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-zinc-500 sm:text-sm">KES</span>
                                            </div>
                                            <input type="number" name="amounts[]" 
                                                class="pl-12 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                required
                                                min="0"
                                                step="100"
                                                value="{{ old('amounts.' . $index, $item->planned_amount) }}"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->first)
                                <button type="button" class="mt-2 text-sm text-red-600 hover:text-red-800 remove-category">
                                    {{ __('Remove Category') }}
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Budget Notes') }}
                        </label>
                        <textarea name="notes" id="notes" rows="3" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="{{ __('Add any notes or reminders about your budget...') }}">{{ old('notes', $budget->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <flux:button variant="ghost" :href="route('budget.show', $budget)" wire:navigate>
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Update Budget') }}
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
            const template = categoriesContainer.children[0].cloneNode(true);

            // Add click handler for existing remove buttons
            document.querySelectorAll('.remove-category').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.budget-category').remove();
                });
            });

            addButton.addEventListener('click', function() {
                const newCategory = template.cloneNode(true);
                // Clear input values
                newCategory.querySelectorAll('input').forEach(input => input.value = '');
                // Add remove button if it's not the first category
                if (categoriesContainer.children.length > 0) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'mt-2 text-sm text-red-600 hover:text-red-800';
                    removeButton.textContent = '{{ __("Remove Category") }}';
                    removeButton.onclick = function() {
                        newCategory.remove();
                    };
                    newCategory.appendChild(removeButton);
                }
                categoriesContainer.appendChild(newCategory);
            });
        });
    </script>
    @endpush
</x-layouts.app> 