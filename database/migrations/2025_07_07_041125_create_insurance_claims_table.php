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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('policy_id')->constrained('insurance_policies')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('insurance_products')->onDelete('restrict');
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->enum('claim_type', [
                'death',
                'disability',
                'medical',
                'dental',
                'vision',
                'hospital',
                'surgery',
                'prescription',
                'fire',
                'theft',
                'flood',
                'earthquake',
                'accident',
                'liability',
                'crop_loss',
                'livestock_death',
                'equipment_damage',
                'business_interruption',
                'other'
            ]);
            $table->date('incident_date');
            $table->date('claim_date');
            $table->decimal('claimed_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('deductible_amount', 15, 2)->default(0);
            $table->enum('status', [
                'pending',
                'investigating',
                'approved',
                'rejected',
                'paid',
                'reopened',
                'appealed',
                'settled',
                'fraud_suspected'
            ])->default('pending');
            $table->text('incident_description');
            $table->json('supporting_documents')->nullable();
            $table->json('medical_reports')->nullable();
            $table->json('police_reports')->nullable();
            $table->json('witness_statements')->nullable();
            $table->json('adjuster_notes')->nullable();
            $table->json('settlement_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('processed_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->date('reopened_date')->nullable();
            $table->text('reopened_reason')->nullable();
            $table->json('fraud_investigation')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['member_id', 'claim_type']);
            $table->index(['policy_id', 'status']);
            $table->index(['product_id', 'claim_type']);
            $table->index('claim_number');
            $table->index(['status', 'claim_date']);
            $table->index('incident_date');
            $table->index(['processed_by', 'processed_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
