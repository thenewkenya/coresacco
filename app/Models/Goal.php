<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'member_id',
        'title',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'type',
        'status',
        'auto_save_amount',
        'auto_save_frequency',
        'metadata'
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'metadata' => 'array',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'auto_save_amount' => 'decimal:2'
    ];

    // Goal types
    const TYPE_EMERGENCY_FUND = 'emergency_fund';
    const TYPE_HOME_PURCHASE = 'home_purchase';
    const TYPE_EDUCATION = 'education';
    const TYPE_RETIREMENT = 'retirement';
    const TYPE_CUSTOM = 'custom';

    // Goal statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';

    // Auto-save frequencies
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';

    /**
     * Get the member that owns the goal
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Get days remaining until target date
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->target_date) {
            return 0;
        }
        return max(0, now()->diffInDays($this->target_date, false));
    }

    /**
     * Get all available goal types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_EMERGENCY_FUND => 'Emergency Fund',
            self::TYPE_HOME_PURCHASE => 'Home Purchase',
            self::TYPE_EDUCATION => 'Education',
            self::TYPE_RETIREMENT => 'Retirement',
            self::TYPE_CUSTOM => 'Custom Goal'
        ];
    }
} 