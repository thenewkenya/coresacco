<?php

use App\Models\Account;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Account Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create([
            'member_id' => $this->user->id,
            'balance' => 1000.00,
            'account_type' => Account::TYPE_SAVINGS,
            'status' => Account::STATUS_ACTIVE,
        ]);
    });

    describe('Account Creation', function () {
        it('can be created with valid data', function () {
            $account = Account::factory()->create();
            expect($account)->toBeInstanceOf(Account::class);
            expect($account->account_number)->toMatch('/^ACC\d{10}$/');
        });

        it('generates unique account numbers', function () {
            $account1 = Account::factory()->create();
            $account2 = Account::factory()->create();
            
            expect($account1->account_number)->not->toBe($account2->account_number);
        });

        it('has default values set correctly', function () {
            $account = Account::factory()->create();
            expect($account->currency)->toBe('KES');
            expect($account->status)->toBe(Account::STATUS_ACTIVE);
        });
    });

    describe('Balance Operations', function () {
        it('can deposit money successfully', function () {
            $initialBalance = $this->account->balance;
            $depositAmount = 500.00;
            
            $result = $this->account->deposit($depositAmount);
            
            expect($result)->toBeTrue();
            $this->account->refresh();
            expect($this->account->balance)->toEqual($initialBalance + $depositAmount);
        });

        it('cannot deposit negative amounts', function () {
            $initialBalance = $this->account->balance;
            
            $result = $this->account->deposit(-100.00);
            
            expect($result)->toBeFalse();
            expect($this->account->fresh()->balance)->toBe($initialBalance);
        });

        it('cannot deposit zero amount', function () {
            $initialBalance = $this->account->balance;
            
            $result = $this->account->deposit(0);
            
            expect($result)->toBeFalse();
            expect($this->account->fresh()->balance)->toBe($initialBalance);
        });

        it('can withdraw money with sufficient balance', function () {
            $initialBalance = $this->account->balance;
            $withdrawAmount = 300.00;
            
            $result = $this->account->withdraw($withdrawAmount);
            
            expect($result)->toBeTrue();
            $this->account->refresh();
            expect($this->account->balance)->toEqual($initialBalance - $withdrawAmount);
        });

        it('cannot withdraw more than available balance', function () {
            $initialBalance = $this->account->balance;
            $withdrawAmount = $initialBalance + 100.00;
            
            $result = $this->account->withdraw($withdrawAmount);
            
            expect($result)->toBeFalse();
            expect($this->account->fresh()->balance)->toBe($initialBalance);
        });

        it('cannot withdraw negative amounts', function () {
            $initialBalance = $this->account->balance;
            
            $result = $this->account->withdraw(-50.00);
            
            expect($result)->toBeFalse();
            expect($this->account->fresh()->balance)->toBe($initialBalance);
        });

        it('cannot withdraw zero amount', function () {
            $initialBalance = $this->account->balance;
            
            $result = $this->account->withdraw(0);
            
            expect($result)->toBeFalse();
            expect($this->account->fresh()->balance)->toBe($initialBalance);
        });
    });

    describe('Account Types', function () {
        it('returns all available account types', function () {
            $types = Account::getAccountTypes();
            
            expect($types)->toContain(Account::TYPE_SAVINGS);
            expect($types)->toContain(Account::TYPE_SHARES);
            expect($types)->toContain(Account::TYPE_DEPOSITS);
            expect(count($types))->toBeGreaterThan(5);
        });

        it('returns correct display names for account types', function () {
            $names = Account::getAccountTypeNames();
            
            expect($names[Account::TYPE_SAVINGS])->toBe('Regular Savings');
            expect($names[Account::TYPE_SHARES])->toBe('Share Capital');
            expect($names[Account::TYPE_EMERGENCY_FUND])->toBe('Emergency Fund');
        });

        it('gets display name for account instance', function () {
            $savingsAccount = Account::factory()->create(['account_type' => Account::TYPE_SAVINGS]);
            
            expect($savingsAccount->getDisplayName())->toBe('Regular Savings');
        });

        it('handles unknown account types gracefully', function () {
            // Create account with valid type first, then modify to test graceful handling
            $account = Account::factory()->create(['account_type' => Account::TYPE_SAVINGS]);
            // Manually set the attribute to test the display name method
            $account->account_type = 'unknown_type';
            
            expect($account->getDisplayName())->toBe('Unknown type');
        });
    });

    describe('Relationships', function () {
        it('belongs to a member', function () {
            expect($this->account->member)->toBeInstanceOf(User::class);
            expect($this->account->member->id)->toBe($this->user->id);
        });

        it('has many transactions', function () {
            expect($this->account->transactions())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        });
    });

    describe('Account Status', function () {
        it('can be set to different statuses', function () {
            $this->account->status = Account::STATUS_FROZEN;
            $this->account->save();
            
            expect($this->account->fresh()->status)->toBe(Account::STATUS_FROZEN);
        });

        it('supports all valid database status values', function () {
            // Only test statuses that are valid in the database enum
            $statuses = [
                Account::STATUS_ACTIVE,
                Account::STATUS_INACTIVE,
                Account::STATUS_FROZEN,
                Account::STATUS_CLOSED,
            ];

            foreach ($statuses as $status) {
                $this->account->status = $status;
                $this->account->save();
                
                expect($this->account->fresh()->status)->toBe($status);
            }
        });
    });

    describe('Account Number Generation', function () {
        it('generates account numbers with correct format', function () {
            $accountNumber = Account::generateAccountNumber();
            
            expect($accountNumber)->toMatch('/^ACC\d{10}$/');
            expect(strlen($accountNumber))->toBe(13);
        });

        it('generates unique account numbers', function () {
            $numbers = [];
            
            for ($i = 0; $i < 10; $i++) {
                $numbers[] = Account::generateAccountNumber();
            }
            
            expect(count(array_unique($numbers)))->toBe(10);
        });

        it('includes current year in account number', function () {
            $accountNumber = Account::generateAccountNumber();
            $currentYear = date('Y');
            
            expect($accountNumber)->toContain($currentYear);
        });
    });
}); 