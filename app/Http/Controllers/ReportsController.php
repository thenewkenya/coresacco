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
        $branchId = $request->get('branch_id');
        $status = $request->get('status');
        $format = $request->get('format', 'view');
        
        // Get branches for filter dropdown
        $branches = \App\Models\Branch::all();
        
        // Always generate summary for the header cards
        $summaryData = $this->generateMemberSummary($startDate, $endDate, $branchId, $status);
        $data = $summaryData; // This includes 'members' and 'summary'
        
        // Generate specific report data based on type
        switch ($reportType) {
            case 'summary':
                // Summary data is already included above
                break;
            case 'activity':
                $activityData = $this->generateMemberActivity($startDate, $endDate, $branchId, $status);
                $data = array_merge($data, $activityData);
                break;
            case 'demographics':
                $demographicsData = $this->generateMemberDemographics($branchId, $status);
                $data = array_merge($data, $demographicsData);
                break;
            case 'growth':
                $growthData = $this->generateMemberGrowth($startDate, $endDate, $branchId, $status);
                $data = array_merge($data, $growthData);
                break;
            case 'financial':
                $financialData = $this->generateMemberFinancialAnalysis($startDate, $endDate, $branchId, $status);
                $data = array_merge($data, $financialData);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['branch_id'] = $branchId;
        $data['status'] = $status;
        $data['branches'] = $branches;
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
        $branchId = $request->get('branch_id');
        $status = $request->get('status');
        $memberId = $request->get('member_id');
        $format = $request->get('format', 'view');
        
        // Get filtering options for dropdowns
        $loanTypes = LoanType::all();
        $branches = \App\Models\Branch::all();
        
        $data = [];
        
        switch ($reportType) {
            case 'portfolio':
                $data = $this->generateLoanPortfolio($startDate, $endDate, $loanTypeId, $branchId, $status, $memberId);
                break;
            case 'arrears':
                $data = $this->generateLoanArrears($loanTypeId, $branchId, $memberId);
                break;
            case 'performance':
                $data = $this->generateLoanPerformance($startDate, $endDate, $loanTypeId, $branchId);
                break;
            case 'collections':
                $data = $this->generateCollectionsReport($startDate, $endDate, $loanTypeId, $branchId, $memberId);
                break;
            case 'risk_analysis':
                $data = $this->generateLoanRiskAnalysis($startDate, $endDate, $loanTypeId, $branchId);
                break;
            case 'profitability':
                $data = $this->generateLoanProfitabilityAnalysis($startDate, $endDate, $loanTypeId, $branchId);
                break;
        }
        
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['loan_type_id'] = $loanTypeId;
        $data['branch_id'] = $branchId;
        $data['status'] = $status;
        $data['member_id'] = $memberId;
        $data['loan_types'] = $loanTypes;
        $data['branches'] = $branches;
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
            case 'daily_summary':
                $data = $this->generateDailySummary($startDate, $endDate);
                break;
            case 'hourly_analysis':
                $data = $this->generateHourlyAnalysis($startDate, $endDate);
                break;
            case 'transaction_types':
                $data = $this->generateTransactionTypeAnalysis($startDate, $endDate);
                break;
            case 'branch_performance':
                $data = $this->generateBranchPerformance($startDate, $endDate);
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
            'other_income' => 0, // Could add other income sources
        ];
        
        $expenses = [
            'operational_expenses' => 25000, // Sample operational expenses
            'staff_salaries' => 180000, // Sample staff costs
            'office_rent' => 30000, // Sample rent
            'utilities' => 8000, // Sample utilities
            'bad_debt_provision' => Transaction::where('type', 'fee')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'failed')
                ->sum('amount') * 0.02, // 2% provision for bad debts
        ];
        
        $total_income = array_sum($income);
        $total_expenses = array_sum($expenses);
        $net_income = $total_income - $total_expenses;
        
        return compact('income', 'expenses', 'total_income', 'total_expenses', 'net_income');
    }

    private function generateBalanceSheet($asOfDate)
    {
        // Calculate cash from all accounts (excluding loans receivable)
        $totalCash = Account::whereIn('account_type', ['savings', 'shares', 'deposits'])
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $loansOutstanding = Loan::whereIn('status', ['active', 'disbursed'])
            ->where('created_at', '<=', $asOfDate)
            ->sum('amount');
            
        $assets = [
            'cash_and_bank' => $totalCash,
            'loans_receivable' => $loansOutstanding,
            'less_loan_loss_provision' => $loansOutstanding * 0.05, // 5% provision
            'office_equipment' => 150000, // Sample fixed assets
            'furniture_fixtures' => 80000,
            'computer_equipment' => 120000,
        ];
        
        $memberDeposits = Account::where('account_type', 'savings')
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $memberShares = Account::where('account_type', 'shares')
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $liabilities = [
            'member_savings' => $memberDeposits,
            'member_shares' => $memberShares,
            'accrued_expenses' => 45000, // Sample accrued expenses
            'other_payables' => 25000,
        ];
        
        // Calculate retained earnings as difference between assets and liabilities
        $total_assets = $assets['cash_and_bank'] + $assets['loans_receivable'] - $assets['less_loan_loss_provision'] 
                       + $assets['office_equipment'] + $assets['furniture_fixtures'] + $assets['computer_equipment'];
        $total_liabilities = array_sum($liabilities);
        
        $retainedEarnings = $total_assets - $total_liabilities - 500000; // Assuming 500k initial capital
        
        $equity = [
            'initial_capital' => 500000,
            'retained_earnings' => max(0, $retainedEarnings),
            'current_year_surplus' => 0, // Would be current year's net income
        ];
        
        $total_equity = array_sum($equity);
        
        return compact('assets', 'liabilities', 'equity', 'total_assets', 'total_liabilities', 'total_equity');
    }

    private function generateCashFlowStatement($startDate, $endDate)
    {
        // Operating Activities
        $cashFromDeposits = Transaction::where('type', 'deposit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $feesCollected = Transaction::where('type', 'fee')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $interestReceived = Transaction::where('type', 'interest')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $cashFromWithdrawals = -Transaction::where('type', 'withdrawal')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $operating = [
            'cash_from_deposits' => $cashFromDeposits,
            'fees_collected' => $feesCollected,
            'interest_received' => $interestReceived,
            'cash_from_withdrawals' => $cashFromWithdrawals,
            'operational_expenses' => -243000, // Sample operating expenses (negative as outflow)
        ];
        
        // Investing Activities
        $loansAdvanced = -Transaction::where('type', 'loan_disbursement')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $investing = [
            'loans_advanced' => $loansAdvanced,
            'equipment_purchases' => -15000, // Sample equipment purchases
            'investments' => 0,
        ];
        
        // Financing Activities
        $loanRepayments = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
            
        $financing = [
            'loan_repayments_received' => $loanRepayments,
            'member_share_contributions' => Account::where('account_type', 'shares')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('balance'),
            'dividends_paid' => 0,
        ];
        
        $net_operating = array_sum($operating);
        $net_investing = array_sum($investing);
        $net_financing = array_sum($financing);
        $net_cash_flow = $net_operating + $net_investing + $net_financing;
        
        return compact('operating', 'investing', 'financing', 'net_operating', 'net_investing', 'net_financing', 'net_cash_flow');
    }

    private function generateTrialBalance($asOfDate)
    {
        // Get balances as of the specified date
        $cashBalance = Account::whereIn('account_type', ['savings', 'shares', 'deposits'])
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $loansReceivable = Loan::whereIn('status', ['active', 'disbursed'])
            ->where('created_at', '<=', $asOfDate)
            ->sum('amount');
            
        $memberSavings = Account::where('account_type', 'savings')
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $memberShares = Account::where('account_type', 'shares')
            ->where('created_at', '<=', $asOfDate)
            ->sum('balance');
            
        $interestIncome = Transaction::where('type', 'interest')
            ->where('created_at', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');
            
        $feeIncome = Transaction::where('type', 'fee')
            ->where('created_at', '<=', $asOfDate)
            ->where('status', 'completed')
            ->sum('amount');
        
        $accounts = [
            // Assets (Debit Balances)
            'cash_and_bank' => ['type' => 'asset', 'debit' => $cashBalance, 'credit' => 0],
            'loans_receivable' => ['type' => 'asset', 'debit' => $loansReceivable, 'credit' => 0],
            'office_equipment' => ['type' => 'asset', 'debit' => 150000, 'credit' => 0],
            'furniture_fixtures' => ['type' => 'asset', 'debit' => 80000, 'credit' => 0],
            'computer_equipment' => ['type' => 'asset', 'debit' => 120000, 'credit' => 0],
            
            // Liabilities (Credit Balances)
            'member_savings' => ['type' => 'liability', 'debit' => 0, 'credit' => $memberSavings],
            'member_shares' => ['type' => 'liability', 'debit' => 0, 'credit' => $memberShares],
            'accrued_expenses' => ['type' => 'liability', 'debit' => 0, 'credit' => 45000],
            'other_payables' => ['type' => 'liability', 'debit' => 0, 'credit' => 25000],
            
            // Equity (Credit Balances)
            'initial_capital' => ['type' => 'equity', 'debit' => 0, 'credit' => 500000],
            'retained_earnings' => ['type' => 'equity', 'debit' => 0, 'credit' => 150000],
            
            // Revenue (Credit Balances)
            'interest_income' => ['type' => 'revenue', 'debit' => 0, 'credit' => $interestIncome],
            'fee_income' => ['type' => 'revenue', 'debit' => 0, 'credit' => $feeIncome],
            
            // Expenses (Debit Balances)
            'staff_salaries' => ['type' => 'expense', 'debit' => 180000, 'credit' => 0],
            'office_rent' => ['type' => 'expense', 'debit' => 30000, 'credit' => 0],
            'utilities' => ['type' => 'expense', 'debit' => 8000, 'credit' => 0],
            'operational_expenses' => ['type' => 'expense', 'debit' => 25000, 'credit' => 0],
        ];
        
        // Calculate totals
        $total_debits = 0;
        $total_credits = 0;
        
        foreach ($accounts as $account) {
            $total_debits += $account['debit'];
            $total_credits += $account['credit'];
        }
        
        return compact('accounts', 'total_debits', 'total_credits');
    }

    private function generateMemberSummary($startDate, $endDate, $branchId = null, $status = null)
    {
        $query = User::with(['accounts', 'loans']);
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('membership_status', $status);
        }
        
        $members = $query->get();
        
        $summary = [
            'total_members' => $members->count(),
            'new_members' => User::whereBetween('created_at', [$startDate, $endDate])
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->count(),
            'active_members' => User::whereHas('transactions', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->count(),
            'members_with_loans' => User::whereHas('loans', function($query) {
                $query->whereIn('status', ['active', 'disbursed']);
            })
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->count(),
        ];
        
        return compact('members', 'summary');
    }

    private function generateMemberActivity($startDate, $endDate, $branchId = null, $status = null)
    {
        $query = User::with(['transactions' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed');
        }, 'accounts', 'loans']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('membership_status', $status);
        }
        
        $members = $query->get();

        $activity = $members->map(function($member) {
            $transactions = $member->transactions;
            $deposits = $transactions->where('type', 'deposit');
            $withdrawals = $transactions->where('type', 'withdrawal');
            $loanRepayments = $transactions->where('type', 'loan_repayment');
            
            return [
                'member' => $member,
                'transaction_count' => $transactions->count(),
                'total_deposits' => $deposits->sum('amount'),
                'deposit_count' => $deposits->count(),
                'total_withdrawals' => $withdrawals->sum('amount'),
                'withdrawal_count' => $withdrawals->count(),
                'loan_repayments' => $loanRepayments->sum('amount'),
                'repayment_count' => $loanRepayments->count(),
                'net_savings' => $deposits->sum('amount') - $withdrawals->sum('amount'),
                'avg_transaction_amount' => $transactions->count() > 0 ? $transactions->avg('amount') : 0,
                'last_transaction_date' => $transactions->max('created_at'),
                'total_account_balance' => $member->accounts->sum('balance'),
                'active_loans_count' => $member->loans->whereIn('status', ['active', 'disbursed'])->count(),
            ];
        })->sortByDesc('transaction_count');

        // Activity summary
        $activitySummary = [
            'most_active_members' => $activity->take(10),
            'total_active_members' => $activity->where('transaction_count', '>', 0)->count(),
            'average_transactions_per_member' => $activity->avg('transaction_count'),
            'total_transaction_volume' => $activity->sum('total_deposits') + $activity->sum('total_withdrawals'),
            'top_depositors' => $activity->sortByDesc('total_deposits')->take(5),
            'top_savers' => $activity->sortByDesc('net_savings')->take(5),
        ];

        return compact('activity', 'activitySummary');
    }

    private function generateMemberDemographics($branchId = null, $status = null)
    {
        $baseQuery = User::query();
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }
        if ($status) {
            $baseQuery->where('membership_status', $status);
        }
        
        $totalMembers = $baseQuery->count();
        
        $demographics = [
            'by_status' => User::selectRaw('membership_status, COUNT(*) as count')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->groupBy('membership_status')
                ->get()
                ->map(function($item) use ($totalMembers) {
                    return [
                        'status' => $item->membership_status ?: 'active',
                        'count' => $item->count,
                        'percentage' => $totalMembers > 0 ? round(($item->count / $totalMembers) * 100, 2) : 0
                    ];
                }),
            
            'by_branch' => User::selectRaw('branch_id, COUNT(*) as count')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->groupBy('branch_id')
                ->with('branch')
                ->get()
                ->map(function($item) use ($totalMembers) {
                    return [
                        'branch_id' => $item->branch_id,
                        'branch_name' => $item->branch->name ?? 'Unassigned',
                        'count' => $item->count,
                        'percentage' => $totalMembers > 0 ? round(($item->count / $totalMembers) * 100, 2) : 0
                    ];
                }),
            
            'by_registration_period' => User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($status, fn($q) => $q->where('membership_status', $status))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get()
                ->map(function($item) {
                    return [
                        'period' => \Carbon\Carbon::create($item->year, $item->month, 1)->format('M Y'),
                        'year' => $item->year,
                        'month' => $item->month,
                        'count' => $item->count
                    ];
                }),
            
            'account_distribution' => User::selectRaw('COUNT(DISTINCT users.id) as member_count, COUNT(accounts.id) as account_count')
                ->leftJoin('accounts', 'users.id', '=', 'accounts.member_id')
                ->when($branchId, fn($q) => $q->where('users.branch_id', $branchId))
                ->when($status, fn($q) => $q->where('users.membership_status', $status))
                ->selectRaw('CASE 
                    WHEN COUNT(accounts.id) = 0 THEN "No Accounts"
                    WHEN COUNT(accounts.id) = 1 THEN "1 Account"
                    WHEN COUNT(accounts.id) BETWEEN 2 AND 3 THEN "2-3 Accounts"
                    ELSE "4+ Accounts"
                END as account_range')
                ->groupBy('users.id')
                ->get()
                ->groupBy('account_range')
                ->map(function($group, $range) use ($totalMembers) {
                    return [
                        'range' => $range,
                        'count' => $group->count(),
                        'percentage' => $totalMembers > 0 ? round(($group->count() / $totalMembers) * 100, 2) : 0
                    ];
                }),
            
            'loan_participation' => [
                'with_loans' => User::whereHas('loans')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($status, fn($q) => $q->where('membership_status', $status))
                    ->count(),
                'without_loans' => User::whereDoesntHave('loans')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($status, fn($q) => $q->where('membership_status', $status))
                    ->count(),
                'active_borrowers' => User::whereHas('loans', function($query) {
                    $query->whereIn('status', ['active', 'disbursed']);
                })
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($status, fn($q) => $q->where('membership_status', $status))
                    ->count(),
            ]
        ];

        // Calculate percentages for loan participation
        $demographics['loan_participation']['with_loans_percentage'] = $totalMembers > 0 ? 
            round(($demographics['loan_participation']['with_loans'] / $totalMembers) * 100, 2) : 0;
        $demographics['loan_participation']['without_loans_percentage'] = $totalMembers > 0 ? 
            round(($demographics['loan_participation']['without_loans'] / $totalMembers) * 100, 2) : 0;
        $demographics['loan_participation']['active_borrowers_percentage'] = $totalMembers > 0 ? 
            round(($demographics['loan_participation']['active_borrowers'] / $totalMembers) * 100, 2) : 0;

        return compact('demographics', 'totalMembers');
    }

    private function generateMemberGrowth($startDate, $endDate, $branchId = null, $status = null)
    {
        // Daily member registrations in the period
        $dailyGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as new_members')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('membership_status', $status))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Monthly growth over the last 12 months
        $monthlyGrowth = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as new_members')
            ->where('created_at', '>=', now()->subMonths(12))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('membership_status', $status))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'period' => \Carbon\Carbon::create($item->year, $item->month, 1)->format('M Y'),
                    'year' => $item->year,
                    'month' => $item->month,
                    'new_members' => $item->new_members,
                    'date' => \Carbon\Carbon::create($item->year, $item->month, 1)
                ];
            });

        // Calculate cumulative totals for the reporting period
        $startingTotal = User::where('created_at', '<', $startDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('membership_status', $status))
            ->count();
        $cumulative = [];
        $runningTotal = $startingTotal;
        
        foreach ($dailyGrowth as $day) {
            $runningTotal += $day->new_members;
            $cumulative[] = [
                'date' => $day->date,
                'new_members' => $day->new_members,
                'total_members' => $runningTotal
            ];
        }

        // Growth metrics
        $periodStart = \Carbon\Carbon::parse($startDate);
        $periodEnd = \Carbon\Carbon::parse($endDate);
        $totalNewInPeriod = $dailyGrowth->sum('new_members');
        $totalAtStart = $startingTotal;
        $totalAtEnd = $runningTotal;
        
        $growthMetrics = [
            'new_members_period' => $totalNewInPeriod,
            'growth_rate' => $totalAtStart > 0 ? round((($totalAtEnd - $totalAtStart) / $totalAtStart) * 100, 2) : 0,
            'avg_daily_growth' => $periodStart->diffInDays($periodEnd) > 0 ? 
                round($totalNewInPeriod / $periodStart->diffInDays($periodEnd), 2) : 0,
            'total_at_start' => $totalAtStart,
            'total_at_end' => $totalAtEnd,
            'peak_registration_day' => $dailyGrowth->sortByDesc('new_members')->first(),
            'days_with_registrations' => $dailyGrowth->where('new_members', '>', 0)->count(),
        ];

        // Branch-wise growth analysis
        $branchGrowth = User::selectRaw('branch_id, COUNT(*) as new_members')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($status, fn($q) => $q->where('membership_status', $status))
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->map(function($item) {
                return [
                    'branch_id' => $item->branch_id,
                    'branch_name' => $item->branch->name ?? 'Unassigned',
                    'new_members' => $item->new_members
                ];
            });

        return compact('dailyGrowth', 'monthlyGrowth', 'cumulative', 'growthMetrics', 'branchGrowth');
    }

    private function generateMemberFinancialAnalysis($startDate, $endDate, $branchId = null, $status = null)
    {
        $query = User::with(['accounts', 'loans', 'transactions' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed');
        }]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('membership_status', $status);
        }
        
        $members = $query->get();

        // Financial analysis per member
        $financialProfiles = $members->map(function($member) {
            $accounts = $member->accounts;
            $loans = $member->loans;
            $transactions = $member->transactions;
            
            $totalBalance = $accounts->sum('balance');
            $totalLoanAmount = $loans->sum('amount');
            $activeLoanAmount = $loans->whereIn('status', ['active', 'disbursed'])->sum('amount');
            
            $deposits = $transactions->where('type', 'deposit');
            $withdrawals = $transactions->where('type', 'withdrawal');
            $loanRepayments = $transactions->where('type', 'loan_repayment');
            
            $savings_rate = $deposits->sum('amount') > 0 ? 
                (($deposits->sum('amount') - $withdrawals->sum('amount')) / $deposits->sum('amount')) * 100 : 0;
            
            return [
                'member' => $member,
                'total_balance' => $totalBalance,
                'total_loan_amount' => $totalLoanAmount,
                'active_loan_amount' => $activeLoanAmount,
                'loan_to_savings_ratio' => $totalBalance > 0 ? ($activeLoanAmount / $totalBalance) * 100 : 0,
                'total_deposits' => $deposits->sum('amount'),
                'total_withdrawals' => $withdrawals->sum('amount'),
                'net_savings' => $deposits->sum('amount') - $withdrawals->sum('amount'),
                'savings_rate' => $savings_rate,
                'loan_repayments' => $loanRepayments->sum('amount'),
                'transaction_frequency' => $transactions->count(),
                'avg_transaction_amount' => $transactions->avg('amount') ?: 0,
                'account_count' => $accounts->count(),
                'loan_count' => $loans->count(),
                'risk_score' => $this->calculateMemberRiskScore($member, $totalBalance, $activeLoanAmount),
            ];
        });

        // Financial summary metrics
        $financialSummary = [
            'total_member_savings' => $financialProfiles->sum('total_balance'),
            'total_loan_portfolio' => $financialProfiles->sum('active_loan_amount'),
            'average_member_balance' => $financialProfiles->avg('total_balance'),
            'average_loan_amount' => $financialProfiles->avg('active_loan_amount'),
            'portfolio_loan_ratio' => $financialProfiles->sum('total_balance') > 0 ? 
                ($financialProfiles->sum('active_loan_amount') / $financialProfiles->sum('total_balance')) * 100 : 0,
            'high_savers_count' => $financialProfiles->where('total_balance', '>', 10000)->count(),
            'high_risk_borrowers' => $financialProfiles->where('risk_score', '>', 70)->count(),
            'inactive_savers' => $financialProfiles->where('transaction_frequency', 0)->count(),
        ];

        // Categorize members by financial behavior
        $memberCategories = [
            'high_value_savers' => $financialProfiles->sortByDesc('total_balance')->take(10),
            'high_risk_borrowers' => $financialProfiles->where('risk_score', '>', 70)->sortByDesc('risk_score')->take(10),
            'most_active_investors' => $financialProfiles->sortByDesc('transaction_frequency')->take(10),
            'growth_potential' => $financialProfiles->where('total_balance', '>', 1000)
                ->where('total_balance', '<', 10000)->sortByDesc('savings_rate')->take(10),
        ];

        // Savings patterns analysis
        $savingsPatterns = [
            'consistent_savers' => $financialProfiles->where('savings_rate', '>', 50)->count(),
            'moderate_savers' => $financialProfiles->whereBetween('savings_rate', [20, 50])->count(),
            'low_savers' => $financialProfiles->where('savings_rate', '<', 20)->where('savings_rate', '>', 0)->count(),
            'net_withdrawers' => $financialProfiles->where('net_savings', '<', 0)->count(),
        ];

        return compact('financialProfiles', 'financialSummary', 'memberCategories', 'savingsPatterns');
    }

    private function calculateMemberRiskScore($member, $totalBalance, $activeLoanAmount)
    {
        $score = 0;
        
        // Loan to savings ratio (40% weight)
        if ($totalBalance > 0) {
            $loanRatio = ($activeLoanAmount / $totalBalance) * 100;
            if ($loanRatio > 80) $score += 40;
            elseif ($loanRatio > 60) $score += 30;
            elseif ($loanRatio > 40) $score += 20;
            elseif ($loanRatio > 20) $score += 10;
        } else if ($activeLoanAmount > 0) {
            $score += 40; // No savings but has loans
        }
        
        // Account balance stability (30% weight)
        if ($totalBalance < 100) $score += 30;
        elseif ($totalBalance < 500) $score += 20;
        elseif ($totalBalance < 1000) $score += 10;
        
        // Transaction activity (20% weight)
        $recentTransactions = $member->transactions()->where('created_at', '>', now()->subMonths(3))->count();
        if ($recentTransactions == 0) $score += 20;
        elseif ($recentTransactions < 3) $score += 15;
        elseif ($recentTransactions < 6) $score += 10;
        
        // Loan payment history (10% weight)
        $overdueLoans = $member->loans()->where('status', 'active')->where('due_date', '<', now())->count();
        if ($overdueLoans > 0) $score += 10;
        
        return min($score, 100); // Cap at 100
    }

    private function generateLoanPortfolio($startDate, $endDate, $loanTypeId = null, $branchId = null, $status = null, $memberId = null)
    {
        $query = Loan::with(['member', 'loanType']);
        
        if ($loanTypeId) {
            $query->where('loan_type_id', $loanTypeId);
        }
        if ($branchId) {
            $query->whereHas('member', fn($q) => $q->where('branch_id', $branchId));
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($memberId) {
            $query->where('member_id', $memberId);
        }
        
        $loans = $query->get();
        $periodicLoans = $query->whereBetween('created_at', [$startDate, $endDate])->get();
        
        // Enhanced portfolio analytics
        $portfolio = [
            'total_loans' => $loans->count(),
            'active_loans' => $loans->whereIn('status', ['active', 'disbursed'])->count(),
            'completed_loans' => $loans->where('status', 'completed')->count(),
            'defaulted_loans' => $loans->where('status', 'defaulted')->count(),
            'pending_loans' => $loans->where('status', 'pending')->count(),
            'total_portfolio_value' => $loans->sum('amount'),
            'active_portfolio_value' => $loans->whereIn('status', ['active', 'disbursed'])->sum('amount'),
            'new_loans_period' => $periodicLoans->count(),
            'new_loans_value' => $periodicLoans->sum('amount'),
            'average_loan_amount' => $loans->avg('amount'),
            'largest_loan' => $loans->max('amount'),
            'smallest_loan' => $loans->min('amount'),
        ];

        // Loan status distribution
        $statusDistribution = $loans->groupBy('status')->map(function($statusLoans, $status) use ($loans) {
            return [
                'count' => $statusLoans->count(),
                'amount' => $statusLoans->sum('amount'),
                'percentage' => $loans->count() > 0 ? round(($statusLoans->count() / $loans->count()) * 100, 2) : 0
            ];
        });

        // Loan type analysis
        $typeAnalysis = $loans->groupBy('loanType.name')->map(function($typeLoans, $typeName) use ($loans) {
            return [
                'count' => $typeLoans->count(),
                'amount' => $typeLoans->sum('amount'),
                'percentage' => $loans->count() > 0 ? round(($typeLoans->count() / $loans->count()) * 100, 2) : 0,
                'avg_amount' => $typeLoans->avg('amount'),
                'active_count' => $typeLoans->whereIn('status', ['active', 'disbursed'])->count()
            ];
        });

        return compact('loans', 'portfolio', 'statusDistribution', 'typeAnalysis');
    }

    private function generateLoanArrears($loanTypeId = null, $branchId = null, $memberId = null)
    {
        $overdue = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->with(['member', 'loanType'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->when($memberId, fn($q) => $q->where('member_id', $memberId))
            ->get();
        
        // Enhanced arrears analysis
        $arrears = [
            'total_overdue' => $overdue->count(),
            'total_overdue_amount' => $overdue->sum('amount'),
            'by_days_overdue' => [
                '1-30' => [
                    'count' => $overdue->filter(function($loan) {
                        $days = $loan->due_date->diffInDays(now());
                        return $days <= 30;
                    })->count(),
                    'amount' => $overdue->filter(function($loan) {
                        return $loan->due_date->diffInDays(now()) <= 30;
                    })->sum('amount')
                ],
                '31-60' => [
                    'count' => $overdue->filter(function($loan) {
                        $days = $loan->due_date->diffInDays(now());
                        return $days > 30 && $days <= 60;
                    })->count(),
                    'amount' => $overdue->filter(function($loan) {
                        $days = $loan->due_date->diffInDays(now());
                        return $days > 30 && $days <= 60;
                    })->sum('amount')
                ],
                '61-90' => [
                    'count' => $overdue->filter(function($loan) {
                        $days = $loan->due_date->diffInDays(now());
                        return $days > 60 && $days <= 90;
                    })->count(),
                    'amount' => $overdue->filter(function($loan) {
                        $days = $loan->due_date->diffInDays(now());
                        return $days > 60 && $days <= 90;
                    })->sum('amount')
                ],
                '90+' => [
                    'count' => $overdue->filter(function($loan) {
                        return $loan->due_date->diffInDays(now()) > 90;
                    })->count(),
                    'amount' => $overdue->filter(function($loan) {
                        return $loan->due_date->diffInDays(now()) > 90;
                    })->sum('amount')
                ]
            ],
            'by_loan_type' => $overdue->groupBy('loanType.name')->map(function($typeOverdue) {
                return [
                    'count' => $typeOverdue->count(),
                    'amount' => $typeOverdue->sum('amount')
                ];
            }),
            'worst_performers' => $overdue->sortBy('due_date')->take(10)
        ];
        
        return compact('overdue', 'arrears');
    }

    private function generateLoanPerformance($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $performance = [
            'repayment_rate' => $this->calculateRepaymentRate($startDate, $endDate, $loanTypeId, $branchId),
            'default_rate' => $this->calculateDefaultRate($startDate, $endDate, $loanTypeId, $branchId),
            'portfolio_at_risk' => $this->calculatePortfolioAtRisk($loanTypeId, $branchId),
            'collection_efficiency' => $this->calculateCollectionEfficiency($startDate, $endDate, $loanTypeId, $branchId),
        ];

        // Additional performance metrics
        $totalLoans = Loan::when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $approvedLoans = Loan::whereIn('status', ['approved', 'disbursed', 'active', 'completed'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $disbursedLoans = Loan::whereIn('status', ['disbursed', 'active', 'completed'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $performance['approval_rate'] = $totalLoans > 0 ? round(($approvedLoans / $totalLoans) * 100, 2) : 0;
        $performance['disbursement_rate'] = $approvedLoans > 0 ? round(($disbursedLoans / $approvedLoans) * 100, 2) : 0;
        $performance['total_applications'] = $totalLoans;
        $performance['approved_applications'] = $approvedLoans;
        $performance['disbursed_applications'] = $disbursedLoans;
        
        return compact('performance');
    }

    private function generateCollectionsReport($startDate, $endDate, $loanTypeId = null, $branchId = null, $memberId = null)
    {
        $collections = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['loan.member', 'loan.loanType'])
            ->when($loanTypeId, fn($q) => $q->whereHas('loan', fn($sq) => $sq->where('loan_type_id', $loanTypeId)))
            ->when($branchId, fn($q) => $q->whereHas('loan.member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->when($memberId, fn($q) => $q->where('member_id', $memberId))
            ->get();
        
        $summary = [
            'total_collected' => $collections->sum('amount'),
            'collection_count' => $collections->count(),
            'average_collection' => $collections->avg('amount'),
            'largest_collection' => $collections->max('amount'),
            'smallest_collection' => $collections->min('amount'),
            'collections_by_day' => $collections->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map(function($dayCollections) {
                return [
                    'count' => $dayCollections->count(),
                    'amount' => $dayCollections->sum('amount')
                ];
            }),
            'collections_by_loan_type' => $collections->groupBy('loan.loanType.name')->map(function($typeCollections) {
                return [
                    'count' => $typeCollections->count(),
                    'amount' => $typeCollections->sum('amount'),
                    'avg_amount' => $typeCollections->avg('amount')
                ];
            }),
            'top_paying_members' => $collections->groupBy('member_id')->map(function($memberCollections) {
                return [
                    'member' => $memberCollections->first()->loan->member ?? null,
                    'total_paid' => $memberCollections->sum('amount'),
                    'payment_count' => $memberCollections->count()
                ];
            })->sortByDesc('total_paid')->take(10)
        ];
        
        return compact('collections', 'summary');
    }

    private function generateLoanRiskAnalysis($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $loans = Loan::with(['member', 'loanType', 'transactions'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->get();

        // Risk scoring for each loan
        $riskAnalysis = $loans->map(function($loan) {
            $riskScore = $this->calculateLoanRiskScore($loan);
            $daysOverdue = $loan->status === 'active' && $loan->due_date < now() ? 
                $loan->due_date->diffInDays(now()) : 0;
            
            return [
                'loan' => $loan,
                'risk_score' => $riskScore,
                'days_overdue' => $daysOverdue,
                'member_credit_score' => $this->calculateMemberCreditScore($loan->member),
                'loan_performance' => $this->calculateLoanPerformanceScore($loan)
            ];
        });

        // Risk categories
        $riskCategories = [
            'low_risk' => $riskAnalysis->where('risk_score', '<=', 30)->count(),
            'medium_risk' => $riskAnalysis->whereBetween('risk_score', [31, 70])->count(),
            'high_risk' => $riskAnalysis->where('risk_score', '>', 70)->count(),
        ];

        // Risk by loan type
        $riskByType = $riskAnalysis->groupBy('loan.loanType.name')->map(function($typeLoans, $typeName) {
            return [
                'type_name' => $typeName,
                'average_risk' => $typeLoans->avg('risk_score'),
                'high_risk_count' => $typeLoans->where('risk_score', '>', 70)->count(),
                'total_count' => $typeLoans->count(),
                'total_amount' => $typeLoans->sum('loan.amount')
            ];
        });

        // Early warning indicators
        $earlyWarnings = [
            'loans_30_days_overdue' => $riskAnalysis->where('days_overdue', '>', 30)->where('days_overdue', '<=', 60)->count(),
            'loans_with_declining_payments' => $this->getLoansWithDecliningPayments($loans),
            'members_with_multiple_overdue' => $this->getMembersWithMultipleOverdue($loans),
        ];

        return compact('riskAnalysis', 'riskCategories', 'riskByType', 'earlyWarnings');
    }

    private function generateLoanProfitabilityAnalysis($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $loans = Loan::with(['member', 'loanType', 'transactions'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $profitabilityAnalysis = $loans->map(function($loan) {
            $totalRepayments = $loan->transactions()
                ->where('type', 'loan_repayment')
                ->where('status', 'completed')
                ->sum('amount');
            
            $expectedInterest = method_exists($loan, 'calculateInterest') ? $loan->calculateInterest() : ($loan->amount * 0.12);
            $actualInterest = $totalRepayments - $loan->amount;
            $roi = $loan->amount > 0 ? ($actualInterest / $loan->amount) * 100 : 0;
            
            return [
                'member_name' => $loan->member->name ?? 'N/A',
                'member_number' => $loan->member->member_number ?? '',
                'loan_type' => $loan->loanType->name ?? 'Unassigned',
                'principal' => $loan->amount,
                'interest_earned' => max(0, $actualInterest),
                'expected_interest' => $expectedInterest,
                'roi' => $roi,
                'status' => $loan->status,
                'completion_percentage' => $loan->amount > 0 ? min(100, ($totalRepayments / ($loan->amount + $expectedInterest)) * 100) : 0,
                'is_profitable' => $actualInterest > 0
            ];
        });

        // Profitability metrics for overview cards
        $profitabilityMetrics = [
            'total_interest_income' => $profitabilityAnalysis->sum('interest_earned'),
            'expected_roi' => $profitabilityAnalysis->avg('roi'),
            'actual_roi' => $profitabilityAnalysis->where('is_profitable', true)->avg('roi'),
            'profit_margin' => $profitabilityAnalysis->avg('completion_percentage'),
        ];

        // Profitability by loan type
        $profitabilityByType = $profitabilityAnalysis->groupBy('loan_type')->map(function($typeLoans, $typeName) {
            return [
                'type_name' => $typeName ?: 'Unassigned',
                'total_count' => $typeLoans->count(),
                'interest_income' => $typeLoans->sum('interest_earned'),
                'expected_roi' => $typeLoans->avg('roi'),
                'actual_roi' => $typeLoans->where('is_profitable', true)->avg('roi') ?: 0,
                'completion_rate' => $typeLoans->avg('completion_percentage'),
                'profit_margin' => $typeLoans->avg('roi')
            ];
        });

        // Top and poor performers
        $topPerformers = $profitabilityAnalysis->sortByDesc('roi')->take(10)->map(function($item) {
            return [
                'member_name' => $item['member_name'],
                'loan_type' => $item['loan_type'],
                'amount' => $item['principal'],
                'roi' => $item['roi'],
                'profit' => $item['interest_earned']
            ];
        });

        $poorPerformers = $profitabilityAnalysis->sortBy('roi')->take(10)->map(function($item) {
            return [
                'member_name' => $item['member_name'],
                'loan_type' => $item['loan_type'],
                'amount' => $item['principal'],
                'roi' => $item['roi'],
                'days_overdue' => 0 // This would need to be calculated from loan data
            ];
        });

        return compact('profitabilityAnalysis', 'profitabilityMetrics', 'profitabilityByType', 'topPerformers', 'poorPerformers');
    }

    // Helper methods for risk and profitability analysis
    private function calculateLoanRiskScore($loan)
    {
        $score = 0;
        
        // Days overdue (40% weight)
        if ($loan->status === 'active' && $loan->due_date < now()) {
            $daysOverdue = $loan->due_date->diffInDays(now());
            if ($daysOverdue > 90) $score += 40;
            elseif ($daysOverdue > 60) $score += 30;
            elseif ($daysOverdue > 30) $score += 20;
            elseif ($daysOverdue > 0) $score += 10;
        }
        
        // Loan to member savings ratio (30% weight)
        $memberBalance = $loan->member->accounts->sum('balance');
        if ($memberBalance > 0) {
            $ratio = ($loan->amount / $memberBalance) * 100;
            if ($ratio > 200) $score += 30;
            elseif ($ratio > 150) $score += 20;
            elseif ($ratio > 100) $score += 15;
            elseif ($ratio > 50) $score += 10;
        } else {
            $score += 30; // No savings is high risk
        }
        
        // Payment history (30% weight)
        $totalPayments = $loan->transactions()->where('type', 'loan_repayment')->where('status', 'completed')->count();
        $expectedPayments = max(1, $loan->term_period ?? 12);
        $paymentRate = ($totalPayments / $expectedPayments) * 100;
        
        if ($paymentRate < 25) $score += 30;
        elseif ($paymentRate < 50) $score += 20;
        elseif ($paymentRate < 75) $score += 10;
        
        return min($score, 100);
    }

    private function calculateMemberCreditScore($member)
    {
        $score = 100; // Start with perfect score
        
        // Deduct for overdue loans
        $overdueLoans = $member->loans()->where('status', 'active')->where('due_date', '<', now())->count();
        $score -= $overdueLoans * 20;
        
        // Deduct for defaulted loans
        $defaultedLoans = $member->loans()->where('status', 'defaulted')->count();
        $score -= $defaultedLoans * 30;
        
        // Add points for completed loans
        $completedLoans = $member->loans()->where('status', 'completed')->count();
        $score += $completedLoans * 5;
        
        return max(0, min(100, $score));
    }

    private function calculateLoanPerformanceScore($loan)
    {
        if ($loan->status === 'completed') return 100;
        if ($loan->status === 'defaulted') return 0;
        
        $totalRepayments = $loan->transactions()->where('type', 'loan_repayment')->where('status', 'completed')->sum('amount');
        $expectedTotal = $loan->amount + $loan->calculateInterest();
        
        return $expectedTotal > 0 ? min(100, ($totalRepayments / $expectedTotal) * 100) : 0;
    }

    private function getLoansWithDecliningPayments($loans)
    {
        // This would require more complex analysis of payment patterns
        return $loans->filter(function($loan) {
            $recentPayments = $loan->transactions()
                ->where('type', 'loan_repayment')
                ->where('created_at', '>', now()->subMonths(3))
                ->count();
            return $recentPayments < 2; // Less than 2 payments in 3 months
        })->count();
    }

    private function getMembersWithMultipleOverdue($loans)
    {
        return $loans->filter(function($loan) {
            return $loan->member->loans()
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->count() > 1;
        })->unique('member_id')->count();
    }

    private function generateTransactionReport($startDate, $endDate)
    {
        $transactions = Transaction::with(['account.member', 'member'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $summary = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'completed_transactions' => $transactions->where('status', 'completed')->count(),
            'pending_transactions' => $transactions->where('status', 'pending')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'by_type' => $transactions->groupBy('type')->map(function($typeTransactions) {
                return [
                    'count' => $typeTransactions->count(),
                    'amount' => $typeTransactions->where('status', 'completed')->sum('amount'),
                    'avg_amount' => $typeTransactions->where('status', 'completed')->avg('amount') ?: 0
                ];
            }),
            'by_status' => $transactions->groupBy('status')->map(function($statusTransactions) {
                return [
                    'count' => $statusTransactions->count(),
                    'amount' => $statusTransactions->sum('amount')
                ];
            }),
            'peak_hour' => $this->calculatePeakTransactionHour($transactions),
            'top_members' => $this->getTopTransactionMembers($transactions),
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
            
            $dayTransactions = Transaction::whereBetween('created_at', [$dayStart, $dayEnd])->get();
            
            $deposits = $dayTransactions->where('type', 'deposit')->where('status', 'completed');
            $withdrawals = $dayTransactions->where('type', 'withdrawal')->where('status', 'completed');
            $loanDisbursements = $dayTransactions->where('type', 'loan_disbursement')->where('status', 'completed');
            $loanRepayments = $dayTransactions->where('type', 'loan_repayment')->where('status', 'completed');
            
            $daily[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $current->format('l'),
                'total_transactions' => $dayTransactions->count(),
                'completed_transactions' => $dayTransactions->where('status', 'completed')->count(),
                'pending_transactions' => $dayTransactions->where('status', 'pending')->count(),
                'failed_transactions' => $dayTransactions->where('status', 'failed')->count(),
                'total_amount' => $dayTransactions->where('status', 'completed')->sum('amount'),
                'deposits' => [
                    'count' => $deposits->count(),
                    'amount' => $deposits->sum('amount'),
                    'avg_amount' => $deposits->avg('amount') ?: 0
                ],
                'withdrawals' => [
                    'count' => $withdrawals->count(),
                    'amount' => $withdrawals->sum('amount'),
                    'avg_amount' => $withdrawals->avg('amount') ?: 0
                ],
                'loan_disbursements' => [
                    'count' => $loanDisbursements->count(),
                    'amount' => $loanDisbursements->sum('amount'),
                    'avg_amount' => $loanDisbursements->avg('amount') ?: 0
                ],
                'loan_repayments' => [
                    'count' => $loanRepayments->count(),
                    'amount' => $loanRepayments->sum('amount'),
                    'avg_amount' => $loanRepayments->avg('amount') ?: 0
                ],
                'net_cash_flow' => $deposits->sum('amount') - $withdrawals->sum('amount'),
                'unique_members' => $dayTransactions->where('status', 'completed')->unique('member_id')->count(),
                'peak_hour' => $this->getDayPeakHour($dayTransactions),
            ];
            
            $current->addDay();
        }
        
        // Calculate summary metrics
        $summaryMetrics = [
            'total_days' => count($daily),
            'avg_daily_transactions' => collect($daily)->avg('total_transactions'),
            'avg_daily_amount' => collect($daily)->avg('total_amount'),
            'highest_transaction_day' => collect($daily)->sortByDesc('total_transactions')->first(),
            'highest_amount_day' => collect($daily)->sortByDesc('total_amount')->first(),
            'busiest_day_of_week' => $this->getBusiestDayOfWeek($daily),
        ];
        
        return compact('daily', 'summaryMetrics');
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
    private function calculateRepaymentRate($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $duePayments = Loan::where('due_date', '>=', $startDate)
            ->where('due_date', '<=', $endDate)
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        $actualPayments = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->when($loanTypeId, fn($q) => $q->whereHas('loan', fn($sq) => $sq->where('loan_type_id', $loanTypeId)))
            ->when($branchId, fn($q) => $q->whereHas('loan.member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        return $duePayments > 0 ? round(($actualPayments / $duePayments) * 100, 2) : 0;
    }

    private function calculateDefaultRate($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $totalLoans = Loan::whereBetween('created_at', [$startDate, $endDate])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->count();
        
        $defaultedLoans = Loan::where('status', 'defaulted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->count();
        
        return $totalLoans > 0 ? round(($defaultedLoans / $totalLoans) * 100, 2) : 0;
    }

    private function calculatePortfolioAtRisk($loanTypeId = null, $branchId = null)
    {
        $totalPortfolio = Loan::whereIn('status', ['active', 'disbursed'])
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        $overdueLoans = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        return $totalPortfolio > 0 ? round(($overdueLoans / $totalPortfolio) * 100, 2) : 0;
    }

    private function calculateCollectionEfficiency($startDate, $endDate, $loanTypeId = null, $branchId = null)
    {
        $expectedCollections = Loan::where('due_date', '>=', $startDate)
            ->where('due_date', '<=', $endDate)
            ->when($loanTypeId, fn($q) => $q->where('loan_type_id', $loanTypeId))
            ->when($branchId, fn($q) => $q->whereHas('member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        $actualCollections = Transaction::where('type', 'loan_repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->when($loanTypeId, fn($q) => $q->whereHas('loan', fn($sq) => $sq->where('loan_type_id', $loanTypeId)))
            ->when($branchId, fn($q) => $q->whereHas('loan.member', fn($sq) => $sq->where('branch_id', $branchId)))
            ->sum('amount');
        
        return $expectedCollections > 0 ? round(($actualCollections / $expectedCollections) * 100, 2) : 0;
    }

    // Helper methods for enhanced transaction reporting
    private function calculatePeakTransactionHour($transactions)
    {
        $hourlyData = $transactions->groupBy(function($transaction) {
            return $transaction->created_at->format('H');
        })->map(function($hourTransactions) {
            return $hourTransactions->count();
        })->sortByDesc(function($count) {
            return $count;
        });

        $peakHour = $hourlyData->keys()->first();
        return $peakHour ? [
            'hour' => $peakHour,
            'count' => $hourlyData->first(),
            'formatted' => $peakHour . ':00 - ' . ($peakHour + 1) . ':00'
        ] : null;
    }

    private function getTopTransactionMembers($transactions)
    {
        return $transactions->where('status', 'completed')
            ->groupBy('member_id')
            ->map(function($memberTransactions) {
                $member = $memberTransactions->first()->member;
                return [
                    'member' => $member,
                    'transaction_count' => $memberTransactions->count(),
                    'total_amount' => $memberTransactions->sum('amount'),
                    'avg_amount' => $memberTransactions->avg('amount')
                ];
            })
            ->sortByDesc('transaction_count')
            ->take(10)
            ->values();
    }

    private function getDayPeakHour($dayTransactions)
    {
        if ($dayTransactions->isEmpty()) {
            return null;
        }

        $hourlyData = $dayTransactions->groupBy(function($transaction) {
            return $transaction->created_at->format('H');
        })->map(function($hourTransactions) {
            return $hourTransactions->count();
        });

        if ($hourlyData->isEmpty()) {
            return null;
        }

        $peakHour = $hourlyData->sortByDesc(function($count) {
            return $count;
        })->keys()->first();

        return [
            'hour' => $peakHour,
            'count' => $hourlyData[$peakHour],
            'formatted' => $peakHour . ':00'
        ];
    }

    private function getBusiestDayOfWeek($daily)
    {
        $dayTotals = collect($daily)->groupBy('day_name')->map(function($dayData) {
            return $dayData->sum('total_transactions');
        })->sortByDesc(function($total) {
            return $total;
        });

        $busiestDay = $dayTotals->keys()->first();
        return $busiestDay ? [
            'day' => $busiestDay,
            'total_transactions' => $dayTotals->first()
        ] : null;
    }

    private function generateHourlyAnalysis($startDate, $endDate)
    {
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourTransactions = $transactions->filter(function($transaction) use ($hour) {
                return $transaction->created_at->format('H') == sprintf('%02d', $hour);
            });

            $hourlyData[] = [
                'hour' => $hour,
                'formatted_hour' => sprintf('%02d:00', $hour),
                'transaction_count' => $hourTransactions->count(),
                'total_amount' => $hourTransactions->sum('amount'),
                'avg_amount' => $hourTransactions->avg('amount') ?: 0,
                'deposits' => $hourTransactions->where('type', 'deposit')->count(),
                'withdrawals' => $hourTransactions->where('type', 'withdrawal')->count(),
                'loan_transactions' => $hourTransactions->whereIn('type', ['loan_disbursement', 'loan_repayment'])->count(),
            ];
        }

        return compact('hourlyData');
    }

    private function generateTransactionTypeAnalysis($startDate, $endDate)
    {
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalTransactions = $transactions->count();

        $typeAnalysis = $transactions->groupBy('type')->map(function($typeTransactions, $type) use ($totalTransactions, $startDate, $endDate) {
            return [
                'type' => $type,
                'display_name' => ucwords(str_replace('_', ' ', $type)),
                'count' => $typeTransactions->count(),
                'total_amount' => $typeTransactions->sum('amount'),
                'avg_amount' => $typeTransactions->avg('amount'),
                'min_amount' => $typeTransactions->min('amount'),
                'max_amount' => $typeTransactions->max('amount'),
                'percentage_of_total' => $totalTransactions > 0 ? round(($typeTransactions->count() / $totalTransactions) * 100, 2) : 0,
                'daily_average' => $typeTransactions->count() / max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1),
            ];
        })->sortByDesc('count');

        return compact('typeAnalysis');
    }
} 