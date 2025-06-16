<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Mobile close toggle -->
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Logo - Desktop in sidebar, Mobile in header -->
        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
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

        <flux:header sticky class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <!-- First Row: Main Header -->
            <div class="flex items-center justify-between w-full px-4 sm:px-6 lg:px-8">
                <!-- Left Section: Page Context & Navigation -->
                <div class="flex items-center space-x-4 min-w-0 flex-1">
                    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                    
                    <!-- Logo (Mobile Only - Desktop logo is in sidebar) -->
                    <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center space-x-2 flex-shrink-0" wire:navigate>
                        <x-app-logo />
                    </a>

                    <!-- Page Context -->
                    <div class="hidden lg:flex items-center space-x-4 min-w-0">
                        @php
                            $currentRoute = request()->route()->getName();
                            $pageContext = [
                                'dashboard' => [
                                    'icon' => 'home',
                                    'title' => __('Dashboard'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'transactions.index' => [
                                    'icon' => 'arrows-right-left',
                                    'title' => __('Transactions'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Transactions'), 'route' => 'transactions.index', 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'transactions.deposit.create' => [
                                    'icon' => 'plus',
                                    'title' => __('New Deposit'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Transactions'), 'route' => 'transactions.index', 'current' => false],
                                        ['label' => __('New Deposit'), 'route' => null, 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'transactions.withdrawal.create' => [
                                    'icon' => 'minus',
                                    'title' => __('Withdrawal'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Transactions'), 'route' => 'transactions.index', 'current' => false],
                                        ['label' => __('New Withdrawal'), 'route' => null, 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'members.index' => [
                                    'icon' => 'users',
                                    'title' => __('Members'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Members'), 'route' => 'members.index', 'current' => true]
                                    ],
                                    'actions' => [
                                        ['label' => __('Add'), 'route' => 'members.create', 'icon' => 'user-plus']
                                    ]
                                ],
                                'members.create' => [
                                    'icon' => 'user-plus',
                                    'title' => __('Add Member'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Members'), 'route' => 'members.index', 'current' => false],
                                        ['label' => __('Add Member'), 'route' => null, 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'savings.index' => [
                                    'icon' => 'banknotes',
                                    'title' => __('Savings'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Savings'), 'route' => 'savings.index', 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'loans.index' => [
                                    'icon' => 'credit-card',
                                    'title' => __('Loans'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Loans'), 'route' => 'loans.index', 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                                'analytics.index' => [
                                    'icon' => 'chart-bar',
                                    'title' => __('Analytics'),
                                    'breadcrumbs' => [
                                        ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => false],
                                        ['label' => __('Analytics'), 'route' => 'analytics.index', 'current' => true]
                                    ],
                                    'actions' => []
                                ],
                            ];

                            $context = $pageContext[$currentRoute] ?? [
                                'icon' => 'home',
                                'title' => __('Dashboard'),
                                'breadcrumbs' => [
                                    ['label' => __('Dashboard'), 'route' => 'dashboard', 'current' => true]
                                ],
                                'actions' => []
                            ];
                        @endphp

                        <!-- Breadcrumbs -->
                        <div class="flex items-center space-x-1 text-sm text-zinc-500 dark:text-zinc-400 min-w-0">
                            @foreach($context['breadcrumbs'] as $index => $breadcrumb)
                                @if($index > 0)
                                    <flux:icon.chevron-right class="h-3 w-3 flex-shrink-0" />
                                @endif
                                
                                @if($breadcrumb['current'] || !$breadcrumb['route'])
                                    <span class="whitespace-nowrap text-zinc-900 dark:text-zinc-100 font-medium">{{ $breadcrumb['label'] }}</span>
                                @else
                                    <a href="{{ route($breadcrumb['route']) }}" 
                                       wire:navigate 
                                       class="whitespace-nowrap hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">
                                        {{ $breadcrumb['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Section: Search & Utilities -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <!-- Search Bar (Desktop) -->
                    <div class="relative hidden md:block">
                        @php
                            $searchPlaceholders = [
                                'dashboard' => __('Search anything...'),
                                'transactions.index' => __('Search transactions, amounts, members...'),
                                'members.index' => __('Search members by name, ID, phone...'),
                                'savings.index' => __('Search accounts, balances...'),
                                'loans.index' => __('Search loans, applications...'),
                            ];
                            $currentPlaceholder = $searchPlaceholders[$currentRoute] ?? __('Search...');
                        @endphp
                        
                        <flux:input 
                            type="search" 
                            placeholder="{{ $currentPlaceholder }}" 
                            class="w-64 xl:w-80 pl-10 pr-4 py-2" 
                        />
                        <flux:icon.magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-zinc-400" />
                    </div>

                    <!-- Action Buttons (based on context) -->
                    @if(!empty($context['actions']))
                        <div class="hidden lg:flex items-center space-x-2">
                            @foreach($context['actions'] as $action)
                                <flux:button size="sm" :href="$action['route'] !== '#' ? route($action['route']) : '#'" wire:navigate variant="primary" icon="{{ $action['icon'] }}">
                                    <span class="hidden lg:inline">{{ $action['label'] }}</span>
                                </flux:button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Utility Icons -->
                    <div class="flex items-center space-x-3">
                        <!-- Mobile search -->
                        <flux:tooltip :content="__('Search')" position="bottom">
                            <flux:navbar.item class="md:hidden" icon="magnifying-glass" href="#" />
                        </flux:tooltip>

                        <!-- External links (Desktop only) -->
                        <flux:tooltip :content="__('Repository')" position="bottom">
                            <flux:navbar.item
                                class="hidden xl:block"
                                icon="folder-git-2"
                                href="https://github.com/thenewkenya/saccocore.git"
                                target="_blank"
                            />
                        </flux:tooltip>

                        <!-- Notifications (Centered) -->
                        @livewire('notifications-dropdown')

                        <!-- Documentation (Desktop only) -->
                        <flux:tooltip :content="__('Documentation')" position="bottom">
                            <flux:navbar.item
                                class="hidden xl:block"
                                icon="book-open-text"
                                href="https://laravel.com/docs/starter-kits#livewire"
                                target="_blank"
                            />
                        </flux:tooltip>
                    </div>
                </div>
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
