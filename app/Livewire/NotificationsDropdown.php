<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationsDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;

    protected $listeners = ['notificationReceived' => 'refreshNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        
        // Get notifications for current user based on role
        $query = $user->notifications();
        
        // Role-based filtering
        if ($user->hasRole('member')) {
            // Members only see their own transaction and loan notifications
            $query->whereIn('type', [
                'App\Notifications\TransactionNotification',
                'App\Notifications\LoanApplicationNotification'
            ]);
        } else {
            // Staff, managers, and admins see all notifications
            $query->whereIn('type', [
                'App\Notifications\TransactionNotification',
                'App\Notifications\LoanApplicationNotification',
                'App\Notifications\LargeDepositNotification'
            ]);
        }

        $notifications = $query->latest()->take(10)->get();
        
        $this->notifications = $notifications->map(function ($notification) {
            $data = $notification->data;
            $data['id'] = $notification->id;
            $data['read_at'] = $notification->read_at;
            $data['is_read'] = !is_null($notification->read_at);
            $data['time_ago'] = $this->timeAgo($notification->created_at);
            return $data;
        })->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    private function timeAgo($date)
    {
        $now = Carbon::now();
        $diff = $date->diffInMinutes($now);
        
        if ($diff < 1) {
            return 'Just now';
        } elseif ($diff < 60) {
            return $diff . ' min ago';
        } elseif ($diff < 1440) {
            $hours = intval($diff / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = intval($diff / 1440);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }

    public function getNotificationIcon($notification)
    {
        return $notification['icon'] ?? 'bell';
    }

    public function getNotificationColor($notification)
    {
        return $notification['color'] ?? 'zinc';
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
