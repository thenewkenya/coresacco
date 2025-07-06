<?php

use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Transaction Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create([
            'member_id' => $this->user->id,
            'balance' => 1000.00,
        ]);
        $this->loan = Loan::factory()->create(['member_id' => $this->user->id]);
    });

    describe('Transaction Creation', function () {
        it('can be created with valid data', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'member_id' => $this->user->id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => 500.00,
            ]);

            expect($transaction)->toBeInstanceOf(Transaction::class);
            expect($transaction->amount)->toEqual(500.00);
            expect($transaction->type)->toBe(Transaction::TYPE_DEPOSIT);
        });

        it('generates reference number correctly', function () {
            $transaction = Transaction::factory()->create();
            $referenceNumber = $transaction->generateReferenceNumber();
            
            expect($referenceNumber)->toMatch('/^TXN\d{8}\d{6}$/');
            expect($referenceNumber)->toContain(date('Ymd'));
        });

        it('stores metadata as array', function () {
            $metadata = ['source' => 'mobile_app', 'device_id' => 'ABC123'];
            
            $transaction = Transaction::factory()->create([
                'metadata' => $metadata,
            ]);

            expect($transaction->fresh()->metadata)->toBe($metadata);
        });
    });

    describe('Transaction Types', function () {
        it('identifies debit transactions correctly', function () {
            $debitTypes = [
                Transaction::TYPE_WITHDRAWAL,
                Transaction::TYPE_LOAN_REPAYMENT,
                Transaction::TYPE_TRANSFER,
                Transaction::TYPE_FEE,
            ];

            foreach ($debitTypes as $type) {
                $transaction = Transaction::factory()->create(['type' => $type]);
                expect($transaction->isDebit())->toBeTrue("$type should be debit");
                expect($transaction->isCredit())->toBeFalse("$type should not be credit");
            }
        });

        it('identifies credit transactions correctly', function () {
            $creditTypes = [
                Transaction::TYPE_DEPOSIT,
                Transaction::TYPE_LOAN_DISBURSEMENT,
                Transaction::TYPE_INTEREST,
            ];

            foreach ($creditTypes as $type) {
                $transaction = Transaction::factory()->create(['type' => $type]);
                expect($transaction->isCredit())->toBeTrue("$type should be credit");
                expect($transaction->isDebit())->toBeFalse("$type should not be debit");
            }
        });

        it('handles transfer type correctly', function () {
            $transfer = Transaction::factory()->create(['type' => Transaction::TYPE_TRANSFER]);
            
            expect($transfer->isDebit())->toBeTrue();
            expect($transfer->isCredit())->toBeFalse();
        });
    });

    describe('Balance Tracking', function () {
        it('tracks balance before and after transaction', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => 500.00,
                'balance_before' => 1000.00,
                'balance_after' => 1500.00,
            ]);

            expect($transaction->balance_before)->toEqual(1000.00);
            expect($transaction->balance_after)->toEqual(1500.00);
            expect($transaction->balance_after - $transaction->balance_before)->toEqual(500.00);
        });

        it('calculates correct balance change for withdrawal', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => 300.00,
                'balance_before' => 1000.00,
                'balance_after' => 700.00,
            ]);

            expect($transaction->balance_before - $transaction->balance_after)->toBe(300.00);
        });
    });

    describe('Relationships', function () {
        it('belongs to an account', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
            ]);

            expect($transaction->account)->toBeInstanceOf(Account::class);
            expect($transaction->account->id)->toBe($this->account->id);
        });

        it('belongs to a member', function () {
            $transaction = Transaction::factory()->create([
                'member_id' => $this->user->id,
            ]);

            expect($transaction->member)->toBeInstanceOf(User::class);
            expect($transaction->member->id)->toBe($this->user->id);
        });

        it('can belong to a loan', function () {
            $transaction = Transaction::factory()->create([
                'loan_id' => $this->loan->id,
                'type' => Transaction::TYPE_LOAN_DISBURSEMENT,
            ]);

            expect($transaction->loan)->toBeInstanceOf(Loan::class);
            expect($transaction->loan->id)->toBe($this->loan->id);
        });

        it('can exist without a loan', function () {
            $transaction = Transaction::factory()->create([
                'loan_id' => null,
                'type' => Transaction::TYPE_DEPOSIT,
            ]);

            expect($transaction->loan)->toBeNull();
        });
    });

    describe('Transaction Status', function () {
        it('supports all defined status constants', function () {
            $statuses = [
                Transaction::STATUS_PENDING,
                Transaction::STATUS_COMPLETED,
                Transaction::STATUS_FAILED,
                Transaction::STATUS_REVERSED,
            ];

            foreach ($statuses as $status) {
                $transaction = Transaction::factory()->create(['status' => $status]);
                expect($transaction->status)->toBe($status);
            }
        });

        it('defaults to pending status', function () {
            $transaction = Transaction::factory()->create();
            expect($transaction->status)->toBe(Transaction::STATUS_PENDING);
        });
    });

    describe('Loan-Related Transactions', function () {
        it('handles loan disbursement transactions', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'member_id' => $this->user->id,
                'loan_id' => $this->loan->id,
                'type' => Transaction::TYPE_LOAN_DISBURSEMENT,
                'amount' => 5000.00,
            ]);

            expect($transaction->type)->toBe(Transaction::TYPE_LOAN_DISBURSEMENT);
            expect($transaction->isCredit())->toBeTrue();
            expect($transaction->loan_id)->toBe($this->loan->id);
        });

        it('handles loan repayment transactions', function () {
            $transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'member_id' => $this->user->id,
                'loan_id' => $this->loan->id,
                'type' => Transaction::TYPE_LOAN_REPAYMENT,
                'amount' => 1200.00,
            ]);

            expect($transaction->type)->toBe(Transaction::TYPE_LOAN_REPAYMENT);
            expect($transaction->isDebit())->toBeTrue();
            expect($transaction->loan_id)->toBe($this->loan->id);
        });
    });

    describe('Fee and Interest Transactions', function () {
        it('handles fee transactions as debits', function () {
            $transaction = Transaction::factory()->create([
                'type' => Transaction::TYPE_FEE,
                'amount' => 50.00,
                'description' => 'Monthly maintenance fee',
            ]);

            expect($transaction->isDebit())->toBeTrue();
            expect($transaction->description)->toContain('fee');
        });

        it('handles interest transactions as credits', function () {
            $transaction = Transaction::factory()->create([
                'type' => Transaction::TYPE_INTEREST,
                'amount' => 25.00,
                'description' => 'Monthly interest earned',
            ]);

            expect($transaction->isCredit())->toBeTrue();
            expect($transaction->description)->toContain('interest');
        });
    });

    describe('Soft Deletes', function () {
        it('can be soft deleted', function () {
            $transaction = Transaction::factory()->create();
            $transactionId = $transaction->id;

            $transaction->delete();

            expect(Transaction::find($transactionId))->toBeNull();
            expect(Transaction::withTrashed()->find($transactionId))->not->toBeNull();
        });

        it('can be restored after soft delete', function () {
            $transaction = Transaction::factory()->create();
            $transactionId = $transaction->id;

            $transaction->delete();
            $transaction->restore();

            expect(Transaction::find($transactionId))->not->toBeNull();
        });
    });
}); 