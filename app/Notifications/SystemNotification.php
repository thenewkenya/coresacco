<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->line('Thank you for using our SACCO services!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'type' => 'system',
            'title' => $this->title,
            'message' => $this->message,
            'icon' => 'information-circle',
            'color' => 'blue',
            'time' => Carbon::now()->toISOString(),
            'url' => '#',
            'priority' => 'medium'
        ], $this->data);
    }
}
