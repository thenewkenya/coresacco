<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMemberWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    public function __construct(
        public User $member
    ) {}

    public function handle(): void
    {
        Log::info("Processing welcome email for member: {$this->member->name} ({$this->member->email})");
        
        // Simulate email processing
        sleep(1);
        
        Log::info("Welcome email sent successfully to {$this->member->name}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send welcome email to {$this->member->name}: " . $exception->getMessage());
    }
} 