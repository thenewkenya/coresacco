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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->string('group')->default('general'); // general, financial, security, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // whether setting can be accessed publicly
            $table->timestamps();
        });

        // Add default settings
        $settings = [
            [
                'key' => 'organization_name',
                'value' => 'Kenya SACCO Limited',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Organization Name',
                'description' => 'The name of your SACCO organization',
                'is_public' => true
            ],
            [
                'key' => 'registration_number',
                'value' => 'SACCO-001-2020',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Registration Number',
                'description' => 'Official registration number',
                'is_public' => true
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@saccocore.co.ke',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Nairobi',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'System timezone',
                'is_public' => false
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
