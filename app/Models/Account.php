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
    const TYPE_EMERGENCY_FUND = 'emergency_fund';
    const TYPE_HOLIDAY_SAVINGS = 'holiday_savings';
    const TYPE_RETIREMENT = 'retirement';
    const TYPE_EDUCATION = 'education';
    const TYPE_DEVELOPMENT = 'development';
    const TYPE_WELFARE = 'welfare';
    const TYPE_INVESTMENT = 'investment';
    const TYPE_LOAN_GUARANTEE = 'loan_guarantee';
    const TYPE_INSURANCE = 'insurance';

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
            self::TYPE_EMERGENCY_FUND,
            self::TYPE_HOLIDAY_SAVINGS,
            self::TYPE_RETIREMENT,
            self::TYPE_EDUCATION,
            self::TYPE_DEVELOPMENT,
            self::TYPE_WELFARE,
            self::TYPE_INVESTMENT,
            self::TYPE_LOAN_GUARANTEE,
            self::TYPE_INSURANCE,
        ];
    }

    /**
     * Get account type display names
     */
    public static function getAccountTypeNames(): array
    {
        return [
            self::TYPE_SAVINGS => 'Regular Savings',
            self::TYPE_SHARES => 'Share Capital',
            self::TYPE_DEPOSITS => 'Term Deposits',
            self::TYPE_EMERGENCY_FUND => 'Emergency Fund',
            self::TYPE_HOLIDAY_SAVINGS => 'Holiday Savings',
            self::TYPE_RETIREMENT => 'Retirement Savings',
            self::TYPE_EDUCATION => 'Education Fund',
            self::TYPE_DEVELOPMENT => 'Development Fund',
            self::TYPE_WELFARE => 'Welfare Fund',
            self::TYPE_INVESTMENT => 'Investment Account',
            self::TYPE_LOAN_GUARANTEE => 'Loan Guarantee Fund',
            self::TYPE_INSURANCE => 'Insurance Fund',
        ];
    }

    /**
     * Get the display name for this account type
     */
    public function getDisplayName(): string
    {
        $names = self::getAccountTypeNames();
        return $names[$this->account_type] ?? ucfirst(str_replace('_', ' ', $this->account_type));
    }
} 