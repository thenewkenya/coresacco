<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;
use Carbon\Carbon;

class LargeDepositNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;
    public $threshold;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, float $threshold = 50000)
    {
        $this->transaction = $transaction;
        $this->threshold = $threshold;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->transaction->amount, 2);
        $memberName = $this->transaction->member->name ?? 'Unknown Member';
        
        return (new MailMessage)
            ->subject('Large Deposit Requires Approval')
            ->greeting("Hello {$notifiable->name},")
            ->line("A large deposit of KES {$amount} by {$memberName} requires your approval.")
            ->line("This deposit exceeds the threshold of KES " . number_format($this->threshold, 2) . " and needs manager approval.")
            ->line("Please review the transaction details and approve or reject as appropriate.")
            ->action('Review Transaction', url('/transactions/' . $this->transaction->id))
            ->line('This notification was sent for compliance and security purposes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'large_deposit',
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'threshold' => $this->threshold,
            'member_id' => $this->transaction->member_id,
            'member_name' => $this->transaction->member->name ?? 'Unknown',
            'member_number' => $this->transaction->member->member_number ?? null,
            'title' => 'Large Deposit Alert',
            'message' => $this->getMessage(),
            'icon' => 'exclamation-triangle',
            'color' => 'amber',
            'time' => Carbon::now()->toISOString(),
            'url' => '/transactions/' . $this->transaction->id,
            'priority' => 'high',
            'requires_action' => true,
            'actions' => [
                [
                    'label' => 'Approve',
                    'action' => 'approve',
                    'color' => 'green'
                ],
                [
                    'label' => 'Review',
                    'action' => 'review',
                    'color' => 'blue'
                ]
            ]
        ];
    }

    private function getMessage(): string
    {
        $amount = number_format($this->transaction->amount, 2);
        $memberName = $this->transaction->member->name ?? 'Unknown';
        
        return "KES {$amount} deposit by {$memberName} needs approval";
    }
}
