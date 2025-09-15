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
        Schema::create('loan_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('loan_id');
            $table->string('account_number')->unique();
            $table->string('loan_type'); // salary_backed, asset_backed, group_loan, business_loan, emergency
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->string('interest_basis'); // flat_rate, reducing_balance, interest_only_period
            $table->integer('term_months');
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('total_payable', 15, 2);
            $table->decimal('total_interest', 15, 2);
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->decimal('insurance_fee', 15, 2)->default(0);
            $table->decimal('other_fees', 15, 2)->default(0);
            $table->decimal('amount_disbursed', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('principal_paid', 15, 2)->default(0);
            $table->decimal('interest_paid', 15, 2)->default(0);
            $table->decimal('fees_paid', 15, 2)->default(0);
            $table->decimal('outstanding_principal', 15, 2);
            $table->decimal('outstanding_interest', 15, 2)->default(0);
            $table->decimal('outstanding_fees', 15, 2)->default(0);
            $table->decimal('arrears_amount', 15, 2)->default(0);
            $table->integer('arrears_days')->default(0);
            $table->date('disbursement_date');
            $table->date('first_payment_date');
            $table->date('maturity_date');
            $table->date('last_payment_date')->nullable();
            $table->date('next_payment_date');
            $table->string('status'); // active, completed, defaulted, written_off
            $table->json('payment_schedule')->nullable(); // Store the payment schedule
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            
            // Indexes
            $table->index(['member_id', 'status']);
            $table->index(['loan_id']);
            $table->index(['status']);
            $table->index(['next_payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_accounts');
    }
};
