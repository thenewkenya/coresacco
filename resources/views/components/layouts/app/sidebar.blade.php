<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Mobile close toggle -->
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Mobile logo - only visible on small screens when sidebar is open -->
        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse lg:hidden" wire:navigate>
            <x-app-logo />
        </a>

        <!-- Main Navigation -->
        <flux:navlist variant="outline">
            <!-- Core Operations -->
            <flux:navlist.group :heading="__('Core Operations')">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
                
                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="arrows-right-left" :href="route('transactions.index')" :current="request()->routeIs('transactions.*')" wire:navigate>
                    {{ __('Transactions') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="arrows-right-left" :href="route('transactions.index')" :current="request()->routeIs('transactions.*')" wire:navigate>
                    {{ __('My Transactions') }}
                </flux:navlist.item>
                @endrole
            </flux:navlist.group>

            <!-- Financial Services -->
            <flux:navlist.group :heading="__('Financial Services')">
                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="banknotes" :href="route('savings.index')" :current="request()->routeIs('savings.*')" wire:navigate>
                    {{ __('Savings Accounts') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="banknotes" :href="route('savings.my')" :current="request()->routeIs('savings.*')" wire:navigate>
                    {{ __('My Savings') }}
                </flux:navlist.item>
                @endrole

                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="credit-card" :href="route('loans.index')" :current="request()->routeIs('loans.*')" wire:navigate>
                    {{ __('Loan Management') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="credit-card" :href="route('loans.my')" :current="request()->routeIs('loans.*')" wire:navigate>
                    {{ __('My Loans') }}
                </flux:navlist.item>
                @endrole

                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="currency-dollar" :href="route('payments.index')" :current="request()->routeIs('payments.*')" wire:navigate>
                    {{ __('Payment Processing') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="currency-dollar" :href="route('payments.my')" :current="request()->routeIs('payments.*')" wire:navigate>
                    {{ __('My Payments') }}
                </flux:navlist.item>
                @endrole
            </flux:navlist.group>

            <!-- Member Services -->
            <flux:navlist.group :heading="__('Member Services')">
                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="users" :href="route('members.index')" :current="request()->routeIs('members.*')" wire:navigate>
                    {{ __('Members') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="user" :href="route('members.profile')" :current="request()->routeIs('members.profile')" wire:navigate>
                    {{ __('My Profile') }}
                </flux:navlist.item>
                @endrole

                @role('member')
                <flux:navlist.item icon="flag" :href="route('goals.index')" :current="request()->routeIs('goals.*')" wire:navigate>
                    {{ __('My Goals') }}
                </flux:navlist.item>
                @endrole

                @role('member')
                <flux:navlist.item icon="currency-dollar" :href="route('budget.index')" :current="request()->routeIs('budget.*')" wire:navigate>
                    {{ __('Budget Planner') }}
                </flux:navlist.item>
                @endrole

                @roleany('admin', 'manager', 'staff')
                <flux:navlist.item icon="heart" :href="route('insurance.index')" :current="request()->routeIs('insurance.*')" wire:navigate>
                    {{ __('Insurance') }}
                </flux:navlist.item>
                @endroleany

                @role('member')
                <flux:navlist.item icon="heart" :href="route('insurance.my')" :current="request()->routeIs('insurance.*')" wire:navigate>
                    {{ __('My Insurance') }}
                </flux:navlist.item>
                @endrole
            </flux:navlist.group>

            <!-- Management & Analytics -->
            @roleany('admin', 'manager')
            <flux:navlist.group :heading="__('Management')">
                <flux:navlist.item icon="chart-bar" :href="route('analytics.index')" :current="request()->routeIs('analytics.*')" wire:navigate>
                    {{ __('Analytics') }}
                </flux:navlist.item>

                <flux:navlist.item icon="document-text" :href="route('reports.index')" :current="request()->routeIs('reports.*')" wire:navigate>
                    {{ __('Reports') }}
                </flux:navlist.item>

                @role('admin')
                <flux:navlist.item icon="building-office-2" :href="route('branches.index')" :current="request()->routeIs('branches.*')" wire:navigate>
                    {{ __('Branches') }}
                </flux:navlist.item>
                @endrole

                @role('admin')
                <flux:navlist.item icon="user-group" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>
                    {{ __('User Roles') }}
                </flux:navlist.item>
                @endrole

                @role('admin')
                <flux:navlist.item icon="cog" :href="route('system.settings')" :current="request()->routeIs('system.*')" wire:navigate>
                    {{ __('System Settings') }}
                </flux:navlist.item>
                @endrole
            </flux:navlist.group>
            @endroleany

            <!-- Staff Tools -->
            @role('staff')
            <flux:navlist.group :heading="__('Staff Tools')">
                <flux:navlist.item icon="check-circle" :href="route('transactions.index', ['status' => 'pending'])" :current="request()->routeIs('transactions.*') && request('status') === 'pending'" wire:navigate>
                    {{ __('Pending Approvals') }}
                </flux:navlist.item>

                <flux:navlist.item icon="calendar" :href="route('schedule.index')" :current="request()->routeIs('schedule.*')" wire:navigate>
                    {{ __('Appointments') }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endrole
        </flux:navlist>

        <flux:spacer />

        <!-- External links for mobile -->
        <flux:navlist variant="outline" class="lg:hidden">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/thenewkenya/saccocore.git" target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop user menu - only visible on desktop -->
        <flux:dropdown class="max-lg:hidden" position="top" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

        <flux:header sticky container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <!-- Search bar and utility navigation -->
            <div class="flex items-center space-x-4">
                <!-- Search bar -->
                <div class="relative max-md:hidden">
                    <flux:input 
                        type="search" 
                        placeholder="Search members, accounts, transactions..." 
                        class="w-80 pl-10 pr-4 py-2" 
                    />
                    <flux:icon.magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-zinc-400" />
                </div>

                <!-- Utility icons -->
                <flux:navbar class="space-x-0.5 rtl:space-x-reverse py-0!">
                    <!-- Mobile search toggle -->
                    <flux:tooltip :content="__('Search')" position="bottom">
                        <flux:navbar.item class="!h-10 [&>div>svg]:size-5 md:hidden" icon="magnifying-glass" href="#" :label="__('Search')" />
                    </flux:tooltip>

                    <!-- Notifications -->
                    <flux:tooltip :content="__('Notifications')" position="bottom">
                        <flux:dropdown position="bottom" align="end">
                            <flux:navbar.item class="!h-10 [&>div>svg]:size-5 relative" icon="bell" :label="__('Notifications')">
                                <!-- Notification badge -->
                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">3</span>
                            </flux:navbar.item>
                            
                            <flux:menu class="w-80">
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Notifications') }}</h3>
                                        <a href="#" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('Mark all read') }}</a>
                                    </div>
                                </div>
                                
                                <div class="max-h-64 overflow-y-auto">
                                    <!-- Sample notifications -->
                                    <flux:menu.item class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <flux:icon.credit-card class="h-5 w-5 text-blue-500" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ __('New loan application') }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('John Doe submitted a loan application for KES 50,000') }}</p>
                                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('2 minutes ago') }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </flux:menu.item>

                                    <flux:menu.item class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <flux:icon.banknotes class="h-5 w-5 text-green-500" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ __('Large deposit received') }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('KES 75,000 deposit requires approval') }}</p>
                                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('5 minutes ago') }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </flux:menu.item>

                                    <flux:menu.item class="px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <flux:icon.exclamation-triangle class="h-5 w-5 text-amber-500" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ __('System backup completed') }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Daily backup finished successfully') }}</p>
                                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('1 hour ago') }}</p>
                                            </div>
                                        </div>
                                    </flux:menu.item>
                                </div>
                                
                                <div class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-700">
                                    <a href="#" class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ __('View all notifications') }}</a>
                                </div>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:tooltip>

                    <!-- External links -->
                    <flux:tooltip :content="__('Repository')" position="bottom">
                        <flux:navbar.item
                            class="h-10 max-lg:hidden [&>div>svg]:size-5"
                            icon="folder-git-2"
                            href="https://github.com/thenewkenya/saccocore.git"
                            target="_blank"
                            :label="__('Repository')"
                        />
                    </flux:tooltip>
                    <flux:tooltip :content="__('Documentation')" position="bottom">
                        <flux:navbar.item
                            class="h-10 max-lg:hidden [&>div>svg]:size-5"
                            icon="book-open-text"
                            href="https://laravel.com/docs/starter-kits#livewire"
                            target="_blank"
                            label="Documentation"
                        />
                    </flux:tooltip>
                </flux:navbar>
            </div>

            <!-- Mobile User Menu - only visible on mobile -->
            <flux:dropdown class="lg:hidden" position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
        {{ $slot }}
    @fluxScripts
</body>

</html>
