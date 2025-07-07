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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance');
    }
};
