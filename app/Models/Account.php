<?php

/* Account handles different types of accounts,
manages account balances and transactions 
has helper methods for deposits and withdrawals
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_number',
        'member_id',
        'account_type',
        'balance',
        'status',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'status' => 'string',
        'account_type' => 'string',
    ];

    // Account types
    const TYPE_SAVINGS = 'savings';
    const TYPE_SHARES = 'shares';
    const TYPE_DEPOSITS = 'deposits';

    // Account statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_FROZEN = 'frozen';
    const STATUS_CLOSED = 'closed';

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }



    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function deposit(float $amount): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $this->balance += $amount;
        return $this->save();
    }

    public function withdraw(float $amount): bool
    {
        if ($amount <= 0 || $amount > $this->balance) {
            return false;
        }

        $this->balance -= $amount;
        return $this->save();
    }

    /**
     * Generate unique account number
     */
    public static function generateAccountNumber(): string
    {
        do {
            $accountNumber = 'ACC' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    /**
     * Get available account types
     */
    public static function getAccountTypes(): array
    {
        return [
            self::TYPE_SAVINGS,
            self::TYPE_SHARES,
            self::TYPE_DEPOSITS,
        ];
    }
} 