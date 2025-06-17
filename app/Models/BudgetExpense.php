<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetExpense extends Model
{
    protected $fillable = [
        'budget_id',
        'category',
        'description',
        'amount',
        'date',
        'receipt_url',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the budget that owns the expense
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the category name
     */
    public function getCategoryNameAttribute(): string
    {
        return Budget::CATEGORIES[$this->category] ?? 'Other';
    }
} 