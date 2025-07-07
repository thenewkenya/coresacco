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
        Schema::create('insurance_premiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('insurance_policies')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->decimal('premium_amount', 15, 2);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'mobile_money',
                'debit_card',
                'credit_card',
                'direct_debit',
                'check',
                'salary_deduction',
                'other'
            ])->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', [
                'pending',
                'paid',
                'overdue',
                'grace_period',
                'waived',
                'cancelled'
            ])->default('pending');
            $table->decimal('late_fee', 15, 2)->default(0);
            $table->date('grace_period_end_date')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['policy_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('payment_date');
            $table->index(['status', 'due_date']);
            $table->index('grace_period_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_premiums');
    }
};
