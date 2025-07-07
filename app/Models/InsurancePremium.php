<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InsurancePremium extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'member_id',
        'premium_amount',
        'commission_amount',
        'due_date',
        'payment_date',
        'payment_method',
        'payment_reference',
        'status',
        'late_fee',
        'grace_period_end_date',
        'processed_by',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'premium_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'grace_period_end_date' => 'date',
        'metadata' => 'json'
    ];

    /**
     * Get the policy associated with this premium
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }

    /**
     * Get the member who pays this premium
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Get the user who processed this premium
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if premium is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if premium is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    /**
     * Check if premium is in grace period
     */
    public function isInGracePeriod(): bool
    {
        return $this->grace_period_end_date && 
               Carbon::now()->lte($this->grace_period_end_date) && 
               $this->status === 'grace_period';
    }

    /**
     * Calculate days overdue
     */
    public function getDaysOverdue(): int
    {
        if ($this->isPaid() || !$this->due_date) {
            return 0;
        }
        
        return Carbon::parse($this->due_date)->diffInDays(Carbon::now());
    }

    /**
     * Calculate late fee
     */
    public function calculateLateFee(): float
    {
        if ($this->isPaid() || !$this->isOverdue()) {
            return 0;
        }
        
        $daysOverdue = $this->getDaysOverdue();
        $product = $this->policy->product;
        
        // Simple late fee calculation - could be made more sophisticated
        $lateFeeRate = 0.05; // 5% of premium amount
        return $this->premium_amount * $lateFeeRate;
    }

    /**
     * Get total amount due (premium + late fee)
     */
    public function getTotalAmountDue(): float
    {
        return $this->premium_amount + ($this->late_fee ?? 0);
    }

    /**
     * Get payment status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'grace_period' => 'Grace Period',
            'waived' => 'Waived',
            'cancelled' => 'Cancelled'
        ];
    }

    /**
     * Get payment method options
     */
    public static function getPaymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'debit_card' => 'Debit Card',
            'credit_card' => 'Credit Card',
            'direct_debit' => 'Direct Debit',
            'check' => 'Check',
            'salary_deduction' => 'Salary Deduction',
            'other' => 'Other'
        ];
    }
} 