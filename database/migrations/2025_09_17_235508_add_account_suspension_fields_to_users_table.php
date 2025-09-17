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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false);
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamp('scheduled_for_deletion')->nullable();
            $table->timestamp('last_login_at')->nullable();
            // Note: deleted_at already exists, so we don't add softDeletes() again
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_suspended',
                'suspended_at', 
                'suspension_reason',
                'scheduled_for_deletion',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};