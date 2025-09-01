<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_income',
        'total_expenses',
        'savings_target',
        'notes',
        'status'
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'savings_target' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer'
    ];

    // Budget statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    // Common expense categories with intelligent insights
    const CATEGORIES = [
        'housing' => 'Housing & Utilities',
        'transportation' => 'Transportation',
        'food' => 'Food & Groceries',
        'healthcare' => 'Healthcare',
        'education' => 'Education',
        'entertainment' => 'Entertainment',
        'savings' => 'Savings & Investments',
        'debt' => 'Debt Payments',
        'personal' => 'Personal Care',
        'other' => 'Other Expenses'
    ];

    // Recommended budget percentages (50/30/20 rule and variations)
    const RECOMMENDED_PERCENTAGES = [
        'housing' => ['min' => 25, 'max' => 35, 'optimal' => 30],
        'transportation' => ['min' => 10, 'max' => 20, 'optimal' => 15],
        'food' => ['min' => 10, 'max' => 15, 'optimal' => 12],
        'healthcare' => ['min' => 5, 'max' => 10, 'optimal' => 8],
        'education' => ['min' => 5, 'max' => 15, 'optimal' => 10],
        'entertainment' => ['min' => 5, 'max' => 10, 'optimal' => 8],
        'savings' => ['min' => 20, 'max' => 30, 'optimal' => 25],
        'debt' => ['min' => 0, 'max' => 20, 'optimal' => 10],
        'personal' => ['min' => 3, 'max' => 8, 'optimal' => 5],
        'other' => ['min' => 0, 'max' => 10, 'optimal' => 5]
    ];

    /**
     * Get the user that owns the budget
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the budget items for this budget
     */
    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    /**
     * Get the actual expenses for this budget period
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(BudgetExpense::class);
    }

    /**
     * Calculate remaining budget
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->total_income - $this->total_expenses;
    }

    /**
     * Calculate savings progress
     */
    public function getSavingsProgressAttribute(): float
    {
        if ($this->savings_target <= 0) {
            return 0;
        }
        $actualSavings = $this->total_income - $this->expenses()->sum('amount');
        return min(100, round(($actualSavings / $this->savings_target) * 100));
    }

    /**
     * Get expense summary by category
     */
    public function getExpenseSummaryAttribute(): array
    {
        $items = $this->items()->with('expenses')->get();
        
        return $items->map(function($item) {
            $actualExpenses = $item->expenses->sum('amount');
            return [
                'name' => $item->category,
                'category' => $item->category,
                'planned' => $item->amount,
                'actual' => $actualExpenses,
                'remaining' => $item->amount - $actualExpenses,
                'progress' => $item->amount > 0 ? min(100, ($actualExpenses / $item->amount) * 100) : 0
            ];
        })->all();
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }

    /**
     * Smart Budget Analysis - AI-powered insights
     */
    public function getSmartInsights(): array
    {
        $insights = [];
        $user = $this->user;

        // 1. Spending Pattern Analysis
        $spendingPattern = $this->analyzeSpendingPattern();
        $insights['spending_pattern'] = $spendingPattern;

        // 2. Budget Health Score
        $healthScore = $this->calculateBudgetHealthScore();
        $insights['health_score'] = $healthScore;

        // 3. Savings Optimization
        $savingsOptimization = $this->getSavingsOptimizationTips();
        $insights['savings_tips'] = $savingsOptimization;

        // 4. Category Recommendations
        $categoryRecommendations = $this->getCategoryRecommendations();
        $insights['category_recommendations'] = $categoryRecommendations;

        // 5. Future Predictions
        $predictions = $this->generateFinancialPredictions();
        $insights['predictions'] = $predictions;

        return $insights;
    }

    /**
     * Analyze spending patterns using AI
     */
    public function analyzeSpendingPattern(): array
    {
        $expenses = $this->expenses()->get()->groupBy('category');
        $totalExpenses = $this->expenses()->sum('amount');
        
        $patterns = [];
        
        foreach ($expenses as $category => $categoryExpenses) {
            $categoryTotal = $categoryExpenses->sum('amount');
            $percentage = $totalExpenses > 0 ? ($categoryTotal / $totalExpenses) * 100 : 0;
            
            $recommended = self::RECOMMENDED_PERCENTAGES[$category] ?? ['min' => 0, 'max' => 100, 'optimal' => 50];
            
            $status = 'optimal';
            $message = '';
            
            if ($percentage > $recommended['max']) {
                $status = 'overspending';
                $message = "You're spending " . number_format($percentage - $recommended['optimal'], 1) . "% more than recommended on " . self::CATEGORIES[$category];
            } elseif ($percentage < $recommended['min']) {
                $status = 'underspending';
                $message = "You might want to allocate more to " . self::CATEGORIES[$category];
            } else {
                $message = "Your spending on " . self::CATEGORIES[$category] . " is within healthy limits";
            }
            
            $patterns[$category] = [
                'name' => self::CATEGORIES[$category],
                'percentage' => $percentage,
                'recommended' => $recommended,
                'status' => $status,
                'message' => $message,
                'trend' => $this->getCategoryTrend($category)
            ];
        }
        
        return $patterns;
    }

    /**
     * Calculate overall budget health score
     */
    public function calculateBudgetHealthScore(): array
    {
        $score = 100;
        $factors = [];
        
        // Factor 1: Savings Rate (40% weight)
        $savingsRate = $this->total_income > 0 ? ($this->savings_target / $this->total_income) * 100 : 0;
        if ($savingsRate >= 20) {
            $savingsScore = 100;
            $factors['savings'] = ['score' => 100, 'message' => 'Excellent savings rate!'];
        } elseif ($savingsRate >= 10) {
            $savingsScore = 75;
            $factors['savings'] = ['score' => 75, 'message' => 'Good savings rate, aim for 20%'];
        } else {
            $savingsScore = 50;
            $factors['savings'] = ['score' => 50, 'message' => 'Try to save at least 10-20% of income'];
        }
        
        // Factor 2: Expense Distribution (30% weight)
        $distributionScore = $this->calculateDistributionScore();
        $factors['distribution'] = $distributionScore;
        
        // Factor 3: Budget Adherence (30% weight)
        $adherenceScore = $this->calculateBudgetAdherence();
        $factors['adherence'] = $adherenceScore;
        
        $finalScore = ($savingsScore * 0.4) + ($distributionScore['score'] * 0.3) + ($adherenceScore['score'] * 0.3);
        
        return [
            'score' => round($finalScore),
            'grade' => $this->getScoreGrade($finalScore),
            'factors' => $factors
        ];
    }

    /**
     * Get personalized savings optimization tips
     */
    public function getSavingsOptimizationTips(): array
    {
        $tips = [];
        $expenses = $this->expenses()->get()->groupBy('category');
        
        // Analyze each category for optimization opportunities
        foreach ($expenses as $category => $categoryExpenses) {
            $categoryTotal = $categoryExpenses->sum('amount');
            $incomePercentage = $this->total_income > 0 ? ($categoryTotal / $this->total_income) * 100 : 0;
            
            $recommended = self::RECOMMENDED_PERCENTAGES[$category] ?? null;
            
            if ($recommended && $incomePercentage > $recommended['optimal']) {
                $potentialSavings = $categoryTotal - (($recommended['optimal'] / 100) * $this->total_income);
                
                if ($potentialSavings > 0) {
                    $tips[] = [
                        'category' => self::CATEGORIES[$category],
                        'current_spending' => $categoryTotal,
                        'recommended_spending' => ($recommended['optimal'] / 100) * $this->total_income,
                        'potential_savings' => $potentialSavings,
                        'tip' => $this->getCategorySpecificTip($category, $potentialSavings),
                        'priority' => $this->getTipPriority($category, $incomePercentage, $recommended['optimal'])
                    ];
                }
            }
        }
        
        // Sort by priority
        usort($tips, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $tips;
    }

    /**
     * Get AI-powered category recommendations
     */
    public function getCategoryRecommendations(): array
    {
        $recommendations = [];
        $userHistory = $this->user->budgets()->where('id', '!=', $this->id)->get();
        
        foreach (self::CATEGORIES as $key => $name) {
            $currentAllocation = $this->items()->where('category', $key)->sum('amount');
            $currentPercentage = $this->total_income > 0 ? ($currentAllocation / $this->total_income) * 100 : 0;
            
            $recommended = self::RECOMMENDED_PERCENTAGES[$key];
            $recommendedAmount = ($recommended['optimal'] / 100) * $this->total_income;
            
            $recommendation = [
                'category' => $name,
                'current_amount' => $currentAllocation,
                'current_percentage' => $currentPercentage,
                'recommended_amount' => $recommendedAmount,
                'recommended_percentage' => $recommended['optimal'],
                'difference' => $recommendedAmount - $currentAllocation,
                'status' => $this->getRecommendationStatus($currentPercentage, $recommended),
                'advice' => $this->getAdviceForCategory($key, $currentPercentage, $recommended)
            ];
            
            $recommendations[$key] = $recommendation;
        }
        
        return $recommendations;
    }

    /**
     * Generate financial predictions
     */
    public function generateFinancialPredictions(): array
    {
        $predictions = [];
        
        // Monthly Surplus/Deficit Prediction
        $currentSurplus = $this->total_income - $this->expenses()->sum('amount');
        $predictions['monthly_surplus'] = [
            'current' => $currentSurplus,
            'projected_3_months' => $currentSurplus * 3,
            'projected_6_months' => $currentSurplus * 6,
            'message' => $currentSurplus > 0 
                ? "You're on track to save KES " . number_format($currentSurplus * 6) . " in 6 months"
                : "You may have a deficit of KES " . number_format(abs($currentSurplus * 6)) . " in 6 months if spending continues"
        ];
        
        // Goal Achievement Prediction
        $userGoals = $this->user->goals()->where('status', 'active')->get();
        foreach ($userGoals as $goal) {
            if ($currentSurplus > 0) {
                $monthsToGoal = ceil(($goal->target_amount - $goal->current_amount) / $currentSurplus);
                $predictions['goals'][] = [
                    'goal_title' => $goal->title,
                    'months_to_achieve' => $monthsToGoal,
                    'achievable' => $monthsToGoal <= $goal->remaining_months,
                    'message' => $monthsToGoal <= $goal->remaining_months 
                        ? "You're on track to achieve this goal!"
                        : "You may need to increase savings or extend the timeline"
                ];
            }
        }
        
        return $predictions;
    }

    /**
     * Get category spending trend
     */
    private function getCategoryTrend(string $category): array
    {
        $userBudgets = $this->user->budgets()
            ->where('id', '!=', $this->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->take(3)
            ->get();
        
        $trend = [];
        foreach ($userBudgets as $budget) {
            $categoryExpenses = $budget->expenses()->where('category', $category)->sum('amount');
            $trend[] = $categoryExpenses;
        }
        
        if (count($trend) >= 2) {
            $direction = $trend[0] > $trend[1] ? 'increasing' : 'decreasing';
            $change = count($trend) >= 2 ? abs($trend[0] - $trend[1]) : 0;
        } else {
            $direction = 'stable';
            $change = 0;
        }
        
        return [
            'direction' => $direction,
            'change_amount' => $change,
            'data' => array_reverse($trend)
        ];
    }

    /**
     * Calculate distribution score
     */
    private function calculateDistributionScore(): array
    {
        $score = 100;
        $issues = [];
        
        foreach ($this->items as $item) {
            $percentage = $this->total_income > 0 ? ($item->amount / $this->total_income) * 100 : 0;
            $recommended = self::RECOMMENDED_PERCENTAGES[$item->category] ?? null;
            
            if ($recommended) {
                if ($percentage > $recommended['max']) {
                    $score -= 10;
                    $issues[] = "Overspending on " . self::CATEGORIES[$item->category];
                } elseif ($percentage < $recommended['min']) {
                    $score -= 5;
                    $issues[] = "Underspending on " . self::CATEGORIES[$item->category];
                }
            }
        }
        
        return [
            'score' => max(0, $score),
            'message' => empty($issues) ? 'Well-balanced budget distribution' : implode(', ', $issues)
        ];
    }

    /**
     * Calculate budget adherence score
     */
    private function calculateBudgetAdherence(): array
    {
        $totalPlanned = $this->items()->sum('amount');
        $totalActual = $this->expenses()->sum('amount');
        
        if ($totalPlanned == 0) {
            return ['score' => 0, 'message' => 'No budget items to compare'];
        }
        
        $adherencePercentage = abs(($totalActual - $totalPlanned) / $totalPlanned) * 100;
        
        if ($adherencePercentage <= 5) {
            $score = 100;
            $message = 'Excellent budget adherence!';
        } elseif ($adherencePercentage <= 15) {
            $score = 80;
            $message = 'Good budget adherence';
        } elseif ($adherencePercentage <= 25) {
            $score = 60;
            $message = 'Fair budget adherence, room for improvement';
        } else {
            $score = 40;
            $message = 'Poor budget adherence, consider revising your budget';
        }
        
        return ['score' => $score, 'message' => $message];
    }

    /**
     * Get score grade
     */
    private function getScoreGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    /**
     * Get category-specific saving tips
     */
    private function getCategorySpecificTip(string $category, float $potentialSavings): string
    {
        $tips = [
            'housing' => 'Consider refinancing, finding a roommate, or exploring cheaper housing options',
            'transportation' => 'Use public transport, carpool, or consider a more fuel-efficient vehicle',
            'food' => 'Cook more at home, buy in bulk, use coupons, and reduce dining out',
            'entertainment' => 'Look for free activities, use streaming services instead of cable, find discounts',
            'personal' => 'Buy generic brands, look for sales, consider DIY alternatives',
            'other' => 'Review all miscellaneous expenses and eliminate unnecessary purchases'
        ];
        
        $baseTip = $tips[$category] ?? 'Review this category for potential savings';
        return $baseTip . ". Potential monthly savings: KES " . number_format($potentialSavings);
    }

    /**
     * Get tip priority
     */
    private function getTipPriority(string $category, float $currentPercentage, float $recommendedPercentage): int
    {
        $overspend = $currentPercentage - $recommendedPercentage;
        
        // Higher priority for larger overspends
        if ($overspend > 15) return 5;
        if ($overspend > 10) return 4;
        if ($overspend > 5) return 3;
        if ($overspend > 0) return 2;
        return 1;
    }

    /**
     * Get recommendation status
     */
    private function getRecommendationStatus(float $currentPercentage, array $recommended): string
    {
        if ($currentPercentage > $recommended['max']) return 'over';
        if ($currentPercentage < $recommended['min']) return 'under';
        if ($currentPercentage >= $recommended['optimal'] - 2 && $currentPercentage <= $recommended['optimal'] + 2) return 'optimal';
        return 'good';
    }

    /**
     * Get advice for category
     */
    private function getAdviceForCategory(string $category, float $currentPercentage, array $recommended): string
    {
        $status = $this->getRecommendationStatus($currentPercentage, $recommended);
        
        $advice = [
            'over' => "Consider reducing spending in this category. You're spending " . number_format($currentPercentage - $recommended['optimal'], 1) . "% more than optimal.",
            'under' => "You might want to allocate more to this category for better financial health.",
            'optimal' => "Perfect allocation! You're spending the optimal amount in this category.",
            'good' => "Good allocation, close to the recommended range."
        ];
        
        return $advice[$status] ?? "Review your allocation for this category.";
    }

    /**
     * Auto-generate smart budget based on income and user history
     */
    public static function generateSmartBudget(User $user, float $income): array
    {
        $budget = [];
        
        // Get user's historical spending patterns
        $historicalBudgets = $user->budgets()->latest()->take(3)->get();
        
        foreach (self::CATEGORIES as $key => $name) {
            // Calculate historical average percentage
            $historicalPercentage = 0;
            if ($historicalBudgets->count() > 0) {
                $historicalPercentage = $historicalBudgets->avg(function($budget) use ($key) {
                    $amount = $budget->items()->where('category', $key)->sum('amount');
                    return $budget->total_income > 0 ? ($amount / $budget->total_income) * 100 : 0;
                });
            }
            
            // Blend historical data with recommendations
            $recommended = self::RECOMMENDED_PERCENTAGES[$key]['optimal'];
            $suggestedPercentage = $historicalPercentage > 0 
                ? ($historicalPercentage * 0.7) + ($recommended * 0.3)  // 70% historical, 30% recommended
                : $recommended;
                
            // Ensure within reasonable bounds
            $min = self::RECOMMENDED_PERCENTAGES[$key]['min'];
            $max = self::RECOMMENDED_PERCENTAGES[$key]['max'];
            $suggestedPercentage = max($min, min($max, $suggestedPercentage));
            
            $budget[$key] = [
                'name' => $name,
                'percentage' => round($suggestedPercentage, 1),
                'amount' => round(($suggestedPercentage / 100) * $income, 2),
                'is_ai_suggested' => true
            ];
        }
        
        return $budget;
    }
} 