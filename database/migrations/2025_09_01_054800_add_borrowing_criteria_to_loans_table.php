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
            // Borrowing criteria fields
            $table->decimal('required_savings_multiplier', 5, 2)->nullable()->after('term_period');
            $table->decimal('minimum_savings_balance', 15, 2)->nullable()->after('required_savings_multiplier');
            $table->integer('minimum_membership_months')->nullable()->after('minimum_savings_balance');
            $table->integer('required_guarantors')->nullable()->after('minimum_membership_months');
            $table->decimal('required_guarantee_amount', 15, 2)->nullable()->after('required_guarantors');
            
            // Evaluation results
            $table->json('evaluation_results')->nullable()->after('required_guarantee_amount');
            $table->boolean('is_eligible')->default(false)->after('evaluation_results');
            $table->text('eligibility_reason')->nullable()->after('is_eligible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'required_savings_multiplier',
                'minimum_savings_balance',
                'minimum_membership_months',
                'required_guarantors',
                'required_guarantee_amount',
                'evaluation_results',
                'is_eligible',
                'eligibility_reason'
            ]);
        });
    }
};
