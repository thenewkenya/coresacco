<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get branches for assignment
        $branches = Branch::all();
        $memberRole = Role::where('slug', 'member')->first();
        $staffRole = Role::where('slug', 'staff')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        // Create admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@saccocore.co.ke'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'member_number' => 'ADM001',
                'membership_status' => 'active',
                'joining_date' => now()->subMonths(48),
                'role' => 'admin',
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);

        // Create manager users
        $managers = [
            ['name' => 'Sarah Wanjiku', 'email' => 'sarah.wanjiku@saccocore.co.ke', 'branch' => 'NRB-001'],
            ['name' => 'John Mukama', 'email' => 'john.mukama@saccocore.co.ke', 'branch' => 'MSA-002'],
            ['name' => 'Mary Atieno', 'email' => 'mary.atieno@saccocore.co.ke', 'branch' => 'KSM-003'],
        ];

        foreach ($managers as $managerData) {
            $branch = Branch::where('code', $managerData['branch'])->first();
            $user = User::updateOrCreate(
                ['email' => $managerData['email']],
                [
                    'name' => $managerData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'member_number' => 'MGR' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'branch_id' => $branch->id,
                    'membership_status' => 'active',
                    'joining_date' => now()->subMonths(rand(12, 48)),
                    'role' => 'manager',
                ]
            );
            $user->roles()->sync([$managerRole->id]);
        }

        // Create staff users
        $staffMembers = [
            ['name' => 'David Mwangi', 'email' => 'david.mwangi@saccocore.co.ke', 'branch' => 'NRB-001'],
            ['name' => 'Grace Muthoni', 'email' => 'grace.muthoni@saccocore.co.ke', 'branch' => 'NRB-001'],
            ['name' => 'Peter Ochieng', 'email' => 'peter.ochieng@saccocore.co.ke', 'branch' => 'MSA-002'],
            ['name' => 'Lucy Nyambura', 'email' => 'lucy.nyambura@saccocore.co.ke', 'branch' => 'KSM-003'],
        ];

        foreach ($staffMembers as $staffData) {
            $branch = Branch::where('code', $staffData['branch'])->first();
            $user = User::updateOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'member_number' => 'STF' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'branch_id' => $branch->id,
                    'membership_status' => 'active',
                    'joining_date' => now()->subMonths(rand(6, 24)),
                    'role' => 'staff',
                ]
            );
            $user->roles()->sync([$staffRole->id]);
        }

        // Create regular members
        $members = [
            ['name' => 'James Kamau', 'email' => 'james.kamau@example.com'],
            ['name' => 'Anne Wanjiru', 'email' => 'anne.wanjiru@example.com'],
            ['name' => 'Robert Kipchoge', 'email' => 'robert.kipchoge@example.com'],
            ['name' => 'Faith Akinyi', 'email' => 'faith.akinyi@example.com'],
            ['name' => 'Samuel Mutua', 'email' => 'samuel.mutua@example.com'],
            ['name' => 'Joyce Wambui', 'email' => 'joyce.wambui@example.com'],
            ['name' => 'Paul Otieno', 'email' => 'paul.otieno@example.com'],
            ['name' => 'Catherine Njeri', 'email' => 'catherine.njeri@example.com'],
            ['name' => 'Michael Kinyua', 'email' => 'michael.kinyua@example.com'],
            ['name' => 'Elizabeth Awino', 'email' => 'elizabeth.awino@example.com'],
            ['name' => 'Daniel Kiprotich', 'email' => 'daniel.kiprotich@example.com'],
            ['name' => 'Margaret Wanjiku', 'email' => 'margaret.wanjiku@example.com'],
            ['name' => 'Francis Mwenda', 'email' => 'francis.mwenda@example.com'],
            ['name' => 'Esther Moraa', 'email' => 'esther.moraa@example.com'],
            ['name' => 'Anthony Juma', 'email' => 'anthony.juma@example.com'],
        ];

        foreach ($members as $memberData) {
            $branch = $branches->random();
            $user = User::updateOrCreate(
                ['email' => $memberData['email']],
                [
                    'name' => $memberData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'member_number' => 'MB' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                    'branch_id' => $branch->id,
                    'membership_status' => rand(1, 10) > 8 ? 'inactive' : 'active', // 80% active, 20% inactive
                    'joining_date' => now()->subMonths(rand(1, 60)),
                    'role' => 'member',
                ]
            );
            $user->roles()->sync([$memberRole->id]);
        }
    }
}
