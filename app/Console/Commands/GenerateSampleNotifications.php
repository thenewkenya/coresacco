<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Loan;
use App\Services\NotificationService;
use App\Notifications\TransactionNotification;
use App\Notifications\LoanApplicationNotification;
use App\Notifications\LargeDepositNotification;

class GenerateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-samples {--count=5 : Number of notifications to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample notifications for testing the notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $notificationService = new NotificationService();
        
        $this->info("Generating {$count} sample notifications...");
        
        // Get a user to receive notifications
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please create a user first.');
            return;
        }
        
        for ($i = 0; $i < $count; $i++) {
            $this->generateRandomNotification($user, $notificationService);
        }
        
        $this->info("Generated {$count} sample notifications successfully!");
        $this->line("You can view them in the notifications dropdown or at /notifications");
    }
    
    private function generateRandomNotification(User $user, NotificationService $notificationService)
    {
        $types = ['transaction', 'loan', 'large_deposit', 'system'];
        $type = $types[array_rand($types)];
        
        switch ($type) {
            case 'transaction':
                $this->generateTransactionNotification($user);
                break;
                
            case 'loan':
                $this->generateLoanNotification($user);
                break;
                
            case 'large_deposit':
                $this->generateLargeDepositNotification($user);
                break;
                
            case 'system':
                $this->generateSystemNotification($user, $notificationService);
                break;
        }
    }
    
    private function generateTransactionNotification(User $user)
    {
        $transactionTypes = ['deposit', 'withdrawal', 'transfer'];
        $actionTypes = ['created', 'approved', 'failed', 'completed'];
        
        $transactionData = [
            'id' => rand(1000, 9999),
            'type' => $transactionTypes[array_rand($transactionTypes)],
            'amount' => rand(1000, 100000),
            'status' => 'pending',
            'member_id' => $user->id,
            'created_at' => now()->subMinutes(rand(1, 1440)),
        ];
        
        // Create a mock transaction object
        $transaction = new Transaction($transactionData);
        $transaction->id = $transactionData['id'];
        $transaction->member = $user;
        
        $actionType = $actionTypes[array_rand($actionTypes)];
        $notification = new TransactionNotification($transaction, $actionType);
        
        $user->notify($notification);
    }
    
    private function generateLoanNotification(User $user)
    {
        $loanTypes = ['application', 'approved', 'rejected', 'disbursed'];
        
        $loanData = [
            'id' => rand(1000, 9999),
            'amount' => rand(50000, 500000),
            'status' => 'pending',
            'member_id' => $user->id,
            'loan_type_id' => 1,
            'created_at' => now()->subMinutes(rand(1, 1440)),
        ];
        
        // Create a mock loan object
        $loan = new Loan($loanData);
        $loan->id = $loanData['id'];
        $loan->member = $user;
        
        // Mock loan type
        $loan->loanType = (object) ['name' => 'Emergency Loan'];
        
        $actionType = $loanTypes[array_rand($loanTypes)];
        $notification = new LoanApplicationNotification($loan, $actionType);
        
        $user->notify($notification);
    }
    
    private function generateLargeDepositNotification(User $user)
    {
        $transactionData = [
            'id' => rand(1000, 9999),
            'type' => 'deposit',
            'amount' => rand(75000, 200000), // Large amounts
            'status' => 'pending',
            'member_id' => $user->id,
            'created_at' => now()->subMinutes(rand(1, 60)), // Recent
        ];
        
        // Create a mock transaction object
        $transaction = new Transaction($transactionData);
        $transaction->id = $transactionData['id'];
        $transaction->member = $user;
        
        $notification = new LargeDepositNotification($transaction);
        
        $user->notify($notification);
    }
    
    private function generateSystemNotification(User $user, NotificationService $notificationService)
    {
        $systemMessages = [
            [
                'title' => 'System Maintenance Scheduled',
                'message' => 'Scheduled maintenance on Sunday 2:00 AM - 4:00 AM',
                'icon' => 'wrench-screwdriver',
                'color' => 'amber',
                'priority' => 'medium'
            ],
            [
                'title' => 'New Feature Available',
                'message' => 'Mobile app now supports biometric authentication',
                'icon' => 'sparkles',
                'color' => 'blue',
                'priority' => 'low'
            ],
            [
                'title' => 'Security Alert',
                'message' => 'New login detected from unusual location',
                'icon' => 'shield-exclamation',
                'color' => 'red',
                'priority' => 'high'
            ],
            [
                'title' => 'Interest Rate Update',
                'message' => 'Savings interest rate increased to 8.5% annually',
                'icon' => 'chart-bar',
                'color' => 'green',
                'priority' => 'medium'
            ]
        ];
        
        $messageData = $systemMessages[array_rand($systemMessages)];
        
        $notificationService->sendSystemNotification(
            [$user->id],
            $messageData['title'],
            $messageData['message'],
            [
                'icon' => $messageData['icon'],
                'color' => $messageData['color'],
                'priority' => $messageData['priority'],
                'url' => '#'
            ]
        );
    }
}
