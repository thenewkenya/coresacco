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
        // Update the account_type enum to include all account types
        DB::statement("ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_account_type_check");
        DB::statement("ALTER TABLE accounts ALTER COLUMN account_type TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE accounts ADD CONSTRAINT accounts_account_type_check CHECK (account_type IN ('savings', 'shares', 'deposits', 'junior', 'goal_based', 'business'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE accounts DROP CONSTRAINT IF EXISTS accounts_account_type_check");
        DB::statement("ALTER TABLE accounts ALTER COLUMN account_type TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE accounts ADD CONSTRAINT accounts_account_type_check CHECK (account_type IN ('savings', 'shares', 'deposits'))");
    }
};
