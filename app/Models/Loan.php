<?php

/* Loan manages loan applications and disbursements,
tracks loan status, terms and repayments,
includes methods for interest and payment calculations
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'metadata',
        'required_savings_multiplier',
        'minimum_savings_balance',
        'member_savings_balance',
        'member_shares_balance',
        'member_total_balance',
        'minimum_membership_months',
        'member_months_in_sacco',
        'meets_savings_criteria',
        'meets_membership_criteria',
        'criteria_evaluation_notes',
        'required_guarantors',
        'approved_guarantors',
        'total_guarantee_amount',
        'required_guarantee_amount',
        'meets_guarantor_criteria',
        'approved_at',
        'approved_by',
        'approval_notes',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'disbursed_by',
        'disbursement_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'term_period' => 'integer',
        'disbursement_date' => 'datetime',
        'due_date' => 'datetime',
        'collateral_details' => 'array',
        'metadata' => 'array',
        'required_savings_multiplier' => 'decimal:2',
        'minimum_savings_balance' => 'decimal:2',
        'member_savings_balance' => 'decimal:2',
        'member_shares_balance' => 'decimal:2',
        'member_total_balance' => 'decimal:2',
        'minimum_membership_months' => 'integer',
        'member_months_in_sacco' => 'integer',
        'meets_savings_criteria' => 'boolean',
        'meets_membership_criteria' => 'boolean',
        'required_guarantors' => 'integer',
        'approved_guarantors' => 'integer',
        'total_guarantee_amount' => 'decimal:2',
        'required_guarantee_amount' => 'decimal:2',
        'meets_guarantor_criteria' => 'boolean',
        'approved_at' => 'datetime',
        'approved_by' => 'integer',
        'rejected_at' => 'datetime',
        'rejected_by' => 'integer',
        'disbursed_by' => 'integer',
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
        return $this->belongsTo(User::class, 'member_id');
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function guarantors(): BelongsToMany
    {
        return $this->belongsToMany(Guarantor::class, 'loan_guarantors')
                    ->withPivot(['guarantee_amount', 'status', 'approved_at', 'rejection_reason'])
                    ->withTimestamps();
    }

    public function loanAccount(): HasOne
    {
        return $this->hasOne(LoanAccount::class);
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

    // Borrowing criteria methods
    public function evaluateBorrowingCriteria(): array
    {
        $member = $this->member;
        $evaluation = [
            'savings_criteria' => $this->evaluateSavingsCriteria($member),
            'membership_criteria' => $this->evaluateMembershipCriteria($member),
            'guarantor_criteria' => $this->evaluateGuarantorCriteria(),
            'overall_eligible' => false,
            'notes' => []
        ];

        $evaluation['overall_eligible'] = $evaluation['savings_criteria']['meets'] && 
                                        $evaluation['membership_criteria']['meets'] && 
                                        $evaluation['guarantor_criteria']['meets'];

        return $evaluation;
    }

    public function evaluateSavingsCriteria($member): array
    {
        $savingsAccounts = $member->accounts()->where('account_type', Account::TYPE_SAVINGS)->get();
        $sharesAccounts = $member->accounts()->where('account_type', Account::TYPE_SHARES)->get();
        
        $savingsBalance = $savingsAccounts->sum('balance');
        $sharesBalance = $sharesAccounts->sum('balance');
        $totalBalance = $savingsBalance + $sharesBalance;

        $maxLoanAmount = $savingsBalance * $this->required_savings_multiplier;
        $meetsMinimumBalance = $savingsBalance >= $this->minimum_savings_balance;
        $meetsLoanAmount = $this->amount <= $maxLoanAmount;

        $meetsSavingsCriteria = $meetsMinimumBalance && $meetsLoanAmount;

        return [
            'meets' => $meetsSavingsCriteria,
            'savings_balance' => $savingsBalance,
            'shares_balance' => $sharesBalance,
            'total_balance' => $totalBalance,
            'max_loan_amount' => $maxLoanAmount,
            'minimum_balance_met' => $meetsMinimumBalance,
            'loan_amount_met' => $meetsLoanAmount,
            'multiplier' => $this->required_savings_multiplier,
            'minimum_required' => $this->minimum_savings_balance
        ];
    }

    public function evaluateMembershipCriteria($member): array
    {
        $joiningDate = $member->joining_date;
        $monthsInSacco = $joiningDate ? $joiningDate->diffInMonths(now()) : 0;
        
        $meetsMembershipCriteria = $monthsInSacco >= $this->minimum_membership_months;

        return [
            'meets' => $meetsMembershipCriteria,
            'months_in_sacco' => $monthsInSacco,
            'minimum_required' => $this->minimum_membership_months,
            'joining_date' => $joiningDate
        ];
    }

    public function evaluateGuarantorCriteria(): array
    {
        $approvedGuarantors = $this->guarantors()->wherePivot('status', 'approved')->count();
        $totalGuaranteeAmount = $this->guarantors()->wherePivot('status', 'approved')->sum('loan_guarantors.guarantee_amount');
        
        $meetsGuarantorCount = $approvedGuarantors >= $this->required_guarantors;
        $meetsGuaranteeAmount = $totalGuaranteeAmount >= $this->required_guarantee_amount;
        
        $meetsGuarantorCriteria = $meetsGuarantorCount && $meetsGuaranteeAmount;

        return [
            'meets' => $meetsGuarantorCriteria,
            'approved_guarantors' => $approvedGuarantors,
            'required_guarantors' => $this->required_guarantors,
            'total_guarantee_amount' => $totalGuaranteeAmount,
            'required_guarantee_amount' => $this->required_guarantee_amount,
            'count_met' => $meetsGuarantorCount,
            'amount_met' => $meetsGuaranteeAmount
        ];
    }

    public function isEligibleForLoan(): bool
    {
        $evaluation = $this->evaluateBorrowingCriteria();
        return $evaluation['overall_eligible'];
    }

    public function getEligibilityReport(): array
    {
        return $this->evaluateBorrowingCriteria();
    }
} 