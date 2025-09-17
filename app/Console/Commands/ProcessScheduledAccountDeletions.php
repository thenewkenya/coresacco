<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledAccountDeletions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:process-deletions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled account deletions for users who have been inactive for 3 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing scheduled account deletions...');
        
        // Find users scheduled for deletion who are past their deletion date
        $usersToDelete = User::where('is_suspended', true)
            ->whereNotNull('scheduled_for_deletion')
            ->where('scheduled_for_deletion', '<=', now())
            ->get();
        
        $deletedCount = 0;
        
        foreach ($usersToDelete as $user) {
            try {
                // Double-check that user can be deleted (no active accounts or debts)
                if ($user->canBeDeleted()) {
                    // Log the deletion
                    Log::info('Deleting user account', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'member_number' => $user->member_number,
                        'scheduled_for_deletion' => $user->scheduled_for_deletion,
                        'suspension_reason' => $user->suspension_reason,
                    ]);
                    
                    // Permanently delete the user (force delete to bypass soft deletes)
                    $user->forceDelete();
                    
                    $deletedCount++;
                    $this->line("Deleted user: {$user->email} ({$user->member_number})");
                } else {
                    $this->warn("Skipping user {$user->email} - has active accounts or debts");
                    
                    // Cancel the scheduled deletion since they can't be deleted
                    $user->cancelScheduledDeletion();
                }
            } catch (\Exception $e) {
                $this->error("Failed to delete user {$user->email}: " . $e->getMessage());
                Log::error('Failed to delete user account', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info("Processed {$deletedCount} account deletions.");
        
        // Also check for users who should be scheduled for deletion but aren't
        $usersToSchedule = User::where('is_suspended', true)
            ->whereNull('scheduled_for_deletion')
            ->get();
        
        $scheduledCount = 0;
        foreach ($usersToSchedule as $user) {
            if ($user->canBeDeleted()) {
                $user->scheduleForDeletion();
                $scheduledCount++;
                $this->line("Scheduled user for deletion: {$user->email}");
            }
        }
        
        if ($scheduledCount > 0) {
            $this->info("Scheduled {$scheduledCount} additional users for deletion.");
        }
        
        return Command::SUCCESS;
    }
}