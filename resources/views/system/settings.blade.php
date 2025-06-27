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
                        <flux:button variant="outline" :href="route('system.settings.export')" icon="arrow-down">
                            {{ __('Export') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit" form="settings-form" icon="check">
                            {{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

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
                    <a href="?tab=import-export" 
                       class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap {{ $activeTab === 'import-export' ? 'bg-blue-600 text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                        <flux:icon.arrow-down class="w-4 h-4" />
                        <span>{{ __('Import/Export') }}</span>
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
                            <flux:label for="general_{{ $key }}">{{ $setting['label'] }}</flux:label>
                            
                            @if($setting['type'] === 'string' && $key === 'default_currency')
                                <flux:select name="general[{{ $key }}]" id="general_{{ $key }}">
                                    <option value="KES" {{ $setting['value'] === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                    <option value="USD" {{ $setting['value'] === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ $setting['value'] === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ $setting['value'] === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
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
                            @foreach(['max_loan_amount', 'min_savings_balance', 'daily_withdrawal_limit', 'loan_term_months'] as $key)
                            @if(isset($settings['financial'][$key]))
                            @php $setting = $settings['financial'][$key]; @endphp
                            <div class="space-y-3">
                                <flux:label for="financial_{{ $key }}">{{ $setting['label'] }}</flux:label>
                                <flux:input 
                                    type="number" 
                                    name="financial[{{ $key }}]" 
                                    id="financial_{{ $key }}"
                                    value="{{ old('financial.'.$key, $setting['value']) }}" 
                                />
                                @if($setting['description'])
                                    <flux:description>{{ $setting['description'] }}</flux:description>
                                @endif
                                @error('financial.'.$key)
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
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

            @elseif($activeTab === 'import-export')
                <!-- Import/Export Settings -->
                <div class="space-y-6">
                    <!-- Export Settings -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="mb-6">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <flux:icon.arrow-down class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ __('Export Settings') }}
                                    </h2>
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Download your current settings as a backup') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div>
                                <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Export Configuration') }}</h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('Download all system settings as a JSON file') }}</p>
                            </div>
                            <flux:button variant="outline" :href="route('system.settings.export')" icon="arrow-down">
                                {{ __('Download Settings') }}
                            </flux:button>
                        </div>
                    </div>

                    <!-- Import Settings -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="mb-6">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.arrow-up class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ __('Import Settings') }}
                                    </h2>
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Upload a settings file to restore configuration') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800 p-4 mb-6">
                            <div class="flex items-start space-x-3">
                                <flux:icon.exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-medium text-amber-900 dark:text-amber-100 mb-1">{{ __('Important Notice') }}</h4>
                                    <p class="text-sm text-amber-800 dark:text-amber-200">{{ __('Importing settings will overwrite your current configuration. Make sure to export your current settings first as a backup.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('system.settings.import') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-3">
                                <flux:label for="settings_file">{{ __('Settings File') }}</flux:label>
                                <flux:input 
                                    type="file" 
                                    name="settings_file" 
                                    id="settings_file" 
                                    accept=".json" 
                                />
                                <flux:description>{{ __('Select a JSON settings file exported from this system') }}</flux:description>
                                @error('settings_file')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary" icon="arrow-up">
                                    {{ __('Import Settings') }}
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </form>

        <!-- Reset Actions -->
        <div class="mt-6 flex flex-col sm:flex-row gap-4">
            <flux:button variant="danger" onclick="resetSettings('{{ $activeTab }}')" icon="arrow-path">
                {{ __('Reset This Section') }}
            </flux:button>
            <flux:button variant="danger" onclick="resetSettings('all')" icon="arrow-path">
                {{ __('Reset All Settings') }}
            </flux:button>
        </div>
    </div>
</div>

<script>
function resetSettings(group) {
    const message = group === 'all' 
        ? 'Are you sure you want to reset ALL settings to their default values? This action cannot be undone.'
        : `Are you sure you want to reset ${group} settings to their default values? This action cannot be undone.`;
        
    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("system.settings.reset") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const groupInput = document.createElement('input');
        groupInput.type = 'hidden';
        groupInput.name = 'group';
        groupInput.value = group;
        
        form.appendChild(csrfToken);
        form.appendChild(groupInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</x-layouts.app>
