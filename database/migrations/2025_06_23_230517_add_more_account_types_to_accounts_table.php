<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, set any NULL account_type values to 'savings' (default)
        DB::statement("UPDATE accounts SET account_type = 'savings' WHERE account_type IS NULL");
        
        Schema::table('accounts', function (Blueprint $table) {
            // First, drop the existing enum constraint
            $table->dropColumn('account_type');
        });

        Schema::table('accounts', function (Blueprint $table) {
            // Add the new enum with all SACCO account types
            $table->enum('account_type', [
                'savings',           // Regular savings account
                'shares',            // Share capital/ownership
                'deposits',          // Term deposits/fixed deposits
                'emergency_fund',    // Emergency savings
                'holiday_savings',   // Holiday/vacation savings
                'retirement',        // Retirement/pension savings
                'education',         // Education savings
                'development',       // Development fund
                'welfare',           // Welfare fund
                'investment',        // Investment account
                'loan_guarantee',    // Loan guarantee fund
                'insurance',         // Insurance fund
            ])->after('member_id')->default('savings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Drop the new enum
            $table->dropColumn('account_type');
        });

        Schema::table('accounts', function (Blueprint $table) {
            // Restore the original enum
            $table->enum('account_type', ['savings', 'shares', 'deposits'])->after('member_id');
        });
    }
};
