<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Cache duration for dashboard statistics (15 minutes)
     */
    private const CACHE_DURATION = 900;

    /**
     * Cache duration for real-time stats (5 minutes)
     */
    private const REALTIME_CACHE_DURATION = 300;

    /**
     * Get main dashboard statistics
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $cacheKey = "dashboard_stats";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return [
                'total_members' => User::count(),
                'active_accounts' => Account::where('status', 'active')->count(),
                'total_deposits' => Account::sum('balance'),
                'active_loans' => Loan::where('status', 'active')->sum('amount'),
                'pending_loans' => Loan::where('status', 'pending')->count(),
                'monthly_deposits' => Transaction::where('type', 'deposit')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'monthly_withdrawals' => Transaction::where('type', 'withdrawal')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'loan_recovery_rate' => $this->calculateLoanRecoveryRate(),
            ];
        });
    }

    /**
     * Get today's transaction summary
     *
     * @return array
     */
    public function getTodayStats(): array
    {
        $cacheKey = "today_stats:" . now()->format('Y-m-d');

        return Cache::remember($cacheKey, self::REALTIME_CACHE_DURATION, function () {
            $today = now()->format('Y-m-d');

            return [
                'todays_deposits' => Transaction::where('type', 'deposit')
                    ->whereDate('created_at', $today)
                    ->sum('amount'),
                'todays_withdrawals' => Transaction::where('type', 'withdrawal')
                    ->whereDate('created_at', $today)
                    ->sum('amount'),
                'todays_transactions' => Transaction::whereDate('created_at', $today)->count(),
                'new_members_today' => User::whereDate('created_at', $today)->count(),
                'loan_applications_today' => Loan::whereDate('created_at', $today)->count(),
            ];
        });
    }

    /**
     * Get member-specific dashboard stats
     *
     * @param int $memberId
     * @return array
     */
    public function getMemberDashboardStats(int $memberId): array
    {
        $cacheKey = "member_dashboard_stats:{$memberId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($memberId) {
            $accounts = Account::where('member_id', $memberId)->get();
            $loans = Loan::where('member_id', $memberId)->get();

            return [
                'total_balance' => $accounts->sum('balance'),
                'active_accounts' => $accounts->where('status', 'active')->count(),
                'active_loans' => $loans->where('status', 'active')->count(),
                'total_loan_amount' => $loans->where('status', 'active')->sum('amount'),
                'monthly_deposits' => Transaction::where('account_id', $accounts->pluck('id'))
                    ->where('type', 'deposit')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'monthly_withdrawals' => Transaction::where('account_id', $accounts->pluck('id'))
                    ->where('type', 'withdrawal')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'recent_transactions' => Transaction::where('account_id', $accounts->pluck('id'))
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        });
    }

    /**
     * Get financial summary statistics
     *
     * @return array
     */
    public function getFinancialSummary(): array
    {
        $cacheKey = "financial_summary";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return [
                'total_assets' => Account::sum('balance'),
                'total_liabilities' => Loan::where('status', 'active')->sum('remaining_balance'),
                'monthly_revenue' => $this->calculateMonthlyRevenue(),
                'interest_earned' => $this->calculateInterestEarned(),
                'loan_portfolio' => Loan::where('status', 'active')->sum('amount'),
                'savings_portfolio' => Account::where('account_type', 'savings')->sum('balance'),
                'member_growth_rate' => $this->calculateMemberGrowthRate(),
            ];
        });
    }

    /**
     * Get transaction trends for charts
     *
     * @param int $days
     * @return array
     */
    public function getTransactionTrends(int $days = 30): array
    {
        $cacheKey = "transaction_trends:{$days}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($days) {
            $startDate = now()->subDays($days);

            $deposits = Transaction::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('type', 'deposit')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $withdrawals = Transaction::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('type', 'withdrawal')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'net_flow' => $deposits->sum('total') - $withdrawals->sum('total'),
            ];
        });
    }

    /**
     * Calculate loan recovery rate
     *
     * @return float
     */
    private function calculateLoanRecoveryRate(): float
    {
        $totalLoansIssued = Loan::whereIn('status', ['active', 'completed'])->sum('amount');
        $totalRepayments = Loan::where('status', 'completed')->sum('amount');

        return $totalLoansIssued > 0 ? ($totalRepayments / $totalLoansIssued) * 100 : 0;
    }

    /**
     * Calculate monthly revenue from fees and interest
     *
     * @return float
     */
    private function calculateMonthlyRevenue(): float
    {
        return Transaction::where('type', 'fee')
            ->whereMonth('created_at', now()->month)
            ->sum('amount') +
            Loan::where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->sum('interest_amount');
    }

    /**
     * Calculate total interest earned
     *
     * @return float
     */
    private function calculateInterestEarned(): float
    {
        return Loan::whereIn('status', ['active', 'completed'])->sum('interest_amount');
    }

    /**
     * Calculate member growth rate (percentage)
     *
     * @return float
     */
    private function calculateMemberGrowthRate(): float
    {
        $currentMonth = User::whereMonth('created_at', now()->month)->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();

        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Clear all dashboard cache
     *
     * @return void
     */
    public function clearDashboardCache(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('financial_summary');
        Cache::forget('today_stats:' . now()->format('Y-m-d'));
        
        // Clear transaction trends cache
        for ($days = 7; $days <= 365; $days += 7) {
            Cache::forget("transaction_trends:{$days}");
        }
    }

    /**
     * Clear member-specific dashboard cache
     *
     * @param int $memberId
     * @return void
     */
    public function clearMemberDashboardCache(int $memberId): void
    {
        Cache::forget("member_dashboard_stats:{$memberId}");
    }
} 