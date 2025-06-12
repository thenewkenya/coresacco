<?php

/* Transaction records all financial transactions,
supports multiple types of transactions,
maintains audit trail with before/after balances
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'member_id',
        'loan_id',
        'type',
        'amount',
        'description',
        'reference_number',
        'status',
        'balance_before',
        'balance_after',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Transaction types
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_LOAN_DISBURSEMENT = 'loan_disbursement';
    const TYPE_LOAN_REPAYMENT = 'loan_repayment';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_FEE = 'fee';
    const TYPE_INTEREST = 'interest';

    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REVERSED = 'reversed';

    // Relationships
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // Helper methods
    public function isDebit(): bool
    {
        return in_array($this->type, [
            self::TYPE_WITHDRAWAL,
            self::TYPE_LOAN_REPAYMENT,
            self::TYPE_TRANSFER,
            self::TYPE_FEE
        ]);
    }

    public function isCredit(): bool
    {
        return in_array($this->type, [
            self::TYPE_DEPOSIT,
            self::TYPE_LOAN_DISBURSEMENT,
            self::TYPE_INTEREST
        ]);
    }

    public function generateReferenceNumber(): string
    {
        return 'TXN' . date('Ymd') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
} 