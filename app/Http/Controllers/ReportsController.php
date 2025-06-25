<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get recent reports (this would typically come from a reports table)
        $recentReports = $this->getRecentReports();
        
        // Get quick stats for dashboard
        $quickStats = $this->getQuickStats();
        
        return view('reports.index', compact('recentReports', 'quickStats'));
    }

    /**
     * Generate Financial Reports
     */
    public function financial(Request $request)
    {
        if (!auth()->user()->hasPermission('view-reports')) {
            abort(403, 'Unauthorized to view reports.');
        }
        
        $reportType = $request->get('type', 'income_statement');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $format = $request->get('format', 'view');
        
        $data = [];
        
        switch ($reportType) {
            case 'income_statement':
                $data = $this->generateIncomeStatement($startDate, $endDate);
                break;
            case 'balance_sheet':
                $data = $this->generateBalanceSheet($endDate);
                break;
            case 'cash_flow':
                $data = $this->generateCashFlowStatement($startDate, $endDate);
                break;
            case 'trial_balance':
                $data = $this->generateTrialBalance($endDate);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['generated_at'] = now();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.financial.pdf', $data);
            return $pdf->download("financial-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return $this->exportToExcel($data, "financial-{$reportType}");
        }
        
        return view('reports.financial.index', $data);
    }

    /**
     * Generate Member Reports
     */
    public function members(Request $request)
    {
        if (!auth()->user()->hasPermission('view-reports')) {
            abort(403, 'Unauthorized to view reports.');
        }
        
        $reportType = $request->get('type', 'summary');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $format = $request->get('format', 'view');
        
        // Always generate summary for the header cards
        $summaryData = $this->generateMemberSummary($startDate, $endDate);
        $data = $summaryData; // This includes 'members' and 'summary'
        
        // Generate specific report data based on type
        switch ($reportType) {
            case 'summary':
                // Summary data is already included above
                break;
            case 'activity':
                $activityData = $this->generateMemberActivity($startDate, $endDate);
                $data = array_merge($data, $activityData);
                break;
            case 'demographics':
                $demographicsData = $this->generateMemberDemographics();
                $data = array_merge($data, $demographicsData);
                break;
            case 'growth':
                $growthData = $this->generateMemberGrowth($startDate, $endDate);
                $data = array_merge($data, $growthData);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['generated_at'] = now();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.members.pdf', $data);
            return $pdf->download("members-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return $this->exportToExcel($data, "members-{$reportType}");
        }
        
        return view('reports.members.index', $data);
    }

    /**
     * Generate Loan Reports
     */
    public function loans(Request $request)
    {
        if (!auth()->user()->hasPermission('view-reports')) {
            abort(403, 'Unauthorized to view reports.');
        }
        
        $reportType = $request->get('type', 'portfolio');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $loanTypeId = $request->get('loan_type_id');
        $format = $request->get('format', 'view');
        
        $data = [];
        
        switch ($reportType) {
            case 'portfolio':
                $data = $this->generateLoanPortfolio($startDate, $endDate, $loanTypeId);
                break;
            case 'arrears':
                $data = $this->generateLoanArrears();
                break;
            case 'performance':
                $data = $this->generateLoanPerformance($startDate, $endDate);
                break;
            case 'collections':
                $data = $this->generateCollectionsReport($startDate, $endDate);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['loan_type_id'] = $loanTypeId;
        $data['loan_types'] = LoanType::all();
        $data['generated_at'] = now();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.loans.pdf', $data);
            return $pdf->download("loans-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return $this->exportToExcel($data, "loans-{$reportType}");
        }
        
        return view('reports.loans.index', $data);
    }

    /**
     * Generate Operational Reports
     */
    public function operational(Request $request)
    {
        if (!auth()->user()->hasPermission('view-reports')) {
            abort(403, 'Unauthorized to view reports.');
        }
        
        $reportType = $request->get('type', 'transactions');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $format = $request->get('format', 'view');
        
        $data = [];
        
        switch ($reportType) {
            case 'transactions':
                $data = $this->generateTransactionReport($startDate, $endDate);
                break;
            case 'branch_performance':
                $data = $this->generateBranchPerformance($startDate, $endDate);
                break;
            case 'daily_summary':
                $data = $this->generateDailySummary($startDate, $endDate);
                break;
            case 'audit_trail':
                $data = $this->generateAuditTrail($startDate, $endDate);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['generated_at'] = now();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.operational.pdf', $data);
            return $pdf->download("operational-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return $this->exportToExcel($data, "operational-{$reportType}");
        }
        
        return view('reports.operational.index', $data);
    }

    /**
     * Generate Custom Report
     */
    public function custom(Request $request)
    {
        $this->authorize('view-reports');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'include_members' => 'boolean',
            'include_loans' => 'boolean',
            'include_accounts' => 'boolean',
            'include_transactions' => 'boolean',
            'filters' => 'array'
        ]);
        
        $data = $this->generateCustomReport($validated);
        
        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.custom.pdf', $data);
            return $pdf->download("custom-{$validated['name']}-" . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportToExcel($data, "custom-{$validated['name']}");
        }
        
        return view('reports.custom.index', $data);
    }

    // Private helper methods for generating specific reports

    private function generateIncomeStatement($startDate, $endDate)
    {
        $income = [
            'loan_interest' => Transaction::where('type', 'interest')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
            'fees' => Transaction::where('type', 'fee')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
        ];
        
        $total_income = array_sum($income);
        $total_expenses = 0; // Would calculate actual expenses
        $net_income = $total_income - $total_expenses;
        
        return compact('income', 'total_income', 'total_expenses', 'net_income');
    }

    private function generateBalanceSheet($asOfDate)
    {
        $assets = [
            'cash_and_equivalents' => Account::where('account_type', 'current')
                ->where('created_at', '<=', $asOfDate)
                ->sum('balance'),
            'loans_receivable' => Loan::whereIn('status', ['active', 'disbursed'])
                ->where('created_at', '<=', $asOfDate)
                ->sum('amount'),
            'other_assets' => 0,
        ];
        
        $liabilities = [
            'member_deposits' => Account::where('account_type', 'savings')
                ->where('created_at', '<=', $asOfDate)
                ->sum('balance'),
            'other_liabilities' => 0,
        ];
        
        $equity = [
            'retained_earnings' => 0, // Would calculate from historical data
            'current_earnings' => 0,
        ];
        
        $total_assets = array_sum($assets);
        $total_liabilities = array_sum($liabilities);
        $total_equity = array_sum($equity);
        
        return compact('assets', 'liabilities', 'equity', 'total_assets', 'total_liabilities', 'total_equity');
    }

    private function generateCashFlowStatement($startDate, $endDate)
    {
        $operating = [
            'cash_from_operations' => Transaction::whereIn('type', ['deposit', 'fee'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
            'cash_used_in_operations' => Transaction::whereIn('type', ['withdrawal', 'loan_disbursement'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
        ];
        
        $investing = [
            'investments' => 0,
            'equipment_purchases' => 0,
        ];
        
        $financing = [
            'member_contributions' => 0,
            'loan_repayments' => Transaction::where('type', 'loan_repayment')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
        ];
        
        return compact('operating', 'investing', 'financing');
    }

    private function generateTrialBalance($asOfDate)
    {
        // This would typically come from a proper chart of accounts
        $accounts = [
            'assets' => [
                'cash' => Account::where('account_type', 'current')->sum('balance'),
                'loans_receivable' => Loan::whereIn('status', ['active', 'disbursed'])->sum('amount'),
            ],
            'liabilities' => [
                'member_deposits' => Account::where('account_type', 'savings')->sum('balance'),
            ],
            'equity' => [
                'retained_earnings' => 0,
            ],
            'revenue' => [
                'interest_income' => Transaction::where('type', 'interest')->sum('amount'),
                'fee_income' => Transaction::where('type', 'fee')->sum('amount'),
            ],
            'expenses' => [
                'operational_expenses' => 0,
            ]
        ];
        
        return compact('accounts');
    }

    private function generateMemberSummary($startDate, $endDate)
    {
        $members = User::with(['accounts', 'loans'])->get();
        
        $summary = [
            'total_members' => $members->count(),
            'new_members' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_members' => User::whereHas('transactions', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
            'members_with_loans' => User::whereHas('loans', function($query) {
                $query->whereIn('status', ['active', 'disbursed']);
            })->count(),
        ];
        
        return compact('members', 'summary');
    }

    private function generateMemberActivity($startDate, $endDate)
    {
        $activity = User::with(['transactions' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])->get()->map(function($member) {
            return [
                'member' => $member,
                'transaction_count' => $member->transactions->count(),
                'total_deposits' => $member->transactions->where('type', 'deposit')->sum('amount'),
                'total_withdrawals' => $member->transactions->where('type', 'withdrawal')->sum('amount'),
                'loan_repayments' => $member->transactions->where('type', 'loan_repayment')->sum('amount'),
            ];
        });
        
        return compact('activity');
    }

    private function generateMemberDemographics()
    {
        $demographics = [
            'by_status' => User::selectRaw('membership_status, COUNT(*) as count')
                ->groupBy('membership_status')
                ->get(),
            'by_branch' => User::selectRaw('branch_id, COUNT(*) as count')
                ->groupBy('branch_id')
                ->get(),
            'by_registration_month' => User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
        ];
        
        return compact('demographics');
    }

    private function generateMemberGrowth($startDate, $endDate)
    {
        $growth = User::selectRaw('DATE(created_at) as date, COUNT(*) as new_members')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $cumulative = [];
        $total = User::where('created_at', '<', $startDate)->count();
        
        foreach ($growth as $day) {
            $total += $day->new_members;
            $cumulative[] = [
                'date' => $day->date,
                'new_members' => $day->new_members,
                'total_members' => $total
            ];
        }
        
        return compact('growth', 'cumulative');
    }

    private function generateLoanPortfolio($startDate, $endDate, $loanTypeId = null)
    {
        $query = Loan::with(['member', 'loanType']);
        
        if ($loanTypeId) {
            $query->where('loan_type_id', $loanTypeId);
        }
        
        $loans = $query->get();
        $periodicLoans = $query->whereBetween('created_at', [$startDate, $endDate])->get();
        
        $portfolio = [
            'total_loans' => $loans->count(),
            'active_loans' => $loans->whereIn('status', ['active', 'disbursed'])->count(),
            'completed_loans' => $loans->where('status', 'completed')->count(),
            'total_portfolio_value' => $loans->sum('amount'),
            'new_loans_period' => $periodicLoans->count(),
        ];
        
        return compact('loans', 'portfolio');
    }

    private function generateLoanArrears()
    {
        $overdue = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->with(['member'])
            ->get();
        
        $arrears = [
            'total_overdue' => $overdue->count(),
            'total_overdue_amount' => $overdue->sum('amount'),
            'by_days_overdue' => [
                '1-30' => $overdue->filter(function($loan) {
                    return $loan->due_date->diffInDays(now()) <= 30;
                })->count(),
                '31-60' => $overdue->filter(function($loan) {
                    $days = $loan->due_date->diffInDays(now());
                    return $days > 30 && $days <= 60;
                })->count(),
                '61-90' => $overdue->filter(function($loan) {
                    $days = $loan->due_date->diffInDays(now());
                    return $days > 60 && $days <= 90;
                })->count(),
                '90+' => $overdue->filter(function($loan) {
                    return $loan->due_date->diffInDays(now()) > 90;
                })->count(),
            ]
        ];
        
        return compact('overdue', 'arrears');
    }

    private function generateLoanPerformance($startDate, $endDate)
    {
        $performance = [
            'repayment_rate' => $this->calculateRepaymentRate($startDate, $endDate),
            'default_rate' => $this->calculateDefaultRate($startDate, $endDate),
            'portfolio_at_risk' => $this->calculatePortfolioAtRisk(),
            'collection_efficiency' => $this->calculateCollectionEfficiency($startDate, $endDate),
        ];
        
        return compact('performance');
    }

    private function generateCollectionsReport($startDate, $endDate)
    {
        $collections = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['loan.member'])
            ->get();
        
        $summary = [
            'total_collected' => $collections->sum('amount'),
            'collection_count' => $collections->count(),
            'average_collection' => $collections->avg('amount'),
            'collections_by_day' => $collections->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map(function($dayCollections) {
                return [
                    'count' => $dayCollections->count(),
                    'amount' => $dayCollections->sum('amount')
                ];
            })
        ];
        
        return compact('collections', 'summary');
    }

    private function generateTransactionReport($startDate, $endDate)
    {
        $transactions = Transaction::with(['account.member', 'member'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $summary = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'by_type' => $transactions->groupBy('type')->map(function($typeTransactions) {
                return [
                    'count' => $typeTransactions->count(),
                    'amount' => $typeTransactions->where('status', 'completed')->sum('amount')
                ];
            }),
        ];
        
        return compact('transactions', 'summary');
    }

    private function generateBranchPerformance($startDate, $endDate)
    {
        // This would need a branches table with proper relationships
        $branches = collect([
            ['id' => 1, 'name' => 'Main Branch', 'members' => 0, 'deposits' => 0, 'loans' => 0],
            ['id' => 2, 'name' => 'Secondary Branch', 'members' => 0, 'deposits' => 0, 'loans' => 0],
        ]);
        
        return compact('branches');
    }

    private function generateDailySummary($startDate, $endDate)
    {
        $daily = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current <= $end) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();
            
            $daily[] = [
                'date' => $current->format('Y-m-d'),
                'transactions' => Transaction::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'deposits' => Transaction::where('type', 'deposit')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', 'completed')
                    ->sum('amount'),
                'withdrawals' => Transaction::where('type', 'withdrawal')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', 'completed')
                    ->sum('amount'),
                'loan_disbursements' => Transaction::where('type', 'loan_disbursement')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', 'completed')
                    ->sum('amount'),
                'loan_repayments' => Transaction::where('type', 'loan_repayment')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
            
            $current->addDay();
        }
        
        return compact('daily');
    }

    private function generateAuditTrail($startDate, $endDate)
    {
        // This would typically use Laravel's activity log or similar
        $activities = collect([]);
        
        return compact('activities');
    }

    private function generateCustomReport($params)
    {
        $data = [];
        
        if ($params['include_members']) {
            $data['members'] = User::whereBetween('created_at', [$params['start_date'], $params['end_date']])->get();
        }
        
        if ($params['include_loans']) {
            $data['loans'] = Loan::whereBetween('created_at', [$params['start_date'], $params['end_date']])->get();
        }
        
        if ($params['include_accounts']) {
            $data['accounts'] = Account::whereBetween('created_at', [$params['start_date'], $params['end_date']])->get();
        }
        
        if ($params['include_transactions']) {
            $data['transactions'] = Transaction::whereBetween('created_at', [$params['start_date'], $params['end_date']])->get();
        }
        
        $data['params'] = $params;
        
        return $data;
    }

    private function getQuickStats()
    {
        return [
            'total_members' => User::count(),
            'total_assets' => Account::sum('balance'),
            'active_loans' => Loan::whereIn('status', ['active', 'disbursed'])->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];
    }

    private function getRecentReports()
    {
        return collect([
            [
                'name' => 'Monthly Financial Summary - ' . now()->format('F Y'),
                'type' => 'Financial',
                'generated_at' => now()->subDay(),
                'size' => '2.4 MB',
                'format' => 'PDF'
            ],
            [
                'name' => 'Loan Portfolio Analysis - Q4 2024',
                'type' => 'Loans',
                'generated_at' => now()->subDays(2),
                'size' => '1.8 MB',
                'format' => 'Excel'
            ],
        ]);
    }

    private function exportToExcel($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '-' . now()->format('Y-m-d') . '.csv"',
        ];
        
        return response()->stream(function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Report Generated:', now()->format('Y-m-d H:i:s')]);
            fclose($handle);
        }, 200, $headers);
    }

    // Helper calculation methods
    private function calculateRepaymentRate($startDate, $endDate)
    {
        $duePayments = Loan::where('due_date', '>=', $startDate)
            ->where('due_date', '<=', $endDate)
            ->sum('amount');
        
        $actualPayments = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
        
        return $duePayments > 0 ? ($actualPayments / $duePayments) * 100 : 0;
    }

    private function calculateDefaultRate($startDate, $endDate)
    {
        $totalLoans = Loan::whereBetween('created_at', [$startDate, $endDate])->count();
        $defaultedLoans = Loan::where('status', 'defaulted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        return $totalLoans > 0 ? ($defaultedLoans / $totalLoans) * 100 : 0;
    }

    private function calculatePortfolioAtRisk()
    {
        $totalPortfolio = Loan::whereIn('status', ['active', 'disbursed'])->sum('amount');
        $overdueLoans = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->sum('amount');
        
        return $totalPortfolio > 0 ? ($overdueLoans / $totalPortfolio) * 100 : 0;
    }

    private function calculateCollectionEfficiency($startDate, $endDate)
    {
        $expectedCollections = Loan::where('due_date', '>=', $startDate)
            ->where('due_date', '<=', $endDate)
            ->sum('amount');
        
        $actualCollections = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
        
        return $expectedCollections > 0 ? ($actualCollections / $expectedCollections) * 100 : 0;
    }
} 