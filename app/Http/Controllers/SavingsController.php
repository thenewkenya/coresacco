<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SavingsController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display savings account management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access to savings management.');
        }

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $accountType = $request->get('account_type');
        $branch = $request->get('branch');

        // Build query
        $query = Account::with(['member'])
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('account_number', 'like', "%{$search}%")
                          ->orWhereHas('member', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($accountType, function ($q) use ($accountType) {
                $q->where('account_type', $accountType);
            });

        $accounts = $query->latest()->paginate(20);

        // Get summary statistics
        $totalAccounts = Account::count();
        $activeAccounts = Account::where('status', Account::STATUS_ACTIVE)->count();
        $totalBalance = Account::where('status', Account::STATUS_ACTIVE)->sum('balance');
        $thisMonthDeposits = Transaction::where('type', Transaction::TYPE_DEPOSIT)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Get account types and branches for filters
        $accountTypes = Account::select('account_type')->distinct()->pluck('account_type');
        $branches = \App\Models\Branch::all();

        return view('savings.index', compact(
            'accounts',
            'totalAccounts',
            'activeAccounts', 
            'totalBalance',
            'thisMonthDeposits',
            'accountTypes',
            'search',
            'status',
            'accountType'
        ));
    }

    /**
     * Display member's own savings accounts
     */
    public function my(Request $request)
    {
        $user = Auth::user();
        
        // Get member's accounts with recent transactions
        $accounts = $user->accounts()
            ->with(['transactions' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        // Calculate summary for each account
        $accountSummaries = [];
        foreach ($accounts as $account) {
            $accountSummaries[$account->id] = $this->transactionService->getAccountTransactionSummary($account, 30);
        }

        // Get savings goals (if implemented)
        $savingsGoals = []; // Placeholder for future implementation

        // Calculate total savings and growth
        $totalSavings = $accounts->sum('balance');
        $lastMonthBalance = $accounts->sum(function($account) {
            return $account->transactions()
                ->where('created_at', '<=', now()->subMonth())
                ->latest()
                ->first()?->balance_after ?? 0;
        });
        
        $growthAmount = $totalSavings - $lastMonthBalance;
        $growthPercentage = $lastMonthBalance > 0 ? ($growthAmount / $lastMonthBalance) * 100 : 0;

        return view('savings.my', compact(
            'accounts',
            'accountSummaries',
            'savingsGoals',
            'totalSavings',
            'growthAmount',
            'growthPercentage'
        ));
    }

    /**
     * Show account details
     */
    public function show(Account $account)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $account->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this account.');
        }

        $account->load(['member']);
        
        // Get transactions with pagination
        $transactions = $account->transactions()
            ->with('member')
            ->latest()
            ->paginate(20);

        // Get account summary
        $summary = $this->transactionService->getAccountTransactionSummary($account, 90);

        // Calculate interest earned (if applicable)
        $interestEarned = $account->transactions()
            ->where('type', Transaction::TYPE_INTEREST)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        return view('savings.show', compact('account', 'transactions', 'summary', 'interestEarned'));
    }

    /**
     * Show create account form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized to create accounts.');
        }

        $members = User::where('role', 'member')->get();
        $accountTypes = Account::getAccountTypes();

        return view('savings.create', compact('members', 'accountTypes'));
    }

    /**
     * Store new account
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized to create accounts.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'account_type' => ['required', Rule::in(Account::getAccountTypes())],
            'initial_deposit' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create account
            $account = Account::create([
                'member_id' => $validated['member_id'],
                'account_number' => Account::generateAccountNumber(),
                'account_type' => $validated['account_type'],
                'balance' => 0,
                'status' => Account::STATUS_ACTIVE,
                'currency' => 'KES',
            ]);

            // Process initial deposit if provided
            if ($validated['initial_deposit'] > 0) {
                $this->transactionService->processDeposit(
                    $account,
                    $validated['initial_deposit'],
                    'Initial deposit for new account',
                    ['account_opening' => true, 'processed_by' => $user->id]
                );
            }

            DB::commit();

            return redirect()->route('savings.show', $account)
                ->with('success', 'Account created successfully with initial deposit of KES ' . number_format($validated['initial_deposit']));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create account: ' . $e->getMessage());
        }
    }

    /**
     * Process deposit
     */
    public function deposit(Request $request, Account $account)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $account->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this account.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:500000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $transaction = $this->transactionService->processDeposit(
                $account,
                $validated['amount'],
                $validated['description'] ?? 'Deposit',
                ['processed_by' => $user->id]
            );

            return back()->with('success', 'Deposit of KES ' . number_format($validated['amount']) . ' processed successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Deposit failed: ' . $e->getMessage());
        }
    }

    /**
     * Process withdrawal
     */
    public function withdraw(Request $request, Account $account)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $account->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this account.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $transaction = $this->transactionService->processWithdrawal(
                $account,
                $validated['amount'],
                $validated['description'] ?? 'Withdrawal',
                ['processed_by' => $user->id]
            );

            $message = $transaction->status === Transaction::STATUS_PENDING 
                ? 'Withdrawal request submitted for approval (amount exceeds limit).'
                : 'Withdrawal of KES ' . number_format($validated['amount']) . ' processed successfully.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Withdrawal failed: ' . $e->getMessage());
        }
    }

    /**
     * Calculate and apply interest
     */
    public function calculateInterest(Account $account)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to calculate interest.');
        }

        try {
            // Get interest rate (8.5% annually as per system settings)
            $annualRate = 8.5;
            $monthlyRate = $annualRate / 12 / 100;
            
            // Calculate interest on current balance
            $interestAmount = $account->balance * $monthlyRate;
            
            if ($interestAmount > 0) {
                $transaction = $this->transactionService->processDeposit(
                    $account,
                    $interestAmount,
                    'Monthly interest payment',
                    [
                        'transaction_type' => Transaction::TYPE_INTEREST,
                        'interest_rate' => $annualRate,
                        'calculated_by' => $user->id
                    ]
                );

                return back()->with('success', 'Interest of KES ' . number_format($interestAmount, 2) . ' calculated and applied.');
            } else {
                return back()->with('info', 'No interest calculated (zero balance).');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Interest calculation failed: ' . $e->getMessage());
        }
    }

    /**
     * Update account status
     */
    public function updateStatus(Request $request, Account $account)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to update account status.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in([Account::STATUS_ACTIVE, Account::STATUS_SUSPENDED, Account::STATUS_CLOSED])],
            'reason' => 'nullable|string|max:255',
        ]);

        $oldStatus = $account->status;
        $account->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Account status updated from {$oldStatus} to {$validated['status']}.");
    }

    /**
     * Generate savings report
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to generate reports.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Build query
        $query = Account::with(['member']);

        $accounts = $query->get();

        // Calculate statistics
        $stats = [
            'total_accounts' => $accounts->count(),
            'active_accounts' => $accounts->where('status', Account::STATUS_ACTIVE)->count(),
            'total_balance' => $accounts->sum('balance'),
            'average_balance' => $accounts->avg('balance'),
            'deposits_this_period' => Transaction::where('type', Transaction::TYPE_DEPOSIT)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'withdrawals_this_period' => Transaction::where('type', Transaction::TYPE_WITHDRAWAL)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
        ];

        return view('savings.report', compact('accounts', 'stats', 'startDate', 'endDate'));
    }
} 