<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Loan;
use App\Models\LoanAccount;
use App\Models\Goal;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        
        if ($user->role === 'member') {
            return $this->memberDashboard($request, $user);
        } else {
            return $this->adminDashboard($request);
        }
    }

    private function memberDashboard(Request $request, $user): Response
    {
        // Get member's accounts (exclude loan accounts as they're special)
        $memberAccounts = Account::where('member_id', $user->id)
            ->where('account_type', '!=', Account::TYPE_LOAN_ACCOUNT)
            ->get();
        
        // Get member's loan accounts with loan relationship
        $memberLoanAccounts = LoanAccount::where('member_id', $user->id)->with('loan')->get();
        
        // Calculate detailed balances by account type
        $totalBalance = $memberAccounts->sum('balance');
        
        // Get all account types with their balances (including loan accounts)
        $accountBalances = $memberAccounts->groupBy('account_type')->map(function ($accounts) {
            return [
                'type' => $accounts->first()->account_type,
                'balance' => $accounts->sum('balance'),
                'count' => $accounts->count()
            ];
        });

        // Add loan accounts to the balance
        if ($memberLoanAccounts->count() > 0) {
            $loanAccountBalance = $memberLoanAccounts->sum('outstanding_principal');
            $accountBalances->push([
                'type' => 'loan_account',
                'balance' => $loanAccountBalance,
                'count' => $memberLoanAccounts->count()
            ]);
        }

        $accountBalances = $accountBalances->sortByDesc('balance');
        
        // Keep individual totals for backward compatibility
        $totalSavings = $memberAccounts->where('account_type', 'savings')->sum('balance');
        $totalShares = $memberAccounts->where('account_type', 'shares')->sum('balance');
        $activeLoans = $memberLoanAccounts->where('status', 'active')->count();
        $totalLoanOutstanding = $memberLoanAccounts->where('status', 'active')->sum('outstanding_principal');
        $totalLoanDisbursed = $memberLoanAccounts->sum('amount_disbursed');
        
        // Get member's transactions with pagination
        $recentTransactions = Transaction::where('member_id', $user->id)
            ->with(['account.member'])
            ->latest()
            ->paginate(10, ['*'], 'page', $request->get('page', 1));
        
        // Monthly transaction summary
        $monthlyTransactions = Transaction::where('member_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $monthlyTransactionAmount = Transaction::where('member_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');
        
        // Transaction trends (last 90 days)
        $transactionTrends = Transaction::where('member_id', $user->id)
            ->selectRaw('DATE(created_at) as date, 
                COALESCE(SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END), 0) as deposits,
                COALESCE(SUM(CASE WHEN type = \'withdrawal\' THEN amount ELSE 0 END), 0) as withdrawals')
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(90)
            ->get();
        
        // Get pending loan applications
        $pendingLoans = Loan::where('member_id', $user->id)
            ->where('status', 'pending')
            ->with(['loanType'])
            ->get();

        // Get current month's budget
        $currentBudget = $user->budgets()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->with(['items', 'expenses'])
            ->first();

        // Calculate monthly expenses from transactions
        $monthlyExpenses = Transaction::where('member_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth())
            ->sum('amount');

        return Inertia::render('dashboard', [
            'userRole' => 'member',
            'stats' => [
                'total_balance' => $totalBalance,
                'total_savings' => $totalSavings,
                'total_shares' => $totalShares,
                'active_loans' => $activeLoans,
                'total_loan_outstanding' => $totalLoanOutstanding,
            ],
            'account_balances' => $accountBalances->values()->toArray(),
            'loan_accounts' => $memberLoanAccounts,
            'pending_loans' => $pendingLoans,
            'transaction_trends' => $transactionTrends,
            'recent_transactions' => $recentTransactions,
            'current_budget' => $currentBudget,
            'monthly_expenses' => $monthlyExpenses,
        ]);
    }

    private function adminDashboard(Request $request): Response
    {
        // Get real statistics from the database
        $totalMembers = Member::count();
        $totalAccounts = Account::count();
        $totalSavings = Account::where('account_type', 'savings')->sum('balance');
        $totalShares = Account::where('account_type', 'shares')->sum('balance');
        
        // Active loans (from loan accounts)
        $activeLoans = LoanAccount::where('status', 'active')->count();
        $totalLoanAmount = LoanAccount::where('status', 'active')->sum('outstanding_principal');
        
        // Monthly transactions (current month)
        $currentMonth = now()->format('Y-m');
        $monthlyTransactions = Transaction::where('created_at', '>=', now()->startOfMonth())->count();
        $monthlyTransactionAmount = Transaction::where('created_at', '>=', now()->startOfMonth())->sum('amount');
        
        // Transaction trends (last 90 days for area chart)
        $transactionTrends = Transaction::selectRaw('DATE(created_at) as date, 
            COALESCE(SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END), 0) as deposits,
            COALESCE(SUM(CASE WHEN type = \'withdrawal\' THEN amount ELSE 0 END), 0) as withdrawals')
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(90)
            ->get();
        
        // Recent transactions with pagination
        $recentTransactions = Transaction::with(['account.member'])
            ->latest()
            ->paginate(10, ['*'], 'page', $request->get('page', 1));
        
        // Monthly growth (compared to last month)
        $lastMonth = now()->subMonth()->format('Y-m');
        $lastMonthSavings = Account::where('account_type', 'savings')
            ->where('created_at', '<', now()->subMonth()->endOfMonth())
            ->sum('balance');
        
        $savingsGrowth = $lastMonthSavings > 0 
            ? (($totalSavings - $lastMonthSavings) / $lastMonthSavings) * 100 
            : 0;

        return Inertia::render('dashboard', [
            'userRole' => 'admin',
            'stats' => [
                'total_members' => $totalMembers,
                'total_accounts' => $totalAccounts,
                'total_savings' => $totalSavings,
                'total_shares' => $totalShares,
                'active_loans' => $activeLoans,
                'total_loan_amount' => $totalLoanAmount,
                'monthly_transactions' => $monthlyTransactions,
                'monthly_transaction_amount' => $monthlyTransactionAmount,
                'savings_growth' => round($savingsGrowth, 2),
            ],
            'transaction_trends' => $transactionTrends,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}





