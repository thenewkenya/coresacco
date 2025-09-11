<x-layouts.app :title="__('My Profile')">
    <div>
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">{{ __('My Profile') }}</flux:heading>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">{{ __('Manage your personal information and account settings') }}</flux:subheading>
                </div>
                <flux:button variant="primary" icon="pencil" :href="route('settings.profile')">
                    {{ __('Edit Profile') }}
                </flux:button>
            </div>

            <!-- Profile Summary -->
            <div class="flex items-center gap-4 mb-8">
                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-base font-bold text-white">{{ auth()->user()->initials() }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <flux:heading size="base" class="truncate">{{ auth()->user()->name }}</flux:heading>
                    <flux:subheading class="truncate">{{ auth()->user()->email }}</flux:subheading>
                    <div class="flex items-center gap-3 text-xs mt-1">
                        <flux:badge variant="lime">{{ __('Active') }}</flux:badge>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Member since') }} {{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <flux:subheading class="!text-xs !mb-0">{{ __('Member #') }}</flux:subheading>
                    <div class="font-mono text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ str_pad(auth()->id(), 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Personal & Address Information -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Personal Information -->
                    <div class="space-y-3">
                        <flux:heading size="base">{{ __('Personal Information') }}</flux:heading>
                        <dl class="divide-y divide-zinc-200 dark:divide-zinc-700 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('Full Name') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('Email Address') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100 truncate">{{ auth()->user()->email }}</dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('Phone Number') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->phone_number ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('ID Number') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->id_number ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Address Information -->
                    <div class="space-y-3">
                        <flux:heading size="base">{{ __('Address Information') }}</flux:heading>
                        <dl class="divide-y divide-zinc-200 dark:divide-zinc-700 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('Physical Address') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->address ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('City') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->city ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-3">
                                <dt class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">{{ __('Postal Code') }}</dt>
                                <dd class="sm:col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ auth()->user()->postal_code ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Membership & Account Summary -->
                <div class="space-y-10">
                    <div class="space-y-3">
                        <flux:heading size="base">{{ __('Membership Status') }}</flux:heading>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</span>
                                <flux:badge variant="lime">{{ __('Active') }}</flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Branch') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ auth()->user()->branch->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:heading size="base">{{ __('Account Summary') }}</flux:heading>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Savings Balance') }}</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">KSh 65,750</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Active Loans') }}</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400">2</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:heading size="base">{{ __('Emergency Contact') }}</flux:heading>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Name') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">Jane Doe</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Relationship') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">Spouse</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Phone') }}</span>
                                <span class="text-zinc-900 dark:text-zinc-100">+254 722 123 456</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 