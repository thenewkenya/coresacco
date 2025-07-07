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
        Schema::create('investment_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('product_type', [
                'fixed_deposit',
                'money_market',
                'government_bond',
                'equity_fund',
                'balanced_fund',
                'retirement_fund'
            ]);
            $table->decimal('minimum_investment', 15, 2);
            $table->decimal('maximum_investment', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0); // Annual interest rate
            $table->decimal('dividend_rate', 5, 2)->default(0); // Expected dividend rate
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->integer('term_months')->nullable(); // Term in months, null for open-ended
            $table->enum('compounding_frequency', [
                'daily',
                'monthly', 
                'quarterly',
                'semi_annually',
                'annually'
            ])->default('monthly');
            $table->enum('liquidity_type', ['high', 'medium', 'low'])->default('medium');
            $table->decimal('early_withdrawal_penalty', 5, 2)->default(0); // Percentage penalty
            $table->decimal('management_fee', 5, 2)->default(0); // Annual management fee percentage
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('maturity_benefits')->nullable(); // Additional benefits at maturity
            $table->json('terms_conditions')->nullable(); // Product terms and conditions
            $table->timestamps();

            // Indexes
            $table->index(['product_type', 'status']);
            $table->index('risk_level');
            $table->index('minimum_investment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_products');
    }
};
