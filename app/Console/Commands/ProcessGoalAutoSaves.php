<?php

namespace App\Console\Commands;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessGoalAutoSaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goals:process-auto-saves 
                           {--frequency=monthly : The auto-save frequency to process (weekly|monthly)}
                           {--dry-run : Show what would be processed without actually executing}
                           {--user= : Process auto-saves for specific user ID only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic savings for financial goals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $frequency = $this->option('frequency');
        $dryRun = $this->option('dry-run');
        $userId = $this->option('user');

        $this->info("🚀 Starting auto-save processing for {$frequency} goals...");
        
        if ($dryRun) {
            $this->warn("⚠️  DRY RUN MODE - No actual transactions will be processed");
        }

        // Validate frequency
        if (!in_array($frequency, [Goal::FREQUENCY_WEEKLY, Goal::FREQUENCY_MONTHLY])) {
            $this->error("Invalid frequency. Must be 'weekly' or 'monthly'");
            return 1;
        }

        // Get goals that need processing
        $goalsQuery = Goal::needingAutoSave($frequency);
        
        if ($userId) {
            $goalsQuery = $goalsQuery->where('member_id', $userId);
        }

        $goals = $goalsQuery->get();

        if ($goals->isEmpty()) {
            $this->info("No goals found with {$frequency} auto-save enabled.");
            return 0;
        }

        $this->info("Found {$goals->count()} goals to process:");

        // Create progress bar
        $progressBar = $this->output->createProgressBar($goals->count());
        $progressBar->start();

        $stats = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'insufficient_funds' => 0,
            'total_amount' => 0,
        ];

        foreach ($goals as $goal) {
            $this->processGoalAutoSave($goal, $dryRun, $stats);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->displayResults($stats, $dryRun);

        // Send notifications for failed auto-saves
        if (!$dryRun && $stats['failed'] > 0) {
            $this->sendFailureNotifications();
        }

        return 0;
    }

    /**
     * Process auto-save for a single goal
     */
    private function processGoalAutoSave(Goal $goal, bool $dryRun, array &$stats): void
    {
        $stats['processed']++;
        
        try {
            DB::beginTransaction();

            $user = $goal->member;
            $amount = $goal->auto_save_amount;

            // Check user's available balance
            $savingsAccount = $user->accounts()->where('account_type', 'savings')->first();
            $availableBalance = $savingsAccount ? $savingsAccount->balance : 0;

            if ($availableBalance < $amount) {
                $stats['insufficient_funds']++;
                $this->logInsufficientFunds($goal, $availableBalance);
                DB::rollBack();
                return;
            }

            if ($dryRun) {
                $this->line("  Would process: {$user->name} - {$goal->title} - KES " . number_format($amount));
                $stats['successful']++;
                $stats['total_amount'] += $amount;
                DB::rollBack();
                return;
            }

            // Process the auto-save
            $success = $this->executeAutoSave($goal, $user, $amount);

            if ($success) {
                $stats['successful']++;
                $stats['total_amount'] += $amount;
                
                // Check for milestone achievements
                $this->checkMilestone($goal);
                
                Log::info("Auto-save successful", [
                    'goal_id' => $goal->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'new_progress' => $goal->fresh()->progress_percentage
                ]);
            } else {
                $stats['failed']++;
                Log::error("Auto-save failed", [
                    'goal_id' => $goal->id,
                    'user_id' => $user->id,
                    'amount' => $amount
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $stats['failed']++;
            
            Log::error("Auto-save error for goal {$goal->id}: " . $e->getMessage(), [
                'goal_id' => $goal->id,
                'user_id' => $goal->member_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Execute the actual auto-save transaction
     */
    private function executeAutoSave(Goal $goal, User $user, float $amount): bool
    {
        try {
            // Get user's savings account
            $savingsAccount = $user->accounts()->where('account_type', 'savings')->first();
            
            if (!$savingsAccount) {
                return false;
            }

            // Deduct from savings account
            $savingsAccount->balance -= $amount;
            $savingsAccount->save();

            // Add to goal
            $goal->addContribution($amount, 'auto_save');

            // Create transaction record (if you have a transactions table)
            // This would depend on your transaction system implementation
            $user->transactions()->create([
                'type' => 'transfer',
                'amount' => $amount,
                'description' => "Auto-save for goal: {$goal->title}",
                'metadata' => [
                    'goal_id' => $goal->id,
                    'goal_title' => $goal->title,
                    'auto_save' => true,
                    'processed_at' => now()->toISOString()
                ]
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to execute auto-save: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if goal reached a milestone and send congratulations
     */
    private function checkMilestone(Goal $goal): void
    {
        $progress = $goal->progress_percentage;
        $milestones = [25, 50, 75, 100];

        foreach ($milestones as $milestone) {
            if ($progress >= $milestone) {
                $metadata = $goal->metadata ?? [];
                $celebratedMilestones = $metadata['celebrated_milestones'] ?? [];

                if (!in_array($milestone, $celebratedMilestones)) {
                    // Mark milestone as celebrated
                    $celebratedMilestones[] = $milestone;
                    $metadata['celebrated_milestones'] = $celebratedMilestones;
                    $goal->metadata = $metadata;
                    $goal->save();

                    // Send milestone notification (implement based on your notification system)
                    $this->sendMilestoneNotification($goal, $milestone);
                    
                    $this->info("🎉 Milestone achieved: {$goal->title} reached {$milestone}%");
                }
            }
        }
    }

    /**
     * Send milestone achievement notification
     */
    private function sendMilestoneNotification(Goal $goal, int $milestone): void
    {
        $messages = [
            25 => "Great start! You've achieved 25% of your {$goal->title} goal! 🚀",
            50 => "Halfway there! You've reached 50% of your {$goal->title} goal! 💪",
            75 => "Almost there! You've achieved 75% of your {$goal->title} goal! 🎯",
            100 => "Congratulations! You've completed your {$goal->title} goal! 🎉🏆"
        ];

        $message = $messages[$milestone] ?? "Milestone achieved!";

        // Implement your notification system here
        // This could be email, SMS, in-app notification, etc.
        Log::info("Milestone notification", [
            'user_id' => $goal->member_id,
            'goal_id' => $goal->id,
            'milestone' => $milestone,
            'message' => $message
        ]);
    }

    /**
     * Log insufficient funds for auto-save
     */
    private function logInsufficientFunds(Goal $goal, float $availableBalance): void
    {
        Log::warning("Insufficient funds for auto-save", [
            'goal_id' => $goal->id,
            'user_id' => $goal->member_id,
            'required_amount' => $goal->auto_save_amount,
            'available_balance' => $availableBalance
        ]);

        // Store failed attempt in goal metadata
        $metadata = $goal->metadata ?? [];
        $metadata['failed_auto_saves'][] = [
            'date' => now()->toISOString(),
            'reason' => 'insufficient_funds',
            'required' => $goal->auto_save_amount,
            'available' => $availableBalance
        ];
        $goal->metadata = $metadata;
        $goal->save();
    }

    /**
     * Display processing results
     */
    private function displayResults(array $stats, bool $dryRun): void
    {
        $action = $dryRun ? 'Would process' : 'Processed';
        
        $this->table(
            ['Metric', 'Count', 'Amount'],
            [
                ['Goals Processed', $stats['processed'], '-'],
                ['Successful', $stats['successful'], 'KES ' . number_format($stats['total_amount'])],
                ['Failed', $stats['failed'], '-'],
                ['Insufficient Funds', $stats['insufficient_funds'], '-'],
            ]
        );

        if ($stats['successful'] > 0) {
            $this->info("✅ {$action} {$stats['successful']} auto-saves totaling KES " . number_format($stats['total_amount']));
        }

        if ($stats['failed'] > 0) {
            $this->error("❌ {$stats['failed']} auto-saves failed");
        }

        if ($stats['insufficient_funds'] > 0) {
            $this->warn("💰 {$stats['insufficient_funds']} auto-saves skipped due to insufficient funds");
        }
    }

    /**
     * Send notifications for failed auto-saves
     */
    private function sendFailureNotifications(): void
    {
        // Implement notification system for failed auto-saves
        // This could notify users about insufficient funds, system errors, etc.
        $this->info("📧 Failure notifications would be sent here");
    }
}
