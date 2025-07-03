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
        // Insert mobile money settings
        $mobileMoneySettings = [
            // M-Pesa Settings
            [
                'key' => 'mpesa_consumer_key',
                'value' => '',
                'type' => 'string',
                'description' => 'M-Pesa Consumer Key from Safaricom Developer Portal',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_consumer_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'M-Pesa Consumer Secret from Safaricom Developer Portal',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_base_url',
                'value' => 'https://sandbox.safaricom.co.ke',
                'type' => 'string',
                'description' => 'M-Pesa API Base URL (sandbox or production)',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_shortcode',
                'value' => '',
                'type' => 'string',
                'description' => 'M-Pesa Business Shortcode',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_passkey',
                'value' => '',
                'type' => 'string',
                'description' => 'M-Pesa Online Passkey',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mpesa_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable/Disable M-Pesa payments',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Airtel Money Settings
            [
                'key' => 'airtel_client_id',
                'value' => '',
                'type' => 'string',
                'description' => 'Airtel Money Client ID',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'airtel_client_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'Airtel Money Client Secret',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'airtel_base_url',
                'value' => 'https://openapi.airtel.africa',
                'type' => 'string',
                'description' => 'Airtel Money API Base URL',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'airtel_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable/Disable Airtel Money payments',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // T-Kash Settings
            [
                'key' => 'tkash_merchant_code',
                'value' => '',
                'type' => 'string',
                'description' => 'T-Kash Merchant Code',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tkash_api_key',
                'value' => '',
                'type' => 'string',
                'description' => 'T-Kash API Key',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tkash_base_url',
                'value' => '',
                'type' => 'string',
                'description' => 'T-Kash API Base URL',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tkash_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable/Disable T-Kash payments',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // General Mobile Money Settings
            [
                'key' => 'mobile_money_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable/Disable Mobile Money payments globally',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mobile_money_min_amount',
                'value' => '10',
                'type' => 'number',
                'description' => 'Minimum amount for mobile money deposits',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mobile_money_max_amount',
                'value' => '500000',
                'type' => 'number',
                'description' => 'Maximum amount for mobile money deposits',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mobile_money_transaction_fee',
                'value' => '0',
                'type' => 'number',
                'description' => 'Transaction fee for mobile money deposits (amount)',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mobile_money_transaction_fee_percentage',
                'value' => '0',
                'type' => 'number',
                'description' => 'Transaction fee for mobile money deposits (percentage)',
                'group' => 'mobile_money',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('settings')->insert($mobileMoneySettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('group', 'mobile_money')->delete();
    }
};
