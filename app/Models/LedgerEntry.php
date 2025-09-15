<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_account_id',
        'transaction_type',
        'amount',
        'principal_amount',
        'interest_amount',
        'fee_amount',
        'penalty_amount',
        'balance_before',
        'balance_after',
        'reference_number',
        'description',
        'transaction_date',
        'processed_by',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    // Transaction types
    const TYPE_DISBURSEMENT = 'disbursement';
    const TYPE_PRINCIPAL_PAYMENT = 'principal_payment';
    const TYPE_INTEREST_PAYMENT = 'interest_payment';
    const TYPE_FEE_PAYMENT = 'fee_payment';
    const TYPE_PENALTY_PAYMENT = 'penalty_payment';
    const TYPE_ADJUSTMENT = 'adjustment';

    // Relationships
    public function loanAccount(): BelongsTo
    {
        return $this->belongsTo(LoanAccount::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Helper methods
    public function isDebit(): bool
    {
        return in_array($this->transaction_type, [
            self::TYPE_DISBURSEMENT,
            self::TYPE_ADJUSTMENT
        ]);
    }

    public function isCredit(): bool
    {
        return in_array($this->transaction_type, [
            self::TYPE_PRINCIPAL_PAYMENT,
            self::TYPE_INTEREST_PAYMENT,
            self::TYPE_FEE_PAYMENT,
            self::TYPE_PENALTY_PAYMENT
        ]);
    }

    public function getFormattedAmount(): string
    {
        return 'KSh ' . number_format($this->amount, 2);
    }

    public function getTransactionTypeLabel(): string
    {
        return match($this->transaction_type) {
            self::TYPE_DISBURSEMENT => 'Disbursement',
            self::TYPE_PRINCIPAL_PAYMENT => 'Principal Payment',
            self::TYPE_INTEREST_PAYMENT => 'Interest Payment',
            self::TYPE_FEE_PAYMENT => 'Fee Payment',
            self::TYPE_PENALTY_PAYMENT => 'Penalty Payment',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            default => ucfirst(str_replace('_', ' ', $this->transaction_type))
        };
    }
}
