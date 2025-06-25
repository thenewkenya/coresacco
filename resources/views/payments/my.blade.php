<x-layouts.app :title="__('My Payments')">
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto space-y-4 sm:space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                        My Payments
                    </h1>
                    <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                        Track your payment history and manage upcoming payments
                    </p>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <flux:button variant="outline" size="sm" icon="document-arrow-down" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Download Statement</span>
                        <span class="sm:hidden">Statement</span>
                    </flux:button>
                    <flux:button variant="primary" size="sm" icon="plus" :href="route('payments.create')" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Make Payment</span>
                        <span class="sm:hidden">Pay</span>
                    </flux:button>
                </div>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                <!-- Total Paid This Month -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Paid</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                                    KSh 28,500
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">This month</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 mt-4">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Payments:</span>
                        <span class="text-sm font-medium text-emerald-600">8</span>
                        <div class="flex items-center text-emerald-600">
                            <flux:icon.arrow-up class="w-3 h-3" />
                            <span class="text-xs">12.3%</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Outstanding</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white">
                                    KSh 5,000
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Due soon</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center text-amber-600">
                            <flux:icon.clock class="w-3 h-3" />
                            <span class="text-xs">Due Dec 28</span>
                        </div>
                        <a href="{{ route('payments.create') }}" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                            Pay Now
                            <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                        </a>
                    </div>
                    <div class="mt-4 h-16 flex items-end space-x-1">
                        @for($i = 0; $i < 8; $i++)
                            <div class="flex-1 bg-amber-500 rounded-sm opacity-{{ rand(30, 100) }}" style="height: {{ rand(20, 100) }}%"></div>
                        @endfor
                    </div>
                </div>

                <!-- Payment Score -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Payment Score</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white">
                                    98%
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">On-time rate</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center text-emerald-600">
                            <flux:icon.arrow-up class="w-3 h-3" />
                            <span class="text-xs">+2.1%</span>
                        </div>
                        <a href="#payment-history" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                            View History
                            <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                        </a>
                    </div>
                    <div class="mt-4">
                        <div class="relative h-16 w-16 mx-auto">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 32 32">
                                <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="3" fill="none" class="text-zinc-200 dark:text-zinc-700" />
                                <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="98 2" class="text-emerald-500" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">98%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Quick Actions</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-20 w-20 mx-auto mb-4">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 32 32">
                            <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-200 dark:text-zinc-700" />
                            <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="75 25" class="text-emerald-500" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-zinc-900 dark:text-white">75%</span>
                            <span class="text-xs text-zinc-500">Success</span>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-xs text-zinc-500 mb-2">Since last month</div>
                        <div class="flex justify-center space-x-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full mr-1"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">Success</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-zinc-300 rounded-full mr-1"></div>
                                <span class="text-zinc-600 dark:text-zinc-400">Failed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div id="payment-history" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white flex items-center">
                            Payment History
                            <flux:icon.information-circle class="w-4 h-4 text-zinc-400 ml-2" />
                        </h3>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <select class="text-xs sm:text-sm border-0 bg-transparent text-zinc-600 dark:text-zinc-400 focus:ring-0 flex-1 sm:flex-none">
                            <option>Last 30 days</option>
                            <option>Last 7 days</option>
                            <option>This month</option>
                        </select>
                        <flux:button variant="outline" size="sm" icon="funnel" class="hidden sm:flex">Filters</flux:button>
                    </div>
                </div>
                
                <div class="mb-4 sm:mb-6">
                    <div class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                        <span class="block sm:inline">KSh 28,500</span>
                        <span class="text-xs sm:text-sm font-normal text-emerald-500">
                            +12.3%
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">vs last month</p>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="w-full min-w-[500px] sm:min-w-0">
                        <thead>
                            <tr class="text-left text-xs text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 font-medium">Payment</th>
                                <th class="pb-3 font-medium">Amount</th>
                                <th class="pb-3 font-medium">Method</th>
                                <th class="pb-3 font-medium">Date</th>
                                <th class="pb-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach([
                                ['type' => 'Loan Payment', 'amount' => '8500', 'status' => 'completed', 'method' => 'M-Pesa', 'date' => '2024-12-15'],
                                ['type' => 'Deposit', 'amount' => '15000', 'status' => 'completed', 'method' => 'Bank Transfer', 'date' => '2024-12-10'],
                                ['type' => 'Insurance Premium', 'amount' => '2500', 'status' => 'completed', 'method' => 'Auto-debit', 'date' => '2024-12-05'],
                                ['type' => 'Loan Payment', 'amount' => '8500', 'status' => 'completed', 'method' => 'Cash', 'date' => '2024-11-28']
                            ] as $payment)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                            <flux:icon.check class="w-4 h-4 text-emerald-600" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $payment['type'] }}</div>
                                            <div class="text-xs text-zinc-500">Payment transaction</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-zinc-900 dark:text-white font-medium">KSh {{ number_format($payment['amount']) }}</td>
                                <td class="py-4 text-zinc-900 dark:text-white">{{ $payment['method'] }}</td>
                                <td class="py-4 text-zinc-900 dark:text-white">{{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}</td>
                                <td class="py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                        {{ ucfirst($payment['status']) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 