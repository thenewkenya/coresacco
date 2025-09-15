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
        // Drop the existing check constraint
        DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_account_type_check');
        
        // Add the new check constraint with loan_account included
        DB::statement("ALTER TABLE accounts ADD CONSTRAINT accounts_account_type_check CHECK (account_type IN ('savings', 'shares', 'deposits', 'emergency_fund', 'holiday_savings', 'retirement', 'education', 'development', 'welfare', 'investment', 'loan_guarantee', 'loan_account', 'junior', 'goal_based', 'business'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new check constraint
        DB::statement('ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_account_type_check');
        
        // Restore the original check constraint without loan_account
        DB::statement("ALTER TABLE accounts ADD CONSTRAINT accounts_account_type_check CHECK (account_type IN ('savings', 'shares', 'deposits', 'emergency_fund', 'holiday_savings', 'retirement', 'education', 'development', 'welfare', 'investment', 'loan_guarantee', 'junior', 'goal_based', 'business'))");
    }
};
