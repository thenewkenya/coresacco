<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Cacheable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Cacheable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'member_number',
        'id_number',
        'phone_number',
        'address',
        'membership_status',
        'joining_date',
        'branch_id',
        'role',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
        'scheduled_for_deletion',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'scheduled_for_deletion' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereJsonContains('permissions', $permission)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->roles()->where(function ($query) use ($permissions) {
            foreach ($permissions as $permission) {
                $query->orWhereJsonContains('permissions', $permission);
            }
        })->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isMember(): bool
    {
        return $this->hasRole('member');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    // SACCO-specific relationships
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'member_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'member_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'member_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function managedBranch(): HasOne
    {
        return $this->hasOne(Branch::class, 'manager_id');
    }

    /**
     * Get the user's financial goals
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'member_id');
    }

    /**
     * Get the user's budgets
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function loanAccounts(): HasMany
    {
        return $this->hasMany(LoanAccount::class, 'member_id');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->member_number)) {
                $user->member_number = static::generateMemberNumber();
            }
        });
    }

    /**
     * Generate a unique member number
     */
    private static function generateMemberNumber(): string
    {
        do {
            $memberNumber = 'MEM' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('member_number', $memberNumber)->exists());

        return $memberNumber;
    }

    /**
     * Check if user can be deleted (no active accounts or debts)
     */
    public function canBeDeleted(): bool
    {
        // Check if user has any accounts with balance > 0
        $hasAccountBalance = $this->accounts()->where('balance', '>', 0)->exists();
        
        // Check if user has any active loans
        $hasActiveLoans = $this->loans()->whereIn('status', ['active', 'disbursed'])->exists();
        
        // Check if user has any active loan accounts
        $hasActiveLoanAccounts = $this->loanAccounts()->where('status', 'active')->exists();
        
        return !$hasAccountBalance && !$hasActiveLoans && !$hasActiveLoanAccounts;
    }

    /**
     * Suspend the user account
     */
    public function suspend(string $reason): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Unsuspend the user account
     */
    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
            'scheduled_for_deletion' => null, // Cancel any scheduled deletion
        ]);
    }

    /**
     * Schedule account for deletion (3 months from now)
     */
    public function scheduleForDeletion(): void
    {
        $this->update([
            'scheduled_for_deletion' => now()->addMonths(3),
        ]);
    }

    /**
     * Cancel scheduled deletion
     */
    public function cancelScheduledDeletion(): void
    {
        $this->update([
            'scheduled_for_deletion' => null,
        ]);
    }

    /**
     * Update last login timestamp and reset deletion timer if suspended
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
        
        // If user is suspended and logs in, reset the deletion timer
        if ($this->is_suspended && $this->scheduled_for_deletion) {
            $this->scheduleForDeletion();
        }
    }

    /**
     * Check if account is scheduled for deletion
     */
    public function isScheduledForDeletion(): bool
    {
        return $this->scheduled_for_deletion && $this->scheduled_for_deletion->isFuture();
    }

    /**
     * Check if account should be deleted (past scheduled date)
     */
    public function shouldBeDeleted(): bool
    {
        return $this->scheduled_for_deletion && $this->scheduled_for_deletion->isPast();
    }
}
