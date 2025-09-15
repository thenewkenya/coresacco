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
        Schema::table('loans', function (Blueprint $table) {
            // Add only the missing columns that LoanController needs
            $table->decimal('member_savings_balance', 15, 2)->default(0);
            $table->decimal('member_shares_balance', 15, 2)->default(0);
            $table->decimal('member_total_balance', 15, 2)->default(0);
            $table->integer('member_months_in_sacco')->default(0);
            $table->boolean('meets_savings_criteria')->default(false);
            $table->boolean('meets_membership_criteria')->default(false);
            $table->text('criteria_evaluation_notes')->nullable();
            $table->integer('approved_guarantors')->default(0);
            $table->decimal('total_guarantee_amount', 15, 2)->default(0);
            $table->boolean('meets_guarantor_criteria')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'member_savings_balance',
                'member_shares_balance',
                'member_total_balance',
                'member_months_in_sacco',
                'meets_savings_criteria',
                'meets_membership_criteria',
                'criteria_evaluation_notes',
                'approved_guarantors',
                'total_guarantee_amount',
                'meets_guarantor_criteria',
            ]);
        });
    }
};