<x-layouts.app :title="__('Open New Account')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Open New Account') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Choose from our range of SACCO account types to meet your financial goals') }}
                        </p>
                    </div>
                    <flux:button href="{{ route('accounts.index') }}" variant="ghost" icon="arrow-left">
                        {{ __('Back to Accounts') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <form method="POST" action="{{ route('accounts.store') }}" id="accountForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Account Type Selection -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                                {{ __('Select Account Type') }}
                            </h2>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($accountTypes as $type)
                                @php
                                    $accountColors = [
                                        'savings' => 'emerald',
                                        'shares' => 'blue', 
                                        'deposits' => 'purple',
                                        'emergency_fund' => 'red',
                                        'holiday_savings' => 'yellow',
                                        'retirement' => 'indigo',
                                        'education' => 'cyan',
                                        'development' => 'orange',
                                        'welfare' => 'pink',
                                        'loan_guarantee' => 'slate',
                                        'investment' => 'amber'
                                    ];
                                    $accountIcons = [
                                        'savings' => 'banknotes',
                                        'shares' => 'building-library', 
                                        'deposits' => 'safe',
                                        'emergency_fund' => 'shield-check',
                                        'holiday_savings' => 'sun',
                                        'retirement' => 'home',
                                        'education' => 'academic-cap',
                                        'development' => 'building-office-2',
                                        'welfare' => 'heart',
                                        'loan_guarantee' => 'shield-exclamation',
                                        'investment' => 'chart-bar'
                                    ];
                                    $color = $accountColors[$type['value']] ?? 'zinc';
                                    $icon = $accountIcons[$type['value']] ?? 'banknotes';
                                @endphp
                                <label class="relative cursor-pointer group account-card" data-color="{{ $color }}">
                                    <input type="radio" name="account_type" value="{{ $type['value'] }}" 
                                           class="sr-only account-radio" required data-color="{{ $color }}" data-label="{{ $type['label'] }}">
                                    
                                    <!-- Card Container -->
                                    <div class="card-container relative overflow-hidden rounded-xl border-2 border-zinc-200 dark:border-zinc-700 
                                                bg-white dark:bg-zinc-800 transition-all duration-300 ease-in-out hover:shadow-md"
                                         style="--account-color: {{ $color }}">
                                        
                                        <!-- Selected State Overlay -->
                                        <div class="selected-overlay absolute inset-0 opacity-0 transition-opacity duration-300"></div>
                                        
                                        <!-- Selection Indicator -->
                                        <div class="selection-indicator absolute top-4 right-4 w-5 h-5 rounded-full border-2 border-zinc-300 dark:border-zinc-600
                                                    transition-all duration-300 ease-in-out">
                                            <div class="indicator-dot absolute inset-0.5 rounded-full bg-white opacity-0 transition-opacity duration-300"></div>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="relative p-6">
                                            <div class="flex items-start space-x-4">
                                                <!-- Icon -->
                                                <div class="account-icon flex-shrink-0 p-3 rounded-xl transition-all duration-300">
                                                    <flux:icon.{{ $icon }} class="w-6 h-6 transition-colors duration-300" />
                                                </div>
                                                
                                                <!-- Text Content -->
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="account-title text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-2 transition-colors duration-300">
                                                        {{ $type['label'] }}
                                                    </h3>
                                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-3">
                                                        {{ $type['description'] }}
                                                    </p>
                                                    
                                                    <!-- Features -->
                                                    <div class="space-y-2">
                                                        @if(isset($type['interest_rate']))
                                                        <div class="flex items-center text-sm">
                                                            <flux:icon.percent-badge class="w-4 h-4 mr-2 account-feature-icon" />
                                                            <span class="account-feature-text font-medium">
                                                                {{ $type['interest_rate'] }}% p.a. interest
                                                            </span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if(isset($type['minimum_balance']))
                                                        <div class="flex items-center text-sm">
                                                            <flux:icon.banknotes class="w-4 h-4 text-zinc-500 mr-2" />
                                                            <span class="text-zinc-600 dark:text-zinc-400">
                                                                Min. balance: KES {{ number_format($type['minimum_balance']) }}
                                                            </span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($type['value'] === 'investment')
                                                        <div class="flex items-center">
                                                            <flux:badge color="amber" size="sm" class="text-xs">
                                                                Admin Approval Required
                                                            </flux:badge>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Member Selection -->
                        @if(auth()->user()->hasRole('member'))
                        <!-- Member Account Holder (Auto-populated for regular members) -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mt-6">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                                {{ __('Account Holder') }}
                            </h2>
                            
                            <div class="flex items-center space-x-4 p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                        <flux:icon.user class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ auth()->user()->email }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                                        {{ __('Account will be opened in your name') }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <flux:badge color="green" size="sm">
                                        {{ __('You') }}
                                    </flux:badge>
                                </div>
                            </div>
                            
                            <!-- Hidden input for the member_id -->
                            <input type="hidden" name="member_id" value="{{ auth()->user()->id }}">
                        </div>
                        @else
                        <!-- Staff Member Selection -->
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mt-6">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                                {{ __('Account Holder') }}
                            </h2>
                            
                            <flux:field>
                                <flux:label>{{ __('Select Member') }}</flux:label>
                                <flux:select name="member_id" required id="memberSelect">
                                    <option value="">{{ __('Choose a member...') }}</option>
                                    @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-name="{{ $member->name }}" data-email="{{ $member->email }}">
                                        {{ $member->name }} ({{ $member->email }})
                                    </option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="member_id" />
                            </flux:field>

                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                            <flux:field class="mt-4">
                                <flux:label>{{ __('Initial Deposit (Optional)') }}</flux:label>
                                <flux:input 
                                    type="number" 
                                    name="initial_deposit" 
                                    step="0.01" 
                                    min="0"
                                    placeholder="0.00"
                                />
                                <flux:description>
                                    {{ __('Enter an initial deposit amount if provided') }}
                                </flux:description>
                                <flux:error name="initial_deposit" />
                            </flux:field>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Account Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Account Summary') }}
                            </h3>
                            
                            <div class="space-y-4" id="summaryContent">
                                <!-- Dynamic Summary Content -->
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mb-4">
                                        <flux:icon.credit-card class="w-8 h-8 text-zinc-400" />
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ __('Select an account type to see details') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:button type="submit" variant="primary" class="w-full" id="submitButton" disabled>
                                    {{ __('Open Account') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Color mappings for account types */
        :root {
            --emerald-400: #34d399; --emerald-500: #10b981; --emerald-600: #059669; --emerald-700: #047857;
            --blue-400: #60a5fa; --blue-500: #3b82f6; --blue-600: #2563eb; --blue-700: #1d4ed8;
            --purple-400: #a78bfa; --purple-500: #8b5cf6; --purple-600: #7c3aed; --purple-700: #6d28d9;
            --red-400: #f87171; --red-500: #ef4444; --red-600: #dc2626; --red-700: #b91c1c;
            --yellow-400: #facc15; --yellow-500: #eab308; --yellow-600: #ca8a04; --yellow-700: #a16207;
            --indigo-400: #818cf8; --indigo-500: #6366f1; --indigo-600: #4f46e5; --indigo-700: #4338ca;
            --cyan-400: #22d3ee; --cyan-500: #06b6d4; --cyan-600: #0891b2; --cyan-700: #0e7490;
            --orange-400: #fb923c; --orange-500: #f97316; --orange-600: #ea580c; --orange-700: #c2410c;
            --pink-400: #f472b6; --pink-500: #ec4899; --pink-600: #db2777; --pink-700: #be185d;
            --slate-400: #94a3b8; --slate-500: #64748b; --slate-600: #475569; --slate-700: #334155;
            --teal-400: #2dd4bf; --teal-500: #14b8a6; --teal-600: #0d9488; --teal-700: #0f766e;
            --amber-400: #fbbf24; --amber-500: #f59e0b; --amber-600: #d97706; --amber-700: #b45309;
        }

        /* Default icon colors - Light mode */
        .account-card[data-color="emerald"] .account-icon { background-color: rgb(209 250 229); color: var(--emerald-600); }
        .account-card[data-color="blue"] .account-icon { background-color: rgb(219 234 254); color: var(--blue-600); }
        .account-card[data-color="purple"] .account-icon { background-color: rgb(237 233 254); color: var(--purple-600); }
        .account-card[data-color="red"] .account-icon { background-color: rgb(254 226 226); color: var(--red-600); }
        .account-card[data-color="yellow"] .account-icon { background-color: rgb(254 249 195); color: var(--yellow-600); }
        .account-card[data-color="indigo"] .account-icon { background-color: rgb(224 231 255); color: var(--indigo-600); }
        .account-card[data-color="cyan"] .account-icon { background-color: rgb(207 250 254); color: var(--cyan-600); }
        .account-card[data-color="orange"] .account-icon { background-color: rgb(254 215 170); color: var(--orange-600); }
        .account-card[data-color="pink"] .account-icon { background-color: rgb(252 231 243); color: var(--pink-600); }
        .account-card[data-color="slate"] .account-icon { background-color: rgb(241 245 249); color: var(--slate-600); }
        .account-card[data-color="teal"] .account-icon { background-color: rgb(204 251 241); color: var(--teal-600); }
        .account-card[data-color="amber"] .account-icon { background-color: rgb(254 243 199); color: var(--amber-600); }

        /* Default icon colors - Dark mode */
        .dark .account-card[data-color="emerald"] .account-icon { background-color: rgb(6 78 59 / 0.4); color: var(--emerald-400); }
        .dark .account-card[data-color="blue"] .account-icon { background-color: rgb(30 58 138 / 0.4); color: var(--blue-400); }
        .dark .account-card[data-color="purple"] .account-icon { background-color: rgb(88 28 135 / 0.4); color: var(--purple-400); }
        .dark .account-card[data-color="red"] .account-icon { background-color: rgb(127 29 29 / 0.4); color: var(--red-400); }
        .dark .account-card[data-color="yellow"] .account-icon { background-color: rgb(133 77 14 / 0.4); color: var(--yellow-400); }
        .dark .account-card[data-color="indigo"] .account-icon { background-color: rgb(55 48 163 / 0.4); color: var(--indigo-400); }
        .dark .account-card[data-color="cyan"] .account-icon { background-color: rgb(21 94 117 / 0.4); color: var(--cyan-400); }
        .dark .account-card[data-color="orange"] .account-icon { background-color: rgb(154 52 18 / 0.4); color: var(--orange-400); }
        .dark .account-card[data-color="pink"] .account-icon { background-color: rgb(131 24 67 / 0.4); color: var(--pink-400); }
        .dark .account-card[data-color="slate"] .account-icon { background-color: rgb(51 65 85 / 0.4); color: var(--slate-400); }
        .dark .account-card[data-color="teal"] .account-icon { background-color: rgb(19 78 74 / 0.4); color: var(--teal-400); }
        .dark .account-card[data-color="amber"] .account-icon { background-color: rgb(146 64 14 / 0.4); color: var(--amber-400); }

        /* Hover states - border color changes */
        .account-card[data-color="emerald"]:hover .card-container { border-color: var(--emerald-400); }
        .account-card[data-color="blue"]:hover .card-container { border-color: var(--blue-400); }
        .account-card[data-color="purple"]:hover .card-container { border-color: var(--purple-400); }
        .account-card[data-color="red"]:hover .card-container { border-color: var(--red-400); }
        .account-card[data-color="yellow"]:hover .card-container { border-color: var(--yellow-400); }
        .account-card[data-color="indigo"]:hover .card-container { border-color: var(--indigo-400); }
        .account-card[data-color="cyan"]:hover .card-container { border-color: var(--cyan-400); }
        .account-card[data-color="orange"]:hover .card-container { border-color: var(--orange-400); }
        .account-card[data-color="pink"]:hover .card-container { border-color: var(--pink-400); }
        .account-card[data-color="slate"]:hover .card-container { border-color: var(--slate-400); }
        .account-card[data-color="teal"]:hover .card-container { border-color: var(--teal-400); }
        .account-card[data-color="amber"]:hover .card-container { border-color: var(--amber-400); }

        /* Selected states using :has() selector */
        .account-card:has(.account-radio:checked) .card-container { 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); 
        }
        .dark .account-card:has(.account-radio:checked) .card-container { 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2); 
        }
        .account-card:has(.account-radio:checked) .selected-overlay { opacity: 1; }
        .account-card:has(.account-radio:checked) .selection-indicator { opacity: 1; }
        .account-card:has(.account-radio:checked) .indicator-dot { opacity: 1; }
        .account-card:has(.account-radio:checked) .account-icon svg { color: white !important; }

        /* Emerald selected state */
        .account-card[data-color="emerald"]:has(.account-radio:checked) .card-container { 
            border-color: var(--emerald-500); 
            background-color: rgb(236 253 245 / 0.5); 
        }
        .dark .account-card[data-color="emerald"]:has(.account-radio:checked) .card-container { 
            background-color: rgb(6 78 59 / 0.2); 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .selected-overlay { 
            background-color: rgb(16 185 129 / 0.05); 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .selection-indicator { 
            border-color: var(--emerald-500); 
            background-color: var(--emerald-500); 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .account-icon { 
            background-color: var(--emerald-500) !important; 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .account-title { 
            color: var(--emerald-700); 
        }
        .dark .account-card[data-color="emerald"]:has(.account-radio:checked) .account-title { 
            color: var(--emerald-400); 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .account-feature-icon { 
            color: var(--emerald-500); 
        }
        .account-card[data-color="emerald"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--emerald-700); 
        }
        .dark .account-card[data-color="emerald"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--emerald-300); 
        }

        /* Blue selected state */
        .account-card[data-color="blue"]:has(.account-radio:checked) .card-container { 
            border-color: var(--blue-500); 
            background-color: rgb(239 246 255 / 0.5); 
        }
        .dark .account-card[data-color="blue"]:has(.account-radio:checked) .card-container { 
            background-color: rgb(30 58 138 / 0.2); 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .selected-overlay { 
            background-color: rgb(59 130 246 / 0.05); 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .selection-indicator { 
            border-color: var(--blue-500); 
            background-color: var(--blue-500); 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .account-icon { 
            background-color: var(--blue-500) !important; 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .account-title { 
            color: var(--blue-700); 
        }
        .dark .account-card[data-color="blue"]:has(.account-radio:checked) .account-title { 
            color: var(--blue-400); 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .account-feature-icon { 
            color: var(--blue-500); 
        }
        .account-card[data-color="blue"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--blue-700); 
        }
        .dark .account-card[data-color="blue"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--blue-300); 
        }

        /* Purple selected state */
        .account-card[data-color="purple"]:has(.account-radio:checked) .card-container { 
            border-color: var(--purple-500); 
            background-color: rgb(245 243 255 / 0.5); 
        }
        .dark .account-card[data-color="purple"]:has(.account-radio:checked) .card-container { 
            background-color: rgb(88 28 135 / 0.2); 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .selected-overlay { 
            background-color: rgb(139 92 246 / 0.05); 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .selection-indicator { 
            border-color: var(--purple-500); 
            background-color: var(--purple-500); 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .account-icon { 
            background-color: var(--purple-500) !important; 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .account-title { 
            color: var(--purple-700); 
        }
        .dark .account-card[data-color="purple"]:has(.account-radio:checked) .account-title { 
            color: var(--purple-400); 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .account-feature-icon { 
            color: var(--purple-500); 
        }
        .account-card[data-color="purple"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--purple-700); 
        }
        .dark .account-card[data-color="purple"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--purple-300); 
        }

        /* Red selected state */
        .account-card[data-color="red"]:has(.account-radio:checked) .card-container { 
            border-color: var(--red-500); 
            background-color: rgb(254 242 242 / 0.5); 
        }
        .dark .account-card[data-color="red"]:has(.account-radio:checked) .card-container { 
            background-color: rgb(127 29 29 / 0.2); 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .selected-overlay { 
            background-color: rgb(239 68 68 / 0.05); 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .selection-indicator { 
            border-color: var(--red-500); 
            background-color: var(--red-500); 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .account-icon { 
            background-color: var(--red-500) !important; 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .account-title { 
            color: var(--red-700); 
        }
        .dark .account-card[data-color="red"]:has(.account-radio:checked) .account-title { 
            color: var(--red-400); 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .account-feature-icon { 
            color: var(--red-500); 
        }
        .account-card[data-color="red"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--red-700); 
        }
        .dark .account-card[data-color="red"]:has(.account-radio:checked) .account-feature-text { 
            color: var(--red-300); 
        }

        /* Simplified selected states for other colors with dark mode support */
        .account-card[data-color="yellow"]:has(.account-radio:checked) .card-container { border-color: var(--yellow-500); background-color: rgb(254 252 232 / 0.5); }
        .dark .account-card[data-color="yellow"]:has(.account-radio:checked) .card-container { background-color: rgb(133 77 14 / 0.2); }
        .account-card[data-color="yellow"]:has(.account-radio:checked) .selection-indicator { border-color: var(--yellow-500); background-color: var(--yellow-500); }
        .account-card[data-color="yellow"]:has(.account-radio:checked) .account-icon { background-color: var(--yellow-500) !important; }
        .account-card[data-color="yellow"]:has(.account-radio:checked) .account-title { color: var(--yellow-700); }
        .dark .account-card[data-color="yellow"]:has(.account-radio:checked) .account-title { color: var(--yellow-400); }

        .account-card[data-color="indigo"]:has(.account-radio:checked) .card-container { border-color: var(--indigo-500); background-color: rgb(238 242 255 / 0.5); }
        .dark .account-card[data-color="indigo"]:has(.account-radio:checked) .card-container { background-color: rgb(55 48 163 / 0.2); }
        .account-card[data-color="indigo"]:has(.account-radio:checked) .selection-indicator { border-color: var(--indigo-500); background-color: var(--indigo-500); }
        .account-card[data-color="indigo"]:has(.account-radio:checked) .account-icon { background-color: var(--indigo-500) !important; }
        .account-card[data-color="indigo"]:has(.account-radio:checked) .account-title { color: var(--indigo-700); }
        .dark .account-card[data-color="indigo"]:has(.account-radio:checked) .account-title { color: var(--indigo-400); }

        .account-card[data-color="cyan"]:has(.account-radio:checked) .card-container { border-color: var(--cyan-500); background-color: rgb(236 254 255 / 0.5); }
        .dark .account-card[data-color="cyan"]:has(.account-radio:checked) .card-container { background-color: rgb(21 94 117 / 0.2); }
        .account-card[data-color="cyan"]:has(.account-radio:checked) .selection-indicator { border-color: var(--cyan-500); background-color: var(--cyan-500); }
        .account-card[data-color="cyan"]:has(.account-radio:checked) .account-icon { background-color: var(--cyan-500) !important; }
        .account-card[data-color="cyan"]:has(.account-radio:checked) .account-title { color: var(--cyan-700); }
        .dark .account-card[data-color="cyan"]:has(.account-radio:checked) .account-title { color: var(--cyan-400); }

        .account-card[data-color="orange"]:has(.account-radio:checked) .card-container { border-color: var(--orange-500); background-color: rgb(255 247 237 / 0.5); }
        .dark .account-card[data-color="orange"]:has(.account-radio:checked) .card-container { background-color: rgb(154 52 18 / 0.2); }
        .account-card[data-color="orange"]:has(.account-radio:checked) .selection-indicator { border-color: var(--orange-500); background-color: var(--orange-500); }
        .account-card[data-color="orange"]:has(.account-radio:checked) .account-icon { background-color: var(--orange-500) !important; }
        .account-card[data-color="orange"]:has(.account-radio:checked) .account-title { color: var(--orange-700); }
        .dark .account-card[data-color="orange"]:has(.account-radio:checked) .account-title { color: var(--orange-400); }

        .account-card[data-color="pink"]:has(.account-radio:checked) .card-container { border-color: var(--pink-500); background-color: rgb(253 244 255 / 0.5); }
        .dark .account-card[data-color="pink"]:has(.account-radio:checked) .card-container { background-color: rgb(131 24 67 / 0.2); }
        .account-card[data-color="pink"]:has(.account-radio:checked) .selection-indicator { border-color: var(--pink-500); background-color: var(--pink-500); }
        .account-card[data-color="pink"]:has(.account-radio:checked) .account-icon { background-color: var(--pink-500) !important; }
        .account-card[data-color="pink"]:has(.account-radio:checked) .account-title { color: var(--pink-700); }
        .dark .account-card[data-color="pink"]:has(.account-radio:checked) .account-title { color: var(--pink-400); }

        .account-card[data-color="slate"]:has(.account-radio:checked) .card-container { border-color: var(--slate-500); background-color: rgb(248 250 252 / 0.5); }
        .dark .account-card[data-color="slate"]:has(.account-radio:checked) .card-container { background-color: rgb(51 65 85 / 0.2); }
        .account-card[data-color="slate"]:has(.account-radio:checked) .selection-indicator { border-color: var(--slate-500); background-color: var(--slate-500); }
        .account-card[data-color="slate"]:has(.account-radio:checked) .account-icon { background-color: var(--slate-500) !important; }
        .account-card[data-color="slate"]:has(.account-radio:checked) .account-title { color: var(--slate-700); }
        .dark .account-card[data-color="slate"]:has(.account-radio:checked) .account-title { color: var(--slate-400); }

        .account-card[data-color="teal"]:has(.account-radio:checked) .card-container { border-color: var(--teal-500); background-color: rgb(240 253 250 / 0.5); }
        .dark .account-card[data-color="teal"]:has(.account-radio:checked) .card-container { background-color: rgb(19 78 74 / 0.2); }
        .account-card[data-color="teal"]:has(.account-radio:checked) .selection-indicator { border-color: var(--teal-500); background-color: var(--teal-500); }
        .account-card[data-color="teal"]:has(.account-radio:checked) .account-icon { background-color: var(--teal-500) !important; }
        .account-card[data-color="teal"]:has(.account-radio:checked) .account-title { color: var(--teal-700); }
        .dark .account-card[data-color="teal"]:has(.account-radio:checked) .account-title { color: var(--teal-400); }

        .account-card[data-color="amber"]:has(.account-radio:checked) .card-container { border-color: var(--amber-500); background-color: rgb(255 251 235 / 0.5); }
        .dark .account-card[data-color="amber"]:has(.account-radio:checked) .card-container { background-color: rgb(146 64 14 / 0.2); }
        .account-card[data-color="amber"]:has(.account-radio:checked) .selection-indicator { border-color: var(--amber-500); background-color: var(--amber-500); }
        .account-card[data-color="amber"]:has(.account-radio:checked) .account-icon { background-color: var(--amber-500) !important; }
        .account-card[data-color="amber"]:has(.account-radio:checked) .account-title { color: var(--amber-700); }
        .dark .account-card[data-color="amber"]:has(.account-radio:checked) .account-title { color: var(--amber-400); }

        /* Animation */
        .account-card-selected {
            animation: selectGlow 0.4s ease-in-out;
        }
        
        @keyframes selectGlow {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
            const memberSelect = document.getElementById('memberSelect');
            const summaryContent = document.getElementById('summaryContent');
            const submitButton = document.getElementById('submitButton');
            
            // Check if current user is a member (for auto-selection logic)
            const isMember = @json(auth()->user()->hasRole('member'));
            const currentUser = @json([
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email
            ]);
            
            let selectedAccountType = null;
            let selectedMember = isMember ? currentUser : null;

            // Account type selection handler
            accountTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    selectedAccountType = {
                        value: this.value,
                        color: this.dataset.color,
                        label: this.dataset.label
                    };
                    
                    // Add selection animation
                    const card = this.nextElementSibling;
                    card.classList.add('account-card-selected');
                    setTimeout(() => card.classList.remove('account-card-selected'), 400);
                    
                    updateSummary();
                    updateSubmitButton();
                });
            });

            // Member selection handler (only for staff)
            if (memberSelect) {
                memberSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    selectedMember = selectedOption.value ? {
                        id: selectedOption.value,
                        name: selectedOption.dataset.name,
                        email: selectedOption.dataset.email
                    } : null;
                    
                    updateSummary();
                    updateSubmitButton();
                });
            }

            function updateSummary() {
                if (!selectedAccountType) return;
                
                const color = selectedAccountType.color;
                const currentDate = new Date().toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                // Get member name - either from selection or current user
                const memberName = selectedMember ? selectedMember.name : 'Please select a member';
                const memberEmail = selectedMember ? selectedMember.email : '';
                
                summaryContent.innerHTML = `
                    <div class="space-y-4">
                        <!-- Account Type -->
                        <div class="p-4 bg-${color}-50 dark:bg-${color}-900/20 rounded-lg border border-${color}-200 dark:border-${color}-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-${color}-500 rounded-lg flex items-center justify-center">
                                    <flux:icon.check class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-${color}-900 dark:text-${color}-100">${selectedAccountType.label}</h4>
                                    <p class="text-xs text-${color}-700 dark:text-${color}-300">Selected account type</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Member Info -->
                        <div>
                            <dt class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Account Holder') }}</dt>
                            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">${memberName}</dd>
                            ${memberEmail ? `<dd class="text-xs text-zinc-500 dark:text-zinc-500">${memberEmail}</dd>` : ''}
                            ${isMember ? '<dd class="text-xs text-green-600 dark:text-green-400 mt-1">{{ __("This account will be opened in your name") }}</dd>' : ''}
                        </div>
                        
                        <!-- Opening Date -->
                        <div>
                            <dt class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Opening Date') }}</dt>
                            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">${currentDate}</dd>
                        </div>

                        <!-- Status -->
                        <div>
                            <dt class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Initial Status') }}</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Active
                                </span>
                            </dd>
                        </div>
                    </div>
                `;
            }

            function updateSubmitButton() {
                // For members, only need account type selected since member is auto-selected
                // For staff, need both account type and member selected
                const canSubmit = selectedAccountType && (isMember || selectedMember);
                
                submitButton.disabled = !canSubmit;
                if (canSubmit) {
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitButton.classList.add('hover:shadow-lg', 'transition-all', 'duration-200');
                } else {
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    submitButton.classList.remove('hover:shadow-lg', 'transition-all', 'duration-200');
                }
            }

            // Initialize the submit button state
            updateSubmitButton();
        });
    </script>
</x-layouts.app> 