<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, string $type = 'created')
    {
        $this->transaction = $transaction;
        $this->type = $type; // created, approved, failed, completed
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
        $subject = $this->getMailSubject();
        $greeting = "Hello {$notifiable->name},";
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->getMailLine())
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
            ->line('Thank you for using our SACCO services!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'transaction_id' => $this->transaction->id,
            'transaction_type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'status' => $this->transaction->status,
            'member_name' => $this->transaction->member->name ?? 'Unknown',
            'action_type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'time' => Carbon::now()->toISOString(),
            'url' => '/transactions/' . $this->transaction->id,
            'priority' => $this->getPriority()
        ];
    }

    private function getTitle(): string
    {
        return match($this->type) {
            'created' => ucfirst($this->transaction->type) . ' Created',
            'approved' => ucfirst($this->transaction->type) . ' Approved',
            'failed' => ucfirst($this->transaction->type) . ' Failed',
            'completed' => ucfirst($this->transaction->type) . ' Completed',
            default => 'Transaction Update'
        };
    }

    private function getMessage(): string
    {
        $amount = number_format($this->transaction->amount, 2);
        $memberName = $this->transaction->member->name ?? 'Unknown';
        
        return match($this->type) {
            'created' => "KES {$amount} {$this->transaction->type} by {$memberName}",
            'approved' => "KES {$amount} {$this->transaction->type} approved",
            'failed' => "KES {$amount} {$this->transaction->type} failed",
            'completed' => "KES {$amount} {$this->transaction->type} completed",
            default => "Transaction update for KES {$amount}"
        };
    }

    private function getIcon(): string
    {
        return match($this->transaction->type) {
            'deposit' => 'plus-circle',
            'withdrawal' => 'minus-circle',
            'transfer' => 'arrows-right-left',
            default => 'credit-card'
        };
    }

    private function getColor(): string
    {
        return match($this->type) {
            'created' => 'blue',
            'approved' => 'green',
            'failed' => 'red',
            'completed' => 'emerald',
            default => 'zinc'
        };
    }

    private function getPriority(): string
    {
        if ($this->transaction->amount >= 50000) {
            return 'high';
        } elseif ($this->transaction->amount >= 10000) {
            return 'medium';
        }
        return 'low';
    }

    private function getMailSubject(): string
    {
        $type = ucfirst($this->transaction->type);
        return match($this->type) {
            'created' => "New {$type} Transaction",
            'approved' => "{$type} Transaction Approved",
            'failed' => "{$type} Transaction Failed",
            'completed' => "{$type} Transaction Completed",
            default => 'Transaction Update'
        };
    }

    private function getMailLine(): string
    {
        $amount = number_format($this->transaction->amount, 2);
        $type = $this->transaction->type;
        
        return match($this->type) {
            'created' => "A new {$type} transaction of KES {$amount} has been created and is pending approval.",
            'approved' => "Your {$type} transaction of KES {$amount} has been approved and will be processed shortly.",
            'failed' => "Your {$type} transaction of KES {$amount} has failed. Please contact support for assistance.",
            'completed' => "Your {$type} transaction of KES {$amount} has been completed successfully.",
            default => "There has been an update to your transaction of KES {$amount}."
        };
    }
}
