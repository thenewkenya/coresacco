<x-layouts.app :title="__('Insurance Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Insurance Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage member insurance policies and claims') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="document-text">
                            {{ __('Claims Report') }}
                        </flux:button>
                        <flux:button variant="primary" icon="plus">
                            {{ __('New Policy') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Insurance Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.heart class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Active</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Policies') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">1,247</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Life & Protection') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.currency-dollar class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Monthly</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Premium Collection') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 2.8M</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('This month') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.exclamation-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Claims') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">8</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Need review') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Total</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Coverage Value') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 580M</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('All policies') }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Claims -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Recent Claims') }}
                        </h3>
                        <flux:button variant="ghost" size="sm">
                            {{ __('View All') }}
                        </flux:button>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        [
                            'claim_id' => 'CLM-001',
                            'member' => 'John Mukama',
                            'policy_type' => 'Life Insurance',
                            'claim_amount' => '250000',
                            'submitted' => '2024-12-14',
                            'status' => 'under_review'
                        ],
                        [
                            'claim_id' => 'CLM-002',
                            'member' => 'Mary Wanjiku',
                            'policy_type' => 'Loan Protection',
                            'claim_amount' => '85000',
                            'submitted' => '2024-12-12',
                            'status' => 'approved'
                        ],
                        [
                            'claim_id' => 'CLM-003',
                            'member' => 'Peter Ochieng',
                            'policy_type' => 'Life Insurance',
                            'claim_amount' => '500000',
                            'submitted' => '2024-12-10',
                            'status' => 'pending_documents'
                        ]
                    ] as $claim)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 rounded-lg {{ $claim['status'] === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30' : ($claim['status'] === 'under_review' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                    @if($claim['status'] === 'approved')
                                        <flux:icon.check class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    @elseif($claim['status'] === 'under_review')
                                        <flux:icon.clock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    @else
                                        <flux:icon.document-text class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $claim['member'] }} - {{ $claim['policy_type'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $claim['claim_id'] }} • {{ \Carbon\Carbon::parse($claim['submitted'])->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    KSh {{ number_format($claim['claim_amount']) }}
                                </p>
                                <span class="px-2 py-1 text-xs font-medium {{ $claim['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : ($claim['status'] === 'under_review' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }} rounded-full">
                                    {{ str_replace('_', ' ', ucfirst($claim['status'])) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Policy Management -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Policy Management') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="funnel">
                                {{ __('Filter') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['member' => 'Sarah Muthoni', 'policy' => 'Life Insurance', 'premium' => '3500', 'coverage' => '750000', 'status' => 'active'],
                        ['member' => 'James Kimani', 'policy' => 'Loan Protection', 'premium' => '1200', 'coverage' => '150000', 'status' => 'active'],
                        ['member' => 'Grace Nyambura', 'policy' => 'Life Insurance', 'premium' => '2800', 'coverage' => '500000', 'status' => 'lapsed'],
                        ['member' => 'David Omondi', 'policy' => 'Group Life', 'premium' => '800', 'coverage' => '250000', 'status' => 'active']
                    ] as $policy)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.heart class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $policy['member'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $policy['policy'] }} • KSh {{ number_format($policy['coverage']) }} coverage
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        KSh {{ number_format($policy['premium']) }}
                                    </p>
                                    <span class="px-2 py-1 text-xs font-medium {{ $policy['status'] === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }} rounded-full">
                                        {{ ucfirst($policy['status']) }}
                                    </span>
                                </div>
                                <flux:button variant="outline" size="sm">
                                    {{ __('Manage') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 