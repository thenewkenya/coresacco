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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('member_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('loan_id')->nullable()->constrained()->onDelete('restrict');
            $table->enum('type', [
                'deposit',
                'withdrawal',
                'loan_disbursement',
                'loan_repayment',
                'transfer',
                'fee',
                'interest'
            ]);
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('reference_number')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
