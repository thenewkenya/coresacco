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
        Schema::table('guarantors', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->constrained('users')->onDelete('set null')->after('id');
            $table->string('full_name', 255)->after('member_id');
            $table->string('id_number', 50)->unique()->after('full_name');
            $table->string('phone_number', 20)->after('id_number');
            $table->text('address')->after('phone_number');
            $table->enum('employment_status', ['employed', 'self_employed', 'unemployed', 'retired'])->default('employed')->after('address');
            $table->decimal('monthly_income', 15, 2)->default(0)->after('employment_status');
            $table->string('relationship_to_borrower', 100)->after('monthly_income');
            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted'])->default('active')->after('relationship_to_borrower');
            $table->decimal('max_guarantee_amount', 15, 2)->default(0)->after('status');
            $table->decimal('current_guarantee_obligations', 15, 2)->default(0)->after('max_guarantee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guarantors', function (Blueprint $table) {
            $table->dropColumn([
                'id_number',
                'phone_number',
                'address',
                'employment_status',
                'monthly_income',
                'relationship_to_borrower',
                'status',
                'max_guarantee_amount',
                'current_guarantee_obligations'
            ]);
        });
    }
};
