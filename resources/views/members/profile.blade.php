<x-layouts.app :title="__('My Profile')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('My Profile') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage your personal information and account settings') }}
                        </p>
                    </div>
                    <flux:button variant="primary" icon="pencil" :href="route('settings.profile')">
                        {{ __('Edit Profile') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Profile Summary -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-8">
                    <div class="flex items-center space-x-6">
                        <div class="h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ auth()->user()->name }}
                            </h2>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-2">
                                {{ auth()->user()->email }}
                            </p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                    {{ __('Active Member') }}
                                </span>
                                <span class="text-zinc-600 dark:text-zinc-400">
                                    {{ __('Member since') }} {{ \Carbon\Carbon::parse('2020-01-15')->format('M Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Member ID') }}</p>
                            <p class="font-mono text-lg font-bold text-zinc-900 dark:text-zinc-100">MB-001234</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Personal Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Personal Information') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Full Name') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Email Address') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ auth()->user()->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Phone Number') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">+254 712 345 678</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('ID Number') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">12345678</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Date of Birth') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">March 15, 1985</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Gender') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">Male</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Address Information') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Physical Address') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">123 Kenyatta Avenue, Nairobi</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('City') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">Nairobi</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                        {{ __('Postal Code') }}
                                    </label>
                                    <p class="text-zinc-900 dark:text-zinc-100">00100</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="space-y-6">
                    <!-- Membership Status -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Membership Status') }}
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</span>
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-sm">
                                        {{ __('Active') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Branch') }}</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">Nairobi Main</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Share Capital') }}</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">KSh 50,000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Account Summary') }}
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Savings Balance') }}</span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">KSh 65,750</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Active Loans') }}</span>
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">2</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Credit Score') }}</span>
                                    <span class="font-semibold text-purple-600 dark:text-purple-400">742</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Emergency Contact') }}
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">Jane Doe</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Spouse</p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Phone') }}</p>
                                    <p class="text-zinc-900 dark:text-zinc-100">+254 722 123 456</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 