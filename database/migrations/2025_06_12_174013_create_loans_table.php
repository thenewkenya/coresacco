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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('loan_type_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_period');
            $table->enum('status', [
                'pending',
                'approved',
                'disbursed',
                'active',
                'completed',
                'defaulted',
                'rejected'
            ])->default('pending');
            $table->timestamp('disbursement_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->json('collateral_details')->nullable();
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
