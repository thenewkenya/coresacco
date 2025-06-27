<x-layouts.app>
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('System Settings') }}
                        </h1>
                        <p class="text-zinc-600 dark:text-zinc-400 mt-1">
                            {{ __('Configure your SACCO system preferences and settings') }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <flux:modal.trigger name="reset-confirmation">
                            <flux:button variant="outline" icon="arrow-path">
                                {{ __('Reset Settings') }}
                            </flux:button>
                        </flux:modal.trigger>
                        <flux:button variant="primary" type="submit" form="settings-form" icon="check">
                            {{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" />
                    <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5" />
                    <div>
                        <p class="text-red-800 dark:text-red-200 font-medium mb-2">Please correct the following errors:</p>
                        <ul class="text-red-700 dark:text-red-300 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2">
                <nav class="flex space-x-1 overflow-x-auto">
                    <a href="?tab=general" 
                       class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'general' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                        <flux:icon.cog class="w-4 h-4" />
                        <span>{{ __('General') }}</span>
                    </a>
                    <a href="?tab=financial" 
                       class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'financial' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                        <flux:icon.currency-dollar class="w-4 h-4" />
                        <span>{{ __('Financial') }}</span>
                    </a>
                    <a href="?tab=features" 
                       class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'features' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                        <flux:icon.puzzle-piece class="w-4 h-4" />
                        <span>{{ __('Features') }}</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <form method="POST" action="{{ route('system.settings.update') }}" id="settings-form">
            @csrf

            @if($activeTab === 'general')
                <!-- General Settings -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="mb-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <flux:icon.cog class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ __('General Settings') }}
                                </h2>
                                <p class="text-zinc-600 dark:text-zinc-400">
                                    {{ __('Basic organization information and system preferences') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($settings['general'] ?? [] as $key => $setting)
                        <div class="space-y-3">
                            <flux:field>
                                <flux:label for="general_{{ $key }}">{{ $setting['label'] }}</flux:label>
                                
                                @if($setting['type'] === 'string' && $key === 'default_currency')
                                    <flux:select name="general[{{ $key }}]" id="general_{{ $key }}">
                                        <option value="KES" {{ $setting['value'] === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                        <option value="USD" {{ $setting['value'] === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ $setting['value'] === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ $setting['value'] === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    </flux:select>
                                @elseif($setting['type'] === 'string' && $key === 'timezone')
                                    <flux:select name="general[{{ $key }}]" id="general_{{ $key }}">
                                        <option value="Africa/Nairobi" {{ $setting['value'] === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                                        <option value="Africa/Lagos" {{ $setting['value'] === 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
                                        <option value="Africa/Cairo" {{ $setting['value'] === 'Africa/Cairo' ? 'selected' : '' }}>Africa/Cairo</option>
                                        <option value="UTC" {{ $setting['value'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </flux:select>
                                @elseif($setting['type'] === 'string')
                                    <flux:input 
                                        type="text" 
                                        name="general[{{ $key }}]" 
                                        id="general_{{ $key }}"
                                        value="{{ old('general.'.$key, $setting['value']) }}" 
                                    />
                                @endif
                                
                                @if($setting['description'])
                                    <flux:description>{{ $setting['description'] }}</flux:description>
                                @endif
                                
                                @error('general.'.$key)
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        </div>
                        @endforeach
                    </div>
                </div>

            @elseif($activeTab === 'financial')
                <!-- Financial Settings -->
                <div class="space-y-6">
                    <!-- Interest Rates -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="mb-6">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <flux:icon.currency-dollar class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ __('Interest Rates') }}
                                    </h2>
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Configure interest rates for savings and loans') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['savings_interest_rate', 'loan_interest_rate', 'emergency_loan_rate', 'late_payment_penalty'] as $key)
                            @if(isset($settings['financial'][$key]))
                            @php $setting = $settings['financial'][$key]; @endphp
                            <div class="space-y-3">
                                <flux:field>
                                    <flux:label for="financial_{{ $key }}">{{ $setting['label'] }}</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01"
                                        name="financial[{{ $key }}]" 
                                        id="financial_{{ $key }}"
                                        value="{{ old('financial.'.$key, $setting['value']) }}" 
                                        suffix="%"
                                    />
                                    @if($setting['description'])
                                        <flux:description>{{ $setting['description'] }}</flux:description>
                                    @endif
                                    @error('financial.'.$key)
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- System Limits -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="mb-6">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <flux:icon.scale class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ __('System Limits') }}
                                    </h2>
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Set financial limits and constraints') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['maximum_loan_amount', 'minimum_savings_balance', 'daily_withdrawal_limit', 'loan_term_months'] as $key)
                            @if(isset($settings['financial'][$key]))
                            @php $setting = $settings['financial'][$key]; @endphp
                            <div class="space-y-3">
                                <flux:field>
                                    <flux:label for="financial_{{ $key }}">{{ $setting['label'] }}</flux:label>
                                    <flux:input 
                                        type="number" 
                                        name="financial[{{ $key }}]" 
                                        id="financial_{{ $key }}"
                                        value="{{ old('financial.'.$key, $setting['value']) }}" 
                                        @if(str_contains($key, 'amount') || str_contains($key, 'balance') || str_contains($key, 'limit'))
                                            prefix="KES"
                                        @endif
                                    />
                                    @if($setting['description'])
                                        <flux:description>{{ $setting['description'] }}</flux:description>
                                    @endif
                                    @error('financial.'.$key)
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'features')
                <!-- Feature Settings -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="mb-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                <flux:icon.puzzle-piece class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ __('Feature Settings') }}
                                </h2>
                                <p class="text-zinc-600 dark:text-zinc-400">
                                    {{ __('Enable or disable system features') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($settings['features'] ?? [] as $key => $setting)
                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex-1">
                                <flux:label for="features_{{ $key }}" class="!mb-0">{{ $setting['label'] }}</flux:label>
                                @if($setting['description'])
                                    <flux:description class="mt-1">{{ $setting['description'] }}</flux:description>
                                @endif
                            </div>
                            <div class="ml-4">
                                <flux:switch 
                                    name="features[{{ $key }}]" 
                                    id="features_{{ $key }}"
                                    value="1"
                                    {{ old('features.'.$key, $setting['value']) ? 'checked' : '' }}
                                />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<flux:modal name="reset-confirmation" class="max-w-lg">
    <div class="flex items-center space-x-3 mb-6">
        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
            <flux:icon.exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
        </div>
        <div>
            <flux:heading size="lg">{{ __('Reset Settings') }}</flux:heading>
            <flux:subheading>{{ __('Choose what to reset') }}</flux:subheading>
        </div>
    </div>

    <div class="space-y-4 mb-6">
        <p class="text-zinc-600 dark:text-zinc-400">
            {{ __('This action will reset the selected settings to their default values and cannot be undone.') }}
        </p>
        
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <flux:icon.information-circle class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-amber-900 dark:text-amber-100 mb-1">{{ __('Recommendation') }}</h4>
                    <p class="text-sm text-amber-800 dark:text-amber-200">{{ __('Consider resetting only the current section first before resetting all settings.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <form method="POST" action="{{ route('system.settings.reset') }}" class="flex-1">
            @csrf
            <input type="hidden" name="group" value="{{ $activeTab }}">
            <flux:button type="submit" variant="outline" class="w-full" icon="arrow-path">
                {{ __('Reset Current Section') }}
            </flux:button>
        </form>
        
        <form method="POST" action="{{ route('system.settings.reset') }}" class="flex-1">
            @csrf
            <input type="hidden" name="group" value="all">
            <flux:button type="submit" variant="danger" class="w-full" icon="arrow-path">
                {{ __('Reset All Settings') }}
            </flux:button>
        </form>
    </div>
</flux:modal>
</x-layouts.app>
