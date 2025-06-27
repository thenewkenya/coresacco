<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Setting;

beforeEach(function () {
    $this->artisan('migrate:fresh');
    
    // Create roles
    Role::create([
        'name' => 'admin', 
        'slug' => 'admin',
        'description' => 'Administrator',
        'permissions' => ['manage-settings', 'manage-roles']
    ]);
    Role::create([
        'name' => 'member', 
        'slug' => 'member',
        'description' => 'Member',
        'permissions' => []
    ]);
});

test('admin can view system settings', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    $this->actingAs($admin)
        ->get(route('system.settings'))
        ->assertOk()
        ->assertSee('System Settings')
        ->assertSee('General Settings');
});

test('non-admin cannot view system settings', function () {
    $memberRole = Role::where('name', 'member')->first();
    $user = User::factory()->create();
    $user->roles()->attach($memberRole);
    
    $this->actingAs($user)
        ->get(route('system.settings'))
        ->assertForbidden();
});

test('admin can update system settings', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    $this->actingAs($admin)
        ->post(route('system.settings.update'), [
            'active_tab' => 'general',
            'general' => [
                'organization_name' => 'Test SACCO',
                'registration_number' => 'SACCO-TEST-2024',
                'contact_email' => 'test@sacco.com',
                'default_currency' => 'USD',
                'timezone' => 'Africa/Nairobi'
            ],
            'financial' => [
                'savings_interest_rate' => 10.5,
                'loan_interest_rate' => 15.0
            ],
            'features' => [
                'enable_sms_notifications' => 'on',
                'enable_email_notifications' => 'on'
            ]
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    expect(setting('organization_name'))->toBe('Test SACCO');
    expect(setting('contact_email'))->toBe('test@sacco.com');
    expect(setting('default_currency'))->toBe('USD');
    expect(setting('registration_number'))->toBe('SACCO-TEST-2024');
    expect(setting('timezone'))->toBe('Africa/Nairobi');
    expect(setting('savings_interest_rate'))->toBe(10.5);
    expect(setting('enable_sms_notifications'))->toBe(true);
});

test('setting helper functions work correctly', function () {
    Setting::set('test_string', 'Hello World', 'string');
    Setting::set('test_number', '42', 'integer');
    Setting::set('test_float', '3.14', 'float');
    Setting::set('test_boolean', 'true', 'boolean');
    
    expect(setting('test_string'))->toBe('Hello World');
    expect(setting('test_number'))->toBe(42);
    expect(setting('test_float'))->toBe(3.14);
    expect(setting('test_boolean'))->toBe(true);
    expect(setting('non_existent', 'default'))->toBe('default');
});

test('currency formatting works correctly', function () {
    Setting::set('default_currency', 'KES');
    expect(format_currency(1500.75))->toBe('KSh 1,500.75');
    
    Setting::set('default_currency', 'USD');
    expect(format_currency(1500.75))->toBe('$1,500.75');
    
    Setting::set('default_currency', 'EUR');
    expect(format_currency(1500.75))->toBe('€1,500.75');
});

test('feature flag helpers work correctly', function () {
    Setting::set('test_feature', 'true', 'boolean');
    expect(is_feature_enabled('test_feature'))->toBe(true);
    
    Setting::set('test_feature', 'false', 'boolean');
    expect(is_feature_enabled('test_feature'))->toBe(false);
    
    expect(is_feature_enabled('non_existent_feature'))->toBe(false);
});

test('admin can export settings', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    $response = $this->actingAs($admin)
        ->get(route('system.settings.export'));
    
    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertHeader('Content-Disposition');
});

test('admin can reset settings', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    // Create a custom setting
    Setting::set('test_setting', 'custom_value');
    expect(setting('test_setting'))->toBe('custom_value');
    
    // Reset all settings
    $this->actingAs($admin)
        ->post(route('system.settings.reset'), ['group' => 'all'])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    // Setting should be gone
    expect(setting('test_setting'))->toBeNull();
});

test('settings are cached correctly', function () {
    // Set a setting
    Setting::set('cached_setting', 'test_value');
    
    // First call should hit database and cache
    $value1 = setting('cached_setting');
    expect($value1)->toBe('test_value');
    
    // Update setting directly in database to bypass model
    \DB::table('settings')->where('key', 'cached_setting')->update(['value' => 'updated_value']);
    
    // Should still return cached value
    $value2 = setting('cached_setting');
    expect($value2)->toBe('test_value');
    
    // Clear cache and check again
    Setting::clearCache();
    $value3 = setting('cached_setting');
    expect($value3)->toBe('updated_value');
});

test('organization name helper works', function () {
    Setting::set('organization_name', 'My SACCO');
    expect(organization_name())->toBe('My SACCO');
    
    // Test default when setting doesn't exist
    Setting::where('key', 'organization_name')->delete();
    Setting::clearCache();
    expect(organization_name())->toBe('Kenya SACCO Limited');
});
