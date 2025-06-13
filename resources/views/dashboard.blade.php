<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Enhanced Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Welcome Back,') }} {{ auth()->user()->name }} 👋
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ now()->format('l, M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative hidden sm:block">
                            <input type="text" placeholder="{{ __('Search Anything') }}" 
                                   class="w-80 px-4 py-2 bg-zinc-100 dark:bg-zinc-700 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:text-white dark:placeholder-zinc-400">
                            <flux:icon.magnifying-glass class="absolute right-3 top-2.5 w-4 h-4 text-zinc-400" />
                        </div>
                        <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white">
                            <flux:icon.bell class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Key Metrics -->
            @role('admin')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Members -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+6.5%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">1,456</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since last week') }}</p>
                    </div>
                </div>

                <!-- Total Assets -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">-0.10%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Assets') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 45.2M</p>
                        <p class="text-xs text-red-600 dark:text-red-400">{{ __('Since last week') }}</p>
                    </div>
                </div>

                <!-- Loan Portfolio -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">-0.2%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Loan Portfolio') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">78%</p>
                        <p class="text-xs text-red-600 dark:text-red-400">{{ __('Since last week') }}</p>
                    </div>
                </div>

                <!-- Active Loans -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.document-text class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+11.5%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Active Loans') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">234</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since last week') }}</p>
                    </div>
                </div>
            </div>
            @endrole

            @roleany('staff', 'manager')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Today's Transactions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.arrows-right-left class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+8.2%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Today\'s Transactions') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">47</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since yesterday') }}</p>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Urgent</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Pending Approvals') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">12</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400">{{ __('Requires attention') }}</p>
                    </div>
                </div>

                <!-- Processed Amount -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+15.3%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Processed Today') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 234K</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since yesterday') }}</p>
                    </div>
                </div>
            </div>
            @endroleany

            @role('member')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Savings Balance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">+5.8%</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Savings Balance') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 45,750</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Since last month') }}</p>
                    </div>
                </div>

                <!-- Current Loan -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Due 15th</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Current Loan') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 25,000</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Next payment due') }}</p>
                    </div>
                </div>

                <!-- Dividends -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.sparkles class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">12% ROI</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Dividends (2024)') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh 3,450</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Annual return') }}</p>
                    </div>
                </div>
            </div>
            @endrole

            <!-- Charts and Analytics Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Loan Portfolio Analytics -->
                @roleany('admin', 'manager')
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Loan Portfolio') }}</h3>
                        <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                            <flux:icon.ellipsis-horizontal class="w-5 h-5" />
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-center mb-6">
                        <!-- Donut Chart Placeholder -->
                        <div class="relative w-40 h-40">
                            <div class="w-40 h-40 rounded-full bg-gradient-to-r from-blue-500 to-blue-600" style="clip-path: polygon(50% 50%, 50% 0%, 100% 0%, 100% 70%, 50% 50%);"></div>
                            <div class="absolute inset-8 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">234</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Active Loans') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Performing') }}</span>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">187 (80%)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Watch List') }}</span>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">32 (14%)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Non-Performing') }}</span>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">15 (6%)</span>
                        </div>
                    </div>
                </div>
                @endroleany

                <!-- Monthly Growth -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            @role('admin'){{ __('Asset Growth') }}@endrole
                            @roleany('staff', 'manager'){{ __('Transaction Trends') }}@endrole
                            @role('member'){{ __('My Savings Growth') }}@endrole
                        </h3>
                        <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                            <flux:icon.ellipsis-horizontal class="w-5 h-5" />
                        </button>
                    </div>
                    
                    <!-- Chart Placeholder -->
                    <div class="h-64 flex items-end justify-between space-x-2 mb-4">
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-t-sm" style="height: {{ rand(30, 100) }}%;"></div>
                            <span class="text-xs text-zinc-500 mt-2">{{ substr($month, 0, 3) }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="text-center">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('This Month') }}</p>
                            <p class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">+KSh 1.2M</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Growth Rate') }}</p>
                            <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">+8.5%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            @role('admin'){{ __('Recent Transactions') }}@endrole
                            @roleany('staff', 'manager'){{ __('Today\'s Activities') }}@endrole
                            @role('member'){{ __('My Recent Activity') }}@endrole
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="funnel">
                                {{ __('Filter') }}
                            </flux:button>
                            <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                                <flux:icon.ellipsis-horizontal class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider w-16">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Transaction') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">1</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">JD</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">John Doe</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <flux:icon.arrow-down class="w-4 h-4 text-emerald-500 mr-2" />
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Savings Deposit') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">{{ now()->subHours(2)->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">{{ __('Completed') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600 dark:text-emerald-400 text-right">+KSh 5,000</td>
                            </tr>

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">2</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-purple-600 dark:text-purple-400">SM</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Sarah Mwangi</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <flux:icon.arrow-up class="w-4 h-4 text-blue-500 mr-2" />
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Repayment') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">{{ now()->subHours(4)->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">{{ __('Processing') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600 dark:text-blue-400 text-right">KSh 2,500</td>
                            </tr>

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">3</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">PK</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Peter Kamau</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <flux:icon.document-text class="w-4 h-4 text-amber-500 mr-2" />
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Application') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">{{ now()->subHours(6)->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full">{{ __('Pending') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-600 dark:text-zinc-400 text-right">KSh 50,000</td>
                            </tr>

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">4</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-red-600 dark:text-red-400">MN</span>
                                        </div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Mary Njeri</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <flux:icon.arrow-up class="w-4 h-4 text-red-500 mr-2" />
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Withdrawal') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">{{ now()->subHours(8)->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">{{ __('Failed') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400 text-right">KSh 10,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Showing 1 to 4 of 47 transactions') }}</p>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm">{{ __('Previous') }}</flux:button>
                            <flux:button variant="primary" size="sm">{{ __('Next') }}</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>