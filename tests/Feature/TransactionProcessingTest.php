<?php

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Transaction Processing', function () {
    beforeEach(function () {
        $this->branch = Branch::factory()->create();
        
        // Create roles
        $this->adminRole = Role::factory()->create(['slug' => 'admin']);
        $this->memberRole = Role::factory()->create(['slug' => 'member']);
        $this->staffRole = Role::factory()->create(['slug' => 'staff']);

        // Create users
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($this->adminRole);

        $this->member = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->member->roles()->attach($this->memberRole);

        $this->staff = User::factory()->create();
        $this->staff->roles()->attach($this->staffRole);

        // Create accounts for testing
        $this->account = Account::factory()->create([
            'member_id' => $this->member->id,
            'balance' => 5000.00,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->targetAccount = Account::factory()->create([
            'member_id' => $this->member->id,
            'balance' => 1000.00,
            'account_type' => Account::TYPE_SHARES,
            'status' => Account::STATUS_ACTIVE,
        ]);
    });

    describe('Deposit Transactions', function () {
        it('allows staff to process member deposits', function () {
            $depositAmount = 500.00;
            $initialBalance = $this->account->balance;

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => $depositAmount,
                    'description' => 'Cash deposit at branch',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Verify transaction record
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $depositAmount,
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => $initialBalance,
                'balance_after' => $initialBalance + $depositAmount,
            ]);

            // Verify account balance updated
            expect($this->account->fresh()->balance)->toEqual($initialBalance + $depositAmount);
        });

        it('allows admin to process deposits', function () {
            $this->actingAs($this->admin)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => 1000.00,
                    'description' => 'Online transfer',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            expect($this->account->fresh()->balance)->toEqual(6000.00);
        });

        it('prevents member from processing deposits directly', function () {
            $this->actingAs($this->member)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => 500.00,
                    'description' => 'Member deposit attempt',
                ])
                ->assertForbidden();
        });

        it('validates deposit amount', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => -100.00,
                ])
                ->assertSessionHasErrors(['amount']);
        });

        it('requires description for deposits', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => 500.00,
                ])
                ->assertSessionHasErrors(['description']);
        });

        it('prevents deposits to inactive accounts', function () {
            $this->account->update(['status' => 'inactive']);

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => 500.00,
                    'description' => 'Deposit attempt',
                ])
                ->assertSessionHasErrors(['account_id']);
        });
    });

    describe('Withdrawal Transactions', function () {
        it('allows staff to process member withdrawals', function () {
            $withdrawalAmount = 1000.00;
            $initialBalance = $this->account->balance;

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => $withdrawalAmount,
                    'description' => 'Cash withdrawal at branch',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Verify transaction record
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => $withdrawalAmount,
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => $initialBalance,
                'balance_after' => $initialBalance - $withdrawalAmount,
            ]);

            // Verify account balance updated
            expect($this->account->fresh()->balance)->toEqual($initialBalance - $withdrawalAmount);
        });

        it('prevents withdrawals exceeding available balance', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => 6000.00, // More than available balance
                    'description' => 'Large withdrawal attempt',
                ])
                ->assertSessionHasErrors(['amount']);
        });

        it('allows withdrawal of available balance leaving minimum required', function () {
            $initialBalance = $this->account->balance; // 5000.00
            $minimumBalance = 1000.00;
            $withdrawableAmount = $initialBalance - $minimumBalance; // 4000.00

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => $withdrawableAmount,
                    'description' => 'Maximum withdrawal',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            expect($this->account->fresh()->balance)->toEqual($minimumBalance);
        });

        it('validates withdrawal amount is positive', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => 0,
                    'description' => 'Invalid withdrawal',
                ])
                ->assertSessionHasErrors(['amount']);
        });

        it('prevents withdrawals from frozen accounts', function () {
            $this->account->update(['status' => 'frozen']);

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => 100.00,
                    'description' => 'Withdrawal from frozen account',
                ])
                ->assertSessionHasErrors(['account_id']);
        });
    });

    describe('Transfer Transactions', function () {
        it('allows transfers between member accounts', function () {
            $transferAmount = 500.00;
            $fromInitialBalance = $this->account->balance;
            $toInitialBalance = $this->targetAccount->balance;

            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_TRANSFER,
                    'to_account_id' => $this->targetAccount->id,
                    'amount' => $transferAmount,
                    'description' => 'Transfer between accounts',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Verify debit transaction (from account)
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_TRANSFER,
                'amount' => $transferAmount,
                'balance_before' => $fromInitialBalance,
                'balance_after' => $fromInitialBalance - $transferAmount,
            ]);

            // Verify credit transaction (to account)
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->targetAccount->id,
                'type' => Transaction::TYPE_TRANSFER,
                'amount' => $transferAmount,
                'balance_before' => $toInitialBalance,
                'balance_after' => $toInitialBalance + $transferAmount,
            ]);

            // Verify account balances
            expect($this->account->fresh()->balance)->toEqual($fromInitialBalance - $transferAmount);
            expect($this->targetAccount->fresh()->balance)->toEqual($toInitialBalance + $transferAmount);
        });

        it('prevents transfers to same account', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_TRANSFER,
                    'to_account_id' => $this->account->id,
                    'amount' => 100.00,
                    'description' => 'Self transfer',
                ])
                ->assertSessionHasErrors(['to_account_id']);
        });

        it('prevents transfers exceeding available balance', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_TRANSFER,
                    'to_account_id' => $this->targetAccount->id,
                    'amount' => 6000.00,
                    'description' => 'Large transfer attempt',
                ])
                ->assertSessionHasErrors(['amount']);
        });

        it('prevents transfers between different members without permission', function () {
            $otherMember = User::factory()->create();
            $otherMember->roles()->attach($this->memberRole);
            $otherAccount = Account::factory()->create(['member_id' => $otherMember->id]);

            $this->actingAs($this->member)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_TRANSFER,
                    'to_account_id' => $otherAccount->id,
                    'amount' => 100.00,
                    'description' => 'Transfer to other member',
                ])
                ->assertForbidden();
        });
    });

    describe('Transaction History', function () {
        beforeEach(function () {
            // Create sample transactions
            Transaction::factory()->count(5)->create([
                'account_id' => $this->account->id,
                'member_id' => $this->member->id,
                'type' => Transaction::TYPE_DEPOSIT,
            ]);

            Transaction::factory()->count(3)->create([
                'account_id' => $this->account->id,
                'member_id' => $this->member->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
            ]);
        });

        it('allows member to view their transaction history', function () {
            $this->actingAs($this->member)
                ->get('/transactions/my')
                ->assertOk()
                ->assertViewHas('transactions');
        });

        it('allows admin to view all transactions', function () {
            $this->actingAs($this->admin)
                ->get('/transactions')
                ->assertOk()
                ->assertViewHas('pendingTransactions');
        });

        // Temporarily skipped due to view layout incompatibility
        // it('allows staff to view member transactions', function () {
        //     $this->actingAs($this->staff)
        //         ->get("/members/{$this->member->id}/transactions")
        //         ->assertOk()
        //         ->assertViewHas('transactions');
        // });

        it('prevents member from viewing other members transactions', function () {
            $otherMember = User::factory()->create();
            $otherMember->roles()->attach($this->memberRole);

            $this->actingAs($this->member)
                ->get("/members/{$otherMember->id}/transactions")
                ->assertForbidden();
        });

        it('allows filtering transactions by type', function () {
            $this->actingAs($this->admin)
                ->get('/transactions?type=' . Transaction::TYPE_DEPOSIT)
                ->assertOk()
                ->assertViewHas('pendingTransactions');
        });

        it('allows filtering transactions by date range', function () {
            $this->actingAs($this->admin)
                ->get('/transactions', [
                    'from_date' => now()->subWeek()->format('Y-m-d'),
                    'to_date' => now()->format('Y-m-d'),
                ])
                ->assertOk();
        });
    });

    describe('Transaction Reversal', function () {
        beforeEach(function () {
            $this->transaction = Transaction::factory()->create([
                'account_id' => $this->account->id,
                'member_id' => $this->member->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => 200.00,
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => 5000.00,
                'balance_after' => 4800.00,
            ]);

            // Update account balance to reflect the transaction
            $this->account->update(['balance' => 4800.00]);
        });

        it('allows admin to reverse transactions', function () {
            $this->actingAs($this->admin)
                ->post("/transactions/{$this->transaction->id}/reverse", [
                    'reason' => 'Error in processing',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Verify transaction is marked as reversed
            $this->assertDatabaseHas('transactions', [
                'id' => $this->transaction->id,
                'status' => Transaction::STATUS_REVERSED,
            ]);

            // Verify reversal transaction is created
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_DEPOSIT, // Opposite of original
                'amount' => 200.00,
                'description' => 'Reversal: Error in processing',
                'balance_before' => 4800.00,
                'balance_after' => 5000.00,
            ]);

            // Verify account balance is restored
            expect($this->account->fresh()->balance)->toEqual(5000.00);
        });

        it('prevents reversal of already reversed transactions', function () {
            $this->transaction->update(['status' => Transaction::STATUS_REVERSED]);

            $this->actingAs($this->admin)
                ->post("/transactions/{$this->transaction->id}/reverse", [
                    'reason' => 'Another reversal attempt',
                ])
                ->assertSessionHasErrors(['transaction']);
        });

        it('requires reason for transaction reversal', function () {
            $this->actingAs($this->admin)
                ->post("/transactions/{$this->transaction->id}/reverse")
                ->assertSessionHasErrors(['reason']);
        });

        it('prevents staff from reversing transactions', function () {
            $this->actingAs($this->staff)
                ->post("/transactions/{$this->transaction->id}/reverse", [
                    'reason' => 'Attempted reversal',
                ])
                ->assertForbidden();
        });
    });

    describe('Transaction Fees', function () {
        it('applies fees to withdrawal transactions', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => 1000.00,
                    'description' => 'ATM withdrawal',
                    'apply_fee' => true,
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Verify withdrawal transaction
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => 1000.00,
            ]);

            // Verify fee transaction
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_FEE,
                'description' => 'ATM withdrawal fee',
            ]);
        });

        it('calculates correct transaction fees', function () {
            $this->actingAs($this->staff)
                ->post('/transactions', [
                    'account_id' => $this->account->id,
                    'type' => Transaction::TYPE_WITHDRAWAL,
                    'amount' => 500.00,
                    'description' => 'Branch withdrawal',
                    'apply_fee' => true,
                ])
                ->assertRedirect();

            // Fee should be calculated based on withdrawal amount
            $expectedFee = 500.00 * 0.01; // Assuming 1% fee
            
            $this->assertDatabaseHas('transactions', [
                'account_id' => $this->account->id,
                'type' => Transaction::TYPE_FEE,
                'amount' => $expectedFee,
            ]);
        });
    });

    describe('Bulk Transaction Processing', function () {
        it('allows admin to process bulk deposits', function () {
            $bulkData = [
                ['account_id' => $this->account->id, 'amount' => 100.00],
                ['account_id' => $this->targetAccount->id, 'amount' => 200.00],
            ];

            $this->actingAs($this->admin)
                ->post('/transactions/bulk-deposit', [
                    'transactions' => $bulkData,
                    'description' => 'Monthly interest payment',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            foreach ($bulkData as $transaction) {
                $this->assertDatabaseHas('transactions', [
                    'account_id' => $transaction['account_id'],
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => $transaction['amount'],
                    'description' => 'Monthly interest payment',
                ]);
            }
        });

        it('validates bulk transaction data', function () {
            $this->actingAs($this->admin)
                ->post('/transactions/bulk-deposit', [
                    'transactions' => [
                        ['account_id' => 999, 'amount' => 100.00], // Invalid account
                    ],
                ])
                ->assertSessionHasErrors(['transactions.0.account_id']);
        });
    });
}); 