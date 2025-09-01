<?php

/* Member (extends User) manages member info and r/ships,
tracks membership status and links accts and loans */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends User
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_number',
        'full_name',
        'id_number',
        'phone_number',
        'address',
        'membership_status',
        'joining_date',
        'branch_id',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'membership_status' => 'string',
    ];

    // Relationships
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }



    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
} 