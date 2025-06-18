<x-layouts.app :title="__('Create Goal')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Create Financial Goal') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Set up a new savings goal and track your progress') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <form action="{{ route('goals.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Goal Title -->
                    <flux:field>
                        <flux:label>{{ __('Goal Title') }}</flux:label>
                        <flux:input 
                            name="title"
                            type="text"
                            required
                            value="{{ old('title') }}"
                            placeholder="{{ __('e.g. Emergency Fund') }}" />
                        @error('title')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Goal Type -->
                    <flux:field>
                        <flux:label>{{ __('Goal Type') }}</flux:label>
                        <flux:select name="type" required>
                            <option value="">{{ __('Select a goal type') }}</option>
                            @foreach($goalTypes as $key => $name)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('type')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Target Amount -->
                    <flux:field>
                        <flux:label>{{ __('Target Amount (KES)') }}</flux:label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-zinc-500 sm:text-sm">KES</span>
                            </div>
                            <flux:input 
                                name="target_amount"
                                type="number"
                                required
                                min="1000"
                                step="100"
                                value="{{ old('target_amount') }}"
                                placeholder="0.00"
                                class="pl-12" />
                        </div>
                        @error('target_amount')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Target Date -->
                    <flux:field>
                        <flux:label>{{ __('Target Date') }}</flux:label>
                        <flux:input 
                            name="target_date"
                            type="date"
                            required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            value="{{ old('target_date') }}" />
                        @error('target_date')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Description -->
                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea 
                            name="description"
                            rows="3"
                            placeholder="{{ __('Describe your financial goal...') }}">{{ old('description') }}</flux:textarea>
                        @error('description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Auto-Save Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('Auto-Save Settings (Optional)') }}
                        </h3>
                        
                        <flux:field>
                            <flux:label>{{ __('Auto-Save Amount (KES)') }}</flux:label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-zinc-500 sm:text-sm">KES</span>
                                </div>
                                <flux:input 
                                    name="auto_save_amount"
                                    type="number"
                                    min="100"
                                    step="100"
                                    value="{{ old('auto_save_amount') }}"
                                    placeholder="0.00"
                                    class="pl-12" />
                            </div>
                            @error('auto_save_amount')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Auto-Save Frequency') }}</flux:label>
                            <flux:select name="auto_save_frequency">
                                <option value="">{{ __('Select frequency') }}</option>
                                <option value="weekly" {{ old('auto_save_frequency') == 'weekly' ? 'selected' : '' }}>
                                    {{ __('Weekly') }}
                                </option>
                                <option value="monthly" {{ old('auto_save_frequency') == 'monthly' ? 'selected' : '' }}>
                                    {{ __('Monthly') }}
                                </option>
                            </flux:select>
                            @error('auto_save_frequency')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <flux:button variant="ghost" :href="route('goals.index')" wire:navigate>
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Create Goal') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app> 