<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeMembers = User::where('membership_status', 'active')
            ->whereNotNull('member_number')
            ->get();

        // 70% of active members should have at least one goal
        $membersWithGoals = $activeMembers->random(intval($activeMembers->count() * 0.7));

        foreach ($membersWithGoals as $member) {
            $numGoals = rand(1, 3); // Each member can have 1-3 goals

            for ($i = 0; $i < $numGoals; $i++) {
                $goalData = $this->generateGoalData();
                
                Goal::create([
                    'member_id' => $member->id,
                    'title' => $goalData['title'],
                    'description' => $goalData['description'],
                    'target_amount' => $goalData['target_amount'],
                    'current_amount' => $goalData['current_amount'],
                    'target_date' => $goalData['target_date'],
                    'type' => $goalData['type'],
                    'status' => $goalData['status'],
                    'auto_save_amount' => $goalData['auto_save_amount'],
                    'auto_save_frequency' => $goalData['auto_save_frequency'],
                    'metadata' => $goalData['metadata'],
                ]);
            }
        }
    }

    /**
     * Generate realistic goal data
     */
    private function generateGoalData(): array
    {
        $goalTypes = [
            [
                'type' => Goal::TYPE_EMERGENCY_FUND,
                'title' => 'Emergency Fund',
                'description' => 'Building an emergency fund for unexpected expenses',
                'target_amount_range' => [50000, 200000],
                'current_percentage_range' => [0.1, 0.8],
            ],
            [
                'type' => Goal::TYPE_HOME_PURCHASE,
                'title' => 'Home Purchase Fund',
                'description' => 'Saving for a down payment on a house',
                'target_amount_range' => [500000, 2000000],
                'current_percentage_range' => [0.05, 0.6],
            ],
            [
                'type' => Goal::TYPE_EDUCATION,
                'title' => 'Education Fund',
                'description' => 'Saving for children\'s education or personal development',
                'target_amount_range' => [100000, 800000],
                'current_percentage_range' => [0.1, 0.7],
            ],
            [
                'type' => Goal::TYPE_RETIREMENT,
                'title' => 'Retirement Savings',
                'description' => 'Building a retirement nest egg',
                'target_amount_range' => [1000000, 5000000],
                'current_percentage_range' => [0.05, 0.4],
            ],
            [
                'type' => Goal::TYPE_CUSTOM,
                'title' => 'Vehicle Purchase',
                'description' => 'Saving for a new car or motorcycle',
                'target_amount_range' => [200000, 800000],
                'current_percentage_range' => [0.1, 0.9],
            ],
            [
                'type' => Goal::TYPE_CUSTOM,
                'title' => 'Wedding Fund',
                'description' => 'Saving for wedding expenses',
                'target_amount_range' => [300000, 1500000],
                'current_percentage_range' => [0.1, 0.8],
            ],
            [
                'type' => Goal::TYPE_CUSTOM,
                'title' => 'Business Investment',
                'description' => 'Capital for starting or expanding a business',
                'target_amount_range' => [400000, 2000000],
                'current_percentage_range' => [0.05, 0.7],
            ],
            [
                'type' => Goal::TYPE_CUSTOM,
                'title' => 'Vacation Fund',
                'description' => 'Saving for a dream vacation',
                'target_amount_range' => [100000, 500000],
                'current_percentage_range' => [0.2, 0.9],
            ],
        ];

        $selectedGoal = $goalTypes[array_rand($goalTypes)];
        
        $targetAmount = rand($selectedGoal['target_amount_range'][0], $selectedGoal['target_amount_range'][1]);
        $currentPercentage = rand($selectedGoal['current_percentage_range'][0] * 100, $selectedGoal['current_percentage_range'][1] * 100) / 100;
        $currentAmount = $targetAmount * $currentPercentage;
        
        // Determine status based on progress
        $status = Goal::STATUS_ACTIVE;
        if ($currentAmount >= $targetAmount) {
            $status = Goal::STATUS_COMPLETED;
        } elseif ($currentPercentage < 0.1) {
            $status = rand(0, 1) ? Goal::STATUS_ACTIVE : Goal::STATUS_PAUSED;
        }

        // Set target date (1-24 months from now)
        $targetDate = now()->addMonths(rand(1, 24));

        // Auto-save settings
        $autoSaveAmount = rand(5000, 25000);
        $autoSaveFrequency = rand(0, 1) ? Goal::FREQUENCY_WEEKLY : Goal::FREQUENCY_MONTHLY;

        return [
            'title' => $selectedGoal['title'],
            'description' => $selectedGoal['description'],
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'target_date' => $targetDate,
            'type' => $selectedGoal['type'],
            'status' => $status,
            'auto_save_amount' => $autoSaveAmount,
            'auto_save_frequency' => $autoSaveFrequency,
            'metadata' => [
                'created_via' => 'seeder',
                'priority' => rand(1, 5),
                'notes' => $this->getRandomGoalNotes($status),
            ],
        ];
    }

    /**
     * Get random goal notes based on status
     */
    private function getRandomGoalNotes(string $status): string
    {
        $notes = [
            Goal::STATUS_ACTIVE => [
                'Making steady progress towards goal',
                'Regular contributions on track',
                'Good momentum building',
                'Consistent savings habit',
                'On track to meet target',
            ],
            Goal::STATUS_COMPLETED => [
                'Goal successfully achieved!',
                'Target reached ahead of schedule',
                'Excellent savings discipline',
                'Goal completed as planned',
                'Mission accomplished!',
            ],
            Goal::STATUS_PAUSED => [
                'Temporarily paused due to other priorities',
                'Taking a break to reassess',
                'Paused for financial review',
                'On hold pending other commitments',
                'Temporary suspension of contributions',
            ],
        ];

        $statusNotes = $notes[$status] ?? ['Goal in progress'];
        return $statusNotes[array_rand($statusNotes)];
    }
}
