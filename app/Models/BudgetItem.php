<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'category',
        'amount',
        'is_recurring'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean'
    ];

    /**
     * Get the budget that owns the item
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function expenses()
    {
        return $this->hasMany(BudgetExpense::class, 'category', 'category')
            ->where('budget_id', $this->budget_id);
    }

    public function getExpensesSumAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->expenses_sum;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->amount <= 0) {
            return 0;
        }
        return min(100, round(($this->expenses_sum / $this->amount) * 100));
    }

    /**
     * Get the category name
     */
    public function getCategoryNameAttribute(): string
    {
        return Budget::CATEGORIES[$this->category] ?? 'Other';
    }
} 