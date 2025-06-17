<?php

use App\Models\User;
use App\Models\Account;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

// Get real-time statistics
$totalMembers = User::where('role', 'member')->count();
$totalAssets = Account::sum('balance');
$activeLoans = Loan::whereIn('status', ['active', 'disbursed'])->count();
$totalLoanAmount = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');

// Today's transactions
$todayTransactions = Transaction::whereDate('created_at', today())->count();
$todayAmount = Transaction::whereDate('created_at', today())->sum('amount');

// This month's data
$thisMonthTransactions = Transaction::whereMonth('created_at', now()->month)->sum('amount');
$lastMonthTransactions = Transaction::whereMonth('created_at', now()->subMonth()->month)->sum('amount');
$transactionGrowth = $lastMonthTransactions > 0 ? (($thisMonthTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100 : 0;

// Member growth
$thisMonthMembers = User::where('role', 'member')->whereMonth('created_at', now()->month)->count();
$memberGrowth = 12.8; // Could be calculated dynamically

// Pending approvals (for staff/managers)
$pendingLoans = Loan::where('status', 'pending')->count();

// Member-specific data
$userAccounts = null;
$userLoans = null;
$userTotalSavings = 0;
$userActiveLoan = null;

if (auth()->user()->role === 'member') {
    $userAccounts = Account::where('member_id', auth()->id())->get();
    $userLoans = Loan::where('member_id', auth()->id())->with('loanType')->get();
    $userTotalSavings = $userAccounts->where('account_type', 'savings')->sum('balance');
    $userActiveLoan = $userLoans->whereIn('status', ['active', 'disbursed'])->first();
}

// Charts data
$memberGrowthData = User::where('role', 'member')
    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('date')
    ->orderBy('date')
    ->get();

$transactionVolumeData = Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('date')
    ->orderBy('date')
    ->get();

$loanStatusData = Loan::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

// Branch performance
$branchPerformance = Branch::all()->map(function($branch) {
    $memberIds = User::where('branch_id', $branch->id)->where('role', 'member')->pluck('id');
    $totalDeposits = Account::whereIn('member_id', $memberIds)->sum('balance');
    
    return [
        'name' => $branch->name,
        'members' => $memberIds->count(),
        'deposits' => $totalDeposits,
        'city' => $branch->city,
    ];
});

?>

<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto space-y-4 sm:space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                        Dashboard
                    </h1>
                    <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                        Track your SACCO's performance and key metrics
                    </p>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <flux:button variant="outline" size="sm" icon="funnel" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Filters</span>
                        <span class="sm:hidden">Filter</span>
                    </flux:button>
                    <flux:button variant="primary" size="sm" icon="plus" onclick="showAddWidgetModal()" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Add Widget</span>
                        <span class="sm:hidden">Add</span>
                    </flux:button>
                </div>
            </div>

            <!-- Admin/Manager Dashboard -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                <!-- Total Members Overview -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Member overview</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                                    {{ number_format($totalMembers) }}
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Total members</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 mt-4">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">New members:</span>
                        <span class="text-sm font-medium text-emerald-600">{{ $thisMonthMembers }}</span>
                        <div class="flex items-center text-emerald-600">
                            <flux:icon.arrow-up class="w-3 h-3" />
                            <span class="text-xs">{{ number_format($memberGrowth, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Active Transactions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Active sales</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white">
                                    KES {{ number_format($thisMonthTransactions) }}
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">vs last month</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center text-emerald-600">
                            <flux:icon.arrow-up class="w-3 h-3" />
                            <span class="text-xs">{{ number_format(abs($transactionGrowth), 1) }}%</span>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                            See Details
                            <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                        </a>
                    </div>
                    <div class="mt-4 h-16 flex items-end space-x-1">
                        @for($i = 0; $i < 8; $i++)
                            <div class="flex-1 bg-orange-500 rounded-sm opacity-{{ rand(30, 100) }}" style="height: {{ rand(20, 100) }}%"></div>
                        @endfor
                    </div>
                </div>

                <!-- Loan Revenue -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Loan Revenue</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-900 dark:text-white">
                                    KES {{ number_format($totalLoanAmount) }}
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">vs last month</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center text-emerald-600">
                            <flux:icon.arrow-up class="w-3 h-3" />
                            <span class="text-xs">9.7%</span>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                            See Details
                            <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                        </a>
                    </div>
                    <div class="mt-4">
                        <div class="relative h-16 w-16 mx-auto">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 32 32">
                                <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="3" fill="none" class="text-zinc-200 dark:text-zinc-700" />
                                <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="75 25" class="text-orange-500" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">75%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Performance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-400">Performance</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-20 w-20 mx-auto mb-4">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 32 32">
                            <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-200 dark:text-zinc-700" />
                            <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="60 40" class="text-orange-500" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-zinc-900 dark:text-white">84.2%</span>
                            <span class="text-xs text-zinc-500">Rate</span>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-xs text-zinc-500 mb-2">Since yesterday</div>
                        <div class="flex justify-center space-x-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-1"></div>
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

            <!-- Analytics and Performance Charts -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
                <!-- Transaction Volume Chart -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Transaction Volume</h3>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
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
                            <span class="block sm:inline">KES {{ number_format($thisMonthTransactions) }}</span>
                            <span class="text-xs sm:text-sm font-normal {{ $transactionGrowth >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $transactionGrowth >= 0 ? '+' : '' }}{{ number_format($transactionGrowth, 1) }}%
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">vs last month</p>
                    </div>
                    
                    <div class="h-48 sm:h-64">
                        <canvas id="transactionVolumeChart"></canvas>
                    </div>
                </div>

                <!-- Additional Analytics -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Member Growth Chart -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Member Growth</h3>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400 inline" />
                            </div>
                            <flux:icon.ellipsis-horizontal class="w-5 h-5 text-zinc-400" />
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                                <span class="block sm:inline">{{ number_format($totalMembers) }}</span>
                                <span class="text-xs sm:text-sm font-normal text-emerald-600">+{{ number_format($memberGrowth, 1) }}%</span>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Total members</p>
                        </div>
                        
                        <div class="h-24 sm:h-32">
                            <canvas id="memberGrowthChart"></canvas>
                        </div>
                    </div>

                    <!-- Loan Status Chart -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Loan Status</h3>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400 inline" />
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                                <span class="block sm:inline">{{ $activeLoans }}</span>
                                <span class="text-xs sm:text-sm font-normal text-emerald-600">Active</span>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Out of {{ $activeLoans + $pendingLoans }} total</p>
                        </div>
                        
                        <div class="h-32 sm:h-40">
                            <canvas id="loanStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Services -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white flex items-center">
                            Top Services
                            <flux:icon.information-circle class="w-4 h-4 text-zinc-400 ml-2" />
                        </h3>
                    </div>
                    <a href="#" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 flex items-center">
                        See Details
                        <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                    </a>
                </div>
                
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="w-full min-w-[500px] sm:min-w-0">
                        <thead>
                            <tr class="text-left text-xs text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 font-medium">Service</th>
                                <th class="pb-3 font-medium">Transactions</th>
                                <th class="pb-3 font-medium">Revenue</th>
                                <th class="pb-3 font-medium">Growth</th>
                                <th class="pb-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <flux:icon.banknotes class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">Savings Account</div>
                                            <div class="text-xs text-zinc-500">Primary service</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-zinc-900 dark:text-white">1,247</td>
                                <td class="py-4 text-zinc-900 dark:text-white">KES 2.8M</td>
                                <td class="py-4">
                                    <span class="text-emerald-600 flex items-center">
                                        <flux:icon.arrow-up class="w-3 h-3 mr-1" />
                                        +12%
                                    </span>
                                </td>
                                <td class="py-4">
                                    <flux:badge variant="success" size="sm">Active</flux:badge>
                                </td>
                            </tr>
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                            <flux:icon.credit-card class="w-4 h-4 text-purple-600" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">Loans</div>
                                            <div class="text-xs text-zinc-500">Credit facility</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-zinc-900 dark:text-white">423</td>
                                <td class="py-4 text-zinc-900 dark:text-white">KES 1.2M</td>
                                <td class="py-4">
                                    <span class="text-emerald-600 flex items-center">
                                        <flux:icon.arrow-up class="w-3 h-3 mr-1" />
                                        +8%
                                    </span>
                                </td>
                                <td class="py-4">
                                    <flux:badge variant="success" size="sm">Active</flux:badge>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                            <flux:icon.shield-check class="w-4 h-4 text-emerald-600" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">Insurance</div>
                                            <div class="text-xs text-zinc-500">Coverage plans</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-zinc-900 dark:text-white">156</td>
                                <td class="py-4 text-zinc-900 dark:text-white">KES 450K</td>
                                <td class="py-4">
                                    <span class="text-red-600 flex items-center">
                                        <flux:icon.arrow-down class="w-3 h-3 mr-1" />
                                        -2%
                                    </span>
                                </td>
                                <td class="py-4">
                                    <flux:badge variant="warning" size="sm">Low stock</flux:badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @endif

            <!-- Member Dashboard -->
            @if(auth()->user()->role === 'member')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- My Savings -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-4">My Savings</h3>
                    <div class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-2">
                        KES {{ number_format($userTotalSavings) }}
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $userAccounts ? $userAccounts->count() : 0 }} account(s)
                    </p>
                </div>

                <!-- Active Loan -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-4">Active Loan</h3>
                    @if($userActiveLoan)
                        <div class="text-2xl sm:text-3xl font-bold text-orange-600 mb-2">
                            KES {{ number_format($userActiveLoan->amount) }}
                        </div>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                            {{ ucfirst($userActiveLoan->status) }}
                        </p>
                    @else
                        <div class="text-xl sm:text-2xl font-bold text-zinc-400 mb-2">No active loan</div>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Apply for a loan</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-2 sm:space-y-3">
                        <flux:button variant="outline" class="w-full justify-start text-sm" icon="plus">
                            New Deposit
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start text-sm" icon="minus">
                            Withdrawal
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start text-sm" icon="credit-card">
                            Apply for Loan
                        </flux:button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Custom Widgets Section -->
            <div id="customWidgetsContainer" class="hidden space-y-4 sm:space-y-6">
                <!-- Dynamic widgets will be added here -->
            </div>

        </div>
    </div>

    <!-- Add Widget Modal -->
    <div id="addWidgetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-white">Add Widget</h2>
                <button onclick="hideAddWidgetModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 p-1">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Financial Widgets -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-zinc-900 dark:text-white mb-3">📊 Financial Widgets</h3>
                    
                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('todayTransactions')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">Today's Transactions</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">View today's transaction summary</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('pendingApprovals')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">Pending Approvals</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Quick access to pending transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('branchPerformance')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m11 0a2 2 0 01-2 2H7a2 2 0 01-2-2m2-4h2.5M9 16h2.5"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">Branch Performance</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Compare branch statistics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operational Widgets -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-zinc-900 dark:text-white mb-3">⚡ Operational Widgets</h3>
                    
                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('recentMembers')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20a3 3 0 01-3-3v-2a3 3 0 013-3c1.667 0 2.5.833 2.5 2.5S8.667 15 7 15s-2.5-.833-2.5-2.5A3 3 0 017 10z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">Recent Members</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Latest member registrations</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('quickActions')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">Quick Actions</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Shortcuts to common tasks</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-option p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors" 
                         onclick="addWidget('systemStatus')">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-white">System Status</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Monitor system health</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <flux:button variant="outline" onclick="hideAddWidgetModal()">
                    Cancel
                </flux:button>
                <flux:button variant="primary" onclick="resetWidgets()">
                    Reset to Default
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Pass data to JavaScript -->
    <script>
        // Pass chart data to JavaScript
        window.memberGrowthData = @json($memberGrowthData);
        window.transactionVolumeData = @json($transactionVolumeData);
        window.loanStatusData = @json($loanStatusData);
        
        // Branch performance data
        window.branchPerformance = @json($branchPerformance);
        
        // Initialize charts when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.initializeDashboardCharts === 'function') {
                window.initializeDashboardCharts();
            }
        });
        
        // Handle Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            // Ensure data is available and charts are initialized
            setTimeout(() => {
                if (typeof window.initializeDashboardCharts === 'function') {
                    window.initializeDashboardCharts();
                }
            }, 150);
        });
        
        // Debug: Log when dashboard is loaded
        console.log('Dashboard script loaded with chart data:', {
            memberGrowth: window.memberGrowthData?.length || 0,
            transactionVolume: window.transactionVolumeData?.length || 0,
            loanStatus: window.loanStatusData?.length || 0
        });

        // Widget Management System
        window.dashboardWidgets = [];

        // Load saved widgets from localStorage
        loadSavedWidgets();

        function showAddWidgetModal() {
            document.getElementById('addWidgetModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideAddWidgetModal() {
            document.getElementById('addWidgetModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function addWidget(widgetType) {
            if (window.dashboardWidgets.includes(widgetType)) {
                alert('This widget is already added to your dashboard!');
                return;
            }

            window.dashboardWidgets.push(widgetType);
            renderWidget(widgetType);
            saveWidgetsToStorage();
            showCustomWidgetsContainer();
            hideAddWidgetModal();

            // Show success message
            showNotification(`Widget "${getWidgetTitle(widgetType)}" added successfully!`, 'success');
        }

        function removeWidget(widgetType) {
            window.dashboardWidgets = window.dashboardWidgets.filter(w => w !== widgetType);
            document.getElementById(`widget-${widgetType}`)?.remove();
            saveWidgetsToStorage();
            
            if (window.dashboardWidgets.length === 0) {
                hideCustomWidgetsContainer();
            }

            showNotification(`Widget removed successfully!`, 'info');
        }

        function renderWidget(widgetType) {
            const container = document.getElementById('customWidgetsContainer');
            const widgetHtml = getWidgetHtml(widgetType);
            container.insertAdjacentHTML('beforeend', widgetHtml);
        }

        function getWidgetHtml(widgetType) {
            const widgetTitle = getWidgetTitle(widgetType);
            const widgetId = `widget-${widgetType}`;

            switch (widgetType) {
                case 'todayTransactions':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Total Transactions</span>
                                    <span class="font-semibold text-sm sm:text-base text-zinc-900 dark:text-white">{{ $todayTransactions }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Total Amount</span>
                                    <span class="font-semibold text-sm sm:text-base text-emerald-600">KES {{ number_format($todayAmount) }}</span>
                                </div>
                                <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full">
                                    <div class="h-2 bg-blue-500 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                    `;

                case 'pendingApprovals':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-2 sm:p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                        <span class="text-xs sm:text-sm text-zinc-900 dark:text-white">Pending Transactions</span>
                                    </div>
                                    <span class="font-semibold text-sm sm:text-base text-orange-600">{{ $pendingLoans }}</span>
                                </div>
                                <a href="{{ route('transactions.index', ['status' => 'pending']) }}" 
                                   class="block w-full text-center py-2 text-sm sm:text-base bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                    Review Pending
                                </a>
                            </div>
                        </div>
                    `;

                case 'branchPerformance':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="space-y-2">
                                ${window.branchPerformance?.map(branch => `
                                    <div class="flex justify-between items-center py-1 sm:py-2">
                                        <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">${branch.name}</span>
                                        <div class="text-right">
                                            <div class="text-xs sm:text-sm font-medium text-zinc-900 dark:text-white">${branch.members} members</div>
                                            <div class="text-xs text-emerald-600">KES ${branch.deposits.toLocaleString()}</div>
                                        </div>
                                    </div>
                                `).join('') || '<p class="text-xs sm:text-sm text-zinc-500">No branch data available</p>'}
                            </div>
                        </div>
                    `;

                case 'recentMembers':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="space-y-3">
                                <div class="text-xl sm:text-2xl font-bold text-purple-600">{{ $thisMonthMembers }}</div>
                                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">New members this month</p>
                                <a href="{{ route('members.index') }}" 
                                   class="block w-full text-center py-2 text-sm sm:text-base bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                                    View All Members
                                </a>
                            </div>
                        </div>
                    `;

                case 'quickActions':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                <a href="{{ route('transactions.deposit.create') }}" 
                                   class="flex flex-col items-center p-2 sm:p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-xs text-blue-600">Deposit</span>
                                </a>
                                <a href="{{ route('transactions.withdrawal.create') }}" 
                                   class="flex flex-col items-center p-2 sm:p-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    <span class="text-xs text-red-600">Withdraw</span>
                                </a>
                                <a href="{{ route('members.create') }}" 
                                   class="flex flex-col items-center p-2 sm:p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    <span class="text-xs text-emerald-600">New Member</span>
                                </a>
                                <a href="{{ route('reports.index') }}" 
                                   class="flex flex-col items-center p-2 sm:p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-xs text-purple-600">Reports</span>
                                </a>
                            </div>
                        </div>
                    `;

                case 'systemStatus':
                    return `
                        <div id="${widgetId}" class="bg-white dark:bg-zinc-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50 relative">
                            <button onclick="removeWidget('${widgetType}')" class="absolute top-3 right-3 sm:top-4 sm:right-4 text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <h3 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white mb-3 sm:mb-4 pr-8">${widgetTitle}</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">System Status</span>
                                    <span class="flex items-center text-xs sm:text-sm text-emerald-600">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                        Online
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Database</span>
                                    <span class="flex items-center text-xs sm:text-sm text-emerald-600">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                        Healthy
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Uptime</span>
                                    <span class="text-xs sm:text-sm text-zinc-900 dark:text-white">99.9%</span>
                                </div>
                            </div>
                        </div>
                    `;

                default:
                    return `<div>Unknown widget type: ${widgetType}</div>`;
            }
        }

        function getWidgetTitle(widgetType) {
            const titles = {
                'todayTransactions': "Today's Transactions",
                'pendingApprovals': 'Pending Approvals',
                'branchPerformance': 'Branch Performance',
                'recentMembers': 'Recent Members',
                'quickActions': 'Quick Actions',
                'systemStatus': 'System Status'
            };
            return titles[widgetType] || widgetType;
        }

        function showCustomWidgetsContainer() {
            const container = document.getElementById('customWidgetsContainer');
            container.classList.remove('hidden');
            container.classList.add('grid', 'grid-cols-1', 'sm:grid-cols-2', 'lg:grid-cols-3', 'gap-4', 'sm:gap-6');
        }

        function hideCustomWidgetsContainer() {
            const container = document.getElementById('customWidgetsContainer');
            container.classList.add('hidden');
            container.classList.remove('grid', 'grid-cols-1', 'sm:grid-cols-2', 'lg:grid-cols-3', 'gap-4', 'sm:gap-6');
        }

        function saveWidgetsToStorage() {
            localStorage.setItem('saccocore_dashboard_widgets', JSON.stringify(window.dashboardWidgets));
        }

        function loadSavedWidgets() {
            const saved = localStorage.getItem('saccocore_dashboard_widgets');
            if (saved) {
                window.dashboardWidgets = JSON.parse(saved);
                if (window.dashboardWidgets.length > 0) {
                    showCustomWidgetsContainer();
                    window.dashboardWidgets.forEach(widgetType => {
                        renderWidget(widgetType);
                    });
                }
            }
        }

        function resetWidgets() {
            if (confirm('Are you sure you want to reset all widgets to default? This will remove all custom widgets.')) {
                window.dashboardWidgets = [];
                document.getElementById('customWidgetsContainer').innerHTML = '';
                hideCustomWidgetsContainer();
                saveWidgetsToStorage();
                hideAddWidgetModal();
                showNotification('Widgets reset to default!', 'info');
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-3 sm:p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full max-w-xs sm:max-w-sm`;
            
            const bgColor = type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.className += ` ${bgColor} text-white`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <span class="text-sm sm:text-base">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 p-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Close modal when clicking outside
        document.getElementById('addWidgetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddWidgetModal();
            }
        });

        // Make functions global
        window.showAddWidgetModal = showAddWidgetModal;
        window.hideAddWidgetModal = hideAddWidgetModal;
        window.addWidget = addWidget;
        window.removeWidget = removeWidget;
        window.resetWidgets = resetWidgets;
    </script>
</x-layouts.app>