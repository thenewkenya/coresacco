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
        Schema::create('insurance', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->unique();
            $table->foreignId('member_id')->constrained('users')->onDelete('restrict');
            $table->enum('insurance_type', ['life', 'health', 'property', 'business']);
            $table->decimal('coverage_amount', 15, 2);
            $table->decimal('premium_amount', 15, 2);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('status', [
                'active',
                'inactive',
                'expired',
                'cancelled',
                'claimed'
            ])->default('active');
            $table->json('beneficiaries');
            $table->json('terms_conditions');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create insurance claims table
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_id')->constrained('insurance')->onDelete('restrict');
            $table->string('claim_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->json('documents')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurance');
    }
};
