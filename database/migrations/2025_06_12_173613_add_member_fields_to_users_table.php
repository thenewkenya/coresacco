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
            $table->string('member_number')->nullable()->unique();
            $table->string('id_number')->nullable()->unique();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('membership_status', ['active', 'inactive', 'suspended'])->nullable();
            $table->date('joining_date')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->enum('role', ['member', 'staff', 'manager', 'admin'])->default('member');
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn([
                'member_number',
                'id_number',
                'phone_number',
                'address',
                'membership_status',
                'joining_date',
                'branch_id',
                'role',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
