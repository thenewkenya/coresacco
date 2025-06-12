<x-layouts.app.header :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <!-- Welcome Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Dashboard') }}</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Welcome back,') }} {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Today') }}</p>
                <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ now()->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Admin Dashboard -->
        @role('admin')
        <div class="space-y-6">
            <!-- Admin Key Metrics -->
            <div class="grid auto-rows-min gap-6 md:grid-cols-4">
                <!-- Total Members Card -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">1,247</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.arrow-trending-up class="h-4 w-4 mr-1" />
                                    +12 {{ __('this month') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                            <flux:icon.users class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Total Assets -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total Assets') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">KSh 45.2M</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.arrow-trending-up class="h-4 w-4 mr-1" />
                                    +15.3% {{ __('YTD') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/30">
                            <flux:icon.banknotes class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                </div>

                <!-- Outstanding Loans -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Outstanding Loans') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">KSh 18.4M</p>
                            <p class="text-sm text-amber-600 dark:text-amber-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.clock class="h-4 w-4 mr-1" />
                                    234 {{ __('active') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/30">
                            <flux:icon.credit-card class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('System Health') }}</p>
                            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">98.5%</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.check-circle class="h-4 w-4 mr-1" />
                                    {{ __('All systems operational') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/30">
                            <flux:icon.cpu-chip class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Panel -->
            <div class="border-t border-red-200 dark:border-red-700 pt-6">
                <h4 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">
                    Administrator Panel
                </h4>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <p class="text-sm text-red-800 dark:text-red-200 mb-4">
                        You have administrative privileges. Handle with care!
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @can('manage-roles')
                        <flux:button variant="primary" class="w-full justify-center bg-red-600 hover:bg-red-700" href="#" wire:navigate>
                            <flux:icon.users class="h-4 w-4 mr-2" />
                            {{ __('Manage Roles') }}
                        </flux:button>
                        @endcan
                        
                        @can('manage-settings')
                        <flux:button variant="primary" class="w-full justify-center bg-red-600 hover:bg-red-700" href="#" wire:navigate>
                            <flux:icon.cog-6-tooth class="h-4 w-4 mr-2" />
                            {{ __('System Settings') }}
                        </flux:button>
                        @endcan

                        <flux:button variant="primary" class="w-full justify-center bg-red-600 hover:bg-red-700" href="#" wire:navigate>
                            <flux:icon.chart-bar class="h-4 w-4 mr-2" />
                            {{ __('System Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        <!-- Staff/Manager Dashboard -->
        @roleany('staff', 'manager')
        <div class="space-y-6">
            <!-- Staff Key Metrics -->
            <div class="grid auto-rows-min gap-6 md:grid-cols-3">
                <!-- Today's Transactions -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Today\'s Transactions') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">47</p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.arrow-trending-up class="h-4 w-4 mr-1" />
                                    KSh 234,500 {{ __('processed') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                            <flux:icon.arrows-right-left class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Pending Approvals') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">12</p>
                            <p class="text-sm text-amber-600 dark:text-amber-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.clock class="h-4 w-4 mr-1" />
                                    {{ __('Requires attention') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/30">
                            <flux:icon.clipboard-document-check class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                </div>

                <!-- Active Members -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Active Members') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">1,247</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.user-plus class="h-4 w-4 mr-1" />
                                    +5 {{ __('this week') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/30">
                            <flux:icon.users class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Operations -->
            <div class="border-t border-blue-200 dark:border-blue-700 pt-6">
                <h4 class="text-lg font-medium text-blue-600 dark:text-blue-400 mb-4">
                    Staff Operations
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @can('process-transactions')
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer">
                        <div class="text-blue-600 dark:text-blue-400 font-medium">{{ __('Process Transactions') }}</div>
                        <div class="text-sm text-blue-500 dark:text-blue-300">{{ __('Handle deposits & withdrawals') }}</div>
                        <div class="mt-2">
                            <flux:button variant="ghost" size="sm" class="text-blue-600" href="#" wire:navigate>
                                {{ __('Open') }} →
                            </flux:button>
                        </div>
                    </div>
                    @endcan

                    @can('approve-loans') 
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer">
                        <div class="text-green-600 dark:text-green-400 font-medium">{{ __('Approve Loans') }}</div>
                        <div class="text-sm text-green-500 dark:text-green-300">{{ __('Review loan applications') }}</div>
                        <div class="mt-2">
                            <flux:button variant="ghost" size="sm" class="text-green-600" href="#" wire:navigate>
                                {{ __('Open') }} →
                            </flux:button>
                        </div>
                    </div>
                    @endcan

                    @can('disburse-loans')
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer">
                        <div class="text-yellow-600 dark:text-yellow-400 font-medium">{{ __('Disburse Loans') }}</div>
                        <div class="text-sm text-yellow-500 dark:text-yellow-300">{{ __('Release approved funds') }}</div>
                        <div class="mt-2">
                            <flux:button variant="ghost" size="sm" class="text-yellow-600" href="#" wire:navigate>
                                {{ __('Open') }} →
                            </flux:button>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endroleany

        <!-- Member Dashboard -->
        @role('member')
        <div class="space-y-6">
            <!-- Member Account Overview -->
            <div class="grid auto-rows-min gap-6 md:grid-cols-3">
                <!-- Savings Balance -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Savings Balance') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">KSh 45,750</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.arrow-trending-up class="h-4 w-4 mr-1" />
                                    +KSh 2,500 {{ __('this month') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/30">
                            <flux:icon.banknotes class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                </div>

                <!-- Current Loan -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Current Loan') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">KSh 25,000</p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.calendar class="h-4 w-4 mr-1" />
                                    {{ __('Next payment: 15th Jan') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                            <flux:icon.credit-card class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Dividends Earned -->
                <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Dividends (2024)') }}</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">KSh 3,450</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">
                                <span class="inline-flex items-center">
                                    <flux:icon.gift class="h-4 w-4 mr-1" />
                                    12% {{ __('return') }}
                                </span>
                            </p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
                            <flux:icon.sparkles class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Services -->
            <div class="border-t border-green-200 dark:border-green-700 pt-6">
                <h4 class="text-lg font-medium text-green-600 dark:text-green-400 mb-4">
                    My SACCO Services
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                            <flux:icon.building-library class="h-6 w-6 text-green-600 dark:text-green-400" />
                            <h5 class="font-medium text-green-800 dark:text-green-200">{{ __('My Accounts') }}</h5>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-300">{{ __('View your savings and current accounts') }}</p>
                    </div>
                    
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                            <flux:icon.credit-card class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            <h5 class="font-medium text-blue-800 dark:text-blue-200">{{ __('My Loans') }}</h5>
                        </div>
                        <p class="text-sm text-blue-600 dark:text-blue-300">{{ __('View your loan status and history') }}</p>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                            <flux:icon.document-text class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            <h5 class="font-medium text-purple-800 dark:text-purple-200">{{ __('Apply for Loan') }}</h5>
                        </div>
                        <p class="text-sm text-purple-600 dark:text-purple-300">{{ __('Submit a new loan application') }}</p>
                    </div>

                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                            <flux:icon.chart-bar class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                            <h5 class="font-medium text-amber-800 dark:text-amber-200">{{ __('Statements') }}</h5>
                        </div>
                        <p class="text-sm text-amber-600 dark:text-amber-300">{{ __('Download account statements') }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Recent Activity') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-700">
                        <div class="flex items-center space-x-3">
                            <div class="rounded-full bg-emerald-100 p-2 dark:bg-emerald-900/30">
                                <flux:icon.arrow-down class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Monthly Contribution') }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Savings deposit') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-emerald-600 dark:text-emerald-400">+KSh 2,500</p>
                            <p class="text-sm text-zinc-500">{{ __('3 days ago') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-700">
                        <div class="flex items-center space-x-3">
                            <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-900/30">
                                <flux:icon.arrow-up class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Loan Repayment') }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Monthly installment') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-blue-600 dark:text-blue-400">KSh 1,875</p>
                            <p class="text-sm text-zinc-500">{{ __('1 week ago') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="rounded-full bg-purple-100 p-2 dark:bg-purple-900/30">
                                <flux:icon.sparkles class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ __('Dividend Payment') }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Annual dividend') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-purple-600 dark:text-purple-400">+KSh 3,450</p>
                            <p class="text-sm text-zinc-500">{{ __('2 weeks ago') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>
</x-layouts.app>