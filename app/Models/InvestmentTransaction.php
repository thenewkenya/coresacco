<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'portfolio_id',
        'product_id',
        'transaction_type',
        'amount',
        'units',
        'unit_price',
        'description',
        'reference_number',
        'external_reference',
        'status',
        'processed_by',
        'approved_by',
        'processed_at',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'units' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'processed_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Transaction types
    const TYPE_PURCHASE = 'purchase';
    const TYPE_DIVIDEND = 'dividend';
    const TYPE_INTEREST = 'interest';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_PARTIAL_WITHDRAWAL = 'partial_withdrawal';
    const TYPE_MATURITY = 'maturity';
    const TYPE_BONUS = 'bonus';
    const TYPE_FEE = 'fee';
    const TYPE_PENALTY = 'penalty';
    const TYPE_TRANSFER = 'transfer';

    // Status
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(InvestmentPortfolio::class, 'portfolio_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InvestmentProduct::class, 'product_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isCredit(): bool
    {
        return in_array($this->transaction_type, [
            self::TYPE_DIVIDEND,
            self::TYPE_INTEREST,
            self::TYPE_BONUS,
            self::TYPE_MATURITY
        ]);
    }

    public function isDebit(): bool
    {
        return in_array($this->transaction_type, [
            self::TYPE_PURCHASE,
            self::TYPE_WITHDRAWAL,
            self::TYPE_PARTIAL_WITHDRAWAL,
            self::TYPE_FEE,
            self::TYPE_PENALTY
        ]);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = match($this->transaction_type) {
            self::TYPE_PURCHASE => 'INV',
            self::TYPE_DIVIDEND => 'DIV',
            self::TYPE_INTEREST => 'INT',
            self::TYPE_WITHDRAWAL => 'WDL',
            self::TYPE_MATURITY => 'MAT',
            default => 'TXN'
        };

        return $prefix . date('Ymd') . str_pad($this->id ?? rand(1000, 9999), 6, '0', STR_PAD_LEFT);
    }

    public static function getTransactionTypes(): array
    {
        return [
            self::TYPE_PURCHASE => 'Investment Purchase',
            self::TYPE_DIVIDEND => 'Dividend Payment',
            self::TYPE_INTEREST => 'Interest Payment',
            self::TYPE_WITHDRAWAL => 'Full Withdrawal',
            self::TYPE_PARTIAL_WITHDRAWAL => 'Partial Withdrawal',
            self::TYPE_MATURITY => 'Maturity Payout',
            self::TYPE_BONUS => 'Bonus Payment',
            self::TYPE_FEE => 'Management Fee',
            self::TYPE_PENALTY => 'Early Withdrawal Penalty',
            self::TYPE_TRANSFER => 'Investment Transfer'
        ];
    }

    // Boot method to auto-generate reference numbers
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->reference_number)) {
                $transaction->reference_number = $transaction->generateReferenceNumber();
            }
        });
    }
} 