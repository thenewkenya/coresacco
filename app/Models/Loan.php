<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'loan_type_id',
        'amount',
        'interest_rate',
        'term_period',
        'status',
        'disbursement_date',
        'due_date',
        'collateral_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'term_period' => 'integer',
        'disbursement_date' => 'datetime',
        'due_date' => 'datetime',
        'collateral_details' => 'array',
    ];

    // Loan statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DISBURSED = 'disbursed';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DEFAULTED = 'defaulted';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function calculateInterest(): float
    {
        return $this->amount * ($this->interest_rate / 100);
    }

    public function calculateTotalRepayment(): float
    {
        return $this->amount + $this->calculateInterest();
    }

    public function calculateMonthlyPayment(): float
    {
        if ($this->term_period <= 0) {
            return 0;
        }
        return $this->calculateTotalRepayment() / $this->term_period;
    }

    public function isDefaulted(): bool
    {
        return $this->due_date < now() && $this->status === self::STATUS_ACTIVE;
    }
} 