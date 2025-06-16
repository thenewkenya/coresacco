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

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
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
