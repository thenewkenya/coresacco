<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Loan;
use Carbon\Carbon;

class LoanApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $loan;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Loan $loan, string $type = 'application')
    {
        $this->loan = $loan;
        $this->type = $type; // application, approved, rejected, disbursed
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
            ->action('View Loan', url('/loans/' . $this->loan->id))
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
            'type' => 'loan',
            'loan_id' => $this->loan->id,
            'loan_type' => $this->loan->loanType->name ?? 'General Loan',
            'amount' => $this->loan->amount,
            'status' => $this->loan->status,
            'member_name' => $this->loan->member->name ?? 'Unknown',
            'action_type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'time' => Carbon::now()->toISOString(),
            'url' => '/loans/' . $this->loan->id,
            'priority' => $this->getPriority()
        ];
    }

    private function getTitle(): string
    {
        return match($this->type) {
            'application' => 'New Loan Application',
            'approved' => 'Loan Approved',
            'rejected' => 'Loan Rejected',
            'disbursed' => 'Loan Disbursed',
            default => 'Loan Update'
        };
    }

    private function getMessage(): string
    {
        $amount = number_format($this->loan->amount, 2);
        $memberName = $this->loan->member->name ?? 'Unknown';
        $loanType = $this->loan->loanType->name ?? 'General Loan';
        
        return match($this->type) {
            'application' => "{$memberName} - KES {$amount} ({$loanType})",
            'approved' => "KES {$amount} {$loanType} approved",
            'rejected' => "KES {$amount} {$loanType} rejected",
            'disbursed' => "KES {$amount} {$loanType} disbursed",
            default => "Loan update for KES {$amount}"
        };
    }

    private function getIcon(): string
    {
        return match($this->type) {
            'application' => 'document-text',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'disbursed' => 'banknotes',
            default => 'credit-card'
        };
    }

    private function getColor(): string
    {
        return match($this->type) {
            'application' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'disbursed' => 'emerald',
            default => 'zinc'
        };
    }

    private function getPriority(): string
    {
        if ($this->loan->amount >= 100000) {
            return 'high';
        } elseif ($this->loan->amount >= 50000) {
            return 'medium';
        }
        return 'low';
    }

    private function getMailSubject(): string
    {
        return match($this->type) {
            'application' => 'New Loan Application Received',
            'approved' => 'Loan Application Approved',
            'rejected' => 'Loan Application Update',
            'disbursed' => 'Loan Disbursement Confirmation',
            default => 'Loan Update'
        };
    }

    private function getMailLine(): string
    {
        $amount = number_format($this->loan->amount, 2);
        $loanType = $this->loan->loanType->name ?? 'General Loan';
        
        return match($this->type) {
            'application' => "A new {$loanType} application for KES {$amount} has been submitted and is under review.",
            'approved' => "Your {$loanType} application for KES {$amount} has been approved. Disbursement will be processed shortly.",
            'rejected' => "We regret to inform you that your {$loanType} application for KES {$amount} was not approved at this time. Please contact us for more information.",
            'disbursed' => "Your {$loanType} of KES {$amount} has been successfully disbursed to your account.",
            default => "There has been an update to your {$loanType} application for KES {$amount}."
        };
    }
}
