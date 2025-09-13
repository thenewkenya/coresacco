<?php

use App\Models\User;
use App\Models\Account;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        $user = auth()->user();
        
        // Cache dashboard data for 5 minutes
        $cacheKey = 'dashboard_data_' . $user->id . '_' . $user->role;
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($user) {
            // Get real-time statistics with optimized queries
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
            $memberGrowth = 12.8;

            // Pending approvals (for staff/managers)
            $pendingLoans = Loan::where('status', 'pending')->count();

            // Member-specific data
            $userAccounts = null;
            $userLoans = null;
            $userTotalSavings = 0;
            $userActiveLoan = null;

            if ($user->role === 'member') {
                $userAccounts = Account::where('member_id', $user->id)->get();
                $userLoans = Loan::where('member_id', $user->id)->with('loanType')->get();
                $userTotalSavings = $userAccounts->where('account_type', 'savings')->sum('balance');
                $userActiveLoan = $userLoans->whereIn('status', ['active', 'disbursed'])->first();
            }

            return [
                'user' => $user,
                'totalMembers' => $totalMembers,
                'totalAssets' => $totalAssets,
                'activeLoans' => $activeLoans,
                'totalLoanAmount' => $totalLoanAmount,
                'todayTransactions' => $todayTransactions,
                'todayAmount' => $todayAmount,
                'thisMonthTransactions' => $thisMonthTransactions,
                'lastMonthTransactions' => $lastMonthTransactions,
                'transactionGrowth' => $transactionGrowth,
                'thisMonthMembers' => $thisMonthMembers,
                'memberGrowth' => $memberGrowth,
                'pendingLoans' => $pendingLoans,
                'userAccounts' => $userAccounts,
                'userLoans' => $userLoans,
                'userTotalSavings' => $userTotalSavings,
                'userActiveLoan' => $userActiveLoan,
            ];
        });
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Dashboard</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Track your SACCO's performance and key metrics</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" icon="funnel">
                Filters
            </flux:button>
            <flux:button variant="primary" icon="plus">
                Add Widget
            </flux:button>
        </div>
    </div>

    <!-- Admin/Manager Dashboard -->
    @if(in_array($user->role, ['admin', 'manager']))
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Members</flux:subheading>
                        <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalMembers) }}</flux:heading>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-emerald-600">
                    <flux:icon.arrow-up class="w-4 h-4 mr-1" />
                    <span class="text-sm">+{{ number_format($memberGrowth, 1) }}%</span>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Assets</flux:subheading>
                        <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">KES {{ number_format($totalAssets) }}</flux:heading>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                        <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-emerald-600">
                    <flux:icon.arrow-up class="w-4 h-4 mr-1" />
                    <span class="text-sm">+{{ number_format($transactionGrowth, 1) }}%</span>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Active Loans</flux:subheading>
                        <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($activeLoans) }}</flux:heading>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-emerald-600">
                    <flux:icon.arrow-up class="w-4 h-4 mr-1" />
                    <span class="text-sm">+9.7%</span>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Pending Loans</flux:subheading>
                        <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($pendingLoans) }}</flux:heading>
                    </div>
                    <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                        <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-amber-600">
                    <flux:icon.arrow-up class="w-4 h-4 mr-1" />
                    <span class="text-sm">Needs Review</span>
                </div>
            </div>
        </div>

        <!-- Analytics and Performance Charts -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Transaction Volume Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Transaction Volume</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Last 30 days</flux:subheading>
                    </div>
                    <div class="flex items-center gap-3">
                        <flux:select size="sm">
                            <option>Last 30 days</option>
                            <option>Last 7 days</option>
                            <option>This month</option>
                        </flux:select>
                        <flux:button variant="outline" size="sm" icon="funnel">Filters</flux:button>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                        KES {{ number_format($thisMonthTransactions) }}
                    </div>
                    <div class="flex items-center text-{{ $transactionGrowth >= 0 ? 'emerald' : 'red' }}-600">
                        <flux:icon.arrow-{{ $transactionGrowth >= 0 ? 'up' : 'down' }} class="w-4 h-4 mr-1" />
                        <span class="text-sm">{{ $transactionGrowth >= 0 ? '+' : '' }}{{ number_format($transactionGrowth, 1) }}% vs last month</span>
                    </div>
                </div>
                
                <div class="h-64 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                    <span class="text-zinc-500 dark:text-zinc-400">Chart placeholder</span>
                </div>
            </div>

            <!-- Member Growth Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Member Growth</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Last 30 days</flux:subheading>
                    </div>
                    <flux:icon.ellipsis-horizontal class="w-5 h-5 text-zinc-400" />
                </div>
                
                <div class="mb-6">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                        {{ number_format($totalMembers) }}
                    </div>
                    <div class="flex items-center text-emerald-600">
                        <flux:icon.arrow-up class="w-4 h-4 mr-1" />
                        <span class="text-sm">+{{ number_format($memberGrowth, 1) }}% this month</span>
                    </div>
                </div>
                
                <div class="h-64 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                    <span class="text-zinc-500 dark:text-zinc-400">Chart placeholder</span>
                </div>
            </div>
        </div>

        <!-- Top Performing Services -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Top Services</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Performance overview</flux:subheading>
                </div>
                <flux:button variant="outline" size="sm" :href="route('reports.index')">
                    See Details
                    <flux:icon.arrow-right class="w-4 h-4 ml-1" />
                </flux:button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Service</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Transactions</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Revenue</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Growth</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <flux:icon.banknotes class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Savings Account</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Primary service</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">1,247</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">KES 2.8M</td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-600 flex items-center">
                                    <flux:icon.arrow-up class="w-3 h-3 mr-1" />
                                    +12%
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge variant="success" size="sm">Active</flux:badge>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <flux:icon.credit-card class="w-4 h-4 text-purple-600" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Loans</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Credit facility</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">423</td>
                            <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100">KES 1.2M</td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-600 flex items-center">
                                    <flux:icon.arrow-up class="w-3 h-3 mr-1" />
                                    +8%
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge variant="success" size="sm">Active</flux:badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Member Dashboard -->
    @if($user->role === 'member')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- My Savings -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">My Savings</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">{{ $userAccounts ? $userAccounts->count() : 0 }} account(s)</flux:subheading>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                        <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="text-3xl font-bold text-emerald-600">
                    KES {{ number_format($userTotalSavings) }}
                </div>
            </div>

            <!-- Active Loan -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Active Loan</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">
                            @if($userActiveLoan)
                                {{ ucfirst($userActiveLoan->status) }}
                            @else
                                No active loan
                            @endif
                        </flux:subheading>
                    </div>
                    <div class="p-3 bg-{{ $userActiveLoan ? 'orange' : 'zinc' }}-100 dark:bg-{{ $userActiveLoan ? 'orange' : 'zinc' }}-900/20 rounded-lg">
                        <flux:icon.credit-card class="w-6 h-6 text-{{ $userActiveLoan ? 'orange' : 'zinc' }}-600 dark:text-{{ $userActiveLoan ? 'orange' : 'zinc' }}-400" />
                    </div>
                </div>
                @if($userActiveLoan)
                    <div class="text-3xl font-bold text-orange-600">
                        KES {{ number_format($userActiveLoan->amount) }}
                    </div>
                @else
                    <div class="text-2xl font-bold text-zinc-400">
                        No active loan
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Quick Actions</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Common tasks</flux:subheading>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon.bolt class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="space-y-3">
                    <flux:button variant="outline" class="w-full justify-start" icon="plus" :href="route('transactions.deposit.create')">
                        New Deposit
                    </flux:button>
                    <flux:button variant="outline" class="w-full justify-start" icon="minus" :href="route('transactions.withdrawal.create')">
                        Withdrawal
                    </flux:button>
                    <flux:button variant="outline" class="w-full justify-start" icon="credit-card" :href="route('loans.apply')">
                        Apply for Loan
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>