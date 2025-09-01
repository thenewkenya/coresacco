<x-layouts.app :title="__('My Loans')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Loans') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Track your loan applications and repayment progress') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="primary" icon="plus" :href="route('loans.apply')" wire:navigate>
                            {{ __('Apply for Loan') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Loan Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">{{ __('Total') }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Borrowed') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh {{ number_format($totalBorrowed) }}</p>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $loans->count() }} {{ __('loan(s)') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.calendar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">{{ __('Repaid') }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Repaid') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh {{ number_format($totalRepaid) }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('All time') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ __('Status') }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Can Apply') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            @if($canApplyForLoan)
                                <span class="text-emerald-600">{{ __('Yes') }}</span>
                            @else
                                <span class="text-red-600">{{ __('No') }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">
                            @if($canApplyForLoan)
                                {{ __('Eligible for new loan') }}
                            @else
                                {{ __('Active loan exists') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Active Loans -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('My Loans') }}
                    </h3>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($loans as $loan)
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $loan->loanType->name ?? 'Loan' }} #{{ $loan->id }}
                                </h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $loan->purpose ?? 'No purpose specified' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($loan->amount) }}
                                </p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ ucfirst($loan->status) }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Interest Rate') }}</p>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $loan->interest_rate }}%
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $loan->term_period }} months
                                </p>
                            </div>

                            <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Applied Date') }}</p>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $loan->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $loan->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end space-x-2">
                                <flux:button variant="outline" size="sm" :href="route('loans.show', $loan->id)">
                                    {{ __('View Details') }}
                                </flux:button>
                                @if($loan->status === 'active')
                                <flux:button variant="primary" size="sm" :href="route('loans.repayment', $loan->id)">
                                    {{ __('Make Payment') }}
                                </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                            <flux:icon.credit-card class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ __('No Loans Yet') }}
                            </h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                                {{ __('You haven\'t applied for any loans yet.') }}
                            </p>
                            <flux:button variant="primary" :href="route('loans.apply')" wire:navigate>
                                {{ __('Apply for Your First Loan') }}
                            </flux:button>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>


        </div>
    </div>
</x-layouts.app> 