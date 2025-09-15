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
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the existing columns that don't match our model
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
            
            // Add the columns our Notification model expects
            $table->unsignedBigInteger('user_id')->after('type');
            $table->string('title')->after('user_id');
            $table->text('message')->after('title');
            // Handle data column conversion for PostgreSQL
            $table->text('data')->nullable()->change();
            $table->string('action_url')->nullable()->after('data');
            $table->string('action_text')->nullable()->after('action_url');
            $table->boolean('is_read')->default(false)->after('action_text');
            $table->timestamp('expires_at')->nullable()->after('read_at');
            $table->string('priority')->default('normal')->after('expires_at');
            $table->string('category')->nullable()->after('priority');
            
            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['user_id']);
            
            // Drop the columns we added
            $table->dropColumn([
                'user_id', 'title', 'message', 'action_url', 'action_text', 
                'is_read', 'expires_at', 'priority', 'category'
            ]);
            
            // Restore the original columns
            $table->morphs('notifiable');
        });
    }
};