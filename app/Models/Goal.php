<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
     * Get months remaining until target date
     */
    public function getMonthsRemainingAttribute(): int
    {
        if (!$this->target_date) {
            return 0;
        }
        return max(1, now()->diffInMonths($this->target_date, false));
    }

    /**
     * Calculate required monthly savings to reach goal
     */
    public function getRequiredMonthlySavingsAttribute(): float
    {
        $months = $this->months_remaining;
        return $months > 0 ? $this->remaining_amount / $months : 0;
    }

    /**
     * Get auto-save monthly equivalent
     */
    public function getMonthlyAutoSaveAttribute(): float
    {
        if (!$this->auto_save_amount) {
            return 0;
        }
        
        return $this->auto_save_frequency === self::FREQUENCY_WEEKLY 
            ? $this->auto_save_amount * 4.33 
            : $this->auto_save_amount;
    }

    /**
     * Check if goal is on track based on current progress vs time passed
     */
    public function getIsOnTrackAttribute(): bool
    {
        if (!$this->target_date) {
            return true;
        }
        
        $totalDays = Carbon::parse($this->created_at)->diffInDays($this->target_date);
        $daysPassed = Carbon::parse($this->created_at)->diffInDays(now());
        
        if ($totalDays <= 0) {
            return $this->progress_percentage >= 100;
        }
        
        $expectedProgress = ($daysPassed / $totalDays) * 100;
        return $this->progress_percentage >= ($expectedProgress * 0.8); // 20% tolerance
    }

    /**
     * Get goal urgency level
     */
    public function getUrgencyLevelAttribute(): string
    {
        $daysRemaining = $this->days_remaining;
        
        if ($daysRemaining <= 7) {
            return 'critical';
        } elseif ($daysRemaining <= 30) {
            return 'high';
        } elseif ($daysRemaining <= 90) {
            return 'medium';
        }
        
        return 'low';
    }

    /**
     * Get intelligent recommendation for this goal
     */
    public function getSmartRecommendationAttribute(): array
    {
        $recommendation = [
            'type' => 'info',
            'message' => '',
            'action' => null
        ];

        // Check if goal is overdue
        if ($this->target_date && $this->target_date->isPast() && $this->status === self::STATUS_ACTIVE) {
            $recommendation['type'] = 'critical';
            $recommendation['message'] = 'This goal is overdue. Consider extending the deadline or making a large contribution.';
            $recommendation['action'] = 'extend_deadline';
            return $recommendation;
        }

        // Check if auto-save is insufficient
        if ($this->auto_save_amount && $this->monthly_auto_save < $this->required_monthly_savings) {
            $shortfall = $this->required_monthly_savings - $this->monthly_auto_save;
            $recommendation['type'] = 'warning';
            $recommendation['message'] = "Increase auto-save by KES " . number_format($shortfall) . " monthly to stay on track.";
            $recommendation['action'] = 'increase_auto_save';
            return $recommendation;
        }

        // Check if no auto-save is set up
        if (!$this->auto_save_amount && $this->required_monthly_savings > 0) {
            $recommendation['type'] = 'suggestion';
            $recommendation['message'] = "Set up auto-save of KES " . number_format($this->required_monthly_savings) . " monthly to reach your goal.";
            $recommendation['action'] = 'setup_auto_save';
            return $recommendation;
        }

        // Check if goal is ahead of schedule
        if ($this->is_on_track && $this->progress_percentage > 50) {
            $recommendation['type'] = 'success';
            $recommendation['message'] = "Great progress! You're ahead of schedule.";
            return $recommendation;
        }

        // Check if goal is behind schedule
        if (!$this->is_on_track) {
            $recommendation['type'] = 'warning';
            $recommendation['message'] = "You're behind schedule. Consider increasing your contributions.";
            $recommendation['action'] = 'increase_contributions';
            return $recommendation;
        }

        return $recommendation;
    }

    /**
     * Calculate projected completion date based on current savings rate
     */
    public function getProjectedCompletionDateAttribute(): ?Carbon
    {
        if ($this->remaining_amount <= 0) {
            return now(); // Already completed
        }

        if (!$this->auto_save_amount) {
            return null; // Can't project without auto-save
        }

        $monthsToComplete = $this->remaining_amount / $this->monthly_auto_save;
        return now()->addMonths(ceil($monthsToComplete));
    }

    /**
     * Get celebration milestone (25%, 50%, 75%, 100%)
     */
    public function getNextMilestoneAttribute(): ?int
    {
        $progress = $this->progress_percentage;
        
        if ($progress < 25) return 25;
        if ($progress < 50) return 50;
        if ($progress < 75) return 75;
        if ($progress < 100) return 100;
        
        return null; // Goal completed
    }

    /**
     * Check if user can afford increased auto-save
     */
    public function canAffordAutoSaveIncrease(float $increaseAmount): bool
    {
        $user = $this->member;
        
        // Get average monthly disposable income
        $avgIncome = $user->transactions()
            ->where('type', 'deposit')
            ->where('created_at', '>=', now()->subMonths(6))
            ->avg('amount') ?? 0;
            
        $avgExpenses = $user->transactions()
            ->where('type', 'withdrawal')
            ->where('created_at', '>=', now()->subMonths(6))
            ->avg('amount') ?? 0;
            
        $disposableIncome = $avgIncome - $avgExpenses;
        $currentAutoSaveTotal = $user->goals()->whereNotNull('auto_save_amount')->sum('auto_save_amount');
        
        // Check if increase would exceed 30% of disposable income
        return ($currentAutoSaveTotal + $increaseAmount) <= ($disposableIncome * 0.3);
    }

    /**
     * Add contribution to goal and check for completion
     */
    public function addContribution(float $amount, string $source = 'manual'): bool
    {
        $this->current_amount += $amount;
        
        // Check if goal is now completed
        if ($this->current_amount >= $this->target_amount) {
            $this->status = self::STATUS_COMPLETED;
            $this->current_amount = $this->target_amount; // Don't exceed target
            
            // Store completion metadata
            $metadata = $this->metadata ?? [];
            $metadata['completed_at'] = now()->toISOString();
            $metadata['completion_source'] = $source;
            $this->metadata = $metadata;
        }
        
        return $this->save();
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

    /**
     * Get goals that need auto-save processing
     */
    public static function needingAutoSave(string $frequency): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('status', self::STATUS_ACTIVE)
            ->where('auto_save_frequency', $frequency)
            ->whereNotNull('auto_save_amount')
            ->where('auto_save_amount', '>', 0)
            ->get();
    }

    /**
     * Process auto-save for this goal
     */
    public function processAutoSave(): bool
    {
        if (!$this->auto_save_amount || $this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        // Check if user has sufficient balance
        $user = $this->member;
        $savingsAccount = $user->accounts()->where('account_type', 'savings')->first();
        
        if (!$savingsAccount || $savingsAccount->balance < $this->auto_save_amount) {
            return false; // Insufficient funds
        }

        // Create internal transfer transaction
        try {
            // This would integrate with your transaction system
            $this->addContribution($this->auto_save_amount, 'auto_save');
            
            // Store auto-save history in metadata
            $metadata = $this->metadata ?? [];
            $metadata['auto_save_history'][] = [
                'amount' => $this->auto_save_amount,
                'date' => now()->toISOString(),
                'success' => true
            ];
            $this->metadata = $metadata;
            $this->save();
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Auto-save failed for goal {$this->id}: " . $e->getMessage());
            return false;
        }
    }
} 