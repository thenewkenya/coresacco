<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sacco:verify-user {email : Email of the user to verify} {--status=active : New membership status (active, inactive, suspended)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify a user by setting their membership status to active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $status = $this->option('status');

        if (!in_array($status, ['active', 'inactive', 'suspended'])) {
            $this->error('Invalid status. Must be one of: active, inactive, suspended');
            return Command::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        $oldStatus = $user->membership_status;
        $user->update(['membership_status' => $status]);

        $this->info("User '{$user->name}' ({$email}) membership status changed from '{$oldStatus}' to '{$status}'.");

        // Clear account cache for this user
        $accountLookupService = app(\App\Services\AccountLookupService::class);
        $accountLookupService->clearAccountCache(null, $user->id);

        return Command::SUCCESS;
    }
}