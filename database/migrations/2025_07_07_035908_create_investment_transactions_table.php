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
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('portfolio_id')->constrained('investment_portfolios')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('investment_products')->onDelete('restrict');
            $table->enum('transaction_type', [
                'purchase',
                'dividend',
                'interest',
                'withdrawal',
                'partial_withdrawal',
                'maturity',
                'fee',
                'penalty',
                'renewal',
                'value_adjustment'
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('units', 15, 4)->default(0); // Units bought/sold
            $table->decimal('unit_price', 15, 4)->default(1); // Price per unit
            $table->string('description');
            $table->string('reference_number')->unique();
            $table->string('external_reference')->nullable(); // Bank reference, etc.
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'cancelled',
                'processing'
            ])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional transaction details
            $table->timestamps();

            // Indexes
            $table->index(['member_id', 'transaction_type']);
            $table->index(['portfolio_id', 'transaction_type']);
            $table->index(['product_id', 'transaction_type']);
            $table->index('reference_number');
            $table->index(['status', 'processed_at']);
            $table->index(['transaction_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
