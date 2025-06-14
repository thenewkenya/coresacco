<?php

namespace Database\Seeders;

use App\Models\Branch;
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
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '08:00-13:00',
                    'sunday' => 'closed',
                ],
                'coordinates' => [
                    'latitude' => -1.2864,
                    'longitude' => 36.8172,
                ],
            ],
            [
                'name' => 'Mombasa Branch',
                'code' => 'MSA-002',
                'address' => 'Digo Road',
                'city' => 'Mombasa',
                'phone' => '+254 41 2234 567',
                'email' => 'mombasa@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2020-03-10',
                'working_hours' => [
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '08:00-13:00',
                    'sunday' => 'closed',
                ],
                'coordinates' => [
                    'latitude' => -4.0435,
                    'longitude' => 39.6682,
                ],
            ],
            [
                'name' => 'Kisumu Branch',
                'code' => 'KSM-003',
                'address' => 'Oginga Odinga Street',
                'city' => 'Kisumu',
                'phone' => '+254 57 2123 456',
                'email' => 'kisumu@saccocore.co.ke',
                'status' => 'active',
                'opening_date' => '2020-06-20',
                'working_hours' => [
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '08:00-13:00',
                    'sunday' => 'closed',
                ],
                'coordinates' => [
                    'latitude' => -0.0917,
                    'longitude' => 34.7680,
                ],
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                $branch
            );
        }
    }
}
