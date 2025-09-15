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
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedBigInteger('disbursed_by')->nullable();
            $table->text('disbursement_notes')->nullable();
            
            // Add foreign key constraint
            $table->foreign('disbursed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['disbursed_by']);
            $table->dropColumn(['disbursed_by', 'disbursement_notes']);
        });
    }
};
