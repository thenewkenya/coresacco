<x-layouts.app :title="$goal->title">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $goal->title }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \App\Models\Goal::getTypes()[$goal->type] }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="pencil" :href="route('goals.edit', $goal)" wire:navigate>
                            {{ __('Edit Goal') }}
                        </flux:button>
                        <flux:button variant="danger" icon="trash" 
                            x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-goal-modal')">
                            {{ __('Delete Goal') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Goal Progress -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('Goal Progress') }}</h2>
                    <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($goal->progress_percentage, 1) }}%
                    </span>
                </div>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5 mb-6">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $goal->progress_percentage }}%"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Current Amount') }}</h3>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            KES {{ number_format($goal->current_amount) }}
                        </p>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Target Amount') }}</h3>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            KES {{ number_format($goal->target_amount) }}
                        </p>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Remaining Amount') }}</h3>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            KES {{ number_format($goal->remaining_amount) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Goal Details -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Goal Details') }}</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Description') }}</h3>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $goal->description }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Target Date') }}</h3>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                            {{ $goal->target_date->format('F d, Y') }}
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">
                                ({{ $goal->days_remaining }} {{ __('days remaining') }})
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</h3>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $goal->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                {{ ucfirst($goal->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Auto-Save Settings -->
            @if($goal->auto_save_amount)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Auto-Save Settings') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Auto-Save Amount') }}</h3>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                            KES {{ number_format($goal->auto_save_amount) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Frequency') }}</h3>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                            {{ ucfirst($goal->auto_save_frequency) }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            @if($transactions->isNotEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Recent Transactions') }}</h2>
                <div class="space-y-4">
                    @foreach($transactions as $transaction)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $transaction->description }}
                            </p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->created_at->format('F d, Y H:i') }}
                            </p>
                        </div>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            KES {{ number_format($transaction->amount) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Goal Modal -->
    <flux:modal name="delete-goal-modal" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <flux:icon.trash class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="mt-3">
                    <flux:heading size="lg">{{ __('Delete Goal') }}</flux:heading>
                    <div class="mt-2">
                        <flux:subheading>
                            {{ __('Are you sure you want to delete this goal? This action cannot be undone.') }}
                        </flux:subheading>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Goal:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $goal->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Current Amount:') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->current_amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Target Amount:') }}</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($goal->target_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex items-start">
                    <flux:icon.exclamation-triangle class="h-5 w-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            {{ __('Warning') }}
                        </h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            {{ __('Deleting this goal will permanently remove all associated data and progress. This action cannot be undone.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="danger" class="flex-1" x-on:click="submitDeleteGoal()">
                    <flux:icon.trash class="w-4 h-4 mr-2" />
                    {{ __('Delete Goal') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <script>
        function submitDeleteGoal() {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('goals.destroy', $goal) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Close modal and submit
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'delete-goal-modal' }));
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-layouts.app> 