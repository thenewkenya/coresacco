<?php

use App\Models\Goal;
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

    public function mount()
    {
        $this->target_date = now()->addYear()->format('Y-m-d');
    }

    public function updatedType()
    {
        // Set default title based on goal type
        $defaultTitles = [
            Goal::TYPE_EMERGENCY_FUND => 'Emergency Fund',
            Goal::TYPE_HOME_PURCHASE => 'Down Payment for Home',
            Goal::TYPE_EDUCATION => 'Education Fund',
            Goal::TYPE_RETIREMENT => 'Retirement Savings',
        ];

        if (isset($defaultTitles[$this->type])) {
            $this->title = $defaultTitles[$this->type];
        }
    }

    public function calculateMonthsToTarget()
    {
        if (!$this->target_date) return 0;
        
        $targetDate = \Carbon\Carbon::parse($this->target_date);
        return now()->diffInMonths($targetDate);
    }

    public function calculateRequiredSavings()
    {
        if (!$this->target_amount || !$this->target_date) return 0;
        
        $months = $this->calculateMonthsToTarget();
        return $months > 0 ? $this->target_amount / $months : 0;
    }

    public function createGoal()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:100',
            'target_date' => 'required|date|after:today',
            'type' => 'required|in:' . implode(',', array_keys($this->goalTypes)),
            'auto_save_amount' => 'nullable|numeric|min:0',
            'auto_save_frequency' => 'nullable|in:' . implode(',', array_keys($this->frequencies)),
        ]);

        try {
            Goal::create([
                'member_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'target_amount' => $this->target_amount,
                'target_date' => $this->target_date,
                'type' => $this->type,
                'auto_save_amount' => $this->auto_save_amount,
                'auto_save_frequency' => $this->auto_save_frequency,
                'status' => Goal::STATUS_ACTIVE,
            ]);

            session()->flash('success', 'Goal created successfully!');
            $this->redirect(route('goals.index'), navigate: true);

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to create goal. Please try again.');
        }
    }


}; ?>

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
                        {{ __('Set and track your financial objectives') }}
                    </p>
                </div>
                <flux:button variant="ghost" :href="route('goals.index')" wire:navigate>
                    {{ __('Back') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto">
            <form wire:submit="createGoal" class="space-y-6">
                
                <!-- Goal Type -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:field>
                        <flux:label>{{ __('Goal Type') }}</flux:label>
                        <flux:select wire:model.live="type" required>
                            @foreach($goalTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>
                            {{ __('Choose the type of financial goal you want to achieve') }}
                        </flux:description>
                    </flux:field>
                </div>

                <!-- Goal Details -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Goal Details') }}
                    </h3>

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
                            <flux:label>{{ __('Description') }}</flux:label>
                            <flux:textarea 
                                wire:model="description"
                                rows="3"
                                placeholder="{{ __('Describe your goal and why it\'s important to you...') }}"></flux:textarea>
                        </flux:field>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>{{ __('Target Amount (KES)') }}</flux:label>
                                <flux:input 
                                    wire:model.live="target_amount"
                                    type="number"
                                    required
                                    min="100"
                                    step="100"
                                    placeholder="0.00" />
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
                    </div>
                </div>

                <!-- Auto-Save Settings -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Auto-Save Settings') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Auto-Save Amount (KES)') }}</flux:label>
                            <flux:input 
                                wire:model="auto_save_amount"
                                type="number"
                                min="0"
                                step="100"
                                placeholder="0.00" />
                            <flux:description>
                                {{ __('Optional: Automatically save this amount towards your goal') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Auto-Save Frequency') }}</flux:label>
                            <flux:select wire:model="auto_save_frequency">
                                <option value="">{{ __('None') }}</option>
                                @foreach($frequencies as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <!-- Goal Summary -->
                @if($target_amount && $target_date)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                            {{ __('Goal Summary') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">Target Amount:</span>
                                <div class="font-semibold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($target_amount) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">Time Frame:</span>
                                <div class="font-semibold text-blue-900 dark:text-blue-100">
                                    {{ $this->calculateMonthsToTarget() }} months
                                </div>
                            </div>
                            <div>
                                <span class="text-blue-600 dark:text-blue-400">Required Monthly Savings:</span>
                                <div class="font-semibold text-blue-900 dark:text-blue-100">
                                    KES {{ number_format($this->calculateRequiredSavings()) }}
                                </div>
                            </div>
                        </div>

                        @if($auto_save_amount && $auto_save_frequency)
                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-sm text-green-800 dark:text-green-200">
                                    <strong>Auto-Save Plan:</strong> 
                                    KES {{ number_format($auto_save_amount) }} {{ strtolower($auto_save_frequency) }}
                                    
                                    @php
                                        $monthlyAutoSave = $auto_save_frequency === 'weekly' ? $auto_save_amount * 4.33 : $auto_save_amount;
                                        $requiredMonthly = $this->calculateRequiredSavings();
                                    @endphp
                                    
                                    @if($monthlyAutoSave >= $requiredMonthly)
                                        <span class="text-green-600 dark:text-green-400">✓ On track to reach your goal!</span>
                                    @else
                                        <span class="text-orange-600 dark:text-orange-400">
                                            ⚠ You may need to save an additional KES {{ number_format($requiredMonthly - $monthlyAutoSave) }} monthly
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

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