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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_account_id')->nullable()->after('loan_id');
            $table->foreign('loan_account_id')->references('id')->on('loan_accounts')->onDelete('set null');
            $table->index(['loan_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['loan_account_id']);
            $table->dropIndex(['loan_account_id']);
            $table->dropColumn('loan_account_id');
        });
    }
};
