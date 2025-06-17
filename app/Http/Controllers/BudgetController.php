<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    /**
     * Display member's budget planner
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get selected month or default to current month
        $selectedDate = $request->has('month') 
            ? \Carbon\Carbon::createFromFormat('Y-m', $request->month)
            : now();
        
        // Get current month's budget
        $currentBudget = $user->budgets()
            ->where('month', $selectedDate->month)
            ->where('year', $selectedDate->year)
            ->with(['items', 'expenses'])
            ->first();

        // Get historical budgets for comparison
        $historicalBudgets = $user->budgets()
            ->where(function($query) {
                $query->where('year', '<', now()->year)
                    ->orWhere(function($q) {
                        $q->where('year', now()->year)
                            ->where('month', '<', now()->month);
                    });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->take(6)
            ->get();

        // Calculate savings trend
        $savingsTrend = $historicalBudgets->map(function($budget) {
            return [
                'month' => $budget->month,
                'year' => $budget->year,
                'planned' => $budget->savings_target,
                'actual' => $budget->total_income - $budget->expenses()->sum('amount')
            ];
        });

        // Get expense categories for the form
        $categories = Budget::CATEGORIES;

        return view('budget.index', compact(
            'currentBudget',
            'historicalBudgets',
            'savingsTrend',
            'categories'
        ));
    }

    /**
     * Show create budget form
     */
    public function create()
    {
        $categories = Budget::CATEGORIES;
        return view('budget.create', compact('categories'));
    }

    /**
     * Store new budget
     */
    public function store(Request $request)
    {
        \Log::info('Budget form data:', $request->all());

        $validated = $request->validate([
            'month' => 'required|string|date_format:Y-m',
            'expected_income' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'required|string|max:255',
            'amounts' => 'required|array',
            'amounts.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            \Log::info('Validated data:', $validated);

            // Parse month and year from the month field
            $date = \Carbon\Carbon::createFromFormat('Y-m', $validated['month']);
            
            // Calculate total expenses
            $totalExpenses = array_sum($validated['amounts']);
            
            // Create budget
            $budget = Auth::user()->budgets()->create([
                'month' => $date->month,
                'year' => $date->year,
                'total_income' => $validated['expected_income'],
                'total_expenses' => $totalExpenses,
                'savings_target' => $validated['expected_income'] - $totalExpenses,
                'notes' => $validated['notes'] ?? null,
                'status' => 'active'
            ]);

            \Log::info('Created budget:', $budget->toArray());

            // Create budget items
            foreach ($validated['categories'] as $index => $category) {
                $budget->items()->create([
                    'category' => $category,
                    'amount' => $validated['amounts'][$index],
                    'is_recurring' => true
                ]);
            }

            DB::commit();

            return redirect()->route('budget.show', $budget)
                ->with('success', __('Budget created successfully! You can now start tracking your expenses.'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create budget:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', __('Failed to create budget: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Show budget details
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);

        $budget->load(['items', 'expenses']);
        
        // Group expenses by category
        $expensesByCategory = $budget->expenses
            ->groupBy('category')
            ->map(function($expenses) {
                return $expenses->sum('amount');
            });

        // Calculate category summaries
        $categorySummaries = collect(Budget::CATEGORIES)->map(function($name, $category) use ($budget, $expensesByCategory) {
            $planned = $budget->items->where('category', $category)->sum('amount');
            $actual = $expensesByCategory[$category] ?? 0;
            return [
                'name' => $name,
                'planned' => $planned,
                'actual' => $actual,
                'remaining' => $planned - $actual,
                'progress' => $planned > 0 ? min(100, ($actual / $planned) * 100) : 0
            ];
        });

        return view('budget.show', compact('budget', 'categorySummaries'));
    }

    /**
     * Record new expense
     */
    public function recordExpense(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'category' => 'required|string|in:' . implode(',', array_keys(Budget::CATEGORIES)),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        // Handle receipt upload
        $receiptUrl = null;
        if ($request->hasFile('receipt')) {
            $receiptUrl = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = $budget->expenses()->create([
            'category' => $validated['category'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'receipt_url' => $receiptUrl
        ]);

        return back()->with('success', 'Expense recorded successfully!');
    }

    /**
     * Delete expense
     */
    public function deleteExpense(Budget $budget, BudgetExpense $expense)
    {
        $this->authorize('update', $budget);

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully!');
    }

    /**
     * Generate budget report
     */
    public function report(Budget $budget)
    {
        $this->authorize('view', $budget);

        $budget->load(['items', 'expenses']);
        
        // Prepare report data
        $reportData = [
            'budget' => $budget,
            'summary' => $budget->getExpenseSummaryAttribute(),
            'savings' => [
                'target' => $budget->savings_target,
                'actual' => $budget->total_income - $budget->expenses()->sum('amount'),
                'progress' => $budget->getSavingsProgressAttribute()
            ]
        ];

        return view('budget.report', $reportData);
    }
} 