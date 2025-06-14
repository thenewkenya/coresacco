<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // First seed roles and branches (no dependencies)
            RoleSeeder::class,
            BranchSeeder::class,
            LoanTypeSeeder::class,
            
            // Then seed members (depends on roles and branches)
            MemberSeeder::class,
            
            // Then seed accounts (depends on members)
            AccountSeeder::class,
            
            // Then seed loans (depends on members and loan types)
            LoanSeeder::class,
            
            // Finally seed transactions (depends on accounts and loans)
            TransactionSeeder::class,
        ]);
    }
}
