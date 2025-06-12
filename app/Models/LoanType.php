<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'interest_rate',
        'minimum_amount',
        'maximum_amount',
        'term_options',
        'requirements',
        'description',
        'processing_fee',
        'status',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'term_options' => 'array',
        'requirements' => 'array',
        'processing_fee' => 'decimal:2',
    ];

    // Statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Relationships
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // Helper methods
    public function isEligibleAmount(float $amount): bool
    {
        return $amount >= $this->minimum_amount && $amount <= $this->maximum_amount;
    }

    public function isValidTerm(int $term): bool
    {
        return in_array($term, $this->term_options);
    }

    public function calculateProcessingFee(float $amount): float
    {
        return $amount * ($this->processing_fee / 100);
    }
} 