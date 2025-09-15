<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Goal;
use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SavingsController extends Controller
{
    /**
     * Display savings dashboard (staff view)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member') {
            return redirect()->route('savings.my');
        }

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $accountType = $request->get('account_type');

        // Build query for savings accounts
        $query = Account::with(['member'])
            ->whereIn('account_type', ['savings', 'shares'])
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
        $totalAccounts = Account::whereIn('account_type', ['savings', 'shares'])->count();
        $activeAccounts = Account::whereIn('account_type', ['savings', 'shares'])
            ->where('status', 'active')->count();
        $totalBalance = Account::whereIn('account_type', ['savings', 'shares'])
            ->where('status', 'active')->sum('balance');
        $thisMonthDeposits = Transaction::where('type', 'deposit')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Get goals statistics
        $totalGoals = Goal::count();
        $activeGoals = Goal::where('status', Goal::STATUS_ACTIVE)->count();
        $completedGoals = Goal::where('status', Goal::STATUS_COMPLETED)->count();
        $totalGoalAmount = Goal::where('status', Goal::STATUS_ACTIVE)->sum('target_amount');
        $totalGoalProgress = Goal::where('status', Goal::STATUS_ACTIVE)->sum('current_amount');

        return Inertia::render('savings/index', [
            'accounts' => $accounts,
            'stats' => [
                'totalAccounts' => $totalAccounts,
                'activeAccounts' => $activeAccounts,
                'totalBalance' => $totalBalance,
                'thisMonthDeposits' => $thisMonthDeposits,
                'totalGoals' => $totalGoals,
                'activeGoals' => $activeGoals,
                'completedGoals' => $completedGoals,
                'totalGoalAmount' => $totalGoalAmount,
                'totalGoalProgress' => $totalGoalProgress,
            ],
            'filters' => $request->only(['search', 'status', 'account_type']),
            'accountTypes' => ['savings', 'shares'],
            'statusOptions' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'frozen' => 'Frozen',
                'closed' => 'Closed',
            ],
        ]);
    }

    /**
     * Display member's own savings dashboard
     */
    public function my(Request $request): Response
    {
        $user = Auth::user();
        
        // Get member's savings accounts
        $accounts = $user->accounts()
            ->whereIn('account_type', ['savings', 'shares'])
            ->with(['transactions' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        // Get member's goals
        $goals = $user->goals()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get member's budgets
        $budgets = $user->budgets()
            ->with('items')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();

        // Calculate summary statistics
        $totalSavings = $accounts->sum('balance');
        $lastMonthBalance = $accounts->sum(function($account) {
            return $account->transactions()
                ->where('created_at', '<=', now()->subMonth())
                ->latest()
                ->first()?->balance_after ?? 0;
        });
        
        $growthAmount = $totalSavings - $lastMonthBalance;
        $growthPercentage = $lastMonthBalance > 0 ? ($growthAmount / $lastMonthBalance) * 100 : 0;

        // Goals statistics
        $activeGoals = $goals->where('status', Goal::STATUS_ACTIVE);
        $completedGoals = $goals->where('status', Goal::STATUS_COMPLETED);
        $totalGoalProgress = $activeGoals->sum('current_amount');
        $totalGoalTarget = $activeGoals->sum('target_amount');

        return Inertia::render('savings/my', [
            'accounts' => $accounts,
            'goals' => $goals,
            'budgets' => $budgets,
            'stats' => [
                'totalSavings' => $totalSavings,
                'growthAmount' => $growthAmount,
                'growthPercentage' => $growthPercentage,
                'activeGoals' => $activeGoals->count(),
                'completedGoals' => $completedGoals->count(),
                'totalGoalProgress' => $totalGoalProgress,
                'totalGoalTarget' => $totalGoalTarget,
                'goalProgressPercentage' => $totalGoalTarget > 0 ? ($totalGoalProgress / $totalGoalTarget) * 100 : 0,
            ],
        ]);
    }

    /**
     * Display savings goals
     */
    public function goals(Request $request): Response
    {
        $user = Auth::user();
        
        $query = Goal::with(['member'])
            ->when($user->role === 'member', function ($query) use ($user) {
                return $query->where('member_id', $user->id);
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQuery) use ($request) {
                      $memberQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $goals = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $totalGoals = Goal::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->count();

        $activeGoals = Goal::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', Goal::STATUS_ACTIVE)->count();

        $completedGoals = Goal::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', Goal::STATUS_COMPLETED)->count();

        $totalTargetAmount = Goal::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', Goal::STATUS_ACTIVE)->sum('target_amount');

        $totalCurrentAmount = Goal::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', Goal::STATUS_ACTIVE)->sum('current_amount');

        return Inertia::render('savings/goals/index', [
            'goals' => $goals,
            'stats' => [
                'totalGoals' => $totalGoals,
                'activeGoals' => $activeGoals,
                'completedGoals' => $completedGoals,
                'totalTargetAmount' => $totalTargetAmount,
                'totalCurrentAmount' => $totalCurrentAmount,
            ],
            'filters' => $request->only(['search', 'status', 'type']),
            'statusOptions' => [
                'active' => 'Active',
                'completed' => 'Completed',
                'paused' => 'Paused',
                'cancelled' => 'Cancelled',
            ],
            'typeOptions' => [
                'emergency_fund' => 'Emergency Fund',
                'home_purchase' => 'Home Purchase',
                'education' => 'Education',
                'retirement' => 'Retirement',
                'custom' => 'Custom',
            ],
        ]);
    }

    /**
     * Display budget planning
     */
    public function budget(Request $request): Response
    {
        $user = Auth::user();
        
        $query = Budget::with(['items'])
            ->when($user->role === 'member', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            });

        // Apply filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $budgets = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Get current budget
        $currentBudget = Budget::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->with('items')
            ->first();

        // Calculate statistics
        $totalBudgets = Budget::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->count();

        $activeBudgets = Budget::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->where('status', Budget::STATUS_ACTIVE)->count();

        return Inertia::render('savings/budget/index', [
            'budgets' => $budgets,
            'currentBudget' => $currentBudget,
            'stats' => [
                'totalBudgets' => $totalBudgets,
                'activeBudgets' => $activeBudgets,
            ],
            'filters' => $request->only(['year', 'month']),
            'categories' => Budget::CATEGORIES,
            'years' => range(now()->year - 2, now()->year + 1),
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ],
        ]);
    }

    /**
     * Show create goal form
     */
    public function createGoal(): Response
    {
        $user = Auth::user();
        
        $accounts = $user->accounts()
            ->whereIn('account_type', ['savings', 'shares'])
            ->where('status', 'active')
            ->get();

        return Inertia::render('savings/goals/create', [
            'accounts' => $accounts,
            'typeOptions' => [
                'emergency_fund' => 'Emergency Fund',
                'home_purchase' => 'Home Purchase',
                'education' => 'Education',
                'retirement' => 'Retirement',
                'custom' => 'Custom',
            ],
            'frequencyOptions' => [
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'yearly' => 'Yearly',
            ],
        ]);
    }

    /**
     * Store new goal
     */
    public function storeGoal(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('=== GOAL CREATION REQUEST START ===');
        \Log::info('Goal creation request:', $request->all());
        \Log::info('Request method:', ['method' => $request->method()]);
        \Log::info('Request URL:', ['url' => $request->url()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:1000',
            'target_date' => 'required|date|after_or_equal:tomorrow',
            'type' => 'required|in:emergency_fund,home_purchase,education,retirement,custom',
            'auto_save_amount' => 'nullable|numeric|min:100',
            'auto_save_frequency' => 'nullable|in:weekly,monthly,quarterly,yearly',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $goal = Goal::create([
                'member_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'target_amount' => $request->target_amount,
                'current_amount' => 0,
                'target_date' => $request->target_date,
                'type' => $request->type,
                'status' => Goal::STATUS_ACTIVE,
                'auto_save_amount' => $request->auto_save_amount,
                'auto_save_frequency' => $request->auto_save_frequency,
            ]);

            DB::commit();

            \Log::info('Goal created successfully:', ['goal_id' => $goal->id]);

            return redirect()->route('savings.goals')
                           ->with('success', 'Savings goal created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Goal creation failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Failed to create goal: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show goal details
     */
    public function showGoal(Goal $goal): Response
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $goal->member_id !== $user->id) {
            abort(403, 'You can only view your own goals.');
        }

        $goal->load(['member']);

        // Calculate progress
        $progressPercentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
        $daysRemaining = now()->diffInDays($goal->target_date, false);
        $daysRemaining = $daysRemaining > 0 ? $daysRemaining : 0;

        return Inertia::render('savings/goals/show', [
            'goal' => $goal,
            'progressPercentage' => $progressPercentage,
            'daysRemaining' => $daysRemaining,
        ]);
    }

    /**
     * Update goal
     */
    public function updateGoal(Request $request, Goal $goal)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $goal->member_id !== $user->id) {
            abort(403, 'You can only update your own goals.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:1000',
            'target_date' => 'required|date|after:today',
            'status' => 'required|in:active,completed,paused,cancelled',
            'auto_save_amount' => 'nullable|numeric|min:100',
            'auto_save_frequency' => 'nullable|in:weekly,monthly,quarterly,yearly',
        ]);

        $goal->update($request->only([
            'title', 'description', 'target_amount', 'target_date', 
            'status', 'auto_save_amount', 'auto_save_frequency'
        ]));

        return back()->with('success', 'Goal updated successfully!');
    }

    /**
     * Delete goal
     */
    public function destroyGoal(Goal $goal)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $goal->member_id !== $user->id) {
            abort(403, 'You can only delete your own goals.');
        }

        $goal->delete();

        return redirect()->route('savings.goals')
                       ->with('success', 'Goal deleted successfully!');
    }

    /**
     * Contribute to goal
     */
    public function contributeToGoal(Request $request, Goal $goal)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $goal->member_id !== $user->id) {
            abort(403, 'You can only contribute to your own goals.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $account = Account::findOrFail($request->account_id);
            
            // Check if account belongs to user
            if ($user->role === 'member' && $account->member_id !== $user->id) {
                throw new \Exception('Account does not belong to you.');
            }

            // Check if account has sufficient balance
            if ($account->balance < $request->amount) {
                throw new \Exception('Insufficient account balance.');
            }

            // Create transaction
            $transaction = Transaction::create([
                'member_id' => $user->id,
                'account_id' => $account->id,
                'type' => 'goal_contribution',
                'amount' => $request->amount,
                'description' => $request->description ?? "Contribution to goal: {$goal->title}",
                'reference_number' => $this->generateReferenceNumber(),
                'status' => 'completed',
                'balance_before' => $account->balance,
                'balance_after' => $account->balance - $request->amount,
            ]);

            // Update account balance
            $account->update(['balance' => $account->balance - $request->amount]);

            // Update goal progress
            $goal->update(['current_amount' => $goal->current_amount + $request->amount]);

            // Check if goal is completed
            if ($goal->current_amount >= $goal->target_amount) {
                $goal->update(['status' => Goal::STATUS_COMPLETED]);
            }

            DB::commit();

            return back()->with('success', 'Contribution of KSh ' . number_format($request->amount) . ' added to goal successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to contribute to goal: ' . $e->getMessage()]);
        }
    }

    /**
     * Show create budget form
     */
    public function createBudget(): Response
    {
        $user = Auth::user();
        
        \Log::info('CreateBudget - User authenticated:', ['user_id' => $user?->id, 'user_name' => $user?->name]);
        
        return Inertia::render('savings/budget/create', [
            'categories' => Budget::CATEGORIES,
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ],
            'years' => range(now()->year, now()->year + 1),
        ]);
    }

    /**
     * Store new budget
     */
    public function storeBudget(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Budget creation request:', $request->all());
        
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:' . now()->year,
            'total_income' => 'required|numeric|min:0',
            'savings_target' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Calculate total expenses
            $totalExpenses = collect($request->items)->sum('amount');

            // Create budget
            $budget = Budget::create([
                'user_id' => $user->id,
                'month' => $request->month,
                'year' => $request->year,
                'total_income' => $request->total_income,
                'total_expenses' => $totalExpenses,
                'savings_target' => $request->savings_target,
                'notes' => $request->notes,
                'status' => Budget::STATUS_ACTIVE,
            ]);

            // Create budget items
            foreach ($request->items as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'category' => $item['category'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();

            \Log::info('Budget created successfully:', ['budget_id' => $budget->id]);

            return redirect()->route('savings.budget')
                           ->with('success', 'Budget created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Budget creation failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Failed to create budget: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show budget details
     */
    public function showBudget(Budget $budget): Response
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $budget->user_id !== $user->id) {
            abort(403, 'You can only view your own budgets.');
        }

        $budget->load(['items']);

        // Calculate budget analysis
        $totalExpenses = $budget->items->sum('amount');
        $remainingIncome = $budget->total_income - $totalExpenses;
        $savingsAchieved = $remainingIncome - $budget->savings_target;
        $savingsPercentage = $budget->total_income > 0 ? ($budget->savings_target / $budget->total_income) * 100 : 0;

        return Inertia::render('savings/budget/show', [
            'budget' => $budget,
            'analysis' => [
                'totalExpenses' => $totalExpenses,
                'remainingIncome' => $remainingIncome,
                'savingsAchieved' => $savingsAchieved,
                'savingsPercentage' => $savingsPercentage,
            ],
        ]);
    }

    /**
     * Update budget
     */
    public function updateBudget(Request $request, Budget $budget)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $budget->user_id !== $user->id) {
            abort(403, 'You can only update your own budgets.');
        }

        $request->validate([
            'total_income' => 'required|numeric|min:0',
            'savings_target' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,completed',
        ]);

        $budget->update($request->only(['total_income', 'savings_target', 'notes', 'status']));

        return back()->with('success', 'Budget updated successfully!');
    }

    private function generateReferenceNumber(): string
    {
        do {
            $referenceNumber = 'TXN' . date('Ymd') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $exists = Transaction::where('reference_number', $referenceNumber)->exists();
        } while ($exists);

        return $referenceNumber;
    }
}