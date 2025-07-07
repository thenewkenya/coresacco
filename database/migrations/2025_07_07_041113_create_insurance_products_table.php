<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurance_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('insurance_type', [
                'life',
                'health',
                'property',
                'crop',
                'micro',
                'travel',
                'business'
            ]);
            $table->enum('coverage_type', [
                'term_life',
                'whole_life',
                'universal_life',
                'medical',
                'dental',
                'vision',
                'home',
                'auto',
                'business_property',
                'crop_yield',
                'crop_revenue',
                'livestock',
                'micro_life',
                'micro_health',
                'micro_property'
            ]);
            $table->decimal('min_coverage_amount', 15, 2);
            $table->decimal('max_coverage_amount', 15, 2);
            $table->integer('min_age')->default(18);
            $table->integer('max_age')->default(75);
            $table->decimal('base_premium_rate', 8, 4); // Percentage rate
            $table->json('risk_factors')->nullable(); // Risk multipliers
            $table->json('coverage_benefits')->nullable(); // Coverage details
            $table->json('exclusions')->nullable(); // What's not covered
            $table->json('terms_conditions')->nullable(); // Terms and conditions
            $table->enum('premium_frequency', [
                'monthly',
                'quarterly',
                'semi_annually',
                'annually'
            ])->default('monthly');
            $table->integer('grace_period_days')->default(30);
            $table->integer('waiting_period_days')->default(0);
            $table->integer('claim_settlement_days')->default(30);
            $table->json('renewal_terms')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->boolean('requires_medical_exam')->default(false);
            $table->boolean('requires_property_inspection')->default(false);
            $table->decimal('commission_rate', 6, 2)->default(0); // Agent commission percentage
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['insurance_type', 'status']);
            $table->index('coverage_type');
            $table->index(['min_coverage_amount', 'max_coverage_amount']);
            $table->index(['min_age', 'max_age']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_products');
    }
};
