<x-layouts.app :title="__('Edit Branch') . ' - ' . $branch->name">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center space-x-4">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('branches.show', $branch)" wire:navigate>
                        {{ __('Back to Branch') }}
                    </flux:button>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Edit Branch') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $branch->name }} • {{ __('Update branch information and settings') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <form action="{{ route('branches.update', $branch) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Basic Information') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>{{ __('Branch Name') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input name="name" value="{{ old('name', $branch->name) }}" placeholder="{{ __('e.g., Nairobi Main Branch') }}" required />
                                <flux:error name="name" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Branch Code') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input name="code" value="{{ old('code', $branch->code) }}" placeholder="{{ __('e.g., NRB-001') }}" required />
                                <flux:error name="code" />
                                <flux:description>{{ __('Unique identifier for the branch') }}</flux:description>
                            </flux:field>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <flux:field>
                                <flux:label>{{ __('City') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input name="city" value="{{ old('city', $branch->city) }}" placeholder="{{ __('e.g., Nairobi') }}" required />
                                <flux:error name="city" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Opening Date') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input type="date" name="opening_date" value="{{ old('opening_date', $branch->opening_date?->format('Y-m-d')) }}" required />
                                <flux:error name="opening_date" />
                            </flux:field>
                        </div>

                        <flux:field class="mt-6">
                            <flux:label>{{ __('Address') }} <span class="text-red-500">*</span></flux:label>
                            <flux:textarea name="address" placeholder="{{ __('Enter complete branch address') }}" required>{{ old('address', $branch->address) }}</flux:textarea>
                            <flux:error name="address" />
                        </flux:field>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Contact Information') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>{{ __('Phone Number') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="{{ __('e.g., +254 712 345 678') }}" required />
                                <flux:error name="phone" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Email Address') }} <span class="text-red-500">*</span></flux:label>
                                <flux:input type="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="{{ __('e.g., nairobi@sacco.com') }}" required />
                                <flux:error name="email" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- Management & Status -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Management & Status') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>{{ __('Branch Manager') }}</flux:label>
                                <flux:select name="manager_id">
                                    <option value="">{{ __('Select manager (optional)') }}</option>
                                    @foreach($availableManagers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id', $branch->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="manager_id" />
                                <flux:description>{{ __('Choose from available managers') }}</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Branch Status') }} <span class="text-red-500">*</span></flux:label>
                                <flux:select name="status" required>
                                    <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    <option value="under_maintenance" {{ old('status', $branch->status) === 'under_maintenance' ? 'selected' : '' }}>{{ __('Under Maintenance') }}</option>
                                </flux:select>
                                <flux:error name="status" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Working Hours') }}</h3>
                        
                        <div class="space-y-4">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                @php
                                    $hours = $branch->working_hours[$day] ?? ['open' => '08:00', 'close' => '17:00'];
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                    <div class="flex items-center">
                                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 capitalize">{{ $day }}</label>
                                    </div>
                                    <div>
                                        <flux:input 
                                            type="time" 
                                            name="working_hours[{{ $day }}][open]" 
                                            value="{{ old("working_hours.{$day}.open", $hours['open'] ?? '08:00') }}" 
                                            placeholder="{{ __('Opening time') }}"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <flux:input 
                                            type="time" 
                                            name="working_hours[{{ $day }}][close]" 
                                            value="{{ old("working_hours.{$day}.close", $hours['close'] ?? '17:00') }}" 
                                            placeholder="{{ __('Closing time') }}"
                                            required
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <flux:description class="mt-4">{{ __('Set working hours for each day of the week. Use 24-hour format.') }}</flux:description>
                    </div>

                    <!-- Location (Optional) -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Location Coordinates (Optional)') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>{{ __('Latitude') }}</flux:label>
                                <flux:input 
                                    type="number" 
                                    step="any" 
                                    name="coordinates[latitude]" 
                                    value="{{ old('coordinates.latitude', $branch->coordinates['latitude'] ?? '') }}" 
                                    placeholder="{{ __('e.g., -1.2921') }}"
                                />
                                <flux:error name="coordinates.latitude" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Longitude') }}</flux:label>
                                <flux:input 
                                    type="number" 
                                    step="any" 
                                    name="coordinates[longitude]" 
                                    value="{{ old('coordinates.longitude', $branch->coordinates['longitude'] ?? '') }}" 
                                    placeholder="{{ __('e.g., 36.8219') }}"
                                />
                                <flux:error name="coordinates.longitude" />
                            </flux:field>
                        </div>
                        <flux:description class="mt-4">{{ __('GPS coordinates for map integration and location services') }}</flux:description>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <flux:button variant="outline" :href="route('branches.show', $branch)" wire:navigate>
                                    {{ __('Cancel') }}
                                </flux:button>
                                @if(auth()->user()->hasRole('admin'))
                                    <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button 
                                            type="submit" 
                                            variant="danger"
                                            onclick="return confirm('{{ __('Are you sure you want to delete this branch? This action cannot be undone.') }}')"
                                        >
                                            {{ __('Delete Branch') }}
                                        </flux:button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <flux:button type="submit" variant="primary">
                                    {{ __('Update Branch') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app> 