<?php

use App\Models\Goal;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $title = '';
    public $description = '';
    public $target_amount = '';
    public $target_date = '';
    public $type = Goal::TYPE_CUSTOM;
    public $auto_save_amount = '';
    public $auto_save_frequency = '';
    public $enable_auto_save = false;
    public $smart_recommendations = true;
    public $enable_notifications = true;

    public $goalTypes = [
        Goal::TYPE_EMERGENCY_FUND => 'Emergency Fund',
        Goal::TYPE_HOME_PURCHASE => 'Home Purchase',
        Goal::TYPE_EDUCATION => 'Education',
        Goal::TYPE_RETIREMENT => 'Retirement',
        Goal::TYPE_CUSTOM => 'Custom Goal',
    ];

    public $frequencies = [
        Goal::FREQUENCY_WEEKLY => 'Weekly',
        Goal::FREQUENCY_MONTHLY => 'Monthly',
    ];

    // Computed properties for intelligent recommendations
    public function mount()
    {
        $this->target_date = now()->addYear()->format('Y-m-d');
        $this->generateSmartDefaults();
    }

    public function generateSmartDefaults()
    {
        $user = auth()->user();
        
        // Get user's average monthly transactions for intelligent defaults
        $monthlyAverage = $user->transactions()
            ->where('type', 'deposit')
            ->where('created_at', '>=', now()->subMonths(6))
            ->avg('amount') ?? 5000;
            
        // Set intelligent defaults based on user behavior
        $this->auto_save_amount = round($monthlyAverage * 0.1, -2); // 10% of average deposit
    }

    public function updatedType()
    {
        // Set default title and intelligent targets based on goal type
        $defaults = [
            Goal::TYPE_EMERGENCY_FUND => [
                'title' => 'Emergency Fund',
                'description' => 'Build a safety net to cover 3-6 months of expenses',
                'target_amount' => auth()->user()->transactions()
                    ->where('type', 'withdrawal')
                    ->where('created_at', '>=', now()->subMonths(3))
                    ->avg('amount') * 6 ?? 150000, // 6 months of average expenses
                'months' => 12
            ],
            Goal::TYPE_HOME_PURCHASE => [
                'title' => 'Down Payment for Home',
                'description' => 'Save for a home down payment (typically 20% of home value)',
                'target_amount' => 2000000, // Default down payment amount
                'months' => 36
            ],
            Goal::TYPE_EDUCATION => [
                'title' => 'Education Fund',
                'description' => 'Save for education expenses and school fees',
                'target_amount' => 500000,
                'months' => 24
            ],
            Goal::TYPE_RETIREMENT => [
                'title' => 'Retirement Savings',
                'description' => 'Build wealth for your retirement years',
                'target_amount' => 5000000,
                'months' => 240 // 20 years
            ],
        ];

        if (isset($defaults[$this->type])) {
            $default = $defaults[$this->type];
            $this->title = $default['title'];
            $this->description = $default['description'];
            $this->target_amount = $default['target_amount'];
            $this->target_date = now()->addMonths($default['months'])->format('Y-m-d');
            
            // Enable auto-save by default for structured goals
            if ($this->type !== Goal::TYPE_CUSTOM) {
                $this->enable_auto_save = true;
                $this->calculateOptimalAutoSave();
            }
        } else {
            $this->enable_auto_save = false;
        }
    }

    public function updatedTargetAmount()
    {
        if ($this->enable_auto_save) {
            $this->calculateOptimalAutoSave();
        }
    }

    public function updatedTargetDate()
    {
        if ($this->enable_auto_save) {
            $this->calculateOptimalAutoSave();
        }
    }

    public function updatedEnableAutoSave()
    {
        if ($this->enable_auto_save) {
            $this->calculateOptimalAutoSave();
            $this->auto_save_frequency = Goal::FREQUENCY_MONTHLY;
        } else {
            $this->auto_save_amount = '';
            $this->auto_save_frequency = '';
        }
    }

    public function calculateOptimalAutoSave()
    {
        if (!$this->target_amount || !$this->target_date) return;
        
        $months = $this->calculateMonthsToTarget();
        if ($months <= 0) return;
        
        // Calculate required monthly savings
        $requiredMonthly = $this->target_amount / $months;
        
        // Get user's financial capacity
        $user = auth()->user();
        $avgMonthlyIncome = $user->transactions()
            ->where('type', 'deposit')
            ->where('created_at', '>=', now()->subMonths(6))
            ->avg('amount') ?? 10000;
            
        $avgMonthlyExpenses = $user->transactions()
            ->where('type', 'withdrawal')
            ->where('created_at', '>=', now()->subMonths(6))
            ->avg('amount') ?? 7000;
            
        $disposableIncome = $avgMonthlyIncome - $avgMonthlyExpenses;
        
        // Suggest auto-save amount (max 30% of disposable income or required amount, whichever is lower)
        $maxRecommended = $disposableIncome * 0.3;
        $recommended = min($requiredMonthly, $maxRecommended);
        
        // Round to nearest 100
        $this->auto_save_amount = round(max(500, $recommended), -2);
    }

    public function calculateMonthsToTarget()
    {
        if (!$this->target_date) return 0;
        
        $targetDate = \Carbon\Carbon::parse($this->target_date);
        return max(1, now()->diffInMonths($targetDate));
    }

    public function calculateRequiredSavings()
    {
        if (!$this->target_amount || !$this->target_date) return 0;
        
        $months = $this->calculateMonthsToTarget();
        return $months > 0 ? $this->target_amount / $months : 0;
    }

    public function getRecommendationMessage()
    {
        if (!$this->target_amount || !$this->target_date) return '';
        
        $requiredMonthly = $this->calculateRequiredSavings();
        $user = auth()->user();
        
        $avgIncome = $user->transactions()
            ->where('type', 'deposit')
            ->where('created_at', '>=', now()->subMonths(3))
            ->avg('amount') ?? 0;
            
        if ($requiredMonthly > $avgIncome * 0.5) {
            return '⚠️ This goal requires ' . number_format(($requiredMonthly / $avgIncome) * 100, 1) . '% of your average income. Consider extending the timeline.';
        } elseif ($requiredMonthly > $avgIncome * 0.3) {
            return '💡 This is an ambitious goal requiring ' . number_format(($requiredMonthly / $avgIncome) * 100, 1) . '% of your income. You can do it!';
        } else {
            return '✅ This goal looks achievable! You\'ll need to save ' . number_format(($requiredMonthly / $avgIncome) * 100, 1) . '% of your income.';
        }
    }

    public function getGoalTypeAdvice()
    {
        $advice = [
            Goal::TYPE_EMERGENCY_FUND => [
                'icon' => '🛡️',
                'text' => 'Emergency funds should cover 3-6 months of expenses. Start with 1 month and build up.',
                'tips' => ['Keep in a separate high-yield savings account', 'Only use for true emergencies', 'Replenish immediately after use']
            ],
            Goal::TYPE_HOME_PURCHASE => [
                'icon' => '🏠',
                'text' => 'Save 20% down payment to avoid PMI. Don\'t forget closing costs and moving expenses.',
                'tips' => ['Research local home prices', 'Factor in maintenance costs', 'Consider location appreciation']
            ],
            Goal::TYPE_EDUCATION => [
                'icon' => '🎓',
                'text' => 'Education is an investment in your future. Consider both tuition and living expenses.',
                'tips' => ['Look into scholarships and grants', 'Consider cost vs. ROI', 'Start early for compound growth']
            ],
            Goal::TYPE_RETIREMENT => [
                'icon' => '🌅',
                'text' => 'Start early to harness compound interest. Aim to replace 70-80% of your current income.',
                'tips' => ['Maximize employer matching', 'Diversify investments', 'Increase contributions with raises']
            ],
            Goal::TYPE_CUSTOM => [
                'icon' => '🎯',
                'text' => 'Custom goals give you flexibility. Be specific about what you want to achieve.',
                'tips' => ['Set SMART goals', 'Break into milestones', 'Track progress regularly']
            ]
        ];
        
        return $advice[$this->type] ?? $advice[Goal::TYPE_CUSTOM];
    }

    public function createGoal()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:1000',
            'target_date' => 'required|date|after:today',
            'type' => 'required|in:' . implode(',', array_keys($this->goalTypes)),
            'auto_save_amount' => 'nullable|numeric|min:0',
            'auto_save_frequency' => 'nullable|in:' . implode(',', array_keys($this->frequencies)),
        ]);

        // Validate auto-save settings
        if ($this->enable_auto_save) {
            if (!$this->auto_save_amount || !$this->auto_save_frequency) {
                $this->addError('auto_save', 'Auto-save amount and frequency are required when auto-save is enabled.');
                return;
            }
        }

        try {
            Goal::create([
                'member_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'target_amount' => $this->target_amount,
                'target_date' => $this->target_date,
                'type' => $this->type,
                'auto_save_amount' => $this->enable_auto_save ? $this->auto_save_amount : null,
                'auto_save_frequency' => $this->enable_auto_save ? $this->auto_save_frequency : null,
                'status' => Goal::STATUS_ACTIVE,
                'metadata' => [
                    'notifications_enabled' => $this->enable_notifications,
                    'smart_recommendations' => $this->smart_recommendations,
                    'created_via' => 'web_form',
                    'initial_auto_save_setup' => $this->enable_auto_save,
                ]
            ]);

            session()->flash('success', 'Goal created successfully! ' . ($this->enable_auto_save ? 'Auto-save has been enabled.' : ''));
            $this->redirect(route('goals.index'), navigate: true);

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to create goal. Please try again.');
        }
    }
}; ?>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ __('Create Financial Goal') }}
                    </h1>
                    <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                        {{ __('Set and track your financial objectives with intelligent auto-saving') }}
                    </p>
                </div>
                <flux:button variant="ghost" :href="route('goals.index')" wire:navigate>
                    {{ __('Back') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-3 sm:p-4 md:p-6 max-w-4xl mx-auto space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700">
                <div class="flex items-start">
                    <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" />
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <form wire:submit="createGoal" class="space-y-6">
            
            <!-- Goal Type Selection -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Goal Type') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    @foreach($goalTypes as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model.live="type" value="{{ $key }}" class="sr-only peer">
                            <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white dark:bg-zinc-800">
                                <div class="flex items-center space-x-3">
                                    <div class="text-2xl">
                                        @if($key === \App\Models\Goal::TYPE_EMERGENCY_FUND)
                                            🛡️
                                        @elseif($key === \App\Models\Goal::TYPE_HOME_PURCHASE)
                                            🏠
                                        @elseif($key === \App\Models\Goal::TYPE_EDUCATION)
                                            🎓
                                        @elseif($key === \App\Models\Goal::TYPE_RETIREMENT)
                                            🌅
                                        @else
                                            🎯
                                        @endif
                                    </div>
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $label }}</h4>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Goal Type Advice -->
                @if($type)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700">
                        <div class="flex items-start space-x-3">
                            <div class="text-2xl">{{ $this->getGoalTypeAdvice()['icon'] }}</div>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                                    {{ $this->getGoalTypeAdvice()['text'] }}
                                </p>
                                <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                                    @foreach($this->getGoalTypeAdvice()['tips'] as $tip)
                                        <li>• {{ $tip }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Goal Details -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Goal Details') }}
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Goal Title') }}</flux:label>
                            <flux:input 
                                wire:model="title"
                                type="text"
                                required
                                placeholder="{{ __('e.g. Emergency Fund') }}" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Target Amount (KES)') }}</flux:label>
                            <flux:input 
                                wire:model.live="target_amount"
                                type="number"
                                required
                                min="1000"
                                step="1000"
                                placeholder="0" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Target Date') }}</flux:label>
                            <flux:input 
                                wire:model.live="target_date"
                                type="date"
                                required
                                min="{{ now()->addDay()->format('Y-m-d') }}" />
                        </flux:field>
                    </div>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Description') }}</flux:label>
                            <flux:textarea 
                                wire:model="description"
                                rows="4"
                                placeholder="{{ __('Describe your goal and why it\'s important to you...') }}"></flux:textarea>
                        </flux:field>

                        <!-- Goal Summary Card -->
                        @if($target_amount && $target_date)
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">Goal Summary</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Target Amount:</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($target_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Time Frame:</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $this->calculateMonthsToTarget() }} months</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Required Monthly:</span>
                                        <span class="font-medium text-blue-600 dark:text-blue-400">KES {{ number_format($this->calculateRequiredSavings()) }}</span>
                                    </div>
                                </div>
                                
                                @if($smart_recommendations && $this->getRecommendationMessage())
                                    <div class="mt-3 p-3 bg-white dark:bg-zinc-600 rounded-lg border border-zinc-200 dark:border-zinc-500 text-xs text-zinc-700 dark:text-zinc-300">
                                        {{ $this->getRecommendationMessage() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Auto-Save Settings -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Auto-Save Settings') }}
                        </h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Automate your savings with smart recommendations') }}
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="enable_auto_save" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/50 dark:peer-focus:ring-blue-800/50 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                @if($enable_auto_save)
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700">
                            <div class="flex items-start space-x-3">
                                <div class="text-green-600 dark:text-green-400">✨</div>
                                <div>
                                    <p class="text-sm text-green-800 dark:text-green-200 font-medium">Auto-Save Enabled</p>
                                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                        We'll automatically calculate optimal saving amounts based on your goal timeline and financial capacity.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>{{ __('Auto-Save Amount (KES)') }}</flux:label>
                                <flux:input 
                                    wire:model="auto_save_amount"
                                    type="number"
                                    min="500"
                                    step="100"
                                    placeholder="0" />
                                <flux:description>
                                    {{ __('Recommended based on your goal timeline and financial history') }}
                                </flux:description>
                                @error('auto_save')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Auto-Save Frequency') }}</flux:label>
                                <flux:select wire:model="auto_save_frequency">
                                    <option value="">{{ __('Select frequency') }}</option>
                                    @foreach($frequencies as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        <!-- Auto-Save Preview -->
                        @if($auto_save_amount && $auto_save_frequency)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700">
                                <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Auto-Save Preview</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-blue-700 dark:text-blue-300">{{ ucfirst($auto_save_frequency) }} Contribution:</span>
                                        <div class="font-medium text-blue-900 dark:text-blue-100">KES {{ number_format($auto_save_amount) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-blue-700 dark:text-blue-300">Goal Achievement:</span>
                                        <div class="font-medium text-blue-900 dark:text-blue-100">
                                            @if($target_amount && $auto_save_amount)
                                                @php
                                                    $monthlyAutoSave = $auto_save_frequency === 'weekly' ? $auto_save_amount * 4 : $auto_save_amount;
                                                    $monthsToComplete = $monthlyAutoSave > 0 ? ceil($target_amount / $monthlyAutoSave) : 0;
                                                @endphp
                                                {{ $monthsToComplete }} months
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($target_amount && $auto_save_amount)
                                    @php
                                        $monthlyAutoSave = $auto_save_frequency === 'weekly' ? $auto_save_amount * 4 : $auto_save_amount;
                                        $requiredMonthly = $this->calculateRequiredSavings();
                                    @endphp
                                    
                                    <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-700">
                                        @if($monthlyAutoSave >= $requiredMonthly)
                                            <div class="flex items-center text-xs text-green-700 dark:text-green-300">
                                                <flux:icon.check-circle class="h-3 w-3 mr-1" />
                                                You're on track to reach your goal on time!
                                            </div>
                                        @else
                                            <div class="flex items-center text-xs text-orange-700 dark:text-orange-300">
                                                <flux:icon.exclamation-triangle class="h-3 w-3 mr-1" />
                                                You'll need an additional KES {{ number_format($requiredMonthly - $monthlyAutoSave) }} monthly
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">⚡</div>
                        <h4 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">Enable Auto-Save</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto">
                            Enable auto-save to automatically contribute towards your goal on a regular schedule. Our intelligent system will recommend optimal amounts based on your financial capacity.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Additional Settings -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Additional Settings') }}
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                        <div>
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">Smart Recommendations</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Get intelligent suggestions to optimize your savings</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="smart_recommendations" class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/50 dark:peer-focus:ring-blue-800/50 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-xl border border-zinc-200 dark:border-zinc-600">
                        <div>
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">Goal Notifications</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Receive reminders and progress updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="enable_notifications" class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/50 dark:peer-focus:ring-blue-800/50 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            @error('general')
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-700">
                    <div class="flex items-center">
                        <flux:icon.exclamation-circle class="h-5 w-5 text-red-600 dark:text-red-400 mr-3" />
                        <p class="text-sm text-red-800 dark:text-red-200 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <flux:button variant="ghost" :href="route('goals.index')" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    <flux:icon.sparkles class="w-4 h-4 mr-2" />
                    {{ __('Create Goal') }}
                </flux:button>
            </div>
        </form>
    </div>
</div> 