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

    // Common expense categories
    const CATEGORIES = [
        'housing' => 'Housing & Utilities',
        'transportation' => 'Transportation',
        'food' => 'Food & Groceries',
        'healthcare' => 'Healthcare',
        'education' => 'Education',
        'entertainment' => 'Entertainment',
        'savings' => 'Savings & Investments',
        'debt' => 'Debt Payments',
        'insurance' => 'Insurance',
        'personal' => 'Personal Care',
        'other' => 'Other Expenses'
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
} 