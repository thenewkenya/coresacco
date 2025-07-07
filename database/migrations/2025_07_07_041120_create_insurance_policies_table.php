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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->unique();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('insurance_products')->onDelete('restrict');
            $table->decimal('coverage_amount', 15, 2);
            $table->decimal('premium_amount', 15, 2);
            $table->enum('premium_frequency', [
                'monthly',
                'quarterly',
                'semi_annually',
                'annually'
            ])->default('monthly');
            $table->date('policy_start_date');
            $table->date('policy_end_date')->nullable();
            $table->date('next_premium_due_date');
            $table->enum('status', [
                'pending',
                'active',
                'grace_period',
                'lapsed',
                'cancelled',
                'expired',
                'claim_paid',
                'suspended'
            ])->default('pending');
            $table->json('beneficiaries')->nullable(); // List of beneficiaries
            $table->json('risk_assessment')->nullable(); // Risk assessment data
            $table->json('medical_exam_results')->nullable(); // Medical exam results
            $table->json('property_inspection_results')->nullable(); // Property inspection results
            $table->json('underwriting_notes')->nullable(); // Underwriting notes
            $table->foreignId('agent_id')->nullable()->constrained('users');
            $table->decimal('commission_rate', 6, 2)->default(0);
            $table->decimal('total_premiums_paid', 15, 2)->default(0);
            $table->decimal('total_claims_paid', 15, 2)->default(0);
            $table->date('last_premium_payment_date')->nullable();
            $table->date('grace_period_end_date')->nullable();
            $table->date('lapse_date')->nullable();
            $table->date('reinstatement_date')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['member_id', 'status']);
            $table->index(['product_id', 'status']);
            $table->index('policy_number');
            $table->index('next_premium_due_date');
            $table->index(['status', 'policy_start_date']);
            $table->index('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
