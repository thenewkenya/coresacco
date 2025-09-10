<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sacco:setup-roles {--admin-email= : Email for admin user} {--admin-password= : Password for admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up SACCO roles and create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up SACCO roles...');

        // Create roles
        $this->call('db:seed', ['--class' => 'RoleSeeder']);

        $this->info('Roles created successfully!');

        // Create admin user if requested
        $adminEmail = $this->option('admin-email') ?? $this->ask('Enter admin email (optional, press Enter to skip)');
        
        if ($adminEmail) {
            $adminPassword = $this->option('admin-password') ?? $this->secret('Enter admin password');
            
            if (!$adminPassword) {
                $this->error('Password is required for admin user creation.');
                return Command::FAILURE;
            }

            $adminUser = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'System Administrator',
                    'password' => Hash::make($adminPassword),
                    'email_verified_at' => now(),
                    'membership_status' => 'active',
                    'role' => 'admin',
                ]
            );

            $adminRole = Role::where('slug', 'admin')->first();
            if ($adminRole && !$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
                $adminUser->roles()->attach($adminRole);
                $this->info("Admin user created: {$adminEmail}");
            } else {
                $this->info("Admin user already exists: {$adminEmail}");
            }
        }

        $this->info('');
        $this->info('✅ SACCO role system setup complete!');
        $this->info('');
        $this->table(
            ['Role', 'Description', 'Key Permissions'],
            [
                ['Admin', 'System administrator', 'All permissions'],
                ['Manager', 'Branch manager', 'Most operations + branch management'],
                ['Staff', 'SACCO staff', 'Member, account, and loan operations'],
                ['Member', 'SACCO member', 'View own accounts and loans'],
            ]
        );

        return Command::SUCCESS;
    }
} 