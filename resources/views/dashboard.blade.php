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
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                        Dashboard
                    </h1>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-1">
                        Track your SACCO's performance and key metrics
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <flux:button variant="outline" size="sm" icon="funnel">
                        Filters
                    </flux:button>
                    <flux:button variant="primary" size="sm" icon="plus">
                        Add Widget
                    </flux:button>
                </div>
            </div>

            <!-- Admin/Manager Dashboard -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Members Overview -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Member overview</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                                    {{ number_format($totalMembers) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">Total members</div>
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
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active sales</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                                    KES {{ number_format($thisMonthTransactions) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">vs last month</div>
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
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Loan Revenue</span>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                                    KES {{ number_format($totalLoanAmount) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">vs last month</div>
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
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Performance</span>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Analytics Chart -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Analytics</h3>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <select class="text-sm border-0 bg-transparent text-zinc-600 dark:text-zinc-400 focus:ring-0">
                                <option>This year</option>
                                <option>Last year</option>
                            </select>
                            <flux:button variant="outline" size="sm" icon="funnel">Filters</flux:button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                            KES -{{ number_format(4530) }} <span class="text-sm font-normal text-red-500">sales +6.04%</span>
                        </div>
                    </div>
                    
                    <div class="h-64 flex items-end space-x-2">
                        @php
                            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG'];
                            $heights = [40, 60, 45, 70, 35, 80, 90, 60];
                        @endphp
                        @foreach($months as $index => $month)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-gradient-to-t from-orange-500/20 to-orange-500/60 rounded-t" 
                                     style="height: {{ $heights[$index] }}%"></div>
                                <span class="text-xs text-zinc-500 mt-2">{{ $month }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Additional Analytics -->
                <div class="space-y-6">
                    <!-- Conversion Rate -->
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-zinc-900 dark:text-white">0.73%</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center">
                                    Conversion rate 
                                    <span class="ml-2 text-emerald-600 flex items-center">
                                        <flux:icon.arrow-up class="w-3 h-3" />
                                        +1.3%
                                    </span>
                                </div>
                            </div>
                            <flux:icon.information-circle class="w-4 h-4 text-zinc-400" />
                        </div>
                    </div>

                    <!-- Total Visits -->
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Total visits by hourly</h3>
                                <flux:icon.information-circle class="w-4 h-4 text-zinc-400 inline" />
                            </div>
                            <flux:icon.ellipsis-horizontal class="w-5 h-5 text-zinc-400" />
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-2xl font-bold text-zinc-900 dark:text-white mb-1">
                                288,822 <span class="text-sm font-normal text-emerald-600">+2.4%</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            @php
                                $days = [
                                    ['day' => 'MON', 'time' => '3:00-9:00 AM', 'color' => 'bg-orange-500', 'width' => '75%'],
                                    ['day' => 'TUE', 'time' => '', 'color' => 'bg-orange-300', 'width' => '45%'],
                                    ['day' => 'WED', 'time' => '', 'color' => 'bg-orange-500', 'width' => '85%']
                                ];
                            @endphp
                            @foreach($days as $day)
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-zinc-500 w-8">{{ $day['day'] }}</span>
                                    <div class="flex-1 bg-zinc-100 dark:bg-zinc-700 rounded-full h-6 flex items-center">
                                        <div class="{{ $day['color'] }} h-4 rounded-full ml-1" style="width: {{ $day['width'] }}"></div>
                                    </div>
                                                                         @if($day['time'])
                                         <span class="text-xs text-zinc-500">{{ $day['time'] }}</span>
                                         <flux:icon.x-mark class="w-3 h-3 text-zinc-400" />
                                     @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Services -->
            <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center">
                            Top Services
                            <flux:icon.information-circle class="w-4 h-4 text-zinc-400 ml-2" />
                        </h3>
                    </div>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                        See Details
                        <flux:icon.arrow-right class="w-3 h-3 ml-1" />
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- My Savings -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">My Savings</h3>
                    <div class="text-3xl font-bold text-emerald-600 mb-2">
                        KES {{ number_format($userTotalSavings) }}
                    </div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $userAccounts ? $userAccounts->count() : 0 }} account(s)
                    </p>
                </div>

                <!-- Active Loan -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Active Loan</h3>
                    @if($userActiveLoan)
                        <div class="text-3xl font-bold text-orange-600 mb-2">
                            KES {{ number_format($userActiveLoan->amount) }}
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ ucfirst($userActiveLoan->status) }}
                        </p>
                    @else
                        <div class="text-2xl font-bold text-zinc-400 mb-2">No active loan</div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Apply for a loan</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-sm border border-zinc-200/50 dark:border-zinc-700/50">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <flux:button variant="outline" class="w-full justify-start" icon="plus">
                            New Deposit
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start" icon="minus">
                            Withdrawal
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start" icon="credit-card">
                            Apply for Loan
                        </flux:button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Include Chart.js for charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // You can add Chart.js initialization here for more dynamic charts
        // This would replace the static chart elements with actual interactive charts
    </script>
</x-layouts.app>