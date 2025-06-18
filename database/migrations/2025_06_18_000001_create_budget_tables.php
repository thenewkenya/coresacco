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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_income', 12, 2);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('savings_target', 12, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year']);
        });

        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();
        });

        Schema::create('budget_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->timestamp('date');
            $table->string('receipt_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_expenses');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
}; 