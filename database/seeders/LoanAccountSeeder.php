<?php

namespace Database\Seeders;

use App\Models\LoanAccount;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LoanAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active and disbursed loans that don't have loan accounts yet
        $loans = Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])
            ->whereDoesntHave('loanAccount')
            ->with(['member', 'loanType'])
            ->get();

        foreach ($loans as $loan) {
            $this->createLoanAccount($loan);
        }
    }

    /**
     * Create a loan account for a given loan
     */
    private function createLoanAccount(Loan $loan): void
    {
        $member = $loan->member;
        $loanType = $loan->loanType;
        
        // Generate unique account number
        $accountNumber = 'LA' . str_pad($loan->id, 6, '0', STR_PAD_LEFT) . '-' . 
                         strtoupper(substr($member->member_number ?? 'M' . $member->id, -4));
        
        // Calculate loan details
        $principalAmount = $loan->amount;
        $interestRate = $loan->interest_rate;
        $termMonths = $loan->term_period;
        
        // Calculate monthly payment (simple interest calculation)
        $monthlyInterest = ($principalAmount * $interestRate / 100) / 12;
        $monthlyPrincipal = $principalAmount / $termMonths;
        $monthlyPayment = $monthlyPrincipal + $monthlyInterest;
        
        // Calculate totals
        $totalInterest = $principalAmount * $interestRate / 100 * ($termMonths / 12);
        $totalPayable = $principalAmount + $totalInterest;
        
        // Calculate fees (simplified)
        $processingFee = $principalAmount * 0.02; // 2% processing fee
        $insuranceFee = $principalAmount * 0.01; // 1% insurance fee
        $otherFees = $principalAmount * 0.005; // 0.5% other fees
        
        // Set disbursement and payment dates
        $disbursementDate = $loan->disbursement_date ?? now()->subDays(rand(1, 30));
        $firstPaymentDate = $disbursementDate->copy()->addMonth();
        $maturityDate = $disbursementDate->copy()->addMonths($termMonths);
        
        // Calculate payments made (for active loans)
        $amountPaid = 0;
        $principalPaid = 0;
        $interestPaid = 0;
        $feesPaid = $processingFee + $insuranceFee + $otherFees;
        
        if ($loan->status === Loan::STATUS_ACTIVE) {
            // Calculate how many payments have been made
            $monthsElapsed = $disbursementDate->diffInMonths(now());
            $monthsElapsed = min($monthsElapsed, $termMonths);
            
            $amountPaid = $monthlyPayment * $monthsElapsed;
            $principalPaid = $monthlyPrincipal * $monthsElapsed;
            $interestPaid = $monthlyInterest * $monthsElapsed;
        }
        
        // Calculate outstanding amounts
        $outstandingPrincipal = $principalAmount - $principalPaid;
        $outstandingInterest = $totalInterest - $interestPaid;
        $outstandingFees = ($processingFee + $insuranceFee + $otherFees) - $feesPaid;
        
        // Calculate next payment date
        $nextPaymentDate = $firstPaymentDate;
        if ($loan->status === Loan::STATUS_ACTIVE && $outstandingPrincipal > 0) {
            $lastPaymentMonth = $disbursementDate->copy()->addMonths($monthsElapsed);
            $nextPaymentDate = $lastPaymentMonth->addMonth();
        }
        
        // Generate payment schedule
        $paymentSchedule = $this->generatePaymentSchedule(
            $disbursementDate,
            $termMonths,
            $monthlyPayment,
            $monthlyPrincipal,
            $monthlyInterest
        );
        
        // Determine loan account status
        $status = LoanAccount::STATUS_ACTIVE;
        if ($loan->status === Loan::STATUS_COMPLETED) {
            $status = LoanAccount::STATUS_COMPLETED;
        } elseif ($loan->status === Loan::STATUS_DEFAULTED) {
            $status = LoanAccount::STATUS_DEFAULTED;
        }
        
        LoanAccount::create([
            'member_id' => $loan->member_id,
            'loan_id' => $loan->id,
            'account_number' => $accountNumber,
            'loan_type' => $this->mapLoanType($loanType->name),
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'interest_basis' => LoanAccount::INTEREST_REDUCING_BALANCE,
            'term_months' => $termMonths,
            'monthly_payment' => $monthlyPayment,
            'total_payable' => $totalPayable,
            'total_interest' => $totalInterest,
            'processing_fee' => $processingFee,
            'insurance_fee' => $insuranceFee,
            'other_fees' => $otherFees,
            'amount_disbursed' => $principalAmount,
            'amount_paid' => $amountPaid,
            'principal_paid' => $principalPaid,
            'interest_paid' => $interestPaid,
            'fees_paid' => $feesPaid,
            'outstanding_principal' => $outstandingPrincipal,
            'outstanding_interest' => $outstandingInterest,
            'outstanding_fees' => $outstandingFees,
            'arrears_amount' => $this->calculateArrears($nextPaymentDate, $monthlyPayment),
            'arrears_days' => $this->calculateArrearsDays($nextPaymentDate),
            'disbursement_date' => $disbursementDate,
            'first_payment_date' => $firstPaymentDate,
            'maturity_date' => $maturityDate,
            'last_payment_date' => $amountPaid > 0 ? $disbursementDate->copy()->addMonths($monthsElapsed) : null,
            'next_payment_date' => $nextPaymentDate,
            'status' => $status,
            'payment_schedule' => $paymentSchedule,
            'notes' => "Loan account created from loan application #{$loan->id}",
        ]);
    }
    
    /**
     * Map loan type name to loan account type
     */
    private function mapLoanType(string $loanTypeName): string
    {
        $mapping = [
            'Personal Loan' => LoanAccount::TYPE_SALARY_BACKED,
            'Business Loan' => LoanAccount::TYPE_BUSINESS_LOAN,
            'Emergency Loan' => LoanAccount::TYPE_EMERGENCY,
            'Education Loan' => LoanAccount::TYPE_SALARY_BACKED,
            'Asset Financing' => LoanAccount::TYPE_ASSET_BACKED,
        ];
        
        return $mapping[$loanTypeName] ?? LoanAccount::TYPE_SALARY_BACKED;
    }
    
    /**
     * Generate payment schedule
     */
    private function generatePaymentSchedule($startDate, $termMonths, $monthlyPayment, $monthlyPrincipal, $monthlyInterest): array
    {
        $schedule = [];
        $currentDate = $startDate->copy();
        $remainingPrincipal = $monthlyPrincipal * $termMonths;
        
        for ($i = 1; $i <= $termMonths; $i++) {
            $currentDate->addMonth();
            $remainingPrincipal -= $monthlyPrincipal;
            
            $schedule[] = [
                'payment_number' => $i,
                'due_date' => $currentDate->format('Y-m-d'),
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'total_amount' => $monthlyPayment,
                'remaining_principal' => max(0, $remainingPrincipal),
            ];
        }
        
        return $schedule;
    }
    
    /**
     * Calculate arrears amount
     */
    private function calculateArrears($nextPaymentDate, $monthlyPayment): float
    {
        if (!$nextPaymentDate || $nextPaymentDate->isFuture()) {
            return 0;
        }
        
        $monthsOverdue = $nextPaymentDate->diffInMonths(now());
        return $monthlyPayment * $monthsOverdue;
    }
    
    /**
     * Calculate arrears days
     */
    private function calculateArrearsDays($nextPaymentDate): int
    {
        if (!$nextPaymentDate || $nextPaymentDate->isFuture()) {
            return 0;
        }
        
        return $nextPaymentDate->diffInDays(now());
    }
}
