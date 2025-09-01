<?php

/* Member (extends User) manages member info and r/ships,
tracks membership status and links accts and loans */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends User
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $fillable = [
        'member_number',
        'full_name',
        'id_number',
        'phone_number',
        'address',
        'membership_status',
        'joining_date',
        'branch_id',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'membership_status' => 'string',
    ];

    // Relationships
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }



    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guarantor(): HasOne
    {
        return $this->hasOne(Guarantor::class);
    }

    // Helper methods for borrowing criteria
    public function getTotalSavingsBalance(): float
    {
        return $this->accounts()->where('account_type', Account::TYPE_SAVINGS)->sum('balance');
    }

    public function getTotalSharesBalance(): float
    {
        return $this->accounts()->where('account_type', Account::TYPE_SHARES)->sum('balance');
    }

    public function getTotalBalance(): float
    {
        return $this->getTotalSavingsBalance() + $this->getTotalSharesBalance();
    }

    public function getMonthsInSacco(): int
    {
        return $this->joining_date ? $this->joining_date->diffInMonths(now()) : 0;
    }

    public function canBorrow(float $amount, float $multiplier = 3.0, float $minimumBalance = 0): bool
    {
        $savingsBalance = $this->getTotalSavingsBalance();
        $maxLoanAmount = $savingsBalance * $multiplier;
        
        return $savingsBalance >= $minimumBalance && $amount <= $maxLoanAmount;
    }
} 