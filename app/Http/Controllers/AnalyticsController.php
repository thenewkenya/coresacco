<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Budget;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Get time period filter (default to last 12 months)
        $period = $request->get('period', '12months');
        $startDate = $this->getStartDate($period);
        
        // Get overview metrics
        $overview = $this->getOverviewMetrics();
        
        // Get financial analytics
        $financial = $this->getFinancialAnalytics($startDate);
        
        // Get member analytics
        $members = $this->getMemberAnalytics($startDate);
        
        // Get operational analytics
        $operations = $this->getOperationalAnalytics($startDate);
        
        // Get trend data for charts
        $trends = $this->getTrendData($startDate);
        
        // Get advanced analytics
        $advanced = $this->getAdvancedAnalytics($startDate);
        
        return view('analytics.index', compact(
            'overview',
            'financial', 
            'members',
            'operations',
            'trends',
            'advanced',
            'period'
        ));
    }

    private function getOverviewMetrics()
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();
        
        // Active members
        $activeMembers = User::where('membership_status', 'active')->count();
        $lastMonthMembers = User::where('membership_status', 'active')
            ->where('created_at', '<=', $lastMonth)
            ->count();
        $memberGrowth = $lastMonthMembers > 0 ? 
            (($activeMembers - $lastMonthMembers) / $lastMonthMembers) * 100 : 0;
        
        // Total assets
        $totalAssets = Account::where('status', 'active')->sum('balance');
        $lastMonthAssets = DB::table('transactions')
            ->where('created_at', '<=', $lastMonth)
            ->where('status', 'completed')
            ->sum('balance_after');
        $assetGrowth = $lastMonthAssets > 0 ? 
            (($totalAssets - $lastMonthAssets) / $lastMonthAssets) * 100 : 0;
        
        // Active loans
        $activeLoans = Loan::whereIn('status', ['disbursed', 'active'])->count();
        $lastMonthLoans = Loan::whereIn('status', ['disbursed', 'active'])
            ->where('created_at', '<=', $lastMonth)
            ->count();
        $loanGrowth = $lastMonthLoans > 0 ? 
            (($activeLoans - $lastMonthLoans) / $lastMonthLoans) * 100 : 0;
        
        // Portfolio performance (loan recovery rate)
        $totalLoanValue = Loan::whereIn('status', ['disbursed', 'active', 'completed'])->sum('amount');
        $completedLoans = Loan::where('status', 'completed')->sum('amount');
        $portfolioPerformance = $totalLoanValue > 0 ? 
            ($completedLoans / $totalLoanValue) * 100 : 0;
        $lastMonthPerformance = 90; // Placeholder for comparison
        $performanceChange = $portfolioPerformance - $lastMonthPerformance;
        
        return [
            'active_members' => [
                'value' => $activeMembers,
                'change' => round($memberGrowth, 1),
                'trend' => $memberGrowth >= 0 ? 'up' : 'down'
            ],
            'total_assets' => [
                'value' => $totalAssets,
                'change' => round($assetGrowth, 1),
                'trend' => $assetGrowth >= 0 ? 'up' : 'down'
            ],
            'active_loans' => [
                'value' => $activeLoans,
                'change' => round($loanGrowth, 1),
                'trend' => $loanGrowth >= 0 ? 'up' : 'down'
            ],
            'portfolio_performance' => [
                'value' => round($portfolioPerformance, 1),
                'change' => round($performanceChange, 1),
                'trend' => $performanceChange >= 0 ? 'up' : 'down'
            ]
        ];
    }

    private function getFinancialAnalytics($startDate)
    {
        // Asset growth trend
        $assetGrowth = DB::table('transactions')
            ->selectRaw('DATE(created_at) as date, SUM(CASE WHEN type IN ("deposit", "loan_disbursement", "interest") THEN amount ELSE -amount END) as net_change')
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Account type distribution
        $accountDistribution = Account::selectRaw('account_type, COUNT(*) as count, SUM(balance) as total_balance')
            ->where('status', 'active')
            ->groupBy('account_type')
            ->get();
        
        // Loan portfolio analysis
        $loanPortfolio = [
            'total_disbursed' => Loan::whereIn('status', ['disbursed', 'active', 'completed'])->sum('amount'),
            'active_loans' => Loan::whereIn('status', ['disbursed', 'active'])->sum('amount'),
            'completed_loans' => Loan::where('status', 'completed')->sum('amount'),
            'defaulted_loans' => Loan::where('status', 'defaulted')->sum('amount'),
        ];
        
        // Revenue streams
        $revenueStreams = [
            'loan_interest' => Transaction::where('type', 'interest')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'fees' => Transaction::where('type', 'fee')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
        ];
        
        return [
            'asset_growth' => $assetGrowth,
            'account_distribution' => $accountDistribution,
            'loan_portfolio' => $loanPortfolio,
            'revenue_streams' => $revenueStreams
        ];
    }

    private function getMemberAnalytics($startDate)
    {
        // Member growth over time
        $memberGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as new_members')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Member engagement metrics
        $engagement = [
            'active_savers' => Account::where('account_type', 'savings')
                ->where('balance', '>', 0)
                ->distinct('member_id')
                ->count('member_id'),
            'loan_members' => Loan::whereIn('status', ['disbursed', 'active'])
                ->distinct('member_id')
                ->count('member_id'),
            'budget_users' => Budget::distinct('user_id')->count('user_id'),
            'goal_setters' => Goal::distinct('member_id')->count('member_id')
        ];
        
        // Savings patterns
        $savingsPatterns = DB::table('transactions')
            ->selectRaw('MONTH(created_at) as month, AVG(amount) as avg_deposit')
            ->where('type', 'deposit')
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Member demographics
        $demographics = [
            'by_status' => User::selectRaw('membership_status, COUNT(*) as count')
                ->groupBy('membership_status')
                ->get(),
            'by_branch' => User::selectRaw('branch_id, COUNT(*) as count')
                ->groupBy('branch_id')
                ->get()
        ];
        
        return [
            'growth' => $memberGrowth,
            'engagement' => $engagement,
            'savings_patterns' => $savingsPatterns,
            'demographics' => $demographics
        ];
    }

    private function getOperationalAnalytics($startDate)
    {
        // Transaction volume
        $transactionVolume = Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total_amount')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Processing efficiency
        $efficiency = [
            'avg_processing_time' => Transaction::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, updated_at)')),
            'success_rate' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->count() / max(1, Transaction::where('created_at', '>=', $startDate)->count()) * 100
        ];
        
        // Service utilization
        $serviceUtilization = [
            'loan_applications' => Loan::where('created_at', '>=', $startDate)->count(),
            'account_openings' => Account::where('created_at', '>=', $startDate)->count(),
            'budget_creations' => Budget::where('created_at', '>=', $startDate)->count(),
            'goal_settings' => Goal::where('created_at', '>=', $startDate)->count()
        ];
        
        return [
            'transaction_volume' => $transactionVolume,
            'efficiency' => $efficiency,
            'service_utilization' => $serviceUtilization
        ];
    }

    private function getTrendData($startDate)
    {
        // Monthly financial trends
        $monthlyTrends = [];
        $current = Carbon::parse($startDate);
        $end = now();
        
        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $deposits = Transaction::where('type', 'deposit')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $loans = Loan::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $members = User::whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $monthlyTrends[] = [
                'month' => $current->format('M Y'),
                'deposits' => $deposits,
                'loans' => $loans,
                'new_members' => $members
            ];
            
            $current->addMonth();
        }
        
        return $monthlyTrends;
    }

    private function getAdvancedAnalytics($startDate)
    {
        return [
            'financial' => $this->getFinancialAnalyticsAdvanced($startDate),
            'member' => $this->getMemberAnalyticsAdvanced($startDate),
            'operational' => $this->getOperationalAnalyticsAdvanced($startDate)
        ];
    }

    private function getFinancialAnalyticsAdvanced($startDate)
    {
        // Asset Growth Trends
        $assetGrowthTrend = DB::table('accounts')
            ->selectRaw('DATE(created_at) as date, account_type, SUM(balance) as total_balance')
            ->where('created_at', '>=', $startDate)
            ->where('status', 'active')
            ->groupBy('date', 'account_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Portfolio Performance Analysis
        $portfolioPerformance = [
            'loan_performance' => [
                'total_issued' => Loan::where('created_at', '>=', $startDate)->sum('amount'),
                'repaid_on_time' => Loan::where('status', 'completed')
                    ->where('created_at', '>=', $startDate)
                    ->count(),
                'overdue_loans' => Loan::where('status', 'active')
                    ->where('due_date', '<', now())
                    ->count(),
                'default_rate' => $this->calculateDefaultRate($startDate)
            ],
            'interest_income' => Transaction::where('type', 'interest')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'fee_income' => Transaction::where('type', 'fee')
                ->where('created_at', '>=', $startDate)
                ->sum('amount')
        ];

        // Risk Assessment
        $riskAssessment = [
            'concentration_risk' => $this->calculateConcentrationRisk(),
            'liquidity_ratio' => $this->calculateLiquidityRatio(),
            'loan_to_deposit_ratio' => $this->calculateLoanToDepositRatio(),
            'high_risk_loans' => Loan::where('amount', '>', 500000)
                ->whereIn('status', ['active', 'disbursed'])
                ->count()
        ];

        // Profitability Analysis
        $profitabilityAnalysis = [
            'gross_income' => Transaction::whereIn('type', ['interest', 'fee'])
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'operating_expenses' => 0, // Could be calculated from expense tracking
            'net_income' => Transaction::whereIn('type', ['interest', 'fee'])
                ->where('created_at', '>=', $startDate)
                ->sum('amount'), // Simplified calculation
            'roi_percentage' => $this->calculateROI($startDate)
        ];

        return [
            'asset_growth_trends' => $assetGrowthTrend,
            'portfolio_performance' => $portfolioPerformance,
            'risk_assessment' => $riskAssessment,
            'profitability_analysis' => $profitabilityAnalysis
        ];
    }

    private function getMemberAnalyticsAdvanced($startDate)
    {
        // Member Growth Analysis
        $memberGrowthAnalysis = [
            'new_members_trend' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'retention_rate' => $this->calculateMemberRetentionRate($startDate),
            'churn_rate' => $this->calculateMemberChurnRate($startDate)
        ];

        // Engagement Metrics
        $engagementMetrics = [
            'active_users' => User::whereHas('transactions', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'avg_transactions_per_member' => Transaction::where('created_at', '>=', $startDate)
                ->count() / max(1, User::count()),
            'digital_adoption' => [
                'budget_users' => Budget::distinct('user_id')->count('user_id'),
                'goal_setters' => Goal::distinct('member_id')->count('member_id'),
                'mobile_users' => 0 // Would need to track device/platform
            ]
        ];

        // Savings Patterns
        $savingsPatterns = [
            'avg_savings_balance' => Account::where('account_type', 'savings')
                ->where('status', 'active')
                ->avg('balance'),
            'savings_growth_rate' => $this->calculateSavingsGrowthRate($startDate),
            'top_savers' => Account::where('account_type', 'savings')
                ->where('balance', '>', 100000)
                ->count(),
            'seasonal_trends' => $this->getSavingsSeasonalTrends($startDate)
        ];

        // Loan Utilization
        $loanUtilization = [
            'loan_uptake_rate' => $this->calculateLoanUptakeRate($startDate),
            'avg_loan_size' => Loan::where('created_at', '>=', $startDate)->avg('amount'),
            'popular_loan_types' => Loan::with('loanType')
                ->where('created_at', '>=', $startDate)
                ->get()
                ->groupBy('loan_type_id')
                ->map(function($loans) {
                    return [
                        'count' => $loans->count(),
                        'total_amount' => $loans->sum('amount')
                    ];
                })
        ];

        return [
            'member_growth' => $memberGrowthAnalysis,
            'engagement_metrics' => $engagementMetrics,
            'savings_patterns' => $savingsPatterns,
            'loan_utilization' => $loanUtilization
        ];
    }

    private function getOperationalAnalyticsAdvanced($startDate)
    {
        // Transaction Volume Analysis
        $transactionVolumeAnalysis = [
            'daily_volume' => Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'peak_hours' => Transaction::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->get(),
            'transaction_types' => Transaction::selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', $startDate)
                ->groupBy('type')
                ->get()
        ];

        // Processing Efficiency
        $processingEfficiency = [
            'avg_processing_time' => Transaction::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, updated_at)')),
            'success_rate' => $this->calculateTransactionSuccessRate($startDate),
            'failed_transactions' => Transaction::where('status', 'failed')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count()
        ];

        // Service Quality Metrics
        $serviceQuality = [
            'customer_satisfaction' => 4.2, // Would come from surveys/feedback
            'complaint_resolution_time' => 24, // Hours - would track complaints
            'service_uptime' => 99.5, // Percentage - would track system availability
            'error_rate' => $this->calculateSystemErrorRate($startDate)
        ];

        // Branch Performance
        $branchPerformance = DB::table('branches')
            ->leftJoin('users', 'branches.id', '=', 'users.branch_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.member_id')
            ->leftJoin('transactions', function($join) use ($startDate) {
                $join->on('accounts.id', '=', 'transactions.account_id')
                     ->where('transactions.created_at', '>=', $startDate);
            })
            ->selectRaw('
                branches.id,
                branches.name,
                branches.city,
                COUNT(DISTINCT users.id) as member_count,
                COUNT(DISTINCT accounts.id) as account_count,
                COALESCE(SUM(accounts.balance), 0) as total_deposits,
                COUNT(DISTINCT transactions.id) as transaction_count,
                COALESCE(SUM(transactions.amount), 0) as transaction_volume
            ')
            ->where('users.role', 'member')
            ->groupBy('branches.id', 'branches.name', 'branches.city')
            ->get();

        return [
            'transaction_volume' => $transactionVolumeAnalysis,
            'processing_efficiency' => $processingEfficiency,
            'service_quality' => $serviceQuality,
            'branch_performance' => $branchPerformance
        ];
    }

    // Helper calculation methods
    private function calculateDefaultRate($startDate)
    {
        $totalLoans = Loan::where('created_at', '>=', $startDate)->count();
        $defaultedLoans = Loan::where('status', 'defaulted')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        return $totalLoans > 0 ? round(($defaultedLoans / $totalLoans) * 100, 2) : 0;
    }

    private function calculateConcentrationRisk()
    {
        $totalLoanValue = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');
        $largeLoans = Loan::where('amount', '>', $totalLoanValue * 0.1)
            ->whereIn('status', ['active', 'disbursed'])
            ->sum('amount');
        
        return $totalLoanValue > 0 ? round(($largeLoans / $totalLoanValue) * 100, 2) : 0;
    }

    private function calculateLiquidityRatio()
    {
        $totalDeposits = Account::where('status', 'active')->sum('balance');
        $totalLoans = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');
        
        return $totalLoans > 0 ? round($totalDeposits / $totalLoans, 2) : 0;
    }

    private function calculateLoanToDepositRatio()
    {
        $totalLoans = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');
        $totalDeposits = Account::where('status', 'active')->sum('balance');
        
        return $totalDeposits > 0 ? round(($totalLoans / $totalDeposits) * 100, 2) : 0;
    }

    private function calculateROI($startDate)
    {
        $income = Transaction::whereIn('type', ['interest', 'fee'])
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
        $assets = Account::where('status', 'active')->sum('balance');
        
        return $assets > 0 ? round(($income / $assets) * 100, 2) : 0;
    }

    private function calculateMemberRetentionRate($startDate)
    {
        $startMembers = User::where('created_at', '<=', $startDate)->count();
        $currentMembers = User::where('membership_status', 'active')->count();
        
        return $startMembers > 0 ? round(($currentMembers / $startMembers) * 100, 2) : 0;
    }

    private function calculateMemberChurnRate($startDate)
    {
        $inactiveMembers = User::where('membership_status', '!=', 'active')
            ->where('updated_at', '>=', $startDate)
            ->count();
        $totalMembers = User::count();
        
        return $totalMembers > 0 ? round(($inactiveMembers / $totalMembers) * 100, 2) : 0;
    }

    private function calculateSavingsGrowthRate($startDate)
    {
        $currentSavings = Account::where('account_type', 'savings')
            ->where('status', 'active')
            ->sum('balance');
        $previousSavings = 1000000; // Would calculate from historical data
        
        return $previousSavings > 0 ? round((($currentSavings - $previousSavings) / $previousSavings) * 100, 2) : 0;
    }

    private function getSavingsSeasonalTrends($startDate)
    {
        return Transaction::where('type', 'deposit')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('MONTH(created_at) as month, AVG(amount) as avg_amount, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function calculateLoanUptakeRate($startDate)
    {
        $totalMembers = User::where('membership_status', 'active')->count();
        $membersWithLoans = Loan::where('created_at', '>=', $startDate)
            ->distinct('member_id')
            ->count('member_id');
        
        return $totalMembers > 0 ? round(($membersWithLoans / $totalMembers) * 100, 2) : 0;
    }

    private function calculateTransactionSuccessRate($startDate)
    {
        $totalTransactions = Transaction::where('created_at', '>=', $startDate)->count();
        $successfulTransactions = Transaction::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        return $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 2) : 0;
    }

    private function calculateSystemErrorRate($startDate)
    {
        $totalTransactions = Transaction::where('created_at', '>=', $startDate)->count();
        $failedTransactions = Transaction::where('status', 'failed')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        return $totalTransactions > 0 ? round(($failedTransactions / $totalTransactions) * 100, 2) : 0;
    }

    private function getStartDate($period)
    {
        switch ($period) {
            case '1month':
                return now()->subMonth();
            case '3months':
                return now()->subMonths(3);
            case '6months':
                return now()->subMonths(6);
            case '12months':
            default:
                return now()->subYear();
            case 'all':
                return now()->subYears(10);
        }
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', '12months');
        
        // Generate report data
        $data = [
            'overview' => $this->getOverviewMetrics(),
            'financial' => $this->getFinancialAnalytics($this->getStartDate($period)),
            'members' => $this->getMemberAnalytics($this->getStartDate($period)),
            'operations' => $this->getOperationalAnalytics($this->getStartDate($period)),
            'period' => $period,
            'generated_at' => now()
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('analytics.report', $data);
            return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
        }
        
        // CSV export
        $filename = 'analytics-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response()->stream(function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Write CSV headers and data
            fputcsv($handle, ['Metric', 'Value', 'Change']);
            fputcsv($handle, ['Active Members', $data['overview']['active_members']['value'], $data['overview']['active_members']['change'] . '%']);
            fputcsv($handle, ['Total Assets', number_format($data['overview']['total_assets']['value'], 2), $data['overview']['total_assets']['change'] . '%']);
            // Add more rows as needed
            
            fclose($handle);
        }, 200, $headers);
    }
} 