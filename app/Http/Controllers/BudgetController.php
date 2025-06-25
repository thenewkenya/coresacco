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
     * Display member's budget planner with AI insights
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get selected month or default to current month
        $selectedDate = now();
        
        if ($request->has('month')) {
            try {
                $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $request->month);
            } catch (\Exception $e) {
                // Invalid month format, use current month
                $selectedDate = now();
            }
        }
        
        // Get current month's budget
        $currentBudget = $user->budgets()
            ->where('month', $selectedDate->month)
            ->where('year', $selectedDate->year)
            ->with(['items', 'expenses'])
            ->first();

        // Get historical budgets for comparison (excluding current selected month)
        $historicalBudgets = $user->budgets()
            ->where(function($query) use ($selectedDate) {
                $query->where('year', '<', $selectedDate->year)
                    ->orWhere(function($q) use ($selectedDate) {
                        $q->where('year', $selectedDate->year)
                            ->where('month', '<', $selectedDate->month);
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

        // Simplified - no complex insights needed for simple budget view
        $smartInsights = null;
        $smartBudgetSuggestions = null;
        $financialHealth = null;

        // Get expense categories for the form
        $categories = Budget::CATEGORIES;

        // Calculate navigation months
        $currentMonth = $selectedDate->copy();
        $prevMonth = $selectedDate->copy()->subMonth();
        $nextMonth = $selectedDate->copy()->addMonth();

        // Ensure historicalBudgets is a collection
        if (!$historicalBudgets) {
            $historicalBudgets = collect();
        }

        return view('budget.index', compact(
            'currentBudget',
            'historicalBudgets',
            'savingsTrend',
            'categories',
            'smartInsights',
            'smartBudgetSuggestions',
            'financialHealth',
            'selectedDate',
            'currentMonth',
            'prevMonth',
            'nextMonth'
        ));
    }

    /**
     * Show create budget form with AI suggestions
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $categories = Budget::CATEGORIES;
        
        // Generate AI-powered budget suggestions
        $suggestedIncome = $request->get('income', 0);
        $smartSuggestions = null;
        
        if ($suggestedIncome > 0) {
            $smartSuggestions = Budget::generateSmartBudget($user, $suggestedIncome);
        }
        
        // Get user's income history for better suggestions
        $incomeHistory = $user->budgets()
            ->latest()
            ->take(3)
            ->pluck('total_income')
            ->avg();

        return view('budget.create', compact(
            'categories', 
            'smartSuggestions', 
            'incomeHistory',
            'suggestedIncome'
        ));
    }

    /**
     * Store new budget with AI validation
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
            
            // AI-powered budget validation
            $budgetWarnings = $this->validateBudgetWithAI($validated);
            
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

            $message = __('Budget created successfully! You can now start tracking your expenses.');
            
            // Add AI warnings to the success message
            if (!empty($budgetWarnings)) {
                $message .= ' Note: ' . implode(' ', $budgetWarnings);
            }

            return redirect()->route('budget.show', $budget)
                ->with('success', $message);

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
     * Show budget details with AI insights
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

        // Get AI insights
        $smartInsights = $budget->getSmartInsights();

        // Get spending trends
        $spendingTrends = $this->getSpendingTrends($budget);

        return view('budget.show', compact(
            'budget', 
            'categorySummaries', 
            'smartInsights',
            'spendingTrends'
        ));
    }

    /**
     * Record new expense with smart categorization
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

        // Smart category suggestion based on description
        $suggestedCategory = $this->suggestCategoryFromDescription($validated['description']);
        if ($suggestedCategory && $suggestedCategory !== $validated['category']) {
            session()->flash('info', "Based on the description, we suggest categorizing this as '{$suggestedCategory}' instead of '{$validated['category']}'.");
        }

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

        // Check for budget alerts
        $alerts = $this->checkBudgetAlerts($budget, $validated['category']);
        
        if (!empty($alerts)) {
            session()->flash('warning', implode(' ', $alerts));
        }

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
     * Generate comprehensive budget report with AI insights
     */
    public function report(Budget $budget)
    {
        $this->authorize('view', $budget);

        $budget->load(['items', 'expenses']);
        
        // Get AI insights
        $smartInsights = $budget->getSmartInsights();
        
        // Prepare comprehensive report data
        $reportData = [
            'budget' => $budget,
            'summary' => $budget->getExpenseSummaryAttribute(),
            'savings' => [
                'target' => $budget->savings_target,
                'actual' => $budget->total_income - $budget->expenses()->sum('amount'),
                'progress' => $budget->getSavingsProgressAttribute()
            ],
            'insights' => $smartInsights,
            'trends' => $this->getSpendingTrends($budget),
            'recommendations' => $this->getPersonalizedRecommendations($budget)
        ];

        return view('budget.report', $reportData);
    }

    /**
     * Get smart budget suggestions API endpoint
     */
    public function getSmartSuggestions(Request $request)
    {
        $income = $request->get('income', 0);
        $user = Auth::user();
        
        if ($income <= 0) {
            return response()->json(['error' => 'Invalid income amount'], 400);
        }
        
        $suggestions = Budget::generateSmartBudget($user, $income);
        
        return response()->json([
            'suggestions' => $suggestions,
            'total_allocated' => array_sum(array_column($suggestions, 'amount')),
            'remaining' => $income - array_sum(array_column($suggestions, 'amount'))
        ]);
    }

    /**
     * AI-powered budget validation
     */
    private function validateBudgetWithAI(array $budgetData): array
    {
        $warnings = [];
        $income = $budgetData['expected_income'];
        $totalExpenses = array_sum($budgetData['amounts']);
        
        // Check savings rate
        $savingsRate = (($income - $totalExpenses) / $income) * 100;
        if ($savingsRate < 10) {
            $warnings[] = "Your savings rate is low ({$savingsRate}%). Aim for at least 20%.";
        }
        
        // Check category allocations
        foreach ($budgetData['categories'] as $index => $category) {
            $amount = $budgetData['amounts'][$index];
            $percentage = ($amount / $income) * 100;
            
            $recommended = Budget::RECOMMENDED_PERCENTAGES[$category] ?? null;
            if ($recommended && $percentage > $recommended['max']) {
                $categoryName = Budget::CATEGORIES[$category];
                $warnings[] = "You're allocating {$percentage}% to {$categoryName}, which is above the recommended {$recommended['max']}%.";
            }
        }
        
        return $warnings;
    }

    /**
     * Suggest category based on expense description
     */
    private function suggestCategoryFromDescription(string $description): ?string
    {
        $description = strtolower($description);
        
        $categoryKeywords = [
            'housing' => ['rent', 'mortgage', 'utilities', 'electricity', 'water', 'gas', 'internet'],
            'transportation' => ['fuel', 'petrol', 'diesel', 'bus', 'taxi', 'uber', 'matatu', 'car', 'vehicle'],
            'food' => ['grocery', 'supermarket', 'restaurant', 'food', 'lunch', 'dinner', 'breakfast'],
            'healthcare' => ['hospital', 'doctor', 'medicine', 'pharmacy', 'medical', 'health'],
            'education' => ['school', 'university', 'college', 'books', 'tuition', 'course'],
            'entertainment' => ['movie', 'cinema', 'music', 'game', 'party', 'club', 'entertainment'],
            'personal' => ['haircut', 'salon', 'cosmetics', 'clothing', 'shoes', 'personal']
        ];
        
        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return null;
    }

    /**
     * Check for budget alerts
     */
    private function checkBudgetAlerts(Budget $budget, string $category): array
    {
        $alerts = [];
        
        // Get category item
        $categoryItem = $budget->items()->where('category', $category)->first();
        if (!$categoryItem) {
            return $alerts;
        }
        
        $totalSpent = $budget->expenses()->where('category', $category)->sum('amount');
        $percentage = ($totalSpent / $categoryItem->amount) * 100;
        
        if ($percentage >= 100) {
            $alerts[] = "⚠️ You've exceeded your {$categoryItem->category_name} budget!";
        } elseif ($percentage >= 80) {
            $alerts[] = "⚡ You've used 80% of your {$categoryItem->category_name} budget.";
        }
        
        return $alerts;
    }

    /**
     * Calculate overall financial health
     */
    private function calculateFinancialHealth($user): array
    {
        $recentBudgets = $user->budgets()->latest()->take(3)->get();
        
        if ($recentBudgets->isEmpty()) {
            return [
                'score' => 0,
                'status' => 'No Data',
                'message' => 'Create your first budget to see your financial health score.'
            ];
        }
        
        $avgSavingsRate = $recentBudgets->avg(function($budget) {
            return $budget->total_income > 0 ? 
                (($budget->total_income - $budget->expenses()->sum('amount')) / $budget->total_income) * 100 : 0;
        });
        
        $consistencyScore = $this->calculateBudgetConsistency($recentBudgets);
        
        $overallScore = ($avgSavingsRate * 0.6) + ($consistencyScore * 0.4);
        
        $status = 'Poor';
        $message = 'Your financial health needs improvement.';
        
        if ($overallScore >= 80) {
            $status = 'Excellent';
            $message = 'Your financial health is excellent! Keep it up!';
        } elseif ($overallScore >= 60) {
            $status = 'Good';
            $message = 'Your financial health is good with room for improvement.';
        } elseif ($overallScore >= 40) {
            $status = 'Fair';
            $message = 'Your financial health is fair. Consider our recommendations.';
        }
        
        return [
            'score' => round($overallScore),
            'status' => $status,
            'message' => $message,
            'savings_rate' => round($avgSavingsRate, 1)
        ];
    }

    /**
     * Calculate budget consistency score
     */
    private function calculateBudgetConsistency($budgets): float
    {
        if ($budgets->count() < 2) {
            return 50; // Neutral score for insufficient data
        }
        
        $adherenceScores = $budgets->map(function($budget) {
            $planned = $budget->items()->sum('amount');
            $actual = $budget->expenses()->sum('amount');
            
            if ($planned == 0) return 0;
            
            $adherence = 100 - min(100, abs(($actual - $planned) / $planned) * 100);
            return $adherence;
        });
        
        return $adherenceScores->avg();
    }

    /**
     * Get spending trends
     */
    private function getSpendingTrends(Budget $budget): array
    {
        $user = $budget->user;
        $historicalBudgets = $user->budgets()
            ->where('id', '!=', $budget->id)
            ->orderBy('year')
            ->orderBy('month')
            ->take(6)
            ->get();
        
        $trends = [];
        
        foreach (Budget::CATEGORIES as $categoryKey => $categoryName) {
            $trendData = $historicalBudgets->map(function($histBudget) use ($categoryKey) {
                return $histBudget->expenses()->where('category', $categoryKey)->sum('amount');
            })->values()->toArray();
            
            $currentSpending = $budget->expenses()->where('category', $categoryKey)->sum('amount');
            $trendData[] = $currentSpending;
            
            $trends[$categoryKey] = [
                'name' => $categoryName,
                'data' => $trendData,
                'current' => $currentSpending,
                'trend' => $this->calculateTrendDirection($trendData)
            ];
        }
        
        return $trends;
    }

    /**
     * Calculate trend direction
     */
    private function calculateTrendDirection(array $data): string
    {
        if (count($data) < 2) return 'stable';
        
        $recent = array_slice($data, -3);
        $older = array_slice($data, 0, -3);
        
        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = count($older) > 0 ? array_sum($older) / count($older) : $recentAvg;
        
        if ($recentAvg > $olderAvg * 1.1) return 'increasing';
        if ($recentAvg < $olderAvg * 0.9) return 'decreasing';
        return 'stable';
    }

    /**
     * Get personalized recommendations
     */
    private function getPersonalizedRecommendations(Budget $budget): array
    {
        $recommendations = [];
        $insights = $budget->getSmartInsights();
        
        // Add top savings tips
        if (!empty($insights['savings_tips'])) {
            $topTips = array_slice($insights['savings_tips'], 0, 3);
            foreach ($topTips as $tip) {
                $recommendations[] = [
                    'type' => 'savings',
                    'title' => 'Optimize ' . $tip['category'],
                    'description' => $tip['tip'],
                    'impact' => 'Potential savings: KES ' . number_format($tip['potential_savings'])
                ];
            }
        }
        
        // Add health score improvements
        if ($insights['health_score']['score'] < 80) {
            foreach ($insights['health_score']['factors'] as $factor => $data) {
                if ($data['score'] < 80) {
                    $recommendations[] = [
                        'type' => 'health',
                        'title' => 'Improve ' . ucfirst($factor),
                        'description' => $data['message'],
                        'impact' => 'Health Score Improvement'
                    ];
                }
            }
        }
        
        return $recommendations;
    }
} 