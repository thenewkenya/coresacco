<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Loan;
use App\Notifications\TransactionNotification;
use App\Notifications\LoanApplicationNotification;
use App\Notifications\LargeDepositNotification;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send transaction notification
     */
    public function sendTransactionNotification(Transaction $transaction, string $type = 'created')
    {
        $notification = new TransactionNotification($transaction, $type);
        
        // Send to member
        if ($transaction->member) {
            $transaction->member->notify($notification);
        }
        
        // Send to staff/managers/admins for certain types
        if (in_array($type, ['created', 'failed']) || $transaction->amount >= 10000) {
            $staffUsers = User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            
            foreach ($staffUsers as $user) {
                $user->notify($notification);
            }
        }
    }
    
    /**
     * Send large deposit notification (for compliance)
     */
    public function sendLargeDepositNotification(Transaction $transaction, float $threshold = 50000)
    {
        if ($transaction->type === 'deposit' && $transaction->amount >= $threshold) {
            $notification = new LargeDepositNotification($transaction, $threshold);
            
            // Send to managers and admins only
            $managers = User::whereIn('role', ['manager', 'admin'])->get();
            
            foreach ($managers as $manager) {
                $manager->notify($notification);
            }
        }
    }
    
    /**
     * Send loan application notification
     */
    public function sendLoanNotification(Loan $loan, string $type = 'application')
    {
        $notification = new LoanApplicationNotification($loan, $type);
        
        // Send to member
        if ($loan->member) {
            $loan->member->notify($notification);
        }
        
        // Send to loan officers and managers for applications
        if ($type === 'application') {
            $loanOfficers = User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            
            foreach ($loanOfficers as $officer) {
                $officer->notify($notification);
            }
        }
    }
    
    /**
     * Send system notification to specific users
     */
    public function sendSystemNotification(array $userIds, string $title, string $message, array $data = [])
    {
        $users = User::whereIn('id', $userIds)->get();
        $notification = new SystemNotification($title, $message, $data);
        
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }
    
    /**
     * Send notification to all users with specific roles
     */
    public function sendRoleBasedNotification(array $roles, string $title, string $message, array $data = [])
    {
        $users = User::whereIn('role', $roles)->get();
        $userIds = $users->pluck('id')->toArray();
        
        $this->sendSystemNotification($userIds, $title, $message, $data);
    }
    
    /**
     * Send bulk transaction notifications
     */
    public function sendBulkTransactionNotifications(array $transactions, string $type = 'bulk_processed')
    {
        $title = 'Bulk Transaction Processed';
        $message = count($transactions) . ' transactions have been processed';
        $data = [
            'type' => 'bulk_transaction',
            'icon' => 'document-check',
            'color' => 'green',
            'priority' => 'medium',
            'url' => '/transactions'
        ];
        
        // Send to staff who can process transactions
        $staffUsers = User::whereIn('role', ['staff', 'manager', 'admin'])->get();
        $notification = new SystemNotification($title, $message, $data);
        
        foreach ($staffUsers as $user) {
            $user->notify($notification);
        }
    }
    
    /**
     * Get notification preferences for user
     */
    public function getUserNotificationPreferences(User $user): array
    {
        // TODO: Implement user preferences from database
        // For now, return default preferences
        return [
            'email_transactions' => true,
            'email_loans' => true,
            'email_large_deposits' => true,
            'push_transactions' => true,
            'push_loans' => true,
            'push_large_deposits' => true,
            'sms_transactions' => false,
            'sms_loans' => true,
            'sms_large_deposits' => true,
        ];
    }
    
    /**
     * Check if user should receive notification based on preferences
     */
    public function shouldSendNotification(User $user, string $type, string $channel = 'database'): bool
    {
        $preferences = $this->getUserNotificationPreferences($user);
        $key = $channel . '_' . $type;
        
        return $preferences[$key] ?? true;
    }
} 