<x-layouts.app :title="__('Edit Goal')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Edit Goal') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Update your financial goal details') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <form action="{{ route('goals.update', $goal) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Goal Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Goal Title') }}
                        </label>
                        <input type="text" name="title" id="title" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                            value="{{ old('title', $goal->title) }}"
                            placeholder="{{ __('e.g. Emergency Fund') }}">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Goal Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Goal Type') }}
                        </label>
                        <select name="type" id="type" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required>
                            <option value="">{{ __('Select a goal type') }}</option>
                            @foreach($goalTypes as $key => $name)
                                <option value="{{ $key }}" {{ old('type', $goal->type) == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Amount -->
                    <div>
                        <label for="target_amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Target Amount (KES)') }}
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-zinc-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" name="target_amount" id="target_amount" 
                                class="pl-12 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                                min="1000"
                                step="100"
                                value="{{ old('target_amount', $goal->target_amount) }}"
                                placeholder="0.00">
                        </div>
                        @error('target_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Date -->
                    <div>
                        <label for="target_date" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Target Date') }}
                        </label>
                        <input type="date" name="target_date" id="target_date" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}">
                        @error('target_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Description') }}
                        </label>
                        <textarea name="description" id="description" rows="3" 
                            class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="{{ __('Describe your financial goal...') }}">{{ old('description', $goal->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Auto-Save Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('Auto-Save Settings (Optional)') }}
                        </h3>
                        
                        <div>
                            <label for="auto_save_amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ __('Auto-Save Amount (KES)') }}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                </div>
                                <input type="number" name="auto_save_amount" id="auto_save_amount" 
                                    class="pl-12 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    min="100"
                                    step="100"
                                    value="{{ old('auto_save_amount', $goal->auto_save_amount) }}"
                                    placeholder="0.00">
                            </div>
                            @error('auto_save_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="auto_save_frequency" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ __('Auto-Save Frequency') }}
                            </label>
                            <select name="auto_save_frequency" id="auto_save_frequency" 
                                class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">{{ __('Select frequency') }}</option>
                                <option value="weekly" {{ old('auto_save_frequency', $goal->auto_save_frequency) == 'weekly' ? 'selected' : '' }}>
                                    {{ __('Weekly') }}
                                </option>
                                <option value="monthly" {{ old('auto_save_frequency', $goal->auto_save_frequency) == 'monthly' ? 'selected' : '' }}>
                                    {{ __('Monthly') }}
                                </option>
                            </select>
                            @error('auto_save_frequency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <flux:button variant="ghost" :href="route('goals.show', $goal)" wire:navigate>
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Update Goal') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app> 