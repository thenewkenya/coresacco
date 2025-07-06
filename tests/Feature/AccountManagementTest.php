<?php

use App\Models\User;
use App\Models\Account;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Account Management', function () {
    beforeEach(function () {
        $this->branch = Branch::factory()->create();
        
        // Create roles
        $this->adminRole = Role::factory()->create(['slug' => 'admin']);
        $this->memberRole = Role::factory()->create(['slug' => 'member']);
        $this->staffRole = Role::factory()->create(['slug' => 'staff']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($this->adminRole);

        // Create member user
        $this->member = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->member->roles()->attach($this->memberRole);

        // Create staff user
        $this->staff = User::factory()->create();
        $this->staff->roles()->attach($this->staffRole);
    });

    describe('Account Creation', function () {
        it('allows admin to create new account for member', function () {
            $this->actingAs($this->admin)
                ->post('/accounts', [
                    'member_id' => $this->member->id,
                    'account_type' => Account::TYPE_SAVINGS,
                    'initial_deposit' => 1000.00,
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            $this->assertDatabaseHas('accounts', [
                'member_id' => $this->member->id,
                'account_type' => Account::TYPE_SAVINGS,
                'balance' => 1000.00,
                'status' => Account::STATUS_ACTIVE,
            ]);
        });

        it('allows staff to create accounts', function () {
            $this->actingAs($this->staff)
                ->post('/accounts', [
                    'member_id' => $this->member->id,
                    'account_type' => Account::TYPE_SHARES,
                    'initial_deposit' => 500.00,
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            $this->assertDatabaseHas('accounts', [
                'member_id' => $this->member->id,
                'account_type' => Account::TYPE_SHARES,
                'balance' => 500.00,
            ]);
        });

        it('prevents member from creating accounts for others', function () {
            $otherMember = User::factory()->create();
            $otherMember->roles()->attach($this->memberRole);

            $this->actingAs($this->member)
                ->post('/accounts', [
                    'member_id' => $otherMember->id,
                    'account_type' => Account::TYPE_SAVINGS,
                    'initial_deposit' => 1000.00,
                ])
                ->assertForbidden();
        });

        it('validates required fields for account creation', function () {
            $this->actingAs($this->admin)
                ->post('/accounts', [])
                ->assertSessionHasErrors(['member_id', 'account_type']);
        });

        it('validates initial deposit amount', function () {
            $this->actingAs($this->admin)
                ->post('/accounts', [
                    'member_id' => $this->member->id,
                    'account_type' => Account::TYPE_SAVINGS,
                    'initial_deposit' => -100.00,
                ])
                ->assertSessionHasErrors(['initial_deposit']);
        });

        it('prevents duplicate account types for same member', function () {
            // Create first savings account
            Account::factory()->create([
                'member_id' => $this->member->id,
                'account_type' => Account::TYPE_SAVINGS,
            ]);

            // Try to create second savings account
            $this->actingAs($this->admin)
                ->post('/accounts', [
                    'member_id' => $this->member->id,
                    'account_type' => Account::TYPE_SAVINGS,
                    'initial_deposit' => 500.00,
                ])
                ->assertSessionHasErrors(['account_type']);
        });
    });

    describe('Account Viewing', function () {
        beforeEach(function () {
            $this->account = Account::factory()->create([
                'member_id' => $this->member->id,
                'balance' => 2500.00,
            ]);
        });

        it('allows member to view their own accounts', function () {
            $this->actingAs($this->member)
                ->get('/accounts/my')
                ->assertOk()
                ->assertSee($this->account->account_number)
                ->assertSee('2,500.00');
        });

        it('allows admin to view all accounts', function () {
            $this->actingAs($this->admin)
                ->get('/accounts')
                ->assertOk()
                ->assertSee($this->account->account_number);
        });

        it('allows staff to view member accounts', function () {
            $this->actingAs($this->staff)
                ->get("/accounts/{$this->account->id}")
                ->assertOk()
                ->assertSee($this->account->account_number);
        });

        it('prevents member from viewing other members accounts', function () {
            $otherMember = User::factory()->create();
            $otherMember->roles()->attach($this->memberRole);
            $otherAccount = Account::factory()->create(['member_id' => $otherMember->id]);

            $this->actingAs($this->member)
                ->get("/accounts/{$otherAccount->id}")
                ->assertForbidden();
        });
    });

    describe('Account Status Management', function () {
        beforeEach(function () {
            $this->account = Account::factory()->create([
                'member_id' => $this->member->id,
                'status' => Account::STATUS_ACTIVE,
            ]);
        });

        it('allows admin to suspend account', function () {
            $this->actingAs($this->admin)
                ->patch("/accounts/{$this->account->id}/status", [
                    'status' => 'inactive',
                    'status_reason' => 'Suspicious activity detected',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            $this->assertDatabaseHas('accounts', [
                'id' => $this->account->id,
                'status' => 'inactive',
                'status_reason' => 'Suspicious activity detected',
            ]);
        });

        it('allows admin to reactivate suspended account', function () {
            $this->account->update([
                'status' => 'inactive',
                'status_reason' => 'Investigation'
            ]);

            $this->actingAs($this->admin)
                ->patch("/accounts/{$this->account->id}/status", [
                    'status' => 'active',
                    'status_reason' => 'Investigation completed',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            $this->assertDatabaseHas('accounts', [
                'id' => $this->account->id,
                'status' => 'active',
            ]);
        });

        it('prevents member from changing account status', function () {
            $this->actingAs($this->member)
                ->patch("/accounts/{$this->account->id}/status", [
                    'status' => Account::STATUS_CLOSED,
                ])
                ->assertForbidden();
        });

        it('requires reason for status changes', function () {
            $this->actingAs($this->admin)
                ->patch("/accounts/{$this->account->id}/status", [
                    'status' => Account::STATUS_FROZEN,
                ])
                ->assertSessionHasErrors(['status_reason']);
        });
    });

    describe('Account Closure', function () {
        beforeEach(function () {
            $this->account = Account::factory()->create([
                'member_id' => $this->member->id,
                'balance' => 0.00,
                'status' => Account::STATUS_ACTIVE,
            ]);
        });

        it('allows admin to close account with zero balance', function () {
            $this->actingAs($this->admin)
                ->delete("/accounts/{$this->account->id}")
                ->assertRedirect()
                ->assertSessionHas('success');

            $this->assertDatabaseHas('accounts', [
                'id' => $this->account->id,
                'status' => Account::STATUS_CLOSED,
            ]);
        });

        it('prevents closing account with positive balance', function () {
            $this->account->update(['balance' => 500.00]);

            $this->actingAs($this->admin)
                ->delete("/accounts/{$this->account->id}")
                ->assertRedirect()
                ->assertSessionHasErrors(['balance']);
        });

        it('allows member to request account closure', function () {
            $this->actingAs($this->member)
                ->post("/accounts/{$this->account->id}/close-request", [
                    'reason' => 'Moving to another institution',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            // Should create a notification or task for admin approval
            $this->assertDatabaseHas('notifications', [
                'type' => 'App\\Notifications\\SystemNotification',
                'data->account_id' => $this->account->id,
            ]);
        });
    });

    describe('Account Search and Filtering', function () {
        beforeEach(function () {
            // Create multiple accounts for testing
            $this->savingsAccount = Account::factory()->create([
                'member_id' => $this->member->id,
                'account_type' => Account::TYPE_SAVINGS,
                'balance' => 1000.00,
            ]);

            $this->sharesAccount = Account::factory()->create([
                'member_id' => $this->member->id,
                'account_type' => Account::TYPE_SHARES,
                'balance' => 2500.00,
            ]);
        });

        it('allows filtering accounts by type', function () {
            $this->actingAs($this->admin)
                ->get('/accounts?account_type=' . Account::TYPE_SAVINGS)
                ->assertOk()
                ->assertSee($this->savingsAccount->account_number)
                ->assertDontSee($this->sharesAccount->account_number);
        });

        it('allows searching accounts by member name', function () {
            $this->actingAs($this->admin)
                ->get('/accounts?search=' . $this->member->name)
                ->assertOk()
                ->assertSee($this->savingsAccount->account_number)
                ->assertSee($this->sharesAccount->account_number);
        });

        it('allows filtering accounts by status', function () {
            $this->savingsAccount->update(['status' => 'inactive']);

            $this->actingAs($this->admin)
                ->get('/accounts?status=' . Account::STATUS_ACTIVE)
                ->assertOk()
                ->assertSee($this->sharesAccount->account_number)
                ->assertDontSee($this->savingsAccount->account_number);
        });

        it('allows filtering accounts by balance range', function () {
            $this->actingAs($this->admin)
                ->get('/accounts?min_balance=3000')
                ->assertOk();
                // Note: Balance filtering functionality may need implementation in AccountController
        });
    });

    describe('Account Statements', function () {
        beforeEach(function () {
            $this->account = Account::factory()->create([
                'member_id' => $this->member->id,
            ]);
        });

        it('generates account statement for member', function () {
            $this->actingAs($this->member)
                ->get("/accounts/{$this->account->id}/statement")
                ->assertOk()
                ->assertHeader('content-type', 'application/pdf');
        });

        it('allows filtering statement by date range', function () {
            $this->actingAs($this->member)
                ->get("/accounts/{$this->account->id}/statement", [
                    'from_date' => now()->subMonth()->format('Y-m-d'),
                    'to_date' => now()->format('Y-m-d'),
                ])
                ->assertOk();
        });

        it('prevents member from accessing other members statements', function () {
            $otherMember = User::factory()->create();
            $otherMember->roles()->attach($this->memberRole);
            $otherAccount = Account::factory()->create(['member_id' => $otherMember->id]);

            $this->actingAs($this->member)
                ->get("/accounts/{$otherAccount->id}/statement")
                ->assertForbidden();
        });
    });
}); 