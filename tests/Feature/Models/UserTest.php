<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Branch;
use App\Models\Goal;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->branch = Branch::factory()->create();
    });

    describe('User Creation', function () {
        it('can be created with valid data', function () {
            $user = User::factory()->create();
            
            expect($user)->toBeInstanceOf(User::class);
            expect($user->email)->not->toBeNull();
            expect($user->name)->not->toBeNull();
        });

        it('generates member number automatically', function () {
            $user = User::factory()->create();
            
            expect($user->member_number)->not->toBeNull();
            expect($user->member_number)->toMatch('/^MEM\d{6}$/');
        });

        it('hashes password correctly', function () {
            $user = User::factory()->create(['password' => 'test123']);
            
            expect($user->password)->not->toBe('test123');
            expect(password_verify('test123', $user->password))->toBeTrue();
        });
    });

    describe('Initials Generation', function () {
        it('generates correct initials from full name', function () {
            $user = User::factory()->create(['name' => 'John Smith']);
            
            expect($user->initials())->toBe('JS');
        });

        it('handles single name correctly', function () {
            $user = User::factory()->create(['name' => 'Madonna']);
            
            expect($user->initials())->toBe('M');
        });

        it('handles multiple names correctly', function () {
            $user = User::factory()->create(['name' => 'Mary Jane Watson Parker']);
            
            expect($user->initials())->toBe('MJ'); // Takes first two words
        });

        it('handles empty name gracefully', function () {
            $user = User::factory()->create(['name' => '']);
            
            expect($user->initials())->toBe('');
        });
    });

    describe('Role Management', function () {
        beforeEach(function () {
            $this->adminRole = Role::factory()->create([
                'name' => 'Administrator',
                'slug' => 'admin',
                'permissions' => ['manage_users', 'view_reports', 'approve_loans'],
            ]);

            $this->memberRole = Role::factory()->create([
                'name' => 'Member',
                'slug' => 'member',
                'permissions' => ['view_account', 'apply_loan'],
            ]);

            $this->staffRole = Role::factory()->create([
                'name' => 'Staff',
                'slug' => 'staff',
                'permissions' => ['process_transactions', 'view_member_data'],
            ]);
        });

        it('can be assigned roles', function () {
            $this->user->roles()->attach($this->adminRole);
            
            expect($this->user->roles)->toHaveCount(1);
            expect($this->user->roles->first()->slug)->toBe('admin');
        });

        it('can have multiple roles', function () {
            $this->user->roles()->attach([$this->adminRole->id, $this->staffRole->id]);
            
            expect($this->user->roles)->toHaveCount(2);
            expect($this->user->roles->pluck('slug'))->toContain('admin', 'staff');
        });

        it('checks single role correctly', function () {
            $this->user->roles()->attach($this->adminRole);
            
            expect($this->user->hasRole('admin'))->toBeTrue();
            expect($this->user->hasRole('member'))->toBeFalse();
        });

        it('checks multiple roles correctly', function () {
            $this->user->roles()->attach([$this->adminRole->id, $this->staffRole->id]);
            
            expect($this->user->hasAnyRole(['admin', 'member']))->toBeTrue();
            expect($this->user->hasAnyRole(['member', 'guest']))->toBeFalse();
        });

        it('provides role shortcuts', function () {
            // Test admin role
            $this->user->roles()->attach($this->adminRole);
            expect($this->user->isAdmin())->toBeTrue();
            expect($this->user->isMember())->toBeFalse();
            expect($this->user->isStaff())->toBeFalse();

            // Reset and test member role
            $this->user->roles()->detach();
            $this->user->roles()->attach($this->memberRole);
            expect($this->user->isAdmin())->toBeFalse();
            expect($this->user->isMember())->toBeTrue();
            expect($this->user->isStaff())->toBeFalse();

            // Reset and test staff role
            $this->user->roles()->detach();
            $this->user->roles()->attach($this->staffRole);
            expect($this->user->isAdmin())->toBeFalse();
            expect($this->user->isMember())->toBeFalse();
            expect($this->user->isStaff())->toBeTrue();
        });
    });

    describe('Permission Management', function () {
        beforeEach(function () {
            $this->adminRole = Role::factory()->create([
                'slug' => 'admin',
                'permissions' => ['manage_users', 'view_reports', 'approve_loans'],
            ]);

            $this->memberRole = Role::factory()->create([
                'slug' => 'member', 
                'permissions' => ['view_account', 'apply_loan'],
            ]);
        });

        it('checks single permission correctly', function () {
            $this->user->roles()->attach($this->adminRole);
            
            expect($this->user->hasPermission('manage_users'))->toBeTrue();
            expect($this->user->hasPermission('unknown_permission'))->toBeFalse();
        });

        it('checks multiple permissions correctly', function () {
            $this->user->roles()->attach($this->adminRole);
            
            expect($this->user->hasAnyPermission(['manage_users', 'view_reports']))->toBeTrue();
            expect($this->user->hasAnyPermission(['unknown1', 'unknown2']))->toBeFalse();
        });

        it('inherits permissions from multiple roles', function () {
            $this->user->roles()->attach([$this->adminRole->id, $this->memberRole->id]);
            
            expect($this->user->hasPermission('manage_users'))->toBeTrue(); // From admin role
            expect($this->user->hasPermission('view_account'))->toBeTrue(); // From member role
        });
    });

    describe('SACCO Relationships', function () {
        it('has many accounts', function () {
            Account::factory()->count(3)->create(['member_id' => $this->user->id]);
            
            expect($this->user->accounts)->toHaveCount(3);
            expect($this->user->accounts()->first())->toBeInstanceOf(Account::class);
        });

        it('has many loans', function () {
            Loan::factory()->count(2)->create(['member_id' => $this->user->id]);
            
            expect($this->user->loans)->toHaveCount(2);
            expect($this->user->loans()->first())->toBeInstanceOf(Loan::class);
        });

        it('has many transactions', function () {
            Transaction::factory()->count(5)->create(['member_id' => $this->user->id]);
            
            expect($this->user->transactions)->toHaveCount(5);
            expect($this->user->transactions()->first())->toBeInstanceOf(Transaction::class);
        });

        it('belongs to a branch', function () {
            $this->user->update(['branch_id' => $this->branch->id]);
            
            expect($this->user->branch)->toBeInstanceOf(Branch::class);
            expect($this->user->branch->id)->toBe($this->branch->id);
        });

        it('can manage a branch', function () {
            $this->branch->update(['manager_id' => $this->user->id]);
            
            expect($this->user->managedBranch)->toBeInstanceOf(Branch::class);
            expect($this->user->managedBranch->id)->toBe($this->branch->id);
        });

        it('has many goals', function () {
            Goal::factory()->count(3)->create(['member_id' => $this->user->id]);
            
            expect($this->user->goals)->toHaveCount(3);
            expect($this->user->goals()->first())->toBeInstanceOf(Goal::class);
        });

        it('has many budgets', function () {
            Budget::factory()->create([
                'user_id' => $this->user->id,
                'month' => 6,
                'year' => 2024,
            ]);
            Budget::factory()->create([
                'user_id' => $this->user->id,
                'month' => 7,
                'year' => 2024,
            ]);
            
            expect($this->user->budgets)->toHaveCount(2);
            expect($this->user->budgets()->first())->toBeInstanceOf(Budget::class);
        });
    });

    describe('Member Information', function () {
        it('stores member-specific data', function () {
            $userData = [
                'member_number' => 'MEM123456',
                'id_number' => '12345678',
                'phone_number' => '+254712345678',
                'address' => '123 Main St, Nairobi',
                'membership_status' => 'active',
                'joining_date' => now()->subYear(),
            ];

            $user = User::factory()->create($userData);

            expect($user->member_number)->toBe('MEM123456');
            expect($user->id_number)->toBe('12345678');
            expect($user->phone_number)->toBe('+254712345678');
            expect($user->address)->toBe('123 Main St, Nairobi');
            expect($user->membership_status)->toBe('active');
            expect($user->joining_date)->toBeInstanceOf(Carbon\Carbon::class);
        });

        it('casts joining_date to Carbon instance', function () {
            $user = User::factory()->create([
                'joining_date' => '2023-01-15',
            ]);

            expect($user->joining_date)->toBeInstanceOf(Carbon\Carbon::class);
            expect($user->joining_date->format('Y-m-d'))->toBe('2023-01-15');
        });
    });

    describe('Authentication Features', function () {
        it('hides sensitive attributes', function () {
            $user = User::factory()->create(['password' => 'secret123']);
            $userArray = $user->toArray();

            expect($userArray)->not->toHaveKey('password');
            expect($userArray)->not->toHaveKey('remember_token');
        });

        it('includes fillable attributes', function () {
            $fillableFields = [
                'name', 'email', 'password', 'member_number', 'id_number',
                'phone_number', 'address', 'membership_status', 'joining_date',
                'branch_id', 'role'
            ];

            $user = new User();
            
            foreach ($fillableFields as $field) {
                expect($user->getFillable())->toContain($field);
            }
        });
    });

    describe('Role Relationships with Timestamps', function () {
        it('stores timestamps when roles are attached', function () {
            $role = Role::factory()->create();
            
            $this->user->roles()->attach($role);
            
            $pivot = $this->user->roles()->where('role_id', $role->id)->first()->pivot;
            expect($pivot->created_at)->not->toBeNull();
            expect($pivot->updated_at)->not->toBeNull();
        });
    });
}); 