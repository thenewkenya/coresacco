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
        // Add indexes for frequently queried columns
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('role');
            $table->index('membership_status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('account_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
            $table->index(['member_id', 'created_at']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('account_type');
            $table->index('status');
            $table->index(['member_id', 'account_type']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['member_id', 'status']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
            $table->dropIndex(['membership_status']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['account_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['member_id', 'created_at']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['account_type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['member_id', 'account_type']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['member_id', 'status']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['role_id']);
        });
    }
};
