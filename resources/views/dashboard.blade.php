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
                        @if(auth()->user()->role !== 'member')
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                {{ ucfirst(auth()->user()->role) }} Dashboard • {{ auth()->user()->branch->name ?? 'Head Office' }}
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Live Data</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ now()->format('g:i A') }}</p>
                        </div>
                        <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white relative">
                            <flux:icon.bell class="w-5 h-5" />
                            @if($pendingLoans > 0 && in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $pendingLoans }}</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Admin Dashboard -->
            @if(auth()->user()->role === 'admin')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Members -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Live</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalMembers) }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Active members') }}</p>
                    </div>
                </div>

                <!-- Total Assets -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">KES</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Assets') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalAssets, 0) }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('All accounts combined') }}</p>
                    </div>
                </div>

                <!-- Active Loans -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">{{ $activeLoans }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Loan Portfolio') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalLoanAmount, 0) }}</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Outstanding amount') }}</p>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        @if($pendingLoans > 0)
                            <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Urgent</span>
                        @else
                            <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Clear</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Pending Loans') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $pendingLoans }}</p>
                        <p class="text-xs {{ $pendingLoans > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $pendingLoans > 0 ? __('Require approval') : __('All caught up') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Staff/Manager Dashboard -->
            @if(in_array(auth()->user()->role, ['staff', 'manager']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Today's Transactions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.arrows-right-left class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Today</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Transactions Today') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $todayTransactions }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Total count') }}</p>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        @if($pendingLoans > 0)
                            <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Action</span>
                        @else
                            <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Clear</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Pending Approvals') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $pendingLoans }}</p>
                        <p class="text-xs {{ $pendingLoans > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $pendingLoans > 0 ? __('Awaiting review') : __('Up to date') }}
                        </p>
                    </div>
                </div>

                <!-- Today's Volume -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">KES</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Volume Today') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($todayAmount, 0) }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Total processed') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Member Dashboard -->
            @if(auth()->user()->role === 'member')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Savings Balance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ $userAccounts->where('account_type', 'savings')->count() }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Savings') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($userTotalSavings, 2) }}</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Across all accounts') }}</p>
                    </div>
                </div>

                <!-- Active Loan -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        @if($userActiveLoan)
                            <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Active</span>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">None</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Current Loan') }}</p>
                        @if($userActiveLoan)
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($userActiveLoan->amount, 0) }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ $userActiveLoan->loanType->name }}</p>
                        @else
                            <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">--</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('No active loans') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Shares -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.sparkles class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Shares</span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Share Capital') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($userAccounts->where('account_type', 'shares')->sum('balance'), 0) }}</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Current value') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Member Growth Chart -->
                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Member Growth (30 days)') }}</h3>
                        <span class="text-sm text-emerald-600 dark:text-emerald-400">{{ $memberGrowthData->sum('count') }} new members</span>
                    </div>
                    <div class="h-64">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                </div>

                <!-- Transaction Volume Chart -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Transaction Volume (30 days)') }}</h3>
                        <span class="text-sm text-blue-600 dark:text-blue-400">KES {{ number_format($transactionVolumeData->sum('total'), 0) }}</span>
                    </div>
                    <div class="h-64">
                        <canvas id="transactionVolumeChart"></canvas>
                    </div>
                </div>
                @endif

                <!-- Loan Status Distribution -->
                @if(auth()->user()->role === 'admin')
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Loan Status Distribution') }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $loanStatusData->sum('count') }} total loans</span>
                    </div>
                    <div class="h-64">
                        <canvas id="loanStatusChart"></canvas>
                    </div>
                </div>

                <!-- Branch Performance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Branch Performance') }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $branchPerformance->count() }} branches</span>
                    </div>
                    <div class="space-y-4">
                        @foreach($branchPerformance as $branch)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['name'] }}</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch['city'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $branch['members'] }} members</p>
                                    <p class="text-sm text-emerald-600 dark:text-emerald-400">KES {{ number_format($branch['deposits'], 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Member-specific charts -->
                @if(auth()->user()->role === 'member')
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('My Account Breakdown') }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $userAccounts->count() }} accounts</span>
                    </div>
                    <div class="space-y-4">
                        @foreach($userAccounts as $account)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 rounded-lg 
                                        @if($account->account_type === 'savings') bg-emerald-100 dark:bg-emerald-900/20
                                        @elseif($account->account_type === 'shares') bg-purple-100 dark:bg-purple-900/20
                                        @else bg-blue-100 dark:bg-blue-900/20 @endif">
                                        @if($account->account_type === 'savings')
                                            <flux:icon.banknotes class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                        @elseif($account->account_type === 'shares')
                                            <flux:icon.sparkles class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                        @else
                                            <flux:icon.document-text class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($account->account_type) }}</h4>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $account->account_number }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($account->balance, 2) }}</p>
                                    <p class="text-sm text-emerald-600 dark:text-emerald-400">{{ ucfirst($account->status) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Activity') }}</h3>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>
                    <div class="space-y-3">
                        @foreach(Transaction::where('member_id', auth()->id())->with('account')->latest()->take(5)->get() as $transaction)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex items-center space-x-3">
                                    <div class="p-1 rounded-full 
                                        @if($transaction->type === 'deposit') bg-emerald-100 dark:bg-emerald-900/20
                                        @elseif($transaction->type === 'withdrawal') bg-red-100 dark:bg-red-900/20
                                        @else bg-blue-100 dark:bg-blue-900/20 @endif">
                                        @if($transaction->type === 'deposit')
                                            <flux:icon.arrow-down class="h-3 w-3 text-emerald-600 dark:text-emerald-400" />
                                        @elseif($transaction->type === 'withdrawal')
                                            <flux:icon.arrow-up class="h-3 w-3 text-red-600 dark:text-red-400" />
                                        @else
                                            <flux:icon.arrows-right-left class="h-3 w-3 text-blue-600 dark:text-blue-400" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $transaction->description }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('M j, g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium 
                                        @if($transaction->type === 'deposit') text-emerald-600 dark:text-emerald-400
                                        @elseif($transaction->type === 'withdrawal') text-red-600 dark:text-red-400
                                        @else text-blue-600 dark:text-blue-400 @endif">
                                        @if($transaction->type === 'deposit')+@elseif($transaction->type === 'withdrawal')-@endif
                                        {{ number_format($transaction->amount, 0) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Quick Actions') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if(auth()->user()->role === 'member')
                        <a href="/member/my-savings" class="flex flex-col items-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                            <flux:icon.banknotes class="h-8 w-8 text-emerald-600 dark:text-emerald-400 mb-2" />
                            <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">View Savings</span>
                        </a>
                        <a href="/member/my-loans" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <flux:icon.credit-card class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-2" />
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">My Loans</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <flux:icon.arrow-up class="h-8 w-8 text-purple-600 dark:text-purple-400 mb-2" />
                            <span class="text-sm font-medium text-purple-900 dark:text-purple-100">Transfer</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                            <flux:icon.document-plus class="h-8 w-8 text-amber-600 dark:text-amber-400 mb-2" />
                            <span class="text-sm font-medium text-amber-900 dark:text-amber-100">Apply Loan</span>
                        </a>
                    @elseif(in_array(auth()->user()->role, ['staff', 'manager']))
                        <a href="#" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <flux:icon.credit-card class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-2" />
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Process Payment</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                            <flux:icon.clock class="h-8 w-8 text-amber-600 dark:text-amber-400 mb-2" />
                            <span class="text-sm font-medium text-amber-900 dark:text-amber-100">Approvals</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                            <flux:icon.user-plus class="h-8 w-8 text-emerald-600 dark:text-emerald-400 mb-2" />
                            <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">New Member</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <flux:icon.document-text class="h-8 w-8 text-purple-600 dark:text-purple-400 mb-2" />
                            <span class="text-sm font-medium text-purple-900 dark:text-purple-100">Reports</span>
                        </a>
                    @else
                        <a href="#" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <flux:icon.chart-bar class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-2" />
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Analytics</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                            <flux:icon.cog class="h-8 w-8 text-emerald-600 dark:text-emerald-400 mb-2" />
                            <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">Settings</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <flux:icon.building-office-2 class="h-8 w-8 text-purple-600 dark:text-purple-400 mb-2" />
                            <span class="text-sm font-medium text-purple-900 dark:text-purple-100">Branches</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                            <flux:icon.user-group class="h-8 w-8 text-amber-600 dark:text-amber-400 mb-2" />
                            <span class="text-sm font-medium text-amber-900 dark:text-amber-100">Users</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    @if(in_array(auth()->user()->role, ['admin', 'manager']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        // Error handling for Chart.js
        if (typeof Chart === 'undefined') {
            console.error('Chart.js failed to load. Charts will not be displayed.');
        }
    </script>
    <script>
        // Chart colors
        const colors = {
            primary: document.documentElement.classList.contains('dark') ? '#3b82f6' : '#2563eb',
            secondary: document.documentElement.classList.contains('dark') ? '#10b981' : '#059669',
            accent: document.documentElement.classList.contains('dark') ? '#8b5cf6' : '#7c3aed',
            warning: document.documentElement.classList.contains('dark') ? '#f59e0b' : '#d97706',
            danger: document.documentElement.classList.contains('dark') ? '#ef4444' : '#dc2626',
            text: document.documentElement.classList.contains('dark') ? '#f4f4f5' : '#27272a',
            grid: document.documentElement.classList.contains('dark') ? '#3f3f46' : '#e4e4e7'
        };

        // Member Growth Chart
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
        const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
        new Chart(memberGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($memberGrowthData->pluck('date')),
                datasets: [{
                    label: 'New Members',
                    data: @json($memberGrowthData->pluck('count')),
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: colors.text }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: colors.text },
                        grid: { color: colors.grid }
                    },
                    y: {
                        ticks: { color: colors.text },
                        grid: { color: colors.grid },
                        beginAtZero: true
                    }
                }
            }
        });

        // Transaction Volume Chart
        const transactionVolumeCtx = document.getElementById('transactionVolumeChart').getContext('2d');
        new Chart(transactionVolumeCtx, {
            type: 'bar',
            data: {
                labels: @json($transactionVolumeData->pluck('date')),
                datasets: [{
                    label: 'Volume (KES)',
                    data: @json($transactionVolumeData->pluck('total')),
                    backgroundColor: colors.secondary + '80',
                    borderColor: colors.secondary,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: colors.text }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: colors.text },
                        grid: { color: colors.grid }
                    },
                    y: {
                        ticks: { 
                            color: colors.text,
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        },
                        grid: { color: colors.grid },
                        beginAtZero: true
                    }
                }
            }
        });
        @endif

        // Loan Status Chart (Admin only)
        @if(auth()->user()->role === 'admin')
        const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
        new Chart(loanStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($loanStatusData->pluck('status')->map(fn($status) => ucfirst($status))),
                datasets: [{
                    data: @json($loanStatusData->pluck('count')),
                    backgroundColor: [
                        colors.secondary,
                        colors.primary,
                        colors.warning,
                        colors.accent,
                        colors.danger
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: colors.text }
                    }
                }
            }
        });
        @endif
    </script>
    @endif
</x-layouts.app>