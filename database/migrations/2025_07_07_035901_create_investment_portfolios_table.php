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
        Schema::create('investment_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('investment_products')->onDelete('restrict');
            $table->string('certificate_number')->unique()->nullable();
            $table->decimal('investment_amount', 15, 2); // Original investment amount
            $table->decimal('units_purchased', 15, 4)->default(0); // For unit-based investments
            $table->decimal('unit_price', 15, 4)->default(1); // Price per unit at purchase
            $table->timestamp('purchase_date');
            $table->timestamp('maturity_date')->nullable();
            $table->decimal('current_value', 15, 2)->default(0); // Current market value
            $table->decimal('accrued_interest', 15, 2)->default(0); // Interest earned to date
            $table->decimal('dividend_earned', 15, 2)->default(0); // Dividends earned to date
            $table->enum('status', [
                'active',
                'matured',
                'withdrawn',
                'renewed',
                'pending_withdrawal'
            ])->default('active');
            $table->boolean('auto_renewal')->default(false);
            $table->timestamp('withdrawal_notice_date')->nullable(); // For medium liquidity products
            $table->json('metadata')->nullable(); // Additional investment details
            $table->timestamps();

            // Indexes
            $table->index(['member_id', 'status']);
            $table->index('product_id');
            $table->index('certificate_number');
            $table->index('maturity_date');
            $table->index(['status', 'maturity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_portfolios');
    }
};
