<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class LoanAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'loan_id',
        'account_number',
        'loan_type',
        'principal_amount',
        'interest_rate',
        'interest_basis',
        'term_months',
        'monthly_payment',
        'total_payable',
        'total_interest',
        'processing_fee',
        'insurance_fee',
        'other_fees',
        'amount_disbursed',
        'amount_paid',
        'principal_paid',
        'interest_paid',
        'fees_paid',
        'outstanding_principal',
        'outstanding_interest',
        'outstanding_fees',
        'arrears_amount',
        'arrears_days',
        'disbursement_date',
        'first_payment_date',
        'maturity_date',
        'last_payment_date',
        'next_payment_date',
        'status',
        'payment_schedule',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'term_months' => 'integer',
        'monthly_payment' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'other_fees' => 'decimal:2',
        'amount_disbursed' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'principal_paid' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'fees_paid' => 'decimal:2',
        'outstanding_principal' => 'decimal:2',
        'outstanding_interest' => 'decimal:2',
        'outstanding_fees' => 'decimal:2',
        'arrears_amount' => 'decimal:2',
        'arrears_days' => 'integer',
        'disbursement_date' => 'date',
        'first_payment_date' => 'date',
        'maturity_date' => 'date',
        'last_payment_date' => 'date',
        'next_payment_date' => 'date',
        'payment_schedule' => 'array',
    ];

    // Loan types
    const TYPE_SALARY_BACKED = 'salary_backed';
    const TYPE_ASSET_BACKED = 'asset_backed';
    const TYPE_GROUP_LOAN = 'group_loan';
    const TYPE_BUSINESS_LOAN = 'business_loan';
    const TYPE_EMERGENCY = 'emergency';

    // Interest basis
    const INTEREST_FLAT_RATE = 'flat_rate';
    const INTEREST_REDUCING_BALANCE = 'reducing_balance';
    const INTEREST_ONLY_PERIOD = 'interest_only_period';

    // Status
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DEFAULTED = 'defaulted';
    const STATUS_WRITTEN_OFF = 'written_off';

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function calculateTotalInterest(): float
    {
        switch ($this->interest_basis) {
            case self::INTEREST_FLAT_RATE:
                return $this->principal_amount * ($this->interest_rate / 100) * $this->term_months;
            
            case self::INTEREST_REDUCING_BALANCE:
                return $this->calculateReducingBalanceInterest();
            
            case self::INTEREST_ONLY_PERIOD:
                return $this->calculateInterestOnlyPeriod();
            
            default:
                return $this->principal_amount * ($this->interest_rate / 100) * $this->term_months;
        }
    }

    private function calculateReducingBalanceInterest(): float
    {
        $monthlyRate = $this->interest_rate / 100 / 12;
        $totalInterest = 0;
        $remainingPrincipal = $this->principal_amount;

        for ($month = 1; $month <= $this->term_months; $month++) {
            $monthlyInterest = $remainingPrincipal * $monthlyRate;
            $monthlyPrincipal = $this->monthly_payment - $monthlyInterest;
            
            $totalInterest += $monthlyInterest;
            $remainingPrincipal -= $monthlyPrincipal;
        }

        return $totalInterest;
    }

    private function calculateInterestOnlyPeriod(): float
    {
        // For interest-only period, calculate interest for the entire term
        return $this->principal_amount * ($this->interest_rate / 100) * $this->term_months;
    }

    public function calculateMonthlyPayment(): float
    {
        switch ($this->interest_basis) {
            case self::INTEREST_FLAT_RATE:
                return ($this->principal_amount + $this->calculateTotalInterest()) / $this->term_months;
            
            case self::INTEREST_REDUCING_BALANCE:
                return $this->calculateReducingBalancePayment();
            
            case self::INTEREST_ONLY_PERIOD:
                return $this->principal_amount * ($this->interest_rate / 100) / 12;
            
            default:
                return ($this->principal_amount + $this->calculateTotalInterest()) / $this->term_months;
        }
    }

    private function calculateReducingBalancePayment(): float
    {
        $monthlyRate = $this->interest_rate / 100 / 12;
        $numerator = $this->principal_amount * $monthlyRate * pow(1 + $monthlyRate, $this->term_months);
        $denominator = pow(1 + $monthlyRate, $this->term_months) - 1;
        
        return $numerator / $denominator;
    }

    public function generatePaymentSchedule(): array
    {
        $schedule = [];
        $remainingPrincipal = $this->principal_amount;
        $currentDate = $this->first_payment_date;

        for ($month = 1; $month <= $this->term_months; $month++) {
            $monthlyInterest = $remainingPrincipal * ($this->interest_rate / 100) / 12;
            $monthlyPrincipal = $this->monthly_payment - $monthlyInterest;
            
            // Ensure we don't overpay in the last month
            if ($month === $this->term_months) {
                $monthlyPrincipal = $remainingPrincipal;
                $monthlyInterest = $this->monthly_payment - $monthlyPrincipal;
            }

            $schedule[] = [
                'installment_number' => $month,
                'due_date' => $currentDate->format('Y-m-d'),
                'principal_amount' => round($monthlyPrincipal, 2),
                'interest_amount' => round($monthlyInterest, 2),
                'total_payment' => round($monthlyPrincipal + $monthlyInterest, 2),
                'outstanding_principal' => round($remainingPrincipal - $monthlyPrincipal, 2),
            ];

            $remainingPrincipal -= $monthlyPrincipal;
            $currentDate = $currentDate->addMonth();
        }

        return $schedule;
    }

    public function calculateArrears(): array
    {
        $today = Carbon::today();
        $overduePayments = 0;
        $totalArrears = 0;

        if ($this->next_payment_date < $today) {
            $overduePayments = $today->diffInMonths($this->next_payment_date);
            $totalArrears = $overduePayments * $this->monthly_payment;
        }

        return [
            'arrears_days' => $today->diffInDays($this->next_payment_date),
            'arrears_amount' => $totalArrears,
            'overdue_installments' => $overduePayments,
        ];
    }

    public function isOverdue(): bool
    {
        return $this->next_payment_date < Carbon::today() && $this->status === self::STATUS_ACTIVE;
    }

    public function getOutstandingBalance(): float
    {
        return $this->outstanding_principal + $this->outstanding_interest + $this->outstanding_fees;
    }

    public function getTotalFees(): float
    {
        return $this->processing_fee + $this->insurance_fee + $this->other_fees;
    }

    public static function generateAccountNumber(): string
    {
        $prefix = 'LA';
        $year = date('Y');
        $month = date('m');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $random;
    }
}
