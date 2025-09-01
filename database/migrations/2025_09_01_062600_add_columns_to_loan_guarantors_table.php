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
        Schema::table('loan_guarantors', function (Blueprint $table) {
            // Add foreign key columns
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade')->after('id');
            $table->foreignId('guarantor_id')->constrained('guarantors')->onDelete('cascade')->after('loan_id');
            
            // Add guarantee details
            $table->decimal('guarantee_amount', 15, 2)->after('guarantor_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('guarantee_amount');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            
            // Add unique constraint
            $table->unique(['loan_id', 'guarantor_id'], 'loan_guarantor_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_guarantors', function (Blueprint $table) {
            $table->dropUnique('loan_guarantor_unique');
            $table->dropColumn([
                'loan_id',
                'guarantor_id', 
                'guarantee_amount',
                'status',
                'rejection_reason',
                'approved_at'
            ]);
        });
    }
};
