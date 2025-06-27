<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Nairobi Main Branch',
                'code' => 'NRB-001',
                'address' => 'Kenyatta Avenue, CBD',
                'city' => 'Nairobi',
                'phone' => '+254 20 2345 678',
                'email' => 'nairobi@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2020-01-15',
                'working_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '13:00'],
                    'sunday' => ['open' => '09:00', 'close' => '12:00'],
                ],
                'coordinates' => [
                    'latitude' => -1.2864,
                    'longitude' => 36.8172,
                ],
            ],
            [
                'name' => 'Mombasa Branch',
                'code' => 'MSA-002',
                'address' => 'Digo Road, Mombasa',
                'city' => 'Mombasa',
                'phone' => '+254 41 2234 567',
                'email' => 'mombasa@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2020-03-10',
                'working_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '13:00'],
                    'sunday' => ['open' => '09:00', 'close' => '12:00'],
                ],
                'coordinates' => [
                    'latitude' => -4.0435,
                    'longitude' => 39.6682,
                ],
            ],
            [
                'name' => 'Kisumu Branch',
                'code' => 'KSM-003',
                'address' => 'Oginga Odinga Street, Kisumu',
                'city' => 'Kisumu',
                'phone' => '+254 57 2123 456',
                'email' => 'kisumu@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2020-06-20',
                'working_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '13:00'],
                    'sunday' => ['open' => '09:00', 'close' => '12:00'],
                ],
                'coordinates' => [
                    'latitude' => -0.0917,
                    'longitude' => 34.7680,
                ],
            ],
            [
                'name' => 'Nakuru Branch',
                'code' => 'NKR-004',
                'address' => 'Kenyatta Avenue, Nakuru',
                'city' => 'Nakuru',
                'phone' => '+254 51 2234 567',
                'email' => 'nakuru@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2021-02-15',
                'working_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '13:00'],
                    'sunday' => ['open' => '09:00', 'close' => '12:00'],
                ],
                'coordinates' => [
                    'latitude' => -0.3031,
                    'longitude' => 36.0800,
                ],
            ],
            [
                'name' => 'Eldoret Branch',
                'code' => 'ELD-005',
                'address' => 'Uganda Road, Eldoret',
                'city' => 'Eldoret',
                'phone' => '+254 53 2234 567',
                'email' => 'eldoret@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2021-08-01',
                'working_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '13:00'],
                    'sunday' => ['open' => '09:00', 'close' => '12:00'],
                ],
                'coordinates' => [
                    'latitude' => 0.5143,
                    'longitude' => 35.2697,
                ],
            ],
        ];

        foreach ($branches as $branchData) {
            $branch = Branch::updateOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );

            // Try to assign managers to branches if available
            if (!$branch->manager_id) {
                $availableManager = User::where('role', 'manager')
                    ->whereDoesntHave('managedBranch')
                    ->first();
                
                if ($availableManager) {
                    $branch->update(['manager_id' => $availableManager->id]);
                }
            }
        }
    }
}
