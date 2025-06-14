<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    private static $referenceCounter = 1;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = Account::with('member')->get();
        $loans = Loan::with('member')->where('status', '!=', Loan::STATUS_PENDING)->get();

        // Generate transactions for the last 12 months
        $startDate = now()->subMonths(12);
        $endDate = now();

        foreach ($accounts as $account) {
            $this->generateAccountTransactions($account, $startDate, $endDate);
        }

        foreach ($loans as $loan) {
            $this->generateLoanTransactions($loan);
        }
    }

    /**
     * Generate transactions for an account
     */
    private function generateAccountTransactions(Account $account, Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();
        $currentBalance = $account->balance;

        // Work backwards to maintain realistic balance progression
        $transactions = [];
        $transactionCount = rand(10, 50); // 10-50 transactions per account

        for ($i = 0; $i < $transactionCount; $i++) {
            $transactionDate = $this->randomDateBetween($startDate, $endDate);
            
            // Higher probability of deposits vs withdrawals
            $isDeposit = rand(1, 10) <= 7; // 70% deposits, 30% withdrawals
            
            if ($isDeposit) {
                $amount = rand(1000, 15000); // Deposit between 1K-15K
                $type = Transaction::TYPE_DEPOSIT;
                $description = $this->getRandomDepositDescription();
            } else {
                $amount = rand(500, min($currentBalance * 0.3, 10000)); // Max 30% of balance or 10K
                $type = Transaction::TYPE_WITHDRAWAL;
                $description = $this->getRandomWithdrawalDescription();
            }

            $transactions[] = [
                'date' => $transactionDate,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'is_deposit' => $isDeposit,
            ];
        }

        // Sort by date and create transactions
        usort($transactions, fn($a, $b) => $a['date']->timestamp <=> $b['date']->timestamp);

        $runningBalance = rand(5000, 20000); // Starting balance
        
        foreach ($transactions as $txn) {
            $balanceBefore = $runningBalance;
            
            if ($txn['is_deposit']) {
                $runningBalance += $txn['amount'];
            } else {
                $runningBalance = max(0, $runningBalance - $txn['amount']);
            }

            Transaction::create([
                'account_id' => $account->id,
                'member_id' => $account->member_id,
                'type' => $txn['type'],
                'amount' => $txn['amount'],
                'description' => $txn['description'],
                'reference_number' => $this->generateReferenceNumber(),
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => $balanceBefore,
                'balance_after' => $runningBalance,
                'created_at' => $txn['date'],
                'updated_at' => $txn['date'],
            ]);
        }

        // Update account with final balance
        $account->update(['balance' => $runningBalance]);
    }

    /**
     * Generate loan-related transactions
     */
    private function generateLoanTransactions(Loan $loan): void
    {
        // Disbursement transaction
        if ($loan->disbursement_date) {
            Transaction::create([
                'member_id' => $loan->member_id,
                'loan_id' => $loan->id,
                'type' => Transaction::TYPE_LOAN_DISBURSEMENT,
                'amount' => $loan->amount,
                'description' => "Loan disbursement - {$loan->loanType->name}",
                'reference_number' => $this->generateReferenceNumber(),
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => 0,
                'balance_after' => $loan->amount,
                'created_at' => $loan->disbursement_date,
                'updated_at' => $loan->disbursement_date,
            ]);

            // Generate repayment transactions for active/completed loans
            if (in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_COMPLETED])) {
                $this->generateLoanRepayments($loan);
            }
        }
    }

    /**
     * Generate loan repayment transactions
     */
    private function generateLoanRepayments(Loan $loan): void
    {
        $monthlyPayment = $loan->calculateMonthlyPayment();
        $startDate = $loan->disbursement_date->copy()->addMonth();
        $currentDate = $startDate->copy();
        $totalPaid = 0;
        $totalOwed = $loan->calculateTotalRepayment();

        while ($currentDate->lte(now()) && $totalPaid < $totalOwed) {
            // 95% chance of on-time payment
            $isOnTime = rand(1, 100) <= 95;
            $paymentDate = $isOnTime ? $currentDate->copy() : $currentDate->copy()->addDays(rand(1, 15));
            
            // Vary payment amount slightly
            $paymentAmount = $monthlyPayment * (rand(95, 105) / 100);
            $paymentAmount = min($paymentAmount, $totalOwed - $totalPaid);

            if ($paymentAmount > 0) {
                Transaction::create([
                    'member_id' => $loan->member_id,
                    'loan_id' => $loan->id,
                    'type' => Transaction::TYPE_LOAN_REPAYMENT,
                    'amount' => $paymentAmount,
                    'description' => "Loan repayment - {$loan->loanType->name}",
                    'reference_number' => $this->generateReferenceNumber(),
                    'status' => Transaction::STATUS_COMPLETED,
                    'balance_before' => $totalOwed - $totalPaid,
                    'balance_after' => $totalOwed - $totalPaid - $paymentAmount,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate,
                ]);

                $totalPaid += $paymentAmount;
            }

            $currentDate->addMonth();
        }
    }

    /**
     * Generate a random date between two dates
     */
    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        $timestamp = rand($start->timestamp, $end->timestamp);
        return Carbon::createFromTimestamp($timestamp);
    }

    /**
     * Generate a unique reference number
     */
    private function generateReferenceNumber(): string
    {
        $reference = 'TXN' . date('YmdHis') . str_pad(self::$referenceCounter, 6, '0', STR_PAD_LEFT);
        self::$referenceCounter++;
        return $reference;
    }

    /**
     * Get random deposit descriptions
     */
    private function getRandomDepositDescription(): string
    {
        $descriptions = [
            'Monthly savings contribution',
            'Salary deposit',
            'Cash deposit',
            'Mobile money transfer',
            'Bank transfer',
            'Interest payment',
            'Bonus deposit',
            'Business income',
            'Share capital contribution',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get random withdrawal descriptions
     */
    private function getRandomWithdrawalDescription(): string
    {
        $descriptions = [
            'Cash withdrawal',
            'School fees payment',
            'Medical expenses',
            'Emergency withdrawal',
            'Shopping withdrawal',
            'Transport withdrawal',
            'Utility bills payment',
            'Mobile money withdrawal',
            'ATM withdrawal',
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
